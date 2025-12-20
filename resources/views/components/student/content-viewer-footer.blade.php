{{--
 * Content Viewer Footer Component
 *
 * A consistent footer for lesson, quiz, and assignment viewers
 * with "Previous" and "Next" navigation between module items.
 *
 * @param string|null $previousUrl - URL for previous content item
 * @param string|null $nextUrl - URL for next content item
--}}

@props([
    'previousUrl' => null,
    'nextUrl' => null,
])

<div class="card-footer bg-transparent border-top pt-6">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        @if($previousUrl)
            <a href="{{ $previousUrl }}" class="btn btn-light">
                {!! getIcon('arrow-left', 'fs-4 me-2') !!}
                {{ __('Previous') }}
            </a>
        @else
            <div></div>
        @endif
        @if($nextUrl)
            <a href="{{ $nextUrl }}" class="btn btn-primary">
                {{ __('Next') }}
                {!! getIcon('arrow-right', 'fs-4 ms-2 text-white') !!}
            </a>
        @endif
    </div>
</div>
