<?php if ( ! defined( 'ABSPATH' ) ) exit();

$destinations = isset( $args['destinations'] ) ? $args['destinations'] : '';

if ( isset( $args['auto_sorted'] ) && ! $args['auto_sorted'] ) {
	$destinations = $args['destination_ids'] ? explode( '|', $args['destination_ids'] ) : '';
}

$posts_per_page = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : 9;
$order 			= isset( $args['order'] ) ? $args['order'] : 'DESC';
$orderby 		= isset( $args['orderby'] ) ? $args['orderby'] : 'date';
$layout 		= isset( $args['layout'] ) ? $args['layout'] : 'grid';
$column 		= isset( $args['column'] ) ? $args['column'] : 'column4';
$thumbnail_type = isset( $args['thumbnail_type'] ) ? $args['thumbnail_type'] : 'image';
$pagination 	= isset( $args['pagination'] ) ? $args['pagination'] : 'yes';

?>

<div class="ovabrw-destination-ajax">
	<ul class="ovabrw-destination-list">
		<?php if ( ! empty( $destinations ) && is_array( $destinations ) ): ?>
			<?php foreach ( $destinations as $k => $destination_id ):
				$title = get_the_title( $destination_id );
			?>
				<li class="destination-item<?php echo $k === 0 ? esc_attr(' active') : ''; ?>" data-destination-id="<?php echo esc_attr( $destination_id ); ?>">
					<?php echo esc_html( $title ); ?>
				</li>
			<?php endforeach; ?>
		<?php else: ?>
			<li class="destination-item active" data-destination-id="0"><?php esc_html_e( 'All', 'ova-brw' ); ?></li>
		<?php endif; ?>
	</ul>
	<div class="ovabrw-destination-products"></div>
	<!-- Load more -->
	<div class="wrap-load-more">
		<svg class="loader" width="50" height="50">
			<circle cx="25" cy="25" r="10" />
			<circle cx="25" cy="25" r="20" />
		</svg>
	</div>
	<input
		type="hidden"
		name="destination-ajax-input"
		data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
		data-paged="1"
		data-order="<?php echo esc_attr( $order ); ?>"
		data-orderby="<?php echo esc_attr( $orderby ); ?>"
		data-layout="<?php echo esc_attr( $layout ); ?>"
		data-column="<?php echo esc_attr( $column ); ?>"
		data-thumbnail-type="<?php echo esc_attr( $thumbnail_type ); ?>"
		data-pagination="<?php echo esc_attr( $pagination ); ?>"
	/>
</div>