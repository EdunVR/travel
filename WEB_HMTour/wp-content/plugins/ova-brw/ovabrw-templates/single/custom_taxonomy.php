<?php 
/**
 * The template for displaying contact form 7 within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/custom_taxonomy.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if ( isset( $args['id'] ) && $args['id'] ) {
	$pid = $args['id'];
} else {
	$pid = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $pid );

if ( ! $product || $product->get_type() !== 'ovabrw_car_rental' ) return;

$all_cus_tax = ovabrw_get_taxonomy_choosed_product( $pid );

?>

<?php if ( $all_cus_tax ) { ?>
	<ul class="ovabrw_cus_taxonomy">
		<?php foreach( $all_cus_tax as $key => $value ) { ?>
			<li class="<?php echo $key; ?>">
				<label>
					<?php echo esc_html( $value['name'] ); ?>
				</label>
				<span>
					<?php echo implode(', ', $value['value']); ?>
				</span>
			</li>
		<?php } ?>			
	</ul>
<?php } ?>