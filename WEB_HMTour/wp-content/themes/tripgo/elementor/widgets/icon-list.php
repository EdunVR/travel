<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Icon_List extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_icon_list';
	}

	public function get_title() {
		return esc_html__( 'Ova Icon List', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
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
			
			// Add Class control

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'icon',
				[
					'label' => esc_html__( 'Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'fas fa-check',
						'library' => 'all',
					],
				]
			);

			$repeater->add_control(
				'title',
				[
					'label' 	=> esc_html__( 'Title', 'tripgo' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> esc_html__('723+ Destinations', 'tripgo' ),
				]
			);

            $repeater->add_control(
				'desc',
				[
					'label' 		=> esc_html__( 'Description', 'tripgo' ),
					'type' 			=> Controls_Manager::TEXTAREA,
					'default' 		=> esc_html__('Available, but the majority have suffered simply', 'tripgo' ),
				]
			);

			$repeater->add_responsive_control(
				'item_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'tripgo' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-icon-list {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ico_items',
				[
					'label' 	=> esc_html__( 'Items', 'tripgo' ),
					'type' 		=> Controls_Manager::REPEATER,
					'fields' 	=> $repeater->get_controls(),
					'default' 	=> [
						[
							'title' 	=> esc_html__( '723+ Destinations', 'tripgo' ),
						],
						[
							'title' 	=> esc_html__( 'Best Price Gurantee', 'tripgo' ),
						],
						[
							'title' 	=> esc_html__( 'Top Notch Support', 'tripgo' ),
						],
					
					],
					'title_field' => '{{{ title }}}',
				]
			);

		$this->end_controls_section();

		/* Begin Item Style */
		$this->start_controls_section(
            'item_style',
            [
               'label' => esc_html__( 'Item', 'tripgo' ),
               'tab' 	=> Controls_Manager::TAB_STYLE,
            ]
        );

			$this->add_control(
				'item_bgcolor',
				[
					'label' 	=> esc_html__( 'Background', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-icon-list .item' => 'background: {{VALUE}};',
					],
				]
			);

		    $this->add_responsive_control(
	            'item_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'tripgo' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ova-icon-list .item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

			$this->add_responsive_control(
				'item_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'tripgo' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .ova-icon-list .item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'item_box_shadow',
					'label' => esc_html__( 'Box Shadow', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-icon-list .item',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'item_border',
					'label' => esc_html__( 'Border', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-icon-list .item',
				]
			);

        $this->end_controls_section();
		/* End Item style */

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'icon_size',
				[
					'label' => esc_html__( 'Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-icon-list .item i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ova-icon-list .item svg' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'color_icon',
				[
					'label' => esc_html__( 'Color', 'tripgo' ),
					'type' 	=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-icon-list .item i' => 'color : {{VALUE}};',
						'{{WRAPPER}} .ova-icon-list .item svg' => 'fill : {{VALUE}};',
						'{{WRAPPER}} .ova-icon-list .item svg path' => 'fill : {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'selector' 	=> '{{WRAPPER}} .ova-icon-list .item .title',
				]
			);

			$this->add_control(
				'color_title',
				[
					'label' 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-icon-list .item .title' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'padding_title',
				[
					'label' 		=> esc_html__( 'Padding', 'tripgo' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-icon-list .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'margin_title',
				[
					'label' 		=> esc_html__( 'Margin', 'tripgo' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-icon-list .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

        $this->end_controls_section();

		$this->start_controls_section(
			'section_desc_style',
			[
				'label' => esc_html__( 'Description', 'tripgo' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'desc_typography',
					'selector' 	=> '{{WRAPPER}} .ova-icon-list .item .desc',
				]
			);

			$this->add_control(
				'color_desc',
				[
					'label' 	=> esc_html__( 'Color', 'tripgo' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-icon-list .item .desc' => 'color : {{VALUE}};',
					],
				]
			);

        $this->end_controls_section();
	}

	// Render Template Here
	protected function render() {
		$settings = $this->get_settings();

        $items = $settings['ico_items'];

		?>

			 <div class="ova-icon-list">
				<?php if( !empty( $items ) ) : ?>
					<?php foreach( $items as $item ): $item_id = 'elementor-repeater-item-' . $item['_id'];?>
						<div class="item <?php echo esc_attr( $item_id ); ?>">
							<?php if(!empty($item['icon']['value']) ) { ?>
								<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
							<?php } ?>
							<div class="info">
								<h3 class="title">
									<?php echo esc_html( $item['title'] );?>
								</h3>
								<?php if(!empty($item['desc'])) { ?>
									<p class="desc">
										<?php echo esc_html( $item['desc'] );?>
									</p>
								<?php } ?>
							</div>
						</div>
					<?php endforeach; 
				endif; ?>
			</div>

		<?php
	}

	
}
$widgets_manager->register( new Tripgo_Elementor_Icon_List() );