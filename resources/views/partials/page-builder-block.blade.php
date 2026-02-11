@props(['block'])

<div class="page-block-wrapper {{ $block['class'] ?? '' }}" 
     @if(!empty($block['id'])) id="{{ $block['id'] }}" @endif
     style="
        background-color: {{ $block['bg_color'] ?? 'transparent' }}; 
        color: {{ $block['text_color'] ?? 'inherit' }};
        @if(!empty($block['width'])) width: {{ $block['width'] }}; @endif
        @if(!empty($block['height'])) height: {{ $block['height'] }}; @endif
        @if(!empty($block['position'])) position: {{ $block['position'] }}; @endif
        @if(!empty($block['top'])) top: {{ $block['top'] }}; @endif
        @if(!empty($block['bottom'])) bottom: {{ $block['bottom'] }}; @endif
        @if(!empty($block['left'])) left: {{ $block['left'] }}; @endif
        @if(!empty($block['right'])) right: {{ $block['right'] }}; @endif
        @if(!empty($block['z_index'])) z-index: {{ $block['z_index'] }}; @endif
        
        {{-- Group Flex Styles --}}
        @if(($block['type'] ?? '') === 'group')
            display: flex;
            flex-direction: {{ $block['flex_dir'] ?? 'row' }};
            justify-content: {{ $block['justify_content'] ?? 'flex-start' }};
            align-items: {{ $block['align_items'] ?? 'stretch' }};
            gap: {{ ($block['gap'] ?? '0') . 'px' }};
        @endif

        @if(!empty($block['custom_css'])) {{ $block['custom_css'] }} @endif
     "
     @if(!empty($block['animation']) && $block['animation'] !== 'none') data-aos="{{ $block['animation'] }}" @endif>

    @switch($block['type'])
        @case('group')
            {{-- Recursive Rendering --}}
            @if(!empty($block['children']) && is_array($block['children']))
                @foreach($block['children'] as $child)
                    @include('partials.page-builder-block', ['block' => $child])
                @endforeach
            @endif
            @break

        @case('hero')
            <div class="hero-section text-center w-100" style="@if(!empty($block['bg_image'])) background-image: url('{{ $block['bg_image'] }}'); background-size: cover; background-position: center; @endif">
                <div class="container py-5">
                    <h1 class="display-3 fw-bold mb-4">{{ $block['body'] ?? 'Hero Title' }}</h1>
                    @if(!empty($block['subtitle']))<p class="lead mb-5 opacity-75">{{ $block['subtitle'] }}</p>@endif
                    <div class="d-flex justify-content-center gap-3">
                         @if(!empty($block['cta_text']))
                            <a href="{{ $block['cta_link'] ?? '#' }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold">{{ $block['cta_text'] }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @break

        @case('image')
            <div class="text-center w-100">
                @if(!empty($block['image_url']))
                    <img src="{{ $block['image_url'] }}" class="img-fluid rounded" alt="{{ $block['body'] ?? 'Image' }}" style="max-width: 100%;">
                @endif
                @if(!empty($block['body']))
                    <p class="text-muted small mt-2">{{ $block['body'] }}</p>
                @endif
            </div>
            @break

        @case('heading')
            <div class="w-100">
                 <{{ $block['level'] ?? 'h2' }} class="{{ $block['align'] ?? 'text-start' }}" style="font-weight: bold;">
                    {{ $block['body'] ?? 'Heading Text' }}
                </{{ $block['level'] ?? 'h2' }}>
            </div>
            @break
            
        @case('content')
            <div class="container">
                <div class="lead" style="line-height: 2;">
                    {!! nl2br(e($block['body'])) !!}
                </div>
            </div>
            @break

        @case('features')
            <div class="container">
                <div class="row g-4 justify-content-center">
                    @php $features = explode("\n", $block['body'] ?? "Feature 1\nFeature 2"); @endphp
                    @foreach($features as $feature)
                    <div class="col-md-4">
                        <div class="feature-box text-center">
                            <div class="brand-avatar bg-primary text-white mx-auto mb-4" style="width: 70px; height: 70px; font-size: 1.5rem; border-radius: 20px;">
                                <i class="fas fa-check"></i>
                            </div>
                            <h4 class="fw-bold mb-3">{{ trim($feature) }}</h4>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @break

        @case('cta')
            <div class="py-5 w-100">
                 <div class="container text-center">
                    <div class="p-5 rounded-5 shadow-lg bg-primary text-white" style="@if(!empty($block['bg_image'])) background-image: url('{{ $block['bg_image'] }}'); background-size: cover; @endif; background: var(--gradient-primary, #0d6efd);">
                        <h2 class="display-5 fw-bold mb-4">{{ $block['body'] ?? 'Ready to Grow?' }}</h2>
                        @if(!empty($block['cta_text']))
                            <a href="{{ $block['cta_link'] ?? '#' }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold shadow">{{ $block['cta_text'] }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @break

        @case('faq')
            <div class="content-section">
                <div class="container">
                    <div class="col-lg-7 mx-auto">
                        <h2 class="text-center fw-bold mb-5">Frequently Asked Questions</h2>
                        <div class="accordion" id="faqAccordion-{{ $block['id'] ?? uniqid() }}">
                            @php $faqs = explode("\n", $block['body'] ?? "Q: How to start?\nA: Just sign up!\nQ: Is it free?\nA: We have a free trial."); @endphp
                            @foreach($faqs as $i => $faqLine)
                                @if(str_contains($faqLine, ':'))
                                    @php [$q, $a] = explode(':', $faqLine, 2); @endphp
                                    <div class="accordion-item mb-3 border-0 rounded-4 overflow-hidden shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $block['id'] ?? '' }}-{{ $i }}">
                                                {{ trim($q) }}?
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $block['id'] ?? '' }}-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion-{{ $block['id'] ?? uniqid() }}">
                                            <div class="accordion-body bg-white opacity-75 py-3 px-4">
                                                {{ trim($a) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @break

        @case('testimonials')
            <div class="content-section shadow-sm" style="background: rgba(0,0,0,0.02)">
                <div class="container">
                    <h2 class="text-center fw-bold mb-5">What Our Clients Say</h2>
                    <div class="row g-4">
                        @php $testimonials = explode("\n", $block['body'] ?? "John Doe: Speed is amazing!\nJane Smith: Handled all our orders perfectly."); @endphp
                        @foreach($testimonials as $testi)
                            @if(str_contains($testi, ':'))
                                @php [$name, $text] = explode(':', $testi, 2); @endphp
                                <div class="col-md-6">
                                    <div class="p-4 bg-white rounded-4 border">
                                        <div class="text-warning mb-3">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                        </div>
                                        <p class="fst-italic opacity-75 mb-4">"{{ trim($text) }}"</p>
                                        <div class="d-flex align-items-center">
                                            <div class="brand-avatar me-3" style="width: 40px; height: 40px; border-radius: 50%;">{{ substr($name, 0, 1) }}</div>
                                            <div class="fw-bold">{{ trim($name) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @break

        @case('contact')
            <div class="content-section">
                <div class="container">
                    <div class="row g-5 align-items-center">
                        <div class="col-lg-5">
                            <h2 class="fw-bold mb-4">Get in Touch</h2>
                            <p class="opacity-75 mb-5">{{ $block['body'] ?: 'Have questions or need support? Our team is available 24/7 to help you succeed.' }}</p>
                        </div>
                        <div class="col-lg-7">
                            <div class="p-5 bg-white rounded-5 shadow-sm border">
                                <form> <!-- Placeholder form -->
                                    <div class="row g-4">
                                        <div class="col-md-6"><input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Full Name"></div>
                                        <div class="col-md-6"><input type="email" class="form-control bg-light border-0 py-3 px-4" placeholder="Email Address"></div>
                                        <div class="col-12"><textarea class="form-control bg-light border-0 py-3 px-4" rows="5" placeholder="Message"></textarea></div>
                                        <div class="col-12"><button class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold">Send Message</button></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @break

         @case('product-grid')
            <div class="container py-5">
                <h2 class="text-center fw-bold mb-5">{{ $block['body'] ?: 'Latest Products' }}</h2>
                <div class="row g-4">
                    @php
                        $latestProducts = \App\Models\Product::where('status', 'active')->latest()->take(4)->get();
                    @endphp
                    @foreach($latestProducts as $prod)
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm rounded-4 product-card overflow-hidden">
                            <a href="{{ route('shop.show', $prod->id) }}" class="position-relative d-block bg-white p-3 text-center">
                                @if($prod->main_image)
                                <img src="{{ Storage::url($prod->main_image) }}" class="img-fluid" alt="{{ $prod->name }}" style="height: 180px; object-fit: contain;">
                                @else
                                <i class="fas fa-image fa-3x text-muted opacity-25 my-4"></i>
                                @endif
                            </a>
                            <div class="card-body p-3 text-center">
                                <h6 class="fw-bold mb-1"><a href="{{ route('shop.show', $prod->id) }}" class="text-decoration-none text-dark">{{ $prod->name }}</a></h6>
                                <p class="text-primary fw-bold mb-0">{{ $prod->formatted_price }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @break

        @case('category-grid')
            <div class="content-section bg-light">
                <div class="container">
                    <h2 class="text-center fw-bold mb-5">{{ $block['body'] ?: 'Shop by Category' }}</h2>
                    <div class="row g-4 justify-content-center">
                        @php
                            $cats = \App\Models\Category::withCount('products')->take(4)->get();
                        @endphp
                        @foreach($cats as $cat)
                        <div class="col-md-3">
                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="card h-100 border-0 shadow-sm rounded-4 text-decoration-none text-dark hover-lift">
                                <div class="card-body p-4 text-center">
                                    <div class="brand-avatar bg-primary text-white mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem; border-radius: 50%;"><i class="fas fa-tag"></i></div>
                                    <h5 class="fw-bold mb-1">{{ $cat->name }}</h5>
                                    <p class="text-muted small mb-0">{{ $cat->products_count }} Products</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @break
        
        @case('list')
            <div class="container py-3">
                @php $items = explode("\n", $block['body'] ?? ''); @endphp
                @if(($block['style'] ?? 'ul') === 'ol')
                    <ol class="list-group list-group-numbered">
                        @foreach($items as $item) @if(trim($item)) <li class="list-group-item">{{ trim($item) }}</li> @endif @endforeach
                    </ol>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($items as $item) @if(trim($item)) <li class="list-group-item"><i class="fas fa-check text-primary me-2"></i> {{ trim($item) }}</li> @endif @endforeach
                    </ul>
                @endif
            </div>
            @break

        @case('table')
            <div class="container py-4"><div class="table-responsive"><table class="table table-hover table-bordered"><tbody><tr><td>Table Content Placeholder</td></tr></tbody></table></div></div>
            @break
        
        @case('code')
            <div class="container py-3">{!! $block['body'] ?? '' !!}</div>
            @break

        @default
            <div class="p-4 border border-dashed text-muted text-center">
                Unknown Block Type: {{ $block['type'] }}
            </div>
    @endswitch
</div>
