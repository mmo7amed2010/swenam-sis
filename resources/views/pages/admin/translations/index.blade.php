<x-default-layout>

    @section('title')
        {{ __('Translations Management') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('translations.index') }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" data-kt-contributors-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="{{ __('Search contributors') }}" id="mySearchInput" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end gap-3" data-kt-contributors-table-toolbar="base">
                    <!--begin::External UI Link-->
                    <a href="{{ $translationsUrl }}" target="_blank" class="btn btn-sm btn-light d-flex align-items-center">
                        {!! getIcon('exit-right-corner', 'fs-6 me-2') !!}
                        {{ __('Open Translations UI') }}
                    </a>
                    <!--end::External UI Link-->

                    <!--begin::Add contributor-->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_contributor">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        {{ __('Add Contributor') }}
                    </button>
                    <!--end::Add contributor-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <!--begin::Table-->
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <!--begin::Table head-->
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th class="min-w-150px">{{ __('Name') }}</th>
                            <th class="min-w-140px">{{ __('Email') }}</th>
                            <th class="min-w-120px">{{ __('Created') }}</th>
                            <th class="min-w-100px text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody>
                        @forelse($contributors as $contributor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px me-5">
                                            <span class="symbol-label bg-light-primary text-primary fs-6 fw-bolder">
                                                {{ strtoupper(substr($contributor->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bold fs-6">{{ $contributor->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">{{ $contributor->email }}</span>
                                </td>
                                <td>
                                    @php
                                        $roleColors = [
                                            0 => 'badge-light-danger', // Owner
                                            1 => 'badge-light-primary', // Translator
                                            2 => 'badge-light-info', // Viewer
                                        ];
                                        $roleColor = $roleColors[$contributor->role] ?? 'badge-light-secondary';
                                    @endphp
                                    <span class="badge {{ $roleColor }} fw-semibold fs-7">{{ $contributor->role_name }}</span>
                                </td>
                                <td>
                                    <span class="text-muted fw-semibold text-muted d-block fs-7">
                                        {{ \Carbon\Carbon::parse($contributor->created_at)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="editContributor({{ $contributor->id }})">
                                        {!! getIcon('pencil', 'fs-6') !!}
                                    </button>
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" onclick="deleteContributor({{ $contributor->id }}, '{{ $contributor->name }}')">
                                        {!! getIcon('trash', 'fs-6') !!}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        {!! getIcon('abstract-32', 'fs-2x text-muted mb-3') !!}
                                        <span class="fs-6 fw-semibold">{{ __('No contributors found') }}</span>
                                        <span class="text-muted fs-7">{{ __('Start by adding your first contributor') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!--end::Table body-->
                </table>
            </div>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>

    <!--begin::Modals-->
    @include('pages.admin.translations.modals.add_edit')
    <!--end::Modals-->

    @push('scripts')
        <script>
            // Search functionality
            document.getElementById('mySearchInput').addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');

                tableRows.forEach(function(row) {
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Edit contributor handled by modal script

            function deleteContributor(id, name) {
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You are about to delete contributor") }} "' + name + '". {{ __("This action cannot be undone!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: '{{ __("Deleting...") }}',
                            text: '{{ __("Please wait while we delete the contributor.") }}',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Make the delete request
                        const deleteUrl = '{{ url('translations-management/contributors') }}/' + id;
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: '{{ __("Deleted!") }}',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Reload the page after successful deletion
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: '{{ __("Error!") }}',
                                    text: data.message,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: '{{ __("Error!") }}',
                                text: '{{ __("An error occurred while deleting the contributor.") }}',
                                icon: 'error'
                            });
                        });
                    }
                });
            }
        </script>
    @endpush

</x-default-layout>
