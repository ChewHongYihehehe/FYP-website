const imgContainer = document.querySelector(".showcase > div");
const img = document.querySelector(".showcase img");
const shadow = document.querySelector(".shadow");

const thumb = document.querySelectorAll(".thumbs img");
const categoryText = document.querySelector(".categoryText");
const titleOverlay = document.querySelector(".titleOverlay");
const title = document.querySelector(".titleText");

const sizes = document.querySelectorAll(".sizes > li");
const price = document.querySelector(".price");
const quantityValue = document.getElementById('quantity-value');

let quantity = 1;
let currentProductIndex = 0;
let currentColorIndex = 0;
let currentSizeIndex = 0;



document.getElementById('increase-quantity').addEventListener('click', function() {
    quantity++;
    quantityValue.textContent = quantity;
});

document.getElementById('decrease-quantity').addEventListener('click', function() {
    if (quantity > 1) {
        quantity--;
        quantityValue.textContent = quantity;
    }
});



// Group products by name
function groupProductsByName(products) {
    const groupedProducts = {};
    products.forEach(productGroup => {
        const name = productGroup[0].name;
        if (!groupedProducts[name]) {
            groupedProducts[name] = [];
        }
        groupedProducts[name].push(productGroup);
    });
    return Object.values(groupedProducts);
}

// Find unique colors across all product groups with the same name
function findUniqueColors(productGroups) {
    const colors = new Set();
    productGroups.forEach(groups => {
        if (!Array.isArray(groups)) return; // Ensure valid structure
        groups.forEach(productList => {
            if (!Array.isArray(productList)) return;
            productList.forEach(product => {
                if (product.color) colors.add(product.color);
            });
        });
    });
    return Array.from(colors);
}

// Prepare grouped products
const groupedProductsByName = groupProductsByName(products);
const uniqueColors = findUniqueColors(groupedProductsByName);

function resetActive(element, elementClass, i) {
    for (let j = 0; j < element.length; j++) {
        element[j].classList.remove(elementClass + "-active");
    }
    element[i].classList.add(elementClass + "-active");
}

function animate(element, time, anim) {
    element.style.animation = anim;
    setTimeout(() => {
        element.style.animation = "none";
    }, time);
}

