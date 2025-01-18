document.addEventListener('DOMContentLoaded', function() {
    // Add Size Modal
    const addSizeBtn = document.getElementById('addSizeBtn');
    const addSizeModal = document.getElementById('addSizeModal');
    const closeAddSizeModal = document.getElementById('closeAddSizeModal');

    addSizeBtn.addEventListener('click', function() {
        addSizeModal.style.display = 'block';
    });

    closeAddSizeModal.addEventListener('click', function() {
        addSizeModal.style.display = 'none';
    });

    // Edit Size Modal
    const editSizeModal = document.getElementById('editSizeModal');
    const closeEditSizeModal = document.getElementById('closeEditSizeModal');
    const editSizeButtons = document.querySelectorAll('.edit-size-btn');
    const editSizeIdInput = document.getElementById('editSizeId');
    const editSizeNameInput = document.getElementById('editSizeName');

    editSizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const sizeId = button.getAttribute('data-id');
            const sizeName = button.getAttribute('data-name');

            // Set the values in the edit modal
            editSizeIdInput.value = sizeId;
            editSizeNameInput.value = sizeName;

            editSizeModal.style.display = 'block'; // Show the modal
        });
    });

    closeEditSizeModal.addEventListener('click', function() {
        editSizeModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === addSizeModal) {
            addSizeModal.style.display = 'none';
        }
        if (event.target === editSizeModal) {
            editSizeModal.style.display = 'none';
        }
    });
});