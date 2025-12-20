<x-default-layout>

@section('title')
    Notification Settings
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('settings.notifications') }}
@endsection

<!--begin::Header-->
<div class="mb-7">
    <h1 class="text-gray-900 fw-bold mb-3">
        {!! getIcon('setting-2', 'fs-2 me-2') !!}
        Notification Preferences
    </h1>
    <p class="text-gray-600 fs-5">
        Customize how you receive notifications. You'll always see notifications in your portal regardless of these settings.
    </p>
</div>
<!--end::Header-->

<form action="{{ route('settings.notifications.update') }}" method="POST">
    @csrf
    @method('PUT')

    <!--begin::Notice-->
    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-7">
        {!! getIcon('information-5', 'fs-2tx text-info me-4') !!}
        <div class="d-flex flex-stack flex-grow-1">
            <div class="fw-semibold">
                <h4 class="text-gray-900 fw-bold mb-2">About Email Notifications</h4>
                <div class="fs-6 text-gray-700">
                    These preferences control whether you receive <strong>email notifications</strong>. 
                    In-app notifications will always appear in your notification center.
                </div>
            </div>
        </div>
    </div>
    <!--end::Notice-->

    <!--begin::Settings Grid-->
    <div class="row g-6 g-xl-9">
        <!--begin::Course Announcements-->
        <div class="col-md-6">
            <div class="card card-flush h-100 shadow-sm">
                <div class="card-body p-7">
                    <div class="d-flex align-items-start">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-primary">
                                {!! getIcon('notification', 'fs-2x text-primary') !!}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fw-bold mb-2">Course Announcements</h3>
                            <p class="text-gray-600 fs-6 mb-4">
                                Get notified when instructors post announcements in your enrolled courses
                            </p>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input h-30px w-50px" type="checkbox" 
                                       name="course_announcements_email" value="1" 
                                       id="course_announcements"
                                       {{ $settings->course_announcements_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="course_announcements">
                                    Email notifications {{ $settings->course_announcements_email ? 'enabled' : 'disabled' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Course Announcements-->

        <!--begin::System Notifications-->
        <div class="col-md-6">
            <div class="card card-flush h-100 shadow-sm">
                <div class="card-body p-7">
                    <div class="d-flex align-items-start">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-warning">
                                {!! getIcon('notification-bing', 'fs-2x text-warning') !!}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fw-bold mb-2">System Notifications</h3>
                            <p class="text-gray-600 fs-6 mb-4">
                                Receive important system-wide announcements and platform updates
                            </p>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input h-30px w-50px" type="checkbox" 
                                       name="system_notifications_email" value="1"
                                       id="system_notifications"
                                       {{ $settings->system_notifications_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="system_notifications">
                                    Email notifications {{ $settings->system_notifications_email ? 'enabled' : 'disabled' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::System Notifications-->

        <!--begin::Assignment Reminders-->
        <div class="col-md-6">
            <div class="card card-flush h-100 shadow-sm">
                <div class="card-body p-7">
                    <div class="d-flex align-items-start">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-info">
                                {!! getIcon('time', 'fs-2x text-info') !!}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fw-bold mb-2">Assignment Reminders</h3>
                            <p class="text-gray-600 fs-6 mb-4">
                                Get reminder emails for upcoming assignment deadlines
                            </p>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input h-30px w-50px" type="checkbox" 
                                       name="assignment_reminders_email" value="1"
                                       id="assignment_reminders"
                                       {{ $settings->assignment_reminders_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="assignment_reminders">
                                    Email notifications {{ $settings->assignment_reminders_email ? 'enabled' : 'disabled' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Assignment Reminders-->

        <!--begin::Grade Notifications-->
        <div class="col-md-6">
            <div class="card card-flush h-100 shadow-sm">
                <div class="card-body p-7">
                    <div class="d-flex align-items-start">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-success">
                                {!! getIcon('chart-line-up', 'fs-2x text-success') !!}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fw-bold mb-2">Grade Notifications</h3>
                            <p class="text-gray-600 fs-6 mb-4">
                                Be notified when grades are posted for your assignments and quizzes
                            </p>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input h-30px w-50px" type="checkbox" 
                                       name="grade_notifications_email" value="1"
                                       id="grade_notifications"
                                       {{ $settings->grade_notifications_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="grade_notifications">
                                    Email notifications {{ $settings->grade_notifications_email ? 'enabled' : 'disabled' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Grade Notifications-->

        <!--begin::Quiz Notifications-->
        <div class="col-md-6">
            <div class="card card-flush h-100 shadow-sm">
                <div class="card-body p-7">
                    <div class="d-flex align-items-start">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-danger">
                                {!! getIcon('question-2', 'fs-2x text-danger') !!}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fw-bold mb-2">Quiz Notifications</h3>
                            <p class="text-gray-600 fs-6 mb-4">
                                Receive emails when new quizzes are available or due soon
                            </p>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input h-30px w-50px" type="checkbox" 
                                       name="quiz_notifications_email" value="1"
                                       id="quiz_notifications"
                                       {{ $settings->quiz_notifications_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="quiz_notifications">
                                    Email notifications {{ $settings->quiz_notifications_email ? 'enabled' : 'disabled' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Quiz Notifications-->

        <!--begin::Application Updates-->
        <div class="col-md-6">
            <div class="card card-flush h-100 shadow-sm">
                <div class="card-body p-7">
                    <div class="d-flex align-items-start">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-dark">
                                {!! getIcon('document', 'fs-2x text-dark') !!}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fw-bold mb-2">Application Updates</h3>
                            <p class="text-gray-600 fs-6 mb-4">
                                Get notified about changes to your application status
                            </p>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input h-30px w-50px" type="checkbox" 
                                       name="application_updates_email" value="1"
                                       id="application_updates"
                                       {{ $settings->application_updates_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="application_updates">
                                    Email notifications {{ $settings->application_updates_email ? 'enabled' : 'disabled' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Application Updates-->
    </div>
    <!--end::Settings Grid-->

    <!--begin::Actions-->
    <div class="d-flex justify-content-between mt-10">
        <a href="{{ route('notifications.index') }}" class="btn btn-light btn-lg">
            {!! getIcon('arrow-left', 'fs-4 me-2') !!}
            Back to Notifications
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            {!! getIcon('check', 'fs-4 me-2') !!}
            Save Preferences
        </button>
    </div>
    <!--end::Actions-->
</form>

@push('scripts')
    <script>
        // Update label text when toggle changes
        document.querySelectorAll('.form-check-input').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const label = this.nextElementSibling;
                const status = this.checked ? 'enabled' : 'disabled';
                label.textContent = `Email notifications ${status}`;
            });
        });
    </script>
@endpush

</x-default-layout>