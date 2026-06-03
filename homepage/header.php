<?php
// Safely get URI path
$uriPath = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
$slug = trim($uriPath, '/');
if (strpos($slug, 'pages/ondemand/') === 0) {
	$slug = str_replace('pages/ondemand/', '', $slug);
	$slug = str_replace('.php', '', $slug);
}
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
	
	// Ride Sharing Apps
	'uber-clone' => 'linear-gradient(45deg, #263238 0%, #1b5e20 50%, #004d40 100%)',
	'lyft-clone' => 'linear-gradient(45deg, #d81b60 0%, #c2185b 50%, #ad1457 100%)',
	'ola-clone' => 'linear-gradient(45deg, #ffa000 0%, #f57c00 50%, #ef6c00 100%)',
	'grab-clone' => 'linear-gradient(45deg, #00a651 0%, #2ecc71 50%, #1b5e20 100%)',
	'Ride-SharingApp' => 'linear-gradient(45deg, #263238 0%, #1b5e20 50%, #004d40 100%)',
	'taxi-go' => 'linear-gradient(45deg, #ffa000 0%, #f57c00 50%, #ef6c00 100%)',
	
	// Healthcare/Pharmacy Apps
	'pharmeasy-clone' => 'linear-gradient(45deg, #00bcd4 0%, #0097a7 50%, #00838f 100%)',
	'1mg-clone' => 'linear-gradient(45deg, #ff7043 0%, #f4511e 50%, #e64a19 100%)',
	'healthcare-apps' => 'linear-gradient(45deg, #00bcd4 0%, #0097a7 50%, #00838f 100%)',

	// Dating / Social Apps
	'frnd-dating-app' => 'linear-gradient(45deg, #e91e63 0%, #c2185b 50%, #ad1457 100%)',
	
	// Other Services
	'urban-clone-app' => 'linear-gradient(45deg, #009688 0%, #26a69a 50%, #00796b 100%)',
	'laundr-clone-app' => 'linear-gradient(45deg, #039be5 0%, #0288d1 50%, #01579b 100%)',
	'Porter-clone-app' => 'linear-gradient(45deg, #3f51b5 0%, #3949ab 50%, #283593 100%)',
	
	// Default gradient (TechWebLabs brand colors)
	'default' => 'linear-gradient(45deg, #4a0079 0%, #4202b2 50%, #4400b1 100%)',
];
$gradient = $themes[$slug] ?? $themes['default'];
?>
<header class="header-pr nav-bg-b nav-bg-w main-header navfix fixed-top menu-white">
	<div class="container-fluid m-pad">
		<div class="menu-header">
			<div class="dsk-logo">
				<a class="nav-brand" href="https://techweblabs.com/" aria-label="Techweblabs - Home">
					<img
						src="images/logo.png"
						alt="Techweblabs - Leading Mobile App and Web Development Company"
						class="mega-white-logo" style="max-height: 50px;">
					<img
						src="images/logo-black.webp"
						alt="Techweblabs - Leading Mobile App and Web Development Company"
						class="mega-darks-logo" style="max-height: 50px;">
				</a>
			</div>
			<div class="custom-nav" role="navigation" aria-label="Main navigation">
				<ul class="nav-list">
					<li class="sbmenu">
						<a href="https://techweblabs.com/" class="menu-links" aria-haspopup="true" aria-expanded="false">Industries</a>
						<div class="nx-dropdown">
							<div class="sub-menu-section">
								<div class="container">
									<div class="col-md-12">
										<div class="sub-menu-center-block">
											<div class="sub-menu-column">
												<div class="menuheading">On-Demand Services</div>
												<ul>
													<li><a href="food-delivery-app-development"><img src="images/icons/ondemand/food.png" class="icon-spacing" alt="Food Delivery App Development Icon" />Food Delivery App</a></li>
													<li><a href="grocery-app-development"><img src="images/icons/ondemand/grocery.png" class="icon-spacing" alt="Grocery Delivery App Development Icon" />Grocery Delivery App</a></li>
													<li><a href="on-demand-taxi-booking-app-development"><img src="images/icons/ondemand/taxi.png" class="icon-spacing" alt="Ride-Sharing App Development Icon" />Ride-Sharing App</a></li>
													<li><a href="on-demand-home-services-app-development"><img src="images/icons/ondemand/homeservice.png" class="icon-spacing" alt="Home Services App Development Icon" />Home Services App</a></li>
													<li><a href="healthcare-mobile-app-development"><img src="images/icons/ondemand/medical.png" class="icon-spacing" alt="Healthcare App Development Icon" />Healthcare App</a></li>
													<li><a href="handyman-mobile-app-development"><img src="images/icons/ondemand/professional.png" class="icon-spacing" alt="Professional Services App Development Icon" />Professional Services App</a></li>
													<li><a href="fitness-mobile-app-development-company"><img src="images/icons/ondemand/fitness.png" class="icon-spacing" alt="Fitness App Development Icon" />Fitness App</a></li>
													<li><a href="pet-care-app-development"><img src="images/icons/ondemand/pet.png" class="icon-spacing" alt="Pet Care App Development Icon" />Pet Care App</a></li>
													<li><a href="beauty-salon-app-development-company"><img src="images/icons/ondemand/saloon.png" class="icon-spacing" alt="Beauty and Salon App Development Icon" />Beauty and Salon App</a></li>
													<li><a href="e-learning-app-development"><img src="images/icons/ondemand/lms.png" class="icon-spacing" alt="Learning and Education App Development Icon" />Learning and Education App</a></li>
												</ul>
											</div>

											<div class="sub-menu-column">
												<div class="menuheading">Marketplace</div>
												<ul>
													<li><a href="general-marketplaces-app"><img src="images/icons/marketplace/marketplace.png" class="icon-spacing" alt="General Marketplaces App Development Icon" />General Marketplaces</a></li>
													<li><a href="handmade-app"><img src="images/icons/marketplace/handmade.png" class="icon-spacing" alt="Handmade or Vintage Goods App Development Icon" />Handmade or Vintage Goods</a></li>
													<li><a href="freelance-services-app"><img src="images/icons/marketplace/freelancer.png" class="icon-spacing" alt="Freelance Services App Development Icon" />Freelance Services</a></li>

													<li><a href="real-estate-app-development"><img src="images/icons/marketplace/realestate.png" class="icon-spacing" alt="Real Estate App Development Icon" />Real Estate</a></li>
													<li><a href="fashion-apparel.app"><img src="images/icons/marketplace/fashion.png" class="icon-spacing" alt="Fashion and Apparel App Development Icon" />Fashion and Apparel</a></li>
													<li><a href="car-rental-app-development"><img src="images/icons/marketplace/automotive.png" class="icon-spacing" alt="Automotive App Development Icon" />Automotive</a></li>
													<li><a href="local-services-app"><img src="images/icons/marketplace/localdelivery.png" class="icon-spacing" alt="Local Services App Development Icon" />Local Services</a></li>

												</ul>
											</div>

											<div class="sub-menu-column">
												<div class="menuheading">Services</div>
												<ul>
													<li><a href="doctor-consultation-app-development"><img src="images/icons/services/consultation.png" class="icon-spacing" alt="Consultation App Development Icon" />Consultation App</a></li>
													<li><a href="social-media-app-development"><img src="images/icons/services/social.png" class="icon-spacing" alt="Social Media App Development Icon" />Social Media App</a></li>
													<li><a href="logistics-transportation-app-development"><img src="images/icons/services/logistics.png" class="icon-spacing" alt="Logistics App Development Icon" />Logistics</a></li>
													<li><a href="education-app-development"><img src="images/icons/marketplace/education.png" class="icon-spacing" alt="Education App Development Icon" />Education</a></li>
													<li><a href="job-portal-app-development"><img src="images/icons/services/hiring.png" class="icon-spacing" alt="Job Boards App Development Icon" />Job Boards</a></li>
													<li><a href="cashback-app-development"><img src="images/icons/services/coupons.png" class="icon-spacing" alt="Cashback and Coupons App Development Icon" />Cashback and Coupons</a></li>
													<li><a href="ott-platform-app-development"><img src="images/icons/services/ott.png" class="icon-spacing" alt="OTT Applications Development Icon" />OTT Applications</a></li>
													<li><a href="dating-app-development"><img src="images/icons/services/dating.png" class="icon-spacing" alt="Dating Applications Development Icon" />Dating Applications</a></li>
													<li><a href="chat-app-development-company"><img src="images/icons/services/chat.png" class="icon-spacing" alt="Chat Applications Development Icon" />Chat Applications</a></li>

												</ul>
											</div>
											<div class="sub-menu-column">
												<div class="menuheading">Solutions</div>
												<ul>
													<li><a href="erp-software-development"><img src="images/icons/solutions/erp.png" class="icon-spacing" alt="Enterprise Resource Planning ERP Development Icon" />Enterprise Resource Planning (ERP)</a></li>
													<li><a href="crm-software-development"><img src="images/icons/solutions/crm.png" class="icon-spacing" alt="Customer Relationship Management CRM Development Icon" />Customer Relationship Management (CRM)</a></li>
													<li><a href="hrm-software-development"><img src="images/icons/solutions/hrm.png" class="icon-spacing" alt="Human Resource Management HRM Development Icon" />Human Resource Management (HRM)</a></li>
													<li><a href="content-management-app"><img src="images/icons/solutions/cms.png" class="icon-spacing" alt="Content Management System CMS Development Icon" />Content Management System (CMS)</a></li>
													<li><a href="ecommerce-app-development"><img src="images/icons/solutions/ecommerce.png" class="icon-spacing" alt="E-commerce Platform Development Icon" />E-commerce Platform</a></li>
													<!-- <li><a href="#"><img src="images/icons/ondemand/lms.png" class="icon-spacing" alt="Icon" />Learning Management System (LMS) </a></li> -->
													<li><a href="inventory-management"><img src="images/icons/solutions/inventory.png" class="icon-spacing" alt="Inventory Management System Development Icon" />Inventory Management </a></li>


												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li class="sbmenu">
						<a href="https://techweblabs.com/portfolio/" class="menu-links" aria-haspopup="true" aria-expanded="false">Business Models</a>
						<div class="nx-dropdown">
							<div class="sub-menu-section">
								<div class="container">
									<div class="col-md-12">
										<div class="sub-menu-center-block">
											<div class="sub-menu-column">
												<ul>
													<li><a href="ubereats-app"><img src="images/business/uber.png" class="icon-spacing" alt="UberEats Clone App Development Icon" />UberEats</a></li>
													<li><a href="doordash-app"><img src="images/business/doordash.png" class="icon-spacing" alt="DoorDash Clone App Development Icon" />DoorDash</a></li>
													<li><a href="grubhub-app"><img src="images/business/grubhub.png" class="icon-spacing" alt="Grubhub Clone App Development Icon" />Grubhub</a></li>
													<li><a href="postmates-clone"><img src="images/business/Postmates.png" class="icon-spacing" alt="Postmates Clone App Development Icon" />Postmates</a></li>
													<li><a href="zomato-clone"><img src="images/business/Zomato.png" class="icon-spacing" alt="Zomato Clone App Development Icon" />Zomato</a></li>
													<li><a href="swiggy-clone"><img src="images/business/Swiggy.png" class="icon-spacing" alt="Swiggy Clone App Development Icon" />Swiggy</a></li>
													<li><a href="deliveroo-app"><img src="images/business/deliveroo.png" class="icon-spacing" alt="Deliveroo Clone App Development Icon" />Deliveroo</a></li>
												</ul>
											</div>
											<div class="sub-menu-column">
												<ul>
													<li><a href="instacart-clone"><img src="images/business/Instacart.png" class="icon-spacing" alt="Instacart Clone App Development Icon" />Instacart</a></li>
													<li><a href="amazon-fresh-app"><img src="images/business/Amazon Fresh.png" class="icon-spacing" alt="Amazon Fresh Clone App Development Icon" />Amazon Fresh</a></li>
													<li><a href="zepto-app"><img src="images/business/zepto.png" class="icon-spacing" alt="Zepto Clone App Development Icon" />Zepto</a></li>
													<li><a href="bigbasket-clone"><img src="images/business/bigBasket.png" class="icon-spacing" alt="BigBasket Clone App Development Icon" />BigBasket</a></li>
													<li><a href="blinkit-app"><img src="images/business/blinkit.png" class="icon-spacing" alt="Blinkit Clone App Development Icon" />Blinkit</a></li>
													<li><a href="swiggy-instamart-clone"><img src="images/business/swiggy Instamart.png" class="icon-spacing" alt="Swiggy Instamart Clone App Development Icon" />Swiggy Instamart</a></li>
												</ul>
											</div>
											<div class="sub-menu-column">
												<ul>
													<li><a href="1mg-clone"><img src="images/business/1mg.png" class="icon-spacing" alt="1mg Clone App Development Icon" />1mg</a></li>
													<li><a href="frnd-dating-app"><img src="images/icons/services/dating.png" class="icon-spacing" alt="Frnd Dating App Development Hyderabad" />Frnd</a></li>
													<li><a href="pharmeasy-clone"><img src="images/business/PharmEasy.png" class="icon-spacing" alt="PharmEasy Clone App Development Icon" />PharmEasy</a></li>
													<li><a href="uber-clone"><img src="images/business/Ubers.png" class="icon-spacing" alt="Uber Clone App Development Icon" />Uber</a></li>
													<li><a href="lyft-clone"><img src="images/business/Lyft.png" class="icon-spacing" alt="Lyft Clone App Development Icon" />Lyft</a></li>
													<li><a href="ola-clone"><img src="images/business/Ola.png" class="icon-spacing" alt="Ola Clone App Development Icon" />Ola</a></li>
													<li><a href="grab-clone"><img src="images/business/Grab.png" class="icon-spacing" alt="Grab Clone App Development Icon" />Grab</a></li>
												</ul>
											</div>
											<div class="sub-menu-column">
												<ul>
													<li><a href="urban-clone-app"><img src="images/business/Urbanclap.png" class="icon-spacing" alt="UrbanClap Clone App Development Icon" />UrbanClap</a></li>
													<li><a href="laundr-clone-app"><img src="images/business/Laundrapp.png" class="icon-spacing" alt="Laundrapp Clone App Development Icon" />Laundrapp</a></li>

													<li><a href="fitness-clone-app"><img src="images/business/Fitness.png" class="icon-spacing" alt="Fitness App Clone Development Icon" />Fitness App</a></li>
													<li><a href="dunzo-clone-app"><img src="images/business/Dunzo.png" class="icon-spacing" alt="Dunzo Clone App Development Icon" />Dunzo</a></li>
													<li><a href="postmates-clone-app"><img src="images/business/Postmate.png" class="icon-spacing" alt="Postmates Clone App Development Icon" />Postmates</a></li>
													<li><a href="Porter-clone-app"><img src="images/business/Porter.png" class="icon-spacing" alt="Porter Clone App Development Icon" />Porter</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
					</li>
					<li class="sbmenu">
						<a href="#" class="menu-links" aria-haspopup="true" aria-expanded="false">Services</a>
						<div class="nx-dropdown">
							<div class="sub-menu-section">
								<div class="container">
									<div class="col-md-12">
										<div class="sub-menu-center-block">
											<div class="sub-menu-column">
												<div class="menuheading">Mobile App Development</div>
												<ul>
													<li><a href="custom-app-development"><img src="images/services/Custom%20App%20Development.png" class="icon-spacing" alt="Custom App Development Services Icon" />Custom App Development</a></li>
													<li><a href="cross-platform-app"><img src="images/services/Cross-Platform%20App%20Developments.png" class="icon-spacing" alt="Cross-Platform App Development Services Icon" />Cross-Platform App Development</a></li>
													<li><a href="ios-app-development"><img src="images/services/iOS%20App%20Development.png" class="icon-spacing" alt="iOS App Development Services Icon" />iOS App Development</a></li>
													<li><a href="android-app-development"><img src="images/services/Android%20App%20Development.png" class="icon-spacing" alt="Android App Development Services Icon" />Android App Development</a></li>
													<li><a href="app-store-optimization"><img src="images/services/App%20Store%20Optimization.png" class="icon-spacing" alt="App Store Optimization ASO Services Icon" />App Store Optimization (ASO)</a></li>
													<li><a href="mobile-app-maintenance"><img src="images/services/Mobile%20App%20Maintenance.png" class="icon-spacing" alt="Mobile App Maintenance Services Icon" />Mobile App Maintenance</a></li>
													<li><a href="mobile-app-designing"><img src="images/services/Mobile%20App%20Designing.png" class="icon-spacing" alt="Mobile App Designing Services Icon" />Mobile App Designing</a></li>
													<li><a href="mvp-development-app"><img src="images/services/MVP%20Development.png" class="icon-spacing" alt="MVP Development Services Icon" />MVP Development</a></li>
												</ul>
											</div>

											<div class="sub-menu-column">
												<div class="menuheading">Website Development</div>
												<ul>
													<li><a href="custom-website-development"><img src="images/services/Custom%20Website%20Development.png" class="icon-spacing" alt="Custom Website Development Services Icon" />Custom Website Development</a></li>
													<li><a href="e-commerce-website"><img src="images/services/E-commerce%20Website.png" class="icon-spacing" alt="E-commerce Website Development Services Icon" />E-commerce Website Development</a></li>
													<!-- <li><a href="CMS-development"><img src="images/services/CMS%20Development.png" class="icon-spacing" alt="Icon" />CMS Development</a></li> -->
													<li><a href="responsive-Web-design"><img src="images/services/Responsive%20Web%20Design.png" class="icon-spacing" alt="Responsive Web Design Services Icon" />Responsive Web Design</a></li>
													<li><a href="website-maintenance"><img src="images/services/Website%20Maintenance.png" class="icon-spacing" alt="Website Maintenance Services Icon" />Website Maintenance</a></li>
													<li><a href="website-testing"><img src="images/services/Website%20Testing.png" class="icon-spacing" alt="Website Testing Services Icon" />Website Testing</a></li>
													<li><a href="landing-Pages"><img src="images/services/Landing%20Pages.png" class="icon-spacing" alt="Landing Pages Development Services Icon" />Landing Pages</a></li>
													<li><a href="admin-panels"><img src="images/services/Admin%20Panels.png" class="icon-spacing" alt="Admin Panels Development Services Icon" />Admin Panels</a></li>
												</ul>
											</div>

											<div class="sub-menu-column">
												<div class="menuheading">UI/UX Design Services</div>
												<ul>
													<li><a href="User-Research"><img src="images/services/User%20Research.png" class="icon-spacing" alt="User Research Services Icon" />User Research</a></li>
													<li><a href="Information-Architecture"><img src="images/services/Information%20Architecture.png" class="icon-spacing" alt="Information Architecture Services Icon" />Information Architecture</a></li>
													<li><a href="wireframing-services"><img src="images/services/Wireframing.png" class="icon-spacing" alt="Wireframing Services Icon" />Wireframing</a></li>
													<li><a href="Prototype-Design-Services"><img src="images/services/Prototyping.png" class="icon-spacing" alt="Prototyping Services Icon" />Prototyping</a></li>
													<li><a href="visual-design-services"><img src="images/services/Visual%20Design.png" class="icon-spacing" alt="Visual Design Services Icon" />Visual Design</a></li>
													<li><a href="intaraction-desgin-services"><img src="images/services/Interaction%20Design.png" class="icon-spacing" alt="Interaction Design Services Icon" />Interaction Design</a></li>
													<li><a href="accessibility-compliance-services"><img src="images/services/Compliance.png" class="icon-spacing" alt="Accessibility Compliance Services Icon" />Accessibility Compliance</a></li>
													<li><a href="graphic-design-service"><img src="images/services/Graphic%20Design.jpg" class="icon-spacing" alt="Graphic Design Services Icon" />Graphic Design</a></li>
												</ul>
											</div>

											<div class="sub-menu-column">
												<div class="menuheading">Digital Marketing Services</div>
												<ul>
													<li><a href="search-engine-marketing"><img src="images/services/SEM.jpg" class="icon-spacing" alt="Search Engine Marketing SEM Services Icon" />Search Engine Marketing (SEM)</a></li>
													<li><a href="social-media-marketing"><img src="images/services/Social%20Media.png" class="icon-spacing" alt="Social Media Marketing Services Icon" />Social Media Marketing</a></li>
													<li><a href="email-marketing"><img src="images/services/Email%20Marketing.png" class="icon-spacing" alt="Email Marketing Services Icon" />Email Marketing</a></li>
													<li><a href="content-marketing"><img src="images/services/Content%20Marketing.png" class="icon-spacing" alt="Content Marketing Services Icon" />Content Marketing</a></li>
													<!-- <li><a href="#"><img src="images/services/ppc.png" class="icon-spacing" alt="Icon" />Pay-Per-Click (PPC) Advertising</a></li> -->
													<li><a href="affiliate-marketing"><img src="images/services/Affiliate%20Marketing.png" class="icon-spacing" alt="Affiliate Marketing Services Icon" />Affiliate Marketing</a></li>
													<li><a href="analytics-reporting"><img src="images/services/Analytics.png" class="icon-spacing" alt="Analytics and Reporting Services Icon" />Analytics and Reporting</a></li>
													<li><a href="online-reputation"><img src="images/services/Online%20Reputation.png" class="icon-spacing" alt="Online Reputation Management Services Icon" />Online Reputation Management</a></li>
												</ul>
											</div>

										</div>
									</div>
								</div>
							</div>
					</li>

					<li class=" rpdropdown">
						<a href="about" class="menu-links">About Us</a>

					</li>
					<li class=" rpdropdown">
						<a href="careers" class="menu-links">Careers</a>

					</li>
					<li class=" rpdropdown">
						<a href="https://techweblabs.com/blogs/" class="menu-links">Blog</a>

					</li>
				</ul>

				<ul class="nav-list right-end-btn">
					<li class="hidemobile">
						<a href="https://wa.link/fucwsq" target="_blank" class="btn-round- btn-br bg-btn2" aria-label="Contact us on WhatsApp">
							<i class="fas fa-phone-alt"></i>
						</a>
					</li>
					<li class="hidemobile">
						<a href="https://wa.link/fucwsq" target="_blank" class="btn-br bg-btn3 btshad-b2 lnk">
							Request A Quote <span class="circle"></span>
						</a>
					</li>
					<li class="hidedesktop darkmodeswitch">
						<div class="switch-wrapper">
							<label class="switch" for="niwax">
								<input type="checkbox" id="niwax" aria-label="Toggle dark mode">
								<span class="slider round"></span>
							</label>
						</div>
					</li>
					<li class="hidedesktop">
						<a href="https://wa.link/fucwsq" target="_blank" class="btn-round- btn-br bg-btn2" aria-label="Contact us on WhatsApp">
							<i class="fas fa-phone-alt"></i>
						</a>
					</li>
					<li class="navm- hidedesktop">
						<a class="toggle" href="#" aria-label="Toggle mobile menu">
							<span></span>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<nav id="main-nav" aria-label="Mobile navigation">
			<ul class="first-nav">
				<li>
					<a href="https://techweblabs.com/">Home</a>
					<ul>
						<li>
							<a href="#">On Demand Services</a>
							<ul>
								<li><a href="#"><img src="images/icons/ondemand/grocery.png" class="icon-spacing" alt="Grocery Delivery App Development Icon" />Grocery Delivery App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/taxi.png" class="icon-spacing" alt="Ride-Sharing App Development Icon" />Ride-Sharing App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/homeservice.png" class="icon-spacing" alt="Home Services App Development Icon" />Home Services App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/medical.png" class="icon-spacing" alt="Healthcare App Development Icon" />Healthcare App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/professional.png" class="icon-spacing" alt="Professional Services App Development Icon" />Professional Services App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/fitness.png" class="icon-spacing" alt="Fitness App Development Icon" />Fitness App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/pet.png" class="icon-spacing" alt="Pet Care App Development Icon" />Pet Care App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/saloon.png" class="icon-spacing" alt="Beauty and Salon App Development Icon" />Beauty and Salon App</a></li>
								<li><a href="#"><img src="images/icons/ondemand/lms.png" class="icon-spacing" alt="Learning and Education App Development Icon" />Learning and Education App</a></li>

							</ul>
						</li>
						<li>
							<a href="#">Marketplace</a>
							<ul>
								<li><a href="#"><img src="images/icons/marketplace/marketplace.png" class="icon-spacing" alt="General Marketplaces App Development Icon" />General Marketplaces</a></li>
								<li><a href="#"><img src="images/icons/marketplace/handmade.png" class="icon-spacing" alt="Handmade or Vintage Goods App Development Icon" />Handmade or Vintage Goods</a></li>
								<li><a href="#"><img src="images/icons/marketplace/freelancer.png" class="icon-spacing" alt="Freelance Services App Development Icon" />Freelance Services</a></li>

								<li><a href="#"><img src="images/icons/marketplace/realestate.png" class="icon-spacing" alt="Real Estate App Development Icon" />Real Estate</a></li>
								<li><a href="#"><img src="images/icons/marketplace/fashion.png" class="icon-spacing" alt="Fashion and Apparel App Development Icon" />Fashion and Apparel</a></li>
								<li><a href="#"><img src="images/icons/marketplace/automotive.png" class="icon-spacing" alt="Automotive App Development Icon" />Automotive</a></li>

								<li><a href="#"><img src="images/icons/marketplace/education.png" class="icon-spacing" alt="Education App Development Icon" />Education</a></li>
								<li><a href="#"><img src="images/icons/marketplace/localdelivery.png" class="icon-spacing" alt="Local Services App Development Icon" />Local Services</a></li>

							</ul>
						</li>
					</ul>
				</li>
				<li class="sbmenu rpdropdown">
					<a href="#" class="menu-links">Portfolio</a>

				</li>
				<li class=" rpdropdown">
					<a href="about" class="menu-links">About Us</a>

				</li>
				<li class=" rpdropdown">
					<a href="careers" class="menu-links">Careers</a>

				</li>
			</ul>
			<ul class="bottom-nav">
				<li class="prb">
					<a href="https://wa.link/fucwsq" target="_blank" aria-label="Contact us on WhatsApp">
						<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 384 384">
							<path
								d="M353.188,252.052c-23.51,0-46.594-3.677-68.469-10.906c-10.719-3.656-23.896-0.302-30.438,6.417l-43.177,32.594
						  c-50.073-26.729-80.917-57.563-107.281-107.26l31.635-42.052c8.219-8.208,11.167-20.198,7.635-31.448
						  c-7.26-21.99-10.948-45.063-10.948-68.583C132.146,13.823,118.323,0,101.333,0H30.813C13.823,0,0,13.823,0,30.813
						  C0,225.563,158.438,384,353.188,384c16.99,0,30.813-13.823,30.813-30.813v-70.323C384,265.875,370.177,252.052,353.188,252.052z"></path>
						</svg>
					</a>
				</li>
				<li class="prb">
					<a href="/cdn-cgi/l/email-protection#1e6d71737b697b7c737b7a777f5e79737f7772307d7173" aria-label="Contact us via email">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24">
							<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
							<path d="M0 0h24v24H0z" fill="none"></path>
						</svg>
					</a>
				</li>
				<li class="prb">
					<a href="#" aria-label="Contact us via phone">
						<svg enable-background="new 0 0 24 24" height="18" viewbox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">
							<path d="m23.309 14.547c1.738-7.81-5.104-14.905-13.139-13.543-4.362-2.707-10.17.352-10.17 5.542 0 1.207.333 2.337.912 3.311-1.615 7.828 5.283 14.821 13.311 13.366 5.675 3.001 11.946-2.984 9.086-8.676zm-7.638 4.71c-2.108.867-5.577.872-7.676-.227-2.993-1.596-3.525-5.189-.943-5.189 1.946 0 1.33 2.269 3.295 3.194.902.417 2.841.46 3.968-.3 1.113-.745 1.011-1.917.406-2.477-1.603-1.48-6.19-.892-8.287-3.483-.911-1.124-1.083-3.107.037-4.545 1.952-2.512 7.68-2.665 10.143-.768 2.274 1.76 1.66 4.096-.175 4.096-2.207 0-1.047-2.888-4.61-2.888-2.583 0-3.599 1.837-1.78 2.731 2.466 1.225 8.75.816 8.75 5.603-.005 1.992-1.226 3.477-3.128 4.253z"></path>
						</svg>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</header>