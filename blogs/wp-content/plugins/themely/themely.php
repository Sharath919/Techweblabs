<?php
/*
Plugin Name: Themely Marketplace
Description: Beautiful, secure and easy-to-use WordPress themes from the most talented creators around the world. Each theme has passed a manual quality & security review by our team. Find, preview & select the perfect theme for your next project!
Version: 1.2.2
Requires at least: 4.8
Requires PHP: 5.6
Author: Themely
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: themely
Update URI: https://marketplace.themely.com
*/

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/**
* Themely Marketplace Methods
*/
if ( !class_exists( 'ThemelyMarketplace' ) ) {

	class ThemelyMarketplace {    

		/**
	    * Variables
	    */
	    var $page;
	    var $url;
	    var $curl;
	    var $contents;
	    var $json;
	    var $array;
	    var $theme;
	    var $main_page;

	    /**
	    * Constructor
	    */ 
		public function __construct() {

			//add_action( 'admin_init', array( $this, 'i18n' ) );
			add_action('admin_menu', array( $this, 'add_pages' ));
			$this->get_session_id();

		}

		/**
	    * Translation
	    */
	    /*
		public function i18n() {
			load_plugin_textdomain( 'themely', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}
		*/

		/**
	    * Enqueue styles
	    */
		public function load_styles() {
			wp_enqueue_style( 'themely-boostrap', plugins_url( 'css/bootstrap.min.css', __FILE__ ), null, '5.0.2', 'screen' );
			wp_enqueue_style( 'themely-style', plugins_url( 'css/style.css', __FILE__ ), null, '1.2.2', 'screen' );
		}

		/**
	    * Enqueue scripts
	    */
		public function load_scripts() {
			wp_enqueue_script( 'themely-bootstrap', plugins_url( 'js/bootstrap.min.js', __FILE__ ), array('jquery'), '5.0.2', 'screen' );
			wp_enqueue_script( 'themely-stripe', 'https://js.stripe.com/v3/', array('jquery'), null, 'screen' );
			wp_enqueue_script( 'themely-script', plugins_url( 'js/script.js', __FILE__ ), array('jquery'), '1.2.2', 'screen' );
			wp_localize_script( 'themely-script', 'ThemelyScript', array( 'pluginsUrl' => plugins_url('', __FILE__ ) ) );
		}

		/**
	    * Add menu
	    */
		public function add_pages() {
			$main_page = add_menu_page( __('Themely Marketplace', 'themely'), __('Themely Market', 'themely'), 'install_themes', 'themely', array( $this, 'marketplace_page' ), 'dashicons-layout', 59 );
			add_action( 'admin_print_styles-' . $main_page, array( $this, 'load_styles' ) );
			add_action( 'admin_print_scripts-' . $main_page, array( $this, 'load_scripts' ) );
		}

		/**
	    * Get directory contents
	    */
		public function curl_get_directory_contents() {
			$url = esc_url('https://d108fh6x7uy5wn.cloudfront.net/themes.json');
	        $curl = curl_init();
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_URL, $url);
	        $contents = curl_exec($curl);
	        curl_close($curl);
	        if ($contents) {
	        	return $contents;
	        }
	    }

	    /**
	    * Get number of themes in directory
	    */
	    public function get_directory_quantity() {
			$json = $this->curl_get_directory_contents();
			$array = json_decode($json, true);
			return count($array['themes']);
	    }

	    /**
	    * Get directory contents
	    */
		public function curl_get_marketplace_contents() {
			$url = esc_url('https://d2ck1uhkzo2shg.cloudfront.net/themes.json');
	        $curl = curl_init();
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_URL, $url);
	        $contents = curl_exec($curl);
	        curl_close($curl);
	        if ($contents) {
	        	return $contents;
	        }
	    }

	    /**
	    * Get number of themes in directory
	    */
	    public function get_marketplace_quantity() {
			$json = $this->curl_get_marketplace_contents();
			$array = json_decode($json, true);
			return count($array['themes']);
	    }

	    /**
	    * Get number of themes in directory
	    */
	    public function get_marketplace_categories() {
			$json = $this->curl_get_marketplace_contents();
			$data = json_decode($json, true);
			$categories = array();
			foreach ($data['themes'] as $theme) {
			    $array = explode(",", $theme['categories']);
			    $array = array_map('trim', $array);
			    $categories = array_merge($array, $categories);
			    sort($categories);
			};
			return array_unique($categories);
	    }

	    /**
	    * Display directory
	    */
		public function marketplace_page() {
			if ( !current_user_can( 'install_themes' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'themely' ) );
			}		
			?>
			<div class="wrap ocwpi-content">

				<div class="page-header">

					<h1 class="ocwpi-page-title"><?php _ex( 'Themely Marketplace', 'title of the main page', 'themely' ); ?></h1>

					<div class="row">

						<div class="col-sm-9 col-md-9 col-lg-9 col-xl-9 col-xxl-8 col-xxxl-7">

							<p class="lead text-muted text-wrap ocwpi-lead"><?php esc_html_e('Beautiful, secure and easy-to-use WordPress themes from the most talented creators around the world.', 'themely'); ?> <?php esc_html_e('Find, preview & select the perfect theme for your next project!', 'themely'); ?></p>

							<a type="button" class="button" data-bs-toggle="modal" data-bs-target="#supportModal">Support</a> <a type="button" class="button" data-bs-toggle="modal" data-bs-target="#faqModal">F.A.Q's</a> <a type="button" class="button" data-bs-toggle="modal" data-bs-target="#aboutModal">About</a> <a type="button" class="button" data-bs-toggle="modal" data-bs-target="#licensesModal">Licenses</a> <a type="button" class="button" data-bs-toggle="modal" data-bs-target="#refundsModal">Refunds</a>

						</div>

						<div class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-4 col-xxxl-5">

							<div class="ocwpi-badge"></div>

						</div>

					</div>

				</div>

	            <div class="page-body">

		            <div class="row form-group">

		              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

		                <h1 class="wp-heading-inline">

		                	<button id="premiumtoggle" type="button" class="premiumtoggle active me-2" title="Click to view premium themes">
		                		<span class="title-count ms-0 me-0" style="background-color: #2271b1;"><?php echo $this->get_marketplace_quantity(); ?></span> <?php esc_html_e( 'Premium Themes', 'themely' ); ?></span>
		                	</button>
		                	<button id="freetoggle" type="button" class="freetoggle" title="Click to view free themes">
		                		<span class="title-count ms-0 me-0" style="background-color:#ea4c89;"><?php echo $this->get_directory_quantity(); ?></span> <?php esc_html_e( 'Free Themes', 'themely' ); ?></span>
		                	</button>

		                </h1>

		              </div>

		              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 position-relative">

		              	<select id="searchcategory" class="category">
		              		<option value="" selected>All categories</option>
							<?php
								$array = $this->get_marketplace_categories();
								foreach ($array as $category) {
								 	echo "<option value='" . $category . "'>" . $category . "</option>";
								};
							?>
		              	</select>

		              </div>

		              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 position-relative">

		              	<input type="text" id="searchtermpremium" class="search" placeholder="<?php esc_attr_e( 'Enter search term...', 'themely' ); ?>">
		              	<input type="text" id="searchtermfree" class="search" style="display: none;" placeholder="<?php esc_attr_e( 'Enter search term...', 'themely' ); ?>">
			            <div id="searchspinner" class="spinner-border search-spinner"></div>

		              </div>

		            </div>

		            <div id="installresults"></div>

		            <input id="sessionid" name="sessionid" type="hidden" value="<?php echo $this->get_session_id(); ?>">

					<div id="searchresults" class="row mt-3">
		            
			            <?php require_once( plugin_dir_path( __FILE__ ) . '/inc/themes/premium.php' ); ?>

		            </div>

		            <div class="modal fade" id="themeModal" tabindex="-1" aria-labelledby="themeModalLabel" aria-hidden="true">
					  <div id="modalContent" class="modal-dialog modal-lg"></div>
					</div>

					<div class="modal fade" id="supportModal" tabindex="-1" aria-labelledby="supportModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
					    <div class="modal-content">
					      <div class="modal-header px-4">
					        <h5 class="modal-title" id="supportModalLabel">Support</h5>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <div class="modal-body py-4 px-4">
					        <h5>Theme Support</h5>
					    	Theme creators are required to provide free support for a minimum of 6 months from the date of purchase, though some creators provide longer periods. In the event you encounter an issue or have questions about your purchase you must contact the creator directly.
					    	<h6 class="mt-3">How to contact your theme creator</h6>
					    	<ul class="list-group list-group-flush">
					    		<li>&#8226; Log into your WordPress Admin Dashboard.</li>
					    		<li>&#8226; Navigate to <em>Appearance > Themes</em></li>
					    		<li>&#8226; Click the screenshot image for your theme</li>
					    		<li>&#8226; Click the creator's name which appears below the name of the theme.</li>
					    		<li>&#8226; You will be redirected to their website.</li>
					    		<li>&#8226; Locate their contact form, support forum or support ticket system to request assistance.</li>
					    		<li class="mb-0"><i>Please note: support does not include installation or customization</i>.</li>
					    	</ul>
					    	<h5 class="mt-4">Marketplace Support</h5>
					    	If you have a question or require assistance with our marketplace you can open a support ticket by submitting the form below or <a href="https://discord.gg/f3m2Pmp" target="_blank">chat live with our team</a> on our Discord server. We usually respond to all requests within 24 hours during normal business hours, 9am-5pm EST (GMT -4), Monday to Friday.
							<form id="ticketform" class="mt-4 p-3 border">
								<div class="row">
									<div class="col-md-12">
										<div id="ticketnotice"></div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<label for="ticketname" class="w-100 mb-1">Name</label>
										<input type="text" id="ticketname" name="ticketname" placeholder="Enter your name" class="w-100 border">
									</div>
									<div class="col-md-6">
										<label for="ticketemail" class="w-100 mb-1">Email</label>
										<input type="email" id="ticketemail" name="ticketemail" placeholder="Enter your email address" class="w-100 border">
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<label for="ticketmessage" class="w-100 mb-1 mt-2">Message</label>
										<textarea id="ticketmessage" name="ticketmessage" rows="5" placeholder="Ask a question or describe the issue you're experiencing" class="w-100 border"></textarea>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<button id="submitticket" name="submitticket" class="button button-primary button-hero mt-2 w-100">Submit Ticket</button>
									</div>
								</div>
							</form>
					      </div>
					    </div>
					  </div>
					</div>
					<div class="modal fade" id="faqModal" tabindex="-1" aria-labelledby="faqModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
					    <div class="modal-content">
					      <div class="modal-header px-4">
					        <h5 class="modal-title" id="faqModalLabel">Frequently Asked Questions</h5>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <div class="modal-body py-4 px-4">
					        <h6>What's the difference between standard and priority support?</h6>
					    	Standard support is provided for free themes and priority support is provided for premium (paid) themes. You will experience faster response times with priority support.
					    	<h6 class="mt-4">If I have a question or experience a problem with my theme, who should I contact?</h6>
					    	You must contact the theme creator directly. Click the Support button for details on how to contact your theme creator.
					    	<h6 class="mt-4">My theme creator isn't responding to my support requests, what should I do?</h6>
					    	Theme creators generally respond to support tickets during week days (Monday-Friday) and during their normal business hours. Please be patient as theme creators may not be in the same time zone as you. If you haven't received a response within 24-48 hours we suggest contacting them again. In the event a creator isn't responsive after you've contacted them a 2nd time, you may contact our support team to file a complaint.
					    	<h6 class="mt-4">How safe is my personal information when I make a purchase?</h6>
					    	We do not collect your personal information or credit card details except for your name and email address. All payments are processed through Stripe's secure, PCI compliant and SSL encrypted checkout system.
					    	<h6 class="mt-4">I'm having problems installing or purchasing a theme, who should I contact?</h6>
					    	We're responsible for the smooth operation of our marketplace, whether it's browsing themes, installing or making a purchase. If you experience an issue or encounter an error; contact our support team by clicking the Support button and submitting a support ticket.
					    	<h6 class="mt-4">What type of license do I receive with my theme?</h6>
					    	We offer 2 types of licenses depending on the theme. All free themes are licensed GNU GPL v2.0 and premium (paid) themes are licensed either GNU GPL v2.0 or under the Themely license. To determine which license is offered with the theme you want to purchase, click the theme's Details button to view a full description. For more information click the Licenses button.
					    	<h6 class="mt-4">What is your sales policy?</h6>
					    	You can request a refund within 90 days from the date of purchase if you are eligible. To determine if you're eligible click the Refunds button.
					      </div>
					    </div>
					  </div>
					</div>
					<div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
					    <div class="modal-content">
					      <div class="modal-header px-4">
					        <h5 class="modal-title" id="aboutModalLabel">About Themely Marketplace</h5>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <div class="modal-body py-4 px-4">
					    	Our mission is to <u>reduce the number of clicks required to build stunning WordPress sites</u>. Whether you're a first time user building a site for youself, or a freelancer/agency building a site for your client, we provide a quick and convenient experience to make your job easier.<br /><br/>
					    	We are based in Montreal, Canada with a remote team of talented individuals from all over the world who serve our customers in English, French and Spanish.<br /><br/>
					    	In collaboration with our partners, we provide our customers, high-quality, free and premium WordPress themes from the most talented creators around the world.
					      </div>
					    </div>
					  </div>
					</div>
					<div class="modal fade" id="licensesModal" tabindex="-1" aria-labelledby="licensesModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
					    <div class="modal-content">
					      <div class="modal-header px-4">
					        <h5 class="modal-title" id="licensesModalLabel">Theme Licenses</h5>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <div class="modal-body py-4 px-4">
					        <h5>Free Themes</h5>
					    	All free themes in our marketplace are licensed under the terms of the GNU General Public License, version 2.0 or later and free for personal and commercial use. <a href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank">Read the full terms</a>.
					    	<h5 class="mt-4">Premium Themes</h5>
					    	All premium (paid) themes in our marketplace are sold under the terms of either 1) the GNU General Public License, version 2.0 or later <u>OR</u> 2) our <u>Themely License</u>. To determine which license applies to the theme you want to purchase, click the theme's screenshot image to view a full description.
					    	<h6 class="mt-4 mb-3"><u>THEMELY LICENSE TERMS</u></h6>
					    	<h6 class="mt-4 mb-3">Things you CAN DO</h6>
				    		<p>&#8226; The license grants you, the purchaser, an ongoing, non-exclusive, worldwide license to make use of the theme.</p>
							<p>&#8226; You are licensed to use the theme to create one single website (end product), for yourself or for one client.</p>
							<p>&#8226; That website is an end product, created through customization within the theme, which requires an application of skill and effort to be created.</p>
							<p>&#8226; You can create one website (end product) for a client, and you can transfer that single website (end product) to your client for any fee. This license is then transferred to your client.</p>
							<p>&#8226; You can use the theme and can combine it with other works and plugins to create your single website (end product), which is subject to the terms of the license. You can do these things as long as the end product you then create is one that’s permitted by the license.</p>
							<h6 class="mt-4 mb-3">Things you CANNOT DO</h6>
				    		<p>&#8226; You can’t sell the website (end product) to more than one client.</p>
							<p>&#8226; You can’t re-distribute the theme as stock, in a tool or template, or with source files.</p>
							<p>&#8226; Although you can modify the theme and therefore change components before creating your single website (end product), you can’t extract and use a single component of the theme on a stand-alone basis.</p>
							<p>&#8226; You must not permit an end user of the website (end product) to extract the theme and use it separately from the website (end product).</p>
					      </div>
					    </div>
					  </div>
					</div>
					<div class="modal fade" id="refundsModal" tabindex="-1" aria-labelledby="refundsModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
					    <div class="modal-content">
					      <div class="modal-header px-4">
					        <h5 class="modal-title" id="refundsModalLabel">Refund Policy</h5>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <div class="modal-body py-4 px-4">
					    	Since WordPress themes are considered non-tangible or digital products, they cannot be returned. However, <u>you can request a refund within 90 days</u> from the date of purchase if you are eligible. 
					    	<h5 class="mt-4">Reasons ELIGIBLE for refund</h5>
					    	<ul class="list-group list-group-flush">
					    		<li>&#8226; Theme is not as described or doesn't function as described.</li>
					    		<li>&#8226; Theme support not provided by the creator.</li>
					    		<li class="mb-0">&#8226; Theme has security vulnerabilities.</li>					    		
					    	</ul>
					    	<h5 class="mt-4">Reasons INELIGIBLE for refund</h5>
					    	<ul class="list-group list-group-flush">
					    		<li>&#8226; It did not meet your expectations or you feel the theme is of low quality.</li>
					    		<li>&#8226; You changed your mind or decided the theme was no longer required.</li>
					    		<li>&#8226; You purchased a theme by mistake or didn't understand what you were purchasing.</li>
					    		<li>&#8226; You don't have sufficient expertise to use the theme.</li>
					    		<li class="mb-0">&#8226; You don't provide sufficient information as to why you are eligible to a refund.</li>
					    	</ul>
					    	<h5 class="mt-4">Steps to request a refund</h5>
					    	Contact the theme creator directly by email or support ticket and state the reasons why you are eligible for a refund. If the creator determines you are eligible they will forward your request to our support team and we will issue the refund immediately. Please note, once we issue the refund it will appear on your credit card approximately 5-10 business days later, depending upon the bank.
					    	<h5 class="mt-4">Dispute resolution</h5>
					    	In the event you and the theme creator can't come to an agreement about a refund you may open a dispute with our support team to investigate. You agree that any decision we make is final. To open a dispute, contact our support team by clicking the Support button and submit a support ticket. 
					      </div>
					    </div>
					  </div>
					</div>
	        	</div>
			</div>
			<?php
		}

		/**
	    * Get session id
	    */
	    public function get_session_id() {
			if ( isset( $_GET['session_id'] ) ) {
				$session_id = $_GET['session_id'];
				return $session_id;
			}
	    }

	}
	new ThemelyMarketplace;
}

