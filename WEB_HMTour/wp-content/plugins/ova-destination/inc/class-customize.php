<?php if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Ova_Destination_Customize' ) ) {
	class Ova_Destination_Customize {

		public function __construct() {
			add_action( 'customize_register', array( $this, 'ova_destination_customize_register' ) );
		}

		public function ova_destination_customize_register( $wp_customize ) {
			$this->ova_destination_init( $wp_customize );
			do_action( 'ova_destination_customize_register', $wp_customize );
		}

		/* Destination */
		public function ova_destination_init( $wp_customize ) {
			$wp_customize->add_panel( 'ova_destination_section', array(
			    'title' 	=> esc_html__( 'Destinations', 'ova-destination' ),
			    'priority' 	=> 5,
			));

			$wp_customize->add_section( 'ova_destination_section_archive' , array(
			    'title' 	=> esc_html__( 'Archive', 'ova-destination' ),
			    'priority'	=> 30,
			    'panel' 	=> 'ova_destination_section',
			));

				$wp_customize->add_setting( 'ova_destination_total_record', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'default' 			=> '7',
					'transport' 		=> 'refresh', // or postMessage
					'sanitize_callback' => 'sanitize_text_field' // Get function name
				));
				
				$wp_customize->add_control('ova_destination_total_record', array(
					'label' 	=> esc_html__('Number of posts per page','ova-destination'),
					'section' 	=> 'ova_destination_section_archive',
					'settings' 	=> 'ova_destination_total_record',
					'type' 		=>'number'
				));	

				$wp_customize->add_setting( 'archive_destination_template', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'default' 			=> 'template1',
					'transport' 		=> 'refresh', // or postMessage
					'sanitize_callback' => 'sanitize_text_field' // Get function name
				));

				$wp_customize->add_control('archive_destination_template', array(
					'label' 	=> esc_html__('Template','ova-destination'),
					'section' 	=> 'ova_destination_section_archive',
					'settings' 	=> 'archive_destination_template',
					'type' 		=>'select',
					'choices' 	=> array(
						'template1' => esc_html__('Template 1', 'ova-destination'),
						'template2' => esc_html__('Template 2', 'ova-destination'),
						'template3' => esc_html__('Template 3', 'ova-destination'),
					)
				));

				$wp_customize->add_setting( 'header_archive_destination', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'default' 			=> 'default',
					'transport' 		=> 'refresh', // or postMessage
					'sanitize_callback' => 'sanitize_text_field' // Get function name
				));

				$wp_customize->add_control('header_archive_destination', array(
					'label' 	=> esc_html__('Header Archive','ova-destination'),
					'section' 	=> 'ova_destination_section_archive',
					'settings' 	=> 'header_archive_destination',
					'type' 		=>'select',
					'choices' 	=> apply_filters('tripgo_list_header', '')
				));

				$wp_customize->add_setting( 'archive_footer_destination', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'default' 			=> 'default',
					'transport' 		=> 'refresh', // or postMessage
					'sanitize_callback' => 'sanitize_text_field' // Get function name
				));

				$wp_customize->add_control('archive_footer_destination', array(
					'label' 	=> esc_html__('Footer Archive','ova-destination'),
					'section' 	=> 'ova_destination_section_archive',
					'settings' 	=> 'archive_footer_destination',
					'type' 		=>'select',
					'choices' 	=> apply_filters('tripgo_list_footer', '')
				));

			$wp_customize->add_section( 'ova_destination_section_single' , array(
			    'title' 	=> esc_html__( 'Single', 'ova-destination' ),
			    'priority' 	=> 30,
			    'panel' 	=> 'ova_destination_section',
			));

			    $wp_customize->add_setting( 'single_destination_template', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'default' 			=> 'template1',
					'transport' 		=> 'refresh', // or postMessage
					'sanitize_callback' => 'sanitize_text_field' // Get function name
				));

				$wp_customize->add_control('single_destination_template', array(
					'label' 	=> esc_html__('Template','ova-destination'),
					'section' 	=> 'ova_destination_section_single',
					'settings' 	=> 'single_destination_template',
					'type' 		=>'select',
					'choices' 	=> array(
						'template1' => esc_html__('Template 1', 'ova-destination'),
						'template2' => esc_html__('Template 2', 'ova-destination'),
					)
				));

				$wp_customize->add_setting( 'single_detail_background_destination', array(
				    'type' 				=> 'theme_mod', // or 'option'
				    'capability' 		=> 'edit_theme_options',
				    'theme_supports' 	=> '', // Rarely needed.
				    'transport' 		=> 'refresh', // or postMessage
				    'sanitize_callback' => 'sanitize_text_field', // Get function name 
				));

				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize,'single_detail_background_destination', array(
					'label' 	=> esc_html__('Background Content Single Template1','ova-destination'),
					'section' 	=> 'ova_destination_section_single',
					'settings' 	=> 'single_detail_background_destination',
					'mime_type' => 'image',
				)));

			    $wp_customize->add_setting( 'single_related_destination_tour', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'default' 			=> 'yes',
					'transport' 		=> 'refresh', // or postMessage
					'sanitize_callback' => 'sanitize_text_field' // Get function name
				));

				$wp_customize->add_control('single_related_destination_tour', array(
					'label' 	=> esc_html__('Show Related Destination Tour','ova-destination'),
					'section' 	=> 'ova_destination_section_single',
					'settings' 	=> 'single_related_destination_tour',
					'type' 		=>'select',
					'choices' 	=> array(
						'yes' 	=> esc_html__('Yes', 'ova-destination'),
						'no' 	=> esc_html__('No', 'ova-destination'),
					)
				));

				$wp_customize->add_setting( 'header_single_destination', array(
				    'type' 				=> 'theme_mod', // or 'option'
				    'capability' 		=> 'edit_theme_options',
				    'theme_supports' 	=> '', // Rarely needed.
				    'default' 			=> 'default',
				    'transport' 		=> 'refresh', // or postMessage
				    'sanitize_callback' => 'sanitize_text_field' // Get function name  
				));

				$wp_customize->add_control('header_single_destination', array(
					'label' 	=> esc_html__('Header Single','ova-destination'),
					'section' 	=> 'ova_destination_section_single',
					'settings' 	=> 'header_single_destination',
					'type' 		=>'select',
					'choices' 	=> apply_filters('tripgo_list_header', '')
				));

				$wp_customize->add_setting( 'single_footer_destination', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'default' 			=> 'default',
					'transport' 		=> 'refresh', // or postMessage
					'sanitize_callback' => 'sanitize_text_field' // Get function name
				));

				$wp_customize->add_control('single_footer_destination', array(
					'label' 	=> esc_html__('Footer Single','ova-destination'),
					'section' 	=> 'ova_destination_section_single',
					'settings' 	=> 'single_footer_destination',
					'type' 		=>'select',
					'choices' 	=> apply_filters('tripgo_list_footer', '')
				));
		}
	}
}

new Ova_Destination_Customize();