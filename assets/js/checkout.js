
document.addEventListener('DOMContentLoaded', function() {
    const cardNumberInput = document.getElementById('card-number');
    const visaLogo = document.querySelector('.icons img[src*="visa"]');
    const mastercardLogo = document.querySelector('.icons img[src*="mastercard"]');
    const expiryInput = document.querySelector('input[placeholder="MM/YY"]');
    const cvvInput = document.getElementById('cvv');

    //Create CVV tooltip
    function createCVVTooltip(){
        const tooltipContainer = document.createElement('div');
        tooltipContainer.classList.add('cvv-tooltip');
        tooltipContainer.innerHTML = `
            <div class="cvv-tooltip-content">
                <p>3-digit security code usually found on the back of your card</p>
                <img src=""`
    }

    function removeExistingError(element){
        const existingError = element.nextElementSibling;
        if(existingError && (existingError.classList.contains('card-error') || existingError.classList.contains('expiry-error'))){
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
});
