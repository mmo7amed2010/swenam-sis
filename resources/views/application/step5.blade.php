@extends('application.layout')

@section('content')
    {{-- Progress Indicator --}}
    <x-application-progress currentStep="5" />

    <form method="POST" action="{{ route('application.submit') }}" class="mt-10" enctype="multipart/form-data">
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

        {{-- Information Notice --}}
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

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-10 pt-8 border-top border-gray-300">
            <a href="{{ route('application.step4') }}" class="btn btn-light btn-lg fw-semibold px-8">
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
