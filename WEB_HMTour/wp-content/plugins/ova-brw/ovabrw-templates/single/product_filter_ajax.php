<?php if ( ! defined( 'ABSPATH' ) ) exit;

    if ( isset( $args['id'] ) && $args['id'] ) {
        $id = $args['id'];
    } else {
        $id = get_the_id();
    }

    // products
    $show_on_sale     =  isset( $args['show_on_sale'] )    ? $args['show_on_sale']      : 'no' ;

    $show_featured    =  isset( $args['show_featured'] )   ? $args['show_featured']     : 'yes' ;
    $show_wishlist    =  isset( $args['show_wishlist'] )   ? $args['show_wishlist']     : 'yes' ;
    $show_duration    =  isset( $args['show_duration'] )   ? $args['show_duration']     : 'yes' ;
    $show_title       =  isset( $args['show_title'] )      ? $args['show_title']        : 'yes' ;
    $show_location    =  isset( $args['show_location'] )   ? $args['show_location']     : 'yes' ;
    $show_rating      =  isset( $args['show_rating'] )     ? $args['show_rating']       : 'yes' ;
    $show_price       =  isset( $args['show_price'] )      ? $args['show_price']       : 'yes' ;
    $show_button      =  isset( $args['show_button'] )     ? $args['show_button']       : 'yes' ;

    $posts_per_page   =  isset( $args['posts_per_page'] )  ? $args['posts_per_page']    : 4 ;
    $orderby          =  isset( $args['product_orderby'] ) ? $args['product_orderby']   : 'ID' ;
    $order            =  isset( $args['product_order'] )   ? $args['product_order']     : 'DESC' ;
    $filter_title	  =  isset( $args['filter_title'] )    ? $args['filter_title']      : '' ;
    
    // categories
    $args_categories  =  isset( $args['categories'] ) ? $args['categories'] : array('all') ;
    if ( $args_categories === 'all') {
        $args_categories = array('all');
    }

    $pro_args = array(
       'taxonomy' => 'product_cat',
       'orderby'  => $args['orderby'],
       'order'    => $args['order']
    );

    $catAll      = isset( $args['catAll'] )  ? $args['catAll']  : esc_html__('All','ova-brw');
    $categories  = get_categories($pro_args);

    // Additional Options Slider
    $data_options['items']              = isset( $args['item_number'] )         ? $args['item_number']      : 1 ;
    $data_options['slideBy']            = isset( $args['slides_to_scroll'] )    ? $args['slides_to_scroll'] : 1 ;
    $data_options['margin']             = isset( $args['margin_items'] )        ? $args['margin_items']     : 0 ;
    $data_options['autoplayTimeout']    = isset( $args['autoplay_speed'] )      ? $args['autoplay_speed']   : 3000;
    $data_options['smartSpeed']         = isset( $args['smartspeed'] )          ? $args['smartspeed']       : 500;
    $data_options['autoplayHoverPause'] = $args['pause_on_hover']   === 'yes'   ? true : false;
    $data_options['loop']               = $args['infinite']         === 'yes'   ? true : false;
    $data_options['autoplay']           = $args['autoplay']         === 'yes'   ? true : false;
    $data_options['nav']                = $args['nav_control']      === 'yes'   ? true : false;
    $data_options['dots']               = $args['dots_control']     === 'yes'   ? true : false;
    $data_options['rtl']                = is_rtl() ? true : false;

    $args_show = array(
        'show_featured' => $show_featured,
        'show_wishlist' => $show_wishlist,
        'show_duration' => $show_duration,
        'show_title'    => $show_title,
        'show_location' => $show_location,
        'show_rating'   => $show_rating,
        'show_price'    => $show_price,
        'show_button'   => $show_button,
    );
?>

<div class="ova-product-filter-ajax"
    data-show_on_sale="<?php echo esc_attr( $show_on_sale ); ?>"
    data-args_show="<?php echo esc_attr( json_encode($args_show) ); ?>"
    data-posts_per_page="<?php echo esc_attr( $posts_per_page ); ?>"
    data-orderby="<?php echo esc_attr( $orderby ); ?>"
    data-order="<?php echo esc_attr( $order ); ?>"
>

    <ul class="product-filter-category">

        <?php if(!empty($filter_title)) { ?>
        	<li class="filter-title">
                <?php echo esc_html($filter_title);?>
            </li>
        <?php } ?>
        
        <li class="product-filter-button active-category" data-slug="all">
            <span class="category"><?php echo esc_html( $catAll ); ?></span>
            <i aria-hidden="true" class="icomoon icomoon-angle-right"></i>
        </li>
        
        <?php  if( !empty( $categories ) && is_array( $categories ) ) : ?>

            <?php foreach ( $categories as $category ) : 
                $name  = $category->name;
                $slug  = $category->slug;
            ?>

                <?php if( in_array( $slug, $args_categories ) ) : ?>

                    <li class="product-filter-button" data-slug="<?php echo esc_attr($slug);?>">
                        <span class="category"><?php echo esc_html( $name ); ?></span>
                        <i aria-hidden="true" class="icomoon icomoon-angle-right"></i>
                    </li>

                <?php endif; ?>

            <?php endforeach; ?>

        <?php endif; ?>

    </ul>


   
    <div class="content-item slide-product owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)); ?>">
        
    </div>  

</div>