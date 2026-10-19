# WINA SHOP — Magasin de Référence Matériel Photo, Vidéo, Cinéma & Drones au Maroc

> WINA SHOP (https://anouar.top) est le spécialiste et distributeur au Maroc de matériel photo, vidéo professionnelle, caméras cinéma, éclairage studio, audio et accessoires. Showroom situé à Casablanca avec livraison express dans tout le Maroc.

## Informations sur l'Entreprise & Coordonnées
- Nom commercial : {{ setting('app_name', 'WINA SHOP') }}
- Site officiel : {{ url('/') }}
- Boutique en ligne : {{ route('shop.index') }}
- Showroom & Siège : {{ setting('company_address', '01 Rue 102, Hay Wiam, Al Oulfa') }}, {{ setting('company_city', 'Casablanca') }}, Maroc
- Téléphone & WhatsApp : {{ setting('company_phone', '+212 629-035777') }}
- Email : {{ setting('company_email', 'contact@winashop.com') }}
- Facturation : Factures officielles avec ICE pour entreprises, auto-entrepreneurs et devis proforma éligibles aux programmes INDH

## Marques Principales Distribuées
- **Caméras & Boîtiers** : Sony (Alpha 7 IV, FX3, FX6, A7S III), Canon (EOS R5, R6, C70), Nikon, Fujifilm, Blackmagic Design
- **Drones & Stabilisateurs** : DJI (Mavic, Mini, Ronin RS3, RS4, Osmo Pocket), Zhiyun
- **Optiques & Objectifs** : Sony G Master, Canon RF, Sigma Art, Tamron
- **Éclairage Studio & LED** : Godox, Aputure, Amaran, Nanlite
- **Prise de Son & Micros** : RØDE (Wireless PRO, Wireless ME, VideoMic), Hollyland (Lark M2, Max)
- **Cartes & Accessoires** : Sony CFexpress Type A & B, SanDisk Extreme Pro, Lexar Professional, SmallRig cages & rigs, Manfrotto trépieds

## Services & Garanties au Maroc
- **Livraison Express 24h-48h** : Casablanca, Rabat, Marrakech, Tanger, Fès, Agadir, Oujda, Meknès, Kénitra, Tétouan, Laâyoune et toutes les villes du Maroc.
- **Paiement Sécurisé** : Paiement en espèces à la livraison (Cash on Delivery) après vérification ou virement bancaire.
- **Garantie Constructeur** : Tous les produits neufs bénéficient d'une garantie constructeur officielle avec service après-vente dédié.
- **Matériel d'Occasion Vérifié** : Produits de seconde main rigoureusement testés et garantis.

## Catégories de Produits
@foreach($categories as $category)
- [{{ $category->name }}]({{ route('shop.index', ['category' => $category->slug]) }})
@endforeach

## Catalogue des Produits Actifs
@foreach($products as $product)
@php
    $prodUrl = route('shop.show', $product->slug ?: $product->id);
    $price = number_format($product->final_price, 2, '.', ' ') . ' ' . setting('currency_code', 'MAD');
    $stock = $product->isInStock() ? 'En stock' : 'Rupture de stock';
    $cond = str_contains(strtolower($product->name), 'occasion') ? 'Occasion' : 'Neuf sous emballage';
@endphp
- [{{ $product->name }}]({{ $prodUrl }}) : {{ $price }} | {{ $stock }} | {{ $cond }}
@endforeach

## Recherche & Requêtes Fréquentes
Ce site répond aux recherches suivantes en Français, Arabe et Anglais :
- *Français* : Où acheter caméra Sony au Maroc, Prix Sony A7 IV Casablanca, Matériel vidéo INDH Maroc, Devis proforma caméra, Boutique photo Casablanca Al Oulfa, Drone DJI Maroc prix.
- *Arabe (العربية)* : شراء كاميرا سوني في المغرب, سعر معدات التصوير الفوتوغرافي الدار البيضاء, درون دي جي اي المغرب, أفضل متجر كاميرات بالتقسيط أو الدفع عند الاستلام, كاميرات كانون وسوني أصلية مع الضمان.
- *English* : Buy Sony camera in Morocco, Best camera shop Casablanca, DJI drones Morocco price, Cinema equipment proforma invoice Morocco.
- *Darija* : Chri camera f lmaghrib, taman camera canon f casa, materiel video indh proforma casablanca.
