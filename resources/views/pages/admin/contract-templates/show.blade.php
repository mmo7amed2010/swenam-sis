<x-default-layout>

    @section('title')
        Contract Template - {{ $contractTemplate->name }}
    @endsection

    <div class="d-flex flex-wrap flex-stack mb-6">
        <h2 class="fw-bold my-2">
            {!! getIcon('document', 'fs-2 me-2 text-primary') !!}
            {{ $contractTemplate->name }}
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.contract-templates.edit', $contractTemplate) }}" class="btn btn-light-primary">
                {!! getIcon('pencil', 'fs-4 me-1') !!}
                Edit
            </a>
            <a href="{{ route('admin.contract-templates.index') }}" class="btn btn-light">
                {!! getIcon('left', 'fs-4 me-1') !!}
                Back
            </a>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-xl-8">
            {{-- Template Info --}}
            <div class="card mb-6">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold text-gray-800">Template Information</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-4 mb-6">
                        <div class="col-md-4">
                            <x-detail.field icon="document" label="Name" :value="$contractTemplate->name" color="primary" />
                        </div>
                        <div class="col-md-4">
                            <x-detail.field icon="abstract-26" label="Program" :value="$contractTemplate->program_name ?? 'N/A'" color="primary" />
                        </div>
                        <div class="col-md-4">
                            <x-detail.field icon="toggle-on" label="Status" color="primary">
                                @if($contractTemplate->is_active)
                                    <span class="badge badge-light-success">Active</span>
                                @else
                                    <span class="badge badge-light-danger">Inactive</span>
                                @endif
                            </x-detail.field>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <x-detail.field icon="profile-user" label="Created By" :value="$contractTemplate->creator?->name ?? 'N/A'" color="info" />
                        </div>
                        <div class="col-md-6">
                            <x-detail.field icon="calendar" label="Created At" :value="$contractTemplate->created_at->format('F d, Y \a\t h:i A')" color="info" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contract Body Preview --}}
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold text-gray-800">Contract Body</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="border border-gray-300 rounded p-6 bg-white">
                        {!! $contractTemplate->body !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            {{-- Placeholders Used --}}
            <div class="card">
                <div class="card-header border-0 bg-light-info py-5">
                    <h3 class="card-title fw-bold text-gray-800">
                        {!! getIcon('code', 'fs-4 me-2 text-info') !!}
                        Placeholders Used
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $sisKeys = array_keys(\App\Models\ContractTemplate::getSisPlaceholders());
                        preg_match_all('/\{\{(\w+)\}\}/', $contractTemplate->body, $matches);
                        $allPlaceholders = array_unique($matches[0]);
                        $usedSis = array_intersect($allPlaceholders, $sisKeys);
                        $usedAdmin = array_diff($allPlaceholders, $sisKeys);
                    @endphp

                    @if(count($usedSis) > 0)
                        <h6 class="fw-bold text-gray-800 mb-3">SIS Auto-Populated</h6>
                        <div class="d-flex flex-wrap gap-2 mb-5">
                            @foreach($usedSis as $placeholder)
                                <span class="badge badge-light-primary fs-8">{{ $placeholder }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if(count($usedAdmin) > 0)
                        <h6 class="fw-bold text-gray-800 mb-3">Admin-Filled</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($usedAdmin as $placeholder)
                                <span class="badge badge-light-warning fs-8">{{ $placeholder }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if(count($allPlaceholders) === 0)
                        <p class="text-gray-500 fs-7 mb-0">No placeholders found in the template body.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-default-layout>
