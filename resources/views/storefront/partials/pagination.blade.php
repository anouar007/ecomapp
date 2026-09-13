@if($paginator->hasPages())
<nav class="pagination" aria-label="صفحات النتائج">
    @if(!$paginator->onFirstPage())<a href="{{ $paginator->previousPageUrl() }}" rel="prev">السابق</a>@endif
    <span>صفحة {{ $paginator->currentPage() }} من {{ $paginator->lastPage() }}</span>
    @if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">التالي</a>@endif
</nav>
@endif
