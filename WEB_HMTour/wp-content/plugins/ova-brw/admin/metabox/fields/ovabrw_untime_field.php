<?php
$date_format = ovabrw_get_date_format();
?>

<tr class="tr_rt_untime">
    <td width="20%">
        <input 
            type="text" 
            name="ovabrw_untime_startdate[]" 
            placeholder="<?php echo esc_html( $date_format ); ?>" 
            class="unavailable_time start_date" 
            autocomplete="off" 
            onfocus="blur(); "/>
    </td>
    <td width="20%">
        <input 
            type="text" 
            name="ovabrw_untime_enddate[]" 
            placeholder="<?php echo esc_html( $date_format ); ?>" 
            class=" unavailable_time end_date" 
            autocomplete="off" 
            onfocus="blur(); "/>
    </td>
    <td width="1%"><a href="#" class="button delete_untime">x</a></td>
</tr>