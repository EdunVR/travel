<?php if ( ! defined( 'ABSPATH' ) ) exit();

$categories = isset( $args['categories'] ) ? $args['categories'] : '';

if ( isset( $args['auto_sorted'] ) && ! $args['auto_sorted'] ) {
	$categories = $args['category_ids'] ? explode( '|', $args['category_ids'] ) : '';
}

$posts_per_page = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : 9;
$order 			= isset( $args['order'] ) ? $args['order'] : 'DESC';
$orderby 		= isset( $args['orderby'] ) ? $args['orderby'] : 'date';
$layout 		= isset( $args['layout'] ) ? $args['layout'] : 'grid';
$grid_template 	= isset( $args['grid_template'] ) ? $args['grid_template'] : 'template_1';
$column 		= isset( $args['column'] ) ? $args['column'] : 'column4';
$thumbnail_type = isset( $args['thumbnail_type'] ) ? $args['thumbnail_type'] : 'image';
$pagination 	= isset( $args['pagination'] ) ? $args['pagination'] : 'yes';

?>

<div class="ovabrw-category-ajax">
	<ul class="ovabrw-category-list">
		<?php if ( ! empty( $categories ) && is_array( $categories ) ): ?>
			<?php foreach ( $categories as $k => $term_id ):
				$obj_term 	= get_term( $term_id );
				$term_name 	= is_object( $obj_term ) ? $obj_term->name : '';
			?>
				<li class="category-item<?php echo $k === 0 ? esc_attr(' active') : ''; ?>" data-term-id="<?php echo esc_attr( $term_id ); ?>">
					<?php echo esc_html( $term_name ); ?>
				</li>
			<?php endforeach; ?>
		<?php else: ?>
			<li class="category-item active" data-term-id="0"><?php esc_html_e( 'All', 'ova-brw' ); ?></li>
		<?php endif; ?>
	</ul>
	<div class="ovabrw-category-products"></div>
	<!-- Load more -->
	<div class="wrap-load-more">
		<svg class="loader" width="50" height="50">
			<circle cx="25" cy="25" r="10" />
			<circle cx="25" cy="25" r="20" />
		</svg>
	</div>
	<input
		type="hidden"
		name="category-ajax-input"
		data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
		data-paged="1"
		data-order="<?php echo esc_attr( $order ); ?>"
		data-orderby="<?php echo esc_attr( $orderby ); ?>"
		data-layout="<?php echo esc_attr( $layout ); ?>"
		data-grid_template="<?php echo esc_attr( $grid_template ); ?>"
		data-column="<?php echo esc_attr( $column ); ?>"
		data-thumbnail-type="<?php echo esc_attr( $thumbnail_type ); ?>"
		data-pagination="<?php echo esc_attr( $pagination ); ?>"
	/>
</div>