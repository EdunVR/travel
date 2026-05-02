<?php if ( !defined( 'ABSPATH' ) ) exit();

$data_options['items']              = $args['item_number'];
$data_options['slideBy']            = $args['slides_to_scroll'];
$data_options['margin']             = $args['margin_items'];
$data_options['autoplayHoverPause'] = $args['pause_on_hover'] === 'yes' ? true : false;
$data_options['loop']               = $args['infinite'] === 'yes' ? true : false;
$data_options['autoplay']           = $args['autoplay'] === 'yes' ? true : false;
$data_options['autoplayTimeout']    = $args['autoplay_speed'];
$data_options['smartSpeed']         = $args['smartspeed'];
$data_options['nav']                = $args['nav_control'] === 'yes' ? true : false;
$data_options['dots']               = $args['dot_control'] === 'yes' ? true : false;
$data_options['rtl']                = is_rtl() ? true : false;

$template 		= $args['template'];
$destinations 	= ovadestination_get_data_destination_slider_el( $args );

?>

<div class="ova-destination-slider">

	<div class="content content-<?php echo esc_attr($template);?> slide-destination owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)) ?>">

		<?php if( $destinations->have_posts() ) : while ( $destinations->have_posts() ) : $destinations->the_post(); ?>

            <?php if( $template === 'template1' ) {
		        	ovadestination_get_template( 'part/item-destination.php', $args );

		        } elseif( $template === 'template2' ) {
		        	ovadestination_get_template( 'part/item-destination2.php', $args );

		        } elseif( $template === 'template3' ) {
		        	ovadestination_get_template( 'part/item-destination3.php', $args );

		        } else {
		        	ovadestination_get_template( 'part/item-destination.php', $args );
		    } ?>

		<?php endwhile; endif; wp_reset_postdata(); ?>

	</div>

</div>
