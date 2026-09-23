import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/home.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# The old section tag
old_tag = """<section class="sp-about-section" id="a-propos" style="background-image: linear-gradient(90deg, rgba(12, 38, 30, 0.94) 0%, rgba(12, 38, 30, 0.76) 42%, rgba(12, 38, 30, 0.25) 75%, transparent 100%), url('{{ asset('assets/images/about_coop_exact.jpg') }}'); background-size: cover; background-position: center right; background-repeat: no-repeat;">"""

# The new section tag (solid green)
new_tag = """<section class="sp-about-section" id="a-propos" style="background-color: #0c261e;">"""

if old_tag in content:
    content = content.replace(old_tag, new_tag)
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print("SUCCESS: Desktop about section updated to solid background")
else:
    print("NOT FOUND: Attempting regex replacement")
    pattern = re.compile(r'<section class="sp-about-section" id="a-propos" style="[^"]+">')
    content, count = pattern.subn(new_tag, content)
    if count > 0:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"SUCCESS: Replaced via regex ({count} times)")
    else:
        print("NOT FOUND via regex either")