/**
* THEMELY ONBOARDING METHODS
*/
if ( !class_exists( 'ThemelyOnboarding' ) ) {

	class ThemelyOnboarding {    

		/**
	    * Variables
	    */

	    /**
	    * Constructor
	    */ 
		public function __construct() {

			$this->onboarding();
        	return $this;

		}

		/**
		* Setup plugin constants
		*/
		private function constants() {

			// Plugin Folder Path
			if ( ! defined( 'THEMELY_DIR' ) ) {
				define( 'THEMELY_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'THEMELY_URL' ) ) {
				define( 'THEMELY_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Includes Path
			if ( ! defined( 'THEMELY_INCLUDES_DIR' ) ) {
				define( 'THEMELY_INCLUDES_DIR', THEMELY_DIR . 'inc/' );
			}

			// Plugin Includes URL
			if ( ! defined( 'THEMELY_INCLUDES_URL' ) ) {
				define( 'THEMELY_INCLUDES_URL', THEMELY_URL . 'inc/' );
			}

		}

		/**
		* Load onboarding files
		*/
		public function onboarding() {
			$this->constants();
			require_once THEMELY_INCLUDES_DIR . 'tgmpa/class-tgm-plugin-activation.php';
			require_once THEMELY_INCLUDES_DIR . 'merlin/vendor/autoload.php';
			require_once THEMELY_INCLUDES_DIR . 'merlin/class-merlin.php';
		}

	}
	new ThemelyOnboarding;
}

/**
* THEMELY UPDATE METHODS
*/
if( ! class_exists( 'ThemelyUpdate' ) ) {

	class ThemelyUpdate{

		public $plugin_slug;
		public $version;
		public $cache_key;
		public $old_cache_key;
		public $cache_allowed;

		public function __construct() {

			$this->plugin_slug = plugin_basename( __DIR__ );
			$this->version = '1.2.2';
			$this->cache_key = 'themely_update';
			$this->cache_allowed = true;

			add_filter( 'plugins_api', array( $this, 'info' ), 20, 3 );
			add_filter( 'site_transient_update_plugins', array( $this, 'update' ) );
			add_action( 'upgrader_process_complete', array( $this, 'purge' ), 10, 2 );

		}

		public function request() {

			$remote = get_transient( $this->cache_key );

			if( false === $remote || ! $this->cache_allowed ) {

				$remote = wp_remote_get(
					'https://themely-cwp.s3.amazonaws.com/info.json',
					array(
						'timeout' => 10,
						'headers' => array(
							'Accept' => 'application/json'
						)
					)
				);

				if(
					is_wp_error( $remote )
					|| 200 !== wp_remote_retrieve_response_code( $remote )
					|| empty( wp_remote_retrieve_body( $remote ) )
				) {
					return false;
				}

				set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );

			}

			$remote = json_decode( wp_remote_retrieve_body( $remote ) );

			return $remote;

		}


		public function info( $res, $action, $args ) {

			// print_r( $action );
			// print_r( $args );

			// do nothing if you're not getting plugin information right now
			if( 'plugin_information' !== $action ) {
				return false;
			}

			// do nothing if it is not our plugin
			if( $this->plugin_slug !== $args->slug ) {
				return false;
			}

			// get updates
			$remote = $this->request();

			if( ! $remote ) {
				return false;
			}

			$res = new stdClass();
			$res->name = $remote->name;
			$res->slug = $remote->slug;
			$res->version = $remote->version;
			$res->tested = $remote->tested;
			$res->requires = $remote->requires;
			$res->author = $remote->author;
			$res->author_profile = $remote->author_profile;
			$res->download_link = $remote->download_url;
			$res->trunk = $remote->download_url;
			$res->requires_php = $remote->requires_php;
			$res->last_updated = $remote->last_updated;

			$res->sections = array(
				'description' => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog' => $remote->sections->changelog
			);

			if( ! empty( $remote->banners ) ) {
				$res->banners = array(
					'low' => $remote->banners->low,
					'high' => $remote->banners->high
				);
			}

			return $res;

		}

		public function update( $transient ) {

			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$remote = $this->request();

			if(
				$remote
				&& version_compare( $this->version, $remote->version, '<' )
				&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<' )
				&& version_compare( $remote->requires_php, PHP_VERSION, '<' )
			) {
				$res = new stdClass();
				$res->slug = $this->plugin_slug;
				$res->plugin = plugin_basename( __FILE__ );
				$res->new_version = $remote->version;
				$res->tested = $remote->tested;
				$res->package = $remote->download_url;

				$transient->response[ $res->plugin ] = $res;

	    	}

			return $transient;

		}

		public function purge($upgrader_object, $options) {

			if (
				$this->cache_allowed
				&& 'update' === $options['action']
				&& 'plugin' === $options[ 'type' ]
			) {
				// just clean the cache when new plugin version is installed
				delete_transient( $this->cache_key );

			}

		}

	}

	new ThemelyUpdate();

}