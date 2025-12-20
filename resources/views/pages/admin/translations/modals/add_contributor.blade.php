<div class="modal fade" id="kt_modal_add_contributor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold mb-0">
                    <span id="contrib-modal-title-add">{{ __('Add Contributor') }}</span>
                    <span id="contrib-modal-title-edit" class="d-none">{{ __('Edit Contributor') }}</span>
                </h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross','fs-1') !!}
                </div>
            </div>
            <div class="modal-body mx-5 my-7">
                <form id="kt_modal_add_contributor_form" class="form" action="#" autocomplete="off">
                    @csrf
                    <input type="hidden" id="contrib_id" />
                    <div id="contrib_form_errors" class="alert alert-danger d-none mb-7"></div>
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ __('Name') }}</label>
                        <input type="text" id="contrib_name" class="form-control form-control-solid" placeholder="{{ __('Full name') }}" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ __('Email') }}</label>
                        <input type="email" id="contrib_email" class="form-control form-control-solid" placeholder="example@domain.com" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">{{ __('Role') }}</label>
                        <select id="contrib_role" class="form-select form-select-solid">
                            <option value="0">Owner</option>
                            <option value="1">Translator</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7" id="contrib_password_wrap">
                        <label class="fw-semibold fs-6 mb-2" id="contrib_password_label">{{ __('Password') }}</label>
                        <input type="password" id="contrib_password" class="form-control form-control-solid" placeholder="{{ __('Password (min 8 chars)') }}" />
                        <div class="form-text d-none" id="contrib_password_hint">{{ __('Leave blank to keep the current password unchanged') }}</div>
                    </div>
                    <div class="text-center pt-10">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Discard') }}</button>
                        <button type="submit" class="btn btn-primary" id="kt_modal_add_contributor_submit">
                            <span class="indicator-label">{{ __('Submit') }}</span>
                            <span class="indicator-progress d-none">{{ __('Please wait...') }}<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            (function(){
                const modalEl = document.getElementById('kt_modal_add_contributor');
                if (!modalEl) return;
                const modal = new bootstrap.Modal(modalEl);
                const form = document.getElementById('kt_modal_add_contributor_form');
                const submitBtn = document.getElementById('kt_modal_add_contributor_submit');
                const idInput = document.getElementById('contrib_id');
                const nameInput = document.getElementById('contrib_name');
                const emailInput = document.getElementById('contrib_email');
                const roleInput = document.getElementById('contrib_role');
                const passwordInput = document.getElementById('contrib_password');
                const passwordHint = document.getElementById('contrib_password_hint');
                const titleAdd = document.getElementById('contrib-modal-title-add');
                const titleEdit = document.getElementById('contrib-modal-title-edit');
                const errorsBox = document.getElementById('contrib_form_errors');

                function csrf(){ return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }
                function setLoading(b){
                    submitBtn.querySelector('.indicator-label').classList.toggle('d-none', b);
                    submitBtn.querySelector('.indicator-progress').classList.toggle('d-none', !b);
                    submitBtn.disabled = !!b;
                }
                function resetForm(){
                    form.reset();
                    idInput.value = '';
                    passwordHint.classList.add('d-none');
                    titleAdd.classList.remove('d-none');
                    titleEdit.classList.add('d-none');
                    errorsBox.classList.add('d-none');
                    errorsBox.innerHTML = '';
                }
                function fillForm(data){
                    idInput.value = data.id || '';
                    nameInput.value = data.name || '';
                    emailInput.value = data.email || '';
                    roleInput.value = (typeof data.role !== 'undefined') ? data.role : (data.role_id || '1');
                    passwordInput.value = '';
                    passwordHint.classList.toggle('d-none', !data.id);
                    titleAdd.classList.toggle('d-none', !!data.id);
                    titleEdit.classList.toggle('d-none', !data.id);
                }
                function showErrors(messages){
                    const list = Array.isArray(messages) ? messages : Object.values(messages || {}).flat();
                    if (!list.length) return;
                    errorsBox.classList.remove('d-none');
                    errorsBox.innerHTML = '<ul class="mb-0">' + list.map(m => '<li>' + m + '</li>').join('') + '</ul>';
                }

                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    setLoading(true);
                    errorsBox.classList.add('d-none');
                    errorsBox.innerHTML = '';
                    const payload = {
                        id: idInput.value || null,
                        name: nameInput.value,
                        email: emailInput.value,
                        role: roleInput.value,
                        password: passwordInput.value
                    };
                    try {
                        const res = await fetch(`{{ route('translations.contributors.save') }}`, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                            body: JSON.stringify(payload)
                        });
                        if (res.status === 422) { const data = await res.json(); showErrors(data.errors || []); return; }
                        if (!res.ok) throw new Error('Request failed');
                        const data = await res.json();
                        if (window.toastr) toastr.success(data.message || 'Saved');
                        modal.hide();
                        setTimeout(() => window.location.reload(), 500);
                    } catch (err) {
                        showErrors([ (window.Lang ? Lang.get('Something went wrong. Please try again.') : 'Something went wrong. Please try again.') ]);
                    } finally { setLoading(false); }
                });

                // Expose helpers
                window.openContributorCreate = function(){ resetForm(); modal.show(); };
                window.openContributorEdit = async function(id){
                    resetForm();
                    try {
                        const res = await fetch(`{{ route('translations.contributors.show', '') }}/${id}`, { headers: { 'Accept': 'application/json' } });
                        const json = await res.json();
                        if (json && json.data) fillForm(json.data);
                    } catch(e) {}
                    modal.show();
                };
                modalEl.addEventListener('hidden.bs.modal', resetForm);
            })();
        </script>
    @endpush
</div>

