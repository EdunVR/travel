<div class="ovabrw_resources">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Unique ID *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Name *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Description', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Adult Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Children Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Baby Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Type', 'ova-brw' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody class="wrap_resources">
			<?php
				$ovabrw_rs_id = get_post_meta( $post_id, 'ovabrw_rs_id', true );

				if ( !empty( $ovabrw_rs_id ) ): 
					$ovabrw_rs_name 			= get_post_meta( $post_id, 'ovabrw_rs_name', true );
					$ovabrw_rs_description 		= get_post_meta( $post_id, 'ovabrw_rs_description', true );
					$ovabrw_rs_adult_price 		= get_post_meta( $post_id, 'ovabrw_rs_adult_price', true );
					$ovabrw_rs_children_price 	= get_post_meta( $post_id, 'ovabrw_rs_children_price', true );
					$ovabrw_rs_baby_price 		= get_post_meta( $post_id, 'ovabrw_rs_baby_price', true );
					$ovabrw_rs_duration_type 	= get_post_meta( $post_id, 'ovabrw_rs_duration_type', true );
					
			?>
				<?php for( $i = 0 ; $i < count( $ovabrw_rs_id ); $i++ ): 
					if ( $ovabrw_rs_id[$i] ):
				?>
					<tr class="tr_rt_resource">
						<td width="13%">
					      	<input 
					      		type="text" 
					      		name="ovabrw_rs_id[]" 
					      		value="<?php echo esc_attr( $ovabrw_rs_id[$i] ); ?>" 
					      		placeholder="<?php esc_html_e( 'No space', 'ova-brw' ); ?>" 
					      		autocomplete="off" />
					    </td>
					    <td width="24%">
					      	<input 
					      		type="text" 
					      		name="ovabrw_rs_name[]" 
					      		value="<?php echo isset( $ovabrw_rs_name[$i] ) ? $ovabrw_rs_name[$i] : ''; ?>" 
					      		autocomplete="off" />
					    </td>
					    <td width="24%">
					      	<input 
					      		type="text" 
					      		name="ovabrw_rs_description[]" 
					      		value="<?php echo isset( $ovabrw_rs_description[$i] ) ? $ovabrw_rs_description[$i] : ''; ?>" 
					      		autocomplete="off" />
					    </td>
					    <td width="9%" class="ovabrw-input-price">
					      	<input 
					      		type="text" 
					      		name="ovabrw_rs_adult_price[]" 
					      		class="wc_input_price"
					      		value="<?php echo isset( $ovabrw_rs_adult_price[$i] ) ? wc_format_localized_price($ovabrw_rs_adult_price[$i]) : 0; ?>" 
					      		placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
					      		autocomplete="off" />
					    </td>
					    <td width="9%" class="ovabrw-input-price">
					      	<input 
					      		type="text" 
					      		name="ovabrw_rs_children_price[]" 
					      		class="wc_input_price"
					      		value="<?php echo isset( $ovabrw_rs_children_price[$i] ) ? wc_format_localized_price($ovabrw_rs_children_price[$i]) : 0; ?>" 
					      		placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
					      		autocomplete="off" />
					    </td>
					    <td width="9%" class="ovabrw-input-price">
					      	<input 
					      		type="text" 
					      		name="ovabrw_rs_baby_price[]" 
					      		class="wc_input_price"
					      		value="<?php echo isset( $ovabrw_rs_baby_price[$i] ) ? wc_format_localized_price($ovabrw_rs_baby_price[$i]) : 0; ?>"placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
					      		autocomplete="off" />
					    </td>
					    <td width="12%">
					    	<select name="ovabrw_rs_duration_type[]" class="short_dura">
								<?php 
									$selected_person 	= $ovabrw_rs_duration_type[$i] == 'person' ? 'selected' : '';
									$selected_total 	= $ovabrw_rs_duration_type[$i] == 'total' ? 'selected' : '';
								?>
									<option value="person" <?php echo esc_attr( $selected_person ); ?> >
										<?php esc_html_e( '/per person', 'ova-brw' ); ?>
									</option>
						    		<option value="total" <?php echo esc_attr( $selected_total ); ?> >
						    			<?php esc_html_e( '/order', 'ova-brw' ); ?>
						    		</option>
					    	</select>
					    </td>
					    <td width="1%"><a href="#" class="button delete_resource">x</a></td>
					</tr>
			<?php endif; endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a 
						href="#" 
						class="button insert_resources" 
						data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_resources_field.php' );
							echo esc_attr( ob_get_clean() );
						?>">
						<?php esc_html_e( 'Add Resources', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>


