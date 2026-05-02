<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_content extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_content';
	}

	public function get_title() {
		return esc_html__( 'Product Content', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-description';
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
					'label'       => esc_html__( 'Product ID', 'ova-brw' ),
					'type'        => Controls_Manager::NUMBER,
				]
			);		

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_short_description_style',
			[
				'label' => esc_html__( 'Style', 'ova-brw' ),
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


			$this->add_responsive_control(
				'align',
				[
					'label' => esc_html__( 'Alignment', 'ova-brw' ),
					'type' 	=> Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => esc_html__( 'Left', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-right',
						],
						'justify' => [
							'title' => esc_html__( 'Justified', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-justify',
						],
					],
					'default' => '',
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'text_color',
				[
					'label'  => esc_html__( 'Text Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .elementor-ovabrw-product-content .content-product-item.tour-description' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	 => 'text_typography',
					'selector' => '{{WRAPPER}} .elementor-ovabrw-product-content .content-product-item.tour-description',
				]
			);

			$this->add_control(
				'heading_color',
				[
					'label'  => esc_html__( 'Heading Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .elementor-ovabrw-product-content .content-product-item.tour-description h2, {{WRAPPER}} .elementor-ovabrw-product-content .content-product-item.tour-description h4' => 'color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {

		$settings 	= $this->get_settings();

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

		<div class="elementor-ovabrw-product-content">
			<?php wc_get_template('rental/loop/description.php', array('id' => $product_id) ); ?>
		</div>
		
		<?php
	}
}