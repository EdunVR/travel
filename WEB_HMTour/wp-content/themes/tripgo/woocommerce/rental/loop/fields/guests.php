<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();

$product = wc_get_product( $product_id );

if ( ! $product || !$product->is_type('ovabrw_car_rental') ) return;

$max_total_guest    = get_post_meta( $product_id, 'ovabrw_max_total_guest', true );

$adult_price        = tripgo_get_price_product( $product_id );
$max_adults         = get_post_meta( $product_id, 'ovabrw_adults_max', true );
$min_adults         = get_post_meta( $product_id, 'ovabrw_adults_min', true );
$show_price_adults  = get_option( 'ova_brw_booking_form_show_price_beside_adults', 'yes' );
$label_adults       = get_option( 'ova_brw_label_beside_adults', '' );

$children_price         = get_post_meta( $product_id, 'ovabrw_children_price', true );
$max_children           = get_post_meta( $product_id, 'ovabrw_childrens_max', true );
$min_children           = get_post_meta( $product_id, 'ovabrw_childrens_min', true );
$show_children          = get_option( 'ova_brw_booking_form_show_children', 'yes' );
$show_price_childrens   = get_option( 'ova_brw_booking_form_show_price_beside_childrens', 'yes' );
$label_childrens        = get_option( 'ova_brw_label_beside_childrens', '' );

$baby_price         = get_post_meta( $product_id, 'ovabrw_baby_price', true );
$max_babies         = get_post_meta( $product_id, 'ovabrw_babies_max', true );
$min_babies         = get_post_meta( $product_id, 'ovabrw_babies_min', true );
$show_baby          = get_option( 'ova_brw_booking_form_show_baby', 'no' );
$show_price_babies  = get_option( 'ova_brw_booking_form_show_price_beside_babies', 'yes' );
$label_babies       = get_option( 'ova_brw_label_beside_babies', '' );

$min_adults     = apply_filters( 'ovabrw_ft_min_adults', $min_adults, $product_id );
$min_children   = apply_filters( 'ovabrw_ft_min_children', $min_children, $product_id );
$min_babies     = apply_filters( 'ovabrw_ft_min_babies', $min_babies, $product_id );

if ( !$min_adults ) $min_adults = 0;
if ( !$min_children ) $min_children = 0;
if ( !$min_babies ) $min_babies = 0;

$number_adults      = isset( $_GET['ovabrw_adults'] ) ? $_GET['ovabrw_adults'] : $min_adults;
$number_children    = isset( $_GET['ovabrw_childrens'] ) ? $_GET['ovabrw_childrens'] : $min_children;
$number_babies      = isset( $_GET['ovabrw_babies'] ) ? $_GET['ovabrw_babies'] : $min_babies;
$gueststotal        = absint( $number_adults ) + absint( $number_children ) + absint( $number_babies );

$wrap_guestspicker_class = '';
if ( $show_children != 'yes' && $show_baby != 'yes' ) {
    $wrap_guestspicker_class = 'only-show-adults';
}

?>

<div class="rental_item">
    <label><?php esc_html_e( 'Guests', 'tripgo' ); ?></label>
    <div class="ovabrw-wrapper-guestspicker <?php echo esc_attr($wrap_guestspicker_class);?>">
        <input type="hidden" name="ovabrw_max_total_guest" value="<?php echo esc_attr( $max_total_guest ); ?>" />
        <div class="ovabrw-guestspicker">
            <div class="guestspicker">
                <span class="gueststotal"><?php echo esc_html( $gueststotal ); ?></span>
            </div>
        </div>

        <div class="ovabrw-guestspicker-content">
            <div class="guests-buttons">
                <div class="description">
                    <label><?php esc_html_e( 'Adults', 'tripgo' ); ?></label>
                    <?php if ( $label_adults ): ?>
                        <span class="guests-labels beside_adults">
                            <?php echo esc_html( $label_adults ); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ( $show_price_adults === 'yes' ): ?>
                        <span class="guests-price adults-price">
                            <?php echo wc_price( $adult_price['regular_price'] ); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="guests-button">
                    <div class="guests-icon minus">
                        <i aria-hidden="true" class="icomoon icomoon-minus"></i>
                    </div>
                    <input 
                        type="text" 
                        name="ovabrw_adults" 
                        class="required ovabrw_adults" 
                        value="<?php echo esc_attr( $number_adults ); ?>" 
                        min="<?php echo esc_attr( $min_adults ); ?>" 
                        max="<?php echo esc_attr( $max_adults ); ?>" 
                        data-error="<?php esc_html_e( 'Adults is required.', 'tripgo' ); ?>" 
                        readonly />
                    <div class="guests-icon plus">
                        <i aria-hidden="true" class="icomoon icomoon-plus"></i>
                    </div>
                </div>
            </div>

            <?php if ( $show_children === 'yes' ): ?>
                <div class="guests-buttons">
                    <div class="description">
                        <label><?php esc_html_e( 'Children', 'tripgo' ); ?></label>
                        <?php if ( $label_childrens ): ?>
                            <span class="guests-labels beside_childrens">
                                <?php echo esc_html( $label_childrens ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( $show_price_childrens === 'yes' ): ?>
                            <span class="guests-price childrens-price">
                                <?php echo ovabrw_wc_price( $children_price ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="guests-button">
                        <div class="guests-icon minus">
                            <i aria-hidden="true" class="icomoon icomoon-minus"></i>
                        </div>
                        <input 
                            type="text" 
                            name="ovabrw_childrens" 
                            class="ovabrw_childrens" 
                            value="<?php echo esc_attr( $number_children ); ?>" 
                            min="<?php echo esc_attr( $min_children ); ?>" 
                            max="<?php echo esc_attr( $max_children ); ?>" 
                            readonly />
                        <div class="guests-icon plus">
                            <i aria-hidden="true" class="icomoon icomoon-plus"></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( $show_baby === 'yes' ): ?>
                <div class="guests-buttons">
                    <div class="description">
                        <label><?php esc_html_e( 'Babies', 'tripgo' ); ?></label>
                         <?php if ( $label_babies ): ?>
                            <span class="guests-labels beside_babies">
                                <?php echo esc_html( $label_babies ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( $show_price_babies === 'yes' ): ?>
                            <span class="guests-price babies-price">
                                <?php echo ovabrw_wc_price( $baby_price ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="guests-button">
                        <div class="guests-icon minus">
                            <i aria-hidden="true" class="icomoon icomoon-minus"></i>
                        </div>
                        <input 
                            type="text" 
                            name="ovabrw_babies" 
                            class="ovabrw_babies" 
                            value="<?php echo esc_attr( $number_babies ); ?>" 
                            min="<?php echo esc_attr( $min_babies ); ?>" 
                            max="<?php echo esc_attr( $max_babies ); ?>" 
                            readonly />
                        <div class="guests-icon plus">
                            <i aria-hidden="true" class="icomoon icomoon-plus"></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>