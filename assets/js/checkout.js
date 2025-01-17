
document.addEventListener('DOMContentLoaded', function() {
    const cardNumberInput = document.getElementById('card-number');
    const visaLogo = document.querySelector('.icons img[src*="visa"]');
    const mastercardLogo = document.querySelector('.icons img[src*="mastercard"]');
    const expiryInput = document.querySelector('input[placeholder="MM/YY"]');
    const cvvInput = document.getElementById('cvv');
    const cardholderNameInput = document.getElementById('cardholder-name');
    const paymentForm = document.getElementById('payment-form');
    const debitCardInput = document.getElementById('debit-card');
    const onlineBankInput = document.getElementById('online-bank');
    const bankSelection = document.getElementById('bank-selection');
    const showOtherAddressesButton = document.getElementById('showOtherAddresses');
    const otherAddressesDiv = document.getElementById('otherAddresses');
    const placeOrderButton = document.getElementById('place-order-btn');
    const proceedButton = document.getElementById('proceed-btn');
    const paymentSubmitForm = document.getElementById('PaymentForm');





    proceedButton.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default form submission
        let isValid = true;

    
        // Remove existing error messages
        removeExistingError(cardholderNameInput);
        removeExistingError(cardNumberInput);
        removeExistingError(expiryInput);
        removeExistingError(cvvInput);


        // Validate Cardholder's name
        if (cardholderNameInput.value.trim() === '') {
            createErrorMessage(cardholderNameInput, 'Cardholder\'s name is required.', 'cardholder-error');
            isValid = false;
        }

        // Validate Card Number
        const cardNumber = cardNumberInput.value.replace(/\D/g, '');
        if (cardNumber.length !== 16) {
            createErrorMessage(cardNumberInput, 'Card number must be 16 digits.', 'card-error');
            isValid = false;
        }

         // Validate Expiry Date
         const expiryValue = expiryInput.value.replace(/\D/g, '');
         if (expiryValue.length !== 4) {
             createErrorMessage(expiryInput, 'Expiry date must be in MM/YY format.', 'expiry-error');
             isValid = false;
         }
 
         // Validate CVV
         const cvvValue = cvvInput.value.replace(/\D/g, '');
         if (cvvValue.length !== 3) {
             createErrorMessage(cvvInput, 'CVV must be 3 digits.', 'cvv-error');
             isValid = false;
         }
 
         if (isValid) {
             // Proceed with form submission or further processing
             paymentSubmitForm.submit();
         }
    });







    
