<?php
defined( 'ABSPATH' ) || exit();

// 1: Validate Booking Form And Rent Time
add_filter( 'woocommerce_add_to_cart_validation', 'ovabrw_validation_booking_form', 10, 3 );
if ( ! function_exists( 'ovabrw_validation_booking_form' ) ) {
    function ovabrw_validation_booking_form( $passed, $product_id, $quantity ) {
        // Check product type
        $product = wc_get_product( $product_id );

        if ( !$product || !$product->is_type('ovabrw_car_rental') ) return $passed;

        $duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

        $data = $_POST;
        $date_format = ovabrw_get_date_format();
        $time_format = ovabrw_get_time_format();

        // Check-in date
        $checkin_date = '';

        if ( $duration ) {
            $date_format    = ovabrw_get_datetime_format();
            $time_from      = ovabrw_get_meta_data( 'ovabrw_time_from', $data );

            // Error empty time from
            if ( empty( $time_from ) ) {
                wc_clear_notices();
                echo wc_add_notice( __( 'No time, please choose another date!', 'ova-brw' ), 'error');
                return false;
            }

            $checkin_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) );

            if ( $time_from ) {
                $checkin_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) . ' ' . $time_from );
            }
        } else {
            $checkin_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) );
        }

        // Check-out date
        $checkout_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickoff_date', $data ) );

        // Check-out
        if ( !$checkout_date ) {
            $checkout_date = ovabrw_get_checkout_date( $product_id, $checkin_date );
            $checkout_date = strtotime( $checkout_date );
        }

        // Number of adults
        $numberof_adults = (int)ovabrw_get_meta_data( 'ovabrw_adults', $data );

        // Number of children
        $numberof_children = (int)ovabrw_get_meta_data( 'ovabrw_childrens', $data );

        // Number of babies
        $numberof_babies = (int)ovabrw_get_meta_data( 'ovabrw_babies', $data );
        
        // Quantity
        $quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $data, 1 );

        // Set Pick-up, Drop-off Date again
        $new_dates              = ovabrw_new_input_date( $product_id, $checkin_date, $checkout_date, $date_format );
        $new_checkin_date       = ovabrw_get_meta_data( 'pickup_date_new', $new_dates );
        $new_checkout_date      = ovabrw_get_meta_data( 'pickoff_date_new', $new_dates );

        // Error empty Pick Up Date
        if ( empty( $new_checkin_date ) ) {
            wc_clear_notices();
            echo wc_add_notice( __("Insert Pick-up date", 'ova-brw'), 'error');
            return false;
        }

        // Error Pick Up Date < Current Time
        if ( $new_checkin_date < current_time('timestamp') ) {
            wc_clear_notices();

            echo wc_add_notice( __("Pick-up Date must be greater than Current Time", 'ova-brw'), 'error');

            return false;
        }

        // Preparation Time
        $preparation_time = get_post_meta( $product_id, 'ovabrw_preparation_time', true );

        if ( $preparation_time && apply_filters( 'ovabrwft_preparation_time', true ) ) {
            $today = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );

            if ( $new_checkin_date < ( $today + $preparation_time*86400 - 86400 ) ) {
                echo wc_add_notice( sprintf( __("Book %s day(s) in advance from the current time", 'ova-brw'), $preparation_time ), 'error');

                return false;
            }
        }
        // End

        // Booking before X hours today
        $product_book_before_x_hours    = get_post_meta( $product_id, 'ovabrw_book_before_x_hours', true );
        $booking_before_x_hours_today   = ovabrw_get_setting( get_option( 'ova_brw_booking_before_x_hours_today', '' ) );

        if ( $product_book_before_x_hours ) {
            $booking_before_x_hours_today = $product_book_before_x_hours;
        }

        if ( $booking_before_x_hours_today && apply_filters( 'ovabrwft_book_before_x_hours', true ) ) {
            $date_format = $date_format . ' ' . $time_format;
            $hours_check = $booking_before_x_hours_today;

            // Check hour
            $args_hours_today = explode( ":", $hours_check );

            if ( isset( $args_hours_today[0] ) && $args_hours_today[0] == '00' ) {
                $hours_check = str_replace( array( ' am', ' pm', ' AM', ' PM' ), '', $hours_check );
                $date_format = str_replace( array( ' a', ' A' ), '', $date_format );
            }

            $strtt_today    = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );
            $strtt_pickup   = strtotime( date( 'Y-m-d', $new_checkin_date ) );

            if ( $preparation_time ) {
                $strtt_today += $preparation_time*86400;
            }

            if ( $strtt_today === $strtt_pickup ) {
                $datetime_check = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) . ' ' . $hours_check );

                if ( current_time( 'timestamp' ) > $datetime_check ) {
                    wc_clear_notices();
                    echo wc_add_notice( sprintf( __("Book before %s hours today", 'ova-brw'), $hours_check ), 'error');

                    return false;
                }
            }
        }

        // Error Pick Up Date > Pick Off Date
        if ( $new_checkin_date > $new_checkout_date ) {
            wc_clear_notices();
            echo wc_add_notice( __("Drop-off Date must be greater than Pick-up Date", 'ova-brw'), 'error');
            return false;
        }

        // Guests validation
        $guests_mesg = ovabrw_guests_validation( $product_id, array(
            'numberof_adults'   => $numberof_adults,
            'numberof_children' => $numberof_children,
            'numberof_babies'   => $numberof_babies
        ));
        if ( $guests_mesg ) {
            wc_clear_notices();
            echo wc_add_notice( $guests_mesg, 'error');
            return false;
        }

        // Error Quantity
        if ( $quantity < 1 ) {
            wc_clear_notices();
            echo wc_add_notice( __("Please choose quantity greater 0", 'ova-brw'), 'error');   
            return false;
        }

        // Check service
        if ( ovabrw_check_array( $data, 'ovabrw_service' ) ) {
            $ovabrw_service = $data['ovabrw_service'];
            $ovabrw_service_required = get_post_meta( $product_id, 'ovabrw_service_required', true );
            if ( $ovabrw_service_required ) {
                foreach( $ovabrw_service_required as $key => $value ) {
                    if ( 'yes' === $value ) {
                        if ( !( isset( $ovabrw_service[$key] ) && $ovabrw_service[$key] ) ) {
                            wc_clear_notices();
                            echo wc_add_notice( __("Please choose Service", 'ova-brw'), 'error');   
                            return false;
                            break;
                        }
                    }
                }
            }
        }

        // Custom Checkout Fields
        $list_extra_fields = ovabrw_get_list_field_checkout( $product_id );

        if ( is_array( $list_extra_fields ) && ! empty( $list_extra_fields ) ) {
            foreach ( $list_extra_fields as $key => $field ) {
                if ( $field['enabled'] === 'on' ) {
                    if ( $field['type'] === 'file' ) {
                        $files      = isset( $_FILES[$key] ) ? $_FILES[$key] : '';
                        $file_name  = isset( $files['name'] ) ? $files['name'] : '';

                        if ( $field['required'] === 'on' && ! $file_name  ) {
                            wc_clear_notices();
                            echo wc_add_notice( sprintf( __( '%s field is required', 'ova-brw'), $field['label'] ), 'error' );
                            return false;
                        }

                        if ( $file_name ) {
                            if ( isset( $files['size'] ) && $files['size'] ) {
                                $mb = absint( $files['size'] ) / 1048576;

                                if ( $mb > $field['max_file_size'] ) {
                                    wc_clear_notices();
                                    echo wc_add_notice( sprintf( __( '%s max file size %sMB', 'ova-brw'), $field['label'], $field['max_file_size'] ), 'error' );
                                    return false;
                                }
                            }

                            $overrides = [
                                'test_form' => false,
                                'mimes'     => apply_filters( 'ovabrw_ft_file_mimes', [
                                    'jpg'   => 'image/jpeg',
                                    'jpeg'  => 'image/pjpeg',
                                    'png'   => 'image/png',
                                    'pdf'   => 'application/pdf',
                                    'doc'   => 'application/msword',
                                ]),
                            ];

                            require_once( ABSPATH . 'wp-admin/includes/admin.php' );

                            $upload = wp_handle_upload( $files, $overrides );

                            if ( isset( $upload['error'] ) ) {
                                wc_clear_notices();
                                echo wc_add_notice( $upload['error'] , 'error' );
                                return false;
                            }
                            
                            $object = array(
                                'name' => basename( $upload['file'] ),
                                'url'  => $upload['url'],
                                'mime' => $upload['type'],
                            );

                            $prefix = 'ovabrw_'.$key;

                            $_POST[$prefix] = $object;
                        }
                    } elseif ( $field['type'] === 'checkbox' ) {
                        $value = isset( $_POST[$key] ) ? $_POST[$key] : '';

                        if ( empty( $value ) && $field['required'] === 'on' ) {
                            wc_clear_notices();
                            echo wc_add_notice( __( $field['label'].' field is required', 'ova-brw'), 'error');
                            return false;
                        }
                    } else {
                        $value = sanitize_text_field( filter_input( INPUT_POST, $key ) );

                        if ( ! $value && $field['required'] === 'on' ) {
                            wc_clear_notices();
                            echo wc_add_notice( __( $field['label'].' field is required', 'ova-brw'), 'error');
                            return false;
                        }
                    }
                }
            }
        }

        // Check guests available
        if ( ovabrw_qty_by_guests( $product_id ) ) {
            $guests = [
                'adults'     => $numberof_adults * $quantity,
                'children'   => $numberof_children * $quantity,
                'babies'     => $numberof_babies * $quantity
            ];

            $guests_available = ovabrw_validate_guests_available( $product_id, $new_checkin_date, $new_checkout_date, $guests, 'cart' );

            if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
                return true;
            }
        } else {
            $quantity_available = ova_validate_manage_store( $product_id, $new_checkin_date, $new_checkout_date, $passed, $validate = 'cart', $quantity );
        
            if ( ! empty( $quantity_available ) ) {
                return $quantity_available['status'];
            }
        }

        return false;
    }
}

