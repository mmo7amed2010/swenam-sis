@props(['items' => []])

<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
    <!--begin::Home Item-->
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
            {!! getIcon('home', 'fs-6 text-gray-400 me-1') !!}
            {{ __('Home') }}
        </a>
    </li>
    <!--end::Home Item-->

    @if(count($items) > 0)
        @foreach($items as $item)
            <!--begin::Separator-->
            <li class="breadcrumb-item">
                <span class="bullet bg-gray-400 w-5px h-2px"></span>
            </li>
            <!--end::Separator-->

            <!--begin::Item-->
            <li class="breadcrumb-item {{ $loop->last ? 'text-gray-900' : 'text-muted' }}">
                @if(!$loop->last && isset($item['url']))
                    <a href="{{ $item['url'] }}" class="text-muted text-hover-primary">
                        {{ $item['title'] }}
                    </a>
                @else
                    {{ $item['title'] }}
                @endif
            </li>
            <!--end::Item-->
        @endforeach
    @endif
</ul>
