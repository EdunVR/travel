<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product 	= wc_get_product( $product_id );
$template 	= ovabrw_get_product_template( $product_id );

get_header( 'shop' ); 

?>

	<?php
		/**
		 * tripgo_wc_before_main_content hook.
		 */
		do_action( 'tripgo_wc_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post();
			if ( 'default' === $template ) {
				wc_get_template_part( 'rental/content', 'single-product' );
			} else {
				// WPML
				$template = apply_filters( 'wpml_object_id', $template, 'elementor_library', true );

				do_action( 'woocommerce_before_single_product' );
				echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template );
				do_action( 'woocommerce_after_single_product' );
			}
		endwhile; // end of the loop. ?>

	<?php
		/**
		 * tripgo_wc_after_main_content hook.
		 */
		do_action( 'tripgo_wc_after_main_content' );
	?>

<?php 
get_footer( 'shop' );