function loadProductData(productIndex, colorIndex = currentColorIndex) {
    const productGroups = groupedProductsByName[productIndex];
    const selectedColor = uniqueColors[colorIndex];
    const colorVariants = productGroups.flat().filter(p => p.color === selectedColor);

    // Find the specific product variant based on color and size
    let selectedProduct = null;

    if (colorVariants.length === 0) {
        selectedProduct = productGroups[0][0]; 
    } else {
        selectedProduct = colorVariants.find(p => p.size === sizes[currentSizeIndex].textContent);
        if (!selectedProduct) {
            selectedProduct = colorVariants[0]; 
        }
    }

    // Update product details
    title.innerText = selectedProduct.name;
    categoryText.innerText = selectedProduct.category || "Category not available";
    price.innerText = "RM" + Number(selectedProduct.price).toFixed(2);

    // Set the main image
    img.src = selectedProduct.image1_display || 'default-image.png';
    img.setAttribute('data-default-image', selectedProduct.image1_display || 'default-image.png');

    // Set thumbnails
    const thumbs = [
        selectedProduct.image1_thumb,
        selectedProduct.image2_thumb,
        selectedProduct.image3_thumb,
        selectedProduct.image4_thumb
    ];

    thumb.forEach((thumbnailImg, index) => {
        if (thumbs[index]) {
            thumbnailImg.src = thumbs[index];
            thumbnailImg.style.display = 'block';
        } else {
            thumbnailImg.style.display = 'none';
        }
    });

    // Reset active thumbnail to the first one
    resetActive(thumb, "thumb", 0);

    // Recreate color buttons
    const colorContainer = document.querySelector(".colors ul");
    colorContainer.innerHTML = "";

    uniqueColors.forEach((color, index) => {
        const colorButton = document.createElement("li");
        colorButton.classList.add("color");
        colorButton.style.backgroundColor = color;
        colorButton.setAttribute('data-color', color);
        
        if (index === currentColorIndex) {
            colorButton.classList.add('color-active');
        }

        colorButton.addEventListener("click", () => {
            currentColorIndex = index;
            loadProductData(productIndex, index);
            
            // Update the data-color attribute of the Add to Cart button
            const addToCartButton = document.getElementById('add-to-cart-button');
            if (addToCartButton) {
                addToCartButton.setAttribute('data-color', color);
                console.log(`Color set to: ${color}`); // Debugging log
            }
            
            // Check if the new color is favorited
            updateFavoriteIcon(uniqueColors[currentColorIndex]);

            // Animate changes
            animate(img, 550, "jump 500ms ease-in-out");
            animate(shadow, 550, "shadow 500ms ease-in-out");
            animate(titleOverlay, 850, "title 800ms ease");
        });

        colorContainer.appendChild(colorButton);
    });

    // Set the default color for the Add to Cart button if not already set
    const addToCartButton = document.getElementById('add-to-cart-button');
    if (addToCartButton && !addToCartButton.getAttribute('data-color')) {
        addToCartButton.setAttribute('data-color', uniqueColors[0]); // Set to the first color
    }

    // Update sizes
    const sizesContainer = document.querySelector(".sizes");
    sizesContainer.innerHTML = "";

    // Collect unique sizes for this product
    const uniqueSizes = [...new Set(colorVariants.flat().map(p => p.size))];
    uniqueSizes.sort((a, b) => a - b);

    uniqueSizes.forEach((size, index) => {
        const sizeElement = document.createElement("li");
        sizeElement.innerText = size;
        sizeElement.classList.toggle('size-active', index === currentSizeIndex);
        sizeElement.addEventListener('click', () => {
            resetActive(sizes, "size", index);
            currentSizeIndex = index; // Update the current size index
            loadProductData(productIndex, colorIndex); // Reload product data with the new size
        });
        sizesContainer.appendChild(sizeElement);
    });

    // Update thumbnail click events to use the current color variant
    thumb.forEach((thumbnailImg, index) => {
        thumbnailImg.addEventListener("click", () => {
            const displayImages = [
                selectedProduct.image1_display,
                selectedProduct.image2_display,
                selectedProduct.image3_display,
                selectedProduct.image4_display
            ];

            // Update main image
            img.src = displayImages[index] || 'default-image.png';

            // Update active thumbnail
            resetActive(thumb, "thumb", index);
            animate(imgContainer, 550, "fade 500ms ease-in-out");
        });
    });

    updateFavoriteIcon(uniqueColors[currentColorIndex]);
}

// Add to Cart functionality
const addToCartButton = document.getElementById('add-to-cart-button');

