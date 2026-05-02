<?php if ( !defined( 'ABSPATH' ) ) exit();

/* Load Template */
add_filter( 'template_include', 'ovabrw_template_loader', 99 );
function ovabrw_template_loader( $template ) {
    $search = isset( $_REQUEST['ovabrw_search'] ) ? esc_html( $_REQUEST['ovabrw_search'] ) : '';
    $request_booking = isset( $_REQUEST['request_booking'] ) ? esc_html( $_REQUEST['request_booking'] ) : '';

    // Get product template
    $product_template = get_option( 'ova_brw_template_elementor_template', 'default' );

    if ( is_product() ) {

        $product_id = get_the_id();
        $product    = wc_get_product( $product_id );

        if ( $product && $product->is_type('ovabrw_car_rental') ) {
            $template = wc_get_template( 'rental/single-product.php', array( 'id' => $product_id ) );
        }
    }
    
    // Search Form
    if ( $search != '' ) {
        return ovabrw_get_template( 'search_result.php' );
    }
    
    // Request Booking Form
    if ( $request_booking != '') {
        if ( ovabrw_request_booking( $_REQUEST ) ) {
            $thank_page = get_option( 'ova_brw_request_booking_form_thank_page', home_url('/') );
            $object_id  = '';

            // Multi language
            if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
                $thank_page_id  = url_to_postid( $thank_page );
                $object_id      = apply_filters( 'wpml_object_id', $thank_page_id, 'page', TRUE  );
            } elseif ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {
                $thank_page_id  = url_to_postid( $thank_page );
                $object_id      = pll_get_post( $thank_page_id );
            }

            if ( $object_id ) {
                $thank_page = get_permalink( $object_id );
            }

            wp_redirect( apply_filters( 'ovabrw_request_booking_thank_page_url', $thank_page ) );
        } else {
            $error_page = get_option( 'ova_brw_request_booking_form_error_page', home_url('/') );
            $object_id  = '';

            // Multi language
            if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
                $error_page_id  = url_to_postid( $error_page );
                $object_id      = apply_filters( 'wpml_object_id', $error_page_id, 'page', TRUE  );
            } elseif ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {
                $error_page_id  = url_to_postid( $error_page );
                $object_id      = pll_get_post( $error_page_id );
            }

            if ( $object_id ) {
                $error_page = get_permalink( $object_id );
            }

            wp_redirect( apply_filters( 'ovabrw_request_booking_error_page_url', $error_page ) );
        }

        exit();
    }

    return $template;
}

// ADD A PRODUCT TYPE **************************************/
add_filter( 'product_type_selector', 'ovabrw_add_custom_product_type', 10, 1 );
add_action( 'init', 'ovabrw_create_custom_product_type' );
function ovabrw_add_custom_product_type( $types ){
    $types[ 'ovabrw_car_rental' ] = esc_html__( 'Tour', 'ova-brw' );
    return $types;
}

function ovabrw_create_custom_product_type() {
    // declare the product class
    class WC_Product_Ovabrw_car_rental extends WC_Product {
        public function get_type() {
            return 'ovabrw_car_rental';
        }
    }
}

// Support Apple and Google Pay Button
add_filter( 'wcpay_payment_request_supported_types', function( $product_types ) {
    if ( ! empty( $product_types ) && is_array( $product_types ) ) {
        array_push( $product_types , 'ovabrw_car_rental' );
    }

    return $product_types;
});

// Support Google Listings & Ads
function ovabrw_gg_listings_ads_add_product_types( $product_types ) {
    if ( ! empty( $product_types ) && is_array( $product_types ) ) {
        array_push( $product_types , 'ovabrw_car_rental' );
    }
    return $product_types;
}
add_filter( 'woocommerce_gla_supported_product_types', 'ovabrw_gg_listings_ads_add_product_types', 10 );
add_filter( 'woocommerce_gla_attributes_tab_applicable_product_types', 'ovabrw_gg_listings_ads_add_product_types', 10 );

/* Add new product default show rental type */
add_filter( 'woocommerce_product_type_query', function( $product_type, $product_id ) {
    global $pagenow, $post_type;

    if ( $pagenow === 'post-new.php' && $post_type === 'product' ) {
        return 'ovabrw_car_rental';
    }

    return $product_type;
}, 10, 2 );

