<x-default-layout>

    @section('title')
        Issue Contract - {{ $application->reference_number }}
    @endsection

    <div class="d-flex flex-wrap flex-stack mb-6">
        <div>
            <h2 class="fw-bold my-2">
                {!! getIcon('document', 'fs-2 me-2 text-primary') !!}
                Issue Contract
            </h2>
            <p class="text-gray-600 fs-7 mb-0">
                For: <strong>{{ $application->full_name }}</strong> ({{ $application->reference_number }})
                &mdash; {{ $application->program_name ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-light">
            {!! getIcon('left', 'fs-4 me-1') !!}
            Back to Application
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center p-5 mb-6">
            {!! getIcon('cross-circle', 'fs-2hx text-danger me-4') !!}
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('admin.applications.contract.store', $application) }}" method="POST" id="issueContractForm">
        @csrf
        <div class="row g-6">
            <div class="col-xl-8">
                <div class="card mb-6">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title fw-bold text-gray-800">Select Template</h3>
                    </div>
                    <div class="card-body pt-0">
                        {{-- Template Selection --}}
                        <div class="mb-6">
                            <label class="form-label required fw-semibold">Contract Template</label>
                            <select name="contract_template_id" id="templateSelect" class="form-select form-select-solid @error('contract_template_id') is-invalid @enderror" required>
                                <option value="">Select a Template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ old('contract_template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contract_template_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($templates->isEmpty())
                                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mt-4">
                                    {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                                    <div class="text-gray-700 fs-7">
                                        No active templates found for this program.
                                        <a href="{{ route('admin.contract-templates.create') }}" class="text-primary fw-bold">Create one first.</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Admin Fields (dynamically loaded) --}}
                        <div id="adminFieldsContainer" class="d-none">
                            <div class="separator separator-dashed mb-6"></div>
                            <h4 class="fw-bold text-gray-800 mb-4">Admin-Defined Fields</h4>
                            <p class="text-gray-500 fs-7 mb-4">Fill in the custom fields defined in this template.</p>
                            <div id="adminFieldsList"></div>
                        </div>
                    </div>
                </div>

                {{-- Preview Area --}}
                <div class="card mb-6 d-none" id="previewCard">
                    <div class="card-header border-0 bg-light-info py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('eye', 'fs-4 me-2 text-info') !!}
                            Contract Preview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="previewContent" class="border border-gray-300 rounded p-6 bg-white"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-light">Cancel</a>
                    <button type="button" class="btn btn-light-info" id="previewBtn" disabled>
                        {!! getIcon('eye', 'fs-4 me-1') !!}
                        Preview
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        {!! getIcon('send', 'fs-4 me-1') !!}
                        Issue Contract
                    </button>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-xl-4">
                {{-- Student Info --}}
                <div class="card mb-6">
                    <div class="card-header border-0 bg-light-primary py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('user', 'fs-4 me-2 text-primary') !!}
                            Student Info
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <div class="text-gray-500 fs-8">Name</div>
                                <div class="fw-bold text-gray-800">{{ $application->full_name }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 fs-8">Email</div>
                                <div class="fw-bold text-gray-800">{{ $application->email }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 fs-8">Program</div>
                                <div class="fw-bold text-gray-800">{{ $application->program_name ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 fs-8">Funding Type</div>
                                <div class="fw-bold text-gray-800">
                                    @if($application->isSelfFunded())
                                        <span class="badge badge-light-warning">Self-Funded</span>
                                    @else
                                        <span class="badge badge-light-success">Government-Funded</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- What Will Happen --}}
                <div class="card">
                    <div class="card-header border-0 bg-light-warning py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('information-5', 'fs-4 me-2 text-warning') !!}
                            What Will Happen
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-start">
                                {!! getIcon('check-circle', 'fs-5 text-success me-2 mt-1 flex-shrink-0') !!}
                                <span class="text-gray-700 fs-7">A PDF contract will be generated</span>
                            </div>
                            <div class="d-flex align-items-start">
                                {!! getIcon('check-circle', 'fs-5 text-success me-2 mt-1 flex-shrink-0') !!}
                                <span class="text-gray-700 fs-7">An email with the contract will be sent to the student</span>
                            </div>
                            <div class="d-flex align-items-start">
                                {!! getIcon('check-circle', 'fs-5 text-success me-2 mt-1 flex-shrink-0') !!}
                                <span class="text-gray-700 fs-7">Application status will change to "Contract Sent"</span>
                            </div>
                            <div class="d-flex align-items-start">
                                {!! getIcon('information-5', 'fs-5 text-info me-2 mt-1 flex-shrink-0') !!}
                                <span class="text-gray-700 fs-7">Student must sign and upload the signed contract</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        const templateSelect = document.getElementById('templateSelect');
        const adminFieldsContainer = document.getElementById('adminFieldsContainer');
        const adminFieldsList = document.getElementById('adminFieldsList');
        const previewBtn = document.getElementById('previewBtn');
        const submitBtn = document.getElementById('submitBtn');
        const previewCard = document.getElementById('previewCard');
        const previewContent = document.getElementById('previewContent');
        const placeholdersUrl = @json(url('admin/contract-templates'));
        const previewUrl = @json(route('admin.applications.contract.preview', $application));

        function stripPlaceholderBraces(str) {
            return str.replace(/^\{\{/, '').replace(/\}\}$/, '');
        }

        function toTitleCase(str) {
            return str.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        }

        templateSelect.addEventListener('change', function() {
            const templateId = this.value;

            if (!templateId) {
                adminFieldsContainer.classList.add('d-none');
                adminFieldsList.innerHTML = '';
                previewBtn.disabled = true;
                submitBtn.disabled = true;
                return;
            }

            fetch(placeholdersUrl + '/' + templateId + '/placeholders')
                .then(r => r.json())
                .then(data => {
                    adminFieldsList.innerHTML = '';

                    if (data.admin_placeholders.length > 0) {
                        adminFieldsContainer.classList.remove('d-none');
                        data.admin_placeholders.forEach(placeholder => {
                            const fieldName = stripPlaceholderBraces(placeholder);
                            const label = toTitleCase(fieldName);
                            const div = document.createElement('div');
                            div.className = 'mb-4';
                            div.innerHTML = '<label class="form-label fw-semibold">' + label + '</label>' +
                                '<input type="text" name="admin_fields[' + fieldName + ']" class="form-control form-control-solid" placeholder="Enter ' + label.toLowerCase() + '" />';
                            adminFieldsList.appendChild(div);
                        });
                    } else {
                        adminFieldsContainer.classList.add('d-none');
                    }

                    previewBtn.disabled = false;
                    submitBtn.disabled = false;
                });
        });

        previewBtn.addEventListener('click', function() {
            const formData = new FormData(document.getElementById('issueContractForm'));

            fetch(previewUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(r => r.json())
            .then(data => {
                previewContent.innerHTML = data.html;
                previewCard.classList.remove('d-none');
                previewCard.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(err => {
                alert('Failed to load preview.');
            });
        });
    </script>
    @endpush

</x-default-layout>
