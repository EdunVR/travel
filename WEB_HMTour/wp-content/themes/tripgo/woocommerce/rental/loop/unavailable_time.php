<?php 
/**
 * The template for displaying unavailable time content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/unavailable_time.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get all id product
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

$ovabrw_date_format = ovabrw_get_date_format();

$ovabrw_date_time_format = $ovabrw_date_format;

$start_untime 	= get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
$end_entime 	= get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

	if( $start_untime ){ ?>

		<div class="ovacrs_single_untime">
			
			<h3><?php esc_html_e( 'You can\'t book product in this time', 'tripgo' ); ?></h3>
					
			<ul>
				<?php foreach ($start_untime as $key => $value) { ?>
					<?php if( $start_untime[$key] != $end_entime[$key] ) : ?>
						<li>
							<?php echo sprintf( esc_html__( '%s. %s to %s', 'tripgo' ), absint( $key + 1 ), date_i18n( $ovabrw_date_time_format, strtotime( $start_untime[$key] ) ), date_i18n( $ovabrw_date_time_format, strtotime( $end_entime[$key] ) ) ); ?>
						</li>
					<?php else : ?>	
						<li>
							<?php echo sprintf( esc_html__( '%s. %s', 'tripgo' ), absint( $key + 1 ), date_i18n( $ovabrw_date_time_format, strtotime( $start_untime[$key] ) ) ); ?>
						</li>
					<?php endif; ?>					
				<?php } ?>
			</ul>

		</div>
		
	<?php } ?>