if (addToCartButton) {
    addToCartButton.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        const size = document.querySelector('.sizes .size-active').textContent; // Get selected size
        const color = this.getAttribute('data-color') || uniqueColors[0]; // Get the updated color or default to the first color

        console.log(`Adding to cart: Product ID: ${productId}, Size: ${size}, Color: ${color}, Quantity: ${quantity}`); // Debugging log

        // Call the function to add to cart
        addToCart(productId, size, color, quantity);
    });
}
// Function to add product to cart
function addToCart(productId, size, color, quantity) {
    fetch('add_to_cart_process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
       body: `product_id=${productId}&size=${size}&color=${encodeURIComponent(color)}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement && data.cart_count !== undefined) {
                cartCountElement.textContent = data.cart_count;
            }

            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Product added to cart successfully!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = `product.php?product_id=${productId}`;
            });

            setTimeout(() => {
                notification.remove();
            }, 2000);
        } else {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => console.error('Error:', error));
}






// Handle favorite icon clicks
const favoriteIcon = document.querySelector('.favorite-product i');
const productId = favoriteIcon.getAttribute('data-product-id');

// Function to check if the current color variant is favorited
function checkIfFavorited(color) {
    return localStorage.getItem(`favorite_${productId}_${color}`) ? true : false;
}

// Function to update the favorite icon based on the selected color
function updateFavoriteIcon(color, callback) {

    const isFavorited = checkIfFavorited(color);

    if (isFavorited) {
        favoriteIcon.classList.add('fas');
        favoriteIcon.classList.remove('far');
        favoriteIcon.style.color = '#fe4c50'; // Filled heart
    } else {
        favoriteIcon.classList.remove('fas');
        favoriteIcon.classList.add('far');
        favoriteIcon.style.color = '#b9b4c7'; // Empty heart
    }

    // AJAX Request to check if the product is in the wishlist
    fetch('check_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&color=${encodeURIComponent(color)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.is_favorited) {
            favoriteIcon.classList.add('fas');
            favoriteIcon.classList.remove('far');
            favoriteIcon.style.color = '#fe4c50'; // Filled heart
        } else {
            favoriteIcon.classList.remove('fas');
            favoriteIcon.classList.add('far');
            favoriteIcon.style.color = '#b9b4c7'; // Empty heart
        }
        
        // Call the callback if provided
        if (callback) callback();
    })
    .catch(error => {
        console.error('Error checking wishlist:', error);
    });
}

// Check local storage to see if this product is favorited for the default color
const defaultColor = document.querySelector('.colors .color-active').style.backgroundColor;
updateFavoriteIcon(defaultColor);

// AJAX Request to check if the product is in the wishlist
fetch('check_wishlist.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `product_id=${productId}&color=${encodeURIComponent(defaultColor)}`
})
.then(response => response.json())
.then(data => {
    if (data.success && data.is_favorited) {
        favoriteIcon.classList.add('fas');
        favoriteIcon.classList.remove('far');
        favoriteIcon.style.color = '#fe4c50'; // Filled heart
    }
})
.catch(error => {
    console.error('Error checking wishlist:', error);
});

// Handle color variant clicks
const colorCircles = document.querySelectorAll('.colors .color');
colorCircles.forEach(circle => {
    circle.addEventListener('click', function() {
        const selectedColor = this.style.backgroundColor;
        const colorIndex = uniqueColors.indexOf(selectedColor);
        if (colorIndex !== -1) {
            currentColorIndex = colorIndex;
            loadProductData(currentProductIndex, colorIndex);
        }
    });
});


favoriteIcon.addEventListener('click', function(e) {
    e.preventDefault();

    // Determine action based on current state
    const isCurrentlyFavorited = favoriteIcon.classList.contains('fas');
    const action = isCurrentlyFavorited ? 'remove' : 'add';

    // Get the currently selected color
    const activeColorCircle = document.querySelector('.colors .color.color-active');
    const color = activeColorCircle ? activeColorCircle.style.backgroundColor : 'Unknown';

    // Update the icon immediately
    if (action === 'add') {
        favoriteIcon.classList.remove('far');
        favoriteIcon.classList.add('fas');
        favoriteIcon.style.color = '#fe4c50'; // Change color to indicate it's favorited
        localStorage.setItem(`favorite_${productId}_${color}`, true); // Store in local storage
    } else {
        favoriteIcon.classList.remove('fas');
        favoriteIcon.classList.add('far');
        favoriteIcon.style.color = '#b9b4c7'; // Reset color to indicate it's not favorited
        localStorage.removeItem(`favorite_${productId}_${color}`); // Remove from local storage
    }

    // AJAX Request to Toggle Wishlist
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&color=${encodeURIComponent(color)}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error(data.message); // Handle error
        }
    })
    .catch(error => {
        console.error('Wishlist Toggle Error:', error);
    });
});



// Initial load
loadProductData(currentProductIndex);