/* Hide Rental Type, Price Detail at Frontend */
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'change_formatted_meta_data', 20, 2 );
function change_formatted_meta_data( $meta_data, $item ) {
    $new_meta       = array();
    $hide_fields    = array();

     $hide_fields = array(
        'pay_total',
        'type_deposit',
        'deposit_value',
    );

    array_push( $hide_fields, 'ovabrw_time_from' );

    $show_checkout  = get_option( 'ova_brw_booking_form_show_checkout', 'yes' );
    $show_quantity  = get_option( 'ova_brw_booking_form_show_quantity', 'no' );
    $show_children  = get_option( 'ova_brw_booking_form_show_children', 'yes' );
    $show_baby      = get_option( 'ova_brw_booking_form_show_baby', 'yes' );

    if ( $show_checkout != 'yes' ) {
        array_push( $hide_fields, 'ovabrw_pickoff_date' );
    }

    if ( $show_quantity != 'yes' ) {
        array_push( $hide_fields, 'ovabrw_quantity' );
    }

    if ( $show_children != 'yes' ) {
        array_push( $hide_fields, 'ovabrw_childrens' );
    }

    if ( $show_baby != 'yes' ) {
        array_push( $hide_fields, 'ovabrw_babies' );
    }

    foreach ( $meta_data as $id => $meta_array ) {
        // We are removing the meta with the key 'something' from the whole array.
        if ( in_array( $meta_array->key, apply_filters( 'ovabrw_ft_hide_fields', $hide_fields ) ) ) { continue; }

        $new_meta[ $id ] = $meta_array;
    }

    return $new_meta;
}

add_filter( 'woocommerce_cart_item_quantity', 'ovabrw_filter_woocommerce_cart_item_quantity', 10, 3 );
function ovabrw_filter_woocommerce_cart_item_quantity( $quantity, $cart_item_key, $cart_item ) {
    if ( $cart_item['data']->is_type('ovabrw_car_rental') ) {

        $product_id = $cart_item['product_id'];

        $quantity = 1;
        if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
            $quantity = absint( $cart_item['ovabrw_quantity'] );
        }

        return '<span class="ovabrw_qty">'.$quantity.'</span>';
    } else {
        return $quantity;
    }
}; 

// Filter Quantity for Checkout
add_filter( 'woocommerce_checkout_cart_item_quantity', 'ovabrw_woocommerce_checkout_cart_item_quantity', 10, 3 );
function ovabrw_woocommerce_checkout_cart_item_quantity( $quantity, $cart_item, $cart_item_key ) {
    if ( $cart_item['data']->is_type('ovabrw_car_rental') ) {

        $product_id = $cart_item['product_id'];

        $quantity = 1;
        if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
            $quantity = absint( $cart_item['ovabrw_quantity'] );
        }

        return '<strong class="product-quantity">x '.$quantity.'</strong>';
    } else{
        return $quantity;
    }
}

// Filter Quantity for Order detail after checkout
add_filter( 'woocommerce_order_item_quantity_html', 'ovabrw_woocommerce_order_item_quantity_html', 10, 2 ); 
function ovabrw_woocommerce_order_item_quantity_html( $quantity, $item ){
    $product_id = $item->get_product_id();
    $product = wc_get_product( $product_id );

    $ovabrw_date_format = ovabrw_get_date_format();

    if ( $product && $product->is_type( 'ovabrw_car_rental' ) ) {
        return '<span class="ovabrw_qty"></span>';  
    }

    return $quantity;
}

/**
 * Changing a meta title
 * @param  string        $key  The meta key
 * @param  WC_Meta_Data  $meta The meta object
 * @param  WC_Order_Item $item The order item object
 * @return string        The title
 */
