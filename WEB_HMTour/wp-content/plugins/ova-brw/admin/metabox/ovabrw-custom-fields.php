<?php if ( ! defined( 'ABSPATH' ) ) exit();

	// Check product if with wcfm plugin
	global $wp;
	
	if ( ! empty($wp->query_vars) ) {
		$post_id = $wp->query_vars['wcfm-products-manage'];
	} else {
		$post_id = false;
	}
		
	if( !$post_id ){
		$post_id = get_the_ID();
	}
	
	global $woocommerce, $post;

	if ( ! function_exists( 'woocommerce_wp_text_input' ) && ! is_admin() ) {
		include_once WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php';
	}
?>

	<div id="ovabrw-rental" class="options_group show_if_ovabrw_car_rental ovabrw_metabox_car_rental">

		<?php  woocommerce_wp_text_input(
			array(
				'id' 			=> 'ovabrw_children_price',
				'class' 		=> 'short',
				'label' 		=> sprintf( esc_html__( 'Children price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ),
				'desc_tip' 		=> 'true',
				'type' 			=> 'text',
				'data_type' 	=> 'price',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_children_price', true ) : '',
			));
		?>

		<?php  woocommerce_wp_text_input(
			array(
				'id' 			=> 'ovabrw_baby_price',
				'class' 		=> 'short',
				'label' 		=> sprintf( esc_html__( 'Baby price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ),
				'desc_tip' 		=> 'true',
				'type' 			=> 'text',
				'data_type' 	=> 'price',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_baby_price', true ) : '',
			));
		?>

		<!-- Insurance amount -->
		<?php  woocommerce_wp_text_input(
			array(
				'id' 			=> 'ovabrw_amount_insurance',
				'class' 		=> 'short',
				'label' 		=> sprintf( esc_html__( 'Insurance amount (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ),
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( 'This amount will be added to the cart', 'ova-brw' ),
				'placeholder' 	=> '10.5',
				'type' 			=> 'text',
				'data_type' 	=> 'price',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_insurance', true ) : '',
				'custom_attributes' => array( 'autocomplete' => 'off' ),
			));
		?>

		<!-- Video -->
		<?php 
			woocommerce_wp_text_input(
			  	array(
					'id' 			=> 'ovabrw_embed_video',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Embed Video Link', 'ova-brw' ),
					'placeholder' 	=> 'https://www.youtube.com/',
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_embed_video', true ) : '',
					'type' 			=> 'text',
					'custom_attributes' => array( 'autocomplete' => 'off' ),
			   	));

		?>

		<!-- Destination -->
		<?php
			$destinations = ovabrw_get_destinations();

			woocommerce_wp_select(
			  	array(
					'id' 				=> 'ovabrw_destination',
					'name' 				=> 'ovabrw_destination[]',
					'label' 			=> esc_html__( 'Destination', 'ova-brw' ),
					'options' 			=> $destinations,
					'value' 			=> $post_id ? get_post_meta( $post_id, 'ovabrw_destination', true ) : '',
					'custom_attributes' => array(
						'multiple' 			=> 'multiple',
						'data-placeholder'	=> esc_html__( 'All Destination', 'ova-brw' ),
					),
			   	));
		?>

		<!-- Stock quantity -->
		<?php  
			$ovabrw_stock_quantity = get_post_meta( $post_id, 'ovabrw_stock_quantity', true ) ? get_post_meta( $post_id, 'ovabrw_stock_quantity', true ) : 1;
			woocommerce_wp_text_input(
		  		array(
					'id' 			=> 'ovabrw_stock_quantity',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Quantity', 'ova-brw' ),
					'placeholder' 	=> '10',
					'value' 		=> $ovabrw_stock_quantity,
					'type' 			=> 'number'
		   		)
		   	);
		?>

		<!-- Duration -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Duration', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<?php $checked = '';
					$ovabrw_duration_checkbox = $post_id ? get_post_meta( $post_id, 'ovabrw_duration_checkbox', true ) : '';
					if ( $ovabrw_duration_checkbox ) $checked = ' checked'; 
				?>
				<p class="form-field ovabrw_duration_field">
				    <label class="ovabrw_duration_label">
				    	<input 
							id="ovabrw_duration_checkbox" 
							class="ovabrw_duration_checkbox" 
							type="checkbox" 
							name="ovabrw_duration_checkbox" 
							value="<?php echo esc_attr( $ovabrw_duration_checkbox ); ?>" 
							<?php echo esc_attr( $checked ); ?>/>
						<span><?php echo esc_html_e( 'Time Slots', 'ova-brw' ); ?></span>
					</label>
				</p>

				<!-- Days -->
				<?php
					$ovabrw_number_days = get_post_meta( $post_id, 'ovabrw_number_days', true );

					woocommerce_wp_text_input(
						array(
							'id' 			=> 'ovabrw_number_days',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Days', 'ova-brw' ),
							'desc_tip' 		=> 'true',
							'description' 	=> esc_html__( 'Tour time (day)', 'ova-brw' ),
							'placeholder' 	=> 1,
							'type' 			=> 'number',
							'value' 		=> $ovabrw_number_days,
							'custom_attributes' => array( 
								'autocomplete' 	=> 'off',
								'min'			=> 0,
							),
					));
				?>

				<!-- Fixed Time -->
				<div class="ovabrw-form-field ovabrw_fixed_time_field">
			  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Fixed Dates', 'ova-brw'); ?></strong>
			  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_fixed_time.php' ); ?>
				</div>

				<!-- Hour -->
				<?php
					$ovabrw_number_hours = $post_id && get_post_meta( $post_id, 'ovabrw_number_hours', true ) ? get_post_meta( $post_id, 'ovabrw_number_hours', true ) : '';

					woocommerce_wp_text_input(
						array(
							'id' 			=> 'ovabrw_number_hours',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Hours', 'ova-brw' ),
							'desc_tip' 		=> 'true',
							'description' 	=> esc_html__( 'Tour time (hour)', 'ova-brw' ),
							'placeholder' 	=> 1,
							'type' 			=> 'text',
							'data_type' 	=> 'price',
							'value' 		=> $ovabrw_number_hours,
							'custom_attributes' => array( 
								'autocomplete' 	=> 'off',
							),
					));
				?>

				<!-- Schedule -->
				<div class="ovabrw-form-field ovabrw_schedule">
			  		<strong class="ovabrw_heading_section"><?php esc_html_e('Schedule', 'ova-brw'); ?></strong>
			  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_daily_price.php' ); ?>
				</div>
			</div>
		</div>

		<!-- Guests settings -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Guests', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<?php // Stock quantity by guests
					$ovabrw_stock_quantity_by_guests = get_post_meta( $post_id, 'ovabrw_stock_quantity_by_guests', true ) ? get_post_meta( $post_id, 'ovabrw_stock_quantity_by_guests', true ) : '';
				?>
				<p class="form-field ovabrw_stock_quantity_by_guests_field">
					<label>
						<?php esc_html_e( 'Stock quantity by Guests' ); ?>
						<span>
	                        <?php echo wc_help_tip( esc_html__( 'Check Tour booking available by Maximum total number of guest', 'ova-brw' ), true ); ?>
	                    </span>
					</label>
					<label class="ovabrw-qty-by-guests">
						<input
							type="checkbox"
							class=""
							name="ovabrw_stock_quantity_by_guests"
							id="ovabrw_stock_quantity_by_guests"
							value="1"
							<?php checked( '1', $ovabrw_stock_quantity_by_guests ); ?>
						/>
					</label>
				</p>


				<?php // Guest
					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_min_total_guest',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Minimum total number of guest', 'ova-brw' ),
							'placeholder' 	=> '2',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_min_total_guest', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));
					
					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_max_total_guest',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Maximum total number of guest', 'ova-brw' ),
							'placeholder' 	=> '10',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_max_total_guest', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));

					// Adults
					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_adults_min',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Minimum Adults', 'ova-brw' ),
							'placeholder' 	=> '1',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_adults_min', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));

					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_adults_max',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Maximum Adults', 'ova-brw' ),
							'placeholder' 	=> '10',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_adults_max', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));

					// Childrens
					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_childrens_min',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Minimum Children', 'ova-brw' ),
							'placeholder' 	=> '1',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_childrens_min', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));

					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_childrens_max',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Maximum Children', 'ova-brw' ),
							'placeholder' 	=> '5',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_childrens_max', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));					

					// Babies
					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_babies_min',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Minimum Babies', 'ova-brw' ),
							'placeholder' 	=> '0',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_babies_min', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));

					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_babies_max',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Maximum Babies', 'ova-brw' ),
							'placeholder' 	=> '3',
							'value' 		=> get_post_meta( $post_id, 'ovabrw_babies_max', true ),
							'type' 			=> 'number',
							'custom_attributes' => array( 
								'min' => 0,
							),
					   	));
					
				?>
			</div>
		</div>

		<!-- Deposit -->
		<?php if( apply_filters( 'ovabrw_show_backend_deposit', true ) ){ ?>
			<div class="ovabrw-advanced-settings">
				<div class="advanced-header">
					<h3 class="advanced-label"><?php esc_html_e( 'Deposit', 'ova-brw' ); ?></h3>
					<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
					<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
				</div>

				<div class="advanced-content">
					<!-- Enable deposit -->
					<?php  woocommerce_wp_select(
						array(
							'id' 			=> 'ovabrw_enable_deposit',
							'label' 		=> esc_html__( 'Enable deposit', 'ova-brw' ),
							'desc_tip' 		=> 'true',
							'description' 	=> esc_html__( 'An advance payment', 'ova-brw' ),
							'placeholder' 	=> '',
							'options' 		=> array(
								'no'	=> esc_html__( 'No', 'ova-brw' ),
								'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
							),
							'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_enable_deposit', true ) : 'no',
					   	));
					?>

					<!-- Force deposit -->
					<?php  woocommerce_wp_select(
						array(
							'id' 			=> 'ovabrw_force_deposit',
							'label' 		=> esc_html__( 'Show Full Payment', 'ova-brw' ),
							'desc_tip' 		=> 'true',
							'description' 	=> esc_html__( '- Yes: show full payment option in booking form. <br/>- No: Only deposit payment option', 'ova-brw' ),
							'placeholder' 	=> '',
							'options' 		=> array(
								'no' 	=> esc_html__( 'No', 'ova-brw' ),
								'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
							),
							'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_force_deposit', true ) : 'no',
					   	));
					?>

					<!-- Type deposit -->
					<?php  woocommerce_wp_select(
					  	array(
							'id' 			=> 'ovabrw_type_deposit',
							'label' 		=> esc_html__( 'Deposit type', 'ova-brw' ),
							'placeholder' 	=> '',
							'options' 		=> array(
								'percent'	=> esc_html__( 'a percentage amount of payment ', 'ova-brw' ),
								'value'		=> esc_html__( 'a fixed amount of payment', 'ova-brw' ),
							),
							'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_type_deposit', true ) : 'percent',
					   	));
					?>
					
					<!-- amount deposit -->
					<?php  woocommerce_wp_text_input(
						array(
							'id' 			=> 'ovabrw_amount_deposit',
							'label' 		=> '',
							'placeholder' 	=> '50',
							'data_type' 	=> 'price',
							'desc_tip' 		=> 'true',
							'description' 	=> esc_html__( 'Insert deposit amount', 'ova-brw' ),
							'type' 			=> 'text',
							'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_deposit', true ) : '',
							'custom_attributes' 	=> array(
								'data-percent-unit'	=> '%',
								'data-fixed-unit'	=> get_woocommerce_currency_symbol()
							)
					   	));
					?>
				</div>
			</div>
		<?php } ?>	

		<!-- Feature -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Features', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<div class="ovabrw-form-field ovabrw_features-field">
			  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_features.php' ); ?>
				</div>
			</div>
		</div>

		<!-- Global Discount -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Global Discount (GD) / Price Per Person', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<div class="ovabrw-form-field price_discount">
			  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_global_discount.php' ); ?>
				</div>
			</div>
		</div>

		<!-- Price by range time -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Special Time (ST) / Price Per Person', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<div class="ovabrw-form-field price_special_time">
					<span class="ovabrw_right"><?php esc_html_e( "Note: ST doesn't use GD, it will use DST", 'ova-brw' ); ?></span>
					<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_st.php' ); ?>
				</div>
			</div>
		</div>
		
		<!-- Resources -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Resources', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<div class="ovabrw-form-field ovabrw_resources_field">
			  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_resources.php' ); ?>
				</div>
			</div>
		</div>

		<!-- Services -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Services', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<div class="ovabrw-form-field ovabrw_service_field">
			  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service.php' ); ?>
				</div>
			</div>
		</div>

		<!-- Unavailable time -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Unavailable Time (UT)', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<div class="ovabrw-form-field ovabrw_untime_field">
					<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_untime.php' ); ?>
				</div>
			</div>
		</div>

		<!-- Advanced options -->
		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Advanced options', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<!-- Product Template -->
				<?php
					// Get templates from elementor
		            $templates = get_posts( array('post_type' => 'elementor_library', 'meta_key' => '_elementor_template_type', 'meta_value' => 'page', 'numberposts'   => -1 ) );
		            
		            $list_templates = array( 'global' => 'Category Settings' );

		            if( ! empty( $templates ) ) {
		                foreach( $templates as $template ) {
		                    $id_template    = $template->ID;
		                    $title_template = $template->post_title;
		                    $list_templates[$id_template] = $title_template;
		                }
		            }

					woocommerce_wp_select(
					  	array(
							'id' 			=> 'ovabrw_product_template',
							'label' 		=> esc_html__( 'Product Template', 'ova-brw' ),
							'placeholder' 	=> '',
							'options' 		=> $list_templates,
							'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_template', true ) : '',
							'desc_tip'		=> true,
							'description'	=> esc_html__( 'Category Settings: Setup in per category', 'ova-brw' )
					   	));
				?>
		
				<!-- Disable Week Day -->
				<?php  
					$ovabrw_product_disable_week_day = ! empty( get_post_meta( $post_id, 'ovabrw_product_disable_week_day', true ) ) ? get_post_meta( $post_id, 'ovabrw_product_disable_week_day', true ) : '';

					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_product_disable_week_day',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'Disable Week Day', 'ova-brw' ),
							'placeholder' 	=> '0,6',
							'value' 		=> $ovabrw_product_disable_week_day,
							'type' 			=> 'text',
							'desc_tip' 		=> 'true',
							'description' 	=> esc_html__( '0: Sunday, 1: Monday, 2: Tuesday, 3: Wednesday, 4: Thursday, 5: Friday, 6: Saturday . Example: 0,6', 'ova-brw' )
						)
					);
				?>

				<!-- Preparation time -->
				<?php  
					$ovabrw_preparation_time = ! empty( get_post_meta( $post_id, 'ovabrw_preparation_time', true ) ) ? get_post_meta( $post_id, 'ovabrw_preparation_time', true ) : '';

					woocommerce_wp_text_input(
					  	array(
							'id' 			=> 'ovabrw_preparation_time',
							'class' 		=> 'short',
							'label' 		=> esc_html__( 'X Days Preparation Time', 'ova-brw' ),
							'placeholder' 	=> esc_html__( 'Number', 'ova-brw' ),
							'value' 		=> $ovabrw_preparation_time,
							'type' 			=> 'number',
							'desc_tip' 		=> 'true',
							'description' 	=> esc_html__( 'The customer book X days in advance from the current time', 'ova-brw' )
						)
					);
				?>

				<?php
					$ovabrw_book_before_x_hours = ! empty( get_post_meta( $post_id, 'ovabrw_book_before_x_hours', true ) ) ? get_post_meta( $post_id, 'ovabrw_book_before_x_hours', true ) : '';
				?>
				<p class="form-field ovabrw_book_before_x_hours_field">
					<label for="ovabrw_book_before_x_hours">
						<?php esc_html_e( 'Book before X hours today', 'ova-brw' ); ?>
					</label>
					<span class="ovabrw-product-book-x-hours">
						<input
							type="text"
							class="short ovabrw_hour_picker"
							name="ovabrw_book_before_x_hours"
							id="ovabrw_book_before_x_hours"
							value="<?php echo esc_attr( $ovabrw_book_before_x_hours ); ?>"
							placeholder="<?php esc_attr_e( 'Choose time', 'ova-brw' ); ?>"
						/>
						<span class="remove-x-hours">x</span>
					</span>
				</p>

				<?php if( apply_filters( 'ovabrw_show_checkout_field_setting_product', true ) ){ ?>
					<div class="ovabrw-form-field">
						<strong class="ovabrw_heading_section"><?php esc_html_e( 'Custom Checkout Fields', 'ova-brw' ); ?></strong>
						<?php  woocommerce_wp_select(
						  	array(
								'id' 			=> 'ovabrw_manage_custom_checkout_field',
								'label' 		=> esc_html__( 'Display custom fields from', 'ova-brw' ),
								'placeholder' 	=> '',
								'options' 		=> array(
									'all'	=> esc_html__( 'Category Settings', 'ova-brw' ),
									'new'	=> esc_html__( 'New', 'ova-brw' ),
								),
								'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', true ) : 'all',
								'desc_tip'	=> true,
								'description'	=> esc_html__( 'Category Settings: Setup in per category', 'ova-brw' )
						   	));
						?>
						
						<?php
							woocommerce_wp_textarea_input(
							    array(
							        'id' 			=> 'ovabrw_product_custom_checkout_field',
							        'placeholder' 	=> esc_html__( 'Insert new custom checkout fields', 'ova-brw' ),
							        'label' 		=> '',
							        'desc_tip'	=> true,
							        'description' 	=> esc_html__('Insert name in general custom checkout field. Example: ova_email_field, ova_address_field', 'ova-brw'),
							        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', true ) : '',
							    )
							);
						?>
					</div>
				<?php } ?>

				<!-- Show/Hide Checkout Field -->
				<div class="ovabrw-form-field">
					<strong class="ovabrw_heading_section"><?php esc_html_e( 'Check out field', 'ova-brw' ); ?></strong>
					<?php  woocommerce_wp_select(
					  	array(
							'id' 			=> 'ovabrw_manage_checkout_field',
							'label' 		=> esc_html__( 'Display', 'ova-brw' ),
							'placeholder' 	=> '',
							'options' 		=> array(
								'global'	=> esc_html__( 'Global Settings', 'ova-brw' ),
								'show'		=> esc_html__( 'Yes', 'ova-brw' ),
								'hide'		=> esc_html__( 'No', 'ova-brw' ),
							),
							'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_checkout_field', true ) : 'global',
							'desc_tip'		=> true,
							'description'	=> esc_html__( 'Global Settings: Go to WooCommerce >> Settings >> Booking Tours >> Show Check out field', 'ova-brw' )
					   	));
					?>
				</div>

				<!-- Show/Hide Form -->
				<div class="ovabrw-form-field">
					<strong class="ovabrw_heading_section"><?php esc_html_e( 'Forms', 'ova-brw' ); ?></strong>
					<?php  woocommerce_wp_select(
					  	array(
							'id' 			=> 'ovabrw_forms_product',
							'label' 		=> esc_html__( 'Show forms', 'ova-brw' ),
							'placeholder' 	=> '',
							'options' 		=> array(
								''			=> esc_html__( 'Global Settings', 'ova-brw' ),
								'booking'	=> esc_html__( 'Only Booking Form', 'ova-brw' ),
								'enquiry'	=> esc_html__( 'Only Enquiry From', 'ova-brw' ),
								'all'		=> esc_html__( 'Show Both', 'ova-brw' ),
							),
							'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_forms_product', true ) : '',
							'desc_tip'		=> true,
							'description'	=> esc_html__( 'Global Settings: Go to WooCommerce >> Settings >> Booking Tours >> Product Details', 'ova-brw' )
					   	));
					?>
				</div>
			</div>
		</div>

		<div class="ovabrw-advanced-settings">
			<div class="advanced-header">
				<h3 class="advanced-label"><?php esc_html_e( 'Place', 'ova-brw' ); ?></h3>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</div>

			<div class="advanced-content">
				<!-- Short Address -->
				<div class="ovabrw-form-field">
					<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Short Address', 'ova-brw' ); ?></strong>
					<?php  
						woocommerce_wp_text_input(
							array(
								'id' 			=> 'ovabrw_short_address',
								'class' 		=> 'short',
								'label'			=> esc_html__( 'Short Address', 'ova-brw' ),
								'placeholder'	=> '',
								'type' 			=> 'text',
								'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_short_address', true ) : '',
								'desc_tip' 		=> 'true',
								'description' 	=> esc_html__( 'If this field is filled in, This field will display as address instead of the Map and Iframe address.', 'ova-brw' )
							)
						);
					?>
				</div>

				<!-- Map -->
				<div class="ovabrw-form-field">
					<div class="ovabrw-map-type">
						<?php 
							$ovabrw_map_type = $post_id && get_post_meta( $post->ID ,'ovabrw_map_type', true ) ? get_post_meta( $post->ID ,'ovabrw_map_type', true ) : 'api'; 
						?>
						<label class="container"><?php esc_html_e( 'Google Map' ); ?>
						<?php if ( $ovabrw_map_type == 'api' ): ?>
					  		<input type="radio" checked="checked" name="map_type" value="api">
					  	<?php else: ?>
					  		<input type="radio" name="map_type" value="api">
					  	<?php endif; ?>
						  	<span class="checkmark"></span>
						</label>
						<label class="container"><?php esc_html_e( 'Google Map Iframe' ); ?>
						<?php if ( $ovabrw_map_type == 'iframe' ): ?>
						  	<input type="radio" checked="checked" name="map_type" value="iframe">
						<?php else: ?>
					  		<input type="radio" name="map_type" value="iframe">
					  	<?php endif; ?>
						  	<span class="checkmark"></span>
						</label>
					</div>
					<div class="ovabrw-gg-map">
						<?php
							$ovabrw_map_name = $post_id ? get_post_meta( $post->ID ,'ovaev_map_name', true ) : esc_html__('New York', 'ova-brw');
							$ovabrw_address  = $post_id ? get_post_meta( $post_id, 'ovabrw_address', true ) : esc_html__( 'New York, NY, USA', 'ova-brw' );
							if ( ! $ovabrw_address ) {
								$ovabrw_address = esc_html__( 'New York, NY, USA', 'ova-brw' );
							}
							// Address
							woocommerce_wp_text_input(
								array(
									'id' 				=> 'pac-input',
									'class' 			=> 'controls',
									'label'				=> esc_html__( '', 'ova-brw' ),
									'placeholder'		=> esc_html__( 'Enter a venue', 'ova-brw' ),
									'type' 				=> 'text',
									'value' 			=> $ovabrw_address,
									'custom_attributes' => array(
										'autocomplete' 	=> 'off',
										'autocorrect'	=> 'off',
										'autocapitalize'=> 'none'
									),
								)
							);
						?>
						<div id="admin_show_map"></div>
						<div id="infowindow-content">
							<span id="place-name" class="title"><?php echo esc_attr( $ovabrw_map_name ); ?></span><br>
							<span id="place-address"><?php echo esc_attr( $ovabrw_address ); ?></span>
						</div>
						<div id="map_info">
							<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_product_map.php' ); ?>
						</div>
						<div class="admin-map-iframe">
							<?php 
								$ovabrw_map_iframe = $post_id && get_post_meta( $post->ID ,'ovabrw_map_iframe', true ) ? get_post_meta( $post->ID ,'ovabrw_map_iframe', true ) : ''; 
							?>
							<textarea name="map_iframe" id="map_iframe" cols="100%" rows="10"><?php echo $ovabrw_map_iframe; ?></textarea>
						</div>
					</div>
				</div>	
			</div>
		</div>
	</div>
