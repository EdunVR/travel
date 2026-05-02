<?php
    $date_format = ovabrw_get_date_format();
?>
<div class="ovabrw_fixed_time">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Check in *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Check out *', 'ova-brw' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody class="wrap_rt_fixed_time">
			<?php
				$fixed_time_check_in 	= get_post_meta( $post_id, 'ovabrw_fixed_time_check_in', true );
				$fixed_time_check_out 	= get_post_meta( $post_id, 'ovabrw_fixed_time_check_out', true );

				if ( ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ): 
					for( $i = 0 ; $i < count( $fixed_time_check_in ); $i++ ):
						if ( $fixed_time_check_in[$i] ):
			?>
				<tr class="tr_rt_fixed_time">
				    <td width="49.5%">
				      	<input 
				      		type="text" 
				      		name="ovabrw_fixed_time_check_in[]" 
				      		class="ovabrw_fixed_timepicker check_in" 
				      		placeholder="<?php esc_attr_e( $date_format ); ?>" 
				      		value="<?php echo esc_attr( $fixed_time_check_in[$i] ); ?>" 
				      		autocomplete="off" 
            				onfocus="blur();" />
				    </td>
				    <td width="49.5%">
				      	<input 
				      		type="text" 
				      		name="ovabrw_fixed_time_check_out[]" 
				      		placeholder="<?php esc_attr_e( $date_format ); ?>" 
				      		class="ovabrw_fixed_timepicker check_out" 
				      		value="<?php echo isset( $fixed_time_check_out[$i] ) ? esc_attr( $fixed_time_check_out[$i] ) : '' ; ?>" 
				      		autocomplete="off" 
            				onfocus="blur();" />
				    </td>
				    <td width="1%"><a href="#" class="button delete_fixed_time">x</a></td>
				</tr>
			<?php endif; endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a 
						href="#" 
						class="button insert_rt_fixed_time" 
						data-row="
							<?php
								ob_start();
								include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_fixed_time_field.php' );
								echo esc_attr( ob_get_clean() );
							?>
						">
						<?php esc_html_e( 'Add Time', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>