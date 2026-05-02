<tr class="tr_rt_feature">
    <?php if( apply_filters( 'ovabrw_show_icon_features', 'true' ) ): ?>
        <td width="30%">
            <input 
                type="text" 
                name="ovabrw_features_icons[]" 
                placeholder="<?php esc_html_e( 'Class Font', 'ova-brw' ); ?>" />
        </td>
    <?php endif; ?>
    <td width="30%">
        <input 
            type="text" 
            name="ovabrw_features_label[]" 
            placeholder="<?php esc_html_e( 'Label', 'ova-brw' ); ?>" />
    </td>

    <?php $cols = apply_filters( 'ovabrw_show_icon_features', 'true' ) ? '29%' : '49%'; ?>
    <td width="<?php echo $cols; ?>">
        <input 
            type="text" 
            name="ovabrw_features_desc[]" 
            placeholder="<?php esc_html_e( 'Description', 'ova-brw' ); ?>" />
    </td>
    <td width="1%"><a href="#" class="button delete_feature">x</a></td>
</tr>