<?php if ( ! defined( 'ABSPATH' ) ) exit();


if ( ! class_exists( 'Ovabrw_Metabox' ) ) {
    class Ovabrw_Metabox{
        // Constructor
        public function __construct() {
            // Render metabox of Product
            add_action( 'woocommerce_product_options_general_product_data', array( $this, 'ovabrw_render_fields_metabox' ) );

            // Save metabox of Product
            add_action( 'after_wcfm_products_manage_meta_save', array( $this, 'ovabrw_save_fields_metabox' ), 10, 2 );
            add_action( 'woocommerce_process_product_meta', array( $this, 'ovabrw_save_fields_metabox' ), 10, 2 );  
        }

        public function ovabrw_render_fields_metabox() {
            include( OVABRW_PLUGIN_PATH.'/admin/metabox/ovabrw-custom-fields.php' );
        }

        public function ovabrw_save_fields_metabox( $post_id, $data ) {
            if ( ! is_array( $data ) ) {
                $data = $_POST;
            }

            /* Save custom field */

            // Children price
            $ovabrw_children_price = isset($data['ovabrw_children_price']) ? ovabrw_recursive_price($data['ovabrw_children_price']) : '';
            update_post_meta( $post_id, 'ovabrw_children_price', esc_attr( $ovabrw_children_price ) );

            // Baby price
            $ovabrw_baby_price = isset($data['ovabrw_baby_price']) ? ovabrw_recursive_price($data['ovabrw_baby_price']) : '';
            update_post_meta( $post_id, 'ovabrw_baby_price', esc_attr( $ovabrw_baby_price ) );

            // Duration
            $ovabrw_duration_checkbox = isset($data['ovabrw_duration_checkbox']) ? $data['ovabrw_duration_checkbox'] : '';
            update_post_meta( $post_id, 'ovabrw_duration_checkbox', esc_attr( $ovabrw_duration_checkbox ) );

            // Schedule
            $ovabrw_schedule_time = isset( $data['ovabrw_schedule_time'] ) ? $data['ovabrw_schedule_time'] : '';
            if ( ! empty( $ovabrw_schedule_time ) && current( $ovabrw_schedule_time ) ) {
                update_post_meta( $post_id, 'ovabrw_schedule_time', $ovabrw_schedule_time );
            } else {
                update_post_meta( $post_id, 'ovabrw_schedule_time', array() );
            }

            $ovabrw_schedule_adult_price = isset( $data['ovabrw_schedule_adult_price'] ) ? ovabrw_recursive_price($data['ovabrw_schedule_adult_price']) : '';
            if ( ! empty( $ovabrw_schedule_adult_price ) && current( $ovabrw_schedule_adult_price ) ) {
                update_post_meta( $post_id, 'ovabrw_schedule_adult_price', $ovabrw_schedule_adult_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_schedule_adult_price', array() );
            }

            $ovabrw_schedule_children_price = isset( $data['ovabrw_schedule_children_price'] ) ? ovabrw_recursive_price($data['ovabrw_schedule_children_price']) : '';
            if ( ! empty( $ovabrw_schedule_children_price ) && current( $ovabrw_schedule_children_price ) ) {
                update_post_meta( $post_id, 'ovabrw_schedule_children_price', $ovabrw_schedule_children_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_schedule_children_price', array() );
            }

            $ovabrw_schedule_baby_price = isset( $data['ovabrw_schedule_baby_price'] ) ? ovabrw_recursive_price($data['ovabrw_schedule_baby_price']) : '';
            if ( ! empty( $ovabrw_schedule_baby_price ) && current( $ovabrw_schedule_baby_price ) ) {
                update_post_meta( $post_id, 'ovabrw_schedule_baby_price', $ovabrw_schedule_baby_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_schedule_baby_price', array() );
            }

            $ovabrw_schedule_type = isset( $data['ovabrw_schedule_type'] ) ? $data['ovabrw_schedule_type'] : '';
            if ( ! empty( $ovabrw_schedule_type ) && current( $ovabrw_schedule_type ) ) {
                update_post_meta( $post_id, 'ovabrw_schedule_type', $ovabrw_schedule_type );
            } else {
                update_post_meta( $post_id, 'ovabrw_schedule_type', array() );
            }

            // Days
            $ovabrw_number_days = isset($data['ovabrw_number_days']) ? $data['ovabrw_number_days'] : '';
            update_post_meta( $post_id, 'ovabrw_number_days', esc_attr( $ovabrw_number_days ) );

            // Hours
            $ovabrw_number_hours = isset($data['ovabrw_number_hours']) ? ovabrw_recursive_price($data['ovabrw_number_hours']) : '';
            if ( floatval( $ovabrw_number_hours ) <= 0 ) { $ovabrw_number_hours = ''; }
            update_post_meta( $post_id, 'ovabrw_number_hours', esc_attr( $ovabrw_number_hours ) );

            // Amount of insurance
            $ovabrw_amount_insurance = isset($data['ovabrw_amount_insurance']) ? ovabrw_recursive_price($data['ovabrw_amount_insurance']) : '';
            update_post_meta( $post_id, 'ovabrw_amount_insurance', esc_attr( $ovabrw_amount_insurance ) );

            // Deposit
            $ovabrw_enable_deposit = isset($data['ovabrw_enable_deposit']) ? $data['ovabrw_enable_deposit'] : 'no';
            update_post_meta( $post_id, 'ovabrw_enable_deposit', esc_attr( $ovabrw_enable_deposit ) );

            $ovabrw_force_deposit = isset($data['ovabrw_force_deposit']) ? $data['ovabrw_force_deposit'] : 'no';
            update_post_meta( $post_id, 'ovabrw_force_deposit', esc_attr( $ovabrw_force_deposit ) );

            $ovabrw_type_deposit = isset($data['ovabrw_type_deposit']) ? $data['ovabrw_type_deposit'] : 'percent';
            update_post_meta( $post_id, 'ovabrw_type_deposit', esc_attr( $ovabrw_type_deposit) );

            $ovabrw_amount_deposit = isset($data['ovabrw_amount_deposit']) ? ovabrw_recursive_price($data['ovabrw_amount_deposit']) : 0;
            update_post_meta( $post_id, 'ovabrw_amount_deposit', esc_attr( $ovabrw_amount_deposit ) );

            // Stock Quantity
            $ovabrw_stock_quantity = isset($data['ovabrw_stock_quantity']) ? $data['ovabrw_stock_quantity'] : '';
            update_post_meta( $post_id, 'ovabrw_stock_quantity', esc_attr( $ovabrw_stock_quantity) );

            // Stock Quantity by Guests
            $ovabrw_stock_quantity_by_guests = isset( $data['ovabrw_stock_quantity_by_guests'] ) ? $data['ovabrw_stock_quantity_by_guests'] : '';
            update_post_meta( $post_id, 'ovabrw_stock_quantity_by_guests', $ovabrw_stock_quantity_by_guests );

            // Maximum total number of guest
            $ovabrw_max_total_guest = isset($data['ovabrw_max_total_guest']) && $data['ovabrw_max_total_guest'] ? $data['ovabrw_max_total_guest'] : '';
            update_post_meta( $post_id, 'ovabrw_max_total_guest', $ovabrw_max_total_guest );

            // Minimum total number of guest
            $ovabrw_min_total_guest = isset($data['ovabrw_min_total_guest']) && $data['ovabrw_min_total_guest'] ? $data['ovabrw_min_total_guest'] : '';
            update_post_meta( $post_id, 'ovabrw_min_total_guest', $ovabrw_min_total_guest );

            // Adults
            $ovabrw_adults_max = isset($data['ovabrw_adults_max']) && $data['ovabrw_adults_max'] ? $data['ovabrw_adults_max'] : '';
            update_post_meta( $post_id, 'ovabrw_adults_max', $ovabrw_adults_max );

            $ovabrw_adults_min = isset($data['ovabrw_adults_min']) && $data['ovabrw_adults_min'] ? $data['ovabrw_adults_min'] : '';
            update_post_meta( $post_id, 'ovabrw_adults_min', $ovabrw_adults_min );

            // Childrens
            $ovabrw_childrens_max = isset($data['ovabrw_childrens_max']) && $data['ovabrw_childrens_max'] ? $data['ovabrw_childrens_max'] : '';
            update_post_meta( $post_id, 'ovabrw_childrens_max', $ovabrw_childrens_max );

            $ovabrw_childrens_min = isset($data['ovabrw_childrens_min']) && $data['ovabrw_childrens_min'] ? $data['ovabrw_childrens_min'] : '';
            update_post_meta( $post_id, 'ovabrw_childrens_min', $ovabrw_childrens_min );

            // Babies
            $ovabrw_babies_max = isset($data['ovabrw_babies_max']) && $data['ovabrw_babies_max'] ? $data['ovabrw_babies_max'] : '';
            update_post_meta( $post_id, 'ovabrw_babies_max', $ovabrw_babies_max );

            $ovabrw_babies_min = isset($data['ovabrw_babies_min']) && $data['ovabrw_babies_min'] ? $data['ovabrw_babies_min'] : '';
            update_post_meta( $post_id, 'ovabrw_babies_min', $ovabrw_babies_min );

            // Embed Video
            $ovabrw_embed_video = isset($data['ovabrw_embed_video']) ? $data['ovabrw_embed_video'] : '';
            update_post_meta( $post_id, 'ovabrw_embed_video', $ovabrw_embed_video );

            // Destination
            $ovabrw_destination = isset($data['ovabrw_destination']) ? $data['ovabrw_destination'] : '';
            update_post_meta( $post_id, 'ovabrw_destination', $ovabrw_destination );
            
            $ovabrw_tour_plan_label = isset($data['ovabrw_tour_plan_label']) ? $data['ovabrw_tour_plan_label'] : '';
            if ( !empty( $ovabrw_tour_plan_label ) && current( $ovabrw_tour_plan_label ) ) {
                update_post_meta( $post_id, 'ovabrw_tour_plan_label', $ovabrw_tour_plan_label );
            }

            $ovabrw_tour_plan_desc = isset($data['ovabrw_tour_plan_desc']) ? $data['ovabrw_tour_plan_desc'] : '';
            if ( !empty( $ovabrw_tour_plan_desc ) && current( $ovabrw_tour_plan_desc ) ) {
                update_post_meta( $post_id, 'ovabrw_tour_plan_desc', $ovabrw_tour_plan_desc );
            }

            // Features
            $ovabrw_features_icons = isset($data['ovabrw_features_icons']) ? $data['ovabrw_features_icons'] : '';
            if ( !empty( $ovabrw_features_icons ) && current( $ovabrw_features_icons ) ) {
                update_post_meta( $post_id, 'ovabrw_features_icons', $ovabrw_features_icons );
            } else {
                update_post_meta( $post_id, 'ovabrw_features_icons', array() );
            }

            $ovabrw_features_desc = isset($data['ovabrw_features_desc']) ? $data['ovabrw_features_desc'] : '';
            if ( !empty( $ovabrw_features_desc ) && current( $ovabrw_features_desc ) ) {
                update_post_meta( $post_id, 'ovabrw_features_desc', $ovabrw_features_desc );
            } else {
                update_post_meta( $post_id, 'ovabrw_features_desc', array() );
            }

            $ovabrw_features_label = isset($data['ovabrw_features_label']) ? $data['ovabrw_features_label'] : '';
            if ( !empty( $ovabrw_features_label ) && current( $ovabrw_features_label ) ) {
                update_post_meta( $post_id, 'ovabrw_features_label', $ovabrw_features_label );
            } else {
                update_post_meta( $post_id, 'ovabrw_features_label', array() );
            }

            // Fixed Time
            $ovabrw_fixed_time_check_in = isset($data['ovabrw_fixed_time_check_in']) ? $data['ovabrw_fixed_time_check_in'] : '';
            if ( !empty( $ovabrw_fixed_time_check_in ) && current( $ovabrw_fixed_time_check_in ) ) {
                update_post_meta( $post_id, 'ovabrw_fixed_time_check_in', $ovabrw_fixed_time_check_in );
            } else {
                update_post_meta( $post_id, 'ovabrw_fixed_time_check_in', '' );
            }

            $ovabrw_fixed_time_check_out = isset($data['ovabrw_fixed_time_check_out']) ? $data['ovabrw_fixed_time_check_out'] : '';
            if ( !empty( $ovabrw_fixed_time_check_out ) && current( $ovabrw_fixed_time_check_out ) ) {
                update_post_meta( $post_id, 'ovabrw_fixed_time_check_out', $ovabrw_fixed_time_check_out );
            } else {
                update_post_meta( $post_id, 'ovabrw_fixed_time_check_out', '' );
            }

            // Global Discount (GD)
            $ovabrw_gd_adult_price = isset($data['ovabrw_gd_adult_price']) ? ovabrw_recursive_price($data['ovabrw_gd_adult_price']) : '';
            if ( !empty( $ovabrw_gd_adult_price ) && current( $ovabrw_gd_adult_price ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_gd_adult_price', $ovabrw_gd_adult_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_gd_adult_price', array() );
            }

            $ovabrw_gd_children_price = isset($data['ovabrw_gd_children_price']) ? ovabrw_recursive_price($data['ovabrw_gd_children_price']) : '';
            if ( !empty( $ovabrw_gd_children_price ) && current( $ovabrw_gd_children_price ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_gd_children_price', $ovabrw_gd_children_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_gd_children_price', array() );
            }

            $ovabrw_gd_baby_price = isset($data['ovabrw_gd_baby_price']) ? ovabrw_recursive_price($data['ovabrw_gd_baby_price']) : '';
            if ( !empty( $ovabrw_gd_baby_price ) && current( $ovabrw_gd_baby_price ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_gd_baby_price', $ovabrw_gd_baby_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_gd_baby_price', array() );
            }

            $ovabrw_gd_duration_min = isset($data['ovabrw_gd_duration_min']) ? $data['ovabrw_gd_duration_min'] : '';
            if ( !empty( $ovabrw_gd_duration_min ) && current( $ovabrw_gd_duration_min ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_gd_duration_min', $ovabrw_gd_duration_min );
            } else {
                update_post_meta( $post_id, 'ovabrw_gd_duration_min', array() );
            }
            
            $ovabrw_gd_duration_max = isset($data['ovabrw_gd_duration_max']) ? $data['ovabrw_gd_duration_max'] : '';
            if ( !empty( $ovabrw_gd_duration_max ) && current( $ovabrw_gd_duration_max ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_gd_duration_max', $ovabrw_gd_duration_max );
            } else {
                update_post_meta( $post_id, 'ovabrw_gd_duration_max', array() );
            }

            // Special Time (ST)
            $ovabrw_st_adult_price = isset($data['ovabrw_st_adult_price']) ? ovabrw_recursive_price($data['ovabrw_st_adult_price']) : '';
            if ( !empty( $ovabrw_st_adult_price ) && current( $ovabrw_st_adult_price ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_st_adult_price', $ovabrw_st_adult_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_st_adult_price', array() );
            }

            $ovabrw_st_children_price = isset($data['ovabrw_st_children_price']) ? ovabrw_recursive_price($data['ovabrw_st_children_price']) : '';
            if ( !empty( $ovabrw_st_children_price ) && current( $ovabrw_st_children_price ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_st_children_price', $ovabrw_st_children_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_st_children_price', array() );
            }

            $ovabrw_st_baby_price = isset($data['ovabrw_st_baby_price']) ? ovabrw_recursive_price($data['ovabrw_st_baby_price']) : '';
            if ( !empty( $ovabrw_st_baby_price ) && current( $ovabrw_st_baby_price ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_st_baby_price', $ovabrw_st_baby_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_st_baby_price', array() );
            }

            $ovabrw_st_startdate = isset($data['ovabrw_st_startdate']) ? $data['ovabrw_st_startdate'] : '';
            if ( !empty( $ovabrw_st_startdate ) && current( $ovabrw_st_startdate ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_st_startdate', $ovabrw_st_startdate );
            } else {
                update_post_meta( $post_id, 'ovabrw_st_startdate', array() );
            }

            $ovabrw_st_enddate = isset($data['ovabrw_st_enddate']) ? $data['ovabrw_st_enddate'] : '';
            if ( !empty( $ovabrw_st_enddate ) && current( $ovabrw_st_enddate ) != '' ) {
                update_post_meta( $post_id, 'ovabrw_st_enddate', $ovabrw_st_enddate );
            } else {
                update_post_meta( $post_id, 'ovabrw_st_enddate', array() );
            }

            $ovabrw_st_discount = isset($data['ovabrw_st_discount']) ? ovabrw_recursive_price($data['ovabrw_st_discount']) : '';
            if ( !empty( $ovabrw_st_discount ) && current( $ovabrw_st_discount ) != '' ) {
                if ( is_array($ovabrw_st_discount) && array_key_exists('ovabrw_key', $ovabrw_st_discount) ) {
                    unset($ovabrw_st_discount['ovabrw_key']);// Remove array has key is ovabrw_key 
                }
                update_post_meta( $post_id, 'ovabrw_st_discount', $ovabrw_st_discount );
            } else {
                update_post_meta( $post_id, 'ovabrw_st_discount', array() );
            }

            // Resources
            $ovabrw_rs_id = isset($data['ovabrw_rs_id']) ? $data['ovabrw_rs_id'] : '';
            if ( !empty( $ovabrw_rs_id ) && current( $ovabrw_rs_id ) ) {
                update_post_meta( $post_id, 'ovabrw_rs_id', $ovabrw_rs_id );
            } else {
                update_post_meta( $post_id, 'ovabrw_rs_id', array() );
            }

            $ovabrw_rs_name = isset($data['ovabrw_rs_name']) ? $data['ovabrw_rs_name'] : '';
            if ( !empty( $ovabrw_rs_name ) && current( $ovabrw_rs_name ) ) {
                update_post_meta( $post_id, 'ovabrw_rs_name', $ovabrw_rs_name );
            } else {
                update_post_meta( $post_id, 'ovabrw_rs_name', array() );
            }

            $ovabrw_rs_description = isset($data['ovabrw_rs_description']) ? $data['ovabrw_rs_description'] : '';
            if ( !empty( $ovabrw_rs_description ) && current( $ovabrw_rs_description ) ) {
                update_post_meta( $post_id, 'ovabrw_rs_description', $ovabrw_rs_description );
            } else {
                update_post_meta( $post_id, 'ovabrw_rs_description', array() );
            }
            
            $ovabrw_rs_adult_price = isset( $data['ovabrw_rs_adult_price'] ) ? ovabrw_recursive_price($data['ovabrw_rs_adult_price']) : '';
            if ( !empty( $ovabrw_rs_adult_price ) && current( $ovabrw_rs_adult_price ) ) {
                update_post_meta( $post_id, 'ovabrw_rs_adult_price', $ovabrw_rs_adult_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_rs_adult_price', array() );
            }

            $ovabrw_rs_children_price = isset( $data['ovabrw_rs_children_price'] ) ? ovabrw_recursive_price($data['ovabrw_rs_children_price']) : '';
            if ( !empty( $ovabrw_rs_children_price ) && current( $ovabrw_rs_children_price ) ) {
                update_post_meta( $post_id, 'ovabrw_rs_children_price', $ovabrw_rs_children_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_rs_children_price', array() );
            }

            $ovabrw_rs_baby_price = isset( $data['ovabrw_rs_baby_price'] ) ? ovabrw_recursive_price($data['ovabrw_rs_baby_price']) : '';
            if ( !empty( $ovabrw_rs_baby_price ) && current( $ovabrw_rs_baby_price ) ) {
                update_post_meta( $post_id, 'ovabrw_rs_baby_price', $ovabrw_rs_baby_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_rs_baby_price', array() );
            }

            $ovabrw_rs_duration_type = isset( $data['ovabrw_rs_duration_type'] ) ? $data['ovabrw_rs_duration_type'] : '';
            if ( !empty( $ovabrw_rs_duration_type ) && current( $ovabrw_rs_duration_type ) ) {
                update_post_meta( $post_id, 'ovabrw_rs_duration_type', $ovabrw_rs_duration_type );
            } else {
                update_post_meta( $post_id, 'ovabrw_rs_duration_type', array() );
            }

            // Services
            $ovabrw_label_service = isset($data['ovabrw_label_service']) ? $data['ovabrw_label_service'] : [];
            if ( !empty( $ovabrw_label_service ) && current( $ovabrw_label_service ) ) {
                update_post_meta( $post_id, 'ovabrw_label_service', $ovabrw_label_service );
            } else {
                update_post_meta( $post_id, 'ovabrw_label_service', array() );
            }

            $ovabrw_service_required = isset($data['ovabrw_service_required']) ? $data['ovabrw_service_required'] : [];
            if ( !empty( $ovabrw_service_required ) && current( $ovabrw_service_required ) ) {
                update_post_meta( $post_id, 'ovabrw_service_required', $ovabrw_service_required );
            } else {
                update_post_meta( $post_id, 'ovabrw_service_required', array() );
            }

            $ovabrw_service_id = isset($data['ovabrw_service_id']) ? $data['ovabrw_service_id'] : [];
            if ( !empty( $ovabrw_service_id ) && current( $ovabrw_service_id ) ) {
                if ( is_array($ovabrw_service_id) && array_key_exists('ovabrw_key', $ovabrw_service_id) ) {
                    unset($ovabrw_service_id['ovabrw_key']);// Remove array has key is ovabrw_key 
                }
                update_post_meta( $post_id, 'ovabrw_service_id', $ovabrw_service_id );
            } else {
                update_post_meta( $post_id, 'ovabrw_service_id', array() );
            }

            $ovabrw_service_name = isset($data['ovabrw_service_name']) ? $data['ovabrw_service_name'] : [];
            if ( !empty( $ovabrw_service_name ) && current( $ovabrw_service_name ) ) {
                if ( is_array($ovabrw_service_name) && array_key_exists('ovabrw_key', $ovabrw_service_name) ) {
                    unset($ovabrw_service_name['ovabrw_key']);// Remove array has key is ovabrw_key 
                }
                update_post_meta( $post_id, 'ovabrw_service_name', $ovabrw_service_name );
            } else {
                update_post_meta( $post_id, 'ovabrw_service_name', array() );
            }

            $ovabrw_service_adult_price = isset($data['ovabrw_service_adult_price']) ? ovabrw_recursive_price($data['ovabrw_service_adult_price']) : [];
            if ( !empty( $ovabrw_service_adult_price ) && current( $ovabrw_service_adult_price ) ) {
                if ( is_array($ovabrw_service_adult_price) && array_key_exists('ovabrw_key', $ovabrw_service_adult_price) ) {
                    unset($ovabrw_service_adult_price['ovabrw_key']);// Remove array has key is ovabrw_key 
                }
                update_post_meta( $post_id, 'ovabrw_service_adult_price', $ovabrw_service_adult_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_service_adult_price', array() );
            }

            $ovabrw_service_children_price = isset($data['ovabrw_service_children_price']) ? ovabrw_recursive_price($data['ovabrw_service_children_price']) : [];
            if ( !empty( $ovabrw_service_children_price ) && current( $ovabrw_service_children_price ) ) {
                if ( is_array($ovabrw_service_children_price) && array_key_exists('ovabrw_key', $ovabrw_service_children_price) ) {
                    unset($ovabrw_service_children_price['ovabrw_key']);// Remove array has key is ovabrw_key 
                }
                update_post_meta( $post_id, 'ovabrw_service_children_price', $ovabrw_service_children_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_service_children_price', array() );
            }

            $ovabrw_service_baby_price = isset($data['ovabrw_service_baby_price']) ? ovabrw_recursive_price($data['ovabrw_service_baby_price']) : [];
            if ( !empty( $ovabrw_service_baby_price ) && current( $ovabrw_service_baby_price ) ) {
                if ( is_array($ovabrw_service_baby_price) && array_key_exists('ovabrw_key', $ovabrw_service_baby_price) ) {
                    unset($ovabrw_service_baby_price['ovabrw_key']);// Remove array has key is ovabrw_key 
                }
                update_post_meta( $post_id, 'ovabrw_service_baby_price', $ovabrw_service_baby_price );
            } else {
                update_post_meta( $post_id, 'ovabrw_service_baby_price', array() );
            }

            $ovabrw_service_duration_type = isset($data['ovabrw_service_duration_type']) ? $data['ovabrw_service_duration_type'] : [];
            if ( !empty( $ovabrw_service_duration_type ) && current( $ovabrw_service_duration_type ) ) {
                if ( is_array($ovabrw_service_duration_type) && array_key_exists('ovabrw_key', $ovabrw_service_duration_type) ) {
                    unset($ovabrw_service_duration_type['ovabrw_key']);// Remove array has key is ovabrw_key 
                }
                update_post_meta( $post_id, 'ovabrw_service_duration_type', $ovabrw_service_duration_type );
            } else {
                update_post_meta( $post_id, 'ovabrw_service_duration_type', array() );
            }

            // Unavailable Time (UT)
            $ovabrw_untime_startdate = isset($data['ovabrw_untime_startdate']) ? $data['ovabrw_untime_startdate'] : '';
            if ( !empty( $ovabrw_untime_startdate ) && current( $ovabrw_untime_startdate ) ) {
                update_post_meta( $post_id, 'ovabrw_untime_startdate', $ovabrw_untime_startdate );
            } else {
                update_post_meta( $post_id, 'ovabrw_untime_startdate', array() );
            }

            $ovabrw_untime_enddate = isset($data['ovabrw_untime_enddate']) ? $data['ovabrw_untime_enddate'] : '';
            if ( !empty( $ovabrw_untime_enddate ) && current( $ovabrw_untime_enddate ) ) {
                update_post_meta( $post_id, 'ovabrw_untime_enddate', $ovabrw_untime_enddate );
            } else {
                update_post_meta( $post_id, 'ovabrw_untime_enddate', array() );
            }

            // Disable Week Day
            $ovabrw_product_disable_week_day = isset($data['ovabrw_product_disable_week_day']) ? $data['ovabrw_product_disable_week_day'] : '';
            update_post_meta( $post_id, 'ovabrw_product_disable_week_day', $ovabrw_product_disable_week_day );

            // Preparation time
            $ovabrw_preparation_time = isset($data['ovabrw_preparation_time']) ? $data['ovabrw_preparation_time'] : '';
            update_post_meta( $post_id, 'ovabrw_preparation_time', $ovabrw_preparation_time );

            // Book before X hours
            $ovabrw_book_before_x_hours = isset($data['ovabrw_book_before_x_hours']) ? $data['ovabrw_book_before_x_hours'] : '';
            update_post_meta( $post_id, 'ovabrw_book_before_x_hours', $ovabrw_book_before_x_hours );
            
            // Custom checkout fields
            $ovabrw_manage_custom_checkout_field = isset($data['ovabrw_manage_custom_checkout_field']) ? $data['ovabrw_manage_custom_checkout_field'] : '';
            update_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', $ovabrw_manage_custom_checkout_field );

            $ovabrw_product_custom_checkout_field = isset($data['ovabrw_product_custom_checkout_field']) ? $data['ovabrw_product_custom_checkout_field'] : 'global';
            update_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', $ovabrw_product_custom_checkout_field );

            // Show/Hide check-out field
            $ovabrw_product_checkout_field = isset( $data['ovabrw_manage_checkout_field']) ? $data['ovabrw_manage_checkout_field'] : '';
            update_post_meta( $post_id, 'ovabrw_manage_checkout_field', $ovabrw_product_checkout_field );

            // Forms Product
            $ovabrw_forms_product = isset($data['ovabrw_forms_product']) ? $data['ovabrw_forms_product'] : '';
            update_post_meta( $post_id, 'ovabrw_forms_product', $ovabrw_forms_product );

            // Product Template
            $ovabrw_product_template = isset($data['ovabrw_product_template']) ? $data['ovabrw_product_template'] : '';
            update_post_meta( $post_id, 'ovabrw_product_template', $ovabrw_product_template );

            // Short address
            $ovabrw_short_address = isset($data['ovabrw_short_address']) && $data['ovabrw_short_address'] ? $data['ovabrw_short_address'] : '';
            update_post_meta( $post_id, 'ovabrw_short_address', $ovabrw_short_address );

            // Google map
            $ovabrw_map_type  = isset($data['map_type']) ? $data['map_type'] : '';
            update_post_meta( $post_id, 'ovabrw_map_type', $ovabrw_map_type );

            $ovabrw_map_iframe = isset($data['map_iframe']) ? $data['map_iframe'] : '';
            update_post_meta( $post_id, 'ovabrw_map_iframe', $ovabrw_map_iframe );
            
            if ( get_option( 'ova_brw_google_key_map', false ) ) {
                $ovabrw_map_name  = isset($data['ovabrw_map_name']) ? $data['ovabrw_map_name'] : '';
                update_post_meta( $post_id, 'ovabrw_map_name', $ovabrw_map_name );

                $ovabrw_address   = isset($data['ovabrw_address']) ? $data['ovabrw_address'] : '';
                update_post_meta( $post_id, 'ovabrw_address', $ovabrw_address );

                $ovabrw_latitude  = isset($data['ovabrw_latitude']) ? $data['ovabrw_latitude'] : '';
                update_post_meta( $post_id, 'ovabrw_latitude', $ovabrw_latitude );

                $ovabrw_longitude = isset($data['ovabrw_longitude']) ? $data['ovabrw_longitude'] : '';
                update_post_meta( $post_id, 'ovabrw_longitude', $ovabrw_longitude );
            }
        }
    }
}

new Ovabrw_Metabox();