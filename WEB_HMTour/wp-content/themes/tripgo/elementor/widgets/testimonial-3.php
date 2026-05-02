<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Testimonial_3 extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_testimonial_3';
	}

	public function get_title() {
		return esc_html__( 'Ova Testimonial 3', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		wp_enqueue_style( 'owl-animate', get_template_directory_uri().'/assets/libs/animate/animate.min.css', array(), '', true );
		return [ 'tripgo-elementor-testimonial-3' ];
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
						'value' 	=> 'icomoon icomoon-quote',
						'library' 	=> 'all',
					],
				]
			);

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
					'link',
					[
						'label' 		=> esc_html__( 'Link', 'tripgo' ),
						'type' 			=> \Elementor\Controls_Manager::URL,
						'placeholder' 	=> esc_html__( 'https://your-link.com', 'tripgo' ),
						'default' 		=> [
							'url' 				=> '',
							'is_external' 		=> false,
							'nofollow' 			=> false,
							'custom_attributes' => '',
						],
						'label_block' => true,
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
						'default' => esc_html__( '"Sed ullamcorper morbi tincidunt or massa eget egestas purus. Non nisi est sit amet facilisis magna etiam."', 'tripgo' ),
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
								'testimonial' => esc_html__('"OMG! I cannot believe that I have got a brand new landing page after getting appmax. It was super easy to edit and publish.I have got a brand new landing page."', 'tripgo'),
							],
							[
								'name_author' => esc_html__('Jenny Wilson', 'tripgo'),
								'job' => esc_html__('UI/UX Designer', 'tripgo'),
								'testimonial' => esc_html__('"OMG! I cannot believe that I have got a brand new landing page after getting appmax. It was super easy to edit and publish.I have got a brand new landing page."', 'tripgo'),
							],
							[
								'name_author' => esc_html__('Mila McSabbu', 'tripgo'),
								'job' => esc_html__('Governer Of Canada', 'tripgo'),
								'testimonial' => esc_html__('"OMG! I cannot believe that I have got a brand new landing page after getting appmax. It was super easy to edit and publish.I have got a brand new landing page."', 'tripgo'),
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
				'margin_items',
				[
					'label'   => esc_html__( 'Margin Right Items', 'tripgo' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 0,
				]
				
			);

			$this->add_control(
				'item_number',
				[
					'label'       => esc_html__( 'Item Number', 'tripgo' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Number Item', 'tripgo' ),
					'default'     => 1,
				]
			);

	

			$this->add_control(
				'slides_to_scroll',
				[
					'label'       => esc_html__( 'Slides to Scroll', 'tripgo' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'tripgo' ),
					'default'     => 1,
				]
			);

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

		/*************  SECTION General. *******************/
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item.active .item .quote i' => 'color : {{VALUE}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item.active .item .quote' => 'font-size: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'opacity_quote',
				[
					'label' => esc_html__( 'Opacity Quote', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0.05,
					],
					'range' => [
						'px' => [
							'max' => 1,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item.active .item .quote i' => 'opacity: {{SIZE}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-dots .owl-dot span' => 'background-color : {{VALUE}};',
						
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-dots .owl-dot span' => 'opacity: {{SIZE}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-dots .owl-dot.active span' => 'background-color : {{VALUE}};',
						
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-dots .owl-dot.active span' => 'opacity: {{SIZE}};',
					],
					'condition' => [
						'dot_control' => 'yes',
					],
				]
			);

			$this->add_control(
				'style_image',
				[
					'label' => esc_html__( 'Image', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'image_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .client' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			// image margin 

		$this->end_controls_section();
		###############  end section general ###############


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
					'selector' => '{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .evaluate',
				]
			);

			$this->add_control(
				'content_color',
				[
					'label'     => esc_html__( 'Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .evaluate' => 'color : {{VALUE}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .evaluate' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .evaluate' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'selector' => '{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .name',
				]
			);

			$this->add_control(
				'author_name_color',
				[
					'label'     => esc_html__( 'Color Author', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'
						{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .name' => 'color : {{VALUE}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'selector' => '{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .job',
				]
			);

			$this->add_control(
				'job_color',
				[
					'label'     => esc_html__( 'Color Job', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'
						{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .job' => 'color : {{VALUE}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-testimonial.version-3 .slide-testimonials .owl-item .item .info-content .client-info .name-job .job' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();
		###############  end section job  ###############

	}

	protected function render() {

		$settings = $this->get_settings();
		$tab_item = $settings['tab_item'];
		$icon = $settings['class_icon'] ? $settings['class_icon'] : '';

		$data_options['items']              = $settings['item_number'];
		$data_options['slideBy']            = $settings['slides_to_scroll'];
		$data_options['margin']             = $settings['margin_items'];
		$data_options['autoplayHoverPause'] = $settings['pause_on_hover'] === 'yes' ? true : false;
		$data_options['loop']               = $settings['infinite'] === 'yes' ? true : false;
		$data_options['autoplay']           = $settings['autoplay'] === 'yes' ? true : false;
		$data_options['autoplayTimeout']    = $settings['autoplay_speed'];
		$data_options['smartSpeed']         = $settings['smartspeed'];
		$data_options['dots']               = $settings['dot_control'] === 'yes' ? true : false;
		$data_options['rtl']				= is_rtl() ? true: false;
		$data_options['nav']	 = false;


		?>
			<section class="ova-testimonial version-3">

					<div class="slide-testimonials owl-carousel owl-theme slide-testimonials-version-3" data-options="<?php echo esc_attr( json_encode($data_options) ); ?>">
						<?php if( !empty($tab_item) ) : foreach ( $tab_item as $item ) : ?>
							<div class="item">
								
									<?php if( $item['testimonial'] != '' ) : ?>
									<p class="evaluate">
										<?php echo esc_html( $item['testimonial'] ); ?>
									</p>
									<?php endif; ?>
									<div class="info-content">
										<div class="client-info">
											<div class="client">

												<?php if( $item['link']['url'] ) {  
													$target = $item['link']['is_external'] ? ' target="_blank"' : '';
													?>
													<a href="<?php echo esc_url( $item['link']['url'] ); ?>" <?php printf( $target ); ?> >
														<?php if( $item['image_author'] != '' ) { ?>
															<?php $alt = isset( $item['name_author'] ) && $item['name_author'] ? $item['name_author'] : esc_html__( 'testimonial','tripgo' ); ?>
															<img src="<?php echo esc_attr( $item['image_author']['url'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" >
														<?php } ?>
													</a>

													<?php } else {?>
														<?php if( $item['image_author'] != '' ) { ?>
															<?php $alt = isset( $item['name_author'] ) && $item['name_author'] ? $item['name_author'] : esc_html__( 'testimonial','tripgo' ); ?>
															<img src="<?php echo esc_attr( $item['image_author']['url'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" >
														<?php } ?>
													<?php } ?>

											</div>
											
											<div class="name-job">
												<?php if( $item['link']['url'] ) {  
													$target = $item['link']['is_external'] ? ' target="_blank"' : '';
														if( $item['name_author'] != '' ) { ?>
															<h6 class="name second_font">
																<a href="<?php echo esc_url( $item['link']['url'] ); ?>" <?php printf( $target ); ?> >
																	<?php echo esc_html( $item['name_author'] ); ?>
																</a>
															</h6>	
														<?php } 
													} else {
														if( $item['name_author'] != '' ) { ?>
															<h6 class="name">
																<?php echo esc_html( $item['name_author'] ); ?>
															</h6>
													<?php } 
												} ?>

												<?php if( $item['job'] != '' ) { ?>
													<p class="job">
														<?php echo esc_html( $item['job'] ); ?>
													</p>
												<?php } ?>
											</div>

										</div>
									</div>

									<div class="quote">
										<?php 
									        \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
									    ?>
									</div>

									<div class=" line1"></div>
									<div class=" line2"></div>
							</div>
							
						<?php endforeach; endif; ?>
					</div>

			</section>
		
		<?php
	}
	// end render
}

$widgets_manager->register( new Tripgo_Elementor_Testimonial_3() );

