<x-default-layout>

    @section('title')
        Create Contract Template
    @endsection

    <div class="d-flex flex-wrap flex-stack mb-6">
        <h2 class="fw-bold my-2">
            {!! getIcon('plus-circle', 'fs-2 me-2 text-primary') !!}
            Create Contract Template
        </h2>
        <a href="{{ route('admin.contract-templates.index') }}" class="btn btn-light">
            {!! getIcon('left', 'fs-4 me-1') !!}
            Back to Templates
        </a>
    </div>

    <form action="{{ route('admin.contract-templates.store') }}" method="POST">
        @csrf
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
                            <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Standard Enrollment Contract" required />
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
                                    <option value="{{ $program['id'] }}" {{ old('program_id') == $program['id'] ? 'selected' : '' }}>
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
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }} />
                                <span class="form-check-label fw-semibold text-gray-700">Active</span>
                            </label>
                        </div>

                        {{-- Body (CKEditor) --}}
                        <div class="mb-6">
                            <label class="form-label required fw-semibold">Contract Body</label>
                            <div class="ck-editor-wrapper border rounded @error('body') border-danger @enderror">
                                <textarea name="body" id="contract-body">{{ old('body') }}</textarea>
                            </div>
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
                        Create Template
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

    @push('styles')
    <style>
        .ck-editor-wrapper .ck.ck-editor { width: 100%; }
        .ck-editor-wrapper .ck.ck-editor__main > .ck-editor__editable { min-height: 600px; }
        .ck-editor-wrapper .ck.ck-toolbar { border-radius: 0.475rem 0.475rem 0 0 !important; flex-wrap: wrap !important; }
        .ck-editor-wrapper .ck.ck-editor__main > .ck-editor__editable { border-radius: 0 0 0.475rem 0.475rem !important; }
        .ck.ck-toolbar .ck-toolbar__separator { height: 1.6em; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <script>
        let editorInstance;

        CKEDITOR.ClassicEditor
            .create(document.querySelector('#contract-body'), {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|', 'heading',
                        '|', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
                        '|', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'removeFormat',
                        '|', 'alignment',
                        '|', 'bulletedList', 'numberedList', 'outdent', 'indent',
                        '|', 'link', 'insertTable', 'blockQuote', 'horizontalLine', 'pageBreak',
                        '|', 'findAndReplace', 'specialCharacters', 'sourceEditing',
                    ],
                    shouldNotGroupWhenFull: true
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    ]
                },
                fontSize: {
                    options: [8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72],
                    supportAllValues: true
                },
                fontFamily: {
                    options: [
                        'default',
                        'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace',
                        'Georgia, serif',
                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif',
                        'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif',
                        'Verdana, Geneva, sans-serif',
                    ],
                    supportAllValues: true
                },
                table: {
                    contentToolbar: [
                        'tableColumn', 'tableRow', 'mergeTableCells',
                        'tableProperties', 'tableCellProperties',
                    ]
                },
                list: {
                    properties: {
                        styles: true,
                        startIndex: true,
                        reversed: true
                    }
                },
                link: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                },
                htmlSupport: {
                    allow: [
                        { name: /.*/, attributes: true, classes: true, styles: true }
                    ]
                },
                removePlugins: [
                    'ExportPdf', 'ExportWord', 'AIAssistant', 'CKBox', 'CKFinder',
                    'EasyImage', 'MultiLevelList', 'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory',
                    'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
                    'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                    'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                    'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
                ]
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
