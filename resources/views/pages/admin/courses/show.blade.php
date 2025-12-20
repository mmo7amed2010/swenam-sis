<x-default-layout>

    @include('partials._ux-components')

    @section('title')
        {{ $course->course_code }} - {{ $course->name }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code]
        ]" />
    @endsection

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="ki-outline ki-check-circle fs-2hx text-success me-4"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="ki-outline ki-information-5 fs-2hx text-danger me-4"></i>
            <div class="d-flex flex-column">
                <h5 class="mb-1 text-danger">{{ __('Error') }}</h5>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="ki-outline ki-information-5 fs-2hx text-warning me-4"></i>
            <div class="d-flex flex-column">
                <h5 class="mb-1 text-warning">{{ __('Warning') }}</h5>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!--begin::Course Header Card-->
    <x-courses.header
        :program="$program"
        :course="$course"
        context="admin"
        :studentCount="$studentCount"
        :pendingGrading="$pendingGrading"
    />
    <!--end::Course Header Card-->

    <div class="row g-5 g-xl-10 mt-2">
        <div class="col-12">
            <!--begin::Content Tabs-->
            <x-courses.tabs :course="$course" :program="$program" :stats="$stats" />

            <!--begin::Tab Content-->
            <div class="tab-content">
                <x-courses.details-tab :program="$program" :course="$course" context="admin" />

                <!--begin::Course Content Tab (Unified Modules, Quizzes, Assignments)-->
                <div class="tab-pane fade" id="modules_tab" role="tabpanel">
                    <div id="modulesTabContent">
                        <x-courses.modules-tab-content
                            context="admin"
                            :program="$program"
                            :course="$course"
                            :modules="$modules"
                            :contentTotals="$contentTotals"
                        />
                    </div>
                </div>
                <!--end::Course Content Tab-->
            </div>
            <!--end::Tab Content-->
            <!--end::Content Tabs-->
        </div>
    </div>

    <!--begin::Clone Course Modal-->
    @include('pages.admin.courses.partials.clone-course-modal', ['program' => $program, 'course' => $course])
    <!--end::Clone Course Modal-->

    <!--begin::Add Module Modal-->
    <x-modals.add-module-form context="admin" :program="$program" :course="$course" :modules="$modules" />
    <!--end::Add Module Modal-->

    <!--begin::Edit Module Modal-->
    <x-modals.edit-module-form context="admin" :program="$program" :course="$course" />
    <!--end::Edit Module Modal-->

    <!--begin::Module Content Modals (Consolidated)-->
    <div id="moduleModalsContainer">
        @foreach($modules as $module)
            {{-- Add modals for each module --}}
            <x-courses.module-modals context="admin" :program="$program" :course="$course" :module="$module" />

            {{-- Edit/Delete modals for each item in module --}}
            @foreach($module->items as $item)
                <x-courses.item-modals context="admin" :program="$program" :course="$course" :module="$module" :item="$item" />
            @endforeach
        @endforeach
    </div>
    <!--end::Module Content Modals-->

    <!--begin::Full Description Modal-->
    @if($course->description && strlen($course->description) > 200)
    <div class="modal fade" id="kt_modal_full_description" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('Course Description') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <p class="text-gray-700 fs-6">{!! nl2br(e($course->description)) !!}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!--end::Full Description Modal-->

    @push('scripts')
        {{-- Course Module AJAX Form Handlers --}}
        <script src="{{ asset('assets/js/custom/admin/courses/main.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/courses/lesson-modals.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/courses/lesson-file-upload.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/courses/assignment-modals.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/courses/quiz-modals.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/courses/content-reorder.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/courses/publish-course.js') }}"></script>

        {{-- Module Edit Handler --}}
        <script>
            (function() {
                // Handle module edit trigger
                document.addEventListener('click', function(e) {
                    const editTrigger = e.target.closest('[data-module-edit-trigger]');
                    if (!editTrigger) return;

                    e.preventDefault();
                    e.stopPropagation();

                    const moduleId = editTrigger.dataset.moduleId;
                    if (!moduleId) {
                        console.error('Module ID not found');
                        return;
                    }

                    const modal = document.getElementById('kt_modal_edit_module');
                    const form = modal?.querySelector('form');

                    if (!modal || !form) {
                        console.error('Edit module modal or form not found');
                        return;
                    }

                    // Get or create Bootstrap modal instance
                    let bsModal = bootstrap.Modal.getInstance(modal);
                    if (!bsModal) {
                        bsModal = new bootstrap.Modal(modal);
                    }

                    // Update form action URL with module ID
                    const originalAction = form.getAttribute('data-original-action') || form.action;
                    if (!form.getAttribute('data-original-action')) {
                        form.setAttribute('data-original-action', originalAction);
                    }
                    form.action = originalAction.replace('__MODULE_ID__', moduleId);

                    // Reset form and clear validation errors
                    const errorAlert = form.querySelector('.ajax-form-errors');
                    if (errorAlert) {
                        errorAlert.classList.add('d-none');
                    }
                    const errorList = form.querySelector('.error-list');
                    if (errorList) {
                        errorList.innerHTML = '';
                    }

                    // Show loading state on submit button
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.setAttribute('data-kt-indicator', 'on');
                        submitBtn.disabled = true;
                    }

                    // Fetch module data
                    const editUrl = `{{ url('/admin/programs/' . $program->id . '/courses/' . $course->id . '/modules') }}/${moduleId}/edit`;

                    fetch(editUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Failed to load module data');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Remove loading state
                        if (submitBtn) {
                            submitBtn.removeAttribute('data-kt-indicator');
                            submitBtn.disabled = false;
                        }

                        if (data.success && data.module) {
                            const module = data.module;

                            // Populate form fields
                            const titleField = form.querySelector('#edit_module_title');
                            const descField = form.querySelector('#edit_module_description');
                            const statusField = form.querySelector('#edit_module_status');
                            const requiresExamPassField = form.querySelector('#edit_module_requires_exam_pass');

                            if (titleField) titleField.value = module.title || '';
                            if (descField) descField.value = module.description || '';
                            if (statusField) statusField.value = module.status || 'draft';
                            if (requiresExamPassField) requiresExamPassField.checked = module.requires_exam_pass || false;

                            // Show the modal AFTER data is loaded
                            bsModal.show();
                        } else {
                            Swal.fire({
                                text: 'Failed to load module data',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Remove loading state
                        if (submitBtn) {
                            submitBtn.removeAttribute('data-kt-indicator');
                            submitBtn.disabled = false;
                        }

                        console.error('Module edit error:', error);
                        Swal.fire({
                            text: error.message || 'An error occurred while loading module data',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    });
                });
            })();

            // Handle module toggle (publish/draft)
            document.addEventListener('click', function(e) {
                const toggleTrigger = e.target.closest('[data-module-toggle-trigger]');
                if (!toggleTrigger) return;

                e.preventDefault();
                e.stopPropagation();

                const moduleId = toggleTrigger.dataset.moduleId;
                const moduleTitle = toggleTrigger.dataset.moduleTitle || 'Module';
                const currentStatus = toggleTrigger.dataset.moduleStatus;
                const toggleUrl = toggleTrigger.dataset.toggleUrl;
                const isPublished = currentStatus === 'published';
                const newStatus = isPublished ? 'draft' : 'published';

                // Show confirmation dialog
                Swal.fire({
                    text: `Are you sure you want to ${isPublished ? 'set this module to draft' : 'publish this module'}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: `Yes, ${isPublished ? 'set to draft' : 'publish'}`,
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        toggleTrigger.setAttribute('data-kt-indicator', 'on');
                        toggleTrigger.disabled = true;

                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                        document.querySelector('input[name="_token"]')?.value ||
                                        '';

                        // Prepare form data
                        const formData = new FormData();
                        formData.append('_token', csrfToken);

                        // Submit via AJAX
                        fetch(toggleUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json().then(data => ({
                                    ok: response.ok,
                                    status: response.status,
                                    data: data
                                }));
                            }
                            throw new Error('Server returned non-JSON response');
                        })
                        .then(result => {
                            // Remove loading state
                            toggleTrigger.removeAttribute('data-kt-indicator');
                            toggleTrigger.disabled = false;

                            if (result.ok && result.data.success) {
                                // Refresh modules tab content immediately (before showing success message)
                                const refreshUrl = `{{ route('admin.programs.courses.modules-content', [$program, $course]) }}`;
                                fetch(refreshUrl, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'text/html'
                                    }
                                })
                                .then(response => response.text())
                                .then(html => {
                                    const container = document.getElementById('modulesTabContent');
                                    if (container) {
                                        container.innerHTML = html;
                                        // Reinitialize any components if needed
                                        if (typeof KTCourseModuleMain !== 'undefined' && KTCourseModuleMain.reinitializeComponents) {
                                            KTCourseModuleMain.reinitializeComponents();
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Failed to refresh modules:', error);
                                    // Don't reload - just log the error
                                });

                                // Show success message (no reload after closing)
                                Swal.fire({
                                    text: result.data.message || 'Module status updated successfully.',
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, got it!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            } else {
                                // Show error message
                                Swal.fire({
                                    text: result.data.message || 'Failed to update module status.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, got it!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            // Remove loading state
                            toggleTrigger.removeAttribute('data-kt-indicator');
                            toggleTrigger.disabled = false;

                            console.error('Toggle module error:', error);
                            Swal.fire({
                                text: error.message || 'An error occurred while updating module status. Please try again.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        });
                    }
                });
            });

                   // Handle lesson toggle (publish/draft)
                   document.addEventListener('click', function(e) {
                       const toggleTrigger = e.target.closest('[data-lesson-toggle-trigger]');
                       if (!toggleTrigger) return;

                       e.preventDefault();
                       e.stopPropagation();

                       const lessonId = toggleTrigger.dataset.lessonId;
                       const lessonTitle = toggleTrigger.dataset.lessonTitle || 'Lesson';
                       const currentStatus = toggleTrigger.dataset.lessonStatus;
                       const toggleUrl = toggleTrigger.dataset.toggleUrl;
                       const isPublished = currentStatus === 'published';
                       const newStatus = isPublished ? 'draft' : 'published';

                       // Show confirmation dialog
                       Swal.fire({
                           text: `Are you sure you want to ${isPublished ? 'set this lesson to draft' : 'publish this lesson'}?`,
                           icon: 'question',
                           showCancelButton: true,
                           confirmButtonText: `Yes, ${isPublished ? 'set to draft' : 'publish'}`,
                           cancelButtonText: 'Cancel',
                           buttonsStyling: false,
                           customClass: {
                               confirmButton: 'btn btn-primary',
                               cancelButton: 'btn btn-light'
                           }
                       }).then((result) => {
                           if (result.isConfirmed) {
                               // Show loading state
                               toggleTrigger.setAttribute('data-kt-indicator', 'on');
                               toggleTrigger.disabled = true;

                               // Get CSRF token
                               const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                               document.querySelector('input[name="_token"]')?.value ||
                                               '';

                               // Prepare form data
                               const formData = new FormData();
                               formData.append('_token', csrfToken);

                               // Submit via AJAX
                               fetch(toggleUrl, {
                                   method: 'POST',
                                   body: formData,
                                   headers: {
                                       'X-Requested-With': 'XMLHttpRequest',
                                       'Accept': 'application/json'
                                   }
                               })
                               .then(response => {
                                   const contentType = response.headers.get('content-type');
                                   if (contentType && contentType.includes('application/json')) {
                                       return response.json().then(data => ({
                                           ok: response.ok,
                                           status: response.status,
                                           data: data
                                       }));
                                   }
                                   throw new Error('Server returned non-JSON response');
                               })
                               .then(result => {
                                   // Remove loading state
                                   toggleTrigger.removeAttribute('data-kt-indicator');
                                   toggleTrigger.disabled = false;

                                   if (result.ok && result.data.success) {
                                       // Show success message
                                       Swal.fire({
                                           text: result.data.message || 'Lesson status updated successfully.',
                                           icon: 'success',
                                           buttonsStyling: false,
                                           confirmButtonText: 'Ok, got it!',
                                           customClass: {
                                               confirmButton: 'btn btn-primary'
                                           }
                                       });
                                   } else {
                                       // Show error message
                                       Swal.fire({
                                           text: result.data.message || 'Failed to update lesson status.',
                                           icon: 'error',
                                           buttonsStyling: false,
                                           confirmButtonText: 'Ok, got it!',
                                           customClass: {
                                               confirmButton: 'btn btn-primary'
                                           }
                                       });
                                   }
                                   // Refresh modules tab content to reflect changes
                                   const refreshUrl = `{{ route('admin.programs.courses.modules-content', [$program, $course]) }}`;
                                   fetch(refreshUrl, {
                                       headers: {
                                           'X-Requested-With': 'XMLHttpRequest',
                                           'Accept': 'text/html'
                                       }
                                   })
                                   .then(response => response.text())
                                   .then(html => {
                                       const container = document.getElementById('modulesTabContent');
                                       if (container) {
                                           container.innerHTML = html;
                                           // Reinitialize any components if needed
                                           if (typeof KTCourseModuleMain !== 'undefined' && KTCourseModuleMain.reinitializeComponents) {
                                               KTCourseModuleMain.reinitializeComponents();
                                           }
                                       }
                                   })
                                   .catch(error => {
                                       console.error('Failed to refresh modules:', error);
                                   });
                               })
                               .catch(error => {
                                   // Remove loading state
                                   toggleTrigger.removeAttribute('data-kt-indicator');
                                   toggleTrigger.disabled = false;

                                   console.error('Lesson toggle error:', error);
                                   Swal.fire({
                                       text: error.message || 'An error occurred while toggling lesson status',
                                       icon: 'error',
                                       buttonsStyling: false,
                                       confirmButtonText: 'Ok, got it!',
                                       customClass: {
                                           confirmButton: 'btn btn-primary'
                                       }
                                   });
                               });
                           }
                       });
                   });

                   // Handle module delete
                   document.addEventListener('click', function(e) {
                const deleteTrigger = e.target.closest('[data-module-delete-trigger]');
                if (!deleteTrigger) return;

                e.preventDefault();
                e.stopPropagation();

                const moduleId = deleteTrigger.dataset.moduleId;
                const moduleTitle = deleteTrigger.dataset.moduleTitle || 'Module';
                const deleteUrl = deleteTrigger.dataset.deleteUrl;

                // Show confirmation dialog
                Swal.fire({
                    text: `Are you sure you want to delete "${moduleTitle}" permanently? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        deleteTrigger.setAttribute('data-kt-indicator', 'on');
                        deleteTrigger.disabled = true;

                        // Show loading dialog
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait while we delete the module',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                        document.querySelector('input[name="_token"]')?.value ||
                                        '';

                        // Prepare form data
                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append('_method', 'DELETE');

                        // Submit via AJAX
                        fetch(deleteUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json().then(data => ({
                                    ok: response.ok,
                                    status: response.status,
                                    data: data
                                }));
                            }
                            throw new Error('Server returned non-JSON response');
                        })
                        .then(result => {
                            // Remove loading state
                            deleteTrigger.removeAttribute('data-kt-indicator');
                            deleteTrigger.disabled = false;

                            if (result.ok && result.data.success) {
                                // Refresh modules tab content immediately (before showing success message)
                                const refreshUrl = `{{ route('admin.programs.courses.modules-content', [$program, $course]) }}`;
                                fetch(refreshUrl, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'text/html'
                                    }
                                })
                                .then(response => response.text())
                                .then(html => {
                                    const container = document.getElementById('modulesTabContent');
                                    if (container) {
                                        container.innerHTML = html;
                                        // Reinitialize any components if needed
                                        if (typeof KTCourseModuleMain !== 'undefined' && KTCourseModuleMain.reinitializeComponents) {
                                            KTCourseModuleMain.reinitializeComponents();
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Failed to refresh modules:', error);
                                    // Don't reload - just log the error
                                });

                                // Show success message (no reload after closing)
                                Swal.fire({
                                    text: result.data.message || 'Module deleted successfully.',
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, got it!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            } else {
                                // Show error message
                                Swal.fire({
                                    text: result.data.message || 'Failed to delete module.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, got it!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            // Remove loading state
                            deleteTrigger.removeAttribute('data-kt-indicator');
                            deleteTrigger.disabled = false;

                            console.error('Delete module error:', error);
                            Swal.fire({
                                text: error.message || 'An error occurred while deleting the module. Please try again.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        });
                    }
                });
            });

            // Handle module creation - inject modals after new module is created
            document.addEventListener('ajax-form-success', function(event) {
                const modal = event.detail?.modal;
                const data = event.detail?.data;

                // Check if this is the add module modal
                if (modal && modal.id === 'kt_modal_add_module' && data && data.module && data.module.id) {
                    const moduleId = data.module.id;
                    const modalsUrl = `{{ route('admin.programs.courses.modules.modals', [$program, $course, '__MODULE_ID__']) }}`.replace('__MODULE_ID__', moduleId);

                    // Fetch modals for the new module
                    fetch(modalsUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Find modals container
                        const modalsContainer = document.getElementById('moduleModalsContainer');
                        if (!modalsContainer) {
                            console.error('Module modals container not found');
                            return;
                        }

                        // Inject the modals HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        // Append each modal to the container
                        while (tempDiv.firstChild) {
                            modalsContainer.appendChild(tempDiv.firstChild);
                        }

                        // Reinitialize AJAX forms for the new modals
                        if (typeof KTCourseModuleMain !== 'undefined' && typeof KTCourseModuleMain.initAjaxForms === 'function') {
                            KTCourseModuleMain.initAjaxForms();
                        }

                        // Reinitialize Quill editors if needed
                        if (typeof KTLessonModals !== 'undefined' && typeof KTLessonModals.reinitializeQuillEditors === 'function') {
                            KTLessonModals.reinitializeQuillEditors();
                        }
                    })
                    .catch(error => {
                        console.error('Failed to load module modals:', error);
                    });
                }
            });
        </script>
    @endpush


    {{-- Module UX Enhancement CSS --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/custom/admin/courses/module-cards.css') }}">
    @endpush

</x-default-layout>
