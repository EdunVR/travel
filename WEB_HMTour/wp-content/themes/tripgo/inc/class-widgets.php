<?php if (!defined( 'ABSPATH' )) exit;

if( !class_exists('Tripgo_Widgets') ){
	
	class Tripgo_Widgets {

		function __construct(){

			/**
			 * Regsiter Widget
			 */
			add_action( 'widgets_init', array( $this, 'tripgo_register_widgets' ) );

		}
		

		function tripgo_register_widgets() {
		  
		  	$args_blog = array(
		    	'name' => esc_html__( 'Main Sidebar', 'tripgo'),
		    	'id' => "main-sidebar",
		    	'description' => esc_html__( 'Main Sidebar', 'tripgo' ),
		    	'class' => '',
		    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
		    	'after_widget' => "</div>",
		    	'before_title' => '<h4 class="widget-title">',
		    	'after_title' => "</h4>",
		  	);

		    register_sidebar( $args_blog );

		  	if( tripgo_is_woo_active() ) {

		    	$args_woo = array(
		      		'name' => esc_html__( 'WooCommerce Sidebar', 'tripgo'),
		      		'id' => "woo-sidebar",
		      		'description' => esc_html__( 'WooCommerce Sidebar', 'tripgo' ),
		      		'class' => '',
		      		'before_widget' => '<div id="%1$s" class="widget woo_widget %2$s">',
		      		'after_widget' => "</div>",
		      		'before_title' => '<h4 class="widget-title">',
		      		'after_title' => "</h4>",
		    	);

		    	register_sidebar( $args_woo );
		   }	  

		}


	}
}

return new Tripgo_Widgets();