// 2: Add Extra Data To Cart Item
add_filter( 'woocommerce_add_cart_item_data', 'ovabrw_add_extra_data_to_cart_item',10, 4 );
if ( ! function_exists( 'ovabrw_add_extra_data_to_cart_item' ) ) {
    function ovabrw_add_extra_data_to_cart_item( $cart_item_data, $product_id, $variation_id, $quantity ) {

        // Check product type: rental
        $product = wc_get_product( $product_id );

        if ( ! $product || !$product->is_type('ovabrw_car_rental') ) return $cart_item_data;

        $data           = $_POST;
        $date_format    = ovabrw_get_date_format();
        $checkin_date   = $checkout_date = '';

        // Duration
        $duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

        if ( $duration ) {
            $time_from = '';

            if ( ovabrw_check_array( $data, 'ovabrw_time_from' ) ) {
                $time_from = $data['ovabrw_time_from'];
            } else {
                $time_from = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_time_from', $data ) );
            }

            $cart_item_data['ovabrw_time_from'] = $time_from;

            // Check-in
            if ( ovabrw_check_array( $data, 'ovabrw_pickup_date' ) ) {
                $checkin_date = $data['ovabrw_pickup_date'] . ' ' . $time_from;
            } else {
                $checkin_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) ) . ' ' . $time_from;
            }
        } else {
            // Check-in
            if ( ovabrw_check_array( $data, 'ovabrw_pickup_date' ) ) {
                $checkin_date = $data['ovabrw_pickup_date'];
            } else {
                $checkin_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) );
            }
        }

        $cart_item_data['ovabrw_pickup_date'] = $checkin_date;

        // Check-out
        if ( ovabrw_check_array( $data, 'ovabrw_pickoff_date' ) ) {
            $checkout_date = $data['ovabrw_pickoff_date'];
        } else {
            $checkout_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickoff_date', $data ) );
        }

        // Check-out
        if ( ! $checkout_date ) {
            $checkout_date = ovabrw_get_checkout_date( $product_id, strtotime( $checkin_date ) );
        }

        $cart_item_data['ovabrw_pickoff_date'] = $checkout_date;

        // If Check-in & Check-out empty
        if ( empty( $checkin_date ) && empty( $checkout_date ) ) {
            return $cart_item_data;
        }

        // Number of Adults
        $cart_item_data['ovabrw_adults'] = (int)ovabrw_get_meta_data( 'ovabrw_adults', $data );

        // Number of Children
        $cart_item_data['ovabrw_childrens'] = (int)ovabrw_get_meta_data( 'ovabrw_childrens', $data );

        // Number of Babies
        $cart_item_data['ovabrw_babies'] = (int)ovabrw_get_meta_data( 'ovabrw_babies', $data );
        
        // Quantity
        $cart_item_data['ovabrw_quantity'] = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $data, 1 );

        // Get Custom Field Booking Form
        $list_extra_fields  = ovabrw_get_list_field_checkout( $product_id );
        $args_ckf           = array();

        if ( is_array( $list_extra_fields ) && ! empty( $list_extra_fields ) ) {
            foreach( $list_extra_fields as $key => $field ) {
                if ( $field['enabled'] == 'on' ) {
                    if ( $field['type'] === 'file' ) {
                        $prefix = 'ovabrw_'.$key;

                        if ( isset( $_POST[$prefix] ) && is_array( $_POST[$prefix] ) ) {
                            $cart_item_data[$key] = '<a href="'.esc_url( $_POST[$prefix]['url'] ).'" title="'.esc_attr( $_POST[$prefix]['name'] ).'" target="_blank">'.esc_attr( $_POST[$prefix]['name'] ).'</a>';
                        } else {
                            $cart_item_data[$key] = '';
                        }
                    } elseif ( $field['type'] === 'select' ) {
                        $options_key = $options_text = array();

                        $val_op = sanitize_text_field( filter_input( INPUT_POST, $key ) );
                        $args_ckf[$key] = $val_op;
                        
                        if ( ovabrw_check_array( $field, 'ova_options_key' ) ) {
                            $options_key = $field['ova_options_key'];
                        }

                        if ( ovabrw_check_array( $field, 'ova_options_text' ) ) {
                            $options_text = $field['ova_options_text'];
                        }

                        $key_op = array_search( $val_op, $options_key );

                        if ( ! is_bool( $key_op ) ) {
                            if ( ovabrw_check_array( $options_text, $key_op ) ) {
                                $val_op = $options_text[$key_op];
                            }
                        }

                        $cart_item_data[$key] = $val_op;
                    } elseif ( $field['type'] === 'checkbox' ) {
                        $checkbox_val = $checkbox_key = $checkbox_text = array();

                        $val_checkbox = isset( $_POST[$key] ) && $_POST[$key] ? $_POST[$key] : '';

                        if ( ! empty( $val_checkbox ) && is_array( $val_checkbox ) ) {
                            $args_ckf[$key] = $val_checkbox;

                            if ( ovabrw_check_array( $field, 'ova_checkbox_key' ) ) {
                                $checkbox_key = $field['ova_checkbox_key'];
                            }

                            if ( ovabrw_check_array( $field, 'ova_checkbox_text' ) ) {
                                $checkbox_text = $field['ova_checkbox_text'];
                            }

                            foreach ( $val_checkbox as $val_cb ) {
                                $key_cb = array_search( $val_cb, $checkbox_key );

                                if ( ! is_bool( $key_cb ) ) {
                                    if ( ovabrw_check_array( $checkbox_text, $key_cb ) ) {
                                        array_push( $checkbox_val , $checkbox_text[$key_cb] );
                                    }
                                }
                            }
                        }

                        if ( ! empty( $checkbox_val ) && is_array( $checkbox_val ) ) {
                            $cart_item_data[$key] = join( ", ", $checkbox_val );
                        }
                    } else {
                        $cart_item_data[$key] = sanitize_text_field( filter_input( INPUT_POST, $key ) );

                        if ( in_array( $field['type'], array( 'radio', 'checkbox' ) ) ) {
                            $args_ckf[$key] = sanitize_text_field( filter_input( INPUT_POST, $key ) );
                        }
                    }
                }
            }
        }

        // Custom Checkout Fields
        if ( $args_ckf ) {
            $cart_item_data['custom_ckf'] = $args_ckf;
        }

        // Resources
        $ovabrw_resource_checkboxs = array();
        if ( ovabrw_check_array( $data, 'ovabrw_rs_checkboxs' ) ) {
            $ovabrw_resource_checkboxs = recursive_array_replace( '\\', '', $data['ovabrw_rs_checkboxs'] );
        }
        $cart_item_data['ovabrw_resources'] = $ovabrw_resource_checkboxs;

        // Services
        $ovabrw_services = array();
        if ( ovabrw_check_array( $data, 'ovabrw_service' ) ) {
            $ovabrw_services = recursive_array_replace( '\\', '', $data['ovabrw_service'] );
        }
        $cart_item_data['ovabrw_services'] = $ovabrw_services;

        // Deposit
        $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
        $type_deposit   = isset( $_REQUEST['ova_type_deposit'] ) ? sanitize_text_field( $_REQUEST['ova_type_deposit'] ) : '';

        if ( 'yes' === $deposit_enable && 'deposit' === $type_deposit ) {
            $cart_item_data['is_deposit'] = true;
        }

        return $cart_item_data;
    }
}

