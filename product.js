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
    const thumbs = product.thumbnails || [];

    title.innerText = product.name;
    categoryText.innerText = product.category || "Category not available";
    desc.innerText = product.description || "Description not available";
    price.innerText = "$" + product.price;
    shoeNum.innerText = "0" + (shoeIndex + 1);
    shoeTotal.innerText = "0" + products.length;
    resetStars(product.rating);

    // Set the main image
    img.src = product.images[0] || 'default-image.png';

    // Set initial color effect based on the first color
    applyColorEffect(product.color);

    // Set thumbnails
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

    // Assign color buttons if available
    const colorContainer = document.querySelector(".colors ul");
    colorContainer.innerHTML = "";

    // Create color buttons based on available colors
    for (let i = 0; i < productGroup.length; i++) {
        const colorBtn = document.createElement("li");
        colorBtn.classList.add("color");
        colorBtn.style.background = productGroup[i].color;
        colorBtn.setAttribute("data-index", i);

        colorContainer.appendChild(colorBtn);

        // Add click event listener for each color button
        colorBtn.addEventListener("click", () => {
            colorType = i + 1;
            const selectedColor = productGroup[i].color;

            img.src = productGroup[i].images[0] || 'default-image.png';

            // Apply color effect when color button is clicked
            applyColorEffect(selectedColor);

            resetActive(colorContainer.children, "color", i);
            animate(img, 550, "jump 500ms ease-in-out");
            animate(shadow, 550, "shadow 500ms ease-in-out");
            animate(titleOverlay, 850, "title 800ms ease");

            // Update thumbnails for the selected color
            const selectedColorThumbnails = productGroup[i].thumbnails || [];
            for (let j = 0; j < thumb.length; j++) {
                if (selectedColorThumbnails[j]) {
                    thumb[j].src = selectedColorThumbnails[j];
                    thumb[j].style.display = 'block';
                } else {
                    thumb[j].style.display = 'none';
                }
            }
            resetActive(thumb, "thumb", 0);
        });
    }
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