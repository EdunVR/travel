<div class="ovabrw_rt">
	<table class="widefat">
		<thead>
			<tr>
				<th class="rt_day" ><?php esc_html_e( 'Adult Price *', 'ova-brw' ); ?></th>
				<th class="rt_day" ><?php esc_html_e( 'Children Price *', 'ova-brw' ); ?></th>
				<th class="rt_day" ><?php esc_html_e( 'Baby Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Start Date *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End Date *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Discount in Special Time (DST)', 'ova-brw' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody class="wrap_rt">
			<!-- Append html here -->
			<?php
				$date_format 			= ovabrw_get_date_format();
				$ovabrw_st_adult_price 	= get_post_meta( $post_id, 'ovabrw_st_adult_price', true );

				if ( !empty( $ovabrw_st_adult_price ) ): 
					$ovabrw_st_children_price 	= get_post_meta( $post_id, 'ovabrw_st_children_price', true );
					$ovabrw_st_baby_price 		= get_post_meta( $post_id, 'ovabrw_st_baby_price', true );
					$ovabrw_st_startdate 		= get_post_meta( $post_id, 'ovabrw_st_startdate', true );
					$ovabrw_st_enddate 			= get_post_meta( $post_id, 'ovabrw_st_enddate', true );
					$ovabrw_st_discount 		= get_post_meta( $post_id, 'ovabrw_st_discount', true );
			?>
				<?php for( $i = 0 ; $i < count( $ovabrw_st_adult_price ); $i++ ): 
					if ( $ovabrw_st_adult_price[$i] != '' ):
				?>
					<tr class="row_rt_record" data-pos="<?php echo esc_attr($i); ?>">
					    <td width="9%" class="ovabrw-input-price">
					    	<input 
				        		type="text" 
				        		placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
				        		name="ovabrw_st_adult_price[]" 
				        		value="<?php echo wc_format_localized_price( $ovabrw_st_adult_price[$i] ); ?>" 
				        		class="ovabrw_rt_price wc_input_price" 
				        		autocomplete="off" />
					    </td>
					    <td width="9%" class="ovabrw-input-price">
					    	<input 
				        		type="text" 
				        		placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
				        		name="ovabrw_st_children_price[]" 
				        		value="<?php echo isset( $ovabrw_st_children_price[$i] ) ? wc_format_localized_price( $ovabrw_st_children_price[$i] ) : 0; ?>" 
				        		class="ovabrw_rt_price wc_input_price" 
				        		autocomplete="off" />
					    </td>
					    <td width="9%" class="ovabrw-input-price">
					    	<input 
				        		type="text" 
				        		placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
				        		name="ovabrw_st_baby_price[]" 
				        		value="<?php echo isset( $ovabrw_st_baby_price[$i] ) ? wc_format_localized_price( $ovabrw_st_baby_price[$i] ) : 0; ?>" 
				        		class="ovabrw_rt_price wc_input_price" 
				        		autocomplete="off" />
					    </td>
					    <td width="12.5%">
						    <input 
						    	type="text" 
						    	name="ovabrw_st_startdate[]" 
						    	placeholder="<?php echo esc_attr( $date_format ); ?>" 
						    	value="<?php echo isset( $ovabrw_st_startdate[$i] ) ? esc_attr( $ovabrw_st_startdate[$i] ) : ''; ?>" 
						    	class="ovabrw_rt_date ovabrw_rt_startdate" 
						    	autocomplete="off" 
						    	onfocus="blur();" />
					    </td>
					    <td width="12.5%">
					      	<input 
					      		type="text" 
					      		name="ovabrw_st_enddate[]" 
					      		placeholder="<?php echo esc_attr( $date_format ); ?>" 
					      		value="<?php echo isset( $ovabrw_st_enddate[$i] ) ? esc_attr( $ovabrw_st_enddate[$i] ) : ''; ?>" 
					      		class="ovabrw_rt_date ovabrw_rt_enddate" 
					      		autocomplete="off" 
					      		onfocus="blur();" />
					    </td>
					    <td width="49%">
					    	<table width="100%" class="ovabrw_rt_discount">
						      	<thead>
									<tr>
										<th><?php esc_html_e( 'Adult Price *', 'ova-brw' ); ?></th>
										<th><?php esc_html_e( 'Children Price *', 'ova-brw' ); ?></th>
										<th><?php esc_html_e( 'Baby Price *', 'ova-brw' ); ?></th>
										<th><?php esc_html_e( 'Min - Max: Guests *', 'ova-brw' ); ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody class="real">
								<?php 
									if ( isset( $ovabrw_st_discount[$i]['adult_price'] ) && !empty( $ovabrw_st_discount[$i]['adult_price'] ) ):
										for( $k = 0; $k < count( $ovabrw_st_discount[$i]['adult_price'] ); $k++ ):
											$children_price = isset( $ovabrw_st_discount[$i]['children_price'][$k] ) ? $ovabrw_st_discount[$i]['children_price'][$k] : 0; 
											$baby_price 	= isset( $ovabrw_st_discount[$i]['baby_price'][$k] ) ? $ovabrw_st_discount[$i]['baby_price'][$k] : 0; 
											$min = isset( $ovabrw_st_discount[$i]['min'][$k]) ? $ovabrw_st_discount[$i]['min'][$k] : 0;
											$max = isset( $ovabrw_st_discount[$i]['max'][$k]) ? $ovabrw_st_discount[$i]['max'][$k] : 0;
								?>
										<tr class="tr_rt_discount">
											<td width="23%" class="ovabrw-input-price">
											    <input 
											    	type="text" 
											    	class="ovabrw_rt_discount_price wc_input_price input_per_100" 
											    	placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
											    	name="ovabrw_st_discount[<?php echo $i;?>][adult_price][]" 
											    	value="<?php echo wc_format_localized_price( $ovabrw_st_discount[$i]['adult_price'][$k] ); ?>" autocomplete="off"
											    />
											</td>
											<td width="23%" class="ovabrw-input-price"> 
											    <input 
											    	type="text" 
											    	class="ovabrw_rt_discount_price wc_input_price input_per_100" 
											    	placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
											    	name="ovabrw_st_discount[<?php echo $i;?>][children_price][]" 
											    	value="<?php echo wc_format_localized_price( $children_price ); ?>" autocomplete="off" />
											</td>
											<td width="23%" class="ovabrw-input-price">
											    <input 
											    	type="text" 
											    	class="ovabrw_rt_discount_price wc_input_price input_per_100" 
											    	placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
											    	name="ovabrw_st_discount[<?php echo $i;?>][baby_price][]" 
											    	value="<?php echo wc_format_localized_price( $baby_price ); ?>" autocomplete="off" />
											</td>
											<td width="30%">
										      	<input 
											      	type="number" 
											      	class="input_text short ovabrw_rt_discount_duration" 
											      	placeholder="1" 
											      	name="ovabrw_st_discount[<?php echo $i; ?>][min][]" 
											      	min=0
											      	value="<?php echo esc_attr( $min ); ?>" 
											      	autocomplete="off" />
										      	<input 
										      		type="number" 
										      		class="input_text short ovabrw_rt_discount_duration" 
										      		placeholder="2" 
										      		name="ovabrw_st_discount[<?php echo $i; ?>][max][]" 
										      		min=0
										      		value="<?php echo esc_attr( $max ); ?>" 
										      		autocomplete="off" />
											</td>
											<td width="1%"><a href="#" class="button delete_discount">x</a></td>
										</tr> 
									<?php endfor; endif; ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="6">
											<a href="#" class="button insert_rt_discount">
												<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
												<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_st_discount.php' ); ?>
											</a>
										</th>
									</tr>
								</tfoot>
					      	</table>
					    </td>
					    <td width="1%"><a href="#" class="button delete_rt">x</a></td>
					</tr>
			<?php endif; endfor; endif; ?>
			<!-- end for $ovabrw_st_adult_price -->
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a 
						href="#" 
						class="button insert_rt_record" 
						data-row="
							<?php
								ob_start();
								include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_st_record.php' );
								echo esc_attr( ob_get_clean() );
							?>
						">
						<?php esc_html_e( 'Add ST', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>