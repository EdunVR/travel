<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit();

$all_ids = ovabrw_get_all_id_product();

if( isset( $args['id'] ) && $args['id'] != '' ) {

    $product_id  = ( in_array( $args['id'], $all_ids ) == true && in_array( get_the_id(), $all_ids ) == false ) ? $args['id'] : get_the_id();

} elseif( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id  = $all_ids[0];

} else {
    $product_id  = get_the_id();
}

$product    = wc_get_product( $product_id );
$map_type   = get_post_meta( $product_id, 'ovabrw_map_type', true );
$address    = get_post_meta( $product_id, 'ovabrw_address', true );
$latitude   = get_post_meta( $product_id, 'ovabrw_latitude', true );
$longitude  = get_post_meta( $product_id, 'ovabrw_longitude', true );
$map_iframe = get_post_meta( $product_id, 'ovabrw_map_iframe', true );
$zoom       = get_option( 'ova_brw_zoom_map_default', 17 );

if ( ! $map_type ) {
    $map_type = 'api';
}

if ( $map_type == 'iframe' ) $address = true;

$show_map = isset( $args['show_map'] ) ? $args['show_map'] : 'yes';

?>
    

<?php if ( $show_map == 'yes' ) {  ?>

    <?php if ( $map_type == 'api' ) {  ?>
        <?php if ( ! empty( $address ) ) {  ?>
            <div class="content-product-item tripgo-tour-map" id="ova-tour-map">
                <div class="heading-map">
                    <h2 class="title-tour-map">
                        <?php esc_html_e( 'Tour Map', 'tripgo' ); ?>
                    </h2>
                   
                    <input type="hidden" class="address" data-zoom="<?php echo esc_attr( $zoom ); ?>" latitude="<?php echo esc_attr( $latitude ); ?>" longitude="<?php echo esc_attr( $longitude ); ?>"/>
                    <input type="hidden" class="pac-input" name="pac-input" id="pac-input" value="<?php echo esc_attr($address); ?>" autocomplete="off" autocapitalize="none">
                </div>
                
                <div id="tour-show-map" class="tour-show-map"></div>
            </div>
        <?php } ?>
    <?php } elseif($map_type == 'iframe') { ?>
        <?php if ( $map_iframe != '' ) { ?>
            <div class="content-product-item tripgo-tour-map" id="ova-tour-map">
                <div class="heading-map">
                    <h2 class="title-tour-map">
                        <?php esc_html_e( 'Tour Map', 'tripgo' ); ?>
                    </h2>
                </div>
                <?php echo $map_iframe; ?>
            </div>
        <?php } ?>
    <?php } ?>

<?php } ?>