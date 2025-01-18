document.addEventListener('DOMContentLoaded', function() {
    // Add Color Modal
    const addColorBtn = document.getElementById('addColorBtn');
    const addColorModal = document.getElementById('addColorModal');
    const closeAddColorModal = document.getElementById('closeAddColorModal');

    addColorBtn.addEventListener('click', function() {
        addColorModal.style.display = 'block';
    });

    closeAddColorModal.addEventListener('click', function() {
        addColorModal.style.display = 'none';
    });

    // Edit Color Modal
    const editColorModal = document.getElementById('editColorModal');
    const closeEditColorModal = document.getElementById('closeEditColorModal');
    const editColorButtons = document.querySelectorAll('.edit-color-btn');
    const editColorIdInput = document.getElementById('editColorId');
    const editColorNameInput = document.getElementById('editColorName');

    editColorButtons.forEach(button => {
        button.addEventListener('click', function() {
            const colorId = button.getAttribute('data-id');
            const colorName = button.getAttribute('data-name');

            // Set the values in the edit modal
            editColorIdInput.value = colorId;
            editColorNameInput.value = colorName;

            editColorModal.style.display = 'block'; // Show the modal
        });
    });

    closeEditColorModal.addEventListener('click', function() {
        editColorModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === addColorModal) {
            addColorModal.style.display = 'none';
        }
        if (event.target === editColorModal) {
            editColorModal.style.display = 'none';
        }
    });
});