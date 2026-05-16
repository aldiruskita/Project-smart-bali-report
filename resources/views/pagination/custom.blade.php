@if ($paginator->hasPages())
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:8px 14px;color:#64748b;">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="current" style="padding:8px 14px;background:var(--primary);color:white;border-radius:8px;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:8px 14px;color:#94a3b8;background:rgba(255,255,255,0.05);border-radius:8px;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach
@endif
