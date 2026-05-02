<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_related extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_related';
	}

	public function get_title() {
		return esc_html__( 'Product Related', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-related';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	public function get_script_depends() {
		wp_enqueue_script('carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/owl.carousel.min.js', array('jquery'),null,true);
		wp_enqueue_style('carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/assets/owl.carousel.min.css', array(), null);

		wp_enqueue_script( 'ovabrw-product-related', OVABRW_PLUGIN_URI.'assets/js/elementor/product-related.js', array('jquery'), false, true );
		return [ '' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_product_related_style',
			[
				'label' => esc_html__( 'Content', 'ova-brw' ),
			]
		);

		    $this->add_control(
				'wc_content_warning',
				[
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> esc_html__( "Don't enter Product ID if you use this element in templates for product detail page.In Elementor Preview ( When empty Product ID ) , this element display an example product related of the latest product", 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'product_id',
				[
					'label'   => esc_html__( 'Product ID', 'ova-brw' ),
					'type'    => Controls_Manager::NUMBER,
				]
			);

			$this->add_control(
				'posts_per_page',
				[
					'label' => __( 'Products Per Page', 'ova-brw' ),
					'type' 	=> Controls_Manager::NUMBER,
					'default' => 5,
					'range' => [
						'px' => [
							'max' => 20,
						],
					],
				]
			);

			$this->add_control(
				'orderby',
				[
					'label' => esc_html__( 'Order By', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'default' => 'date',
					'options' => [
						'date' 			=> esc_html__( 'Date', 'ova-brw' ),
						'title' 		=> esc_html__( 'Title', 'ova-brw' ),
						'price' 		=> esc_html__( 'Price', 'ova-brw' ),
						'popularity' 	=> esc_html__( 'Popularity', 'ova-brw' ),
						'rating' 		=> esc_html__( 'Rating', 'ova-brw' ),
						'rand' 			=> esc_html__( 'Random', 'ova-brw' ),
						'menu_order' 	=> esc_html__( 'Menu Order', 'ova-brw' ),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label' => esc_html__( 'Order', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
						'asc' 	=> esc_html__( 'ASC', 'ova-brw' ),
						'desc' 	=> esc_html__( 'DESC', 'ova-brw' ),
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
				'label' => esc_html__( 'Additional Options', 'ova-brw' ),
			]
		);


		/***************************  VERSION 1 ***********************/
			$this->add_control(
				'margin_items',
				[
					'label'   => esc_html__( 'Margin Right Items', 'ova-brw' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 24,
				]
				
			);

			$this->add_control(
				'item_number',
				[
					'label'       => esc_html__( 'Item Number', 'ova-brw' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Number Item', 'ova-brw' ),
					'default'     => 4,
				]
			);

	

			$this->add_control(
				'slides_to_scroll',
				[
					'label'       => esc_html__( 'Slides to Scroll', 'ova-brw' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'ova-brw' ),
					'default'     => 1,
				]
			);

			$this->add_control(
				'pause_on_hover',
				[
					'label'   => esc_html__( 'Pause on Hover', 'ova-brw' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);


			$this->add_control(
				'infinite',
				[
					'label'   => esc_html__( 'Infinite Loop', 'ova-brw' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label'   => esc_html__( 'Autoplay', 'ova-brw' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'no',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_speed',
				[
					'label'     => esc_html__( 'Autoplay Speed', 'ova-brw' ),
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
					'label'   => esc_html__( 'Smart Speed', 'ova-brw' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 500,
				]
			);

			$this->add_control(
				'nav_control',
				[
					'label'   => esc_html__( 'Show Nav', 'ova-brw' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		/****************************  END SECTION ADDITIONAL *********************/

		/* STYLE */
		$this->start_controls_section(
			'section_product_slider_style',
			[
				'label' => esc_html__( 'Content', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'content_background',
				[
					'label' 	=> esc_html__( 'Background', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'content_box_shadow',
					'label' 	=> esc_html__( 'Box Shadow', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'content_border',
					'label' 	=> esc_html__( 'Border', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'content_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-slider .ova-product' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		// Tour Day style
		$this->start_controls_section(
			'section_tour_day_style',
			[
				'label' => esc_html__( 'Tour Day', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		    $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'tour_day_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-tour-day',
				]
			);

			$this->add_control(
				'tour_day_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-tour-day' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'tour_day_bgcolor',
				[
					'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-tour-day' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'tour_day_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-tour-day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'tour_day_border',
					'label' => esc_html__( 'Border', 'ova-brw' ),
					'selector' => '{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-tour-day',
				]
			);

		$this->end_controls_section();

		// Is Featured style
		$this->start_controls_section(
			'section_is_featured_style',
			[
				'label' => esc_html__( 'Is Featured', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		    $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'is_featured_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-is-featured',
				]
			);

			$this->add_control(
				'is_featured_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-is-featured' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'is_featured_bgcolor',
				[
					'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-is-featured' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'is_featured_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-is-featured' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		// Favorite style
		$this->start_controls_section(
			'section_favorite_style',
			[
				'label' => esc_html__( 'Favorite', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		    $this->add_responsive_control(
				'favourite_size',
				[
					'label' 		=> esc_html__( 'Size', 'ova-brw' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-product-wishlist .yith-wcwl-add-to-wishlist .yith-wcwl-add-button .add_to_wishlist i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'favorite_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-product-wishlist .yith-wcwl-add-to-wishlist .yith-wcwl-add-button .add_to_wishlist i' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'favorite_bgcolor',
				[
					'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_head_product .ova-product-wishlist .yith-wcwl-add-to-wishlist .yith-wcwl-add-button' => 'background-color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();
        
        // Title
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-title a',
				]
			);


			$this->add_control(
				'title_normal_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-title a' => 'color: {{VALUE}}',
					],
				]
			);


			$this->add_control(
				'title_hover_color',
				[
					'label' 	=> esc_html__( 'Color Hover', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-title:hover a' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'title_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_style',
			[
				'label' => esc_html__( 'Review', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'star_color',
				[
					'label' 	=> esc_html__( 'Star Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-review .star-rating' => 'color: {{VALUE}}',
					],
				]
			);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_price_style',
			[
				'label' => esc_html__( 'Price', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		    $this->add_control(
				'new_price_options',
				[
					'label' => esc_html__( 'New Price', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 		=> 'new_price_typography',
						'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .ova-product-price .new-product-price',
					]
				);

				$this->add_control( 
					'new_price_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .ova-product-price .new-product-price' => 'color: {{VALUE}}',
						],
					]
				);

			$this->add_control(
				'old_price_options',
				[
					'label' => esc_html__( 'Old Price', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 		=> 'old_price_typography',
						'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .ova-product-price .old-product-price',
					]
				);

				$this->add_control(
					'old_price_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .ova-product-price .old-product-price' => 'color: {{VALUE}}',
						],
					]
				);

			$this->add_control(
				'negotiable_price_options',
				[
					'label' => esc_html__( 'Negotiable Price', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 		=> 'negotiable_price_typography',
						'selector' 	=> '{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .ova-product-price .no-product-price',
					]
				);

				$this->add_control(
					'negotiable_price_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .ova-product-price .no-product-price' => 'color: {{VALUE}}',
						],
					]
				);

		$this->end_controls_section();

		// Button style
		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'ova-brw' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_tabs_button'
		);

			$this->start_controls_tab(
				'style_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'ova-brw' ),
				]
			);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'button_typography',		
						'label' => esc_html__( 'Typography', 'ova-brw' ),
						'selector' => '{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .product-btn-book-now',
						
					]
				);

				$this->add_control(	
					'color_button',
					[
						'label' => esc_html__( 'Color', 'ova-brw' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .product-btn-book-now' => 'color : {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'color_button_background',
					[
						'label' => esc_html__( 'Background ', 'ova-brw' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .product-btn-book-now' => 'background-color : {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' => 'button_border',
						'label' => esc_html__( 'Border', 'ova-brw' ),
						'selector' => '{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .product-btn-book-now',
					]
				);
				
				$this->add_control(
					'border_radius_button',
					array(
						'label'      => esc_html__( 'Border Radius', 'ova-brw' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors'  => array(
							'{{WRAPPER}} .ova-product-slider .ova-product .ova_foot_product .ova-product-wrapper-price .product-btn-book-now' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'style_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'ova-brw' ),
				]
			);

				$this->add_control(	
					'color_button_hover',
					[
						'label' => esc_html__( 'Color', 'ova-brw' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-product-slider .ova-product:hover .ova_foot_product .ova-product-wrapper-price .product-btn-book-now' => 'color : {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'color_button_background_hover',
					[
						'label' => esc_html__( 'Background ', 'ova-brw' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-product-slider .ova-product:hover .ova_foot_product .ova-product-wrapper-price .product-btn-book-now' => 'background-color : {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' => 'button_border_hover',
						'label' => esc_html__( 'Border', 'ova-brw' ),
						'selector' => '{{WRAPPER}} .ova-product-slider .ova-product:hover .ova_foot_product .ova-product-wrapper-price .product-btn-book-now',
					]
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/* Begin Nav Style */
		$this->start_controls_section(
            'nav_style',
            [
                'label' => esc_html__( 'Nav Control', 'ova-brw' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
                'condition' => [
					'nav_control' => 'yes',
				]
            ]
        );

			$this->add_responsive_control(
				'nav_icon_size',
				[
					'label' 	=> esc_html__( 'Icon Size', 'ova-brw' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 40,
						],
					],
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'nav_top_position',
				[
					'label' 	=> esc_html__( 'Top Position', 'ova-brw' ),
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
						'{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button' => 'top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'nav_right_position',
				[
					'label' 	=> esc_html__( 'Right Position', 'ova-brw' ),
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
						'{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button' => 'right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'tabs_nav_style' );

				$this->start_controls_tab(
		            'tab_nav_normal',
		            [
		                'label' => esc_html__( 'Normal', 'ova-brw' ),
		            ]
		        );

					$this->add_control(
			            'nav_color_normal',
			            [
			                'label' 	=> esc_html__( 'Color', 'ova-brw' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button i' => 'color: {{VALUE}}',
			                ],
			            ]
			        );

			        $this->add_control(
			            'nav_color_border_normal',
			            [
			                'label' 	=> esc_html__( 'Border Color', 'ova-brw' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button.owl-next, {{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button.owl-prev' => 'border-color: {{VALUE}}',
			                ],
			            ]
			        );

			        $this->add_control(
			            'nav_bgcolor_normal',
			            [
			                'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button.owl-next, {{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button.owl-prev' => 'background-color: {{VALUE}}',
			                ],
			            ]
			        );

				$this->end_controls_tab();

				$this->start_controls_tab(
		            'tab_nav_hover',
		            [
		                'label' => esc_html__( 'Hover', 'ova-brw' ),
		            ]
		        );

					$this->add_control(
			            'nav_color_hover',
			            [
			                'label' 	=> esc_html__( 'Color', 'ova-brw' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button:hover i' => 'color: {{VALUE}}',
			                ],
			            ]
			        );

			         $this->add_control(
			            'nav_color_border_hover',
			            [
			                'label' 	=> esc_html__( 'Border Color', 'ova-brw' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button.owl-next:hover, {{WRAPPER}} .ova-product-slider .owl-carousel .owl-nav button.owl-prev:hover' => 'border-color: {{VALUE}}',
			                ],
			            ]
			        );

			        $this->add_control(
			            'nav_bgcolor_hover',
			            [
			                'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
			                'type' 		=> Controls_Manager::COLOR,
			                'selectors' => [
			                    '{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button.owl-next:hover, {{WRAPPER}} .ova-product-slider .owl-carousel .owl-nav button.owl-prev:hover' => 'background-color: {{VALUE}}',
			                ],
			            ]
			        );

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_control(
	            'nav_border_radius',
	            [
	                'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-product-slider.owl-carousel .owl-nav button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

        $this->end_controls_section();
        /* End Nav Style */

		/* Begin Dots Style */
		$this->start_controls_section(
            'dots_style',
            [
                'label' => esc_html__( 'Dots (Mobile)', 'ova-brw' ),
                'tab' 	=> Controls_Manager::TAB_STYLE,
                'condition' => [
					'dot_control' => 'yes',
				]
            ]
        );

            $this->add_responsive_control(
				'dots_margin',
				[
					'label'      => esc_html__( 'Margin', 'ova-brw' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'tabs_dots_style' );
				
				$this->start_controls_tab(
		            'tab_dots_normal',
		            [
		                'label' => esc_html__( 'Normal', 'ova-brw' ),
		            ]
		        );

		            $this->add_control(
						'dot_color',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot span' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_responsive_control(
						'dots_width',
						[
							'label' 	=> esc_html__( 'Width', 'ova-brw' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot span' => 'width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'dots_height',
						[
							'label' 	=> esc_html__( 'Height', 'ova-brw' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot span' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
			            'dots_border_radius',
			            [
			                'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
			                'type' 			=> Controls_Manager::DIMENSIONS,
			                'size_units' 	=> [ 'px', '%' ],
			                'selectors' 	=> [
			                    '{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			                ],
			            ]
			        );

		        $this->end_controls_tab();

		        $this->start_controls_tab(
		            'tab_dots_active',
		            [
		                'label' => esc_html__( 'Active', 'ova-brw' ),
		            ]
		        );

		             $this->add_control(
						'dot_color_active',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot.active span' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_responsive_control(
						'dots_width_active',
						[
							'label' 	=> esc_html__( 'Width', 'ova-brw' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot.active span' => 'width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'dots_height_active',
						[
							'label' 	=> esc_html__( 'Height', 'ova-brw' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'size_units' 	=> [ 'px' ],
							'selectors' 	=> [
								'{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot.active span' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
			            'dots_border_radius_active',
			            [
			                'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
			                'type' 			=> Controls_Manager::DIMENSIONS,
			                'size_units' 	=> [ 'px', '%' ],
			                'selectors' 	=> [
			                    '{{WRAPPER}} .elementor-ralated-slide .elementor-ralated .owl-dots .owl-dot.active span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        // carousel option
		$data_options['items']              = $settings['item_number'];
		$data_options['slideBy']            = $settings['slides_to_scroll'];
		$data_options['margin']             = $settings['margin_items'];
		$data_options['autoplayHoverPause'] = $settings['pause_on_hover'] === 'yes' ? true : false;
		$data_options['loop']               = $settings['infinite'] === 'yes' ? true : false;
		$data_options['autoplay']           = $settings['autoplay'] === 'yes' ? true : false;
		$data_options['autoplayTimeout']    = $settings['autoplay_speed'];
		$data_options['smartSpeed']         = $settings['smartspeed'];
		$data_options['nav']                = $settings['nav_control'] === 'yes' ? true : false;
		$data_options['rtl']				= is_rtl() ? true: false;

		$all_ids = ovabrw_get_all_id_product();

		if( isset( $settings['product_id'] ) && $settings['product_id'] != '' ) {

		    $product_id     = ( in_array( $settings['product_id'], $all_ids ) == true ) ? $settings['product_id'] : get_the_id();

		} elseif( in_array( get_the_id(), $all_ids ) == false ) {

		    $product_id     = isset( $all_ids[0] ) ? $all_ids[0] : '' ;

		} else {
		    $product_id     = get_the_id();
		}

		// Get Product
		$product  = wc_get_product($product_id);
		
		if ( !$product || !$product->is_type('ovabrw_car_rental') ) {
			?>
				<div class="ovabrw_elementor_no_product">
					<span><?php echo $this->get_title(); ?></span>
				</div>
			<?php
			return;
		} 

		$args = [
			'posts_per_page' => 5,
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
		];

		if ( ! empty( $settings['posts_per_page'] ) ) {
			$args['posts_per_page'] = $settings['posts_per_page'];
		}

		// Get visible related products then sort them at random.
		$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

		// Handle orderby.
		$related_products = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

		?>

        <div class="elementor-ralated-slide">
            <div class="ova-product-slider elementor-ralated owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)) ?>">
            	<?php if( $related_products ) {
	            	foreach ( $related_products as $related_product ) {
						$post_object = get_post( $related_product->get_id() );
						setup_postdata( $GLOBALS['post'] =& $post_object );
						wc_get_template_part( 'content', 'product' );
					}
					
					$post_object = get_post( $product->get_id() );
					setup_postdata( $GLOBALS['post'] =& $post_object );
		       	} else { 
		       		esc_html_e( 'Not Found', 'ova-brw' );
		       	} ?>
			</div>
        </div>	

		<?php
	}
}