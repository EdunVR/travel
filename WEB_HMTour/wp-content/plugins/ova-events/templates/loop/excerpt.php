<?php if ( !defined( 'ABSPATH' ) ) exit(); 

	if( isset( $args['id'] ) ){
		$id = $args['id'];
	}else{
		$id = get_the_id();	
	}

	$excerpt = get_the_excerpt($id);
	$excerpt = wp_trim_words($excerpt,10);

?>

 	<?php if($excerpt && !empty($excerpt)) : ?>
 		<p class="event-excerpt">
 			<?php echo esc_html($excerpt);?>
 		</p>
 	<?php endif; ?>