import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/home.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace inline style of .sp-about-section
old_html = r'<section class="sp-about-section" id="a-propos" style="background-image: linear-gradient\(90deg, rgba\(12, 38, 30, 0.94\) 0%, rgba\(12, 38, 30, 0.76\) 42%, rgba\(12, 38, 30, 0.25\) 75%, transparent 100%\), url\(\'\{\{ asset\(\'assets/images/about_coop_exact\.jpg\'\) \}\}\'\); background-size: cover; background-position: center right; background-repeat: no-repeat;">'
new_html = '<section class="sp-about-section" id="a-propos">'

content = re.sub(old_html, new_html, content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("HTML updated")
