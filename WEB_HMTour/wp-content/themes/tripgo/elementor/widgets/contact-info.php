<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Contact_Info extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_contact_info';
	}

	public function get_title() {
		return esc_html__( 'Contact Info', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-email-field';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ '' ];
	}

	protected function register_controls() {

		/**
		 * Content Tab
		 */
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
				
			]
		);

			$this->add_control(
				'icon',
				[
					'label' => esc_html__( 'Class Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'ovaicon-facebook-logo',
						'library' 	=> 'all',
					],
				]
			);

			$this->add_control(
				'label',
				[
					'label' => esc_html__( 'Label', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__('Label', 'tripgo'),
				]
			);



			$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'type',
					[
						'label' => esc_html__( 'Type', 'tripgo' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'email',
						'options' => [
							'email' => esc_html__('Email', 'tripgo'),
							'phone' => esc_html__('Phone', 'tripgo'),
							'link' => esc_html__('Link', 'tripgo'),
							'text' => esc_html__('Text', 'tripgo'),
						]
					]
				);

				$repeater->add_control(
					'email_label',
					[
						'label'   => esc_html__( 'Email Label', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
						'description' => esc_html__( 'email@company.com', 'tripgo' ),
						'condition' => [
							'type' => 'email',
						]

					]
				);

				$repeater->add_control(
					'email_address',
					[
						'label'   => esc_html__( 'Email Adress', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
						'description' => esc_html__( 'email@company.com', 'tripgo' ),
						'condition' => [
							'type' => 'email',
						]

					]
				);


				$repeater->add_control(
					'phone_label',
					[
						'label'   => esc_html__( 'Phone Label', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
						'description' => esc_html__( '+012 (345) 678', 'tripgo' ),
						'condition' => [
							'type' => 'phone',
						]

					]
				);

				$repeater->add_control(
					'phone_address',
					[
						'label'   => esc_html__( 'Phone Adress', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
						'description' => esc_html__( '+012345678', 'tripgo' ),
						'condition' => [
							'type' => 'phone',
						]

					]
				);

				$repeater->add_control(
					'link_label',
					[
						'label'   => esc_html__( 'Link Label', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
						'description' => esc_html__( 'https://your-domain.com', 'tripgo' ),
						'condition' => [
							'type' => 'link',
						]

					]
				);

				$repeater->add_control(
					'link_address',
					[
						'label'   => esc_html__( 'Link Adress', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::URL,
						'description' => esc_html__( 'https://your-domain.com', 'tripgo' ),
						'condition' => [
							'type' => 'link',
						],
						'show_external' => false,
						'default' => [
							'url' => '#',
							'is_external' => false,
							'nofollow' => false,
						],

					]
				);

				$repeater->add_control(
					'text',
					[
						'label'   => esc_html__( 'Text', 'tripgo' ),
						'type'    => \Elementor\Controls_Manager::TEXT,
						'description' => esc_html__( 'Your text', 'tripgo' ),
						'condition' => [
							'type' => 'text',
						]

					]
				);

				$this->add_control(
					'items_info',
					[
						'label'       => esc_html__( 'Items Info', 'tripgo' ),
						'type'        => Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'default' => [
							[
								'type' => 'email',
								'email_label' => esc_html__('email@company.com', 'tripgo'),
								'email_address' => esc_html__('email@company.com', 'tripgo'),
							],
							
						],
						'title_field' => '{{{ type }}}',
					]
				);

			

		$this->end_controls_section(); // End Content Tab



		/**
		 * Icon Style Tab
		 */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'icon_fontsize',
				[
					'label' => esc_html__( 'Font Size', 'tripgo' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 300,
							'step' => 1,
						]
					],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .icon' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ova-contact-info .icon svg' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'icon_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .icon' => 'color : {{VALUE}};',
						'{{WRAPPER}} .ova-contact-info .icon svg, {{WRAPPER}} .ova-contact-info .icon svg path' => 'fill : {{VALUE}};'
					],
				]
			);

			$this->add_control(
				'icon_background',
				[
					'label' => esc_html__( 'Background', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .icon' => 'background-color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'icon_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'icon_padding',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'border_icon',
						'label' 	=> esc_html__( 'Border', 'tripgo' ),
						'selector' 	=> '{{WRAPPER}} .ova-contact-info .icon',
					]
				);

			$this->add_responsive_control(
				'border_radius_icon',
				array(
					'label'      => esc_html__( 'Border Radius', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .ova-contact-info .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);


		$this->end_controls_section(); // End Icon Style Tab

		/**
		 * Label Style Tab
		 */
		$this->start_controls_section(
			'section_label_style',
			[
				'label' => esc_html__( 'Label', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			
			$this->add_control(
				'label_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .contact .label' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'label_typography',
					'selector' => '{{WRAPPER}} .ova-contact-info .contact .label',
				]
			);

			$this->add_responsive_control(
				'label_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .contact .label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section(); // End Label Style Tab


		/**
		 * Info Style Tab
		 */
		$this->start_controls_section(
			'section_info_style',
			[
				'label' => esc_html__( 'Info', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			

			$this->add_control(
				'info_color',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .contact .info .item' => 'color : {{VALUE}};',
						'{{WRAPPER}} .ova-contact-info .contact .info .item a' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'info_color_hover',
				[
					'label' => esc_html__( 'Link Color hover', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .contact .info .item a:hover' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'info_typography',
					'selector' => '{{WRAPPER}} .ova-contact-info .contact .info .item, {{WRAPPER}} .ova-contact-info .contact .info .item a',
				]
			);

			$this->add_responsive_control(
				'info_margin',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-info .contact .info .item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
                'info_alignment',
                [
                    'label' => esc_html__( 'Alignment', 'tripgo' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'flex-start' => [
                            'title' => esc_html__( 'Flex start', 'tripgo' ),
                            'icon' => 'eicon-v-align-top',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'tripgo' ),
                            'icon' => 'eicon-v-align-middle',
                        ],
                        'end' => [
                            'title' => esc_html__( 'End', 'tripgo' ),
                            'icon' => 'eicon-v-align-bottom',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ova-contact-info' => 'align-items: {{VALUE}};',
                    ],
                ]
            );


		$this->end_controls_section(); // End Label Style Tab

	}

	protected function render() {

		$settings = $this->get_settings();

		$icon = $settings['icon'] ? $settings['icon'] : '';
		$label = $settings['label'] ? $settings['label'] : '';
		$items_info = $settings['items_info'];
		
		?>
			<div class="ova-contact-info">
				
				<?php if( $icon ){ ?>
					<div class="icon">
						<?php 
					        \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
					    ?>
					</div>	
				<?php } ?>
				

				<div class="contact">
					
					<?php if( $label ){ ?>
						<div class="label">
							<?php echo esc_html( $label ); ?>
						</div>
					<?php } ?>

					<ul class="info">
						<?php foreach( $items_info as $item ):

							$type 	= $item['type'];
							
							?>

								<li class="item">

									<?php switch ( $type ) {

										case 'email':

											$email_address = $item['email_address'];
											$email_label = $item['email_label'];
											if( $email_address && $email_label ){
											?>
												<a href="mailto:<?php echo esc_attr( $email_address ); ?> ">
													<?php echo esc_html( $email_label ); ?>
												</a>
											<?php
											}
											break;

										case 'phone':

											$phone_address = $item['phone_address'];
											$phone_label = $item['phone_label'];
											if( $phone_address && $phone_label ){
											?>
												<a href="tel:<?php echo esc_attr( $phone_address ) ?> ">
													<?php echo esc_html( $phone_label ); ?>
												</a>
											<?php
											}
											break;

										case 'link':

											$this->add_render_attribute( 'title' );

											$link_address = $item['link_address']['url'];
											$link_label = $item['link_label'];

											$title = $item['link_label'] ? $item['link_label'] : '';

											if ( ! empty( $item['link_address']['url'] ) ) {

												$this->add_link_attributes( 'url', $item['link_address'] );

												echo sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );

											}else{

												echo esc_html( $title );

											}
											
											break;

										case 'text':
											$text = $item['text'];
											?>
												<?php echo esc_html( $text ); ?>
											<?php
											break;
										default:
											break;
									} ?>
								</li>
							
						<?php endforeach; ?>
					</ul>

				</div>

			</div>

		<?php
	}
// end render

}
$widgets_manager->register( new Tripgo_Elementor_Contact_Info() );