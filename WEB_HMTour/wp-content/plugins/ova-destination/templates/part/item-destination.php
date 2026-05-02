<?php if ( !defined( 'ABSPATH' ) ) exit();

	$id = get_the_id();

	$show_thumbnail = isset( $args['show_thumbnail'] ) ? $args['show_thumbnail'] : 'yes' ;
	$show_title   	= isset( $args['show_title'] ) ? $args['show_title'] : 'yes' ;
	$show_count   	= isset( $args['show_count'] ) ? $args['show_count'] : 'yes' ;
	$show_link_to 	= isset( $args['show_link_to_detail'] ) ? $args['show_link_to_detail'] : 'yes' ;
    
    // get size image
	$thumbnail_square     = wp_get_attachment_image_url( get_post_thumbnail_id( $id ), 'ova_destination_square' );  
	$thumbnail_square_s   = wp_get_attachment_image_url( get_post_thumbnail_id( $id ), 'ova_destination_square_small' );  
	$thumbnail 			  = wp_get_attachment_image_url( get_post_thumbnail_id( $id ), 'ova_destination_thumbnail' ); 

	if( $show_thumbnail != 'yes' || !has_post_thumbnail($id) ) {
		$thumbnail = $thumbnail_square_s = $thumbnail_square = \Elementor\Utils::get_placeholder_image_src();
	}
    
    if ( isset($args['flag']) ) {
    	$flag = $args['flag'];
    } else {
    	$flag = 'slider';
    }

    $args_query = array(
	    'post_type' 		=> 'product',
	    'post_status' 		=> 'publish',
	    'posts_per_page' 	=> -1,
	    'meta_key' 			=> 'ovabrw_destination',
	    'meta_value' 		=> $id,
	    'meta_compare' 		=> 'LIKE',
	    'fields' 			=> 'ids',
	);

	$query_posts  	= get_posts( $args_query );
    $count      	= count( $query_posts );

?>

<?php if( $show_link_to == 'yes' ): ?>
    <a href="<?php the_permalink(); ?>">
<?php endif; ?>	

	<div class="item-destination item-destination-<?php echo esc_attr($flag); ?>">
        
        <!-- Image -->
		<div class="img">
			
	        <?php if( $flag == 1 ) : ?>

	    	    <img src="<?php echo esc_url( $thumbnail_square ); ?>" class="destination-img" alt="<?php the_title(); ?>">

	    	<?php elseif( ($flag == 2) || ($flag == 7)  ) : ?>

	    		<img src="<?php echo esc_url( $thumbnail ); ?>" class="destination-img" alt="<?php the_title(); ?>">

	    	<?php elseif($flag == 'slider') : ?>

	    		<img src="<?php echo esc_url( $thumbnail_square ); ?>" class="destination-img" alt="<?php the_title(); ?>">

    		<?php else : ?>

    		<img src="<?php echo esc_url( $thumbnail_square_s ); ?>" class="destination-img" alt="<?php the_title(); ?>">

	    	<?php endif; ?>
	    	
			<div class="mask"></div>

		</div>

		<!-- Info -->
		<div class="info">	

			<?php if($show_title == 'yes') { ?>
				<h3 class="name">
					<?php the_title(); ?>
				</h3>
			<?php } ?>

			<?php if($show_count == 'yes') { ?>
				<div class="count-tour">
				    <span class="number">
				    	<?php if( $count === 0) {
	                        printf($count) ;
				    	} else {
	                        echo sprintf('%02s', $count) ;
				    	} ?>		    	
				    </span>
				    <?php if ( $count != 1 ): ?>
				    	<?php esc_html_e(' Tours','ova-destination') ;?>
				    <?php else: ?>
				    	<?php esc_html_e(' Tour','ova-destination') ;?>
				    <?php endif; ?>
				</div>
			<?php } ?>

		</div>
		
	</div>

<?php if( $show_link_to == 'yes' ): ?>
    </a>
<?php endif; ?>	