<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ovaev_shortcode_event_filter' ) ) {

	class ovaev_shortcode_event_filter {

		public $shortcode = 'ovaev_event_filter';

		public function __construct() {
			//add shortcode
			add_shortcode( $this->shortcode, array( $this, 'init_shortcode' ) );
		}

		public function get_data_shortcode( $args ) {
			// Base
			$args_base = array(
				'post_type' 		=> 'event',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> $args['page_per_posts'],
				'order' 			=> $args['order'],
			);

			// Sort
			if ( $args['orderby'] === 'ovaev_start_date_time' || $args['orderby'] === 'event_custom_sort' ) {
		        $args_base['meta_key'] 	= $args['orderby'];
		        $args_base['orderby'] 	= 'meta_value_num';
		        $args_base['meta_type'] = 'NUMERIC';
		    } else {
		        $args_base['orderby'] = $args['orderby'];
		    }

			// Time
			$args_time = [];
			if ( $args['time'] === 'today' ) {
				$end = ovaev_get_end_date( 'today' );

				$args_time = array(
					'meta_query' => array(
	                    array(
                            'key'     => 'ovaev_start_date_time',
                            'value'   => array( current_time( 'timestamp' ), $end ),
                            'type'    => 'numeric',
                        	'compare' => 'BETWEEN'
                        ),
	                )
				);
			} elseif ( $args['time'] === 'week' ) {
				$end = ovaev_get_end_date( 'week' );

				$args_time = array(
					'meta_query' => array(
                        array(
                            'key'     => 'ovaev_start_date_time',
                            'value'   => array( current_time( 'timestamp' ), $end ),
                            'type'    => 'numeric',
                        	'compare' => 'BETWEEN'
                        ),
	                )
				);
			} elseif ( $args['time'] === 'weekend' ) {
				$date_format 	= OVAEV_Settings::archive_event_format_date();
				$start 			= strtotime( date( $date_format, strtotime('this Saturday') ) );
				$end 			= ovaev_get_end_date( 'weekend' );

				$args_time = array(
					'meta_query' => array(
	                    array(
                            'key'     => 'ovaev_start_date_time',
                            'value'   => array( $start, $end ),
                            'type'    => 'numeric',
                        	'compare' => 'BETWEEN'
                       	),
	                )
				);
			} elseif ( $args['time'] === 'upcoming' ) {
				$args_time = array(
					'meta_query' => array(
	                    array(
                            'key'     => 'ovaev_start_date_time',
                            'value'   => current_time( 'timestamp' ),
                            'compare' => '>'
                        ),
	                )
				);
			} else {
				$args_time = [];
			}

			// Featured
			$args_featured = [];
			if ( $args['featured'] ) {
				$args_featured = array(
					'meta_query' => array(
						array(
							'key' 		=> 'ovaev_special',
							'compare' 	=> '=',
							'value' 	=> 'checked',
						)
					)
				);
			}

			// Category in
			$args_incl_category = [];
			if ( $args['incl_category'] ) {
				$args_incl_category = array(
					'tax_query' => array(
						array(
							'taxonomy' => 'event_category',
							'field'    => 'term_id',
							'terms'    => explode( ",", $args['incl_category'] ),
							'operator' => 'IN',
						)
					)
				);
			}

			// Category not in
			$args_excl_category = [];
			if ( $args['excl_category'] ) {
				$args_excl_category = array(
					'tax_query' => array(
						array(
							'taxonomy' => 'event_category',
							'field'    => 'term_id',
							'terms'    => explode( ",", $args['excl_category'] ),
							'operator' => 'NOT IN',
						)
					)
				);
			}
			
			$query 	= array_merge_recursive( $args_base, $args_time, $args_featured, $args_incl_category, $args_excl_category );
			$events = new \WP_Query( $query );

			return [
				'events' 	=> $events,
				'settings' 	=> $args,
			];
		}

		function init_shortcode( $args, $content = null ) {
			//get content
			$content = get_the_content( get_the_ID() );

			//check shortcode
			if ( has_shortcode( $content, 'ovaev_event_filter') ) {
				wp_enqueue_style( 'datetimepicker-style', OVAEV_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.css' );
				wp_enqueue_script( 'datetimepicker-script', OVAEV_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.js', array('jquery'), false, true );
			}
			//check variable shortcode

			if ( ! empty( $args ) ) {
				$attr = [
					//query events
					'page_per_posts' 	=> isset( $args['page_per_posts'] ) ? absint( $args['page_per_posts'] ) : 9,
					'time' 				=> isset( $args['time'] ) 			? $args['time'] 					: 'upcoming',
					'order' 			=> isset( $args['order'] ) 			? $args['order'] 					: 'ASC',
					'orderby' 			=> isset( $args['orderby'] ) 		? $args['orderby'] 					: 'ovaev_start_date_time',
					'featured' 			=> isset( $args['featured'] ) 		? $args['featured'] 				: '',
					'excl_category' 	=> isset( $args['excl_category'] ) 	? $args['excl_category'] 			: '',
					'incl_category' 	=> isset( $args['incl_category'] ) 	? $args['incl_category'] 			: '',
					'template' 			=> isset( $args['layout'] ) 		? absint( $args['layout'] ) 		: 1,
					'column' 			=> isset( $args['column'] ) 		? absint( $args['column'] ) 		: 3,	
					'pagination' 		=> isset( $args['pagination'] ) 	? absint( $args['pagination'] ) 	: '',
				];
			} else {
				$attr = [
					//query events
					'page_per_posts' 	=> 9,
					'time' 				=> 'upcoming',
					'order' 			=> 'ASC',
					'orderby' 			=> 'ovaev_start_date_time',
					'featured' 			=> '',
					'excl_category' 	=> '',
					'incl_category' 	=> '',
					'template' 			=> 1,
					'column' 			=> 3,
					'pagination' 		=> '',
				];
			}

			$data = $this->get_data_shortcode( $attr );
			
			//get template
			$template = apply_filters( 'shortcode_ovaev_event_filter', 'shortcode/ovaev_event_filter.php' );

			ob_start();
			ovaev_get_template( $template, $data );
			return ob_get_clean();
		}
	}

	new ovaev_shortcode_event_filter();
}
