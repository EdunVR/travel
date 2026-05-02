<?php

namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_unavailable_time extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_unavailable_time';
	}

	public function get_title() {
		return esc_html__( 'Unavailable Time', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-price';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_product_id_options',
			[
				'label' => esc_html__( 'Product Option', 'ova-brw' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		    $this->add_control(
				'wc_content_warning',
				[
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> esc_html__( "Don't enter Product ID if you use this element in templates for product detail page.In Elementor Preview ( When empty Product ID ) , this element display an example product unavailable_time of the latest product", 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'product_id',
				[
					'label'       => esc_html__( 'Product ID', 'ova-brw' ),
					'type'        => Controls_Manager::NUMBER,
				]
			);		

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_unavailable_time_style',
			[
				'label' => esc_html__( 'Unavailable Time', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning',
				[
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);


			$this->add_control(
				'title_options',
				[
					'label' => esc_html__( 'Title Options', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::HEADING,
				]
			);

				$this->add_control(
					'title_color',
					[
						'label' => esc_html__( 'Color', 'ova-brw' ),
						'type' 	=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovacrs_single_untime h3' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 	 => 'title_typography',
						'selector' => '{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime h3',
					]
				);

				$this->add_responsive_control(
					'title_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
						'type' 		 => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->add_control(
				'time_options',
				[
					'label' => esc_html__( 'Time Options', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_control(
					'time_color',
					[
						'label' => esc_html__( 'Color', 'ova-brw' ),
						'type' 	=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovacrs_single_untime ul li' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 	 => 'time_typography',
						'selector' => '{{WRAPPER}} .ovacrs_single_untime ul li',
					]
				);

				$this->add_responsive_control(
					'time_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
						'type' 		 => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .ovacrs_single_untime ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

		$this->end_controls_section();
	}

	protected function render() {

		$settings 	= $this->get_settings();
		$all_ids 	= ovabrw_get_all_id_product();

		$product_id = $settings['product_id'];

        if ( empty( $all_ids ) ) {
        	?>
				<div class="ovabrw_elementor_no_product">
					<span><?php echo $this->get_title(); ?></span>
				</div>
			<?php
			return;
		}

		?>

		<div class="elementor-unavailable-time">
			<?php
	            wc_get_template( 'rental/loop/unavailable_time.php', array( 'id' => $product_id ) );
	        ?>
		</div>
		
		<?php
	}
}