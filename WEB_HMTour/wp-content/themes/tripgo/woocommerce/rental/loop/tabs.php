<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$all_ids = ovabrw_get_all_id_product();

if ( isset( $args['product_id'] ) && $args['product_id'] != '' ) {

    $product_id = ( in_array( $args['product_id'], $all_ids ) == true && in_array( get_the_id(), $all_ids ) == false ) ? $args['product_id'] : get_the_id();

} elseif ( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id = $all_ids[0];

} else {
    $product_id = get_the_id();
}


$tour_description    = wpautop( get_post($product_id)->post_content );

$group_tour_included = get_post_meta( $product_id,'ovabrw_group_tour_included',true );
$group_tour_excluded = get_post_meta( $product_id,'ovabrw_group_tour_excluded',true );

$group_tour_plan     = get_post_meta( $product_id,'ovabrw_group_tour_plan',true );
$address             = get_post_meta( $product_id, 'ovabrw_address', true );

// Description
$description_label  = isset( $args['description_label'] ) ? $args['description_label'] : esc_html__( 'Description', 'tripgo' );
$description_show   = isset( $args['show_description'] ) ? $args['show_description'] : 'yes';

// Included/Excluded
$incl_excl_label    = isset( $args['incl_excl_label'] ) ? $args['incl_excl_label'] : esc_html__( 'Included/Excluded', 'tripgo' );
$incl_excl_show     = isset( $args['show_incl_excl'] ) ? $args['show_incl_excl'] : 'yes';

// Tour Plan
$tour_plan_label    = isset( $args['tour_plan_label'] ) ? $args['tour_plan_label'] : esc_html__( 'Tour Plan', 'tripgo' );
$tour_plan_show     = isset( $args['show_tour_plan'] ) ? $args['show_tour_plan'] : 'yes';

// Tour Plan
$tour_map_label    = isset( $args['tour_map_label'] ) ? $args['tour_map_label'] : esc_html__( 'Tour Map', 'tripgo' );
$tour_map_show     = isset( $args['show_tour_map'] ) ? $args['show_tour_map'] : 'yes';

// Reviews
$reviews_label  = isset( $args['reviews_label'] ) ? $args['reviews_label'] : esc_html__( 'Reviews', 'tripgo' );
$reviews_show   = isset( $args['show_reviews'] ) ? $args['show_reviews'] : 'yes';

?>

<div class="ova-tabs-product">
    <div class="tabs">
        <?php if ( ! empty( $tour_description ) && $description_show === 'yes' ) { ?>
            <div class="item" data-id="#tour-description">
                <?php echo esc_html( $description_label ); ?>
            </div>
        <?php } ?>
        <?php if ( ( ! empty( $group_tour_included ) || ! empty( $group_tour_excluded  ) ) && $incl_excl_show === 'yes' ) {  ?>
            <div class="item" data-id="#tour-included-excluded">
                 <?php echo esc_html( $incl_excl_label ); ?>
            </div>
        <?php } ?>
        <?php if ( ! empty( $group_tour_plan ) && $tour_plan_show === 'yes' ) {  ?>
            <div class="item" data-id="#tour-plan">
                <?php echo esc_html( $tour_plan_label ); ?>
            </div>
        <?php } ?>
        <?php if ( ! empty( $address ) && $tour_map_show === 'yes' ) {  ?>
            <div class="item" data-id="#ova-tour-map">
                <?php echo esc_html( $tour_map_label ); ?>
            </div>
        <?php } ?>
        <?php if ( $reviews_show === 'yes' ) {  ?>
            <div class="item" data-id="#ova-tour-review">
                <?php echo esc_html( $reviews_label ); ?>
            </div>
        <?php } ?>
    </div>
    <?php
        /* Tab Content */
        wc_get_template( 'rental/loop/description.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/included-excluded.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/plan.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/map.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/review.php', array( 'id' => $product_id ) );
    ?>
</div>