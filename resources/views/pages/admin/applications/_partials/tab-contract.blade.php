@if($application->latestContract)
    <div class="tab-pane fade" id="tab_contract" role="tabpanel">
        {{-- Contract Details --}}
        <x-cards.section title="Contract Details" class="mb-5">
            @php $contract = $application->latestContract; @endphp
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <x-detail.field icon="document" label="Template Used" :value="$contract->template?->name ?? 'N/A'" color="primary" />
                </div>
                <div class="col-md-6">
                    <x-detail.field icon="calendar" label="Issued Date" :value="$contract->issued_at ? $contract->issued_at->format('F d, Y \a\t h:i A') : 'N/A'" color="primary" />
                </div>
                <div class="col-md-6">
                    <x-detail.field icon="profile-user" label="Issued By" :value="$contract->issuer?->name ?? 'N/A'" color="primary" />
                </div>
                <div class="col-md-6">
                    <x-detail.field icon="verify" label="Signed" color="primary">
                        @if($contract->isSigned())
                            <span class="badge badge-light-success">Yes - {{ $contract->signed_at?->format('M d, Y') }}</span>
                        @else
                            <span class="badge badge-light-warning">Pending</span>
                        @endif
                    </x-detail.field>
                </div>
            </div>

            <div class="d-flex gap-3">
                @if($contract->generated_pdf_path)
                    <a href="{{ route('admin.contracts.download-generated', $contract) }}" class="btn btn-sm btn-light-primary">
                        {!! getIcon('down', 'fs-5 me-1') !!}
                        Download Generated Contract
                    </a>
                @endif
                @if($contract->signed_pdf_path)
                    <a href="{{ route('admin.contracts.download-signed', $contract) }}" class="btn btn-sm btn-light-success">
                        {!! getIcon('down', 'fs-5 me-1') !!}
                        Download Signed Contract
                    </a>
                @endif
            </div>
        </x-cards.section>

        {{-- Payment Information (if self-funded) --}}
        @if($application->isSelfFunded() && $application->payment_amount)
            <x-cards.section title="Payment Information" class="mb-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-detail.field icon="wallet" label="Payment Amount" color="warning">
                            <span class="fw-bolder fs-4">${{ number_format($application->payment_amount, 2) }}</span>
                        </x-detail.field>
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="verify" label="Payment Status" color="warning">
                            @if($application->isPaymentApproved() || $application->isApproved())
                                <span class="badge badge-light-success">Approved</span>
                            @elseif($application->isPaymentUploaded())
                                <span class="badge badge-light-info">Receipt Uploaded - Awaiting Review</span>
                            @elseif($application->isPaymentPending())
                                <span class="badge badge-light-warning">Awaiting Payment</span>
                            @endif
                        </x-detail.field>
                    </div>
                </div>
            </x-cards.section>
        @endif

        {{-- Payment Receipt --}}
        @if($application->isSelfFunded() && $application->payment_receipt_path)
            <x-cards.section title="Payment Receipt">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <x-detail.field icon="calendar" label="Uploaded At" :value="$application->payment_uploaded_at ? $application->payment_uploaded_at->format('F d, Y \a\t h:i A') : 'N/A'" color="warning" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="verify" label="Payment Status" color="warning">
                            @if($application->isPaymentApproved() || $application->isApproved())
                                <span class="badge badge-light-success">Approved</span>
                            @elseif($application->isPaymentUploaded())
                                <span class="badge badge-light-info">Awaiting Review</span>
                            @else
                                <span class="badge badge-light-warning">Pending</span>
                            @endif
                        </x-detail.field>
                    </div>
                </div>
                <a href="{{ route('admin.applications.payment.download', $application) }}" class="btn btn-sm btn-light-warning">
                    {!! getIcon('down', 'fs-5 me-1') !!}
                    Download Payment Receipt
                </a>
            </x-cards.section>
        @endif
    </div>
@endif
