<?php if ( !defined( 'ABSPATH' ) ) exit();

get_header( );

$id = get_the_ID();

$date_format 	   	 = OVAEV_Settings::archive_event_format_date();
$time 			   	 = OVAEV_Settings::archive_event_format_time();
$event_template 	 = get_post_meta( $id, 'event_template', true ) ? get_post_meta( $id, 'event_template', true ) : 'global' ;

$template = OVAEV_Settings::ovaev_get_template_single();
$gallery  = get_post_meta( $id, 'ovaev_gallery_id', true);

// Basic
$start_date 	= get_post_meta( $id, 'ovaev_start_date', true );
$location   	= get_post_meta( $id, 'ovaev_venue', true );
$booking_links  = get_post_meta( $id, 'ovaev_booking_links', true ) ? get_post_meta( $id, 'ovaev_booking_links', true ) : '' ;

// Contact Infomation
$name        	= get_post_meta( $id, 'ovaev_organizer', true);
$phone       	= get_post_meta( $id, 'ovaev_phone', true);
$email       	= get_post_meta( $id, 'ovaev_email', true);
$website     	= get_post_meta( $id, 'ovaev_website', true);

?>

<?php if ( 'default' != $template ): ?>
	<?php if ( 'global' != $event_template ): ?>
		<?php echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $event_template ); ?>
	<?php else: ?>
		<?php echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template ); ?>
	<?php endif; ?>
<?php else: ?>
	<?php if ( 'global' != $event_template ): ?>
		<?php echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $event_template ); ?>
	<?php else: ?>
		<div class="single_event">
			<div class="container-event">

				<div class="content-event">	

					<div class="event_intro">

                        <div class="wrap-info">
							<?php 
								/**
								 * action oavev_single_time_loc
								 * Hooked oavev_single_time_loc_date_time
								 * Hooked oavev_single_time_loc_location
								 */
								do_action( 'oavev_single_time_loc' );
								do_action( 'oavev_single_type' );
							?>
						</div>

						<?php do_action( 'ovaev_single_title' ); ?>
						<?php do_action( 'oavev_single_thumbnail' ); ?>
                        
						<!-- Content -->
						<div class="content">
							<?php if( have_posts() ) : while( have_posts() ) : the_post();
								the_content();
					 		?>
					 		<?php endwhile; endif; wp_reset_postdata(); ?>
						</div>

						<div class="event_tags_share">
							<?php do_action( 'oavev_single_tags' ); ?>
						</div>			

				        <?php if( comments_open( get_the_ID() ) ) {
					        comments_template(); 
					    } ?>

					</div>
                    
                    <?php if($booking_links != '' || $location != '' || $start_date || $name != '' || $phone != '' || $email != '' || $website != '' || $gallery != '' ) : ?>
						<div class="intro_bar">
							<?php if( $booking_links != '' || $location != '' || $start_date != '' || $name != '' || $phone != '' || $email != '' || $website != ''  ) : ?>
								<div class="event_bar has-background">
									<h3 class="bar-title">
										<?php esc_html_e('Event Information','ovaev');?>
									</h3>
                                    <ul class="info-contact">
                                    	<?php if ( $start_date != '' ): ?>
											<li>
												<span><?php esc_html_e('Start date:','ovaev'); ?></span>
												<span class="info"><?php echo esc_html( $start_date ); ?></span>
											</li>
										<?php endif; ?>
										<?php if ( $location != '' ): ?>
											<li>
												<span><?php esc_html_e('Location:','ovaev'); ?></span>
												<span class="info"><?php echo esc_html( $location ); ?></span>
											</li>
										<?php endif; ?>
										<?php if ( $name != '' ): ?>
											<li>
												<span><?php esc_html_e('Organizer Name:','ovaev'); ?></span>
												<span class="info"><?php echo esc_html( $name ); ?></span>
											</li>
										<?php endif; ?>
										<?php if ( $phone != ''): ?>
											<li>
												<span><?php esc_html_e('Phone:','ovaev'); ?></span>
												<a href="tel:<?php echo esc_attr( $phone ); ?>" class="info"><?php echo esc_html( $phone ); ?></a>
											</li>
										<?php endif; ?>
										<?php if ( $email != ''): ?>
											<li>
												<span><?php esc_html_e('Email:','ovaev'); ?></span>
												<a href="mailto:<?php echo esc_attr( $email ); ?>" class="info"><?php echo esc_html( $email ); ?></a>
											</li>
										<?php endif; ?>
										<?php if ( $website != ''): ?>
											<li>
												<span><?php esc_html_e('Website:','ovaev'); ?></span>
												<a href="<?php echo esc_url( $website ); ?>" class="info" target="_blank"><?php echo esc_html( $website ); ?></a>
											</li>
										<?php endif; ?>
										<?php if ( $booking_links != ''): ?>
											<a href="<?php echo esc_html( $booking_links ); ?>" target="_blank" class="single-event-button">
												<?php esc_html_e('Booking Now','ovaev'); ?>
											</a>	
										<?php endif; ?>
									</ul>
								</div>
							<?php endif; ?>				

							<?php if ( $gallery != '' ):  ?>
				 		 		<div class="event_bar">
				 		 			<h3 class="bar-title">
										<?php esc_html_e('Gallery','ovaev');?>
									</h3>
									<div class="event_col-3">
										 <?php foreach ( $gallery as $items ): ?>
											<div class="gallery-items">
												<?php $img_url = wp_get_attachment_image_url( $items, 'large' );?>
												<a href="<?php echo esc_url( $img_url ); ?>" data-gal="prettyPhoto[gal]">
													<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo get_the_title(); ?>" />
													<span class="overlay">
														<i aria-hidden="true" class="icomoon icomoon-plus"></i>
													</span>
												</a> 
											</div>
					 		 			<?php endforeach; ?>
									</div>
				 		 		</div>
			 		 		<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>

			</div>

			<!-- Related -->
			<div class="event-related-container">
				<h2 class="related-event-title">
		        	<?php esc_html_e( 'Explore Related Events', 'ovaev' ) ?>
		        </h2>
				<?php do_action( 'oavev_single_related' ); ?>
			</div>

		</div>
	<?php endif; ?>
<?php endif; ?>
<?php get_footer();