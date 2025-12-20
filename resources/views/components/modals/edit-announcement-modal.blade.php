{{--
 * Edit Announcement Modal Component
 *
 * Modal form for editing course announcements
 *
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
--}}

@props([
    'program',
    'course',
])

<!--begin::Modal - Edit Announcement-->
<div class="modal fade" id="kt_modal_edit_announcement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Edit Announcement</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form id="kt_modal_edit_announcement_form"
                data-original-action="{{ route('instructor.announcements.update', [$program, $course, '__ANNOUNCEMENT_ID__']) }}"
                action="{{ route('instructor.announcements.update', [$program, $course, '__ANNOUNCEMENT_ID__']) }}"
                method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <!--begin::Alert for errors-->
                    <div class="alert alert-danger d-none ajax-form-errors">
                        <div class="alert-text">
                            <ul class="error-list mb-0"></ul>
                        </div>
                    </div>
                    <!--end::Alert-->

                    <!--begin::Input group - Title-->
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Title</label>
                        <input type="text" name="title" id="edit_announcement_title"
                            class="form-control form-control-solid mb-3 mb-lg-0"
                            placeholder="Announcement title" required />
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Content-->
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Content</label>
                        <textarea name="content" id="edit_announcement_content"
                            class="form-control form-control-solid" rows="6"
                            placeholder="Announcement content" required></textarea>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Priority-->
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Priority</label>
                        <select name="priority" id="edit_announcement_priority"
                            class="form-select form-select-solid" required>
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Is Published-->
                    <div class="fv-row mb-7">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                id="edit_is_published" />
                            <label class="form-check-label fw-semibold text-gray-700 ms-3" for="edit_is_published">
                                Publish announcement
                            </label>
                        </div>
                        <div class="form-text">Unpublished announcements will be saved as drafts</div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Send Email-->
                    <div class="fv-row mb-7">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="send_email" value="1"
                                id="edit_send_email" />
                            <label class="form-check-label fw-semibold text-gray-700 ms-3" for="edit_send_email">
                                Send email notification to students
                            </label>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">Update Announcement</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal - Edit Announcement-->
