{{-- Change Documents Modal --}}
<div class="modal fade" id="changeDocumentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-info border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-info">
                            {!! getIcon('document', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Upload / Replace Documents</h2>
                        <p class="text-gray-600 fs-7 mb-0">Update supporting documents for {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.update-documents', $application) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-8">
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-6">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            Only upload files you want to <strong>replace</strong>. Leave fields empty to keep existing documents.
                        </div>
                    </div>

                    @php
                        $docFields = [
                            ['key' => 'government_id', 'label' => 'Government-Issued Photo ID', 'path' => $application->government_id_path],
                            ['key' => 'degree_certificate', 'label' => 'Degree Certificate', 'path' => $application->degree_certificate_path],
                            ['key' => 'transcripts', 'label' => 'Academic Transcripts', 'path' => $application->transcripts_path],
                            ['key' => 'cv', 'label' => 'Curriculum Vitae', 'path' => $application->cv_path],
                            ['key' => 'english_test', 'label' => 'English Test Results', 'path' => $application->english_test_path],
                        ];
                    @endphp

                    @foreach($docFields as $doc)
                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold d-flex align-items-center gap-2">
                                {{ $doc['label'] }}
                                @if($doc['path'])
                                    <span class="badge badge-light-success fs-8">Uploaded</span>
                                @else
                                    <span class="badge badge-light-secondary fs-8">Not uploaded</span>
                                @endif
                            </label>
                            <input type="file" name="{{ $doc['key'] }}" class="form-control form-control-solid @error($doc['key']) is-invalid @enderror"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            @error($doc['key'])
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Upload Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
