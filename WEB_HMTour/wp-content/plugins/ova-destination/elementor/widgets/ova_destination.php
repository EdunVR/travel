<?php
namespace ova_destination_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ova_destination extends Widget_Base {


	public function get_name() {
		return 'ova_destination';
	}

	public function get_title() {
		return esc_html__( 'Our Destination', 'ova-destination' );
	}

	public function get_icon() {
		return 'eicon-image-hotspot';
	}

	public function get_categories() {
		return [ 'destination' ];
	}

	public function get_script_depends() {
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
			'total_count',
			[
				'label'   => esc_html__( 'Total', 'ova-destination' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4
			]
		);

		$this->add_control(
			'orderby_post',
			[
				'label' 	=> esc_html__( 'OrderBy Post', 'ova-destination' ),
				'type' 		=> \Elementor\Controls_Manager::SELECT,
				'default' 	=> 'ID',
				'options' 	=> [
					'ID'  	=> esc_html__( 'ID', 'ova-destination' ),
					'title'	=> esc_html__( 'Title', 'ova-destination' ),
					'date'	=> esc_html__( 'Date', 'ova-destination' ),
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
			'offset',
			[
				'label'   => esc_html__( 'Offset', 'ova-destination' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0
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

		/* Begin Image Style */
		$this->start_controls_section(
			'section_image',
			[
				'label' => esc_html__( 'Image', 'ova-destination' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		    $this->add_responsive_control(
				'image_height',
				[
					'label' 	=> esc_html__( 'Height', 'ova-destination' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 180,
							'max' => 360,
						],
					],
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-destination .content .item-destination .img img' => 'height: {{SIZE}}{{UNIT}};min-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		    $this->add_responsive_control(
	            'image_border_radius',
	            [
	                'label' 		=> esc_html__( 'Border Radius', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

			$this->add_control(
	            'overlay_color',
	            [
	                'label' 	=> esc_html__( 'Overlay Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .img .mask' => 'background-color: {{VALUE}}',
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

			$this->add_responsive_control(
	            'info_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-destination' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
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
					'selector' 	=> '{{WRAPPER}} .ova-destination .content .item-destination .info .name',	
				]
			);

			$this->add_control(
	            'name_color_normal',
	            [
	                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .name' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

			$this->add_control(
	            'name_color_hover',
	            [
	                'label' 	=> esc_html__( 'Color Hover', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination .content .item-destination:hover .info .name' => 'color: {{VALUE}}',
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
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'selector' 	=> '{{WRAPPER}} .ova-destination .content .item-destination .info .count-tour, {{WRAPPER}} .ova-destination .content .item-destination .count-tour',	
				]
			);

			$this->add_control(
	            'count_tour_color_normal',
	            [
	                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .count-tour, {{WRAPPER}} .ova-destination .content .item-destination .count-tour' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

	        $this->add_control(
	            'count_tour_bgcolor_normal',
	            [
	                'label' 	=> esc_html__( 'Background Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .count-tour, {{WRAPPER}} .ova-destination .content .item-destination .count-tour' => 'background-color: {{VALUE}}',
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
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .count-tour, {{WRAPPER}} .ova-destination .content .item-destination .count-tour' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .count-tour, {{WRAPPER}} .ova-destination .content .item-destination .count-tour' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'selector' 	=> '{{WRAPPER}} .ova-destination .content .item-destination .info .rating',	
				]
			);

			$this->add_control(
	            'rating_color',
	            [
	                'label' 	=> esc_html__( 'Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .rating' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

	        $this->add_control(
	            'icon_star_rating_color',
	            [
	                'label' 	=> esc_html__( 'Icon Color', 'ova-destination' ),
	                'type' 		=> Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .ova-destination .content .item-destination .info .rating i' => 'color: {{VALUE}}',
	                ],
	            ]
	        );

        $this->end_controls_section();
        /* End Rating Style */

	}


	protected function render() {

		$settings = $this->get_settings();

		$template = apply_filters( 'el_elementor_ova_destination', 'elementor/ova_destination.php' );

		ob_start();
		ovadestination_get_template( $template, $settings );
		echo ob_get_clean();
		
	}
}