document.addEventListener('DOMContentLoaded', function() {
    // Edit User Modal
    const editUser_Modal = document.getElementById('editUser_Modal');
    const closeEditModal = document.getElementById('closeEditModal');
    const editUserButtons = document.querySelectorAll('.edit-user-btn');
    const editUserIdInput = document.getElementById('editUserId');
    const editUserFullnameInput = document.getElementById('editUser_Fullname');
    const editUserEmailInput = document.getElementById('editUser_Email');
    const editUserPhoneInput = document.getElementById('editUser_Phone');

    // Show Edit User Modal
    editUserButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userFullname = this.getAttribute('data-fullname');
            const userEmail = this.getAttribute('data-email');
            const userPhone = this.getAttribute('data-phone');

            // Set the values in the edit modal
            editUserIdInput.value = userId;
            editUserFullnameInput.value = userFullname;
            editUserEmailInput.value = userEmail;
            editUserPhoneInput.value = userPhone;

            editUser_Modal.style.display = 'block'; // Show the modal
        });
    });

    // Close Edit User Modal
    closeEditModal.addEventListener('click', function() {
        editUser_Modal.style.display = 'none';
    });

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === editUser_Modal) {
            editUser_Modal.style.display = 'none';
        }
    });
});