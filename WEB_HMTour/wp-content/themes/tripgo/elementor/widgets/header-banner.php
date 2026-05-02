<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Tripgo_Elementor_Header_Banner extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tripgo_elementor_header_banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Header Banner', 'tripgo' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-archive-title';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'hf' ];
	}

	

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
			]
		);

			
			$this->add_control(
				'header_boxed_content',
				[
					'label'        => esc_html__( 'Display Boxed Content', 'tripgo' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no'
				]
			);

			$this->add_control(
				'header_bg_source',
				[
					'label'        => esc_html__( 'Display Background by Feature Image in Post/Page', 'tripgo' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no'
				]
			);


			// Background Color
			$this->add_control(
				'cover_color',
				[
					'label' => esc_html__( 'Background Cover Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'default' => 'rgba(0,0,0,0.51)',
					'description' => esc_html__( 'You can add background image in Advanced Tab', 'tripgo' ),
					'selectors' => [
						'{{WRAPPER}} .cover_color' => 'background-color: {{VALUE}};',
					],
					'separator' => 'after'
				]
			);

			// Title
			$this->add_control(
				'show_title',
				[
					'label'        => esc_html__( 'Show Title', 'tripgo' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'yes',
					'selector'	=> '{{WRAPPER}} .header_banner_el .header_title',
				]
			);
			
			$this->add_control(
				'title_color',
				[
					'label' => esc_html__( 'Title Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#343434',
					'selectors' => [
						'{{WRAPPER}} .header_banner_el .header_title' => 'color: {{VALUE}};',
					]
				]
			);

			$this->add_responsive_control(
				'title_padding',
				[
					'label' => esc_html__( 'Title Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .header_banner_el .header_title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_control(
				'title_tag',
				[
					'label' => esc_html__( 'Choose Title Format', 'tripgo' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'h1' => esc_html__('H1', 'tripgo'),
						'h2' => esc_html__('H2', 'tripgo'),
						'h3' => esc_html__('H3', 'tripgo'),
						'h4' => esc_html__('H4', 'tripgo'),
						'h5' => esc_html__('H5', 'tripgo'),
						'h6' => esc_html__('H6', 'tripgo'),
						'div' => esc_html__('DIV', 'tripgo'),
					],
					'default' => 'h1'
				]
			);
			

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'header_title',
					'label' => esc_html__( 'Title Typo', 'tripgo' ),
					'selector'	=> '{{WRAPPER}} .header_banner_el .header_title'
				]
			);


			// Breadcrumbs
			$this->add_control(
				'show_breadcrumbs',
				[
					'label'        => esc_html__( 'Show Breadcrumbs', 'tripgo' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'yes',
					'selector'	=> '{{WRAPPER}} .header_breadcrumbs',
					'separator' => 'before'
				]
			);
			
			$this->add_control(
				'breadcrumbs_color',
				[
					'label' => esc_html__( 'Breadcrumbs Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#343434',
					'selectors' => [
						'{{WRAPPER}} .header_banner_el ul.breadcrumb li' => 'color: {{VALUE}};',
						'{{WRAPPER}} .header_banner_el ul.breadcrumb li a' => 'color: {{VALUE}};',
						'{{WRAPPER}} .header_banner_el ul.breadcrumb a' => 'color: {{VALUE}};',
						'{{WRAPPER}} .header_banner_el ul.breadcrumb li .separator i' => 'color: {{VALUE}};',
					]
				]
			);
			$this->add_control(
				'breadcrumbs_current_color',
				[
					'label' => esc_html__( 'Breadcrumbs Last Child Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .header_banner_el ul.breadcrumb li:last-child' => 'color: {{VALUE}};',
					]
				]
			);

			$this->add_control(
				'breadcrumbs_end_opacity',
				[
					'label' => esc_html__( 'Breadcrumbs Current Color Opacity', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0.9,
					],
					'range' => [
						'px' => [
							'max' => 1,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .header_banner_el ul.breadcrumb li:not(:first-child)' => 'opacity: {{SIZE}};',
						
					],
					
				]
			);

			$this->add_control(
				'breadcrumbs_color_hover',
				[
					'label' => esc_html__( 'Breadcrumbs Color Hover', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#343434',
					'selectors' => [
						'{{WRAPPER}} .header_banner_el ul.breadcrumb li a:hover' => 'color: {{VALUE}};',
					]
				]
			);

			$this->add_control(
				'breadcrumbs_opacity_hover',
				[
					'label' => esc_html__( 'Breadcrumbs Opacity Hover', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'max' => 1,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .header_banner_el ul.breadcrumb li:not(:first-child) a:hover' => 'opacity: {{SIZE}};',
					],
					
				]
			);
			

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'header_breadcrumbs_typo',
					'label' => esc_html__( 'Breadcrumbs Typography', 'tripgo' ),
					'selector'	=> '{{WRAPPER}} .header_banner_el ul.breadcrumb li, {{WRAPPER}} .header_banner_el ul.breadcrumb li a'
				]
			);

			$this->add_responsive_control(
				'breadcrumbs_padding',
				[
					'label' => esc_html__( 'Breadcrumbs Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .header_banner_el .header_breadcrumbs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' => esc_html__( 'Alignment', 'tripgo' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => esc_html__( 'Left', 'tripgo' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'tripgo' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'tripgo' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wrap_header_banner' => 'text-align: {{VALUE}};',
						'{{WRAPPER}} .wrap_header_banner ul.breadcrumb' => 'width: auto; display: initial;'
					],
					'default'	=> 'center',
					'separator' => 'before'
				]
			);
			

			$this->add_control(
				'class',
				[
					'label' => esc_html__( 'Class', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
				]
			);

		$this->end_controls_section();

		
	}



	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		$class_bg = $attr_style = '';
		if( $settings['header_bg_source'] == 'yes' ){
			$current_id = tripgo_get_current_id();
			$header_bg_source =  get_the_post_thumbnail_url( $current_id, 'full' );	
			$class_bg = 'bg_feature_img';
			$attr_style = 'style="background: url( '.$header_bg_source.' )" ';
		}

		$align = isset( $settings['align'] ) ? $settings['align'] : '';

		 ?>
		 	<!-- Display when you choose background per Post -->
		 	<div class="wrap_header_banner <?php echo esc_attr($class_bg).' '.$align; ?> " <?php printf( '%s', $attr_style ); ?> >

		 		<?php if( $settings['header_boxed_content'] == 'yes' ){ ?><div class="row_site"><div class="container_site"><?php } ?>
			 	
				 	<div class="cover_color"></div>

					<div class="header_banner_el <?php echo esc_attr( $settings['class'] ); ?>">
						
						<?php if( $settings['show_title'] == 'yes' ){ ?>
							
							<?php add_filter( 'tripgo_show_singular_title', '__return_false' ); ?>

							<?php $title_tag = $settings['title_tag']; ?>
							<<?php echo esc_html( $title_tag ); ?> class=" header_title">
								<?php echo get_template_part( 'template-parts/parts/breadcrumbs_title' ); ?>
							</<?php echo esc_html( $title_tag ); ?>>
								
						<?php } ?>


						<?php if( $settings['show_breadcrumbs'] == 'yes' ){ ?>
							<div class="header_breadcrumbs">
								<?php echo get_template_part( 'template-parts/parts/breadcrumbs' ); ?>
							</div>
						<?php } ?>

					</div>

				<?php if( $settings['header_boxed_content'] == 'yes' ){ ?> </div></div> <?php } ?>

			</div>
		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Header_Banner() );