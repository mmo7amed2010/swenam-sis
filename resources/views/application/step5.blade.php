@extends(session()->has('agent_submission') ? 'application.agent-layout' : 'application.layout')
@php $rp = session()->has('agent_submission') ? 'agent.application.' : 'application.'; @endphp

@section('content')
    {{-- Progress Indicator --}}
    <x-application-progress currentStep="5" />

    <form method="POST" action="{{ route($rp . 'submit') }}" class="mt-10" enctype="multipart/form-data">
        @csrf

        {{-- Section Title --}}
        <div class="mb-10">
            <h2 class="text-gray-900 fw-bold fs-2 mb-2">Supporting Documents</h2>
            <p class="text-gray-600 fs-6">Upload all required documents to complete your application</p>
        </div>

        {{-- Instructions --}}
        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-10">
            <i class="ki-outline ki-information-5 fs-2tx text-primary me-4"></i>
            <div class="d-flex flex-stack flex-grow-1">
                <div class="fw-semibold">
                    <h6 class="text-gray-900 fw-bold mb-3">File Upload Guidelines</h6>
                    <div class="fs-6 text-gray-700">
                        <ul class="mb-0">
                            <li>All files must be in PDF, JPG, PNG, or DOCX format</li>
                            <li>Maximum file size: 10MB per file</li>
                            <li>Ensure all documents are clear and legible</li>
                            <li>Upload original or certified copies of documents</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documents Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-document fs-3 text-primary me-2"></i>
                Required Documents
            </h3>

            <div class="row g-6">
                {{-- Government-Issued Photo ID --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">
                        <i class="ki-outline ki-verify fs-5 text-primary me-2"></i>
                        Government-Issued Photo ID
                    </label>
                    <input type="file" name="government_id" id="government_id" class="form-control form-control-lg @error('government_id') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="form-text mt-2">
                        <i class="ki-outline ki-information-5 fs-6 text-muted me-1"></i>
                        Upload a clear photo of your passport, official national ID
                    </div>
                    @error('government_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Degree Certificate --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">
                        <i class="ki-outline ki-file-up fs-5 text-primary me-2"></i>
                        Degree Certificate
                    </label>
                    <input type="file" name="degree_certificate" id="degree_certificate" class="form-control form-control-lg @error('degree_certificate') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                    <div class="form-text mt-2">
                        <i class="ki-outline ki-information-5 fs-6 text-muted me-1"></i>
                        Upload your most recent degree or diploma certificate
                    </div>
                    @error('degree_certificate')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Academic Transcripts --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">
                        <i class="ki-outline ki-file-up fs-5 text-primary me-2"></i>
                        Academic Transcripts
                    </label>
                    <input type="file" name="transcripts" id="transcripts" class="form-control form-control-lg @error('transcripts') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                    <div class="form-text mt-2">
                        <i class="ki-outline ki-information-5 fs-6 text-muted me-1"></i>
                        Upload your official academic transcripts
                    </div>
                    @error('transcripts')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Curriculum Vitae (CV) --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">
                        <i class="ki-outline ki-file-up fs-5 text-primary me-2"></i>
                        up-to-date CV
                    </label>
                    <input type="file" name="cv" id="cv" class="form-control form-control-lg @error('cv') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                    <div class="form-text mt-2">
                        <i class="ki-outline ki-information-5 fs-6 text-muted me-1"></i>
                        Upload your current CV or resume
                    </div>
                    @error('cv')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- English Language Test Results (Optional) --}}
                <div class="col-12">
                    <label class="form-label fs-6 fw-semibold mb-3">
                        <i class="ki-outline ki-file-up fs-5 text-primary me-2"></i>
                        English Language Test Results <span class="text-muted">(Optional)</span>
                    </label>
                    <input type="file" name="english_test" id="english_test" class="form-control form-control-lg @error('english_test') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.docx">
                    <div class="form-text mt-2">
                        <i class="ki-outline ki-information-5 fs-6 text-muted me-1"></i>
                        Upload IELTS, TOEFL, or other English proficiency test results (if applicable)
                    </div>
                    @error('english_test')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        @if(session()->has('agent_submission'))
            {{-- Agent Information Notice --}}
            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-10">
                <i class="ki-outline ki-information-5 fs-2tx text-primary me-4"></i>
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-semibold">
                        <h6 class="text-gray-900 fw-bold mb-3">Before You Submit</h6>
                        <div class="fs-6 text-gray-700">
                            <ul class="mb-0">
                                <li>Please review all student information for accuracy</li>
                                <li>Ensure all required documents are uploaded</li>
                                <li>The student will receive a confirmation email with the application reference number</li>
                                <li>You can track the application status from your agent dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Student Information Notice --}}
            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-10">
                <i class="ki-outline ki-information-5 fs-2tx text-primary me-4"></i>
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-semibold">
                        <h6 class="text-gray-900 fw-bold mb-3">Before You Submit</h6>
                        <div class="fs-6 text-gray-700">
                            <ul class="mb-0">
                                <li>Please review all information for accuracy</li>
                                <li>Ensure all required documents are uploaded</li>
                                <li>You will receive a confirmation email with your application reference number</li>
                                <li>Our admissions team will review your application and contact you within 5-7 business days</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Declarations & Acknowledgments --}}
        @php
            $isGovernmentFunded = ($data['funding_type'] ?? '') === 'government_funded';
        @endphp
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-2">
                <i class="ki-outline ki-shield-tick fs-3 text-primary me-2"></i>
                Declarations & Acknowledgments
            </h3>
            <p class="text-gray-600 fs-6 mb-6">Please read each statement carefully and confirm your agreement</p>

            @if($isGovernmentFunded)
                {{-- Government Funding Loan Acknowledgment --}}
                <div class="mb-8">
                    <p class="fs-6 text-gray-700 mb-3">
                        Do you acknowledge and fully understand that any government funding you may receive can include a repayable loan component, which constitutes a legal financial obligation that must be repaid in full in accordance with the terms, conditions, and repayment schedule established by the applicable government funding authority (such as StudentAid BC or other provincial/federal bodies), and that only a portion of the funding, if approved based on your eligibility assessment, may be provided as a non-repayable grant?
                    </p>
                    <label class="form-check form-check-custom form-check-solid form-check-lg">
                        <input type="checkbox"
                               name="ack_gov_funding_loan"
                               value="1"
                               class="form-check-input @error('ack_gov_funding_loan') is-invalid @enderror"
                               {{ old('ack_gov_funding_loan') ? 'checked' : '' }}
                               required />
                        <span class="form-check-label fw-semibold text-gray-700">Yes, I understand and acknowledge the above <span class="text-danger">*</span></span>
                    </label>
                    @error('ack_gov_funding_loan')
                        <div class="invalid-feedback d-block ms-9">{{ $message }}</div>
                    @enderror
                </div>

                <div class="separator separator-dashed mb-8"></div>
            @endif

            {{-- Applicant Information Declaration --}}
            <div class="mb-8">
                <p class="fs-6 text-gray-700 mb-3">
                    Do you acknowledge and declare that all information, contact details, and supporting documents provided in this application are complete, accurate, and truthful to the best of your knowledge, and that you bear full responsibility for ensuring their correctness? You further understand that providing false, misleading, outdated, or incomplete information may result in the refusal of admission, cancellation of enrollment, reporting to applicable authorities (including funding bodies such as StudentAid), and any other actions deemed necessary by the institution.
                </p>
                <label class="form-check form-check-custom form-check-solid form-check-lg">
                    <input type="checkbox"
                           name="ack_information_declaration"
                           value="1"
                           class="form-check-input @error('ack_information_declaration') is-invalid @enderror"
                           {{ old('ack_information_declaration') ? 'checked' : '' }}
                           required />
                    <span class="form-check-label fw-semibold text-gray-700">Yes, I confirm and acknowledge the above <span class="text-danger">*</span></span>
                </label>
                @error('ack_information_declaration')
                    <div class="invalid-feedback d-block ms-9">{{ $message }}</div>
                @enderror
            </div>

            @if($isGovernmentFunded)
                {{-- Academic Engagement (Government Funded Only) --}}
                <div class="separator separator-dashed mb-8"></div>
                <div class="mb-8">
                    <p class="fs-6 text-gray-700 mb-3">
                        Do you acknowledge and understand that you are required to actively participate in your program (including logging into the learning platform, completing assignments, and engaging with course materials), and that failure to demonstrate academic engagement may result in termination of your enrollment and notification to applicable funding authorities (e.g., StudentAid), which may impact your funding eligibility?
                    </p>
                    <label class="form-check form-check-custom form-check-solid form-check-lg">
                        <input type="checkbox"
                               name="ack_gov_academic_engagement"
                               value="1"
                               class="form-check-input @error('ack_gov_academic_engagement') is-invalid @enderror"
                               {{ old('ack_gov_academic_engagement') ? 'checked' : '' }}
                               required />
                        <span class="form-check-label fw-semibold text-gray-700">Yes, I understand and acknowledge <span class="text-danger">*</span></span>
                    </label>
                    @error('ack_gov_academic_engagement')
                        <div class="invalid-feedback d-block ms-9">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            {{-- Contact Information Responsibility --}}
            <div class="separator separator-dashed mb-8"></div>
            <div class="mb-8">
                <p class="fs-6 text-gray-700 mb-3">
                    Do you acknowledge that it is your responsibility to provide valid and active contact information (phone number and email) and to regularly monitor and respond to communications from the college, and that failure to do so may affect your enrollment status and access to services?
                </p>
                <label class="form-check form-check-custom form-check-solid form-check-lg">
                    <input type="checkbox"
                           name="ack_contact_responsibility"
                           value="1"
                           class="form-check-input @error('ack_contact_responsibility') is-invalid @enderror"
                           {{ old('ack_contact_responsibility') ? 'checked' : '' }}
                           required />
                    <span class="form-check-label fw-semibold text-gray-700">Yes, I understand and acknowledge <span class="text-danger">*</span></span>
                </label>
                @error('ack_contact_responsibility')
                    <div class="invalid-feedback d-block ms-9">{{ $message }}</div>
                @enderror
            </div>

            {{-- Non-Attendance / Withdrawal --}}
            <div class="separator separator-dashed mb-8"></div>
            <div class="mb-8">
                <p class="fs-6 text-gray-700 mb-3">
                    Do you understand that failure to attend, participate, or respond to institutional communications may be considered non-attendance or withdrawal, which may result in cancellation of your enrollment, reporting to funding authorities, and financial consequences in accordance with institutional and regulatory policies?
                </p>
                <label class="form-check form-check-custom form-check-solid form-check-lg">
                    <input type="checkbox"
                           name="ack_non_attendance"
                           value="1"
                           class="form-check-input @error('ack_non_attendance') is-invalid @enderror"
                           {{ old('ack_non_attendance') ? 'checked' : '' }}
                           required />
                    <span class="form-check-label fw-semibold text-gray-700">Yes, I understand and acknowledge <span class="text-danger">*</span></span>
                </label>
                @error('ack_non_attendance')
                    <div class="invalid-feedback d-block ms-9">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-10 pt-8 border-top border-gray-300">
            <a href="{{ route($rp . 'step4') }}" class="btn btn-light btn-lg fw-semibold px-8">
                <i class="ki-outline ki-arrow-left fs-3 me-2"></i>
                Previous
            </a>
            <button type="submit" class="btn btn-primary btn-lg fw-semibold px-8">
                <i class="ki-outline ki-check fs-3 me-2"></i>
                Submit Application
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            // File size validation on client side for better UX
            document.querySelectorAll('input[type="file"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                        if (file.size > maxSize) {
                            alert('File size must not exceed 10MB. Please choose a smaller file.');
                            this.value = '';
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
