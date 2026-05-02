<?php if (!defined( 'ABSPATH' )) exit;

// Get current ID of post/page, etc
if( !function_exists( 'tripgo_get_current_id' )):
	function tripgo_get_current_id(){
	    
	    $current_page_id = '';
	    // Get The Page ID You Need
	    
	    if(class_exists("woocommerce")) {
	        if( is_shop() ){ ///|| is_product_category() || is_product_tag()) {
	            $current_page_id  =  get_option ( 'woocommerce_shop_page_id' );
	        }elseif(is_cart()) {
	            $current_page_id  =  get_option ( 'woocommerce_cart_page_id' );
	        }elseif(is_checkout()){
	            $current_page_id  =  get_option ( 'woocommerce_checkout_page_id' );
	        }elseif(is_account_page()){
	            $current_page_id  =  get_option ( 'woocommerce_myaccount_page_id' );
	        }elseif(is_view_order_page()){
	            $current_page_id  = get_option ( 'woocommerce_view_order_page_id' );
	        }
	    }
	    if($current_page_id=='') {
	        if ( is_home () && is_front_page () ) {
	            $current_page_id = '';
	        } elseif ( is_home () ) {
	            $current_page_id = get_option ( 'page_for_posts' );
	        } elseif ( is_search () || is_category () || is_tag () || is_tax () || is_archive() ) {
	            $current_page_id = '';
	        } elseif ( !is_404 () ) {
	           $current_page_id = get_the_id();
	        } 
	    }

	    return $current_page_id;
	}
endif;



if (!function_exists('tripgo_is_elementor_active')) {
    function tripgo_is_elementor_active(){
        return did_action( 'elementor/loaded' );
    }
}

if (!function_exists('tripgo_is_woo_active')) {
    function tripgo_is_woo_active(){
        return class_exists('woocommerce');    
    }
}

if (!function_exists('tripgo_is_blog_archive')) {
    function tripgo_is_blog_archive() {
        return (is_home() && is_front_page()) || is_archive() || is_category() || is_tag() || is_home();
    }
}

if (!function_exists('tripgo_is_woo_archive')) {
    function tripgo_is_woo_archive() {
        return is_post_type_archive('product') || is_tax('product_cat') || is_tax('product_tag');
    }
}


/* Get ID from Slug of Header Footer Builder - Post Type */
function tripgo_get_id_by_slug( $page_slug ) {
    $page = get_page_by_path( $page_slug, OBJECT, 'ova_framework_hf_el' ) ;
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}


function tripgo_custom_text ($content = "",$limit = 15) {

    $content = explode(' ', $content, $limit);

    if (count($content)>=$limit) {
        array_pop($content);
        $content = implode(" ",$content).'...';
    } else {
        $content = implode(" ",$content);
    }

    $content = preg_replace('`[[^]]*]`','',$content);
    
    return strip_tags( $content );
}



/**
 * Google Font sanitization
 *
 * @param  string   JSON string to be sanitized
 * @return string   Sanitized input
 */
if ( ! function_exists( 'tripgo_google_font_sanitization' ) ) {
    function tripgo_google_font_sanitization( $input ) {
        $val =  json_decode( $input, true );
        if( is_array( $val ) ) {
            foreach ( $val as $key => $value ) {
                $val[$key] = sanitize_text_field( $value );
            }
            $input = json_encode( $val );
        }
        else {
            $input = json_encode( sanitize_text_field( $val ) );
        }
        return $input;
    }
}


/* Default Primary Font in Customize */
if ( ! function_exists( 'tripgo_default_primary_font' ) ) {
    function tripgo_default_primary_font() {
        $customizer_defaults = json_encode(
            array(
                'font' => 'HK Grotesk',
                'regularweight' => '300,400,500,600,700,800,900',
                'category' => 'serif'
            )
        );

        return $customizer_defaults;
    }
}

if ( ! function_exists( 'tripgo_woo_sidebar' ) ) {
    function tripgo_woo_sidebar(){
        if( class_exists('woocommerce') && is_product() ){
            return get_theme_mod( 'woo_product_layout', 'woo_layout_1c' );
        }else{
            return get_theme_mod( 'woo_archive_layout', 'woo_layout_1c' );
        }
    }
}

