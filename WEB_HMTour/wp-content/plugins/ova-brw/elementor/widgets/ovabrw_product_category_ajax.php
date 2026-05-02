<?php namespace ovabrw_product_elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_category_ajax extends \Elementor\Widget_Base {

	public function get_name() {		
		return 'ovabrw_product_category_ajax';
	}

	public function get_title() {
		return esc_html__( 'Product Category Ajax', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-products-archive';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	public function get_script_depends() {
		wp_enqueue_script( 'ovabrw-product-category-ajax', OVABRW_PLUGIN_URI.'assets/js/elementor/product-category-ajax.js', array('jquery'), false, true );
		return [ '' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'ova-brw' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_CONTENT
			]
		);

			$this->add_control(
				'auto_sorted',
				[
					'label' 		=> esc_html__( 'Auto Sorted', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Yes', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'No', 'ova-brw' ),
					'return_value' 	=> 'yes',
					'default' 		=> 'yes',
				]
			);

			$args_query	= [
				'taxonomy' 	=> 'product_cat',
				'orderby' 	=> 'name',
	        	'order'   	=> 'ASC'
			];

			$categories 	= get_categories( $args_query );
			$args_category 	= [];
			$defaults 		= [];

			if ( $categories && is_array( $categories ) ) {
				foreach ( $categories as $k => $obj_term ) {
					$args_category[$obj_term->term_id] = $obj_term->name;

					if ( $k < 3 ) $defaults[] = $obj_term->term_id;
				}
			}

			$this->add_control(
				'categories',
				[
					'label' 	=> esc_html__( 'Product Categories', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT2,
					'multiple' 	=> true,
					'options' 	=> $args_category,
					'default' 	=> $defaults,
					'condition' => [
						'auto_sorted' => [ 'yes' ]
					]
				]
			);

			$this->add_control(
				'category_ids',
				[
					'label' 		=> esc_html__( 'Product Category IDs', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> implode( '|', $defaults ),
					'placeholder' 	=> esc_html__( '1|2|3', 'ova-brw' ),
					'description' 	=> esc_html__( 'Category IDs are separated by "|"', 'ova-brw' ),
					'condition' 	=> [
						'auto_sorted' => ''
					]
				]
			);

			$this->add_control(
				'posts_per_page',
				[
					'label' 	=> esc_html__( 'Posts per page', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> -1,
					'default' 	=> 9
				]
			);

			$this->add_control(
				'order',
				[
					'label' 	=> esc_html__( 'Order', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'DESC',
					'options' 	=> [
						'ASC' 	=> esc_html__( 'Ascending', 'ova-brw' ),
						'DESC'  => esc_html__( 'Descending', 'ova-brw' ),
					]
				]
			);

			$this->add_control(
				'orderby',
				[
					'label' 	=> esc_html__( 'OrderBy', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'date',
					'options' 	=> [
						'date' 		=> esc_html__( 'Date', 'ova-brw' ),
						'ID'  		=> esc_html__( 'ID', 'ova-brw' ),
						'title'  	=> esc_html__( 'title', 'ova-brw' ),
						'rand'  	=> esc_html__( 'rand', 'ova-brw' )
					]
				]
			);

			$this->add_control(
				'layout',
				[
					'label' 	=> esc_html__( 'Layout', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'grid',
					'options' 	=> [
						'grid' 	=> esc_html__( 'Grid', 'ova-brw' ),
						'list' 	=> esc_html__( 'List', 'ova-brw' )
					]
				]
			);

			$this->add_control(
				'grid_template',
				[
					'label' 	=> esc_html__( 'Grid Template', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'template_1',
					'options' 	=> [
						'template_1' 	=> esc_html__( 'Template 1', 'ova-brw' ),
						'template_2' 	=> esc_html__( 'Template 2', 'ova-brw' )
					],
					'condition' => [
						'layout' => 'grid'
					]
				]
			);

			$this->add_control(
				'column',
				[
					'label' 	=> esc_html__( 'Column', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'column3',
					'options' 	=> [
						'column2'  	=> esc_html__( '2 Column', 'ova-brw' ),
						'column3'  	=> esc_html__( '3 Column', 'ova-brw' ),
						'column4'  	=> esc_html__( '4 Column', 'ova-brw' ),
					],
					'condition' => [
						'layout' 	=> 'grid'
					]
				]
			);

			$this->add_control(
				'thumbnail_type',
				[
					'label' 	=> esc_html__( 'Thumbnail', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'image',
					'options' 	=> [
						'image' 	=> esc_html__( 'Image', 'ova-brw' ),
						'gallery' 	=> esc_html__( 'Gallery', 'ova-brw' ),
					]
				]
			);

			$this->add_control(
				'pagination',
				[
					'label' 		=> esc_html__( 'Pagination', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
					'return_value' 	=> 'yes',
					'default' 		=> 'yes'
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_category',
			[
				'label' => esc_html__( 'Category', 'ova-brw' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'category_item_typography',
					'selector' 	=> '{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item',
				]
			);

			$this->start_controls_tabs(
				'category_item_tabs'
			);

				$this->start_controls_tab(
					'category_item_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'ova-brw' ),
					]
				);

					$this->add_control(
						'item_color',
						[
							'label' => esc_html__( 'Color', 'ova-brw' ),
							'type' 	=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'item_background_color',
						[
							'label' => esc_html__( 'Background', 'ova-brw' ),
							'type' 	=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' 		=> 'item_border',
							'selector' 	=> '{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item',
						]
					);


				$this->end_controls_tab();

				$this->start_controls_tab(
					'category_item_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'ova-brw' ),
					]
				);

					$this->add_control(
						'item_color_hover',
						[
							'label' => esc_html__( 'Color', 'ova-brw' ),
							'type' 	=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'item_background_color_hover',
						[
							'label' => esc_html__( 'Background', 'ova-brw' ),
							'type' 	=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' 		=> 'item_border_hover',
							'selector' 	=> '{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item:hover',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'category_item_active_tab',
					[
						'label' => esc_html__( 'Active', 'ova-brw' ),
					]
				);

					$this->add_control(
						'item_color_active',
						[
							'label' => esc_html__( 'Color', 'ova-brw' ),
							'type' 	=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item.active' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'item_background_color_active',
						[
							'label' => esc_html__( 'Background', 'ova-brw' ),
							'type' 	=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item.active' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' 		=> 'item_border_active',
							'selector' 	=> '{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item.active',
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'item_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' 	=> 'before'
				]
			);

			$this->add_responsive_control(
				'item_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'item_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list .category-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'alignment',
				[
					'label' => esc_html__( 'Alignment', 'ova-brw' ),
					'type' 	=> \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => esc_html__( 'Left', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-center',
						],
						'flex-end' => [
							'title' => esc_html__( 'Right', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw-category-ajax .ovabrw-category-list' => 'justify-content: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
		$template = apply_filters( 'ovabrw_ft_element_product_category_ajax', 'elementor/ovabrw_product_category_ajax.php' );

		ob_start();
		ovabrw_get_template( $template, $settings );
		echo ob_get_clean();
	}
}