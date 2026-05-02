<?php if (!defined( 'ABSPATH' )) exit;

if (!class_exists( 'Tripgo_Customize' )){

	class Tripgo_Customize {
		
		public function __construct() {
	        add_action( 'customize_register', array( $this, 'tripgo_customize_register' ) );
	    }

	    public function tripgo_customize_register($wp_customize) {
	        
	        $this->tripgo_init_remove_setting( $wp_customize );
	        $this->tripgo_init_ova_typography( $wp_customize );
	        $this->tripgo_init_ova_color( $wp_customize );
	        $this->tripgo_init_ova_layout( $wp_customize );
	        $this->tripgo_init_ova_header( $wp_customize );
	        $this->tripgo_init_ova_footer( $wp_customize );
	        $this->tripgo_init_ova_blog( $wp_customize );
	        

	        if( tripgo_is_woo_active() ){
	        	$this->tripgo_init_ova_tour( $wp_customize );
	        	$this->tripgo_init_ova_woo( $wp_customize );	
	        }
	   
	        do_action( 'tripgo_customize_register', $wp_customize );
	    }

	    public function tripgo_init_remove_setting( $wp_customize ){
	    	/* Remove Colors &  Header Image Customize */
			$wp_customize->remove_section('colors');
			$wp_customize->remove_section('header_image');

			$wp_customize->add_setting( 'logo', array(
		      	'type' => 'theme_mod', // or 'option'
		      	'capability' => 'edit_theme_options',
		      	'theme_supports' => '', // Rarely needed.
		      	'default' => '',
		      	'transport' => 'refresh', // or postMessage
		      	'sanitize_callback' => 'sanitize_text_field' // Get function name 
		    ) );

		    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo', array(
		        'label'    => esc_html__( 'Logo Default', 'tripgo' ),
		        'section'  => 'title_tagline',
		        'settings' => 'logo'
		    )));
	    }
	   
	    
	    /* Typo */
	    public function tripgo_init_ova_typography($wp_customize){


    		/* Body Pane ******************************/
			$wp_customize->add_section( 'typo_general' , array(
			    'title'      => esc_html__( 'Typography', 'tripgo' ),
			    'priority'   => 1,
			    // 'panel' => 'typo_panel',
			) );


				/* General Typo */
				$wp_customize->add_setting( 'general_heading', array(
				  'default' => '',
				  'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );


				/* Message */
				$wp_customize->add_setting( 'text_typo_message', array(
				  'type' => 'theme_mod', // or 'option'
				  'capability' => 'edit_theme_options',
				  'theme_supports' => '', // Rarely needed.
				  'default' => '',
				  'transport' => 'refresh', // or postMessage
				  'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new Tripgo_Customize_Control_Heading( 
					$wp_customize, 
					'text_typo_message', 
					array(
						'label'          => esc_html__('Text Font','tripgo'),
			            'section'        => 'typo_general',
			            'settings'       => 'text_typo_message',
					) )
				);

					/* Font Size */
					$wp_customize->add_setting( 'general_font_size', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => '16px',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('general_font_size', array(
						'label' => esc_html__('Font Size','tripgo'),
						'description' => esc_html__('Example: 16px, 1.2em','tripgo'),
						'section' => 'typo_general',
						'settings' => 'general_font_size',
						'type' 		=>'text'
					));

					/* Line Height */
					$wp_customize->add_setting( 'general_line_height', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => '1.63em',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('general_line_height', array(
						'label' => esc_html__('Line height','tripgo'),
						'description' => esc_html__('Recommend use em. Example: 1.6em, 23px','tripgo'),
						'section' => 'typo_general',
						'settings' => 'general_line_height',
						'type' 		=>'text'
					));

					/* Letter Space */
					$wp_customize->add_setting( 'general_letter_space', array(
					  'type' => 'theme_mod', // or 'option'
					  'capability' => 'edit_theme_options',
					  'theme_supports' => '', // Rarely needed.
					  'default' => '0px',
					  'transport' => 'refresh', // or postMessage
					  'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('general_letter_space', array(
						'label' => esc_html__('Letter Spacing','tripgo'),
						'description' => esc_html__('Example: 0px, 0.5em','tripgo'),
						'section' => 'typo_general',
						'settings' => 'general_letter_space',
						'type' 		=>'text'
					));


			$wp_customize->add_control(
				new Tripgo_Customize_Control_Heading( 
				$wp_customize, 
				'general_heading', 
				array(
					'label'          => esc_html__('Primary Font','tripgo'),
		            'section'        => 'typo_general',
		            'settings'       => 'general_heading',
				) )
			);


			/* General Font */
			$wp_customize->add_setting( 'primary_font',
				array(
					'default' => tripgo_default_primary_font(),
					'sanitize_callback' => 'tripgo_google_font_sanitization'
				)
			);

				$wp_customize->add_control( new Tripgo_Google_Font_Select_Custom_Control( $wp_customize, 'primary_font',
					array(
						'label' => esc_html__( 'Primary Font', 'tripgo' ),
						'section' => 'typo_general',
						'input_attrs' => array(
							'font_count' => 'all',
							'orderby' => 'popular',
						),
					)
				) );		

			/* Custom Font */
			/* Message */
			$wp_customize->add_setting( 'custom_font_message', array(
			  'type' => 'theme_mod', // or 'option'
			  'capability' => 'edit_theme_options',
			  'theme_supports' => '', // Rarely needed.
			  'default' => '',
			  'transport' => 'refresh', // or postMessage
			  'sanitize_callback' => 'sanitize_text_field' // Get function name 
			) );

			$wp_customize->add_control(
				new Tripgo_Customize_Control_Heading( 
				$wp_customize, 
				'custom_font_message', 
				array(
					'label'          => esc_html__('Custom Font','tripgo'),
		            'section'        => 'typo_general',
		            'settings'       => 'custom_font_message',
				) )
			);


				$wp_customize->add_control(
					new Tripgo_Customize_Control_Heading( 
					$wp_customize, 
					'custom_font_message', 
					array(
						'label'          => esc_html__('Custom Font','tripgo'),
			            'section'        => 'typo_general',
			            'settings'       => 'custom_font_message',
					) )
				);

				$wp_customize->add_setting( 'ova_custom_font', array(
				  'type' => 'theme_mod', // or 'option'
				  'capability' => 'edit_theme_options',
				  'theme_supports' => '', // Rarely needed.
				  'default' => '["HK Grotesk", "300:400:500:600:700:800:900"]',
				  'transport' => 'refresh', // or postMessage
				  'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control('ova_custom_font', array(
					'label' => esc_html__('Custom Font','tripgo'),
					'description' => esc_html__('Step 1: Insert font-face in style.css file: Refer https://www.w3schools.com/cssref/css3_pr_font-face_rule.asp. Step 2: Insert font-family and font-weight like format: 
						["Perpetua", "Regular:Bold:Italic:Light"] | ["Name-Font", "Regular:Bold:Italic:Light"]. Step 3: Refresh customize page to display new font in dropdown font field.','tripgo'),
					'section' => 'typo_general',
					'settings' => 'ova_custom_font',
					'type' =>'textarea'
				));

	    }


	    /* Color */
	    public function tripgo_init_ova_color( $wp_customize ){

	    	/* Body Pane ******************************/
			$wp_customize->add_section( 'color_section' , array(
			    'title'      => esc_html__( 'Color', 'tripgo' ),
			    'priority'   => 2,
			) );

				$wp_customize->add_setting( 'primary_color', array(
					'type' 				=> 'theme_mod', // or 'option'
					'capability' 		=> 'edit_theme_options',
					'theme_supports' 	=> '', // Rarely needed.
					'transport' 		=> 'refresh', // or postMessage
					'default'			=> '#FD4C5C',
					'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'primary_color', 
					array(
						'label' 	=> esc_html__("Primary",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'primary_color',

					) ) 
				);

				$wp_customize->add_setting( 'primary_color_hover', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'transport' 		=> 'refresh', // or postMessage
				  	'default'			=> '#E64251',
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'primary_color_hover', 
					array(
						'label' 	=> esc_html__("Primary Hover",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'primary_color_hover',
					) ) 
				);


				$wp_customize->add_setting( 'secondary_color', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#00BB98',
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'secondary_color', 
					array(
						'label' 	=> esc_html__("Secondary",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'secondary_color',
					) ) 
				);

				$wp_customize->add_setting( 'secondary_color_hover', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				 	'default'			=> '#05977C',
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'secondary_color_hover', 
					array(
						'label' 	=> esc_html__("Secondary Hover",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'secondary_color_hover',
					) ) 
				);

				$wp_customize->add_setting( 'heading_color', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#111B19',		
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'heading_color', 
					array(
						'label' 	=> esc_html__("Heading",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'heading_color',
					) ) 
				);

				$wp_customize->add_setting( 'text_color', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#444444',
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'text_color', 
					array(
						'label' 	=> esc_html__("Text",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'text_color',
					) ) 
				);

				$wp_customize->add_setting( 'light_color', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#999999',	
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'light_color', 
					array(
						'label' 	=> esc_html__("Light",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'light_color',
					) ) 
				);

				$wp_customize->add_setting( 'border_color', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#E6E6E6',	
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'border_color', 
					array(
						'label' 	=> esc_html__("Border",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'border_color',
					) ) 
				);

				$wp_customize->add_setting( 'first_background', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#F5F5F5',	
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'first_background', 
					array(
						'label' 	=> esc_html__("First Background",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'first_background',
					) ) 
				);

				$wp_customize->add_setting( 'second_background', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#F2FBFA',	
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'second_background', 
					array(
						'label' 	=> esc_html__("Second Background",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'second_background',
					) ) 
				);

				$wp_customize->add_setting( 'third_background', array(
				  	'type' 				=> 'theme_mod', // or 'option'
				  	'capability' 		=> 'edit_theme_options',
				  	'theme_supports' 	=> '', // Rarely needed.
				  	'default'			=> '#1A1A3D',	
				  	'transport' 		=> 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize, 
					'third_background', 
					array(
						'label'		=> esc_html__("Third Background",'tripgo'),
			            'section' 	=> 'color_section',
			            'settings' 	=> 'third_background',
					) ) 
				);
	    }


	    /* Layout */
	    public function tripgo_init_ova_layout( $wp_customize ){

	    	$wp_customize->add_section( 'layout_section' , array(
			    'title'      => esc_html__( 'Layout', 'tripgo' ),
			    'priority'   => 2,
			) );

				$wp_customize->add_setting( 'global_boxed_container_width', array(
				  	'type' => 'theme_mod', // or 'option'
				  	'capability' => 'edit_theme_options',
				  	'theme_supports' => '', // Rarely needed.
				  	'default' => '1290',
				  	'transport' => 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name   
				) );

				$wp_customize->add_control('global_boxed_container_width', array(
					'label' => esc_html__('Container (px)','tripgo'),
					'section' => 'layout_section',
					'settings' => 'global_boxed_container_width',
					'type' =>'number',
					'default' => '1290'
				));

				$wp_customize->add_setting( 'global_layout', array(
				  	'type' => 'theme_mod', // or 'option'
				  	'capability' => 'edit_theme_options',
				  	'theme_supports' => '', // Rarely needed.
				   	'default' => 'layout_2r',
				   	'transport' => 'refresh', // or postMessage
				   	'sanitize_callback' => 'sanitize_text_field' // Get function name  
				) );

				$wp_customize->add_control('global_layout', array(
					'label' => esc_html__('Layout','tripgo'),
					'section' => 'layout_section',
					'settings' => 'global_layout',
					'type' =>'select',
					'choices' => apply_filters( 'tripgo_define_layout', '' )
				));

				$wp_customize->add_setting( 'global_sidebar_width', array(
				  	'type' => 'theme_mod', // or 'option'
				  	'capability' => 'edit_theme_options',
				  	'theme_supports' => '', // Rarely needed.
				  	'default' => '320',
				  	'transport' => 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control('global_sidebar_width', array(
					'label' => esc_html__('Sidebar Width (px)','tripgo'),
					'section' => 'layout_section',
					'settings' => 'global_sidebar_width',
					'type' =>'number'
				));
				

				$wp_customize->add_setting( 'global_wide_site', array(
				  	'type' => 'theme_mod', // or 'option'
				  	'capability' => 'edit_theme_options',
				  	'theme_supports' => '', // Rarely needed.
				  	'default' => 'wide',
				  	'transport' => 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name   
				) );

				$wp_customize->add_control('global_wide_site', array(
					'label' => esc_html__('Wide Site','tripgo'),
					'section' => 'layout_section',
					'settings' => 'global_wide_site',
					'type' =>'select',
					'choices' => apply_filters('tripgo_define_wide_boxed', '')
				));

				
				$wp_customize->add_setting( 'global_boxed_offset', array(
				  	'type' => 'theme_mod', // or 'option'
				  	'capability' => 'edit_theme_options',
				  	'theme_supports' => '', // Rarely needed.
				  	'default' => '20',
				  	'transport' => 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control('global_boxed_offset', array(
					'label' => esc_html__('Boxed Offset (px)','tripgo'),
					'section' => 'layout_section',
					'settings' => 'global_boxed_offset',
					'type' =>'number',
					'default' => '20'
				));

	    }

	    /* Header */
	    public function tripgo_init_ova_header( $wp_customize ){

	    	$wp_customize->add_section( 'header_section' , array(
			    'title'      => esc_html__( 'Header', 'tripgo' ),
			    'priority'   => 3,
			) );

				$wp_customize->add_setting( 'global_header', array(
				  	'type' => 'theme_mod', // or 'option'
				  	'capability' => 'edit_theme_options',
				  	'theme_supports' => '', // Rarely needed.
				  	'default' => 'default',
				  	'transport' => 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
				) );

				$wp_customize->add_control('global_header', array(
					'label' => esc_html__('Header Default','tripgo'),
					'description' => esc_html__('This isn\'t effect in Blog' ,'tripgo'),
					'section' => 'header_section',
					'settings' => 'global_header',
					'type' =>'select',
					'choices' => apply_filters('tripgo_list_header', '')
				));

	    }

	    /* Footer */
	    public function tripgo_init_ova_footer( $wp_customize ){

	    	$wp_customize->add_section( 'footer_section' , array(
			    'title'      => esc_html__( 'Footer', 'tripgo' ),
			    'priority'   => 4,
			) );

				$wp_customize->add_setting( 'global_footer', array(
				  	'type' => 'theme_mod', // or 'option'
				  	'capability' => 'edit_theme_options',
				  	'theme_supports' => '', // Rarely needed.
				  	'default' => 'default',
				  	'transport' => 'refresh', // or postMessage
				  	'sanitize_callback' => 'sanitize_text_field' // Get function name 		  
				) );

				$wp_customize->add_control('global_footer', array(
					'label' => esc_html__('Footer Default','tripgo'),
					'description' => esc_html__('This isn\'t effect in Blog' ,'tripgo'),
					'section' => 'footer_section',
					'settings' => 'global_footer',
					'type' =>'select',
					'choices' => apply_filters('tripgo_list_footer', '')
				));

	    }


	    /* Blog */
	    public function tripgo_init_ova_blog( $wp_customize ){

	    	$wp_customize->add_panel( 'blog_panel', array(
			    'title'    => esc_html__( 'Blog', 'tripgo' ),
			    'priority' => 5,
			) );

				$wp_customize->add_section( 'blog_section' , array(
				    'title'      => esc_html__( 'Archive', 'tripgo' ),
				    'priority'   => 30,
				    'panel' => 'blog_panel',
				) );

					$wp_customize->add_setting( 'blog_template', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'default',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );

					$wp_customize->add_control('blog_template', array(
						'label' => esc_html__('Type','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_template',
						'type' =>'select',
						'choices' => array(
							'default' => esc_html__('Default', 'tripgo'),
							'grid'	  => esc_html__('Grid', 'tripgo'),
							'masonry' => esc_html__('Masonry', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_archive_show_media', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_archive_show_media', array(
						'label' => esc_html__('Show Media','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_media',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_archive_show_title', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name   
					) );
					
					$wp_customize->add_control('blog_archive_show_title', array(
						'label' => esc_html__('Show Title','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_title',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_archive_show_date', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_archive_show_date', array(
						'label' => esc_html__('Show Date','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_date',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_archive_show_cat', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_archive_show_cat', array(
						'label' => esc_html__('Show Category','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_cat',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'		=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_archive_show_author', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'no',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_archive_show_author', array(
						'label' => esc_html__('Show Author','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_author',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_archive_show_comment', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_archive_show_comment', array(
						'label' => esc_html__('Show Comment','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_comment',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));


					$wp_customize->add_setting( 'blog_archive_show_excerpt', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_archive_show_excerpt', array(
						'label' => esc_html__('Show Excerpt','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_excerpt',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_archive_show_readmore', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_archive_show_readmore', array(
						'label' => esc_html__('Show Read More','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_archive_show_readmore',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));


					$wp_customize->add_setting( 'blog_layout', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'layout_2r',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );

					$wp_customize->add_control('blog_layout', array(
						'label' => esc_html__('Layout','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_layout',
						'type' =>'select',
						'choices' => apply_filters( 'tripgo_define_layout', '' )
					));
					

					$wp_customize->add_setting( 'blog_header', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'default',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );

					$wp_customize->add_control('blog_header', array(
						'label' => esc_html__('Header','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_header',
						'type' =>'select',
						'choices' => apply_filters('tripgo_list_header', '')
					));

					$wp_customize->add_setting( 'blog_footer', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'default',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );

					$wp_customize->add_control('blog_footer', array(
						'label' => esc_html__('Footer','tripgo'),
						'section' => 'blog_section',
						'settings' => 'blog_footer',
						'type' =>'select',
						'choices' => apply_filters('tripgo_list_footer', '')
					));


				$wp_customize->add_section( 'single_section' , array(
				    'title'      => esc_html__( 'Single', 'tripgo' ),
				    'priority'   => 30,
				    'panel' => 'blog_panel',
				) );	

					$wp_customize->add_setting( 'single_layout', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'layout_2r',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );

					$wp_customize->add_control('single_layout', array(
						'label' => esc_html__('Layout','tripgo'),
						'section' => 'single_section',
						'settings' => 'single_layout',
						'type' =>'select',
						'choices' => apply_filters( 'tripgo_define_layout', '' )
					));

					$wp_customize->add_setting( 'blog_single_show_media', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name  
					) );
					
					$wp_customize->add_control('blog_single_show_media', array(
						'label' => esc_html__('Show Media','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_media',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_title', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_single_show_title', array(
						'label' => esc_html__('Show Title','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_title',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_date', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name   
					) );
					
					$wp_customize->add_control('blog_single_show_date', array(
						'label' => esc_html__('Show Date','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_date',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_cat', array(
					  'type' => 'theme_mod', // or 'option'
					  'capability' => 'edit_theme_options',
					  'theme_supports' => '', // Rarely needed.
					  'default' => 'yes',
					  'transport' => 'refresh', // or postMessage
					  'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_single_show_cat', array(
						'label' => esc_html__('Show Category','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_cat',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_author', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'no',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_single_show_author', array(
						'label' => esc_html__('Show Author','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_author',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_comment', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_single_show_comment', array(
						'label' => esc_html__('Show Comment','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_comment',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_content', array(
						'type' 				=> 'theme_mod', // or 'option'
						'capability' 		=> 'edit_theme_options',
						'theme_supports' 	=> '', // Rarely needed.
						'default' 			=> 'yes',
						'transport' 		=> 'refresh', // or postMessage
						'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_single_show_content', array(
						'label' 	=> esc_html__('Show Content','tripgo'),
						'section' 	=> 'single_section',
						'settings' 	=> 'blog_single_show_content',
						'type' 		=>'select',
						'choices' 	=> array(
							'yes' 	=> esc_html__('Yes', 'tripgo'),
							'no' 	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_tag', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name  
					) );
					
					$wp_customize->add_control('blog_single_show_tag', array(
						'label' => esc_html__('Show Tag','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_tag',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_share_social_icon', array(
					  'type' => 'theme_mod', // or 'option'
					  'capability' => 'edit_theme_options',
					  'theme_supports' => '', // Rarely needed.
					  'default' => 'yes',
					  'transport' => 'refresh', // or postMessage
					  'sanitize_callback' => 'sanitize_text_field' // Get function name  
					) );
					
					$wp_customize->add_control('blog_single_share_social_icon', array(
						'label' => esc_html__('Show Share Social Icon','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_share_social_icon',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'blog_single_show_next_prev_post', array(
					  'type' => 'theme_mod', // or 'option'
					  'capability' => 'edit_theme_options',
					  'theme_supports' => '', // Rarely needed.
					  'default' => 'yes',
					  'transport' => 'refresh', // or postMessage
					  'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_single_show_next_prev_post', array(
						'label' => esc_html__('Show Next Prev Post','tripgo'),
						'section' => 'single_section',
						'settings' => 'blog_single_show_next_prev_post',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'	=> esc_html__('No', 'tripgo'),
						)
					));	

					$wp_customize->add_setting( 'blog_single_show_leave_a_reply', array(
						'type' 				=> 'theme_mod', // or 'option'
						'capability' 		=> 'edit_theme_options',
						'theme_supports' 	=> '', // Rarely needed.
						'default' 			=> 'yes',
						'transport' 		=> 'refresh', // or postMessage
						'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('blog_single_show_leave_a_reply', array(
						'label' 	=> esc_html__('Show Leave a Reply','tripgo'),
						'section' 	=> 'single_section',
						'settings' 	=> 'blog_single_show_leave_a_reply',
						'type' 		=>'select',
						'choices' 	=> array(
							'yes' 	=> esc_html__('Yes', 'tripgo'),
							'no' 	=> esc_html__('No', 'tripgo'),
						)
					));				

					$wp_customize->add_setting( 'single_header', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'default',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );

					$wp_customize->add_control('single_header', array(
						'label' => esc_html__('Header','tripgo'),
						'section' => 'single_section',
						'settings' => 'single_header',
						'type' =>'select',
						'choices' => apply_filters('tripgo_list_header', '')
					));

					$wp_customize->add_setting( 'single_footer', array(
					  'type' => 'theme_mod', // or 'option'
					  'capability' => 'edit_theme_options',
					  'theme_supports' => '', // Rarely needed.
					  'default' => 'default',
					  'transport' => 'refresh', // or postMessage
					  'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );

					$wp_customize->add_control('single_footer', array(
						'label' => esc_html__('Footer','tripgo'),
						'section' => 'single_section',
						'settings' => 'single_footer',
						'type' =>'select',
						'choices' => apply_filters('tripgo_list_footer', '')
					));

	    }

	    /* Tour */
	    public function tripgo_init_ova_tour( $wp_customize ){
            $wp_customize->add_panel( 'tour_panel', array(
			    'title'    => esc_html__( 'Tour', 'tripgo' ),
			    'priority' => 6,
			) );

				$wp_customize->add_section( 'tour_archive_section' , array(
				    'title'      => esc_html__( 'Archive', 'tripgo' ),
				    'priority'   => 30,
				    'panel' => 'tour_panel',
				) );
				
					$wp_customize->add_setting( 'tour_archive_show_featured', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_featured', array(
						'label' => esc_html__('Show Featured','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_featured',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_wishlist', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_wishlist', array(
						'label' => esc_html__('Show Wishlist','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_wishlist',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_max_guest', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_max_guest', array(
						'label' => esc_html__('Show Max Guest','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_max_guest',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_duration', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_duration', array(
						'label' => esc_html__('Show Duration','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_duration',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_title', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_title', array(
						'label' => esc_html__('Show Title','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_title',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_location', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_location', array(
						'label' => esc_html__('Show Location','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_location',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_rating', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_rating', array(
						'label' => esc_html__('Show Rating','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_rating',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_price', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_price', array(
						'label' => esc_html__('Show Price','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_price',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_archive_show_explore_button', array(
					 	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_archive_show_explore_button', array(
						'label' => esc_html__('Show Button','tripgo'),
						'section' => 'tour_archive_section',
						'settings' => 'tour_archive_show_explore_button',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

				$wp_customize->add_section( 'tour_single_section' , array(
				    'title'      => esc_html__( 'Single', 'tripgo' ),
				    'priority'   => 30,
				    'panel' => 'tour_panel',
				    'description'  => esc_html__( 'Apply for Product Templates: Default', 'tripgo' ),
				) );	

					$wp_customize->add_setting( 'tour_single_show_title', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_title', array(
						'label' => esc_html__('Show Title','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_title',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_location', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_location', array(
						'label' => esc_html__('Show Location','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_location',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_rating', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_rating', array(
						'label' => esc_html__('Show Rating','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_rating',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_wishlist', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_wishlist', array(
						'label' => esc_html__('Show Wishlist','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_wishlist',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_video_button', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_video_button', array(
						'label' => esc_html__('Show Video Button','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_video_button',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));
					
					$wp_customize->add_setting( 'tour_single_show_gallery_button', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_gallery_button', array(
						'label' => esc_html__('Show Gallery Button','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_gallery_button',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_share_button', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_share_button', array(
						'label' => esc_html__('Show Share Button','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_share_button',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_gallery_slide', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_gallery_slide', array(
						'label' => esc_html__('Show Gallery Slide','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_gallery_slide',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));


					$wp_customize->add_setting( 'tour_single_show_features', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_features', array(
						'label' => esc_html__('Show Features','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_features',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));


					$wp_customize->add_setting( 'tour_single_show_description', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_description', array(
						'label' => esc_html__('Show Description','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_description',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_inc_exc', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_inc_exc', array(
						'label' => esc_html__('Show Included/Excluded','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_inc_exc',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_tour_plan', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_tour_plan', array(
						'label' => esc_html__('Show Tour Plan','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_tour_plan',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_map', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_map', array(
						'label' => esc_html__('Show Map','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_map',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_reviews', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_reviews', array(
						'label' => esc_html__('Show Reviews','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_reviews',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_form', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_form', array(
						'label' => esc_html__('Show Form','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_form',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_table_price', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_table_price', array(
						'label' => esc_html__('Show Table Price','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_table_price',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));

					$wp_customize->add_setting( 'tour_single_show_related', array(
					  	'type' => 'theme_mod', // or 'option'
					  	'capability' => 'edit_theme_options',
					  	'theme_supports' => '', // Rarely needed.
					  	'default' => 'yes',
					  	'transport' => 'refresh', // or postMessage
					  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
					) );
					
					$wp_customize->add_control('tour_single_show_related', array(
						'label' => esc_html__('Show Related','tripgo'),
						'section' => 'tour_single_section',
						'settings' => 'tour_single_show_related',
						'type' =>'select',
						'choices' => array(
							'yes' => esc_html__('Yes', 'tripgo'),
							'no'  => esc_html__('No', 'tripgo'),
						)
					));
            
	    }

	    /* Woo */
	    public function tripgo_init_ova_woo( $wp_customize ){
            
            /* Show/hide products by product type */
			$wp_customize->add_setting( 'woo_archive_display', array(
				'type'              => 'theme_mod', // or 'option'
				'capability'        => 'edit_theme_options',
				'theme_supports'    => '', // Rarely needed.
				'default'           => 'all',
				'transport'         => 'refresh', // or postMessage
				'sanitize_callback' => 'sanitize_text_field', // Get function name 	
			) );
            
			$wp_customize->add_control('woo_archive_display', array(
				'label'    => esc_html__('Shop products display','tripgo'),
				'section'  => 'woocommerce_product_catalog',
				'settings' => 'woo_archive_display',
				'type'     =>'select',
				'choices'  => array(
					'all' => esc_html__('All products', 'tripgo'),
					'ovabrw_car_rental' => esc_html__('Rental products', 'tripgo'),
					'not_rental' => esc_html__('Not Rental products', 'tripgo'),
				)
			));

			$wp_customize->add_setting( 'woo_archive_layout', array(
				'type'              => 'theme_mod', // or 'option'
				'capability'        => 'edit_theme_options',
				'theme_supports'    => '', // Rarely needed.
				'default'           => 'woo_layout_1c',
				'transport'         => 'refresh', // or postMessage
				'sanitize_callback' => 'sanitize_text_field' // Get function name 	  
			) );

			$wp_customize->add_control('woo_archive_layout', array(
				'label'    => esc_html__('Archive Layout','tripgo'),
				'section'  => 'woocommerce_product_catalog',
				'settings' => 'woo_archive_layout',
				'type'     =>'select',
				'choices'  => array(
					'woo_layout_1c' => esc_html__('No Sidebar', 'tripgo'),
					'woo_layout_2r' => esc_html__('Right Sidebar', 'tripgo'),
					'woo_layout_2l' => esc_html__('Left Sidebar', 'tripgo'),
				)
			));

			$wp_customize->add_setting( 'woo_archive_header', array(
			  	'type' 				=> 'theme_mod', // or 'option'
			  	'capability' 		=> 'edit_theme_options',
			  	'theme_supports' 	=> '', // Rarely needed.
			  	'default' 			=> 'default',
			  	'transport' 		=> 'refresh', // or postMessage
			  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
			) );

			$wp_customize->add_control('woo_archive_header', array(
				'label' 	=> esc_html__('Header','tripgo'),
				'section' 	=> 'woocommerce_product_catalog',
				'settings' 	=> 'woo_archive_header',
				'type' 		=>'select',
				'choices' 	=> apply_filters('tripgo_list_header', '')
			));

			$wp_customize->add_setting( 'woo_archive_footer', array(
				'type' 				=> 'theme_mod', // or 'option'
				'capability' 		=> 'edit_theme_options',
				'theme_supports' 	=> '', // Rarely needed.
				'default' 			=> 'default',
				'transport' 		=> 'refresh', // or postMessage
				'sanitize_callback' => 'sanitize_text_field' // Get function name
			) );

			$wp_customize->add_control('woo_archive_footer', array(
				'label' 	=> esc_html__('Footer','tripgo'),
				'section' 	=> 'woocommerce_product_catalog',
				'settings' 	=> 'woo_archive_footer',
				'type' 		=>'select',
				'choices' 	=> apply_filters('tripgo_list_footer', '')
			));

			$wp_customize->add_setting( 'woo_sidebar_width', array(
				'type'              => 'theme_mod', // or 'option'
				'capability'        => 'edit_theme_options',
				'theme_supports'    => '', // Rarely needed.
				'default'           => '320',
				'transport'         => 'refresh', // or postMessage
				'sanitize_callback' => 'sanitize_text_field' // Get function name
			) );

			$wp_customize->add_control('woo_sidebar_width', array(
				'label'    => esc_html__('Sidebar Width (px)','tripgo'),
				'section'  => 'woocommerce_product_catalog',
				'settings' => 'woo_sidebar_width',
				'type'     =>'number'
			));

			/* Show/hide title in category,tag */
			$wp_customize->add_setting( 'woo_archive_show_title', array(
				'type' => 'theme_mod', // or 'option'
				'capability' => 'edit_theme_options',
				'theme_supports' => '', // Rarely needed.
				'transport' => 'refresh', // or postMessage
				'sanitize_callback' => 'sanitize_text_field', // Get function name 
				'default'  => 'yes',
			) );

	    	$wp_customize->add_control('woo_archive_show_title', array(
	    		'label'    => esc_html__('Show/Hide Title','tripgo'),
	    		'section'  => 'woocommerce_product_catalog',
	    		'settings' => 'woo_archive_show_title',
	    		'type'     => 'select',
	    		'choices'  => array(
	    			'yes' => esc_html__('Yes', 'tripgo'),
	    			'no' => esc_html__('No', 'tripgo'),
	    		)
	    	));

	    	/* Product Detail */
			$wp_customize->add_section( 'product_detail' , array(
			    'title'      => esc_html__( 'Product detail', 'tripgo' ),
			    'priority'   => 30,
			    'panel' => 'woocommerce',
			) );

			$wp_customize->add_setting( 'woo_product_layout', array(
				'type'              => 'theme_mod', // or 'option'
				'capability'        => 'edit_theme_options',
				'theme_supports'    => '', // Rarely needed.
				'default'           => 'woo_layout_1c',
				'transport'         => 'refresh', // or postMessage
				'sanitize_callback' => 'sanitize_text_field' // Get function name 
			) );

			$wp_customize->add_control('woo_product_layout', array(
				'label'    => esc_html__('Single Layout','tripgo'),
				'section'  => 'product_detail',
				'settings' => 'woo_product_layout',
				'type'     =>'select',
				'choices'  => array(
					'woo_layout_1c' => esc_html__('No Sidebar', 'tripgo'),
					'woo_layout_2r' => esc_html__('Right Sidebar', 'tripgo'),
					'woo_layout_2l' => esc_html__('Left Sidebar', 'tripgo'),
				)
			));

			$wp_customize->add_setting( 'woo_single_header', array(
			  	'type' 				=> 'theme_mod', // or 'option'
			  	'capability' 		=> 'edit_theme_options',
			  	'theme_supports' 	=> '', // Rarely needed.
			  	'default' 			=> 'default',
			  	'transport' 		=> 'refresh', // or postMessage
			  	'sanitize_callback' => 'sanitize_text_field' // Get function name 
			) );

			$wp_customize->add_control('woo_single_header', array(
				'label' 	=> esc_html__('Header','tripgo'),
				'section' 	=> 'product_detail',
				'settings' 	=> 'woo_single_header',
				'type' 		=>'select',
				'choices' 	=> apply_filters('tripgo_list_header', '')
			));

			$wp_customize->add_setting( 'woo_single_footer', array(
				'type' 				=> 'theme_mod', // or 'option'
				'capability' 		=> 'edit_theme_options',
				'theme_supports' 	=> '', // Rarely needed.
				'default' 			=> 'default',
				'transport' 		=> 'refresh', // or postMessage
				'sanitize_callback' => 'sanitize_text_field' // Get function name
			) );

			$wp_customize->add_control('woo_single_footer', array(
				'label' 	=> esc_html__('Footer','tripgo'),
				'section' 	=> 'product_detail',
				'settings' 	=> 'woo_single_footer',
				'type' 		=>'select',
				'choices' 	=> apply_filters('tripgo_list_footer', '')
			));


	    	$wp_customize->add_setting( 'woo_product_detail_show_title', array(
			  	'type' => 'theme_mod', // or 'option'
			  	'capability' => 'edit_theme_options',
			  	'theme_supports' => '', // Rarely needed.
			  	'transport' => 'refresh', // or postMessage
			  	'sanitize_callback' => 'sanitize_text_field', // Get function name 
			  	'default'  => 'yes',
			) );

	    	$wp_customize->add_control('woo_product_detail_show_title', array(
	    		'label'    => esc_html__('Show/Hide Title','tripgo'),
	    		'section'  => 'product_detail',
	    		'settings' => 'woo_product_detail_show_title',
	    		'type'     => 'select',
	    		'choices'  => array(
	    			'yes' => esc_html__('Yes', 'tripgo'),
	    			'no' => esc_html__('No', 'tripgo'),
	    		)
	    	));
	    }

		
	}

}

new Tripgo_Customize();