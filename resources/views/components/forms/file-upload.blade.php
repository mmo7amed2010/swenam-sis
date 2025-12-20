{{--
/**
 * File Upload Component
 *
 * File input field with label, help text, validation, and optional preview.
 * Supports multiple files, drag & drop, and file type validation.
 *
 * @param string $name - Input name attribute (required)
 * @param string $label - Field label text (required)
 * @param bool $required - Mark field as required (default: false)
 * @param string|null $accept - Accepted file types (e.g., "image/*", ".pdf,.doc")
 * @param bool $multiple - Allow multiple files (default: false)
 * @param string|null $maxSize - Max file size (e.g., "2MB", "5MB")
 * @param string|null $help - Help text displayed below input
 * @param bool $preview - Show preview for images (default: false)
 * @param string $inputClass - Additional CSS classes for input
 * @param string $groupClass - Additional CSS classes for wrapper
 *
 * @example Basic File Upload
 * <x-forms.file-upload
 *     name="attachment"
 *     label="Upload Document"
 *     accept=".pdf,.doc,.docx"
 * />
 *
 * @example Image Upload with Preview
 * <x-forms.file-upload
 *     name="course_image"
 *     label="Course Image"
 *     accept="image/*"
 *     maxSize="2MB"
 *     :preview="true"
 *     :required="true"
 * />
 *
 * @example Multiple Files
 * <x-forms.file-upload
 *     name="documents[]"
 *     label="Upload Documents"
 *     :multiple="true"
 *     accept=".pdf"
 *     help="You can upload multiple PDF files"
 * />
 */
--}}

@props([
    'name',
    'label',
    'required' => false,
    'accept' => null,
    'multiple' => false,
    'maxSize' => null,
    'help' => null,
    'preview' => false,
    'inputClass' => '',
    'groupClass' => '',
])

@php
    $inputId = 'file_' . str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has(str_replace('[]', '', $name));

    $inputAttributes = [
        'type' => 'file',
        'name' => $name,
        'id' => $inputId,
        'class' => 'form-control ' . $inputClass . ($hasError ? ' is-invalid' : ''),
    ];

    if ($required) {
        $inputAttributes['required'] = true;
        $inputAttributes['aria-required'] = 'true';
    }

    if ($accept) $inputAttributes['accept'] = $accept;
    if ($multiple) $inputAttributes['multiple'] = true;
    if ($hasError) $inputAttributes['aria-invalid'] = 'true';
    if ($help || $maxSize) {
        $describedBy = [];
        if ($help) $describedBy[] = $inputId . '_help';
        if ($maxSize) $describedBy[] = $inputId . '_size';
        $inputAttributes['aria-describedby'] = implode(' ', $describedBy);
    }
@endphp

<div class="mb-10 fv-row {{ $groupClass }}">
    {{-- Label --}}
    <label for="{{ $inputId }}" class="form-label {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>

    {{-- File Input --}}
    <input {{ $attributes->merge($inputAttributes) }} />

    {{-- Preview Container --}}
    @if($preview)
    <div id="{{ $inputId }}_preview" class="mt-3 d-none">
        <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;" />
    </div>
    @endif

    {{-- Help Text and File Info --}}
    <div class="d-flex justify-content-between align-items-start mt-1">
        @if($help)
        <div id="{{ $inputId }}_help" class="form-text text-muted fs-7 flex-grow-1">{{ $help }}</div>
        @endif

        @if($maxSize)
        <div id="{{ $inputId }}_size" class="text-muted fs-7 ms-auto">
            Max: {{ $maxSize }}
        </div>
        @endif
    </div>

    {{-- File Info Display --}}
    <div id="{{ $inputId }}_info" class="text-muted fs-7 mt-1 d-none"></div>

    {{-- Error Message --}}
    @error(str_replace('[]', '', $name))
    <div class="invalid-feedback" role="alert">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
(function() {
    const fileInput = document.getElementById('{{ $inputId }}');
    const fileInfo = document.getElementById('{{ $inputId }}_info');
    @if($preview)
    const previewContainer = document.getElementById('{{ $inputId }}_preview');
    const previewImg = previewContainer ? previewContainer.querySelector('img') : null;
    @endif

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(this.files);

            // Display file info
            if (files.length > 0) {
                const fileNames = files.map(f => f.name).join(', ');
                const totalSize = files.reduce((sum, f) => sum + f.size, 0);
                const sizeStr = formatBytes(totalSize);

                fileInfo.textContent = `${files.length} file(s) selected (${sizeStr}): ${fileNames}`;
                fileInfo.classList.remove('d-none');

                @if($preview)
                // Show image preview if it's an image file
                if (files[0] && files[0].type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (previewImg) {
                            previewImg.src = e.target.result;
                            previewContainer.classList.remove('d-none');
                        }
                    };
                    reader.readAsDataURL(files[0]);
                } else if (previewContainer) {
                    previewContainer.classList.add('d-none');
                }
                @endif

                @if($maxSize)
                // Validate file size
                const maxBytes = parseSize('{{ $maxSize }}');
                if (totalSize > maxBytes) {
                    fileInfo.classList.add('text-danger');
                    fileInfo.textContent += ' - File size exceeds maximum allowed';
                } else {
                    fileInfo.classList.remove('text-danger');
                }
                @endif
            } else {
                fileInfo.classList.add('d-none');
                @if($preview)
                if (previewContainer) {
                    previewContainer.classList.add('d-none');
                }
                @endif
            }
        });
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function parseSize(sizeStr) {
        const units = { 'B': 1, 'KB': 1024, 'MB': 1024*1024, 'GB': 1024*1024*1024 };
        const match = sizeStr.match(/^(\d+(?:\.\d+)?)\s*(B|KB|MB|GB)$/i);
        if (!match) return Infinity;
        return parseFloat(match[1]) * units[match[2].toUpperCase()];
    }
})();
</script>
@endpush
