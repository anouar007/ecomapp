import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/checkout/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove Hero Section
pattern_hero = re.compile(r'<header class="sp-page-hero".*?</header>', re.DOTALL)
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
content = pattern_hero.sub(new_hero, content)

# 2. Update the left side (Form and Payment) and right side (Order Summary) layout
pattern_body = re.compile(r'<div class="py-5" style=".*?<div class="container">.*?<div class="row">.*?<div class="col-lg-7">.*?<div class="card border-0 shadow-sm rounded-4 mb-4">.*?<div class="card-body p-4">.*?<h4 class="fw-bold mb-4">Informations de livraison</h4>(.*?)</form>.*?</div>.*?</div>.*?<div class="card border-0 shadow-sm rounded-4">.*?<h4 class="fw-bold mb-4">Paiement</h4>(.*?)</div>.*?</div>.*?</div>.*?<div class="col-lg-5">(.*?)<div class="text-center mt-4">', re.DOTALL)

# Let's use a simpler string replace approach to guarantee we hit the sections since regex on a huge HTML block is risky.
