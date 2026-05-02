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
	
$ovabrw_label_service 		= get_post_meta( $product_id, 'ovabrw_label_service', true );
$ovabrw_service_required 	= get_post_meta( $product_id, 'ovabrw_service_required', true );
$ovabrw_service_id 			= get_post_meta( $product_id, 'ovabrw_service_id', true );
$ovabrw_service_name 		= get_post_meta( $product_id, 'ovabrw_service_name', true );
$service_adult_price 		= get_post_meta( $product_id, 'ovabrw_service_adult_price', true );
$service_children_price 	= get_post_meta( $product_id, 'ovabrw_service_children_price', true );
$service_baby_price 		= get_post_meta( $product_id, 'ovabrw_service_baby_price', true );
$service_duration_type 		= get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

// Settings
$show_children  = get_option( 'ova_brw_booking_form_show_children', 'yes' );
$show_baby  	= get_option( 'ova_brw_booking_form_show_baby', 'yes' );

?>

<?php if( $ovabrw_label_service ): ?>
	<div class="ovabrw_services rental_item">
		<label>
            <?php esc_html_e( 'Services', 'tripgo' ); ?>
        </label>

		<?php for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ): 
			$label_group_service 	= $ovabrw_label_service[$i];
			$service_group_required = isset( $ovabrw_service_required[$i] ) ? $ovabrw_service_required[$i] : '';
			$required = '';

			if ( 'yes' === $service_group_required ) {
				$required = ' class="required"';
			}
		?>
			<div class="ovabrw_service_select">
				<select 
					name="ovabrw_service[]"<?php echo printf( $required ); ?> 
					data-error="<?php printf( esc_html__( '%s is required.', 'tripgo' ), $label_group_service ); ?>">
					<option value="">
						<?php printf( esc_html__( 'Select %s', 'tripgo' ), $label_group_service ); ?>
					</option>
					<?php if ( !empty( $ovabrw_service_id ) && isset( $ovabrw_service_id[$i] ) ): ?>
						<?php foreach( $ovabrw_service_id[$i] as $key => $value ): 
							$service_name 	= isset( $ovabrw_service_name[$i][$key] ) ? $ovabrw_service_name[$i][$key] : '';
							$adult_price 	= isset( $service_adult_price[$i][$key] ) ? $service_adult_price[$i][$key] : 0;
							$children_price = isset( $service_children_price[$i][$key] ) ? $service_children_price[$i][$key] : 0;
							$baby_price 	= isset( $service_baby_price[$i][$key] ) ? $service_baby_price[$i][$key] : 0;
							$duration_type 	= isset( $service_duration_type[$i][$key] ) ? $service_duration_type[$i][$key] : 'person';
							$html_duration 	= esc_html__( '/total', 'tripgo' );

							if ( 'person' === $duration_type ) {
								$html_duration = esc_html__( '/per person', 'tripgo' );
							}

							$html_price = '';
							
							if ( $show_children != 'yes' ) {
								$html_price = sprintf( esc_html__( ' (Adult: %s%s)', 'tripgo' ), ovabrw_wc_price($adult_price), $html_duration );
							}

							if ( $show_children != 'yes' && $show_baby != 'yes' ) {
								$html_price = sprintf( esc_html__( ' (Adult: %s%s)', 'tripgo' ), ovabrw_wc_price($adult_price), $html_duration );
							} elseif ( $show_children === 'yes' && $show_baby != 'yes' ) {
								$html_price = sprintf( esc_html__( ' (Adult: %s%s - Children:%s%s)', 'tripgo' ), ovabrw_wc_price($adult_price), $html_duration, ovabrw_wc_price($children_price), $html_duration );
							} elseif ( $show_children != 'yes' && $show_baby === 'yes' ) {
								$html_price = sprintf( esc_html__( ' (Adult: %s%s - Baby:%s%s)', 'tripgo' ), ovabrw_wc_price($adult_price), $html_duration, ovabrw_wc_price($baby_price), $html_duration );
							} else {
								$html_price = sprintf( esc_html__( ' (Adult: %s%s - Children:%s%s - Baby:%s%s)', 'tripgo' ), ovabrw_wc_price($adult_price), $html_duration, ovabrw_wc_price($children_price), $html_duration, ovabrw_wc_price($baby_price), $html_duration );
							}
						?>
							<option value="<?php echo esc_attr( $value ); ?>">
								<?php if ( apply_filters( 'ft_services_show_duration', true ) ): ?>
									<?php echo esc_html($service_name) . sprintf('%s', $html_price); ?>
								<?php else: ?>
									<?php echo esc_html($service_name); ?>
								<?php endif; ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		<?php endfor; ?>
	</div>
<?php endif; ?>