{{-- Change Program Modal --}}
@if(!$application->isRejected() && isset($programs))
<div class="modal fade" id="changeProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-primary border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-primary">
                            {!! getIcon('abstract-26', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Change Program</h2>
                        <p class="text-gray-600 fs-7 mb-0">Update the program for {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.update-program', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="form-label text-gray-700 fw-semibold required">Select New Program</label>
                        <select name="program_id" class="form-select form-select-solid @error('program_id') is-invalid @enderror" required>
                            <option value="">-- Select Program --</option>
                            @foreach($programs as $program)
                                <option value="{{ $program['id'] }}" {{ (int)$application->program_id === (int)$program['id'] ? 'selected' : '' }}>
                                    {{ $program['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-primary me-3') !!}
                                <span class="text-gray-700 fs-7">Update the program on this <strong>application record</strong></span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-primary me-3') !!}
                                <span class="text-gray-700 fs-7">Update the program on the <strong>SIS student account</strong> (if exists)</span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-primary me-3') !!}
                                <span class="text-gray-700 fs-7">Sync the program to the <strong>LMS student account</strong> (if exists)</span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('document', 'fs-4 text-info me-3') !!}
                                <span class="text-gray-700 fs-7">Create an <strong>audit log</strong> entry for this change</span>
                            </div>
                        </div>
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Important:</strong> If the student has an LMS account, this change will be synced to LMS. The operation will fail if LMS is unreachable.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Update Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
