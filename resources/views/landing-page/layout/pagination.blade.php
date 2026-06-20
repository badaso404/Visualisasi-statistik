@if ($paginator->hasPages())
    <div class="posts-pagination mb-30 mt-20">
        <ul>
            @if ($paginator->onFirstPage())
                <li class="left-arrow"><a href="#" class="disabled"><span class="flaticon-arrow-1"></span></a></li>
            @else
                <li class="left-arrow"><a href="{{ $paginator->previousPageUrl() }}"><span class="flaticon-arrow-1"></span></a></li>
            @endif

            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li><a href="#" class="disabled">{{ $element }}</a></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><a href="#" class="active">{{ $page }}</a></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}"><span class="flaticon-arrow-1"></span></a></li>
            @else
                <li><a href="#" class="disabled"><span class="flaticon-arrow-1"></span></a></li>
            @endif
        </ul>
    </div>
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
                    {!! __('Menampilkan') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        {!! __('sampai') !!}
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('dari') !!}
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    {!! __('hasil') !!}
                </p>
            </div>
        </div>
    </nav>
@endif