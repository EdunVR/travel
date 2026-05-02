<?php
    $date_format = ovabrw_get_date_format();
?>
<tr class="tr_rt_fixed_time">
    <td width="49.5%">
        <input 
            type="text" 
            name="ovabrw_fixed_time_check_in[]" 
            placeholder="<?php esc_attr_e( $date_format ); ?>" 
            class="ovabrw_fixed_timepicker check_in" 
            autocomplete="off" 
            onfocus="blur();" />
    </td>
    <td width="49.5%">
        <input 
            type="text" 
            name="ovabrw_fixed_time_check_out[]" 
            placeholder="<?php esc_attr_e( $date_format ); ?>" 
            class="ovabrw_fixed_timepicker check_out" 
            autocomplete="off" 
            onfocus="blur();" />
    </td>
    <td width="1%"><a href="#" class="button delete_fixed_time">x</a></td>
</tr>