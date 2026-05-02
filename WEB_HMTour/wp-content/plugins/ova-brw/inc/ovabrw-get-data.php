<?php if ( !defined( 'ABSPATH' ) ) exit();

/*
* Get disabled dates
*/
if ( ! function_exists( 'ovabrw_get_disabled_dates' ) ) {
    function ovabrw_get_disabled_dates( $product_id = false, $order_status = array( 'wc-completed' ) ) {
        global $wpdb;

        $order_disabled_dates = $dates_guests = $disabled_dates = array();
        $quantity       = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );
        $max_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_max', true ) );
        $max_children   = absint( get_post_meta( $product_id, 'ovabrw_childrens_max', true ) );
        $max_babies     = absint( get_post_meta( $product_id, 'ovabrw_babies_max', true ) );
        $min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
        $min_children   = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
        $min_babies     = absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );
        $duration       = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

        if ( ! $duration ) {
            // Get array product ids when use WPML
            $array_product_ids  = ovabrw_get_wpml_product_ids( $product_id );
            $orders_ids         = ovabrw_get_orders_by_product_id( $product_id, $order_status );

            foreach ( $orders_ids as $key => $value ) {
                // Get Order Detail by Order ID
                $order = wc_get_order($value);

                // Get Meta Data type line_item of Order
                $order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
               
                // For Meta Data
                foreach ( $order_items as $item_id => $item ) {
                    if ( in_array( $item->get_product_id(), $array_product_ids ) ) {
                        if ( ovabrw_qty_by_guests( $product_id ) ) {
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
                                $date_format    = ovabrw_get_date_format();
                                $date           = date( $date_format, $item_check_in );
                                
                                if ( isset( $dates_guests[$date] ) ) {
                                    if ( ! $dates_guests[$date] ) continue;

                                    $dates_guests[$date]['adults']      -= $item_adults * $item_qty;
                                    $dates_guests[$date]['children']    -= $item_children * $item_qty;
                                    $dates_guests[$date]['babies']      -= $item_babies * $item_qty;
                                } else {
                                    $dates_guests[$date] = [
                                        'adults'    => $max_adults * $quantity - $item_adults * $item_qty,
                                        'children'  => $max_children * $quantity - $item_children * $item_qty,
                                        'babies'    => $max_babies * $quantity - $item_babies * $item_qty
                                    ];
                                }

                                if ( ( ! $dates_guests[$date]['adults'] || $dates_guests[$date]['adults'] < 0 ) && ( ! $dates_guests[$date]['children'] || $dates_guests[$date]['children'] < 0 ) && ( ! $dates_guests[$date]['babies'] || $dates_guests[$date]['babies'] < 0 ) ) {
                                    array_push( $disabled_dates, $date );
                                }

                                if ( apply_filters( 'ovabrw_ft_check_min_guests', true, $product_id ) && ! in_array( $date, $disabled_dates ) ) {
                                    if ( $dates_guests[$date]['adults'] < $min_adults || $dates_guests[$date]['children'] < $min_children || $dates_guests[$date]['babies'] < $min_babies  ) {
                                        array_push( $disabled_dates, $date );
                                    }
                                }
                            }
                        } else {
                            $pickup_date_store = '';
                            if ( strtotime( $item->get_meta( 'ovabrw_pickup_date' ) ) ) {
                                $pickup_date_store  = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                            }

                            $pickoff_date_store = '';
                            if ( strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) ) ) {
                                $pickoff_date_store  = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                            }

                            $order_quantity = 1;
                            if ( absint( $item->get_meta( 'ovabrw_quantity' ) ) ) {
                                $order_quantity  = absint( $item->get_meta( 'ovabrw_quantity' ) );
                            }

                            if ( $pickoff_date_store >= current_time( 'timestamp' ) ) {
                                for( $i = 0; $i < $order_quantity ; $i++ ) { 
                                    $order_push_disabled    = ovabrw_push_disabled_dates( $pickup_date_store, $pickoff_date_store, $product_id );
                                    $order_disabled_dates   = array_merge_recursive( $order_disabled_dates, $order_push_disabled );
                                }
                            }
                        }
                    }
                }
            }
        }

        // Check Unavaiable Time in Product
        $untime_startdate   = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $untime_enddate     = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( ! empty( $untime_startdate ) && is_array( $untime_startdate ) ) {
            foreach( $untime_startdate as $key => $value ) {
                if ( $value && isset( $untime_enddate[$key] ) && $untime_enddate[$key] ) {
                    $untime_dates   = array();
                    $untime_dates   = ovabrw_push_disabled_dates( strtotime( $value ), strtotime( $untime_enddate[$key] ) );
                    $disabled_dates = array_merge_recursive( $disabled_dates, $untime_dates );
                }
            }
        }

        // Add disabled dates in order
        if ( ! empty( $order_disabled_dates ) && is_array( $order_disabled_dates ) ) {
            $order_disabled_dates = array_count_values( $order_disabled_dates );
            foreach( $order_disabled_dates as $date => $count ) {
                if ( $count >= $quantity && !in_array( $date, $disabled_dates ) ) {
                    array_push( $disabled_dates, $date );
                }
            }
        }

        // Preparation Time
        $preparation_time = get_post_meta( $product_id, 'ovabrw_preparation_time', true );

        if ( $preparation_time ) {
            $today          = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );
            $untime_dates   = ovabrw_push_disabled_dates( $today, $today + $preparation_time*86400 - 86400 );
            $disabled_dates = array_merge_recursive( $disabled_dates, $untime_dates );
        }

        // Remove duplicate value
        $disabled_dates = array_values( array_unique( $disabled_dates ) );

        return json_encode( $disabled_dates );
    }
}

if ( ! function_exists( 'ovabrw_push_disabled_dates' ) ) {
    function ovabrw_push_disabled_dates( $pickup_date_store = '', $pickoff_date_store = '', $product_id = false ) {
        $date_format    = ovabrw_get_date_format();
        $start_date     = date( $date_format, $pickup_date_store );
        $end_date       = date( $date_format, $pickoff_date_store );
        $disabled_dates = array();
        $dates_between  = total_between_2_days( $start_date, $end_date );

        if ( $dates_between == 0 ) {
            if ( ! in_array( $start_date, $disabled_dates ) ) {
                array_push( $disabled_dates, $start_date );
            }

        } else {
            $dates_between = ovabrw_createDatefull( strtotime( $start_date ), strtotime( $end_date ), $date_format );

            foreach ( $dates_between as $key => $value ) {
                if ( !in_array( $value, $disabled_dates ) ) {
                    array_push( $disabled_dates, $value );
                }
            }
        }
        
        return $disabled_dates;
    }
}

/**
 * get_order_rent_time return all date available
 * @param  number $product_id   Product ID
 * @param  array  $order_status wc-completed, wc-processing
 * @return json               dates available
 */
