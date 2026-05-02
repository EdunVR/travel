<tr class="tr_rt_service">
    <td width="13%">
        <input 
            type="text" 
            name="ovabrw_service_id[ovabrw_key][]" 
            class="ovabrw_service_key" 
            placeholder="<?php esc_html_e( 'No space', 'ova-brw' ); ?>" 
            autocomplete="off" />
    </td>
    <td width="29%">
        <input 
            type="text" 
            name="ovabrw_service_name[ovabrw_key][]" 
            autocomplete="off" />
    </td>
    <td width="15%" class="ovabrw-input-price">
        <input 
            type="text" 
            name="ovabrw_service_adult_price[ovabrw_key][]"
            class="wc_input_price"  
            placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
            autocomplete="off" />
    </td>
    <td width="15%" class="ovabrw-input-price">
        <input 
            type="text" 
            name="ovabrw_service_children_price[ovabrw_key][]" 
            class="wc_input_price" 
            placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
            autocomplete="off" />
    </td>
    <td width="15%" class="ovabrw-input-price">
        <input 
            type="text" 
            name="ovabrw_service_baby_price[ovabrw_key][]"
            class="wc_input_price"  
            placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" 
            autocomplete="off" />
    </td>
    <td width="12%">
        <select name="ovabrw_service_duration_type[ovabrw_key][]" class="short_dura">
            <option value="person">
                <?php esc_html_e( '/per person', 'ova-brw' ); ?>
            </option>
            <option value="total">
                <?php esc_html_e( '/order', 'ova-brw' ); ?>
            </option>
        </select>
    </td>
    <td width="1%"><a href="#" class="button delete_service">x</a></td>
</tr>