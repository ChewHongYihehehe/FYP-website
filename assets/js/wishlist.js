document.addEventListener('DOMContentLoaded', function() {
    // Select all cancel icons
    const cancelIcons = document.querySelectorAll('.cancel-icon');

    // Add event listener to each cancel icon
    cancelIcons.forEach(function(icon) {
        icon.addEventListener('click', function(e) {
            e.preventDefault();

            // Get the product ID
            const productId = this.getAttribute('data-product-id');
            const productItem = this.closest('.product-item');

            // Confirm removal
            if (confirm('Are you sure you want to remove this item from your wishlist?')) {
                // Create FormData to send the request
                const formData = new FormData();
                formData.append('product_id', productId);

                // Send AJAX request using fetch
                fetch('remove_wishlist_item.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data === "success") {
                        // Use CSS transition for smooth removal
                        productItem.style.transition = 'opacity 0.5s';
                        productItem.style.opacity = '0';

                        // Remove from DOM after transition
                        setTimeout(() => {
                            productItem.remove();
                            
                            // Optional: Check if grid is empty
                            const productGrid = document.querySelector('.product-grid');
                            const remainingProducts = productGrid.querySelectorAll('.product-item');
                            
                            if (remainingProducts.length === 0) {
                                productGrid.innerHTML = '<p>Your wishlist is empty.</p>';
                            }
                            
                            // Reload page to refresh pagination
                            location.reload();
                        }, 500);
                    } else {
                        // Handle different error scenarios
                        switch(data) {
                            case "error_not_logged_in":
                                alert("You must be logged in to remove items from wishlist");
                                break;
                            case "error_no_product":
                                alert("No product specified");
                                break;
                            case "error_database":
                                alert("A database error occurred");
                                break;
                            default:
                                alert("Failed to remove product");
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the product');
                });
            }
        });
    });
});