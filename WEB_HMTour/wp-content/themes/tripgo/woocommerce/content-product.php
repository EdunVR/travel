<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

?>

<div <?php wc_product_class( 'ova-product', $product ); ?>>
	<?php
	/**
	 * Hook: tripgo_wc_before_shop_loop_item.
	 */
	do_action( 'tripgo_wc_before_shop_loop_item' );

	/**
	 * Hook: tripgo_wc_loop_item.
	 */
	do_action( 'tripgo_wc_loop_item' );

	/**
	 * Hook: tripgo_wc_after_shop_loop_item.
	 */
	do_action( 'tripgo_wc_after_shop_loop_item' );
	?>
</div>