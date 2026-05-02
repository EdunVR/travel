<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Border;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Tripgo_Elementor_Gallery_Slide extends Widget_Base {

	
	public function get_name() {
		return 'tripgo_elementor_gallery_slide';
	}

	
	public function get_title() {
		return esc_html__( 'Gallery Slide', 'tripgo' );
	}

	
	public function get_icon() {
		return 'eicon-slider-album';
	}

	
	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ 'tripgo-elementor-gallery-slide' ];
	}
	
	// Add Your Controll In This Function
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
			]
		);	

			$this->add_control(
				'template',
				[
					'label' => esc_html__( 'Template', 'tripgo' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'template1',
					'options' => [
						'template1' => esc_html__('Template 1', 'tripgo'),
						'template2' => esc_html__('Template 2', 'tripgo'),
					]
				]
			);

			$this->add_control(
				'show_icon',
				[
					'label' => esc_html__( 'Show Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			
			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'link',
				[
					'label' => esc_html__( 'Link', 'tripgo' ),
					'type' => Controls_Manager::URL,
					'dynamic' => [
						'active' => true,
					],
					'placeholder' => esc_html__( 'https://your-link.com', 'tripgo' ),
					'show_label' => true,
					'default' => [
						'url' => '#',
					],
				]
			);

			$repeater->add_control(
				'image',
				[
					'label'   => esc_html__( 'Choose Image', 'tripgo' ),
					'type'    => Controls_Manager::MEDIA,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);

			$repeater->add_control(
				'title',
				[
					'label' => esc_html__( 'Title', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Children Park', 'tripgo' ),
				]
			);

			$repeater->add_control(
				'category',
				[
					'label' => esc_html__( 'Category', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Favorite place', 'tripgo' ),
				]
			);

			$this->add_control(
				'tab_item',
				[
					'label'		=> esc_html__( 'Tabs', 'tripgo' ),
					'type'		=> Controls_Manager::REPEATER,
					'fields'  	=> $repeater->get_controls(),
					'default' 	=> [
						[
							'title' => esc_html__('Children Park', 'tripgo'),
						],
						[
							'title' => esc_html__('Metro Stations', 'tripgo'),
						],
						[
							'title' => esc_html__('Historical Building', 'tripgo'),
						],
						[
							'title' => esc_html__('New York City Museum', 'tripgo'),
						],
						[
							'title' => esc_html__('The Bund', 'tripgo'),
						],
					],
				]
			);

		$this->end_controls_section();

		/*****************************************************************
						START SECTION ADDITIONAL
		******************************************************************/

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => esc_html__( 'Additional Options', 'tripgo' ),
			]
		);

			$this->add_control(
				'margin_items',
				[
					'label'   => esc_html__( 'Margin Right Items', 'tripgo' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 30,
				]
				
			);

			$this->add_control(
				'stagePadding',
				[
					'label'   => esc_html__( 'Stage Padding', 'tripgo' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 0,
				]
				
			);

			$this->add_control(
				'item_number',
				[
					'label'       => esc_html__( 'Item Number', 'tripgo' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Number Item', 'tripgo' ),
					'default'     => 3,
				]
			);

			$this->add_control(
				'slides_to_scroll',
				[
					'label'       => esc_html__( 'Slides to Scroll', 'tripgo' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'tripgo' ),
					'default'     => 1,
				]
			);

			$this->add_control(
				'pause_on_hover',
				[
					'label'   => esc_html__( 'Pause on Hover', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);


			$this->add_control(
				'infinite',
				[
					'label'   => esc_html__( 'Infinite Loop', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label'   => esc_html__( 'Autoplay', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_speed',
				[
					'label'     => esc_html__( 'Autoplay Speed', 'tripgo' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 3000,
					'step'      => 500,
					'condition' => [
						'autoplay' => 'yes',
					],
					'frontend_available' => false,
				]
			);

			$this->add_control(
				'smartspeed',
				[
					'label'   => esc_html__( 'Smart Speed', 'tripgo' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 500,
				]
			);

			$this->add_control(
				'dot_control',
				[
					'label'   => esc_html__( 'Show Dots', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		/****************************  END SECTION ADDITIONAL *********************/
        
        $this->start_controls_section(
			'section_gallery_slide',
			[
				'label' => esc_html__( 'Image', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
            
            $this->add_responsive_control(
				'image_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'image_height',
				[
					'label' 	=> esc_html__( 'Height', 'tripgo' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 380,
							'max' => 600,
						],
					],
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img img' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'image_overlay_color',
				[
					'label'     => esc_html__( 'Overlay Color (Hover)', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .overlay' => 'background-color : {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'selector' => '{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .title',
				]
			);

			$this->add_control(
				'title_color',
				[
					'label'     => esc_html__( 'Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .title' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'title_bgcolor',
				[
					'label'     => esc_html__( 'Background Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .title' => 'background-color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'title_padding',
				[
					'label'      => esc_html__( 'Padding', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_category',
			[
				'label' => esc_html__( 'Category', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'category_typography',
					'selector' => '{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .category',
				]
			);

			$this->add_control(
				'category_color',
				[
					'label'     => esc_html__( 'Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .category' => 'color : {{VALUE}};',
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .category:before, {{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .category:after' => 'background-color : {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'category_bgcolor',
				[
					'label'     => esc_html__( 'Background Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .category' => 'background-color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'category_padding',
				[
					'label'      => esc_html__( 'Padding', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .info .category' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		// Icon
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'icon_normal_color',
				[
					'label' 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .view-detail' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'icon_hover_color',
				[
					'label' 	=> esc_html__( 'Color Hover', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .view-detail:hover' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'icon_normal_bgcolor',
				[
					'label' 	=> esc_html__( 'Background Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .view-detail' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'icon_hover_bgcolor',
				[
					'label' 	=> esc_html__( 'Background Color Hover', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-gallery-slide .gallery-slide-img .view-detail:hover' => 'background-color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

        /* Begin Dots Style */
		$this->start_controls_section(
            'dots_style',
            [
                'label' => esc_html__( 'Dots', 'tripgo' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
                'condition' => [
					'dot_control' => 'yes',
				]
            ]
        );

            $this->add_responsive_control(
				'dots_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-gallery-slide .owl-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'tabs_dots_style' );
				
				$this->start_controls_tab(
		            'tab_dots_normal',
		            [
		                'label' => esc_html__( 'Normal', 'tripgo' ),
		            ]
		        );

		            $this->add_control(
						'dot_color',
						[
							'label' 	=> esc_html__( 'Color', 'tripgo' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot span' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_responsive_control(
						'dots_width',
						[
							'label' 	=> esc_html__( 'Width', 'tripgo' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot span' => 'width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'dots_height',
						[
							'label' 	=> esc_html__( 'Height', 'tripgo' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot span' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
			            'dots_border_radius',
			            [
			                'label' 		=> esc_html__( 'Border Radius', 'tripgo' ),
			                'type' 			=> Controls_Manager::DIMENSIONS,
			                'size_units' 	=> [ 'px', '%' ],
			                'selectors' 	=> [
			                    '{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			                ],
			            ]
			        );

		        $this->end_controls_tab();

		        $this->start_controls_tab(
		            'tab_dots_active',
		            [
		                'label' => esc_html__( 'Active', 'tripgo' ),
		            ]
		        );

		             $this->add_control(
						'dot_color_active',
						[
							'label' 	=> esc_html__( 'Color', 'tripgo' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot.active span' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_responsive_control(
						'dots_width_active',
						[
							'label' 	=> esc_html__( 'Width', 'tripgo' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot.active span' => 'width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'dots_height_active',
						[
							'label' 	=> esc_html__( 'Height', 'tripgo' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot.active span' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
			            'dots_border_radius_active',
			            [
			                'label' 		=> esc_html__( 'Border Radius', 'tripgo' ),
			                'type' 			=> Controls_Manager::DIMENSIONS,
			                'size_units' 	=> [ 'px', '%' ],
			                'selectors' 	=> [
			                    '{{WRAPPER}} .ova-gallery-slide .owl-dots .owl-dot.active span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			                ],
			            ]
			        );

		        $this->end_controls_tab();
			$this->end_controls_tabs();

        $this->end_controls_section();
        /* End Dots Style */
		
	}

	// Render Template Here
	protected function render() {

		$settings 	= $this->get_settings();

		$template 	= $settings['template'];
		$show_icon	= $settings['show_icon'];
		$tab_item 	= $settings['tab_item'];

		$data_options['items']              = $settings['item_number'];
		$data_options['slideBy']            = $settings['slides_to_scroll'];
		$data_options['margin']             = $settings['margin_items'];
		$data_options['stagePadding']       = $settings['stagePadding'];
		$data_options['autoplayHoverPause'] = $settings['pause_on_hover'] === 'yes' ? true : false;
		$data_options['loop']               = $settings['infinite'] === 'yes' ? true : false;
		$data_options['autoplay']           = $settings['autoplay'] === 'yes' ? true : false;
		$data_options['autoplayTimeout']    = $settings['autoplay_speed'];
		$data_options['smartSpeed']         = $settings['smartspeed'];
		$data_options['dots']               = $settings['dot_control'] === 'yes' ? true : false;
		$data_options['rtl']				= is_rtl() ? true: false;

		 ?>

		 	<div class="ova-gallery-slide <?php echo esc_attr($template);?>">

				<div class="gallery-slide-carousel owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)) ?>">
					<?php if(!empty($tab_item)) : foreach ($tab_item as $items) : 
					
						$title       = $items['title'];
						$category    = $items['category'];

						$img_url 	 = $items['image']['url'];
						$img_alt 	 = isset( $items['image']['alt'] ) ? $items['image']['alt'] : $title;

						$link        = $items['link'];
						$nofollow    = ( isset( $link['nofollow'] ) && $link['nofollow'] ) ? ' rel="nofollow"' : '';
						$target      = ( isset( $link['is_external'] ) && $link['is_external'] !== '' ) ? ' target="_blank"' : '';

						?>

						<div class="gallery-slide-img">

							<div class="gallery-img">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
							</div>

							<div class="overlay" data-fancybox="accommodation-gallery-slide" data-src="<?php echo esc_url( $img_url ); ?>"
							data-caption="<?php echo esc_attr( $title ); ?>"></div>
                            
                            <div class="info-wrapper">
                            	<?php if ( $show_icon == 'yes' ) : ?>
	                            	<?php if ( !empty($link['url']) ) : ?>	
									<a href="<?php echo esc_url( $link['url'] ); ?>" aria-label="<?php esc_attr_e('View Detail','tripgo'); ?>" <?php echo esc_attr( $target ); ?> <?php echo esc_attr( $nofollow ); ?>>
								    <?php endif; ?>
										<div class="view-detail">
											<i aria-hidden="true" class="icomoon icomoon-arrow-right"></i>
										</div>
									<?php if ( !empty($link['url']) ) : ?>
								    </a>
							        <?php endif; ?>
							    <?php endif; ?>

                            	<div class="info">
                            		<?php if( $template == "template1") { ?>
										<?php if ( !empty ($category)) : ?>
											<span class="category">
												<?php echo esc_html($category); ?>
											</span>
										<?php endif; ?>
									<?php } ?>
									<?php if ( !empty ($title)) : ?>
										<?php if ( !empty($link['url'])) : ?>	
										<a href="<?php echo esc_url( $link['url'] ); ?>" <?php echo esc_attr( $target ); ?> <?php echo esc_attr( $nofollow ); ?>>
									    <?php endif; ?>
										<h3 class="title">
											<?php echo esc_html($title); ?>
										</h3>
										<?php if ( !empty($link['url']) ) : ?>
									    </a>
								        <?php endif; ?>
									<?php endif; ?>
									<?php if( $template == "template2") { ?>
										<?php if ( !empty ($category)) : ?>
											<span class="category">
												<?php echo esc_html($category); ?>
											</span>
										<?php endif; ?>
									<?php } ?>
								</div>

                            </div>			

						</div>
					

					<?php endforeach; endif; ?>

				</div>

			</div>

		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Gallery_Slide() );