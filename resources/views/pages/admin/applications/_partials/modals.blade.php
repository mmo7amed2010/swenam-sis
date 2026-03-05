{{-- Initial Approve Modal --}}
<div class="modal fade" id="initialApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-info border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-info">
                            {!! getIcon('shield-tick', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Initial Approve Application</h2>
                        <p class="text-gray-600 fs-7 mb-0">Mark {{ $application->full_name }}'s application for further processing</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.initial-approve', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-info me-3') !!}
                                <span class="text-gray-700 fs-7">Mark the application as <strong>Initially Approved</strong></span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('information-5', 'fs-4 text-warning me-3') !!}
                                <span class="text-gray-700 fs-7"><strong>No student account</strong> will be created yet</span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('information-5', 'fs-4 text-warning me-3') !!}
                                <span class="text-gray-700 fs-7"><strong>No email</strong> will be sent to the applicant</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add notes about pricing discussions, next steps, etc..."></textarea>
                    </div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Next Step:</strong> Contact the student offline to discuss pricing and enrollment details.
                            Return here to complete the final approval.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        {!! getIcon('shield-tick', 'fs-4 me-2') !!}
                        Initial Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Final Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-success border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-success">
                            {!! getIcon('check', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Final Approve Application</h2>
                        <p class="text-gray-600 fs-7 mb-0">Complete {{ $application->full_name }}'s enrollment</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">Create a student account with email <strong>{{ $application->email }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">Send login credentials to the applicant</span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">Enroll in <strong>{{ $application->program_name ?? 'the program' }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes about this approval...">{{ $application->admin_notes }}</textarea>
                    </div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Note:</strong> This action cannot be undone. Please ensure all information has been verified.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Final Approve & Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-danger border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-danger">
                            {!! getIcon('cross', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Reject Application</h2>
                        <p class="text-gray-600 fs-7 mb-0">Reject {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.reject', $application) }}" method="POST" x-data="{ reason: '', minLength: 20, maxLength: 1000 }">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold required">Rejection Reason</label>
                        <textarea
                            name="rejection_reason"
                            class="form-control form-control-solid @error('rejection_reason') is-invalid @enderror"
                            rows="5"
                            placeholder="Provide a clear and detailed reason for rejection..."
                            x-model="reason"
                            required
                            minlength="20"
                            maxlength="1000"></textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-gray-500 fs-8">
                                <span x-text="reason.length"></span> / <span x-text="maxLength"></span> characters
                            </span>
                            <span x-show="reason.length > 0 && reason.length < minLength" class="text-danger fs-8">
                                Minimum <span x-text="minLength"></span> characters required
                            </span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes..."></textarea>
                    </div>

                    <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-danger me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Warning:</strong> This action cannot be undone. The applicant will be notified of the rejection.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" :disabled="reason.length < minLength">
                        {!! getIcon('cross', 'fs-4 me-2') !!}
                        Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-light-primary">
                <h2 class="fw-bold text-gray-800" id="previewTitle">Document Preview</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <div class="modal-body p-0" id="previewBody" style="min-height: 600px; overflow: hidden;">
                <!-- Dynamic content loaded here -->
            </div>
        </div>
    </div>
</div>

{{-- Approve Contract Modal --}}
<div class="modal fade" id="approveContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-success border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-success">
                            {!! getIcon('check', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Approve Contract</h2>
                        <p class="text-gray-600 fs-7 mb-0">Approve {{ $application->full_name }}'s signed contract</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.contract.approve', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">Mark the contract as <strong>Approved</strong></span>
                            </div>
                            @if($application->isGovernmentFunded())
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                    <span class="text-gray-700 fs-7">Auto-approve the application and <strong>create student account</strong></span>
                                </div>
                            @else
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('information-5', 'fs-4 text-warning me-3') !!}
                                    <span class="text-gray-700 fs-7">Move to <strong>Payment Pending</strong> (self-funded student)</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($application->isSelfFunded())
                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold required">Payment Amount</label>
                            <div class="input-group input-group-solid">
                                <span class="input-group-text fw-bold">$</span>
                                <input type="number" name="payment_amount" class="form-control form-control-solid @error('payment_amount') is-invalid @enderror" placeholder="0.00" step="0.01" min="0.01" required />
                            </div>
                            @error('payment_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-gray-500 fs-8">Enter the amount the student needs to pay to complete enrollment.</div>
                        </div>
                    @endif

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any notes...">{{ $application->admin_notes }}</textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Approve Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Contract Modal --}}
<div class="modal fade" id="rejectContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-warning border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-warning">
                            {!! getIcon('cross', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Reject Contract</h2>
                        <p class="text-gray-600 fs-7 mb-0">Choose how to handle the rejected contract</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.contract.reject', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="form-label text-gray-700 fw-semibold required">Rejection Action</label>
                        <div class="d-flex flex-column gap-3">
                            <label class="d-flex align-items-start bg-gray-100 rounded p-4 cursor-pointer border border-2 border-transparent" id="rejectOnlyLabel">
                                <input type="radio" name="reject_action" value="reject_only" class="form-check-input mt-1 me-3" checked id="rejectOnlyRadio">
                                <div>
                                    <div class="fw-bold text-gray-800 fs-6">Reject Only</div>
                                    <div class="text-gray-600 fs-7">Student will re-upload their signed contract using the existing PDF.</div>
                                </div>
                            </label>
                            <label class="d-flex align-items-start bg-gray-100 rounded p-4 cursor-pointer border border-2 border-transparent" id="regenerateLabel">
                                <input type="radio" name="reject_action" value="reject_and_regenerate" class="form-check-input mt-1 me-3" id="regenerateRadio">
                                <div>
                                    <div class="fw-bold text-gray-800 fs-6">
                                        {!! getIcon('arrows-circle', 'fs-6 me-1 text-primary') !!}
                                        Reject & Regenerate Contract
                                    </div>
                                    <div class="text-gray-600 fs-7">Generate a new contract PDF from the latest template and re-send to the student. Use this when the template has been updated.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="separator separator-dashed mb-6"></div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold required">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control form-control-solid" rows="4" placeholder="Explain why the signed contract is being rejected..." required minlength="10"></textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes..."></textarea>
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4" id="rejectOnlyNotice">
                        {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            The student will be able to re-upload their signed contract using the existing contract PDF.
                        </div>
                    </div>
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4 d-none" id="regenerateNotice">
                        {!! getIcon('arrows-circle', 'fs-2x text-primary me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            A <strong>new contract PDF</strong> will be generated from the latest template and emailed to the student. They must download, sign, and upload the new version.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="rejectContractBtn">
                        {!! getIcon('cross', 'fs-4 me-2') !!}
                        Reject Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Approve Payment Modal --}}
<div class="modal fade" id="approvePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-success border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-success">
                            {!! getIcon('check', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Approve Payment</h2>
                        <p class="text-gray-600 fs-7 mb-0">Approve {{ $application->full_name }}'s payment receipt</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.payment.approve', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">Mark the payment as <strong>Approved</strong></span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7"><strong>Approve the application</strong> and create student LMS account</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any notes...">{{ $application->admin_notes }}</textarea>
                    </div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Note:</strong> This action cannot be undone. The student will be fully enrolled.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Approve Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Payment Modal --}}
<div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-warning border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-warning">
                            {!! getIcon('cross', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Reject Payment</h2>
                        <p class="text-gray-600 fs-7 mb-0">Student will need to re-upload their payment receipt</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.payment.reject', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold required">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control form-control-solid" rows="4" placeholder="Explain why the payment receipt is being rejected..." required minlength="10"></textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes..."></textarea>
                    </div>
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            The student will be able to re-upload their payment receipt.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        {!! getIcon('cross', 'fs-4 me-2') !!}
                        Reject Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Approve NOA Modal --}}
<div class="modal fade" id="approveNoaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-success border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-success">
                            {!! getIcon('check', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Approve NOA</h2>
                        <p class="text-gray-600 fs-7 mb-0">Approve {{ $application->full_name }}'s NOA document</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.noa.approve', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">Mark the NOA as <strong>Approved</strong></span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">The student will be notified of the approval</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Note:</strong> Approving the NOA confirms the student's Notice of Acceptance document is valid.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Approve NOA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject NOA Modal --}}
<div class="modal fade" id="rejectNoaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-warning border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-warning">
                            {!! getIcon('cross', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Reject NOA</h2>
                        <p class="text-gray-600 fs-7 mb-0">Student will need to re-upload their NOA document</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.noa.reject', $application) }}" method="POST" x-data="{ noaReason: '', minLength: 10, maxLength: 1000 }">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold required">Rejection Reason</label>
                        <textarea
                            name="rejection_reason"
                            class="form-control form-control-solid @error('rejection_reason') is-invalid @enderror"
                            rows="4"
                            placeholder="Explain why the NOA document is being rejected..."
                            x-model="noaReason"
                            required
                            minlength="10"
                            maxlength="1000"></textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-gray-500 fs-8">
                                <span x-text="noaReason.length"></span> / <span x-text="maxLength"></span> characters
                            </span>
                            <span x-show="noaReason.length > 0 && noaReason.length < minLength" class="text-danger fs-8">
                                Minimum <span x-text="minLength"></span> characters required
                            </span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes..."></textarea>
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            The student will be able to re-upload a new NOA document.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" :disabled="noaReason.length < minLength">
                        {!! getIcon('cross', 'fs-4 me-2') !!}
                        Reject NOA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Approve MSFAA Modal --}}
<div class="modal fade" id="approveMsfaaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-success border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-success">
                            {!! getIcon('check', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Approve MSFAA</h2>
                        <p class="text-gray-600 fs-7 mb-0">Approve {{ $application->full_name }}'s MSFAA confirmation</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.msfaa.approve', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                <span class="text-gray-700 fs-7">Mark the MSFAA as <strong>Approved</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Note:</strong> Approving the MSFAA confirms the student's financial assistance agreement is valid.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Approve MSFAA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject MSFAA Modal --}}
<div class="modal fade" id="rejectMsfaaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-warning border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-warning">
                            {!! getIcon('cross', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Reject MSFAA</h2>
                        <p class="text-gray-600 fs-7 mb-0">Student will need to re-confirm their MSFAA</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.msfaa.reject', $application) }}" method="POST" x-data="{ msfaaReason: '', minLength: 10, maxLength: 1000 }">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold required">Rejection Reason</label>
                        <textarea
                            name="rejection_reason"
                            class="form-control form-control-solid @error('rejection_reason') is-invalid @enderror"
                            rows="4"
                            placeholder="Explain why the MSFAA is being rejected..."
                            x-model="msfaaReason"
                            required
                            minlength="10"
                            maxlength="1000"></textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-gray-500 fs-8">
                                <span x-text="msfaaReason.length"></span> / <span x-text="maxLength"></span> characters
                            </span>
                            <span x-show="msfaaReason.length > 0 && msfaaReason.length < minLength" class="text-danger fs-8">
                                Minimum <span x-text="minLength"></span> characters required
                            </span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                        <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes..."></textarea>
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            The student will need to re-confirm their MSFAA.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" :disabled="msfaaReason.length < minLength">
                        {!! getIcon('cross', 'fs-4 me-2') !!}
                        Reject MSFAA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Change Intake Modal --}}
@if(!$application->isRejected() && isset($intakes))
<div class="modal fade" id="changeIntakeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-primary border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-primary">
                            {!! getIcon('calendar', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Change Intake</h2>
                        <p class="text-gray-600 fs-7 mb-0">Update the intake for {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.update-intake', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-6">
                        <label class="form-label text-gray-700 fw-semibold required">Select New Intake</label>
                        <select name="intake_id" class="form-select form-select-solid @error('intake_id') is-invalid @enderror" required>
                            <option value="">-- Select Intake --</option>
                            @foreach($intakes as $intake)
                                <option value="{{ $intake['id'] }}" {{ (int)$application->intake_id === (int)$intake['id'] ? 'selected' : '' }}>
                                    {{ $intake['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('intake_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-primary me-3') !!}
                                <span class="text-gray-700 fs-7">Update the intake on this <strong>application record</strong></span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-primary me-3') !!}
                                <span class="text-gray-700 fs-7">Update the intake on the <strong>SIS student account</strong> (if exists)</span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('check-circle', 'fs-4 text-primary me-3') !!}
                                <span class="text-gray-700 fs-7">Update the intake on the <strong>LMS student account</strong> (if exists)</span>
                            </div>
                            <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                {!! getIcon('document', 'fs-4 text-info me-3') !!}
                                <span class="text-gray-700 fs-7">Create an <strong>audit log</strong> entry for this change</span>
                            </div>
                        </div>
                    </div>

                    @if($application->latestContract)
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                            {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                <strong>Warning:</strong> This application already has a contract. Changing the intake will <strong>not</strong> automatically update the contract PDF. If the contract needs to reflect the new intake, use "Reject & Regenerate Contract" after changing the intake.
                            </div>
                        </div>
                    @endif

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            <strong>Note:</strong> If the student does not yet have an LMS account, the new intake will be used when the account is created upon final approval.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Update Intake
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Edit Field Modals --}}
@include('pages.admin.applications._partials.modal-change-program')
@include('pages.admin.applications._partials.modal-change-agency')
@include('pages.admin.applications._partials.modal-change-funding')
@include('pages.admin.applications._partials.modal-change-education')
@include('pages.admin.applications._partials.modal-change-work')
@include('pages.admin.applications._partials.modal-change-documents')
