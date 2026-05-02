<?php if ( !defined( 'ABSPATH' ) ) exit(); 

$id = get_the_id();

$target 		= $args['target'] ? ' target="_blank"' : '';
$booking_url 	= get_post_meta( $id, 'ovaev_booking_links', true );

?>
<?php if ( $booking_url ): ?>
	<div class="ovaev-booking-btn">
		<a href="<?php echo esc_url( $booking_url ); ?>"<?php echo esc_attr( $target ); ?>>
			<?php esc_html_e( 'Booking Now', 'ovaev' ); ?>
		</a>
	</div>
<?php endif; ?>