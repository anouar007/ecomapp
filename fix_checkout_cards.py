import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/checkout/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Add a style block for the form
style_block = """
@section('content')
<style>
    /* Premium Form Styling */
    .premium-input:focus {
        background-color: #ffffff !important;
        border-color: #e2ad50 !important;
        box-shadow: 0 0 0 4px rgba(226, 173, 80, 0.15) !important;
        outline: none;
    }
    .premium-form-card {
        background: #ffffff;
        border: 1px solid rgba(12, 38, 30, 0.08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(12, 38, 30, 0.03);
    }
    
    /* Premium Product Cards for Checkout */
    .checkout-product-card {
        background: #ffffff;
        border: 1px solid rgba(12, 38, 30, 0.06);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .checkout-product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }
</style>
"""
content = content.replace("@section('content')", style_block)

# Apply premium classes to form
content = content.replace('class="form-control py-3"', 'class="form-control py-3 premium-input"')
content = content.replace('class="form-select py-3"', 'class="form-select py-3 premium-input"')
content = content.replace('class="card border-0 shadow-sm rounded-4 mb-4"', 'class="premium-form-card mb-4"')
content = content.replace('class="card border-0 shadow-sm rounded-4"', 'class="premium-form-card"')

# Rewrite product cards in summary
old_summary = re.compile(r'@foreach\(\$cart as \$id => \$details\).*?@endforeach', re.DOTALL)
new_summary = """@foreach($cart as $id => $details)
                        <div class="checkout-product-card d-flex align-items-center">
                            <div class="me-3 position-relative flex-shrink-0">
                                @if($details['image'])
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 70px; height: 70px;">
                                @else
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: #fbf9f4;">
                                    <i class="fas fa-image text-muted opacity-25"></i>
                                </div>
                                @endif
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="background: #e2ad50; color: #fff; width: 22px; height: 22px; font-size: 0.75rem; border: 2px solid #fff; font-weight: 700;">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0 pr-2">
                                <h6 class="fw-bold mb-1 text-truncate" style="color: #0c261e; font-size: 0.95rem; font-family: 'Playfair Display', serif;">{{ $details['name'] }}</h6>
                                <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ $details['category_name'] ?? 'Soin Naturel' }}</p>
                            </div>
                            <div class="fw-bold flex-shrink-0" style="color: #0c261e; font-size: 1.05rem;">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach"""

content = old_summary.sub(new_summary, content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Checkout cards upgraded")
