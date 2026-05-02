<?php if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product 	= wc_get_product( $product_id );

$product_title = get_the_title($product_id);

$date_format 	= ovabrw_get_date_format();

$min_adults     = get_post_meta( $product_id, 'ovabrw_adults_min', true );
$min_childrens  = get_post_meta( $product_id, 'ovabrw_childrens_min', true );

if ( ! $min_adults ) $min_adults = 1;
if ( ! $min_childrens ) $min_childrens = 0;

// link
$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink($product_id), $product );

// show fields
$show_featured    =  isset( $args_show['show_featured'] )   ? $args_show['show_featured']     : 'yes' ;
$show_wishlist    =  isset( $args_show['show_wishlist'] )   ? $args_show['show_wishlist']     : 'yes' ;
$show_duration    =  isset( $args_show['show_duration'] )   ? $args_show['show_duration']     : 'yes' ;
$show_title       =  isset( $args_show['show_title'] )      ? $args_show['show_title']        : 'yes' ;
$show_location    =  isset( $args_show['show_location'] )   ? $args_show['show_location']     : 'yes' ;
$show_rating      =  isset( $args_show['show_rating'] )     ? $args_show['show_rating']       : 'yes' ;
$show_price       =  isset( $args_show['show_price'] )      ? $args_show['show_price']         : 'yes' ;
$show_button      =  isset( $args_show['show_button'] )     ? $args_show['show_button']       : 'yes' ;

// tour days and hours
$tour_day  	= get_post_meta ( $product_id,'ovabrw_number_days', true );
$tour_hour  = get_post_meta ( $product_id,'ovabrw_number_hours', true );
$duration 	= get_post_meta ( $product_id,'ovabrw_duration_checkbox', true );

// location and review
$address        = get_post_meta( $product_id, 'ovabrw_address', true );
$review_count   = $product->get_review_count();
$rating         = $product->get_average_rating();

$short_address  = get_post_meta( $product_id, 'ovabrw_short_address', true );
if( $short_address && !empty($short_address) ) {
	$address = $short_address;
}

// Wishlist
$wishlist = '[yith_wcwl_add_to_wishlist product_id=' . $product_id . ']';

// Featured product
$is_featured = $product->is_featured();

// Price
$regular_price = $product->get_regular_price();

if ( $product->is_on_sale() ) {
    $sale_price = $product->get_sale_price();
}

?>

