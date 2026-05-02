<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_features extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_features';
	}

	public function get_title() {
		return esc_html__( 'Product Features', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
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
					'raw' 	=> esc_html__( "Don't enter Product ID if you use this element in templates for product detail page.In Elementor Preview ( When empty Product ID ) , this element display an example product features of the latest product", 'ova-brw' ),
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
			'section_price_style',
			[
				'label' => esc_html__( 'Features', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'wc_style_warning',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_responsive_control(
			'feature_max_width',
			[
				'label' => esc_html__( 'Max Width', 'ova-brw' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 600,
						'max' => 1920,
					],
					'%' => [
						'min' => 50,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .ova-features-product' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'feature_bgcolor',
			[
				'label'  => esc_html__( 'Background Color', 'ova-brw' ),
				'type' 	 => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-features-product' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'feature_color',
			[
				'label'  => esc_html__( 'Color Title', 'ova-brw' ),
				'type' 	 => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-features-product .feature  .title-desc .title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'features_typography',
				'selector' 	=> '{{WRAPPER}} .ova-features-product .feature .title-desc .title',
			]
		);

		$this->add_responsive_control(
			'features_padding',
			[
				'label' 	 => esc_html__( 'Padding', 'ova-brw' ),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ova-features-product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'features_border_radius',
			[
				'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ova-features-product' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'feature_value_color',
			[
				'label'  => esc_html__( 'Color Value', 'ova-brw' ),
				'type' 	 => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-features-product .feature .title-desc .desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 	 => 'feature_value_typography',
				'selector' => '{{WRAPPER}} .ova-features-product .feature .title-desc .desc',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings();
		$all_ids  = ovabrw_get_all_id_product();


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

		<div class="elementor-features">
			<?php wc_get_template( 'rental/loop/features.php', array('id' => $product_id) ); ?>
		</div>

		<?php
	}
}