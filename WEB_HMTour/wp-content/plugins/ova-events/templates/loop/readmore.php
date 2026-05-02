<?php if ( !defined( 'ABSPATH' ) ) exit(); 

if( isset( $args['id'] ) ){
	$id = $args['id'];
}else{
	$id = get_the_id();	
}


?>

<div class="event-readmore">
	<a href="<?php echo get_the_permalink( $id ) ?>" class="readmore second_font">
		<?php echo esc_html_e( 'Event details', 'ovaev' ) ?>
		<i aria-hidden="true" class="icomoon icomoon-arrow-right"></i>
	</a>
</div>