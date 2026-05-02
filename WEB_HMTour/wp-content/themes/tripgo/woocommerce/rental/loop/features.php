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

$show_features  = isset( $args['show_features'] ) ? $args['show_features'] : 'yes';

$features_icons = get_post_meta( $product_id, 'ovabrw_features_icons', true );

?>

<?php if ( $features_icons && is_array( $features_icons ) && $show_features == 'yes' ):
    $features_title = get_post_meta( $product_id, 'ovabrw_features_label', true );
    $features_desc  = get_post_meta( $product_id, 'ovabrw_features_desc', true );
?>
    <div class="ova-features-product">
        <?php foreach( $features_icons as $k => $icon ): ?>
            <div class="feature">
                <i aria-hidden="true" class="<?php echo esc_attr( $icon ); ?>"></i>
                <div class="title-desc">
                    <?php if ( isset( $features_title[$k] ) && $features_title[$k] ): ?>
                        <h6 class="title">
                            <?php echo esc_html( $features_title[$k] ); ?>
                        </h6>
                    <?php endif; ?>
                    <?php if ( isset( $features_desc[$k] ) && $features_desc[$k] ): ?>
                        <p class="desc">
                            <?php echo esc_html( $features_desc[$k] ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
