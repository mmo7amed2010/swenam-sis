<script>
    (function() {
        const modalEl = document.getElementById('kt_modal_add_user');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('kt_modal_add_user_form');
        const submitBtn = document.getElementById('kt_modal_add_user_submit');
        const titleEl = document.querySelector('#kt_modal_add_user_header h2');
        const idInput = form.querySelector('input[name="user_id"]');
        const nameInput = form.querySelector('input[name="name"]');
        const emailInput = form.querySelector('input[name="email"]');
        const passwordInput = form.querySelector('input[name="password"]');
        const passwordConfirmationInput = form.querySelector('input[name="password_confirmation"]');
        const passwordLabel = form.querySelector('#password_label');
        const passwordHelp = form.querySelector('#password_help');
        const passwordConfirmationRow = form.querySelector('#password_confirmation_row');
        const avatarWrapper = modalEl.querySelector('.image-input');

        const originalTitle = titleEl?.textContent || 'Add User';
        const storeAction = form.getAttribute('action');

        function csrf() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

        function setLoading(b) {
            submitBtn?.querySelector('.indicator-label')?.classList.toggle('d-none', b);
            submitBtn?.querySelector('.indicator-progress')?.classList.toggle('d-none', !b);
            submitBtn && (submitBtn.disabled = !!b);
        }

        function resetForm() {
            form.reset();
            if (idInput) idInput.value = '';
            // Reset roles radios (default to first)
            const radios = form.querySelectorAll('input[name="roles[]"]');
            let first = true; radios.forEach(r => { r.checked = first; first = false; });
            // Remove method override
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
            // Restore action to store
            form.setAttribute('action', storeAction);
            // Restore title
            if (titleEl) titleEl.textContent = originalTitle;
            // Reset avatar preview
            avatarWrapper?.classList.add('image-input-empty');
            const preview = avatarWrapper?.querySelector('.image-input-wrapper');
            if (preview) preview.style.backgroundImage = '';
            // Reset password fields for create mode
            if (passwordLabel) passwordLabel.classList.add('required');
            if (passwordHelp) passwordHelp.style.display = 'none';
            if (passwordConfirmationRow) passwordConfirmationRow.style.display = 'none';
            if (passwordConfirmationInput) passwordConfirmationInput.removeAttribute('required');
        }

        async function openEditUser(id) {
            resetForm();
            setLoading(true);
            try {
                const res = await fetch(`/user-management/users/${id}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed to load user');
                const json = await res.json();
                const data = json.data || {};
                if (idInput) idInput.value = data.id || '';
                if (nameInput) nameInput.value = data.name || '';
                if (emailInput) emailInput.value = data.email || '';
                if (passwordInput) passwordInput.value = '';
                if (passwordConfirmationInput) passwordConfirmationInput.value = '';
                // Set role radio
                const radios = form.querySelectorAll('input[name="roles[]"]');
                radios.forEach(r => { r.checked = (r.value === data.role); });
                // Set avatar preview if provided
                if (data.profile_photo_url && avatarWrapper) {
                    avatarWrapper.classList.remove('image-input-empty');
                    const preview = avatarWrapper.querySelector('.image-input-wrapper');
                    if (preview) preview.style.backgroundImage = `url('${data.profile_photo_url}')`;
                }
                // Switch form to update - show password confirmation and make password optional
                form.setAttribute('action', `/user-management/users/${data.id}`);
                const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'PUT';
                form.appendChild(method);
                // Update password field for edit mode
                if (passwordLabel) passwordLabel.classList.remove('required');
                if (passwordHelp) passwordHelp.style.display = 'block';
                if (passwordConfirmationRow) passwordConfirmationRow.style.display = 'block';
                if (passwordInput) passwordInput.removeAttribute('required');
                if (titleEl) titleEl.textContent = '{{ __("Edit Admin") }}';
                modal.show();
            } catch (e) {
                if (window.toastr) toastr.error('Failed to load user');
            } finally { setLoading(false); }
        }

        document.addEventListener('click', function(e) {
            // Handle edit action
            const editLink = e.target.closest('[data-kt-action="update_row"][data-kt-user-id]');
            if (editLink) {
                e.preventDefault();
                const id = editLink.getAttribute('data-kt-user-id');
                openEditUser(id);
            }

            // Handle delete action
            const deleteBtn = e.target.closest('[data-kt-action="delete_row"][data-kt-user-id]');
            if (deleteBtn) {
                e.preventDefault();
                const deleteUrl = deleteBtn.getAttribute('data-kt-delete-url');
                if (!deleteUrl) return;

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You will not be able to recover this admin!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf(),
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire('{{ __("Deleted!") }}', data.message || '{{ __("Admin has been deleted.") }}', 'success');
                            if (window.usersTable) window.usersTable.reload();
                        })
                        .catch(() => {
                            Swal.fire('{{ __("Error!") }}', '{{ __("Failed to delete admin.") }}', 'error');
                        });
                    }
                });
            }
        });

        modalEl.addEventListener('hidden.bs.modal', resetForm);

        // Show/hide password confirmation based on password input
        if (passwordInput && passwordConfirmationRow) {
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    passwordConfirmationRow.style.display = 'block';
                    if (passwordConfirmationInput) passwordConfirmationInput.setAttribute('required', 'required');
                } else {
                    passwordConfirmationRow.style.display = 'none';
                    if (passwordConfirmationInput) {
                        passwordConfirmationInput.removeAttribute('required');
                        passwordConfirmationInput.value = '';
                    }
                }
            });
        }
    })();
</script>