// 3: Display Extra Data in the Cart
add_filter( 'woocommerce_get_item_data', 'ovabrw_display_extra_data_cart', 10, 2 );
if ( ! function_exists( 'ovabrw_display_extra_data_cart' ) ) {
    function ovabrw_display_extra_data_cart( $item_data, $cart_item ) {
        // Check product type: rental
        if ( ! $cart_item['data']->is_type('ovabrw_car_rental') ) return $item_data;

        if ( $item_data ) {
            unset( $item_data );
        }

        if ( empty( $cart_item['ovabrw_pickup_date'] ) && empty( $cart_item['ovabrw_pickoff_date'] ) ) {
            wc_clear_notices();
            wc_add_notice( __('Insert full data in booking form', 'ova-brw'), 'notice');

            return false;
        }

        $date_format = ovabrw_get_date_format();

        if ( ovabrw_check_array( $cart_item, 'ovabrw_time_from' ) ) {
            $date_format    = ovabrw_get_datetime_format();
            $time_from      = $cart_item['ovabrw_time_from'];

            $item_data[] = array(
                'key'     => esc_html__( 'Time from', 'ova-brw' ),
                'value'   => wc_clean( $time_from ),
                'display' => '',
                'hidden'  => true,
            );
        }

        // Check-in date
        if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
            $checkin_date = date_i18n( $date_format, strtotime( $cart_item['ovabrw_pickup_date'] ) );

            $item_data[] = array(
                'key'     => esc_html__( 'Check in', 'ova-brw' ),
                'value'   => wc_clean( $checkin_date ),
                'display' => '',
            );
        }

        // Check-out date
        if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
            $checkout_date = date_i18n( $date_format, strtotime( $cart_item['ovabrw_pickoff_date'] ) );

            if ( 'yes' === get_option( 'ova_brw_booking_form_show_checkout', 'yes' ) ) {
                $item_data[] = array(
                    'key'     => esc_html__( 'Check out', 'ova-brw' ),
                    'value'   => wc_clean( $checkout_date ),
                    'display' => '',
                );
            } else {
                $item_data[] = array(
                    'key'     => esc_html__( 'Check out', 'ova-brw' ),
                    'value'   => wc_clean( $checkout_date ),
                    'display' => '',
                    'hidden'  => true,
                );
            }
        }

        // Number of Adults
        if ( (int)ovabrw_get_meta_data( 'ovabrw_adults', $cart_item ) ) {
            $item_data[] = array(
                'key'     => esc_html__( 'Adults', 'ova-brw' ),
                'value'   => $cart_item['ovabrw_adults'],
                'display' => '',
            );
        }

        // Number of children
        if ( (int)ovabrw_get_meta_data( 'ovabrw_childrens', $cart_item ) ) {
            $item_data[] = array(
                'key'     => esc_html__( 'Children', 'ova-brw' ),
                'value'   => $cart_item['ovabrw_childrens'],
                'display' => '',
            );
        }

        // Number of babies
        if ( (int)ovabrw_get_meta_data( 'ovabrw_babies', $cart_item ) ) {
            $item_data[] = array(
                'key'     => esc_html__( 'Babies', 'ova-brw' ),
                'value'   => $cart_item['ovabrw_babies'],
                'display' => '',
            );
        }

        // Custom checkout fields
        $list_extra_fields = ovabrw_get_list_field_checkout( $cart_item['product_id'] );
        if ( is_array( $list_extra_fields ) && ! empty( $list_extra_fields ) ) {
            foreach ( $list_extra_fields as $key => $field ) {
                $value = array_key_exists( $key, $cart_item ) ? $cart_item[$key] : '';

                if ( ! empty( $value ) && $field['enabled'] == 'on' ) {
                    if ( $field['type'] === 'file' ) {
                        $item_data[] = array(
                            'key'     => $field['label'],
                            'value'   => $value,
                            'display' => '',
                        );
                    } else {
                        $item_data[] = array(
                            'key'     => $field['label'],
                            'value'   => wc_clean( $value ),
                            'display' => '',
                        );
                    }
                }
            }
        }

        // Services
        if ( ovabrw_check_array( $cart_item, 'ovabrw_services' ) ) {
            $label_service      = get_post_meta( $cart_item['product_id'], 'ovabrw_label_service', true ); 
            $service_id         = get_post_meta( $cart_item['product_id'], 'ovabrw_service_id', true ); 
            $service_name       = get_post_meta( $cart_item['product_id'], 'ovabrw_service_name', true );
            $ovabrw_services    = $cart_item['ovabrw_services'];

            if ( is_array( $ovabrw_services ) ) {
                foreach ( $ovabrw_services as $ser_id ) {
                    if( ! empty( $service_id ) && is_array( $service_id ) ) {
                        foreach( $service_id as $key => $value ) {
                            if ( !empty( $value ) && is_array( $value ) ) {
                                foreach( $value as $k => $val ) {
                                    if ( !empty( $val ) && $ser_id == $val ) {
                                        $s_label  = $label_service[$key];
                                        $s_name   = $service_name[$key][$k];
                                        
                                        $item_data[] = array(
                                            'key'     => $s_label,
                                            'value'   => wc_clean( $s_name ),
                                            'display' => '',
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Resources
        if ( ovabrw_check_array( $cart_item, 'ovabrw_resources' ) ) {
            $resource_arr = array();

            foreach ( $cart_item['ovabrw_resources'] as $r_key => $r_value ) {
                if ( !in_array( $r_value, $resource_arr ) ) {
                    array_push( $resource_arr, $r_value );
                }
            }

            if ( !empty( $resource_arr ) ) {
                if ( 1 === count( $resource_arr ) ) {
                    $item_data[] = array(
                        'key'     => esc_html__( 'Resource', 'ova-brw' ),
                        'value'   => wc_clean( join( ', ', $resource_arr ) ),
                        'display' => '',
                    );
                } else {
                    $item_data[] = array(
                        'key'     => esc_html__( 'Resources', 'ova-brw' ),
                        'value'   => wc_clean( join( ', ', $resource_arr ) ),
                        'display' => '',
                    );
                }
            }
        }

        return $item_data;
    }
}

// 4: Checkout Validate
add_action( 'woocommerce_after_checkout_validation', 'ovabrw_after_checkout_validation', 10, 2 );
if ( ! function_exists( 'ovabrw_after_checkout_validation' ) ) {
    function ovabrw_after_checkout_validation( $data, $errors ) {
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $product = $cart_item['data'];

            $pickup_date = '';
            if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
                $pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
            }

            $pickoff_date = '';
            if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
                $pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );
            }

            $stock_quantity = 1;
            if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
                $stock_quantity = absint( $cart_item['ovabrw_quantity'] );
            }

            if ( ! empty( $product ) && $product->is_type( 'ovabrw_car_rental' ) ) {
                $product_id     = $product->get_id();
                $date_format    = ovabrw_get_date_format();

                // Duration
                $duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

                if ( $duration ) {
                    $date_format = ovabrw_get_datetime_format();
                }

                // Set Pick-up, Drop-off Date again
                $new_input_date     = ovabrw_new_input_date( $product_id, $pickup_date, $pickoff_date, $date_format );
                $pickup_date_new    = $new_input_date['pickup_date_new'];
                $pickoff_date_new   = $new_input_date['pickoff_date_new'];

                if ( ovabrw_qty_by_guests( $product_id ) ) {
                    $ovabrw_adults = 1;
                    if ( ovabrw_check_array( $cart_item, 'ovabrw_adults' ) ) {
                        $ovabrw_adults = strtotime( $cart_item['ovabrw_adults'] );
                    }

                    $ovabrw_childrens = 0;
                    if ( ovabrw_check_array( $cart_item, 'ovabrw_childrens' ) ) {
                        $ovabrw_childrens = strtotime( $cart_item['ovabrw_childrens'] );
                    }

                    $ovabrw_babies = 0;
                    if ( ovabrw_check_array( $cart_item, 'ovabrw_babies' ) ) {
                        $ovabrw_babies = strtotime( $cart_item['ovabrw_babies'] );
                    }

                    $guests = [
                        'adults'     => $ovabrw_adults * $stock_quantity,
                        'children'   => $ovabrw_childrens * $stock_quantity,
                        'babies'     => $ovabrw_babies * $stock_quantity
                    ];

                    $guests_available = ovabrw_validate_guests_available( $product_id, $pickup_date, $pickoff_date, $guests, 'checkout' );

                    if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
                        return true;
                    } else {
                        $errors->add( 'validation', sprintf( __('%s isn\'t available for this time, Please book other time.', 'ova-brw'), $product->name ) );
                    }
                } else {
                    $validate_manage_store = ova_validate_manage_store( $product_id, $pickup_date_new, $pickoff_date_new, $passed = true, $validate = 'checkout', $stock_quantity );

                    if ( ! empty( $validate_manage_store ) ) {
                        return $validate_manage_store['status'];
                    } else {
                        $errors->add( 'validation', sprintf( __('%s isn\'t available for this time, Please book other time.', 'ova-brw'), $product->name ) );
                    }
                }
            }
        }
    }
}

// 5: Save to Order
add_action( 'woocommerce_checkout_create_order_line_item', 'ovabrw_add_extra_data_to_order_items', 10, 4 );
if ( ! function_exists( 'ovabrw_add_extra_data_to_order_items' ) ) {
    function ovabrw_add_extra_data_to_order_items( $item, $cart_item_key, $values, $order ) {
        $product_id = $item->get_product_id();

        // Check product type: rental
        $product = wc_get_product( $product_id );

        if ( ! empty( $product ) && ! $product->is_type('ovabrw_car_rental') ) return;

        if ( empty( $values['ovabrw_pickup_date'] ) && empty( $values['ovabrw_pickoff_date'] ) ) {
            return;
        }

        // Check in & Check out
        $item->add_meta_data( 'ovabrw_pickup_date', $values['ovabrw_pickup_date'] );
        $item->add_meta_data( 'ovabrw_pickoff_date', $values['ovabrw_pickoff_date'] );

        // Duration
        if ( ovabrw_check_array( $values, 'ovabrw_time_from' ) ) {
            $item->add_meta_data( 'ovabrw_time_from', $values['ovabrw_time_from'] );
        }

        // Guests
        if ( ovabrw_check_array( $values, 'ovabrw_adults' ) ) {
            $item->add_meta_data( 'ovabrw_adults', $values['ovabrw_adults'] );
        } else {
            $item->add_meta_data( 'ovabrw_adults', 1 );
        }

        if ( ovabrw_check_array( $values, 'ovabrw_childrens' ) && !empty($values['ovabrw_childrens']) ) {
            $item->add_meta_data( 'ovabrw_childrens', $values['ovabrw_childrens'] );
        }

        if ( ovabrw_check_array( $values, 'ovabrw_babies' ) && !empty($values['ovabrw_babies'])  ) {
            $item->add_meta_data( 'ovabrw_babies', $values['ovabrw_babies'] );
        }

        // Quantity
        if ( ovabrw_check_array( $values, 'ovabrw_quantity' ) ) {
            $item->add_meta_data( 'ovabrw_quantity', $values['ovabrw_quantity'] );
        } else {
            $item->add_meta_data( 'ovabrw_quantity', 1 );
        }

        // Custom Checkout Fields
        $list_extra_fields = ovabrw_get_list_field_checkout( $product_id );

        if ( is_array( $list_extra_fields ) && ! empty( $list_extra_fields ) ) {
            foreach( $list_extra_fields as $key => $field ) {
                $value = array_key_exists( $key, $values ) ? $values[$key] : '';

                if ( ! empty( $value ) && $field['enabled'] == 'on' ) {
                    if ( 'select' === $field['type'] ) {
                        $options_key = $options_text = array();

                        if ( ovabrw_check_array( $field, 'ova_options_key' ) ) {
                            $options_key = $field['ova_options_key'];
                        }

                        if ( ovabrw_check_array( $field, 'ova_options_text' ) ) {
                            $options_text = $field['ova_options_text'];
                        }

                        $key_op = array_search( $value, $options_key );

                        if ( ! is_bool( $key_op ) ) {
                            if ( ovabrw_check_array( $options_text, $key_op ) ) {
                                $value = $options_text[$key_op];
                            }
                        }
                    }

                    $item->add_meta_data( $key, $value );
                }
            }
        }

        if ( isset( $values['custom_ckf'] ) && $values['custom_ckf'] ) {
            $item->add_meta_data( 'ovabrw_custom_ckf', $values['custom_ckf'] );
        }

        // Resouces
        if ( ovabrw_check_array( $values, 'ovabrw_resources' ) ) {
            $resource_arr = array();
            foreach( $values['ovabrw_resources'] as $r_key => $r_value ) {
                if ( !in_array( $r_value, $resource_arr ) ) {
                    array_push( $resource_arr, $r_value );
                }
            }

            if ( ! empty( $resource_arr ) ) {
                if ( count( $resource_arr ) == 1 ) {
                    $item->add_meta_data( esc_html__( 'Resource', 'ova-brw' ), join( ', ', $resource_arr ) );
                } else {
                    $item->add_meta_data( esc_html__( 'Resources', 'ova-brw' ), join( ', ', $resource_arr ) );
                }
            }

            $item->add_meta_data( 'ovabrw_resources', $values['ovabrw_resources'] ); 
        }

        // Services
        if ( ovabrw_check_array( $values, 'ovabrw_services' ) ) {
            $label_service      = get_post_meta( $product_id, 'ovabrw_label_service', true ); 
            $service_id         = get_post_meta( $product_id, 'ovabrw_service_id', true ); 
            $service_name       = get_post_meta( $product_id, 'ovabrw_service_name', true );
            $ovabrw_services    = $values['ovabrw_services'];

            $item->add_meta_data( 'ovabrw_services', $ovabrw_services );

            if ( is_array( $ovabrw_services ) ) {
                foreach( $ovabrw_services as $ser_id ) {
                    if( ! empty( $service_id ) && is_array( $service_id ) ) {
                        foreach( $service_id as $key => $value ) {
                            if ( !empty( $value ) && is_array( $value ) ) {
                                foreach( $value as $k => $val ) {
                                    if ( !empty( $val ) && $ser_id == $val ) {
                                        $s_label  = $label_service[$key];
                                        $s_name   = $service_name[$key][$k];
                                        $item->add_meta_data( $s_label, $s_name );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Insurance
        $insurance_amount = $values['data']->get_meta('insurance_amount');
        if ( $insurance_amount ) {
            $item->add_meta_data( 'ovabrw_insurance_amount', ovabrw_convert_price( $insurance_amount ) );

            // Insurance tax
            $insurance_tax = $values['data']->get_meta('insurance_tax');
            if ( $insurance_tax ) {
                $item->add_meta_data( 'ovabrw_insurance_tax', ovabrw_convert_price( $insurance_tax ) );
            }

            // Remaining insurance
            $remaining_insurance = $values['data']->get_meta('remaining_insurance');
            if ( $remaining_insurance ) {
                $item->add_meta_data( 'ovabrw_remaining_insurance', ovabrw_convert_price( $remaining_insurance ) );
            }

            // Remaining insurance tax
            $remaining_insurance_tax = $values['data']->get_meta('remaining_insurance_tax');
            if ( $remaining_insurance_tax ) {
                $item->add_meta_data( 'ovabrw_remaining_insurance_tax', ovabrw_convert_price( $remaining_insurance_tax ) );
            }
        }

        // Deposit
        if ( isset( WC()->cart->deposit_info[ 'has_deposit' ] ) && WC()->cart->deposit_info[ 'has_deposit' ] ) {
            $deposit_type       = $values['data']->get_meta('deposit_type');
            $deposit_value      = $values['data']->get_meta('deposit_value');
            $deposit_amount     = $values['data']->get_meta('deposit_amount');
            $remaining_amount   = $values['data']->get_meta('remaining_amount');
            $remaining_tax      = $values['data']->get_meta('remaining_tax');
            $total_payable      = $values['data']->get_meta('total_payable');

            if ( $deposit_type ) {
                $item->add_meta_data( 'ovabrw_deposit_type', $deposit_type );
            }
            if ( $deposit_value ) {
                $item->add_meta_data( 'ovabrw_deposit_value', $deposit_value );
            }
            if ( $deposit_amount ) {
                $item->add_meta_data( 'ovabrw_deposit_amount', ovabrw_convert_price( $deposit_amount ) );
            }
            if ( $remaining_amount ) {
                $item->add_meta_data( 'ovabrw_remaining_amount', ovabrw_convert_price( $remaining_amount ) );
            }
            if ( $remaining_tax ) {
                $item->add_meta_data( 'ovabrw_remaining_tax', $remaining_tax );
            }
            if ( $total_payable ) {
                $item->add_meta_data( 'ovabrw_total_payable', ovabrw_convert_price( $total_payable ) );
            }
        }
    }
}

// Checkout create order fee item
add_action( 'woocommerce_checkout_create_order_fee_item', function( $item, $fee_key, $fee, $order ) {
    $insurance_key = isset( WC()->cart->deposit_info[ 'insurance_key' ] ) ? WC()->cart->deposit_info[ 'insurance_key' ] : '';

    if ( $insurance_key === $fee_key ) {
        $order->add_meta_data( '_ova_insurance_key', $insurance_key );
    }
}, 10, 4 );

/**
 * [ova_validate_manage_store check product available]
 * @param  [number]  $product_id - ID product
 * @param  [strtotime]  $pickup_date - the date has been filtered via function
 * @param  [strtotime]  $pickoff_date - the date has been filtered via function  
 * @param  [string] $passed - true, false
 * @param  string  $validate - cart, checkout, empty
 * @param  integer $quantity - quantity in cart
 * @return [array] status
 */
if ( !function_exists( 'ova_validate_manage_store' ) ) {
    function ova_validate_manage_store( $product_id = false, $pickup_date = '', $pickoff_date = '', $passed = false,  $validate = 'cart', $quantity = 1 ) {
        $quantity = absint( $quantity );

        // Unavailable Time (UT)
        $validate_ut = ovabrw_validate_unavailable_time( $product_id, $pickup_date, $pickoff_date, $validate );
        if ( $validate_ut ) return false;

        // Disable week day
        $validate_dwd = ovabrw_validate_disable_week_day( $product_id, $pickup_date, $pickoff_date, $validate );
        if ( $validate_dwd ) return false;

        // Check Count Product in Order
        $store_quantity = ovabrw_quantity_available_in_order( $product_id, $pickup_date, $pickoff_date );
        
        // Check Count Product in Cart
        $cart_quantity  = ovabrw_quantity_available_in_cart( $product_id, $validate, $pickup_date, $pickoff_date );
        
        // Check Quantity Available
        $qty_available = ovabrw_get_quantity_available( $product_id, $store_quantity, $cart_quantity, $quantity, $passed, $validate );
        
        if ( ! empty( $qty_available ) && $qty_available['passed'] && $qty_available['quantity_available'] > 0 ) {
            return array( 
                'status'                => $qty_available['passed'],
                'quantity_available'    => $qty_available['quantity_available']
            );
        }

        return false;
    }
}

/**
 * Check quantity available in order
 */
if ( !function_exists( 'ovabrw_quantity_available_in_order' ) ) {
    function ovabrw_quantity_available_in_order( $product_id, $pickup_date, $pickoff_date ) {
        $quantity = $qty_order = 0;

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $product_id );

        // Get all Order ID by Product ID
        $statuses   = brw_list_order_status();
        $orders_ids = ovabrw_get_orders_by_product_id( $product_id, $statuses );

        if ( $orders_ids ) {
            foreach( $orders_ids as $key => $value ) {
                // Get Order Detail by Order ID
                $order = wc_get_order($value);

                // Get Meta Data type line_item of Order
                $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
                
                // For Meta Data
                foreach( $order_line_items as $item_id => $item ) {
                    $pickup_date_store = $pickoff_date_store = $id_vehicle_rented = '';

                    // Get product
                    $product_id = $item->get_product_id();

                    // Check Line Item have item ID is Car_ID
                    if ( in_array( $product_id , $array_product_ids ) ) {
                        $pickup_date_store  = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                        $pickoff_date_store = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                        $qty_order          = absint( $item->get_meta( 'ovabrw_quantity' ) );

                        // Only compare date when "PickOff Date in Store" > "Current Time" becaue "PickOff Date Rent" have to > "Current Time"
                        if ( $pickoff_date_store >= current_time( 'timestamp' ) ) {
                            if ( ! ( $pickup_date >= $pickoff_date_store || $pickoff_date <= $pickup_date_store ) ) {
                                $quantity += $qty_order;
                            }
                        }  
                    }
                }
            }
        }

        return $quantity;
    }
}

/**
 * Check quantity available in cart
 */
if ( !function_exists( 'ovabrw_quantity_available_in_cart' ) ) {
    function ovabrw_quantity_available_in_cart( $product_id, $validate, $pickup_date, $pickoff_date ) {
        $quantity = 0;

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $product_id );

        if ( $validate == 'cart' ) {
            foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ( in_array( $product_id, $array_product_ids ) ) {
                    $cart_pickup_date = '';
                    if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
                        $cart_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
                    }

                    $cart_pickoff_date = '';
                    if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
                        $cart_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );
                    }

                    if ( $cart_pickup_date && $cart_pickoff_date ) {
                        if ( !( $pickup_date >= $cart_pickoff_date || $pickoff_date <= $cart_pickup_date ) ) {
                            $quantity += $cart_item['ovabrw_quantity'];
                        }
                    }
                }
            }
        }

        return $quantity;
    }
}

/**
 * Get quantity available store
 */
if ( !function_exists( 'ovabrw_get_quantity_available' ) ) {
    function ovabrw_get_quantity_available( $product_id = false, $store_quantity = 0, $cart_quantity = 0, $quantity = 1, $passed = false, $validate = 'cart' ) {
        // Get stock quantity of product
        $stock_quantity = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );

        $quantity_available = (int)( $stock_quantity - $store_quantity - $cart_quantity );

        if ( $quantity_available > 0 && $quantity_available >= $quantity ) {
            $passed = true;
        } else {
            if ( $validate != 'search' && ! wp_doing_ajax() ) {
                if ( $quantity > $quantity_available && $quantity_available != 0 && $quantity_available > 0 ) {
                    wc_clear_notices();
                    echo wc_add_notice( sprintf( esc_html__( 'Available tour is %s', 'ova-brw'  ), $number_available ), 'error');
                } else {
                    wc_clear_notices();
                    echo wc_add_notice( esc_html__( 'Tour isn\'t available for this time, Please book other time.', 'ova-brw' ), 'error');
                }
            }

            if ( $quantity_available < 0 ) {
                $quantity_available = 0;
            }

            return false;
        }

        $data_quantity = [
            'passed'              => $passed,
            'quantity_available'  => $quantity_available,
        ];

        return $data_quantity;
    }
}

/**
 * Check Unavailable
 */
if ( !function_exists( 'ovabrw_check_unavailable' ) ) {
    function ovabrw_check_unavailable( $product_id, $pickup_date, $pickoff_date ) {
        // Error: Unvailable time for renting
        $untime_startdate = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $untime_enddate   = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( $untime_startdate ) {
            foreach( $untime_startdate as $key => $value ) {
                if ( isset( $untime_enddate[$key] ) && $untime_enddate[$key] && $untime_startdate[$key] ) {
                    $stt_untime_startdate   = strtotime( $untime_startdate[$key] );
                    $stt_untime_enddate     = strtotime( $untime_enddate[$key] );

                    if ( ! ( $pickup_date > $stt_untime_enddate || $pickoff_date < $stt_untime_startdate ) ) {
                        return true;
                    }
                }
            }
        }

        // Error: Unavailable Date for booking in settings
        $disable_week_day = get_post_meta( $product_id, 'ovabrw_product_disable_week_day', true );

        if ( ! $disable_week_day ) {
            $disable_week_day = get_option( 'ova_brw_calendar_disable_week_day', '' );
        }
        
        $data_disable_week_day = $disable_week_day != '' ? explode( ',', $disable_week_day ) : '';

        if ( $data_disable_week_day && $pickup_date && $pickoff_date ) {
            if ( apply_filters( 'ovabrw_disable_week_day', true ) ) {
                $datediff       = absint( $pickoff_date ) - absint( $pickup_date );
                $total_datediff = round( $datediff / (60 * 60 * 24), wc_get_price_decimals() ) + 1;

                // get number day
                $pickup_date_of_week   = date( 'w', $pickup_date );

                $pickup_date_timestamp = $pickup_date;
                
                $i = 0;

                while ( $i <= $total_datediff ) {
                    if ( in_array( $pickup_date_of_week, $data_disable_week_day ) ) {
                        return true;
                    }

                    $pickup_date_of_week    = date('w', $pickup_date_timestamp );
                    $pickup_date_timestamp  = strtotime('+1 day', $pickup_date_timestamp);
                    $i++;
                }
            } else {
                // get number day
                $pickup_date_of_week = date( 'w', $pickup_date );

                if ( in_array( $pickup_date_of_week, $data_disable_week_day ) ) return true;
            }
        }

        return false;
    }
}

/**
 * Standardized Pick-up, Drop-off that the Guest enter at frontend
 * User for: Search, Compare with real date
 */
if ( !function_exists( 'ovabrw_new_input_date' ) ) {
    function ovabrw_new_input_date( $product_id = '', $pickup_date = '', $pickoff_date = '', $date_format = 'd-m-Y' ) {
        if ( ! $product_id ) return array( 'pickup_date_new' => '', 'pickoff_date_new' => '' );

        $pickup_date    = $pickup_date ? strtotime( date( $date_format, $pickup_date ) ) : '';
        $pickoff_date   = $pickoff_date ? strtotime( date( $date_format, $pickoff_date ) ) : '';

        return array( 'pickup_date_new' => $pickup_date, 'pickoff_date_new' => $pickoff_date );
    }
}

/** ======== Quantity by Guests ======== */
// Product validate Guests available
if ( !function_exists( 'ovabrw_validate_guests_available' ) ) {
    function ovabrw_validate_guests_available( $product_id = null, $check_in = null, $check_out = null, $guests = [], $validate = 'cart' ) {
        if ( ! $product_id || ! $check_in || ! $check_out ) return false;

        // Unavailable Time (UT)
        $validate_ut = ovabrw_validate_unavailable_time( $product_id, $check_in, $check_out, $validate );
        if ( $validate_ut ) return false;

        // Disable week day
        $validate_dwd = ovabrw_validate_disable_week_day( $product_id, $check_in, $check_out, $validate );
        if ( $validate_dwd ) return false;

        // Get Guests in Cart
        $guests_in_cart = ovabrw_get_guests_in_cart( $product_id, $check_in, $validate );

        // Get Guests in Order
        $guests_in_order = ovabrw_get_guests_in_order( $product_id, $check_in );

        // Get Guests available
        $guests_available = ovabrw_get_guests_available( $product_id, $guests, $guests_in_cart, $guests_in_order, $validate );

        if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
            return $guests_available;
        }

        return false;
    }
}

// Product validate Unavailable time
if ( !function_exists( 'ovabrw_validate_unavailable_time' ) ) {
    function ovabrw_validate_unavailable_time( $product_id = null, $check_in = null, $check_out = null, $validate = 'cart' ) {
        if ( ! $product_id || ! $check_in || ! $check_out ) return false;

        do_action( 'ovabrw_before_validate_unavailable_time', $product_id, $check_in, $check_out, $validate );

        $untime_startdate = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $untime_enddate   = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( $untime_startdate ) {
            foreach ( $untime_startdate as $k => $start_date ) {
                if ( isset( $untime_enddate[$k] ) && $untime_enddate[$k] && $untime_startdate[$k] ) {
                    $strtt_startdate   = strtotime( $start_date );
                    $strtt_enddate     = strtotime( $untime_enddate[$k] );

                    if ( ! ( $check_in > $strtt_enddate || $check_out < $strtt_startdate ) ) {
                        if ( $validate != 'search' ) {
                            wc_clear_notices();
                            echo wc_add_notice( esc_html__( 'This time is not available for booking', 'ova-brw' ), 'error');
                        }

                        return true;
                    }
                }
            }
        }

        do_action( 'ovabrw_after_validate_unavailable_time', $product_id, $check_in, $check_out, $validate );

        return false;
    }
}

// Product validate Disable week day
if ( !function_exists( 'ovabrw_validate_disable_week_day' ) ) {
    function ovabrw_validate_disable_week_day( $product_id = null, $check_in = null, $check_out = null, $validate = 'cart' ) {
        if ( ! $product_id || ! $check_in || ! $check_out ) return false;

        do_action( 'ovabrw_before_validate_disable_week_day', $product_id, $check_in, $check_out, $validate );

        $disable_week_day = get_post_meta( $product_id, 'ovabrw_product_disable_week_day', true );

        if ( ! $disable_week_day ) {
            $disable_week_day = get_option( 'ova_brw_calendar_disable_week_day', '' );
        }

        if ( $disable_week_day != '' && $check_in && $check_out ) {
            $disable_week_day = explode( ',', $disable_week_day );

            if ( apply_filters( 'ovabrw_disable_week_day', true ) ) {
                $datediff       = absint( $check_out ) - absint( $check_in );
                $total_datediff = round( $datediff / 86400, 2 ) + 1;

                // Get number day
                $check_in_of_week   = date( 'w', $check_in );
                $check_out_of_week  = date( 'w', $check_out );

                $check_in_timestamp = $check_in;
                
                $i = 0;

                while ( $i <= $total_datediff ) {
                    if ( in_array( $check_in_of_week, $disable_week_day ) || in_array( $check_out_of_week, $disable_week_day ) ) {
                        if ( $validate != 'search' ) {
                            wc_clear_notices();
                            echo wc_add_notice( esc_html__( 'This time is not available for booking', 'ova-brw' ), 'error');
                        }

                        return true;
                    }

                    $check_in_of_week    = date('w', $check_in_timestamp );
                    $check_in_timestamp  = strtotime('+1 day', $check_in_timestamp);

                    $i++;
                }
            } else {
                // Get number day
                $check_in_of_week = date( 'w', $check_in );

                if ( in_array( $check_in_of_week, $disable_week_day ) ) {
                    if ( $validate != 'search' ) {
                        wc_clear_notices();
                        echo wc_add_notice( esc_html__( 'This time is not available for booking', 'ova-brw' ), 'error');
                    }
                    
                    return true;
                }
            }
        }

        do_action( 'ovabrw_after_validate_disable_week_day', $product_id, $check_in, $check_out, $validate );

        return false;
    }
}

// Product get Guests in Cart
if ( !function_exists( 'ovabrw_get_guests_in_cart' ) ) {
    function ovabrw_get_guests_in_cart( $product_id = null, $check_in = null, $validate = 'cart' ) {
        if ( ! $product_id || ! $check_in ) return false;

        if ( $validate === 'cart' || $validate === 'search' ) {
            $results = [
                'adults'     => 0,
                'children'   => 0,
                'babies'     => 0
            ];

            // Get array product ids when use WPML
            $args_product_ids = ovabrw_get_wpml_product_ids( $product_id );
            
            if ( WC()->cart && is_object( WC()->cart ) ) {
                foreach ( WC()->cart->get_cart() as $k => $cart_item ) {
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $k );

                    if ( in_array( $product_id, $args_product_ids ) ) {
                        $cart_check_in = '';
                        if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
                            $cart_check_in = strtotime( $cart_item['ovabrw_pickup_date'] );
                        }

                        $cart_adults = 0;
                        if ( ovabrw_check_array( $cart_item, 'ovabrw_adults' ) ) {
                            $cart_adults = absint( $cart_item['ovabrw_adults'] );
                        }

                        $cart_children = 0;
                        if ( ovabrw_check_array( $cart_item, 'ovabrw_childrens' ) ) {
                            $cart_children = absint( $cart_item['ovabrw_childrens'] );
                        }

                        $cart_babies = 0;
                        if ( ovabrw_check_array( $cart_item, 'ovabrw_babies' ) ) {
                            $cart_babies = absint( $cart_item['ovabrw_babies'] );
                        }

                        $cart_qty = 1;
                        if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
                            $cart_qty = absint( $cart_item['ovabrw_quantity'] );
                        }

                        if ( $cart_check_in && $cart_check_in >= current_time( 'timestamp' ) ) {
                            if ( $check_in == $cart_check_in ) {
                                $results['adults']      += $cart_adults * $cart_qty;
                                $results['children']    += $cart_children * $cart_qty;
                                $results['babies']      += $cart_babies * $cart_qty;
                            }
                        }
                    }
                }
            }

            return $results;
        }

        return false;
    }
}

