<x-default-layout>

    @section('title')
        {{ __('Manage Enrollments') }} - {{ $course->course_code }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => __('Enrollments')]
        ]" />
    @endsection

    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center my-1">
            <h3 class="fw-bold me-5 my-1">{{ $course->name }}
                @if($course->version > 1)
                <span class="badge badge-light-info">v{{ $course->version }}</span>
                @endif
            </h3>
            @if($course->status === 'draft')
            <span class="badge badge-light-secondary fs-7 fw-bold my-1">{{ __('Draft') }}</span>
            @elseif($course->status === 'published')
            <span class="badge badge-light-info fs-7 fw-bold my-1">{{ __('Published') }}</span>
            @elseif($course->status === 'active')
            <span class="badge badge-light-success fs-7 fw-bold my-1">{{ __('Active') }}</span>
            @elseif($course->status === 'archived')
            <span class="badge badge-light-warning fs-7 fw-bold my-1">{{ __('Archived') }}</span>
            @endif
        </div>
        <div class="d-flex my-1 gap-3">
            <a href="{{ route('admin.programs.courses.show', [$program, $course]) }}" class="btn btn-sm btn-light">{{ __('Back to Course') }}</a>
            <button class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#bulkEnrollModal">
                {!! getIcon('file-up', 'fs-2') !!}
                {{ __('Bulk Upload CSV') }}
            </button>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                {!! getIcon('plus', 'fs-2') !!}
                {{ __('Enroll Student') }}
            </button>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Stats-->
    <div class="row g-5 g-xl-8 mb-5">
        <div class="col-xl-3">
            <x-cards.stat-card
                title="{{ __('Active') }}"
                :value="$stats['active']"
                icon="check-circle"
                color="success"
                cardClass="mb-xl-8"
            />
        </div>
        <div class="col-xl-3">
            <x-cards.stat-card
                title="{{ __('Pending') }}"
                :value="$stats['pending']"
                icon="hourglass"
                color="primary"
                cardClass="mb-xl-8"
            />
        </div>
        <div class="col-xl-3">
            <x-cards.stat-card
                title="{{ __('Completed') }}"
                :value="$stats['completed']"
                icon="check"
                color="info"
                cardClass="mb-xl-8"
            />
        </div>
        <div class="col-xl-3">
            <x-cards.stat-card
                title="{{ __('Dropped') }}"
                :value="$stats['dropped']"
                icon="information"
                color="warning"
                cardClass="mb-xl-8"
            />
        </div>
    </div>
    <!--end::Stats-->

    <!--begin::Card-->
    <x-tables.card-wrapper>
        <x-slot:toolbar>
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-1 position-absolute ms-6') !!}
                    <input type="text" id="searchInput" class="form-control form-control-solid w-250px ps-15" placeholder="{{ __('Search students...') }}" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <select id="statusFilter" class="form-select form-select-solid w-150px">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="dropped">{{ __('Dropped') }}</option>
                    </select>
                </div>
            </div>
        </x-slot:toolbar>

        <!--begin::Card body-->
            @if($enrollments->count() > 0)
            <!--begin::Table-->
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="enrollmentsTable">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">{{ __('Student') }}</th>
                            <th class="min-w-100px">{{ __('Status') }}</th>
                            <th class="min-w-100px">{{ __('Enrolled Date') }}</th>
                            <th class="min-w-100px">{{ __('Progress') }}</th>
                            <th class="min-w-100px">{{ __('Grade') }}</th>
                            <th class="text-end min-w-100px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach($enrollments as $enrollment)
                        <tr data-status="{{ $enrollment->status }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                        <div class="symbol-label">
                                            <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 text-hover-primary mb-1">{{ $enrollment->student->name }}</span>
                                        <span class="text-gray-500">{{ $enrollment->student->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($enrollment->status === 'pending')
                                <span class="badge badge-light-primary">{{ __('Pending') }}</span>
                                @elseif($enrollment->status === 'active')
                                <span class="badge badge-light-success">{{ __('Active') }}</span>
                                @elseif($enrollment->status === 'completed')
                                <span class="badge badge-light-info">{{ __('Completed') }}</span>
                                @elseif($enrollment->status === 'dropped')
                                <span class="badge badge-light-warning">{{ __('Dropped') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : '-' }}
                            </td>
                            <td>
                                @if($enrollment->progress_percentage !== null)
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-gray-500 fs-7">{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                                    </div>
                                    <div class="progress h-6px w-100">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $enrollment->progress_percentage }}%" aria-valuenow="{{ $enrollment->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($enrollment->final_grade !== null)
                                <span class="badge badge-light-{{ $enrollment->final_grade >= 70 ? 'success' : 'danger' }}">
                                    {{ number_format($enrollment->final_grade, 1) }}%
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-actions.dropdown buttonText="{{ __('Actions') }}" buttonClass="btn-light btn-active-light-primary btn-sm" buttonIcon="down">
                                    <x-actions.modal-button
                                        target="#editEnrollmentModal{{ $enrollment->id }}"
                                        permission="edit enrollments"
                                    >
                                        {{ __('Update Status') }}
                                    </x-actions.modal-button>

                                    @if($enrollment->status !== 'dropped')
                                        <x-actions.separator />
                                        <x-actions.form-button
                                            action="{{ route('admin.programs.courses.enrollments.destroy', [$program, $course, $enrollment]) }}"
                                            method="DELETE"
                                            permission="drop students"
                                            confirm="{{ __('Drop this student from the course?') }}"
                                            :danger="true"
                                        >
                                            {{ __('Drop Student') }}
                                        </x-actions.form-button>
                                    @endif
                                </x-actions.dropdown>
                            </td>
                        </tr>

                        {{-- Edit Enrollment Modal --}}
                        <div class="modal fade" id="editEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('admin.programs.courses.enrollments.update', [$program, $course, $enrollment]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('Update Enrollment') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-5">
                                                <h6 class="mb-3">{{ $enrollment->student->name }}</h6>
                                            </div>

                                            <div class="mb-5">
                                                <label class="form-label required">{{ __('Status') }}</label>
                                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                    <option value="pending" {{ $enrollment->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                    <option value="active" {{ $enrollment->status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="completed" {{ $enrollment->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                                    <option value="dropped" {{ $enrollment->status === 'dropped' ? 'selected' : '' }}>{{ __('Dropped') }}</option>
                                                </select>
                                                @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-5">
                                                <label class="form-label">{{ __('Admin Notes') }}</label>
                                                <textarea name="admin_notes" class="form-control @error('admin_notes') is-invalid @enderror" rows="3" placeholder="{{ __('Optional notes...') }}">{{ old('admin_notes', $enrollment->admin_notes) }}</textarea>
                                                @error('admin_notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ __('Update Enrollment') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!--end::Table-->

            <!--begin::Pagination-->
            <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                <div class="text-gray-500">
                    {{ __('Showing') }} {{ $enrollments->firstItem() ?? 0 }} {{ __('to') }} {{ $enrollments->lastItem() ?? 0 }} {{ __('of') }} {{ $enrollments->total() }} {{ __('enrollments') }}
                </div>
                {{ $enrollments->links() }}
            </div>
            <!--end::Pagination-->
            @else
            <!--begin::Empty state-->
            <x-tables.empty-state
                icon="profile-user"
                title="{{ __('No Students Enrolled') }}"
                message="{{ __('This course does not have any students enrolled yet.') }}"
                actionText="{{ __('Enroll First Student') }}"
                actionModal="enrollStudentModal"
                actionPermission="enroll students"
                bgColor="ffffff"
            />
            <!--end::Empty state-->
            @endif
        </div>
        <!--end::Card body-->
    </x-tables.card-wrapper>
    <!--end::Card-->

    {{-- Enroll Student Modal --}}
    <div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.programs.courses.enrollments.store', [$program, $course]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Enroll Student') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label required">{{ __('Select Student') }}</label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">{{ __('Choose student...') }}</option>
                                @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label required">{{ __('Status') }}</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="active" selected>{{ __('Active') }}</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label">{{ __('Admin Notes') }}</label>
                            <textarea name="admin_notes" class="form-control @error('admin_notes') is-invalid @enderror" rows="3" placeholder="{{ __('Optional notes about this enrollment...') }}"></textarea>
                            @error('admin_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Enroll Student') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bulk Enroll Modal --}}
    <div class="modal fade" id="bulkEnrollModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.programs.courses.enrollments.bulk', [$program, $course]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Bulk Enroll Students (CSV Upload)') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info d-flex align-items-start">
                            {!! getIcon('information-5', 'fs-2hx text-info me-4 mt-1') !!}
                            <div class="d-flex flex-column">
                                <h5 class="mb-2">{{ __('CSV Format Requirements') }}</h5>
                                <p class="mb-2">{{ __('Your CSV file must include the following column:') }}</p>
                                <ul class="mb-2">
                                    <li><strong>email</strong> {{ __('or') }} <strong>student_email</strong> - {{ __('Student email address (required)') }}</li>
                                </ul>
                                <p class="mb-2">{{ __('Optional columns:') }}</p>
                                <ul class="mb-0">
                                    <li><strong>status</strong> - pending, active (default: active)</li>
                                    <li><strong>notes</strong> - {{ __('Admin notes') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label required">{{ __('Upload CSV File') }}</label>
                            <input type="file" name="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv" required />
                            @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="skip_duplicates" value="1" id="skip_duplicates" checked />
                                <label class="form-check-label" for="skip_duplicates">
                                    {{ __('Skip already enrolled students') }}
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <strong>{{ __('Note') }}:</strong> {{ __('Students not found in the system will be skipped with an error message.') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Upload & Enroll') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Search functionality with debouncing
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                clearTimeout(searchTimeout);
                const searchValue = this.value.toLowerCase();

                searchTimeout = setTimeout(() => {
                    const rows = document.querySelectorAll('#enrollmentsTable tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchValue) ? '' : 'none';
                    });
                }, 300);
            });

            // Status filter
            document.getElementById('statusFilter').addEventListener('change', function() {
                const filterValue = this.value;
                const rows = document.querySelectorAll('#enrollmentsTable tbody tr');

                rows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    if (!filterValue || status === filterValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush

</x-default-layout>
