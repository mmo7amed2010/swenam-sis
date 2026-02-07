<x-default-layout>

    @section('title')
        Edit Contract Template - {{ $contractTemplate->name }}
    @endsection

    <div class="d-flex flex-wrap flex-stack mb-6">
        <h2 class="fw-bold my-2">
            {!! getIcon('pencil', 'fs-2 me-2 text-primary') !!}
            Edit Contract Template
        </h2>
        <a href="{{ route('admin.contract-templates.index') }}" class="btn btn-light">
            {!! getIcon('left', 'fs-4 me-1') !!}
            Back to Templates
        </a>
    </div>

    <form action="{{ route('admin.contract-templates.update', $contractTemplate) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-6">
            {{-- Main Form --}}
            <div class="col-xl-8">
                <div class="card mb-6">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title fw-bold text-gray-800">Template Details</h3>
                    </div>
                    <div class="card-body pt-0">
                        {{-- Name --}}
                        <div class="mb-6">
                            <label class="form-label required fw-semibold">Template Name</label>
                            <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror" value="{{ old('name', $contractTemplate->name) }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Program --}}
                        <div class="mb-6">
                            <label class="form-label required fw-semibold">Program</label>
                            <select name="program_id" class="form-select form-select-solid @error('program_id') is-invalid @enderror" required>
                                <option value="">Select a Program</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program['id'] }}" {{ old('program_id', $contractTemplate->program_id) == $program['id'] ? 'selected' : '' }}>
                                        {{ $program['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('program_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Active Status --}}
                        <div class="mb-6">
                            <label class="form-check form-check-custom form-check-solid">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $contractTemplate->is_active) ? 'checked' : '' }} />
                                <span class="form-check-label fw-semibold text-gray-700">Active</span>
                            </label>
                        </div>

                        {{-- Body (CKEditor) --}}
                        <div class="mb-6">
                            <label class="form-label required fw-semibold">Contract Body</label>
                            <textarea name="body" id="contract-body" class="form-control @error('body') is-invalid @enderror" rows="20">{{ old('body', $contractTemplate->body) }}</textarea>
                            @error('body')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.contract-templates.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-1') !!}
                        Update Template
                    </button>
                </div>
            </div>

            {{-- Sidebar: Placeholders --}}
            <div class="col-xl-4">
                <div class="card mb-6">
                    <div class="card-header border-0 bg-light-primary py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('code', 'fs-4 me-2 text-primary') !!}
                            Available Placeholders
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-gray-600 fs-7 mb-4">Click a placeholder to insert it at the cursor position in the editor.</p>

                        <h6 class="fw-bold text-gray-800 mb-3">SIS Auto-Populated</h6>
                        <p class="text-gray-500 fs-8 mb-3">These are automatically filled from the student's application data.</p>
                        <div class="d-flex flex-wrap gap-2 mb-6">
                            @foreach($sisPlaceholders as $placeholder => $description)
                                <button type="button" class="btn btn-sm btn-light-primary placeholder-btn" data-placeholder="{{ $placeholder }}" title="{{ $description }}">
                                    {{ $placeholder }}
                                </button>
                            @endforeach
                        </div>

                        <div class="separator separator-dashed mb-5"></div>

                        <h6 class="fw-bold text-gray-800 mb-3">Admin Custom Placeholders</h6>
                        <p class="text-gray-500 fs-8 mb-3">
                            You can add your own placeholders in the body using <code>@{{placeholder_name}}</code> syntax.
                            These will be filled by the admin when issuing a contract.
                        </p>
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-3">
                            {!! getIcon('information-5', 'fs-3 text-info me-2 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-8">
                                Examples: <code>@{{tuition_fee}}</code>, <code>@{{payment_deadline}}</code>, <code>@{{start_date}}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        let editorInstance;

        ClassicEditor
            .create(document.querySelector('#contract-body'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo'],
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        // Click-to-insert placeholder
        document.querySelectorAll('.placeholder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (editorInstance) {
                    editorInstance.model.change(writer => {
                        editorInstance.model.insertContent(
                            writer.createText(this.dataset.placeholder)
                        );
                    });
                    editorInstance.editing.view.focus();
                }
            });
        });
    </script>
    @endpush

</x-default-layout>
