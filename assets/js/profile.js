const tabBtn = document.querySelectorAll(".tab");
const tab = document.querySelectorAll(".tabShow");

function tabs(panelIndex) {
    tab.forEach(function(node) {
        node.style.display = "none"; // Hide all tabs
    });
    tab[panelIndex].style.display = "flex"; // Show the selected tab
}
tabs(0); // Initialize with the first tab visible

function toggleAddressForm() {
    const addressForm = document.getElementById("addressForm");
    const overlay = document.getElementById("overlay");
    const profileContainer = document.querySelector('.profile-container');

    if (!addressForm || !overlay || !profileContainer) {
        console.error('One or more elements not found');
        return;
    }

    // Store the original height when first opening the form
    if (!addressForm.dataset.originalHeight) {
        addressForm.dataset.originalHeight = profileContainer.style.height;
    }

    // Check if the form is currently active
    const isActive = addressForm.classList.contains("active");

    if (isActive) {
        // Slide out the form
        addressForm.classList.remove("active");
        overlay.classList.remove("active");

        // Completely remove form after animation
        setTimeout(() => {
            addressForm.style.display = "none";
            overlay.style.display = "none";
            
            // Restore the original height
            profileContainer.style.height = addressForm.dataset.originalHeight || 'calc(100% - 50%)';
        }, 400); // Match the CSS transition time
    } else {
        // Prepare to show the form
        addressForm.style.display = "block";
        overlay.style.display = "block";

        // Small delay to ensure display is set
        setTimeout(() => {
            addressForm.classList.add("active");
            overlay.classList.add("active");

            // Maintain the original height when opening
            profileContainer.style.height = `${profileContainer.offsetHeight}px`;
        }, 10);
    }
}

// Ensure cancel icon works
document.addEventListener('DOMContentLoaded', () => {
    const cancelIcon = document.querySelector('.cancel-icon');
    if (cancelIcon) {
        cancelIcon.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent event bubbling
            toggleAddressForm();
        });
    }
});
let addressCount = 0;

function saveAddress() {
    addressCount++;
    document.querySelector('.address-count').textContent = addressCount;
}

document.getElementById('updateBtn').addEventListener('click', function() {
    // Populate the update form with user information
    document.getElementById('updateFullName').value = document.getElementById('fullname').value;
    document.getElementById('updateEmail').value = document.getElementById('email').value;
    document.getElementById('updatePhone').value = document.getElementById('phone').value;

    toggleUpdateForm(); // Show the update form
});

function toggleUpdateForm() {
    const updateForm = document.getElementById('updateForm');
    const overlay = document.getElementById("overlay");

    if (updateForm.classList.contains("active")) {
        updateForm.classList.remove("active");
        setTimeout(() => {
            updateForm.style.display = "none";
        }, 800);
    } else {
        updateForm.style.display = "block";
        setTimeout(() => {
            updateForm.classList.add("active");
        }, 10);
    }

    overlay.classList.toggle("active");
}

function togglePasswordForm() {
    const passwordForm = document.getElementById('passwordUpdateForm');
    const overlay = document.getElementById("overlay");

    if(passwordForm.classList.contains("active")){
        passwordForm.classList.remove("active");
        setTimeout(() => {
            passwordForm.style.display = "none";
        }, 800);
    }else{
        passwordForm.style.display = "block";
        setTimeout(() => {
            passwordForm.classList.add("active");
        }, 10);
    }

    overlay.classList.toggle("active");
}

function editAddress(id, firstName, lastName, addressLine1, addressLine2, city, zipCode, state) {
    // Show the edit address form with overlay
    const editAddressForm = document.getElementById('editAddressForm');
    const overlay = document.getElementById('overlay');
    
    // Populate form fields
    document.getElementById('editFirstName').value = firstName;
    document.getElementById('editLastName').value = lastName;
    document.getElementById('editAddressLine1').value = addressLine1;
    document.getElementById('editAddressLine2').value = addressLine2 || ''; // Handle potential null value
    document.getElementById('editCity').value = city;
    document.getElementById('editZipCode').value = zipCode;
    document.getElementById('editState').value = state;
    
    // Set the address ID in the hidden input
    document.getElementById('editAddressId').value = id;
    
    // Display the form with animation
    editAddressForm.style.display = 'block';
    overlay.classList.add('active');
    
    // Add a small delay to trigger the animation
    setTimeout(() => {
        editAddressForm.classList.add('active');
    }, 10);
}

function toggleEditAddressForm() {
    const editAddressForm = document.getElementById('editAddressForm');
    const overlay = document.getElementById('overlay');
    
    // Remove active class to trigger close animation
    editAddressForm.classList.remove('active');
    overlay.classList.remove('active');
    
    // Hide the form after animation
    setTimeout(() => {
        editAddressForm.style.display = 'none';
    }, 800);
}


    function updateWishlistCount() {
        const wishlistGrid = document.querySelector('.wishlist-grid');
        const wishlistContainer = document.querySelector('.wishlist-container');
        
        if (wishlistGrid) {
            const wishlistCount = wishlistGrid.children.length;
            
            // If no items left, show empty message
            if (wishlistCount === 0) {
                wishlistContainer.innerHTML = '<p>Your wishlist is empty.</p>';
            }
        }
    }