/* Change Order at backend */
add_filter( 'woocommerce_order_item_display_meta_key', 'ovabrw_change_order_item_meta_title', 20, 3 );
function ovabrw_change_order_item_meta_title( $key, $meta, $item ) {
    $date_format    = ovabrw_get_date_format();
    $product_id     = $item['product_id'];
    $duration       = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

    if ( $duration ) {
        $date_format = ovabrw_get_datetime_format();
    }

    // wc_tax_enabled
    $tax_text = $tax_text_remaining = '';

    if ( wc_tax_enabled() ) {

        $item_id  = $item->get_id();
        $order_id = $item->get_order_id();
        $order    = wc_get_order( $order_id );

        $remaining_item  = wc_get_order_item_meta( $item_id, 'ovabrw_remaining_amount', true );
        $is_tax_included = $order->get_meta( '_ova_tax_display_cart', true );
        $remaining_taxes = $order->get_meta( '_ova_remaining_taxes', true );
        $tax_message     = $is_tax_included ? __( '(incl. tax)', 'ova-brw' ) : __( '(excl. tax)', 'ova-brw' );

        if ( ! empty( $remaining_taxes ) ) {
            $tax_text    = ' <small class="tax_label">' . $tax_message . '</small>';
        }

        if ( ! empty( $remaining_item && ! empty( $remaining_taxes ) ) ) {
            $tax_text_remaining = ' <small class="tax_label">' . $tax_message . '</small>';
        }
    }

    if ( 'ovabrw_pickup_date' === $meta->key ) { 
        $ovabrw_pickup_date = $meta->value;
        $meta->value = date_i18n( $date_format, strtotime( $ovabrw_pickup_date ) );

        $key = esc_html__(' Check-in date', 'ova-brw'); 
    }

    if ( 'ovabrw_pickoff_date' === $meta->key ) { 
        $ovabrw_pickoff_date = $meta->value;
        $meta->value = date_i18n( $date_format, strtotime( $ovabrw_pickoff_date ) );
        $key = esc_html__(' Check-out date', 'ova-brw'); 
    }

    if ( 'ovabrw_time_from' === $meta->key ) { $key = esc_html__( 'Time From', 'ova-brw'); }

    if ( 'ovabrw_adults' === $meta->key ) { $key = esc_html__( 'Adults', 'ova-brw'); }

    if ( 'ovabrw_childrens' === $meta->key ) { $key = esc_html__( 'Childrens', 'ova-brw'); }

    if ( 'ovabrw_babies' === $meta->key ) { $key = esc_html__( 'Babies', 'ova-brw'); }

    if ( 'ovabrw_quantity' === $meta->key ) { $key = esc_html__( 'Quantity', 'ova-brw'); }
    
    if ( 'ovabrw_amount_insurance' === $meta->key ) { $key = esc_html__( 'Amount Of Insurance', 'ova-brw' ); }

    if ( 'ovabrw_deposit_amount' === $meta->key ) { $key = esc_html__( 'Deposit Amount', 'ova-brw' ) . $tax_text; }

    if ( 'ovabrw_remaining_amount' === $meta->key ) { $key = esc_html__( 'Remaining Amount', 'ova-brw' ) . $tax_text_remaining; }

    if ( 'ovabrw_deposit_full_amount' === $meta->key ) { $key = esc_html__( 'Full Amount', 'ova-brw' ) . $tax_text; }
    
    $list_fields = get_option( 'ovabrw_booking_form', array() );

    if( is_array( $list_fields ) && ! empty( $list_fields ) ) {
        foreach( $list_fields as $key_field => $field ) {

            if( $key_field === $meta->key ) {
                $key = $field['label'];
            }
        }
    }
    
    return $key;
}

/**
 * Changing a meta value
 * @param  string        $value  The meta value
 * @param  WC_Meta_Data  $meta   The meta object
 * @param  WC_Order_Item $item   The order item object
 * @return string        The title
 */
/* Change in mail */
add_filter( 'woocommerce_order_item_display_meta_value', 'change_order_item_meta_value', 20, 3 );
function change_order_item_meta_value( $value, $meta, $item ) {
    $order = $item->get_order();

    // By using $meta-key we are sure we have the correct one.
    if ( 'ovabrw_pickup_date' === $meta->key ) { $key = esc_html__(' Check-in date ', 'ova-brw'); }
    if ( 'ovabrw_pickoff_date' === $meta->key ) { $key = esc_html__(' Check-out date  ', 'ova-brw'); }

    if ( 'ovabrw_amount_insurance' === $meta->key ) { 
        $key = esc_html__( 'Amount Of Insurance', 'ova-brw' );
        $value = wc_price( $meta->value, array( 'currency' => $order->get_currency() ));
    }
    if ( 'ovabrw_deposit_amount' === $meta->key ) { 
        $key =esc_html__( 'Deposit Amount', 'ova-brw' );
        $value = wc_price( $meta->value, array( 'currency' => $order->get_currency() ) );
    }
    if ( 'ovabrw_remaining_amount' === $meta->key ) { 
        $key = esc_html__( 'Remaining Amount', 'ova-brw' );
        $value = wc_price( $meta->value, array( 'currency' => $order->get_currency() ) );
    }
    if ( 'ovabrw_deposit_full_amount' === $meta->key ) { 
        $key = esc_html__( 'Full Amount', 'ova-brw' );
        $value = wc_price( $meta->value, array( 'currency' => $order->get_currency() ) );
    }

    return $value;
}


