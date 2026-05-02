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

// Get Product
$product  = wc_get_product($product_id);
if ( !$product || !$product->is_type('ovabrw_car_rental') ) return;

$args = [
    'posts_per_page' => 5,
    'orderby' => 'ID',
    'order' => 'DESC',
];

if ( isset( $args['data_options'] ) ) {
    $data_options = $args['data_options'];
} else {
    $data_options   = apply_filters( 'ft_wc_related_options', array(
        'items'                 => 4,
        'slideBy'               => 1,
        'margin'                => 24,
        'autoplayHoverPause'    => true,
        'loop'                  => false,
        'autoplay'              => true,
        'autoplayTimeout'       => 3000,
        'smartSpeed'            => 500,
        'autoWidth'             => false,
        'center'                => false,
        'lazyLoad'              => true,
        'dots'                  => true,
        'nav'                   => true,
        'rtl'                   => is_rtl() ? true: false,
        'nav_left'              => 'icomoon icomoon-angle-left',
        'nav_right'             => 'icomoon icomoon-angle-right',
    ));
}


// Get visible related products then sort them at random.
$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

// Handle orderby.
$related_products = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

?>

<?php if( !empty($related_products) && is_array($related_products) ) : ?>
    <div class="elementor-ralated-slide">
        <h3 class="related-title">
            <?php echo esc_html__('You May Like', 'tripgo'); ?>
        </h3>
        <div class="ova-product-slider elementor-ralated owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)) ?>">
            <?php foreach ( $related_products as $related_product ) {
                    $post_object = get_post( $related_product->get_id() );
                    setup_postdata( $GLOBALS['post'] =& $post_object );
                    wc_get_template_part( 'content', 'product' );
                }
                
                $post_object = get_post( $product->get_id() );
                setup_postdata( $GLOBALS['post'] =& $post_object );
            ?>
        </div>
    </div>  
<?php endif; ?>