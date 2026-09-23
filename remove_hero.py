import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/cart/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Define the pattern to replace the header block
pattern = re.compile(r'<header class="sp-page-hero".*?</header>\s*', re.DOTALL)

# Add a simpler title above the cart instead of the hero
new_header = """<div class="container mt-5 mb-4">
    <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.8rem, 3vw, 2.4rem); font-weight: 700; color: #0c261e; margin: 0;">Mon Panier</h1>
</div>
"""

new_content = pattern.sub(new_header, content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)
print("Hero removed successfully")
