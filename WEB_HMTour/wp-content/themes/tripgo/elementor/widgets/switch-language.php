<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Switch_Language extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_switch_language';
	}

	public function get_title() {
		return esc_html__( 'Switch Language', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-select';
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
				'icon_before',
				[
					'label' => esc_html__( 'Icon Before', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'icon_before_size',
				[
					'label' 		=> esc_html__( 'Size', 'tripgo' ),
					'type' 			=> \Elementor\Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 5,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 15,
					],
					'selectors' => [
						'{{WRAPPER}} .switch-languages .current-language .first-icon' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .switch-languages .current-language .first-icon' => 'width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .switch-languages .current-language .first-icon' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'current_language',
				[
					'label' => esc_html__( 'Current Language', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'English', 'tripgo' ),
					'placeholder' => esc_html__( 'Type your language here', 'tripgo' ),
				]
			);

			$this->add_control(
				'icon',
				[
					'label' => esc_html__( 'Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'ovaicon ovaicon-download',
						'library' => 'all',
					],
				]
			);
			
			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'languages',
				[
					'label' => esc_html__( 'Languages', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'English', 'tripgo' ),
				]
			);

			$this->add_control(
				'item',
				[
					'label' => esc_html__( 'Languages', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'default' => [
						[
							'languages' => esc_html__('France', 'tripgo'),
						],
						[
							'languages' => esc_html__('Italy', 'tripgo'),
						],
					],
					'title_field' => '{{{ languages }}}',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'content_typography',
					'selector' => '{{WRAPPER}} .switch-languages .current-language .text , {{WRAPPER}} .switch-languages .dropdown-language .dropdown-item',
				]
			);

			$this->add_control(
				'text_color',
				[
					'label' => esc_html__( 'Text Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .switch-languages .current-language .text ,{{WRAPPER}} .switch-languages .current-language i' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'list_language',
				[
					'label' => esc_html__( 'List Languages ', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'list_language_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .switch-languages .dropdown-language .dropdown-item' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'list_language_color_hover',
				[
					'label' => esc_html__( 'Hover Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .switch-languages .dropdown-language .dropdown-item:hover' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'background_color',
				[
					'label' => esc_html__( 'Background Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .switch-languages .dropdown-language' => 'background-color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();
	}

	// Render Template Here
	protected function render() {
		$settings = $this->get_settings();

		$current_language = $settings['current_language'];

		?>
			<div class="switch-languages ">

				<a href="javascript:;" class="current-language">
					<?php \Elementor\Icons_Manager::render_icon( $settings['icon_before'], [ 'aria-hidden' => 'true', 'class' => 'first-icon' ] ); ?>
					<span class="text"><?php echo esc_html( $current_language ); ?></span>
					<?php \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</a>

				<div class="dropdown-language">
					<?php foreach ( $settings['item'] as $item ) : ?>

						<a href="javascript:;" class="dropdown-item">
							<?php echo esc_html( $item['languages'] ); ?>
						</a>

					<?php endforeach; ?>
				</div>
			</div>
		<?php
	}

}
$widgets_manager->register( new Tripgo_Elementor_Switch_Language() );