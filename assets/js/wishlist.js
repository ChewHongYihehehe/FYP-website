document.addEventListener('DOMContentLoaded', function() {
    // Function to setup quick add functionality
    function setupQuickAdd() {
        const quickAddButtons = document.querySelectorAll('.quick-add-button');

        quickAddButtons.forEach(button => {
            const productItem = button.closest('.product-item');
            const productId = productItem.getAttribute('data-product-id');
            const availableSizesJSON = productItem.getAttribute('data-available-sizes');
            const availableSizes = JSON.parse(availableSizesJSON || '[]');
            let sizesContainer = null;
            let isQuickAddActive = false;

            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event from bubbling

                // If container is already open, close it
                if (isQuickAddActive) {
                    closeSizesContainer();
                    return;
                }

                // Create sizes container
                sizesContainer = document.createElement('div');
                sizesContainer.classList.add('sizes-container');
                productItem.appendChild(sizesContainer);

                // Create size buttons
                availableSizes.forEach(size => {
                    const sizeButton = document.createElement('button');
                    sizeButton.textContent = size;
                    sizeButton.classList.add('size-button');

                    sizeButton.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent event from bubbling
                        
                        // Add to cart logic
                        addToCart(productId, size);
                        
                        // Remove sizes container
                        closeSizesContainer();
                    });

                    sizesContainer.appendChild(sizeButton);
                });

                // Mark the quick add as active
                isQuickAddActive = true;
            });

            // Function to close sizes container
            function closeSizesContainer() {
                if (sizesContainer) {
                    productItem.removeChild(sizesContainer);
                    sizesContainer = null;
                }
                isQuickAddActive = false;
            }
        });
    }

    // Function to add product to cart
    function addToCart(productId, size) {
        fetch('add _to_cart_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&size=${size}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement && data.cart_count !== undefined) {
                    cartCountElement.textContent = data.cart_count;
                    
                    // Optional: Add animation
                    cartCountElement.classList.add('cart-count-updated');
                    setTimeout(() => {
                        cartCountElement.classList.remove('cart-count-updated');
                    }, 300);
                }
                
                // Optional: Show notification
                const notification = document.createElement('div');
                notification.classList.add('cart-notification');
                notification.textContent = 'Added to cart!';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                },  2000);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Initial setup
    setupQuickAdd();
});