<?php if ( !defined( 'ABSPATH' ) ) exit();

// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'ovabrw_create_type_taxonomies', 0 );
function ovabrw_create_type_taxonomies() {
	// Get Custom Taxonomy from Database
	$ovabrw_custom_taxonomy =  get_option( 'ovabrw_custom_taxonomy', '' );
	$name_taxonomy = $ovabrw_custom_tax = array();

	if ( $ovabrw_custom_taxonomy ) {
		$i = 1;
		foreach ($ovabrw_custom_taxonomy as $slug => $value) {
			$labels = array(
				'name'              => _x( $value['name'], 'taxonomy general name', 'ova-brw' ),
				'singular_name'     => _x( $value['singular_name'], 'taxonomy singular name', 'ova-brw' ),
				'search_items'      => esc_html__( 'Search ' . $value['name'], 'ova-brw' ),
				'all_items'         => esc_html__( 'All ' . $value['name'], 'ova-brw' ),
				'parent_item'       => esc_html__( 'Parent ' . $value['name'], 'ova-brw' ),
				'parent_item_colon' => esc_html__( 'Parent ' . $value['name'] .': ', 'ova-brw' ),
				'edit_item'         => esc_html__( 'Edit ' . $value['name'], 'ova-brw' ),
				'update_item'       => esc_html__( 'Update ' . $value['name'], 'ova-brw' ),
				'add_new_item'      => esc_html__( 'Add New ' . $value['name'], 'ova-brw' ),
				'new_item_name'     => esc_html__( 'New ' . $value['name'] .' Name', 'ova-brw' ),
				'menu_name'         => esc_html__( 'Custom ' . $value['name'], 'ova-brw' ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => apply_filters( 'ovabrw_show_ui_custom_tax', true),
				'show_admin_column' => false,
				'query_var'         => true,
				'show_in_nav_menus' => false,
				'show_in_menu'	=> false,
				'rewrite'           => array( 'slug' => $slug ),
			);

			if( $value['enabled'] == 'on' ){
				register_taxonomy( $slug, array( 'product' ), $args );
			}
			
			$ovabrw_custom_tax[$i]['slug'] = $slug;
			$ovabrw_custom_tax[$i]['name'] = $value['name'];
			if( isset( $value['label_frontend'] ) && $value['label_frontend'] ){
				$ovabrw_custom_tax[$i]['name'] = $value['label_frontend'];
			}else{
				$ovabrw_custom_tax[$i]['name'] = $value['name'];
			}
			
			$i++;
		}
	}

	// Get Custom Taxonomy from Code
	// Add new taxonomy, make it hierarchical (like categories)
	$number_taxonomy = ovabrw_get_setting( get_option( 'ova_brw_number_taxonomy', 0 ) );
	if ( $number_taxonomy > 0 ) {
		for ( $i = 1; $number_taxonomy >= $i; $i++ ) {
			$param_arr = [];
			$param_arr = apply_filters( 'register_taxonomy_ovabrw_' . $i, $param_arr ) ;

			if ( empty( $param_arr ) || ! is_array( $param_arr ) ) {
				$labels = array(
					'name'              => _x( 'Custom Taxonomy ' . $i, 'taxonomy general name', 'ova-brw' ),
					'singular_name'     => _x( 'taxonomy' . $i, 'taxonomy singular name', 'ova-brw' ),
					'search_items'      => esc_html__( 'Search Taxonomy ' . $i, 'ova-brw' ),
					'all_items'         => esc_html__( 'All Taxonomy ' . $i, 'ova-brw' ),
					'parent_item'       => esc_html__( 'Parent Taxonomy ' . $i, 'ova-brw' ),
					'parent_item_colon' => esc_html__( 'Parent Taxonomy ' . $i .': ', 'ova-brw' ),
					'edit_item'         => esc_html__( 'Edit Taxonomy ' . $i, 'ova-brw' ),
					'update_item'       => esc_html__( 'Update Taxonomy ' . $i, 'ova-brw' ),
					'add_new_item'      => esc_html__( 'Add New Taxonomy ' . $i, 'ova-brw' ),
					'new_item_name'     => esc_html__( 'New Taxonomy ' . $i .' Name', 'ova-brw' ),
					'menu_name'         => esc_html__( 'Custom Taxonomy ' . $i, 'ova-brw' ),
					'type'         => 'taxonomy_default' . $i,
				);

				$args = array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_admin_column' => false,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'taxonomy_default' . $i ),

				);
			} else {
				$labels = array(
					'name'              => _x( $param_arr['name'], 'taxonomy general name', 'ova-brw' ),
					'singular_name'     => _x( $param_arr['slug'], 'taxonomy singular name', 'ova-brw' ),
					'search_items'      => esc_html__( 'Search ' . $param_arr['name'], 'ova-brw' ),
					'all_items'         => esc_html__( 'All ' . $param_arr['name'], 'ova-brw' ),
					'parent_item'       => esc_html__( 'Parent ' . $param_arr['name'], 'ova-brw' ),
					'parent_item_colon' => esc_html__( 'Parent ' . $param_arr['name'] .': ', 'ova-brw' ),
					'edit_item'         => esc_html__( 'Edit ' . $param_arr['name'], 'ova-brw' ),
					'update_item'       => esc_html__( 'Update ' . $param_arr['name'], 'ova-brw' ),
					'add_new_item'      => esc_html__( 'Add New ' . $param_arr['name'], 'ova-brw' ),
					'new_item_name'     => esc_html__( 'New ' . $param_arr['name'] , 'ova-brw' ),
					'menu_name'         => esc_html__( $param_arr['name'], 'ova-brw' ),
					'type'         => $param_arr['slug'],
				);

				$args = array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_admin_column' => false,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => $param_arr['slug'] ),

				);
			}

			$name_taxonomy[$i]['slug'] = $args['labels']['type'];
			$name_taxonomy[$i]['name'] = $args['labels']['name'];

			register_taxonomy( $args['labels']['type'], array( 'product' ), $args );
		}
	}

	$name_taxonomy = array_merge_recursive( $name_taxonomy, $ovabrw_custom_tax);

	return $name_taxonomy;
}
