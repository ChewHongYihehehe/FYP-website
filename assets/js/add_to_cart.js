document.addEventListener('DOMContentLoaded', function() {
    // Function to update cart count
    function updateCartCount(count) {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
            console.log(`Cart count updated to: ${count}`); // Debugging line
        } else {
            console.error('Cart count element not found'); // Debugging line
        }
    }

    // Function to update total price
    function updateTotalPrice(total) {
        const totalPriceElement = document.getElementById('total-amount');
        if (totalPriceElement) {
            totalPriceElement.innerHTML = `<strong>RM${total.toFixed(2)}</strong>`;
            console.log(`Total price updated to: RM${total.toFixed(2)}`); // Debugging line
        } else {
            console.error('Total price element not found'); // Debugging line
        }
    }


    // Function to update subtotal
    function updateSubtotal(subtotal) {
        const subtotalElement = document.getElementById('subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = `RM${subtotal.toFixed(2)}`;
            console.log(`Subtotal updated to: RM${subtotal.toFixed(2)}`);
        } else {
            console.error('Subtotal element not found');
        }
    }


    // Remove item from cart
    const removeButtons = document.querySelectorAll('.remove-item');

    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            const cartItemRow = this.closest('.row');

            // Confirm before removing
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }

            // Send AJAX request to remove item
            fetch('add_to_cart_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cart_id=${cartId}`
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Log the response for debugging
                if (data.success) {
                    // Remove the item row from the DOM
                    cartItemRow.remove();

                    // Update cart count and total price based on server response
                    updateCartCount(data.cart_count); // Update cart count from server response
                    updateTotalPrice(data.total_price); // Update total price from server response

                    // Update cart summary
                    updateCartSummary();
                } else {
                    // Show error message
                    alert(data.message);
                }
            })
        });
    });

    // Function to update cart summary
    function updateCartSummary() {
        // Get all remaining cart items
        const cartItems = document.querySelectorAll('.row[data-cart-id]');
        
        // Recalculate total
        let totalPrice = 0;
        cartItems.forEach(item => {
            const quantityInput = item.querySelector('.quantity-input');
            const itemPrice = parseFloat(quantityInput.getAttribute('data-price'));
            const quantity = parseInt(quantityInput.value);
            const itemTotal = itemPrice * quantity;
            totalPrice += itemTotal;
        });

        // Update total in summary
        const subtotalElement = document.getElementById('subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = `RM${totalPrice.toFixed(2)}`;
            console.log(`Subtotal updated to: RM${totalPrice.toFixed( 2)}`); // Debugging line
        }

        // Update cart count in header if needed
        updateCartCount(cartItems.length);
    }
});