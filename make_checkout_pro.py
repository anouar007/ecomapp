import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/public/css/sunpure-theme.css'
with open(filepath, 'a', encoding='utf-8') as f:
    f.write("""
/* ---- PRO MOBILE CHECKOUT ---- */
@media (max-width: 768px) {
  /* Sticky checkout button */
  .mobile-sticky-checkout {
    position: fixed !important;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #ffffff;
    padding: 16px 20px;
    box-shadow: 0 -4px 20px rgba(12, 38, 30, 0.1);
    z-index: 1000;
    border-top: 1px solid rgba(12, 38, 30, 0.05);
  }
  .mobile-sticky-checkout .btn {
    border-radius: 100px !important;
    height: 54px;
    font-size: 1.05rem;
    letter-spacing: 0.02em;
  }
  /* Push body up so sticky footer doesn't cover content */
  body.checkout-page {
    padding-bottom: 90px;
  }
  
  /* Hide whatsapp widget on checkout to avoid overlapping the button */
  body.checkout-page .sp-wa-widget {
    display: none !important;
  }
}
""")
print("CSS updated for pro mobile checkout")
