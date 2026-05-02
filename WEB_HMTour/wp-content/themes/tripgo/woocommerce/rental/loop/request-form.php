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

if ( ! $product || !$product->is_type('ovabrw_car_rental') ) return;

$title = $product->get_title();

// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
    $first_day = 0;
}

// Total Day
$total_day = get_post_meta( $product_id, 'ovabrw_number_days', true );
if ( !$total_day ) {
    $total_day = 1;
}

// Duration
$duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

// Get booked time
$statuses   = brw_list_order_status();
$order_time = ovabrw_get_disabled_dates( $product_id, $statuses );

$placeholder_date   = ovabrw_get_placeholder_date();
$label_time         = esc_html__( 'Choose time', 'tripgo' );

$check_in_default   = ovabrw_get_current_date_from_search( 'pickup_date', $product_id );
$check_out_default  = ovabrw_get_current_date_from_search( 'dropoff_date', $product_id );

$fixed_time_check_in    = get_post_meta( $product_id, 'ovabrw_fixed_time_check_in', true );
$fixed_time_check_out   = get_post_meta( $product_id, 'ovabrw_fixed_time_check_out', true );

$readonly = $check_in_class = '';
if ( ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) && ! $duration ) {
    $readonly = 'readonly';
    $check_in_class = ' ovabrw_readonly';
}

// Show/Hide checkout field
$hide_checkout  = '';
$checkout_field = get_post_meta( $product_id, 'ovabrw_manage_checkout_field', true );
$show_date      = get_option( 'ova_brw_request_booking_form_show_dates', 'yes' );

if ( !$checkout_field ) $checkout_field = 'global';
if ( $checkout_field === 'global' ) {
    if ( $show_date != 'yes' ) {
        $hide_checkout = ' ovabrw-hide-field';
    }
} elseif ( $checkout_field === 'hide' ) {
    $hide_checkout = ' ovabrw-hide-field';
} else {
    $hide_checkout = '';
}

// Terms & conditions
$terms_conditions   = get_option( 'ova_brw_request_booking_terms_conditions', '' );
$terms_content      = get_option( 'ova_brw_request_booking_terms_conditions_content', '' );

?>

