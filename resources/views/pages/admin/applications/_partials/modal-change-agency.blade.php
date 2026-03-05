{{-- Change Agency Referral Modal --}}
@if(!$application->isRejected())
<div class="modal fade" id="changeAgencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-primary border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-primary">
                            {!! getIcon('people', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Change Agency Referral</h2>
                        <p class="text-gray-600 fs-7 mb-0">Update the agency referral for {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.update-agency', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="form-label text-gray-700 fw-semibold required">Has Agency Referral?</label>
                        <div class="d-flex gap-4">
                            <label class="d-flex align-items-center cursor-pointer">
                                <input type="radio" name="has_referral" value="1" class="form-check-input me-2" onchange="document.getElementById('agencyNameField').style.display='block'" {{ $application->has_referral ? 'checked' : '' }}>
                                <span class="text-gray-700 fw-semibold">Yes</span>
                            </label>
                            <label class="d-flex align-items-center cursor-pointer">
                                <input type="radio" name="has_referral" value="0" class="form-check-input me-2" onchange="document.getElementById('agencyNameField').style.display='none'" {{ !$application->has_referral ? 'checked' : '' }}>
                                <span class="text-gray-700 fw-semibold">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6" id="agencyNameField" style="{{ $application->has_referral ? '' : 'display: none;' }}">
                        <label class="form-label text-gray-700 fw-semibold required">Agency Name</label>
                        <input type="text" name="referral_agency_name" class="form-control form-control-solid @error('referral_agency_name') is-invalid @enderror"
                               value="{{ old('referral_agency_name', $application->referral_agency_name) }}"
                               placeholder="Enter the agency name"
                               maxlength="255">
                        @error('referral_agency_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Update Agency Referral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
