<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_location_review extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_location_review';
	}

	public function get_title() {
		return esc_html__( 'Location & Rating', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-rating';
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
					'raw' 	=> esc_html__( "Don't enter Product ID if you use this element in templates for product detail page.In Elementor Preview ( When empty Product ID ) , this element display an example product location and review of the latest product", 'ova-brw' ),
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
			'section_product_location_review_style',
			[
				'label' => esc_html__( 'Style', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		    $this->add_responsive_control(
				'location_review_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-location-review' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'location_options',
				[
					'label' => esc_html__( 'Location Options', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_control(
					'location_color',
					[
						'label' => esc_html__( 'Color', 'ova-brw' ),
						'type' 	=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-location-review .ova-product-location a' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 	 => 'location_typography',
						'selector' => '{{WRAPPER}} .ova-location-review .ova-product-location a',
					]
				);

				$this->add_responsive_control(
					'location_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
						'type' 		 => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .ova-location-review .ova-product-location' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->add_control(
				'review_options',
				[
					'label' => esc_html__( 'Review Options', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_control(
					'review_color',
					[
						'label' => esc_html__( 'Color', 'ova-brw' ),
						'type' 	=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-location-review .ova-product-review .star-rating, {{WRAPPER}} .ova-location-review .ova-product-review .star-rating:before' => 'color: {{VALUE}}',
						],
					]
				);


				$this->add_responsive_control(
					'reiview_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
						'type' 		 => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', '%' ],
						'selectors'  => [
							'{{WRAPPER}}  .ova-location-review .ova-product-review .star-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		<div class="elementor-location-review">
			<?php wc_get_template( 'rental/loop/location-review.php', array('id' => $product_id) ); ?>
		</div>

		<?php
	}
}