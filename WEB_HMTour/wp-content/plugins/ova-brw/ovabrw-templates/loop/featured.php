<?php
/**
 * The template for displaying featured content within loop
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/loop/featured.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

// if the product type isn't ovabrw_car_rental
if( ! $product || $product->get_type() !== 'ovabrw_car_rental' ) return;

$pid = $product->get_id();
    
    
$icon = get_post_meta( $pid, 'ovabrw_features_icons', true );
$label = get_post_meta( $pid, 'ovabrw_features_label', true );
$desc = get_post_meta( $pid, 'ovabrw_features_desc', true );
$show_in_cat = get_post_meta( $pid, 'ovabrw_features_special', true );

$d = 0;

if( $desc ){ ?>
    <ul class="ovabrw-features">
        <?php
        foreach ($desc as $key => $value) {
            if ( $show_in_cat[$key] == 'yes' && $value ) { ?>
                
                <?php $class = ($d%2) ? 'eve' : 'odd'; ?>

                    <li class="feature-item <?php echo esc_attr( $class ); ?> ">
                        
                        <?php if( apply_filters( 'ovabrw_show_features_icon', true ) && $icon ){ ?>
                            <i class="<?php echo esc_attr( $icon[$key] ); ?>"> </i>
                        <?php } ?>
                        
                        <?php if( apply_filters( 'ovabrw_show_features_label', false ) ){ ?>    
                            <span class="label"><?php echo esc_html( $label[$key] ); ?></span>
                        <?php } ?>

                        <?php if( apply_filters( 'ovabrw_show_features_desc', true ) ){ ?>    
                            <span class="desc"><?php echo esc_html( $value ); ?></span>
                        <?php } ?>

                    </li>

                <?php $d++; ?>
                
            <?php }
        } ?>
    </ul>

<?php } 

