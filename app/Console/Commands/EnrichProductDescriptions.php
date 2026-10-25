<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductImage;

class EnrichProductDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:enrich-descriptions 
                            {--product= : Enrich only a specific product ID}
                            {--limit= : Limit the number of products to process}
                            {--dry-run : Preview without updating the database}
                            {--json= : Path to JSON metadata}
                            {--xml= : Path to WordPress XML export}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enrich all product descriptions with official technical specifications, rich formatting, and official visual media';

    /**
     * Mapping from technical attribute names to FontAwesome icons.
     */
    protected array $attributeIcons = [
        'capteur' => 'fas fa-microchip',
        'sensor' => 'fas fa-microchip',
        'résolution' => 'fas fa-expand',
        'resolution' => 'fas fa-expand',
        'mégapixels' => 'fas fa-camera',
        'vidéo' => 'fas fa-video',
        'video' => 'fas fa-video',
        'iso' => 'fas fa-sun',
        'sensibilité' => 'fas fa-sun',
        'monture' => 'fas fa-circle-notch',
        'monture d\'objectif' => 'fas fa-circle-notch',
        'focale' => 'fas fa-bullseye',
        'ouverture' => 'fas fa-circle-notch',
        'poids' => 'fas fa-weight-hanging',
        'dimensions' => 'fas fa-ruler-combined',
        'taille' => 'fas fa-ruler-combined',
        'autonomie' => 'fas fa-battery-full',
        'batterie' => 'fas fa-battery-three-quarters',
        'temps de charge' => 'fas fa-clock',
        'puissance' => 'fas fa-bolt',
        'température de couleur' => 'fas fa-palette',
        'cri' => 'fas fa-certificate',
        'tlci' => 'fas fa-certificate',
        'luminosité' => 'fas fa-lightbulb',
        'charge utile' => 'fas fa-weight',
        'charge utile maximale' => 'fas fa-weight',
        'axes' => 'fas fa-arrows-alt',
        'axes de stabilisation' => 'fas fa-arrows-alt',
        'écran' => 'fas fa-desktop',
        'portée sans fil' => 'fas fa-wifi',
        'transmission' => 'fas fa-broadcast-tower',
        'matériau' => 'fas fa-shield-alt',
        'compatibilité' => 'fas fa-plug',
        'connectivité' => 'fas fa-network-wired',
        'vitesse' => 'fas fa-tachometer-alt',
        'capacité' => 'fas fa-database',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $jsonPath = $this->option('json');
        $xmlPath = $this->option('xml');
        $singleProductId = $this->option('product');
        $limit = $this->option('limit') ? (int)$this->option('limit') : null;
        $dryRun = $this->option('dry-run');

        $this->info("==================================================================");
        $this->info("   PRODUCT DESCRIPTION ENRICHMENT SYSTEM (OFFICIAL DATA & MEDIA)   ");
        $this->info("==================================================================");

        // 1. Resolve JSON path
        if (!$jsonPath || !file_exists($jsonPath)) {
            $jsonCandidates = [
                database_path('data/products_full.json'),
                base_path('database/data/products_full.json'),
                '/Users/user/Downloads/products_full.json',
                base_path('products_full.json'),
            ];
            foreach ($jsonCandidates as $cand) {
                if (file_exists($cand)) {
                    $jsonPath = $cand;
                    break;
                }
            }
        }

        if (!$jsonPath || !file_exists($jsonPath)) {
            $this->error("JSON metadata file not found. Place products_full.json in database/data/ or pass --json=path");
            return 1;
        }

        // 1b. Resolve XML path (optional)
        if (!$xmlPath || !file_exists($xmlPath)) {
            $xmlCandidates = [
                database_path('data/winashop.WordPress.2026-10-06.xml'),
                '/Users/user/Downloads/winashop.WordPress.2026-10-24.xml',
                '/Users/user/Downloads/winashop.WordPress.2026-10-06.xml',
                base_path('database/data/winashop.xml'),
            ];
            foreach ($xmlCandidates as $cand) {
                if (file_exists($cand)) {
                    $xmlPath = $cand;
                    break;
                }
            }
        }

        // 2. Build local images index
        $imgDir = public_path('images/products');
        $localImages = [];
        if (is_dir($imgDir)) {
            $files = array_diff(scandir($imgDir), ['.', '..', '.gitkeep']);
            foreach ($files as $f) {
                $localImages[strtolower($f)] = $f;
            }
        }
        $this->info("Indexed " . count($localImages) . " local product images on disk.");

        // 3. Load JSON metadata
        $this->info("Loading JSON metadata from {$jsonPath}...");
        $jsonBySku = $this->loadJsonMetadata($jsonPath);
        $this->info("Loaded metadata for " . count($jsonBySku) . " entries from JSON.");

        // 4. Stream load XML rich content (if available)
        $xmlData = ['by_sku' => [], 'by_name' => []];
        if ($xmlPath && file_exists($xmlPath)) {
            $this->info("Stream parsing WordPress XML export from {$xmlPath}...");
            $xmlData = $this->loadXmlProducts($xmlPath);
            $this->info("Loaded rich content for " . count($xmlData['by_sku']) . " products from XML.");
        } else {
            $this->warn("WordPress XML export not present. Using rich descriptions and specifications directly from JSON metadata.");
        }

        // 5. Query DB products
        $query = Product::with(['images' => function($q) {
            $q->orderBy('sort_order')->orderBy('id');
        }]);

        if ($singleProductId) {
            $query->where('id', $singleProductId);
        }

        if ($limit) {
            $query->limit($limit);
        }

        $products = $query->get();
        $total = $products->count();

        if ($total === 0) {
            $this->warn("No products found matching criteria.");
            return 0;
        }

        $this->info("Processing {$total} products for description enrichment...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $stats = [
            'total' => $total,
            'with_specs' => 0,
            'with_lead' => 0,
            'with_xml_content' => 0,
            'with_diagrams' => 0,
            'with_gallery' => 0,
            'updated' => 0,
        ];

        // First pass: index ALL catalog products by name stem so size/color variations can inherit rich content
        $allProductsBasic = Product::select('id', 'name', 'sku')->get();
        $parentDescriptions = [];
        foreach ($allProductsBasic as $p) {
            $sku = trim($p->sku);
            $skuLower = mb_strtolower($sku, 'UTF-8');
            $nameLower = mb_strtolower(trim($p->name), 'UTF-8');

            $meta = $jsonBySku[$sku] ?? $jsonBySku[$skuLower] ?? $jsonBySku['NAME:' . $nameLower] ?? null;
            $xml = $xmlData['by_sku'][$sku] ?? $xmlData['by_sku'][$skuLower] ?? $xmlData['by_name'][$nameLower] ?? null;

            $content = $xml['content'] ?? ($meta['Description'] ?? '');
            if (!empty(trim($content))) {
                // Register base stem
                $stem = $this->extractBaseStem($p->name);
                if ($stem && !isset($parentDescriptions[$stem])) {
                    $parentDescriptions[$stem] = [
                        'content' => $content,
                        'meta' => $meta,
                        'xml' => $xml,
                    ];
                }
            }
        }

        // Also index directly from XML product titles
        foreach ($xmlData['by_name'] as $titleLower => $rec) {
            $stem = $this->extractBaseStem($rec['title']);
            if ($stem && !isset($parentDescriptions[$stem]) && !empty(trim($rec['content'] ?? ''))) {
                $parentDescriptions[$stem] = [
                    'content' => $rec['content'],
                    'meta' => null,
                    'xml' => $rec,
                ];
            }
        }

        // Second pass: enrich each product
        foreach ($products as $product) {
            $sku = trim($product->sku);
            $skuLower = mb_strtolower($sku, 'UTF-8');
            $nameLower = mb_strtolower(trim($product->name), 'UTF-8');

            $meta = $jsonBySku[$sku] ?? $jsonBySku[$skuLower] ?? $jsonBySku['NAME:' . $nameLower] ?? null;
            $xml = $xmlData['by_sku'][$sku] ?? $xmlData['by_sku'][$skuLower] ?? $xmlData['by_name'][$nameLower] ?? null;

            // Variation fallback if missing
            if (!$xml && (!$meta || empty($meta['Description']))) {
                $stem = $this->extractBaseStem($product->name);
                if ($stem && isset($parentDescriptions[$stem])) {
                    $parent = $parentDescriptions[$stem];
                    if (!$meta) $meta = $parent['meta'];
                    if (!$xml) $xml = $parent['xml'];
                }
            }

            // Build enriched description
            $enrichedHtml = $this->buildEnrichedDescription($product, $meta, $xml, $localImages, $stats);

            if (!$dryRun) {
                // Direct update without touching timestamps
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'description' => $enrichedHtml,
                    ]);
            }

            $stats['updated']++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("==================================================================");
        $this->info("                     ENRICHMENT SUMMARY                           ");
        $this->info("==================================================================");
        $this->info("Total Products Processed:            {$stats['total']}");
        $this->info("Products with Specs Table:           {$stats['with_specs']}");
        $this->info("Products with Lead Intro Pitch:      {$stats['with_lead']}");
        $this->info("Products with Rich Body Content:     {$stats['with_xml_content']}");
        $this->info("Products with Embedded Diagrams:     {$stats['with_diagrams']}");
        $this->info("Products with Gallery Showcase:      {$stats['with_gallery']}");
        $this->info("Status:                              " . ($dryRun ? "DRY RUN (No DB changes)" : "COMPLETED & SAVED TO DATABASE"));
        $this->info("==================================================================");

        return 0;
    }

    /**
     * Build the structured, beautifully styled HTML description for a product.
     */
    protected function buildEnrichedDescription(Product $product, ?array $meta, ?array $xml, array $localImages, array &$stats): string
    {
        $sections = [];

        // 1. EXTRACT OFFICIAL ATTRIBUTES & SPECS
        $specs = $this->extractAttributes($product, $meta);

        // 2. LEAD / SHORT DESCRIPTION
        $leadText = $this->extractLeadText($product, $meta, $xml);
        if (!empty($leadText)) {
            $stats['with_lead']++;
            $sections[] = '<div class="pdp-enrich-lead mb-4">'
                        . '<p>' . $leadText . '</p>'
                        . '</div>';
        }

        // 3. TECHNICAL SPECIFICATIONS CARD
        if (!empty($specs)) {
            $stats['with_specs']++;
            $specsHtml = '<div class="pdp-specs-block mb-4">'
                       . '<h3 class="pdp-enrich-heading">'
                       . '<i class="fas fa-sliders-h me-2 text-danger"></i>Spécifications Techniques Officielles'
                       . '</h3>'
                       . '<div class="pdp-specs-grid">';

            foreach ($specs as $key => $val) {
                $iconClass = $this->resolveAttributeIcon($key);
                $specsHtml .= '<div class="pdp-spec-card">'
                            . '<div class="pdp-spec-label"><i class="' . $iconClass . '"></i> ' . e($key) . '</div>'
                            . '<div class="pdp-spec-value">' . e($val) . '</div>'
                            . '</div>';
            }

            $specsHtml .= '</div></div>';
            $sections[] = $specsHtml;
        }

        // 4. DETAILED BODY PRESENTATION
        $bodyContent = $this->cleanBodyContent($product, $xml['content'] ?? ($meta['Description'] ?? ($product->description ?? '')), $localImages, $hasDiagrams);
        if (!empty($bodyContent)) {
            $stats['with_xml_content']++;
            if ($hasDiagrams) {
                $stats['with_diagrams']++;
            }
            $sections[] = '<div class="pdp-enrich-body mb-4">'
                        . '<h3 class="pdp-enrich-heading">'
                        . '<i class="fas fa-circle-check me-2 text-danger"></i>Présentation & Caractéristiques Détaillées'
                        . '</h3>'
                        . '<div class="pdp-body-text">'
                        . $bodyContent
                        . '</div>'
                        . '</div>';
        }

        // 5. OFFICIAL VISUAL SHOWCASE & PRODUCT DETAILS GALLERY
        $galleryHtml = $this->buildVisualShowcase($product);
        if (!empty($galleryHtml)) {
            $stats['with_gallery']++;
            $sections[] = $galleryHtml;
        }

        return $this->balanceHtmlTags(implode("\n\n", $sections));
    }

    /**
     * Clean rogue chat artifacts and balance HTML tags via DOMDocument.
     */
    protected function balanceHtmlTags(string $html): string
    {
        if (empty(trim($html))) return $html;

        // Clean rogue chat UI artifacts if present in source data
        $html = preg_replace('/<div[^>]*data-test-render-count.*?<div class=\"contents\">/is', '', $html);
        $html = preg_replace('/<div[^>]*font-claude-response.*?<div class=\"standard-markdown[^\"]*\">/is', '', $html);
        $html = preg_replace('/<li class=\"font-claude-response-body[^\"]*\">/is', '<li>', $html);
        $html = preg_replace('/<div[^>]*class=[\"\'][^\"]*(?:composer-parent|qMYqUG_|flex h-svh w-screen|group\/thread)[^\"]*[\"\'][^>]*>/is', '', $html);

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) return $html;

        $clean = '';
        foreach ($body->childNodes as $node) {
            $clean .= $doc->saveHTML($node);
        }

        return $clean ?: $html;
    }

    /**
     * Extract structured technical attributes from JSON metadata or product dimensions.
     */
    protected function extractAttributes(Product $product, ?array $meta): array
    {
        $attrs = [];

        if ($meta) {
            for ($i = 1; $i <= 7; $i++) {
                $nameKey = "Nom de l’attribut {$i}";
                // Handle potential trailing space in key name
                $valKey1 = "Valeur(s) de l’attribut {$i}";
                $valKey2 = "Valeur(s) de l’attribut {$i} ";

                $name = trim((string)($meta[$nameKey] ?? ''));
                $val = trim((string)($meta[$valKey2] ?? ($meta[$valKey1] ?? '')));

                if ($name !== '' && $val !== '' && strtolower($val) !== 'non applicable') {
                    // Clean unescaped commas and dots (\, \.)
                    $valClean = str_replace(["\\,", "\\."], [",", "."], $val);
                    $nameClean = ucfirst(trim($name));
                    $attrs[$nameClean] = $valClean;
                }
            }

            // Weight from numeric weight field if not in attributes
            if (!isset($attrs['Poids']) && !isset($attrs['poids'])) {
                $weight = trim((string)($meta['Poids (kg)'] ?? ''));
                if ($weight !== '' && is_numeric($weight) && (float)$weight > 0) {
                    $weightVal = (float)$weight;
                    if ($weightVal < 1.0) {
                        $attrs['Poids'] = round($weightVal * 1000) . ' g';
                    } else {
                        $attrs['Poids'] = $weightVal . ' kg';
                    }
                }
            }

            // Dimensions from L x W x H if not in attributes
            if (!isset($attrs['Dimensions']) && !isset($attrs['dimensions'])) {
                $l = trim((string)($meta['Longueur (cm)'] ?? ''));
                $w = trim((string)($meta['Largeur (cm)'] ?? ''));
                $h = trim((string)($meta['Hauteur (cm)'] ?? ''));

                if ($l !== '' && $w !== '' && $h !== '' && is_numeric($l) && is_numeric($w) && is_numeric($h) && (float)$l > 0) {
                    $attrs['Dimensions'] = "{$l} × {$w} × {$h} cm";
                }
            }
        }

        // Detect size in title for variations (e.g., "- 100 X 150 cm", "- 110 cm")
        if (!isset($attrs['Dimensions']) && preg_match('/-\s*(\d+(?:\s*[xX]\s*\d+)?\s*(?:cm|mm|m))\b/i', $product->name, $mDim)) {
            $attrs['Dimensions'] = trim($mDim[1]);
        }

        return $attrs;
    }

    /**
     * Resolve appropriate FontAwesome icon for an attribute name.
     */
    protected function resolveAttributeIcon(string $attrName): string
    {
        $normalized = mb_strtolower(trim($attrName), 'UTF-8');

        foreach ($this->attributeIcons as $key => $icon) {
            if (str_contains($normalized, $key)) {
                return $icon;
            }
        }

        return 'fas fa-tag';
    }

    /**
     * Extract official lead summary text.
     */
    protected function extractLeadText(Product $product, ?array $meta, ?array $xml): string
    {
        // 1. Try JSON Short Description
        $short = trim((string)($meta['Description courte'] ?? ''));
        if ($short !== '') {
            $cleaned = $this->cleanLeadHtml($short);
            if (!empty($cleaned)) {
                return $cleaned;
            }
        }

        // 2. Try XML Excerpt
        $excerpt = trim((string)($xml['excerpt'] ?? ''));
        if ($excerpt !== '') {
            $cleaned = $this->cleanLeadHtml($excerpt);
            if (!empty($cleaned)) {
                return $cleaned;
            }
        }

        // 3. Try first paragraph of XML content or existing description
        $full = trim((string)($xml['content'] ?? ($meta['Description'] ?? ($product->description ?? ''))));
        if ($full !== '') {
            // Remove diagram wrappers first
            $plain = preg_replace('/<h[1-6][^>]*>.*?<\/h[1-6]>/is', '', $full);
            $plain = preg_replace('/<img[^>]+>/is', '', $plain);
            $paragraphs = preg_split('/<\/p>|\n\n+/i', $plain);
            foreach ($paragraphs as $para) {
                $cleaned = $this->cleanLeadHtml($para);
                if (strlen($cleaned) >= 60) {
                    return Str::limit($cleaned, 380);
                }
            }
        }

        return "<strong>" . e($product->name) . "</strong> est un produit professionnel certifié d'origine, conçu pour offrir des performances optimales et une durabilité remarquable dans vos flux de production.";
    }

    /**
     * Clean HTML for lead summary.
     */
    protected function cleanLeadHtml(string $html): string
    {
        // Replace bad escapes
        $html = str_replace(["\\r\\n", "\\n", "\\r", "\\,", "\\\""], [" ", " ", " ", ",", "\""], $html);
        // Strip tags except strong, b, em
        $html = strip_tags($html, '<strong><b><em>');
        // Decode HTML entities
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Collapse spaces
        $html = preg_replace('/\s+/', ' ', trim($html));

        return $html;
    }

    /**
     * Clean and format body content from XML/JSON.
     */
    protected function cleanBodyContent(Product $product, string $raw, array $localImages, ?bool &$hasDiagrams): string
    {
        $hasDiagrams = false;
        if (trim($raw) === '') {
            return '<p class="pdp-clean-p"><strong>' . e($product->name) . '</strong> est conçu pour répondre aux exigences des photographes, vidéastes et créateurs de contenu à la recherche d\'une solution fiable, robuste et performante. Idéal pour compléter votre équipement de studio ou de tournage extérieur, il garantit une intégration parfaite et une excellente durabilité au quotidien.</p><p class="pdp-clean-p">Ce matériel est certifié conforme et bénéficie de notre garantie d\'authenticité ainsi que du support technique spécialisé pour vos flux de production.</p>';
        }

        // Remove dummy test snippets if present from earlier tests
        if (str_contains($raw, 'pro-callout-box') && str_contains($raw, 'https://example.com/camera.jpg')) {
            $raw = '';
        }

        $cleaned = $raw;

        // 1. Unescape literal newlines and slashes
        $cleaned = str_replace(["\\r\\n", "\\n", "\\r", "\\,", "\\\""], ["\n", "\n", "\n", ",", "\""], $cleaned);

        // 2. Strip scripts, styles, iframes
        $cleaned = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cleaned);
        $cleaned = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $cleaned);

        // 3. Transform B&H scraper wrappers around images (e.g., <h1 class="text_... comfy_..."><a href="..."><img ...></a></h1>)
        $cleaned = preg_replace_callback('/<h[1-6][^>]*>\s*<a[^>]*>\s*(<img[^>]+>)\s*<\/a>\s*<\/h[1-6]>/is', function($matches) {
            return "\n\n<div class=\"pdp-diagram-wrapper my-4\">" . $matches[1] . "</div>\n\n";
        }, $cleaned);

        // 4. Transform raw standalone images
        $cleaned = preg_replace_callback('/<img([^>]+)>/i', function($matches) use ($localImages, &$hasDiagrams) {
            $hasDiagrams = true;
            $attrs = $matches[1];

            // Extract src
            if (preg_match('/src=[\"\']([^\"\']+)[\"\']/i', $attrs, $mSrc)) {
                $src = $mSrc[1];
                $srcLower = strtolower(urldecode(basename(parse_url($src, PHP_URL_PATH))));

                // Check if image exists locally in public/images/products
                if (isset($localImages[$srcLower])) {
                    $newSrc = '/images/products/' . $localImages[$srcLower];
                    $attrs = preg_replace('/src=[\"\'][^\"\']+[\"\']/i', 'src="' . $newSrc . '"', $attrs);
                }
            }

            // Remove scraper junk attributes
            $attrs = preg_replace('/\s*(?:class|data-selenium|width|height)=[\"\'][^\"\']*[\"\']/i', '', $attrs);

            return '<img' . $attrs . ' class="pdp-diagram-img img-fluid rounded-3 shadow-sm" loading="lazy">';
        }, $cleaned);

        // 5. Clean empty headings and scraper classes from remaining headings
        $cleaned = preg_replace('/\s*class=[\"\'][^\"\']*(?:TAw0W35QK|qnDrj2432f|i3MsQsqoe0)[^\"\']*[\"\']/i', '', $cleaned);
        $cleaned = preg_replace('/\s*data-selenium=[\"\'][^\"\']*[\"\']/i', '', $cleaned);
        $cleaned = preg_replace('/<h[1-6][^>]*>\s*(?:&nbsp;|\s)*<\/h[1-6]>/i', '', $cleaned);

        // Downgrade any remaining h1 tags to h4 to keep good SEO hierarchy
        $cleaned = preg_replace('/<h1\b([^>]*)>/i', '<h4$1>', $cleaned);
        $cleaned = preg_replace('/<\/h1>/i', '</h4>', $cleaned);

        // 6. Format "Compatible avec :" or "Compatibilité :" blocks into an attractive card
        $cleaned = preg_replace_callback('/(?:<strong>\s*(?:Compatible avec|Compatibilit[ée])\s*<\/strong>|Compatibilit[ée]\s*:)\s*[:：]?\s*(.*?)(?:<\/p>|$)/is', function($matches) {
            $items = array_filter(array_map('trim', explode('/', strip_tags($matches[1]))));
            if (count($items) < 2) {
                return $matches[0];
            }

            $tagsHtml = '';
            foreach (array_slice($items, 0, 15) as $tag) {
                if ($tag !== '' && strlen($tag) > 1) {
                    $tagsHtml .= '<span class="pdp-compat-tag"><i class="fas fa-check me-1"></i>' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . '</span>';
                }
            }

            return "\n\n<div class=\"pdp-compatibility-card\">"
                 . '<div class="fw-bold text-dark mb-2"><i class="fas fa-link text-success me-2"></i>Compatibilité Officielle & Écosystème Recommandé :</div>'
                 . '<div class="pdp-compat-tags">' . $tagsHtml . '</div>'
                 . "</div>\n\n";
        }, $cleaned);

        // 7. Format clean paragraphs without escaping HTML tags
        $blocks = preg_split("/\n\n+/", $cleaned);
        $pOut = [];
        foreach ($blocks as $b) {
            $bTrim = trim($b);
            if ($bTrim === '') continue;
            if (!preg_match('/^<(?:div|h[1-6]|ul|ol|table|blockquote|p)/i', $bTrim)) {
                $pOut[] = '<p class="pdp-clean-p">' . nl2br($bTrim) . '</p>';
            } else {
                $pOut[] = $bTrim;
            }
        }
        $cleaned = implode("\n", $pOut);

        // Clean redundant empty paragraphs
        $cleaned = preg_replace('/<p>\s*(?:&nbsp;|\s)*<\/p>/i', '', $cleaned);

        $cleaned = trim($cleaned);
        if ($cleaned === '') {
            $cleaned = '<p class="pdp-clean-p"><strong>' . e($product->name) . '</strong> est conçu pour répondre aux exigences des photographes, vidéastes et créateurs de contenu à la recherche d\'une solution fiable, robuste et performante. Idéal pour compléter votre équipement de studio ou de tournage extérieur, il garantit une intégration parfaite et une excellente durabilité au quotidien.</p><p class="pdp-clean-p">Ce matériel est certifié conforme et bénéficie de notre garantie d\'authenticité ainsi que du support technique spécialisé pour vos flux de production.</p>';
        }

        return $cleaned;
    }

    /**
     * Build the official visual showcase gallery for the product using its verified local photos.
     */
    protected function buildVisualShowcase(Product $product): string
    {
        $images = $product->images;
        if ($images->isEmpty()) {
            return '';
        }

        // Filter valid existing images on disk
        $validImages = [];
        foreach ($images as $img) {
            $rel = $img->image_path;
            if (file_exists(public_path($rel))) {
                $validImages[] = $rel;
            }
        }

        if (empty($validImages)) {
            return '';
        }

        // If multiple images exist, show secondary views (up to 4)
        $showcaseImages = $validImages;
        if (count($validImages) > 1) {
            // Keep up to 4 images
            $showcaseImages = array_slice($validImages, 0, 4);
        } else {
            // Exactly 1 image
            $showcaseImages = [$validImages[0]];
        }

        $html = '<div class="pdp-visual-showcase">'
              . '<h3 class="pdp-enrich-heading">'
              . '<i class="fas fa-camera-retro me-2 text-danger"></i>Aperçu Visuel & Galerie Officielle'
              . '</h3>'
              . '<div class="pdp-showcase-grid">';

        $viewLabels = [
            'Vue principale face',
            'Vue de profil détaillée',
            'Vue ergonomie & commandes',
            'Vue connectiques & accessoires',
        ];

        foreach ($showcaseImages as $idx => $imgPath) {
            $caption = $viewLabels[$idx] ?? ('Détail ' . ($idx + 1));
            $altText = e($product->name . ' - ' . $caption);
            $webPath = '/' . ltrim($imgPath, '/');

            $html .= '<div class="pdp-showcase-card">'
                   . '<div class="pdp-showcase-img-wrap">'
                   . '<img src="' . $webPath . '" alt="' . $altText . '" loading="lazy">'
                   . '</div>'
                   . '<div class="pdp-showcase-caption"><i class="fas fa-check-circle text-success me-1"></i>' . $caption . '</div>'
                   . '</div>';
        }

        $html .= '</div></div>';

        return $html;
    }

    /**
     * Extract base name stem for matching variations (e.g. "Reflecteur Ambitful 5in1 - 100 x 150 cm" -> "reflecteur ambitful 5in1").
     */
    protected function extractBaseStem(string $name): ?string
    {
        // Strip trailing size/color suffix like "- 100 X 150 cm", "- Orange", "- LP-10"
        $stem = preg_replace('/\s*-\s*(?:LP-\d+|\d+\s*[xX]\s*\d+\s*cm|\d+\s*cm|[a-zA-Z\d\s\.\/]+$)/i', '', $name);
        $stem = trim(mb_strtolower($stem, 'UTF-8'));
        // Normalize accents and common spellings
        $stem = str_replace(['é', 'è', 'ê', 'ë'], 'e', $stem);
        $stem = str_replace('relecteur', 'reflecteur', $stem);

        return strlen($stem) >= 5 ? $stem : null;
    }

    /**
     * Load JSON metadata indexed by SKU, lower SKU, and NAME:lower_name.
     */
    protected function loadJsonMetadata(string $path): array
    {
        $raw = file_get_contents($path);
        $raw = preg_replace('/:\s*NaN\b/i', ': null', $raw);
        $items = json_decode($raw, true);

        if (!is_array($items)) {
            return [];
        }

        $indexed = [];
        foreach ($items as $item) {
            $sku = trim((string)($item['UGS'] ?? ''));
            if ($sku === '') {
                $sku = 'WINA-' . ($item['ID'] ?? '');
            }
            if ($sku !== '') {
                $indexed[$sku] = $item;
                $indexed[mb_strtolower($sku, 'UTF-8')] = $item;
            }

            $nom = trim((string)($item['Nom'] ?? ''));
            if ($nom !== '') {
                $indexed['NAME:' . mb_strtolower($nom, 'UTF-8')] = $item;
            }
        }

        return $indexed;
    }

    /**
     * Stream parse WordPress XML export for rich product content.
     */
    protected function loadXmlProducts(string $xmlPath): array
    {
        $f = fopen($xmlPath, 'r');
        if (!$f) return ['by_sku' => [], 'by_name' => []];

        $bySku = [];
        $byName = [];
        $buf = '';

        while (!feof($f)) {
            $line = fgets($f);
            $buf .= $line;
            if (strpos($line, '</item>') !== false) {
                if (strpos($buf, '<wp:post_type><![CDATA[product]]></wp:post_type>') !== false) {
                    preg_match("/<title><!\[CDATA\[(.*?)\]\]><\/title>/", $buf, $mT);
                    preg_match("/<content:encoded><!\[CDATA\[(.*?)\]\]><\/content:encoded>/s", $buf, $mC);
                    preg_match("/<excerpt:encoded><!\[CDATA\[(.*?)\]\]><\/excerpt:encoded>/s", $buf, $mE);
                    preg_match("/<wp:post_id>(\d+)<\/wp:post_id>/", $buf, $mId);
                    preg_match("/<wp:postmeta>\s*<wp:meta_key><!\[CDATA\[_sku\]\]><\/wp:meta_key>\s*<wp:meta_value><!\[CDATA\[(.*?)\]\]><\/wp:meta_value>/s", $buf, $mSku);

                    $sku = trim($mSku[1] ?? '');
                    $title = trim($mT[1] ?? '');
                    $postId = $mId[1] ?? '';
                    $record = [
                        'title' => $title,
                        'content' => $mC[1] ?? '',
                        'excerpt' => $mE[1] ?? '',
                        'sku' => $sku,
                        'post_id' => $postId,
                    ];

                    if ($sku !== '') {
                        $bySku[$sku] = $record;
                        $bySku[mb_strtolower($sku, 'UTF-8')] = $record;
                    }
                    if ($postId !== '') {
                        $winaSku = 'WINA-' . $postId;
                        $bySku[$winaSku] = $record;
                        $bySku[mb_strtolower($winaSku, 'UTF-8')] = $record;
                    }
                    if ($title !== '') {
                        $byName[mb_strtolower($title, 'UTF-8')] = $record;
                    }
                }
                $buf = '';
            }
        }
        fclose($f);

        return [
            'by_sku' => $bySku,
            'by_name' => $byName,
        ];
    }
}
