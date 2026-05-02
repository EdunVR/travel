<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$form 		= isset( $args['form'] ) && $args['form'] ? $args['form'] : '';

$product = wc_get_product( $product_id );

if ( ! $product || !$product->is_type('ovabrw_car_rental') ) return;

$list_extra_fields 	= ovabrw_get_list_field_checkout( $product_id );
$special_fields 	= [ 'textarea', 'select', 'radio', 'checkbox', 'file' ];

if ( ! empty( $list_extra_fields ) && is_array( $list_extra_fields ) ):
	foreach( $list_extra_fields as $key => $field ):
		if ( array_key_exists( 'enabled', $field ) && $field['enabled'] == 'on' ):
			$class_field = $class_required = '';

			if ( ovabrw_check_array( $field, 'required' ) ) {
				$class_required = 'required';
			}

			if ( $field['class'] && $class_required ) {
				$class_field = $field['class'] . ' ' . $class_required;
			} else if ( !$field['class'] && $class_required ) {
				$class_field = $class_required;
			} else {
				$class_field = $field['class'];
			}

	?>
		<div class="rental_item">
			<label>
				<?php echo esc_html( $field['label'] ); ?>
			</label>
			<?php if ( ! in_array( $field['type'], $special_fields ) ): ?>
				<input 
					type="<?php echo esc_attr( $field['type'] ); ?>" 
					name="<?php echo esc_attr( $key ); ?>"  
					class="<?php echo esc_attr( $class_field ); ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
					value="<?php echo esc_attr( $field['default'] ); ?>" 
					data-error="<?php echo sprintf( esc_html__( '%s is required.', 'tripgo' ), esc_attr( $field['label'] ) ); ?>" />
			<?php endif; ?>

			<?php if ( 'textarea' === $field['type'] ): ?>
				<textarea 
					name="<?php echo esc_attr( $key ); ?>" 
					class="<?php echo esc_attr( $class_field ); ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
					value="<?php echo esc_attr( $field['default'] ); ?>" 
					rows="5" 
					data-error="<?php echo sprintf( esc_html__( '%s is required.', 'tripgo' ), esc_attr( $field['label'] ) ); ?>"></textarea>
			<?php endif; ?>

			<?php if ( 'select' === $field['type'] ): 
				$ova_options_key = $ova_options_text = [];

				if ( array_key_exists( 'ova_options_key', $field ) ) {
					$ova_options_key = $field['ova_options_key'];
				}

				if ( array_key_exists( 'ova_options_text', $field ) ) {
					$ova_options_text = $field['ova_options_text'];
				}
			?>
				<select name="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $class_field ); ?>" data-error="<?php echo sprintf( esc_html__( '%s field is required.', 'tripgo' ), esc_attr( $field['label'] ) ); ?>">
				<?php 
					if ( !empty( $ova_options_text ) && is_array( $ova_options_text ) ): ?>
						<option value="">
							<?php echo sprintf( esc_html__( 'Select %s', 'tripgo' ), esc_attr( $field['label'] ) ); ?>
						</option>
						<?php foreach( $ova_options_text as $key => $text ): 
							$value = '';

							if ( ovabrw_check_array( $ova_options_key, $key ) ) {
								$value = $ova_options_key[$key];
							}
				?>
							<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $field['default'], $ova_options_key[$key] ); ?>>
								<?php echo esc_html( $text ); ?>
							</option>
				<?php endforeach; endif; ?>
				</select>
			<?php endif; ?>

			<?php if ( $field['type'] === 'radio' ):
				$values 	= isset( $field['ova_radio_values'] ) ? $field['ova_radio_values'] : '';
				$default 	= isset( $field['default'] ) ? $field['default'] : '';

				if ( ! empty( $values ) && is_array( $values ) ):
					foreach ( $values as $k => $value ):
						$checked = '';

						if ( ! $default && $field['required'] === 'on' ) $default = $values[0];

						if ( $default === $value ) $checked = 'checked';
			?>			
					<div class="ovabrw-ckf-radio" data-error="<?php printf( esc_html__( '%s field is required.', 'tripgo' ), $field['label'] ); ?>">
						<input 
							type="radio" 
							id="<?php echo 'ovabrw-radio'.esc_attr( $k ).esc_attr( $form ); ?>" 
							name="<?php echo esc_attr( $key ); ?>" 
							value="<?php echo esc_attr( $value ); ?>" <?php echo esc_html( $checked ); ?>
						/>
						<span class="checkmark"></span>
						<label for="<?php echo 'ovabrw-radio'.esc_attr( $k ).esc_attr( $form ); ?>"><?php echo esc_html( $value ); ?></label>
					</div>
			<?php endforeach; endif; endif; ?>

			<?php if ( $field['type'] === 'checkbox' ):
				$default 		= isset( $field['default'] ) ? $field['default'] : '';
				$checkbox_key 	= isset( $field['ova_checkbox_key'] ) ? $field['ova_checkbox_key'] : [];
				$checkbox_text 	= isset( $field['ova_checkbox_text'] ) ? $field['ova_checkbox_text'] : [];
				$checkbox_price = isset( $field['ova_checkbox_price'] ) ? $field['ova_checkbox_price'] : [];

				if ( ! empty( $checkbox_key ) && is_array( $checkbox_key ) ):
					foreach ( $checkbox_key as $k => $val ):
						$checked = '';

						if ( ! $default && $field['required'] === 'on' ) $default = $val;

						if ( $default === $val ) $checked = 'checked';
			?>
				<div class="ovabrw-checkbox" data-error="<?php printf( esc_html__( '%s field is required.', 'tripgo' ), $field['label'] ); ?>">
					<input 
						type="checkbox" 
						id="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ).esc_attr( $form ); ?>" 
						class="<?php echo esc_attr( $class_required ); ?>" 
						name="<?php echo esc_attr( $key ).'['.$val.']'; ?>" 
						value="<?php echo esc_attr( $val ); ?>" <?php echo esc_html( $checked ); ?>
					/>
					<span class="checkmark"></span>
					<label for="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ).esc_attr( $form ); ?>">
						<?php echo isset( $checkbox_text[$k] ) ? esc_html( $checkbox_text[$k] ) : ''; ?>
					</label>
				</div>
			<?php endforeach; endif; endif; ?>

			<?php if ( $field['type'] === 'file' ):
				$mimes = apply_filters( 'ovabrw_ft_file_mimes', [
                    'jpg'   => 'image/jpeg',
                    'jpeg'  => 'image/pjpeg',
                    'png'   => 'image/png',
                    'pdf'   => 'application/pdf',
                    'doc'   => 'application/msword',
                ]);
			?>
				<div class="ovabrw-file">
					<label for="<?php echo 'ovabrw-file-'.esc_attr( $key ).esc_attr( $form ); ?>">
						<span class="ovabrw-file-chosen"><?php esc_html_e( 'Choose File', 'tripgo' ); ?></span>
						<span class="ovabrw-file-name"></span>
					</label>
					<input 
						type="<?php echo esc_attr( $field['type'] ); ?>" 
						id="<?php echo 'ovabrw-file-'.esc_attr( $key ).esc_attr( $form ); ?>" 
						name="<?php echo esc_attr( $key ); ?>" 
						class="<?php echo esc_attr( $field['class'] ) . $class_required; ?>" 
						data-max-file-size="<?php echo esc_attr( $field['max_file_size'] ); ?>" 
						data-file-mimes="<?php echo esc_attr( json_encode( $mimes ) ); ?>" 
						data-max-file-size-msg="<?php printf( esc_html__( 'Max file size: %sMB', 'tripgo' ), $field['max_file_size'] ); ?>" 
						data-formats="<?php esc_attr_e( 'Formats: .jpg, .jpeg, .png, .pdf, .doc', 'tripgo' ); ?>" 
						data-error="<?php printf( esc_html__( '%s field is required.', 'tripgo' ), $field['label'] ); ?>" 
					/>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; endforeach; ?>
	<input 
		type="hidden" 
		name="data_custom_ckf" 
		data-ckf="<?php echo esc_attr( json_encode( $list_extra_fields ) ); ?>" />
<?php endif; ?>