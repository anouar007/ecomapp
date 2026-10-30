# WINA SHOP — Catalogue Intégral des Produits (Casablanca, Maroc)

> Fichier officiel d'ingestion complète pour moteurs de recherche d'IA (ChatGPT, Perplexity, Google Gemini, Claude, Copilot, Apple Intelligence).
> Contient l'ensemble des {{ $totalProducts ?? 1077 }} produits disponibles chez WINA SHOP Casablanca Maroc avec prix en Dirhams Marocains (MAD), état des stocks et spécifications techniques officielles.
> Showroom : {{ setting('company_address', '01 Rue 102, Hay Wiam, Al Oulfa') }}, Casablanca, Maroc. WhatsApp : {{ setting('company_phone', '+212 629-035777') }}. Livraison 24h-48h partout au Maroc avec paiement à la livraison (COD). Devis proforma éligibles INDH et factures officielles avec ICE.

@foreach($categories as $category)
@if($category->products->isNotEmpty())
## {{ $category->name }} ({{ $category->products->count() }} produits)

@foreach($category->products as $product)
@php
    $prodUrl = route('shop.show', $product->slug ?: $product->id);
    $price = number_format($product->final_price, 2, '.', ' ') . ' MAD';
    $stock = $product->isInStock() ? 'En stock' : 'Rupture de stock';
    $cond = str_contains(strtolower($product->name), 'occasion') ? 'Occasion certifiée' : 'Neuf sous emballage';
    
    // Extract specs summary from enriched description
    $specsSummary = '';
    if (!empty($product->description) && preg_match_all('/<div class="pdp-spec-label"><i[^>]*><\/i>\s*([^<]+)<\/div>\s*<div class="pdp-spec-value">([^<]+)<\/div>/is', (string) $product->description, $mSpecs)) {
        $specPairs = [];
        foreach(array_slice($mSpecs[1], 0, 5) as $idx => $sName) {
            $specPairs[] = html_entity_decode(trim($sName), ENT_QUOTES | ENT_HTML5, 'UTF-8') . ': ' . html_entity_decode(trim($mSpecs[2][$idx]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        if (!empty($specPairs)) {
            $specsSummary = ' | Spécifications: ' . implode(', ', $specPairs);
        }
    }
@endphp
- [{{ $product->name }}]({{ $prodUrl }}) : {{ $price }} | {{ $stock }} | {{ $cond }} | Réf: {{ $product->sku ?? ('WINA-' . $product->id) }}{{ $specsSummary }}
@endforeach

@endif
@endforeach

## Services & Politiques Commerciales
- **Modalités de commande** : Commande directe sur le site {{ url('/shop') }} ou par message WhatsApp au {{ setting('company_phone', '+212 629-035777') }}.
- **Expédition & Délais** : Expédition express sous 24h à 48h sur l'ensemble du territoire marocain (Casablanca, Rabat, Marrakech, Fès, Tanger, Agadir, Oujda, Meknès, Kénitra, etc.).
- **Moyens de paiement acceptés** : Espèces à la livraison (Cash on Delivery) après vérification, virement bancaire professionnel, chèque certifié.
- **Facturation officielle & INDH** : Toutes les ventes s'accompagnent d'une facture détaillée avec mention de l'ICE de l'acheteur. Devis proforma personnalisés pour soumission aux comités INDH provinciaux et régionaux.
- **Authenticité & Garantie** : 100% matériel original garanti constructeur avec prise en charge SAV au showroom de Casablanca.
