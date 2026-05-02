<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Our_Team extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_our_team';
	}

	public function get_title() {
		return esc_html__( 'Our Team', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-person';
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
				'label' => __( 'Content', 'tripgo' ),
			]
		);

			$this->add_control(
				'version',
				[
					'label' => esc_html__( 'Version', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'version_1',
					'options' => [
						'version_1'  => esc_html__( 'Version 1', 'tripgo' ),
						'version_2'  => esc_html__( 'Version 2', 'tripgo' ),
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' => esc_html__( 'Link', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::URL,
					'placeholder' => esc_html__( 'https://your-link.com', 'tripgo' ),
					'options' => [ 'url', 'is_external', 'nofollow' ],
					'default' => [
						'url' => '',
						'is_external' => false,
						'nofollow' => false,
					],
					'label_block' => true,
				]
			);

			$this->add_control(
				'image',
				[
					'label' => esc_html__( 'Choose Image', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);

			$this->add_control(
				'name',
				[
					'label' => esc_html__( 'Name', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Leland D. Johnston', 'tripgo' ),
					'placeholder' => esc_html__( 'Type your name here', 'tripgo' ),
				]
			);

			$this->add_control(
				'job',
				[
					'label' => esc_html__( 'Job', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'CEO & Founder', 'tripgo' ),
					'placeholder' => esc_html__( 'Type your job here', 'tripgo' ),
				]
			);

			$this->add_control(
				'text_align',
				[
					'label' => esc_html__( 'Alignment', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
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
					'toggle' => true,
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'show_social',
				[
					'label' => __( 'Show Social', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'tripgo' ),
					'label_off' => __( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
					'separator' => 'before'
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'list_url',
				[
					'label' => esc_html__( 'Link', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::URL,
					'placeholder' => esc_html__( 'https://your-link.com', 'tripgo' ),
					'options' => [ 'url', 'is_external', 'nofollow' ],
					'default' => [
						'url' => '#',
					],
					'label_block' => true,
				]
			);


			$repeater->add_control(
				'list_icon',
				[
					'label' => esc_html__( 'Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'label_block' => true,
				]
			);

			$this->add_control(
				'list',
				[
					'label' => esc_html__( 'List Socials', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'default' => [
						[
							'list_icon' => [
								'value' => 'fab fa-twitter',
								'library' => 'all',
							],
						],
						[
							'list_icon' => [
								'value' => 'fab fa-facebook-f',
								'library' => 'all',
							],
						],
						[
							'list_icon' => [
								'value' => 'fab fa-instagram',
								'library' => 'all',
							],
						],
						[
							'list_icon' => [
								'value' => 'fab fa-pinterest',
								'library' => 'all',
							],
						],
					],
					'condition' => [
						'show_social' => 'yes',
					],
				],
			);
		
		$this->end_controls_section();

		/* General */
		$this->start_controls_section(
				'general_style',
				[
					'label' => esc_html__( 'General', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'background',
				[
					'label' => esc_html__( 'Background', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team' => 'background: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'background_hover',
				[
					'label' => esc_html__( 'Background Hover', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team:hover' => 'background: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Image */
		$this->start_controls_section(
				'image_style',
				[
					'label' => esc_html__( 'Image', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'image_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'image_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'image_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
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
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .img .team-img' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'image_size',
				[
					'label' => esc_html__( 'Height', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'range' => [
						'px' => [
							'min' => 150,
							'max' => 600,
							'step' => 5,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .img .team-img' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Name */
		$this->start_controls_section(
				'name_style',
				[
					'label' => esc_html__( 'Name', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'name_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'name_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info .name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'name_typography',
					'selector' => '{{WRAPPER}} .tripgo-our-team .item-team .info .name',
				]
			);

			$this->add_control(
				'name_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info .name' => 'color: {{VALUE}}',
						'{{WRAPPER}} .tripgo-our-team .item-team .info .name a' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'name_hover_color',
				[
					'label' => esc_html__( 'Hover Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info .name:hover' => 'color: {{VALUE}}',
						'{{WRAPPER}} .tripgo-our-team .item-team .info .name:hover a' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Job */
		$this->start_controls_section(
				'job_style',
				[
					'label' => esc_html__( 'Job', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'job_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'job_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info .job' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'job_typography',
					'selector' => '{{WRAPPER}} .tripgo-our-team .item-team .info .job',
				]
			);

			$this->add_control(
				'job_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .info .job' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Social */
		$this->start_controls_section(
				'social_style',
				[
					'label' => esc_html__( 'Social', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_social' => 'yes',
					],
				]
			);

			$this->add_responsive_control(
				'social_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .socials' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'social_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .socials' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'social_icon_size',
				[
					'label' => esc_html__( 'Icon Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
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
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .socials .item a i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tripgo-our-team .item-team .socials .item a svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'social_background',
				[
					'label' => esc_html__( 'Background', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tripgo-our-team .item-team .socials' => 'background: {{VALUE}}',
					],
				]
			);

			$this->start_controls_tabs(
				'social_icon_style_tabs'
			);

				$this->start_controls_tab(
					'social_icon_style_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'tripgo' ),
					]
				);

					$this->add_control(
						'social_icon_color',
						[
							'label' => esc_html__( 'Color', 'tripgo' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .tripgo-our-team .item-team .socials .item a i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .tripgo-our-team .item-team .socials .item a svg' => 'fill: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'social_icon_style_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'tripgo' ),
					]
				);

					$this->add_control(
						'social_icon_hover_color',
						[
							'label' => esc_html__( 'Color', 'tripgo' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .tripgo-our-team .item-team .socials .item a:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .tripgo-our-team .item-team .socials .item a:hover svg' => 'fill: {{VALUE}}',
							],
						]
					);

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	// Render Template Here
	protected function render() {
		$settings 		= $this->get_settings();

		$version      	= $settings['version'];

		$link 			= $settings['link'];
		$items 			= $settings['list'];
		$show_social 	= $settings['show_social'];
		$name 			= $settings['name'];
		$job 			= $settings['job'];
		$image_url 		=  $settings['image']['url']; 
		$image_alt 		=  ( isset( $settings['image']['alt']) &&  $settings['image']['alt'] != '' ) ?  $settings['image']['alt'] : $name;
	?>
		
		<div class="tripgo-our-team our-team-<?php echo esc_attr( $version ); ?>">
			<div class="item-team">
				<div class="img">
			    	<img src="<?php echo esc_url( $image_url ); ?>" class="img-responsive team-img" alt="<?php echo esc_attr( $image_alt ); ?>">
			    	<?php if ( $items && is_array($items) && $show_social == 'yes' ): ?>
						<ul class="socials">
							<?php foreach ( $items as $item ): 

								$social_url = $item['list_url']['url'];
								$target 	= ( isset( $item['list_url']['is_external'] ) && $item['list_url']['is_external'] == 'on' ) ? '_blank' : '_self';
								$nofollow  	= ( isset( $item['nofollow'] ) && $item['nofollow'] ) ? ' rel="nofollow"' : '';
								$icon 		= $item['list_icon'];
							?>
								<li class="item">
									<a href="<?php echo esc_url($social_url); ?>" target="<?php echo esc_attr($target); ?>" <?php printf( $nofollow ); ?>>
										<?php if ( $icon ) {
											\Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
										}
										?>	
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="info">
					<?php if ( $name ): ?>
						<h2 class="name">
							
							<?php if ( $link && $link['url'] ): ?>
								<a href="<?php echo esc_url( $link['url'] ); ?>">
									<?php echo esc_html( $name ); ?>
								</a>
							<?php else: ?>
								<?php echo esc_html( $name ); ?>
							<?php endif; ?>

						</h2>
					<?php endif; ?>
					<?php if ( $job ): ?>
						<p class="job"><?php echo esc_html( $job ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>

	<?php }
	
}
$widgets_manager->register( new Tripgo_Elementor_Our_Team() );