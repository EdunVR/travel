<?php
$date_format = ovabrw_get_date_format();
$ovabrw_untime_startdate 	= get_post_meta( $post_id, 'ovabrw_untime_startdate', true );
$ovabrw_untime_enddate 		= get_post_meta( $post_id, 'ovabrw_untime_enddate', 'false' );
?>
<div class="ovabrw_rt_untime">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody class="wrap_rt_untime">
			<!-- Append html here -->
			<?php if ( $ovabrw_untime_startdate && $ovabrw_untime_enddate ): ?>
				<?php for( $i = 0 ; $i < count( $ovabrw_untime_startdate ); $i++ ): 
					if ( $ovabrw_untime_startdate[$i] ):
				?>
					<tr class="tr_rt_untime">
					    <td width="20%">
					      	<input 
								type="text" 
								name="ovabrw_untime_startdate[]" 
								placeholder="<?php echo esc_attr( $date_format ); ?>" 
								value="<?php echo isset( $ovabrw_untime_startdate[$i] ) ? date( $date_format, strtotime( $ovabrw_untime_startdate[$i] ) ) : ''; ?>" 
								class="unavailable_time start_date" 
								autocomplete="off" 
								onfocus="blur(); "/>
					    </td>
					    <td width="20%">
					      	<input 
					      		type="text" 
					      		name="ovabrw_untime_enddate[]" 
					      		placeholder="<?php echo esc_attr( $date_format ); ?>" 
					      		value="<?php echo isset( $ovabrw_untime_enddate[$i] ) ? date( $date_format, strtotime( $ovabrw_untime_enddate[$i] ) ) : ''; ?>" 
					      		class="unavailable_time end_date" 
					      		autocomplete="off" 
					      		onfocus="blur();"/>
					    </td>
					    <td width="1%"><a href="#" class="button delete_untime">x</a></td>
					</tr>

			<?php endif; endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a 
						href="#" 
						class="button insert_rt_untime" 
						data-row="
							<?php
								ob_start();
								include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_untime_field.php' );
								echo esc_attr( ob_get_clean() );
							?>
						">
						<?php esc_html_e( 'Add UT', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>