// Product get Guests in Order
if ( !function_exists( 'ovabrw_get_guests_in_order' ) ) {
    function ovabrw_get_guests_in_order( $product_id = null, $check_in = null ) {
        if ( ! $product_id || ! $check_in ) return false;

        // Get all Order ID by Product ID
        $status     = brw_list_order_status();
        $order_ids  = ovabrw_get_orders_by_product_id( $product_id, $status );

        if ( ! empty( $order_ids ) && is_array( $order_ids ) ) {
            $results = [
                'adults'     => 0,
                'children'   => 0,
                'babies'     => 0
            ];

            // Get array product ids when use WPML
            $args_product_ids = ovabrw_get_wpml_product_ids( $product_id );

            foreach ( $order_ids as $k => $order_id ) {
                // Get Order
                $order = wc_get_order( $order_id );

                // Get order items
                $order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

                if ( ! empty( $order_items ) && is_array( $order_items ) ) {
                    foreach ( $order_items as $item_id => $item ) {
                        $product_id = $item->get_product_id();

                        // Check Line Item have item ID is Car_ID
                        if ( in_array( $product_id , $args_product_ids ) ) {
                            $item_check_in = '';
                            if ( strtotime( $item->get_meta( 'ovabrw_pickup_date' ) ) ) {
                                $item_check_in = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                            }

                            $item_adults = 0;
                            if ( absint( $item->get_meta( 'ovabrw_adults' ) ) ) {
                                $item_adults = absint( $item->get_meta( 'ovabrw_adults' ) );
                            }

                            $item_children = 0;
                            if ( absint( $item->get_meta( 'ovabrw_childrens' ) ) ) {
                                $item_children = absint( $item->get_meta( 'ovabrw_childrens' ) );
                            }

                            $item_babies = 0;
                            if ( absint( $item->get_meta( 'ovabrw_babies' ) ) ) {
                                $item_babies = absint( $item->get_meta( 'ovabrw_babies' ) );
                            }

                            $item_qty = 1;
                            if ( absint( $item->get_meta( 'ovabrw_quantity' ) ) ) {
                                $item_qty = absint( $item->get_meta( 'ovabrw_quantity' ) );
                            }

                            if ( $item_check_in && $item_check_in >= current_time( 'timestamp' ) ) {
                                if ( $check_in == $item_check_in ) {
                                    $results['adults']      += $item_adults * $item_qty;
                                    $results['children']    += $item_children * $item_qty;
                                    $results['babies']      += $item_babies * $item_qty;
                                }
                            }  
                        }
                    }
                }
            }

            return $results;
        }

        return false;
    }
}

