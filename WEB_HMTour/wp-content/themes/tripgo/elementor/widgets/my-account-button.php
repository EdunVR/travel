<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_My_Account_Button extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_my_account_button';
	}

	public function get_title() {
		return esc_html__( 'My Account Button', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-dual-button';
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
			$this->add_control(
				'text_my_account_button',
				[
					'label' => esc_html__( 'Text My Account Button', 'tripgo' ),
					'type' 	=> \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'default' => esc_html__( 'My account', 'tripgo' ),
				]
			);

			$this->add_control(
				'text_login_button',
				[
					'label' => esc_html__( 'Text Login Button', 'tripgo' ),
					'type' 	=> \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'default' => esc_html__( 'Login', 'tripgo' ),
				]
			);

			$this->add_control(
				'text_signup_button',
				[
					'label' => esc_html__( 'Text Sign Up Button', 'tripgo' ),
					'type' 	=> \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'default' => esc_html__( 'Sign Up', 'tripgo' ),
				]
			);

			$this->add_control(
				'logout_status',
				[
					'label' => __( 'Logout Status', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'tripgo' ),
					'label_off' => __( 'No', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);

		$this->end_controls_section();

		//SECTION TAB STYLE button
		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'content_typography_title_btn',
					'label' => esc_html__( 'Typography', 'tripgo' ),
					'selector' => '{{WRAPPER}} .ova-my-account-button a.ma-button',
					
				]
			);
			
			$this->add_responsive_control(
				'padding_button',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-my-account-button a.ma-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'border_radius_button',
				[
					'label' => esc_html__( 'Border radius', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-my-account-button a.ma-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

					$this->add_control(	
						'color_title_btn',
						[
							'label' => esc_html__( 'Login Color', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.login-button' => 'color : {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'color_button_background',
						[
							'label' => esc_html__( 'Login Background ', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.login-button' => 'background-color : {{VALUE}};',
							],
						]
					);

					$this->add_control(	
						'color_title_signup_btn',
						[
							'label' => esc_html__( 'Singup Color', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.singup-button' => 'color : {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'color_signup_button_background',
						[
							'label' => esc_html__( 'Singup Background ', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.singup-button' => 'background-color : {{VALUE}};',
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
							'label' => esc_html__( 'Login Color', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.login-button:hover' => 'color : {{VALUE}} ;',
							],
						]
					);

					$this->add_control(
						'color_button_hover_background',
						[
							'label' => esc_html__( 'Login Background', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.login-button:hover' => 'background-color : {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'color_title_signup_btn_hover',
						[
							'label' => esc_html__( 'Singup Color', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.singup-button:hover' => 'color : {{VALUE}} ;',
							],
						]
					);

					$this->add_control(
						'color_signup_button_hover_background',
						[
							'label' => esc_html__( 'Singup Background', 'tripgo' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-my-account-button a.ma-button.singup-button:hover' => 'background-color : {{VALUE}};',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
		//END SECTION TAB STYLE button	
	}

	// Render Template Here
	protected function render() {
		$settings = $this->get_settings();

		$text_my_account_button = $settings['text_my_account_button'];
		$text_login_button 		= $settings['text_login_button'];
		$text_signup_button 	= $settings['text_signup_button'];

		$logout_status = $settings['logout_status'];

		$my_account_page_url 	= get_permalink( get_option('woocommerce_myaccount_page_id') );

		?>

		<?php if ( is_user_logged_in() && $logout_status != "yes" ) { 
			$current_user = wp_get_current_user();
		?>
			<div class="ova-my-account-button logged-in">
				<?php if ( ($current_user instanceof WP_User) ) {
			        echo get_avatar( $current_user->ID, 30 );
			    } ?>
				<a href="<?php echo esc_url($my_account_page_url); ?>">
					<?php echo esc_html($text_my_account_button);?>
				</a>
			</div>
		<?php } else { ?>
			<div class="ova-my-account-button logged-out">
				<a class="ma-button login-button" href="<?php echo esc_url($my_account_page_url); ?>">
					<?php echo esc_html($text_login_button);?>
				</a>
				<a class="ma-button singup-button" href="<?php echo esc_url($my_account_page_url); ?>">
					<?php echo esc_html($text_signup_button);?>
				</a>
			</div>
		<?php }
	}
	
}
$widgets_manager->register( new Tripgo_Elementor_My_Account_Button() );