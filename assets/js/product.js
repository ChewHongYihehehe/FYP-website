const imgContainer = document.querySelector(".showcase > div");
const img = document.querySelector(".showcase img");
const shadow = document.querySelector(".shadow");

const thumb = document.querySelectorAll(".thumbs img");
const categoryText = document.querySelector(".categoryText"); // Adjusted to match HTML class
const titleOverlay = document.querySelector(".titleOverlay");
const title = document.querySelector(".titleText");
const desc = document.querySelector(".description");

const sizes = document.querySelectorAll(".sizes > li");
const stars = document.querySelectorAll(".stars span");
const price = document.querySelector(".price");
const colorBtn = document.querySelectorAll(".color");

const pag = document.querySelectorAll(".pag");
const prev = document.querySelector(".arr-left");
const next = document.querySelector(".arr-right");
const shoeNum = document.querySelector(".shoe-num");
const shoeTotal = document.querySelector(".shoe-total");

let id = 1;
let colorType = 1;
let shoe = 0; // Start from the first product group

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

function resetStars(rating) {
    for (let i = 0; i < stars.length; i++) {
        stars[i].innerText = "star_outline";
    }
    for (let i = 0; i < rating; i++) {
        stars[i].innerText = "star";
    }
}

function loadProductData(shoeIndex) {
    const productGroup = products[shoeIndex];
    const product = productGroup[0];  // Get the first product (default color option)

    // Update product details
    title.innerText = product.name;
    categoryText.innerText = product.category || "Category not available";
    desc.innerText = "Lorem ipsum dolor sit amet"; // Default description as in PHP
    price.innerText = "$" + Number(product.price).toFixed(2);
    
    // Update shoe number and total
    shoeNum.innerText = "0" + (shoeIndex + 1).toString();
    shoeTotal.innerText = "0" + products.length.toString();

    // Set the main image
    img.src = product.image1_display || 'default-image.png';
    img.setAttribute('data-default-image', product.image1_display || 'default-image.png');

    // Set thumbnails
    const thumbs = [
        product.image1_thumb,
        product.image2_thumb,
        product.image3_thumb,
        product.image4_thumb
    ];

    for (let i = 0; i < thumb.length; i++) {
        if (thumbs[i]) {
            thumb[i].src = thumbs[i];
            thumb[i].style.display = 'block';
        } else {
            thumb[i].style.display = 'none';
        }
    }

    // Reset active thumbnail to the first one
    resetActive(thumb, "thumb", 0);

    // Recreate color buttons based on unique colors in the product group
    const colorContainer = document.querySelector(".colors ul");
    colorContainer.innerHTML = "";

    // Collect unique colors
    const uniqueColors = [...new Set(productGroup.map(p => p.color))];

    uniqueColors.forEach((color, index) => {
        const colorButton = document.createElement("li");
        colorButton.classList.add("color");
        colorButton.style.backgroundColor = color;
        colorButton.setAttribute('data-color', color);
        
        if (index === 0) {
            colorButton.classList.add('color-active');
        }

        colorContainer.appendChild(colorButton);

        colorButton.addEventListener("click", (event) => {
            const selectedColor = event.currentTarget.getAttribute("data-color");
            
            // Find the product variant with this color
            const selectedVariant = productGroup.find(variant => variant.color === selectedColor);
            
            if (selectedVariant) {
                // Update images
                img.src = selectedVariant.image1_display || 'default-image.png';
                
                // Update thumbnails
                const thumbs = [
                    selectedVariant.image1_thumb,
                    selectedVariant.image2_thumb,
                    selectedVariant.image3_thumb,
                    selectedVariant.image4_thumb
                ];
        
                for (let i = 0; i < thumb.length; i++) {
                    if (thumbs[i]) {
                        thumb[i].src = thumbs[i];
                        thumb[i].style.display = 'block';
                    } else {
                        thumb[i].style.display = 'none';
                    }
                }
        
                // Reset active thumbnail to the first one
                resetActive(thumb, "thumb", 0);
        
                // Update color selection
                resetActive(colorContainer.children, "color", index);
        
                // Animate changes
                animate(img, 550, "jump 500ms ease-in-out");
                animate(shadow, 550, "shadow 500ms ease-in-out");
                animate(titleOverlay, 850, "title 800ms ease");
        
                // Update price
                price.innerText = "$" + Number(selectedVariant.price).toFixed(2);
            }
        });
    });

    // Update sizes
    const sizesContainer = document.querySelector(".sizes");
    sizesContainer.innerHTML = "";

    // Collect unique sizes
    const uniqueSizes = [...new Set(productGroup.map(p => p.size))];
    uniqueSizes.sort((a, b) => a - b);

    uniqueSizes.forEach((size, index) => {
        const sizeElement = document.createElement("li");
        sizeElement.innerText = size;
        
        if (index === 0) {
            sizeElement.classList.add('size-active');
        }

        sizesContainer.appendChild(sizeElement);
    });
}

// Function to apply color effect to shadow and title overlay
function applyColorEffect(color) {
    shadow.style.backgroundColor = darkenColor(color, 0.5);  // Slightly darkened for shadow effect
    titleOverlay.style.backgroundColor = color;
}

// Helper function to darken color
function darkenColor(color, amount) {
    let r = parseInt(color.slice(1, 3), 16);
    let g = parseInt(color.slice(3, 5), 16);
    let b = parseInt(color.slice(5, 7), 16);

    r = Math.floor(r * (1 - amount));
    g = Math.floor(g * (1 - amount));
    b = Math.floor(b * (1 - amount));

    return `rgba(${r}, ${g}, ${b}, 0.5)`;  // 0.5 opacity for the shadow effect
}


function slider(shoeIndex) {
shoe = shoeIndex;
loadProductData(shoeIndex);

animate(img, 1550, "replace 1.5s ease-in");
animate(shadow, 1550, "shadow2 1.5s ease-in");
animate(titleOverlay, 850, "title 800ms ease");
}

// Event listeners for thumbnails
for (let i = 0; i < thumb.length; i++) {
    thumb[i].addEventListener("click", () => {
        // Get the currently selected product and color
        const currentProductGroup = products[shoe]; // Get the current product group
        const currentColorIndex = colorType - 1; // Get the index of the selected color

        // Update the main image based on the thumbnail clicked
        img.src = currentProductGroup[currentColorIndex].images[i] || 'default-image.png';
        resetActive(thumb, "thumb", i); // Set the active thumbnail
        animate(imgContainer, 550, "fade 500ms ease-in-out"); // Animate the image change
    });
}
// Navigation button event listeners
prev.addEventListener("click", () => {
shoe--;
if (shoe < 0) {
    shoe = products.length - 1; // Loop back to the last product
}
slider(shoe);
});

next.addEventListener("click", () => {
shoe++;
if (shoe >= products.length) {
    shoe = 0; // Loop back to the first product
}
slider(shoe);
});

// Pagination event listeners
for (let i = 0; i < pag.length; i++) {
pag[i].addEventListener("click", () => {
    slider(i);
});
}

// Initial load
loadProductData(shoe);