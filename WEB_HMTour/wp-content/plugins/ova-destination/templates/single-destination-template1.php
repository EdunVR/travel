<?php if ( !defined( 'ABSPATH' ) ) exit();

$id = $args['id'];
if ( !$id ) {
	$id = get_the_ID();
}

$thumbnail     = wp_get_attachment_image_url( get_post_thumbnail_id( $id ), 'ova_destination_square' );
if( $thumbnail == '') {
	$thumbnail = \Elementor\Utils::get_placeholder_image_src();
}

$short_desc         = get_post_meta( $id, 'ova_destination_met_short_desc', true );
$group_tour_details = get_post_meta( $id, 'ova_destination_met_tour_details', true );
$sights             = get_post_meta( $id, 'ova_destination_met_sights', true );
$info               = get_post_meta( $id, 'ova_destination_met_info', true );
$map                = get_post_meta( $id, 'ova_destination_met_map', true );
if ( ($map == '') || ($map['latitude'] == '') || ($map['longitude'] == '') )  {
	$map = [];
	$map['latitude']  = get_option( 'ova_brw_latitude_map_default', 39.177972 ); 
	$map['longitude'] = get_option( 'ova_brw_longitude_map_default', -100.36375 );
}

$id_bg = get_theme_mod('single_detail_background_destination');
if ( $id_bg ) {
	$bg_url = wp_get_attachment_image_url( $id_bg, 'large' ) ;
}

$show_related_destination_tour = get_theme_mod('single_related_destination_tour', 'yes');

?>

<div class="info">

	<div class="main_content">

		 <!-- Background image -->
		<?php if( isset( $bg_url ) ) { ?>
			<div class="main-content-background">
				<img src="<?php echo esc_url( $bg_url ); ?>" alt="<?php echo get_the_title(); ?>">
			</div>
		<?php } ?>

        <!-- Short Description -->
		<?php if( ! empty( $short_desc ) ) { ?>
			<div class="short-description">
				<?php echo apply_filters( 'ova_the_content', $short_desc ); ?>
			</div>
		<?php } ?>

		<!-- Sights -->
		<div class="destination-sights-wrapper">

			<h4 class="heading heading-sights">
				<?php echo esc_html__('Sights', 'ova-destination'); ?>
			</h4>

            <div class="destination-sights">
				<ul class="list-img">
					<li class="item-img featured-img">
						<a class="gallery-fancybox" 
							data-src="<?php echo esc_url( $thumbnail ); ?>" 
							data-fancybox="ova_destiantion_sights_group" 
							data-caption="<?php  echo get_the_title(); ?>">
		  					<img src="<?php echo esc_url( $thumbnail ); ?>" class="img-responsive" alt="<?php echo get_the_title(); ?>" title="<?php the_title(); ?>" >
		  				</a>
					</li>
					<?php if ( $sights ):  $k = 0; ?>
						<?php foreach( $sights as $image_id => $image_url ):
							$image_alt   = get_post_meta($image_id, '_wp_attachment_image_alt', true);
			        	    $image_title = get_the_title( $image_id );
			        	  
							if ( ! $image_alt ) {
								$image_alt = get_the_title( $image_id );
							}

							$hidden = ( $k > 1 ) ? ' gallery_hidden' : '';
							$blur 	= ( $k == 1 && count( $sights ) > 2 ) ? ' gallery_blur' : '';

						?>
							<li class="item-img<?php printf( $hidden ); ?><?php printf( $blur ); ?>">
								<a class="gallery-fancybox" 
									data-src="<?php echo esc_url( $image_url ); ?>" 
									data-fancybox="ova_destiantion_sights_group" 
									data-caption="<?php echo esc_attr( $image_alt ); ?>">
				  					<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" title="<?php echo esc_attr($image_title); ?>">
				  					<?php if ( $blur ): ?>
				  						<div class="blur-bg">
				  							<span class="gallery-count">
				  								<?php echo esc_html( '+', 'ova-destination' ) . esc_html( count( $sights ) - 2 ); ?>
				  							</span>
				  						</div>
				  					<?php endif; ?>
				  				</a>
							</li>
						<?php $k = $k +1 ; endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>

		</div>
        
        <!-- Map -->
        <?php if( $map ): ?>
	        <div class="ova_destination_map">
	        	<h4 class="heading heading-map">
					<?php echo esc_html__('Map', 'ova-destination'); ?>
				</h4>

			    <div id="ova_destination_admin_show_map" data-zoom="<?php esc_attr_e( get_option( 'ova_brw_zoom_map_default', 17 ) ); ?>">
			        <div class="marker" data-lat="<?php echo esc_attr( $map['latitude'] ); ?>" data-lng="<?php echo esc_attr( $map['longitude'] ); ?>"></div>
			    </div>
			</div>
		<?php endif; ?>

		<!-- Info -->
		<?php if( ! empty( $info ) ) { ?>
			<div class="destination-info">
				 <h4 class="heading heading-info">
					<?php echo esc_html__('Info', 'ova-destination'); ?>
				</h4>
				<p>
					<?php echo apply_filters( 'ova_the_content', $info ); ?>
				</p>
			</div>
		<?php } ?>

		<!-- Next Preview Post -->
		
		    <div class="ova-next-pre-post">
				<?php
					$prev_post      = get_previous_post();
					$next_post      = get_next_post();
				?>
				
				<?php if($prev_post) { ?>

					<a class="pre" href="<?php echo esc_attr(get_permalink($prev_post->ID)); ?>">
						<?php echo get_the_post_thumbnail( $prev_post->ID, 'thumbnail' ); ?>
						<span class="num-1">
							<i class="icomoon icomoon-angle-left"></i>
						</span>
						<span  class="num-2">
							<span class="second_font text-label"><?php esc_html_e('Previous', 'ova-destination'); ?></span>
							<span  class="second_font title" ><?php echo esc_html(get_the_title($prev_post->ID)); ?></span>
						</span>
					</a>

				<?php } ?>		
				
				<?php if($next_post) { ?>

					<a class="next" href="<?php echo esc_attr(get_permalink($next_post->ID)); ?> ">
						<?php echo get_the_post_thumbnail( $next_post->ID, 'thumbnail' ); ?>
						<span class="num-1">
							<i class="icomoon icomoon-angle-right"></i>
						</span>
						<span  class="num-2">
							<span class="second_font text-label"><?php esc_html_e('Next', 'ova-destination'); ?></span>
							<span class="second_font title" ><?php echo esc_html(get_the_title($next_post->ID)); ?></span>
						</span>
					</a>
				<?php } ?>
			</div>

		
	</div>
	<!--  End Main Content -->

    <!--  Tour Details -->
	<?php if( ! empty( $group_tour_details ) ) {  ?>
		<div class="tour-details-wrapper">
			<h4 class="heading-tour-details">
				<?php echo esc_html__('Tour details', 'ova-destination'); ?>
			</h4>
			<ul class="tour-details-content">
				<?php
					foreach( $group_tour_details as $tour_detail ){

                        $tour_detail_title   = isset( $tour_detail['ova_destination_met_tour_details_title'] ) ? $tour_detail['ova_destination_met_tour_details_title'] : ''; 
                        $tour_detail_content = isset( $tour_detail['ova_destination_met_tour_details_content'] ) ? $tour_detail['ova_destination_met_tour_details_content'] : ''; 

					?>

					<li class="item-tour-details">
						<span class="title">
							<?php echo esc_html($tour_detail_title); ?>
						</span>
						<span class="content">
							<?php echo esc_html($tour_detail_content); ?>
						</span>
					</li>

				<?php } ?>					
			</ul>
	    </div>
	<?php } ?>	
	      
