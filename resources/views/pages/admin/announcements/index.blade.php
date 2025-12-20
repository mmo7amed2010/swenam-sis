<x-default-layout>

    @section('title')
        System Announcements
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.announcements.index') }}
    @endsection

    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack mb-6">
        <h2 class="fw-bold my-2">
            {!! getIcon('megaphone', 'fs-2 me-2 text-primary') !!}
            System Announcements
        </h2>

        <div class="d-flex align-items-center gap-2 my-2">
            <!--begin::Filter-->
            <select id="filter-status" class="form-select form-select-solid w-150px">
                <option value="all">All Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>

            <select id="filter-priority" class="form-select form-select-solid w-150px">
                <option value="all">All Priority</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#createAnnouncementModal">
                {!! getIcon('plus', 'fs-6 me-1') !!}
                New Announcement
            </button>
        </div>
    </div>
    <!--end::Toolbar-->

    @if($announcements->count() > 0)
        <!--begin::Announcements Grid-->
        <div class="row g-6 g-xl-9" id="announcements-grid">
            @foreach($announcements as $announcement)
                @php
                    $priorityColors = [
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                    ];
                    $priorityColor = $priorityColors[$announcement->priority] ?? 'primary';
                    $isPublished = $announcement->is_published;
                @endphp

                <div class="col-md-6 col-xl-4 announcement-card"
                    data-status="{{ $isPublished ? 'published' : 'draft' }}"
                    data-priority="{{ $announcement->priority }}">
                    <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <div class="card-title flex-column">
                                <h3 class="fw-bold text-gray-800 mb-3 text-break" style="word-wrap: break-word; overflow-wrap: break-word;">{{ $announcement->title }}</h3>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge badge-light-{{ $priorityColor }}">
                                        {{ ucfirst($announcement->priority) }} Priority
                                    </span>

                                    @if($isPublished)
                                        <span class="badge badge-light-success">Published</span>
                                    @else
                                        <span class="badge badge-light-secondary">Draft</span>
                                    @endif

                                    <span class="badge badge-light-primary">
                                        {!! getIcon('shield-tick', 'fs-7 me-1') !!}
                                        System
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="text-gray-600 fw-normal mb-5 text-break" style="min-height: 60px; word-wrap: break-word; overflow-wrap: break-word;">
                                {{ Str::limit(strip_tags($announcement->content), 120) }}
                            </div>

                            <div class="separator separator-dashed mb-5"></div>

                            <!--begin::Meta-->
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center">
                                    {!! getIcon('user', 'fs-6 me-2') !!}
                                    <span>{{ $announcement->user->name ?? 'System' }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    {!! getIcon('time', 'fs-6 me-2') !!}
                                    <span>{{ $announcement->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    {!! getIcon('people', 'fs-6 me-2') !!}
                                    <span>
                                        @if($announcement->target_audience === 'all')
                                            All Users
                                        @elseif($announcement->target_audience === 'students')
                                            Students Only
                                        @elseif($announcement->target_audience === 'instructors')
                                            Instructors Only
                                        @elseif($announcement->target_audience === 'admins')
                                            Admins Only
                                        @elseif($announcement->target_audience === 'program' && $announcement->program_id)
                                            @php
                                                $program = \App\Models\Program::find($announcement->program_id);
                                            @endphp
                                            {{ $program ? $program->name : 'Program' }}
                                            @if($announcement->course_id)
                                                @php
                                                    $course = \App\Models\Course::find($announcement->course_id);
                                                @endphp
                                                <span class="badge badge-light-info ms-1">{{ $course ? $course->name : 'Course' }}</span>
                                            @endif
                                        @else
                                            {{ ucfirst($announcement->target_audience) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <!--end::Meta-->
                        </div>
                        <!--end::Body-->

                        <!--begin::Footer-->
                        <div class="card-footer d-flex justify-content-between pt-0">
                            <a href="{{ route('admin.announcements.show', $announcement) }}"
                                class="btn btn-sm btn-light-primary">
                                {!! getIcon('eye', 'fs-6 me-1') !!}
                                View
                            </a>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-light-info edit-announcement-btn"
                                    data-announcement-id="{{ $announcement->id }}">
                                    {!! getIcon('pencil', 'fs-6') !!}
                                </button>

                                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light-danger">
                                        {!! getIcon('trash', 'fs-6') !!}
                                    </button>
                                </form>
                            </div>
                        </div>
                        <!--end::Footer-->
                    </div>
                </div>
            @endforeach
        </div>
        <!--end::Announcements Grid-->

        <!--begin::Pagination-->
        <div class="d-flex justify-content-center mt-10">
            {{ $announcements->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
        <!--end::Pagination-->
    @else
        <!--begin::Empty State-->
        <div class="card shadow-sm">
            <div class="card-body p-20">
                <div class="text-center">
                    <div class="symbol symbol-150px mb-7">
                        <span class="symbol-label bg-light-primary">
                            {!! getIcon('megaphone', 'fs-4x text-primary') !!}
                        </span>
                    </div>
                    <h2 class="text-gray-800 fw-bold mb-3">No Announcements Yet</h2>
                    <p class="text-gray-600 fs-5 mb-7">Create your first system announcement to notify all users</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#createAnnouncementModal">
                        {!! getIcon('plus', 'fs-6 me-1') !!}
                        Create Announcement
                    </button>
                </div>
            </div>
        </div>
        <!--end::Empty State-->
    @endif

    <!--begin::Create Modal-->
    <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Create System Announcement</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createAnnouncementForm" method="POST" action="{{ route('admin.announcements.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-6">
                            <!--begin::Title-->
                            <div class="col-12">
                                <label class="required form-label">Title</label>
                                <input type="text" name="title" class="form-control form-control-lg"
                                    placeholder="Enter announcement title" required>
                            </div>
                            <!--end::Title-->

                            <!--begin::Content-->
                            <div class="col-12">
                                <label class="required form-label">Content</label>
                                <textarea name="content" class="form-control" rows="6"
                                    placeholder="Enter announcement content" required></textarea>
                            </div>
                            <!--end::Content-->

                            <!--begin::Priority-->
                            <div class="col-md-4">
                                <label class="required form-label">Priority</label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <!--end::Priority-->

                            <!--begin::Target Audience-->
                            <div class="col-md-4">
                                <label class="required form-label">Target Audience</label>
                                <select name="target_audience" class="form-select" id="create-target-audience" required>
                                    <option value="all">All Users</option>
                                    <option value="students">Students Only</option>
                                    <option value="instructors">Instructors Only</option>
                                    <option value="admins">Admins Only</option>
                                    <option value="program">Specific Program</option>
                                </select>
                            </div>
                            <!--end::Target Audience-->

                            <!--begin::Program (conditional)-->
                            <div class="col-md-4" id="create-program-selector" style="display: none;">
                                <label class="form-label">Program</label>
                                <select name="program_id" class="form-select">
                                    <option value="">Select Program</option>
                                    @foreach(\App\Models\Program::all() as $program)
                                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Program-->

                            <!--begin::Course (conditional)-->
                            <div class="col-md-4" id="create-course-selector" style="display: none;">
                                <label class="form-label">Course (Optional)</label>
                                <select name="course_id" id="create-course-id" class="form-select">
                                    <option value="">All Students in Program</option>
                                </select>
                                <div class="form-text">Leave empty to target all students in the program</div>
                            </div>
                            <!--end::Course-->

                            <!--begin::Options-->
                            <div class="col-12">
                                <div class="form-check form-switch form-check-custom form-check-solid mb-3">
                                    <input type="hidden" name="is_published" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                        id="create-is-published" checked>
                                    <label class="form-check-label" for="create-is-published">
                                        Publish Announcement
                                    </label>
                                    
                                </div>
                                <div class="form-check form-switch form-check-custom form-check-solid" id="create-email-option">
                                    <input type="hidden" name="send_email" value="0">
                                    <input class="form-check-input" type="checkbox" name="send_email" value="1"
                                        id="create-send-email" disabled>
                                    <label class="form-check-label" for="create-send-email">
                                        Send Email Notifications
                                    </label>
                                </div>
                                <div class="form-text text-muted" id="create-email-disabled-text">
                                    Email notifications are only available for Program and Course targeting
                                </div>
                                <div class="alert alert-warning d-none mt-3" id="create-email-warning">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-information-5 fs-2x text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div>
                                            <strong>Warning:</strong> Sending emails to large numbers of users may consume your email quota and could temporarily affect mail server performance. 
                                            Notifications will be sent in background batches to minimize impact.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Options-->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            {!! getIcon('check', 'fs-6 me-1') !!}
                            Create Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Create Modal-->

    <!--begin::Edit Modal-->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Edit System Announcement</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAnnouncementForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-6">
                            <!--begin::Title-->
                            <div class="col-12">
                                <label class="required form-label">Title</label>
                                <input type="text" name="title" id="edit-title" class="form-control form-control-lg"
                                    required>
                            </div>

                            <!--begin::Content-->
                            <div class="col-12">
                                <label class="required form-label">Content</label>
                                <textarea name="content" id="edit-content" class="form-control" rows="6"
                                    required></textarea>
                            </div>

                            <!--begin::Priority-->
                            <div class="col-md-4">
                                <label class="required form-label">Priority</label>
                                <select name="priority" id="edit-priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>

                            <!--begin::Target Audience-->
                            <div class="col-md-4">
                                <label class="required form-label">Target Audience</label>
                                <select name="target_audience" id="edit-target-audience" class="form-select" required>
                                    <option value="all">All Users</option>
                                    <option value="students">Students Only</option>
                                    <option value="instructors">Instructors Only</option>
                                    <option value="admins">Admins Only</option>
                                    <option value="program">Specific Program</option>
                                </select>
                            </div>

                            <!--begin::Program-->
                            <div class="col-md-4" id="edit-program-selector" style="display: none;">
                                <label class="form-label">Program</label>
                                <select name="program_id" id="edit-program-id" class="form-select">
                                    <option value="">Select Program</option>
                                    @foreach(\App\Models\Program::all() as $program)
                                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!--begin::Course (conditional)-->
                            <div class="col-md-4" id="edit-course-selector" style="display: none;">
                                <label class="form-label">Course (Optional)</label>
                                <select name="course_id" id="edit-course-id" class="form-select">
                                    <option value="">All Students in Program</option>
                                </select>
                                <div class="form-text">Leave empty to target all students in the program</div>
                            </div>
                            <!--end::Course-->

                            <!--begin::Options-->
                            <div class="col-12">
                                <div class="form-check form-switch form-check-custom form-check-solid mb-3">
                                    <input type="hidden" name="is_published" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                        id="edit-is-published">
                                    <label class="form-check-label" for="edit-is-published">
                                        Publish Announcement
                                    </label>
                                    
                                </div>
                                <div class="form-check form-switch form-check-custom form-check-solid" id="edit-email-option">
                                    <input type="hidden" name="send_email" value="0">
                                    <input class="form-check-input" type="checkbox" name="send_email" value="1"
                                        id="edit-send-email" disabled>
                                    <label class="form-check-label" for="edit-send-email">
                                        Send Email Notifications
                                    </label>
                                </div>
                                <div class="form-text text-muted" id="edit-email-disabled-text">
                                    Email notifications are only available for Program and Course targeting
                                </div>
                                <div class="alert alert-warning d-none mt-3" id="edit-email-warning">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-information-5 fs-2x text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div>
                                            <strong>Warning:</strong> Sending emails to large numbers of users may consume your email quota and could temporarily affect mail server performance. 
                                            Notifications will be sent in background batches to minimize impact.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            {!! getIcon('check', 'fs-6 me-1') !!}
                            Update Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Edit Modal-->

    @push('scripts')
            <script>
                // Filter functionality
                document.getElementById('filter-status').addEventListener('change', filterAnnouncements);
                document.getElementById('filter-priority').addEventListener('change', filterAnnouncements);

                function filterAnnouncements() {
                    const status = document.getElementById('filter-status').value;
                    const priority = document.getElementById('filter-priority').value;
                    const cards = document.querySelectorAll('.announcement-card');

                    cards.forEach(card => {
                        const cardStatus = card.dataset.status;
                        const cardPriority = card.dataset.priority;

                        const statusMatch = status === 'all' || cardStatus === status;
                        const priorityMatch = priority === 'all' || cardPriority === priority;

                        card.style.display = (statusMatch && priorityMatch) ? 'block' : 'none';
                    });
                }

                // Target audience conditional display for CREATE modal
                document.getElementById('create-target-audience').addEventListener('change', function () {
                    const showProgram = this.value === 'program';
                    document.getElementById('create-program-selector').style.display = showProgram ? 'block' : 'none';
                    document.getElementById('create-course-selector').style.display = 'none'; // Hide course until program selected
                    
                    // Reset selections
                    if (!showProgram) {
                        document.getElementById('create-program-id').value = '';
                        document.getElementById('create-course-id').value = '';
                    }
                });

                // Load courses when program is selected in CREATE modal
                document.querySelector('#create-program-selector select[name="program_id"]').addEventListener('change', function () {
                    const programId = this.value;
                    const courseSelector = document.getElementById('create-course-selector');
                    const courseDropdown = document.getElementById('create-course-id');
                    
                    if (programId) {
                        // Show course selector
                        courseSelector.style.display = 'block';
                        
                        // Load courses via AJAX
                        fetch(`/admin/programs/${programId}/courses`)
                            .then(response => response.json())
                            .then(courses => {
                                courseDropdown.innerHTML = '<option value="">All Students in Program</option>';
                                courses.forEach(course => {
                                    courseDropdown.innerHTML += `<option value="${course.id}">${course.name}</option>`;
                                });
                            })
                            .catch(error => {
                                console.error('Error loading courses:', error);
                                courseDropdown.innerHTML = '<option value="">Error loading courses</option>';
                            });
                    } else {
                        courseSelector.style.display = 'none';
                        courseDropdown.value = '';
                    }
                });

                // Target audience conditional display for EDIT modal
                document.getElementById('edit-target-audience').addEventListener('change', function () {
                    const showProgram = this.value === 'program';
                    document.getElementById('edit-program-selector').style.display = showProgram ? 'block' : 'none';
                    document.getElementById('edit-course-selector').style.display = 'none'; // Hide course until program selected
                    
                    // Reset selections
                    if (!showProgram) {
                        document.getElementById('edit-program-id').value = '';
                        document.getElementById('edit-course-id').value = '';
                    }
                });

                // Load courses when program is selected in EDIT modal
                document.querySelector('#edit-program-selector select[name="program_id"]').addEventListener('change', function () {
                    const programId = this.value;
                    const courseSelector = document.getElementById('edit-course-selector');
                    const courseDropdown = document.getElementById('edit-course-id');
                    
                    if (programId) {
                        // Show course selector
                        courseSelector.style.display = 'block';
                        
                        // Load courses via AJAX
                        fetch(`/admin/programs/${programId}/courses`)
                            .then(response => response.json())
                            .then(courses => {
                                courseDropdown.innerHTML = '<option value="">All Students in Program</option>';
                                courses.forEach(course => {
                                    courseDropdown.innerHTML += `<option value="${course.id}">${course.name}</option>`;
                                });
                            })
                            .catch(error => {
                                console.error('Error loading courses:', error);
                                courseDropdown.innerHTML = '<option value="">Error loading courses</option>';
                            });
                    } else {
                        courseSelector.style.display = 'none';
                        courseDropdown.value = '';
                    }
                });

                // Edit announcement - load data
                document.querySelectorAll('.edit-announcement-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const announcementId = this.dataset.announcementId;

                        // Fetch announcement data
                        fetch(`/admin/announcements/${announcementId}/edit`)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('edit-title').value = data.title;
                                document.getElementById('edit-content').value = data.content;
                                document.getElementById('edit-priority').value = data.priority;
                                document.getElementById('edit-target-audience').value = data.target_audience;
                                document.getElementById('edit-program-id').value = data.program_id || '';
                                document.getElementById('edit-is-published').checked = data.is_published;
                                document.getElementById('edit-send-email').checked = data.send_email;

                                // Show/hide program selector
                                const showProgram = data.target_audience === 'program';
                                document.getElementById('edit-program-selector').style.display = showProgram ? 'block' : 'none';

                                // Load courses and set course_id if program is selected
                                if (showProgram && data.program_id) {
                                    const courseSelector = document.getElementById('edit-course-selector');
                                    const courseDropdown = document.getElementById('edit-course-id');
                                    
                                    // Load courses for the selected program
                                    fetch(`/admin/programs/${data.program_id}/courses`)
                                        .then(response => response.json())
                                        .then(courses => {
                                            courseDropdown.innerHTML = '<option value="">All Students in Program</option>';
                                            courses.forEach(course => {
                                                const selected = course.id == data.course_id ? 'selected' : '';
                                                courseDropdown.innerHTML += `<option value="${course.id}" ${selected}>${course.name}</option>`;
                                            });
                                            
                                            // Show course selector if courses loaded
                                            courseSelector.style.display = 'block';
                                        })
                                        .catch(error => {
                                            console.error('Error loading courses:', error);
                                        });
                                } else {
                                    document.getElementById('edit-course-selector').style.display = 'none';
                                }

                                // Update form action
                                document.getElementById('editAnnouncementForm').action = `/admin/announcements/${announcementId}`;

                                // Show modal
                                new bootstrap.Modal(document.getElementById('editAnnouncementModal')).show();
                            });
                    });
                });

                // AJAX form submission for create announcement
                document.getElementById('createAnnouncementForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    const form = this;
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    
                    // Disable button and show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('createAnnouncementModal')).hide();
                            
                            // Reset form
                            form.reset();
                            document.getElementById('create-is-published').checked = true;
                            document.getElementById('create-send-email').checked = true;
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message || 'Announcement created successfully',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload page to show new announcement
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Failed to create announcement');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to create announcement'
                        });
                    })
                    .finally(() => {
                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                });

                // AJAX form submission for edit announcement
                document.getElementById('editAnnouncementForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    const form = this;
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    
                    // Disable button and show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('editAnnouncementModal')).hide();
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message || 'Announcement updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload page to show updated announcement
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Failed to update announcement');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to update announcement'
                        });
                    })
                    .finally(() => {
                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                });


                // Delete confirmation with SweetAlert2
                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        
                        Swal.fire({
                            title: 'Delete Announcement?',
                            text: "This action cannot be undone!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // Hover effect
                const style = document.createElement('style');
                style.textContent = `
            .hover-elevate-up {
                transition: all 0.3s ease;
            }
            .hover-elevate-up:hover {
                transform: translateY(-5px);
                box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
            }
        `;
                document.head.appendChild(style);

                // Email checkbox management for CREATE modal
                const createTargetAudience = document.getElementById('create-target-audience');
                const createEmailCheckbox = document.getElementById('create-send-email');
                const createEmailWarning = document.getElementById('create-email-warning');
                const createEmailDisabledText = document.getElementById('create-email-disabled-text');

                createTargetAudience.addEventListener('change', function() {
                    if (this.value === 'program') {
                        createEmailCheckbox.disabled = false;
                        createEmailDisabledText.classList.add('d-none');
                    } else {
                        createEmailCheckbox.disabled = true;
                        createEmailCheckbox.checked = false;
                        createEmailWarning.classList.add('d-none');
                        createEmailDisabledText.classList.remove('d-none');
                    }
                });

                // Show warning when email is checked
                createEmailCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        createEmailWarning.classList.remove('d-none');
                    } else {
                        createEmailWarning.classList.add('d-none');
                    }
                });

                // Email checkbox management for EDIT modal
                const editTargetAudience = document.getElementById('edit-target-audience');
                const editEmailCheckbox = document.getElementById('edit-send-email');
                const editEmailWarning = document.getElementById('edit-email-warning');
                const editEmailDisabledText = document.getElementById('edit-email-disabled-text');

                editTargetAudience.addEventListener('change', function() {
                    if (this.value === 'program') {
                        editEmailCheckbox.disabled = false;
                        editEmailDisabledText.classList.add('d-none');
                    } else {
                        editEmailCheckbox.disabled = true;
                        editEmailCheckbox.checked = false;
                        editEmailWarning.classList.add('d-none');
                        editEmailDisabledText.classList.remove('d-none');
                    }
                });

                // Show warning when email is checked
                editEmailCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        editEmailWarning.classList.remove('d-none');
                    } else {
                        editEmailWarning.classList.add('d-none');
                    }
                });
            </script>
    @endpush

</x-default-layout>