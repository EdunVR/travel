<?php if ( !defined( 'ABSPATH' ) ) exit();

get_header();

$paged 		= get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$template 	= get_theme_mod( 'archive_destination_template', 'template1');
if ( isset( $_GET['destination_template'] ) && $_GET['destination_template'] ) {
	$template = $_GET['destination_template'];
}

$args['flag']  = 1;


?>
<div class="row_site">
	<div class="container_site">

		<div class="archive_destination">
			
			<div class="content content-<?php echo esc_attr($template);?> content-archive-destination">

				<div class="grid-sizer"></div>

				<?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<?php if( $template === 'template1' ) {
				        	ovadestination_get_template( 'part/item-destination.php', $args );

				        } elseif( $template === 'template2' ) {
				        	ovadestination_get_template( 'part/item-destination2.php', $args );

				        } elseif( $template === 'template3' ) {
				        	ovadestination_get_template( 'part/item-destination3.php', $args );

				        } else {
				        	ovadestination_get_template( 'part/item-destination.php', $args );
				    } ?>

					<?php $args['flag'] += 1; ?>

				<?php endwhile; endif; wp_reset_postdata(); ?>
				
			</div>
			
			<?php 
	    		 $args = array(
	                'type'      => 'list',
	                'next_text' => '<i class="ovaicon-next"></i>',
	                'prev_text' => '<i class="ovaicon-back"></i>',
	            );

	            the_posts_pagination($args);
	    	 ?>
		

		</div>
	</div>
</div>

<?php 
 get_footer();