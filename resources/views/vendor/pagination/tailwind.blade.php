@if ($paginator->hasPages())
<nav aria-label="Pagination Navigation" style="display:flex;align-items:center;gap:4px;">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="display:inline-flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:8px;font-size:13px;font-weight:500;color:#cbd5e1;background:#f8fafc;border:1px solid #e2e8f0;cursor:not-allowed;">
            ‹ Prev
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:8px;font-size:13px;font-weight:500;color:#374151;background:#f8fafc;border:1px solid #e2e8f0;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='#ede9fe';this.style.color='#6366f1'" onmouseout="this.style.background='#f8fafc';this.style.color='#374151'">
            ‹ Prev
        </a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:13px;color:#94a3b8;">...</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:13px;font-weight:700;color:#fff;background:#6366f1;border:1px solid #6366f1;">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:13px;font-weight:500;color:#374151;background:#f8fafc;border:1px solid #e2e8f0;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='#ede9fe';this.style.color='#6366f1'" onmouseout="this.style.background='#f8fafc';this.style.color='#374151'">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:8px;font-size:13px;font-weight:500;color:#374151;background:#f8fafc;border:1px solid #e2e8f0;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='#ede9fe';this.style.color='#6366f1'" onmouseout="this.style.background='#f8fafc';this.style.color='#374151'">
            Next ›
        </a>
    @else
        <span style="display:inline-flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:8px;font-size:13px;font-weight:500;color:#cbd5e1;background:#f8fafc;border:1px solid #e2e8f0;cursor:not-allowed;">
            Next ›
        </span>
    @endif

</nav>
@endif
