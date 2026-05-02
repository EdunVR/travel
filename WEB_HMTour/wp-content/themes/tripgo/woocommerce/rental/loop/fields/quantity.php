<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product    = wc_get_product( $product_id );

if ( ! $product || ! $product->is_type('ovabrw_car_rental') ) return;

$max_quantity = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );

?>

<?php if ( 'yes' === get_option( 'ova_brw_booking_form_show_quantity', 'no' ) ): ?>
    <div class="rental_item">
        <label><?php esc_html_e( 'Quantity', 'tripgo' ); ?></label>
        <input 
            type="number" 
            class="required ovabrw-quantity" 
            name="ovabrw_quantity" 
            value="1" 
            min="1" 
            max="<?php echo esc_attr( $max_quantity ); ?>" 
            data-error="<?php esc_html_e( 'Quantity is required.', 'tripgo' ); ?>" />
    </div>
<?php endif; ?>