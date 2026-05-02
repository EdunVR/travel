<?php
namespace ova_destination_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ova_destination_slider extends Widget_Base {


	public function get_name() {
		return 'ova_destination_slider';
	}

	public function get_title() {
		return esc_html__( 'Our Destination Slide', 'ova-destination' );
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	public function get_categories() {
		return [ 'destination' ];
	}

	public function get_script_depends() {
		wp_enqueue_style( 'carousel', OVADESTINATION_PLUGIN_URI.'assets/libs/owl-carousel/assets/owl.carousel.min.css' );
		wp_enqueue_script( 'carousel', OVADESTINATION_PLUGIN_URI.'assets/libs/owl-carousel/owl.carousel.min.js', array('jquery'), false, true );
		return [ '' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'ova-destination' ),
			]
		);

		$args = array(
           'taxonomy' => 'cat_destination',
           'orderby' => 'name',
           'order'   => 'ASC'
       	);
	
		$categories = get_categories($args);
		$catAll = array( 'all' => 'All categories');
		$cate_array = array();
		if ($categories) {
			foreach ( $categories as $cate ) {
				$cate_array[$cate->slug] = $cate->cat_name;
			}
		} else {
			$cate_array["No content Category found"] = esc_html__( 'No content Category found', 'ova-destination' );
		}

			$this->add_control(
				'category',
				[
					'label'   => esc_html__( 'Category', 'ova-destination' ),
					'type'    => Controls_Manager::SELECT2,
					'multiple' => true,
					'default' => 'all',
					'options' => array_merge( $catAll, $cate_array )
				]
			);

			$this->add_control(
				'template',
				[
					'label' => esc_html__( 'Template', 'ova-destination' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'template1',
					'options' => [
						'template1' => esc_html__( 'Template 1', 'ova-destination' ),
						'template2' => esc_html__( 'Template 2', 'ova-destination' ),
						'template3' => esc_html__( 'Template 3', 'ova-destination' ),
					]
				]
			);

			$this->add_control(
				'template3_style',
				[
					'label' => esc_html__( 'Template 3 - Style', 'ova-destination' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'Default', 'ova-destination' ),
						'circle' => esc_html__( 'Circle', 'ova-destination' ),
					],
					'condition' => [
						'template' => 'template3'
					]
				]
			);