</div>

<!-- related destination tour -->
<?php

    $data_options   = apply_filters( 'ft_wc_related_destination_tour_options', array(
        'items'                 => 4,
        'slideBy'               => 1,
        'margin'                => 24,
        'autoplayHoverPause'    => true,
        'loop'                  => false,
        'autoplay'              => true,
        'autoplayTimeout'       => 3000,
        'smartSpeed'            => 500,
        'autoWidth'             => false,
        'center'                => false,
        'lazyLoad'              => true,
        'dots'                  => true,
        'nav'                   => true,
        'rtl'                   => is_rtl() ? true: false,
        'nav_left'              => 'icomoon icomoon-angle-left',
        'nav_right'             => 'icomoon icomoon-angle-right',
    ));

    $query_args = array( 
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => '-1',
        'orderby'        => 'rand',
        'meta_query' => array(
        	array(
        		'key'     => 'ovabrw_destination',
	            'value'   => $id,
	            'compare' => 'LIKE',
        	),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'ovabrw_car_rental', 
            ),
        ),
    );

    $products = new WP_Query( $query_args );
?>

<?php if ( $show_related_destination_tour == "yes"): ?>
	<?php if( $products->have_posts() ) : ?>

		<div class="ova-destination-related-wrapper">

			<h3 class="title">
				<?php echo esc_html__( 'Explore', 'ova-destination' ); ?>
			</h3>
			
			<?php if ( $products->have_posts() ): ?>

				<div class="ova-product-slider owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)) ?>">
					<?php while( $products->have_posts() ) : $products->the_post(); ?>
						<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; ?>
				</div>

			<?php endif; wp_reset_postdata(); ?>	

		</div>	

	<?php endif; ?>
<?php endif; ?>