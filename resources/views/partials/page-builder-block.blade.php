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
            <div class="hero-section text-center w-100 bg-surface" style="@if(!empty($block['bg_image'])) background-image: url('{{ $block['bg_image'] }}'); background-size: cover; background-position: center; @endif">
                <div class="container py-5 py-lg-6">
                    <h1 class="brand-heading display-3 mb-4 text-dark">{{ $block['body'] ?? 'Hero Title' }}</h1>
                    @if(!empty($block['subtitle']))<p class="font-body lead mb-5 text-muted mx-auto" style="max-width: 700px;">{{ $block['subtitle'] }}</p>@endif
                    <div class="d-flex justify-content-center gap-3">
                         @if(!empty($block['cta_text']))
                            <a href="{{ $block['cta_link'] ?? '#' }}" class="btn-brand-primary px-5 py-3 text-decoration-none hvr-grow">{{ $block['cta_text'] }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @break

        @case('image')
            <div class="text-center w-100 py-3">
                @if(!empty($block['image_url']))
                    <img src="{{ Str::startsWith($block['image_url'], 'http') ? $block['image_url'] : Storage::url($block['image_url']) }}" class="img-fluid rounded-4 shadow-sm" alt="{{ $block['body'] ?? 'Image' }}" style="max-width: 100%;">
                @endif
                @if(!empty($block['body']))
                    <p class="text-muted small mt-3 font-body">{{ $block['body'] }}</p>
                @endif
            </div>
            @break

        @case('heading')
            <div class="w-100 py-3">
                 <{{ $block['level'] ?? 'h2' }} class="brand-heading {{ $block['align'] ?? 'text-start' }} text-dark">
                    {{ $block['body'] ?? 'Heading Text' }}
                </{{ $block['level'] ?? 'h2' }}>
                <div class="bg-gold rounded mb-3" style="width: 40px; height: 3px;"></div>
            </div>
            @break
            
        @case('content')
        @case('text')
        @case('paragraph')
            <div class="py-2">
                <div class="font-body text-muted" style="line-height: 1.8; font-size: 1.1rem;">
                    {!! nl2br($block['body'] ?? $block['content'] ?? '') !!}
                </div>
            </div>
            @break

        @case('features')
            <div class="py-5">
                <div class="row g-4 justify-content-center">
                    @php $features = explode("\n", $block['body'] ?? "Feature 1\nFeature 2"); @endphp
                    @foreach($features as $feature)
                    <div class="col-md-4">
                        <div class="brand-card p-4 text-center bg-white h-100">
                            <div class="bg-gold-light text-gold mx-auto mb-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px; border-radius: 50%;">
                                <i class="fas fa-crown"></i>
                            </div>
                            <h4 class="brand-heading h5 mb-0">{{ trim($feature) }}</h4>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @break

        @case('cta')
            <div class="py-5 w-100">
                 <div class="text-center">
                    <div class="p-5 rounded-4 shadow-sm text-white" style="@if(!empty($block['bg_image'])) background-image: url('{{ $block['bg_image'] }}'); background-size: cover; @endif; background: var(--brand-gold-gradient);">
                        <h2 class="brand-heading h1 mb-4 text-white">{{ $block['body'] ?? __('Join us today') }}</h2>
                        @if(!empty($block['cta_text']))
                            <a href="{{ $block['cta_link'] ?? '#' }}" class="btn btn-white px-5 py-3 rounded-pill fw-bold text-gold hvr-float">{{ $block['cta_text'] }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @break

        @case('faq')
            <div class="content-section">
                <div class="col-lg-10 mx-auto">
                    <h2 class="brand-heading text-center mb-5">{{ __('Frequently Asked Questions') }}</h2>
                    <div class="accordion" id="faqAccordion-{{ $block['id'] ?? uniqid() }}">
                        @php $faqs = explode("\n", $block['body'] ?? "Q: How to start?\nA: Just sign up!"); @endphp
                        @foreach($faqs as $i => $faqLine)
                            @if(str_contains($faqLine, ':'))
                                @php [$q, $a] = explode(':', $faqLine, 2); @endphp
                                <div class="accordion-item mb-3 border-0 rounded-4 overflow-hidden shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold font-body text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $block['id'] ?? '' }}-{{ $i }}">
                                            {{ trim($q) }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $block['id'] ?? '' }}-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion-{{ $block['id'] ?? uniqid() }}">
                                        <div class="accordion-body bg-gold-light font-body text-muted py-3">
                                            {{ trim($a) }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @break

        @case('testimonials')
            <div class="py-5">
                <h2 class="brand-heading text-center mb-5">{{ __('Customer Reviews') }}</h2>
                <div class="row g-4">
                    @php $testimonials = explode("\n", $block['body'] ?? "John: Speed is amazing!"); @endphp
                    @foreach($testimonials as $testi)
                        @if(str_contains($testi, ':'))
                            @php [$name, $text] = explode(':', $testi, 2); @endphp
                            <div class="col-md-6">
                                <div class="brand-card p-4 bg-white">
                                    <div class="text-gold mb-3 small">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                    <p class="font-body text-muted fst-italic mb-4">"{{ trim($text) }}"</p>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-gold-light text-gold d-flex align-items-center justify-content-center me-3 small fw-bold" style="width: 35px; height: 35px; border-radius: 50%;">{{ substr($name, 0, 1) }}</div>
                                        <div class="brand-heading small mb-0">{{ trim($name) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @break

        @case('contact')
            <div class="py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-5">
                        <h2 class="brand-heading mb-4">{{ __('Contact Us') }}</h2>
                        <p class="font-body text-muted mb-5">{{ $block['body'] ?: __('We are here to help you choose the best look that suits your beauty.') }}</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="brand-card p-4 p-lg-5 bg-white">
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-6"><input type="text" class="form-control border-light py-3 bg-light focus-gold shadow-none" placeholder="{{ __('Full Name') }}"></div>
                                    <div class="col-md-6"><input type="email" class="form-control border-light py-3 bg-light focus-gold shadow-none" placeholder="{{ __('Email') }}"></div>
                                    <div class="col-12"><textarea class="form-control border-light py-3 bg-light focus-gold shadow-none" rows="4" placeholder="{{ __('Your message') }}"></textarea></div>
                                    <div class="col-12"><button class="btn-brand-primary w-100 py-3 hvr-grow">{{ __('Send Message') }}</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @break

         @case('product-grid')
            <div class="py-5 w-100">
                <h2 class="brand-heading text-center mb-5">{{ $block['body'] ?: __('Latest Collections') }}</h2>
                <div class="row g-3 g-lg-4">
                    @php
                        $latestProducts = \App\Models\Product::where('status', 'active')->latest()->take(4)->get();
                    @endphp
                    @foreach($latestProducts as $prod)
                    <div class="col-6 col-md-3">
                        <div class="brand-card border-0 h-100 bg-white overflow-hidden hvr-float">
                            <a href="{{ route('shop.show', $prod->id) }}" class="d-block position-relative">
                                @if($prod->main_image)
                                <img src="{{ Storage::url($prod->main_image) }}" class="card-img-top object-fit-cover" alt="{{ $prod->name }}" style="aspect-ratio: 3/4; height: auto;">
                                @else
                                <div class="bg-gold-light d-flex align-items-center justify-content-center" style="aspect-ratio: 3/4;"><i class="fas fa-crown text-gold opacity-25 fa-2x"></i></div>
                                @endif
                            </a>
                            <div class="card-body p-3 text-center">
                                <h6 class="brand-heading small mb-1"><a href="{{ route('shop.show', $prod->id) }}" class="text-decoration-none text-dark">{{ $prod->name }}</a></h6>
                                <p class="text-gold fw-bold mb-0 small font-body">{{ $prod->formatted_price }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @break

        @case('code')
            <div class="w-100">{!! $block['body'] ?? $block['content'] ?? '' !!}</div>
            @break

        @default
            <div class="p-4 border border-gold border-dashed text-gold text-center rounded-4 mb-4 small opacity-50">
                <i class="fas fa-info-circle me-1"></i> Unknown Block Type: {{ $block['type'] }}
            </div>
    @endswitch
</div>
