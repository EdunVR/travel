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

$show_gallery   = isset( $args['show_gallery'] ) ? $args['show_gallery'] : 'yes';

$arr_image_ids  = tripgo_get_gallery_ids( $product_id );
$data_gallery   = array();

if ( isset( $args['data_options'] ) ) {
    $data_options = $args['data_options'];
} else {
    $data_options   = apply_filters( 'ft_wc_slideshow_options', array(
        'items'                 => 3,
        'slideBy'               => 1,
        'margin'                => 24,
        'autoplayHoverPause'    => true,
        'loop'                  => true,
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

$thumbnail_size = 'tripgo_product_slider';
if ( isset( $args['thumbnail_size'] ) && $args['thumbnail_size'] ) {
    $thumbnail_size = $args['thumbnail_size'];
}

$gallery_size = apply_filters( 'ovabrw_ft_gallery_product_size', $thumbnail_size );

?>

<?php if ( $arr_image_ids && is_array( $arr_image_ids ) && $show_gallery == 'yes' ): ?>
    <div class="ova-gallery-popup">
        <div class="ova-gallery-slideshow owl-carousel owl-theme" data-options="<?php echo esc_attr( json_encode( $data_options ) ); ?>">
            <?php foreach( $arr_image_ids as $k => $img_id ):
                $gallery_url = wp_get_attachment_url( $img_id );
                $gallery_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

                if ( !$gallery_alt ) {
                    $gallery_alt = get_the_title( $img_id );
                }

                array_push( $data_gallery , array(
                    'src'       => $gallery_url,
                    'caption'   => $gallery_alt,
                    'thumb'     => $gallery_url,
                ));
            ?>
                <div class="item">
                    <a class="gallery-fancybox" data-index="<?php echo esc_attr( $k ); ?>" href="javascript:void(0)">
                        <?php echo wp_get_attachment_image( $img_id, $gallery_size ); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" class="ova-data-gallery" data-gallery="<?php echo esc_attr( json_encode( $data_gallery ) ); ?>">
    </div>
<?php endif; ?>