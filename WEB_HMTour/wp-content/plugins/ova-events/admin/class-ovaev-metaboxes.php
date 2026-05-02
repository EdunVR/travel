<?php if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'OVAEV_metaboxes' ) ) {
	class OVAEV_metaboxes {

		public function __construct() {
			$this->require_metabox();

			add_action( 'add_meta_boxes', array( $this , 'OVAEV_add_metabox' ) );
			add_action( 'save_post', array( $this , 'OVAEV_save_metabox' ) );

			// Save
			add_action( 'ovaev_update_meta_event', array( 'OVAEV_metaboxes_render_event' ,'save' ), 10, 2 );

			// Category
			add_action( 'event_category_add_form_fields', array( $this, 'OVAEV_taxonomy_add_new_meta_field' ) );
			add_action( 'event_category_edit_form_fields', array( $this, 'OVAEV_taxonomy_edit_meta_field' ) );
			add_action( 'edited_event_category', array( $this, 'OVAEV_taxonomy_save_meta_field' ) );
			add_action( 'create_event_category', array( $this, 'OVAEV_taxonomy_save_meta_field' ) );
		}

		public function require_metabox() {
			require_once( OVAEV_PLUGIN_PATH.'admin/meta-boxes/ovaev-metaboxes-event.php' );
		}

		public function OVAEV_add_metabox() {
			add_meta_box( 'ovaev-metabox-settings-event',
				'Events',
				array('OVAEV_metaboxes_render_event', 'render'),
				'event',
				'normal',
				'high'
			);
		}

		public function OVAEV_save_metabox( $post_id ) {
			// Bail if we're doing an auto save
			if ( empty( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) return;

			// if our nonce isn't there, or we can't verify it, bail
			if ( ! isset( $_POST['ovaev_nonce'] ) || !wp_verify_nonce( $_POST['ovaev_nonce'], 'ovaev_nonce' ) ) return;

			do_action( 'ovaev_update_meta_event', $post_id, $_POST );
		}

		public function OVAEV_taxonomy_add_new_meta_field( $array ) {
			?>
			<div class="form-field ovaev-icon-class-wrap">
				<label for="ovaev-icon-class"><?php esc_html_e( 'Icon Class', 'ovaev' ); ?></label>
				<input name="ovaev_icon_class" id="ovaev-icon-class" type="text" value="" size="40" aria-describedby="icon-class-description">
				<p class="description" id="icon-class-description"><?php esc_html_e( 'Applies to [ovaev_event_filter] shortcode', 'ovaev' ); ?></p>
			</div>
			<?php
		}

		public function OVAEV_taxonomy_edit_meta_field( $term ) {
			$term_id = $term->term_id;

			$icon_class = get_term_meta( $term_id, 'ovaev_icon_class', true );

			?>
			<tr class="form-field ovaev-icon-class-wrap">
				<th scope="row"><label for="ovaev-icon-class"><?php esc_html_e( 'Icon Class', 'ovaev' ); ?></label></th>
				<td>
					<input name="ovaev_icon_class" id="ovaev-icon-class" type="text" value="<?php echo esc_attr( $icon_class ); ?>" size="40" aria-describedby="icon-class-description">
					<p class="description" id="icon-class-description"><?php esc_html_e( 'Applies to [ovaev_event_filter] shortcode', 'ovaev' ); ?></p>
				</td>
			</tr>
			<?php
		}

		public function OVAEV_taxonomy_save_meta_field( $term_id ) {
			$icon_class = isset( $_REQUEST['ovaev_icon_class'] ) ? $_REQUEST['ovaev_icon_class'] : '';

    		update_term_meta( $term_id, 'ovaev_icon_class', $icon_class );
		}
	}

	new OVAEV_metaboxes();
}
?>