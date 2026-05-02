<?php if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'OVADESTINATION_custom_post_typel' ) ) {

	class OVADESTINATION_custom_post_typel{

		public function __construct(){
			add_action( 'init', array( $this, 'OVAdestination_register_post_type_destination' ) );
			add_action( 'init', array( $this, 'OVAdestination_register_taxonomy_destination' ) );
		}
		
		function OVAdestination_register_post_type_destination() {
			$labels = array(
				'name'                  => _x( 'Destinations', 'Post Type General Name', 'ova-destination' ),
				'singular_name'         => _x( 'Destination', 'Post Type Singular Name', 'ova-destination' ),
				'menu_name'             => __( 'Destinations', 'ova-destination' ),
				'name_admin_bar'        => __( 'Destination', 'ova-destination' ),
				'archives'              => __( 'Item Archives', 'ova-destination' ),
				'attributes'            => __( 'Item Attributes', 'ova-destination' ),
				'parent_item_colon'     => __( 'Parent Item:', 'ova-destination' ),
				'all_items'             => __( 'All Destinations', 'ova-destination' ),
				'add_new_item'          => __( 'Add New Destination', 'ova-destination' ),
				'add_new'               => __( 'Add New', 'ova-destination' ),
				'new_item'              => __( 'New Item', 'ova-destination' ),
				'edit_item'             => __( 'Edit destination', 'ova-destination' ),
				'view_item'             => __( 'View Item', 'ova-destination' ),
				'view_items'            => __( 'View Items', 'ova-destination' ),
				'search_items'          => __( 'Search Item', 'ova-destination' ),
				'not_found'             => __( 'Not found', 'ova-destination' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'ova-destination' ),
			);

			$args = array(
				'description'         => __( 'Post Type Description', 'ova-destination' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'thumbnail' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'menu_position'       => 5,
				'query_var'           => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'rewrite'             => array(
					'slug' => apply_filters( 'ova_destination_slug', _x( 'tour_destination', 'URL slug', 'ova-destination' ) )
				),
				'capability_type'     => 'post',
				'menu_icon'           => 'dashicons-location-alt',
			);

			register_post_type( 'destination', $args );
		}

		function OVAdestination_register_taxonomy_destination(){
			$labels = array(
				'name'                       => _x( 'Destination Categories', 'Post Type General Name', 'ova-destination' ),
				'singular_name'              => _x( 'Category Destination', 'Post Type Singular Name', 'ova-destination' ),
				'menu_name'                  => __( 'Destination Categories', 'ova-destination' ),
				'all_items'                  => __( 'All Destination Categories', 'ova-destination' ),
				'parent_item'                => __( 'Parent Item', 'ova-destination' ),
				'parent_item_colon'          => __( 'Parent Item:', 'ova-destination' ),
				'new_item_name'              => __( 'New Item Name', 'ova-destination' ),
				'add_new_item'               => __( 'Add New Category Destination', 'ova-destination' ),
				'add_new'                    => __( 'Add New Category Destination', 'ova-destination' ),
				'edit_item'                  => __( 'Edit Category Destination', 'ova-destination' ),
				'view_item'                  => __( 'View Item', 'ova-destination' ),
				'separate_items_with_commas' => __( 'Separate items with commas', 'ova-destination' ),
				'add_or_remove_items'        => __( 'Add or remove items', 'ova-destination' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'ova-destination' ),
				'popular_items'              => __( 'Popular Items', 'ova-destination' ),
				'search_items'               => __( 'Search Items', 'ova-destination' ),
				'not_found'                  => __( 'Not Found', 'ova-destination' ),
				'no_terms'                   => __( 'No items', 'ova-destination' ),
				'items_list'                 => __( 'Items list', 'ova-destination' ),
				'items_list_navigation'      => __( 'Items list navigation', 'ova-destination' ),

			);

			$args = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'publicly_queryable' => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => false,
				'rewrite'            => array(
					'slug'       => _x( 'cat_destination','Destination Slug', 'ova-destination' ),
					'with_front' => false,
					'feeds'      => true,
				),
			);
			
			register_taxonomy( 'cat_destination', array( 'destination' ), $args );
		}
	}

	new OVADESTINATION_custom_post_typel();
}