<div class="ova-request-form" id="request-form">
    <form 
        class="form request-form" 
        action="<?php echo home_url('/'); ?>" 
        method="post" 
        enctype="multipart/form-data">

        <div class="ovabrw-form-container">
            <div class="rental_item"> 
                <label>
                    <?php esc_html_e( 'Name *', 'tripgo' ); ?>
                </label>
                <input 
                    type="text" 
                    class="required" 
                    name="name" 
                    placeholder="<?php esc_html_e( 'Your name', 'tripgo' ); ?> "
                    data-error="<?php esc_html_e( 'Name is required.', 'tripgo' ); ?>" />
            </div>
            <div class="rental_item"> 
                <label>
                    <?php esc_html_e( 'Email *', 'tripgo' ); ?>
                </label>
                <input 
                    type="text" 
                    class="required" 
                    name="email" 
                    placeholder="<?php esc_html_e( 'example@gmail.com', 'tripgo' ); ?> "
                    data-error="<?php esc_html_e( 'Email is required.', 'tripgo' ); ?>" />
            </div>
            <?php if ( 'yes' === ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_number', 'yes') ) ): ?>
                <div class="rental_item"> 
                    <label>
                        <?php esc_html_e( 'Phone *', 'tripgo' ); ?>
                    </label>
                    <input 
                        type="text" 
                        class="required" 
                        name="phone" 
                        placeholder="<?php esc_html_e( '(229) 555-2872', 'tripgo' ); ?>" 
                        data-error="<?php esc_html_e( 'Phone is required.', 'tripgo' ); ?>" />
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_address', 'yes') ) ): ?>
                <div class="rental_item"> 
                    <label>
                        <?php esc_html_e( 'Address *', 'tripgo' ); ?>
                    </label>
                    <input 
                        type="text" 
                        class="required" 
                        name="address" 
                        placeholder="<?php esc_html_e( 'Your address', 'tripgo' ); ?>" 
                        data-error="<?php esc_html_e( 'Address is required.', 'tripgo' ); ?>" />
                </div>
            <?php endif; ?>
            <?php if ( ! $duration && ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ):
                $fixed_dates = function_exists( 'ovabrw_get_fixed_dates' ) ? ovabrw_get_fixed_dates( $product_id ) : '';
            ?>
                <div class="rental_item">
                    <label>
                        <?php echo esc_html( $label_time ); ?>
                    </label>
                    <select name="ovabrw_fixed_time" class="ovabrw_fixed_time">
                        <?php if ( !empty( $fixed_dates ) && is_array( $fixed_dates ) ): ?>
                            <?php foreach ( $fixed_dates as $date_range => $date_string ): ?>
                                <option value="<?php echo esc_attr( $date_range ); ?>">
                                    <?php echo esc_html( $date_string ); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value=""><?php esc_html_e( 'No time', 'tripgo' ); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <?php if ( 'yes' == $show_date ): ?>
                    <div class="rental_item ovabrw_checkin_field"> 
                        <label>
                            <?php esc_html_e( 'Check in *', 'tripgo' ); ?>
                        </label>
                        <input 
                            type="text" 
                            name="ovabrw_request_pickup_date"  
                            class="required ovabrw_datetimepicker ovabrw_start_date<?php echo esc_attr( $check_in_class ); ?>" 
                            placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
                            autocomplete="off" 
                            value="<?php echo esc_attr( $check_in_default ); ?>" 
                            data-firstday="<?php echo esc_attr( $first_day ); ?>" 
                            data-total-day="<?php echo esc_attr( $total_day ); ?>" 
                            data-order-time='<?php echo esc_attr( $order_time ); ?>' 
                            data-error="<?php esc_html_e( 'Check-in is required.', 'tripgo' ); ?>" 
                            data-readonly="<?php echo esc_attr( $readonly ); ?>"
                            <?php echo esc_html( $readonly ); ?>/>
                        <span class="ovabrw-date-loading">
                            <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
                        </span>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="rental_item ovabrw_checkin_field"> 
                    <label>
                        <?php esc_html_e( 'Check in *', 'tripgo' ); ?>
                    </label>
                    <input 
                        type="text" 
                        name="ovabrw_request_pickup_date"  
                        class="required ovabrw_datetimepicker ovabrw_start_date<?php echo esc_attr( $check_in_class ); ?>" 
                        placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
                        autocomplete="off" 
                        value="<?php echo esc_attr( $check_in_default ); ?>" 
                        data-firstday="<?php echo esc_attr( $first_day ); ?>" 
                        data-total-day="<?php echo esc_attr( $total_day ); ?>" 
                        data-order-time='<?php echo esc_attr( $order_time ); ?>' 
                        data-error="<?php esc_html_e( 'Check-in is required.', 'tripgo' ); ?>" 
                        data-readonly="<?php echo esc_attr( $readonly ); ?>"
                        <?php echo esc_html( $readonly ); ?>/>
                    <span class="ovabrw-date-loading">
                        <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
                    </span>
                </div>
            <?php endif; ?>
            <div class="rental_item ovabrw_checkout_field<?php echo esc_attr( $hide_checkout ); ?>"> 
                <label>
                    <?php esc_html_e( 'Check out *', 'tripgo' ); ?>
                </label>
                <input 
                    type="text" 
                    name="ovabrw_request_pickoff_date"  
                    class="required ovabrw_end_date" 
                    placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
                    autocomplete="off" 
                    value="<?php echo esc_attr( $check_in_default ); ?>" 
                    data-firstday="<?php echo esc_attr( $first_day ); ?>" 
                    data-total-day="<?php echo esc_attr( $total_day ); ?>" 
                    data-order-time='<?php echo esc_attr( $order_time ); ?>' 
                    data-error="<?php esc_html_e( 'Check-out is required.', 'tripgo' ); ?>" 
                    readonly />
                <span class="ovabrw-date-loading">
                    <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
                </span>
            </div>

            <?php wc_get_template( 'rental/loop/fields/guests.php', array( 'id' => $product_id ) ); ?>

            <?php if ( 'yes' === ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_ckf', 'yes' ) ) ): ?>
                <?php wc_get_template( 'rental/loop/fields/extra_fields.php', array( 'id' => $product_id, 'form' => 'request' ) ); ?>
            <?php endif; ?>

            <?php wc_get_template( 'rental/loop/fields/quantity.php', array( 'id' => $product_id, 'form' => 'request' ) ); ?>

            <?php if ( 'yes' === ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) ): ?>
                <?php wc_get_template( 'rental/loop/fields/resources.php', array( 'id' => $product_id, 'form' => 'request' ) ); ?>
            <?php endif; ?>

            <?php if ( 'yes' === ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) ): ?>
                <?php wc_get_template( 'rental/loop/fields/services.php', array( 'id' => $product_id ) ); ?>
            <?php endif; ?>
            
            <?php if ( 'yes' === ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) ): ?>
                <div class="rental_item">
                    <textarea name="extra" cols="50" rows="5" placeholder="<?php esc_html_e( 'Extra Information', 'tripgo' ); ?>"></textarea>
                </div>
            <?php endif; ?>
        </div>
        <?php if ( $terms_conditions === 'yes' && $terms_content ): ?>
            <div class="terms-conditions">
                <label>
                    <input
                        type="checkbox"
                        name="ovabrw-request-booking-terms-conditions"
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
        <?php if ( get_option( 'ova_brw_recapcha_enable', 'no' ) === 'yes' && ovabrw_get_recaptcha_form( 'enquiry' ) ): ?>
            <div id="ovabrw-g-recaptcha-enquiry"></div>
            <input
                type="hidden"
                id="ovabrw-recaptcha-enquiry-token"
                value=""
                data-mess="<?php esc_attr_e( 'Requires reCAPTCHA', 'tripgo' ); ?>"
                data-error="<?php esc_attr_e( 'reCAPTCHA response error, please try again later', 'tripgo' ); ?>"
                data-expired="<?php esc_attr_e( 'reCAPTCHA response expires, you needs to re-verify', 'tripgo' ); ?>"
            />
        <?php endif; ?>
        <div class="ajax-error"></div>
        <button type="submit" class="request-form-submit">
            <?php esc_html_e( 'Send Now', 'tripgo' ); ?>
            <span class="ovabrw-submit-loading">
                <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
            </span>
        </button>
        <input type="hidden" name="product_name" value="<?php echo esc_html( $title ); ?>" />
        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
        <input type="hidden" name="request_booking" value="request_booking" />
        <input type="hidden" name="quantity" value="1" />
        <input
            type="hidden"
            name="qty-by-guests"
            value="<?php echo esc_attr( ovabrw_qty_by_guests( $product_id ) ); ?>"
        />
    </form>
</div>