if( !function_exists( 'tripgo_blog_show_media' ) ){
    function tripgo_blog_show_media(){
        $show_media = get_theme_mod( 'blog_archive_show_media', 'yes' );
        return isset( $_GET['show_media'] ) ? sanitize_text_field( $_GET['show_media'] ) : $show_media;
    }
}

if( !function_exists( 'tripgo_blog_show_title' ) ){
    function tripgo_blog_show_title(){
        $show_title = get_theme_mod( 'blog_archive_show_title', 'yes' );
        return isset( $_GET['show_title'] ) ? sanitize_text_field( $_GET['show_title'] ) : $show_title;
    }
}

if( !function_exists( 'tripgo_blog_show_date' ) ){
    function tripgo_blog_show_date(){
        $show_date = get_theme_mod( 'blog_archive_show_date', 'yes' );
        return isset( $_GET['show_date'] ) ? sanitize_text_field( $_GET['show_date'] ) : $show_date;
    }
}

if( !function_exists( 'tripgo_blog_show_cat' ) ){
    function tripgo_blog_show_cat(){
        $show_cat = get_theme_mod( 'blog_archive_show_cat', 'yes' );
        return isset( $_GET['show_cat'] ) ? sanitize_text_field( $_GET['show_cat'] ) : $show_cat;
    }
}

if( !function_exists( 'tripgo_blog_show_author' ) ){
    function tripgo_blog_show_author(){
        $show_author = get_theme_mod( 'blog_archive_show_author', 'yes' );
        return isset( $_GET['show_author'] ) ? sanitize_text_field( $_GET['show_author'] ) : $show_author;
    }
}

if( !function_exists( 'tripgo_blog_show_comment' ) ){
    function tripgo_blog_show_comment(){
        $show_comment = get_theme_mod( 'blog_archive_show_comment', 'yes' );
        return isset( $_GET['show_comment'] ) ? sanitize_text_field( $_GET['show_comment'] ) : $show_comment;
    }
}

if( !function_exists( 'tripgo_blog_show_excerpt' ) ){
    function tripgo_blog_show_excerpt(){
        $show_excerpt = get_theme_mod( 'blog_archive_show_excerpt', 'yes' );
        return isset( $_GET['show_excerpt'] ) ? sanitize_text_field( $_GET['show_excerpt'] ) : $show_excerpt;
    }
}


if( !function_exists( 'tripgo_blog_show_readmore' ) ){
    function tripgo_blog_show_readmore(){
        $show_readmore = get_theme_mod( 'blog_archive_show_readmore', 'yes' );
        return isset( $_GET['show_readmore'] ) ? sanitize_text_field( $_GET['show_readmore'] ) : $show_readmore;
    }
}


if( !function_exists( 'tripgo_post_show_media' ) ){
    function tripgo_post_show_media(){
        $show_media = get_theme_mod( 'blog_single_show_media', 'yes' );
        return isset( $_GET['show_media'] ) ? sanitize_text_field( $_GET['show_media'] ) : $show_media;
    }
}

if( !function_exists( 'tripgo_post_show_title' ) ){
    function tripgo_post_show_title(){
        $show_title = get_theme_mod( 'blog_single_show_title', 'yes' );
        return isset( $_GET['show_title'] ) ? sanitize_text_field( $_GET['show_title'] ) : $show_title;
    }
}

if( !function_exists( 'tripgo_post_show_date' ) ){
    function tripgo_post_show_date(){
        $show_date = get_theme_mod( 'blog_single_show_date', 'yes' );
        return isset( $_GET['show_date'] ) ? sanitize_text_field( $_GET['show_date'] ) : $show_date;
    }
}

if( !function_exists( 'tripgo_post_show_cat' ) ){
    function tripgo_post_show_cat(){
        $show_cat = get_theme_mod( 'blog_single_show_cat', 'yes' );
        return isset( $_GET['show_cat'] ) ? sanitize_text_field( $_GET['show_cat'] ) : $show_cat;
    }
}

