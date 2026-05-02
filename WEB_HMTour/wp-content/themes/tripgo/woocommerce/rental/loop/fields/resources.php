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

$ovabrw_rs_id 	= get_post_meta( $product_id, 'ovabrw_rs_id', true );
$form 			= isset( $args['form'] ) && $args['form'] ? $args['form'] : '';
$show_children  = get_option( 'ova_brw_booking_form_show_children', 'yes' );
$show_baby  	= get_option( 'ova_brw_booking_form_show_baby', 'yes' );

?>

<?php if ( $ovabrw_rs_id ): 
	$ovabrw_rs_name 			= get_post_meta( $product_id, 'ovabrw_rs_name', true );
	$ovabrw_rs_description 	    = get_post_meta( $product_id, 'ovabrw_rs_description', true );
	$ovabrw_rs_adult_price 		= get_post_meta( $product_id, 'ovabrw_rs_adult_price', true );
	$ovabrw_rs_children_price 	= get_post_meta( $product_id, 'ovabrw_rs_children_price', true );
	$ovabrw_rs_baby_price 		= get_post_meta( $product_id, 'ovabrw_rs_baby_price', true );
	$ovabrw_rs_duration_type 	= get_post_meta( $product_id, 'ovabrw_rs_duration_type', true );

?>
	<div class="ovabrw-resources rental_item">
		<label>
            <?php esc_html_e( 'Extra Services', 'tripgo' ); ?>
        </label>

		<?php foreach ( $ovabrw_rs_id as $k => $id ): 
			if ( $id ):
				$rs_name 			= isset( $ovabrw_rs_name[$k] ) ? $ovabrw_rs_name[$k] : '';
				$rs_description     = isset( $ovabrw_rs_description[$k] ) ? $ovabrw_rs_description[$k] : '';
				$rs_adult_price 	= isset( $ovabrw_rs_adult_price[$k] ) ? $ovabrw_rs_adult_price[$k] : 0;
				$rs_children_price 	= isset( $ovabrw_rs_children_price[$k] ) ? $ovabrw_rs_children_price[$k] : 0;
				$rs_baby_price 		= isset( $ovabrw_rs_baby_price[$k] ) ? $ovabrw_rs_baby_price[$k] : 0;
				$rs_duration_type 	= isset( $ovabrw_rs_duration_type[$k] ) ? $ovabrw_rs_duration_type[$k] : 'person';
		?>
			<div class="item">
				<div class="ovabrw-resource-title">
					<?php if ( 'request' === $form ): ?>
						<input 
							type="checkbox" 
							id="ovabrw_request_rs_checkbox_<?php echo esc_attr( $k ).""; ?>" 
							data-rs-key="<?php echo esc_attr( $id ); ?>" 
							name="ovabrw_rs_checkboxs[<?php echo esc_attr( $id ); ?>]" 
							value="<?php echo esc_attr( $rs_name ); ?>" 
							class="ovabrw_resource_checkboxs" />
						<span class="checkmark"></span>
						<label for="ovabrw_request_rs_checkbox_<?php echo esc_attr( $k); ?>">
							<?php echo esc_html( $rs_name ); ?>
							<?php if ($rs_description) echo wc_help_tip( $rs_description, true ); ?>
						</label>
					<?php else: ?>
						<input 
							type="checkbox" 
							id="ovabrw_rs_checkbox_<?php echo esc_attr( $k ); ?>" 
							data-rs-key="<?php echo esc_attr( $id ); ?>" 
							name="ovabrw_rs_checkboxs[<?php echo esc_attr( $id ); ?>]" 
							value="<?php echo esc_attr( $rs_name ); ?>" 
							class="ovabrw_resource_checkboxs" />
						<span class="checkmark"></span>
						<label for="ovabrw_rs_checkbox_<?php echo esc_attr( $k); ?>">
							<?php echo esc_html( $rs_name ); ?>
							<?php if ($rs_description) echo wc_help_tip( $rs_description, true ); ?>
						</label>
					<?php endif; ?>
				</div>
				<div class="ovabrw-resource-price">
					<div class="ovabrw-adult-price">
						<label>
							<?php echo esc_html__( 'Adult:', 'tripgo' ); ?>
						</label>
						<div class="ovabrw-guests-price">
							<span class="ovabrw-adult-amount">
								<?php echo ovabrw_wc_price( $rs_adult_price ); ?>
							</span>
							<?php if ( apply_filters( 'ft_resources_show_duration', true ) ): ?>
								<span class="ovabrw-rs-duration">
									<?php if ( 'person' === $rs_duration_type ): ?>
										<?php echo esc_html__( '/per person', 'tripgo' ); ?>
									<?php else: ?>
										<?php echo esc_html__( '/total', 'tripgo' ); ?>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						</div>
					</div>
					<?php if ( $show_children === 'yes' ): ?>
						<div class="ovabrw-children-price">
							<label>
								<?php echo esc_html__( 'Children:', 'tripgo' ); ?>
							</label>
							<div class="ovabrw-guests-price">
								<span class="ovabrw-children-amount">
									<?php echo ovabrw_wc_price( $rs_children_price ); ?>
								</span>
								<?php if ( apply_filters( 'ft_resources_show_duration', true ) ): ?>
									<span class="ovabrw-rs-duration">
										<?php if ( 'person' === $rs_duration_type ): ?>
											<?php echo esc_html__( '/per person', 'tripgo' ); ?>
										<?php else: ?>
											<?php echo esc_html__( '/total', 'tripgo' ); ?>
										<?php endif; ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( $show_baby === 'yes' ): ?>
						<div class="ovabrw-baby-price">
							<label>
								<?php echo esc_html__( 'Baby:', 'tripgo' ); ?>
							</label>
							<div class="ovabrw-guests-price">
								<span class="ovabrw-baby-amount">
									<?php echo ovabrw_wc_price( $rs_baby_price ); ?>
								</span>
								<?php if ( apply_filters( 'ft_resources_show_duration', true ) ): ?>
									<span class="ovabrw-rs-duration">
										<?php if ( 'person' === $rs_duration_type ): ?>
											<?php echo esc_html__( '/per person', 'tripgo' ); ?>
										<?php else: ?>
											<?php echo esc_html__( '/total', 'tripgo' ); ?>
										<?php endif; ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; endforeach; ?>
	</div>
<?php endif; ?>