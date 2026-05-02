<?php if (!defined( 'ABSPATH' )) exit;

if( !class_exists('Tripgo_Metaboxes') ){
    
    class Tripgo_Metaboxes {

        public $prefix = 'ova_met_';

        public function __construct() {
            
            

            add_action( 'add_meta_boxes', array( $this, 'add' ) );
            add_action( 'save_post', array($this, 'save') );
        }

        function add(){

            // General Setting
            add_meta_box(
                $this->prefix.'general_setting',          // Unique ID
                esc_html__('General Setting', 'ova-framework'), // Box title
                array( $this, 'general_setting' ),   // Content callback, must be of type callable
                apply_filters( 'tripgo_set_header_version' ,array( 'post', 'page' ) )                  // Post type
            );

            // Post Format Setting
            add_meta_box(
                $this->prefix.'embed_setting',          // Unique ID
                esc_html__('Embed setting', 'ova-framework'), // Box title
                array( $this, 'embed_setting' ),   // Content callback, must be of type callable
                array( 'post' ),
                'side', // priority
                'high' // position
            );

           

            add_meta_box(
             $this->prefix.'gallery_setting',
             esc_html__('Gallery', 'ova-framework'),
             array( $this, 'galery_setting' ),
             array( 'post' ),
             'side',
             'high'
             );
             
        

        }

        function save( int $post_id  ){

            // Header Version
            if ( array_key_exists(  $this->prefix.'header_version', $_POST ) ) {
                update_post_meta(
                    $post_id,
                    $this->prefix.'header_version',
                    $_POST[$this->prefix.'header_version']
                );
            }

            // Footer Version
            if ( array_key_exists(  $this->prefix.'footer_version', $_POST ) ) {
                update_post_meta(
                    $post_id,
                    $this->prefix.'footer_version',
                    $_POST[$this->prefix.'footer_version']
                );
            }

            // Main layout
            if ( array_key_exists(  $this->prefix.'main_layout', $_POST ) ) {
                update_post_meta(
                    $post_id,
                    $this->prefix.'main_layout',
                    $_POST[$this->prefix.'main_layout']
                );
            }

            // Wide layout
            if ( array_key_exists(  $this->prefix.'wide_site', $_POST ) ) {
                update_post_meta(
                    $post_id,
                    $this->prefix.'wide_site',
                    $_POST[$this->prefix.'wide_site']
                );
            }

            // Embed Media
            if ( array_key_exists(  $this->prefix.'embed_media', $_POST ) ) {
                update_post_meta(
                    $post_id,
                    $this->prefix.'embed_media',
                    $_POST[$this->prefix.'embed_media']
                );
            }

            // Save
            if (!isset($_POST['gallery_meta_nonce']) || !wp_verify_nonce($_POST['gallery_meta_nonce'], basename(__FILE__))) return;
            if (!current_user_can('edit_post', $post_id)) return;
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

            if(isset($_POST[$this->prefix.'gallery_id'])) {
                update_post_meta($post_id, $this->prefix.'gallery_id', $_POST[$this->prefix.'gallery_id']);
            } else {
                delete_post_meta($post_id, $this->prefix.'gallery_id');
            }
            
        }

        function general_setting( $post ) {

            // Header Version
            $header_selected = get_post_meta( $post->ID, $this->prefix.'header_version', true );

            $list_header = apply_filters('tripgo_list_header', '') != '' ? array_merge( array( 'global' => 'Global' ),  apply_filters('tripgo_list_header', '') ) : array( 'global' => 'Global' );
            ?>
            <label for="<?php echo $this->prefix.'header_version' ?>">
                <?php esc_html_e('Header Version', 'ova-framework'); ?>
            </label>

            <select name="<?php echo $this->prefix.'header_version' ?>" id="<?php echo $this->prefix.'header_version' ?>" class="postbox">
                <?php foreach ($list_header as $key => $value) { ?>
                    <option value="<?php echo $key ?>" <?php selected( $header_selected, $key ); ?>>
                        <?php echo $value; ?>
                    </option>
                <?php } ?>
            </select>

            <br><br>
            <?php

            // Footer Version
            $footer_selected = get_post_meta( $post->ID, $this->prefix.'footer_version', true );

            $list_footer = apply_filters('tripgo_list_footer', '') != '' ? array_merge( array( 'global' => 'Global' ),  apply_filters('tripgo_list_footer', '') ) : array( 'global' => 'Global' );
            ?>
            <label for="<?php echo $this->prefix.'footer_version' ?>">
                <?php esc_html_e('Footer Version', 'ova-framework'); ?>
            </label>

            <select name="<?php echo $this->prefix.'footer_version' ?>" id="<?php echo $this->prefix.'footer_version' ?>" class="postbox">
                <?php foreach ($list_footer as $key => $value) { ?>
                    <option value="<?php echo $key ?>" <?php selected( $footer_selected, $key ); ?>>
                        <?php echo $value; ?>
                    </option>
                <?php } ?>
            </select>

            <br><br>
            <?php

            // Main layout
            $layout_selected = get_post_meta( $post->ID, $this->prefix.'main_layout', true );

            $layouts = apply_filters('tripgo_define_layout', '') != '' ? array_merge( array( 'global' => 'Global' ),  apply_filters('tripgo_define_layout', '') ) : array( 'global' => 'Global' );
            ?>
            <label for="<?php echo $this->prefix.'main_layout' ?>">
                <?php esc_html_e('Main layout', 'ova-framework'); ?>
            </label>

            <select name="<?php echo $this->prefix.'main_layout' ?>" id="<?php echo $this->prefix.'main_layout' ?>" class="postbox">
                <?php foreach ($layouts as $key => $value) { ?>
                    <option value="<?php echo $key ?>" <?php selected( $layout_selected, $key ); ?>>
                        <?php echo $value; ?>
                    </option>
                <?php } ?>
            </select>

            <br><br>
            <?php

            // Wide site
            $wide_site_selected = get_post_meta( $post->ID, $this->prefix.'wide_site', true );

            $wide_site = apply_filters('tripgo_define_wide_boxed', '') != '' ? array_merge( array( 'global' => 'Global' ),  apply_filters('tripgo_define_wide_boxed', '') ) : array( 'global' => 'Global' );
            ?>
            <label for="<?php echo $this->prefix.'wide_site' ?>">
                <?php esc_html_e('Wide Site', 'ova-framework'); ?>
            </label>

            <select name="<?php echo $this->prefix.'wide_site' ?>" id="<?php echo $this->prefix.'wide_site' ?>" class="postbox">
                <?php foreach ($wide_site as $key => $value) { ?>
                    <option value="<?php echo $key ?>" <?php selected( $wide_site_selected, $key ); ?>>
                        <?php echo $value; ?>
                    </option>
                <?php } ?>
            </select>
            <?php

            
        }

        function embed_setting( $post ){

            // Embed Media
            $header_selected = get_post_meta( $post->ID, $this->prefix.'embed_media', true );
            ?>
            <label for="<?php echo $this->prefix.'embed_media' ?>">
                <?php esc_html_e('Embed Video Link', 'ova-framework'); ?>
            </label>

            <input type="text" name="<?php echo $this->prefix.'embed_media' ?>" value="<?php echo $header_selected; ?>" id="<?php echo $this->prefix.'embed_media' ?>" class="postbox" />
            <?php

        }

        
        
        function galery_setting( $post ) {

           wp_nonce_field( basename(__FILE__), 'gallery_meta_nonce' );
           $ids = get_post_meta($post->ID, $this->prefix.'gallery_id', true);
            ?>
             <table class="form-table ova_metabox_gallery">
                <tr>
                    <td>
                        <a class="gallery-add button" href="#" data-uploader-title="<?php esc_html_e( 'Add Images', 'ova-framework' ) ?>" data-uploader-button-text="<?php esc_html_e( 'Add Images', 'ova-framework' ) ?>">
                            <?php esc_html_e( 'Add Images', 'ova-framework' ) ?>
                        </a>

                        <ul id="gallery-metabox-list">
                            <?php if ($ids) : foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value); ?>
                            <li>
                               <input type="hidden" name="<?php echo $this->prefix.'gallery_id';?>[<?php echo $key; ?>]" value="<?php echo $value; ?>">

                               <img class="image-preview" src="<?php echo $image[0]; ?>">

                               <a class="change-image button button-small" href="#" data-uploader-title="<?php esc_html_e( 'Change Image', 'ova-framework' ) ?>" data-uploader-button-text="<?php esc_html_e( 'Change Image', 'ova-framework' ) ?>" title="<?php esc_html_e( 'Delete image', 'ova-framework' ); ?>">
                                    <?php esc_html_e( 'Change', 'ova-framework' ) ?>
                                </a>
                                <br>
                               <small>
                                <a class="remove-image" href="#" title="<?php esc_html_e( 'Delete image', 'ova-framework' ); ?>">
                                    <?php esc_html_e( 'Delete', 'ova-framework' ); ?>
                                </a>
                                </small>
                            </li>
                            <?php endforeach; endif; ?>
                        </ul>

                    </td>
                </tr>
            </table>
         <?php
        }
       

    }
}



return new Tripgo_Metaboxes();

