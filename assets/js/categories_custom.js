/* JS Document */

/******************************

[Table of Contents]

1. Vars and Inits
2. Set Header
3. Init Menu
5. Init Fix Product Border
6. Init Isotope Filtering
7. Init Price Slider
8. Init Checkboxes



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
	initFixProductBorder();
	initIsotopeFiltering();
	initPriceSlider();
	initCheckboxes();

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

	5. Init Fix Product Border

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
				for(var i = 2; i < products.length; i+=3)
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
				for(var i = 3; i < products.length; i+=4)
				{
					var product = $(products[i]);
					product.css('border-right', 'none');
				}
			}	
    	}
    }

    /* 

	6. Init Isotope Filtering

	*/

    function initIsotopeFiltering()
    {
    	var sortTypes = $('.type_sorting_btn');
    	var sortNums = $('.num_sorting_btn');
    	var sortTypesSelected = $('.sorting_type .item_sorting_btn is-checked span');
    	var filterButton = $('.filter_button');

    	if($('.product-grid').length)
    	{
    		$('.product-grid').isotope({
				itemSelector: '.product-item',
				getSortData: {
					price: function(itemElement) {
						var priceEle = $(itemElement).find('.product_price').text().replace('RM', '').trim();
						return parseFloat(priceEle);
					},
					name: '.product_name'
				},
				animationOptions: {
					duration: 750,
					easing: 'linear',
					queue: false
				}
			});

    		// Short based on the value from the sorting_type dropdown
	        sortTypes.each(function()
	        {
	        	$(this).on('click', function()
	        	{
	        		$('.type_sorting_text').text($(this).text());
	        		var option = $(this).attr('data-isotope-option');
	        		option = JSON.parse( option );
    				$('.product-grid').isotope( option );
	        	});
	        });

	        // Show only a selected number of items
	        sortNums.each(function()
	        {
	        	$(this).on('click', function()
	        	{
	        		var numSortingText = $(this).text();
					var numFilter = ':nth-child(-n+' + numSortingText + ')';
	        		$('.num_sorting_text').text($(this).text());
    				$('.product-grid').isotope({filter: numFilter });
	        	});
	        });	

	        // Filter based on the price range slider
	        filterButton.on('click', function()
	        {
	        	$('.product-grid').isotope({
		            filter: function()
		            {
		            	var priceRange = $('#amount').val();
			        	var priceMin = parseFloat(priceRange.split('-')[0].replace('$', ''));
			        	var priceMax = parseFloat(priceRange.split('-')[1].replace('$', ''));
			        	var itemPrice = $(this).find('.product_price').clone().children().remove().end().text().replace( '$', '' );

			        	return (itemPrice > priceMin) && (itemPrice < priceMax);
		            },
		            animationOptions: {
		                duration: 750,
		                easing: 'linear',
		                queue: false
		            }
		        });
	        });
    	}
    }

    /* 

	7. Init Price Slider

	*/

	function initPriceSlider() {
		var minPrice = parseFloat($("#slider-range").data("min")) || 0;
		var maxPrice = parseFloat($("#slider-range").data("max")) || 1000;
	
		$("#slider-range").slider({
			range: true,
			min: minPrice,
			max: maxPrice,
			values: [
				parseFloat($("#min_price").val()) || minPrice,
				parseFloat($("#max_price").val()) || maxPrice
			],
			slide: function(event, ui) {
				$("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
				$("#min_price").val(ui.values[0]);
				$("#max_price").val(ui.values[1]);
				
				// Automatically set filter_applied to 1
				$("#filter_applied").val("1");
			}
		});
	
		$("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));
	}
	
	$(document).ready(function() {
		initPriceSlider();
	
		$('#apply-filter').on('click', function() {
			// Ensure filter_applied is set to 1
			$("#filter_applied").val("1");
			$(this).closest('form').submit();
		});
	});
	
	function initIsotopeFiltering() {
		var $grid = $('.product-grid').isotope({
			itemSelector: '.product-item',
			layoutMode: 'fitRows',
			getSortData: {
				price: function(itemElement) {
					var priceEle = $(itemElement).find('.product_price').text().replace('$', '');
					return parseFloat(priceEle);
				},
				name: '.product_name'
			}
		});
	
		// Filter items on button click
		$('#apply-filter').on('click', function() {
			var filters = [];
	
			// Collect filters for price
			if ($('#min_price').val() && $('#max_price').val()) {
				filters.push(function() {
					var priceMin = parseFloat($('#min_price').val());
					var priceMax = parseFloat($('#max_price').val());
					var itemPrice = parseFloat($(this).find('.product_price').text().replace('$', ''));
					return itemPrice >= priceMin && itemPrice <= priceMax;
				});
			}
	
			// Add other filters (category, color, brand) similarly
			// Example for category
			if ($('input[name="category"]:checked').length) {
				filters.push(function() {
					var selectedCategories = $('input[name="category"]:checked').map(function() {
						return $(this).val();
					}).get();
					return selectedCategories.includes($(this).data('category'));
				});
			}
	
			// Apply filters
			$grid.isotope({
				filter: function() {
					return filters.every(function(filter) {
						return filter.call(this);
					}, this);
				},
				animationOptions: {
					duration: 750,
					easing: 'linear',
					queue: false
				}
			});
		});
	}
    /* 

	8. Init Checkboxes

	*/

    function initCheckboxes()
    {
    	if($('.checkboxes li').length)
    	{
    		var boxes = $('.checkboxes li');

    		boxes.each(function()
    		{
    			var box = $(this);

    			box.on('click', function()
    			{
    				if(box.hasClass('active'))
    				{
    					box.find('i').removeClass('fa-square');
    					box.find('i').addClass('fa-square-o');
    					box.toggleClass('active');
    				}
    				else
    				{
    					box.find('i').removeClass('fa-square-o');
    					box.find('i').addClass('fa-square');
    					box.toggleClass('active');
    				}
    				// box.toggleClass('active');
    			});
    		});

    		if($('.show_more').length)
    		{
    			var checkboxes = $('.checkboxes');

    			$('.show_more').on('click', function()
    			{
    				checkboxes.toggleClass('active');
    			});
    		}
    	};
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