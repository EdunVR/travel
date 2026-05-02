<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Testimonial_5 extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_testimonial_5';
	}

	public function get_title() {
		return esc_html__( 'Ova Testimonial 5', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ '' ];
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
				'quote',
				[
					'label' => esc_html__( 'Quote', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'ovaicon ovaicon-right-quote',
						'library' => 'ovaicon',
					],
				]
			);

			$this->add_control(
				'evaluate',
				[
					'label' => esc_html__( 'Evaluate', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Good Experience', 'tripgo' ),
					'placeholder' => esc_html__( 'Type your evaluate here', 'tripgo' ),
				]
			);

			$this->add_control(
				'name_author',
				[
					'label'   => esc_html__( 'Author Name', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => _( 'Mila McSabbu'),
				]
			);

			$this->add_control(
				'job',
				[
					'label'   => esc_html__( 'Job', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => _( 'Freelance Designer'),
				]
			);

			$this->add_control(
				'image_author',
				[
					'label'   => esc_html__( 'Author Image', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);

			$this->add_control(
				'testimonial',
				[
					'label'   => esc_html__( 'Testimonial ', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'default' => esc_html__( 'OMG! I cannot believe that I have got a brand new landing page after getting appmax. It was super easy to edit and publish.I have got a brand new landing page.', 'tripgo' ),
				]
			);

			$this->add_control(
				'active',
				[
					'label' => esc_html__( 'Border Highlight', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Yes', 'tripgo' ),
					'label_off' => esc_html__( 'No', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);

			$this->add_control(
				'show_rating',
				[
					'label' => esc_html__( 'Show Rating', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control(
				'rating',
				[
					'label' => esc_html__( 'Rating', 'tripgo' ),
					'type' => Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 10,
					'step' => 0.1,
					'default' => 5,
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'show_rating' => 'yes'
					]
				]
			);
			
		$this->end_controls_section();

		/* General */
		$this->start_controls_section(
				'section_general_style',
				[
					'label' => esc_html__( 'General', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'item_background_color',
				[
					'label' => esc_html__( 'Background Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'item_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'item_border',
					'selector' => '{{WRAPPER}} .ova-testimonial-5',
				]
			);

			$this->add_control(
				'item_border_color_center',
				[
					'label' => esc_html__( 'Border Color Center', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5.ova-testimonial-template1 .owl-item.center' => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'template' => 'template1',
					],
				]
			);

			$this->add_control(
				'item_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'item_box_shadow',
					'selector' => '{{WRAPPER}} .ova-testimonial-5 ',
				]
			);

		$this->end_controls_section();

		/* Evaluate */
		$this->start_controls_section(
				'section_evaluate',
				[
					'label' => esc_html__( 'Evaluate', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'evaluate_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-5 .evaluate .text',
				]
			);

			$this->add_control(
				'evaluate_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .evaluate .text' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'evaluate_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .evaluate .text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Rating */
		$this->start_controls_section(
				'section_rating',
				[
					'label' => esc_html__( 'Rating', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'rating_size',
				[
					'label' => esc_html__( 'Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .rating i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'rating_spacing',
				[
					'label' => esc_html__( 'Spacing', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .rating i' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'rating_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .rating i' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'rating_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Quote */
		$this->start_controls_section(
				'section_quote_style',
				[
					'label' => esc_html__( 'Quote', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'quote_size',
				[
					'label' => esc_html__( 'Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .quote i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'quote_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .quote i' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'quote_position_margin',
				[
					'label'      => esc_html__( 'Margin', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-testimonial-5 .quote' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Content Style */
		$this->start_controls_section(
				'section_content_testimonial',
				[
					'label' => esc_html__( 'Content', 'tripgo' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'content_testimonial_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-5 .content',
				]
			);

			$this->add_control(
				'content_color',
				[
					'label'     => esc_html__( 'Color', 'tripgo' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .content' => 'color : {{VALUE}};',
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
						'{{WRAPPER}} .ova-testimonial-5 .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Image */
		$this->start_controls_section(
				'section_author_style',
				[
					'label' => esc_html__( 'Image', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'author_image_size',
				[
					'label' => esc_html__( 'Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .info .author-image img' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'author_image_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .info .author-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'author_image_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .info .author-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Name */
		$this->start_controls_section(
				'section_name',
				[
					'label' => esc_html__( 'Name', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'name_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-5 .info .name-job .name',
				]
			);

			$this->add_control(
				'name_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .info .name-job .name' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'name_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .info .name-job .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Job */
		$this->start_controls_section(
				'section_job',
				[
					'label' => esc_html__( 'Job', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'job_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-5 .info .name-job .job',
				]
			);

			$this->add_control(
				'job_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .info .name-job .job' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'job_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-5 .info .name-job .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

	}

	protected function get_rating($rating) {

		$settings = $this->get_settings();
		$rating_scale = 5;

		return [ $rating, $rating_scale ];
	}

	protected function render_stars( $icon, $rating ) {
		$rating_data = $this->get_rating($rating);
		$rating = (float) $rating_data[0];
		$floored_rating = floor( $rating );
		$stars_html = '';

		for ( $stars = 1.0; $stars <= $rating_data[1]; $stars++ ) {
			if ( $stars <= $floored_rating ) {
				$stars_html .= '<i class="elementor-star-full">' . $icon . '</i>';
			} elseif ( $floored_rating + 1 === $stars && $rating !== $floored_rating ) {
				$stars_html .= '<i class="elementor-star-' . ( $rating - $floored_rating ) * 10 . '">' . $icon . '</i>';
			} else {
				$stars_html .= '<i class="elementor-star-empty">' . $icon . '</i>';
			}
		}

		return $stars_html;
	}

	// Render Template Here
	protected function render() {
		$settings 		= $this->get_settings();
		$evaluate 		= $settings['evaluate'];
		$quote 			= $settings['quote'];
		$testimonial 	= $settings['testimonial'];
		$name_author 	= $settings['name_author'];
		$image_author 	= $settings['image_author'];
		$job 			= $settings['job'];
		$active 		= $settings['active'] == 'yes' ? 'active' : '';
		$show_rating    = $settings['show_rating'];
		$rating			= $settings['rating'];

		?>

		<div class="ova-testimonial-5 <?php echo esc_attr( $active ); ?>">
			<div class="wrap-evaluate">
				<div class="evaluate">
					<?php if( $evaluate != '' ) : ?>
						<p class="text">
							<?php echo esc_html( $evaluate ) ; ?>
						</p>
					<?php endif; ?>

					<!-- rating -->
					<?php if($show_rating == 'yes') {
						$icon = '&#xE934;';
						$rating_data 		 = $this->get_rating($rating);
						$textual_rating 	 = $rating_data[0] . '/' . $rating_data[1];
						$rating 			 = (float) $rating;

						$stars_element = '<div class="elementor-star-rating" title="'.$textual_rating.'">' . $this->render_stars( $icon, $rating ) . ' </div>';
					?>
						<?php if($rating != 0) : ?>
							<div class="rating">
								<?php print_r ( $stars_element ); ?>
							</div>
						<?php endif; ?>
					<?php } ?>
				</div>
                
                <?php if( $quote != '' ) : ?>
	                <span class="quote">
	                	<?php \Elementor\Icons_Manager::render_icon( $quote, [ 'aria-hidden' => 'true' ] ); ?>
	                </span>
				<?php endif; ?>
			</div>
			
			<?php if( $testimonial != '' ) : ?>
				<p class="content">
					<?php echo esc_html( $testimonial ); ?>
				</p>
			<?php endif; ?>
				
			<div class="info">
				<?php if( $image_author['url'] != '' ) :
					$alt = isset( $name_author ) && $name_author ? $name_author : esc_html__( 'testimonial','tripgo' ); 
				?>
					<div class="author-image">
						<img src="<?php echo esc_attr( $image_author['url'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" >
					</div>
				<?php endif; ?>

				<div class="name-job">
					<?php if( $name_author != '' ) : ?>
						<p class="name">
							<?php echo esc_html( $name_author ) ; ?>
						</p>
					<?php endif; ?>

					<?php if( $job  != '' ) : ?>
						<p class="job">
							<?php echo esc_html( $job )  ; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Testimonial_5() );