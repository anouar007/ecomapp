import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/public/css/sunpure-theme.css'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Define the pattern to replace the mobile about section
old_css = """/* ---- ABOUT SECTION ---- */
@media (max-width: 768px) {
  .sp-about-section {
    background-image:
      linear-gradient(180deg, rgba(12,38,30,0.92) 0%, rgba(12,38,30,0.85) 100%),
      url('../assets/images/about_coop_exact.jpg') !important;
    background-position: center center !important;
  }
  .sp-about-container {
    padding: 48px 18px;
    flex-direction: column;
    gap: 28px;
  }
  .sp-about-title {
    font-size: clamp(1.8rem, 7vw, 2.4rem);
    line-height: 1.15;
  }
  .sp-about-text {
    font-size: 0.92rem;
    line-height: 1.65;
  }
  .sp-btn-outline {
    padding: 11px 22px;
    font-size: 0.88rem;
    width: 100%;
    justify-content: center;
  }
  .sp-terroir-badge {
    width: 100%;
    text-align: center;
    align-items: center;
  }
}"""

new_css = """/* ---- ABOUT SECTION ---- */
@media (max-width: 768px) {
  .sp-about-section {
    background-image:
      linear-gradient(180deg, rgba(12,38,30,0.95) 0%, rgba(12,38,30,0.88) 100%),
      url('../assets/images/about_coop_exact.jpg') !important;
    background-position: center center !important;
    padding: 40px 0 !important;
  }
  .sp-about-container {
    padding: 20px 18px;
    flex-direction: column;
    gap: 24px;
    align-items: center;
    text-align: center;
  }
  .sp-about-content-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 100%;
  }
  .sp-section-kicker {
    font-size: 0.75rem;
    letter-spacing: 0.15em;
    margin-bottom: 12px;
  }
  .sp-about-title {
    font-size: clamp(1.7rem, 7vw, 2.2rem);
    line-height: 1.2;
    margin-bottom: 16px;
    text-align: center;
  }
  .sp-about-text {
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 24px;
    text-align: center;
    opacity: 0.9;
  }
  .sp-btn-outline {
    padding: 14px 28px;
    font-size: 0.9rem;
    width: auto;
    min-width: 200px;
    justify-content: center;
    margin-top: 10px;
  }
  .sp-terroir-badge {
    width: 100%;
    max-width: 300px;
    margin: 20px auto 0;
    text-align: center;
    align-items: center;
    padding: 16px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(226,173,80,0.2);
    border-radius: 16px;
    flex-direction: column;
    gap: 12px;
  }
  .sp-terroir-icon {
    margin-right: 0;
  }
  .sp-terroir-text {
    font-size: 0.9rem;
    text-align: center;
  }
  .sp-terroir-line {
    display: none;
  }
}"""

if old_css in content:
    content = content.replace(old_css, new_css)
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print("SUCCESS: Mobile about section updated")
else:
    print("NOT FOUND")
    
