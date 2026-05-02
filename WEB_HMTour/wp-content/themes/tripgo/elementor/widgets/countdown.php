<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Countdown extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_countdown';
	}

	public function get_title() {
		return esc_html__( 'Countdown', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		wp_enqueue_script( 'plugin', get_theme_file_uri('/assets/libs/countdown/jquery.plugin.js'), array('jquery'), false, true);
		wp_enqueue_script( 'countdown', get_theme_file_uri('/assets/libs/countdown/jquery.countdown.min.js'), array('jquery'), false, true);
		return [ 'tripgo-elementor-countdown' ];
	}
	
	// Add Your Controll In This Function
	protected function register_controls() {

		/* Content */
		$this->start_controls_section(
				'section_content',
				[
					'label' => esc_html__( 'Content', 'tripgo' ),
				]
			);

			$this->add_control(
				'due_date',
				[
					'label' => esc_html__( 'Due Date', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DATE_TIME,
				]
			);

			$this->add_control(
				'text_day',
				[
					'label' => esc_html__( 'Text Day', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Days', 'tripgo' ),
				]
			);

			$this->add_control(
				'text_hour',
				[
					'label' => esc_html__( 'Text Hour', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Hour', 'tripgo' ),
				]
			);

			$this->add_control(
				'text_min',
				[
					'label' => esc_html__( 'Text Minute', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => _( 'Min' ),
				]
			);

			$this->add_control(
				'text_sec',
				[
					'label' => esc_html__( 'Text Second', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => _( 'Sec' ),
				]
			);

		$this->end_controls_section();

		/* Number */
		$this->start_controls_section(
				'number_style_section',
				[
					'label' => esc_html__( 'Number', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'number_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .ova-countdown .number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'number_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .ova-countdown .number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'number_typography',
					'selector' => '{{WRAPPER}} .ova-countdown .number',
				]
			);

			$this->add_control(
				'number_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-countdown .number' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Text */
		$this->start_controls_section(
				'text_style_section',
				[
					'label' => esc_html__( 'Text', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'text_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .ova-countdown .text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'text_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .ova-countdown .text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'text_typography',
					'selector' => '{{WRAPPER}} .ova-countdown .text',
				]
			);

			$this->add_control(
				'text_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-countdown .text' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

	}

	// Render Template Here
	protected function render() {
		$settings 	= $this->get_settings();
		$due_date 	= $settings['due_date'] ? strtotime( $settings['due_date'] ) : time();
		$text_day 	= $settings['text_day'];
		$text_hour 	= $settings['text_hour'];
		$text_min 	= $settings['text_min'];
		$text_sec 	= $settings['text_sec'];
		$data 		= array(
						'year' 		=> date( "Y", $due_date ),
						'month' 	=> date( "n", $due_date ),
						'day' 		=> date( "j", $due_date ),
						'hours' 	=> date( "G", $due_date ),
						'minutes' 	=> intval( date( "i", $due_date ) ),
						'timezone' 	=> get_option( 'gmt_offset' ),
						'textDay' 	=> $text_day,
						'textHour'  => $text_hour,
						'textMin' 	=> $text_min,
						'textSec' 	=> $text_sec,
					);

		?>
		<div class="ova-countdown" data-date="<?php echo esc_attr( json_encode( $data ) ); ?>"></div>
		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Countdown() );