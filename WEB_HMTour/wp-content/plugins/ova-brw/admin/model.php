<?php if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'Ovabrw_Model' ) ) {
    class Ovabrw_Model {
        public function __construct() {
            // Create new order manually
            add_action( 'admin_init', array( $this, 'ovabrw_create_new_order_manully' ) );
        }

        public function ovabrw_get_address() {
            $first_name         = isset( $_POST['ovabrw_first_name'] )  ? sanitize_text_field( $_POST['ovabrw_first_name'] )    : '';
            $last_name          = isset( $_POST['ovabrw_last_name'] )   ? sanitize_text_field( $_POST['ovabrw_last_name'] )     : '';
            $company            = isset( $_POST['ovabrw_company'] )     ? sanitize_text_field( $_POST['ovabrw_company'] )       : '';
            $email              = isset( $_POST['ovabrw_email'] )       ? sanitize_text_field( $_POST['ovabrw_email'] )         : '';
            $phone              = isset( $_POST['ovabrw_phone'] )       ? sanitize_text_field( $_POST['ovabrw_phone'] )         : '';
            $address_1          = isset( $_POST['ovabrw_address_1'] )   ? sanitize_text_field( $_POST['ovabrw_address_1'] )     : '';
            $address_2          = isset( $_POST['ovabrw_address_2'] )   ? sanitize_text_field( $_POST['ovabrw_address_2'] )     : '';
            $city               = isset( $_POST['ovabrw_city'] )        ? sanitize_text_field( $_POST['ovabrw_city'] )          : '';
            $country_setting    = isset( $_POST['ovabrw_country'] )     ? sanitize_text_field( $_POST['ovabrw_country'] )       : 'US';

            if ( strstr( $country_setting, ':' ) ) {
                $country_setting = explode( ':', $country_setting );
                $country         = current( $country_setting );
                $state           = end( $country_setting );
            } else {
                $country = $country_setting;
                $state   = '*';
            }

            $data_address = array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'company'    => $company,
                'email'      => $email,
                'phone'      => $phone,
                'address_1'  => $address_1,
                'address_2'  => $address_2,
                'city'       => $city,
                'country'    => $country,
            );

            return apply_filters( 'ovabrw_ft_get_address', $data_address );
        }

        public function ovabrw_add_order_item( $order_id ) {
            $data = $_POST;

            // order data
            $data_order = array();

            if ( ! $order_id ) {
                return $data_order;
            }

            $order = wc_get_order( $order_id ); // Get order

            // Get order items
            $has_deposit = $item_has_deposit = false;
            $order_items = $order->get_items(); 
            $order_total = $total_deposit = $total_remaining = $remaining_tax = $total_insurance = $insurance_tax = 0;
            $tax_rate_id = '';
            $tax_amount  = 0;

            // Init $i
            $i = 0;

            // Loop order items
            foreach ( $order_items as $item_id => $item ) {
                $data_item = array();

                $item = WC_Order_Factory::get_order_item( absint( $item_id ) );
                if ( ! $item ) {
                    continue;
                }

                $product_id = $item->get_product_id();
                $product    = $item->get_product();

                $time_from = '';
                if ( isset( $data['ovabrw_time_from'] ) && ovabrw_check_array( $data['ovabrw_time_from'], $product_id ) ) {
                    $time_from = $data['ovabrw_time_from'][$product_id];
                }

                $ovabrw_pickup_date = '';
                if ( isset( $data['ovabrw_pickup_date'] ) && ovabrw_check_array( $data['ovabrw_pickup_date'], $i ) ) {
                    $ovabrw_pickup_date = $data['ovabrw_pickup_date'][$i];

                    if ( $time_from ) {
                        $ovabrw_pickup_date .= ' ' . $time_from;
                    }
                }

                $ovabrw_pickoff_date = '';
                if ( isset( $data['ovabrw_pickoff_date'] ) && ovabrw_check_array( $data['ovabrw_pickoff_date'], $i ) ) {
                    $ovabrw_pickoff_date = $data['ovabrw_pickoff_date'][$i];
                }

                $ovabrw_adults = 1;
                if ( isset( $data['ovabrw_adults'] ) && ovabrw_check_array( $data['ovabrw_adults'], $i ) ) {
                    $ovabrw_adults = absint( $data['ovabrw_adults'][$i] );
                }

                $ovabrw_childrens = 0;
                if ( isset( $data['ovabrw_childrens'] ) && ovabrw_check_array( $data['ovabrw_childrens'], $i ) ) {
                    $ovabrw_childrens = absint( $data['ovabrw_childrens'][$i] );
                }

                $ovabrw_babies = 0;
                if ( isset( $data['ovabrw_babies'] ) && ovabrw_check_array( $data['ovabrw_babies'], $i ) ) {
                    $ovabrw_babies = absint( $data['ovabrw_babies'][$i] );
                }

                $ovabrw_resources = [];
                if ( isset( $data['ovabrw_resource_checkboxs'] ) && ovabrw_check_array( $data['ovabrw_resource_checkboxs'], $product_id ) ) {
                    $ovabrw_resources = $data['ovabrw_resource_checkboxs'][$product_id];
                }

                $ovabrw_services = [];
                if ( isset( $data['ovabrw_service'] ) && ovabrw_check_array( $data['ovabrw_service'], $product_id ) ) {
                    $ovabrw_services = $data['ovabrw_service'][$product_id];
                }

                // Intem insurance
                $item_insurance = 0;
                if ( isset( $data['ovabrw_amount_insurance'] ) && ovabrw_check_array( $data['ovabrw_amount_insurance'], $i ) ) {
                    $item_insurance = floatval( $data['ovabrw_amount_insurance'][$i] );
                }

                // Item deposit
                $item_deposit = 0;
                if ( isset( $data['ovabrw_amount_deposite'] ) && ovabrw_check_array( $data['ovabrw_amount_deposite'], $i ) ) {
                    $item_deposit = floatval( $data['ovabrw_amount_deposite'][$i] );
                }

                // Item remaining
                $item_remaining = 0;
                if ( isset( $data['ovabrw_amount_remaining'] ) && ovabrw_check_array( $data['ovabrw_amount_remaining'], $i ) ) {
                    $item_remaining = floatval( $data['ovabrw_amount_remaining'][$i] );
                }

                // Item subtotal
                $item_subtotal = 0;
                if ( isset( $data['ovabrw-total-product'] ) && ovabrw_check_array( $data['ovabrw-total-product'], $i ) ) {
                    $item_subtotal = floatval( $data['ovabrw-total-product'][$i] );
                }
                if ( $item_insurance ) $item_subtotal -= $item_insurance;

                if ( $time_from ) {
                    $data_item[ 'ovabrw_time_from' ] = $time_from;
                }

                $data_item[ 'ovabrw_pickup_date' ]  = $ovabrw_pickup_date;
                $data_item[ 'ovabrw_pickoff_date' ] = $ovabrw_pickoff_date;
                $data_item[ 'ovabrw_adults' ]       = $ovabrw_adults;
                $data_item[ 'ovabrw_childrens' ]    = $ovabrw_childrens;
                $data_item[ 'ovabrw_babies' ]       = $ovabrw_babies;
                $data_item[ 'ovabrw_quantity' ]     = 1;

                // Custom Checkout Fields
                $list_extra_fields  = ovabrw_get_list_field_checkout( $product_id );;
                $custom_ckf         = array();

                if ( ! empty( $list_extra_fields ) && is_array( $list_extra_fields ) ) {
                    foreach ( $list_extra_fields as $key => $field ) {
                        if ( $field['type'] === 'file' ) {
                            $data_file = isset( $_FILES[$key] ) ? $_FILES[$key] : '';

                            if ( $data_file ) {
                                $files = array();

                                if ( isset( $data_file['name'][$product_id] ) ) {
                                    $files['name'] = $data_file['name'][$product_id];
                                }
                                if ( isset( $data_file['full_path'][$product_id] ) ) {
                                    $files['full_path'] = $data_file['full_path'][$product_id];
                                }
                                if ( isset( $data_file['type'][$product_id] ) ) {
                                    $files['type'] = $data_file['type'][$product_id];
                                }
                                if ( isset( $data_file['tmp_name'][$product_id] ) ) {
                                    $files['tmp_name'] = $data_file['tmp_name'][$product_id];
                                }
                                if ( isset( $data_file['error'][$product_id] ) ) {
                                    $files['error'] = $data_file['error'][$product_id];
                                }
                                if ( isset( $data_file['size'][$product_id] ) ) {
                                    $files['size'] = $data_file['size'][$product_id];
                                }

                                if ( isset( $files['size'] ) && $files['size'] ) {
                                    $mb = absint( $files['size'] ) / 1048576;

                                    if ( $mb > $field['max_file_size'] ) {
                                        continue;
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
                                    continue;
                                }

                                $data_item[$key] = '<a href="'.esc_url( $upload['url'] ).'" title="'.esc_attr( basename( $upload['file'] ) ).'" target="_blank">'.esc_attr( basename( $upload['file'] ) ).'</a>';
                            }
                        } else {
                            $value = isset( $_POST[$key][$product_id] ) ? $_POST[$key][$product_id] : '';

                            if ( ! empty( $value ) && 'on' === $field['enabled'] ) {
                                if ( 'select' === $field['type'] ) {
                                    $custom_ckf[$key] = sanitize_text_field( $value );

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

                                if ( 'checkbox' === $field['type'] ) {
                                    $checkbox_val = $checkbox_key = $checkbox_text = array();

                                    if ( ! empty( $value ) && is_array( $value ) ) {
                                        $custom_ckf[$key] = $value;

                                        if ( ovabrw_check_array( $field, 'ova_checkbox_key' ) ) {
                                            $checkbox_key = $field['ova_checkbox_key'];
                                        }

                                        if ( ovabrw_check_array( $field, 'ova_checkbox_text' ) ) {
                                            $checkbox_text = $field['ova_checkbox_text'];
                                        }

                                        foreach ( $value as $val_cb ) {
                                            $key_cb = array_search( $val_cb, $checkbox_key );

                                            if ( ! is_bool( $key_cb ) ) {
                                                if ( ovabrw_check_array( $checkbox_text, $key_cb ) ) {
                                                    array_push( $checkbox_val , $checkbox_text[$key_cb] );
                                                }
                                            }
                                        }

                                        if ( ! empty( $checkbox_val ) && is_array( $checkbox_val ) ) {
                                            $value = join( ", ", $checkbox_val );
                                        }
                                    }
                                }

                                $data_item[$key] = $value;

                                if ( in_array( $field['type'], array( 'radio' ) ) ) {
                                    $custom_ckf[$key] = sanitize_text_field( $value );
                                }
                            }
                        }
                    }
                }

                if ( ! empty( $custom_ckf ) ) {
                    $data_item[ 'ovabrw_custom_ckf' ] = $custom_ckf;
                }
                // End custom checkout field

                // Item resources
                if ( ! empty( $ovabrw_resources ) && is_array( $ovabrw_resources ) ) {
                    $resource_ids   = get_post_meta( $product_id, 'ovabrw_rs_id', true );
                    $resource_name  = get_post_meta( $product_id, 'ovabrw_rs_name', true );
                    $data_resources = array();
                    $data_res_name  = array();

                    foreach ( $ovabrw_resources as $rs_id ) {
                        $rs_k = array_search( $rs_id, $resource_ids );

                        if ( ! is_bool( $rs_k ) ) {
                            if ( ovabrw_check_array( $resource_name, $rs_k ) ) {
                                array_push( $data_res_name, $resource_name[$rs_k] );
                                $data_resources[$rs_id] = $resource_name[$rs_k];
                            }
                        }
                    }

                    $data_item['ovabrw_resources'] = $data_resources;

                    if ( count( $data_res_name ) == 1 ) {
                        $data_item[ esc_html__( 'Resource', 'ova-brw' ) ] = join( ', ', $data_res_name );
                    } else {
                        $data_item[ esc_html__( 'Resources', 'ova-brw' ) ] = join( ', ', $data_res_name );
                    }
                }
                // End resources

                // Item services
                if ( $ovabrw_services && is_array( $ovabrw_services ) ) {
                    $data_item['ovabrw_services'] = $ovabrw_services;

                    $service_ids    = get_post_meta( $product_id, 'ovabrw_service_id', true );
                    $service_name   = get_post_meta( $product_id, 'ovabrw_service_name', true );
                    $service_label  = get_post_meta( $product_id, 'ovabrw_label_service', true );

                    foreach ( $ovabrw_services as $ovabrw_s_id ) {
                        $label = $name = '';
                        if ( $ovabrw_s_id && $service_ids && is_array( $service_ids ) ) {
                            foreach( $service_ids as $key_id => $service_id_arr ) {
                                $key = array_search( $ovabrw_s_id, $service_id_arr );

                                if ( !is_bool( $key ) ) {
                                    if ( ovabrw_check_array( $service_label, $key_id ) ) {
                                        $label = $service_label[$key_id];
                                    }
                                    if ( ovabrw_check_array( $service_name, $key_id ) ) {
                                        if ( ovabrw_check_array( $service_name[$key_id], $key ) ) {
                                            $name = $service_name[$key_id][$key];
                                        }
                                    }

                                    if ( $label && $name ) {
                                        $data_item[$label] = $name;
                                    }
                                }
                            }
                        }
                    }
                }
                // End services

                // Insurance amount
                if ( $item_insurance ) {
                    $data_item[ 'ovabrw_insurance_amount' ] = $item_insurance;

                    $sub_insurance_tax = ovabrw_get_insurance_tax_amount( $item_insurance );

                    if ( $sub_insurance_tax ) {
                        $insurance_tax += $sub_insurance_tax;

                        $data_item[ 'ovabrw_insurance_tax' ] = $sub_insurance_tax;
                    }
                }
                // End
                
                // Deposit
                if ( $item_deposit ) {
                    $data_item[ 'ovabrw_deposit_type' ]     = 'value';
                    $data_item[ 'ovabrw_deposit_value' ]    = $item_deposit;
                    $data_item[ 'ovabrw_deposit_amount' ]   = $item_deposit;
                    $data_item[ 'ovabrw_remaining_amount' ] = $item_remaining;
                    $data_item[ 'ovabrw_total_payable' ]    = $item_subtotal;
                    $item_subtotal = $item_deposit;
                }
                // End
                
                // Order total
                $order_total += $item_subtotal;

                // Taxable
                $item_taxes = false;

                if ( wc_tax_enabled() ) {
                    $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
                    if ( ! empty( $tax_rates ) ) {
                        $tax_rate_id = key( $tax_rates );
                    }

                    // Remaining tax
                    $item_remaining_tax = ovabrw_get_taxes_by_price( $product, $item_remaining );
                    $remaining_tax      += $item_remaining_tax;

                    // Add item remaining tax
                    $data_item['ovabrw_remaining_tax'] = $item_remaining_tax;

                    if ( wc_prices_include_tax() ) {
                        $taxes          = WC_Tax::calc_inclusive_tax( $item_subtotal, $tax_rates );
                        $item_tax       = WC_Tax::get_tax_total( $taxes );
                        $tax_amount    += $item_tax;
                        $item_subtotal -= $item_tax;
                    } else {
                        $taxes          = WC_Tax::calc_exclusive_tax( $item_subtotal, $tax_rates );
                        $item_tax       = WC_Tax::get_tax_total( $taxes );
                        $tax_amount    += $item_tax;
                        $order_total   += $item_remaining_tax;
                    }

                    $item_taxes = array(
                        'total'    => $taxes,
                        'subtotal' => $taxes,
                    );
                }

                // Update item meta data
                foreach ( $data_item as $meta_key => $meta_value ) {
                    $item->update_meta_data( $meta_key, $meta_value );
                }

                // Update item meta
                $item->set_props(
                    array(
                        'total'     => $item_subtotal,
                        'subtotal'  => $item_subtotal,
                        'taxes'     => $item_taxes
                    )
                );

                $item->save();
                // End update item meta
                
                $total_deposit      += $item_deposit;
                $total_remaining    += $item_remaining;
                $total_insurance    += $item_insurance;

                $i++;
            }

            $data_order = [
                'order_total'       => $order_total,
                'total_insurance'   => $total_insurance,
                'insurance_tax'     => $insurance_tax,
                'total_deposit'     => $total_deposit,
                'total_remaining'   => $total_remaining,
                'remaining_tax'     => $remaining_tax,
                'tax_rate_id'       => $tax_rate_id,
                'tax_amount'        => $tax_amount
            ];

            return apply_filters( 'ovabrw_ft_add_order_item', $data_order, $order_id );
        }

        public function ovabrw_create_new_order_manully() {
            $data = $_POST;

            if ( isset( $data['ovabrw_create_order'] ) && $data['ovabrw_create_order'] === 'create_order' ) {

                // Check Permission
                if ( !current_user_can( apply_filters( 'ovabrw_create_order' ,'publish_posts' ) ) ) {

                    echo '<div class="notice notice-error is-dismissible">
                            <h2>'.esc_html__( 'You don\'t have permission to create order', 'ova-brw' ).'</h2>
                        </div>';
                    return;
                }

                $order      = wc_create_order(); // Create new order
                $order_id   = $order->get_id(); // Get order id
                $order_meta = array(); // Order meta boxes

                // Get array product ids
                $product_ids    = isset( $data['ovabrw-data-product'] ) ? $data['ovabrw-data-product'] : [];
                $currency       = isset( $_POST['currency'] ) && $_POST['currency'] ? $_POST['currency'] : '';

                if ( $currency ) $order->set_currency( $currency );

                // Check order deposit
                $has_deposit = false;

                if ( !empty( $product_ids ) && is_array( $product_ids ) ) {
                    foreach ( $product_ids as $key => $product_id ) {
                        $product_id     = trim( sanitize_text_field( $product_id ) );
                        $product        = wc_get_product( $product_id );
                        $item_deposit   = isset( $_POST['ovabrw_amount_deposite'][$key] ) ? floatval( $_POST['ovabrw_amount_deposite'][$key] ) : 0;

                        if ( $item_deposit && floatval( $item_deposit ) > 0 ) {
                            $has_deposit = true;
                        }

                        $order->add_product( $product, 1 );
                    }
                }

                // Order item
                $order_data = $this->ovabrw_add_order_item( $order_id );

                // Get data total
                $order_total = $order_data['order_total'];

                // Taxable
                $tax_rate_id    = $order_data['tax_rate_id'];
                $tax_amount     = $order_data['tax_amount'];
                
                if ( $has_deposit ) {
                    $order_meta['_ova_has_deposit']        = 1;
                    $order_meta['_ova_deposit_amount']     = $order_data['total_deposit'];
                    $order_meta['_ova_remaining_amount']   = $order_data['total_remaining'];

                    if ( $order_data['remaining_tax'] ) {
                        $order_meta['_ova_remaining_tax'] = $order_data['remaining_tax'];
                    }
                }

                // Insurance
                if ( $order_data['total_insurance'] ) {
                    $insurance_tax  = floatval( $order_data['insurance_tax'] );
                    $insurance_name = ovabrw_get_insurance_fee_name();
                    $order_meta['_ova_insurance_amount'] = $order_data['total_insurance'];

                    if ( $insurance_tax ) {
                        $order_total    += $insurance_tax;
                        $tax_amount     += $insurance_tax;
                        
                        $order_meta['_ova_insurance_tax'] = $insurance_tax;
                    }

                    $item_fee = new WC_Order_Item_Fee();
                    $item_fee->set_props( array(
                        'name'      => $insurance_name,
                        'tax_class' => 0,
                        'amount'    => $order_data['total_insurance'],
                        'total'     => $order_data['total_insurance'],
                        'total_tax' => $insurance_tax,
                        'taxes'     => array(
                            'total' => array( $tax_rate_id => $insurance_tax ),
                        ),
                        'order_id'  => $order_id
                    ));

                    $item_fee->save();

                    $order->add_item( $item_fee );

                    $order_meta['_ova_insurance_key'] = sanitize_title( $insurance_name );
                    $order_total += $order_data['total_insurance'];
                }

                foreach ( $order_meta as $key => $update ) {
                    $order->update_meta_data( $key, $update );
                }

                // Set customer
                $email = isset( $_POST['ovabrw_email'] ) ? sanitize_text_field( $_POST['ovabrw_email'] )         : '';
                $user = get_user_by( 'email', $email );
                if ( $user ) {
                    $order->set_customer_id( $user->ID );
                }

                $order->set_address( $this->ovabrw_get_address(), 'billing' );

                // Taxable
                if ( wc_tax_enabled() ) {
                    $item_tax = new WC_Order_Item_Tax();

                    $item_tax->set_props(
                        array(
                            'rate_id'            => $tax_rate_id,
                            'tax_total'          => $tax_amount,
                            'shipping_tax_total' => 0,
                            'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
                            'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
                            'compound'           => WC_Tax::is_compound( $tax_rate_id ),
                            'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id ),
                        )
                    );

                    $item_tax->save();

                    $order->add_item( $item_tax );
                    $order->set_cart_tax( $tax_amount );

                    if ( wc_prices_include_tax() ) {
                        $order->update_meta_data( '_ova_prices_include_tax', 1 );
                    }
                }

                // Order status
                $order_status = isset( $_POST['status_order'] ) ? sanitize_text_field( $_POST['status_order'] ) : '';

                if ( $order_status ) {
                    $order->update_status( $order_status );
                }

                // Order set total
                $order->set_total( $order_total );
                $order->save();

                do_action( 'ovabrw_after_create_new_order_manully', $_POST, $order );

                // Redirect to order detail
                if ( $order_id ) {
                    wp_redirect( $order->get_edit_order_url() );
                    exit;
                }
            }
        }
    }

    new Ovabrw_Model();
}