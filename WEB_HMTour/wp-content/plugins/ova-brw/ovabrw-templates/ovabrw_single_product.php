<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
		
// Get product_id from do_action - use when insert shortcode
if ( isset( $args['id'] ) && $args['id'] ) {
	$pid = $args['id'];
} else {
	$pid = get_the_id();
}

$product 	= wc_get_product( $pid );
$template 	= ovabrw_get_product_template( $pid );

get_header( 'shop' ); ?>

	<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>

			<?php 
				// if the product type isn't ovabrw_car_rental
				if( ! $product || $product->get_type() !== 'ovabrw_car_rental' ){

					wc_get_template_part( 'content', 'single-product' );

				}else{  
					do_action( 'woocommerce_before_single_product' );
					echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template );
					do_action( 'woocommerce_after_single_product' );

				}

		endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
		
	?>

<?php
get_footer( 'shop' );