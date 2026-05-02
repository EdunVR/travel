<?php

namespace ovabrw_product_elementor;

use ovabrw_product_elementor\widgets\ovabrw_product_images;
use ovabrw_product_elementor\widgets\ovabrw_product_title;
use ovabrw_product_elementor\widgets\ovabrw_product_short_description;
use ovabrw_product_elementor\widgets\ovabrw_product_features;
use ovabrw_product_elementor\widgets\ovabrw_product_table_price;
use ovabrw_product_elementor\widgets\ovabrw_product_forms;
use ovabrw_product_elementor\widgets\ovabrw_product_tabs;
use ovabrw_product_elementor\widgets\ovabrw_product_related;
use ovabrw_product_elementor\widgets\ovabrw_product_location_review;
use ovabrw_product_elementor\widgets\ovabrw_unavailable_time;
use ovabrw_product_elementor\widgets\ovabrw_search;
use ovabrw_product_elementor\widgets\ovabrw_product_list;
use ovabrw_product_elementor\widgets\ovabrw_product_categories;
use ovabrw_product_elementor\widgets\ovabrw_product_slider;
use ovabrw_product_elementor\widgets\ovabrw_search_ajax;
use ovabrw_product_elementor\widgets\ovabrw_search_ajax_sidebar;
use ovabrw_product_elementor\widgets\ovabrw_product_content;
use ovabrw_product_elementor\widgets\ovabrw_product_video_gallery;
use ovabrw_product_elementor\widgets\ovabrw_product_included_excluded;
use ovabrw_product_elementor\widgets\ovabrw_product_plan;
use ovabrw_product_elementor\widgets\ovabrw_product_map;
use ovabrw_product_elementor\widgets\ovabrw_product_reviews;
use ovabrw_product_elementor\widgets\ovabrw_product_custom_taxonomy;
use ovabrw_product_elementor\widgets\ovabrw_product_filter_ajax;
use ovabrw_product_elementor\widgets\ovabrw_product_category_ajax;
use ovabrw_product_elementor\widgets\ovabrw_product_destination_ajax;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class Ovabrw_Register_Elementor {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
     	// Register Ovatheme Category in Pane
	    add_action( 'elementor/elements/categories_registered', array( $this, 'add_ovatheme_category' ) );
		add_action( 'elementor/widgets/register', [ $this, 'on_widgets_registered' ] );
	}
	
	public function add_ovatheme_category(  ) {
	    \Elementor\Plugin::instance()->elements_manager->add_category(
	        'ovatheme',
	        [
	            'title' => __( 'Ovatheme', 'ova-brw' ),
	            'icon' => 'fa fa-plug',
	        ]
	    );
	}


	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_images.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_title.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_short_description.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_features.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_table_price.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_forms.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_tabs.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_related.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_location_review.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_categories.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_unavailable_time.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_search.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_list.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_slider.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_search_ajax.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_search_ajax_sidebar.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_content.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_video_gallery.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_included_excluded.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_plan.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_map.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_reviews.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_custom_taxonomy.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_filter_ajax.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_category_ajax.php';
		require OVABRW_PLUGIN_PATH . 'elementor/widgets/ovabrw_product_destination_ajax.php';

	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {

		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_images() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_title() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_short_description() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_features() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_table_price() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_forms() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_tabs() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_related() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_location_review() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_categories() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_unavailable_time() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_search() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_list() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_slider() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_search_ajax() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_search_ajax_sidebar() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_content() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_video_gallery() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_included_excluded() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_plan() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_map() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_reviews() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_custom_taxonomy() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_filter_ajax() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_category_ajax() );
		\Elementor\Plugin::instance()->widgets_manager->register( new ovabrw_product_destination_ajax() );

	}
	

}

new Ovabrw_Register_Elementor();