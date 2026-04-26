@extends('layouts.frontend')

@section('meta_title', $page->meta_title . ' — ' . setting('app_name', 'Bio Nature'))
@section('meta_description', $page->meta_description)

@section('content')
    @if(!empty($page->custom_css))
        @push('styles')
            <style>
                {!! $page->custom_css !!}
            </style>
        @endpush
    @endif

    <div class="bg-surface min-vh-100">
        {{-- Page Header --}}
        <div class="bg-gold-light py-5 mb-5 border-bottom border-gold-subtle" data-aos="fade-down">
            <div class="container px-xl-5 text-center">
                <h1 class="brand-heading h2 mb-0">{{ $page->title }}</h1>
                <div class="bg-gold rounded mx-auto mt-3" style="width: 40px; height: 3px;"></div>
            </div>
        </div>

        {{-- Page Content --}}
        <div class="container px-xl-5 pb-5">
            <div class="brand-card border-0 shadow-sm bg-white p-4 p-lg-5 font-body" data-aos="fade-up">
                <div class="page-builder-content">
                    @forelse($page->content ?? [] as $block)
                        @include('partials.page-builder-block', ['block' => $block])
                    @empty
                        <div class="text-center py-5 opacity-50">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <p>{{ __('No content available for this page currently.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if(!empty($page->custom_js))
        @push('scripts')
            <script>
                {!! $page->custom_js !!}
            </script>
        @endpush
    @endif
@endsection
