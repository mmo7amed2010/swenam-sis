"use strict";

// Class definition
var KTSigninGeneral = function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleValidation = function (e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'email': {
                        validators: {
                            regexp: {
                                regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                message: 'The value is not a valid email address',
                            },
                            notEmpty: {
                                message: 'Email address is required'
                            }
                        }
                    },
                    'password': {
                        validators: {
                            notEmpty: {
                                message: 'The password is required'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',  // comment to enable invalid state icons
                        eleValidClass: '' // comment to enable valid state icons
                    })
                }
            }
        );
    }

    var handleSubmitDemo = function (e) {
        // Handle form submit
        submitButton.addEventListener('click', function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Show loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;


                    // Simulate ajax request
                    setTimeout(function () {
                        // Hide loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;

                        // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        Swal.fire({
                            text: "You have successfully logged in!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                form.querySelector('[name="email"]').value = "";
                                form.querySelector('[name="password"]').value = "";

                                //form.submit(); // submit form
                                var redirectUrl = form.getAttribute('data-kt-redirect-url');
                                if (redirectUrl) {
                                    location.href = redirectUrl;
                                }
                            }
                        });
                    }, 2000);
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });
    }

    var handleSubmitAjax = function (e) {
        // Handle form submit
        submitButton.addEventListener('click', function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Show loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;

                    // Check axios library docs: https://axios-http.com/docs/intro
                    axios.post(submitButton.closest('form').getAttribute('action'), new FormData(form), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        validateStatus: function (status) {
                            // Accept both 2xx and 3xx status codes (redirects)
                            return status >= 200 && status < 400;
                        }
                    }).then(function (response) {
                        // Laravel returns redirect (302) on successful login
                        // Check if response is a redirect or success
                        if (response.status === 200 || response.status === 302 || (response.data && response.data.redirect)) {
                            // Success - redirect to dashboard
                            const redirectUrl = response.data && response.data.redirect 
                                ? response.data.redirect 
                                : form.getAttribute('data-kt-redirect-url');
                            
                            if (redirectUrl) {
                                window.location.href = redirectUrl;
                            } else {
                                // Fallback: reload page
                                window.location.reload();
                            }
                        } else {
                            // Hide loading indication
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            // Show error popup
                            Swal.fire({
                                text: "Sorry, the email or password is incorrect, please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    }).catch(function (error) {
                        // Hide loading indication
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;

                        // Handle validation errors (422)
                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors || {};
                            let errorMessage = "Please correct the following errors:\n";
                            
                            for (let field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    errorMessage += errors[field][0] + "\n";
                                }
                            }

                            Swal.fire({
                                text: errorMessage,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        } else if (error.response && error.response.status === 302) {
                            // Redirect response - follow it
                            const redirectUrl = form.getAttribute('data-kt-redirect-url');
                            if (redirectUrl) {
                                window.location.href = redirectUrl;
                            } else {
                                window.location.reload();
                            }
                        } else {
                            // Other errors
                            const errorMessage = error.response && error.response.data && error.response.data.message
                                ? error.response.data.message
                                : "Sorry, looks like there are some errors detected, please try again.";

                            Swal.fire({
                                text: errorMessage,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });
    }

    var isValidUrl = function(url) {
        try {
            new URL(url);
            return true;
        } catch (e) {
            return false;
        }
    }

    var handleSubmitNormal = function (e) {
        // Handle form submit with normal POST (not AJAX)
        submitButton.addEventListener('click', function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Show loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;

                    // Submit form normally (not AJAX)
                    form.submit();
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Please fill in all required fields correctly.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });
    }

    // Public functions
    return {
        // Initialization
        init: function () {
            form = document.querySelector('#kt_sign_in_form');
            submitButton = document.querySelector('#kt_sign_in_submit');

            if (!form || !submitButton) {
                return;
            }

            handleValidation();

            var action = form.getAttribute('action');
            // Use normal form submission for relative URLs (Laravel login)
            // Use AJAX only for absolute URLs
            if (action && isValidUrl(action)) {
                handleSubmitAjax(); // use for ajax submit
            } else {
                handleSubmitNormal(); // use normal form submission for Laravel
            }
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTSigninGeneral.init();
});
