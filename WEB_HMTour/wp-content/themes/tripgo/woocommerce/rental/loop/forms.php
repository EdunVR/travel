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


if ( ! $product_id ) return '';

// Loading reCAPTCHA
if ( function_exists( 'ovabrw_loading_reCAPTCHA' ) ) ovabrw_loading_reCAPTCHA();

$arr_price = tripgo_get_price_product( $product_id );

$show_booking_form = get_option( 'ova_brw_template_show_booking_form', 'yes' );
$show_enquiry_form = get_option( 'ova_brw_template_show_request_booking', 'yes' );

if ( $product_id ) {
    $forms_product = get_post_meta( $product_id, 'ovabrw_forms_product', true );

    if ( $forms_product === 'all' ) {
        $show_booking_form = $show_enquiry_form = 'yes';
    } elseif ( $forms_product === 'booking' ) {
        $show_booking_form = 'yes';
        $show_enquiry_form = '';
    } elseif ( $forms_product === 'enquiry' ) {
        $show_booking_form = '';
        $show_enquiry_form = 'yes';
    }
}

$show_form = isset( $args['show_form'] ) ? $args['show_form'] : 'yes';

?>

<?php if($show_form == 'yes') { ?>
    <div class="ova-forms-product">
        <div class="forms-wrapper">
            <div class="price-product">
                <div class="label">
                    <i aria-hidden="true" class="icomoon icomoon-tag"></i>
                    <span><?php esc_html_e( 'From', 'tripgo' ); ?></span>
                </div>
                <div class="price">
                    <span class="regular-price">
                        <?php echo wc_price( $arr_price['regular_price'] ); ?>
                    </span>
                    <?php if ( $arr_price['sale_price'] ): ?>
                        <span class="sale-price">
                            <?php echo wc_price( $arr_price['sale_price'] ); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="logo">
                <span class="line"></span>
                <i aria-hidden="true" class="<?php echo esc_attr( apply_filters( 'ovaft_logo_booking_form', 'icomoon icomoon-flig-outline' ) ); ?>"></i>
            </div>
            <div class="tabs">
                <?php if ( $show_booking_form == 'yes' ): ?>
                    <div class="item" data-id="#booking-form">
                        <?php esc_html_e( 'Booking Form', 'tripgo' ); ?>
                    </div>
                <?php endif; ?>
                <?php if ( $show_enquiry_form == 'yes' ): ?>
                    <div class="item" data-id="#request-form">
                        <?php esc_html_e( 'Enquiry Form', 'tripgo' ); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
                /* Booking Form */
                wc_get_template( 'rental/loop/booking-form.php', array( 'id' => $product_id ) );
                wc_get_template( 'rental/loop/request-form.php', array( 'id' => $product_id ) );
            ?>
        </div>
    </div>
<?php } ?>