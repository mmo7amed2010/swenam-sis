{{--
/**
 * Card List Component
 *
 * Card containing a list of items with optional header and empty state.
 *
 * @param string|null $title - Card title
 * @param bool $flush - Add card-flush class (default: false)
 *
 * @slot toolbar - Toolbar actions
 * @slot default - List items content
 * @slot footer - Card footer
 *
 * @example
 * <x-cards.list title="Course List">
 *     @foreach($courses as $course)
 *         <x-lists.item title="{{ $course->name }}" />
 *     @endforeach
 * </x-cards.list>
 */
--}}

@props([
    'title' => null,
    'flush' => false,
])

<x-cards.section :title="$title" :flush="$flush" {{ $attributes }}>
    @if (isset($toolbar))
        <x-slot:toolbar>
            {{ $toolbar }}
        </x-slot:toolbar>
    @endif

    {{ $slot }}

    @if (isset($footer))
        <x-slot:footer>
            {{ $footer }}
        </x-slot:footer>
    @endif
</x-cards.section>

