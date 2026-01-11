@if ($paginator->hasPages())
    <div style="display: flex; justify-content: center; align-items: center; gap: 8px; padding: 20px 0; margin-top: 10px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button disabled style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #f1f5f9; border: none; border-radius: 12px; color: #94a3b8; cursor: not-allowed; transition: all 0.2s;">
                <i data-feather="chevron-left" style="width: 20px; height: 20px;"></i>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; color: #64748b; cursor: pointer; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.03);" onmouseover="this.style.borderColor='#3b82f6'; this.style.color='#3b82f6'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'; this.style.transform='translateY(0)'">
                <i data-feather="chevron-left" style="width: 20px; height: 20px;"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div style="display: flex; gap: 8px; align-items: center; background: white; padding: 6px; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; color: #94a3b8; font-weight: 600;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border-radius: 10px; font-size: 14px; font-weight: 600; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; color: #64748b; border-radius: 10px; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.color='#1f2937'" onmouseout="this.style.background='transparent'; this.style.color='#64748b'">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; color: #64748b; cursor: pointer; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.03);" onmouseover="this.style.borderColor='#3b82f6'; this.style.color='#3b82f6'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'; this.style.transform='translateY(0)'">
                <i data-feather="chevron-right" style="width: 20px; height: 20px;"></i>
            </a>
        @else
            <button disabled style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #f1f5f9; border: none; border-radius: 12px; color: #94a3b8; cursor: not-allowed; transition: all 0.2s;">
                <i data-feather="chevron-right" style="width: 20px; height: 20px;"></i>
            </button>
        @endif
    </div>

    <script>
        // Re-initialize feather icons for pagination
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>
@endif
