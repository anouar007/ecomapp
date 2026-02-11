@extends('layouts.frontend')

@section('meta_title', 'Shop - ' . setting('app_name', 'Speed Store'))
@section('meta_description', 'Browse our curated collection of premium products.')

@section('content')
<!-- Shop Header -->
<section class="hero-elegant" style="padding: 80px 0;">
    <div class="container text-center position-relative">
        <h1 class="hero-title" style="font-size: 2.75rem;">Our Collection</h1>
        <p class="hero-subtitle mb-0" style="max-width: 500px; margin: 0 auto;">
            Discover premium products curated for quality and style
        </p>
    </div>
</section>

<section class="section-elegant" style="background: var(--bg-light); padding: 60px 0 100px;">
    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <div class="sticky-top" style="top: 100px;">
                    <!-- Categories Filter -->
                    <div class="filter-card mb-4">
                        <h6 class="filter-title">Categories</h6>
                        <ul class="filter-list">
                            <li>
                                <a href="#" class="filter-link category-filter {{ !request('category') ? 'active' : '' }}" data-slug="">
                                    <span>All Products</span>
                                    <span class="filter-count">{{ \App\Models\Product::where('status', 'active')->count() }}</span>
                                </a>
                            </li>
                            @foreach($categories as $category)
                            <li>
                                <a href="#" class="filter-link category-filter {{ request('category') == $category->slug ? 'active' : '' }}" data-slug="{{ $category->slug }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="filter-count">{{ $category->products_count }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-card mb-4">
                        <h6 class="filter-title">Price Range</h6>
                        <form id="priceFilterForm">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}" min="0">
                                <span class="text-muted">—</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}" min="0">
                            </div>
                            <button type="submit" class="btn-elegant btn-elegant-dark w-100" style="padding: 10px 20px; font-size: 0.85rem;">Apply</button>
                        </form>
                    </div>

                    <!-- Search -->
                    <div class="filter-card">
                        <h6 class="filter-title">Search</h6>
                        <form id="searchForm">
                            <div class="position-relative">
                                <input type="text" name="q" class="form-control" placeholder="Search products..." value="{{ request('q') }}">
                                <button type="submit" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="mb-0 fw-600" id="categoryTitle">
                        {{ request('category') ? $categories->where('slug', request('category'))->first()->name ?? 'All Products' : 'All Products' }}
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small">Sort:</label>
                        <select class="form-select form-select-sm" style="width: auto; border-radius: var(--btn-radius);" id="sortSelect">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        </select>
                    </div>
                </div>

                <div id="productGridContainer">
                    @include('frontend.shop.partials.product-grid')
                </div>
                
                <!-- Loader -->
                <div id="loader" class="d-none text-center py-5">
                    <div class="spinner-border" style="color: var(--primary-accent);" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.filter-card {
    background: var(--bg-white);
    border-radius: var(--card-radius);
    padding: 24px;
    box-shadow: var(--shadow-sm);
}

.filter-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-light);
}

.filter-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.filter-list li {
    margin-bottom: 2px;
}

.filter-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    border-radius: 6px;
    text-decoration: none;
    color: var(--text-body);
    transition: all 0.2s;
}

.filter-link:hover {
    background: var(--bg-warm);
    color: var(--text-dark);
}

.filter-link.active {
    background: var(--primary-accent);
    color: #fff;
}

.filter-link.active .filter-count {
    background: rgba(255,255,255,0.2);
    color: #fff;
}

.filter-count {
    font-size: 0.75rem;
    background: var(--bg-warm);
    padding: 2px 8px;
    border-radius: 10px;
    color: var(--text-muted);
}
</style>

<script>
    let currentCategory = "{{ request('category') }}";
    
    function getParams() {
        const params = new URLSearchParams();
        if(currentCategory) params.append('category', currentCategory);
        
        const sort = document.getElementById('sortSelect').value;
        if(sort) params.append('sort', sort);
        
        const q = document.querySelector('input[name="q"]').value;
        if(q) params.append('q', q);
        
        const minPrice = document.querySelector('input[name="min_price"]').value;
        if(minPrice) params.append('min_price', minPrice);
        
        const maxPrice = document.querySelector('input[name="max_price"]').value;
        if(maxPrice) params.append('max_price', maxPrice);
        
        return params;
    }

    function fetchProducts(url = "{{ route('shop.index') }}") {
        const grid = document.getElementById('productGridContainer');
        const loader = document.getElementById('loader');
        
        grid.classList.add('opacity-50');
        loader.classList.remove('d-none');
        
        const fetchUrl = url.includes('?') ? url : `${url}?${getParams().toString()}`;
        window.history.pushState(null, '', fetchUrl);

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            grid.innerHTML = html;
            grid.classList.remove('opacity-50');
            loader.classList.add('d-none');
            attachPaginationListeners();
        })
        .catch(err => {
            console.error(err);
            grid.classList.remove('opacity-50');
            loader.classList.add('d-none');
        });
    }

    function attachPaginationListeners() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchProducts(this.href);
                document.getElementById('productGridContainer').scrollIntoView({ behavior: 'smooth' });
            });
        });
    }

    // Category Filter
    document.querySelectorAll('.category-filter').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.category-filter').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.slug;
            document.getElementById('categoryTitle').innerText = this.querySelector('span').innerText;
            fetchProducts();
        });
    });

    document.getElementById('sortSelect').addEventListener('change', () => fetchProducts());
    document.getElementById('priceFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetchProducts();
    });
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetchProducts();
    });

    attachPaginationListeners();
    

    function addToCart(id) {
        const btn = document.querySelector(`button[onclick="addToCart(${id})"]`);
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(`/cart/add/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            
            if(data.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Added to cart!',
                    showConfirmButton: false,
                    timer: 2500
                });
                
                const badge = document.getElementById('header-cart-count');
                if(badge && data.cartCount !== undefined) badge.innerText = data.cartCount;
                
                // Refresh mini-cart content
                if(typeof refreshMiniCart === 'function') refreshMiniCart();
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }
</script>
@endsection
