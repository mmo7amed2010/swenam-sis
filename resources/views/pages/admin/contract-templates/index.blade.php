<x-default-layout>

    @section('title')
        Contract Templates
    @endsection

    <div class="d-flex flex-wrap flex-stack mb-6">
        <h2 class="fw-bold my-2">
            {!! getIcon('document', 'fs-2 me-2 text-primary') !!}
            Contract Templates
        </h2>
        <a href="{{ route('admin.contract-templates.create') }}" class="btn btn-primary">
            {!! getIcon('plus', 'fs-6 me-1') !!}
            New Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-6">
            {!! getIcon('check-circle', 'fs-2hx text-success me-4') !!}
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center p-5 mb-6">
            {!! getIcon('cross-circle', 'fs-2hx text-danger me-4') !!}
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="card">
        <div class="card-body py-4">
            @if($templates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4 min-w-200px">Name</th>
                                <th class="min-w-150px">Program</th>
                                <th class="min-w-100px text-center">Status</th>
                                <th class="min-w-125px">Created By</th>
                                <th class="min-w-125px">Created At</th>
                                <th class="min-w-100px text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('admin.contract-templates.show', $template) }}" class="text-gray-800 text-hover-primary fw-bold">
                                            {{ $template->name }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $programsMap->get($template->program_id)['name'] ?? 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        @if($template->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $template->creator?->name ?? 'N/A' }}</td>
                                    <td>{{ $template->created_at->format('M d, Y') }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.contract-templates.edit', $template) }}" class="btn btn-sm btn-light-primary me-1">
                                            {!! getIcon('pencil', 'fs-5') !!}
                                        </a>
                                        <form action="{{ route('admin.contract-templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger">
                                                {!! getIcon('trash', 'fs-5') !!}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    {{ $templates->links() }}
                </div>
            @else
                <x-tables.empty-state
                    icon="document"
                    title="No Contract Templates"
                    message="Create your first contract template to get started."
                />
            @endif
        </div>
    </div>

</x-default-layout>
