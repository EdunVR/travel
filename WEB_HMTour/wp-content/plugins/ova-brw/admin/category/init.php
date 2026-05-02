<?php if ( ! defined( 'ABSPATH' ) ) exit();

//Product Cat Create page
add_action( 'product_cat_add_form_fields', 'ovabrw_taxonomy_add_new_meta_field', 10, 1 );
function ovabrw_taxonomy_add_new_meta_field( $array ) {
    ?>
    <!-- Display category -->
    <div class="form-field">
        <label for="Display"><?php esc_html_e('Display', 'ova-brw'); ?></label>
        <select name="ovabrw_cat_dis">
        	<option value="rental"><?php esc_html_e( 'Tour', 'ova-brw' ); ?></option>
        	<option value="shop"><?php esc_html_e( 'Shop', 'ova-brw' ); ?></option>
        </select>
    </div>

    <!-- Custom Taxonomy -->
    <div class="form-field">
        <label>
            <?php esc_html_e('Choose Taxonomies', 'ova-brw'); ?>
        </label>
        <select name="ovabrw_custom_tax[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
            <?php 
                $list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

                if ( ! empty( $list_fields ) ) {
                    foreach ( $list_fields as $slug => $field ) {
                        $name           = array_key_exists( 'name', $field ) ? $field['name'] : '';
                        $singular_name  = array_key_exists( 'singular_name', $field ) ? $field['singular_name'] : '';
                        ?>
                            <option value="<?php echo esc_attr( $slug ); ?>">
                                <?php echo esc_html( $name ); ?>
                            </option>
                        <?php
                    }
                }
            ?>
        </select>
    </div>

    <!-- Custom Checkout Field -->
    <div class="form-field">
        <div class="choose_custom_checkout_field">
            <label>
                <?php esc_html_e('Choose custom checkout fields', 'ova-brw'); ?>
            </label>
            <select name="ovabrw_choose_custom_checkout_field" id="">
                <option value="all"><?php esc_html_e( 'All', 'ova-brw' ); ?></option>
                <option value="special"><?php esc_html_e( 'Choose other fields', 'ova-brw' ); ?></option>
            </select>
        </div>
        <div id="special_cus_fields" class="show_special_checkout_field">
            <br>
            <label>
                <?php esc_html_e('Choose other custom checkout fields', 'ova-brw'); ?>
            </label>
            <select name="ovabrw_custom_checkout_field[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                <?php 
                    $list_fields = get_option( 'ovabrw_booking_form', array() );

                    if ( ! empty( $list_fields ) ) {
                        foreach( $list_fields as $slug => $field ) {
                            $label = array_key_exists( 'label', $field ) ? $field['label'] : '';
                            ?>
                                <option value="<?php echo esc_attr( $slug ); ?>">
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <!-- Product Templates -->
    <div class="form-field">
        <label>
            <?php esc_html_e('Product template', 'ova-brw'); ?>
            <span>
                <?php 
                echo wc_help_tip( esc_html__( 'Global Setting (WooCommerce >> Settings >> Booking & Rental >> Product Template) or Other (made in Templates of Elementor )', 'ova-brw' ), true );
                ?>
            </span>
        </label>
        <?php
            // Get templates from elementor
            $global_template    = get_option( 'ova_brw_template_elementor_template', 'default' );
            $templates          = get_posts( array('post_type' => 'elementor_library', 'meta_key' => '_elementor_template_type', 'meta_value' => 'page', 'numberposts'   => -1 ) );
            $list_templates     = '<option value="global" selected="selected">' . esc_html__( 'Global Settings', 'ova-brw' ) . '</option>';

            if ( ! empty( $templates ) ) {
                foreach ( $templates as $template ) {
                    $id_template    = $template->ID;

                    if ( $global_template == $id_template ) {
                        continue;
                    }

                    $title_template = $template->post_title;
                    $list_templates .= '<option value="' . $id_template . '">' . $title_template . '</option>';
                }
            }
        ?>
        <select id="ovabrw_product_templates" name="ovabrw_product_templates">
            <?php echo $list_templates; ?>
        </select>
    </div>
    <?php
}

//Product Cat Edit page
add_action( 'product_cat_edit_form_fields', 'ovabrw_taxonomy_edit_meta_field', 10, 1 );
function ovabrw_taxonomy_edit_meta_field( $term ) {
    //getting term ID
    $term_id = $term->term_id;

    // retrieve the existing value(s) for this meta field.
    $ovabrw_cat_dis     = get_term_meta( $term_id, 'ovabrw_cat_dis', true );
    $ovabrw_custom_tax  = get_term_meta( $term_id, 'ovabrw_custom_tax', true );
    
    if ( ! $ovabrw_custom_tax ) {
        $ovabrw_custom_tax = array();
    }

    $ovabrw_custom_checkout_field = get_term_meta($term_id, 'ovabrw_custom_checkout_field', true);

    if ( ! $ovabrw_custom_checkout_field ) {
        $ovabrw_custom_checkout_field = array();
    }
    
    $ovabrw_choose_custom_checkout_field = get_term_meta($term_id, 'ovabrw_choose_custom_checkout_field', true);

    // Get product template
    $global_template            = get_option( 'ova_brw_template_elementor_template', 'default' );
    $product_template           = get_term_meta( $term_id, 'ovabrw_product_templates', true );
    $templates                  = get_posts( array('post_type' => 'elementor_library', 'meta_key' => '_elementor_template_type', 'meta_value' => 'page', 'numberposts'   => -1 ) );
    $list_templates             = array();
    $list_templates['global']   = esc_html__( 'Global Settings', 'ova-brw' );

    if ( ! empty( $templates ) ) {
        foreach ( $templates as $template ) {

            $id_template    = $template->ID;

            if ( $global_template == $id_template ) {
                continue;
            }

            $title_template = $template->post_title;
            $list_templates[$id_template] = $title_template;
        }
    }

    $html_templates = '';
    foreach ( $list_templates as $id => $title ) {
        if ( $id == $product_template ) {
            $html_templates .= '<option value="' . $id . '" selected="selected">' . $title . '</option>';
        } else {
            $html_templates .= '<option value="' . $id . '">' . $title . '</option>';
        }
    }
    ?>

    <!-- Display category -->
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="ovabrw_cat_dis">
                <?php esc_html_e('Display', 'ova-brw'); ?>
            </label>
        </th>
        <td>
        	<select name="ovabrw_cat_dis">
	        	<option value="rental" <?php echo ( esc_attr($ovabrw_cat_dis) == 'rental' ) ? 'selected' : ''; ?>>
                    <?php esc_html_e( 'Rental', 'ova-brw' ); ?>
                </option>
	        	<option value="shop" <?php echo ( esc_attr($ovabrw_cat_dis) == 'shop' ) ? 'selected' : ''; ?>>/
                    <?php esc_html_e( 'Shop', 'ova-brw' ); ?>
                </option>
	        </select>
        </td>
    </tr>

    <!-- Custom Taxonomy -->
    <tr class="form-field">
        <th scope="row" valign="top">
            <label>
                <?php esc_html_e('Choose Taxonomies', 'ova-brw'); ?>
            </label>
        </th>
        <td>
            <select name="ovabrw_custom_tax[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                <?php 
                    $list_fields = get_option( 'ovabrw_custom_taxonomy', array() );
                    if ( ! empty( $list_fields ) ) {
                        foreach ( $list_fields as $slug => $field ) {
                            $name = array_key_exists( 'name', $field ) ? $field['name'] : '';
                            $singular_name = array_key_exists( 'singular_name', $field ) ? $field['singular_name'] : '';

                            $selected = in_array( $slug, $ovabrw_custom_tax) ? ' selected' : '';
                            ?>
                                <option value="<?php echo esc_attr( $slug ); ?>" <?php echo ' '.$selected; ?>>
                                    <?php echo esc_html( $name ); ?>
                                </option>
                            <?php
                        }
                    }
                ?>
            </select>
        </td>
    </tr>

    <!-- Custom Checkout Field -->
    <tr class="form-field choose_custom_checkout_field">
        <th scope="row" valign="top">
          <label>
            <?php esc_html_e('Choose custom checkout fields', 'ova-brw'); ?>
        </label>
        </th>
        <td>
            <select name="ovabrw_choose_custom_checkout_field" id="">
                <option value="all" <?php echo ( $ovabrw_choose_custom_checkout_field == 'all' ) ? 'selected' : '' ?> >
                    <?php esc_html_e( 'All', 'ova-brw' ); ?>
                </option>
                <option value="special" <?php echo ( $ovabrw_choose_custom_checkout_field == 'special' ) ? 'selected' : '' ?>>
                    <?php esc_html_e( 'Choose other fields', 'ova-brw' ); ?>
                </option>
            </select>
        </td>
    </tr>
    <tr class="form-field show_special_checkout_field">
        <th scope="row" valign="top">
            <label>
                <?php esc_html_e('Choose other custom checkout fields', 'ova-brw'); ?>
            </label>
        </th>
        <td>
            <select name="ovabrw_custom_checkout_field[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">

                <?php 
                    $list_fields = get_option( 'ovabrw_booking_form', array() );

                    if ( ! empty( $list_fields ) ) {
                        foreach ( $list_fields as $slug => $field ) {
                            $label = array_key_exists( 'label', $field ) ? $field['label'] : '';

                            $selected = in_array( $slug, $ovabrw_custom_checkout_field) ? ' selected' : '';
                        ?>
                            <option value="<?php echo esc_attr( $slug ); ?>" <?php echo ' '.$selected; ?>>
                                <?php echo esc_html( $label ); ?>
                            </option>
                        <?php
                        }
                    }
                ?>
            </select>
        </td>
    </tr>

    <!-- Product template -->
    <tr class="form-field ovabrw_product_templates">
        <th>
            <label>
                <?php esc_html_e('Product template', 'ova-brw'); ?>
                <span>
                    <?php 
                    echo wc_help_tip( esc_html__( 'Global Setting (WooCommerce >> Settings >> Booking & Rental >> Product Template) or Other (made in Templates of Elementor )', 'ova-brw' ), true );
                    ?>
                </span>
            </label>
        </th>
        <td>
            <select id="ovabrw_product_templates" name="ovabrw_product_templates">
                <?php echo $html_templates; ?>
            </select>
        </td>
    </tr>
    <?php
}

// Save extra taxonomy fields callback function.
add_action( 'edited_product_cat', 'ovabrw_save_taxonomy_custom_meta', 10, 1 );
add_action( 'create_product_cat', 'ovabrw_save_taxonomy_custom_meta', 10, 1 );
function ovabrw_save_taxonomy_custom_meta( $term_id ) {
    $ovabrw_cat_dis = filter_input(INPUT_POST, 'ovabrw_cat_dis');
    $ovabrw_custom_tax = isset( $_REQUEST['ovabrw_custom_tax'] ) ? $_REQUEST['ovabrw_custom_tax'] : array();
    $ovabrw_custom_checkout_field = isset( $_REQUEST['ovabrw_custom_checkout_field'] ) ? $_REQUEST['ovabrw_custom_checkout_field'] : array();;
    $ovabrw_choose_custom_checkout_field = isset( $_REQUEST['ovabrw_choose_custom_checkout_field'] ) ? $_REQUEST['ovabrw_choose_custom_checkout_field'] : 'all';

    // Get product template
    $product_template = isset( $_REQUEST['ovabrw_product_templates'] ) ? $_REQUEST['ovabrw_product_templates'] : 'global';

    update_term_meta($term_id, 'ovabrw_cat_dis', $ovabrw_cat_dis);
    update_term_meta($term_id, 'ovabrw_custom_tax', $ovabrw_custom_tax);
    update_term_meta($term_id, 'ovabrw_custom_checkout_field', $ovabrw_custom_checkout_field);
    update_term_meta($term_id, 'ovabrw_choose_custom_checkout_field', $ovabrw_choose_custom_checkout_field);

    // Save product template
    update_term_meta($term_id, 'ovabrw_product_templates', $product_template);
}