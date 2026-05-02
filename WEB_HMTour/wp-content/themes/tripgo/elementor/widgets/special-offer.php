<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Tripgo_Elementor_Special_Offer extends Widget_Base {

	
	public function get_name() {
		return 'tripgo_elementor_special_offer';
	}

	
	public function get_title() {
		return esc_html__( 'Special Offer', 'tripgo' );
	}

	
	public function get_icon() {
		return 'eicon-image-box';
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
				'version',
				[
					'label' => esc_html__( 'Version', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'version_1',
					'options' => [
						'version_1'  => esc_html__( 'Version 1', 'tripgo' ),
						'version_2'  => esc_html__( 'Version 2', 'tripgo' ),
						'version_3'  => esc_html__( 'Version 3', 'tripgo' ),
						'version_4'  => esc_html__( 'Version 4', 'tripgo' ),	
						'version_5'  => esc_html__( 'Version 5', 'tripgo' ),
					],
				]
			);

			$this->add_control(
				'link_address',
				[
					'label'   => esc_html__( 'Link', 'tripgo' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'show_external' => false,
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
				'title',
				[
					'label' => esc_html__( 'Title', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => 'Special <br> Offers',
					'description' => esc_html__( 'Can use <br> tag for line breaks', 'tripgo'),
				]
			);
			
			$this->add_control(
				'sale_type',
				[
					'label' => esc_html__( 'Type Sale', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'save_up_to',
					'options' => [
						'normal' => esc_html__( 'Normal', 'tripgo' ),
						'up_to_off' => esc_html__( 'Sale Off', 'tripgo' ),
						'save_up_to'  => esc_html__( 'Save Up To', 'tripgo' ),	
					],
				]
			);

			$this->add_control(
				'sub_title_normal',
				[
					'label' => esc_html__( 'Sub Title', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Plan your next trip', 'tripgo'),
					'condition' => [
						'sale_type' => 'normal',
					],
				]
			);
			
			$this->add_control(
				'sub_title_on_both_side_1',
				[
					'label' => esc_html__( 'Sub Title 1', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Up to', 'tripgo'),
					'condition' => [
						'sale_type' => 'up_to_off',
					],
				]
			);

			$this->add_control(
				'sub_title_on_both_side_2',
				[
					'label' => esc_html__( 'Sub Title 2', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'off', 'tripgo'),
					'condition' => [
						'sale_type' => 'up_to_off',
					],
				]
			);

			$this->add_control(
				'sub_title_front',
				[
					'label' => esc_html__( 'Sub Title', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Want to save up to', 'tripgo'),
					'condition' => [
						'sale_type' => 'save_up_to',
					],
				]
			);

			$this->add_control(
				'discount_percent',
				[
					'label' => esc_html__( 'Discount Percent', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 100,
					'step' => 1,
					'default' => 30,
					'condition' => [
						'sale_type' => ['save_up_to','up_to_off'],
					],
				]
			);

			$this->add_control(
				'show_button',
				[
					'label' => esc_html__( 'Show button', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control(
				'text_button',
				[
					'label' => esc_html__( 'Text Button', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__('View Deals', 'tripgo'),
					'condition' => [
						'show_button' => 'yes',
					],
				]
			);

			$this->add_control(
				'icon',
				[
					'label' => __( 'Icon', 'tripgo' ),
					'type' => Controls_Manager::ICONS,
					'condition' => [
						'show_button' => 'yes',	
					],
				]
			);

			$this->add_responsive_control(
				'size_icon',
				[
					'label' 		=> esc_html__( 'Size', 'tripgo' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 40,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .btn-special-offer i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'show_button' => 'yes',	
					],
				]
			);

		$this->end_controls_section();

		// Image Style 
		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__( 'Image', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name' => 'background',
					'label' => esc_html__( 'Background', 'tripgo' ),
					'types' => [ 'classic', 'gradient', 'video' ],
					'selector' => '{{WRAPPER}} .ova-special-offer .mask',
				]
			);

			$this->add_responsive_control(
				'image_height',
				[
					'label' 		=> esc_html__( 'Height', 'tripgo' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%'],
					'range' => [
						'px' => [
							'min' => 300,
							'max' => 500,
							'step' => 10,
						],
						'%' => [
							'min' => 50,
							'max' => 100,
							'step' => 2,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .special-offer-img' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		//SECTION TAB STYLE CONTENT VERSION 5
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'version' => 'version_5'
				]
			]
		);

			$this->add_control(
				'bgcolor_content',
				[
					'label' => esc_html__( 'Background Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .special-offer-content' => 'background-color : {{VALUE}};'	
					],
				]
			);

			$this->add_responsive_control(
				'padding_content',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .special-offer-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		//END SECTION TAB STYLE CONTENT V5

		//SECTION TAB STYLE TITLE
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'content_typography_title',
					'label' => esc_html__( 'Typography', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-special-offer .title',
				]
			);

			$this->add_control(
				'color_title',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .title' => 'color : {{VALUE}};'
						
						
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'title_text_shadow',
					'label' => esc_html__( 'Text Shadow', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-special-offer .title',
				]
			);

			$this->add_responsive_control(
				'padding_title',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .title ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'content_typography_sub_title',
					'label' => esc_html__( 'Typography', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-special-offer .sub-title-wrapper .sub-title',
				]
			);

			$this->add_control(
				'color_sub_title',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .sub-title-wrapper .sub-title' => 'color : {{VALUE}};'
						
						
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'sub_title_text_shadow',
					'label' => esc_html__( 'Text Shadow', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-special-offer .sub-title-wrapper .sub-title',
				]
			);

			$this->add_responsive_control(
				'padding_sub_title',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .sub-title-wrapper .sub-title ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
		$this->end_controls_section();
		//END SECTION TAB STYLE SUB TITLE


		//SECTION TAB STYLE SALE
		$this->start_controls_section(
			'section_sale',
			[
				'label' => esc_html__( 'Discount', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'sale_type' => ['save_up_to','up_to_off'],
				],
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'content_typography_sale',
					'label' => esc_html__( 'Typography', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-special-offer .sub-title-wrapper .discount',
				]
			);

			$this->add_control(
				'color_sale',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .sub-title-wrapper .discount' => 'color : {{VALUE}};'
						
						
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'sale_text_shadow',
					'label' => esc_html__( 'Text Shadow', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-special-offer .sub-title-wrapper .discount',
				]
			);

			$this->add_responsive_control(
				'padding_sale',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .sub-title-wrapper .discount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		//END SECTION TAB STYLE SALE

		//SECTION TAB STYLE button
		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'style_tabs_button'
		);

			$this->start_controls_tab(
				'style_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'tripgo' ),
				]
			);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'content_typography_title_btn',
						'label' => esc_html__( 'Typography', 'tripgo' ),
						'selector' => '{{WRAPPER}} .ova-special-offer .btn-special-offer .text',
						
					]
				);

				$this->add_control(	
					'color_title_btn',
					[
						'label' => esc_html__( 'Color', 'tripgo' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-special-offer .btn-special-offer .text' => 'color : {{VALUE}};',
						],
					]
				);

				$this->add_control(	
					'color_icon_btn',
					[
						'label' => esc_html__( 'Icon Color', 'tripgo' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-special-offer .btn-special-offer i' => 'color : {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'color_button_background',
					[
						'label' => esc_html__( 'Background ', 'tripgo' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-special-offer .btn-special-offer' => 'background-color : {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'style_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'tripgo' ),
				]
			);

				$this->add_control(
					'color_title_btn_hover',
					[
						'label' => esc_html__( 'Color', 'tripgo' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-special-offer .btn-special-offer:hover span' => 'color : {{VALUE}} ;',
						],
					]
				);

				$this->add_control(
					'color_button_hover_background',
					[
						'label' => esc_html__( 'Background', 'tripgo' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-special-offer .btn-special-offer:hover' => 'background-color : {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		    $this->add_responsive_control(
				'margin_button',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .btn-special-offer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'padding_button',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-special-offer .btn-special-offer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		//END SECTION TAB STYLE button	
	}

	// Render Template Here
	protected function render() {

		$settings     = $this->get_settings();

		$version      = $settings['version'];
		$title        = $settings['title'];

		//image
		$img_url      = $settings['image']['url'];
		$img_id       = $settings['image']['id'];
		$image_title  = get_the_title( $img_id );
		$image_alt    = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

		// link
		$link         = $settings['link_address']['url'];
		$target       = $settings['link_address']['is_external'] ? ' target="_blank"' : '';
        
        // discount percent
		$discount_percent  = $settings['discount_percent'];
        
        // type sale
        $type               =  $settings['sale_type'];
        $sub_normal         =  $settings['sub_title_normal'];
		$sub_front          =  $settings['sub_title_front'];
		$sub_on_both_side_1 =  $settings['sub_title_on_both_side_1'];
		$sub_on_both_side_2 =  $settings['sub_title_on_both_side_2'];
	    
	    // button	
		$show_btn	= $settings['show_button'];
		$text_btn	= $settings['text_button'];
		$icon_btn	= $settings['icon']['value'];

		?>

			<div class="ova-special-offer ova-special-offer-<?php echo esc_attr( $version ); ?>">

				<?php if( !empty( $link) && ( ($version == "version_4")  || ($version == "version_5") ) ) { ?>
		        	<a href="<?php echo esc_url( $link ); ?>"<?php printf( $target ); ?>>
		        <?php } ?>

	                <!-- Mask ( Overlay Image ) -->
				    <div class="mask"></div>

					<img src="<?php echo esc_url( $img_url ); ?>" class="special-offer-img" title ="<?php echo esc_attr( $image_title ); ?>"  alt="<?php echo esc_attr( $image_alt );  ?>">

				<?php if( !empty( $link ) && ( ($version == "version_4")  || ($version == "version_5") ) ) { ?>
		        	</a>
		        <?php } ?>

			    <!-- Special Offer Content -->
				<div class="special-offer-content">
					
					<h3 class="title">
						<?php printf( $title ); ?>
					</h3>

					<div class="sub-title-wrapper">

						<?php if( $type == 'up_to_off' ) { ?>
							<span class="sub-title">
								<?php echo esc_html( $sub_on_both_side_1 ); ?>	
							</span> 
							<span class="discount">
								<?php echo esc_html( $discount_percent ) . '%'; ?>	
							</span> 
							<span class="sub-title">
								<?php echo esc_html( $sub_on_both_side_2 ); ?>	
							</span>
						<?php } ?>

						<?php if( $type == 'normal' ) { ?>	
							<span class="sub-title">
								<?php echo esc_html( $sub_normal ); ?>	
							</span> 
						<?php } ?>

						<?php if( $type == 'save_up_to' ) { ?>
							<span class="sub-title">
								<?php echo esc_html( $sub_front ) ; ?>	
							</span> 
							<?php if( !empty($discount_percent) ) { ?>
								<span class="discount">
									<?php echo esc_html( $discount_percent ) . '%'; ?>	
								</span> 
							<?php } ?>
						<?php } ?>
					</div>
					
					<!-- Button -->
					<?php if( $show_btn == 'yes' && ( !empty($text_btn) || !empty($icon_btn) ) ) { ?>
						
						<?php if( $link == '' ) : ?>
							<div class="btn-special-offer">
								<span class="text"> 
									<?php echo esc_html( $text_btn ) ?> 
								</span>
								<?php if(!empty( $icon_btn )) : ?>
									<i class="<?php echo esc_attr( $icon_btn ); ?>"></i>
								<?php endif; ?>
							</div>

						<?php else : ?>	
							<a href="<?php echo esc_url( $link ); ?>"<?php printf( $target ); ?>>
								<div class="btn-special-offer">
									<span class="text"> 
										<?php echo esc_html( $text_btn ) ?> 
									</span>
									<?php if(!empty( $icon_btn )) : ?>
										<i class="<?php echo esc_attr( $icon_btn ); ?>"></i>
									<?php endif; ?>
								</div>	
							</a>
						<?php endif; ?>  
						 
					<?php } ?>

				</div>

			</div>

		<?php
	}
}

$widgets_manager->register( new Tripgo_Elementor_Special_Offer() );