// Product Get Guests available
if ( !function_exists( 'ovabrw_get_guests_available' ) ) {
    function ovabrw_get_guests_available( $product_id = null, $guests = [], $guests_cart = [], $guests_order = [], $validate = 'cart' ) {
        if ( ! $product_id ) return false;

        $max_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_max', true ) );
        $max_children   = absint( get_post_meta( $product_id, 'ovabrw_childrens_max', true ) );
        $max_babies     = absint( get_post_meta( $product_id, 'ovabrw_babies_max', true ) );
        $min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
        $min_children   = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
        $min_babies     = absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );
        $quantity       = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );

        $guests_available = [
            'adults'     => $max_adults * $quantity,
            'children'   => $max_children * $quantity,
            'babies'     => $max_babies * $quantity
        ];

        if ( ! empty( $guests_cart ) && is_array( $guests_cart ) ) {
            $guests_available['adults']    -= $guests_cart['adults'];
            $guests_available['children']  -= $guests_cart['children'];
            $guests_available['babies']    -= $guests_cart['babies'];
        }

        if ( ! empty( $guests_order ) && is_array( $guests_order ) ) {
            $guests_available['adults']    -= $guests_order['adults'];
            $guests_available['children']  -= $guests_order['children'];
            $guests_available['babies']    -= $guests_order['babies'];
        }

        // Check guests
        if ( isset( $guests['adults'] ) && $guests_available['adults'] < $guests['adults'] ) {
            if ( $validate != 'search' ) {
                wc_clear_notices();

                if ( ! $guests_available['adults'] ) {
                    echo wc_add_notice( esc_html__( 'Adults out of slots', 'ova-brw' ), 'error' );
                } else {
                    echo wc_add_notice( sprintf( esc_html__( 'Maximum number of adults: %s', 'ova-brw'  ), $guests_available['adults'] ), 'error' );
                }

                return false;
            }
        }

        if ( isset( $guests['children'] ) && $guests_available['children'] < $guests['children'] ) {
            if ( $validate != 'search' ) {
                wc_clear_notices();

                if ( ! $guests_available['children'] ) {
                    echo wc_add_notice( esc_html__( 'Children out of slots', 'ova-brw' ), 'error' );
                } else {
                    echo wc_add_notice( sprintf( esc_html__( 'Maximum number of children: %s', 'ova-brw'  ), $guests_available['children'] ), 'error' );
                }

                return false;
            }
        }

        if ( isset( $guests['babies'] ) && $guests_available['babies'] < $guests['babies'] ) {
            if ( $validate != 'search' ) {
                wc_clear_notices();

                if ( ! $guests_available['babies'] ) {
                    echo wc_add_notice( esc_html__( 'Babies out of slots', 'ova-brw' ), 'error' );
                } else {
                    echo wc_add_notice( sprintf( esc_html__( 'Maximum number of babies: %s', 'ova-brw'  ), $guests_available['babies'] ), 'error' );
                }

                return false;
            }
        }

        if ( ( ! $guests_available['adults'] || $guests_available['adults'] < 0 ) && ( ! $guests_available['children'] || $guests_available['children'] < 0 ) && ( ! $guests_available['babies'] || $guests_available['babies'] < 0 ) ) {
            return false;
        }

        if ( apply_filters( 'ovabrw_ft_check_min_guests', true, $product_id ) ) {
            if ( $guests_available['adults'] < $min_adults || $guests_available['children'] < $min_children || $guests_available['babies'] < $min_babies  ) {
                return false;
            }
        }

        return $guests_available;
    }
}

