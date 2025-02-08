document.addEventListener('DOMContentLoaded', function() {
    
    // Function to update cart count
    function updateCartCount(count) {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
            console.log(`Cart count updated to: ${count}`);
        } else {
            console.error('Cart count element not found');
        }
    }

    // Function to update total price
    function updateTotalPrice(total) {
        const totalPriceElement = document.getElementById('total-amount');
        if (totalPriceElement) {
            totalPriceElement.innerHTML = `<strong>RM${parseFloat(total).toFixed(2)}</strong>`;
            console.log(`Total price updated to: RM${parseFloat(total).toFixed(2)}`);
        } else {
            console.error('Total price element not found');
        }
    }

    // Function to update subtotal
    function updateSubtotal(subtotal) {
        const subtotalElement = document.getElementById('subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = `RM${parseFloat(subtotal).toFixed(2)}`;
            console.log(`Subtotal updated to: RM${parseFloat(subtotal).toFixed(2)}`);
        } else {
            console.error('Subtotal element not found');
        }
    }

    // Function to recalculate total price
    function recalculateTotalPrice() {
        const cartItems = document.querySelectorAll('.row .quantity-input');
        let totalPrice = 0;

        cartItems.forEach(input => {
            const price = parseFloat(input.getAttribute('data-price'));
            const quantity = parseInt(input.value);
            totalPrice += price * quantity;
        });

        updateSubtotal(totalPrice);
        updateTotalPrice(totalPrice);
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

                    // Recalculate total price
                    recalculateTotalPrice();

                    // Update cart count based on server response
                    updateCartCount(data.cart_count);
                } else {
                    // Show error message
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the item');
            });
        });
    });

        // Function to show SweetAlert2 message
        function showAlert(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Alert',
                text: message,
                confirmButtonText: 'OK'
            });
        }
    

     // Function to adjust quantity
     function adjustQuantity(cartId, newQuantity, currentQuantity, productId, size, color) {
        fetch('add_to_cart_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart_id=${cartId}&quantity=${newQuantity}&current_quantity=${currentQuantity}&product_id=${productId}&size=${size}&color=${color}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the quantity input and total price
                const row = document.querySelector(`.row [data-cart-id="${cartId}"]`).closest('.row');
                const input = row.querySelector('.quantity-input');
                const itemTotalElement = row.querySelector('.item-total-price strong');

                if (input) {
                    input.value = newQuantity;
                }

                if (itemTotalElement) {
                    const price = parseFloat(input.getAttribute('data-price'));
                    const itemTotal = (price * newQuantity).toFixed(2);
                    itemTotalElement.textContent = `RM${itemTotal}`;
                }

                // Recalculate total price
                recalculateTotalPrice();
            } else {
                showAlert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating quantity');
        });
    }

    // Setup quantity buttons
    function setupQuantityButtons() {
        // Decrease quantity buttons
        const decreaseButtons = document.querySelectorAll('.decrease-quantity');
        decreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.row');
                const input = row.querySelector('.quantity-input');
                const cartId = input.getAttribute('data-cart-id');
                const currentValue = parseInt(input.value);
                const productId = input.getAttribute('data-product-id');
                const size = input.getAttribute('data-size');
                const color = input.getAttribute('data-color');
                const availableStock = parseInt(input.getAttribute('data-stock'));

                if (currentValue > 1) {
                    adjustQuantity(cartId, currentValue - 1, currentValue, productId, size, color);
                }
            });
        });

        // Increase quantity buttons
        const increaseButtons = document.querySelectorAll('.increase-quantity');
        increaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.row');
                const input = row.querySelector('.quantity-input');
                const cartId = input.getAttribute('data-cart-id');
                const currentValue = parseInt(input.value);
                const productId = input.getAttribute('data-product-id');
                const size = input.getAttribute('data-size');
                const color = input.getAttribute('data-color');
                const availableStock = parseInt(input.getAttribute('data-stock'));

                if (currentValue < availableStock) {
                    adjustQuantity(cartId, currentValue + 1, currentValue, productId, size, color);
                } else {
                    showAlert('Cannot increase quantity beyond available stock.');
                }
            });
        });
    }

    // Initial setup of quantity buttons
    setupQuantityButtons();
});