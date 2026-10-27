<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProductDescriptionService
{
    protected array $knownBrands = [
        'Sony', 'Canon', 'Nikon', 'Fujifilm', 'Panasonic', 'Blackmagic', 'Leica',
        'Sigma', 'Tamron', 'DJI', 'GoPro', 'Insta360', 'Zhiyun', 'Moza', 'Feiyutech',
        'Rode', 'Hollyland', 'Boya', 'Saramonic', 'Sennheiser', 'Godox', 'Aputure',
        'Amaran', 'Nanlite', 'Neewer', 'K&F Concept', 'SmallRig', 'Tilta', 'Falcam',
        'Ulanzi', 'Manfrotto', 'Benro', 'Sirui', 'Peak Design', 'Lowepro', 'SanDisk',
        'Lexar', 'NiceFoto', 'Manbily', 'Ambitful'
    ];

    /**
     * Generate the best description for a product.
     */
    public function generateBestDescription(Product $product, array $options = []): string
    {
        $style = $options['style'] ?? 'pro_ecommerce'; // pro_ecommerce, technical, storytelling
        $lang = $options['lang'] ?? 'fr';
        $brand = $this->detectBrand($product->name);
        $category = $product->category_name ?? '';

        // 1. Gather web intelligence & technical specs
        $webInsights = $this->fetchWebInsights($product->name, $brand, $category);

        // 2. If AI key is configured, use LLM generation
        $aiDescription = $this->generateWithAiIfAvailable($product, $webInsights, $style, $lang);
        if ($aiDescription && strlen(trim($aiDescription)) > 150) {
            return trim($aiDescription);
        }

        // 3. Fallback to specialized Cinema/Tech e-commerce copywriting engine
        return $this->craftStructuredDescription($product, $webInsights, $style, $lang);
    }

    /**
     * Bulk generate and apply descriptions for multiple products.
     */
    public function bulkGenerateDescriptions(array $productIds, array $options = []): array
    {
        $overwriteMode = $options['overwrite_mode'] ?? 'empty_only'; // 'empty_only' or 'all'
        $style = $options['style'] ?? 'pro_ecommerce';

        $products = Product::whereIn('id', $productIds)->get();
        $updated = 0;
        $skipped = 0;
        $details = [];

        foreach ($products as $product) {
            $hasExisting = !empty(trim($product->description ?? ''));
            if ($overwriteMode === 'empty_only' && $hasExisting && strlen($product->description) > 80) {
                $skipped++;
                $details[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'status' => 'skipped',
                    'reason' => 'Déjà renseigné',
                ];
                continue;
            }

            try {
                $newDescription = $this->generateBestDescription($product, ['style' => $style]);
                if ($newDescription) {
                    $product->update([
                        'description' => $newDescription,
                    ]);
                    $updated++;
                    $details[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'status' => 'updated',
                        'chars' => mb_strlen($newDescription),
                        'preview' => Str::limit($newDescription, 120),
                    ];
                } else {
                    $skipped++;
                    $details[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'status' => 'failed',
                        'reason' => 'Génération échouée',
                    ];
                }
            } catch (Throwable $e) {
                Log::error("Failed to generate description for product #{$product->id}: " . $e->getMessage());
                $skipped++;
                $details[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'status' => 'error',
                    'reason' => $e->getMessage(),
                ];
            }
        }

        // Flush product & catalog caches
        Cache::forget('frontend_home_data');
        Cache::forget('shop_catalog_categories');

        return [
            'total' => count($products),
            'updated' => $updated,
            'skipped' => $skipped,
            'details' => $details,
        ];
    }

    /**
     * Fetch live web snippets & technical highlights.
     */
    public function fetchWebInsights(string $productName, ?string $brand, ?string $category): array
    {
        $cleanName = trim(preg_replace('/\([^)]*\)/', '', $productName));
        $query = $cleanName . ' fiche technique caracteristiques test avis';

        $snippets = [];

        try {
            $url = "https://html.duckduckgo.com/html/?q=" . urlencode($query);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 4,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            ]);
            $html = curl_exec($ch);

            if ($html && preg_match_all('/<a class="result__snippet"[^>]*>(.*?)<\/a>/s', $html, $matches)) {
                foreach (array_slice($matches[1], 0, 5) as $rawSnippet) {
                    $text = trim(strip_tags(html_entity_decode($rawSnippet, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                    if (strlen($text) > 30) {
                        $snippets[] = $text;
                    }
                }
            }
        } catch (Throwable $e) {
            Log::info("Web insight lookup failed for {$productName}: " . $e->getMessage());
        }

        return $snippets;
    }

    /**
     * If Gemini or OpenAI API keys are present in env or DB settings, call AI.
     */
    protected function generateWithAiIfAvailable(Product $product, array $webInsights, string $style, string $lang): ?string
    {
        $geminiKey = env('GEMINI_API_KEY') ?: setting('gemini_api_key');
        $openaiKey = env('OPENAI_API_KEY') ?: setting('openai_api_key');

        if (!$geminiKey && !$openaiKey) {
            return null;
        }

        $insightsText = !empty($webInsights) ? "Données web & spécifications trouvées :\n- " . implode("\n- ", $webInsights) : "";

        $prompt = <<<PROMPT
Tu es le rédacteur en chef et expert technique chez Full Frame House Maroc, showroom référence en matériel de cinéma, caméras, optiques, stabilisateurs et studio au Maroc.
Rédige la MEILLEURE fiche descriptive e-commerce pour le produit suivant :

Nom du produit : {$product->name}
Marque : {$this->detectBrand($product->name)}
Catégorie : {$product->category_name}
Prix indicatif : {$product->formatted_price}
{$insightsText}

Consignes obligatoires de rédaction :
- Langue : Français soigné, professionnel et engageant.
- Style : {$style} (professionnel, orienté créateurs vidéo, réalisateurs, chefs opérateurs et photographes).
- Structure souhaitée :
  1. 🎯 Une accroche percutante et une vue d'ensemble du produit (2-3 phrases captivantes).
  2. ✨ 5 à 6 Points Forts Majeurs (avec puces claires et émojis adaptés).
  3. 📋 Spécifications Techniques Clés (Capteur/Optique, Définition/Résolution, Connectique/Autonomie, Poids/Dimensions).
  4. 🎬 Cas d'usage recommandés (Fiction, Reportage, Studio, Vlogging, Drone, etc.).
  5. 📦 Contenu du coffret standard.
  6. 🛡️ Mention de garantie 2 ans constructeur, test possible au showroom à Casablanca, et livraison express partout au Maroc.

Format de sortie : Texte brut bien espacé avec sauts de lignes (pas de markdown complexe avec des astérisques partout, privilégie une mise en page aérée et élégante).
PROMPT;

        // Try Gemini if available
        if ($geminiKey) {
            try {
                $response = Http::timeout(12)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        return $text;
                    }
                }
            } catch (Throwable $e) {
                Log::warning("Gemini description generation failed: " . $e->getMessage());
            }
        }

        // Try OpenAI if available
        if ($openaiKey) {
            try {
                $response = Http::withToken($openaiKey)->timeout(12)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['choices'][0]['message']['content'] ?? null;
                }
            } catch (Throwable $e) {
                Log::warning("OpenAI description generation failed: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Craft a high-converting, domain-specific e-commerce description.
     */
    protected function craftStructuredDescription(Product $product, array $webInsights, string $style, string $lang): string
    {
        $name = $product->name;
        $brand = $this->detectBrand($name);
        $category = strtolower($product->category_name ?? '');

        // Detect device domain
        $isCinema = str_contains(strtolower($name), 'cinéma') || str_contains(strtolower($name), 'fx') || str_contains(strtolower($name), 'cine') || str_contains($category, 'caméra');
        $isLens = str_contains(strtolower($name), 'objectif') || str_contains(strtolower($name), 'lens') || str_contains($category, 'optique');
        $isGimbal = str_contains(strtolower($name), 'stabilisateur') || str_contains(strtolower($name), 'gimbal') || str_contains(strtolower($name), 'ronin') || str_contains(strtolower($name), 'crane');
        $isAudio = str_contains(strtolower($name), 'micro') || str_contains(strtolower($name), 'wireless') || str_contains($category, 'audio');
        $isLighting = str_contains(strtolower($name), 'light') || str_contains(strtolower($name), 'led') || str_contains(strtolower($name), 'softbox') || str_contains($category, 'éclairage');
        $isDrone = str_contains(strtolower($name), 'drone') || str_contains(strtolower($name), 'dji') && str_contains(strtolower($name), 'mini');

        $intro = "";
        $keyPoints = [];
        $specs = [];
        $usage = "";
        $boxContent = [];

        if ($isCinema) {
            $intro = "Le {$name} est un concentré de technologies cinématographiques conçu pour les créateurs exigeants, réalisateurs et vidéastes professionnels. Il combine ergonomie de tournage compacte, colorimétrie cinématographique de pointe et une polyvalence exceptionnelle sur le terrain.";
            $keyPoints = [
                "Qualité d'image professionnelle avec dynamique étendue et rendu colorimétrique cinématographique naturel.",
                "Enregistrement vidéo haute fréquence 4K Ultra-HD avec échantillonnage couleur 10-bit pour un étalonnage poussé.",
                "Système de refroidissement actif permettant des tournages continus sans surchauffe même en conditions extrêmes.",
                "Autofocus intelligent de précision avec suivi en temps réel des yeux et des visages.",
                "Boîtier compact et modulaire avec points de fixation intégrés pour cages, moniteurs et poignées audio.",
            ];
            $specs = [
                "Définition vidéo : 4K Ultra-HD & flux ralenti haute cadence",
                "Colorimétrie : Profils Log & profils cinématographiques haute dynamique",
                "Autofocus : Suivi prédictif hybride temps réel",
                "Connectivité : Sortie HDMI pleine taille, USB-C Power Delivery, prises audio dédiées",
                "Stockage : Compatible cartes haute vitesse CFexpress & SD UHS-II",
            ];
            $usage = "Parfaitement adapté aux productions de fiction, publicités, documentaires, clips vidéo et tournages en équipe réduite ou solo.";
            $boxContent = [
                "1x {$name}",
                "1x Batterie rechargeable haute autonomie",
                "1x Poignée supérieure ou adaptateur modulaire",
                "1x Câble d'alimentation & adaptateur secteur",
                "1x Guide de démarrage rapide & carte de garantie",
            ];
        } elseif ($isLens) {
            $intro = "L'optique {$name} offre une résolution optique remarquable et un piqué saisissant du centre jusqu'aux bords de l'image. Idéal pour sublimer vos prises de vues, il assure un rendu des contrastes fidèle et un bokeh créatif et velouté.";
            $keyPoints = [
                "Formule optique haute précision réduisant au minimum les aberrations chromatiques et les reflets parasites.",
                "Grande ouverture lumineuse garantissant d'excellentes performances en basse lumière et une profondeur de champ maîtrisée.",
                "Moteur d'autofocus ultra-rapide, précis et silencieux, idéal en photo comme en vidéo.",
                "Conception tropicalisée robuste résistant aux éclaboussures et à la poussière pour les tournages en extérieur.",
                "Bague de mise au point fluide et progressive offrant un contrôle manuel millimétré.",
            ];
            $specs = [
                "Traitement optique : Multicouches anti-reflets nano",
                "Diaphragme : Circulaire pour un bokeh fluide",
                "Mise au point : Autofocus interne ultra-silencieux",
                "Construction : Barillet en alliage léger et monture métallique de précision",
            ];
            $usage = "Idéal pour le portrait, le reportage, les productions vidéo broadcast, la fiction et la photographie d'art.";
            $boxContent = [
                "1x Objectif {$name}",
                "1x Pare-soleil dédié",
                "1x Bouchon avant et bouchon arrière",
                "1x Housse de transport de protection",
            ];
        } elseif ($isGimbal) {
            $intro = "Le stabilisateur {$name} repousse les limites de la fluidité cinématique. Doté d'une motorisation puissante et d'algorithmes d'équilibrage de dernière génération, il vous garantit des mouvements de caméra impeccables même dans l'action.";
            $keyPoints = [
                "Stabilisation 3 axes avancée compensant instantanément les secousses de marche et de course.",
                "Capacité de charge élevée compatible avec une large variété de boîtiers hybrides et caméras cinéma avec optiques lourdes.",
                "Écran de contrôle tactile intégré pour un réglage rapide des paramètres sans passer par le smartphone.",
                "Basculement vertical instantané pour la création de formats vidéo réseaux sociaux ultra-qualitatifs.",
                "Batterie longue durée assurant une journée complète de tournage intensif.",
            ];
            $specs = [
                "Axes de rotation : Pan 360°, Tilt, Roll",
                "Connectivité : Bluetooth & commandes caméra intégrées",
                "Autonomie : Jusqu'à 12 heures d'utilisation continue",
                "Matériaux : Alliage d'aluminium aéronautique et fibre de carbone",
            ];
            $usage = "Incontournable pour les clips musicaux, les plans-séquences, l'événementiel, les mariages et le cinéma dynamique.";
            $boxContent = [
                "1x Stabilisateur {$name}",
                "1x Poignée-batterie",
                "1x Trépied d'extension",
                "1x Plaque de fixation rapide",
                "1x Câbles de contrôle caméra & mallette de transport",
            ];
        } elseif ($isAudio) {
            $intro = "Le système audio {$name} garantit une capture sonore d'une clarté broadcast sans interférences. Conçu pour les créateurs nomades, journalistes et vidéastes, il allie simplicité d'utilisation plug-and-play et fidélité acoustique irréprochable.";
            $keyPoints = [
                "Qualité d'enregistrement audio haute définition avec préamplis à très faible bruit de fond.",
                "Transmission sans fil numérique cryptée stable avec portée étendue sans coupures.",
                "Enregistrement de secours interne de sécurité (float 32-bit ou sauvegarde intégrée) pour éviter toute saturation.",
                "Format ultra-compact et discret qui s'accroche facilement aux vêtements ou sur la griffe porte-flash.",
                "Boîtier de charge compact offrant plusieurs cycles d'autonomie en déplacement.",
            ];
            $specs = [
                "Fréquence : Numérique sécurisée 2.4 GHz",
                "Réponse en fréquence : 20 Hz - 20 kHz",
                "Compatibilité : Caméras, enregistreurs audio, smartphones iOS & Android, PC/Mac",
                "Autonomie : Jusqu'à 18 heures avec l'étui de charge",
            ];
            $usage = "Parfait pour les interviews, le vlogging, les podcasts, les vidéos corporate et les reportages de terrain.";
            $boxContent = [
                "1x Émetteur(s) & Récepteur {$name}",
                "1x Boîtier de charge de transport",
                "2x Bonnettes anti-vent en fourrure (deadcat)",
                "1x Câbles de connexion 3.5mm TRS, TRRS & USB-C",
            ];
        } elseif ($isLighting) {
            $intro = "L'éclairage studio {$name} offre une puissance lumineuse homogène et une fidélité colorimétrique professionnelle (CRI/TLCI 96+). Indispensable pour façonner la lumière de vos vidéos et shootings, il assure un rendu naturel et soigné de vos sujets.";
            $keyPoints = [
                "Excellente fidélité des couleurs garantissant des tons de peau naturels et un étalonnage simplifié.",
                "Contrôle précis de l'intensité de 0 à 100% avec réglage de température de couleur ou effets créatifs intégrés.",
                "Système de dissipation thermique silencieux évitant toute gêne pour la prise de son sur le plateau.",
                "Monture universelle standard compatible avec une multitude de modificateurs (softboxes, diffuseurs, coupe-flux).",
                "Pilotage à distance via application mobile ou télécommande sans fil.",
            ];
            $specs = [
                "Indice de rendu : CRI ≥ 96, TLCI ≥ 97",
                "Refroidissement : Ventilateur ultra-silencieux & dissipateur passif",
                "Alimentation : Secteur AC & batteries nomades professionnelles",
            ];
            $usage = "Idéal pour les studios photo, les plateaux YouTube, le streaming live, les interviews et les productions publicitaires.";
            $boxContent = [
                "1x Projecteur / Torche {$name}",
                "1x Réflecteur standard ou adaptateur",
                "1x Câble d'alimentation & bloc secteur",
                "1x Sacoche de transport rembourrée",
            ];
        } else {
            // General Pro Audiovisual Equipment
            $intro = "Le {$name} est un équipement professionnel hautement fiable, conçu pour répondre aux normes exigeantes des techniciens et créateurs de l'audiovisuel. Il associe robustesse des matériaux, ergonomie soignée et efficacité immédiate au travail.";
            $keyPoints = [
                "Conception professionnelle robuste conçue pour un usage intensif sur les tournages et en studio.",
                "Facilité de mise en œuvre avec une ergonomie intuitive réduisant le temps d'installation.",
                "Compatibilité étendue avec les principaux écosystèmes et accessoires du marché audiovisuel.",
                "Matériaux durables et finitions de haute précision garantissant une longévité maximale.",
            ];
            $specs = [
                "Marque : " . ($brand ?: 'Professionnel'),
                "Compatibilité : Écosystème universel photo & vidéo",
                "Finition : Alliage haute résistance & composants certifiés",
            ];
            $usage = "Recommandé pour tous les créateurs de contenu, studios de production et passionnés de matériel de qualité.";
            $boxContent = [
                "1x {$name}",
                "1x Ensemble d'accessoires de fixation ou câblage requis",
                "1x Notice d'utilisation officielle",
            ];
        }

        // Add web insight enhancements if relevant
        if (!empty($webInsights)) {
            $relevantSnippets = array_slice($webInsights, 0, 2);
            $intro .= "\n\n" . implode(" ", $relevantSnippets);
        }

        // Build structured text representation
        $out = "🎯 VUE D'ENSEMBLE\n";
        $out .= $intro . "\n\n";

        $out .= "✨ POINTS FORTS & INNOVATIONS\n";
        foreach ($keyPoints as $kp) {
            $out .= "• " . $kp . "\n";
        }
        $out .= "\n";

        $out .= "📋 CARACTÉRISTIQUES TECHNIQUES\n";
        foreach ($specs as $sp) {
            $out .= "• " . $sp . "\n";
        }
        $out .= "\n";

        $out .= "🎬 USAGE RECOMMANDÉ\n";
        $out .= $usage . "\n\n";

        $out .= "📦 CONTENU DE LA BOÎTE\n";
        foreach ($boxContent as $bc) {
            $out .= "• " . $bc . "\n";
        }
        $out .= "\n";

        $out .= "🛡️ GARANTIE & SERVICE FULL FRAME HOUSE\n";
        $out .= "• Produit 100% authentique avec garantie constructeur 2 ans.\n";
        $out .= "• Conseils et prise en main par nos techniciens au showroom de Casablanca.\n";
        $out .= "• Expédition rapide et sécurisée avec suivi partout au Maroc.\n";

        return trim($out);
    }

    /**
     * Detect known brand name from product name.
     */
    protected function detectBrand(string $name): ?string
    {
        foreach ($this->knownBrands as $b) {
            if (preg_match('/\b' . preg_quote($b, '/') . '\b/i', $name)) {
                return $b;
            }
        }
        return null;
    }
}
