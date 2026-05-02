<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Background;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Tripgo_Elementor_Video extends Widget_Base {

	
	public function get_name() {
		return 'tripgo_elementor_video';
	}

	
	public function get_title() {
		return esc_html__( 'Ova Video', 'tripgo' );
	}

	
	public function get_icon() {
		return 'eicon-play-o';
	}

	
	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ 'tripgo-elementor-video' ];
	}
	
	// Add Your Controll In This Function
	protected function register_controls() {

		/* Begin section icon */
		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Icon', 'tripgo' ),
			]
		);	

			// $this->add_control(
			// 	'icon_class',
			// 	[
			// 		'label' 	=> esc_html__( 'Icon Class', 'tripgo' ),
			// 		'type' 		=> Controls_Manager::TEXT,
			// 		'default' 	=> 'fa fa-play',
			// 	]
			// );

			$this->add_control(
				'icon_class',
				[
					'label' => esc_html__( 'Icon Class', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'fa fa-play',
						'library' => 'all',
					],
				]
			);

			$this->add_control(
				'icon_url_video',
				[
					'label' 	=> esc_html__( 'URL Video', 'tripgo' ),
					'type' 		=> Controls_Manager::TEXT,
					'placeholder' 	=> esc_html__( 'Enter your URL', 'tripgo' ) . ' (YouTube)',
					'default' 		=> 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				]
			);

			$this->add_control(
				'icon_text',
				[
					'label' 	=> esc_html__( 'Text', 'tripgo' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> esc_html__( '', 'tripgo' ),
				]
			);

			$this->add_control(
	            'link',
	            [
	                'label' 	=> esc_html__( 'Link', 'tripgo' ),
	                'type' 		=> Controls_Manager::URL,
	                'dynamic' 	=> [
	                    'active' => true,
	                ],
	                'condition' => [
	                	'icon_url_video' => '',
	                ],
	            ]
	        );

	        $this->add_control(
				'video_options',
				[
					'label' 	=> esc_html__( 'Video Options', 'tripgo' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'icon_url_video!' => '',
					],
				]
			);

			$this->add_control(
				'autoplay_video',
				[
					'label' 	=> esc_html__( 'Autoplay', 'tripgo' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'tripgo' ),
					'label_off' => esc_html__( 'No', 'tripgo' ),
					'default' 	=> 'yes',
					'condition' => [
						'icon_url_video!' => '',
					],
				]
			);

			$this->add_control(
				'mute_video',
				[
					'label' 	=> esc_html__( 'Mute', 'tripgo' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'tripgo' ),
					'label_off' => esc_html__( 'No', 'tripgo' ),
					'default' 	=> 'no',
					'condition' => [
						'icon_url_video!' => '',
					],
				]
			);

			$this->add_control(
				'loop_video',
				[
					'label' 	=> esc_html__( 'Loop', 'tripgo' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'tripgo' ),
					'label_off' => esc_html__( 'No', 'tripgo' ),
					'default' 	=> 'yes',
					'condition' => [
						'icon_url_video!' => '',
					],
				]
			);

			$this->add_control(
				'player_controls_video',
				[
					'label' 	=> esc_html__( 'Player Controls', 'tripgo' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'tripgo' ),
					'label_off' => esc_html__( 'No', 'tripgo' ),
					'default' 	=> 'yes',
					'condition' => [
						'icon_url_video!' => '',
					],
				]
			);

			$this->add_control(
				'modest_branding_video',
				[
					'label' 	=> esc_html__( 'Modest Branding', 'tripgo' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'tripgo' ),
					'label_off' => esc_html__( 'No', 'tripgo' ),
					'default' 	=> 'yes',
					'condition' => [
						'icon_url_video!' => '',
					],
				]
			);

			$this->add_control(
				'show_info_video',
				[
					'label' 	=> esc_html__( 'Show Info', 'tripgo' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'tripgo' ),
					'label_off' => esc_html__( 'No', 'tripgo' ),
					'default' 	=> 'no',
					'condition' => [
						'icon_url_video!' => '',
					],
				]
			);

		$this->end_controls_section();
		/* End section icon */

		/* Begin section icon style */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);	

			$this->start_controls_tabs( 'tabs_icon_style' );
				$this->start_controls_tab(
		            'tab_icon_normal',
		            [
		                'label' => esc_html__( 'Normal', 'tripgo' ),
		            ]
		        );

		        	$this->add_control(
			            'icon_color_normal',
			            [
			                'label' 	=> esc_html__( 'Color', 'tripgo' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-video .icon-content-view .content i' => 'color: {{VALUE}};',
			                ],
			            ]
			        );

			        $this->add_control(
			            'icon_background_normal',
			            [
			                'label' 	=> esc_html__( 'Background', 'tripgo' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-video .icon-content-view .content' => 'background-color: {{VALUE}};',
			                ],
			            ]
			        );

			        $this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'icon_bg_gradient_normal',
							'label' 	=> esc_html__( 'Background Gradient', 'tripgo' ),
							'types' 	=> [ 'gradient' ],
							'selector' 	=> '{{WRAPPER}} .ova-video .icon-content-view .content',
						]
					);

		        $this->end_controls_tab();

		        $this->start_controls_tab(
		            'tab_icon_hover',
		            [
		                'label' => esc_html__( 'Hover', 'tripgo' ),
		            ]
		        );

		        	$this->add_control(
			            'icon_color_hover',
			            [
			                'label' 	=> esc_html__( 'Color', 'tripgo' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-video .icon-content-view .content:hover i' => 'color: {{VALUE}};',
			                ],
			            ]
			        );

		        	$this->add_control(
			            'icon_background_hover',
			            [
			                'label' 	=> esc_html__( 'Background', 'tripgo' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-video .icon-content-view .content:hover' => 'background-color: {{VALUE}};',
			                ],     
			            ]
			        );

			        $this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'icon_bg_gradient_hover',
							'label' 	=> esc_html__( 'Background Gradient', 'tripgo' ),
							'types' 	=> [ 'gradient' ],
							'selector' 	=> '{{WRAPPER}} .ova-video .icon-content-view .content:hover',
						]
					);

		        $this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_responsive_control(
				'icon_width',
				[
					'label' 	=> esc_html__( 'Width', 'tripgo' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'unit' 	=> 'px',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 400,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' 	=> [ '%', 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-video .icon-content-view .content' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
					],
					'separator' => 'before'
				]
			);

			$this->add_responsive_control(
				'icon_height',
				[
					'label' 	=> esc_html__( 'Height', 'tripgo' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'unit' 	=> 'px',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 400,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' 	=> [ '%', 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-video .icon-content-view .content' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'icon_typography',
					'selector' 	=> '{{WRAPPER}} .ova-video .icon-content-view .content i',
				]
			);

			$this->add_group_control(
	            Group_Control_Border::get_type(), [
	                'name' 		=> 'icon_before_border',
	                'selector' 	=> '{{WRAPPER}} .ova-video .icon-content-view .content:after',
	                'separator' => 'before',  
	            ]
	        );

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'icon_box_shadow',
					'label' 	=> esc_html__( 'Box Shadow', 'tripgo' ),
					'selector' 	=> '{{WRAPPER}} .ova-video .icon-content-view .content',
				]
			);

	        $this->add_control(
	            'icon_border_radius',
	            [
	                'label' 		=> esc_html__( 'Border Radius', 'tripgo' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-video .icon-content-view .content'	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_responsive_control(
	            'content_margin',
	            [
	                'label' 		=> esc_html__( 'Margin', 'tripgo' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-video .icon-content-view .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	    $this->end_controls_section();

	    /* Begin text Style */
		$this->start_controls_section(
            'text_style',
            [
                'label' => esc_html__( 'Text', 'tripgo' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'text_typography',
					'selector' 	=> '{{WRAPPER}} .ova-video .icon-content-view .ova-text',
				]
			);

			$this->add_control(
				'text_color',
				[
					'label' 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-video .icon-content-view .ova-text, {{WRAPPER}} .ova-video .icon-content-view .ova-text a' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'text_color_hover',
				[
					'label' 	=> esc_html__( 'Color Hover', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-video .icon-content-view .ova-text:hover a, {{WRAPPER}} .ova-video .icon-content-view .ova-text:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
	            'text_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'tripgo' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-video .icon-content-view .ova-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

        $this->end_controls_section();
		/* End text style */
		
	}

	// Render Template Here
	protected function render() {

		$settings = $this->get_settings();

		$icon_class = $settings['icon_class'];
		$url_video 	= $settings['icon_url_video'];
		$icon_text 	= $settings['icon_text'];
		$link 		= $settings['link']['url'];
		$tg_blank 	= '';
		if ( $settings['link']['is_external'] == 'on' ) {
			$tg_blank = 'target="_blank"';
		}

		if ( ! $link ) {
			$url = $link;
		}

		$autoplay 	= $settings['autoplay_video'];
		$mute 		= $settings['mute_video'];
		$loop 		= $settings['loop_video'];
		$controls 	= $settings['player_controls_video'];
		$modest 	= $settings['modest_branding_video'];
		$show_info 	= $settings['show_info_video'];

		 ?>
         
         <div class="ova-video">
			<div class="icon-content-view video_active">
				<?php if ( ! empty( $url_video ) ) : ?>
					<div class="content video-btn" id="ova-video" 
						data-src="<?php echo esc_url( $url_video ); ?>" 
						data-autoplay="<?php echo esc_attr( $autoplay ); ?>" 
						data-mute="<?php echo esc_attr( $mute ); ?>" 
						data-loop="<?php echo esc_attr( $loop ); ?>" 
						data-controls="<?php echo esc_attr( $controls ); ?>" 
						data-modest="<?php echo esc_attr( $modest ); ?>" 
						data-show_info="<?php echo esc_attr( $show_info ); ?>"
					>
						<?php \Elementor\Icons_Manager::render_icon( $icon_class, [ 'aria-hidden' => 'true' ] ); ?>
					</div>
				<?php else: ?>
					<div class="content">
						<?php \Elementor\Icons_Manager::render_icon( $icon_class, [ 'aria-hidden' => 'true' ] ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $icon_text ): ?>
					<p class="ova-text">
						<?php if ( $url ): ?>
							<a href="<?php echo esc_url( $url ); ?>" <?php echo esc_html( $tg_blank ); ?>>
								<?php echo esc_html( $icon_text ); ?>
							</a>
						<?php else: ?>
							<?php echo esc_html( $icon_text ); ?>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
			<div class="modal-container">
				<div class="modal">
					<i class="ovaicon-cancel"></i>
					<iframe class="modal-video" allow="autoplay" allowFullScreen="allowFullScreen" frameBorder="0"></iframe>
				</div>
			</div>
		</div>
		 	
		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Video() );