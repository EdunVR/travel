<?php
	$date_format = ovabrw_get_date_format();
?>
<tr class="row_rt_record" data-pos="">
    <td width="9%" class="ovabrw-input-price">
    	<input 
    		type="text" 
    		placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
    		name="ovabrw_st_adult_price[]" 
    		class="ovabrw_rt_price wc_input_price" 
    		autocomplete="off" />
    </td>
    <td width="9%" class="ovabrw-input-price">
    	<input 
    		type="text" 
    		placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
    		name="ovabrw_st_children_price[]" 
    		class="ovabrw_rt_price wc_input_price" 
    		autocomplete="off" />
    </td>
    <td width="9%" class="ovabrw-input-price">
    	<input 
    		type="text" 
    		placeholder="<?php esc_html_e('10', 'ova-brw'); ?>" 
    		name="ovabrw_st_baby_price[]" 
    		class="ovabrw_rt_price wc_input_price" 
    		autocomplete="off" />
    </td>
    <td width="12.5%">
      	<input 
      		type="text" 
      		name="ovabrw_st_startdate[]" 
      		class="ovabrw_rt_date ovabrw_rt_startdate" 
      		placeholder="<?php echo esc_attr( $date_format ); ?>" 
      		autocomplete="off" 
      		onfocus="blur();" />
    </td>
    <td width="12.5%">
      	<input 
      		type="text" 
      		name="ovabrw_st_enddate[]" 
      		class="ovabrw_rt_date ovabrw_rt_enddate" 
      		placeholder="<?php echo esc_attr( $date_format ); ?>" 
      		autocomplete="off" 
      		onfocus="blur();" />
    </td>
    <td width="39%">
    	<table width="100%" class="ovabrw_rt_discount">
	      	<thead>
				<tr>
					<th><?php esc_html_e( 'Adult Price', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Children Price', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Baby Price *', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Min - Max: Guests', 'ova-brw' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody class="real"></tbody>
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