<?php
/**
 * Get gradient background based on page slug
 * Returns gradient CSS string for breadcrumb area
 */
function getBreadcrumbGradient($slug = '') {
	$themes = [
		// Food Delivery Apps
		'swiggy-clone' => 'linear-gradient(45deg, #ff6a00 0%, #ff8f1f 50%, #ffb347 100%)',
		'zomato-clone' => 'linear-gradient(45deg, #e53935 0%, #d81b60 50%, #b71c1c 100%)',
		'ubereats-app' => 'linear-gradient(45deg, #00c853 0%, #64dd17 50%, #1b5e20 100%)',
		'doordash-app' => 'linear-gradient(45deg, #ff3d00 0%, #ff6e40 50%, #d84315 100%)',
		'grubhub-app' => 'linear-gradient(45deg, #c62828 0%, #e53935 50%, #b71c1c 100%)',
		'postmates-clone' => 'linear-gradient(45deg, #455a64 0%, #37474f 50%, #263238 100%)',
		'postmates-clone-app' => 'linear-gradient(45deg, #455a64 0%, #37474f 50%, #263238 100%)',
		'deliveroo-app' => 'linear-gradient(45deg, #00b8a9 0%, #26a69a 50%, #00897b 100%)',
		'food-delivery-app' => 'linear-gradient(45deg, #ff6a00 0%, #ff8f1f 50%, #ffb347 100%)',
		'food-delivery-app-development' => 'linear-gradient(45deg, #ff6a00 0%, #ff8f1f 50%, #ffb347 100%)',
		
		// Grocery Delivery Apps
		'instacart-clone' => 'linear-gradient(45deg, #2ecc71 0%, #27ae60 50%, #1b5e20 100%)',
		'amazon-fresh-app' => 'linear-gradient(45deg, #2e7d32 0%, #43a047 50%, #1b5e20 100%)',
		'zepto-app' => 'linear-gradient(45deg, #6a1b9a 0%, #8e24aa 50%, #9c27b0 100%)',
		'bigbasket-clone' => 'linear-gradient(45deg, #2d6a4f 0%, #40916c 50%, #1b4332 100%)',
		'blinkit-app' => 'linear-gradient(45deg, #fdd835 0%, #fb8c00 50%, #f57c00 100%)',
		'blinkit-clone' => 'linear-gradient(45deg, #fdd835 0%, #fb8c00 50%, #f57c00 100%)',
		'swiggy-instamart-clone' => 'linear-gradient(45deg, #ff9100 0%, #ff6d00 50%, #ffab40 100%)',
		'grocery-delivery-app' => 'linear-gradient(45deg, #2ecc71 0%, #27ae60 50%, #1b5e20 100%)',
		'grocery-app-development' => 'linear-gradient(45deg, #2ecc71 0%, #27ae60 50%, #1b5e20 100%)',
		
		// Ride Sharing Apps
		'uber-clone' => 'linear-gradient(45deg, #263238 0%, #1b5e20 50%, #004d40 100%)',
		'lyft-clone' => 'linear-gradient(45deg, #d81b60 0%, #c2185b 50%, #ad1457 100%)',
		'ola-clone' => 'linear-gradient(45deg, #ffa000 0%, #f57c00 50%, #ef6c00 100%)',
		'grab-clone' => 'linear-gradient(45deg, #00a651 0%, #2ecc71 50%, #1b5e20 100%)',
		'Ride-SharingApp' => 'linear-gradient(45deg, #263238 0%, #1b5e20 50%, #004d40 100%)',
		'on-demand-taxi-booking-app-development' => 'linear-gradient(45deg, #263238 0%, #1b5e20 50%, #004d40 100%)',
		'taxi-go' => 'linear-gradient(45deg, #ffa000 0%, #f57c00 50%, #ef6c00 100%)',
		
		// Healthcare/Pharmacy Apps
		'pharmeasy-clone' => 'linear-gradient(45deg, #00bcd4 0%, #0097a7 50%, #00838f 100%)',
		'1mg-clone' => 'linear-gradient(45deg, #ff7043 0%, #f4511e 50%, #e64a19 100%)',
		'frnd-dating-app' => 'linear-gradient(45deg, #e91e63 0%, #c2185b 50%, #ad1457 100%)',
		'healthcare-apps' => 'linear-gradient(45deg, #00bcd4 0%, #0097a7 50%, #00838f 100%)',
		'healthcare-mobile-app-development' => 'linear-gradient(45deg, #00bcd4 0%, #0097a7 50%, #00838f 100%)',
		'on-demand-home-services-app-development' => 'linear-gradient(45deg, #009688 0%, #26a69a 50%, #00796b 100%)',
		'handyman-mobile-app-development' => 'linear-gradient(45deg, #009688 0%, #26a69a 50%, #00796b 100%)',
		'fitness-mobile-app-development-company' => 'linear-gradient(45deg, #e91e63 0%, #f06292 50%, #c2185b 100%)',
		'pet-care-app-development' => 'linear-gradient(45deg, #ff9800 0%, #ffb74d 50%, #f57c00 100%)',
		'beauty-salon-app-development-company' => 'linear-gradient(45deg, #e91e63 0%, #f06292 50%, #c2185b 100%)',
		'education-app-development' => 'linear-gradient(45deg, #3f51b5 0%, #5c6bc0 50%, #283593 100%)',
		'e-learning-app-development' => 'linear-gradient(45deg, #3f51b5 0%, #5c6bc0 50%, #283593 100%)',
		'real-estate-app-development' => 'linear-gradient(45deg, #795548 0%, #a1887f 50%, #5d4037 100%)',
		'ecommerce-app-development' => 'linear-gradient(45deg, #2196f3 0%, #64b5f6 50%, #1976d2 100%)',
		'car-rental-app-development' => 'linear-gradient(45deg, #607d8b 0%, #90a4ae 50%, #455a64 100%)',
		'doctor-consultation-app-development' => 'linear-gradient(45deg, #00bcd4 0%, #4dd0e1 50%, #0097a7 100%)',
		'social-media-app-development' => 'linear-gradient(45deg, #3f51b5 0%, #7986cb 50%, #303f9f 100%)',
		'logistics-transportation-app-development' => 'linear-gradient(45deg, #ff9800 0%, #ffb74d 50%, #f57c00 100%)',
		'job-portal-app-development' => 'linear-gradient(45deg, #4caf50 0%, #81c784 50%, #388e3c 100%)',
		'cashback-app-development' => 'linear-gradient(45deg, #ff5722 0%, #ff8a65 50%, #d84315 100%)',
		'ott-platform-app-development' => 'linear-gradient(45deg, #e91e63 0%, #f06292 50%, #c2185b 100%)',
		'dating-app-development' => 'linear-gradient(45deg, #e91e63 0%, #f06292 50%, #c2185b 100%)',
		'chat-app-development-company' => 'linear-gradient(45deg, #00bcd4 0%, #4dd0e1 50%, #0097a7 100%)',
		'erp-software-development' => 'linear-gradient(45deg, #673ab7 0%, #9575cd 50%, #512da8 100%)',
		'crm-software-development' => 'linear-gradient(45deg, #009688 0%, #4db6ac 50%, #00796b 100%)',
		'hrm-software-development' => 'linear-gradient(45deg, #ff9800 0%, #ffb74d 50%, #f57c00 100%)',
		'inventory-management-software-development' => 'linear-gradient(45deg, #607d8b 0%, #90a4ae 50%, #455a64 100%)',
		
		// Other Services
		'urban-clone-app' => 'linear-gradient(45deg, #009688 0%, #26a69a 50%, #00796b 100%)',
		'laundr-clone-app' => 'linear-gradient(45deg, #039be5 0%, #0288d1 50%, #01579b 100%)',
		'Porter-clone-app' => 'linear-gradient(45deg, #3f51b5 0%, #3949ab 50%, #283593 100%)',
		
		// Default gradient (TechWebLabs brand colors)
		'default' => 'linear-gradient(45deg, #4a0079 0%, #4202b2 50%, #4400b1 100%)',
	];
	
	return $themes[$slug] ?? $themes['default'];
}

