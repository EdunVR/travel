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

$show_related_destination_tour = get_theme_mod('single_related_destination_tour', 'yes');


?>

<div class="info info-template2">

   <div class="left_main_content">
   	    <!-- Short Description -->
		<?php if( ! empty( $short_desc ) ) { ?>
			<div class="short-description">
				<?php echo apply_filters( 'ova_the_content', $short_desc ); ?>
			</div>
		<?php } ?>

	   <!--  Tour Details -->
		<?php if( ! empty( $group_tour_details ) ) {  ?>
			<div class="tour-details-wrapper">
				<h3 class="heading heading-tour-details">
					<?php echo esc_html__('Tour details', 'ova-destination'); ?>
				</h3>
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

	<div class="main_content">

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