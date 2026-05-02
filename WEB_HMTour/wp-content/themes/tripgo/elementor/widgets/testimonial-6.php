<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Testimonial_6 extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_testimonial_6';
	}

	public function get_title() {
		return esc_html__( 'Ova Testimonial 6', 'tripgo' );
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
				'background_image',
				[
					'label' => esc_html__( 'Choose Backround Image', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);

			$this->add_control(
				'icon',
				[
					'label' => esc_html__( 'Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'fas fa-quote-left',
						'library' => 'all',
					],
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
				'name_author',
				[
					'label'   => esc_html__( 'Author Name', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => 'Henry M. Becerra',
				]
			);

			$this->add_control(
				'job',
				[
					'label'   => esc_html__( 'Job', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'CEO & Founder', 'tripgo' ),
				]
			);

			$this->add_control(
				'testimonial',
				[
					'label'   => esc_html__( 'Testimonial ', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'default' => esc_html__( 'This is due to their excellent service, competitive pricing and customer support. It’s throughly refresing to get such', 'tripgo' ),
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
					'separator' => 'before'
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
		/*****************  END SECTION CONTENT ******************/

		/* General */
		$this->start_controls_section(
				'general_style_section',
				[
					'label' => esc_html__( 'General', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'general_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'general_background',
				[
					'label' => esc_html__( 'Backround', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6' => 'background: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Testimonial */
		$this->start_controls_section(
				'desc_style_section',
				[
					'label' => esc_html__( 'Testimonial', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'desc_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'desc_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-6 .desc',
				]
			);

			$this->add_control(
				'desc_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .desc' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Icon */
		$this->start_controls_section(
				'icon_style_section',
				[
					'label' => esc_html__( 'Icon', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'icon_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .author .img .icon i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ova-testimonial-6 .author .img .icon svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'icon_bg',
				[
					'label' => esc_html__( 'Background', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .author .img .icon' => 'background: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Name */
		$this->start_controls_section(
				'name_style_section',
				[
					'label' => esc_html__( 'Author Name', 'tripgo' ),
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
						'{{WRAPPER}} .ova-testimonial-6 .author .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'name_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-6 .author .name',
				]
			);

			$this->add_control(
				'name_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .author .name' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Job */
		$this->start_controls_section(
				'job_style_section',
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
						'{{WRAPPER}} .ova-testimonial-6 .author .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'job_typography',
					'selector' => '{{WRAPPER}} .ova-testimonial-6 .author .job',
				]
			);

			$this->add_control(
				'job_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .author .job' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		/* Rating */
		$this->start_controls_section(
				'rating_style_section',
				[
					'label' => esc_html__( 'Rating', 'tripgo' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'rating_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .author .rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'rating_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-testimonial-6 .author .rating i' => 'color: {{VALUE}}',
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

		$settings 				= $this->get_settings();
		$testimonial 			= $settings['testimonial'];
		$name_author 			= $settings['name_author'];
		$icon 					= $settings['icon'];
		$background_image 		= $settings['background_image'];
		$background_image_url 	= $background_image ? $background_image['url'] : Utils::get_placeholder_image_src();
		$image_author 			= $settings['image_author'];
		$image_author_url 		= $image_author ? $image_author['url'] : Utils::get_placeholder_image_src();
		$image_author_alt 		= $name_author;
		$image_author_title 	= $name_author;
		$job 					= $settings['job'];
		$show_rating 			= $settings['show_rating'];
		$rating			        = $settings['rating'];

		if ( $image_author && $image_author['id'] ) {
			$image_author_alt   = get_post_meta( $image_author['id'], '_wp_attachment_image_alt', true);
			$image_author_title = get_the_title( $image_author['id'] );
		}

		?>
			<div class="ova-testimonial-6">
				<div class="background-img" style="background-image: url('<?php echo esc_url( $background_image_url ); ?>');"></div>

				<div class="wrapper">
					<?php if ( $testimonial ): ?>
						<p class="desc"><?php echo esc_html( $testimonial ); ?></p>
					<?php endif; ?>

					<div class="author">
						<?php if ( !empty($icon['value']) || !empty($image_author_url) ) { ?>
							<div class="img">
								<?php if ( !empty($icon['value']) ) { ?>
									<div class="icon">
										<?php \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
									</div>
								<?php } ?>
								<?php if ( !empty($image_author_url) ) { ?>
									<img src="<?php echo esc_url( $image_author_url ); ?>" alt="<?php echo esc_attr( $image_author_alt ); ?>">
								<?php } ?>
							</div>
						<?php } ?>

						<div class="info">
							<h3 class="name"><?php echo esc_html( $name_author ); ?></h3>
							<p class="job"><?php echo esc_html( $job ); ?></p>
						
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
					</div>
				</div>
			</div>
		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Testimonial_6() );