<div class="ovabrw_service">
	<div class="wrap_ovabrw_service_group">
		<?php
			$ovabrw_label_service = get_post_meta( $post_id, 'ovabrw_label_service', true );
			
			if ( ! empty( $ovabrw_label_service ) ): 
				$ovabrw_service_required 		= get_post_meta( $post_id, 'ovabrw_service_required', true );
				$ovabrw_service_name 			= get_post_meta( $post_id, 'ovabrw_service_name', true );
				$ovabrw_service_id 				= get_post_meta( $post_id, 'ovabrw_service_id', true );
				$ovabrw_service_adult_price 	= get_post_meta( $post_id, 'ovabrw_service_adult_price', true );
				$ovabrw_service_children_price 	= get_post_meta( $post_id, 'ovabrw_service_children_price', true );
				$ovabrw_service_baby_price 		= get_post_meta( $post_id, 'ovabrw_service_baby_price', true );
				$ovabrw_service_duration_type 	= get_post_meta( $post_id, 'ovabrw_service_duration_type', true );

				for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ): 
					if ( $ovabrw_label_service[$i] ):
			?>
				<div class="ovabrw_service_group">
					<div class="service_input">
						<div class="ovabrw_label_service">
							<span class="ovabrw_label_service">
								<?php esc_html_e( 'Label *', 'ova-brw' ); ?>
							</span>
							<input 
								type="text" 
								class="ovabrw_input_label" 
								name="ovabrw_label_service[]" 
								value="<?php echo esc_attr( $ovabrw_label_service[$i] ); ?>" 
								autocomplete="off" />
							<span>
								<?php esc_html_e( 'Required', 'ova-brw' ); ?>
							</span>
							<select name="ovabrw_service_required[]">
								<option value="yes"<?php if ( $ovabrw_service_required[$i] == 'yes' ) echo ' selected'; ?>>
									<?php esc_html_e( 'Yes', 'ova-brw' ); ?>
								</option>
								<option value="no"<?php if ( $ovabrw_service_required[$i] == 'no' ) echo ' selected'; ?>>
									<?php esc_html_e( 'No', 'ova-brw' ); ?>
								</option>
							</select>
						</div>
						<a href="#" class="button delete_service_group">x</a>
					</div>
					<table class="widefat">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Unique ID *', 'ova-brw' ); ?></th>
								<th><?php esc_html_e( 'Name *', 'ova-brw' ); ?></th>
								<th><?php esc_html_e( 'Adult Price *', 'ova-brw' ); ?></th>
								<th><?php esc_html_e( 'Children Price *', 'ova-brw' ); ?></th>
								<th><?php esc_html_e( 'Baby Price *', 'ova-brw' ); ?></th>
								<th><?php esc_html_e( 'Type', 'ova-brw' ); ?></th>
							</tr>
						</thead>
						<tbody class="wrap_service">
							<?php
								if ( isset( $ovabrw_service_id[$i] ) && is_array( $ovabrw_service_id[$i] ) ): 
									foreach( $ovabrw_service_id[$i] as $key => $val ): 
							?>
								<tr class="tr_rt_service">
								    <td width="13%">
								      	<input 
								      		type="text" 
								      		name="ovabrw_service_id[<?php echo $i; ?>][]" 
								      		placeholder="<?php esc_html_e( 'No space', 'ova-brw' ); ?>" 
								      		class="ovabrw_service_key" 
								      		value="<?php echo esc_attr( $val ); ?>" 
								      		autocomplete="off" />
								    </td>
								    <td width="29%">
								      	<input 
								      		type="text" 
								      		name="ovabrw_service_name[<?php echo $i; ?>][]" 
								      		value="<?php echo isset( $ovabrw_service_name[$i][$key] ) ? esc_attr( $ovabrw_service_name[$i][$key] ) : ''; ?>" 
								      		autocomplete="off" />
								    </td>
								    <td width="15%" class="ovabrw-input-price">
								      	<input 
								      		type="text" 
								      		name="ovabrw_service_adult_price[<?php echo $i; ?>][]" 
								      		class="wc_input_price" 
								      		value="<?php echo isset( $ovabrw_service_adult_price[$i][$key] ) ? wc_format_localized_price( $ovabrw_service_adult_price[$i][$key] ) : 0; ?>" 
								      		placeholder="<?php esc_html_e( '10', 'ova-brw' ); ?>" 
								      		autocomplete="off" />
								    </td>
								    <td width="15%" class="ovabrw-input-price">
								      	<input 
								      		type="text" 
								      		name="ovabrw_service_children_price[<?php echo $i; ?>][]"
								      		class="wc_input_price" 
								      		value="<?php echo isset( $ovabrw_service_children_price[$i][$key] ) ? wc_format_localized_price( $ovabrw_service_children_price[$i][$key] ) : 0; ?>" 
								      		placeholder="<?php esc_html_e( '10', 'ova-brw' ); ?>" 
								      		autocomplete="off" />
								    </td>
								    <td width="15%" class="ovabrw-input-price">
								      	<input 
								      		type="text" 
								      		name="ovabrw_service_baby_price[<?php echo $i; ?>][]" 
								      		class="wc_input_price" 
								      		value="<?php echo isset( $ovabrw_service_baby_price[$i][$key] ) ? wc_format_localized_price( $ovabrw_service_baby_price[$i][$key] ) : 0; ?>" 
								      		placeholder="<?php esc_html_e( '10', 'ova-brw' ); ?>" 
								      		autocomplete="off" />
								    </td>
								    <td width="12%">
								      	<select name="ovabrw_service_duration_type[<?php echo $i; ?>][]" class="short_dura">
										<?php 
											$selected_person 	= isset( $ovabrw_service_duration_type[$i][$key] ) && $ovabrw_service_duration_type[$i][$key] == 'person' ? 'selected' : '';
											$selected_total 	=  isset( $ovabrw_service_duration_type[$i][$key] ) && $ovabrw_service_duration_type[$i][$key] == 'total' ? 'selected' : '';
										?>
											<option value="person" <?php echo esc_attr( $selected_person ); ?> >
												<?php esc_html_e( '/per person', 'ova-brw' ); ?>
											</option>
								    		<option value="total" <?php echo esc_attr( $selected_total ); ?> >
								    			<?php esc_html_e( '/order', 'ova-brw' ); ?>
								    		</option>
								        </select>
								    </td>
								    <td width="1%"><a href="#" class="button delete_service">x</a></td>
								</tr>
							<?php endforeach; endif; ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="6">
									<a href="#" class="button insert_service_option" data-pos="<?php echo esc_attr($i); ?>">
									<?php esc_html_e( 'Add Option', 'ova-brw' ); ?>
									</a>
									<div class="ovabrw_content_service" style="display: none">
										<table>
											<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service_field.php' ); ?>
										</table>
									</div>
								</th>
							</tr>
						</tfoot>

					</table>
				</div>
		<?php endif; endfor; endif; ?>
	</div>
	<a 
		href="#" 
		class="button insert_service_group" 
		data-row="
			<?php
				ob_start();
				include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service_group.php' );
				echo esc_attr( ob_get_clean() );
			?>
		">
		<?php esc_html_e( 'Add Service', 'ova-brw' ); ?></a>
	</a>
</div>


