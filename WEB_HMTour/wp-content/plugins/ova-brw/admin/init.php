<?php if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'Ovabrw_Admin' ) ) {
    class Ovabrw_Admin {
        public function __construct() {
            // Create Menu in Admin Woo
            if ( current_user_can( 'administrator' ) || current_user_can('edit_posts') ) {
                add_action('admin_menu', array( $this, 'ovabrw_add_menu' ) );

                // Setting
                require_once( OVABRW_PLUGIN_PATH.'/admin/setting/class_ovabrw_settings.php' );

                // Admin Ajax
                require_once( OVABRW_PLUGIN_PATH.'/admin/class_admin_ajax.php' );
                
                // Display Table Order 
                require_once( OVABRW_PLUGIN_PATH.'/admin/order/class_render_table_order.php' );
                require_once( OVABRW_PLUGIN_PATH.'/admin/order/class_display_table_order.php' );
                
                // Create Order
                require_once( OVABRW_PLUGIN_PATH.'/admin/order/class_create_order.php' );
                
                // Check Product
                require_once( OVABRW_PLUGIN_PATH.'/admin/class_ovabrw_check_product.php' );

                // Custom checkout fields
                require_once( OVABRW_PLUGIN_PATH.'/admin/custom-checkout-fields/class_custom_checkout_field.php' );

                // Custom Taxonomy
                require_once( OVABRW_PLUGIN_PATH.'/admin/custom-taxonomy/custom_taxonomy.php' );

                // Category
                require_once( OVABRW_PLUGIN_PATH.'/admin/category/init.php' );

                // Save database
                require_once( OVABRW_PLUGIN_PATH.'/admin/model.php' );

            }
            
            //add metabox
            require_once( OVABRW_PLUGIN_PATH.'/admin/metabox/class_ovabrw_metabox.php' );
        }
        
        public function ovabrw_add_menu() {
            add_submenu_page(
                'edit.php?post_type=product',
                __( 'Create Order', 'ova-brw' ),
                __( 'Create Order', 'ova-brw' ),
                apply_filters( 'ovabrw_create_order_cap' ,'edit_posts' ),
                'ovabrw-create-order',
                'ovabrw_create_order'
            );
            
            add_submenu_page(
                'edit.php?post_type=product',
                __( 'Manage Order', 'ova-brw' ),
                __( 'Manage Order', 'ova-brw' ),
                apply_filters( 'ovabrw_manage_order_cap' ,'edit_posts' ),
                'ovabrw-manage-order',
                'ovabrw_display_order'
            );

            add_submenu_page(
                'edit.php?post_type=product',
                __( 'Check Product', 'ova-brw' ),
                __( 'Check Product', 'ova-brw' ),
                apply_filters( 'ovabrw_check_product_cap' ,'edit_posts' ),
                'ovabrw-check-product',
                'ovabrw_check_product'
            );

            add_submenu_page(
                'edit.php?post_type=product',
                __( 'Custom Checkout Field', 'ova-brw' ),
                __( 'Custom Checkout Field', 'ova-brw' ),
                apply_filters( 'ovabrw_add_checkout_field_cap' ,'manage_options' ),
                'ovabrw-custom-checkout-field',
                'ovabrw_custom_checkout_field'
            );

            add_submenu_page(
                'edit.php?post_type=product',
                __( 'Custom Taxonomy', 'ova-brw' ),
                __( 'Custom Taxonomy', 'ova-brw' ),
                apply_filters( 'ovabrw_add_custom_taxonomy_field_cap' ,'manage_options' ),
                'ovabrw-custom-taxonomy',
                'ovabrw_custom_taxonomy'
            );
        }
    }
}

new Ovabrw_Admin();