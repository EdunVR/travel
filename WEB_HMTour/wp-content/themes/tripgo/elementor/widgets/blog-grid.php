<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tripgo_Elementor_Blog_Grid extends Widget_Base {

	public function get_name() {
		return 'tripgo_elementor_blog_grid';
	}

	public function get_title() {
		return esc_html__( 'Blog Grid', 'tripgo' );
	}

	public function get_icon() {
		return 'eicon-posts-ticker';
	}

	public function get_categories() {
		return [ 'tripgo' ];
	}

	public function get_script_depends() {
		return [ '' ];
	}

	protected function register_controls() {

		$args = array(
		  'orderby' => 'name',
		  'order' => 'ASC'
		);

		$categories = get_categories($args);
		$cate_array = array();
		$arrayCateAll = array( 'all' => esc_html__( 'All categories', 'tripgo' ) );
		if ($categories) {
			foreach ( $categories as $cate ) {
				$cate_array[$cate->slug] = $cate->cat_name;
			}
		} else {
			$cate_array[ esc_html__( 'No content Category found', 'tripgo' ) ] = 0;
		}

		//SECTION CONTENT
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
			]
		);

			$this->add_control(
				'template',
				[
					'label' => esc_html__( 'Template', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'template1',
					'options' => [
						'template1' => esc_html__( 'Template 1', 'tripgo' ),
						'template2' => esc_html__( 'Template 2', 'tripgo' ),
						'template3' => esc_html__( 'Template 3', 'tripgo' ),
						'template4' => esc_html__( 'Template 4', 'tripgo' ),
					],
				]
			);

			$this->add_control(
				'category',
				[
					'label' => esc_html__( 'Category', 'tripgo' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'all',
					'multiple' => true,
					'options' => array_merge($arrayCateAll,$cate_array),
				]
			);

			$this->add_control(
				'total_count',
				[
					'label' => esc_html__( 'Post Total', 'tripgo' ),
					'type' => Controls_Manager::NUMBER,
					'default' => 3,
				]
			);

			$this->add_control(
				'number_column',
				[
					'label' => esc_html__( 'Columns', 'tripgo' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'columns3',
					'options' => [
						'columns2' => esc_html__( '2 Columns', 'tripgo' ),
						'columns3' => esc_html__( '3 Columns', 'tripgo' ),
						'columns4' => esc_html__( '4 Columns', 'tripgo' ),
					],
					'condition' => [
						'template!' => 'template4',
					],
				]
			);
            
            $this->add_control(
				'orderby',
				[
					'label' 	=> esc_html__('Order By', 'tripgo'),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'ID',
					'options' 	=> [
						'ID' 		=> esc_html__('ID', 'tripgo'),
						'title' 	=> esc_html__('Title', 'tripgo'),
						'date' 		=> esc_html__('Date', 'tripgo'),
						'modified' 	=> esc_html__('Modified', 'tripgo'),
						'rand' 		=> esc_html__('Rand', 'tripgo'),
					]
				]
			);

			$this->add_control(
				'order_by',
				[
					'label' => esc_html__('Order', 'tripgo'),
					'type' => Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
						'asc' => esc_html__('Ascending', 'tripgo'),
						'desc' => esc_html__('Descending', 'tripgo'),
					]
				]
			);

			$this->add_control(
				'text_readmore',
				[
					'label' => esc_html__( 'Text Read More', 'tripgo' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__('Read More', 'tripgo'),
				]
			);

			$this->add_control(
				'show_thumbnail',
				[
					'label' => esc_html__( 'Show Thumbnail', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_short_desc',
				[
					'label' => esc_html__( 'Show Short Description', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control(
				'order_text',
				[
					'label' => esc_html__( 'Words Total', 'tripgo' ),
					'type' => Controls_Manager::NUMBER,
					'default' => 20,
					'condition' => [
						'show_short_desc' => 'yes',
					]
				]
			);


			$this->add_control(
				'show_date',
				[
					'label' => esc_html__( 'Show Date', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_author',
				[
					'label' => esc_html__( 'Show Author', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);


			$this->add_control(
				'show_title',
				[
					'label' => esc_html__( 'Show Title', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);


			$this->add_control(
				'show_read_more',
				[
					'label' => esc_html__( 'Show Read More', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'tripgo' ),
					'label_off' => esc_html__( 'Hide', 'tripgo' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);


			$this->add_control(
				'icon',
				[
					'label' => esc_html__( 'Icon', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'icomoon icomoon-arrow-right',
						'library' 	=> 'all',
					],
					'condition'=> [
						'show_read_more' => 'yes',
					],
				]
			);

		$this->end_controls_section();
		//END SECTION CONTENT

		//SECTION TAB STYLE CONTENT
		$this->start_controls_section(
			'section_content_item',
			[
				'label' => esc_html__( 'Content', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'bg_content',
				[
					'label' => esc_html__( 'Background', 'tripgo' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-blog .item .ova-content' => 'background-color : {{VALUE}};',
					],
					'condition' => [
						'template!' => 'template3',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name' => 'background',
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .ova-blog.template3 .item .media .overlay',
					'exclude' => ['image'],
					'condition' => [
						'template' => 'template3',
					],
				]
			);

			$this->add_responsive_control(
				'paddding_content',
				[
					'label' => esc_html__( 'Padding', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-blog .item .ova-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'margin_content',
				[
					'label' => esc_html__( 'Margin', 'tripgo' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ova-blog .item .ova-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'content_box_shadow',
					'selector' => '{{WRAPPER}} .ova-blog .item .ova-content , {{WRAPPER}} .ova-blog.template1 .item ',
					'condition' => [
						'template!' => 'template3',
					],
				]
			);

			$this->add_responsive_control(
				'content_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'tripgo' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
					   '{{WRAPPER}} .ova-blog.template1 .item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					   '{{WRAPPER}} .ova-blog .item .ova-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'template!' => 'template3',
					],
				]
			);

			$this->add_control(
				'image_heading',
				[
					'label' => esc_html__( 'Image', 'tripgo' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'image_min_height',
				[
					'label' 		=> esc_html__( 'Min height (px)', 'tripgo' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' 	=> 50,
							'max' 	=> 500,
							'step' 	=> 1,
						]
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-blog .item .media a img' => 'min-height: {{SIZE}}{{UNIT}};',

					],
				]
			);

			$this->add_responsive_control(
				'image_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'tripgo' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-blog .item .media a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .ova-blog .item .media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		//END SECTION TAB STYLE CONTENT

		//SECTION TAB STYLE TITLE
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ova-blog .post-title a',
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__( 'Color', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .post-title a' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_title_hover',
			[
				'label' => esc_html__( 'Color Hover', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .post-title a:hover' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_title',
			[
				'label' => esc_html__( 'Margin', 'tripgo' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-blog .post-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		//END SECTION TAB STYLE TITLE

		//START SECTION TAB STYLE DESCRIPTION
		$this->start_controls_section(
			'section_short_desc',
			[
				'label' => esc_html__( 'Short Description', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'template!' => 'template3',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'short_desc_typography',
				'selector' => '{{WRAPPER}} .ova-blog .short_desc p',
			]
		);

		$this->add_control(
			'color_short_desc',
			[
				'label' => esc_html__( 'Color', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .short_desc p' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_short_desc',
			[
				'label' => esc_html__( 'Margin', 'tripgo' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-blog .short_desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		//END SECTION TAB STYLE DESCRIPTION

		$this->start_controls_section(
			'section_meta',
			[
				'label' => esc_html__( 'Meta', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tripgo' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_meta',
			[
				'label' => esc_html__( 'Margin', 'tripgo' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'meta_icon_heading',
			[
				'label' => esc_html__( 'Icon ', 'tripgo' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'meta_icon_size',
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
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_icon_opacity',
			[
				'label' => esc_html__( 'Opacity', 'tripgo' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta .left' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'icon_color_meta',
			[
				'label' => esc_html__( 'Color', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta i' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_meta_icon',
			[
				'label' => esc_html__( 'Margin', 'tripgo' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta .left' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'meta_text_heading',
			[
				'label' => esc_html__( 'Text ', 'tripgo' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'selector' => '{{WRAPPER}} .ova-blog .item .post-meta .item-meta .right, {{WRAPPER}} .ova-blog .item .post-meta .item-meta .right a',
			]
		);

		$this->add_control(
			'text_color_meta',
			[
				'label' => esc_html__( 'Color', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .post-meta .item-meta .right' => 'color : {{VALUE}};',
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta .right a' => 'color : {{VALUE}};',
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta i' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_color_meta',
			[
				'label' => esc_html__( 'Link Color', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta.wp-author .post-author a' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_color_meta_hover',
			[
				'label' => esc_html__( 'Link Color hover', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .ova-content .post-meta .item-meta.wp-author .post-author:hover a' => 'color : {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


		//SECTION TAB STYLE READMORE
		$this->start_controls_section(
			'section_readmore',
			[
				'label' => esc_html__( 'Read More', 'tripgo' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'readmore_typography',
				'selector' => '{{WRAPPER}} .ova-blog .item .read-more',
			]
		);

		$this->add_control(
			'color_readmore',
			[
				'label' => esc_html__( 'Color', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .read-more' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_readmore_hover',
			[
				'label' => esc_html__( 'Color Hover', 'tripgo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .read-more:hover' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_readmore',
			[
				'label' => esc_html__( 'Margin', 'tripgo' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-blog .item .read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		//END SECTION TAB STYLE READMORE

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		
		$template        	= $settings['template'];
		$category        	= $settings['category'];
		$total_count     	= $settings['total_count'];
		$order           	= $settings['order_by'];
		$orderby 		 	= $settings['orderby'];
		$number_column   	= $settings['number_column'];
		$text_readmore   	= $settings['text_readmore'];
		$show_thumbnail     = $settings['show_thumbnail'];
		$show_date       	= $settings['show_date'];
		$show_author     	= $settings['show_author'];
		$show_title      	= $settings['show_title'];
		$show_short_desc 	= $settings['show_short_desc'];
		$show_read_more  	= $settings['show_read_more'];
		$icon            	= $settings['icon'] ? $settings['icon'] : '';
		$order_text	 		= $settings['order_text'] ? $settings['order_text'] : '20';

		$args = [
			'post_type' 		=> 'post',
			'post_status'       => 'publish',
			'posts_per_page' 	=> $total_count,
			'order' 			=> $order,
			'orderby' 			=> $orderby,
		];

		if ( !is_array($category) && ($category != 'all') ) {
			$args['category_name'] = $category;
		}

		if ( is_array($category) && !in_array('all', $category) ) {
			$args['category_name'] = implode(',',$category);
		}

		$blog = new \WP_Query($args);

		?>
		
		<ul class="ova-blog <?php echo esc_attr( $number_column ); ?> <?php echo esc_attr( $template ); ?>">
			<?php
				if($blog->have_posts()) : while($blog->have_posts()) : $blog->the_post();
			?>
				<li class="item <?php if($show_thumbnail != 'yes') echo 'no-thumbnail'; ?>">
					<?php if($show_thumbnail == 'yes') : ?>
						<?php if(has_post_thumbnail()){ ?>
						    <div class="media">
					        	<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
					        		<?php the_post_thumbnail( 'tripgo_thumbnail' , array('class'=> 'img-responsive' )); ?>
					        		<div class="overlay"></div>
					        	</a>
					        </div>
				        <?php } else { ?>
				        	<div class="media">
					        	<?php 
					        		$thumbnail = \Elementor\Utils::get_placeholder_image_src();
					        	?>
					        	<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
					        		<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php the_title(); ?>">
					        		<div class="overlay"></div>
					        	</a>
					        </div>
				        <?php } ?>
				    <?php endif; ?>

				    <?php if($show_thumbnail != 'yes' && $template == 'template3') : ?>
			        	<div class="media">
				        	<?php 
				        		$thumbnail = \Elementor\Utils::get_placeholder_image_src();
				        	?>
				        	<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
				        		<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php the_title(); ?>">
				        		<div class="overlay"></div>
				        	</a>
				        </div>
				    <?php endif; ?>

		        	<div class="ova-content">
	        			<ul class="post-meta">
						    <?php if( $show_author == 'yes' ){ ?>
								<li class="item-meta wp-author">
							    	<span class="left author"> 
							    	 	<i class="icomoon icomoon-profile-circle"></i>
							    	</span>
							    	<!-- far fa-user-circle -->
								    <span class="right post-author">
							        	<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							        		<?php the_author_meta( 'display_name' ); ?>
							        	</a>
								    </span>
							    </li>
							<?php } ?>

							<?php if( $show_date == 'yes' ){ ?>
							    <li class="item-meta post-date">
							        <span class="left date">
							        	<i class="icomoon icomoon-calander"></i>
							        </span>
							        <span class="right date">
							        	<?php the_time( get_option( 'date_format' ));?>
							        </span>
							    </li>
						    <?php } ?>

						</ul>
						
						<?php if( $show_title == 'yes' ){ ?>
				            <h4 class="post-title">
						        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						    </h4>
					    <?php } ?>

					    <?php if( $show_short_desc == 'yes'){ ?>
						    <div class="short_desc">
						    	<p> <?php echo wp_trim_words(get_the_excerpt(), $order_text); ?> </p>
						    </div>
						<?php } ?>

					    <?php if( $show_read_more == 'yes' ){ ?>
						    <a class="read-more" href="<?php the_permalink(); ?>">
						    	<span><?php echo esc_html( $text_readmore ); ?></span>
						    	<?php 
						        	\Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
						    	?>
						    </a>
					    <?php }?>
		        	</div>
				</li>		
			<?php endwhile; endif; wp_reset_postdata(); ?>
		</ul>
		
		
		<?php
	}
}

$widgets_manager->register( new Tripgo_Elementor_Blog_Grid() );