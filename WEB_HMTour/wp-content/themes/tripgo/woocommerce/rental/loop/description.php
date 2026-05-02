<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$all_ids = ovabrw_get_all_id_product();

if ( isset( $args['id'] ) && $args['id'] != '' ) {
    $product_id = ( in_array( $args['id'], $all_ids ) == true && in_array( get_the_id(), $all_ids ) == false ) ? $args['id'] : get_the_id();
} elseif ( in_array( get_the_id(), $all_ids ) == false ) {
    $product_id  = $all_ids[0];
} else {
    $product_id  = get_the_id();
}

// Get elementor preview
$el_preview = isset( $_GET['elementor-preview'] ) ? $_GET['elementor-preview'] : '';

// Get tour description
$tour_description = apply_filters( 'woocommerce_short_description', get_post($product_id)->post_content );

if ( !empty( $tour_description ) ): ?>
    <div class="content-product-item tour-description" id="tour-description">
        <?php if ( $el_preview && wc_get_product( $el_preview ) ) {
            the_content();
        } else {
            echo wp_kses_post( $tour_description ); // WPCS: XSS ok.
        } ?>
    </div>
<?php endif; ?>
