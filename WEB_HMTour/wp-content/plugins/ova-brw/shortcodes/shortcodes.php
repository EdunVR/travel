<?php if ( ! defined( 'ABSPATH' ) ) exit();

add_filter( 'widget_text', 'do_shortcode' );

// Search Shortcode
add_shortcode( 'ovabrw_search', 'ovabrw_search' );
function ovabrw_search( $atts = array(), $content = null ){
    global $product;

    $atts = extract( shortcode_atts(
    array(
        'template'  => 'search_form_full',
        'column' => '',
        
        'show_name_product' => '',
        'show_attribute' => '',
        'show_tag_product' => '',
        'show_pickup_loc' => '',
        'show_dropoff_loc' => '',
        'show_pickup_date' => '',
        'show_dropoff_date' => '',
        'show_cat' => '',
        'show_tax' => '',
        
        'name_product_required' => '',
        'tag_product_required' => '',
        'pickup_loc_required' => '',
        'dropoff_loc_required' => '',
        'pickup_date_required' => '',
        'dropoff_date_required' => '',
        'category_required' => '',
        'attribute_required' => '',

        'hide_taxonomies_slug' => '',
        'remove_cats_id'    => '',
        'taxonomies_slug_required' => '',
        'timepicker' => 'false',
        'dateformat' => '',
        'hour_default'  => '',
        'time_step' => '',
        'order' => 'ASC',
        'orderby' => 'date',
        'class'   => '',
    ), $atts) );

    
    $column = $column == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_column', 'one-column' ) ) : $column;
    $timepicker = $timepicker == '' ? 'false' : $timepicker;

    $show_name_product = $show_name_product == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_name_product', 'yes' ) ) : $show_name_product;
    $show_attribute = $show_attribute == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_attribute', 'yes' ) ) : $show_attribute;
    $show_tag_product = $show_tag_product == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_tag_product', 'yes' ) ) : $show_tag_product;
    $show_pickup_loc = $show_pickup_loc == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_pick_up_location', 'yes' ) ) : $show_pickup_loc;
    $show_dropoff_loc = $show_dropoff_loc == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_drop_off_location', 'yes' ) ) : $show_dropoff_loc;
    $show_pickup_date = $show_pickup_date == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_pick_up_date', 'yes' ) ) : $show_pickup_date;
    $show_dropoff_date = $show_dropoff_date == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_drop_off_date', 'yes' ) ) : $show_dropoff_date;
    $show_cat = $show_cat == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_category', 'yes' ) ) : $show_cat;
    $show_tax = $show_tax == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_show_taxonomy', 'yes' ) ) : $show_tax;

    if( $name_product_required == '' ){
        $name_product_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_name_product', 'no' ) ) == 'yes' ? 'required' : '';
    }

    if( $tag_product_required == '' ){
        $tag_product_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_tag_product', 'no' ) ) == 'yes' ? 'required' : '';
    }

    if( $pickup_loc_required == '' ){
        $pickup_loc_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_pick_up_location', 'no' ) ) == 'yes' ? 'required' : '';
    }

    if( $dropoff_loc_required == '' ){
        $dropoff_loc_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_drop_off_location', 'no' ) ) == 'yes' ? 'required' : '';
    }
    if( $pickup_date_required == '' ){
        $pickup_date_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_pick_up_date', 'no' ) ) == 'yes' ? 'required' : '';    
    }

    if( $dropoff_date_required == '' ){
        $dropoff_date_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_drop_off_date', 'no' ) ) == 'yes' ? 'required' : '';
    }

    if( $category_required == '' ){
        $category_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_category', 'no' ) ) == 'yes' ? 'required' : '';
    }

    if( $attribute_required == '' ){
        $attribute_required = ovabrw_get_setting( get_option( 'ova_brw_search_require_attribute', 'no' ) ) == 'yes' ? 'required' : '';
    }

    $dateformat = $dateformat == '' ? ovabrw_get_date_format() : $dateformat;
    $hour_default = $hour_default == '' ? ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) ) : $hour_default;
    $time_step = $time_step == '' ? ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) ) : $time_step;


    $hide_taxonomies_slug =  $hide_taxonomies_slug == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_hide_taxonomy_slug', '' ) ) : $hide_taxonomies_slug; 
    $taxonomies_slug_required = $taxonomies_slug_required == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_require_taxonomy_slug', '' ) ) : $taxonomies_slug_required;

    $arr_hide_taxonomy = array_map( 'trim',  explode( ',', $hide_taxonomies_slug ) );
    $arr_require_taxonomy = array_map( 'trim', explode( ',',  $taxonomies_slug_required ) );
    

    $taxonomy_list_wrap = [];
    if ( ! empty( $arr_hide_taxonomy ) && is_array( $arr_hide_taxonomy ) ) {
        foreach ( $arr_hide_taxonomy as $key => $taxo) {
            $taxonomy_list_wrap['taxonomy_hide'][$taxo] = 'hide';
        }
    }

    if ( ! empty( $arr_require_taxonomy ) && is_array( $arr_require_taxonomy ) ) {
        foreach ( $arr_require_taxonomy as $key => $taxo) {
            $taxonomy_list_wrap['taxonomy_require'][$taxo] = 'require';
        }
    }

    $list_taxonomy = ovabrw_create_type_taxonomies();
    $taxonomy_list_wrap['taxonomy_list_all'] = $list_taxonomy;

    if ( ! empty( $list_taxonomy ) ) {
        foreach( $list_taxonomy as $tax ) {
            $taxonomy_list_wrap['taxonomy_get'][$tax['slug']] = isset( $_GET[$tax['slug'].'_name'] ) ? $_GET[$tax['slug'].'_name'] : '';
        }
    }


    $name_product = isset( $_GET["ovabrw_name_product"] ) ? sanitize_text_field( $_GET["ovabrw_name_product"] ) : '';
    $name_attribute = isset( $_GET["ovabrw_attribute"] ) ? sanitize_text_field( $_GET["ovabrw_attribute"] ) : '';
    $value_attribute = isset( $_GET[$name_attribute] ) ? sanitize_text_field( $_GET[$name_attribute] ) : '';
    $tag_product = isset( $_GET["ovabrw_tag_product"] ) ? sanitize_text_field( $_GET["ovabrw_tag_product"] ) : '';
    $pickup_loc = isset( $_GET["ovabrw_pickup_loc"] ) ? sanitize_text_field( $_GET["ovabrw_pickup_loc"] ) : '';
    $pickoff_loc = isset( $_GET["ovabrw_pickoff_loc"] ) ? sanitize_text_field( $_GET["ovabrw_pickoff_loc"] ) : '';
    $pickup_date = isset( $_GET["ovabrw_pickup_date"] ) ? sanitize_text_field( $_GET["ovabrw_pickup_date"] ) : '';
    $pickoff_date = isset( $_GET["ovabrw_pickoff_date"] ) ? sanitize_text_field( $_GET["ovabrw_pickoff_date"] ) : '';
    $cat = isset( $_GET["cat"] ) ? sanitize_text_field( $_GET["cat"] ) : '';

    
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    $list_value_attribute = $tax_attribute = [];
    $html_select_attribute = $html_select_value_attribute = '';

    if ( $attribute_taxonomies ) :
        $html_select_attribute .= '<select name="ovabrw_attribute" class="'.$attribute_required.'"><option value="">'.esc_html__( 'Select Attribute', 'ova-brw' ).'</option>';
        
        foreach ($attribute_taxonomies as $tax) :
        if (taxonomy_exists(wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) :

            $class_acctive = ( $name_attribute == $tax->attribute_name ) ? 'active' : '';
            $checked_name_attr = ( $name_attribute == $tax->attribute_name ) ? 'selected' : '';
            $html_select_value_attribute .= '<div class="s_field '.$column.' ovabrw-value-attribute '.$class_acctive.'" id="'.$tax->attribute_name.'"><div class="content"><label>'.esc_html__( 'Value Attribute', 'ova-brw' ).'</label><select name="'.$tax->attribute_name.'" ><div class="wrap-error"></div>';
            $label_attribute = $tax->attribute_label;

            $tax_attribute[$tax->attribute_name] = $tax->attribute_label;

            $term_attributes = get_terms( wc_attribute_taxonomy_name($tax->attribute_name), 'orderby=name&hide_empty=0' );

            $html_select_attribute .= "<option ".$checked_name_attr." value='".$tax->attribute_name."'>".$tax->attribute_label."</option>";
            foreach ($term_attributes as $attr) {
                $checked_value_attr = ( $value_attribute == $attr->slug ) ? "selected" : "";
                $html_select_value_attribute .= '<option '.$checked_value_attr.' value="'.$attr->slug.'">'.$attr->name.'</option>';
            }
            $html_select_value_attribute .= '</select></div></div>';
        endif;
    endforeach;
    $html_select_attribute .= '</select>';
    endif;


    $remove_cats_id = $remove_cats_id == '' ? ovabrw_get_setting( get_option( 'ova_brw_search_cat_remove', '' ) ) : $remove_cats_id;

    $args = array(
        'column' => $column,
        'template'  => $template,
        'show_name_product' => $show_name_product,
        'show_attribute' => $show_attribute,
        'show_tag_product' => $show_tag_product,
        'show_pickup_loc' => $show_pickup_loc,
        'show_dropoff_loc' => $show_dropoff_loc,
        'show_pickup_date' => $show_pickup_date,
        'show_dropoff_date' => $show_dropoff_date,
        'show_cat' => $show_cat,
        'show_tax' => $show_tax,
        
        'name_product_required' => $name_product_required,
        'tag_product_required' => $tag_product_required,
        'pickup_loc_required' => $pickup_loc_required,
        'dropoff_loc_required' => $dropoff_loc_required,
        'pickup_date_required' => $pickup_date_required,
        'dropoff_date_required' => $dropoff_date_required,
        'category_required' => $category_required,
        'attribute_required' => $attribute_required,
        'remove_cats_id'    => $remove_cats_id,
        
        'dateformat' => $dateformat,
        'hour_default'  => $hour_default,
        'time_step' => $time_step,
        'order' => $order,
        'orderby' => $orderby,
        'class'   => $class,
        'timepicker' => $timepicker,
        'name_product' => $name_product,
        'name_attribute' => $name_attribute,
        'value_attribute' => $value_attribute,
        'tag_product'   => $tag_product,        
        'pickup_loc'    => $pickup_loc,
        'pickoff_loc'   => $pickoff_loc,
        'pickup_date'   => $pickup_date,
        'pickoff_date'  => $pickoff_date,
        'cat'   => $cat,
        'html_select_attribute' => $html_select_attribute,
        'html_select_value_attribute' => $html_select_value_attribute,
        'taxonomy_list_wrap' => $taxonomy_list_wrap,

    );

    ob_start();


    // Check show custom taxonomy depend category   
    // Custom taxonomies choosed in post
    $all_cus_tax = array();
    $exist_cus_tax = array();
    $cus_tax_hide_p_loaded = array();


    // Get All Custom taxonomy
    $ovabrw_custom_taxonomy = ovabrw_create_type_taxonomies();

    // All custom slug tax
    if( $ovabrw_custom_taxonomy ){
        
        foreach ($ovabrw_custom_taxonomy as $key => $value) {
            
            array_push($all_cus_tax, $value['slug']);

        }
    }
    

    $ova_brw_search_show_tax_depend_cat = ovabrw_get_setting( get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' ) );
    

    if( $ova_brw_search_show_tax_depend_cat == 'no' ){
        $cus_tax_hide_p_loaded = $all_cus_tax = array();
    }



    echo '<script type="text/javascript"> var ova_brw_search_show_tax_depend_cat = "'.$ova_brw_search_show_tax_depend_cat.'"; var cus_tax_hide_p_loaded = "'.implode(',', $cus_tax_hide_p_loaded).'"; var all_cus_tax = "'.implode(',', $all_cus_tax).'"; </script>';



    $template_file = ovabrw_locate_template( 'shortcode/'.$template.'.php' );
    if ( ! file_exists( $template_file ) ){
        esc_html_e( 'Please check surely you made template file for search form in plugin or theme', 'ova-brw' );
        
    }else{
        ovabrw_get_template( 'shortcode/'.$template.'.php', $args );
    }
    return ob_get_clean();
    
}

// Booking Form Shortcode
add_shortcode( 'ovabrw_st_booking_form', 'ovabrw_st_booking_form' );
function ovabrw_st_booking_form( $atts ) {
    $atts = extract( shortcode_atts(
    array(
        'id'  => '',
        'class'   => '',
    ), $atts) );

    $args = array(
        'id' => $id,
        'class'  => $class
    );

    
    ob_start();

    $template_file = ovabrw_locate_template( 'shortcode/st-booking-form.php' );
    if ( ! file_exists( $template_file ) ){
        esc_html_e( 'Please check surely you made template file for search form in plugin or theme', 'ova-brw' );
        
    }else{

        ovabrw_get_template( 'shortcode/st-booking-form.php', $args );

    }
    return ob_get_clean();
}

// Request Booking Form shortcode
add_shortcode( 'ovabrw_st_request_booking_form', 'ovabrw_st_request_booking_form' );
function ovabrw_st_request_booking_form( $atts ) {

    $atts = extract( shortcode_atts(
    array(
        'id'  => '',
        'class'   => '',
    ), $atts) );

    $args = array(
        'id' => $id,
        'class'  => $class
    );

    
    ob_start();

    $template_file = ovabrw_locate_template( 'shortcode/st-request-booking.php' );
    if ( ! file_exists( $template_file ) ){
        esc_html_e( 'Please check surely you made template file for search form in plugin or theme', 'ova-brw' );
        
    }else{

        ovabrw_get_template( 'shortcode/st-request-booking.php', $args );

    }
    return ob_get_clean();
}

// Product Calendar shortcode
add_shortcode( 'ovabrw_st_product_calendar', 'ovabrw_st_product_calendar' );
function ovabrw_st_product_calendar( $atts ) {

    $atts = extract( shortcode_atts(
    array(
        'id'  => '',
        'class'   => '',
    ), $atts) );

    $args = array(
        'id' => $id,
        'class'  => $class
    );

    
    ob_start();

    $template_file = ovabrw_locate_template( 'shortcode/st-calendar.php' );
    if ( ! file_exists( $template_file ) ){
        esc_html_e( 'Please check surely you made template file for search form in plugin or theme', 'ova-brw' );
        
    }else{

        ovabrw_get_template( 'shortcode/st-calendar.php', $args );

    }
    return ob_get_clean();
}

// Table Price shortcode
add_shortcode( 'ovabrw_st_table_price_product', 'ovabrw_st_table_price_product' );
function ovabrw_st_table_price_product( $atts ) {

    $atts = extract( shortcode_atts(
    array(
        'id'  => '',
        'class'   => '',
    ), $atts) );

    $args = array(
        'id' => $id,
        'class'  => $class
    );

    
    ob_start();

    $template_file = ovabrw_locate_template( 'shortcode/st-table-price.php' );
    if ( ! file_exists( $template_file ) ){
        esc_html_e( 'Please check surely you made template file for search form in plugin or theme', 'ova-brw' );
        
    }else{

        ovabrw_get_template( 'shortcode/st-table-price.php', $args );

    }
    return ob_get_clean();
}

// Feature Product shortcode
add_shortcode( 'ovabrw_st_feature_product', 'ovabrw_st_feature_product' );
function ovabrw_st_feature_product( $atts ) {

    $atts = extract( shortcode_atts(
    array(
        'id'  => '',
        'class'   => '',
    ), $atts) );

    $args = array(
        'id' => $id,
        'class'  => $class
    );

    
    ob_start();

    $template_file = ovabrw_locate_template( 'shortcode/st-features.php' );
    if ( ! file_exists( $template_file ) ){
        esc_html_e( 'Please check surely you made template file for search form in plugin or theme', 'ova-brw' );
        
    }else{

        ovabrw_get_template( 'shortcode/st-features.php', $args );

    }
    return ob_get_clean();
}