// Guests validation
if ( !function_exists( 'ovabrw_guests_validation' ) ) {
    function ovabrw_guests_validation( $product_id, $args = array() ) {
        if ( !$product_id ) {
            $mesg = esc_html__( 'Product does not exist.', 'ova-brw' );
            return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
        }

        // Number of guests
        $numberof_guests = 0; // Total number of guests

        // Number of adults
        $numberof_adults = (int)ovabrw_get_meta_data( 'numberof_adults', $args );

        // Minimun number of adults
        $min_adults = (int)get_post_meta( $product_id, 'ovabrw_adults_min', true );

        // Maximum number of adults
        $max_adults = (int)get_post_meta( $product_id, 'ovabrw_adults_max', true );

        // Check minimun number of adults
        if ( $numberof_adults < $min_adults ) {
            $mesg = sprintf( esc_html__( 'Please choose the number of adults larger %d', 'ova-brw' ), $min_adults );
            return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
        }

        // Check maximum number of adults
        if ( $max_adults && $numberof_adults > $max_adults ) {
            $mesg = sprintf( esc_html__( 'Please choose the number of adults less than or equals %d', 'ova-brw' ), $max_adults );
            return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
        }

        // Number of guests
        $numberof_guests += $numberof_adults;

        // Number of children
        if ( 'yes' === get_option( 'ova_brw_booking_form_show_children', 'yes' ) ) {
            $numberof_children = (int)ovabrw_get_meta_data( 'numberof_children', $args );

            // Minimum number of children
            $min_children = (int)get_post_meta( $product_id, 'ovabrw_childrens_min', true );

            // Maximum number of children
            $max_children = (int)get_post_meta( $product_id, 'ovabrw_childrens_max', true );

            // Check minimun number of children
            if ( $numberof_children < $min_children ) {
                $mesg = sprintf( esc_html__( 'Please choose the number of children larger %d', 'ova-brw' ), $min_children );
                return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
            }

            // Check maximum number of children
            if ( $max_children && $numberof_children > $max_children ) {
                $mesg = sprintf( esc_html__( 'Please choose the number of children less than or equals %d', 'ova-brw' ), $max_children );
                return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
            }

            // Number of guests
            $numberof_guests += $numberof_children;
        }

        // Number of babies
        if ( 'yes' === get_option( 'ova_brw_booking_form_show_baby', 'yes' ) ) {
            $numberof_babies = (int)ovabrw_get_meta_data( 'numberof_babies', $args );

            // Minimum number of babies
            $min_babies = (int)get_post_meta( $product_id, 'ovabrw_babies_min', true );

            // Maximum number of babies
            $max_babies = (int)get_post_meta( $product_id, 'ovabrw_babies_max', true );

            // Check minimun number of babies
            if ( $numberof_babies < $min_babies ) {
                $mesg = sprintf( esc_html__( 'Please choose the number of babies larger %d', 'ova-brw' ), $min_babies );
                return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
            }

            // Check maximum number of babies
            if ( $max_babies && $numberof_babies > $max_babies ) {
                $mesg = sprintf( esc_html__( 'Please choose the number of babies less than or equals %d', 'ova-brw' ), $max_babies );
                return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
            }

            // Number of guests
            $numberof_guests += $numberof_babies;
        }

        // Minimum number of guests
        $min_guests = (int)get_post_meta( $product_id, 'ovabrw_min_total_guest', true );

        // Maximum number of guests
        $max_guests = (int)get_post_meta( $product_id, 'ovabrw_max_total_guest', true );

        // Check number of guests
        if ( $numberof_guests <= 0 ) {
            $mesg = esc_html__( 'The number of guests is required.', 'ova-brw' );
            return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
        }

        // Check minimum number of guests
        if ( $numberof_guests < $min_guests ) {
            $mesg = sprintf( esc_html__( 'The total number of guests must be greater than %d', 'ova-brw' ), $min_guests );
            return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
        }

        // Check maximum number of guests
        if ( $max_guests && $numberof_guests > $max_guests ) {
            $mesg = sprintf( esc_html__( 'The total number of guests must be less than %d', 'ova-brw' ), $max_guests );
            return apply_filters( 'ovabrw_guests_validation', $mesg, $product_id, $args );
        }

        return apply_filters( 'ovabrw_guests_validation', false, $product_id, $args );
    }
}