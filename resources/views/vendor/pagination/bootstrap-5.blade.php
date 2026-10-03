@if ($paginator->hasPages())
    <nav class="custom-pagination-nav" aria-label="{{ __('Pagination') }}">
        <ul class="custom-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link page-link-arrow" aria-hidden="true">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link page-link-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('Previous') }}">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled d-none d-sm-inline-flex" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php
                            $isCurrent = $page == $paginator->currentPage();
                            $isCloseToCurrent = abs($page - $paginator->currentPage()) <= 1;
                            $isFirstOrLast = $page == 1 || $page == $paginator->lastPage();
                        @endphp
                        @if ($isCurrent)
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @elseif ($isCloseToCurrent || $isFirstOrLast)
                            {{-- Always show current, immediate neighbors, and first/last page on mobile --}}
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @else
                            {{-- Show remaining pages on tablet & desktop --}}
                            <li class="page-item d-none d-md-inline-flex"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link page-link-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('Next') }}">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link page-link-arrow" aria-hidden="true">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                    </span>
                </li>
            @endif
        </ul>

        {{-- Results & Page Counter summary --}}
        <div class="pagination-summary text-center text-muted small mt-2">
            <span>{{ __('Showing') }}</span> <strong class="text-dark">{{ $paginator->firstItem() }}</strong> - <strong class="text-dark">{{ $paginator->lastItem() }}</strong> <span>{{ __('of') }}</span> <strong class="text-dark">{{ $paginator->total() }}</strong> <span>{{ __('results') }}</span>
            <span class="mx-2 opacity-50">&bull;</span>
            <span>{{ __('Page') }}</span> <strong class="text-brand-red">{{ $paginator->currentPage() }}</strong> <span>{{ __('of') }}</span> <strong class="text-dark">{{ $paginator->lastPage() }}</strong>
        </div>
    </nav>
@endif
