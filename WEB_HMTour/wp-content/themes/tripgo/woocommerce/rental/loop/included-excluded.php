<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$all_ids = ovabrw_get_all_id_product();

if( isset( $args['id'] ) && $args['id'] != '' ) {

    $product_id  = ( in_array( $args['id'], $all_ids ) == true && in_array( get_the_id(), $all_ids ) == false ) ? $args['id'] : get_the_id();

} elseif( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id  = $all_ids[0];

} else {
    $product_id  = get_the_id();
}


$product = wc_get_product( $product_id );

$show_inc_exc = isset( $args['show_inc_exc'] ) ? $args['show_inc_exc'] : 'yes';

$group_tour_included = get_post_meta( $product_id,'ovabrw_group_tour_included',true );
$group_tour_excluded = get_post_meta( $product_id,'ovabrw_group_tour_excluded',true );

?>

<!--  Tour Included/Excluded -->
<?php if( (! empty( $group_tour_included ) || ! empty( $group_tour_excluded ) ) && $show_inc_exc == 'yes' ) { ?>
    <div class="content-product-item tour-included-excluded-wrapper" id="tour-included-excluded">
        <h2 class="heading-tour-included-excluded">
            <?php echo esc_html__('Included/Excluded', 'tripgo'); ?>
        </h2>
        <div class="tour-included-excluded-content">
            <?php if( ! empty( $group_tour_included ) ) {  ?>
                <ul class="tour-included">
                     <?php
                        foreach( $group_tour_included as $tour_included ){

                            $tour_included_text   = isset( $tour_included['ovabrw_tour_included_text'] ) ? $tour_included['ovabrw_tour_included_text'] : ''; 

                        ?>

                        <li class="item-tour-included">
                            <?php echo esc_html($tour_included_text); ?>
                        </li>

                    <?php } ?>
                </ul>  
            <?php } ?> 
            <?php if( ! empty( $group_tour_excluded ) ) {  ?>
                <ul class="tour-excluded">
                     <?php
                        foreach( $group_tour_excluded as $tour_excluded ){

                            $tour_excluded_text   = isset( $tour_excluded['ovabrw_tour_excluded_text'] ) ? $tour_excluded['ovabrw_tour_excluded_text'] : ''; 

                        ?>

                        <li class="item-tour-excluded">
                            <?php echo esc_html($tour_excluded_text); ?>
                        </li>

                    <?php } ?>
                </ul>  
            <?php } ?>                
        </div>
    </div>
<?php } ?>