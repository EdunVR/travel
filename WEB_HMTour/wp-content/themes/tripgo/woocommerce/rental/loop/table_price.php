<?php 
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get id from do_action - use when insert shortcode
$all_ids = ovabrw_get_all_id_product();

if( isset( $args['id'] ) && $args['id'] != '' ) {

    $product_id  = ( in_array( $args['id'], $all_ids ) == true && in_array( get_the_id(), $all_ids ) == false ) ? $args['id'] : get_the_id();

} elseif( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id  = $all_ids[0];

} else {
    $product_id  = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $product_id );
if ( ! $product || $product->get_type() !== 'ovabrw_car_rental' ) return;

// Global Discount
$gd_duration_val_min = get_post_meta( $product_id, 'ovabrw_gd_duration_min', true );
$gd_duration_val_max = get_post_meta( $product_id, 'ovabrw_gd_duration_max', true );
if( $gd_duration_val_min ) asort( $gd_duration_val_min );
if( $gd_duration_val_max ) asort( $gd_duration_val_max );

$gd_adult_price 		= get_post_meta( $product_id, 'ovabrw_gd_adult_price', true );
$gd_children_price 		= get_post_meta( $product_id, 'ovabrw_gd_children_price', true );
$gd_baby_price 			= get_post_meta( $product_id, 'ovabrw_gd_baby_price', true );
$title_text 			= esc_html__( 'Min - Max (Persons)', 'tripgo' );
$price_adult_text 		= esc_html__( 'Adult Price', 'tripgo' ); 
$price_children_text 	= esc_html__( 'Children Price', 'tripgo' ); 
$price_baby_text 		= esc_html__( 'Baby Price', 'tripgo' ); 

// Special Time
$st_startdate 		= get_post_meta( $product_id, 'ovabrw_st_startdate', true ); 
$st_enddate   		= get_post_meta( $product_id, 'ovabrw_st_enddate', true );
$st_adult_price 	= get_post_meta( $product_id, 'ovabrw_st_adult_price', true );
$st_children_price  = get_post_meta( $product_id, 'ovabrw_st_children_price', true );
$st_baby_price  	= get_post_meta( $product_id, 'ovabrw_st_baby_price', true );
$st_discount  		= get_post_meta( $product_id, 'ovabrw_st_discount', true );

$ovabrw_date_format = ovabrw_get_date_format();

// Settings
$show_children 	= get_option( 'ova_brw_booking_form_show_children', 'yes' );
$show_baby 		= get_option( 'ova_brw_booking_form_show_baby', 'yes' );

$show_table_price = isset( $args['show_table_price'] ) ? $args['show_table_price'] : 'yes';

?>

