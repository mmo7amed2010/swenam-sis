<x-default-layout>

    @section('title')
        {{ __('Instructor Details') }} - {{ $instructor->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.instructors.show', $instructor) }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <div class="col-xl-8">
            <!--begin::Card-->
            <x-cards.section
                :title="__('Instructor Information')"
            >
                <x-slot:toolbar>
                    <x-buttons.action-button
                        text="{{ __('Edit') }}"
                        icon="pencil"
                        href="{{ route('admin.instructors.edit', $instructor) }}"
                        color="primary"
                        size="sm"
                    />
                </x-slot:toolbar>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Full Name') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $instructor->name }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('First Name') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $instructor->first_name }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Last Name') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $instructor->last_name }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Email') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $instructor->email }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('User Type') }}</label>
                        <div class="col-lg-8">
                            <span class="badge badge-light-primary">{{ ucfirst($instructor->user_type) }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Created At') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $instructor->created_at->format('F d, Y h:i A') }}</span>
                        </div>
                    </div>
                    @if($instructor->last_login_at)
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Last Login') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $instructor->last_login_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endif
            </x-cards.section>
            <!--end::Card-->

            <!--begin::Card - Course Assignments-->
            @if($instructor->courseInstructors->count() > 0)
            <x-cards.section
                :title="__('Course Assignments')"
                class="mt-5"
            >
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-200px">{{ __('Course') }}</th>
                                    <th class="min-w-150px">{{ __('Assigned Date') }}</th>
                                    <th class="min-w-100px">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach($instructor->courseInstructors as $assignment)
                                <tr>
                                    <td>{{ $assignment->course->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @if($assignment->removed_at)
                                            <span class="badge badge-light-danger">{{ __('Removed') }}</span>
                                        @else
                                            <span class="badge badge-light-success">{{ __('Active') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            </x-cards.section>
            @endif
            <!--end::Card - Course Assignments-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-xl-4">
            <!--begin::Card - Actions-->
            <x-cards.section
                :title="__('Actions')"
            >
                <form action="{{ route('admin.instructors.destroy', $instructor) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this instructor?') }}');">
                    @csrf
                    @method('DELETE')
                    <x-buttons.action-button
                        text="{{ __('Delete Instructor') }}"
                        icon="trash"
                        color="danger"
                        type="submit"
                        class="w-100"
                    />
                </form>
            </x-cards.section>
            <!--end::Card - Actions-->
        </div>
        <!--end::Col-->
    </div>

</x-default-layout>

