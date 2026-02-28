{{-- Action Card for Pending --}}
@if($application->isPending())
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header border-0 bg-light-primary py-5">
            <h3 class="card-title fw-bold text-gray-800">
                {!! getIcon('shield-tick', 'fs-4 me-2 text-primary') !!}
                Review Actions
            </h3>
        </div>
        <div class="card-body p-6">
            <div class="d-grid gap-3">
                <button type="button"
                        class="btn btn-info btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#initialApproveModal">
                    {!! getIcon('shield-tick', 'fs-3') !!}
                    <span>Initial Approve</span>
                </button>

                <button type="button"
                        class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Application</span>
                </button>
            </div>

            <div class="separator separator-dashed my-5"></div>

            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                <div class="text-gray-700 fs-7">
                    <strong>Initial Approve:</strong> Use this to mark the application for further discussion with the student (pricing, etc.).
                    No student account will be created yet.
                </div>
            </div>
        </div>
    </div>
@elseif($application->isInitialApproved())
    {{-- Action Card for Initial Approved: Send Contract or Final Approve --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header border-0 bg-light-info py-5">
            <h3 class="card-title fw-bold text-gray-800">
                {!! getIcon('shield-tick', 'fs-4 me-2 text-info') !!}
                Next Step
            </h3>
        </div>
        <div class="card-body p-6">
            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                <div class="text-gray-700 fs-7">
                    This application has been initially approved. You can send an enrollment contract or directly approve the application.
                </div>
            </div>

            <div class="d-grid gap-3">
                <a href="{{ route('admin.applications.contract.create', $application) }}"
                   class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                    {!! getIcon('send', 'fs-3') !!}
                    <span>Send Contract</span>
                </a>

                <button type="button"
                        class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#approveModal">
                    {!! getIcon('check-circle', 'fs-3') !!}
                    <span>Final Approve (Skip Contract)</span>
                </button>

                <button type="button"
                        class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Application</span>
                </button>
            </div>
        </div>
    </div>
@elseif($application->isContractSent())
    {{-- Contract Sent: Waiting for student --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header border-0 bg-light-primary py-5">
            <h3 class="card-title fw-bold text-gray-800">
                {!! getIcon('send', 'fs-4 me-2 text-primary') !!}
                Contract Sent
            </h3>
        </div>
        <div class="card-body p-6">
            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                <div class="text-gray-700 fs-7">
                    Waiting for the student to upload their signed contract.
                </div>
            </div>

            <div class="d-grid gap-3">
                <button type="button"
                        class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Application</span>
                </button>
            </div>
        </div>
    </div>
@elseif($application->isContractUploaded())
    {{-- Contract Uploaded: Approve/Reject --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header border-0 bg-light-info py-5">
            <h3 class="card-title fw-bold text-gray-800">
                {!! getIcon('document', 'fs-4 me-2 text-info') !!}
                Review Signed Contract
            </h3>
        </div>
        <div class="card-body p-6">
            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                <div class="text-gray-700 fs-7">
                    The student has uploaded their signed contract. Review and approve or reject.
                </div>
            </div>

            @if($application->latestContract?->signed_pdf_path)
                <a href="{{ route('admin.contracts.download-signed', $application->latestContract) }}" class="btn btn-light-primary w-100 mb-4">
                    {!! getIcon('down', 'fs-5 me-1') !!}
                    Download Signed Contract
                </a>
            @endif

            <div class="d-grid gap-3">
                <button type="button"
                        class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#approveContractModal">
                    {!! getIcon('check-circle', 'fs-3') !!}
                    <span>Approve Contract</span>
                </button>

                <button type="button"
                        class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectContractModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Contract</span>
                </button>

                <button type="button"
                        class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Application</span>
                </button>
            </div>
        </div>
    </div>
@elseif($application->isContractApproved() && $application->isSelfFunded())
    {{-- Contract Approved (Self-Funded): Waiting for payment --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header border-0 bg-light-warning py-5">
            <h3 class="card-title fw-bold text-gray-800">
                {!! getIcon('wallet', 'fs-4 me-2 text-warning') !!}
                Awaiting Payment
            </h3>
        </div>
        <div class="card-body p-6">
            @if($application->payment_amount)
                <div class="d-flex align-items-center bg-light-warning rounded p-4 mb-4">
                    <div>
                        <div class="text-gray-500 fs-8">Payment Amount</div>
                        <div class="fw-bolder text-gray-800 fs-2">${{ number_format($application->payment_amount, 2) }}</div>
                    </div>
                </div>
            @endif
            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                <div class="text-gray-700 fs-7">
                    Contract approved. Waiting for student to upload payment receipt.
                </div>
            </div>
            <div class="d-grid gap-3">
                <button type="button"
                        class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Application</span>
                </button>
            </div>
        </div>
    </div>
@elseif($application->isPaymentPending())
    {{-- Payment Pending: Waiting for student --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header border-0 bg-light-warning py-5">
            <h3 class="card-title fw-bold text-gray-800">
                {!! getIcon('wallet', 'fs-4 me-2 text-warning') !!}
                Awaiting Payment
            </h3>
        </div>
        <div class="card-body p-6">
            @if($application->payment_amount)
                <div class="d-flex align-items-center bg-light-warning rounded p-4 mb-4">
                    <div>
                        <div class="text-gray-500 fs-8">Payment Amount</div>
                        <div class="fw-bolder text-gray-800 fs-2">${{ number_format($application->payment_amount, 2) }}</div>
                    </div>
                </div>
            @endif
            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                <div class="text-gray-700 fs-7">
                    Waiting for the student to upload their payment receipt.
                </div>
            </div>
            <div class="d-grid gap-3">
                <button type="button"
                        class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Application</span>
                </button>
            </div>
        </div>
    </div>
@elseif($application->isPaymentUploaded())
    {{-- Payment Uploaded: Approve/Reject --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header border-0 bg-light-info py-5">
            <h3 class="card-title fw-bold text-gray-800">
                {!! getIcon('wallet', 'fs-4 me-2 text-info') !!}
                Review Payment
            </h3>
        </div>
        <div class="card-body p-6">
            @if($application->payment_amount)
                <div class="d-flex align-items-center bg-light-info rounded p-4 mb-4">
                    <div>
                        <div class="text-gray-500 fs-8">Expected Payment Amount</div>
                        <div class="fw-bolder text-gray-800 fs-2">${{ number_format($application->payment_amount, 2) }}</div>
                    </div>
                </div>
            @endif
            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                <div class="text-gray-700 fs-7">
                    The student has uploaded their payment receipt. Review and approve or reject.
                </div>
            </div>

            @if($application->payment_receipt_path)
                <a href="{{ route('admin.applications.payment.download', $application) }}" class="btn btn-light-warning w-100 mb-4">
                    {!! getIcon('down', 'fs-5 me-1') !!}
                    Download Payment Receipt
                </a>
            @endif

            <div class="d-grid gap-3">
                <button type="button"
                        class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#approvePaymentModal">
                    {!! getIcon('check-circle', 'fs-3') !!}
                    <span>Approve Payment</span>
                </button>

                <button type="button"
                        class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectPaymentModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Payment</span>
                </button>

                <button type="button"
                        class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    {!! getIcon('cross-circle', 'fs-3') !!}
                    <span>Reject Application</span>
                </button>
            </div>
        </div>
    </div>
@else
    {{-- Status Card for Reviewed (Approved/Rejected/Payment Approved) --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body text-center p-8">
            <div class="symbol symbol-80px mb-5">
                <span class="symbol-label bg-light-{{ $config['badge'] }}">
                    {!! getIcon($config['icon'], 'fs-2x text-' . $config['badge']) !!}
                </span>
            </div>
            <h2 class="fw-bolder text-gray-800 mb-2">{{ $config['label'] }}</h2>
            <p class="text-gray-600 fs-7 mb-0">
                @if($application->status === 'approved')
                    Student account has been created successfully
                @elseif($application->status === 'rejected')
                    This application has been rejected
                @else
                    Processing...
                @endif
            </p>
        </div>
    </div>

    @if($application->isRejected())
        <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-5 mb-5">
            {!! getIcon('information-5', 'fs-2x text-danger me-3 flex-shrink-0') !!}
            <div>
                <h4 class="text-danger fw-bold mb-1">Application Rejected</h4>
                <p class="text-gray-700 fs-7 mb-0">The applicant must submit a new application to be considered.</p>
            </div>
        </div>
    @endif

    {{-- NOA Actions (only for approved applications) --}}
    @if($application->status === 'approved')
        @if($application->canRequestNoa())
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header border-0 bg-light-primary py-5">
                    <h3 class="card-title fw-bold text-gray-800">
                        {!! getIcon('document', 'fs-4 me-2 text-primary') !!}
                        NOA (Notice of Acceptance)
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            Request the student to upload their Notice of Acceptance (NOA) document. The student will be notified via email.
                        </div>
                    </div>

                    <form action="{{ route('admin.applications.noa.request', $application) }}" method="POST">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                                {!! getIcon('send', 'fs-3') !!}
                                <span>Request NOA</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($application->isNoaRequested())
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header border-0 bg-light-warning py-5">
                    <h3 class="card-title fw-bold text-gray-800">
                        {!! getIcon('document', 'fs-4 me-2 text-warning') !!}
                        NOA Requested
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                        {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            Waiting for the student to upload their NOA document.
                        </div>
                    </div>

                    <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                        {!! getIcon('time', 'fs-4 text-warning me-3') !!}
                        <span class="text-gray-700 fs-7">
                            <strong>{{ $application->noa_elapsed_days ?? 0 }} days</strong> since NOA was requested
                        </span>
                    </div>
                </div>
            </div>
        @elseif($application->canReviewNoa())
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header border-0 bg-light-info py-5">
                    <h3 class="card-title fw-bold text-gray-800">
                        {!! getIcon('document', 'fs-4 me-2 text-info') !!}
                        Review NOA
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                        {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            The student has uploaded their NOA document. Review and approve or reject.
                        </div>
                    </div>

                    @if($application->noa_document_path)
                        <a href="{{ route('admin.applications.noa.download', $application) }}" class="btn btn-light-primary w-100 mb-4">
                            {!! getIcon('down', 'fs-5 me-1') !!}
                            Download NOA Document
                        </a>
                    @endif

                    <div class="d-grid gap-3">
                        <button type="button"
                                class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                                data-bs-toggle="modal"
                                data-bs-target="#approveNoaModal">
                            {!! getIcon('check-circle', 'fs-3') !!}
                            <span>Approve NOA</span>
                        </button>

                        <button type="button"
                                class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2"
                                data-bs-toggle="modal"
                                data-bs-target="#rejectNoaModal">
                            {!! getIcon('cross-circle', 'fs-3') !!}
                            <span>Reject NOA</span>
                        </button>
                    </div>
                </div>
            </div>
        @elseif($application->isNoaApproved())
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header border-0 bg-light-success py-5">
                    <h3 class="card-title fw-bold text-gray-800">
                        {!! getIcon('check-circle', 'fs-4 me-2 text-success') !!}
                        NOA Approved
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4 mb-5">
                        {!! getIcon('check-circle', 'fs-2x text-success me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            The NOA document has been approved.
                        </div>
                    </div>

                    @if($application->noa_document_path)
                        <a href="{{ route('admin.applications.noa.download', $application) }}" class="btn btn-light-primary w-100">
                            {!! getIcon('down', 'fs-5 me-1') !!}
                            Download NOA Document
                        </a>
                    @endif
                </div>
            </div>
        @elseif($application->noa_status === 'rejected')
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header border-0 bg-light-danger py-5">
                    <h3 class="card-title fw-bold text-gray-800">
                        {!! getIcon('cross-circle', 'fs-4 me-2 text-danger') !!}
                        NOA Rejected
                    </h3>
                </div>
                <div class="card-body p-6">
                    @if($application->noa_rejection_reason)
                        <div class="bg-light-danger rounded p-4 mb-5">
                            <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-2 d-block">Rejection Reason</label>
                            <p class="text-danger mb-0">{{ $application->noa_rejection_reason }}</p>
                        </div>
                    @endif

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                        <div class="text-gray-700 fs-7">
                            The student can re-upload a new NOA document.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- MSFAA Actions (only after NOA is approved) --}}
        @if($application->isNoaApproved())
            @if($application->canRequestMsfaa())
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-primary py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('verify', 'fs-4 me-2 text-primary') !!}
                            MSFAA
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                Request the student to confirm their Master Student Financial Assistance Agreement (MSFAA). The student will be notified via email.
                            </div>
                        </div>

                        <form action="{{ route('admin.applications.msfaa.request', $application) }}" method="POST">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                                    {!! getIcon('send', 'fs-3') !!}
                                    <span>Request MSFAA</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($application->isMsfaaRequested())
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-warning py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('verify', 'fs-4 me-2 text-warning') !!}
                            MSFAA Requested
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                            {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                Waiting for the student to confirm their MSFAA.
                            </div>
                        </div>

                        <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                            {!! getIcon('time', 'fs-4 text-warning me-3') !!}
                            <span class="text-gray-700 fs-7">
                                <strong>{{ $application->msfaa_elapsed_days ?? 0 }} days</strong> since MSFAA was requested
                            </span>
                        </div>
                    </div>
                </div>
            @elseif($application->canReviewMsfaa())
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-info py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('verify', 'fs-4 me-2 text-info') !!}
                            Review MSFAA
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                The student has confirmed their MSFAA. Review and approve or reject.
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveMsfaaModal">
                                {!! getIcon('check-circle', 'fs-3') !!}
                                <span>Approve MSFAA</span>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectMsfaaModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject MSFAA</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($application->isMsfaaApproved())
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-success py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('check-circle', 'fs-4 me-2 text-success') !!}
                            MSFAA Approved
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4">
                            {!! getIcon('check-circle', 'fs-2x text-success me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                The MSFAA has been approved.
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($application->isMsfaaRejected())
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-danger py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('cross-circle', 'fs-4 me-2 text-danger') !!}
                            MSFAA Rejected
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        @if($application->msfaa_rejection_reason)
                            <div class="bg-light-danger rounded p-4 mb-5">
                                <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-2 d-block">Rejection Reason</label>
                                <p class="text-danger mb-0">{{ $application->msfaa_rejection_reason }}</p>
                            </div>
                        @endif

                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                            {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                The student can re-confirm their MSFAA.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif

    @if(!$application->isRejected())
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-6">
                <div class="separator separator-dashed mb-4"></div>
                <div class="d-grid">
                    <button type="button"
                            class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal">
                        {!! getIcon('cross-circle', 'fs-3') !!}
                        <span>Reject Application</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
@endif
