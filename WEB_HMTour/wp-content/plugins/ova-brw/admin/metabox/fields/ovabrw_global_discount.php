<div class="ovabrw_global_discount">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Adult Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Children Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Baby Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Min - Max: Guests *', 'ova-brw' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$ovabrw_gd_adult_price = get_post_meta( $post_id, 'ovabrw_gd_adult_price', true );

				if ( ! empty( $ovabrw_gd_adult_price ) ):
					$ovabrw_gd_children_price 	= get_post_meta( $post_id, 'ovabrw_gd_children_price', true );
					$ovabrw_gd_baby_price 		= get_post_meta( $post_id, 'ovabrw_gd_baby_price', true );
					$ovabrw_gd_duration_min 	= get_post_meta( $post_id, 'ovabrw_gd_duration_min', true );
					$ovabrw_gd_duration_max 	= get_post_meta( $post_id, 'ovabrw_gd_duration_max', true );

				for( $i = 0 ; $i < count( $ovabrw_gd_adult_price ); $i++ ): 
					if ( $ovabrw_gd_adult_price[$i] != '' ):
				?>
					<tr class="row_discount">
					    <td width="20%" class="ovabrw-input-price">
					        <input 
					        	type="text" 
					        	class="input_text wc_input_price" 
					        	placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
					        	name="ovabrw_gd_adult_price[]" 
					        	value="<?php echo wc_format_localized_price( $ovabrw_gd_adult_price[$i] ); ?>" autocomplete="off" />
					    </td>
					    <td width="20%" class="ovabrw-input-price">
					        <input 
					        	type="text" 
					        	class="input_text wc_input_price" 
					        	placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
					        	name="ovabrw_gd_children_price[]" 
					        	value="<?php echo isset( $ovabrw_gd_children_price[$i] ) ? wc_format_localized_price( $ovabrw_gd_children_price[$i] ) : 0; ?>" 
					        	autocomplete="off" />
					    </td>
					    <td width="20%" class="ovabrw-input-price">
					        <input 
					        	type="text" 
					        	class="input_text wc_input_price" 
					        	placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
					        	name="ovabrw_gd_baby_price[]" 
					        	value="<?php echo isset( $ovabrw_gd_baby_price[$i] ) ? wc_format_localized_price( $ovabrw_gd_baby_price[$i] ) : 0; ?>" 
					        	autocomplete="off" />
					    </td>
					    <td width="39%">
						    <input 
						    	type="number" 
						    	class="input_text ovabrw-global-duration" 
						    	placeholder="1" 
						    	name="ovabrw_gd_duration_min[]" 
						    	value="<?php echo isset( $ovabrw_gd_duration_min[$i] ) ? $ovabrw_gd_duration_min[$i] : 0; ?>" 
						    	min=0
						    	autocomplete="off" />
						    <input 
						    	type="number" 
						    	class="input_text ovabrw-global-duration" 
						    	placeholder="2" 
						    	name="ovabrw_gd_duration_max[]" 
						    	value="<?php echo isset( $ovabrw_gd_duration_max[$i] ) ? $ovabrw_gd_duration_max[$i] : 0; ?>" 
						    	min=0
						    	autocomplete="off" />
					    </td>
					    <td width="1%"><a href="#" class="button delete">x</a></td>
					</tr>
				<?php endif; endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a 
						href="#" 
						class="button insert_discount" 
						data-row="
							<?php
								ob_start();
								include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_global_discount_field.php' );
								echo esc_attr( ob_get_clean() );
							?>
						">
						<?php esc_html_e( 'Add GD', 'ova-brw' ); ?>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>