if ( ! function_exists( 'get_order_rent_time' ) ) {
    function get_order_rent_time( $product_id = false, $order_status = array( 'wc-completed' ) ) {
        global $wpdb;

        $order_date     = $dates_un_avaiable = array();
        $quantity       = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );
        $max_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_max', true ) );
        $max_children   = absint( get_post_meta( $product_id, 'ovabrw_childrens_max', true ) );
        $max_babies     = absint( get_post_meta( $product_id, 'ovabrw_babies_max', true ) );
        $duration       = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

        if ( ! $duration ) {
            // Get array product ids when use WPML
            $array_product_ids  = ovabrw_get_wpml_product_ids( $product_id );
            $orders_ids         = ovabrw_get_orders_by_product_id( $product_id, $order_status );
            $date_format        = ovabrw_get_date_format();

            foreach ( $orders_ids as $key => $value ) {
                // Get Order Detail by Order ID
                $order = wc_get_order( $value );

                // Get Meta Data type line_item of Order
                $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
               
                // For Meta Data
                foreach ( $order_line_items as $item_id => $item ) {
                    $push_date_unavailable  = array();

                    if ( in_array( $item->get_product_id(), $array_product_ids) ) {
                        if ( ovabrw_qty_by_guests( $product_id ) ) {
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
                                $date = date( 'Y-m-d', $item_check_in );
                                
                                if ( isset( $dates_guests[$date] ) ) {
                                    if ( ! $dates_guests[$date] ) continue;

                                    $dates_guests[$date]['adults']      -= $item_adults * $item_qty;
                                    $dates_guests[$date]['children']    -= $item_children * $item_qty;
                                    $dates_guests[$date]['babies']      -= $item_babies * $item_qty;

                                    if ( ( ! $dates_guests[$date]['adults'] || $dates_guests[$date]['adults'] < 0 ) && ( ! $dates_guests[$date]['children'] || $dates_guests[$date]['children'] < 0 ) && ( ! $dates_guests[$date]['babies'] || $dates_guests[$date]['babies'] < 0 ) ) {
                                        $dates_guests[$date] = false;
                                        array_push( $dates_un_avaiable, $date );
                                    }
                                } else {
                                    $dates_guests[$date] = [
                                        'adults'    => $max_adults * $quantity - $item_adults * $item_qty,
                                        'children'  => $max_children * $quantity - $item_children * $item_qty,
                                        'babies'    => $max_babies * $quantity - $item_babies * $item_qty
                                    ];
                                }
                            }
                        } else {
                            $pickup_date_store = '';
                            if ( strtotime( $item->get_meta( 'ovabrw_pickup_date' ) ) ) {
                                $pickup_date_store  = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                            }

                            $pickoff_date_store = '';
                            if ( strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) ) ) {
                                $pickoff_date_store  = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                            }

                            $order_quantity = 1;
                            if ( absint( $item->get_meta( 'ovabrw_quantity' ) ) ) {
                                $order_quantity  = absint( $item->get_meta( 'ovabrw_quantity' ) );
                            }

                            if ( $pickup_date_store && $pickoff_date_store >= current_time( 'timestamp' ) ) {
                                $push_date_unavailable = push_date_unavailable( $pickup_date_store, $pickoff_date_store );

                                if ( ! empty( $push_date_unavailable ) ) {
                                    for ( $i = 0; $i < $order_quantity ; $i++ ) { 
                                        $dates_un_avaiable = array_merge_recursive( $dates_un_avaiable, $push_date_unavailable );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Check Unavaiable Time in Product
        $ovabrw_untime_startdate    = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $ovabrw_untime_enddate      = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( ! empty( $ovabrw_untime_startdate ) && is_array( $ovabrw_untime_startdate ) ) {
            foreach ($ovabrw_untime_startdate as $key => $value) {
                if ( ! empty( $ovabrw_untime_startdate[$key] ) ) {
                    if ( ovabrw_check_array( $ovabrw_untime_enddate, $key ) ) {
                        $un_start   = strtotime( $ovabrw_untime_startdate[$key] );
                        $un_end     = strtotime( $ovabrw_untime_enddate[$key] );
                        $push_date_unavailable_untime = push_date_unavailable( $un_start, $un_end );

                        if ( ! empty( $push_date_unavailable_untime ) ) {
                            for( $i = 0; $i < $quantity; $i++ ) {
                                $dates_un_avaiable = array_merge_recursive( $dates_un_avaiable, $push_date_unavailable_untime );
                            }
                        }
                    }
                }
            }
        }

        if ( ovabrw_qty_by_guests( $product_id ) ) {
            if ( ! empty( $dates_un_avaiable ) && is_array( $dates_un_avaiable ) ) {
                $dates_un_avaiable = array_unique( $dates_un_avaiable );

                foreach ( $dates_un_avaiable as $date ) {
                    array_push( $order_date, array(
                        'start'             => $date,
                        'display'           => 'background',
                        'backgroundColor'   => apply_filters( 'ovabrw_ft_background_color_event', '#FF1A1A' ),
                    ));
                }
            }
        } else {
            // Unavailable Date for booking
            $data_unavailable = array_count_values( $dates_un_avaiable );

            if ( ! empty( $data_unavailable ) && is_array( $data_unavailable ) ) {
                foreach( $data_unavailable as $date => $qty ) {
                    array_push( $order_date, array(
                        'title'     => $qty . esc_html__( '/', 'ova-brw' ) . $quantity,
                        'start'     => $date,
                        'color'     => apply_filters( 'ovabrw_ft_color_event', '#FF1A1A' ),
                        'textColor' => apply_filters( 'ovabrw_ft_text_color_event', '#FFFFFF' ),
                    ));

                    if ( $qty >= $quantity ) {
                        array_push( $order_date, array(
                            'start'             => $date,
                            'display'           => 'background',
                            'backgroundColor'   => apply_filters( 'ovabrw_ft_background_color_event', '#FF1A1A' ),
                        ));
                    }
                }
            }
        }

        return $order_date;
    }
}

if ( ! function_exists( 'push_date_unavailable' ) ) {
    function push_date_unavailable( $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
        $date_format    = 'Y-m-d';
        $date_pickup    = date( $date_format, $ovabrw_pickup_date );
        $date_pickoff   = date( $date_format, $ovabrw_pickoff_date );
        $dates_avaiable = array();
        $between_2_days = total_between_2_days( $date_pickup, $date_pickoff );

        if ( $between_2_days == 0 ) { // In a day
            array_push( $dates_avaiable, $date_pickup );
        } else if ( $between_2_days == 1 ) { // 2 day beside
            array_push( $dates_avaiable, $date_pickup );
            array_push( $dates_avaiable, $date_pickoff );
        } else { // from 3 days 
            array_push( $dates_avaiable, $date_pickup ); 

            $date_between = ovabrw_createDatefull( strtotime( $date_pickup ), strtotime( $date_pickoff ), $format= $date_format );
            // Remove first and last array
            array_shift( $date_between ); 
            array_pop( $date_between );

            foreach( $date_between as $key => $value ) {
                array_push( $dates_avaiable, $value ); 
            }

            array_push( $dates_avaiable, $date_pickoff );
        }
        
        return $dates_avaiable;
    }
}

/**
 * get all Order id of a product
 * @param  [number] $product_id   product id
 * @param  array  $order_status wc-completed, wc-processing
 * @return [array object]               all order id
 */
if ( ! function_exists( 'ovabrw_get_orders_by_product_id' ) ) {
    function ovabrw_get_orders_by_product_id( $product_id = false, $order_status = array( 'wc-completed' ) ){
        global $wpdb;
        $order_ids = array();

        // Get array product ids when use WPML
        $product_ids = ovabrw_get_wpml_product_ids( $product_id );

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitems.order_item_type = 'line_item'
                AND oitem_meta.meta_key = '_product_id'
                AND oitem_meta.meta_value IN ( ".implode( ',', $product_ids )." )
                AND o.status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        } else {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitems.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND oitems.order_item_type = 'line_item'
                AND oitem_meta.meta_key = '_product_id'
                AND oitem_meta.meta_value IN ( ".implode( ',', $product_ids )." )
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        }
        
        return $order_ids;
    }
}

/**
 * ovabrw_search_vehicle Search Product
 * @param  array $data_search more value
 * @return [array object]              false | array object wq_query
 */
function ovabrw_search_vehicle( $data_search ) {
    $destination     = isset( $data_search['ovabrw_destination'] )  ? sanitize_text_field( $data_search['ovabrw_destination'] )     : 'all';
    $name_product    = isset( $data_search['ovabrw_name_product'] ) ? sanitize_text_field( $data_search['ovabrw_name_product'] )    : '';
    $pickup_date     = isset( $data_search['ovabrw_pickup_date'] )  ? strtotime( $data_search['ovabrw_pickup_date'] )               : '';
    $adults          = isset( $data_search['ovabrw_adults'] )       ? sanitize_text_field( $data_search['ovabrw_adults'] )          : '';
    $childrens       = isset( $data_search['ovabrw_childrens'] )    ? sanitize_text_field( $data_search['ovabrw_childrens'] )       : '';
    $babies          = isset( $data_search['ovabrw_babies'] )       ? sanitize_text_field( $data_search['ovabrw_babies'] )          : '';

    $order           = isset( $data_search['order'] )   ? sanitize_text_field( $data_search['order'] ) : 'DESC';
    $orderby         = isset( $data_search['orderby'] ) ? sanitize_text_field( $data_search['orderby'] ) : 'ID' ;

    $name_attribute  = isset( $data_search['ovabrw_attribute'] ) ? sanitize_text_field( $data_search['ovabrw_attribute'] ) : '';
    $value_attribute = isset( $data_search[$name_attribute] )    ? sanitize_text_field( $data_search[$name_attribute] ) : '';

    $category        = isset( $data_search['cat'] ) ? sanitize_text_field( $data_search['cat'] ) : '';

    if ( ! empty( $category ) && ! is_array( $category ) ) {
        $category = explode( '|', $category );
    }

    $tag_product     = isset( $data_search['ovabrw_tag_product'] ) ? sanitize_text_field( $data_search['ovabrw_tag_product'] ) : '';
    $list_taxonomy   = ovabrw_create_type_taxonomies();

    $slug_custom_taxonomy   = isset( $data_search['ovabrw_slug_custom_taxonomy'] ) ? sanitize_text_field( $data_search['ovabrw_slug_custom_taxonomy'] ) : '';
    $ovabrw_slug_taxonomies = isset( $data_search['ovabrw_slug_taxonomies'] ) ? $data_search['ovabrw_slug_taxonomies'] : array();

    if ( ! empty( $ovabrw_slug_taxonomies ) && ! is_array( $ovabrw_slug_taxonomies ) ) {
        $ovabrw_slug_taxonomies = explode( '|', $ovabrw_slug_taxonomies );
    } else {
        $ovabrw_slug_taxonomies = array();
    }

    $arg_taxonomy_arr = [];

    if ( ! empty( $list_taxonomy ) ) {
        foreach( $list_taxonomy as $taxonomy ) {
            $taxonomy_get  = isset( $data_search[$taxonomy['slug'].'_name'] ) ? sanitize_text_field( $data_search[$taxonomy['slug'].'_name'] ) : '';
            if ( $taxonomy_get != 'all' && ( $taxonomy['slug'] == $slug_custom_taxonomy || in_array( trim( $taxonomy['slug'] ), $ovabrw_slug_taxonomies ) ) ) {
                $arg_taxonomy_arr[] = array(
                    'taxonomy' => $taxonomy['slug'],
                    'field'    => 'slug',
                    'terms'    => $taxonomy_get
                );
            }
        }
    }

    $statuses = brw_list_order_status();
    $error    = array();
    $items_id = $args_cus_tax_custom = array();

    $args_meta_query_arr   =  $args_cus_meta_custom = array();

    if ( $destination != 'all' ) {
        $args_meta_query_arr[] = [
            'key'     => 'ovabrw_destination',
            'value'   => $destination,
            'compare' => 'LIKE',
        ];
    }

    if ( $name_product == '' ) {
        $args_base = array(
            'post_type'      => 'product',
            'posts_per_page' => '-1',
            'post_status'    => 'publish'
        );
    } else {
        $args_base = array(
            'post_type'      => 'product',
            'posts_per_page' => '-1',
            's'              => $name_product,
            'post_status'    => 'publish'
        );
    }
    
    if ( !empty($category) ) {
        $arg_taxonomy_arr[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $category
        ];
    }

    if ( $name_attribute != '' ) {
        $arg_taxonomy_arr[] = [
            'taxonomy' => 'pa_' . $name_attribute,
            'field' => 'slug',
            'terms' => [$value_attribute],
            'operator'       => 'IN',
        ];
    }

    if ( $tag_product != '' ) {
        $arg_taxonomy_arr[] = [
            'taxonomy' => 'product_tag',
            'field' => 'name',
            'terms' => $tag_product
        ];
    }

    if ( ! empty( $arg_taxonomy_arr ) ) {
        $args_cus_tax_custom = array(
            'tax_query' => array(
                'relation'  => 'AND',
                $arg_taxonomy_arr
            )
        );
    }

    if ( $adults != '' ) {
        $args_meta_query_arr[] = [
            'key'     => 'ovabrw_adults_max',
            'value'   => $adults,
            'type'    => 'numeric',
            'compare' => '>=',
        ];
    }

    if ( $childrens != '' ) {
        $args_meta_query_arr[] = [
            'key'     => 'ovabrw_childrens_max',
            'value'   => $childrens,
            'type'    => 'numeric',
            'compare' => '>=',
        ];
    }

    if ( $babies != '' ) {
        $args_meta_query_arr[] = [
            'key'     => 'ovabrw_babies_max',
            'value'   => $babies,
            'type'    => 'numeric',
            'compare' => '>=',
        ];
    }

    if ( ! empty( $args_meta_query_arr ) ) {
        $args_cus_meta_custom = array(
            'meta_query' => array(
                'relation'  => 'AND',
                $args_meta_query_arr
            )
        );
    }
   
    $args = array_merge_recursive( $args_base, $args_cus_tax_custom, $args_cus_meta_custom );

    // Get All products
    $items = new WP_Query( $args );

    if ( $items->have_posts() ) : while ( $items->have_posts() ) : $items->the_post();
        // Product ID
        $id     = get_the_id();
        $day    = (int)get_post_meta( $id, 'ovabrw_number_days', true );

        // Preparation Time
        $preparation_time = get_post_meta( $id, 'ovabrw_preparation_time', true );

        if ( $preparation_time && $pickup_date ) {
            $today = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );

            if ( $pickup_date < ( $today + $preparation_time*86400 - 86400 ) ) {
                continue;
            }

            $pickup_date += $preparation_time*86400 - 86400;
        }
        // End

        $pickoff_date = '';
        if ( $pickup_date ) {
            $pickoff_date = $pickup_date + $day*86400;
        }

        // Set Pick-up, Drop-off Date again
        $new_input_date     = ovabrw_new_input_date( $id, $pickup_date, $pickoff_date, '' );
        $pickup_date_new    = $new_input_date['pickup_date_new'];
        $pickoff_date_new   = $new_input_date['pickoff_date_new'];

        $ova_validate_manage_store = ova_validate_manage_store( $id, $pickup_date, $pickup_date, $passed = false, $validate = 'search' ) ;
        
        if ( $ova_validate_manage_store && $ova_validate_manage_store['status'] ) {
            array_push( $items_id, $id );
        }
    endwhile; else :
        return $items_id;
    endif; wp_reset_postdata();
    
    if ( $items_id ) {

        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
        $search_items_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();

        $args_product = array(
            'post_type'         => 'product',
            'posts_per_page'    => $search_items_page,
            'paged'             => $paged,
            'post_status'       => 'publish',
            'post__in'          => $items_id,
            'order'             => $order,
            'orderby'           => $orderby
        );

        $rental_products = new WP_Query( $args_product );

        return $rental_products;
    }

    return false;
}

// Get Price Type
function ovabrw_get_price_type( $post_id ) {
    return get_post_meta( $post_id, 'ovabrw_price_type', true ) ;
}

// Get Global Price of Rental Type - Day
function ovabrw_get_price_day( $post_id ) {
    return wc_price( get_post_meta( $post_id, '_regular_price', true ) );
}

// Get Global Price of Rental Type - Hour
function ovabrw_get_price_hour( $post_id ) {
    return wc_price( get_post_meta( $post_id, 'ovabrw_regul_price_hour', true ) );
}

// Get All Rooms
function get_all_rooms(){
    $args = array(
        'post_type'         => 'product',
        'posts_per_page'    => '-1',
        'post_status'       => 'publish',
        'tax_query'         => array(
            array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'ovabrw_car_rental', 
            ),
        )

    );

    $rooms = new WP_Query( $args );
    
    return $rooms;
}

// Get all location
function ovabrw_get_locations() {
    $locations = new WP_Query(
        array(
            'post_type'         => 'location',
            'post_status'       => 'publish',
            'posts_per_page'    => '-1'
        )
    );

    return $locations;
}

// Get all Products has Product Data: Rental
if ( ! function_exists( 'ovabrw_get_all_products' ) ) {
    function ovabrw_get_all_products() {

        $args = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'posts_per_page'    => '-1',
            'tax_query'         => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'ovabrw_car_rental', 
                ),
            ),
        );

        $results = new WP_Query( $args );

        return $results;
    }
}

function ovabrw_get_locations_array() {
    $locations = get_posts(
        array(
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => '-1',
            'fields'    => 'id',
        )
    );

    $html = array();

    if ( $locations ) { 
        foreach ( $locations as $location ) {

        $html[trim( get_the_title( $location->ID  ) )] = trim( get_the_title( $location->ID ) );

        }
    }
    
    return $html;
}

function ovabrw_get_list_pickup_dropoff_loc_transport( $id_product ) {
    if ( ! $id_product ) return [];

    $ovabrw_pickup_location     = get_post_meta( $id_product, 'ovabrw_pickup_location', 'false' );
    $ovabrw_dropoff_location    = get_post_meta( $id_product, 'ovabrw_dropoff_location', 'false' );

    $list_loc_pickup_dropoff = [];

    if ( ! empty( $ovabrw_pickup_location ) && ! empty( $ovabrw_dropoff_location ) ) {
        foreach( $ovabrw_pickup_location as $key => $location ) {
            $list_loc_pickup_dropoff[$location][] = $ovabrw_dropoff_location[$key];
        }
    }

    return $list_loc_pickup_dropoff;
}

function ovabrw_get_locations_transport_html( $name = '', $required = 'required', $selected = '', $id_product = false, $type = 'pickup' ) {
    $list_loc_pickup_dropoff = ovabrw_get_list_pickup_dropoff_loc_transport( $id_product );

    $html = '<select name="'.$name.'" class="ovabrw-transport '.$required.'">';
    $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';

    if ( $type == 'pickup' ) {
        
        if( $list_loc_pickup_dropoff ) {
            foreach( $list_loc_pickup_dropoff as $loc => $item_loc ) {
                $active = ( trim( $loc ) === trim( $selected ) ) ? 'selected="selected"' : '';
                $html .= '<option data-item_loc="'.esc_attr(json_encode($item_loc)).'" value="'.trim( $loc ).'" '.$active.'>'.trim( $loc ).'</option>';
            }
        }
    }
    
    $html .= '</select>';

    return $html;
}

