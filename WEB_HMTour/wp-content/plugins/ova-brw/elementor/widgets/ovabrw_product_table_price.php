<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_table_price extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_table_price';
	}

	public function get_title() {
		return esc_html__( 'Product Price Table', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-price-table';
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
					'raw' 	=> esc_html__( "Don't enter Product ID if you use this element in templates for product detail page.In Elementor Preview ( When empty Product ID ) , this element display an example product content of the latest product", 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'product_id',
				[
					'label'  => esc_html__( 'Product ID', 'ova-brw' ),
					'type'   => Controls_Manager::NUMBER,
				]
			);		

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning_content',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_responsive_control(
				'content_padding',
				[
					'label' 	 => esc_html__( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_border_radius',
				[
					'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'content_bgcolor',
				[
					'label'  => esc_html__( 'Background Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent' => 'background-color: {{VALUE}}',
					],
				]
			);
		

			$this->add_control(
				'content_title_color',
				[
					'label'  => esc_html__( 'Color Title', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table label' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_title_typography',
					'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table label',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_value_style',
			[
				'label' => esc_html__( 'Table', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning_table',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'content_title_table_bg',
				[
					'label'  => esc_html__( 'Background', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table thead' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'content_title_table_color',
				[
					'label'  => esc_html__( 'Color Title', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table thead tr th' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_title_table_typography',
					'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table thead tr th',
				]
			);

			$this->add_responsive_control(
				'content_title_table_padding',
				[
					'label' 	 => esc_html__( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table thead tr th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_title_table_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table thead tr th' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_style',
			[
				'label' => esc_html__( 'Table Value', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning_table_value',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'table_value_color',
				[
					'label'  => esc_html__( 'Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table tbody tr td' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'table_value_typography',
					'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table tbody tr td',
				]
			);

			$this->add_responsive_control(
				'table_value_padding',
				[
					'label' 	 => esc_html__( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'table_value_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .price_table table tbody tr td' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();

		$all_ids = ovabrw_get_all_id_product();

		if ( empty( $all_ids ) ) {
        	?>
				<div class="ovabrw_elementor_no_product">
					<span><?php echo $this->get_title(); ?></span>
				</div>
			<?php
			return;
		}

		$product_id = $settings['product_id'];

		?>

		<div class="elementor-table-price">
			<?php
	            wc_get_template( 'rental/loop/table_price.php', array( 'id' => $product_id ) );
	        ?>
		</div>

		<?php
	}
}