// Add javascript to head
add_action('admin_head', 'ovabrw_hook_javascript');
add_action('wp_head', 'ovabrw_hook_javascript');
function ovabrw_hook_javascript() {
    $lang_general_calendar = ovabrw_get_setting( get_option( 'ova_brw_calendar_language_general', 'en' ) );
    if ( function_exists('pll_current_language') ) {
        $lang_general_calendar = pll_current_language();
    }

    $date_format        = ovabrw_get_date_format();
    $time_format        = ovabrw_get_time_format();
    $step_time          = ovabrw_get_step_time();
    $disable_week_day   = ovabrw_get_setting( get_option( 'ova_brw_calendar_disable_week_day', '' ) );
    // Get first day in week
    $first_day          = ovabrw_get_setting( get_option( 'ova_brw_calendar_first_day', '0' ) );

    // Next years
    $next_year = apply_filters( 'ovabrw_ft_next_year', '' );

    // Defined label for custom checkout field
    $label_option_value =  esc_html__( 'Option Value', 'ova-brw' );
    $label_option_text  =  esc_html__( 'Option Text', 'ova-brw' );
    $label_add_new_opt  = esc_html__( 'Add new option', 'ova-brw' );
    $label_remove_opt   = esc_html__( 'Remove option', 'ova-brw' );
    $label_are_you_sure = esc_html__( 'Are you sure?', 'ova-brw' );
    $notifi_disable_day = esc_html__( 'You can\'t book on this day!', 'ova-brw' );
    ?>
        <script type="text/javascript">
            var brw_date_format = '<?php echo $date_format; ?>';
            var brw_time_format = '<?php echo $time_format; ?>';
            var brw_step_time   = '<?php echo $step_time; ?>';
            var brw_next_year   = '<?php echo $next_year; ?>';
            var brw_lang_general_calendar = '<?php echo $lang_general_calendar; ?>';
            var brw_first_day = '<?php echo $first_day; ?>';

            var label_option_value = '<?php echo $label_option_value; ?>';
            var label_option_text = '<?php echo $label_option_text; ?>';
            var label_add_new_opt = '<?php echo $label_add_new_opt; ?>';
            var label_remove_opt = '<?php echo $label_remove_opt; ?>';
            var label_are_you_sure = '<?php echo $label_are_you_sure; ?>';
            var notifi_disable_day = '<?php echo $notifi_disable_day; ?>';
        </script>
    <?php
    if ( is_product() ) {
    ?>
        <script type="text/javascript">
            var brw_disable_week_day = '<?php echo $disable_week_day; ?>';
        </script>
    <?php
    }
}

add_filter( 'wc_order_statuses', 'wc_closed_order_statuses' );
// Register in wc_order_statuses.
function wc_closed_order_statuses( $order_statuses ) {
    $order_statuses['wc-closed'] = _x( 'Closed', 'Order status', 'ova-brw' );

    return $order_statuses;
}

// Replace product link in Search Result Page
add_filter( 'woocommerce_loop_product_link', 'ovarbrw_woocommerce_loop_product_link', 10, 1 );
function ovarbrw_woocommerce_loop_product_link( $product_link ){
    if ( isset( $_GET['ovabrw_search'] ) ) {
        if ( isset( $_GET['ovabrw_pickup_date'] ) && $_GET['ovabrw_pickup_date'] ) {
            $product_link = add_query_arg( 'pickup_date', $_GET['ovabrw_pickup_date'], $product_link );
        }

        if ( isset( $_GET['ovabrw_pickoff_date'] ) && $_GET['ovabrw_pickoff_date'] ) {
            $product_link = add_query_arg( 'dropoff_date', $_GET['ovabrw_pickoff_date'], $product_link );
        }
    }

    return $product_link;
}

