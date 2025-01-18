document.addEventListener('DOMContentLoaded', function() {
    // Add Product Modal
    const addProductBtn = document.getElementById('addProductBtn');
    const addProductModal = document.getElementById('addProductModal');
    const closeAddModal = document.getElementById('closeAddModal');

    addProductBtn.addEventListener('click', function() {
        addProductModal.style.display = 'block';
    });

    closeAddModal.addEventListener('click', function() {
        addProductModal.style.display = 'none';
    });

    // Edit Product Modal
    const editProductModal = document.getElementById('editProductModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const editProductButtons = document.querySelectorAll('.edit-product-btn');
    const editProductIdInput = document.getElementById('editProductId');
    const editProductNameInput = document.getElementById('editProductName');
    const editCategoryIdInput = document.getElementById('editCategoryName');
    const editBrandIdInput = document.getElementById('editBrandName');
    const editColorIdInput = document.getElementById('editColorName');
    const editProductPriceInput = document.getElementById('editProductPrice');

    // Current images for thumbnails
    const currentThumb1 = document.getElementById('currentThumb1');
    const currentThumb2 = document.getElementById('currentThumb2');
    const currentThumb3 = document.getElementById('currentThumb3');
    const currentThumb4 = document.getElementById('currentThumb4');

    // Current images for showcases
    const currentShowcase1 = document.getElementById('currentShowcase1');
    const currentShowcase2 = document.getElementById('currentShowcase2');
    const currentShowcase3 = document.getElementById('currentShowcase3');
    const currentShowcase4 = document.getElementById('currentShowcase4');

    editProductButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = button.getAttribute('data-id');
            const productName = button.getAttribute('data-name');
            const category = button.getAttribute('data-category');
            const brand = button.getAttribute('data-brand');
            const color = button.getAttribute('data-color');
            const price = button.getAttribute('data-price');

            // Set the values in the edit modal
            editProductIdInput.value = productId;
            editProductNameInput.value = productName;
            editCategoryIdInput.value = category; // Set the category
            editBrandIdInput.value = brand; // Set the brand
            editColorIdInput.value = color; // Set the color
            editProductPriceInput.value = price;

            // Set the current thumbnail images
            currentThumb1.src = button.getAttribute('data-thumb1') || '';
            currentThumb2.src = button.getAttribute('data-thumb2') || '';
            currentThumb3.src = button.getAttribute('data-thumb3') || '';
            currentThumb4.src = button.getAttribute('data-thumb4') || '';

            // Set the current showcase images
            currentShowcase1.src = button.getAttribute('data-showcase1') || '';
            currentShowcase2.src = button.getAttribute('data-showcase2') || '';
            currentShowcase3.src = button.getAttribute('data-showcase3') || '';
            currentShowcase4.src = button.getAttribute('data-showcase4') || '';

            editProductModal.style.display = 'block'; // Show the modal
        });
    });

    closeEditModal.addEventListener('click', function() {
        editProductModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === addProductModal) {
            addProductModal.style.display = 'none';
        }
        if (event.target === editProductModal) {
            editProductModal.style.display = 'none';
        }
    });
});