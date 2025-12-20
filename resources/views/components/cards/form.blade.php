{{--
/**
 * Card Form Component
 *
 * Card specifically styled for form sections.
 *
 * @param string|null $title - Form section title
 * @param string|null $subtitle - Optional description
 * @param bool $flush - Add card-flush class (default: true for forms)
 *
 * @slot toolbar - Toolbar actions
 * @slot default - Form fields
 * @slot footer - Form actions (submit buttons, etc.)
 *
 * @example
 * <x-cards.form title="Course Information">
 *     <x-forms.field-group label="Course Name" name="name" />
 * </x-cards.form>
 */
--}}

@props([
    'title' => null,
    'subtitle' => null,
    'flush' => true,
])

<x-cards.section :title="$title" :subtitle="$subtitle" :flush="$flush" class="py-4" {{ $attributes }}>
    @if (isset($toolbar))
        <x-slot:toolbar>
            {{ $toolbar }}
        </x-slot:toolbar>
    @endif

    <div class="card-body pt-0">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <x-slot:footer>
            {{ $footer }}
        </x-slot:footer>
    @endif
</x-cards.section>

