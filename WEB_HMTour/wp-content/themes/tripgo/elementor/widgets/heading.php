<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Tripgo_Elementor_Heading extends Widget_Base {

	
	public function get_name() {
		return 'tripgo_elementor_heading';
	}

	
	public function get_title() {
		return esc_html__( 'Ova Heading', 'tripgo' );
	}

	
	public function get_icon() {
		return 'eicon-heading';
	}

	
	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ '' ];
	}
	
	// Add Your Controll In This Function
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
			]
		);	
			
			
			$this->add_control(
				'sub_title',
				[
					'label' 	=> esc_html__( 'Sub Title', 'tripgo' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> 'Sub title'
				]
			);

			$this->add_control(
				'title',
				[
					'label' 	=> esc_html__( 'Title', 'tripgo' ),
					'type' 		=> Controls_Manager::TEXTAREA,
					'default' 	=> 'Title'
				]
			);

			$this->add_control(
				'description',
				[
					'label' 	=> esc_html__( 'Description', 'tripgo' ),
					'type' 		=> Controls_Manager::TEXTAREA,
					'default' 	=> ''
				]
			);

			$this->add_control(
				'link_address',
				[
					'label'   		=> esc_html__( 'Link', 'tripgo' ),
					'type'    		=> \Elementor\Controls_Manager::URL,
					'show_external' => false,
					'default' 		=> [
						'url' 			=> '',
						'is_external' 	=> false,
						'nofollow' 		=> false,
					],
				]
			);
			
			$this->add_control(
				'html_tag',
				[
					'label' 	=> esc_html__( 'HTML Tag', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'h2',
					'options' 	=> [
						'h1' 		=> esc_html__( 'H1', 'tripgo' ),
						'h2'  		=> esc_html__( 'H2', 'tripgo' ),
						'h3'  		=> esc_html__( 'H3', 'tripgo' ),
						'h4' 		=> esc_html__( 'H4', 'tripgo' ),
						'h5' 		=> esc_html__( 'H5', 'tripgo' ),
						'h6' 		=> esc_html__( 'H6', 'tripgo' ),
						'div' 		=> esc_html__( 'Div', 'tripgo' ),
						'span' 		=> esc_html__( 'span', 'tripgo' ),
						'p' 		=> esc_html__( 'p', 'tripgo' ),
					],
				]
			);

			$this->add_responsive_control(
				'align_heading',
				[
					'label' 	=> esc_html__( 'Alignment', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' => [
							'title' => esc_html__( 'Left', 'tripgo' ),
							'icon' 	=> 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'tripgo' ),
							'icon' 	=> 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'tripgo' ),
							'icon' 	=> 'eicon-text-align-right',
						],
					],
					'default' 	=> 'center',
					'toggle' 	=> true,
					'selectors' => [
						'{{WRAPPER}} .ova-title' => 'text-align: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		//SECTION TAB STYLE TITLE
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_typography_title',
					'label' 	=> esc_html__( 'Typography', 'tripgo' ),
					'selector' 	=> '{{WRAPPER}} .ova-title .title',
				]
			);

			$this->add_control(
				'color_title',
				[
					'label' 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-title .title' => 'color : {{VALUE}};',
						'{{WRAPPER}} .ova-title .title a' => 'color : {{VALUE}};',	
					],
				]
			);

			$this->add_control(
				'color_title_hover',
				[
					'label' 	=> esc_html__( 'Color hover', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-title .title a:hover' => 'color : {{VALUE}};'
					],
					
				]
			);

			$this->add_control(
				'bgcolor_title',
				[
					'label' 	=> esc_html__( 'Background Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-title .title' => 'background-color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'padding_title',
				[
					'label' 	 => esc_html__( 'Padding', 'tripgo' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-title .title ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'margin_title',
				[
					'label' 	 => esc_html__( 'Margin', 'tripgo' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-title .title ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		//END SECTION TAB STYLE TITLE

		//SECTION TAB STYLE SUB TITLE
		$this->start_controls_section(
			'section_sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_typography_sub_title',
					'label' 	=> esc_html__( 'Typography', 'tripgo' ),
					'selector' 	=> '{{WRAPPER}} .ova-title h3.sub-title',
				]
			);

			$this->add_control(
				'sub_title_font_family',
				[
					'label' 	=> esc_html__( 'Font Family', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::FONT,
					'default' 	=> "La Belle Aurore",
					'selectors' => [
						'{{WRAPPER}} .ova-title .sub-title' => 'font-family: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'color_sub_title',
				[
					'label'	 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-title h3.sub-title' => 'color : {{VALUE}};'
						
						
					],
				]
			);

			$this->add_responsive_control(
				'padding_sub_title',
				[
					'label' 	 => esc_html__( 'Padding', 'tripgo' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-title h3.sub-title ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'margin_sub_title',
				[
					'label' 	 => esc_html__( 'Margin', 'tripgo' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-title h3.sub-title ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
		$this->end_controls_section();
		//END SECTION TAB STYLE SUB TITLE

		//SECTION TAB STYLE DESCRIPTION
		$this->start_controls_section(
			'section_description',
			[
				'label' => esc_html__( 'Description', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_typography_description',
					'label' 	=> esc_html__( 'Typography', 'tripgo' ),
					'selector' 	=> '{{WRAPPER}} .ova-title .description',
				]
			);

			$this->add_control(
				'color_description',
				[
					'label'	 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-title .description' => 'color : {{VALUE}};'		
					],
				]
			);

			$this->add_responsive_control(
				'padding_description',
				[
					'label' 	 => esc_html__( 'Padding', 'tripgo' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-title .description ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'margin_description',
				[
					'label' 	 => esc_html__( 'Margin', 'tripgo' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-title .description ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
		$this->end_controls_section();
		//END SECTION TAB STYLE DESCRIPTION
		
	}

	// Render Template Here
	protected function render() {

		$settings = $this->get_settings();

		$title     		= $settings['title'];
		$sub_title 		= $settings['sub_title'];
		$description	= $settings['description']; 
		$link      		= $settings['link_address']['url'];
		$target    		= $settings['link_address']['is_external'] ? ' target="_blank"' : '';
		$html_tag  		= $settings['html_tag'];

		?>
		<div class="ova-title">

			<?php if($sub_title): ?>
				<h3 class="sub-title"><?php echo esc_html( $sub_title ); ?></h3>
			<?php endif; ?>

			<?php if($title): ?>
				<?php if( $link ) { ?>
				
					<<?php echo esc_attr($html_tag); ?> class="title"><a href="<?php echo esc_url( $link ); ?>"<?php printf( $target ); ?>><?php echo esc_html( $title ); ?></a>
					</<?php echo esc_attr($html_tag); ?>>

				<?php } else { ?>

					<<?php echo esc_attr($html_tag); ?> class="title"><?php echo esc_html($title); ?></<?php echo esc_attr($html_tag); ?>>
				<?php } ?>
			<?php endif; ?>

			<?php if($description): ?>
				<p class="description"><?php echo esc_html($description); ?></p>
			<?php endif; ?>

		</div> 
		 	
		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Heading() );