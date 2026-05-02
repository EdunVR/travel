<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product 	= wc_get_product( $product_id );

$date_format 	= ovabrw_get_date_format();

$min_adults     = get_post_meta( $product_id, 'ovabrw_adults_min', true );
$min_childrens  = get_post_meta( $product_id, 'ovabrw_childrens_min', true );

if ( ! $min_adults ) $min_adults = 1;
if ( ! $min_childrens ) $min_childrens = 0;

// link
$link  = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

// parameters
$param = array();

global $_POST;

$clicked 		= isset( $_POST['clicked'] ) 	? $_POST['clicked'] 	: '';
$start_date 	= isset( $_POST['start_date'] ) ? $_POST['start_date'] 	: '';
$adults 		= isset( $_POST['adults'] ) 	? $_POST['adults'] 		: $min_adults;
$childrens 		= isset( $_POST['childrens'] ) 	? $_POST['childrens'] 	: $min_childrens;

$dropoff_date 	= '';

if ( $clicked  ) {
	if ( $start_date ) {
		$param['pickup_date'] 	= $start_date;
		$day 					= get_post_meta( $product_id, 'ovabrw_number_days', true );
		$dropoff_date 			= strtotime( $start_date ) + $day*86400;
		$dropoff_date 		  	= date_i18n( $date_format, $dropoff_date );
	}

	if ( $dropoff_date ) {
		$param['dropoff_date']  = $dropoff_date;
	}

	if ( $adults ) {
		$param['ovabrw_adults'] = $adults;
	}

	if ( $childrens ) {
		$param['ovabrw_childrens'] = $childrens;
	}

	if ( $param ) {
		$link = add_query_arg( $param, $link );
	}
}

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
$wishlist = do_shortcode('[yith_wcwl_add_to_wishlist]');

// Featured product
$is_featured = $product->is_featured();

// Price
$regular_price = $product->get_regular_price();

if ( $product->is_on_sale() ) {
    $sale_price = $product->get_sale_price();
}

// show fields
$show_featured	= get_theme_mod('tour_archive_show_featured','yes');
$show_wishlist	= get_theme_mod('tour_archive_show_wishlist','yes');
$show_duration  = get_theme_mod('tour_archive_show_duration','yes');
$show_title 	= get_theme_mod('tour_archive_show_title','yes');
$show_location	= get_theme_mod('tour_archive_show_location','yes');
$show_rating	= get_theme_mod('tour_archive_show_rating','yes');
$show_price		= get_theme_mod('tour_archive_show_price','yes');
$show_button	= get_theme_mod('tour_archive_show_explore_button','yes');


?>

<div class="ova_head_product">
	<?php if ( $is_featured && $show_featured == 'yes' ): ?>
		<div class="ova-is-featured">
			<?php esc_html_e( 'Featured', 'tripgo' ); ?>
		</div>
	<?php endif; ?>
	<?php if ( '[yith_wcwl_add_to_wishlist]' != $wishlist && $show_wishlist == 'yes' ): ?>
		<div class="ova-product-wishlist">
			<?php echo $wishlist; ?>
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
			<?php echo woocommerce_get_product_thumbnail('tripgo_product_slider'); ?>
		</a>
	<?php endif; ?>
</div>
<div class="ova_foot_product">
	<div class="ova-product-day-title-location">
		<?php if ( ( $tour_day || ( $duration && $tour_hour ) ) && $product->is_type( 'ovabrw_car_rental' ) && $show_duration == 'yes' ) : ?>
			<div class="ova-tour-day">
				<i aria-hidden="true" class="icomoon icomoon-clock"></i>
				<?php if ( $duration ):
					$hours 		= ovabrw_convert_number_to_hours( $tour_hour );
					$minutes 	= ovabrw_convert_number_to_minutes( $tour_hour );
				?>
					<?php if ( $hours && $minutes ): ?>
						<?php if ( $hours > 1 && $minutes > 1 ): ?>
							<?php printf( esc_html__( '%s hours %s minutes', 'tripgo' ), $hours, $minutes ); ?>
						<?php elseif ( $hours == 1 && $minutes > 1 ): ?>
							<?php printf( esc_html__( '%s hour %s minutes', 'tripgo' ), $hours, $minutes ); ?>
						<?php elseif ( $hours > 1 && $minutes == 1 ): ?>
							<?php printf( esc_html__( '%s hours %s minute', 'tripgo' ), $hours, $minutes ); ?>
						<?php else: ?>
							<?php printf( esc_html__( '%s hour %s minute', 'tripgo' ), $hours, $minutes ); ?>
						<?php endif; ?>
					<?php elseif ( ! $hours && $minutes ): ?>
						<?php if ( $minutes == 1 ): ?>
							<?php printf( esc_html__( '%s minute', 'tripgo' ), $minutes ); ?>
						<?php else: ?>
							<?php printf( esc_html__( '%s minutes', 'tripgo' ), $minutes ); ?>
						<?php endif; ?>
					<?php else: ?>
						<?php if ( $hours == 1 ): ?>
							<?php printf( esc_html__( '%s hour', 'tripgo' ), $hours ); ?>
						<?php else: ?>
							<?php printf( esc_html__( '%s hours', 'tripgo' ), $hours ); ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php else: ?>
					<?php if ( absint( $tour_day ) == 1 ): ?>
						<?php echo esc_html( $tour_day ) . ' ' . esc_html__('day','tripgo'); ?>
					<?php else: ?>
						<?php echo esc_html( $tour_day ) . ' ' . esc_html__('days','tripgo'); ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endif;?>

		<?php if ( $show_title == 'yes' ): ?>
			<h2 class="ova-product-title">
				<a href="<?php echo esc_url( $link ); ?>">
			        <?php echo get_the_title(); ?>
			    </a>
			</h2>
		<?php endif;?>

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
    	<?php if ( wc_review_ratings_enabled() && $rating > 0 && $show_rating == 'yes' ) : ?>
	        <div class="ova-product-review">
	            <div class="star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'tripgo' ), $rating ); ?>">
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
				    		<span class="no-product-price"><?php esc_html_e( 'Option Price', 'tripgo' ); ?></span>
				    	<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_button == 'yes' ): ?>
				<a href="<?php echo esc_url( $link ); ?>" class="btn product-btn-book-now">
					<span><?php esc_html_e('Explore', 'tripgo'); ?></span>
				</a>
			<?php endif; ?>
		</div>
    </div>
</div>