function ovabrw_get_locations_html( $name = '', $required = 'required', $selected = '', $pid = '', $type = 'pickup' ) {
    $locations = new WP_Query(
        array(
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => '-1'
        )
    );

    $show_other_loc = true;

    if ( $pid ) {
        if( $type == 'pickup' ) {
            $show_other_loc = get_post_meta( $pid, 'ovabrw_show_other_location_pickup_product', true );
            $show_other_loc = ( $show_other_loc == 'no' ) ? false : true;
        } else {
            $show_other_loc = get_post_meta( $pid, 'ovabrw_show_other_location_dropoff_product', true );
            $show_other_loc = ( $show_other_loc == 'no' ) ? false : true;
        }
    }

    $html = '<select name="'.$name.'" class="'.$required.'">';
    $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';
    
    if ( $locations->have_posts() ) : while ( $locations->have_posts() ) : $locations->the_post();
        global $post;
        $active = ( trim( get_the_title() ) === trim( $selected ) ) ? 'selected="selected"' : '';
        $html .= '<option value="'.get_the_title().'" '.$active.'>'.get_the_title().'</option>';
    endwhile; endif;wp_reset_postdata();
    
    if ( $show_other_loc ) {
        $active = ( 'other_location' === trim( $selected ) ) ? 'selected="selected"' : '';
        $html .= '<option value="other_location" '.$active.'>'. esc_html__( 'Other Location', 'ova-brw' ).'</option>';
    }
    
    $html .= '</select>';

    return $html;
}

// Get all ids product rental
function ovabrw_get_all_id_product(){
   $all_ids = get_posts( array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'tax_query'      => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'ovabrw_car_rental', 
                ),
            ),
        ) );

    return $all_ids;
}

function ovabrw_get_vehicle_loc_title( $id_metabox ) {
    $vehicle_arr = array();
    $vehicle = new WP_Query(
        array(
            'post_type'         => 'vehicle',
            'post_status'       => 'publish',
            'posts_per_page'    => 1,
            'meta_query'        => array(
                array(
                    'key'     => 'ovabrw_id_vehicle',
                    'value'   => $id_metabox,
                    'compare' => '=',
                ),
            ),
        )
    );

    if ( $vehicle->have_posts() ) : while ( $vehicle->have_posts() ) : $vehicle->the_post();
        $vehicle_arr['loc'] = get_post_meta( get_the_id(), 'ovabrw_id_vehicle_location', true );
        $vehicle_arr['require_loc'] = get_post_meta( get_the_id(), 'ovabrw_vehicle_require_location', true );
        $vehicle_arr['untime'] = get_post_meta( get_the_id(), 'ovabrw_id_vehicle_untime_from_day', true );
        $vehicle_arr['id_vehicle'] = get_post_meta( get_the_id(), 'ovabrw_id_vehicle', true );
        $vehicle_arr['title'] = get_the_title();
    endwhile;endif;wp_reset_postdata();

    return $vehicle_arr;
}

function ovabrw_taxonomy_dropdown( $selected, $required, $exclude_id, $slug_taxonomy, $name_taxonomy ) {
    $args = array(
        'show_option_all'    => '',
        'show_option_none'   => esc_html__( 'All ', 'ova-brw' ) . esc_html( $name_taxonomy ) ,
        'option_none_value'  => '',
        'orderby'            => 'ID',
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 0,
        'child_of'           => 0,
        'exclude'            => $exclude_id,
        'include'            => '',
        'echo'               => 0,
        'selected'           => $selected,
        'hierarchical'       => 1,
        'name'               => $slug_taxonomy.'_name',
        'id'                 => '',
        'class'              => 'postform '.$required,
        'depth'              => 0,
        'tab_index'          => 0,
        'taxonomy'           => $slug_taxonomy,
        'hide_if_empty'      => false,
        'value_field'        => 'slug',
    );

    return wp_dropdown_categories($args);
}

/* Select html Category Rental */
function ovabrw_cat_rental( $selected = '', $required = '', $exclude_id = '', $label = '' ) {
    if ( ! $label ) {
        $label = esc_html__( 'Select Category', 'ova-brw' );
    }
    
    $args = array(
        'show_option_all'    => '',
        'show_option_none'   => $label,
        'option_none_value'  => '',
        'orderby'            => 'ID',
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 0,
        'child_of'           => 0,
        'exclude'            => $exclude_id,
        'include'            => '',
        'echo'               => 0,
        'selected'           => $selected,
        'hierarchical'       => 1,
        'name'               => 'cat',
        'id'                 => '',
        'class'              => 'postform '.$required,
        'depth'              => 0,
        'tab_index'          => 0,
        'taxonomy'           => 'product_cat',
        'hide_if_empty'      => false,
        'value_field'        => 'slug',
    );

    return wp_dropdown_categories( $args );
}

// Return start time, end time when rental is period hour or time
function get_rental_info_period( $product_id, $start_date, $ovabrw_rental_type, $ovabrw_period_package_id ) {
    $start_date = $start_date == '' ? null : $start_date;
    
    $ovabrw_unfixed = get_post_meta( $product_id, 'ovabrw_unfixed_time', true );

    if ( $ovabrw_unfixed != 'yes' ) {
        $start_date = date('Y-m-d', $start_date);
    } else {
        $start_date = date('Y-m-d H:i', $start_date);
    }


    $rental_start_time = $rental_end_time = 0;
    $period_label = '';
    $period_price = 0;
    $start_date_totime = strtotime($start_date);

    $package_type = '';

    if ( trim( $ovabrw_rental_type ) == trim( 'period_time' ) ) {
        $ovabrw_petime_id       = get_post_meta( $product_id, 'ovabrw_petime_id', true );
        $ovabrw_petime_price    = get_post_meta( $product_id, 'ovabrw_petime_price', true );
        $ovabrw_petime_days     = get_post_meta( $product_id, 'ovabrw_petime_days', true );
        $ovabrw_petime_label    = get_post_meta( $product_id, 'ovabrw_petime_label', true );
        $ovabrw_petime_discount = get_post_meta( $product_id, 'ovabrw_petime_discount', true );
        $ovabrw_package_type    = get_post_meta( $product_id, 'ovabrw_package_type', true );

        $ovabrw_pehour_unfixed      = get_post_meta( $product_id, 'ovabrw_pehour_unfixed', true );
        $ovabrw_pehour_start_time   = get_post_meta( $product_id, 'ovabrw_pehour_start_time', true );
        $ovabrw_pehour_end_time     = get_post_meta( $product_id, 'ovabrw_pehour_end_time', true );

        if ( $ovabrw_petime_id ) { 
            foreach ( $ovabrw_petime_id as $key => $value ) {
                if ( $ovabrw_petime_id[$key] ==  $ovabrw_period_package_id ) {
                    // Check pakage type
                    if ( $ovabrw_package_type[$key] == 'inday' ) {
                        $rental_start_time  = isset( $ovabrw_pehour_start_time[$key] ) ? strtotime( $start_date.' '.$ovabrw_pehour_start_time[$key] ) : 0;
                        $rental_end_time    = isset( $ovabrw_pehour_end_time[$key] ) ? strtotime( $start_date.' '.$ovabrw_pehour_end_time[$key] ) : 0;
                        
                        if ( $ovabrw_unfixed == 'yes' ) {
                            $retal_pehour_unfixed   = isset( $ovabrw_pehour_unfixed[$key] ) ? floatval( $ovabrw_pehour_unfixed[$key] ) : 0;
                            $rental_start_time      = $start_date_totime;
                            $rental_end_time        = $start_date_totime + $retal_pehour_unfixed * 3600;
                        }
                        
                        $period_label = isset( $ovabrw_petime_label[$key] ) ? $ovabrw_petime_label[$key] : '';
                        $period_price = isset( $ovabrw_petime_price[$key] ) ? floatval( $ovabrw_petime_price[$key] ) : 0;
                        $package_type = 'inday';

                        if ( isset( $ovabrw_petime_discount[$key] ) && $ovabrw_petime_discount[$key]['price'] ) {
                            foreach ( $ovabrw_petime_discount[$key]['price'] as $k => $v ) {
                                // Start Time Discount < Rental Time < End Time Discount
                                $start_time_dis = strtotime( $ovabrw_petime_discount[$key]['start_time'][$k] );
                                $end_time_dis   = strtotime( $ovabrw_petime_discount[$key]['end_time'][$k] );

                                if ( $start_time_dis <= $start_date_totime && $start_date_totime <= $end_time_dis ) {
                                    $period_price = floatval( $ovabrw_petime_discount[$key]['price'][$k] );
                                    break;
                                }
                            }    
                        }

                    } elseif ( $ovabrw_package_type[$key] == 'other' ) {
                        if ( $ovabrw_unfixed == 'yes' ) {
                            $start_date_date = date( 'Y-m-d H:i', $start_date_totime ) ;
                        } else {
                            $start_date_date = date( 'Y-m-d', $start_date_totime ) ;
                        }
                        
                        $start_date_totime  = strtotime( $start_date_date );
                        $rental_start_time  = $start_date_totime;
                        $rental_end_time    = $start_date_totime + intval( $ovabrw_petime_days[$key] )*24*60*60;
                        $period_label       = isset( $ovabrw_petime_label[$key] ) ? $ovabrw_petime_label[$key] : '';
                        $period_price       = isset( $ovabrw_petime_price[$key] ) ? floatval( $ovabrw_petime_price[$key] ) : 0;
                        $package_type       = 'other';

                        if ( isset( $ovabrw_petime_discount[$key] ) && $ovabrw_petime_discount[$key]['price'] ) {
                            foreach ( $ovabrw_petime_discount[$key]['price'] as $k => $v) {
                                // Start Time Discount < Rental Time < End Time Discount
                                $start_time_dis = strtotime( $ovabrw_petime_discount[$key]['start_time'][$k] );
                                $end_time_dis   = strtotime( $ovabrw_petime_discount[$key]['end_time'][$k] );

                                if ( $start_time_dis <= $start_date_totime && $start_date_totime <= $end_time_dis ) {
                                    $period_price = floatval( $ovabrw_petime_discount[$key]['price'][$k] );
                                    break;
                                }
                            }    
                        }
                    }

                    break;
                }
            }
        }
    }

    return array( 
        'start_time'    => $rental_start_time,
        'end_time'      => $rental_end_time,
        'period_label'  => $period_label,
        'period_price'  => $period_price,
        'package_type'  => $package_type 
    );
}

// Get all order has pickup date larger current time
if ( ! function_exists( 'ovabrw_get_orders_feature' ) ) {
    function ovabrw_get_orders_feature() {
        global $wpdb;

        $order_ids      = [];
        $order_status   = brw_list_order_status();

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                WHERE o.status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        } else {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts
                ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        }

        return $order_ids;
    }
}

// Get orders that have not yet created a remaining invoice
if ( ! function_exists( 'ovabrw_get_orders_not_remaining_invoice' ) ) {
    function ovabrw_get_orders_not_remaining_invoice() {
        global $wpdb;

        $order_ids      = [];
        $order_status   = brw_list_order_status();

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitem_meta.meta_key = 'ovabrw_remaining_amount_product'
                AND oitem_meta.meta_value != 0
                AND o.status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        } else {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts
                ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND oitem_meta.meta_key = 'ovabrw_remaining_amount_product'
                AND oitem_meta.meta_value != 0
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        }

        return $order_ids;
    }
}

/**
 * Get price include tax
 */
function ovabrw_get_price_include_tax( $product_id, $product_price ) {
    $display_price_cart = get_option( 'woocommerce_tax_display_cart', 'incl' );

    if ( wc_tax_enabled() && wc_prices_include_tax() && $product_price && $display_price_cart === 'excl' ) {

        $product_data   = wc_get_product($product_id);
        $tax_rates_data = WC_Tax::get_rates( $product_data->get_tax_class() );
        $rate_data      = reset($tax_rates_data);

        if ( $rate_data && isset( $rate_data['rate'] ) ) {

            $rate           = $rate_data['rate'];
            $tax_price      = $product_price - ( $product_price / ( ( $rate / 100 ) + 1 ) );
            $product_price  = $product_price - round( $tax_price, wc_get_price_decimals() );
        }
    }
    
    return apply_filters( 'ovabrw_get_price_include_tax', $product_price );;
}

/**
 * Get html Resources
 */
