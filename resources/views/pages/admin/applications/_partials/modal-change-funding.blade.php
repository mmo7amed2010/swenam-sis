{{-- Change Funding Type Modal --}}
<div class="modal fade" id="changeFundingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-primary border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-primary">
                            {!! getIcon('wallet', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Change Funding Type</h2>
                        <p class="text-gray-600 fs-7 mb-0">Update the funding type for {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.update-funding', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="form-label text-gray-700 fw-semibold required">Funding Type</label>
                        <div class="d-flex flex-column gap-3">
                            <label class="d-flex align-items-center bg-gray-100 rounded p-4 cursor-pointer border border-2 {{ $application->funding_type === 'self_funded' ? 'border-primary' : 'border-transparent' }}">
                                <input type="radio" name="funding_type" value="self_funded" class="form-check-input me-3" {{ $application->funding_type === 'self_funded' ? 'checked' : '' }} required>
                                <div>
                                    <span class="text-gray-800 fw-bold">Self-Funded</span>
                                    <span class="text-gray-500 fs-7 d-block">Student is paying for their own education</span>
                                </div>
                            </label>
                            <label class="d-flex align-items-center bg-gray-100 rounded p-4 cursor-pointer border border-2 {{ $application->funding_type === 'government_funded' ? 'border-primary' : 'border-transparent' }}">
                                <input type="radio" name="funding_type" value="government_funded" class="form-check-input me-3" {{ $application->funding_type === 'government_funded' ? 'checked' : '' }}>
                                <div>
                                    <span class="text-gray-800 fw-bold">Government-Funded</span>
                                    <span class="text-gray-500 fs-7 d-block">Student is funded by a government program</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Update Funding Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
