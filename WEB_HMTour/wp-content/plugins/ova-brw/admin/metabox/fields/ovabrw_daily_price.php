<?php
	$time_format = ovabrw_get_time_format();

	$ovabrw_schedule_time 			= get_post_meta( $post_id, 'ovabrw_schedule_time', true );
	$ovabrw_schedule_adult_price 	= get_post_meta( $post_id, 'ovabrw_schedule_adult_price', true );
	$ovabrw_schedule_children_price = get_post_meta( $post_id, 'ovabrw_schedule_children_price', true );
	$ovabrw_schedule_baby_price 	= get_post_meta( $post_id, 'ovabrw_schedule_baby_price', true );
	$ovabrw_schedule_type 			= get_post_meta( $post_id, 'ovabrw_schedule_type', true );

	$args_daily = array(
		'monday',
		'tuesday',
		'wednesday',
		'thursday',
		'friday',
		'saturday',
		'sunday',
	);
?>

<div class="ovabrw_daily_price">
	<div class="ovabrw_daily_group">
	<?php
		foreach ( $args_daily as $day ):
	?>
		<div class="ovabrw_daily_day ovabrw_<?php echo esc_attr( $day ); ?>">
			<?php
				$schedule_time				= isset( $ovabrw_schedule_time[$day] ) 				? $ovabrw_schedule_time[$day] 			: array();
				$schedule_adult_price 		= isset( $ovabrw_schedule_adult_price[$day] ) 		? $ovabrw_schedule_adult_price[$day] 	: array();
				$schedule_children_price 	= isset( $ovabrw_schedule_children_price[$day] ) 	? $ovabrw_schedule_children_price[$day] : array();
				$schedule_baby_price		= isset( $ovabrw_schedule_baby_price[$day] ) 		? $ovabrw_schedule_baby_price[$day] 	: array();
				$schedule_type 				= isset( $ovabrw_schedule_type[$day] ) 				? $ovabrw_schedule_type[$day] 			: array();

				$label_day = '';

				if ( 'monday' === $day ) {
					$label_day = esc_html__( 'Monday', 'ova-brw' );
				} elseif ( 'tuesday' === $day ) {
					$label_day = esc_html__( 'Tuesday', 'ova-brw' );
				} elseif ( 'wednesday' === $day ) {
					$label_day = esc_html__( 'Wednesday', 'ova-brw' );
				} elseif ( 'thursday' === $day ) {
					$label_day = esc_html__( 'Thursday', 'ova-brw' );
				} elseif ( 'friday' === $day ) {
					$label_day = esc_html__( 'Friday', 'ova-brw' );
				} elseif ( 'saturday' === $day ) {
					$label_day = esc_html__( 'Saturday', 'ova-brw' );
				} else {
					$label_day = esc_html__( 'Sunday', 'ova-brw' );
				}
			?>
			<div class="ovabrw_daily_label"><?php echo esc_html( $label_day ); ?></div>
			<table class="ovabrw_daily_time widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time *', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Adult Price *', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Children Price *', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Baby Price *', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Type', 'ova-brw' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $schedule_time ) && is_array( $schedule_time ) ): ?>
						<?php foreach ( $schedule_time as $k => $time ): ?>
							<tr>
								<td width="19%">
							        <input 
							            type="text" 
							            name="ovabrw_schedule_time[<?php echo esc_attr( $day ); ?>][]" 
							            class="ovabrw_hour_picker" 
							            value="<?php echo esc_attr( $time ); ?>" 
							            placeholder="<?php echo esc_attr( $time_format ); ?>" 
							            autocomplete="off" />
							    </td>
							    <td width="22%" class="ovabrw-input-price">
							        <input 
							            type="text" 
							            name="ovabrw_schedule_adult_price[<?php echo esc_attr( $day ); ?>][]" 
							            class="wc_input_price"
							            value="<?php echo isset( $schedule_adult_price[$k] ) ? wc_format_localized_price( $schedule_adult_price[$k] ) : ''; ?>" 
							            placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
							            autocomplete="off" />
							    </td>
							    <td width="22%" class="ovabrw-input-price">
							        <input 
							            type="text" 
							            name="ovabrw_schedule_children_price[<?php echo esc_attr( $day ); ?>][]" 
							            class="wc_input_price"
							            value="<?php echo isset( $schedule_children_price[$k] ) ? wc_format_localized_price( $schedule_children_price[$k] ) : ''; ?>"
							            placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
							            autocomplete="off" />
							    </td>
							    <td width="22%" class="ovabrw-input-price">
							        <input 
							            type="text" 
							            name="ovabrw_schedule_baby_price[<?php echo esc_attr( $day ); ?>][]" 
							            class="wc_input_price"
							            value="<?php echo isset( $schedule_baby_price[$k] ) ? wc_format_localized_price( $schedule_baby_price[$k] ) : ''; ?>" 
							            placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
							            autocomplete="off" />
							    </td>
							    <td width="14%">
							    	<?php 
										$type = isset( $schedule_type[$k] ) ? esc_attr( $schedule_type[$k] ) : '';
									?>
							        <select name="ovabrw_schedule_type[<?php echo esc_attr( $day ); ?>][]" class="short_dura">
							            <option value="person"<?php selected( 'person', $type ); ?>>
							                <?php esc_html_e( '/per person', 'ova-brw' ); ?>
							            </option>
							            <option value="total"<?php selected( 'total', $type ); ?>>
							                <?php esc_html_e( '/order', 'ova-brw' ); ?>
							            </option>
							        </select>
							    </td>
							    <td width="1%"><a href="#" class="button remove_time">x</a></td>
							</tr>
					<?php endforeach; endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">
							<a 
								href="#" 
								class="button insert_time" 
								data-row="
								<?php
									$template = OVABRW_PLUGIN_PATH.'/admin/metabox/fields/schedule/'.$day.'.php';
									ob_start();
									include( $template );
									echo esc_attr( ob_get_clean() );
								?>">
								<?php esc_html_e( 'Add Time', 'ova-brw' ); ?>
							</a>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	<?php endforeach; ?>
	</div>
</div>