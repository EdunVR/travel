<?php if( ! defined( 'ABSPATH' ) ) exit();
    
    $icon 				 = $args['icon'] ? $args['icon'] : '';
	$text_view_details 	 = $args['label_view_details'];

	$show_image = $args['show_image'];
	$show_title = $args['show_title'];

	$show_product_counts = $args['show_product_counts'];
	$text_product_counts = isset($args['text_product_counts']) ? $args['text_product_counts'] : '' ;

	// data option carousel
	$data_options['items']  			= $args['item_number'];
	$data_options['slideBy']            = $args['slides_to_scroll'];
	$data_options['margin']             = $args['margin_items'];
	$data_options['autoplayHoverPause'] = $args['pause_on_hover'] === 'yes' ? true : false;
	$data_options['loop']               = $args['infinite'] === 'yes' ? true : false;
	$data_options['autoplay']           = $args['autoplay'] === 'yes' ? true : false;
	$data_options['autoplayTimeout']    = $args['autoplay_speed'];
	$data_options['smartSpeed']         = $args['smartspeed'];
	$data_options['dots']               = false;
	$data_options['nav']               	= $args['nav_control'] === 'yes' ? true : false;
	$data_options['rtl']				= is_rtl() ? true: false;

	// categories
    $categories  =  isset( $args['categories'] ) ? $args['categories'] : array('all') ;
    if ( $categories == 'all') {
        $categories = array('all');
    }

	// get data
	$args_term  = array(
		'taxonomy'   => 'product_cat',
		'orderby' 	 => 'slug',
		'hide_empty' => true,
	);

	if ( !empty($categories) && !in_array('all', $categories) ) {
        $args_term['include'] = $categories;
    }

	$terms = get_terms( $args_term );
	
?>

	<div class="ova_product_categories owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)); ?>">

		<?php foreach ($terms as $term) :
			$thumbnail_id 	= get_term_meta($term->term_id, 'thumbnail_id', true);
			$thumbnail_url 	= wp_get_attachment_url($thumbnail_id) ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src('thumbnail');
			$name  			= $term->name;
			$link 			= get_term_link( $term->term_id, 'product_cat' );
		?>

			<div class="item">
				<?php if( $show_image == 'yes') { ?>
					<div class="image-thumbnail">
						<img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_html($name);?>">

						<?php if( $text_view_details != '' || $icon['value'] != '' ): ?>
							<a href="<?php echo esc_url( $link ); ?>" class="btn read-more">
								<span>
									<?php echo esc_html($text_view_details); ?>
								</span>
								<?php if( $icon['value'] != '' ){ 
								    \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
								} ?>
							</a>
						<?php endif; ?>
					</div>
				<?php } ?>

				<?php if( $show_title == 'yes') { ?>
					<h4 class="title">
						<a href="<?php echo esc_url( $link ); ?>" >
							<?php echo esc_html($name); ?>
						</a>
					</h4>
				<?php } ?>

				<?php if( $show_product_counts == 'yes') { ?>
					<div class="counts">
						<span><?php echo esc_html($term->count) . esc_html__('+','ova-brw');?></span>
						<span class="text"><?php echo esc_html($text_product_counts);?></span>
					</div>
				<?php } ?>

				<?php if( $show_image != 'yes') { ?>
					<div class="image-thumbnail no-thumbnail">
						<?php if( $text_view_details != '' || $icon['value'] != '' ): ?>
							<a href="<?php echo esc_url( $link ); ?>" class="btn read-more">
								<span>
									<?php echo esc_html($text_view_details); ?>
								</span>
								<?php if( $icon['value'] != '' ){ 
								    \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
								} ?>
							</a>
						<?php endif; ?>
					</div>
				<?php } ?>
			</div>

		<?php endforeach; ?>

	</div>