<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductSearchService
{
    /**
     * Common camera & audiovisual synonym mappings.
     */
    protected static array $synonyms = [
        'micro' => ['son', 'audio', 'microphone', 'cravate', 'hf', 'lavalier', 'rode', 'dji mic', 'wireless'],
        'microphone' => ['son', 'audio', 'micro', 'cravate', 'hf', 'lavalier', 'rode', 'boya'],
        'cravate' => ['lavalier', 'micro cravate', 'micro sans fil', 'wireless mic'],
        'lavalier' => ['cravate', 'micro cravate', 'micro sans fil', 'rode'],
        'gimbal' => ['stabilisateur', 'stabilisateurs', 'ronin', 'rs', 'osmo', 'zhiyun'],
        'stabilisateur' => ['gimbal', 'ronin', 'rs', 'osmo', 'stabilisateurs', 'zhiyun'],
        'tripod' => ['trépied', 'trepied', 'monopied', 'pied'],
        'trepied' => ['tripod', 'trépied', 'monopied'],
        'trépied' => ['tripod', 'trepied', 'monopied'],
        'monopod' => ['monopied', 'trépied'],
        'monopied' => ['monopod', 'trépied'],
        'lens' => ['objectif', 'objectifs', 'optique', 'focale'],
        'lentille' => ['objectif', 'objectifs', 'optique', 'lens'],
        'optique' => ['objectif', 'objectifs', 'lens'],
        'objectif' => ['lens', 'optique', 'focale'],
        'sd' => ['carte mémoire', 'carte', 'sandisk', 'sdxc', 'lexar', 'cfexpress'],
        'carte sd' => ['carte mémoire', 'carte', 'sandisk', 'sdxc', 'lexar'],
        'carte' => ['carte mémoire', 'sd', 'sdxc', 'lexar', 'sandisk'],
        'memory' => ['carte mémoire', 'sandisk', 'lexar', 'cfexpress', 'sdxc'],
        'lighting' => ['éclairage', 'lumière', 'led', 'godox', 'studio', 'aputure', 'amaran', 'nanlite'],
        'lumiere' => ['éclairage', 'lumieres', 'led', 'godox', 'studio', 'aputure', 'amaran'],
        'lumieres' => ['éclairage', 'lumière', 'led', 'godox', 'studio', 'aputure', 'amaran'],
        'eclairage' => ['lumières', 'lumière', 'led', 'godox', 'studio', 'aputure', 'softbox'],
        'éclairage' => ['lumières', 'lumière', 'led', 'godox', 'studio', 'aputure', 'softbox'],
        'softbox' => ['boite a lumiere', 'éclairage', 'godox', 'aputure', 'octabox'],
        'flash' => ['speedlite', 'strobe', 'godox', 'éclairage'],
        'action cam' => ['insta360', 'gopro', 'action', 'dji action'],
        'pocket' => ['osmo pocket', 'dji pocket', 'pocket 3'],
        'pocket3' => ['pocket 3', 'osmo pocket 3', 'dji pocket 3'],
        'podcast' => ['audio', 'micro', 'son', 'rode', 'interface audio'],
        'sac' => ['housse', 'valise', 'sac à dos', 'transport', 'lowepro', 'peak design'],
        'bag' => ['sac', 'sac à dos', 'housse', 'valise'],
        'cage' => ['smallrig', 'rig', 'chassis'],
        'batterie' => ['chargeur', 'accu', 'alimentation', 'np-f', 'v-mount'],
        'battery' => ['batterie', 'chargeur', 'accu', 'np-f', 'v-mount'],
        'chargeur' => ['batterie', 'alimentation', 'charge'],
        'filtre' => ['nd', 'cpl', 'polarisant', 'uv', 'k&f'],
        'filter' => ['filtre', 'nd', 'cpl', 'polarisant', 'k&f'],
        'moniteur' => ['écran', 'monitor', 'atomos', 'feelworld', 'portkeys'],
        'monitor' => ['moniteur', 'écran', 'atomos', 'feelworld', 'portkeys'],
        'transmetteur' => ['wireless', 'hollyland', 'accsoon', 'vidéo sans fil'],
        'transmitter' => ['transmetteur', 'hollyland', 'accsoon'],
        'drone' => ['dji', 'mavic', 'mini', 'air', 'avata', 'fpv'],
        'camera' => ['boîtier', 'appareil photo', 'sony', 'canon', 'fujifilm', 'lumix'],
        'boitier' => ['caméra', 'camera', 'hybride', 'appareil photo'],
    ];

    /**
     * Exact canonical cross-language / manufacturer SKU aliases evaluated at query time.
     */
    protected static array $phraseAliases = [
        'ilce-7m4' => ['sony a7 iv', 'alpha 7 iv', 'a7 iv'],
        'ilce7m4' => ['sony a7 iv', 'alpha 7 iv', 'a7 iv'],
        'a7m4' => ['a7 iv', 'alpha 7 iv'],
        'a74' => ['a7 iv', 'alpha 7 iv'],
        'ilme-fx3' => ['sony fx3', 'fx3'],
        'ilmefx3' => ['sony fx3', 'fx3'],
        'ilme-fx30' => ['sony fx30', 'fx30'],
        'ilmefx30' => ['sony fx30', 'fx30'],
        'ilce-7sm3' => ['a7s iii', 'alpha 7s iii'],
        'a7sm3' => ['a7s iii', 'alpha 7s iii'],
        'a7s3' => ['a7s iii', 'alpha 7s iii'],
        'ilce-7rm5' => ['a7r v', 'alpha 7r v'],
        'a7rm5' => ['a7r v', 'alpha 7r v'],
        'a7r5' => ['a7r v', 'alpha 7r v'],
        'ilce-7cm2' => ['a7c ii', 'alpha 7c ii'],
        'a7cm2' => ['a7c ii'],
        'a7c2' => ['a7c ii'],
        'zve10' => ['zv e10', 'zv-e10'],
        'zv e10' => ['zve10', 'zv-e10'],
        'zve1' => ['zv e1', 'zv-e1'],
        'op3' => ['osmo pocket 3', 'pocket 3'],
        'oa4' => ['osmo action 4', 'action 4'],
        'oa5' => ['osmo action 5', 'action 5'],
        'rs4' => ['dji rs 4', 'rs 4'],
        'rs3' => ['dji rs 3', 'rs 3'],
        'r6m2' => ['r6 mark ii', 'eos r6 mark ii'],
        'r5m2' => ['r5 mark ii', 'eos r5 mark ii'],
        'memory card' => ['carte memoire'],
        'sd card' => ['carte memoire', 'carte sd'],
        'card reader' => ['lecteur de carte'],
        'studio light' => ['eclairage studio', 'lumiere studio'],
        'video light' => ['torche led', 'lumiere video', 'eclairage'],
        'ring light' => ['anneau lumineux', 'lumiere'],
        'camera bag' => ['sac photo', 'sac camera'],
        'nd filter' => ['filtre nd'],
        'cpl filter' => ['filtre cpl', 'filtre polarisant'],
        'wireless mic' => ['micro sans fil'],
        'shotgun mic' => ['micro canon'],
        'teleprompter' => ['teleprompteur'],
    ];

    /**
     * Token-level unambiguous multilingual aliases (preserving token count in multi-word queries).
     */
    protected static array $canonicalWordAliases = [
        'tripod' => 'trepied',
        'tripods' => 'trepied',
        'trepied' => 'tripod',
        'trepieds' => 'tripod',
        'monopod' => 'monopied',
        'monopied' => 'monopod',
        'gimbal' => 'stabilisateur',
        'stabilizer' => 'stabilisateur',
        'stabiliser' => 'stabilisateur',
        'stabilisateur' => 'gimbal',
        'stabilisateurs' => 'gimbal',
        'lens' => 'objectif',
        'lenses' => 'objectif',
        'objectif' => 'lens',
        'objectifs' => 'lens',
        'lighting' => 'eclairage',
        'light' => 'lumiere',
        'lights' => 'lumiere',
        'eclairage' => 'lighting',
        'lumiere' => 'lighting',
        'lumieres' => 'lighting',
        'battery' => 'batterie',
        'batteries' => 'batterie',
        'batterie' => 'battery',
        'charger' => 'chargeur',
        'chargeur' => 'charger',
        'bag' => 'sac',
        'backpack' => 'sac',
        'sac' => 'bag',
        'filter' => 'filtre',
        'filters' => 'filtre',
        'filtre' => 'filter',
        'filtres' => 'filter',
        'polarizer' => 'polarisant',
        'polarisant' => 'polarizer',
        'monitor' => 'moniteur',
        'moniteur' => 'monitor',
        'recorder' => 'enregistreur',
        'enregistreur' => 'recorder',
        'transmitter' => 'transmetteur',
        'transmetteur' => 'transmitter',
        'lavalier' => 'cravate',
        'cravate' => 'lavalier',
        'boitier' => 'camera',
    ];

    /**
     * Arabic & Darija audiovisual keywords transliterated to catalog nouns before ASCII folding.
     */
    protected static array $arabicKeywordMap = [
        'كاميرا' => 'camera',
        'كاميرة' => 'camera',
        'عدسة' => 'objectif',
        'عدسات' => 'objectif',
        'ميكروفون' => 'microphone',
        'مايك' => 'micro',
        'مثبت' => 'stabilisateur',
        'حامل' => 'trepied',
        'ترايبود' => 'trepied',
        'إضاءة' => 'eclairage',
        'اضاءة' => 'eclairage',
        'فلاش' => 'flash',
        'بطارية' => 'batterie',
        'شاحن' => 'chargeur',
        'درون' => 'drone',
        'حقيبة' => 'sac',
        'فلتر' => 'filtre',
        'ذاكرة' => 'carte memoire',
        'شاشة' => 'moniteur',
    ];

    /**
     * Words that explicitly indicate the shopper is looking for an accessory rather than the main body/unit.
     */
    protected static array $accessoryIntentWords = [
        'cage', 'rig', 'batterie', 'battery', 'chargeur', 'charger', 'filtre', 'filter',
        'sac', 'bag', 'coque', 'case', 'etui', 'housse', 'verre', 'protection', 'cable',
        'adaptateur', 'adapter', 'bague', 'support', 'poignee', 'handle', 'pare-soleil',
        'caisson', 'bracket', 'plate', 'plateau', 'sangle', 'strap', 'cache', 'bouchon',
    ];

    /**
     * Roman numeral <-> Arabic digits mappings for camera models (e.g., A7 IV <-> A7 4, Mark III <-> Mark 3).
     */
    protected static array $romanMap = [
        'iv' => '4',
        '4' => 'iv',
        'iii' => '3',
        '3' => 'iii',
        'ii' => '2',
        '2' => 'ii',
        'vi' => '6',
        '6' => 'vi',
        'v' => '5',
        '5' => 'v',
    ];

    /**
     * Curated popular searches for zero-result states and quick discovery chips.
     */
    protected static array $popularSearches = [
        'Sony A7 IV',
        'DJI Osmo Pocket 3',
        'Insta360 Ace Pro 2',
        'Canon EOS R6 Mark II',
        'Objectif 50mm f/1.8',
        'Microphone RODE Wireless',
        'Stabilisateur DJI RS 4',
        'Lumière Godox LED',
        'Carte SD 128GB'
    ];

    /**
     * Normalize and clean search query string, splitting model names and camera suffixes.
     */
    public static function normalizeQuery(string $rawQuery): array
    {
        $raw = trim($rawQuery);
        if (mb_strlen($raw) === 0) {
            return [
                'raw' => '',
                'clean' => '',
                'normalized' => '',
                'tokens' => [],
                'roman_variants' => [],
                'synonyms' => [],
            ];
        }

        // 1. Strip dangerous characters but preserve hyphen, slash, and dot
        $clean = preg_replace('/[^\p{L}\p{N}\s\/\.\-]/u', ' ', $raw);
        $clean = preg_replace('/\s+/u', ' ', trim($clean));

        // 2. Camera Model Normalization
        $normalized = $clean;

        // Pattern 1: Camera model with Roman numeral suffix: a7iv -> a7 iv, a7siii -> a7s iii, r6ii -> r6 ii
        $normalized = preg_replace('/\b([a-zA-Z]+\d+[s|r]?)(iv|vi|v|iii|ii)\b/i', '$1 $2', $normalized);

        // Pattern 2: Word with number: pocket3 -> pocket 3, action4 -> action 4, osmo3 -> osmo 3
        $normalized = preg_replace('/\b([a-zA-Z]{3,})(\d+)\b/u', '$1 $2', $normalized);

        // Pattern 3: Model with letter suffix: g7x -> g7 x, a7s -> a7 s, a7r -> a7 r
        $normalized = preg_replace('/\b([a-zA-Z]+\d+)([a-zA-Z])\b/u', '$1 $2', $normalized);

        $normalized = preg_replace('/\s+/u', ' ', trim($normalized));

        // 3. Extract tokens
        $rawTokens = array_filter(explode(' ', mb_strtolower($normalized)), fn($t) => mb_strlen(trim($t)) > 0);
        $tokens = array_values(array_unique($rawTokens));

        // Also add the original clean query as a token if different (e.g. "g7x")
        $origTokens = array_filter(explode(' ', mb_strtolower($clean)), fn($t) => mb_strlen(trim($t)) > 0);
        foreach ($origTokens as $ot) {
            if (!in_array($ot, $tokens, true)) {
                $tokens[] = $ot;
            }
        }

        // 4. Roman Numeral Variants
        $romanVariants = [];
        foreach ($tokens as $token) {
            if (isset(self::$romanMap[$token])) {
                $romanVariants[$token] = self::$romanMap[$token];
            }
        }

        // 5. Synonym expansion (strict word-boundary match so partial substrings inside gibberish or unrelated words never trigger)
        $expandedSynonyms = [];
        $lowerClean = mb_strtolower($clean);
        foreach (self::$synonyms as $key => $syns) {
            if (preg_match('/(?:\b|^)' . preg_quote($key, '/') . '(?:\b|$)/iu', $lowerClean)) {
                foreach ($syns as $s) {
                    if (!in_array($s, $expandedSynonyms, true)) {
                        $expandedSynonyms[] = $s;
                    }
                }
            }
        }

        return [
            'raw' => $raw,
            'clean' => $clean,
            'normalized' => $normalized,
            'tokens' => $tokens,
            'roman_variants' => $romanVariants,
            'synonyms' => $expandedSynonyms,
        ];
    }

    /**
     * Calculate match score (0.0 to 1.0) between search query and product name.
     * Supports Damerau-Levenshtein transposition typos, phonetics, multilingual aliases,
     * manufacturer SKU codes, and flagship-above-accessory ranking.
     */
    public static function calculateNameMatchScore(string $query, string $productName, float $minWordSimilarity = 70.0): float
    {
        $rawQuery = trim($query);
        $rawName = trim($productName);
        if ($rawQuery === '' || $rawName === '') {
            return 0.0;
        }

        $variants = self::expandCanonicalVariants($rawQuery);
        $bestScore = 0.0;

        foreach ($variants as $index => $variantQuery) {
            $isAlias = $index > 0;
            $score = self::scoreQueryVariantAgainstProduct($variantQuery, $rawName, $minWordSimilarity, $isAlias);
            if ($score > $bestScore) {
                $bestScore = $score;
            }
            if ($bestScore >= 0.995 && !$isAlias) {
                break;
            }
        }

        return $bestScore;
    }

    /**
     * Generate direct + canonical alternate-name variants for a query while preserving token count semantics.
     *
     * @return array<int, string>
     */
    protected static function expandCanonicalVariants(string $query): array
    {
        $lower = mb_strtolower(trim($query));
        $variants = [$lower];

        // Transliterate Arabic/Darija equipment nouns before ASCII folding
        $arabicMapped = $lower;
        $hasArabic = false;
        foreach (self::$arabicKeywordMap as $ar => $fr) {
            if (mb_strpos($arabicMapped, $ar) !== false) {
                $arabicMapped = str_replace($ar, ' ' . $fr . ' ', $arabicMapped);
                $hasArabic = true;
            }
        }
        if ($hasArabic) {
            $arabicMapped = preg_replace('/\s+/u', ' ', trim($arabicMapped));
            if ($arabicMapped !== '' && !in_array($arabicMapped, $variants, true)) {
                $variants[] = $arabicMapped;
            }
        }

        $ascii = preg_replace('/\s+/', ' ', trim(Str::ascii($arabicMapped)));
        if ($ascii !== '' && !in_array($ascii, $variants, true)) {
            $variants[] = $ascii;
        }

        // Exact phrase / model code aliases (e.g. "ilce-7m4" -> "sony a7 iv", "op3" -> "osmo pocket 3")
        foreach (self::$phraseAliases as $alias => $replacements) {
            if ($ascii === $alias || preg_match('/(?:\b|^)' . preg_quote($alias, '/') . '(?:\b|$)/i', $ascii)) {
                foreach ($replacements as $rep) {
                    $replaced = ($ascii === $alias)
                        ? $rep
                        : preg_replace('/(?:\b|^)' . preg_quote($alias, '/') . '(?:\b|$)/i', $rep, $ascii);
                    $replaced = preg_replace('/\s+/', ' ', trim((string) $replaced));
                    if ($replaced !== '' && !in_array($replaced, $variants, true)) {
                        $variants[] = $replaced;
                    }
                }
            }
        }

        // Unambiguous single-token multilingual aliases (e.g. "tripod" -> "trepied", "gimbal" -> "stabilisateur")
        $words = array_values(array_filter(explode(' ', $ascii), fn($w) => $w !== ''));
        if (!empty($words) && count($words) <= 8) {
            $swapped = $words;
            $changed = false;
            foreach ($words as $idx => $w) {
                if (isset(self::$canonicalWordAliases[$w])) {
                    $swapped[$idx] = self::$canonicalWordAliases[$w];
                    $changed = true;
                }
            }
            if ($changed) {
                $joined = implode(' ', $swapped);
                if (!in_array($joined, $variants, true)) {
                    $variants[] = $joined;
                }
            }
        }

        return array_slice($variants, 0, 6);
    }

    /**
     * Score one query variant strictly against a product title, then apply primary vs accessory weighting.
     */
    protected static function scoreQueryVariantAgainstProduct(string $query, string $productName, float $minWordSimilarity, bool $isAliasVariant): float
    {
        $qLower = mb_strtolower(trim($query));
        $nLower = mb_strtolower(trim($productName));
        if ($qLower === '' || $nLower === '') {
            return 0.0;
        }

        $minThresh = max(0.10, min(0.99, $minWordSimilarity / 100.0));

        // Clean punctuation to spaces while preserving unicode letters and digits
        $qClean = preg_replace('/\s+/u', ' ', trim(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $qLower)));
        $nClean = preg_replace('/\s+/u', ' ', trim(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $nLower)));
        if ($qClean === '' || $nClean === '') {
            return 0.0;
        }

        $qAscii = preg_replace('/\s+/', ' ', trim(Str::ascii($qClean)));
        $nAscii = preg_replace('/\s+/', ' ', trim(Str::ascii($nClean)));

        $rawBaseScore = null;

        if ($qClean === $nClean || ($qAscii !== '' && $qAscii === $nAscii)) {
            $rawBaseScore = 1.0;
        } elseif (preg_match('/(?:\b|^)' . preg_quote($qClean, '/') . '(?:\b|$)/iu', $nClean)) {
            $rawBaseScore = 1.0;
        } elseif (mb_strlen($qClean) >= 3 && mb_strpos($nClean, $qClean) !== false) {
            $rawBaseScore = 0.96;
        } elseif ($qAscii !== '' && preg_match('/(?:\b|^)' . preg_quote($qAscii, '/') . '(?:\b|$)/i', $nAscii)) {
            $rawBaseScore = 0.99;
        } elseif (strlen($qAscii) >= 3 && stripos($nAscii, $qAscii) !== false) {
            $rawBaseScore = 0.95;
        }

        // Compact matching without spaces with digit boundaries (e.g. "fx 3" -> "fx3", "pocket 3" -> "pocket3")
        $qNoSpace = preg_replace('/[^a-z0-9]/', '', $qAscii);
        $nNoSpace = preg_replace('/[^a-z0-9]/', '', $nAscii);
        if ($rawBaseScore === null && mb_strlen($qNoSpace) >= 3) {
            $hasStartDigit = (bool) preg_match('/^\d/', $qNoSpace);
            $hasEndDigit = (bool) preg_match('/\d$/', $qNoSpace);
            $pattern = '/' . ($hasStartDigit ? '(?<![0-9])' : '') . preg_quote($qNoSpace, '/') . ($hasEndDigit ? '(?![0-9])' : '') . '/i';
            if (preg_match($pattern, $nNoSpace)) {
                $rawBaseScore = 0.99;
            }
        }

        // Tokenize query & product name
        $rawTokens = array_values(array_filter(explode(' ', $qAscii), fn($t) => strlen($t) > 0));
        $nWords = array_values(array_filter(explode(' ', $nAscii), fn($w) => strlen($w) > 0));
        if (empty($rawTokens) || empty($nWords)) {
            return 0.0;
        }

        if ($rawBaseScore === null) {
            // Split concatenated camera model suffixes (e.g. 'a7iv' -> ['a7', 'iv'], 'pocket3' -> ['pocket', '3'])
            $tokens = [];
            foreach ($rawTokens as $rt) {
                if (preg_match('/^([a-z]+\d+)(iv|vi|v|iii|ii)$/i', $rt, $m)) {
                    $tokens[] = $m[1];
                    $tokens[] = $m[2];
                } elseif (preg_match('/^([a-z]{3,})(\d+)$/i', $rt, $m)) {
                    $tokens[] = $m[1];
                    $tokens[] = $m[2];
                } elseif (preg_match('/^([a-z]+\d+)([a-z])$/i', $rt, $m)) {
                    $tokens[] = $m[1];
                    $tokens[] = $m[2];
                } else {
                    $tokens[] = $rt;
                }
            }

            $totalScore = 0.0;
            foreach ($tokens as $token) {
                $tokenScore = 0.0;
                $hasDigits = (bool) preg_match('/\d/', $token);
                $romanEquiv = self::$romanMap[$token] ?? null;

                if (in_array($token, $nWords, true) || ($romanEquiv && in_array($romanEquiv, $nWords, true))) {
                    $tokenScore = 1.0;
                } elseif ($token === 'a7' && in_array('alpha', $nWords, true) && in_array('7', $nWords, true)) {
                    $tokenScore = 1.0;
                } elseif ($token === '7' && (in_array('a7', $nWords, true) || in_array('7', $nWords, true))) {
                    $tokenScore = 1.0;
                } else {
                    $tPhonetic = (!$hasDigits && strlen($token) >= 4) ? self::phoneticKey($token) : '';
                    $tDigits = $hasDigits ? preg_replace('/\D/', '', $token) : '';

                    foreach ($nWords as $word) {
                        if ($word === $token || ($romanEquiv && $word === $romanEquiv)) {
                            $tokenScore = 1.0;
                            break;
                        }

                        // Prefix / substring match on pure text words
                        if (!$hasDigits && strlen($token) >= 3 && str_contains($word, $token)) {
                            $subScore = max(0.82, strlen($token) / max(1, strlen($word)));
                            if ($subScore > $tokenScore) {
                                $tokenScore = $subScore;
                            }
                        }

                        // Typo similarity: similar_text + Damerau-Levenshtein transpositions + phonetic key
                        if (strlen($token) >= 2 && strlen($word) >= 2) {
                            $wHasDigits = (bool) preg_match('/\d/', $word);

                            if ($hasDigits || $wHasDigits) {
                                // Short model codes (<= 4 chars, e.g. fx3 vs x3, r6 vs z6) must never cross-match on partial chars
                                if (max(strlen($token), strlen($word)) <= 4) {
                                    continue;
                                }

                                $wDigits = preg_replace('/\D/', '', $word);
                                $digitsCompatible = ($tDigits !== '' && $wDigits !== '')
                                    && ($tDigits === $wDigits || str_starts_with($wDigits, $tDigits) || str_starts_with($tDigits, $wDigits));

                                $tAlpha = preg_replace('/\d/', '', $token);
                                $wAlpha = preg_replace('/\d/', '', $word);
                                $alphaPrefixCompatible = ($tAlpha !== '' && $wAlpha !== '' && $tAlpha[0] === $wAlpha[0]);

                                if ($digitsCompatible && $alphaPrefixCompatible) {
                                    similar_text($token, $word, $pct);
                                    $sim = max($pct / 100.0, self::damerauLevenshteinSimilarity($token, $word));
                                    if ($sim >= max(0.75, $minThresh) && $sim > $tokenScore) {
                                        $tokenScore = $sim;
                                    }
                                }
                            } else {
                                similar_text($token, $word, $pct);
                                $bestWordSim = max($pct / 100.0, self::damerauLevenshteinSimilarity($token, $word));

                                if ($tPhonetic !== '' && strlen($word) >= 4) {
                                    $wPhonetic = self::phoneticKey($word);
                                    if ($tPhonetic === $wPhonetic) {
                                        $bestWordSim = max($bestWordSim, 0.94);
                                    } else {
                                        similar_text($tPhonetic, $wPhonetic, $phPct);
                                        $bestWordSim = max($bestWordSim, ($phPct / 100.0) * 0.92);
                                    }
                                }

                                if ($bestWordSim >= $minThresh && $bestWordSim > $tokenScore) {
                                    $tokenScore = $bestWordSim;
                                }
                            }
                        }
                    }
                }

                $totalScore += $tokenScore;
            }

            $avgTokenScore = $totalScore / max(1, count($tokens));

            // Sliding window check for concatenated or slightly misspelled contiguous phrases
            $windowSize = min(count($nWords), max(1, count($rawTokens)));
            $maxWindowScore = 0.0;
            if (strlen($qNoSpace) >= 3) {
                $qWinDigits = preg_replace('/\D/', '', $qNoSpace);
                for ($i = 0; $i <= count($nWords) - $windowSize; $i++) {
                    $windowSlice = implode('', array_slice($nWords, $i, $windowSize));
                    $wWinDigits = preg_replace('/\D/', '', $windowSlice);

                    // Prevent digit model mismatch in sliding window (e.g. "sonyfx3" must never match "sonyfx5" or "sonyfx6")
                    if ($qWinDigits !== '' || $wWinDigits !== '') {
                        if ($qWinDigits === '' || $wWinDigits === '') {
                            continue;
                        }
                        if ($qWinDigits !== $wWinDigits && !(strlen($qWinDigits) >= 2 && (str_starts_with($wWinDigits, $qWinDigits) || str_starts_with($qWinDigits, $wWinDigits)))) {
                            continue;
                        }
                    }

                    similar_text($qNoSpace, $windowSlice, $winPct);
                    $winSim = max($winPct / 100.0, self::damerauLevenshteinSimilarity($qNoSpace, $windowSlice));
                    if ($winSim >= $minThresh) {
                        $maxWindowScore = max($maxWindowScore, $winSim);
                    }
                }
            }

            $rawBaseScore = max($avgTokenScore, $maxWindowScore);
        }

        if ($rawBaseScore < $minThresh) {
            return $rawBaseScore;
        }

        return self::applyMatchQualityRanking($rawBaseScore, $qAscii, $nAscii, count($rawTokens), count($nWords), $minThresh, $isAliasVariant);
    }

    /**
     * Adjust ranking score among matched products so primary bodies/flagships outrank compatible accessories.
     */
    protected static function applyMatchQualityRanking(
        float $baseScore,
        string $qAscii,
        string $nAscii,
        int $qWordCount,
        int $nWordCount,
        float $minThresh,
        bool $isAliasVariant
    ): float {
        // Exact equality stays at the very top
        if (!$isAliasVariant && $qAscii === $nAscii) {
            return 1.0;
        }

        $score = $baseScore;
        $queryTokens = array_filter(explode(' ', $qAscii));
        $queryWantsAccessory = count(array_intersect($queryTokens, self::$accessoryIntentWords)) > 0;

        // Detect accessory prepositions (e.g., "SmallRig Half Cage for Sony FX3" vs "Sony FX3 Cinema Camera")
        $isAccessoryForTarget = false;
        if (!$queryWantsAccessory) {
            foreach ([' for ', ' pour ', ' compatible ', ' adapte ', ' with '] as $prep) {
                $prepPos = strpos($nAscii, $prep);
                if ($prepPos !== false) {
                    $beforePrep = substr($nAscii, 0, $prepPos);
                    $afterPrep = substr($nAscii, $prepPos + strlen($prep));
                    if (stripos($afterPrep, $qAscii) !== false || (stripos($beforePrep, $qAscii) === false && $prepPos >= 3)) {
                        $isAccessoryForTarget = true;
                        break;
                    }
                }
            }

            // Also demote if the product title starts with an explicit accessory noun (e.g., "Batterie Sony FX3")
            if (!$isAccessoryForTarget) {
                $firstWord = strtok($nAscii, ' ');
                if ($firstWord && in_array($firstWord, self::$accessoryIntentWords, true)) {
                    $isAccessoryForTarget = true;
                }
            }
        }

        if ($isAccessoryForTarget) {
            return round(max($minThresh, min(0.84, $score * 0.80)), 4);
        }

        if ($isAliasVariant) {
            $score = min(0.95, $score * 0.95);
        } elseif (str_starts_with($nAscii, $qAscii)) {
            $score = min(0.995, max($score, 0.985));
        } elseif ($score >= 0.90) {
            // Reward concise primary product names where search terms cover a large fraction of the title
            $coverage = min(1.0, $qWordCount / max(1, $nWordCount));
            $score = max($score, 0.92 + (0.06 * $coverage));
        }

        return round(min(0.999, $score), 4);
    }

    /**
     * Optimal String Alignment (Damerau-Levenshtein) similarity in [0.0, 1.0] to forgive character transpositions (e.g. snoy -> sony).
     */
    protected static function damerauLevenshteinSimilarity(string $a, string $b): float
    {
        $lenA = strlen($a);
        $lenB = strlen($b);
        if ($lenA === 0 && $lenB === 0) {
            return 1.0;
        }
        if ($lenA === 0 || $lenB === 0) {
            return 0.0;
        }
        if ($a === $b) {
            return 1.0;
        }

        $maxLen = max($lenA, $lenB);
        if (abs($lenA - $lenB) > max(3, (int) ceil($maxLen * 0.45))) {
            return 0.0;
        }
        if ($maxLen > 32) {
            similar_text($a, $b, $pct);
            return $pct / 100.0;
        }

        $d = [];
        for ($i = 0; $i <= $lenA; $i++) {
            $d[$i][0] = $i;
        }
        for ($j = 0; $j <= $lenB; $j++) {
            $d[0][$j] = $j;
        }

        for ($i = 1; $i <= $lenA; $i++) {
            for ($j = 1; $j <= $lenB; $j++) {
                $cost = ($a[$i - 1] === $b[$j - 1]) ? 0 : 1;
                $del = $d[$i - 1][$j] + 1;
                $ins = $d[$i][$j - 1] + 1;
                $sub = $d[$i - 1][$j - 1] + $cost;
                $val = min($del, $ins, $sub);

                if ($i > 1 && $j > 1 && $a[$i - 1] === $b[$j - 2] && $a[$i - 2] === $b[$j - 1]) {
                    $val = min($val, $d[$i - 2][$j - 2] + $cost);
                }

                $d[$i][$j] = $val;
            }
        }

        return max(0.0, 1.0 - ($d[$lenA][$lenB] / $maxLen));
    }

    /**
     * French/English phonetic key for typo tolerance ("microfone" -> "microphone", "cannon" -> "canon").
     */
    protected static function phoneticKey(string $word): string
    {
        $k = strtolower($word);
        $k = str_replace(['ph', 'qu', 'ck', 'eau', 'au', 'y'], ['f', 'k', 'k', 'o', 'o', 'i'], $k);
        $k = preg_replace('/c(?=[eiy])/i', 's', $k);
        $k = str_replace('c', 'k', $k);
        $k = preg_replace('/(.)\1+/', '$1', (string) $k);
        return (string) $k;
    }

    /**
     * Compute matching products scores sorted by relevance, in-stock, flagship sort_order, and featured priority.
     */
    public static function getMatchingProductScores(string $search, float $threshold = 0.70, bool $activeOnly = true): array
    {
        $raw = trim($search);
        if (mb_strlen($raw) === 0) {
            return [];
        }

        $minSim = round($threshold * 100, 1);
        $fileCache = Cache::store('file');
        $cacheVer = \App\Http\Middleware\FastGuestPageCache::getVersion();
        $cacheKey = 'prod_search_name_v8_' . $cacheVer . '_' . ($activeOnly ? 'act_' : 'all_') . $minSim . '_' . md5(mb_strtolower($raw));

        return $fileCache->remember($cacheKey, 600, function () use ($raw, $threshold, $minSim, $activeOnly, $fileCache, $cacheVer) {
            $namesMapKey = 'all_product_names_map_v3_' . $cacheVer . '_' . ($activeOnly ? 'act' : 'all');
            $productMeta = $fileCache->remember($namesMapKey, 1800, function () use ($activeOnly) {
                $q = Product::select(['id', 'name', 'sort_order', 'views_count', 'stock']);
                if ($activeOnly) {
                    $q->where('status', 'active');
                }
                return $q->get()->keyBy('id')->map(fn($p) => [
                    'name' => (string) $p->name,
                    'sort_order' => (int) ($p->sort_order ?? 0),
                    'views_count' => (int) ($p->views_count ?? 0),
                    'stock' => (int) ($p->stock ?? 0),
                ])->all();
            });

            $candidates = [];
            foreach ($productMeta as $prodId => $meta) {
                $score = self::calculateNameMatchScore($raw, $meta['name'], $minSim);
                if ($score >= $threshold) {
                    $candidates[$prodId] = [
                        'score' => $score,
                        'in_stock' => $meta['stock'] > 0 ? 1 : 0,
                        'has_sort' => $meta['sort_order'] > 0 ? 1 : 0,
                        'sort_order' => $meta['sort_order'],
                        'views_count' => $meta['views_count'],
                    ];
                }
            }

            // Sort: relevance tier (0.04 buckets), in-stock, custom flagship sort_order, popularity (views_count), exact score desc
            uasort($candidates, function ($a, $b) {
                $bucketA = (int) round($a['score'] * 25);
                $bucketB = (int) round($b['score'] * 25);
                if ($bucketA !== $bucketB) {
                    return $bucketB <=> $bucketA;
                }
                if ($a['in_stock'] !== $b['in_stock']) {
                    return $b['in_stock'] <=> $a['in_stock'];
                }
                if ($a['has_sort'] !== $b['has_sort']) {
                    return $b['has_sort'] <=> $a['has_sort'];
                }
                if ($a['has_sort'] === 1 && $a['sort_order'] !== $b['sort_order']) {
                    return $a['sort_order'] <=> $b['sort_order'];
                }
                if ($a['views_count'] !== $b['views_count']) {
                    return $b['views_count'] <=> $a['views_count'];
                }
                return $b['score'] <=> $a['score'];
            });

            $matched = [];
            foreach ($candidates as $id => $row) {
                $matched[$id] = $row['score'];
            }

            return $matched;
        });
    }

    /**
     * Get IDs of products matching a query across SKU (code), name (typos/aliases/transpositions), variants SKU, ID and brand.
     * Ordered by relevance: Exact/prefix SKU matches first, then primary/flagship name scores, accessories below bodies, then Tier 2 recovery.
     *
     * @param string $search
     * @param bool $activeOnly
     * @return array
     */
    public static function getSearchProductIds(string $search, bool $activeOnly = true): array
    {
        $clean = trim($search);
        if (mb_strlen($clean) === 0) {
            return [];
        }

        // 1. Check SKU, Variant SKU, and numeric ID (#...)
        $qSku = Product::query();
        if ($activeOnly) {
            $qSku->where('products.status', 'active');
        }

        $skuMatches = $qSku->where(function ($q) use ($clean) {
            $q->where('products.sku', 'like', "%{$clean}%")
              ->orWhereHas('variants', fn($v) => $v->where('sku', 'like', "%{$clean}%"));
            $num = ltrim($clean, '#');
            if (is_numeric($num)) {
                $q->orWhere('products.id', (int) $num);
            }
        })
        ->orderByRaw("CASE WHEN products.sku = ? THEN 0 WHEN products.sku LIKE ? THEN 1 ELSE 2 END", [$clean, "{$clean}%"])
        ->orderByRaw("CASE WHEN products.stock > 0 THEN 1 ELSE 0 END DESC")
        ->pluck('products.id')
        ->all();

        // 2. Fuzzy + Typo-Tolerant + Canonical Alias Name Match (>= 70% threshold)
        $nameScores = self::getMatchingProductScores($clean, 0.70, $activeOnly);
        $nameIds = array_keys($nameScores);

        // 3. Direct Name / Brand LIKE matches
        $qLike = Product::query();
        if ($activeOnly) {
            $qLike->where('products.status', 'active');
        }
        $likeMatches = $qLike->where(function ($q) use ($clean) {
            $q->where('products.name', 'like', "%{$clean}%")
              ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$clean}%"));
        })
        ->orderByRaw("CASE WHEN products.stock > 0 THEN 1 ELSE 0 END DESC")
        ->orderByRaw("CASE WHEN products.sort_order > 0 THEN 0 ELSE 1 END ASC")
        ->orderBy('products.sort_order', 'asc')
        ->take(50)
        ->pluck('products.id')
        ->all();

        $tier1Ids = array_values(array_unique(array_merge($skuMatches, $nameIds, $likeMatches)));
        if (!empty($tier1Ids)) {
            return $tier1Ids;
        }

        // 4. Tier 2 Recovery (Alternate Product Names / Synonyms / Severe Multi-Word Typos)
        // Executed ONLY when Tier 1 returns 0 results AND query has <= 4 words so multi-word noise queries return 0.
        $norm = self::normalizeQuery($clean);
        $tokenCount = count($norm['tokens']);
        if ($tokenCount === 0 || $tokenCount > 4) {
            return [];
        }

        $tier2Ids = [];

        // 4A. Direct + fuzzy synonym expansion via $synonyms map
        $candidateSynonyms = $norm['synonyms'];
        if (empty($candidateSynonyms)) {
            foreach ($norm['tokens'] as $tok) {
                $tokAscii = Str::ascii($tok);
                if (strlen($tokAscii) < 4) {
                    continue;
                }
                foreach (self::$synonyms as $synKey => $synValues) {
                    if (self::damerauLevenshteinSimilarity($tokAscii, Str::ascii($synKey)) >= 0.74) {
                        foreach ($synValues as $sv) {
                            if (!in_array($sv, $candidateSynonyms, true)) {
                                $candidateSynonyms[] = $sv;
                            }
                        }
                    }
                }
            }
        }

        foreach (array_slice($candidateSynonyms, 0, 5) as $synTerm) {
            $synScores = self::getMatchingProductScores($synTerm, 0.75, $activeOnly);
            if (!empty($synScores)) {
                $tier2Ids = array_merge($tier2Ids, array_slice(array_keys($synScores), 0, 20));
            }
        }

        // 4B. Fuzzy brand name recovery (e.g. "snoy", "godoox", "smalrig")
        if (empty($tier2Ids) && $tokenCount <= 2) {
            $brands = Brand::select(['id', 'name'])->get();
            $matchedBrandIds = [];
            foreach ($norm['tokens'] as $tok) {
                $tokAscii = Str::ascii($tok);
                if (strlen($tokAscii) < 3) {
                    continue;
                }
                foreach ($brands as $b) {
                    $bAscii = strtolower(Str::ascii((string) $b->name));
                    if (self::damerauLevenshteinSimilarity($tokAscii, $bAscii) >= 0.72) {
                        $matchedBrandIds[] = $b->id;
                    }
                }
            }
            if (!empty($matchedBrandIds)) {
                $qBrand = Product::whereIn('brand_id', array_unique($matchedBrandIds));
                if ($activeOnly) {
                    $qBrand->where('status', 'active');
                }
                $tier2Ids = $qBrand->orderByRaw("CASE WHEN stock > 0 THEN 1 ELSE 0 END DESC")
                    ->orderByRaw("CASE WHEN sort_order > 0 THEN 0 ELSE 1 END ASC")
                    ->orderBy('sort_order', 'asc')
                    ->take(40)
                    ->pluck('id')
                    ->all();
            }
        }

        // 4C. Relaxed double-typo name threshold (>= 0.56) as final recovery on short queries
        if (empty($tier2Ids)) {
            $relaxedScores = self::getMatchingProductScores($clean, 0.56, $activeOnly);
            if (!empty($relaxedScores)) {
                $tier2Ids = array_slice(array_keys($relaxedScores), 0, 24);
            }
        }

        return array_values(array_unique($tier2Ids));
    }

    /**
     * Suggest closest matching product term when a user query has typos or matches via alternate names.
     */
    public static function getSearchMeta(string $search): array
    {
        $clean = trim($search);
        if ($clean === '') {
            return ['did_you_mean' => null, 'matched_count' => 0];
        }

        $directScores = self::getMatchingProductScores($clean, 0.70, true);
        if (!empty($directScores)) {
            return ['did_you_mean' => null, 'matched_count' => count($directScores)];
        }

        $fallbackIds = self::getSearchProductIds($clean, true);
        if (empty($fallbackIds)) {
            return ['did_you_mean' => null, 'matched_count' => 0];
        }

        $topProduct = Product::select(['id', 'name'])->find($fallbackIds[0]);
        return [
            'did_you_mean' => $topProduct?->name,
            'matched_count' => count($fallbackIds),
        ];
    }

    /**
     * Build search query for products supporting Product Name (fuzzy), Product SKU, Variant SKU, and Brand.
     */
    public static function buildQuery(
        ?string $search = '',
        ?string $categorySlug = null,
        ?string $brandSlug = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $sort = null
    ): Builder {
        $cleanSearch = trim((string) ($search ?? ''));
        $query = Product::where('products.status', 'active');

        // 1. Category Filter
        if (!empty($categorySlug)) {
            if ($categorySlug === 'occasion') {
                $query->where(function ($q) {
                    $q->whereHas('categories', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhereHas('productCategory', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhere('products.name', 'like', '%(occasion)%')
                      ->orWhere('products.name', 'like', '%occasion%');
                });
            } else {
                $query->where(function ($q) use ($categorySlug) {
                    $q->whereHas('categories', function ($sub) use ($categorySlug) {
                        $sub->where('slug', $categorySlug);
                    })->orWhereHas('productCategory', function ($sub) use ($categorySlug) {
                        $sub->where('slug', $categorySlug);
                    });
                });
            }
        }

        // 2. Brand Filter
        if (!empty($brandSlug)) {
            $query->whereHas('brand', function ($b) use ($brandSlug) {
                $b->where('slug', $brandSlug);
            });
        }

        // 3. Price Filter (matches actual selling price: sale_price if on promo, otherwise regular price)
        if ($minPrice !== null && $minPrice !== '') {
            $query->whereRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) >= ?', [(float) $minPrice]);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->whereRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) <= ?', [(float) $maxPrice]);
        }

        // Primary Sort: In-stock products (stock > 0) always appear first, out-of-stock products appear last
        $query->orderByRaw('CASE WHEN products.stock > 0 THEN 1 ELSE 0 END DESC');

        // 4. Search Matching: Product Name (fuzzy), Product SKU, Variant SKU, ID, and Brand
        $cleanSearch = trim($search);
        if (mb_strlen($cleanSearch) > 0) {
            $matchedIds = self::getSearchProductIds($cleanSearch, true);

            if (empty($matchedIds)) {
                // Return zero results if no product reaches match on name or SKU
                $query->whereRaw('1 = 0');
                return $query;
            }

            $idList = implode(',', $matchedIds);
            $query->whereIn('products.id', $matchedIds);

            // Sorting
            switch ($sort) {
                case 'price_asc':
                    $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) ASC');
                    break;
                case 'price_desc':
                    $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) DESC');
                    break;
                case 'newest':
                    $query->orderByDesc('products.created_at');
                    break;
                default:
                    // Primary: Highest relevance match (exact SKU, primary product before accessories) first,
                    // Secondary: custom merchandising sort_order, featured status, and stock quantity descending
                    $query->orderByRaw("FIELD(products.id, {$idList})")
                          ->orderByRaw('CASE WHEN products.sort_order > 0 THEN 0 ELSE 1 END ASC')
                          ->orderBy('products.sort_order', 'asc')
                          ->orderByDesc('products.views_count')
                          ->orderByDesc('products.stock');
                    break;
            }

            return $query;
        }

        // No search query: standard sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) DESC');
                break;
            case 'newest':
            default:
                // Custom product order: products with sort_order > 0 appear first (1, 2, 3...)
                // followed by items with sort_order = 0 sorted by newest created_at desc
                $query->orderByRaw('CASE WHEN products.sort_order > 0 THEN 0 ELSE 1 END ASC')
                      ->orderBy('products.sort_order', 'asc')
                      ->orderBy('products.created_at', 'desc');
                break;
        }

        return $query;
    }

    /**
     * Get matching categories for a search query (based on category name/slug).
     */
    public static function getMatchingCategories(?string $rawQuery = '', int $limit = 4)
    {
        $queryString = trim((string) ($rawQuery ?? ''));
        if (mb_strlen($queryString) === 0) {
            return collect();
        }

        $norm = self::normalizeQuery($queryString);
        $clean = $norm['clean'];
        $tokens = $norm['tokens'];

        if (mb_strlen($clean) === 0) {
            return collect();
        }

        return Category::where('status', 'active')
            ->where(function ($q) use ($clean, $tokens) {
                $q->where('name', 'like', "%{$clean}%")
                  ->orWhere('slug', 'like', "%{$clean}%");

                foreach ($tokens as $token) {
                    if (mb_strlen($token) >= 3) {
                        $q->orWhere('name', 'like', "%{$token}%");
                    }
                }
            })
            ->withCount(['products' => function ($pq) {
                $pq->where('status', 'active');
            }])
            ->having('products_count', '>', 0)
            ->take($limit)
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'count' => $cat->products_count,
                    'url' => route('shop.index', ['category' => $cat->slug], false),
                ];
            });
    }

    /**
     * Perform live search for navbar / mobile search modal.
     */
    public static function liveSearch(?string $rawQuery = '', int $limit = 8): array
    {
        $raw = trim((string) ($rawQuery ?? ''));
        if (mb_strlen($raw) === 0) {
            return [
                'success' => true,
                'query' => '',
                'count' => 0,
                'categories' => [],
                'products' => [],
                'popular' => self::$popularSearches,
                'all_url' => route('shop.index', [], false),
            ];
        }

        $cacheVer = \App\Http\Middleware\FastGuestPageCache::getVersion();
        $cacheKey = 'live_search_v6_' . $cacheVer . '_' . md5(mb_strtolower($raw));
        return Cache::store('file')->remember($cacheKey, 600, function () use ($raw, $limit) {
            $categories = self::getMatchingCategories($raw, 3);
            $query = self::buildQuery($raw);
            $meta = self::getSearchMeta($raw);

            $totalCount = $query->count();
            $products = $query->with(['productCategory', 'images'])
                ->take($limit)
                ->get();

            $results = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku ?: '',
                    'category' => $product->category_name ?? 'Matériel Caméra',
                    'price' => $product->formatted_price,
                    'is_sale' => $product->isOnSale(),
                    'sale_price' => $product->isOnSale() ? $product->formatted_sale_price : null,
                    'discount' => $product->discount_percentage,
                    'in_stock' => $product->isInStock(),
                    'image' => $product->card_thumbnail,
                    'url' => route('shop.show', $product->slug ?: $product->id, false),
                ];
            });

            return [
                'success' => true,
                'query' => $raw,
                'did_you_mean' => $meta['did_you_mean'] ?? null,
                'count' => $totalCount,
                'categories' => $categories->toArray(),
                'products' => $results->toArray(),
                'popular' => self::$popularSearches,
                'all_url' => route('shop.index', ['q' => $raw], false),
            ];
        });
    }

    /**
     * Get popular search suggestions.
     */
    public static function getPopularSearches(): array
    {
        return self::$popularSearches;
    }

    /**
     * Get brands that have active products matching the current category filter and/or search query.
     * Returns active brands with dynamic product counts, sorted by products_count descending.
     *
     * @param string|null $categorySlug
     * @param string $search
     * @return \Illuminate\Support\Collection
     */
    public static function getSmartBrands(?string $categorySlug = null, ?string $search = ''): Collection
    {
        $cleanSearch = trim((string) ($search ?? ''));
        $cacheVer = \App\Http\Middleware\FastGuestPageCache::getVersion();
        $cacheKey = 'smart_brands_v2_' . $cacheVer . '_' . md5((string) $categorySlug . '|' . mb_strtolower($cleanSearch));

        return Cache::store('file')->remember($cacheKey, 1800, function () use ($categorySlug, $cleanSearch) {
            $productQuery = Product::where('products.status', 'active')
                ->whereNotNull('products.brand_id');

            // Apply category filter if provided
            if (!empty($categorySlug)) {
                if ($categorySlug === 'occasion') {
                    $productQuery->where(function ($q) {
                        $q->whereHas('categories', function ($c) {
                            $c->where('slug', 'occasion');
                        })->orWhereHas('productCategory', function ($c) {
                            $c->where('slug', 'occasion');
                        })->orWhere('products.name', 'like', '%(occasion)%')
                          ->orWhere('products.name', 'like', '%occasion%');
                    });
                } else {
                    $productQuery->where(function ($q) use ($categorySlug) {
                        $q->whereHas('categories', function ($sub) use ($categorySlug) {
                            $sub->where('slug', $categorySlug);
                        })->orWhereHas('productCategory', function ($sub) use ($categorySlug) {
                            $sub->where('slug', $categorySlug);
                        });
                    });
                }
            }

            // Apply search filter if provided (name, SKU, brand)
            if (mb_strlen($cleanSearch) > 0) {
                $matchedIds = self::getSearchProductIds($cleanSearch, true);

                if (empty($matchedIds)) {
                    return collect();
                }

                $productQuery->whereIn('products.id', $matchedIds);
            }

            // Count products per brand
            $brandCounts = $productQuery->select('products.brand_id')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('products.brand_id')
                ->pluck('count', 'products.brand_id');

            if ($brandCounts->isEmpty()) {
                return collect();
            }

            $brands = Brand::where('status', 'active')
                ->whereIn('id', $brandCounts->keys())
                ->get()
                ->map(function ($brand) use ($brandCounts) {
                    $brand->products_count = (int) ($brandCounts[$brand->id] ?? 0);
                    return $brand;
                });

            return $brands->sort(function ($a, $b) {
                $orderA = (int) ($a->sort_order ?? 0);
                $orderB = (int) ($b->sort_order ?? 0);
                if ($orderA !== $orderB) {
                    return $orderA <=> $orderB;
                }
                return strcasecmp($a->name, $b->name);
            })->values();
        });
    }
}
