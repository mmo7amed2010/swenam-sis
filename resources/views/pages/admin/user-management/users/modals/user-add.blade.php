<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h2 class="fw-bold">{{ __('Add Admin') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_add_user_form" class="form" action="{{ route('user-management.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="" />
                    <x-forms.validation-errors />
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7">
                            <label class="d-block fw-semibold fs-6 mb-5">{{ __('Avatar') }}</label>
                            <style>
                                .image-input-placeholder { background-image: url('{{ image('svg/files/blank-image.svg') }}'); }
                                [data-bs-theme="dark"] .image-input-placeholder { background-image: url('{{ image('svg/files/blank-image-dark.svg') }}'); }
                            </style>
                            <div class="image-input image-input-outline image-input-placeholder image-input-empty" data-kt-image-input="true">
                                <div class="image-input-wrapper w-125px h-125px"></div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="{{ __('Change avatar') }}">
                                    {!! getIcon('pencil', 'fs-7') !!}
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="avatar_remove" />
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="{{ __('Cancel avatar') }}">
                                    {!! getIcon('cross', 'fs-2') !!}
                                </span>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="{{ __('Remove avatar') }}">
                                    {!! getIcon('cross', 'fs-2') !!}
                                </span>
                            </div>
                            <div class="form-text">{{ __('Allowed file types: png, jpg, jpeg.') }}</div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">{{ __('Full Name') }}</label>
                            <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="{{ __('Full name') }}" value="{{ old('name') }}" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="{{ __('example@domain.com') }}" value="{{ old('email') }}" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2" id="password_label">{{ __('Password') }}</label>
                            <input type="password" name="password" id="password_input" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="{{ __('Password') }}" />
                            <div class="form-text" id="password_help" style="display: none;">{{ __('Leave blank to keep current password. Minimum 8 characters if changing.') }}</div>
                        </div>
                        <div class="fv-row mb-7" id="password_confirmation_row" style="display: none;">
                            <label class="required fw-semibold fs-6 mb-2">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation_input" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="{{ __('Confirm Password') }}" />
                        </div>

                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Discard') }}</button>
                        <button type="submit" class="btn btn-primary" id="kt_modal_add_user_submit">
                            <span class="indicator-label">{{ __('Submit') }}</span>
                            <span class="indicator-progress" style="display: none;">{{ __('Please wait...') }}<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
