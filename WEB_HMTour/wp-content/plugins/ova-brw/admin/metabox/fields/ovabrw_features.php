<div class="ovabrw_features">
	<table class="widefat">
		<thead>
			<tr>
				<?php if( apply_filters( 'ovabrw_show_icon_features', 'true' ) ){ ?>
					<th><?php esc_html_e( 'Icon Class *', 'ova-brw' ); ?></th>
				<?php } ?>
				<th><?php esc_html_e( 'Label *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Description *', 'ova-brw' ); ?></th>
				<th></th>
			</tr>
		</thead>

		<tbody class="wrap_rt_features">
			<?php
				$ovabrw_features_label = get_post_meta( $post_id, 'ovabrw_features_label', true );

				if ( !empty( $ovabrw_features_label ) ): 
					$ovabrw_features_desc 		= get_post_meta( $post_id, 'ovabrw_features_desc', true );
					$ovabrw_features_icons 		= get_post_meta( $post_id, 'ovabrw_features_icons', true );
					$ovabrw_features_special 	= get_post_meta( $post_id, 'ovabrw_features_special', true );

					for( $i = 0 ; $i < count( $ovabrw_features_desc ); $i++ ): 
						if ( $ovabrw_features_label[$i] ):
			?>
				<tr class="tr_rt_feature">
					<?php if ( apply_filters( 'ovabrw_show_icon_features', 'true' ) ): ?>
					    <td width="30%">
					      	<input 
					      		type="text" 
					      		name="ovabrw_features_icons[]" 
					      		placeholder="<?php esc_html_e( 'Icon-Class', 'ova-brw' ); ?>" 
					      		value="<?php echo isset($ovabrw_features_icons[$i]) ? esc_attr($ovabrw_features_icons[$i]) : ''; ?>" />
					    </td>
					<?php endif; ?>

				    <td width="30%">
				      	<input 
				      		type="text" 
				      		name="ovabrw_features_label[]" 
				      		placeholder="<?php esc_html_e( 'Label', 'ova-brw' ); ?>" 
				      		value="<?php echo esc_attr( $ovabrw_features_label[$i] ); ?>" />
				    </td>

				    <?php $cols = apply_filters( 'ovabrw_show_icon_features', 'true' ) ? '29%' : '49%'; ?>
				    <td width="<?php echo $cols; ?>">
				      	<input 
				      		type="text" 
				      		name="ovabrw_features_desc[]" 
				      		placeholder="<?php esc_html_e( 'Description', 'ova-brw' ); ?>" 
				      		value="<?php echo isset( $ovabrw_features_desc[$i] ) ? esc_attr( $ovabrw_features_desc[$i] ) : '' ; ?>" />
				    </td>
				    <td width="1%"><a href="#" class="button delete_feature">x</a></td>
				</tr>
			<?php endif; endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a 
						href="#" 
						class="button insert_rt_feature" 
						data-row="
							<?php
								ob_start();
								include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_feature_field.php' );
								echo esc_attr( ob_get_clean() );
							?>
						">
						<?php esc_html_e( 'Add Feature', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>