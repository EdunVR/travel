<?php

	$version 		= $args['version'];
	$column 		= $args['column'];
	$column_temp 	= $args['column_template'];
	$type_event 	= $args['type_event'];

	$term 		= get_term_by('name', $args['category'], 'event_category');
	$term_link 	= get_term_link( $term );

	$events 	= ovaev_get_events_elements( $args );

?>

<div class="ovaev-event-element <?php echo esc_attr( $version ) ?>" >

	<?php 

		if ( $version == 'version_1' ) { 

			if( $events->have_posts() ) : while( $events->have_posts() ) : $events->the_post();

				echo ovaev_get_template( 'elements/__content_list.php' );
			
			endwhile; endif; wp_reset_postdata();
		
		} elseif( $version == 'version_2' ) { ?>

			<div class="wp-content <?php echo esc_attr( $column ); ?>">
				<?php
					if( $events->have_posts() ) : while( $events->have_posts() ) : $events->the_post();
						
						ovaev_get_template( 'event-templates/event-type2.php' );
						
					endwhile; endif; wp_reset_postdata();
				?>
			</div>

		<?php } else { ?>
			<div class="container-event">
				<div id="main-event" class="content-event">
					<div class="archive_event <?php echo esc_attr( $column_temp ); ?>">

						<?php if( $events->have_posts() ) : while( $events->have_posts() ) : $events->the_post(); ?>
							<?php ovaev_get_template( 'event-templates/event-'.$type_event.'.php' ); ?>
						<?php endwhile; else: ?>
							<div class="search_not_found">
								<?php esc_html_e( 'Not Found Events', 'ovaev' ); ?>
							</div>
						<?php endif; wp_reset_postdata(); ?>

					</div>
				</div>
		</div>

		<?php } ?>
</div>