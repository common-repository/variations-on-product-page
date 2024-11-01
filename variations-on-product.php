<?php
	/*
		Plugin Name: Variations On Product Page
		Description: Display all the available variations on the product page
		Version: 1.2
	*/
	
	
	define( 'VARIATIONS_ON_PRODUCT_VER', '1.2' );
	define( 'VARIATIONS_ON_PRODUCT_PATH', plugin_dir_path( __FILE__ ) );
	define( 'VARIATIONS_ON_PRODUCT_URL', plugin_dir_url( __FILE__ ) );
	
	require_once 'settings.php';
	
	if ( ! isset( Variations_Product_Settings::getSettings()['enabled'] ) ) {
		return;
	}
	
	add_action( 'wp_enqueue_scripts', function () {
		wp_enqueue_script( 'variations-on-product', VARIATIONS_ON_PRODUCT_URL . 'assets/variations-on-product.js', [ 'jquery' ], VARIATIONS_ON_PRODUCT_VER );
		wp_localize_script( 'variations-on-product', 'variations_on_product_settings', apply_filters( 'variations_on_product_js_settings', [
			'options' => Variations_Product_Settings::getSettings(),
		] ) );
		wp_enqueue_style( 'variations-on-product', VARIATIONS_ON_PRODUCT_URL . 'assets/variations-on-product.css', [], VARIATIONS_ON_PRODUCT_VER );
	} );
	
	add_action( 'woocommerce_before_add_to_cart_button', function () {
		global $product;
		/* @var WC_Product $product */
		if( !$product->is_type('variable') ) return;
		?>
		<style>
			#product-variations .attachment-woocommerce_thumbnail {
				max-width: 100px;
			}

			table#product-variations .custom_form .quantity {
				float: none !important;
				width: 100% !important;
				margin-right: 0 !important;
			}

			table#product-variations .custom_form .input-text.qty {
				width: 100% !important;
				box-sizing: border-box;
			}

			table#product-variations .custom_form button.button.alt {
				width: 100%;
			}

			table#product-variations .custom_form p.stock {
				margin-bottom: 3px !important;
				text-align: center;
				font-size: 14px
			}
		</style>

		<table id="product-variations">
			<thead>
			<tr>
				<?php if( !Variations_Product_Settings::getSettings()['hide_thumbnail'] ): ?>
					<td>Image</td>
				<?php endif; ?>
				<td>Title</td>
				<td>Price</td>
				<td>*</td>
			</tr>
			</thead>
			<tbody></tbody>
		</table>
		<a href="#" class="button" style="width: 100%;text-align: center;" id="product-variations-load-more"><?=Variations_Product_Settings::getSettings()['load_more_text'] ?: 'Load more'?></a>
		
		<?php
	} );
	
	add_filter( 'woocommerce_available_variation', function ( $default, $product, $variation ) {
		
		/* @var WC_Product_Variation $variation */
		ob_start();
		?>
		<tr>
			
			<?php if( !Variations_Product_Settings::getSettings()['hide_thumbnail'] ): ?>
				<td>
					<?= $variation->get_image() ?>
				</td>
			<?php endif; ?>
			<td>
				<?= $variation->get_name() ?>
				<p>
					<small><?= $variation->get_sku() ?></small>
				</p>
			</td>
			<td>
				<?= $variation->get_price_html() ?>
			</td>
			<td>
				<?php if ( $variation->is_in_stock() ) : ?>
					<form class="custom_form" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
						<?php
							if( !Variations_Product_Settings::getSettings()['hide_stock_status'] ) {
								if ( $variation->managing_stock() ) {
									echo wc_get_stock_html( $variation );
								}
							}
						?>
						<?php
							if( !Variations_Product_Settings::getSettings()['hide_qty'] ) {
								woocommerce_quantity_input( [
									'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $variation->get_min_purchase_quantity(), $variation ),
									'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $variation->get_max_purchase_quantity(), $variation ),
									'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $variation->get_min_purchase_quantity(),
								] );
							}
						?>

						<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $variation->get_id() ); ?>" class=" button alt"><?php echo esc_html( $variation->single_add_to_cart_text() ); ?></button>

					</form>
				
				<?php endif; ?>
			</td>
		</tr>
		<?php
		$default['c_template'] = apply_filters('vop_variation_template', ob_get_clean(), $variation);
		
		return $default;
	}, 10, 3 );
	
	// Hide add to cart on product
	
	if ( isset( Variations_Product_Settings::getSettings()['hide_product_addtocart'] ) ) {
		add_action( 'woocommerce_before_add_to_cart_button', function () {
			?>
			<style>
				.single_variation_wrap {
					display: none !important;
				}
			</style>
			<?php
		} );
	}