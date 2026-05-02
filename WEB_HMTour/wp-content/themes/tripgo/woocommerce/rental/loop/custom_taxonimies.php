<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();
$custom_taxonomy_choosed = '';
if( function_exists('ovabrw_get_custom_taxonomies') ){
	$custom_taxonomy_choosed = ovabrw_get_custom_taxonomies( $id );	
}

$i = 1;
$limit = apply_filters( 'tripgo_limit_custom_taxonomies_display', 3 );

if ( ! empty( $custom_taxonomy_choosed ) && is_array( $custom_taxonomy_choosed ) ): ?>
	<div class="tripgo-product-features">
		<ul class="feature-list">
			<?php foreach ( $custom_taxonomy_choosed as $taxonomy ):
				if ( $i > $limit ) {
					continue;
				}
			?>
				<li class="label"><?php echo esc_html( $taxonomy['name'] ); ?></li>
			<?php $i++; endforeach; ?>
		</ul>
	</div>
<?php endif; ?>