if ( !function_exists( 'ovabrw_get_html_resources' ) ) {
    function ovabrw_get_html_resources( $product_id = false, $resources = array(), $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0, $order_id = false ) {
        $resource_html = '';

        if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'no' ) {
            return $html;
        }

        $currency = '';

        if ( $order_id ) {
            $order = wc_get_order( $order_id );

            if ( ! empty( $order ) && is_object( $order ) ) {
                $currency = $order->get_currency();
            }
        }

        if ( ! empty( $resources ) && is_array( $resources ) ) {
            $rs_ids             = get_post_meta( $product_id, 'ovabrw_rs_id', true );
            $rs_names           = get_post_meta( $product_id, 'ovabrw_rs_name', true );
            $rs_adult_price     = get_post_meta( $product_id, 'ovabrw_rs_adult_price', true );
            $rs_child_price     = get_post_meta( $product_id, 'ovabrw_rs_children_price', true );
            $rs_baby_price      = get_post_meta( $product_id, 'ovabrw_rs_baby_price', true );
            $rs_duration_type   = get_post_meta( $product_id, 'ovabrw_rs_duration_type', true );

            foreach ( $resources as $rs_id => $rs_name ) {
                $rs_price = 0;

                $key = array_search( $rs_id, $rs_ids );

                if ( ! is_bool( $key ) ) {
                    $adult_price = 0;
                    if ( ovabrw_check_array( $rs_adult_price, $key ) ) {
                        $adult_price = $rs_adult_price[$key];
                    }

                    $child_price = 0;
                    if ( ovabrw_check_array( $rs_child_price, $key ) ) {
                        $child_price = $rs_child_price[$key];
                    }

                    $baby_price = 0;
                    if ( ovabrw_check_array( $rs_baby_price, $key ) ) {
                        $baby_price = $rs_baby_price[$key];
                    }

                    $duration_type = 'person';
                    if ( ovabrw_check_array( $rs_duration_type, $key ) ) {
                        $duration_type = $rs_duration_type[$key];
                    }

                    if ( 'person' === $duration_type ) {
                        $rs_price += floatval( $adult_price*$numberof_adults ) + floatval( $child_price*$numberof_children ) + floatval( $baby_price*$numberof_babies );
                    } else {
                        $rs_price += (float)$adult_price + (float)$child_price + (float)$baby_price;
                    }

                    $rs_price = ovabrw_convert_price_in_admin( $rs_price, $currency );

                    $resource_html .=  '<dt>' . $rs_name . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $rs_price, ['currency' => $currency] ) . '</dd>';
                }
            }
        }

        return apply_filters( 'ovabrw_get_html_resources', $resource_html, $product_id, $resources, $numberof_adults, $numberof_children, $numberof_babies, $order_id );
    }
}

/**
 * Get html Services
 */
if ( !function_exists( 'ovabrw_get_html_services' ) ) {
    function ovabrw_get_html_services( $product_id = false, $services = array(), $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0, $order_id = false ) {
        $service_html = '';

        if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'no' ) {
            return $html;
        }

        $currency = '';

        if ( $order_id ) {
            $order = wc_get_order( $order_id );

            if ( ! empty( $order ) && is_object( $order ) ) {
                $currency = $order->get_currency();
            }
        }

        if ( $services && is_array( $services ) ) {
            $service_ids            = get_post_meta( $product_id, 'ovabrw_service_id', true );
            $service_name           = get_post_meta( $product_id, 'ovabrw_service_name', true );
            $service_adult_price    = get_post_meta( $product_id, 'ovabrw_service_adult_price', true );
            $service_child_price    = get_post_meta( $product_id, 'ovabrw_service_children_price', true );
            $service_baby_price     = get_post_meta( $product_id, 'ovabrw_service_baby_price', true );
            $service_duration_type  = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

            foreach ( $services as $ovabrw_s_id ) {
                $service_price = 0;

                if ( $ovabrw_s_id && $service_ids && is_array( $service_ids ) ) {
                    foreach ( $service_ids as $key_id => $service_id_arr ) {
                        $key = array_search( $ovabrw_s_id, $service_id_arr );

                        if ( ! is_bool( $key ) ) {
                            $adult_price = 0;
                            if ( ovabrw_check_array( $service_adult_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_adult_price[$key_id], $key ) ) {
                                    $adult_price = $service_adult_price[$key_id][$key];
                                }
                            }

                            $child_price = 0;
                            if ( ovabrw_check_array( $service_child_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_child_price[$key_id], $key ) ) {
                                    $child_price = $service_child_price[$key_id][$key];
                                }
                            }

                            $baby_price = 0;
                            if ( ovabrw_check_array( $service_baby_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_baby_price[$key_id], $key ) ) {
                                    $baby_price = $service_baby_price[$key_id][$key];
                                }
                            }

                            $duration_type = 'person';
                            if ( ovabrw_check_array( $service_duration_type, $key_id ) ) {
                                if ( ovabrw_check_array( $service_duration_type[$key_id], $key ) ) {
                                    $duration_type = $service_duration_type[$key_id][$key];
                                }
                            }

                            if ( 'person' === $duration_type ) {
                                $service_price += floatval( $adult_price*$numberof_adults ) + floatval( $child_price*$numberof_children ) + floatval( $baby_price*$numberof_babies );
                            } else {
                                $service_price += (float)$adult_price + (float)$child_price + (float)$baby_price;
                            }

                            $service_price = ovabrw_convert_price_in_admin( $service_price, $currency );

                            $service_html .= '<dt>' . $service_name[$key_id][$key] . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $service_price, ['currency' => $currency] ) . '</dd>';
                        }
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_html_services', $service_html, $product_id, $services, $numberof_adults, $numberof_children, $numberof_babies, $order_id );
    }
}

/**
 * Get html Custom checkout fields
 */
if ( ! function_exists( 'ovabrw_get_html_ckf' ) ) {
    function ovabrw_get_html_ckf( $custom_ckf = [], $order_id = false ) {
        $cckf_html = '';

        if ( ! empty( $custom_ckf ) && is_array( $custom_ckf ) ) {
            $currency = '';

            if ( $order_id ) {
                $order = wc_get_order( $order_id );

                if ( ! empty( $order ) && is_object( $order ) ) {
                    $currency = $order->get_currency();
                }
            }

            $list_fields = get_option( 'ovabrw_booking_form', array() );

            foreach ( $custom_ckf as $k => $val ) {
                if ( isset( $list_fields[$k] ) && ! empty( $list_fields[$k] ) ) {
                    $type = $list_fields[$k]['type'];

                    if ( $type === 'radio' ) {
                        $val_key = array_search( $val, $list_fields[$k]['ova_radio_values'] );

                        if ( ! is_bool( $val_key ) ) {
                            $price = $list_fields[$k]['ova_radio_prices'][$val_key];
                            $price = ovabrw_convert_price_in_admin( $price, $currency );

                            if ( $price ) {
                                $cckf_html .= '<dt>' . $val . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price, ['currency' => $currency] ) . '</dd>';
                            }
                        }
                    }

                    if ( $type === 'checkbox' ) {
                        if ( ! empty( $val ) && is_array( $val ) ) {
                            foreach ( $val as $val_cb ) {
                                $val_key = array_search( $val_cb, $list_fields[$k]['ova_checkbox_key'] );

                                if ( ! is_bool( $val_key ) ) {
                                    $label = $list_fields[$k]['ova_checkbox_text'][$val_key];
                                    $price = $list_fields[$k]['ova_checkbox_price'][$val_key];
                                    $price = ovabrw_convert_price_in_admin( $price, $currency );

                                    if ( $price ) {
                                        $cckf_html .= '<dt>' . $label . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price, ['currency' => $currency] ) . '</dd>';
                                    }
                                }
                            }
                        }
                    }

                    if ( $type === 'select' ) {
                        $val_key = array_search( $val, $list_fields[$k]['ova_options_key'] );

                        if ( ! is_bool( $val_key ) ) {
                            $label = $list_fields[$k]['ova_options_text'][$val_key];
                            $price = $list_fields[$k]['ova_options_price'][$val_key];
                            $price = ovabrw_convert_price_in_admin( $price, $currency );

                            if ( $price ) {
                                $cckf_html .= '<dt>' . $label . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price, ['currency' => $currency] ) . '</dd>';
                            }
                        }
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_html_ckf', $cckf_html, $custom_ckf, $order_id );
    }
}

/**
 * Get html Resources + Services + Custom checkout fields
 */
if ( !function_exists( 'ovabrw_get_html_extra' ) ) {
    function ovabrw_get_html_extra( $resource_html = '', $service_html = '', $cckf_html = '' ) {
        $html = '';

        if ( ! empty( $resource_html ) || ! empty( $service_html ) || ! empty( $cckf_html ) ) {
            $html .= '<dl class="ovabrw_extra_item">';
            $html .= $cckf_html;
            $html .= $resource_html;
            $html .= $service_html;
            $html .= '</dl>';
        }

        return apply_filters( 'ovabrw_get_html_extra', $html, $resource_html, $service_html, $cckf_html );
    }
}

/**
 * Get taxes when wc_tax_enabled()
 */
if ( !function_exists( 'ovabrw_get_taxes_by_price' ) ) {
    function ovabrw_get_taxes_by_price( $product, $price ) {
        if ( ! $product || ! $price ) return 0;

        // Tax amount
        $taxes = 0;

        if ( $product->is_taxable() ) {
            $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );

            if ( wc_prices_include_tax() ) {
                $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                $taxes    = wc_round_tax_total( array_sum( $incl_tax ) );
            } else {
                $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                $taxes    = wc_round_tax_total( array_sum( $excl_tax ) );
            }
        }

        return apply_filters( 'ovabrw_get_taxes_by_price', $taxes, $product, $price );
    }
}

/**
 * Get html custom checkout fields
 */
