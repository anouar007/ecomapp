import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/checkout/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove Hero
hero_pattern = re.compile(r'<header class="sp-page-hero".*?</header>', re.DOTALL)
new_hero = """<div class="container mt-5 mb-4">
    <nav class="sp-breadcrumb mb-3" aria-label="Fil d'Ariane" style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
      <a href="{{ route('home') }}" style="color: #6b7a72; text-decoration: none;">Accueil</a>
      <span class="sep" style="color: #c28d32;">/</span>
      <a href="{{ route('cart.index') }}" style="color: #6b7a72; text-decoration: none;">Panier</a>
      <span class="sep" style="color: #c28d32;">/</span>
      <span class="current" style="color: #0c261e; font-weight: 600;">Commande</span>
    </nav>
    <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.8rem, 3vw, 2.4rem); font-weight: 700; color: #0c261e; margin: 0;">Finaliser ma Commande</h1>
</div>"""
content = hero_pattern.sub(new_hero, content)

# 2. Re-write the form inputs to look more premium
content = content.replace('class="form-control bg-light border-0 py-2"', 'class="form-control py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;"')
content = content.replace('<label class="form-label small fw-bold text-muted">', '<label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">')
content = content.replace('class="form-select bg-light border-0 py-2"', 'class="form-select py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;"')

# 3. Fix the Order Summary Layout (Right Column)
old_summary_pattern = re.compile(r'<div class="card border-0 shadow-sm rounded-4">.*?<div class="card-header bg-white p-4 border-bottom-0">.*?<h5 class="fw-bold m-0">Récapitulatif de la commande</h5>.*?</div>.*?<div class="card-body p-4 pt-0">.*?@foreach\(\$cart as \$id => \$details\).*?</div>.*?@endforeach', re.DOTALL)

new_summary = """<div class="card border-0 shadow-sm rounded-4" style="background: #ffffff; border: 1px solid rgba(12,38,30,0.05) !important;">
                    <div class="card-header bg-white p-4 border-bottom-0 rounded-top-4">
                        <h5 class="fw-bold m-0" style="font-family: 'Playfair Display', serif; color: #0c261e; font-size: 1.3rem;">Récapitulatif</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @foreach($cart as $id => $details)
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(12,38,30,0.05) !important;">
                            <div class="me-3 position-relative flex-shrink-0">
                                @if($details['image'])
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 shadow-sm object-fit-cover" style="width: 75px; height: 75px;">
                                @else
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 75px; height: 75px; background: #fbf9f4;">
                                    <i class="fas fa-image text-muted opacity-25"></i>
                                </div>
                                @endif
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="background: #0c261e; width: 22px; height: 22px; font-size: 0.75rem; border: 2px solid #fff;">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0 pr-2">
                                <h6 class="fw-bold mb-1 text-truncate" style="color: #0c261e; font-size: 0.95rem;">{{ $details['name'] }}</h6>
                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">{{ $details['category_name'] ?? 'Soin Naturel' }}</p>
                            </div>
                            <div class="fw-bold flex-shrink-0" style="color: #c28d32;">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach"""

content = old_summary_pattern.sub(new_summary, content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Checkout page fixed")