<div class="ova-product">
	<div class="ova_head_product">
		<?php if ( $is_featured && $show_featured == 'yes' ): ?>
			<div class="ova-is-featured">
				<?php esc_html_e( 'Featured', 'ova-brw' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( '[yith_wcwl_add_to_wishlist product_id=' . $product_id . ']' != do_shortcode($wishlist) && $show_wishlist == 'yes' ): ?>
			<div class="ova-product-wishlist">
				<?php echo do_shortcode($wishlist); ?>
			</div>
		<?php endif; ?>
		<?php if ( apply_filters( 'ovabrw_ft_product_list_card_gallery', false ) ): ?>
			<div class="ova-card-gallery">
				<?php
					$data_options = apply_filters( 'ft_wc_card_gallery_slideshow_options', array(
				        'items'                 => 1,
				        'slideBy'               => 1,
				        'margin'                => 24,
				        'autoplayHoverPause'    => true,
				        'loop'                  => true,
				        'autoplay'              => false,
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
				?>
				<?php wc_get_template( 'rental/loop/gallery-slideshow.php', [ 'data_options' => $data_options ] ); ?>
			</div>
		<?php else: ?>
			<a href="<?php echo esc_url( $link ); ?>" class="ova-product-thumbnail">
				<?php 
					if( has_post_thumbnail($product_id) ) {
						$product_img_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'tripgo_product_slider' )[0];
					} else {
						$product_img_url = \Elementor\Utils::get_placeholder_image_src();
					}
				?>
				<img src="<?php echo esc_url($product_img_url); ?>" alt="<?php echo esc_attr($product_title);?>">
			</a>
		<?php endif; ?>
	</div>
	<div class="ova_foot_product">
		<div class="ova-product-day-title-location">
			<?php if ( ( $tour_day || ( $duration && $tour_hour ) ) && $product->is_type( 'ovabrw_car_rental' ) && $show_duration == 'yes') : ?>
				<div class="ova-tour-day">
					<i aria-hidden="true" class="icomoon icomoon-clock"></i>
					<?php if ( $duration ):
						$hours 	 = ovabrw_convert_number_to_hours( $tour_hour );
						$minutes = ovabrw_convert_number_to_minutes( $tour_hour );
					?>
						<?php if ( $hours && $minutes ): ?>
							<?php if ( $hours > 1 && $minutes > 1 ): ?>
								<?php printf( esc_html__( '%s hours %s minutes', 'ova-brw' ), $hours, $minutes ); ?>
							<?php elseif ( $hours == 1 && $minutes > 1 ): ?>
								<?php printf( esc_html__( '%s hour %s minutes', 'ova-brw' ), $hours, $minutes ); ?>
							<?php elseif ( $hours > 1 && $minutes == 1 ): ?>
								<?php printf( esc_html__( '%s hours %s minute', 'ova-brw' ), $hours, $minutes ); ?>
							<?php else: ?>
								<?php printf( esc_html__( '%s hour %s minute', 'ova-brw' ), $hours, $minutes ); ?>
							<?php endif; ?>
						<?php elseif ( ! $hours && $minutes ): ?>
							<?php if ( $minutes == 1 ): ?>
								<?php printf( esc_html__( '%s minute', 'ova-brw' ), $minutes ); ?>
							<?php else: ?>
								<?php printf( esc_html__( '%s minutes', 'ova-brw' ), $minutes ); ?>
							<?php endif; ?>
						<?php else: ?>
							<?php if ( $hours == 1 ): ?>
								<?php printf( esc_html__( '%s hour', 'ova-brw' ), $hours ); ?>
							<?php else: ?>
								<?php printf( esc_html__( '%s hours', 'ova-brw' ), $hours ); ?>
							<?php endif; ?>
						<?php endif; ?>
					<?php else: ?>
						<?php if ( absint( $tour_day ) == 1 ): ?>
							<?php echo esc_html( $tour_day ) . ' ' . esc_html__('day','ova-brw'); ?>
						<?php else: ?>
							<?php echo esc_html( $tour_day ) . ' ' . esc_html__('days','ova-brw'); ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif;?>
			<?php if ( $show_title == 'yes' ): ?>
				<h2 class="ova-product-title">
					<a href="<?php echo esc_url( $link ); ?>">
				        <?php echo get_the_title($product_id); ?>
				    </a>
				</h2>
			<?php endif; ?>

			<?php if ( $address && $show_location == 'yes' ): ?>
		        <div class="ova-product-location">
		            <i aria-hidden="true" class="icomoon icomoon-location"></i>
		            <span class="location">
		                <?php echo esc_html( $address ); ?>
		            </span>
		        </div>
		    <?php endif; ?>
		</div>
	    <div class="ova-product-review-and-price">
	    	<?php if ( wc_review_ratings_enabled() && $rating > 0 && $show_rating == 'yes' ): ?>
		        <div class="ova-product-review">
		            <div class="star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'ova-brw' ), $rating ); ?>">
		                <span class="rating-percent" style="width: <?php echo esc_attr( ( $rating / 5 ) * 100 ).'%'; ?>;"></span>
		                <?php if ( $review_count > 0 ): ?>
		                    <span class="rating"><?php echo esc_html( $review_count ); ?></span>'
		                <?php else: ?>
		                    <strong class="rating"><?php echo esc_html( $rating ); ?></strong>
		                <?php endif; ?>
		            </div>
		        </div>
		    <?php endif; ?>
		   
			<div class="ova-product-wrapper-price">
				<?php if ( $show_price == 'yes' ): ?>
					<div class="ova-product-price">
						<?php if ( isset( $sale_price ) && $regular_price ): ?>
							<span class="new-product-price"><?php echo wc_price( $sale_price ); ?></span>
							<span class="old-product-price"><?php echo wc_price( $regular_price ); ?></span>
						<?php elseif ( ! isset( $sale_price ) && $regular_price ): ?>
							<span class="new-product-price"><?php echo wc_price( $regular_price ); ?></span>
					    <?php else: ?>
					    	<?php if ( $product && ! $product->is_type('ovabrw_car_rental') ): ?>
					    		<span class="new-product-price"><?php echo $product->get_price_html(); ?></span>
					    	<?php else: ?>
					    		<span class="no-product-price"><?php esc_html_e( 'Option Price', 'ova-brw' ); ?></span>
					    	<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $show_button == 'yes' ): ?>
					<a href="<?php echo esc_url( $link ); ?>" class="btn product-btn-book-now">
						<span><?php esc_html_e('Explore', 'ova-brw'); ?></span>
					</a>
				<?php endif; ?>
			</div>
			
	    </div>
	</div>
</div>