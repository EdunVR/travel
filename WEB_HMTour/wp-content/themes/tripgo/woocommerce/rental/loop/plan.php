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

$group_tour_plan = get_post_meta( $product_id,'ovabrw_group_tour_plan',true );

$show_tour_plan = isset( $args['show_tour_plan'] ) ? $args['show_tour_plan'] : 'yes';

?>


<?php if( ! empty( $group_tour_plan ) && $show_tour_plan == 'yes' ) {  ?>
    <div class="content-product-item tour-plan-wrapper" id="tour-plan">
        
        <h2 class="heading-tour-plan">
            <?php echo esc_html__('Tour Plan', 'tripgo'); ?>
        </h2>

        <div class="tour-plan-content">
            <?php
                foreach( $group_tour_plan as $key => $tour_plan ){
                    $tour_plan_day      = isset( $tour_plan['ovabrw_tour_plan_day'] )       ? $tour_plan['ovabrw_tour_plan_day']    : ''; 
                    $tour_plan_label    = isset( $tour_plan['ovabrw_tour_plan_label'] )     ? $tour_plan['ovabrw_tour_plan_label']  : ''; 
                    $tour_plan_desc     = isset( $tour_plan['ovabrw_tour_plan_desc'] )      ? $tour_plan['ovabrw_tour_plan_desc']   : ''; 
                ?>

                <div class="item-tour-plan  <?php if( $key == 0 ) { echo "active"; } ?>">

                    <div class="tour-plan-title">

                        <?php if( $tour_plan_day != '') : ?>
                            <span class="tour-plan-day">
                                <?php echo esc_html($tour_plan_day); ?>
                            </span>
                        <?php endif; ?>

                        <?php if( $tour_plan_label != '') : ?>
                            <span class="tour-plan-label">
                                <?php echo esc_html($tour_plan_label); ?>
                            </span>
                        <?php endif; ?>

                        <?php if( $key == 0 ) : ?>
                            <i aria-hidden="true" class="icomoon icomoon-chevron-up"></i>
                        <?php else : ?>
                            <i aria-hidden="true" class="icomoon icomoon-chevron-down"></i>
                        <?php endif; ?>

                    </div>

                    <?php if( $tour_plan_desc != '') : ?>
                        <div class="tour-plan-description">
                            <?php echo wpautop( wp_trim_excerpt( $tour_plan_desc ) ); ?>
                        </div>
                    <?php endif; ?>

                </div>

            <?php } ?>                  
        </div>
    </div>
<?php } ?>