<?php if($show_table_price == 'yes') { ?>
	<?php if( !empty( $gd_duration_val_min ) || !empty( $gd_duration_val_max ) || !empty( $st_startdate ) || !empty( $st_enddate ) ) { ?>
		<div class="product_table_price">

			<div class="ovacrs_price_rent">
				  
				<?php if( !empty( $gd_duration_val_min ) || !empty( $gd_duration_val_max ) ) { ?>

					<div class="price_table">
						<label><?php esc_html_e( 'Global Discount', 'tripgo' ); ?></label>
						<table class="gb-discount">
							<thead>
								<tr>
									<th><?php printf( $title_text ) ; ?></th>
									<th><?php printf( $price_adult_text ) ; ?></th>
	                                <?php if ( ! empty( $gd_children_price ) && $show_children === 'yes' ): ?>
									    <th><?php printf( $price_children_text ) ; ?></th>
									<?php endif; ?>
									<?php if ( ! empty( $gd_baby_price ) && $show_baby === 'yes' ): ?>
									    <th><?php printf( $price_baby_text ) ; ?></th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php $k = 0; foreach ($gd_duration_val_min as $key => $value) { ?>
									<tr class="<?php echo intval($k%2) ? 'eve' : 'odd'; $k++; ?>">
										<td class="bold" data-title="<?php echo esc_attr( $title_text ) ; ?>">
											<?php echo esc_html( $gd_duration_val_min[$key] ); ?>
											<?php echo esc_html__('-','tripgo'); ?>
											<?php echo esc_html( $gd_duration_val_max[$key] ); ?>
										</td>
										<td data-title="<?php echo esc_attr( $price_adult_text ).' '.esc_html__( 'from', 'tripgo' ).' '.$gd_duration_val_min[$key].' - '.$gd_duration_val_max[$key].' '.esc_html__( 'days', 'tripgo' ); ?>">
											<?php echo ovabrw_wc_price( $gd_adult_price[$key] ); ?>
										</td>
	                                    <?php if ( ! empty( $gd_children_price ) && $show_children === 'yes' ): ?>
											<td data-title="<?php echo esc_attr( $price_children_text ).' '.esc_html__( 'from', 'tripgo' ).' '.$gd_duration_val_min[$key].' - '.$gd_duration_val_max[$key].' '.esc_html__( 'days', 'tripgo' ); ?>">
												<?php echo ovabrw_wc_price( $gd_children_price[$key] ); ?>
											</td>
										<?php endif; ?>
										<?php if ( ! empty( $gd_baby_price ) && $show_baby === 'yes' ): ?>
											<td data-title="<?php echo esc_attr( $price_baby_text ).' '.esc_html__( 'from', 'tripgo' ).' '.$gd_duration_val_min[$key].' - '.$gd_duration_val_max[$key].' '.esc_html__( 'days', 'tripgo' ); ?>">
												<?php echo ovabrw_wc_price( $gd_baby_price[$key] ); ?>
											</td>
										<?php endif; ?>
									</tr>	
								<?php } ?>
							</tbody>
						</table>
					</div>
				<?php } ?>
		        
		        <?php if ( ! empty( $st_startdate ) || !empty( $st_enddate ) ) { ?>

					<div class="price_table">
	 
						<label><?php esc_html_e( 'Special Time', 'tripgo' ); ?></label>
						<table class="special-time">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Start Date', 'tripgo' ); ?></th>
									<th><?php esc_html_e( 'End Date', 'tripgo' ); ?></th>
									<th><?php esc_html_e( 'Adult Price', 'tripgo' ); ?></th>
									<?php if ( $show_children === 'yes' ): ?>
										<th><?php esc_html_e( 'Children Price', 'tripgo' ); ?></th>
									<?php endif; ?>
									<?php if ( $show_baby === 'yes' ): ?>
										<th><?php esc_html_e( 'Baby Price', 'tripgo' ); ?></th>
									<?php endif; ?>
									<th><?php esc_html_e( 'Special Discount', 'tripgo' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if ( $st_adult_price ) {
									$d = 0;

									foreach( $st_adult_price as $key => $value ) {
										if ( $st_adult_price[$key] ) { 
											$date_start = $st_startdate[$key] ;
											$date_end 	= $st_enddate[$key] ;
										?>
											<tr class="<?php echo intval($d%2) ? 'eve' : 'odd'; $d++; ?>">
												<td class="date bold"  data-title="<?php esc_html_e( 'Start Date', 'tripgo' ); ?>">
													<?php echo esc_html( $date_start ); ?>
												</td>
												<td class="date bold"  data-title="<?php esc_html_e( 'End Date', 'tripgo' ); ?>">
													<?php echo esc_html( $date_end ); ?>
												</td>
												<td data-title="<?php echo esc_html__( 'Price from', 'tripgo' ).' '.$date_start.' - '.$date_end; ?>">
													<?php echo ovabrw_wc_price( $st_adult_price[$key] ); ?>
												</td>
	                                            <?php if ( $show_children === 'yes' ): ?>
													<td data-title="<?php echo esc_html__( 'Price from', 'tripgo' ).' '.$date_start.' - '.$date_end; ?>">
														<?php echo isset( $st_children_price[$key] ) ? ovabrw_wc_price( $st_children_price[$key] ) : ovabrw_wc_price(0); ?>
													</td>
												<?php endif; ?>
												<?php if ( $show_baby === 'yes' ): ?>
													<td data-title="<?php echo esc_html__( 'Price from', 'tripgo' ).' '.$date_start.' - '.$date_end; ?>">
														<?php echo isset( $st_baby_price[$key] ) ? ovabrw_wc_price( $st_baby_price[$key] ) : ovabrw_wc_price(0); ?>
													</td>
												<?php endif; ?>
												<td data-title="<?php esc_html_e( 'Special Discount', 'tripgo' ); ?>">
													<a href="#" class="ovabrw_open_popup" data-popup-open="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>">
														<?php esc_html_e( 'View Discount', 'tripgo' ); ?>
														<div class="ovacrs_st_discount popup" data-popup="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>">
															<div class="popup-inner">
																<div class="price_table">
																	<div class="time_discount">
																		<span><?php esc_html_e( 'Time Discount: ', 'tripgo' ); ?></span>
																		<span class="time">
																			<?php echo esc_html($date_start).esc_html__( ' to ', 'tripgo' ).esc_html($date_end); ?>  
																		</span>
																	</div>
																	<?php $st_discount_price = isset( $st_discount[$key]['price'] ) ? $st_discount[$key]['price'] : '';
																		$st_discount_duration_min 	= isset( $st_discount[$key]['min'] ) ? $st_discount[$key]['min'] : '';
																		$st_discount_duration_max 	= isset( $st_discount[$key]['max'] ) ? $st_discount[$key]['max'] : '';
																		$st_discount_adult_price	= isset( $st_discount[$key]['adult_price'] ) ? $st_discount[$key]['adult_price'] : '';
																		$st_discount_children_price = isset( $st_discount[$key]['children_price'] ) ? $st_discount[$key]['children_price'] : '';
																		$st_discount_baby_price 	= isset( $st_discount[$key]['baby_price'] ) ? $st_discount[$key]['baby_price'] : '';
																	?>

																	<?php if( $st_discount_duration_min || $st_discount_duration_max ){ 
																		asort($st_discount_duration_min);
																		asort($st_discount_duration_max);
																	?>
																		<table>
																			<thead>
																				<tr>
																					<th><?php esc_html_e( 'Min - Max (Persons)', 'tripgo' ); ?></th>
																					<th><?php esc_html_e( 'Adult Price', 'tripgo' ); ?></th>
																					<?php if ( $show_children === 'yes' ): ?>
																						<th><?php esc_html_e( 'Children Price', 'tripgo' ); ?></th>
																					<?php endif; ?>
																					<?php if ( $show_baby === 'yes' ): ?>
																						<th><?php esc_html_e( 'Baby Price', 'tripgo' ); ?></th>
																					<?php endif; ?>
																				</tr>
																			</thead>

																			<tbody>
																				<?php $n = 0; foreach( $st_discount_duration_min as $k => $v ) { ?>
																					<tr class="<?php echo intval($n%2) ? 'eve' : 'odd'; $n++; ?>">
																						<td class="bold" data-title="<?php esc_html_e( 'Min - Max (Days)', 'tripgo' ); ?>">
																							<?php echo esc_html( $st_discount_duration_min[$k] ).' - '.esc_html( $st_discount_duration_max[$k] ); ?>
																						</td>
																						<td data-title="<?php echo esc_html__( 'Price from', 'tripgo' ).' '.esc_html( $st_discount_duration_min[$k] ).' - '.esc_html( $st_discount_duration_max[$k] ).' '.esc_html__('days', 'tripgo'); ?>">
																							<?php echo ovabrw_wc_price( $st_discount_adult_price[$k] ); ?>
																						</td>
																						<?php if ( $show_children === 'yes' ): ?>
																							<td data-title="<?php echo esc_html__( 'Price from', 'tripgo' ).' '.esc_html( $st_discount_duration_min[$k] ).' - '.esc_html( $st_discount_duration_max[$k] ).' '.esc_html__('days', 'tripgo'); ?>">
																								<?php echo ovabrw_wc_price( $st_discount_children_price[$k] ); ?>
																							</td>
																						<?php endif; ?>
																						<?php if ( $show_baby === 'yes' ): ?>
																							<td data-title="<?php echo esc_html__( 'Price from', 'tripgo' ).' '.esc_html( $st_discount_duration_min[$k] ).' - '.esc_html( $st_discount_duration_max[$k] ).' '.esc_html__('days', 'tripgo'); ?>">
																								<?php echo isset( $st_discount_baby_price[$k] ) ? ovabrw_wc_price( $st_discount_baby_price[$k] ) : ovabrw_wc_price(0); ?>
																							</td>
																						<?php endif; ?>
																					</tr>
																				<?php } ?>
																			</tbody>
																		</table>
																		<?php } else { ?>
																			<div class="no_discount">
																				<?php esc_html_e( 'No Discount in this time', 'tripgo' ); ?>
																			</div>
																		<?php } ?>
																</div>	
																<div class="close_discount">
																	<a class="popup-close-2" data-popup-close="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>" href="#"><?php esc_html_e( 'Close', 'tripgo' ); ?>
																	</a>
																</div>
																<a class="popup-close" data-popup-close="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>" href="#">x</a>
															</div>
														</div>
													</a>
												</td>
											</tr>			
								<?php } } } ?>
							</tbody>
						</table>
					</div>	
				<?php } ?>
			</div>		
		</div>
	<?php } ?>
<?php } ?>