document.addEventListener('DOMContentLoaded', function() {
    // Add Size Modal
    const addSizeModal = document.getElementById('addSizeModal');
    const closeAddSizeModal = document.getElementById('closeAddSizeModal');

    document.querySelectorAll('.add-size-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const color = button.getAttribute('data-color');

            // Set the values in the add modal
            document.getElementById('addProductId').value = productId;
            document.getElementById('addProductName').textContent = productName;
            document.getElementById('addColor').value = color;
            document.getElementById('addColorDisplay').textContent = color;

            addSizeModal.style.display = 'block';
        });
    });

    closeAddSizeModal.addEventListener('click', function() {
        addSizeModal.style.display = 'none';
    });

    const editSizeButtons = document.querySelectorAll('.edit-size-btn');
    const editSizeModal = document.getElementById('editSizeModal');
    const closeEditSizeModal = document.getElementById('closeEditSizeModal');
    
    editSizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const sizeId = button.getAttribute('data-id'); // This should be the unique variant_id
            const size = button.getAttribute('data-size');
            const stock = button.getAttribute('data-stock');
    
            // Set the values in the edit modal
            document.getElementById('editSizeId').value = sizeId; // Set the unique size ID
            document.getElementById('editSize').value = size; // Set the size
            document.getElementById('editStock').value = stock; // Set the stock
    
            editSizeModal.style.display = 'block'; // Show the modal
        });
    });

    closeEditSizeModal.addEventListener('click', function() {
        editSizeModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === editSizeModal) {
            editSizeModal.style.display = 'none';
        }
    });
});