import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/cart/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Define the pattern to replace the whole left side column
pattern = re.compile(r'<div class="col-lg-8">.*?<div class="col-lg-4">', re.DOTALL)

new_cart_html = """<div class="col-lg-8">
                <div class="cart-items-wrapper d-flex flex-column gap-3">
                    @php $total = 0; @endphp
                    @foreach(session('cart') as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp
                    <div class="card border-0 rounded-4 shadow-sm" style="background: #ffffff; padding: 20px;" id="cart-row-{{ $id }}">
                        <div class="row align-items-center g-3">
                            <!-- Image & Name -->
                            <div class="col-12 col-md-6 d-flex align-items-center">
                                <div class="flex-shrink-0 me-3 position-relative">
                                    @if($details['image'])
                                    <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 shadow-sm object-fit-cover" style="width: 85px; height: 85px;">
                                    @else
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 85px; height: 85px; background: #fbf9f4;">
                                        <i class="fas fa-image fa-2x opacity-50"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-truncate" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.1rem; line-height: 1.3;">
                                        <a href="{{ route('shop.show', $id) }}" class="text-decoration-none" style="color: #0c261e;">{{ $details['name'] }}</a>
                                    </h6>
                                    <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">{{ $details['category_name'] ?? 'Soin Naturel' }}</p>
                                    
                                    <!-- Price (Mobile only) -->
                                    <div class="d-md-none mt-2">
                                        <span class="fw-bold" style="color: #c28d32; font-size: 1.1rem;">{{ currency($details['price']) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price (Desktop only) -->
                            <div class="col-md-2 d-none d-md-block text-center">
                                <span class="fw-bold" style="color: #c28d32; font-size: 1.1rem;">{{ currency($details['price']) }}</span>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="col-6 col-md-3 d-flex justify-content-start justify-content-md-center align-items-center">
                                <div class="quantity-control rounded-pill d-flex align-items-center px-1 py-1" style="background: #fbf9f4; border: 1.5px solid rgba(12,38,30,0.08); width: 110px;">
                                    <button class="btn btn-sm btn-link text-decoration-none p-0 w-100 h-100 d-flex align-items-center justify-content-center" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})" style="color: #0c261e !important;">
                                        <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly style="color: #0c261e; font-size: 0.95rem;">
                                    <button class="btn btn-sm btn-link text-decoration-none p-0 w-100 h-100 d-flex align-items-center justify-content-center" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})" style="color: #0c261e !important;">
                                        <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Remove -->
                            <div class="col-6 col-md-1 d-flex justify-content-end align-items-center">
                                <button class="btn btn-sm p-2 rounded-circle transition-all" onclick="removeItem({{ $id }})" title="Supprimer" style="background: rgba(220,38,38,0.08); color: #dc2626; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">"""

new_content = pattern.sub(new_cart_html, content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)
print("Cart page updated successfully 2")
