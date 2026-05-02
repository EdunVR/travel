<?php
/**
 * The template for displaying attributes content within loop
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/loop/attributes.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

// if the product type isn't ovabrw_car_rental
if( ! $product || $product->get_type() !== 'ovabrw_car_rental' ) return;

$html = '';

$attributes = $product->get_attributes();
foreach ( $attributes as $attribute ) :
    
    $values = array();

    if ( $attribute->is_taxonomy() ) {
        $attribute_taxonomy = $attribute->get_taxonomy_object();
        $attribute_values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

        foreach ( $attribute_values as $attribute_value ) {

            $value_name = esc_html( $attribute_value->name );

            if ( $attribute_taxonomy->attribute_public ) {
                $values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
            } else {
                $values[] = $value_name;
            }
        }
    } else {

        $values = $attribute->get_options();

        foreach ( $values as &$value ) {
            $value = make_clickable( esc_html( $value ) );
        }

    }

    if( !empty( $values ) ){ ?>

        <div class="ovabrw_product_attr">

        	<span class="label">
        		<?php echo wc_attribute_label( $attribute->get_name() ); ?> : 
        	</span>

        	<span class="value">
        		<?php echo apply_filters( 'woocommerce_attribute',  wptexturize( implode( ', ', $values ) ) , $attribute, $values ); ?>
        	<span>
        		
        </div>

    <?php }
    
endforeach;

