const imgContainer = document.querySelector(".showcase > div");
const img = document.querySelector(".showcase img");
const shadow = document.querySelector(".shadow");

const thumb = document.querySelectorAll(".thumbs img");
const categoryText = document.querySelector(".categoryText");
const titleOverlay = document.querySelector(".titleOverlay");
const title = document.querySelector(".titleText");
const desc = document.querySelector(".description");

const sizes = document.querySelectorAll(".sizes > li");
const price = document.querySelector(".price");
const colorBtn = document.querySelectorAll(".color");

const pag = document.querySelectorAll(".pag");
const prev = document.querySelector(".arr-left");
const next = document.querySelector(".arr-right");
const shoeNum = document.querySelector(".shoe-num");
const shoeTotal = document.querySelector(".shoe-total");

let currentProductIndex = 0;
let currentColorIndex = 0;
let currentSizeIndex = 0;

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
        groups.forEach(group => {
            group.forEach(product => {
                colors.add(product.color);
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

function loadProductData(productIndex) {
    const productGroups = groupedProductsByName[productIndex];
    
    // Find the specific product variant based on color and size
    let selectedProduct = null;
    
    // First, try to find a product with the current color and size
    for (let group of productGroups) {
        selectedProduct = group.find(p => 
            p.color === uniqueColors[currentColorIndex] && 
            p.size == sizes[currentSizeIndex].textContent
        );
        
        if (selectedProduct) break;
    }
    
    // If not found, default to the first product in the group
    if (!selectedProduct) {
        selectedProduct = productGroups[0][0];
    }

    // Update product details
    title.innerText = selectedProduct.name;
    categoryText.innerText = selectedProduct.category || "Category not available";
    desc.innerText = "Lorem ipsum dolor sit amet";
    price.innerText = "$" + Number(selectedProduct.price).toFixed(2);
    
    // Update shoe number and total
    shoeNum.innerText = "0" + (productIndex + 1).toString();
    shoeTotal.innerText = "0" + groupedProductsByName.length.toString();

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
            loadProductData(productIndex);
            
            // Animate changes
            animate(img, 550, "jump 500ms ease-in-out");
            animate(shadow, 550, "shadow 500ms ease-in-out");
            animate(titleOverlay, 850, "title 800ms ease");
        });

        colorContainer.appendChild(colorButton);
    });

    // Update sizes
    const sizesContainer = document.querySelector(".sizes");
    sizesContainer.innerHTML = "";

    // Collect unique sizes for this product
    const uniqueSizes = [...new Set(productGroups.flat().map(p => p.size))];
    uniqueSizes.sort((a, b) => a - b);

    uniqueSizes.forEach((size, index) => {
        const sizeElement = document.createElement("li");
        sizeElement.innerText = size;
        
        if (index === currentSizeIndex) {
            sizeElement.classList.add('size-active');
        }

        sizeElement.addEventListener('click', () => {
            currentSizeIndex = index;
            loadProductData(productIndex);
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
}

function slider(direction) {
    // Move to next/previous product group
    currentProductIndex = (currentProductIndex + direction + groupedProductsByName.length) % groupedProductsByName.length;
    
    // Reset color and size indices
    currentColorIndex = 0;
    currentSizeIndex = 0;
    
    loadProductData(currentProductIndex);

    animate(img, 1550, "replace 1.5s ease-in");
    animate(shadow, 1550, "shadow2 1.5s ease-in");
    animate(titleOverlay, 850, "title 800ms ease");
}


// Navigation buttons
prev.addEventListener("click", () => slider(-1));
next.addEventListener("click", () => slider(1));

// Pagination
pag.forEach((pagination, index) => {
    pagination.addEventListener("click", () => {
        currentProductIndex = index;
        currentColorIndex = 0;
        currentSizeIndex = 0;
        loadProductData(currentProductIndex); animate(img, 1550, "replace 1.5s ease-in");
        animate(shadow, 1550, "shadow2 1.5s ease-in");
        animate(titleOverlay, 850, "title 800ms ease");
    });
});

// Initial load
loadProductData(currentProductIndex);