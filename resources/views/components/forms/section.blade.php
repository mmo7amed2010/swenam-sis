{{--
/**
 * Form Section Component
 *
 * Form section with title and description, wrapped in a card.
 *
 * @param string|null $title - Section title
 * @param string|null $description - Section description
 * @param bool $required - Show required indicator (default: false)
 *
 * @slot default - Form fields
 *
 * @example
 * <x-forms.section title="Basic Information" description="Enter course details">
 *     <x-forms.field-group label="Name" name="name" required />
 * </x-forms.section>
 */
--}}

@props([
    'title' => null,
    'description' => null,
    'required' => false,
])

<x-cards.form :title="$title" :flush="true" class="py-4" {{ $attributes }}>
    @if ($title)
        <div class="card-header">
            <div class="card-title">
                <h2 class="fw-bold text-gray-900">
                    {{ $title }}
                    @if ($required)
                        <span class="text-danger">*</span>
                    @endif
                </h2>
                @if ($description)
                    <p class="text-muted fs-6 mb-0 mt-1">{{ $description }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="card-body pt-0">
        {{ $slot }}
    </div>
</x-cards.form>