if( !function_exists( 'tripgo_post_show_author' ) ){
    function tripgo_post_show_author(){
        $show_author = get_theme_mod( 'blog_single_show_author', 'yes' );
        return isset( $_GET['show_author'] ) ? sanitize_text_field( $_GET['show_author'] ) : $show_author;
    }
}

if( !function_exists( 'tripgo_post_show_comment' ) ){
    function tripgo_post_show_comment(){
        $show_comment = get_theme_mod( 'blog_single_show_comment', 'yes' );
        return isset( $_GET['show_comment'] ) ? sanitize_text_field( $_GET['show_comment'] ) : $show_comment;
    }
}

if( !function_exists( 'tripgo_post_show_content' ) ){
    function tripgo_post_show_content(){
        $show_content = get_theme_mod( 'blog_single_show_content', 'yes' );
        return isset( $_GET['show_content'] ) ? $_GET['show_content'] : $show_content;
    }
}

if( !function_exists( 'tripgo_post_show_tag' ) ){
    function tripgo_post_show_tag(){
        $show_tag = get_theme_mod( 'blog_single_show_tag', 'yes' );
        return isset( $_GET['show_tag'] ) ? sanitize_text_field( $_GET['show_tag'] ) : $show_tag;
    }
}

if( !function_exists( 'tripgo_post_show_share_social_icon' ) ){
    function tripgo_post_show_share_social_icon(){
        $show_share_social_icon = get_theme_mod( 'blog_single_show_share_social_icon', 'yes' );
        return isset( $_GET['show_share_social_icon'] ) ? sanitize_text_field( $_GET['show_share_social_icon'] ) : $show_share_social_icon;
    }
}

if( !function_exists( 'tripgo_post_show_next_prev_post' ) ){
    function tripgo_post_show_next_prev_post(){
        $show_next_prev_post = get_theme_mod( 'blog_single_show_next_prev_post', 'yes' );
        return isset( $_GET['show_next_prev_post'] ) ? sanitize_text_field( $_GET['show_next_prev_post'] ) : $show_next_prev_post;
    }
}

if( !function_exists( 'tripgo_post_show_leave_a_reply' ) ){
    function tripgo_post_show_leave_a_reply(){
        $show_leave_a_reply = get_theme_mod( 'blog_single_show_leave_a_reply', 'yes' );
        return isset( $_GET['show_leave_a_reply'] ) ? $_GET['show_leave_a_reply'] : $show_leave_a_reply;
    }
}

/* Get Gallery ids Product */
if ( !function_exists( 'tripgo_get_gallery_ids' ) ) {
    function tripgo_get_gallery_ids( $product_id ) {
        $product = wc_get_product( $product_id );

        if ( $product ) {
            $arr_image_ids = array();

            $product_image_id = $product->get_image_id();
            if ( $product_image_id ) {
                array_push( $arr_image_ids, $product_image_id );
            }

            $product_gallery_ids = $product->get_gallery_image_ids();
            if ( $product_gallery_ids && is_array( $product_gallery_ids ) ) {
                $arr_image_ids = array_merge( $arr_image_ids, $product_gallery_ids );
            }

            return $arr_image_ids;
        }
        return false;
    }
}

/* Get Price Product */
if ( !function_exists( 'tripgo_get_price_product' ) ) {
    function tripgo_get_price_product( $product_id ) {
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return array(
                'regular_price' => 0,
                'sale_price'    => 0,
            );
        }

        $regular_price = $sale_price = 0;

        if ( $product->is_on_sale() && $product->get_sale_price() ) {
            $regular_price  = $product->get_sale_price();
            $sale_price     = $product->get_regular_price();
        } else {
            $regular_price = $product->get_regular_price();
        }

        $result = array(
            'regular_price' => $regular_price,
            'sale_price'    => $sale_price,
        );

        return $result;
    }
}

