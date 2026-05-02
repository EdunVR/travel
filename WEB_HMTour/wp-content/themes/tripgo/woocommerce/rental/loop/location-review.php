<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$all_ids = ovabrw_get_all_id_product();

if( isset( $args['id'] ) && $args['id'] != '' ) {

    $product_id  = ( in_array( $args['id'], $all_ids ) == true && in_array( get_the_id(), $all_ids ) == false ) ? $args['id'] : get_the_id();

} elseif( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id  = $all_ids[0];

} else {
    $product_id  = get_the_id();
}


$product = wc_get_product($product_id);

if ( ! $product ) return;

$address        = get_post_meta( $product_id, 'ovabrw_address', true );
$review_count   = $product->get_review_count();
$rating         = $product->get_average_rating();

$short_address  = get_post_meta( $product_id, 'ovabrw_short_address', true );
if( $short_address && !empty($short_address) ) {
    $address = $short_address;
}

// Wishlist
$wishlist = do_shortcode('[yith_wcwl_add_to_wishlist]');

// show fields
$show_location  = isset( $args['show_location'] ) ? $args['show_location']   : 'yes';
$show_rating    = isset( $args['show_rating'] )   ? $args['show_rating']     : 'yes';
$show_wishlist  = isset( $args['show_wishlist'] ) ? $args['show_wishlist']   : 'yes';

?>

<div class="ova-location-review">
    <?php if ( $address && $show_location == 'yes' ): ?>
        <div class="ova-product-location">
            <i aria-hidden="true" class="icomoon icomoon-location-2"></i>
            <a href="#ova-tour-map">
                <?php echo esc_html( $address ); ?>
            </a>
        </div>
    <?php endif; ?>
    <?php if ( wc_review_ratings_enabled() && $rating > 0 && $show_rating == 'yes' ): ?>
        <div class="ova-product-review">
            <div class="star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'tripgo' ), $rating ); ?>">
                <span class="rating-percent" style="width: <?php echo esc_attr( ( $rating / 5 ) * 100 ).'%'; ?>;"></span>
            </div>
            <a href="#reviews" class="woo-review-link" rel="nofollow">
                ( <?php printf( _n( '%s review', '%s reviews', $review_count, 'tripgo' ), esc_html( $review_count ) ); ?> )
            </a>
        </div>
    <?php endif; ?>
    <?php if ( '[yith_wcwl_add_to_wishlist]' != $wishlist && $show_wishlist == 'yes' ): ?>
        <div class="ova-single-product-wishlist">
            <?php echo $wishlist; ?>
        </div>
    <?php endif; ?>
</div>