<div class="modal fade" id="kt_modal_add_contributor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_contributor_header">
                <h2 class="fw-bold mb-0">
                    <span id="tr-modal-title-add">{{ __('Add Contributor') }}</span>
                    <span id="tr-modal-title-edit" class="d-none">{{ __('Edit Contributor') }}</span>
                </h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross','fs-1') !!}
                </div>
            </div>
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_add_contributor_form" class="form" action="#" autocomplete="off">
                    @csrf
                    <input type="hidden" id="tr_id" value="">

                    <div id="tr_form_errors" class="alert alert-danger d-none mb-7"></div>

                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ __('Name') }}</label>
                        <input type="text" id="tr_name" class="form-control form-control-solid" placeholder="{{ __('Full name') }}" />
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ __('Email') }}</label>
                        <input type="email" id="tr_email" class="form-control form-control-solid" placeholder="example@domain.com" />
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ __('Role') }}</label>
                        <select id="tr_role" class="form-select form-select-solid">
                            <option value="1">{{ __('Owner') }}</option>
                            <option value="2">{{ __('Translator') }}</option>
                        </select>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">{{ __('Password') }}</label>
                        <input type="password" id="tr_password" class="form-control form-control-solid" placeholder="********" />
                        <div class="form-text d-none" id="tr_password_hint">{{ __('Leave blank to keep the current password unchanged') }}</div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Discard') }}</button>
                        <button type="submit" class="btn btn-primary" id="kt_modal_add_contributor_submit">
                            <span class="indicator-label">{{ __('Submit') }}</span>
                            <span class="indicator-progress d-none">
                                {{ __('Please wait...') }}
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('pages.admin.translations.scripts.modal')
    @endpush
</div>
