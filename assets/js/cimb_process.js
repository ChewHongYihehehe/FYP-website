document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('password-form');
    const passwordInput = document.getElementById('password');
    const formError = document.getElementById('form-error');
    const secureCheckbox = document.getElementById('secure-checkbox');
    const loginButton = passwordForm.querySelector('.btn');
    const toggleVisibilityButton = document.querySelector('.toggle-visibility');

    passwordInput.disabled = true;
    loginButton.disabled = true;

    function validateInput() {
        formError.style.display = 'none'; // Clear previous error messages

        if (passwordInput.value.trim() === '') {
            formError.textContent = 'This field is required';
            formError.style.display = 'block';
        } else if (passwordInput.value.trim().length < 8) {
            formError.textContent = 'Please enter at least 8 characters';
            formError.style.display = 'block';
        }
    }

    // Validate on input
    passwordInput.addEventListener('input', function(){
        if(!secureCheckbox.checked){
            return;
        }
        validateInput();
    });

    // Validate on form submission
    passwordForm.addEventListener('submit', function(event) {
        let hasError = false; // Flag to track if there are errors

        // Clear previous error messages
        formError.style.display = 'none';

        // Check if the input is empty
        if (passwordInput.value.trim() === '') {
            event.preventDefault();
            formError.textContent = 'This field is required';
            formError.style.display = 'block';
            hasError = true;
        } 
        // Check if the input is less than 8 characters
        else if (passwordInput.value.trim().length < 8) {
            event.preventDefault();
            formError.textContent = 'Please enter at least 8 characters';
            formError.style.display = 'block';
            hasError = true;
        }
    });

    secureCheckbox.addEventListener('change', function () {
        if (secureCheckbox.checked) {
            passwordInput.disabled = false; // Enable password input
            loginButton.disabled = false; // Enable login button
            passwordInput.focus(); // Optional: focus on the password input
        } else {
            passwordInput.disabled = true; // Disable password input
            loginButton.disabled = true; // Disable login button
            passwordInput.value = ''; // Clear the password field
            formError.style.display = 'none'; // Clear any error messages
        }
    });

    toggleVisibilityButton.addEventListener('click', function(){
        if(passwordInput.type === 'password'){
            passwordInput.type= 'text';
            toggleVisibilityButton.innerHTML = '<i class="fas fa-eye"></i>';
        }else{
            passwordInput.type = 'password';
            toggleVisibilityButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
        }


    });
});