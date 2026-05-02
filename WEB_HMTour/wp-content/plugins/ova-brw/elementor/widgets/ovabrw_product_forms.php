<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_forms extends Widget_Base {


	public function get_name() {	
		return 'ovabrw_product_forms';
	}

	public function get_title() {
		return esc_html__( 'Product Forms', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	public function get_script_depends() {
		wp_enqueue_script( 'ovabrw-product-forms', OVABRW_PLUGIN_URI.'assets/js/elementor/product-forms.js', array('jquery'), false, true );
		return [ '' ];
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
					'raw' 	=> esc_html__( "Don't enter Product ID if you use this element in templates for product detail page.In Elementor Preview ( When empty Product ID ) , this element display an example product forms of the latest product", 'ova-brw' ),
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
			'section_product_forms_style',
			[
				'label' => esc_html__( 'Products Forms', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

            $this->add_control(
				'forms_bgcolor',
				[
					'label'  => esc_html__( 'Background Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-forms-product .forms-wrapper' => 'background-color: {{VALUE}};',
					],
				]
			);

		    $this->add_responsive_control(
				'forms_border_radius',
				[
					'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-forms-product .forms-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'forms_box_shadow',
					'label' => esc_html__( 'Box Shadow', 'ova-brw' ),
					'selector' => '{{WRAPPER}} .ova-forms-product .forms-wrapper',
				]
			);

		$this->end_controls_section();

	}

	protected function render() {

		$settings 	= $this->get_settings();

		$all_ids  	= ovabrw_get_all_id_product();
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

		<div class="elementor-ovabrw-product-forms">
	        <?php
	            wc_get_template( 'rental/loop/forms.php', array( 'id' => $product_id ) );
	        ?>
		</div>

		<?php

	}
}