<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product    = wc_get_product( $product_id );

if ( ! $product || ! $product->is_type('ovabrw_car_rental') ) return;

$terms_conditions   = get_option( 'ova_brw_booking_form_terms_conditions', '' );
$terms_content      = get_option( 'ova_brw_booking_form_terms_conditions_content', '' );

?>
<div class="ova-booking-form" id="booking-form">
    <form 
        class="form booking-form" 
        action="<?php home_url('/'); ?>" 
        method="post" 
        enctype="multipart/form-data">
        <div class="ovabrw-form-container">
            <?php
                /**
                 * Hook: tripgo_booking_form
                 * @hooked: tripgo_booking_form_dates - 5
                 * @hooked: tripgo_booking_form_guests - 5
                 * @hooked: tripgo_booking_form_extra_fields - 10
                 * @hooked: tripgo_booking_form_quantity - 10
                 * @hooked: tripgo_booking_form_resources - 15
                 * @hooked: tripgo_booking_form_services - 15
                 * @hooked: tripgo_booking_form_deposit - 20
                 * @hooked: tripgo_booking_form_ajax_total - 25
                 */
                do_action( 'tripgo_booking_form', array( 'id' => $product_id ) );
            ?>
        </div>
        <?php if ( $terms_conditions === 'yes' && $terms_content ): ?>
            <div class="terms-conditions">
                <label>
                    <input
                        type="checkbox"
                        name="ovabrw-booking-terms-conditions"
                        value="yes"
                        data-error="<?php esc_attr_e( 'Please read and accept the terms and conditions to continue.', 'ova-brw' ); ?>"
                    />
                    <span class="terms-conditions-content">
                        <?php echo $terms_content; ?>
                        <span class="terms-conditions-required">*</span>
                    </span>
                </label>
            </div>
        <?php endif; ?>
        <?php if ( get_option( 'ova_brw_recapcha_enable', 'no' ) === 'yes' && ovabrw_get_recaptcha_form( 'booking' ) ): ?>
            <div id="ovabrw-g-recaptcha-booking"></div>
            <input
                type="hidden"
                id="ovabrw-recaptcha-booking-token"
                value=""
                data-mess="<?php esc_attr_e( 'Requires reCAPTCHA', 'tripgo' ); ?>"
                data-error="<?php esc_attr_e( 'reCAPTCHA response error, please try again later', 'tripgo' ); ?>"
                data-expired="<?php esc_attr_e( 'reCAPTCHA response expires, you needs to re-verify', 'tripgo' ); ?>"
            />
        <?php endif; ?>
        <div class="ajax-error"></div>
        <button type="submit" class="booking-form-submit">
            <?php esc_html_e( 'Booking Now', 'tripgo' ); ?>
            <span class="ovabrw-submit-loading">
                <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
            </span>
        </button>
        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
        <input type="hidden" name="custom_product_type" value="ovabrw_car_rental" />
        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" />
        <input type="hidden" name="quantity" value="1" />
        <input
            type="hidden"
            name="qty-by-guests"
            value="<?php echo esc_attr( ovabrw_qty_by_guests( $product_id ) ); ?>"
        />
    </form>
</div>