<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Testimonial extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_testimonial';
	}

	public function get_title() {
		return esc_html__( 'Ova Testimonial', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		
		// Slick carousel
		wp_enqueue_style( 'slick-carousel', get_template_directory_uri().'/assets/libs/slick/slick.css' );
		wp_enqueue_style( 'slick-carousel-theme', get_template_directory_uri().'/assets/libs/slick/slick-theme.css' );
		wp_enqueue_script( 'slick-carousel', get_template_directory_uri().'/assets/libs/slick/slick.min.js', array('jquery'), false, true );
		return [ 'tripgo-elementor-testimonial' ];
	}

	protected function register_controls() {


		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
			]
		);

			
            $this->add_control(
				'class_icon',
				[
					'label' => esc_html__( 'Icon Quote', 'tripgo' ),
					'type' => Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'icomoon icomoon-quote-outline',
						'library' 	=> 'all',
					],
				]
			);

			// Add Class control
			$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'name_author',
					[
						'label'   => esc_html__( 'Author Name', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
					]
				);

				$repeater->add_control(
					'job',
					[
						'label'   => esc_html__( 'Job', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,

					]
				);

				$repeater->add_control(
					'image_author',
					[
						'label'   => esc_html__( 'Author Image', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => Utils::get_placeholder_image_src(),
						],
					]
				);

				$repeater->add_control(
					'testimonial',
					[
						'label'   => esc_html__( 'Testimonial ', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXTAREA,
						'default' => esc_html__( '"Duis tristique volutpat facilisis. Integer vitae augue tellus. Phasellus fringilla tortor a maximus laoreet. Morbi a tristique erat. Fusce luctus urna vitae ornare aliquam."', 'tripgo' ),
					]
				);

				$this->add_control(
					'tab_item',
					[
						'label'       => esc_html__( 'Items Testimonial', 'tripgo' ),
						'type'        => Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'default' => [
							[
								'name_author' => esc_html__('Mila McSabbu', 'tripgo'),
								'job' => esc_html__('Freelance Designer', 'tripgo'),
								'testimonial' => esc_html__('Duis tristique volutpat facilisis. Integer vitae augue tellus. Phasellus fringilla tortor a maximus laoreet. Morbi a tristique erat. Fusce luctus urna vitae ornare aliquam.', 'tripgo'),
							],
							[
								'name_author' => esc_html__('jenny wilson', 'tripgo'),
								'job' => esc_html__('Marketing head', 'tripgo'),
								'testimonial' => esc_html__('Duis tristique volutpat facilisis. Integer vitae augue tellus. Phasellus fringilla tortor a maximus laoreet. Morbi a tristique erat. Fusce luctus urna vitae ornare aliquam.', 'tripgo'),
							],
							[
								'name_author' => esc_html__('Mike Hardson', 'tripgo'),
								'job' => esc_html__('Developer', 'tripgo'),
								'testimonial' => esc_html__('Duis tristique volutpat facilisis. Integer vitae augue tellus. Phasellus fringilla tortor a maximus laoreet. Morbi a tristique erat. Fusce luctus urna vitae ornare aliquam.', 'tripgo'),
							],
						],
						'title_field' => '{{{ name_author }}}',
					]
				);

			

		$this->end_controls_section();

		/*****************  END SECTION CONTENT ******************/


		/*****************************************************************
						START SECTION ADDITIONAL
		******************************************************************/

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => esc_html__( 'Additional Options', 'tripgo' ),
			]
		);


		/***************************  VERSION 1 ***********************/

			$this->add_control(
				'pause_on_hover',
				[
					'label'   => esc_html__( 'Pause on Hover', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);


			$this->add_control(
				'infinite',
				[
					'label'   => esc_html__( 'Infinite Loop', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label'   => esc_html__( 'Autoplay', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_speed',
				[
					'label'     => esc_html__( 'Autoplay Speed', 'tripgo' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 3000,
					'step'      => 500,
					'condition' => [
						'autoplay' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'smartspeed',
				[
					'label'   => esc_html__( 'Smart Speed', 'tripgo' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 500,
				]
			);

			$this->add_control(
				'dot_control',
				[
					'label'   => esc_html__( 'Show Dots', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		/****************************  END SECTION ADDITIONAL *********************/

		/*************  SECTION NAME JOB. *******************/
		$this->start_controls_section(
			'section_general',
			[
				'label' => esc_html__( 'General', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'style_quote',
				[
					'label' => esc_html__( 'Quote', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'quote_color',
				[
					'label'     => esc_html__( 'Quote Job', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .quote i' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'quote_size',
				[
					'label' => esc_html__( 'Size quote', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .quote i' => 'font-size: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'style_dots',
				[
					'label' => esc_html__( 'Dots', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);

			$this->add_control(
				'dot_color',
				[
					'label'     => esc_html__( 'Dot Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .slick-dots button' => 'background-color : {{VALUE}};',
						
					],
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);

			$this->add_control(
				'opacity_dots',
				[
					'label' => esc_html__( 'Opacity Dots', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0.2,
					],
					'range' => [
						'px' => [
							'max' => 1,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .slick-dots button' => 'opacity: {{SIZE}};',
					],
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);

			$this->add_control(
				'dot_active_color',
				[
					'label'     => esc_html__( 'Dot Active Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .slick-dots li.slick-active button' => 'background-color : {{VALUE}};',
						
					],
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);

			$this->add_control(
				'opacity_dots_active',
				[
					'label' => esc_html__( 'Opacity Dots', 'tripgo' ),
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
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .slick-dots li.slick-active button' => 'opacity: {{SIZE}};',
					],
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);

			// .ova-testimonial .slide-testimonials 
			
			$this->add_control(
				'style_content_testimonial',
				[
					'label' => esc_html__( 'Content', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]

			);

			$this->add_responsive_control(
				'content__margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content__padding',
				[
					'label'      => esc_html__( 'Padding', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();
		###############  end section job  ###############


		/*************  SECTION content testimonial  *******************/
		$this->start_controls_section(
			'section_content_testimonial',
			[
				'label' => esc_html__( 'Content Testimonial', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'content_testimonial_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info p.ova-evaluate',
				]
			);

			$this->add_control(
				'content_color',
				[
					'label'     => esc_html__( 'Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info p.ova-evaluate' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info p.ova-evaluate' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_padding',
				[
					'label'      => esc_html__( 'Padding', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info p.ova-evaluate' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();
		###############  end section content testimonial  ###############


		/*************  SECTION NAME AUTHOR. *******************/
		$this->start_controls_section(
			'section_author_name',
			[
				'label' => esc_html__( 'Author Name', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'author_name_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .name',
				]
			);

			$this->add_control(
				'author_name_color',
				[
					'label'     => esc_html__( 'Color Author', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'
						{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .name' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'author_name_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'author_name_padding',
				[
					'label'      => esc_html__( 'Padding', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client_info .info .name-job .name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		###############  end section author  ###############


		/*************  SECTION NAME JOB. *******************/
		$this->start_controls_section(
			'section_job',
			[
				'label' => esc_html__( 'Job', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'job_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .job',
				]
			);

			$this->add_control(
				'job_color',
				[
					'label'     => esc_html__( 'Color Job', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'
						{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .job' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'job_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'job_padding',
				[
					'label'      => esc_html__( 'Padding', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial .slide-testimonials .client-info .info .name-job .job' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();
		###############  end section job  ###############

		/*************  SECTION AVATAR. *******************/
		$this->start_controls_section(
			'section_avatar',
			[
				'label' => esc_html__( 'Avatar', 'tripgo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		// .ova-testimonial .slide-for .small-img img

			$this->add_control(
				'image_size',
				[
					'label' 		=> esc_html__( 'Size', 'tripgo' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' 	=> 0,
							'max' 	=> 70,
							'step' 	=> 1,
						]
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-testimonial .slide-for .small-img img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);


			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'border_image',
					'label' 	=> esc_html__( 'Border', 'tripgo' ),
					'selector' 	=> '{{WRAPPER}} .ova-testimonial .slide-for .small-img img',
				]
			);

			$this->add_control(
				'border_color',
				[
					'label' 	=> esc_html__( 'Border Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-for .small-img img' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'opacity_image_active',
				[
					'label' => esc_html__( 'Opacity image active', 'tripgo' ),
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
						'{{WRAPPER}} .ova-testimonial .slide-for .slick-current .small-img img' => 'opacity: {{SIZE}};',
					],
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);

			$this->add_control(
				'opacity_image_',
				[
					'label' => esc_html__( 'Opacity image', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0.4,
					],
					'range' => [
						'px' => [
							'max' => 1,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial .slide-for .small-img img' => 'opacity: {{SIZE}};',
					],
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);


		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();

		$tab_item = $settings['tab_item'];
		
		$icon = $settings['class_icon'] ? $settings['class_icon'] : '';

		// carousel data option
		$data_options['pause_on_hover']     = $settings['pause_on_hover'] === 'yes' ? true : false;
		$data_options['loop']               = $settings['infinite'] === 'yes' ? true : false;
		$data_options['autoplay']           = $settings['autoplay'] === 'yes' ? true : false;
		$data_options['autoplay_speed']     = $settings['autoplay_speed'];
		$data_options['smartSpeed']         = $settings['smartspeed'];
		$data_options['dots']         		= $settings['dot_control'] === 'yes' ? true : false;
		$data_options['rtl']				= is_rtl() ? true: false;

		?>

		<div class="ova-testimonial template_1">

            <div class="slide-for">
            	<?php if(!empty($tab_item)) : foreach ($tab_item as $k => $item) :  if ($k >= 3) break;
            		$alt = isset($item['name_author']) && $item['name_author'] ? $item['name_author'] : esc_html__( 'testimonial','tripgo' ); 
            	?>
            		<?php if(!empty($item['image_author']['url'])) : ?>
		         	    <div class="small-img">
							<img src="<?php echo esc_attr($item['image_author']['url']); ?>" alt="<?php echo esc_attr( $alt ); ?>">
						</div>	
					<?php endif; ?>
				<?php endforeach; endif; ?>
			</div>

			<div class="slide-testimonials slide-testimonial-version1" data-options="<?php echo esc_attr(json_encode($data_options)) ; ?>">
				<?php if(!empty($tab_item)) : foreach ($tab_item as $item) : ?>
					<div class="item">
						<div class="client-info">
							<div class="info">
	
								<?php if( $item['testimonial'] != '' ) : ?>
									<p class="ova-evaluate">
										<?php echo esc_html($item['testimonial']) ; ?>
									</p>
								<?php endif; ?>

								<div class="name-job">
									<div class="quote">
										<?php 
									        \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
									    ?>
									</div>

									<div class="name_job">
										<?php if( $item['name_author'] != '' ) { ?>
											<h6 class="name second_font">
												<?php echo esc_html($item['name_author']) ; ?>
											</h6>
										<?php } ?>

										<?php if( $item['job'] != '' ) { ?>
											<p class="job">
												<?php echo esc_html($item['job'])  ; ?>
											</p>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; endif; ?>
			</div>

		</div>

	<?php 
	}
	// end render
}

$widgets_manager->register( new Tripgo_Elementor_Testimonial() );

