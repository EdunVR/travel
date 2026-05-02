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

if ( !$product || !$product->is_type('ovabrw_car_rental') ) return;

$deposit_force 	= get_post_meta ( $product_id, 'ovabrw_force_deposit', true );
$deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
$deposit_type 	= get_post_meta ( $product_id, 'ovabrw_type_deposit', true );
$deposit_value 	= get_post_meta ( $product_id, 'ovabrw_amount_deposit', true );

?>

<?php if ( 'yes' === $deposit_enable ): ?>
	<div class="ovabrw-deposit rental_item">
		<div class="title-deposit">
			<span class=""><?php esc_html_e('Deposit Option', 'tripgo'); ?></span>
				<?php if ( 'percent' === $deposit_type ): ?>
					<span><?php echo esc_html( $deposit_value ).'%'; ?></span>
				<?php else: ?>
					<span><?php printf( ovabrw_wc_price( $deposit_value ) ); ?></span>
				<?php endif; ?>
			<span><?php esc_html_e('Per item', 'tripgo') ?></span>
		</div>
		<div class="ovabrw-type-deposit">
			<?php if ( $deposit_force == 'yes' ): ?>
				<input type="radio" id="ovabrw-pay-full" class="ovabrw-pay-full" name="ova_type_deposit" value="full" checked />
				<label class="ovabrw-pay-full" for="ovabrw-pay-full">
					<?php esc_html_e('Full Payment', 'tripgo') ?>
				</label>
				<input type="radio" id="ovabrw-pay-deposit" class="ovabrw-pay-deposit" name="ova_type_deposit" value="deposit" />
				<label class="ovabrw-pay-deposit" for="ovabrw-pay-deposit">
					<?php esc_html_e('Pay Deposit', 'tripgo') ?>
				</label>
			<?php else: ?>
				<input type="radio" id="ovabrw-pay-deposit" class="ovabrw-pay-deposit" name="ova_type_deposit" value="deposit" checked />
				<label class="ovabrw-pay-deposit" for="ovabrw-pay-deposit">
					<?php esc_html_e('Pay Deposit', 'tripgo') ?>
				</label>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>