// Place Order button functionality
    placeOrderButton.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default anchor behavior
        const cimbBankSelected = document.getElementById('cimb-bank').checked;

        if (cimbBankSelected) {
            // Redirect to cimb_login.php
            window.location.href = 'cimb_login.php';
        } else {
            // Show alert if CIMB Bank is not selected
            alert('Please select CIMB Bank before placing the order.');
        }
    });

    showOtherAddressesButton.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent event bubbling
        console.log("Show Other Addresses element clicked");
        const isVisible = window.getComputedStyle(otherAddressesDiv).display !== "none";
        if (!isVisible) {
            otherAddressesDiv.style.display = "block";
            this.textContent = "Hide Other Addresses";
            console.log("Other addresses shown");
        } else {
            otherAddressesDiv.style.display = "none";
            this.textContent = "Show Other Addresses";
            console.log("Other addresses hidden");
        }
    });

    const shippingAddresses = document.querySelectorAll('.shipping-address');
    shippingAddresses.forEach(address =>{
        address.addEventListener('click', function(){
            shippingAddresses.forEach(addr => addr.classList.remove('selected'));

            this.classList.add('selected');
        });
    });


    cardholderNameInput.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        // Allow letters, spaces, and common punctuation
        if (!/^[a-zA-Z\s.,'-]*$/.test(char)) {
            e.preventDefault();
        }
    });


    cvvInput.addEventListener('input', function(){
        let value = this.value.replace(/\D/g,'');

        value = value.slice(0,3);

        this.value = value;

        removeExistingError(this);

        // Validate CVV
        if (value.length > 0 && value.length < 3) {
            createErrorMessage(this, 'CVV must be 3 digits.', 'cvv-error');
        }
        
    });

    // Prevent non-numeric input for CVV
    cvvInput.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/^\d$/.test(char)) {
            e.preventDefault();
        }
    });

    function removeExistingError(element){
        const existingError = element.nextElementSibling;
        if(existingError && (existingError.classList.contains('card-error') || 
        existingError.classList.contains('expiry-error') || 
        existingError.classList.contains('cvv-error')
        )){
            existingError.remove();
        }
    }

    function createErrorMessage(element, message, errorClass){
        //Remove any existing error first
        removeExistingError(element);

        const errorMsg = document.createElement('div');
        errorMsg.classList.add(errorClass, 'text-danger');
        errorMsg.textContent = message;
        element.after(errorMsg);
    }

    cardNumberInput.addEventListener('input', function(e) {
        // Remove non-numeric characters
        let value = this.value.replace(/\D/g, '');

        // Limit to 16 digits
        value = value.slice(0, 16);

        // Format with spaces every 4 digits
        let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');

        // Update input value
        this.value = formattedValue;

        removeExistingError(this);


        // Detect card type and highlight logos
        if (value.startsWith('4')) {
            // Visa
            visaLogo.style.display = 'inline-block';
            mastercardLogo.style.display = 'none';
        } else if (value.startsWith('5')) {
            // Mastercard
            mastercardLogo.style.display = 'inline-block';
            visaLogo.style.display = 'none';
        } else {
            // Reset logo opacity
            visaLogo.style.display = 'inline-block';
            mastercardLogo.style.display = 'inline-block';

            const errorMsg = document.createElement('div');
            errorMsg.classList.add('card-error', 'text-danger');
            errorMsg.textContent = 'Invalid card number. Please use Visa or Mastercard.';
            this.after(errorMsg);

        }
    });

    // Prevent non-numeric input
    cardNumberInput.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/^\d$/.test(char)) {
            e.preventDefault();
        }
    });

    //Expiry date formatting and validation
    expiryInput.addEventListener('input', function(e){
        let value = this.value.replace(/\D/g,'');

        //Limit to 4 digits
        value = value.slice(0,4);

        //Format as MM/YY
        let formattedValue = '';
        if(value.length > 2){
            formattedValue = value.slice(0,2) + '/' + value.slice(2);   
        }else{
            formattedValue = value;
        }

        this.value = formattedValue;

        //Remove any previous error message
        removeExistingError(this);

        if(value.length === 4){
            const month = parseInt(value.slice(0,2), 10);
            const year = parseInt(value.slice(2), 10);
            const currentYear = new Date().getFullYear() % 100;
            const currentMonth = new Date().getMonth() + 1;

            if(month < 1 || month > 12){
                createErrorMessage(this, 'Invalid month. Please enter a month between 01 and 12.', 'expiry-error');
            }else if(year < currentYear || (year === currentYear && month < currentMonth)){
                createErrorMessage(this, 'Card has expired. Please use a valid card.', 'expiry-error');
            }
        }
    
    });

    expiryInput.addEventListener('keypress', function(e){
        const char = String.fromCharCode(e.which);
        if(!/^\d$/.test(char)){
            e.preventDefault();
        }
    });



    
    // Initialize payment method display
    const paymentMethods = document.querySelectorAll('.payment-methods div');
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            if (this.classList.contains('onlineBankMethod')) {
                onlineBankInput.checked = true;
                bankSelection.style.display = 'block';
                paymentForm.style.display = 'none';
            } else if (this.classList.contains('debitCardMethod')) {
                debitCardInput.checked = true;
                paymentForm.style.display = 'block';
                bankSelection.style.display = 'none';
            }
        });
    });

    

});
