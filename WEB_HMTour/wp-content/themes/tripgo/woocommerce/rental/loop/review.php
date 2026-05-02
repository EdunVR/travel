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

$product = wc_get_product( $product_id );

// ReviewX shortcode
$rvx_summary        =  '[rvx-summary product_id='.$product_id.']';
$rvx_list           =  '[rvx-review-list product_id='.$product_id.']'; 
$rvx_woo_reviews    =  '[rvx-woo-reviews product_id='.$product_id.']';

// Review
$product_review = array(
    'title'    => sprintf( __( 'Reviews (%d)', 'tripgo' ), $product->get_review_count() ),
    'priority' => 30,
    'callback' => 'comments_template',
);

$show_reviews = isset( $args['show_reviews'] ) ? $args['show_reviews'] : 'yes';

?>


<?php if( $show_reviews == 'yes') : ?>
    <?php if( is_singular('product') ) { ?>
        <div class="content-product-item ova-tour-review" id="ova-tour-review">
            <?php call_user_func( $product_review['callback'], 'reviews', $product_review ); ?>
        </div>
    <?php } else { ?>
        <div class="content-product-item ova-tour-review" id="ova-tour-review">
            <h4>
                <?php echo esc_html__('Reviews are only visible in a single product page','tripgo'); ?>
            </h4>
        </div>
    <?php } ?>
<?php endif; ?>