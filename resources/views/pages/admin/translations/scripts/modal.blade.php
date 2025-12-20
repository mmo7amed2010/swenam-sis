<script>
    (function() {
        const modalEl = document.getElementById('kt_modal_add_contributor');
        if (!modalEl) return;

        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('kt_modal_add_contributor_form');
        const submitBtn = document.getElementById('kt_modal_add_contributor_submit');
        const errorsBox = document.getElementById('tr_form_errors');

        const titleAdd = document.getElementById('tr-modal-title-add');
        const titleEdit = document.getElementById('tr-modal-title-edit');

        const idInput = document.getElementById('tr_id');
        const nameInput = document.getElementById('tr_name');
        const emailInput = document.getElementById('tr_email');
        const roleInput = document.getElementById('tr_role');
        const passwordInput = document.getElementById('tr_password');
        const passwordHint = document.getElementById('tr_password_hint');

        const routes = {
            save: @json(route('translations.contributors.save')),
            show: (id) => @json(route('translations.contributors.show', ['id' => '___ID___'])).replace('___ID___', id),
        };

        function csrf() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

        function setLoading(loading) {
            if (loading) {
                submitBtn.querySelector('.indicator-label').classList.add('d-none');
                submitBtn.querySelector('.indicator-progress').classList.remove('d-none');
                submitBtn.setAttribute('disabled', 'disabled');
            } else {
                submitBtn.querySelector('.indicator-label').classList.remove('d-none');
                submitBtn.querySelector('.indicator-progress').classList.add('d-none');
                submitBtn.removeAttribute('disabled');
            }
        }

        function resetForm() {
            form.reset();
            idInput.value = '';
            errorsBox.classList.add('d-none');
            errorsBox.innerHTML = '';
            titleAdd.classList.remove('d-none');
            titleEdit.classList.add('d-none');
            passwordHint.classList.add('d-none');
        }

        function fillForm(data) {
            idInput.value = data.id || '';
            nameInput.value = data.name || '';
            emailInput.value = data.email || '';
            roleInput.value = data.role || '';
            passwordInput.value = '';
            titleAdd.classList.add('d-none');
            titleEdit.classList.remove('d-none');
            passwordHint.classList.remove('d-none');
        }

        function showErrors(messages) {
            const list = Array.isArray(messages) ? messages : Object.values(messages || {}).flat();
            if (!list.length) return;
            errorsBox.classList.remove('d-none');
            errorsBox.innerHTML = '<ul class="mb-0">' + list.map(m => '<li>' + m + '</li>').join('') + '</ul>';
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            setLoading(true);
            errorsBox.classList.add('d-none');
            errorsBox.innerHTML = '';

            const payload = {
                id: idInput.value || null,
                name: nameInput.value,
                email: emailInput.value,
                role: roleInput.value,
                password: passwordInput.value,
            };

            try {
                const res = await fetch(routes.save, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                    },
                    body: JSON.stringify(payload),
                });
                if (res.status === 422) {
                    const data = await res.json();
                    showErrors(data.errors || []);
                    return;
                }
                if (!res.ok) throw new Error('Request failed');
                const data = await res.json();
                if (data.success) {
                    if (window.toastr) toastr.success(data.message || 'Saved');
                    modal.hide();
                    setTimeout(function() { window.location.reload(); }, 500);
                } else {
                    showErrors([data.message || 'Failed']);
                }
            } catch (err) {
                showErrors([Lang.get ? Lang.get('Something went wrong. Please try again.') : 'Something went wrong. Please try again.']);
            } finally {
                setLoading(false);
            }
        });

        // global for list page
        window.editContributor = async function(id) {
            resetForm();
            try {
                const res = await fetch(routes.show(id), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed');
                const json = await res.json();
                fillForm(json.data || {});
                modal.show();
            } catch (err) {
                if (window.toastr) toastr.error('Failed to load contributor');
            }
        }

        modalEl.addEventListener('hidden.bs.modal', resetForm);
    })();
</script>

