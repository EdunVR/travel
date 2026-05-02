<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Testimonial_4 extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_testimonial_4';
	}

	public function get_title() {
		return esc_html__( 'Ova Testimonial 4', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ 'tripgo-elementor-testimonial-4' ];
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
				'quote',
				[
					'label' => esc_html__( 'Quote', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'ovaicon ovaicon-right-quote',
						'library' => 'ovaicon',
					],
				]
			);

			$this->add_control(
				'show_rating',
				[
					'label' => esc_html__( 'Show Rating', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'evaluate',
					[
						'label' => esc_html__( 'Evaluate', 'tripgo' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__( 'Good Experience', 'tripgo' ),
						'placeholder' => esc_html__( 'Type your evaluate here', 'tripgo' ),
					]
				);

				$repeater->add_control(
					'name_author',
					[
						'label'   => esc_html__( 'Author Name', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
					]
				);

				$repeater->add_control(
					'job',
					[
						'label'   => esc_html__( 'Job', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
					]
				);

				$repeater->add_control(
					'rating',
					[
						'label' => esc_html__( 'Rating', 'tripgo' ),
						'type' => Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
						'default' => 5,
						'dynamic' => [
							'active' => true,
						],
					]
				);
		
				$repeater->add_control(
					'star_style',
					[
						'label' => esc_html__( 'Icon', 'tripgo' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'star_fontawesome' => 'Font Awesome',
							'star_unicode' => 'Unicode',
						],
						'default' => 'star_fontawesome',
						'render_type' => 'template',
						'prefix_class' => 'elementor--star-style-',
						'separator' => 'before',
					]
				);
		
				$repeater->add_control(
					'unmarked_star_style',
					[
						'label' => esc_html__( 'Unmarked Style', 'tripgo' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'solid' => [
								'title' => esc_html__( 'Solid', 'tripgo' ),
								'icon' => 'eicon-star',
							],
							'outline' => [
								'title' => esc_html__( 'Outline', 'tripgo' ),
								'icon' => 'eicon-star-o',
							],
						],
						'default' => 'solid',
					]
				);

				$repeater->add_control(
					'image_author',
					[
						'label'   => esc_html__( 'Author Image', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => Utils::get_placeholder_image_src(),
						],
					]
				);

				$repeater->add_control(
					'testimonial',
					[
						'label'   => esc_html__( 'Testimonial ', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXTAREA,
						'default' => esc_html__( 'OMG! I cannot believe that I have got a brand new landing page after getting appmax. It was super easy to edit and publish.I have got a brand new landing page.', 'tripgo' ),
					]
				);

				$this->add_control(
					'tab_item',
					[
						'label'       => esc_html__( 'Items Testimonial', 'tripgo' ),
						'type'        => Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'default' => [
							[
								'name_author' => esc_html__('Mila McSabbu', 'tripgo'),
								'job' => esc_html__('Freelance Designer', 'tripgo'),
							],
							[
								'name_author' => esc_html__('Henry K. Melendez', 'tripgo'),
								'job' => esc_html__('Freelance Designer', 'tripgo'),
							],
							[
								'name_author' => esc_html__('Somalia D. Silva', 'tripgo'),
								'job' => esc_html__('Freelance Designer', 'tripgo'),
							],
							[
								'name_author' => esc_html__('Pacific D. Lee', 'tripgo'),
								'job' => esc_html__('Freelance Designer', 'tripgo'),
							],
						],
						'title_field' => '{{{ name_author }}}',
					]
				);
			
		$this->end_controls_section();
		/*****************  END SECTION CONTENT ******************/

		/******************  START SECTION ADDITIONAL*************/
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
					'default' => 24,
					'condition' => [
						'template' => 'template1',
					],
				]
			);

			$this->add_control(
				'margin_items_v2',
				[
					'label'   => esc_html__( 'Margin Right Items', 'tripgo' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 0,
					'condition' => [
						'template' => 'template2',
					],
				]
			); 

			$this->add_control(
				'item_number',
				[
					'label'       => esc_html__( 'Item Number', 'tripgo' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Number Item', 'tripgo' ),
					'default'     => 3,
					'condition' => [
						'template' => 'template1',
					],
				]
			);

			$this->add_control(
				'item_number_v2',
				[
					'label'       => esc_html__( 'Item Number', 'tripgo' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Number Item', 'tripgo' ),
					'default'     => 1,
					'condition' => [
						'template' => 'template2',
					],
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
				'center_mode',
				[
					'label'   => esc_html__( 'Center Mode', 'tripgo' ),
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
					'frontend_available' => true,
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
			'section_item_style',
			[
				'label' => esc_html__( 'Item', 'tripgo' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'item_background_color',
				[
					'label' => esc_html__( 'Background Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'item_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'item_border',
					'selector' => '{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item',
				]
			);

			$this->add_control(
				'item_border_color_center',
				[
					'label' => esc_html__( 'Border Color Center', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4.ova-testimonial-template1 .owl-item.center .item' => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'template' => 'template1',
					],
				]
			);

			$this->add_control(
				'item_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'item_box_shadow',
					'selector' => '{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item ',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_rating',
			[
				'label' => esc_html__( 'Rating', 'tripgo' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'rating_size',
				[
					'label' => esc_html__( 'Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .rating i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'rating_spacing',
				[
					'label' => esc_html__( 'Spacing', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .rating i' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'rating_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .owl-stage-outer .item .wrap-evaluate .evaluate .rating .elementor-star-rating .elementor-star-full::before' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'rating_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .item .rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_quote_style',
			[
				'label' => esc_html__( 'Quote', 'tripgo' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'quote_size',
				[
					'label' => esc_html__( 'Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .item .quote i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'quote_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .item .quote i' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'quote_position_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial-4 .item .quote' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/*************  SECTION content testimonial  *******************/
		$this->start_controls_section(
			'section_content_testimonial',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'content_testimonial_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .owl-stage-outer .item .content',
				]
			);

			$this->add_control(
				'content_color',
				[
					'label'     => esc_html__( 'Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .owl-stage-outer .item .content' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .owl-stage-outer .item .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_author_style',
			[
				'label' => esc_html__( 'Image', 'tripgo' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'author_image_size',
				[
					'label' => esc_html__( 'Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .owl-stage-outer .item .info .author-image img' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'author_image_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .owl-stage-outer .item .info .author-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'author_image_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .owl-stage-outer .item .info .author-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_name',
			[
				'label' => esc_html__( 'Name', 'tripgo' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'name_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .info .name-job .name',
				]
			);

			$this->add_control(
				'name_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .info .name-job .name' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'name_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .info .name-job .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_job',
			[
				'label' => esc_html__( 'Job', 'tripgo' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'job_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .info .name-job .job',
				]
			);

			$this->add_control(
				'job_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .info .name-job .job' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'job_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .slide-testimonials .item .info .name-job .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'dots_size',
				[
					'label' 	=> esc_html__( 'Size', 'tripgo' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-testimonial-4 .owl-carousel .owl-dots .owl-dot' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'dots_spacing',
				[
					'label' => esc_html__( 'Spacing', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .owl-carousel .owl-dots .owl-dot' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'dots_color',
				[
					'label' 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .owl-carousel .owl-dots .owl-dot' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'dots_color_active',
				[
					'label' 	=> esc_html__( 'Active Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-4 .owl-carousel .owl-dots .owl-dot.active' => 'background-color: {{VALUE}}',
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
	                    '{{WRAPPER}} .ova-testimonial-4 .owl-carousel .owl-dots .owl-dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_responsive_control(
	            'dots_margin',
	            [
	                'label' 		=> esc_html__( 'Margin', 'tripgo' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-testimonial-4 .owl-carousel .owl-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

        $this->end_controls_section();
        /* End Dots Style */

	}

	protected function get_rating($rating) {

		$settings = $this->get_settings();
		$rating_scale = 5;

		return [ $rating, $rating_scale ];
	}

	protected function render_stars( $icon, $rating ) {
		$rating_data = $this->get_rating($rating);
		$rating = (float) $rating_data[0];
		$floored_rating = floor( $rating );
		$stars_html = '';

		for ( $stars = 1.0; $stars <= $rating_data[1]; $stars++ ) {
			if ( $stars <= $floored_rating ) {
				$stars_html .= '<i class="elementor-star-full">' . $icon . '</i>';
			} elseif ( $floored_rating + 1 === $stars && $rating !== $floored_rating ) {
				$stars_html .= '<i class="elementor-star-' . ( $rating - $floored_rating ) * 10 . '">' . $icon . '</i>';
			} else {
				$stars_html .= '<i class="elementor-star-empty">' . $icon . '</i>';
			}
		}

		return $stars_html;
	}


	// Render Template Here
	protected function render() {
		$settings = $this->get_settings();

		$template     = $settings['template'];
		$quote  	  = $settings['quote'];
		$tab_item     = $settings['tab_item'];
		$show_rating  = $settings['show_rating'];
		
		$data_options['slideBy']            = $settings['slides_to_scroll'];
		$data_options['center']				= $settings['center_mode'] === 'yes' ? true : false;
		$data_options['autoplayHoverPause'] = $settings['pause_on_hover'] === 'yes' ? true : false;
		$data_options['loop']               = $settings['infinite'] === 'yes' ? true : false;
		$data_options['autoplay']           = $settings['autoplay'] === 'yes' ? true : false;
		$data_options['autoplayTimeout']    = $settings['autoplay_speed'];
		$data_options['smartSpeed']         = $settings['smartspeed'];
		$data_options['rtl']				= is_rtl() ? true: false;
		$data_options['dots']       		= $settings['dot_control'] === 'yes' ? true : false;
		$data_options['template']			= $template;

		if($template == 'template1' ){
			$data_options['items']    = $settings['item_number'];
			$data_options['margin']   = $settings['margin_items'];
		}

		if($template == 'template2' ){
			$data_options['items']    = $settings['item_number_v2'];
			$data_options['margin']   = $settings['margin_items_v2'];
		}

		?>

			<div class="ova-testimonial-4 ova-testimonial-<?php echo esc_attr( $template ); ?>">

				<div class="slide-testimonials owl-carousel owl-theme " data-options="<?php echo esc_attr(json_encode($data_options)) ; ?>">
					<?php if(!empty($tab_item)) : foreach ($tab_item as $item) : ?>
						<div class="item">
							<div class="wrap-evaluate">
								<div class="evaluate">
									<?php if( $item['evaluate'] != '' ) : ?>
										<p class="text">
											<?php echo esc_html($item['evaluate']) ; ?>
										</p>
									<?php endif; ?>

									<!-- rating -->
										<?php if($show_rating == 'yes') {
											$icon = '&#xE934;';
											$rating_data 		 = $this->get_rating($item['rating']);
											$textual_rating 	 = $rating_data[0] . '/' . $rating_data[1];
											$rating 			 = (float) $item['rating'];
											$unmarked_star_style = $item['unmarked_star_style'];
											$star_style 		 = $item['star_style'];
											
											if ( 'star_fontawesome' === $star_style ) {
												if ( 'outline' === $unmarked_star_style ) {
													$icon = '&#xE933;';
												}
											} elseif ( 'star_unicode' === $star_style ) {
												$icon = '&#9733;';
									
												if ( 'outline' === $unmarked_star_style ) {
													$icon = '&#9734;';
												}
											}

											$stars_element = '<div class="elementor-star-rating" title="'.$textual_rating.'">' . $this->render_stars( $icon, $item['rating'] ) . ' </div>';
										?>

										<?php if($rating != 0) : ?>
											<div class="rating <?php echo esc_html( $star_style ); ?>">
												<?php print_r ( $stars_element ); ?>
											</div>
										<?php endif; ?>
									<?php } ?>
								</div>
	                            
	                            <?php if( $quote != '' ) : ?>
		                            <span class="quote">
		                            	<?php \Elementor\Icons_Manager::render_icon( $quote, [ 'aria-hidden' => 'true' ] ); ?>
		                            </span>
								<?php endif; ?>
							</div>
							
							<?php if( $item['testimonial'] != '' ) : ?>
								<p class="content">
									<?php echo esc_html($item['testimonial']); ?>
								</p>
							<?php endif; ?>
								
							<div class="info">
								<?php if( $item['image_author']['url'] != '' ) { 
									$alt = isset($item['name_author']) && $item['name_author'] ? $item['name_author'] : esc_html__( 'testimonial','tripgo' ); 
								?>
									<div class="author-image">
										<img src="<?php echo esc_attr($item['image_author']['url']); ?>" alt="<?php echo esc_attr( $alt ); ?>">
									</div>
								<?php } ?>

								<div class="name-job">
									<?php if( $item['name_author'] != '' ) { ?>
										<p class="name">
											<?php echo esc_html($item['name_author']) ; ?>
										</p>
									<?php } ?>

									<?php if( $item['job'] != '' ) { ?>
										<p class="job">
											<?php echo esc_html($item['job'])  ; ?>
										</p>
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
$widgets_manager->register( new Tripgo_Elementor_Testimonial_4() );