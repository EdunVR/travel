<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Tripgo_Elementor_Ova_Image_Gallery extends Widget_Base {

	
	public function get_name() {
		return 'tripgo_elementor_ova_image_gallery';
	}

	
	public function get_title() {
		return esc_html__( 'Image Gallery', 'tripgo' );
	}

	
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	
	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		wp_enqueue_style( 'tripgo-fancybox', get_template_directory_uri().'/assets/libs/fancybox/fancybox.css' );
		wp_enqueue_script( 'tripgo-fancybox', get_template_directory_uri().'/assets/libs/fancybox/fancybox.umd.js', array('jquery'));

		return [ 'tripgo-elementor-ova-image-gallery' ];
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
				'column',
				[
					'label' => esc_html__( 'Column', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'three_column',
					'options' => [
						'two_column' => esc_html__( '2 Columns', 'tripgo' ),
						'three_column' => esc_html__( '3 Columns', 'tripgo' ),
						'four_column' => esc_html__( '4 Columns', 'tripgo' ),
					],
				]
			);

			$this->add_control(
				'show_title',
				[
					'label'   => esc_html__( 'Show Title', 'tripgo' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'tripgo' ),
						'no'  => esc_html__( 'No', 'tripgo' ),
					],
					'frontend_available' => true,
				]
			);

			//title
			$this->add_control(
				'title',
				[
					'label' => esc_html__( 'Title', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Photo Gallery', 'tripgo' ),
					'placeholder' => esc_html__( 'Type your title here', 'tripgo' ),
					'condition' => [
						'show_title' => 'yes',
					],
				]
			);

			// Add Class control
			$this->add_control(
				'ova_image_gallery',
				[
					'label' => esc_html__( 'Add Images', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::GALLERY,
					'default' => [],
				]
			);

			$this->add_control(
				'icon',
				[
					'label' => esc_html__( 'Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'fab fa-instagram',
						'library' 	=> 'all',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Image_Size::get_type(),
				[
					'name' => 'medium', // Usage: `{name}_size` and `{name}_custom_dimension`
					'exclude' => [ 'custom' ],
					'default' => 'medium',
					'separator' => 'none',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ova_image_gallery_style',
			[
				'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				'label' => esc_html__( 'Content', 'tripgo' ),
			]
		);

			$this->add_responsive_control(
				'gap',
				[
					'label' => esc_html__( 'Gap', 'tripgo' ),
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
						'{{WRAPPER}} .ova-image-gallery-ft' => 'gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'overlay_opacity',
				[
					'label' => esc_html__( 'Overlay Hover Opacity', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0.84,
					],
					'range' => [
						'px' => [
							'max' => 1,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-image-gallery-ft .item-fancybox-ft:hover .overlay' => 'opacity: {{SIZE}};',
					],
					
				]
			);

	        $this->add_control(
				'overlay_color',
				[
					'label' 	=> esc_html__( 'Overlay Hover Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-image-gallery-ft .item-fancybox-ft .overlay' => 'background-color: {{VALUE}};',
					],
				]
			);


		$this->end_controls_section();

		/*****************************************************************
						START SECTION TITLE
		******************************************************************/

		$this->start_controls_section(
			'title_section',
			[
				'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				'label' => esc_html__( 'Title', 'tripgo' ),
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_typography',
					'selector' => '{{WRAPPER}} .ova-image-gallery .title',
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' => esc_html__( 'Text Color', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-image-gallery .title' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'title_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors' => [
						'{{WRAPPER}} .ova-image-gallery .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		/****************************  END SECTION TITLE *********************/
		
	}

	// Render Template Here
	protected function render() {

		$settings 	= $this->get_settings();

		$title 					= $settings['title'];
		$column 				= $settings['column'];
		$list_image 			= $settings['ova_image_gallery'];
		$icon 					= $settings['icon'] ? $settings['icon'] : '';
		$show_title 			= $settings['show_title'];

		?>

		<div class="ova-image-gallery">

			<?php if( $show_title == 'yes' && !empty($title) ) : ?>
				<h3 class="title">
					<?php echo esc_html( $title ); ?>	
				</h3>
			<?php endif; ?>
            
            <?php if( !empty($list_image) ) : ?>
				<div class="ova-image-gallery-ft <?php echo esc_attr( $column ); ?>">
					<?php foreach( $list_image as $item ): ?>
						<?php 

							$image_id 		= $item['id']; 
							$url 	  		= $item['url'] ;
		                    $thumbnail_url  = wp_get_attachment_image_src( $image_id, $settings['medium_size'] )[0];

		                    $alt 			= get_post_meta($image_id, '_wp_attachment_image_alt', true) ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : esc_html__('Gallery Slide','tripgo');  

		                    $caption        = wp_get_attachment_caption( $image_id );
		                        
		                    if ( $caption == '') {
		                    	$caption = $alt;
		                    }

						?>
							<a href="javascript:void(0)" data-src="<?php echo esc_url( $url ); ?>" class="item-fancybox-ft"  data-fancybox="image-gallery-ft"  data-caption="<?php echo esc_attr($caption); ?>">
								
								<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">

								<div class="overlay">
									<?php if( $icon ){ ?>
										<div class="icon">
											<?php 
										        \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
										    ?>
										</div>	
									<?php } ?>
								</div>
							</a>
					<?php endforeach; ?>
					
				</div> 
			<?php endif; ?>

		</div>	

		<?php
	}
}
$widgets_manager->register( new Tripgo_Elementor_Ova_Image_Gallery() );