if ( ! function_exists( 'ovabrw_get_html_ckf_order' ) ) {
    function ovabrw_get_html_ckf_order( $id ) {
        if ( ! $id ) return '';

        $list_extra_fields  = ovabrw_get_list_field_checkout( $id );
        $special_fields     = [ 'textarea', 'select', 'radio', 'checkbox', 'file' ];

        ob_start();
        if ( ! empty( $list_extra_fields ) && is_array( $list_extra_fields ) ):
            foreach ( $list_extra_fields as $key => $field ):
                if ( array_key_exists( 'enabled', $field ) && $field['enabled'] == 'on' ):
                    if ( array_key_exists('required', $field) && $field['required'] == 'on' ) {
                        $class_required = 'required';
                    } else {
                        $class_required = '';
                    }
        ?>
                <div class="ovabrw-ckf ovabrw-ckf-<?php echo esc_attr( $key ); ?>">
                    <label><?php echo esc_html( $field['label'] ); ?></label>
                    <?php if ( $field['type'] === 'checkbox' ): ?>
                        <span class="ovabrw-ckf-span ovabrw-ckf-checkbox <?php echo 'ovabrw-'.esc_attr( $class_required ); ?>">
                    <?php else: ?>
                        <span class="ovabrw-ckf-span">
                    <?php endif; ?>
                        <?php if ( ! in_array( $field['type'], $special_fields ) ): ?>
                            <input 
                                type="<?php echo esc_attr( $field['type'] ); ?>" 
                                name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                class="<?php echo esc_attr( $field['class'] ); ?>" 
                                placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
                                value="<?php echo esc_attr( $field['default'] ); ?>" 
                                <?php echo esc_attr( $class_required ); ?>/>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'textarea' ): ?>
                            <textarea name="<?php echo esc_attr( $key ) ;?>" class="<?php echo esc_attr( $field['class'] ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo $field['default']; ?>" cols="10" rows="5" <?php echo esc_attr( $class_required ); ?>></textarea>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'select' ):
                            $ova_options_key = $ova_options_text = [];

                            if ( array_key_exists( 'ova_options_key', $field ) ) {
                                $ova_options_key = $field['ova_options_key'];
                            }

                            if ( array_key_exists( 'ova_options_text', $field ) ) {
                                $ova_options_text = $field['ova_options_text'];
                            }
                        ?>
                            <select 
                                name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                class="ovabrw-ckf-price <?php echo esc_attr( $field['class'] ); ?>" 
                                <?php echo esc_attr( $class_required ); ?>>
                            <?php 
                                if ( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ):
                                    if ( $field['required'] != 'on' ): ?>
                                        <option value="">
                                            <?php printf( esc_html__( 'Select %s', 'ova_brw' ), $field['label'] ); ?>
                                        </option>
                                    <?php
                                    endif;
                                    foreach ( $ova_options_text as $k => $value ):
                                        ?>
                                        <option value="<?php echo esc_attr( $ova_options_key[$k] ); ?>"<?php selected( $field['default'], $ova_options_key[$k] ); ?>>
                                            <?php echo esc_html( $value ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            ?>
                            </select>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'radio' ):
                            $values     = isset( $field['ova_radio_values'] ) ? $field['ova_radio_values'] : '';
                            $default    = isset( $field['default'] ) ? $field['default'] : '';

                            if ( ! empty( $values ) && is_array( $values ) ):
                                foreach ( $values as $k => $value ):
                                    $checked = '';

                                    if ( ! $default && $field['required'] === 'on' ) $default = $values[0];

                                    if ( $default === $value ) $checked = 'checked';
                        ?>          
                                <div class="ovabrw-radio ovabrw-ckf-price">
                                    <input 
                                        type="radio" 
                                        id="<?php echo 'ovabrw-radio'.esc_attr( $k ); ?>" 
                                        name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                        value="<?php echo esc_attr( $value ); ?>" <?php echo esc_html( $checked ); ?>/>
                                    <label for="<?php echo 'ovabrw-radio'.esc_attr( $k ); ?>"><?php echo esc_html( $value ); ?></label>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'checkbox' ):
                            $default        = isset( $field['default'] ) ? $field['default'] : '';
                            $checkbox_key   = isset( $field['ova_checkbox_key'] ) ? $field['ova_checkbox_key'] : [];
                            $checkbox_text  = isset( $field['ova_checkbox_text'] ) ? $field['ova_checkbox_text'] : [];
                            $checkbox_price = isset( $field['ova_checkbox_price'] ) ? $field['ova_checkbox_price'] : [];

                            if ( ! empty( $checkbox_key ) && is_array( $checkbox_key ) ):
                                foreach ( $checkbox_key as $k => $val ):
                                    $checked = '';

                                    if ( ! $default && $field['required'] === 'on' ) $default = $val;

                                    if ( $default === $val ) $checked = 'checked';
                        ?>
                            <div class="ovabrw-checkbox ovabrw-ckf-price">
                                <input 
                                    type="checkbox" 
                                    id="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ).'['.$id.']'; ?>" 
                                    class="" 
                                    name="<?php echo esc_attr( $key ).'['.$id.'][]'; ?>" 
                                    value="<?php echo esc_attr( $val ); ?>" <?php echo esc_html( $checked ); ?>/>
                                <label for="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ).'['.$id.']'; ?>">
                                    <?php echo isset( $checkbox_text[$k] ) ? esc_html( $checkbox_text[$k] ) : ''; ?>
                                </label>
                            </div>
                            <?php endforeach;?>
                            <span class="ovabrw-error">
                                <?php printf( esc_html__( '%s field is required', 'ova-brw' ), $field['label'] ); ?>
                            </span>
                        <?php endif; endif; ?>
                        <?php if ( $field['type'] === 'file' ):
                            $mimes = apply_filters( 'ovabrw_ft_file_mimes', [
                                'jpg'   => 'image/jpeg',
                                'jpeg'  => 'image/pjpeg',
                                'png'   => 'image/png',
                                'pdf'   => 'application/pdf',
                                'doc'   => 'application/msword',
                            ]);
                        ?>
                            <div class="ovabrw-file">
                                <label for="<?php echo 'ovabrw-file-'.esc_attr( $key ); ?>">
                                    <span class="ovabrw-file-chosen">
                                        <?php esc_html_e( 'Choose File', 'ova-brw' ); ?>
                                    </span>
                                    <span class="ovabrw-file-name"></span>
                                </label>
                                <input 
                                    type="<?php echo esc_attr( $field['type'] ); ?>" 
                                    id="<?php echo 'ovabrw-file-'.esc_attr( $key ); ?>" 
                                    name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                    class="<?php echo esc_attr( $field['class'] ); ?>" 
                                    data-max-file-size="<?php echo esc_attr( $field['max_file_size'] ); ?>" 
                                    data-file-mimes="<?php echo esc_attr( json_encode( $mimes ) ); ?>" 
                                    data-max-file-size-msg="<?php printf( esc_html__( 'Max file size: %sMB', 'ova-brw' ), $field['max_file_size'] ); ?>" 
                                    data-formats="<?php esc_attr_e( 'Formats: .jpg, .jpeg, .png, .pdf, .doc', 'ova-brw' ); ?>" <?php echo esc_attr( $class_required ); ?> 
                                    data-required="<?php esc_attr_e( 'This field is required', 'ova-brw' ); ?>"/>
                            </div>
                        <?php endif; ?>
                    </span>
                </div>
        <?php
                endif;
            endforeach;
        ?>
            <input 
                type="hidden" 
                name="data_custom_ckf" 
                data-ckf="<?php echo esc_attr( json_encode( $list_extra_fields ) ); ?>" />
        <?php
        endif;

        $html = ob_get_contents(); 
        ob_end_clean();

        return $html;
    }
}

/**
 * Get html resources when created order in admin
 */
function ovabrw_get_html_resources_order( $product_id = false, $currency = '' ) {
    $html = '';

    if ( ! $product_id ) return $html;

    $ovabrw_rs_id = get_post_meta( $product_id, 'ovabrw_rs_id', true );

    if ( $ovabrw_rs_id ) {
        $ovabrw_rs_name             = get_post_meta( $product_id, 'ovabrw_rs_name', true );
        $ovabrw_rs_adult_price      = get_post_meta( $product_id, 'ovabrw_rs_adult_price', true );
        $ovabrw_rs_children_price   = get_post_meta( $product_id, 'ovabrw_rs_children_price', true );
        $ovabrw_rs_baby_price       = get_post_meta( $product_id, 'ovabrw_rs_baby_price', true );
        $ovabrw_rs_duration_type    = get_post_meta( $product_id, 'ovabrw_rs_duration_type', true );

        $show_children  = get_option( 'ova_brw_booking_form_show_children', 'yes' );
        $show_baby      = get_option( 'ova_brw_booking_form_show_baby', 'yes' );

        $html .= '<div class="resources_order">';

        foreach ( $ovabrw_rs_id as $k => $rs_id ) {
            if ( $rs_id ) {
                $rs_name            = isset( $ovabrw_rs_name[$k] ) ? $ovabrw_rs_name[$k] : '';
                $rs_adult_price     = isset( $ovabrw_rs_adult_price[$k] ) ? $ovabrw_rs_adult_price[$k] : 0;
                $rs_children_price  = isset( $ovabrw_rs_children_price[$k] ) ? $ovabrw_rs_children_price[$k] : 0;
                $rs_baby_price      = isset( $ovabrw_rs_baby_price[$k] ) ? $ovabrw_rs_baby_price[$k] : 0;
                $rs_duration_type   = isset( $ovabrw_rs_duration_type[$k] ) ? $ovabrw_rs_duration_type[$k] : 'person';

                $html .=    '<div class="item"><div class="left">';
                $html .=    '<input 
                                type="checkbox" 
                                id="ovabrw_resource_checkboxs_bk_'. esc_html( $k ) .'" 
                                data-resource_key="'. $rs_id .'" 
                                name="ovabrw_resource_checkboxs['. $product_id .'][]" 
                                value="'. esc_attr( $rs_id ) .'" 
                                class="ovabrw_resource_checkboxs" />';
                $html .=    '<label for="ovabrw_resource_checkboxs_bk_'. esc_html( $k ) .'">'. $rs_name .'</label>';
                $html .=    '</div>';

                $html .=    '<div class="right">';

                // Adult price
                $html .=    '<div class="adult-price">';
                $html .=    '<label class="adult-label">'. esc_html__( 'Adult: ', 'ova-brw' ) .'</label>';
                $html .=    '<span class="price">'. ovabrw_wc_price( $rs_adult_price, ['currency' => $currency] ) .'</span>';
                $html .=    '<span class="duration">';

                if ( 'person' === $rs_duration_type ) {
                    $html .= esc_html__( '/per person', 'ova-brw' );
                } else {
                    $html .= esc_html__( '/total', 'ova-brw' );
                }

                $html .=    '</span>';
                $html .=    '</div>';

                // Children price
                if ( $show_children === 'yes' ) {
                    $html .=    '<div class="children-price">';
                    $html .=    '<label class="children-label">'. esc_html__( 'Children: ', 'ova-brw' ) .'</label>';
                    $html .=    '<span class="price">'. ovabrw_wc_price( $rs_children_price, ['currency' => $currency] ) .'</span>';
                    $html .=    '<span class="duration">';

                    if ( 'person' === $rs_duration_type ) {
                        $html .= esc_html__( '/per person', 'ova-brw' );
                    } else {
                        $html .= esc_html__( '/total', 'ova-brw' );
                    }

                    $html .=    '</span>';
                    $html .=    '</div>';
                }

                // Baby price
                if ( $show_baby === 'yes' ) {
                    $html .=    '<div class="baby-price">';
                    $html .=    '<label class="baby-label">'. esc_html__( 'Baby: ', 'ova-brw' ) .'</label>';
                    $html .=    '<span class="price">'. ovabrw_wc_price( $rs_baby_price, ['currency' => $currency] ) .'</span>';
                    $html .=    '<span class="duration">';

                    if ( 'person' === $rs_duration_type ) {
                        $html .= esc_html__( '/per person', 'ova-brw' );
                    } else {
                        $html .= esc_html__( '/total', 'ova-brw' );
                    }

                    $html .=    '</span>';
                    $html .=    '</div>';
                }

                $html .=    '</div></div>';
            }
        }
        $html .= '</div>';
    }

    return $html;
}

/**
 * Get html services when created order in admin
 */
function ovabrw_get_html_services_order( $product_id = false, $currency = '' ) {
    $html = '';

    if ( ! $product_id ) return $html;

    $services = get_post_meta( $product_id, 'ovabrw_label_service', true );

    if ( $services ) {
        $service_id                 = get_post_meta( $product_id, 'ovabrw_service_id', true );
        $service_required           = get_post_meta( $product_id, 'ovabrw_service_required', true );
        $service_name               = get_post_meta( $product_id, 'ovabrw_service_name', true );
        $service_adult_price        = get_post_meta( $product_id, 'ovabrw_service_adult_price', true );
        $service_children_price     = get_post_meta( $product_id, 'ovabrw_service_children_price', true );
        $service_baby_price         = get_post_meta( $product_id, 'ovabrw_service_baby_price', true );
        $service_duration_type      = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

        $show_children  = get_option( 'ova_brw_booking_form_show_children', 'yes' );
        $show_baby      = get_option( 'ova_brw_booking_form_show_baby', 'yes' );

        $html .= '<div class="services_order">';

        for ( $i = 0; $i < count( $services ); $i++ ) {
            $sv_ids = isset( $service_id[$i] ) && $service_id[$i] ? $service_id[$i] : '';
            $service_required = isset( $service_required[$i] ) ? $service_required[$i] : '';

            if ( 'yes' === $service_required ) {
                $requires = ' class="required" data-error="'.sprintf( esc_html__( '%s is required.', 'ova-brw' ), $services[$i] ).'"';
            } else {
                $requires = '';
            }

            if ( $sv_ids && is_array( $sv_ids ) ) {
                $html .= '<div class="item">';
                $html .= '<select name="ovabrw_service['.$product_id.'][]"'. $requires .'>';
                $html .= '<option value="">'. sprintf( esc_html__( 'Select %s', 'ova-brw' ), $services[$i] ) .'</option>';

                foreach( $sv_ids as $key => $value ) {
                    $sv_name            = isset( $service_name[$i][$key] ) ? $service_name[$i][$key] : '';
                    $sv_adult_price     = isset( $service_adult_price[$i][$key] ) ? $service_adult_price[$i][$key] : 0;
                    $sv_children_price  = isset( $service_children_price[$i][$key] ) ? $service_children_price[$i][$key] : 0;
                    $sv_baby_price      = isset( $service_baby_price[$i][$key] ) ? $service_baby_price[$i][$key] : 0;
                    $sv_duration_type   = isset( $service_duration_type[$i][$key] ) ? $service_duration_type[$i][$key] : 'person';

                    $html_duration      = esc_html__( '/total', 'ova-brw' );

                    if ( 'person' === $sv_duration_type ) {
                        $html_duration = esc_html__( '/per person', 'ova-brw' );
                    }

                    $html_price = '';

                    if ( $show_children != 'yes' && $show_baby != 'yes' ) {
                        $html_price = sprintf( esc_html__( ' (Adult: %s%s)', 'ova-brw' ), ovabrw_wc_price( $sv_adult_price, ['currency' => $currency] ), $html_duration );
                    } elseif ( $show_children === 'yes' && $show_baby != 'yes' ) {
                        $html_price = sprintf( esc_html__( ' (Adult: %s%s - Children:%s%s)', 'ova-brw' ), ovabrw_wc_price( $sv_adult_price, ['currency' => $currency] ), $html_duration, ovabrw_wc_price( $sv_children_price, ['currency' => $currency] ), $html_duration );
                    } elseif ( $show_children != 'yes' && $show_baby === 'yes' ) {
                        $html_price = sprintf( esc_html__( ' (Adult: %s%s - Baby: %s%s)', 'ova-brw' ), ovabrw_wc_price( $sv_adult_price, ['currency' => $currency] ), $html_duration, ovabrw_wc_price( $sv_baby_price, ['currency' => $currency] ), $html_duration );
                    } else {
                        $html_price = sprintf( esc_html__( ' (Adult: %s%s - Children:%s%s - Baby: %s%s)', 'ova-brw' ), ovabrw_wc_price( $sv_adult_price, ['currency' => $currency] ), $html_duration, ovabrw_wc_price( $sv_children_price, ['currency' => $currency] ), $html_duration, ovabrw_wc_price( $sv_baby_price, ['currency' => $currency] ), $html_duration );
                    }
                    
                    $html .= '<option value="'. esc_attr( $value ) .'">'. esc_html( $sv_name ) . $html_price .'</option>';
                }

                $html .= '</select>';
                $html .= '</div>';
            }
        }
        $html .=    '</div>';
    }

    return $html;
}

