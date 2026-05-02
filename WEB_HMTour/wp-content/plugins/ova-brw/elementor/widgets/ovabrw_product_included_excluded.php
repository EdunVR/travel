<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_included_excluded extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_included_excluded';
	}

	public function get_title() {
		return esc_html__( 'Product Included/Excluded', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
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
					'raw' 	=> esc_html__( "Don't enter Product ID if you use this element in templates for product detail page.In Elementor Preview ( When empty Product ID ) , this element display an example product Included/Excluded of the latest product", 'ova-brw' ),
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
			'section_product_included_excluded_style',
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

			$this->add_control(
				'text_color',
				[
					'label'  => esc_html__( 'Text Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .elementor-ovabrw-product-included-excluded .content-product-item' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'heading_color',
				[
					'label'  => esc_html__( 'Heading Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .elementor-ovabrw-product-included-excluded .content-product-item h2' => 'color: {{VALUE}};',
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

		<div class="elementor-ovabrw-product-included-excluded">
			<?php wc_get_template('rental/loop/included-excluded.php', array('id' => $product_id) ); ?>
		</div>
		
		<?php
	}
}