// Get Price - Multi Currency
if ( ! function_exists( 'ovabrw_wc_price' ) ) {
    function ovabrw_wc_price( $price = null, $args = array(), $convert = true ) {
        $new_price = $price;

        if ( ! $price ) $new_price = 0;

        do_action( 'ovabrw_wc_price_before', $price, $args, $convert );

        $current_currency = isset( $args['currency'] ) && $args['currency'] ? $args['currency'] : false;

        // CURCY - Multi Currency for WooCommerce
        // WooCommerce Multilingual & Multicurrency
        if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
            $new_price = wmc_get_price( $price, $current_currency );
        } elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
            if ( $convert ) {
                // WPML multi currency
                global $woocommerce_wpml;

                if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
                    if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

                    $multi_currency     = $woocommerce_wpml->get_multi_currency();
                    $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
                    $WMCP               = new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
                    $new_price          = $WMCP->convert_price_amount( $price, $current_currency );
                }
            }
        } else {
            // nothing
        }

        do_action( 'ovabrw_wc_price_after', $price, $args, $convert );
        
        return apply_filters( 'ovabrw_wc_price', wc_price( $new_price, $args ), $price, $args, $convert );
    }
}

// Convert Price - Multi Currency
if ( ! function_exists( 'ovabrw_convert_price' ) ) {
    function ovabrw_convert_price( $price = null, $args = array(), $convert = true ) {
        $new_price = $price;

        if ( ! $price ) $new_price = 0;

        do_action( 'ovabrw_convert_price_before', $price, $args, $convert );

        $current_currency = isset( $args['currency'] ) && $args['currency'] ? $args['currency'] : false;

        // CURCY - Multi Currency for WooCommerce
        // WooCommerce Multilingual & Multicurrency
        if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
            $new_price = wmc_get_price( $price, $current_currency );
        } elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
            if ( $convert ) {
                // WPML multi currency
                global $woocommerce_wpml;

                if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
                    if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

                    $multi_currency     = $woocommerce_wpml->get_multi_currency();
                    $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
                    $WMCP               = new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
                    $new_price          = $WMCP->convert_price_amount( $price, $current_currency );
                }
            }
        } else {
            // nothing
        }

        do_action( 'ovabrw_convert_price_after', $price, $args, $convert );
        
        return apply_filters( 'ovabrw_convert_price', $new_price, $price, $args, $convert );
    }
}

// Convert Price in Admin - Multi Currency
if ( ! function_exists( 'ovabrw_convert_price_in_admin' ) ) {
    function ovabrw_convert_price_in_admin( $price = null, $currency_code = '' ) {
        $new_price = $price;

        if ( ! $price ) $new_price = 0;

        if ( is_admin() && ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) ) {
            $setting = '';
            
            if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
                $setting = WOOMULTI_CURRENCY_F_Data::get_ins();
            }

            if ( is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
                $setting = WOOMULTI_CURRENCY_Data::get_ins();
            }

            if ( ! empty( $setting ) && is_object( $setting ) ) {
                /*Check currency*/
                $selected_currencies = $setting->get_list_currencies();
                $current_currency    = $setting->get_current_currency();

                if ( ! $currency_code || $currency_code === $current_currency ) {
                    return $new_price;
                }

                if ( $new_price ) {
                    if ( $currency_code && isset( $selected_currencies[ $currency_code ] ) ) {
                        $new_price = $price * (float) $selected_currencies[ $currency_code ]['rate'];
                    } else {
                        $new_price = $price * (float) $selected_currencies[ $current_currency ]['rate'];
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_convert_price_in_admin', $new_price, $price, $currency_code );
    }
}

// Conver number to hours
if ( ! function_exists( 'ovabrw_convert_number_to_hours' ) ) {
    function ovabrw_convert_number_to_hours( $number = '' ) {
        if ( ! $number ) return false;

        $hours = floor( (float)$number );

        return absint( $hours );
    }
}


// Conver number to minutes
if ( ! function_exists( 'ovabrw_convert_number_to_minutes' ) ) {
    function ovabrw_convert_number_to_minutes( $number = '' ) {
        if ( ! $number ) return false;

        $hours      = floor( (float)$number );
        $minutes    = round( ( $number - $hours ) * 60 );

        return absint( $minutes );
    }
}