/**
 * Get html fixed time when created order in admin
 */
function ovabrw_get_html_fixed_time_order( $product_id = null ) {
    $html = '';
    
    if ( ! $product_id ) return $html;

    $duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
    $fixed_time_check_in    = get_post_meta( $product_id, 'ovabrw_fixed_time_check_in', true );
    $fixed_time_check_out   = get_post_meta( $product_id, 'ovabrw_fixed_time_check_out', true );

    if ( ! $duration && ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ) {
        $date_format    = ovabrw_get_date_format();
        $had_time       = false;

        // Preparation Time
        $preparation_time = get_post_meta( $product_id, 'ovabrw_preparation_time', true );

        $html .= '<div class="rental_item ovabrw-fixed-time">';
            $html .= '<label for="ovabrw-fixed-time">';
                $html .= esc_html__( 'Choose time *', 'ova-brw' );
            $html .= '</label>';
            $html .= '<select name="ovabrw-fixed-time">';
                $flag = 0;
                foreach ( $fixed_time_check_in as $k => $check_in ) {
                    $check_out = isset( $fixed_time_check_out[$k] ) ? $fixed_time_check_out[$k] : '';

                    if ( $check_in && $check_out ) {
                        if ( strtotime( $check_in ) < current_time('timestamp') ) continue;

                        if ( $preparation_time ) {
                            $new_input_date = ovabrw_new_input_date( $product_id, strtotime( $check_in ), strtotime( $fixed_time_check_out[$k] ), $date_format );

                            if ( $new_input_date['pickup_date_new'] < ( current_time( 'timestamp' ) + $preparation_time*86400 - 86400 ) ) continue;
                        }

                        if ( ovabrw_qty_by_guests( $product_id ) ) {
                            $guests_available = ovabrw_validate_guests_available( $product_id, strtotime( $check_in ), strtotime( $check_out ), [], 'search' );

                            if ( ! $guests_available ) {
                                continue;
                            }
                        } else {
                            $ovabrw_quantity    = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_quantity' ) );
                            $quantity           = ! empty( $ovabrw_quantity ) ? absint( $ovabrw_quantity ) : 1;

                            $qty_available = ova_validate_manage_store( $product_id, strtotime( $check_in ), strtotime( $check_out ), false, 'search', $quantity );

                            if ( ! $qty_available ) continue;
                        }

                        $had_time = true;
                        $txt_time = sprintf( esc_html__( 'From %s to %s', 'ova-brw' ), $check_in, $check_out );

                        $html .= '<option value="'.esc_html( $check_in.'|'.$check_out ).'"'.selected( $flag, 0, false ).'>';
                            $html .= esc_html( $txt_time );
                        $html .= '</option>';

                        $flag++;
                    }
                }

                if ( ! $had_time ) {
                    $html .= '<option value="">';
                        $html .= esc_html__( 'No time', 'ova-brw' );
                    $html .= '</option>';
                }
            $html .= '</select>';
        $html .= '</div';
    }

    return $html;
}

/**
 *  HTML Dropdown Attributes
 */
function ovabrw_dropdown_attributes( $label = '' ) {
    $args       = array(); 
    $html       = $html_attr_value = '';
    $attributes = wc_get_attribute_taxonomies();

    if ( ! $label ) {
        $label = esc_html__( 'Select Attribute', 'ova-brw' );
    }

    if ( ! empty( $attributes ) ) {
        $html .= '<select name="ovabrw_attribute" class="ovabrw_attribute"><option value="">'. $label .'</option>';

        foreach ( $attributes as $obj_attr ) {
            if ( taxonomy_exists( wc_attribute_taxonomy_name( $obj_attr->attribute_name ) ) ) {
                $html .= "<option value='". $obj_attr->attribute_name ."'>". $obj_attr->attribute_label ."</option>";

                $term_attributes = get_terms( wc_attribute_taxonomy_name( $obj_attr->attribute_name ), 'orderby=name&hide_empty=0' );
                if ( ! empty( $term_attributes ) ) {

                    $html_attr_value .= '<div class="label_search s_field ovabrw-value-attribute" id="'. $obj_attr->attribute_name .'">
                                            <select name="ovabrw_attribute_value" >';

                    foreach ( $term_attributes as $obj_attr_value ) {
                        $html_attr_value .= '<option value="'.$obj_attr_value->slug.'">'.$obj_attr_value->name.'</option>';
                    }

                    $html_attr_value .= '</select></div>';
                }
            }
        }
        $html .= '</select>';
    }
    $args['html_attr']         = $html;
    $args['html_attr_value']   = $html_attr_value;

    return $args;
}

/**
 *  HTML Destinantion Dropdown
 */
function ovabrw_destination_dropdown( $placeholder, $id_selected ) {
    $html     = '';
    $args_cat = array(
       'taxonomy' => 'cat_destination',
       'orderby' => 'name',
       'order'   => 'ASC'
    );

    $cats = get_categories($args_cat);

    if ( ! $placeholder ) {
        $placeholder = esc_html__( 'What are you going?', 'ova-brw' );
    }

    if ( ! empty( $cats ) ) {
        $html .= '<select id="brw-destinations-select-box" name="ovabrw_destination"><option value="all">'. $placeholder .'</option>';

        foreach ( $cats as $cat ) {

            $cat_id = $cat->term_id;
            $html .= '<optgroup label="'. $cat->name. '">';

            $args_destination  = array( 
                'post_type' => 'destination',
                'posts_per_page' => -1,
                'order' => 'ASC',
                'orderby' => 'title',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'cat_destination',
                        'field'    => 'term_id',
                        'terms'    => $cat_id,
                    ),
                ),
            ); 

            $destinations = new WP_Query( $args_destination );

            if ( $destinations->have_posts()) : while ( $destinations->have_posts()) : $destinations->the_post(); ?>
                <?php
                    global $post;
                    $id    = get_the_id();
                    $title = get_the_title();
                    if( $id == $id_selected ) {
                        $html .= '<option value="'.$id.'" selected="selected">'.$title.'</option>';
                    } else {
                        $html .= '<option value="'.$id.'">'.$title.'</option>';
                    } 
                ?>
            <?php endwhile; endif; wp_reset_postdata();  $html .= '</optgroup>';
        }

        $html .= '</select>';
    }

    if ( empty( $cats ) ) {
        $html .= '<select id="brw-destinations-select-box" name="ovabrw_destination"><option value="all">'. $placeholder .'</option>';

        $args_destination  = array( 
            'post_type' => 'destination',
            'posts_per_page' => -1,
            'order' => 'ASC',
            'orderby' => 'title'
        ); 

        $destinations = new WP_Query( $args_destination );

        if ( $destinations->have_posts()) : while ( $destinations->have_posts()) : $destinations->the_post(); ?>
            <?php
                global $post;
                $id    = get_the_id();
                $title = get_the_title();

                if ( $id == $id_selected ) {
                    $html .= '<option value="'.$id.'" selected="selected">'.$title.'</option>';
                } else {
                    $html .= '<option value="'.$id.'">'.$title.'</option>';
                }
            ?>
        <?php endwhile; endif; wp_reset_postdata();

        $html .= '</select>';
    }

    return $html;
}

/**
 *  Get html taxonomy search ajax
 */
function ovabrw_search_taxonomy_dropdown( $taxonomy, $name, $selected ) {
    // WPML
    if ( $selected ) {
        $selected = ovabrw_get_translated_term_slug( $selected, $taxonomy );
    }

    $args = array(
        'show_option_all'    => '',
        'show_option_none'   => esc_html( $name ) ,
        'option_none_value'  => 'all',
        'orderby'            => 'ID',
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 0,
        'child_of'           => 0,
        'exclude'            => '',
        'include'            => '',
        'echo'               => 0,
        'selected'           => $selected,
        'hierarchical'       => 1,
        'name'               => $taxonomy.'_name',
        'id'                 => '',
        'class'              => 'brw_custom_taxonomy_dropdown',
        'depth'              => 0,
        'tab_index'          => 0,
        'taxonomy'           => $taxonomy,
        'hide_if_empty'      => false,
        'value_field'        => 'slug',
    );

    return wp_dropdown_categories($args);
}

/**
 * Get translated slug taxonomy
 */
