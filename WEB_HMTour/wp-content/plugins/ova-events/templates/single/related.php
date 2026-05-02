<?php if ( !defined( 'ABSPATH' ) ) exit();

$post_ID = get_the_ID();

$event_related = ovaev_get_event_related_by_id( $post_ID );

if( $event_related->have_posts() ){ ?>

    <div class="event-related">
        
		<?php if($event_related->have_posts() ) : while ( $event_related->have_posts() ) : $event_related->the_post();		   
        	ovaev_get_template( 'event-templates/event-type2.php' );
        	endwhile; endif; wp_reset_postdata();
        ?>
        
    </div>

<?php }