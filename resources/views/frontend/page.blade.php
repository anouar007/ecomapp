@extends('layouts.frontend')

@section('meta_title', $page->meta_title)
@section('meta_description', $page->meta_description)

@section('content')
    @if(!empty($page->custom_css))
        @push('styles')
            <style>
                {!! $page->custom_css !!}
            </style>
        @endpush
    @endif

    <div class="page-builder-content">
        @foreach($page->content ?? [] as $block)
            @include('partials.page-builder-block', ['block' => $block])
        @endforeach
    </div>

    @if(!empty($page->custom_js))
        @push('scripts')
            <script>
                {!! $page->custom_js !!}
            </script>
        @endpush
    @endif
@endsection
