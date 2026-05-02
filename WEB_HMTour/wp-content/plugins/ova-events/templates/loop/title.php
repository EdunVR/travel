<?php if ( !defined( 'ABSPATH' ) ) exit(); 

if( isset( $args['id'] ) ){
	$id = $args['id'];
}else{
	$id = get_the_id();	
}


?>

<a href="<?php echo get_the_permalink( $id );?>">
	<h2 class="second_font event_title">
		<?php echo get_the_title( $id ); ?>
	</h2>
</a>