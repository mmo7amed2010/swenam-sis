// Initialize KTMenu
KTMenu.init();

// Add click event listener to delete buttons
document.querySelectorAll('[data-kt-action="delete_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Swal.fire({
            text: 'Are you sure you want to remove?',
            icon: 'warning',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const id = this.getAttribute('data-kt-user-id');
                if (window.deleteUser) window.deleteUser(id);
            }
        });
    });
});

// Add click event listener to update buttons
document.querySelectorAll('[data-kt-action="update_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        const id = this.getAttribute('data-kt-user-id');
        if (window.openUserEdit) window.openUserEdit(id);
    });
});

// Listen for 'success' event emitted by Livewire
document.addEventListener('app:success', () => {
    if (window.LaravelDataTables && window.LaravelDataTables['users-table']) {
        LaravelDataTables['users-table'].ajax.reload();
    }
});
