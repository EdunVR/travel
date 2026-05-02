<?php

/**
$column,

$name_product,
$show_name_product,
$name_product_required,


$pickup_loc,
$show_pickup_loc,
$pickup_loc_required,


$pickoff_loc,
$show_dropoff_loc,
$dropoff_loc_required,

$pickup_date,
$show_pickup_date,
$pickup_date_required,

$pickoff_date,
$show_dropoff_date,
$dropoff_date_required,

$cat,
$show_cat,
$category_required,
$remove_cats_id,

$name_attribute,
$value_attribute,
$show_attribute,
$attribute_required,
$html_select_attribute,
$html_select_value_attribute,

$taxonomy_list_wrap,
$show_tax,

$tag_product,
$show_tag_product,
$tag_product_required,


$dateformat,
$hour_default,
$time_step,

$class,
 */


extract(  $args );
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );
if ( empty( $first_day ) ) {
    $first_day = 0;
}

$html = '<div class="ovabrw_wd_search">';
    $html .= '<form action="'.home_url().'" class="'.$class.' '.' ovabrw_search form_ovabrw row" method="get" enctype="multipart/form-data" data-mesg_required="'.esc_html__( 'This field is required.', 'ova-brw' ).'">';
            $html .= '<div class="wrap_content">';

                $html .= ($show_name_product == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'.esc_html__( 'Name Product', 'ova-brw' ).'</label><div class="wrap-error"></div>
                    <input class="'.$name_product_required.'" placeholder="'.esc_html__('Name Product', 'ova-brw').'" name="ovabrw_name_product" value="'.esc_html( $name_product ).'" autocomplete="off" />
                    
                </div><div class="'.$class.'"></div></div>' : '';

                

                $html .= ($show_cat == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'.esc_html__( 'Category', 'ova-brw' ).'</label><div class="wrap-error"></div>
                    '.ovabrw_cat_rental( $cat, $category_required, $remove_cats_id ).'</div></div>' : '';


               

                $html .= ($show_pickup_loc == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'.esc_html__( 'Pick-up Location', 'ova-brw' ).'</label><div class="wrap-error"></div>
                    '.ovabrw_get_locations_html( $class = 'ovabrw_pickup_loc', $pickup_loc_required, $seleted = $pickup_loc ).'
                </div><div class="'.$class.'"></div></div>' : '';


                $html .= ($show_dropoff_loc == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'. esc_html__( 'Drop-off Location', 'ova-brw' ) .'</label><div class="wrap-error"></div>
                    '.ovabrw_get_locations_html( $class = 'ovabrw_pickoff_loc',$dropoff_loc_required, $seleted = $pickoff_loc ).'
                </div><div class="'.$class.'"></div></div>' : '';


                $html .= ($show_pickup_date == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'. esc_html__( 'Pick-up Date', 'ova-brw' ) .'</label><div class="wrap-error"></div>
                    <input type="text" name="ovabrw_pickup_date" value="'.$pickup_date.'" onkeydown="return false" class="'.$pickup_date_required.' ovabrw_datetimepicker ovabrw_start_date" placeholder="'.ovabrw_get_date_format().'"  autocomplete="off" data-hour_default="'.esc_attr( $hour_default ).'" data-time_step="'.esc_attr( $time_step ).'" data-dateformat="'.esc_attr( $dateformat ).'"  data-error=".ovabrw_pickup_date" onfocus="blur();" data-firstday="'.esc_attr( $first_day ).'" timepicker="'.esc_attr( $timepicker ).'" />
                </div></div>' : '';
                
                $html .= ($show_dropoff_date == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'. esc_html__( 'Drop-off Date', 'ova-brw' ) .'</label><div class="wrap-error"></div>
                    <input type="text" name="ovabrw_pickoff_date" value="'.$pickoff_date.'" onkeydown="return false" class="'.$dropoff_date_required.' ovabrw_datetimepicker ovabrw_end_date" placeholder="'.ovabrw_get_date_format().'"  autocomplete="off" data-hour_default="'.esc_attr( $hour_default ).'" data-time_step="'.esc_attr( $time_step ).'" data-dateformat="'.esc_attr( $dateformat ).'"  data-error=".ovabrw_pickoff_date" onfocus="blur();" data-firstday="'.esc_attr( $first_day ).'" timepicker="'.esc_attr( $timepicker ).'" />
                </div></div>' : '';
                   

                $html .= ($show_attribute == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'.esc_html__( 'Name Attribute', 'ova-brw' ).'</label><div class="wrap-error"></div>'.$html_select_attribute.'</div><div class="'.$class.'"></div></div>' : '';

                $html .= $html_select_value_attribute;

                $html .= ($show_tag_product == 'yes') ? '<div class="s_field '.$column.'"><div class="content">
                    <label>'.esc_html__( 'Tag Product', 'ova-brw' ).'</label><div class="wrap-error"></div>
                    <input class="'.$tag_product_required.'" placeholder="'.esc_html__('Tag Product', 'ova-brw').'" name="ovabrw_tag_product" value="'.esc_html( $tag_product ).'" autocomplete="off" />
                </div><div class="'.$class.'"></div></div>' : '';

                $list_taxonomy = array_key_exists( 'taxonomy_list_all' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_list_all'] : [];
                $arr_require_taxonomy_key = array_key_exists( 'taxonomy_require' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_require'] : [];
                $arr_hide_taxonomy_key = array_key_exists( 'taxonomy_hide' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_hide'] : [];
                $list_get_taxonomy = array_key_exists( 'taxonomy_get' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_get'] : [];

                if ( ! empty( $list_taxonomy ) && $show_tax == 'yes' ) {
                    foreach( $list_taxonomy as $taxonomy ) {
                        
                        $required = '';
                        if ( is_array( $arr_require_taxonomy_key ) && array_key_exists( $taxonomy['slug'], $arr_require_taxonomy_key ) && $arr_require_taxonomy_key[$taxonomy['slug']] == 'require' ) {
                            $required = 'required';
                        } else {
                            $required = '';
                        }

                        if ( is_array( $arr_hide_taxonomy_key ) && array_key_exists( $taxonomy['slug'], $arr_hide_taxonomy_key ) && $arr_hide_taxonomy_key[$taxonomy['slug']] == 'hide' ) {
                            $html .= '';
                        } else {
                            $html .= '<div class="s_field '.$column.' s_field_cus_tax '.$taxonomy['slug'].'"><div class="content">
                                <label>' . $taxonomy['name'] . '</label><div class="wrap-error"></div>
                                '.ovabrw_taxonomy_dropdown( $list_get_taxonomy[$taxonomy['slug']], $required , '',$taxonomy['slug'], $taxonomy['name'] ).'</div></div>';
                        }
                    }
                }


            $html .= '<input type="hidden" name="order" value="'.esc_attr( $order ).'">';
            $html .= '<input type="hidden" name="orderby" value="'.esc_attr( $orderby ).'">';


            $html .= '</div>';

            $html .= '<div class="s_submit">
                        <button class="ovabrw_btn_submit" type="submit">'.esc_html__( 'Search', 'ova-brw' ).'</button>
                    </div>';

            $html .= '<input type="hidden" name="ovabrw_search_product" value="ovabrw_search_product" />
                    <input type="hidden" name="ovabrw_search" value="search_item" />
                    <input type="hidden" name="post_type" value="product" />';  
            if( defined( 'ICL_LANGUAGE_CODE' ) ){
                $html .= '<input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />';
            }

    $html .= '</form>';
$html .= '</div>';

echo $html;