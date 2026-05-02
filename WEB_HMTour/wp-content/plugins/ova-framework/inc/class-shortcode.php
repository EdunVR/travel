<?php if (!defined( 'ABSPATH' )) exit;

if( !class_exists('Tripgo_Shortcode') ){
    
    class Tripgo_Shortcode {

        public function __construct() {

            add_shortcode( 'tripgo-elementor-template', array( $this, 'tripgo_elementor_template' ) );
            
        }

        public function tripgo_elementor_template( $atts ){

            $atts = extract( shortcode_atts(
            array(
                'id'  => '',
            ), $atts) );

            $args = array(
                'id' => $id
                
            );

            if( did_action( 'elementor/loaded' ) ){
                return Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id );    
            }
            return;

            
        }

        

    }
}



return new Tripgo_Shortcode();

