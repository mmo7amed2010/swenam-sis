{{--
 * Import Content Modal
 *
 * Modal for importing course content via CSV or ZIP upload.
 *
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
--}}

<div class="modal fade" id="kt_modal_import_content" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('Import Course Content') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs nav-line-tabs mb-5" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#csv_tab" type="button" role="tab">
                            {{ __('CSV Upload') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#zip_tab" type="button" role="tab">
                            {{ __('ZIP Upload') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- CSV Tab -->
                    <div class="tab-pane fade show active" id="csv_tab" role="tabpanel">
                        <form id="csvImportForm" enctype="multipart/form-data"
                              data-import-url="{{ route('admin.programs.courses.import.csv', [$program, $course]) }}"
                              data-process-url="{{ route('admin.programs.courses.import.csv.process', [$program, $course]) }}">
                            @csrf
                            <div class="mb-5">
                                <label class="required form-label">{{ __('CSV File') }}</label>
                                <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required />
                                <div class="text-muted fs-7 mt-1">{{ __('Maximum file size: 10MB') }}</div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.courses.import.template') }}" class="btn btn-sm btn-light-primary">
                                        {!! getIcon('download', 'fs-2') !!}
                                        {{ __('Download CSV Template') }}
                                    </a>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <strong>{{ __('CSV Format:') }}</strong><br>
                                Module Title, Module Description, Lesson Title, Content Type, Content<br>
                                <small>{{ __('Content Type must be: text_html, video, pdf, or external_link') }}</small>
                            </div>
                            <div id="csvPreview" class="d-none mt-5">
                                <h5>{{ __('Import Preview') }}</h5>
                                <div id="csvPreviewContent"></div>
                                <button type="button" class="btn btn-primary mt-3" id="confirmCsvImport">{{ __('Confirm Import') }}</button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">{{ __('Upload & Preview') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait...') }}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ZIP Tab -->
                    <div class="tab-pane fade" id="zip_tab" role="tabpanel">
                        <form id="zipImportForm" enctype="multipart/form-data"
                              data-import-url="{{ route('admin.programs.courses.import.zip', [$program, $course]) }}"
                              data-process-url="{{ route('admin.programs.courses.import.zip.process', [$program, $course]) }}">
                            @csrf
                            <div class="mb-5">
                                <label class="required form-label">{{ __('ZIP File') }}</label>
                                <input type="file" name="zip_file" class="form-control" accept=".zip" required />
                                <div class="text-muted fs-7 mt-1">{{ __('Maximum file size: 100MB') }}</div>
                            </div>
                            <div class="alert alert-info">
                                <strong>{{ __('ZIP Structure:') }}</strong><br>
                                <pre class="mb-0">course-import.zip
├── Module 1 - Introduction/
│   ├── lesson1.pdf
│   ├── lesson2.html
│   └── video-link.txt (contains YouTube URL)
├── Module 2 - Basics/
│   ├── lecture.pdf
│   └── exercise.html</pre>
                            </div>
                            <div id="zipPreview" class="d-none mt-5">
                                <h5>{{ __('Import Preview') }}</h5>
                                <div id="zipPreviewContent"></div>
                                <button type="button" class="btn btn-primary mt-3" id="confirmZipImport">{{ __('Confirm Import') }}</button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">{{ __('Upload & Preview') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait...') }}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
