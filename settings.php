<?php
	
	class Variations_Product_Settings {
		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;
		
		/**
		 * @return array ['hide_test' => 1]
		 */
		static function getSettings() {
			return get_option( 'variations_product_options' );
		}
		
		/**
		 * Start up
		 */
		public function __construct() {
			add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
			add_action( 'admin_init', [ $this, 'page_init' ] );
		}
		
		/**
		 * Add options page
		 */
		public function add_plugin_page() {
			add_submenu_page( 'woocommerce', 'Product Variations', 'Product Variations', 'manage_options', 'variations-on-product', [
				$this,
				'create_admin_page',
			] );
		}
		
		/**
		 * Options page callback
		 */
		public function create_admin_page() {
			// Set class property
			$this->options = get_option( 'variations_product_options' );
			?>
			<div class="wrap">
				<h1>Display Variations on Product page</h1>
				<form method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields( 'variations_product_options_group' );
						do_settings_sections( 'variations_product_page' );
						submit_button();
					?>
				</form>
			</div>
			<?php
		}
		
		/**
		 * Register and add settings
		 */
		public function page_init() {
			register_setting(
				'variations_product_options_group', // Option group
				'variations_product_options', // Option name
				[ $this, 'sanitize' ] // Sanitize
			);
			
			add_settings_section(
				'setting_section_id', // ID
				'Settings', // Title
				[ $this, 'print_section_info' ], // Callback
				'variations_product_page' // Page
			);
			
			add_settings_field(
				'enabled',
				'Enable',
				function () {
					printf(
						'<input type="checkbox" name="variations_product_options[enabled]" %s />',
						isset( $this->options['enabled'] ) ? 'checked' : ''
					);
				},
				'variations_product_page',
				'setting_section_id'
			);
			add_settings_field(
				'per_page',
				'Per page',
				function () {
					printf(
						'<input type="number" id="per_page" name="variations_product_options[per_page]" value="%s" />',
						isset( $this->options['per_page'] ) ? esc_attr( $this->options['per_page'] ) : ''
					);
				},
				'variations_product_page',
				'setting_section_id'
			);
			add_settings_field(
				'hide_stock_status',
				'Hide stock status',
				function () {
					printf(
						'<input type="checkbox" name="variations_product_options[hide_stock_status]" %s />',
						isset( $this->options['hide_stock_status'] ) ? 'checked' : ''
					);
				},
				'variations_product_page',
				'setting_section_id'
			);
			add_settings_field(
				'hide_qty',
				'Hide quantity field',
				function () {
					printf(
						'<input type="checkbox" name="variations_product_options[hide_qty]" %s />',
						isset( $this->options['hide_qty'] ) ? 'checked' : ''
					);
				},
				'variations_product_page',
				'setting_section_id'
			);
			add_settings_field(
				'hide_thumbnail',
				'Hide Thumbnail',
				function () {
					printf(
						'<input type="checkbox" name="variations_product_options[hide_thumbnail]" %s />',
						isset( $this->options['hide_thumbnail'] ) ? 'checked' : ''
					);
				},
				'variations_product_page',
				'setting_section_id'
			);
			add_settings_field(
				'load_more_text',
				'Load more text',
				function () {
					printf(
						'<input type="text" id="load_more_text" name="variations_product_options[load_more_text]" value="%s" />',
						isset( $this->options['load_more_text'] ) ? esc_attr( $this->options['load_more_text'] ) : 'Load more'
					);
				},
				'variations_product_page',
				'setting_section_id'
			);
			add_settings_field(
				'hide_product_addtocart',
				'Hide Product AddToCart',
				function () {
					printf(
						'<input type="checkbox" name="variations_product_options[hide_product_addtocart]" %s />',
						isset( $this->options['hide_product_addtocart'] ) ? 'checked' : ''
					);
				},
				'variations_product_page',
				'setting_section_id'
			);
		}
		
		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 *
		 * @return array
		 */
		public function sanitize( $input ) {
//			$new_input = array();
//			if( isset( $input['id_number'] ) )
//				$new_input['id_number'] = absint( $input['id_number'] );
//
//			if( isset( $input['title'] ) )
//				$new_input['title'] = sanitize_text_field( $input['title'] );
			
			return $input;
		}
		
		/**
		 * Print the Section text
		 */
		public function print_section_info() {
			print '';
		}
		
		/**
		 * Get the settings option array and print one of its values
		 */
		public function id_number_callback() {
			printf(
				'<input type="text" id="id_number" name="variations_product_options[id_number]" value="%s" />',
				isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number'] ) : ''
			);
		}
		
		/**
		 * Get the settings option array and print one of its values
		 */
		public function title_callback() {
			printf(
				'<input type="text" id="title" name="variations_product_options[title]" value="%s" />',
				isset( $this->options['title'] ) ? esc_attr( $this->options['title'] ) : ''
			);
		}
	}
	
	if ( is_admin() ) {
		$variations_product_settings = new Variations_Product_Settings();
		$_GLOBALS['variations_product_settings'] = $variations_product_settings;
	}