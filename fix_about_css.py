import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/public/css/sunpure-theme.css'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update the background of the main section
old_css_bg = """.sp-about-section {
  position: relative;
  min-height: 520px;
  display: flex;
  align-items: center;
  background-color: #2b2319;
  background-image: 
    linear-gradient(90deg, rgba(12, 38, 30, 0.94) 0%, rgba(12, 38, 30, 0.76) 42%, rgba(12, 38, 30, 0.25) 75%, transparent 100%),
    url('../assets/images/about_coop_exact.jpg');
  background-size: cover;
  background-position: center right;
  background-repeat: no-repeat;
  padding: 100px 0;
  overflow: hidden;
}"""
new_css_bg = """.sp-about-section {
  position: relative;
  min-height: 520px;
  display: flex;
  align-items: center;
  background-color: #0c261e;
  padding: 100px 0;
  overflow: hidden;
}"""
content = content.replace(old_css_bg, new_css_bg)

# 2. Update the overlay (remove gradient)
old_overlay = """.sp-about-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(247, 244, 237, 0.95) 0%, rgba(247, 244, 237, 0.88) 32%, rgba(247, 244, 237, 0.25) 54%, transparent 100%);
  pointer-events: none;
  z-index: 2;
}"""
new_overlay = """.sp-about-overlay {
  display: none;
}"""
content = content.replace(old_overlay, new_overlay)

# 3. Update text colors
old_title = """.sp-about-title {
  font-family: var(--sp-font-serif);
  font-size: clamp(2.2rem, 3.2vw, 2.85rem);
  font-weight: 600;
  color: var(--sp-dark-green);
  line-height: 1.15;
  margin: 12px 0 20px 0;
}"""
new_title = """.sp-about-title {
  font-family: var(--sp-font-serif);
  font-size: clamp(2.2rem, 3.2vw, 2.85rem);
  font-weight: 600;
  color: #ffffff;
  line-height: 1.15;
  margin: 12px 0 20px 0;
}"""
content = content.replace(old_title, new_title)

old_text = """.sp-about-text {
  font-size: 0.96rem;
  line-height: 1.75;
  color: #2d4239;
  margin-bottom: 28px;
}

.sp-about-text strong {
  color: var(--sp-dark-green);
  font-weight: 700;
}"""
new_text = """.sp-about-text {
  font-size: 0.96rem;
  line-height: 1.75;
  color: rgba(255, 255, 255, 0.85);
  margin-bottom: 28px;
}

.sp-about-text strong {
  color: #ffffff;
  font-weight: 700;
}"""
content = content.replace(old_text, new_text)

# Write back
with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("CSS updated")
