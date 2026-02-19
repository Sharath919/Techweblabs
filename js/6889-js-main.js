(function (g) { new WOW().init(); var f = g("#main-nav"); var c = g(".toggle"); var b = { disableAt: false, customToggle: c, levelSpacing: 10, navTitle: "Menu", levelTitles: true, levelTitles: true, labelClose: false, levelTitleAsBack: true, levelOpen: "expand", closeOnClick: true, insertClose: true, closeActiveLevel: true, insertBack: true }; var e = f.hcOffcanvasNav(b); function h() { if (g(window).scrollTop() >= 80) { g(".navfix").addClass("sticky") } else { g(".navfix").removeClass("sticky") } } g(function () { g(window).scroll(h); h() }); var a = g("li.sbmenu"); a.hover(function () { g(this).addClass("hover") }, function () { g(this).removeClass("hover") }); g(".video-link").magnificPopup({ type: "iframe", mainClass: "mfp-fade", removalDelay: 160, }); var d = g(".service-card-prb"); d.owlCarousel({ items: 4, loop: true, autoplay: true, margin: 20, nav: false, dots: false, autoplayTimeout: 3500, autoplayHoverPause: true, smartSpeed: 2000, responsive: { 0: { items: 1 }, 520: { items: 2 }, 768: { items: 3 }, 1200: { items: 3 }, 1400: { items: 3 }, 1600: { items: 3 }, } }); var d = g(".testimonial-card-a"); d.owlCarousel({ items: 1, loop: true, autoplay: true, autoplayTimeout: 6000, autoplayHoverPause: true, smartSpeed: 500, responsive: { 0: { items: 1 }, 768: { items: 1 }, 1024: { items: 1 }, 1400: { items: 1 } } }); var d = g(".video-testimonials"); d.owlCarousel({ items: 2, nav: false, dots: false, autoplay: false, autoplayTimeout: 3500, smartSpeed: 1500, margin: 20, responsive: { 0: { items: 1 }, 768: { items: 2 }, 1024: { items: 2 }, 1400: { items: 2 } } }); var d = g(".project-screens"); d.owlCarousel({ items: 4, loop: true, autoplay: true, margin: 20, nav: false, dots: false, autoplayTimeout: 3500, autoplayHoverPause: true, smartSpeed: 2000, responsive: { 0: { items: 1 }, 520: { items: 2 }, 768: { items: 3 }, 1200: { items: 3 }, 1400: { items: 3 }, 1600: { items: 3 }, } }); var d = g(".porto-slide"); d.owlCarousel({ items: 1, loop: true, autoplay: true, margin: 10, nav: false, dots: true, stagePadding: 50, autoplayTimeout: 350000, autoplayHoverPause: true, smartSpeed: 2000, responsive: { 0: { items: 1, stagePadding: 0 }, 520: { items: 1, stagePadding: 0 }, 768: { items: 1, stagePadding: 0 }, 1200: { items: 1 }, 1400: { items: 1 }, 1600: { items: 1 }, } }); var d = g(".single-slide"); d.owlCarousel({ items: 1, loop: true, autoplay: true, margin: 10, nav: false, dots: true, stagePadding: 100, autoplayTimeout: 3500, autoplayHoverPause: true, smartSpeed: 2000, responsive: { 0: { items: 1, stagePadding: 0 }, 520: { items: 1, stagePadding: 0 }, 768: { items: 1, stagePadding: 0 }, 1200: { items: 1 }, 1400: { items: 1 }, 1600: { items: 1 }, } }); var d = g(".bages-slider"); d.owlCarousel({ items: 4, loop: true, autoplay: true, centre: true, margin: 20, nav: false, dots: false, autoplayTimeout: 4000, autoplayHoverPause: true, smartSpeed: 2000, responsive: { 0: { items: 2 }, 520: { items: 3 }, 768: { items: 3 }, 1200: { items: 3 }, 1400: { items: 4 }, 1600: { items: 4 }, } }); var d = g(".logo-weworkfor"); d.owlCarousel({ items: 4, loop: true, autoplay: true, margin: 10, nav: false, dots: false, autoplayTimeout: 1800, autoplayHoverPause: false, smartSpeed: 2000, responsive: { 0: { items: 3 }, 520: { items: 3 }, 768: { items: 4 }, 1200: { items: 4 }, 1400: { items: 5 }, 1600: { items: 6 }, } }); var d = g(".testimonial-card-b"); d.owlCarousel({ items: 1, loop: true, autoplay: true, autoplayTimeout: 3000, autoplayHoverPause: true, dots: true, dotsContainer: "#testimonials-avatar", smartSpeed: 500, responsive: { 0: { items: 1 }, 768: { items: 1 }, 1024: { items: 1 }, 1400: { items: 1 } } }); var d = g(".zoomowl"); d.owlCarousel({ stagePadding: 200, loop: true, margin: 10, nav: false, items: 1, lazyLoad: true, responsive: { 0: { items: 1, stagePadding: 60 }, 600: { items: 1, stagePadding: 100 }, 1000: { items: 1, stagePadding: 200 }, 1200: { items: 1, stagePadding: 250 }, 1400: { items: 1, stagePadding: 300 }, 1600: { items: 1, stagePadding: 350 }, 1800: { items: 1, stagePadding: 400 } } }); g(".counter").counterUp({ delay: 10, time: 2500, }); g.scrollUp({ animation: "fade", scrollImg: { active: true, type: "background" } }); g(".card-list").imagesLoaded(function () { var i = g(".card-list").isotope({ itemSelector: ".single-card-item", percentPosition: true, masonry: { columnWidth: ".grid-sizer" } }); g(".filter-menu").on("click", "li", function () { var j = g(this).attr("data-filter"); i.isotope({ filter: j }) }) }); g(".filter-menu li").on("click", function (i) { g(this).siblings(".is-checked").removeClass("is-checked"); g(this).addClass("is-checked"); i.preventDefault() }); g("[data-background]").each(function () { 
	var bg = g(this).attr("data-background");
	// Skip if it's banner-5.jpg (we'll use gradients instead)
	if (bg && bg.indexOf("banner-5.jpg") === -1) {
		g(this).css("background-image", "url(" + bg + ")");
	}
});
// Apply gradient backgrounds based on page slug
(function() {
	var slug = window.location.pathname.replace(/^\//, '').replace(/\/$/, '').replace(/\.php$/, '');
	var gradients = {
		'swiggy-clone': 'linear-gradient(45deg, #ff6a00 0%, #ff8f1f 50%, #ffb347 100%)',
		'zomato-clone': 'linear-gradient(45deg, #e53935 0%, #d81b60 50%, #b71c1c 100%)',
		'ubereats-app': 'linear-gradient(45deg, #00c853 0%, #64dd17 50%, #1b5e20 100%)',
		'doordash-app': 'linear-gradient(45deg, #ff3d00 0%, #ff6e40 50%, #d84315 100%)',
		'grubhub-app': 'linear-gradient(45deg, #c62828 0%, #e53935 50%, #b71c1c 100%)',
		'postmates-clone': 'linear-gradient(45deg, #455a64 0%, #37474f 50%, #263238 100%)',
		'postmates-clone-app': 'linear-gradient(45deg, #455a64 0%, #37474f 50%, #263238 100%)',
		'deliveroo-app': 'linear-gradient(45deg, #00b8a9 0%, #26a69a 50%, #00897b 100%)',
		'food-delivery-app': 'linear-gradient(45deg, #ff6a00 0%, #ff8f1f 50%, #ffb347 100%)',
		'food-delivery-app-development': 'linear-gradient(45deg, #ff6a00 0%, #ff8f1f 50%, #ffb347 100%)',
		'instacart-clone': 'linear-gradient(45deg, #2ecc71 0%, #27ae60 50%, #1b5e20 100%)',
		'amazon-fresh-app': 'linear-gradient(45deg, #2e7d32 0%, #43a047 50%, #1b5e20 100%)',
		'zepto-app': 'linear-gradient(45deg, #6a1b9a 0%, #8e24aa 50%, #9c27b0 100%)',
		'bigbasket-clone': 'linear-gradient(45deg, #2d6a4f 0%, #40916c 50%, #1b4332 100%)',
		'blinkit-app': 'linear-gradient(45deg, #fdd835 0%, #fb8c00 50%, #f57c00 100%)',
		'swiggy-instamart-clone': 'linear-gradient(45deg, #ff9100 0%, #ff6d00 50%, #ffab40 100%)',
		'grocery-delivery-app': 'linear-gradient(45deg, #2ecc71 0%, #27ae60 50%, #1b5e20 100%)',
		'uber-clone': 'linear-gradient(45deg, #263238 0%, #1b5e20 50%, #004d40 100%)',
		'lyft-clone': 'linear-gradient(45deg, #d81b60 0%, #c2185b 50%, #ad1457 100%)',
		'ola-clone': 'linear-gradient(45deg, #ffa000 0%, #f57c00 50%, #ef6c00 100%)',
		'grab-clone': 'linear-gradient(45deg, #00a651 0%, #2ecc71 50%, #1b5e20 100%)',
		'Ride-SharingApp': 'linear-gradient(45deg, #263238 0%, #1b5e20 50%, #004d40 100%)',
		'taxi-go': 'linear-gradient(45deg, #ffa000 0%, #f57c00 50%, #ef6c00 100%)',
		'pharmeasy-clone': 'linear-gradient(45deg, #00bcd4 0%, #0097a7 50%, #00838f 100%)',
		'1mg-clone': 'linear-gradient(45deg, #ff7043 0%, #f4511e 50%, #e64a19 100%)',
		'healthcare-apps': 'linear-gradient(45deg, #00bcd4 0%, #0097a7 50%, #00838f 100%)',
		'urban-clone-app': 'linear-gradient(45deg, #009688 0%, #26a69a 50%, #00796b 100%)',
		'laundr-clone-app': 'linear-gradient(45deg, #039be5 0%, #0288d1 50%, #01579b 100%)',
		'Porter-clone-app': 'linear-gradient(45deg, #3f51b5 0%, #3949ab 50%, #283593 100%)',
		'default': 'linear-gradient(45deg, #4a0079 0%, #4202b2 50%, #4400b1 100%)'
	};
	var gradient = gradients[slug] || gradients['default'];
	// Apply gradient to breadcrumb sections with banner-5.jpg or data-background-gradient
	g('.breadcrumb-areav2[data-background*="banner-5.jpg"], .breadcrumb-areav2[data-background-gradient]').css('background-image', gradient);
	
	// Function to determine if a color is dark (returns true if dark, false if light)
	function isDarkColor(hex) {
		hex = hex.replace('#', '');
		if (hex.length === 3) {
			hex = hex.split('').map(function(h) { return h + h; }).join('');
		}
		var r = parseInt(hex.substr(0, 2), 16);
		var g = parseInt(hex.substr(2, 2), 16);
		var b = parseInt(hex.substr(4, 2), 16);
		// Calculate luminance using relative luminance formula (WCAG standard)
		var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
		return luminance < 0.5; // Threshold for dark/light
	}
	
	// Extract colors from gradient and determine if it's dark
	function isGradientDark(gradientStr) {
		// Extract hex colors from gradient string (6 or 3 digit hex codes)
		var colors = gradientStr.match(/#[0-9a-fA-F]{3,6}/gi) || [];
		if (colors.length === 0) return true; // Default to dark if no colors found
		
		// Check all colors in the gradient and calculate average luminance
		var totalLuminance = 0;
		for (var i = 0; i < colors.length; i++) {
			var hex = colors[i].replace('#', '');
			if (hex.length === 3) {
				hex = hex.split('').map(function(h) { return h + h; }).join('');
			}
			var r = parseInt(hex.substr(0, 2), 16);
			var g = parseInt(hex.substr(2, 2), 16);
			var b = parseInt(hex.substr(4, 2), 16);
			var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
			totalLuminance += luminance;
		}
		var avgLuminance = totalLuminance / colors.length;
		// If average luminance is less than 0.5, gradient is dark
		return avgLuminance < 0.5;
	}
	
	// Determine if gradient is dark and apply text color
	var isDark = isGradientDark(gradient);
	var breadcrumbSection = g('.breadcrumb-areav2');
	var bannerText = breadcrumbSection.find('.banner_text');
	
	if (bannerText.length) {
		// Remove any existing text color classes
		bannerText.removeClass('text-white text-dark');
		
		if (isDark) {
			// Dark gradient - use white text
			bannerText.addClass('text-white');
			bannerText.find('h1, h2, h3, h4, h5, h6, p, span').css('color', '#ffffff');
			bannerText.find('a.btn-outline').css({
				'color': '#ffffff',
				'border-color': '#ffffff'
			});
		} else {
			// Light gradient - use dark text
			bannerText.addClass('text-dark');
			bannerText.find('h1, h2, h3, h4, h5, h6, p, span').css('color', '#000000');
			bannerText.find('a.btn-outline').css({
				'color': '#000000',
				'border-color': '#000000'
			});
		}
	}
})(); })(jQuery);