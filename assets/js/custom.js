/* JS Document */

/******************************

[Table of Contents]

1. Vars and Inits
2. Set Header
3. Init Menu
4. Init Timer
5. Init Favorite
6. Init Fix Product Border
7. Init Isotope Filtering
8. Init Slider


******************************/


jQuery(document).ready(function($)
{
	"use strict";

	/* 

	1. Vars and Inits

	*/

	var header = $('.header');
	var topNav = $('.top_nav')
	var mainSlider = $('.main_slider');
	var hamburger = $('.hamburger_container');
	var menu = $('.hamburger_menu');
	var menuActive = false;
	var hamburgerClose = $('.hamburger_close');
	var fsOverlay = $('.fs_menu_overlay');

	setHeader();

	$(window).on('resize', function()
	{
		initFixProductBorder();
		setHeader();
	});

	$(document).on('scroll', function()
	{
		setHeader();
	});

	initMenu();
	initTimer();
	initFavorite();
	initFixProductBorder();
	initIsotopeFiltering();
	initSlider();

	/* 

	2. Set Header

	*/

	function setHeader()
	{
		if(window.innerWidth < 992)
		{
			if($(window).scrollTop() > 100)
			{
				header.css({'top':"0"});
			}
			else
			{
				header.css({'top':"0"});
			}
		}
		else
		{
			if($(window).scrollTop() > 100)
			{
				header.css({'top':"-50px"});
			}
			else
			{
				header.css({'top':"0"});
			}
		}
		if(window.innerWidth > 991 && menuActive)
		{
			closeMenu();
		}
	}

	/* 

	3. Init Menu

	*/

	function initMenu()
	{
		if(hamburger.length)
		{
			hamburger.on('click', function()
			{
				if(!menuActive)
				{
					openMenu();
				}
			});
		}

		if(fsOverlay.length)
		{
			fsOverlay.on('click', function()
			{
				if(menuActive)
				{
					closeMenu();
				}
			});
		}

		if(hamburgerClose.length)
		{
			hamburgerClose.on('click', function()
			{
				if(menuActive)
				{
					closeMenu();
				}
			});
		}

		if($('.menu_item').length)
		{
			var items = document.getElementsByClassName('menu_item');
			var i;

			for(i = 0; i < items.length; i++)
			{
				if(items[i].classList.contains("has-children"))
				{
					items[i].onclick = function()
					{
						this.classList.toggle("active");
						var panel = this.children[1];
					    if(panel.style.maxHeight)
					    {
					    	panel.style.maxHeight = null;
					    }
					    else
					    {
					    	panel.style.maxHeight = panel.scrollHeight + "px";
					    }
					}
				}	
			}
		}
	}

	function openMenu()
	{
		menu.addClass('active');
		// menu.css('right', "0");
		fsOverlay.css('pointer-events', "auto");
		menuActive = true;
	}

	function closeMenu()
	{
		menu.removeClass('active');
		fsOverlay.css('pointer-events', "none");
		menuActive = false;
	}

	/* 

	4. Init Timer

	*/

	function initTimer()
    {
    	if($('.timer').length)
    	{
    		// Uncomment line below and replace date
	    	// var target_date = new Date("Dec 7, 2017").getTime();

	    	// comment lines below
	    	var date = new Date();
	    	date.setDate(date.getDate() + 3);
	    	var target_date = date.getTime();
	    	//----------------------------------------
	 
			// variables for time units
			var days, hours, minutes, seconds;

			var d = $('#day');
			var h = $('#hour');
			var m = $('#minute');
			var s = $('#second');

			setInterval(function ()
			{
			    // find the amount of "seconds" between now and target
			    var current_date = new Date().getTime();
			    var seconds_left = (target_date - current_date) / 1000;
			 
			    // do some time calculations
			    days = parseInt(seconds_left / 86400);
			    seconds_left = seconds_left % 86400;
			     
			    hours = parseInt(seconds_left / 3600);
			    seconds_left = seconds_left % 3600;
			     
			    minutes = parseInt(seconds_left / 60);
			    seconds = parseInt(seconds_left % 60);

			    // display result
			    d.text(days);
			    h.text(hours);
			    m.text(minutes);
			    s.text(seconds); 
			 
			}, 1000);
    	}	
    }

    /* 

	5. Init Favorite

	*/

    function initFavorite() {
		if ($('.favorite').length) {
			$('.favorite').each(function() {
				var fav = $(this);
				var productId = fav.closest('.product-item').data('product-id');
	
				// Check local storage to see if this product is favorited
				if (localStorage.getItem('favorite_' + productId)) {
					fav.addClass('active');
					fav.html('<i class="fas fa-heart" style="color: #fe4c50;"></i>'); // Filled heart
				}
	
				fav.on('click', function() {
					fav.toggleClass('active'); // Toggle the active class
	
					// Change the icon based on the active state
					if (fav.hasClass('active')) {
						fav.html('<i class="fas fa-heart" style="color: #fe4c50;"></i>'); // Filled heart
						localStorage.setItem('favorite_' + productId, true); // Store in local storage
					} else {
						fav.html('<i class="far fa-heart" style="color: #b9b4c7;"></i>'); // Empty heart
						localStorage.removeItem('favorite_' + productId); // Remove from local storage
					}
				});
			});
		}
	}
	

    /* 

	6. Init Fix Product Border

	*/

    function initFixProductBorder()
    {
    	if($('.product_filter').length)
    	{
			var products = $('.product_filter:visible');
    		var wdth = window.innerWidth;

    		// reset border
    		products.each(function()
    		{
    			$(this).css('border-right', 'solid 1px #e9e9e9');
    		});

    		// if window width is 991px or less

    		if(wdth < 480)
			{
				for(var i = 0; i < products.length; i++)
				{
					var product = $(products[i]);
					product.css('border-right', 'none');
				}
			}

    		else if(wdth < 576)
			{
				if(products.length < 5)
				{
					var product = $(products[products.length - 1]);
					product.css('border-right', 'none');
				}
				for(var i = 1; i < products.length; i+=2)
				{
					var product = $(products[i]);
					product.css('border-right', 'none');
				}
			}

    		else if(wdth < 768)
			{
				if(products.length < 5)
				{
					var product = $(products[products.length - 1]);
					product.css('border-right', 'none');
				}
				for(var i = 2; i < products.length; i+=3)
				{
					var product = $(products[i]);
					product.css('border-right', 'none');
				}
			}

    		else if(wdth < 992)
			{
				if(products.length < 5)
				{
					var product = $(products[products.length - 1]);
					product.css('border-right', 'none');
				}
				for(var i = 3; i < products.length; i+=4)
				{
					var product = $(products[i]);
					product.css('border-right', 'none');
				}
			}

			//if window width is larger than 991px
			else
			{
				if(products.length < 5)
				{
					var product = $(products[products.length - 1]);
					product.css('border-right', 'none');
				}
				for(var i = 4; i < products.length; i+=5)
				{
					var product = $(products[i]);
					product.css('border-right', 'none');
				}
			}	
    	}
    }

    /* 

	7. Init Isotope Filtering

	*/

    function initIsotopeFiltering()
    {
    	if($('.grid_sorting_button').length)
    	{
    		$('.grid_sorting_button').click(function()
	    	{
	    		// putting border fix inside of setTimeout because of the transition duration
	    		setTimeout(function()
		        {
		        	initFixProductBorder();
		        },500);

		        $('.grid_sorting_button.active').removeClass('active');
		        $(this).addClass('active');
		 
		        var selector = $(this).attr('data-filter');
		        $('.product-grid').isotope({
		            filter: selector,
		            animationOptions: {
		                duration: 750,
		                easing: 'linear',
		                queue: false
		            }
		        });

		        
		         return false;
		    });
    	}
    }

    /* 

	8. Init Slider

	*/

    function initSlider()
    {
    	if($('.product_slider').length)
    	{
    		var slider1 = $('.product_slider');

    		slider1.owlCarousel({
    			loop:false,
    			dots:false,
    			nav:false,
    			responsive:
				{
					0:{items:1},
					480:{items:2},
					768:{items:3},
					991:{items:4},
					1280:{items:5},
					1440:{items:5}
				}
    		});

    		if($('.product_slider_nav_left').length)
    		{
    			$('.product_slider_nav_left').on('click', function()
    			{
    				slider1.trigger('prev.owl.carousel');
    			});
    		}

    		if($('.product_slider_nav_right').length)
    		{
    			$('.product_slider_nav_right').on('click', function()
    			{
    				slider1.trigger('next.owl.carousel');
    			});
    		}
    	}
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle color variant clicks for both product grid and carousel
    function setupColorVariants(container) {
        const colorCircles = container.querySelectorAll('.color-circle');
        
        colorCircles.forEach(circle => {
            circle.addEventListener('click', function() {
                // Find the parent product item
                const productItem = this.closest('.product-item');
                
                // Remove active state from all circles in this product
                const allCircles = productItem.querySelectorAll('.color-circle');
                allCircles.forEach(c => c.classList.remove('color-active'));
                
                // Add active state to clicked circle
                this.classList.add('color-active');
                
                // Get new product details
                const productId = this.getAttribute('data-product-id');
                const productImage = this.getAttribute('data-product-image');
                const productPrice = this.getAttribute('data-product-price');
                
                // Select elements for animation
                const mainImage = productItem.querySelector('.main-product-image');
                const mainImageLink = productItem.querySelector('a[href^="product.php"]');
                const priceElement = productItem.querySelector('.product_price');
                
                // Animate image change
                function animateImageChange() {
                    mainImage.style.opacity = 0;
                    mainImage.style.transform = 'scale(0.9)';
                    
                    setTimeout(() => {
                        // Change image source
                        mainImage.src = productImage;
                        
                        // Update product link if exists
                        if (mainImageLink) {
                            mainImageLink.href = `product.php?product_id=${productId}`;
                        }
                        
                        // Restore image
                        mainImage.style.opacity = 1;
                        mainImage.style.transform = 'scale(1)';
                    }, 250);
                }
                
                // Animate image change
                animateImageChange();
                
                // Update price
                priceElement.textContent = `$${productPrice}`;
            });
        });
    }

    // Setup color variants for product grid
    const productGrid = document.querySelector('.product-grid');
    if (productGrid) {
        setupColorVariants(productGrid);
    }

    // Setup color variants for product slider
    const productSlider = document.querySelector('.product_slider');
    if (productSlider) {
        setupColorVariants(productSlider);
    }
});


document.addEventListener('DOMContentLoaded', function() {
    function setupAddToCart() {
        const quickAddButtons = document.querySelectorAll('.quick-add-button');

        quickAddButtons.forEach(button => {
            const productItem = button.closest('.product-item');
            const quickAddLink = button.querySelector('a');
            const plusIcon = quickAddLink.querySelector('.quick-add-icon');
            let sizesContainer = null;
            let isQuickAddActive = false;

            const productId = productItem.getAttribute('data-product-id');
            const availableSizesJSON = productItem.getAttribute('data-available-sizes');
            const availableSizes = JSON.parse(availableSizesJSON || '[]');

            // Check if the product has color variants
            const colorCircles = productItem.querySelectorAll('.color-circle');
            const hasColorVariants = colorCircles.length > 0;

            // Track the active color variant
            let activeColorCircle = hasColorVariants 
                ? productItem.querySelector('.color-circle.color-active') 
                : null;

            // If color variants exist, add event listeners
            if (hasColorVariants) {
                colorCircles.forEach(circle => {
                    circle.addEventListener('click', function() {
                        // Remove active class from all circles
                        colorCircles.forEach(c => c.classList.remove('color-active'));
                        // Add active class to the clicked circle
                        circle.classList.add('color-active');
                        // Update the active color circle
                        activeColorCircle = circle;
                    });
                });
            }

            // Function to close sizes container
            function closeSizesContainer() {
                if (sizesContainer) {
                    productItem.removeChild(sizesContainer);
                    sizesContainer = null;
                }
                plusIcon.classList.remove('rotated');
                isQuickAddActive = false;
            }

            // Close sizes container when clicking outside
            document.addEventListener('click', function(event) {
                if (isQuickAddActive && 
                    !productItem.contains(event.target) && 
                    !button.contains(event.target)) {
                    closeSizesContainer();
                }
            });

            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event from bubbling

                // If container is already open, close it
                if (isQuickAddActive) {
                    closeSizesContainer();
                    return;
                }

                // Rotate the plus icon to minus
                plusIcon.classList.add('rotated');
                isQuickAddActive = true;

                let sizesToShow = [];

                // Determine sizes to show based on product type
                if (hasColorVariants && activeColorCircle) {
                    // Get sizes for the active color variant
                    const activeAvailableSizesJSON = activeColorCircle.getAttribute('data-available-sizes');
                    sizesToShow = JSON.parse(activeAvailableSizesJSON || '[]');
                } else {
                    // If no color variants or no active color, use the main product sizes
                    sizesToShow = availableSizes;
                }

                // Check if there are available sizes
                if (!sizesToShow || sizesToShow.length === 0) {
                    alert('No sizes available for this product');
                    plusIcon.classList.remove('rotated');
                    isQuickAddActive = false;
                    return;
                }

                // Create sizes container
                sizesContainer = document.createElement('div');
                sizesContainer.classList.add('sizes-container');
                productItem.appendChild(sizesContainer);

                // Create size buttons
                sizesToShow.forEach(size => {
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
            });
        });
    }

    // Function to add product to cart
    function addToCart(productId, size) {
		let color = 'Unknown';
		
		const activeColorCircle = document.querySelector(`.color-circle[data-product-id="${productId}"].color-active`);
		
		if (activeColorCircle) {
			color = activeColorCircle.getAttribute('data-color') || 
					activeColorCircle.style.backgroundColor || 'Unknown';
		}
	
		fetch('add_to_cart_process.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: `product_id=${productId}&size=${size}&color=${encodeURIComponent(color)}`
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
    setupAddToCart();


});
// Wishlist Toggle Handler
document.querySelectorAll('.favorite').forEach(favoriteIcon => {
    favoriteIcon.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Find the product item
        const productItem = this.closest('.product-item');
        const productId = productItem.getAttribute('data-product-id');
        
        // Get color from active color circle
        let activeColorCircle = productItem.querySelector('.color-circle.color-active');
        
        // Fallback to first color circle if no active one
        if (!activeColorCircle) {
            activeColorCircle = productItem.querySelector('.color-circle');
            if (activeColorCircle) {
                activeColorCircle.classList.add('color-active');
            }
        }

        // Get the color name from the active color circle
        const color = activeColorCircle ? activeColorCircle.style.backgroundColor : 'Unknown';

        // Determine action based on current state
        const heartIcon = this.querySelector('i');
        const isCurrentlyFavorited = heartIcon.classList.contains('fas');
        const action = isCurrentlyFavorited ? 'remove' : 'add';

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
            if (data.success) {
                // Toggle heart icon
                if (action === 'add') {
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas');
                    heartIcon.style.color = '#fe4c50';
                    showToast('Added to wishlist', 'success');
                } else {
                    heartIcon.classList.remove('fas');
                    heartIcon.classList.add('far');
                    heartIcon.style.color = '#b9b4c7';
                    showToast('Removed from wishlist', 'success');
                }
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Wishlist Toggle Error:', error);
            showToast('Failed to update wishlist', 'error');
        });
    });
});