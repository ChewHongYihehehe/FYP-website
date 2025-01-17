document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const formError = document.getElementById('form-error');
    const loginIdInput = document.getElementById('login_id');
    const cancelIcon = document.getElementById('cancel-icon');

    function validateInput(){
        cancelIcon.style.display = 'none';
        formError.style.display = 'none';

        if(loginIdInput.value.trim() === ''){
            cancelIcon.style.display = 'inline';
            formError.textContent = 'CIMB Clicks ID is mandatory';
            formError.style.display = 'block';
        }
        // Check if the input is less than 6 characters
        else if (loginIdInput.value.trim().length < 6) {
            cancelIcon.style.display = 'inline';
            formError.textContent = 'Please enter at least 6 characters';
            formError.style.display = 'block';
        }else{
            cancelIcon.style.display = 'inline';
        }
    }

// Validate on input
loginIdInput.addEventListener('input', validateInput);

    cancelIcon.addEventListener('click', function(){
        loginIdInput.value = '';
        cancelIcon.style.display = 'none';
        formError.style.display = 'none';
    });

    loginForm.addEventListener('submit', function(event) {
        let hasError = false; // Flag to track if there are errors

        // Clear previous error messages
        cancelIcon.style.display = 'none';
        formError.style.display = 'none';

        // Check if the input is empty
        if (loginIdInput.value.trim() === '') {
            event.preventDefault();
            cancelIcon.style.display = 'inline';
            formError.textContent = 'CIMB Clicks ID is mandatory';
            formError.style.display = 'block';
            hasError = true;
        } 
        // Check if the input is less than 6 characters
        else if (loginIdInput.value.trim().length < 6) {
            event.preventDefault();
            cancelIcon.style.display = 'inline';
            formError.textContent = 'Please enter at least 6 characters';
            formError.style.display = 'block';
            hasError = true;
        }

    });
});