<?php
/**
 * The template for displaying custom taxonomy content within loop
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/loop/taxonomy.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

$pid = $product->get_id();

$all_cus_choosed = get_all_cus_tax_dis_listing( $pid );


if( $all_cus_choosed ){
?>
	<ul class="product_listing_custom_tax">
		<?php foreach ($all_cus_choosed as $key => $value) { ?>
			<li class="<?php echo 'tax_'.$value['slug']; ?> ">
				<?php echo $value['name']; ?>
			</li>
		<?php } ?>
	</ul>
<?php } ?>