if ( ! function_exists( 'ovabrw_get_translated_term_slug' ) ) {
    function ovabrw_get_translated_term_slug( $selected, $taxonomy ) {
        // WPML
        if ( $selected && is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
            $term = get_term_by('slug', $selected, $taxonomy);
            
            if ( $term && is_object( $term ) ) {
                $term_id = apply_filters( 'wpml_object_id', $term->term_id, $taxonomy );
                
                if ( $term_id ) {
                    $translated_term = get_term( $term_id, $taxonomy );
                    
                    if ( $translated_term && is_object( $translated_term ) ) {
                        $selected = $translated_term->slug;
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_translated_term_slug', $selected, $taxonomy );
    }
}

/**
 *  Get product search ajax
 */
function ovabrw_search_products( $data ) {
    $number     = $data['posts_per_page']   ? $data['posts_per_page']   : 12;
    $orderby    = $data['orderby']          ? $data['orderby']          : 'date';
    $order      = $data['order']            ? $data['order']            : 'DESC';

    $args = array(
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'posts_per_page'    => $number,
        'orderby'           => $orderby,
        'order'             => $order,
    );

    $products = new WP_Query( $args );

    return $products;
}

/**
 * Pagination ajax
 */
function ovabrw_pagination_ajax( $total, $limit, $current  ) {

    $html   = '';
    $pages  = ceil( $total / $limit );

    if ( $pages > 1 ) {
        $html .= '<ul>';

        if ( $current > 1 ) {
            $html .=    '<li><span data-paged="'. ( $current - 1 ) .'" class="prev page-numbers" >'
                            . '<i class="icomoon icomoon-angle-left"></i>' . esc_html__( 'Prev', 'ova-brw' ) .
                        '</span></li>';
        }

        for ( $i = 1; $i <= $pages; $i++ ) {
            if ( $current == $i ) {
                $html .=    '<li><span data-paged="'. $i .'" class="prev page-numbers current" >'. esc_html( $i ) .'</span></li>';
            } else {
                $html .=    '<li><span data-paged="'. $i .'" class="prev page-numbers" >'. esc_html( $i ) .'</span></li>';
            }
        }

        if ( $current < $pages ) {
            $html .=    '<li><span data-paged="'. ( $current + 1 ) .'" class="next page-numbers" >'
                            . esc_html__( 'Next', 'ova-brw' ) . '<i class="icomoon icomoon-angle-right"></i>' .
                        '</span></li>';
        }
    }

    return $html;
}

/**
 * Recursive array replace \\
 */
if ( ! function_exists('recursive_array_replace') ) {
    function recursive_array_replace( $find, $replace, $array ) {
        if ( !is_array( $array ) ) {
            return str_replace( $find, $replace, $array );
        }

        foreach ( $array as $key => $value ) {
            $array[$key] = recursive_array_replace( $find, $replace, $value );
        }

        return $array;
    }
}

/**
 * Get destinations
 */
if ( ! function_exists('ovabrw_get_destinations') ) {
    function ovabrw_get_destinations() {
        
        $results = array(
            '' => esc_html__( 'All Destination', 'ova-brw' ),
        );

        $args = array(
            'post_type'         => 'destination',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'orderby'           => 'ID',
            'order'             => 'DESC',
            'fields'            => 'ids'
        );

        $destinations = get_posts( $args );

        if ( $destinations && is_array( $destinations ) ) {
            foreach( $destinations as $destination_id ) {
                $destination_title = get_the_title( $destination_id );
                $results[$destination_id] = $destination_title;
            }
        }

        return $results;
    }
}

if ( ! function_exists('ovabrw_get_filtered_price') ) {
    function ovabrw_get_filtered_price() {
        global $wpdb;

        $sql = "
            SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
            FROM {$wpdb->wc_product_meta_lookup}
            WHERE product_id IN (
                SELECT ID FROM {$wpdb->posts} 
                WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', array( 'product' ) ) ) . "')
                AND {$wpdb->posts}.post_status = 'publish'
                " . ')';
                
        return $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
    }
}

/**
 * Get exclude ids not available pickup date
 */
if ( ! function_exists('ovabrw_get_exclude_ids') ) {
    function ovabrw_get_exclude_ids( $pickup_date ) {
        $products    = ovabrw_get_all_products();
        $exclude_ids = array();

        if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();

            $product_id = get_the_id();
            $duration   = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

            // Preparation Time
            
            $preparation_time = get_post_meta( $product_id, 'ovabrw_preparation_time', true );

            if ( $preparation_time && $pickup_date ) {
                $today = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );

                if ( $pickup_date < ( $today + $preparation_time*86400 - 86400 ) ) {
                    array_push( $exclude_ids, $product_id );
                    continue;
                }

                $pickup_date += $preparation_time*86400 - 86400;
            }
            // End

            if ( $duration ) {
                $duration_time = ovabrw_get_duration_time( $product_id, $pickup_date );
                    
                if ( empty( $duration_time ) ) {
                    array_push( $exclude_ids, $product_id );
                }
            } else {
                $day = (int)get_post_meta( $product_id, 'ovabrw_number_days', true );

                $pickoff_date = '';
                
                if ( $pickup_date ) {
                    $pickoff_date = $pickup_date + $day*86400;
                }

                // Check product in order
                $store_quantity = ovabrw_quantity_available_in_order( $product_id, $pickup_date, $pickoff_date );

                // Check product in cart
                $cart_quantity  = ovabrw_quantity_available_in_cart( $product_id, 'cart', $pickup_date, $pickoff_date );

                // Get array quantity available
                $data_quantity  = ovabrw_get_quantity_available( $product_id, $store_quantity, $cart_quantity, 1, false, 'cart' );

                // Check Unavailable
                $unavailable = ovabrw_check_unavailable( $product_id, $pickup_date, $pickoff_date );

                if ( $data_quantity ) {
                    $qty_available = $data_quantity['quantity_available'];

                    if ( $unavailable ) {
                        $qty_available = 0;
                    }

                    if ( $qty_available <= 0 || is_null( $qty_available ) ) {
                        array_push( $exclude_ids, $product_id );
                    }
                }

                // Check time in Fixed Time
                $in_fixed_time = ovabrw_check_fixed_time( $product_id, $pickup_date );

                if ( ! $in_fixed_time && ! in_array( $product_id, $exclude_ids ) ) {
                    array_push( $exclude_ids, $product_id );
                }
            }
            
        endwhile; endif; wp_reset_postdata();

        return $exclude_ids;
    }
}

if ( ! function_exists( 'ovabrw_check_fixed_time' ) ) {
    function ovabrw_check_fixed_time( $product_id, $pickup_date ) {
        $flag = false;
        $fixed_time_check_in    = get_post_meta( $product_id, 'ovabrw_fixed_time_check_in', true );
        $fixed_time_check_out   = get_post_meta( $product_id, 'ovabrw_fixed_time_check_out', true );

        if ( ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ) {
            foreach( $fixed_time_check_in as $k => $check_in ) {
                if ( isset( $fixed_time_check_out[$k] ) && $fixed_time_check_out[$k] ) {
                    if ( strtotime( $check_in ) <= $pickup_date && $pickup_date <= strtotime( $fixed_time_check_out[$k] ) ) {
                        $flag = true;
                        break;
                    }
                }
            }
        } else {
            $flag = true;
        }

        return $flag;
    }
}

if ( ! function_exists( 'ovabrw_get_weekday' ) ) {
    function ovabrw_get_weekday( $pickup_date = false ) {
        if ( ! $pickup_date ) return false;

        $day        = date('w', $pickup_date );
        $week_day   = '';

        if ( $day == '0' ) {
            $week_day = 'sunday';
        } elseif ( $day == '1' ) {
            $week_day = 'monday';
        } elseif ( $day == '2' ) {
            $week_day = 'tuesday';
        } elseif ( $day == '3' ) {
            $week_day = 'wednesday';
        } elseif ( $day == '4' ) {
            $week_day = 'thursday';
        } elseif ( $day == '5' ) {
            $week_day = 'friday';
        } elseif ( $day == '6' ) {
            $week_day = 'saturday';
        } else {
            $week_day = '';
        }

        return $week_day;
    }
}

if ( ! function_exists( 'ovabrw_get_duration_time' ) ) {
    function ovabrw_get_duration_time( $product_id = null, $pickup_date = false ) {
        if ( ! $product_id || ! $pickup_date ) return false;

        $duration_data      = array();
        $date_format        = ovabrw_get_date_format();
        $datetime_format    = ovabrw_get_datetime_format();
        $week_day           = ovabrw_get_weekday( $pickup_date );

        $ovabrw_quantity    = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_quantity' ) );
        $quantity           = ! empty( $ovabrw_quantity ) ? absint( $ovabrw_quantity ) : 1;

        if ( ! $week_day ) return false;

        $ovabrw_schedule_time = get_post_meta( $product_id, 'ovabrw_schedule_time', true );

        if ( isset( $ovabrw_schedule_time[$week_day] ) && ! empty( $ovabrw_schedule_time[$week_day] ) && is_array( $ovabrw_schedule_time[$week_day] ) ) {
            foreach( $ovabrw_schedule_time[$week_day] as $time ) {
                $checkin = date_i18n( $date_format, $pickup_date );

                if ( $time ) $checkin .= ' ' . $time;

                if ( strtotime( $checkin ) < current_time( 'timestamp' ) ) continue;

                $checkout = ovabrw_get_checkout_date( $product_id, strtotime( $checkin ) );

                if ( ovabrw_qty_by_guests( $product_id ) ) {
                    $guests_available = ovabrw_validate_guests_available( $product_id, strtotime( $checkin ), strtotime( $checkout ), [], 'search' );

                    if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
                        array_push( $duration_data , $time );
                    }
                } else {
                    $qty_available = ova_validate_manage_store( $product_id, strtotime( $checkin ), strtotime( $checkout ), true, 'search', $quantity );

                    if ( $qty_available ) {
                        array_push( $duration_data , $time );
                    }
                }
            }

            return $duration_data;
        }

        return false;
    }
}

if ( ! function_exists( 'ovabrw_get_html_duration' ) ) {
    function ovabrw_get_html_duration( $duration_time = array() ) {
        if ( empty( $duration_time ) || ! is_array( $duration_time ) ) return false;

        $html = '<div class="rental_item ovabrw_times_field">';
            $html .= '<label>';
                $html .= esc_html__( 'Time', 'ova-brw' );
            $html .= '</label>';
            $html .= '<div class="ovabrw-times">';

                foreach ( $duration_time as $k => $time ) {
                    $html .= '<label class="duration">' . $time;

                    if ( $k == 0 ) {
                        $html .= '<input type="radio" name="ovabrw_time_from" class="ovabrw_time_from" value="'.$time.'" checked="checked">';
                    } else {
                        $html .= '<input type="radio" name="ovabrw_time_from" class="ovabrw_time_from" value="'.$time.'">';
                    }
                    
                    $html .= '<span class="checkmark"></span>';
                    $html .= '</label>';
                }

            $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}

if ( ! function_exists( 'ovabrw_create_order_get_html_duration' ) ) {
    function ovabrw_create_order_get_html_duration( $product_id = false, $duration_time = array() ) {
        if ( ! $product_id || empty( $duration_time ) || ! is_array( $duration_time ) ) return false;

        $html = '<div class="rental_item ovabrw_times_field">';
            $html .= '<label>';
                $html .= esc_html__( 'Time', 'ova-brw' );
            $html .= '</label>';
            $html .= '<div class="ovabrw-times">';

                foreach ( $duration_time as $k => $time ) {
                    $html .= '<label class="duration">' . $time;

                    if ( $k == 0 ) {
                        $html .= '<input type="radio" name="ovabrw_time_from['.$product_id.']" class="ovabrw_time_from" value="'.$time.'" checked="checked">';
                    } else {
                        $html .= '<input type="radio" name="ovabrw_time_from['.$product_id.']" class="ovabrw_time_from" value="'.$time.'">';
                    }
                    
                    $html .= '<span class="checkmark"></span>';
                    $html .= '</label>';
                }

            $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}

// Duration
if ( ! function_exists( 'ovabrw_get_checkout_date' ) ) {
    function ovabrw_get_checkout_date( $product_id = false, $check_in = '' ) {
        if ( ! $product_id || ! $check_in ) return false;

        $check_out      = '';
        $date_format    = ovabrw_get_date_format();
        $duration       = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
        $number_days    = get_post_meta( $product_id, 'ovabrw_number_days', true );
        $number_hours   = get_post_meta( $product_id, 'ovabrw_number_hours', true );

        if ( $duration ) {
            $date_format    = ovabrw_get_datetime_format();
            $check_out      = $check_in + floatval( $number_hours )*60*60;
        } else {
            $check_out = $check_in + absint( $number_days )*24*60*60;
        }

        return date_i18n( $date_format, $check_out );
    }
}

// Check Qty by Guests
if ( ! function_exists( 'ovabrw_qty_by_guests' ) ) {
    function ovabrw_qty_by_guests( $product_id = null ) {
        if ( ! $product_id ) return false;

        $qty_by_guests = get_post_meta( $product_id, 'ovabrw_stock_quantity_by_guests', true );

        if ( $qty_by_guests ) return true;

        return false;
    }
}

// Get total number Guests
if ( ! function_exists( 'ovabrw_get_total_guests' ) ) {
    function ovabrw_get_total_guests( $product_id = null ) {
        if ( ! $product_id ) return 0;

        $stock_quantity     = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );
        $number_adults      = absint( get_post_meta( $product_id, 'ovabrw_adults_max', true ) );
        $number_children    = absint( get_post_meta( $product_id, 'ovabrw_childrens_max', true ) );
        $number_babies      = absint( get_post_meta( $product_id, 'ovabrw_babies_max', true ) );

        return $stock_quantity * ( $number_adults + $number_children + $number_babies );
    }
}

// Get fixed dates
if ( !function_exists( 'ovabrw_get_fixed_dates' ) ) {
    function ovabrw_get_fixed_dates( $product_id = false, $next = 0 ) {
        $return_dates = array();

        if ( $product_id ) {
            // Fixed dates
            $checkin_dates  = get_post_meta( $product_id, 'ovabrw_fixed_time_check_in', true );
            $checkout_dates = get_post_meta( $product_id, 'ovabrw_fixed_time_check_out', true );

            if ( !empty( $checkin_dates ) && is_array( $checkin_dates ) && !empty( $checkout_dates ) && is_array( $checkout_dates ) ) {
                // Date format
                $date_format = ovabrw_get_date_format();

                // Number of days limit appears
                $limited = (int)apply_filters( 'ovabrw_number_of_days_limit_appears', 5 );

                // Quantity
                $quantity = (int)get_post_meta( $product_id, 'ovabrw_stock_quantity', true );

                // Number of guests
                if ( ovabrw_qty_by_guests( $product_id ) ) {
                    // Min number of adults
                    $min_adults = (int)get_post_meta( $product_id, 'ovabrw_adults_min', true );

                    // Max number of adults
                    $max_adults         = (int)get_post_meta( $product_id, 'ovabrw_adults_max', true );
                    $numberof_adults    = $max_adults*$quantity;

                    // Min number of children
                    $min_children = (int)get_post_meta( $product_id, 'ovabrw_childrens_min', true );

                    // Max number of children
                    $max_children       = (int)get_post_meta( $product_id, 'ovabrw_childrens_max', true );
                    $numberof_children  = $max_children*$quantity;

                    // Min number of babies
                    $min_babies = (int)get_post_meta( $product_id, 'ovabrw_babies_min', true );

                    // Max number of babies
                    $max_babies         = (int)get_post_meta( $product_id, 'ovabrw_babies_max', true );
                    $numberof_babies    = $max_babies*$quantity;
                }

                // Next date
                if ( $next ) {
                    $next += 1;
                    $checkin_dates  = array_slice( $checkin_dates, $next );
                    $checkout_dates = array_slice( $checkout_dates, $next );
                }

                // Unavailable time
                $untime_startdate = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
                $untime_enddate   = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

                // Disable weekdays
                $disable_weekdays = get_post_meta( $product_id, 'ovabrw_product_disable_week_day', true );
                if ( !$disable_weekdays ) {
                    $disable_weekdays = get_option( 'ova_brw_calendar_disable_week_day', '' );
                }
                if ( '' !== $disable_weekdays ) $disable_weekdays = explode( ',', $disable_weekdays );

                // Cart
                $cart_booked = ovabrw_get_booked_dates_form_cart( $product_id );

                // Order
                $order_booked = ovabrw_get_booked_dates_from_order( $product_id );

                // Loop
                foreach ( $checkin_dates as $k => $checkin_date ) {
                    $checkin_date   = strtotime( $checkin_date );
                    $checkout_date  = isset( $checkout_dates[$k] ) ? strtotime( $checkout_dates[$k] ) : '';

                    // Check exits
                    if ( !$checkin_date || !$checkout_date || $checkout_date < $checkin_date ) {
                        continue;
                    }

                    // Preparation Time
                    $preparation_time = get_post_meta( $product_id, 'ovabrw_preparation_time', true );
                    if ( $preparation_time ) {
                        $new_dates = ovabrw_new_input_date( $product_id, $checkout_date, $checkout_date, $date_format );

                        if ( $new_dates['pickup_date_new'] < ( current_time( 'timestamp' ) + $preparation_time*86400 - 86400 ) ) continue;
                    }

                    // Check Unavailable time
                    if ( ! empty( $untime_startdate ) && is_array( $untime_startdate ) ) {
                        // Day is blocked
                        $is_blocked = false;

                        foreach ( $untime_startdate as $untime_key => $start_date ) {
                            $start_date = strtotime( $start_date );
                            $end_date   = isset( $untime_enddate[$untime_key] ) ? strtotime( $untime_enddate[$untime_key] ) : '';

                            if ( !$start_date || !$end_date ) continue;
                            if ( !( $checkin_date > $end_date || $checkout_date < $start_date ) ) {
                                $is_blocked = true;
                                break;
                            }
                        }

                        // This day is blocked
                        if ( $is_blocked ) continue;
                    }
                    // End Check Unavailable time

                    // Check disable weekdays
                    if ( !empty( $disable_weekdays ) && is_array( $disable_weekdays ) ) {
                        // Day is blocked
                        $is_blocked = false;

                        // Init dates
                        $checkin_weekday    = $checkin_date;
                        $checkout_weekday   = $checkout_date;

                        // Get number of week
                        $checkin_numberof_week  = date('w', $checkin_weekday );
                        $checkout_numberof_week = date('w', $checkout_weekday );

                        if ( apply_filters( 'ovabrw_disable_week_day', true ) ) {
                            if ( in_array( $checkin_numberof_week, $disable_weekdays ) || in_array( $checkout_numberof_week, $disable_weekdays ) ) {
                                continue;
                            }

                            while ( $checkin_weekday < $checkout_weekday ) {
                                $checkin_weekday    += 86400; // 24*60*60 = 1 day
                                $numberof_week      = date('w', $checkin_weekday );

                                if ( in_array( $numberof_week, $disable_weekdays ) ) {
                                    $is_blocked = true;
                                    break;
                                }
                            }
                        } else {
                            if ( in_array( $checkin_numberof_week, $disable_weekdays ) ) {
                                continue;
                            }
                        }

                        // This day is blocked
                        if ( $is_blocked ) continue;
                    }
                    // End Check disable weekdays
                    
                    // Check Cart
                    if ( !empty( $cart_booked ) && is_array( $cart_booked ) ) {
                        // Day is blocked
                        $is_blocked = false;

                        foreach ( $cart_booked as $cart_item ) {
                            if ( ovabrw_qty_by_guests( $product_id ) && $checkin_date === $cart_item['checkout_date'] ) {
                                $checkin_date     -= (int)$cart_item['numberof_adults'];
                                $numberof_children   -= (int)$cart_item['numberof_children'];
                                $numberof_babies     -= (int)$cart_item['numberof_babies'];

                                // Get number of guests available
                                if ( $numberof_adults <= 0 && $numberof_children <= 0 && $numberof_babies <= 0 ) {
                                    $is_blocked = true;
                                    continue;
                                }

                                // Check min guests
                                if ( apply_filters( 'ovabrw_ft_check_min_guests', true, $product_id ) ) {
                                    if ( $numberof_adults < $min_adults || $numberof_children < $min_children || $numberof_babies < $min_babies ) {
                                        $is_blocked = true;
                                        continue;
                                    }
                                }
                            } else {
                                if ( !( $checkin_date >= $cart_item['checkout_date'] || $checkout_date <= $cart_item['checkin_date'] ) ) {
                                    $quantity -= (int)$cart_item['quantity'];

                                    if ( $quantity <= 0 ) {
                                        $is_blocked = true;
                                        continue;
                                    }
                                }
                            }
                        }

                        // This day is blocked
                        if ( $is_blocked ) continue;
                    }
                    // End Check Cart
                    
                    // Check Order
                    if ( !empty( $order_booked ) && is_array( $order_booked ) ) {
                        // Day is blocked
                        $is_blocked = false;

                        foreach ( $order_booked as $order_item ) {
                            if ( ovabrw_qty_by_guests( $product_id ) && $checkin_date === $cart_item['checkout_date'] ) {
                                $numberof_adults     -= (int)$order_item['numberof_adults'];
                                $numberof_children   -= (int)$order_item['numberof_children'];
                                $numberof_babies     -= (int)$order_item['numberof_babies'];

                                // Get number of guests available
                                if ( $numberof_adults <= 0 && $numberof_children <= 0 && $numberof_babies <= 0 ) {
                                    $is_blocked = true;
                                    continue;
                                }

                                // Check min guests
                                if ( apply_filters( 'ovabrw_ft_check_min_guests', true, $product_id ) ) {
                                    if ( $numberof_adults < $min_adults || $numberof_children < $min_children || $numberof_babies < $min_babies ) {
                                        $is_blocked = true;
                                        continue;
                                    }
                                }
                            } else {
                                if ( !( $checkin_date >= $order_item['checkout_date'] || $checkout_date <= $order_item['checkin_date'] ) ) {
                                    $quantity -= (int)$order_item['quantity'];

                                    if ( $quantity <= 0 ) {
                                        $is_blocked = true;
                                        continue;
                                    }
                                }
                            }
                        }

                        // This day is blocked
                        if ( $is_blocked ) continue;
                    }
                    // End Check Order

                    // Convert check-in date and check-out date
                    $checkin_date   = date( $date_format, $checkin_date );
                    $checkout_date  = date( $date_format, $checkout_date );
                    $return_dates[$checkin_date.'|'.$checkout_date] = sprintf( esc_html__( 'From %s to %s', 'ova-brw' ), $checkin_date, $checkout_date );

                    if ( count( $return_dates ) === $limited ) {
                        break;
                    }
                }
                // End Loop
                
                // Loading more option
                if ( count( $checkin_dates ) > $limited ) {
                    $k += $next;
                    $return_dates[$k] = esc_html__( 'more...', 'ova-brw' );
                }
            }
        }

        return apply_filters( 'ovabrw_get_fixed_dates', $return_dates, $product_id );
    }
}

// Get booked dates from Cart
if ( !function_exists( 'ovabrw_get_booked_dates_form_cart' ) ) {
    function ovabrw_get_booked_dates_form_cart( $product_id ) {
        $cart_booked = array();

        if ( $product_id && WC()->cart && !empty( WC()->cart->get_cart() ) ) {
            // WPML
            $product_ids = ovabrw_get_wpml_product_ids( $product_id );

            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $prod_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                if ( in_array( $prod_id, $product_ids ) ) {
                    // Check-in date
                    $checkin_date = isset( $cart_item['ovabrw_pickup_date'] ) ? strtotime( $cart_item['ovabrw_pickup_date'] ) : '';
                    if ( !$checkin_date ) continue;

                    // Check-out date
                    $checkout_date = isset( $cart_item['ovabrw_pickoff_date'] ) ? strtotime( $cart_item['ovabrw_pickoff_date'] ) : '';
                    if ( !$checkout_date ) continue;

                    // Cart quantity
                    $quantity = isset( $cart_item['ovabrw_quantity'] ) ? (int)$cart_item['ovabrw_quantity'] : 1;

                    // Number of Guests
                    $numberof_adults    = isset( $cart_item['ovabrw_adults'] ) ? (int)$cart_item['ovabrw_adults'] : 0;
                    $numberof_children  = isset( $cart_item['ovabrw_childrens'] ) ? (int)$cart_item['ovabrw_childrens'] : 0;
                    $numberof_babies    = isset( $cart_item['ovabrw_babies'] ) ? (int)$cart_item['ovabrw_babies'] : 0;

                    // Cart booked
                    $cart_booked[] = array(
                        'checkin_date'      => $checkin_date,
                        'checkout_date'     => $checkout_date,
                        'quantity'          => $quantity,
                        'numberof_adults'   => $numberof_adults*$quantity,
                        'numberof_children' => $numberof_children*$quantity,
                        'numberof_babies'   => $numberof_babies*$quantity,
                    );
                }
            }
        }

        return apply_filters( 'ovabrw_get_booked_dates_form_cart', $cart_booked, $product_id );
    }
}

// Get booked dates from Order
if ( !function_exists( 'ovabrw_get_booked_dates_from_order' ) ) {
    function ovabrw_get_booked_dates_from_order( $product_id ) {
        $order_booked = array();

        if ( $product_id ) {
            $status     = brw_list_order_status();
            $order_ids  = ovabrw_get_orders_by_product_id( $product_id, $status );

            if ( !empty( $order_ids ) && is_array( $order_ids ) ) {
                // WPML
                $product_ids = ovabrw_get_wpml_product_ids( $product_id );

                foreach ( $order_ids as $k => $order_id ) {
                    // Get Order Detail by Order ID
                    $order = wc_get_order( $order_id );


                    // Get Meta Data type line_item of Order
                    $line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

                    foreach ( $line_items as $item_id => $item ) {
                        $prod_id = $item->get_product_id();

                        if ( in_array( $prod_id, $product_ids ) ) {
                            // Check-in date
                            $checkin_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                            if ( !$checkin_date ) continue;

                            // Check-out date
                            $checkout_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                            if ( !$checkout_date ) continue;

                            // Quantity
                            $quantity = (int)$item->get_meta( 'ovabrw_quantity' );

                            // Number of adults
                            $numberof_adults = (int)$item->get_meta( 'ovabrw_adults' );

                            // Number of children
                            $numberof_children = (int)$item->get_meta( 'ovabrw_childrens' );

                            // Number of babies
                            $numberof_babies = (int)$item->get_meta( 'ovabrw_babies' );

                            // Order booked
                            $order_booked[] = array(
                                'checkin_date'      => $checkin_date,
                                'checkout_date'     => $checkout_date,
                                'quantity'          => $quantity,
                                'numberof_adults'   => $numberof_adults*$quantity,
                                'numberof_children' => $numberof_children*$quantity,
                                'numberof_babies'   => $numberof_babies*$quantity,
                            );
                        }
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_booked_dates_from_order', $order_booked, $product_id );
    }
}

// Show insurance amount
if ( !function_exists( 'ovabrw_show_insurance_amount' ) ) {
    function ovabrw_show_insurance_amount() {
        return apply_filters( 'ovabrw_show_insurance_amount', 'yes' === get_option( 'ovabrw_show_insurance_amount', 'yes' ) ? true : false );
    }
}

// Get insurance fee name
if ( !function_exists( 'ovabrw_get_insurance_fee_name' ) ) {
    function ovabrw_get_insurance_fee_name() {
        return apply_filters( 'ovabrw_get_insurance_fee_name', esc_html__( 'Insurance fees', 'ova-brw' ) );
    }
}

// Get insurance fee key
if ( !function_exists( 'ovabrw_get_insurance_fee_key' ) ) {
    function ovabrw_get_insurance_fee_key() {
        return apply_filters( 'ovabrw_get_insurance_fee_key', sanitize_title( ovabrw_get_insurance_fee_name() ) );
    }
}

// Insurance tax enabled
if ( !function_exists( 'ovabrw_insurance_tax_enabled' ) ) {
    function ovabrw_insurance_tax_enabled() {
        return apply_filters( 'ovabrw_insurance_tax_enabled', ( wc_tax_enabled() && 'yes' === get_option( 'ovabrw_insurance_tax_enabled', 'no' ) ? true : false ) );
    }
}

// Insurance paid once
if ( !function_exists( 'ovabrw_insurance_paid_once' ) ) {
    function ovabrw_insurance_paid_once() {
        return apply_filters( 'ovabrw_insurance_paid_once', 'yes' === get_option( 'ovabrw_insurance_paid_once', 'no' ) ? true : false );
    }
}

// Get insurance tax class
if ( !function_exists( 'ovabrw_get_insurance_tax_class' ) ) {
    function ovabrw_get_insurance_tax_class() {
        return apply_filters( 'ovabrw_get_insurance_tax_class', '' );
    }
}

// Get insurance tax amount
if ( !function_exists( 'ovabrw_get_insurance_tax_amount' ) ) {
    function ovabrw_get_insurance_tax_amount( $price = 0 ) {
        $tax_amount = 0;

        if ( ovabrw_insurance_tax_enabled() ) {
            $tax_rates  = WC_Tax::get_rates( $ovabrw_get_insurance_tax_class() );
            $taxes      = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
            $tax_amount += WC_Tax::get_tax_total( $taxes );
        }

        return apply_filters( 'ovabrw_get_insurance_tax_amount', $tax_amount, $price );
    }
}

// Get insurance inclusive tax
if ( !function_exists( 'ovabrw_get_insurance_inclusive_tax' ) ) {
    function ovabrw_get_insurance_inclusive_tax( $price = 0 ) {
        $tax_display = get_option( 'woocommerce_tax_display_cart' );

        if ( wc_tax_enabled() && 'incl' === $tax_display ) {
            $price += ovabrw_get_insurance_tax_amount( $price );
        }

        return apply_filters( 'ovabrw_get_insurance_inclusive_tax', $price );
    }
}

// Get orders not remaining invoice
if ( !function_exists( 'ovabrw_get_orders_not_remaining_invoice' ) ) {
    function ovabrw_get_orders_not_remaining_invoice() {
        global $wpdb;

        $order_ids      = [];
        $order_status   = brw_list_order_status();

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitem_meta.meta_key = 'ovabrw_remaining_amount'
                AND oitem_meta.meta_value != 0
                AND o.status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        } else {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts
                ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND oitem_meta.meta_key = 'ovabrw_remaining_amount'
                AND oitem_meta.meta_value != 0
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        }

        return apply_filters( 'ovabrw_get_orders_not_remaining_invoice', $order_ids );
    }
}