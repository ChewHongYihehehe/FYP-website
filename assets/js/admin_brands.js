document.addEventListener('DOMContentLoaded', function() {
    // Add Brand Modal
    const addBrandBtn = document.getElementById('addBrandBtn');
    const addBrandModal = document.getElementById('addBrandModal');
    const closeAddBrandModal = document.getElementById('closeAddBrandModal');

    addBrandBtn.addEventListener('click', function() {
        addBrandModal.style.display = 'block';
    });

    closeAddBrandModal.addEventListener('click', function() {
        addBrandModal.style.display = 'none';
    });

    // Edit Brand Modal
    const editBrandModal = document.getElementById('editBrandModal');
    const closeEditBrandModal = document.getElementById('closeEditBrandModal');
    const editBrandButtons = document.querySelectorAll('.edit-brand-btn');
    const editBrandIdInput = document.getElementById('editBrandId');
    const editBrandNameInput = document.getElementById('editBrandName');

    editBrandButtons.forEach(button => {
        button.addEventListener('click', function() {
            const brandId = button.getAttribute('data-id');
            const brandName = button.getAttribute('data-name');

            // Set the values in the edit modal
            editBrandIdInput.value = brandId;
            editBrandNameInput.value = brandName;

            editBrandModal.style.display = 'block'; // Show the modal
        });
    });

    closeEditBrandModal.addEventListener('click', function() {
        editBrandModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === addBrandModal) {
            addBrandModal.style.display = 'none';
        }
        if (event.target === editBrandModal) {
            editBrandModal.style.display = 'none';
        }
    });
});