add_filter( 'woocommerce_loop_add_to_cart_link', 'ovarbrw_woocommerce_loop_add_to_cart_link', 10, 3 );
function ovarbrw_woocommerce_loop_add_to_cart_link( $link, $product, $args ){
    $product_link = $product->add_to_cart_url();

    if ( isset( $_GET['ovabrw_search'] ) ){
        if( isset( $_GET['ovabrw_pickup_date'] ) && $_GET['ovabrw_pickup_date'] ){
            $product_link = add_query_arg( 'pickup_date', $_GET['ovabrw_pickup_date'], $product_link );
        }

        if( isset( $_GET['ovabrw_pickoff_date'] ) && $_GET['ovabrw_pickoff_date'] ){
            $product_link = add_query_arg( 'dropoff_date', $_GET['ovabrw_pickoff_date'], $product_link );
        }
    }

    return sprintf(
        '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
        esc_url( $product_link ),
        esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
        esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
        isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
        esc_html( $product->add_to_cart_text() )
    );
}

// Allow users cancel Order
add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'ovabrw_woo_valid_order_statuses_for_cancel', 10, 2 );
function ovabrw_woo_valid_order_statuses_for_cancel( $array_status, $order ){
    $order_status_can_cancel = $time_can_cancel = $other_condition = $total_order_valid = true;
     
    if ( in_array( $order->get_status(), array( 'pending', 'failed' ) ) ) {
        return array( 'pending', 'failed' );
    }

    // Check order status can order
    if ( ! in_array( $order->get_status(), apply_filters( 'ovabrw_order_status_can_cancel', array( 'completed', 'processing', 'on-hold', 'pending', 'failed' ) ) )  ){
        $order_status_can_cancel = false;
    }
    
    // Validate before x hours can cancel
    // Get Meta Data type line_item of Order
    $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

    foreach ( $order_line_items as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $product    = wc_get_product( $product_id );

        $cancel_valid_minutes   = ovabrw_get_setting( get_option( 'ova_brw_cancel_before_x_hours', 0 ) );
        $cancel_valid_total     = ovabrw_get_setting( get_option( 'ova_brw_cancel_condition_total_order', 1 ) );

        // Check if product type is rental
        if ( $product && $product->get_type() == 'ovabrw_car_rental' ) {
            // Get value of pickup date, pickoff date
            $ovabrw_pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

            if ( ! ( $ovabrw_pickup_date > current_time( 'timestamp' ) && $ovabrw_pickup_date - current_time( 'timestamp' ) > $cancel_valid_minutes*60*60  ) ) {
                $time_can_cancel = false;
                break;
            }
        }
    }

    // Cancel by total order
    if( empty( $cancel_valid_total ) ){
        $total_order_valid = true;
    }else if( $order->get_total() > floatval( $cancel_valid_total ) ){
        $total_order_valid = false;
    }

    // Other condition
    $other_condition = apply_filters( 'ovabrw_other_condition_to_cancel_order', true, $order );

    if ( $order_status_can_cancel && $time_can_cancel && $total_order_valid && $other_condition ) {
        return array( 'completed', 'processing', 'on-hold', 'pending', 'failed' );
    } else {
        return array();
    }
}

// Display Item Meta in Order Detail
add_filter( 'woocommerce_display_item_meta', 'ovabrw_woocommerce_display_item_meta', 10, 3 );
function ovabrw_woocommerce_display_item_meta( $html, $item, $args ){
    $strings = array();
    $html    = '';
    $args    = wp_parse_args(
        $args,
        array(
            'before'       => '<ul class="wc-item-meta"><li>',
            'after'        => '</li></ul>',
            'separator'    => '</li><li>',
            'echo'         => true,
            'autop'        => false,
            'label_before' => '<strong class="wc-item-meta-label">',
            'label_after'  => ':</strong> ',
        )
    );

    foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
        if ( in_array( $meta->key , apply_filters( 'ovabrw_order_detail_hide_fields', array() ) ) ) {
            $strings[] = '';
        } else {
            $value     = $args['autop'] ? wp_kses_post( $meta->display_value ) : wp_kses_post( make_clickable( trim( $meta->display_value ) ) );
            $strings[] = $args['label_before'] . wp_kses_post( $meta->display_key ) . $args['label_after'] . $value;    
        }
    }

    if ( $strings ) {
        $html = $args['before'] . implode( $args['separator'], $strings ). $args['after'];
    }

    $html = str_replace('ovabrw_pickup_date', esc_html__(' Check-in ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_pickoff_date', esc_html__(' Check-out ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_price_detail', esc_html__(' Price Detail ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_original_order_id', esc_html__(' Original Order ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_remaining_balance_order_id', esc_html__(' Remaining Balance Order ', 'ova-brw') , $html );

    return $html;
}