<?php
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Share_Buttons extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_share_buttons';
	}

	public function get_title() {
		return esc_html__( 'Share Buttons', 'tripgo' );
	}
	
	public function get_icon() {
		return 'eicon-share';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ 'tripgo-elementor-video' ];
	}
	
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Social Icons', 'tripgo' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'network',
				[
					'label' 	=> esc_html__( 'Network', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'facebook',
					'options' 	=> $this->get_networks(),
				]
			);

			$repeater->add_control(
				'app_id',
				[
					'label' 	=> esc_html__( 'App ID', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'condition' => [
	                	'network' => 'messenger',
	                ],
				]
			);

			$repeater->add_control(
				'redirect_uri',
				[
					'label' 	=> esc_html__( 'Redirect URL', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'condition' => [
	                	'network' => 'messenger',
	                ],
				]
			);

			$repeater->add_control(
				'custom_icon',
				[
					'label' => esc_html__( 'Custom Icon', 'tripgo' ),
					'type' 	=> \Elementor\Controls_Manager::ICONS,
				]
			);

			$repeater->add_control(
				'custom_link',
				[
					'label' => esc_html__( 'Custom Link', 'tripgo' ),
					'type' 	=> \Elementor\Controls_Manager::URL,
					'placeholder' => esc_html__( 'https://your-link.com', 'tripgo' ),
					'options' => [ 'url', 'is_external', 'nofollow' ],
					'default' => [
						'url' 			=> '',
						'is_external' 	=> true,
						'nofollow' 		=> true,
					],
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'custom_color',
				[
					'label' => esc_html__( 'Custom Color', 'tripgo' ),
					'type' 	=> \Elementor\Controls_Manager::COLOR,
					'selectors' => [
	                    '{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
	                    '{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}',
	                ],
				]
			);

			$repeater->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name' 		=> 'custom_background',
					'types' 	=> [ 'classic', 'gradient' ],
					'exclude' 	=> ['image'],
					'selector' 	=> '{{WRAPPER}} {{CURRENT_ITEM}}',
				]
			);

			$this->add_control(
				'share_buttons',
				[
					'label' 	=> esc_html__( 'Social Icons', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::REPEATER,
					'fields' 	=> $repeater->get_controls(),
					'default' => [
						[
							'network' => 'facebook',
						],
						[
							'network' => 'twitter',
						],
						[
							'network' => 'linkedin',
						],
					],
					'title_field' => '{{{ network }}}',
				]
			);

			$this->add_control(
				'column',
				[
					'label' 	=> esc_html__( 'Column', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> '0',
					'options' 	=> [
						'0' => esc_html__( 'Auto', 'tripgo' ),
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					],
				]
			);

			$this->add_control(
				'alignment',
				[
					'label' 	=> esc_html__( 'Alignment', 'tripgo' ),
					'type' 		=> \Elementor\Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' => [
							'title' => esc_html__( 'Left', 'tripgo' ),
							'icon' 	=> 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'tripgo' ),
							'icon' 	=> 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'tripgo' ),
							'icon' 	=> 'eicon-text-align-right',
						],
						'space-between' => [
							'title' => esc_html__( 'Justify', 'tripgo' ),
							'icon' => 'eicon-text-align-justify',
						],
					],
					'toggle' 	=> true,
					'selectors' => [
						'{{WRAPPER}} .ova-share-buttons' => 'justify-content: {{VALUE}};',
					],
					'condition' => [
						'column' => '0'
					]
				]
			);

			$this->add_control(
				'open_new_window',
				[
					'label' 		=> esc_html__( 'Open in new window', 'tripgo' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Yes', 'tripgo' ),
					'label_off' 	=> esc_html__( 'No', 'tripgo' ),
					'return_value' 	=> 'yes',
					'default' 		=> 'yes',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_style',
			[
				'label' => esc_html__( 'Share Buttons', 'tripgo' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'column_gap',
				[
					'label' => esc_html__( 'Columns Gap', 'tripgo' ),
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
						'{{WRAPPER}} .ova-share-buttons:not(.share-grid) .share-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ova-share-buttons.share-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'row_gap',
				[
					'label' => esc_html__( 'Rows Gap', 'tripgo' ),
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
						'{{WRAPPER}} .ova-share-buttons:not(.share-grid) .share-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ova-share-buttons.share-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'button_size',
				[
					'label' => esc_html__( 'Button Size', 'tripgo' ),
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
						'{{WRAPPER}} .ova-share-buttons .share-item .share-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'icon_size',
				[
					'label' => esc_html__( 'Icon Size', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem', 'custom' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 5,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-share-buttons .share-item .share-btn i' => 'font-size: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'button_border',
					'selector' 	=> '{{WRAPPER}} .ova-share-buttons .share-item .share-btn',
				]
			);

			$this->add_responsive_control(
				'button_border_radius',
				[
					'label' 		=> esc_html__( 'Button Border Radius', 'tripgo' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-share-buttons .share-item .share-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function get_networks( $network = '' ) {
		$args_network = array(
			'facebook' 		=> esc_html__( 'Facebook', 'tripgo' ),
			'messenger' 	=> esc_html__( 'Messenger', 'tripgo' ),
			'twitter' 		=> esc_html__( 'Twitter', 'tripgo' ),
			'linkedin' 		=> esc_html__( 'Linkedin', 'tripgo' ),
			'pinterest' 	=> esc_html__( 'Pinterest', 'tripgo' ),
			'reddit' 		=> esc_html__( 'Reddit', 'tripgo' ),
			'vk' 			=> esc_html__( 'VK', 'tripgo' ),
			'odnoklassniki' => esc_html__( 'OK', 'tripgo' ),
			'tumblr' 		=> esc_html__( 'Tumblr', 'tripgo' ),
			'digg' 			=> esc_html__( 'Digg', 'tripgo' ),
			'skype' 		=> esc_html__( 'Skype', 'tripgo' ),
			'stumbleupon' 	=> esc_html__( 'StumbleUpon', 'tripgo' ),
			'telegram' 		=> esc_html__( 'Telegram', 'tripgo' ),
			'pocket' 		=> esc_html__( 'Pocket', 'tripgo' ),
			'xing' 			=> esc_html__( 'Xing', 'tripgo' ),
			'whatsapp' 		=> esc_html__( 'Whatsapp', 'tripgo' ),
			'email' 		=> esc_html__( 'Email', 'tripgo' ),
			'print' 		=> esc_html__( 'Print', 'tripgo' ),
			'tiktok' 		=> esc_html__( 'Tiktok', 'tripgo' ),
		);

		if ( $network && isset( $args_network[$network] ) ) {
			return $args_network[$network];
		}

		return $args_network;
	}

	protected function get_social( $network = '' ) {
		if ( ! $network ) return false;

		$args_social = array(
			'facebook' 	=> array(
				'icon' 	=> 'fab fa-facebook',
				'url' 	=> 'https://www.facebook.com/sharer.php?u={url}',
			),
			'messenger' 	=> array(
				'icon' 	=> 'fab fa-facebook-messenger',
				'url' 	=> 'https://www.facebook.com/dialog/send?app_id={app_id}&link={url}&redirect_uri={redirect_uri}',
			),
			'twitter' 	=> array(
				'icon' 	=> 'fab fa-twitter',
				'url' 	=> 'https://twitter.com/intent/tweet?text={title}&url={url}',
			),
			'linkedin' 	=> array(
				'icon' 	=> 'fab fa-linkedin',
				'url' 	=> 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
			),
			'pinterest' => array(
				'icon' 	=> 'fab fa-pinterest',
				'url' 	=> 'https://www.pinterest.com/pin/create/button/?url={url}',
			),
			'reddit' 	=> array(
				'icon' 	=> 'fab fa-reddit',
				'url' 	=> 'https://reddit.com/submit?url={url}&title={title}',
			),
			'vk' 		=> array(
				'icon' 	=> 'fab fa-vk',
				'url' 	=> 'https://vkontakte.ru/share.php?url={url}&title={title}',
			),
			'odnoklassniki' => array(
				'icon' 	=> 'fab fa-odnoklassniki',
				'url' 	=> 'https://connect.ok.ru/offer?url={url}&title={title}',
			),
			'tumblr' 	=> array(
				'icon' 	=> 'fab fa-tumblr',
				'url' 	=> 'https://tumblr.com/share/link?url={url}',
			),
			'digg' 		=> array(
				'icon' 	=> 'fab fa-digg',
				'url' 	=> 'https://digg.com/submit?url={url}',
			),
			'skype' 	=> array(
				'icon' 	=> 'fab fa-skype',
				'url' 	=> 'https://web.skype.com/share?url={url}',
			),
			'stumbleupon' => array(
				'icon' 	=> 'fab fa-stumbleupon',
				'url' 	=> 'https://www.stumbleupon.com/submit?url={url}',
			),
			'telegram' 	=> array(
				'icon' 	=> 'fab fa-telegram',
				'url' 	=> 'https://telegram.me/share/url?url={url}&text={title}',
			),
			'pocket' 	=> array(
				'icon' 	=> 'fab fa-get-pocket',
				'url' 	=> 'https://getpocket.com/edit?url={url}',
			),
			'xing' 		=> array(
				'icon' 	=> 'fab fa-xing',
				'url' 	=> 'https://www.xing.com/spi/shares/new?url={url}',
			),
			'whatsapp' 	=> array(
				'icon' 	=> 'fab fa-whatsapp',
				'url' 	=> 'https://api.whatsapp.com/send?text=*{title}*%0A{url}',
			),
			'email' 	=> array(
				'icon' 	=> 'fas fa-envelope',
				'url' 	=> 'mailto:?subject={title}&body={url}',
			),
			'print' 	=> array(
				'icon' 	=> 'fas fa-print',
				'url' 	=> 'javascript:print()',
			),
			'tiktok' 	=> array(
				'icon' 	=> 'fab fa-tiktok',
				'url' 	=> 'https://www.tiktok.com/',
			),
		);

		return $args_social[$network];
	}

	protected function render() {
		$settings 		= $this->get_settings();

		$share_buttons 	= $settings['share_buttons'];
		$column 		= $settings['column'] ? ' share-grid column_'.$settings['column'] : '';
		$target 		= $settings['open_new_window'] ? ' target="_blank"' : '';
		$post_title 	= get_the_title();
		$post_link 		= get_permalink();

		if ( ! empty( $share_buttons ) && is_array( $share_buttons ) ):
	?>
		<div class="ova-share-buttons<?php echo esc_attr( $column ); ?>">
			<?php
			foreach ( $share_buttons as $item ):
				$network 		= $item['network'];
				$args_social 	= $this->get_social( $network );

				$class_icon 	= $args_social['icon'];
				$url_network 	= $args_social['url'];
				$app_id 		= $item['app_id'];
				$redirect_uri 	= $item['redirect_uri'];
				$url_network 	= str_replace( array( '{title}', '{url}', '{app_id}', '{redirect_uri}' ), array( $post_title, $post_link, $app_id, $redirect_uri ), $url_network );
			?>
			<div class="share-item">
				<?php 
				if ( $item['custom_link']['url'] ):
					$url_network 	= $item['custom_link']['url'];
					$target_item 	= $item['custom_link']['is_external'] ? ' target="_blank"' : '';
					$nofollow_item 	= $item['custom_link']['nofollow'] ? ' rel="nofollow"' : '';
					$attr_item 		= $item['custom_link']['custom_attributes'];
					$attr_html 		= '';

					if ( $attr_item ) {
						$attr_item = explode( ',', $attr_item );

						if ( ! empty( $attr_item ) && is_array( $attr_item ) ) {
							foreach ( $attr_item as $attr ) {
								$attr = explode( '|', $attr );

								if ( isset( $attr[0] ) && isset( $attr[1] ) ) {
									$attr_html .= ' '.trim( $attr[0] ) . '="' . trim( $attr[1] ) . '"';
								}
							}
						}
					}
				?>
					<a <?php echo esc_html($nofollow_item) ; ?> href="<?php echo esc_url( $url_network ); ?>" class="share-btn elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> ova-share-<?php echo esc_attr( $network ); ?>"<?php echo esc_html($target_item); ?><?php echo esc_html($attr_html); ?>>
						<?php  ?>
				<?php else: ?>
					<a href="<?php echo esc_url( $url_network ); ?>" class="share-btn ova-share-<?php echo esc_attr( $network ); ?>"<?php echo esc_html($target); ?>>
				<?php endif; ?>
						<?php if ( $item['custom_icon']['value'] ):
							\Elementor\Icons_Manager::render_icon( $item['custom_icon'], [ 'aria-hidden' => 'true' ] );
						?>
						<?php else: ?>
							<i class="<?php echo esc_attr( $class_icon ); ?>" aria-hidden="true"></i>
						<?php endif; ?>
					</a>
			</div>

		<?php endforeach; ?>
		</div>	
	<?php
		endif;
	}
}
$widgets_manager->register( new Tripgo_Elementor_Share_Buttons() );