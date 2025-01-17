document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.getElementById('editButton');
    const addButton = document.getElementById('addButton');
    const editModal = document.getElementById('editModal');
    const editAddressModal = document.getElementById('editAddressModal');
    const addModal = document.getElementById('addModal');
    const closeEditModal = document.getElementById('closeModal');
    const closeAddressModal = document.getElementById('closeAddressModal');
    const closeAddModal = document.getElementById('closeAddModal');
    const editAddressButtons = document.querySelectorAll('.edit-address-btn');   
    const deleteAddressButtons = document.querySelectorAll('.delete-address-btn');

    if (editButton && editModal && closeEditModal) {
        editButton.onclick = function() {
            editModal.style.display = "block";
            document.body.style.overflow = "hidden";
        }

        closeEditModal.onclick = function() {
            editModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }
    // Add button functionality
    if (addButton && addModal && closeAddModal) {
        addButton.onclick = function() {
            addModal.style.display = "block";
            document.body.style.overflow = "hidden";
        }

        closeAddModal.onclick = function() {
            addModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }

    if(closeAddressModal){
        closeAddressModal.onclick = function(){
            editAddressModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }



    window.onclick = function(event) {
        if (event.target == editModal) {
            editModal.style.display = "none";
            document.body.style.overflow = "auto"; // Allow background scrolling
        }
        if(event.target == addModal){
            addModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
        if(event.target == editAddressModal){
            editAddressModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }

    editAddressButtons.forEach(button => {
        button.addEventListener('click', function() {
            const addressId = this.getAttribute('data-id');
            const fullname = this.getAttribute('data-fullname');
            const addressLine = this.getAttribute('data-address-line');
            const city = this.getAttribute('data-city');
            const postcode = this.getAttribute('data-postcode');
            const state = this.getAttribute('data-state');
            const isDefault = this.getAttribute('data-is-default').trim() === '1';
    
            const editForm = document.getElementById('editForm');
    
            const addressIdInput = editForm.querySelector('input[name="address_id"]');
            if (addressIdInput) {
                addressIdInput.value = addressId;
            }
    
            const fullnameInput = editForm.querySelector('input[name="fullname"]');
            const addressLineInput = editForm.querySelector('input[name="address_line"]');
            const cityInput = editForm.querySelector('input[name="city"]');
            const postcodeInput = editForm.querySelector('input[name="postcode"]');
            const stateSelect = editForm.querySelector('select[name="state"]');
            const isDefaultCheckbox = editForm.querySelector('input[name="is_default"]');
    
            if (fullnameInput) fullnameInput.value = fullname;
            if (addressLineInput) addressLineInput.value = addressLine;
            if (cityInput) cityInput.value = city;
            if (postcodeInput) postcodeInput.value = postcode;
            if (stateSelect) {
                stateSelect.value = state; // Set the state value
            }
    
            // Set checkbox state based on is_default
            if (isDefaultCheckbox) {
                isDefaultCheckbox.checked = isDefault; // Set checkbox based on is_default
            }
    
            editAddressModal.style.display = "block";
            document.body.style.overflow = "hidden";
        });
    });

    deleteAddressButtons.forEach(button =>{
        button.addEventListener('click',function(event){
            event.preventDefault();
            const confirmDelete = confirm('Are you sure you want to delete this address?');
            if(confirmDelete){
                this.closest('form').submit();
            } 
        })
    })

    function validateInput(){
        const fullnameInputs = document.querySelectorAll('input[name="fullname"]');
        fullnameInputs.forEach(input => {
            input.addEventListener('input', function(e){
                //Allow only letters, spaces and hypens
                this.value = this.value.replace(/[^A-Za-z\s-]/g, '');
            });
        });

        const addressLineInputs = document.querySelectorAll('input[name="address_line"]');
        addressLineInputs.forEach(input =>{
            input.addEventListener('input', function(e){
                this.value = this.value.replace(/[^A-Za-z0-9\s,.-]/g,'');

            });
        });


        const cityInputs = document.querySelectorAll('input[name="city"]');
        cityInputs.forEach(input =>{
            input.addEventListener('input', function(e){
                this.value = this.value.replace(/[^A-Za-z\s]/g,'');
            });
        });
        
        const postcodeInput = document.querySelectorAll('input[name="postcode"]');
        postcodeInput.forEach(input =>{
            input.addEventListener('input', function(e){
                this.value = this.value.replace(/[A-Za-z\s]/g, '');
            });
        });

        const stateInputs = document.querySelectorAll('input[name="state"]');
        stateInputs.forEach(input =>{
            input.addEventListener('input', function(e){
                this.value = this.value.replace(/[^A-Za-z\s]/g, '');
            });
        });
        
        const phoneInputs = document.querySelectorAll('input[name="phone"]');
        phoneInputs.forEach(input => {
        // Set initial value to +60 if empty
        if (!input.value) {
            input.value = '+60 ';
        }

        input.addEventListener('input', function(e) {
            // Remove all non-numeric characters except +
            let value = this.value.replace(/[^0-9+]/g, '');
            
            // Ensure the input starts with +60
            if (!value.startsWith('+60')) {
                value = '+60' + value;
            }

            // Format the number
            let formatted = '+60 ';
            let numericPart = value.replace(/\D/g, '').slice(2); // Remove +60 and get only numbers
            
            // Add first group of 3 digits
            if (numericPart.length > 0) {
                formatted += numericPart.slice(0, 2);
            }
            
            // Add space after first 3 digits
            if (numericPart.length > 2) {
                formatted += '-' + numericPart.slice(2, 5);
            }
            
            // Add space and last 4 digits
            if (numericPart.length > 5) {
                formatted += ' ' + numericPart.slice(5, 9);
            }

            // Limit to full Malaysian phone number format
            this.value = formatted.slice(0, 17);

            // Prevent typing beyond the format
            if (this.value.length === 17) {
                e.preventDefault();
            }
        });


    });

}

const emailInput = document.querySelector('input[name="email"]');
if (emailInput) {
    emailInput.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Email address cannot be changed. Please contact support if you need to update your email.');
    });

    // Prevent any modifications
    emailInput.addEventListener('keydown', function(e) {
        e.preventDefault();
    });
}


validateInput();
    
});