import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/cart/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Define the pattern to replace the whole table-responsive div
pattern = re.compile(r'<div class="table-responsive">.*?</table>\s*</div>', re.DOTALL)

new_cart_html = """<div class="cart-items-wrapper">
                            @php $total = 0; @endphp
                            @foreach(session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity']; @endphp
                            <div class="cart-page-item border-bottom p-4 transition-all hover-bg-light" id="cart-row-{{ $id }}">
                                <div class="row align-items-center g-3">
                                    <!-- Image & Name -->
                                    <div class="col-12 col-md-5 d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3 position-relative">
                                            @if($details['image'])
                                            <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-4 shadow-sm object-fit-cover" style="width: 85px; height: 85px;">
                                            @else
                                            <div class="bg-light rounded-4 d-flex align-items-center justify-content-center text-muted" style="width: 85px; height: 85px;">
                                                <i class="fas fa-image fa-2x opacity-50"></i>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h6 class="fw-bold mb-1 text-truncate" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.1rem;">
                                                <a href="{{ route('shop.show', $id) }}" class="text-decoration-none" style="color: #0c261e;">{{ $details['name'] }}</a>
                                            </h6>
                                            <p class="text-muted small mb-0 text-uppercase ls-1" style="font-size: 0.75rem;">{{ $details['category_name'] ?? 'Soin Naturel' }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Price (Desktop only) -->
                                    <div class="col-md-2 d-none d-md-block text-center">
                                        <span class="fw-bold" style="color: #6b7a72;">{{ currency($details['price']) }}</span>
                                    </div>
                                    
                                    <!-- Quantity -->
                                    <div class="col-6 col-md-3 d-flex justify-content-md-center align-items-center">
                                        <div class="quantity-control rounded-pill d-flex align-items-center px-1 py-1" style="background: #fbf9f4; border: 1.5px solid rgba(12,38,30,0.08); width: 110px;">
                                            <button class="btn btn-sm btn-link text-dark text-decoration-none p-0 w-100 h-100 d-flex align-items-center justify-content-center" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})" style="color: #0c261e !important;">
                                                <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                            </button>
                                            <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly style="color: #0c261e; font-size: 0.95rem;">
                                            <button class="btn btn-sm btn-link text-dark text-decoration-none p-0 w-100 h-100 d-flex align-items-center justify-content-center" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})" style="color: #0c261e !important;">
                                                <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Total & Remove -->
                                    <div class="col-6 col-md-2 d-flex justify-content-end align-items-center gap-3">
                                        <span class="fw-bold h5 mb-0" style="color: #c28d32;">{{ currency($details['price'] * $details['quantity']) }}</span>
                                        <button class="btn btn-sm p-2 rounded-circle transition-all" onclick="removeItem({{ $id }})" title="Supprimer" style="background: rgba(220,38,38,0.08); color: #dc2626; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>"""

new_content = pattern.sub(new_cart_html, content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)
print("Cart page updated successfully")