			$this->add_control(
				'total_count',
				[
					'label'   => esc_html__( 'Total', 'ova-destination' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 7
				]
			);

			$this->add_control(
				'orderby_post',
				[
					'label' 	=> esc_html__( 'OrderBy', 'ova-destination' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'ID',
					'options' 	=> [
						'ID'  	=> esc_html__( 'ID', 'ova-destination' ),
						'title' => esc_html__( 'Title', 'ova-destination' ),
						'date' 	=> esc_html__( 'Date', 'ova-destination' ),
						'rand'  => esc_html__( 'Random', 'ova-destination' ),
						'ova_destination_met_order_destination' => esc_html__( 'Custom Order', 'ova-destination' ),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label' => esc_html__( 'Order', 'ova-destination' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => [
						'ASC'  => esc_html__( 'Ascending', 'ova-destination' ),
						'DESC'  => esc_html__( 'Descending', 'ova-destination' ),
					],
				]
			);

			$this->add_control(
				'show_thumbnail',
				[
					'label' => esc_html__( 'Show Thumbnail', 'ova-destination' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'ova-destination' ),
					'label_off' => esc_html__( 'Hide', 'ova-destination' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_title',
				[
					'label' => esc_html__( 'Show Title', 'ova-destination' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'ova-destination' ),
					'label_off' => esc_html__( 'Hide', 'ova-destination' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_rating',
				[
					'label' => esc_html__( 'Show Rating', 'ova-destination' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'ova-destination' ),
					'label_off' => esc_html__( 'Hide', 'ova-destination' ),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => [
						'template!' => 'template1'
					]
				]
			);

			$this->add_control(
				'show_count',
				[
					'label' => esc_html__( 'Show Count', 'ova-destination' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'ova-destination' ),
					'label_off' => esc_html__( 'Hide', 'ova-destination' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_link_to_detail',
				[
					'label' => esc_html__( 'Show Link to Detail', 'ova-destination' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'ova-destination' ),
					'label_off' => esc_html__( 'Hide', 'ova-destination' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => esc_html__( 'Additional Options', 'ova-destination' ),
			]
		);

			$this->add_control(
				'margin_items',
				[
					'label'   => esc_html__( 'Space between 2 items', 'ova-destination' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 24,
				]
				
			);

			$this->add_control(
				'item_number',
				[
					'label'       => esc_html__( 'Number of Items', 'ova-destination' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Number of Items', 'ova-destination' ),
					'default'     => 4,
				]
			);
	

			$this->add_control(
				'slides_to_scroll',
				[
					'label'       => esc_html__( 'Slides to Scroll', 'ova-destination' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'ova-destination' ),
					'default'     => 1,
				]
			);

			$this->add_control(
				'pause_on_hover',
				[
					'label'   => esc_html__( 'Pause on Hover', 'ova-destination' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-destination' ),
						'no'  => esc_html__( 'No', 'ova-destination' ),
					],
					'frontend_available' => true,
				]
			);


			$this->add_control(
				'infinite',
				[
					'label'   => esc_html__( 'Infinite Loop', 'ova-destination' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-destination' ),
						'no'  => esc_html__( 'No', 'ova-destination' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label'   => esc_html__( 'Autoplay', 'ova-destination' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-destination' ),
						'no'  => esc_html__( 'No', 'ova-destination' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_speed',
				[
					'label'     => esc_html__( 'Autoplay Speed', 'ova-destination' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 3000,
					'step'      => 500,
					'condition' => [
						'autoplay' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'smartspeed',
				[
					'label'   => esc_html__( 'Smart Speed', 'ova-destination' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 500,
				]
			);

			$this->add_control(
				'nav_control',
				[
					'label'   => esc_html__( 'Show Nav', 'ova-destination' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-destination' ),
						'no'  => esc_html__( 'No', 'ova-destination' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'dot_control',
				[
					'label'   => esc_html__( 'Show Dots', 'ova-destination' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'no',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-destination' ),
						'no'  => esc_html__( 'No', 'ova-destination' ),
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		/* Begin Image Style */
		$this->start_controls_section(
			'section_image',
			[
				'label' => esc_html__( 'Image', 'ova-destination' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		     $this->add_responsive_control(
	            'image_border_radius',
	            [
	                'label' 		=> esc_html__( 'Border Radius', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

			$this->add_control(
	            'overlay_color',
	            [
	                'label' 	=> esc_html__( 'Overlay Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .img .mask' => 'background-color: {{VALUE}}',
	                ],
	            ]
	        );

		$this->end_controls_section();

        /* Begin Info Style */
		$this->start_controls_section(
            'info_style',
            [
                'label' => esc_html__( 'Info', 'ova-destination' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
            ]
        );

			$this->add_control(
	            'info_bgcolor_normal',
	            [
	                'label' 	=> esc_html__( 'Background Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info' => 'background-color: {{VALUE}}',
	                ],
	            ]
	        );

			$this->add_responsive_control(
	            'info_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'info_box_shadow',
					'label' => esc_html__( 'Box Shadow', 'ova-destination' ),
					'selector' => '{{WRAPPER}} .ova-destination-slider .item-destination .info',
				]
			);

        $this->end_controls_section();
        /* End Info Style */

		/* Begin Name Style */
		$this->start_controls_section(
            'name_style',
            [
                'label' => esc_html__( 'Name', 'ova-destination' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
            ]
        );

        	$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	=> 'name_typography',
					'selector' 	=> '{{WRAPPER}} .ova-destination-slider .content .item-destination .info .name',	
				]
			);

			$this->add_control(
	            'name_color_normal',
	            [
	                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .name' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

			$this->add_control(
	            'name_color_hover',
	            [
	                'label' 	=> esc_html__( 'Color Hover', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination:hover .info .name' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

			$this->add_responsive_control(
	            'name_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

        $this->end_controls_section();
        /* End Name Style */

        /* Begin Count Tour Style */
		$this->start_controls_section(
            'count_tour_style',
            [
                'label' => esc_html__( 'Count Tour', 'ova-destination' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
            ]
        );

        	$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	=> 'count_tour_typography',
					'selector' 	=> '{{WRAPPER}} .ova-destination-slider .item-destination .info .count-tour, {{WRAPPER}} .ova-destination-slider .item-destination .count-tour',	
				]
			);

			$this->add_control(
	            'count_tour_color_normal',
	            [
	                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .count-tour, {{WRAPPER}} .ova-destination-slider .item-destination .count-tour' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

	        $this->add_control(
	            'count_tour_bgcolor_normal',
	            [
	                'label' 	=> esc_html__( 'Background Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .count-tour, {{WRAPPER}} .ova-destination-slider .item-destination .count-tour' => 'background-color: {{VALUE}}',
	                ],
	            ]
	        );

			$this->add_responsive_control(
	            'count_tour_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .count-tour, {{WRAPPER}} .ova-destination-slider .item-destination .count-tour' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_responsive_control(
	            'count_tour_border_radius',
	            [
	                'label' 		=> esc_html__( 'Border Radius', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .count-tour, {{WRAPPER}} .ova-destination-slider .item-destination .count-tour' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

        $this->end_controls_section();
        /* End Count Tour Style */

        /* Begin Rating Style */
		$this->start_controls_section(
            'rating_section_style',
            [
                'label' => esc_html__( 'Rating', 'ova-destination' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
                'condition' => [
                	'template!' => 'template1'
                ]
            ]
        );

        	$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	=> 'rating_typography',
					'selector' 	=> '{{WRAPPER}} .ova-destination-slider .item-destination .info .rating',	
				]
			);

			$this->add_control(
	            'rating_color',
	            [
	                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .rating' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

	        $this->add_control(
	            'icon_star_rating_color',
	            [
	                'label' 	=> esc_html__( 'Icon Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination-slider .item-destination .info .rating i' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

        $this->end_controls_section();
        /* End Rating Style */


        /* Begin Nav Style */
		$this->start_controls_section(
            'nav_style',
            [
                'label' => esc_html__( 'Nav Control', 'ova-destination' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
                'condition' => [
					'nav_control' => 'yes',
				]
            ]
        );

			$this->add_responsive_control(
				'nav_icon_size',
				[
					'label' 	=> esc_html__( 'Icon Size', 'ova-destination' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 40,
						],
					],
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'nav_top_position',
				[
					'label' 	=> esc_html__( 'Top Position', 'ova-destination' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => -150,
							'max' => 450,
						],
						'%' => [
							'min' => -150,
							'max' => 150,
						],
					],
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button' => 'top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'nav_right_position',
				[
					'label' 	=> esc_html__( 'Right Position', 'ova-destination' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 450,
						],
						'%' => [
							'min' => 0,
							'max' => 150,
						],
					],
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button' => 'right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'tabs_nav_style' );

				$this->start_controls_tab(
		            'tab_nav_normal',
		            [
		                'label' => esc_html__( 'Normal', 'ova-destination' ),
		            ]
		        );

					$this->add_control(
			            'nav_color_normal',
			            [
			                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button i' => 'color: {{VALUE}}',
			                ],
			            ]
			        );

			        $this->add_control(
			            'nav_color_border_normal',
			            [
			                'label' 	=> esc_html__( 'Border Color', 'ova-destination' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-next, {{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-prev' => 'border-color: {{VALUE}}',
			                ],
			            ]
			        );

			        $this->add_control(
			            'nav_bgcolor_normal',
			            [
			                'label' 	=> esc_html__( 'Background Color', 'ova-destination' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-next, {{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-prev' => 'background-color: {{VALUE}}',
			                ],
			            ]
			        );

				$this->end_controls_tab();

				$this->start_controls_tab(
		            'tab_nav_hover',
		            [
		                'label' => esc_html__( 'Hover', 'ova-destination' ),
		            ]
		        );

					$this->add_control(
			            'nav_color_hover',
			            [
			                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button:hover i' => 'color: {{VALUE}}',
			                ],
			            ]
			        );

			         $this->add_control(
			            'nav_color_border_hover',
			            [
			                'label' 	=> esc_html__( 'Border Color', 'ova-destination' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-next:hover, {{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-prev:hover' => 'border-color: {{VALUE}}',
			                ],
			            ]
			        );

			        $this->add_control(
			            'nav_bgcolor_hover',
			            [
			                'label' 	=> esc_html__( 'Background Color', 'ova-destination' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-next:hover, {{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button.owl-prev:hover' => 'background-color: {{VALUE}}',
			                ],
			            ]
			        );

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_control(
	            'nav_border_radius',
	            [
	                'label' 		=> esc_html__( 'Border Radius', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination-slider .owl-carousel .owl-nav button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

        $this->end_controls_section();
        /* End Nav Style */

        /* Begin Dots Style */
		$this->start_controls_section(
            'dots_style',
            [
                'label' => esc_html__( 'Dots', 'ova-destination' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
            ]
        );

			$this->start_controls_tabs( 'tabs_dots_style' );
				
				$this->start_controls_tab(
		            'tab_dots_normal',
		            [
		                'label' => esc_html__( 'Normal', 'ova-destination' ),
		            ]
		        );

		            $this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'dots_bg_gradient_normal',
							'label' 	=> esc_html__( 'Background Gradient', 'ova-destination' ),
							'types' 	=> [ 'classic', 'gradient' ],
							'exclude' 	=> [ 'image' ],
							'selector' 	=> '{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot span',
						]
					);

					$this->add_responsive_control(
						'dots_width',
						[
							'label' 	=> esc_html__( 'Width', 'ova-destination' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot span' => 'width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'dots_height',
						[
							'label' 	=> esc_html__( 'Height', 'ova-destination' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot span' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
			            'dots_border_radius',
			            [
			                'label' 		=> esc_html__( 'Border Radius', 'ova-destination' ),
			                'type' 			=> Controls_Manager::DIMENSIONS,
			                'size_units' 	=> [ 'px', '%' ],
			                'selectors' 	=> [
			                    '{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			                ],
			            ]
			        );

		        $this->end_controls_tab();

		        $this->start_controls_tab(
		            'tab_dots_hover',
		            [
		                'label' => esc_html__( 'Hover', 'ova-destination' ),
		            ]
		        );

			        $this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'dots_bg_gradient_hover',
							'label' 	=> esc_html__( 'Background Gradient', 'ova-destination' ),
							'types' 	=> [ 'classic', 'gradient' ],
							'exclude' 	=> [ 'image' ],
							'selector' 	=> '{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot span:hover',
						]
					);

		        $this->end_controls_tab();

		        $this->start_controls_tab(
		            'tab_dots_active',
		            [
		                'label' => esc_html__( 'Active', 'ova-destination' ),
		            ]
		        );

			        $this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'dots_bg_gradient_active',
							'label' 	=> esc_html__( 'Background Gradient', 'ova-destination' ),
							'types' 	=> [ 'classic', 'gradient' ],
							'exclude' 	=> [ 'image' ],
							'selector' 	=> '{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot.active span',
						]
					);

					$this->add_responsive_control(
						'dots_width_active',
						[
							'label' 	=> esc_html__( 'Width', 'ova-destination' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot.active span' => 'width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'dots_height_active',
						[
							'label' 	=> esc_html__( 'Height', 'ova-destination' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot.active span' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
			            'dots_border_radius_active',
			            [
			                'label' 		=> esc_html__( 'Border Radius', 'ova-destination' ),
			                'type' 			=> Controls_Manager::DIMENSIONS,
			                'size_units' 	=> [ 'px', '%' ],
			                'selectors' 	=> [
			                    '{{WRAPPER}} .ova-destination-slider .owl-dots .owl-dot.active span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			                ],
			            ]
			        );

		        $this->end_controls_tab();
			$this->end_controls_tabs();

        $this->end_controls_section();
        /* End Dots Style */

	}


	protected function render() {

		$settings = $this->get_settings();

		$template = apply_filters( 'el_elementor_ova_destination_slider', 'elementor/ova_destination_slider.php' );

		ob_start();
		ovadestination_get_template( $template, $settings );
		echo ob_get_clean();
		
	}
}