 document.addEventListener('DOMContentLoaded', function() {
            // Add Category Modal
            const addCategoryBtn = document.getElementById('addCategoryBtn');
            const addCategoryModal = document.getElementById('addCategoryModal');
            const closeAddModal = document.getElementById('closeAddModal');

            addCategoryBtn.addEventListener('click', function() {
                addCategoryModal.style.display = 'block';
            });

            closeAddModal.addEventListener('click', function() {
                addCategoryModal.style.display = 'none';
            });

            // Edit Category Modal
            const editCategoryModal = document.getElementById('editCategoryModal');
            const closeEditModal = document.getElementById('closeEditModal');
            const editCategoryButtons = document.querySelectorAll('.edit-category-btn');
            const editCategoryIdInput = document.getElementById('editCategoryId');
            const editCategoryNameInput = document.getElementById('editCategoryName'); // Ensure this is defined

            editCategoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = button.getAttribute('data-id');
                    const categoryName = button.getAttribute('data-name');
                    const categoryImage = button.getAttribute('data-image');

                    // Set the values in the edit modal
                    editCategoryIdInput.value = categoryId;
                    editCategoryNameInput.value = categoryName; // Set the category name as the input value

                    // Set the current image
                    const currentCategoryImage = document.getElementById('currentCategoryImage');
                    const noImageText = document.getElementById('noImageText');

                    if (categoryImage) {
                        currentCategoryImage.src = 'assets/image/' + categoryImage;
                        currentCategoryImage.style.display = 'block';
                        noImageText.style.display = 'none';
                    } else {
                        currentCategoryImage.style.display = 'none';
                        noImageText.style.display = 'block';
                    }

                    editCategoryModal.style.display = 'block'; // Show the modal
                });
            });
            closeEditModal.addEventListener('click', function() {
                editCategoryModal.style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target === addCategoryModal) {
                    addCategoryModal.style.display = 'none';
                }
                if (event.target === editCategoryModal) {
                    editCategoryModal.style.display = 'none';
                }
            });
        });
