<?php if( ! defined( 'ABSPATH' ) ) exit();

extract( $args );

$dateformat 	 = ovabrw_get_date_format();
$placeholder 	 = ovabrw_get_placeholder_date( $dateformat );
$hour_default 	 = ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) );
$time_step 		 = ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) );

// form action
$action 		 = home_url();

if ( isset( $args['search_result'] ) && ( 'new_page' == $args['search_result'] ) ) {
	$action = $args['search_result_url']['url'];
}

// form method
$method = isset( $args['method'] ) ? $args['method'] : 'GET';

$data_get = $_GET;

$id_selected = isset( $data_get['ovabrw_destination'] ) ? sanitize_text_field( $data_get['ovabrw_destination'] ) : 0;

if ( ! $id_selected && isset( $destination_default ) && $destination_default ) {
	$id_selected = $destination_default;
}

$ovabrw_pickup_date  = isset( $data_get['ovabrw_pickup_date'] ) ? sanitize_text_field( $data_get['ovabrw_pickup_date'] ) : '';

// guests ( adult & children )
$max_adult 			= $args['max_adult'];
$min_adult 			= $args['min_adult'];
$show_adult 		= $args['show_adult'];

$max_children   	= $args['max_children'];
$min_children 	 	= $args['min_children'];
$show_children 		= $args['show_children'];

$max_baby   		= $args['max_baby'];
$min_baby 	 		= $args['min_baby'];
$show_baby 			= $args['show_baby'];

$guests_label 	 	= $args['guests_label'];
$show_guests 		= $args['show_guests'];
$adults_label 	 	= $args['adults_label'];
$childrens_label 	= $args['childrens_label'];
$babies_label 		= $args['babies_label'];
$icon_guests     	= $args['icon_guests'];

$default_adult_number    = isset( $data_get['ovabrw_adults'] ) ? absint( $data_get['ovabrw_adults'] ) : absint( $args['default_adult_number'] );
$default_children_number = isset( $data_get['ovabrw_childrens'] ) ? absint( $data_get['ovabrw_childrens'] ) : absint( $args['default_children_number'] );
$default_babies_number 	 = isset( $data_get['ovabrw_babies'] ) ? absint( $data_get['ovabrw_babies'] ) : absint( $args['default_babies_number'] );
$guests                  = $default_adult_number + $default_children_number + $default_babies_number;
$guests_placeholder      = $args['guests_placeholder']; 

// button
$icon_button     = $args['icon_button'];
$button_label 	 = $args['button_label'];

// check in
$icon_check_in   = $args['icon_check_in'];
$check_in_label  = $args['check_in_label'];
$show_check_in 	 = $args['show_check_in'];

// custom taxonomy
$icon_custom_taxonomy        = $args['icon_custom_taxonomy'];
$custom_taxonomy_label 	     = $args['custom_taxonomy_label'];
$custom_taxonomy_placeholder = $args['custom_taxonomy_placeholder'];
$slug_custom_taxonomy        = $args['slug_custom_taxonomy'];
$slug_value_selected 		 = '';

if ( $slug_custom_taxonomy ) {
	$slug_value_selected = isset( $data_get[$slug_custom_taxonomy.'_name'] ) ? sanitize_text_field( $data_get[$slug_custom_taxonomy.'_name'] ) : 'all';
}

$ctx_slug_value_selected = isset( $args['ctx_slug_value_selected'] ) ? $args['ctx_slug_value_selected'] : '';

if ( $ctx_slug_value_selected ) {
	$slug_value_selected = $ctx_slug_value_selected;
}

if ( isset( $data_get[$slug_custom_taxonomy.'_name'] ) && sanitize_text_field( $data_get[$slug_custom_taxonomy.'_name'] ) ) {
	$slug_value_selected = sanitize_text_field( $data_get[$slug_custom_taxonomy.'_name'] );
}

$terms = get_taxonomy( $slug_custom_taxonomy );

// Categories
$search_categories = isset($args['search_categories']) ? $args['search_categories'] : [];

// Destination
$icon_destination        = $args['icon_destination'];
$destination_label 	     = $args['destination_label'];
$destination_placeholder = $args['destination_placeholder'];
$show_destination 		 = $args['show_destination'];

// Taxonomies
$mutiple_custom_taxonomy 	= $args['mutiple_custom_taxonomy'];
$list_taxonomies 			= isset( $args['list_custom_taxonomy'] ) ? $args['list_custom_taxonomy'] : array();
$data_taxonomies 			= array();

if ( ! isset( $template ) ) $template = '';

?>

<div class="ovabrw-search ovabrw-search-<?php echo esc_attr($template);?>">

	<form action="<?php echo esc_url( $action ); ?>" method="<?php echo esc_attr( $method ); ?>" class="ovabrw-search-form">
		<div class="ovabrw-s-field">

			<!-- destinations dropdown -->
			<?php if ( $show_destination === 'yes' ): ?>
				<div class="search-field">
					<div class="ovabrw-label">
						<?php 
						    if( $icon_destination ) {
						    	\Elementor\Icons_Manager::render_icon( $icon_destination, [ 'aria-hidden' => 'true' ] );
						    }     
					    ?>
						<span class="label"><?php echo esc_html( $destination_label ); ?></span>
					</div>
					<div class="ovabrw-input search_in_destination">
						<?php echo ovabrw_destination_dropdown( $destination_placeholder, $id_selected ); ?>
					</div>
				</div>
			<?php endif; ?>

	        <?php if( !empty( $slug_custom_taxonomy ) && !empty( $terms ) ) : ?>
	            <!-- custom taxonomy -->
				<div class="search-field">
					<div class="ovabrw-label">
						<?php 
						    if( $icon_custom_taxonomy ) {
						    	\Elementor\Icons_Manager::render_icon( $icon_custom_taxonomy, [ 'aria-hidden' => 'true' ] );
						    }     
					    ?>
						<span class="label"><?php echo esc_html( $custom_taxonomy_label ); ?></span>
					</div>
					<div class="ovabrw-input search_in_taxonomy">
						<?php echo ovabrw_search_taxonomy_dropdown( $slug_custom_taxonomy, $custom_taxonomy_placeholder, $slug_value_selected, 'required' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Taxonomies -->
			<?php if ( $mutiple_custom_taxonomy === 'yes' && ! empty( $list_taxonomies ) && is_array( $list_taxonomies ) ): ?>
				<?php foreach( $list_taxonomies as $data_taxonomy ):
					$item_slug_taxonomy 		= isset( $data_taxonomy['item_slug_taxonomy'] ) ? $data_taxonomy['item_slug_taxonomy'] : '';
					$item_icon_taxonomy 		= isset( $data_taxonomy['item_icon_taxonomy'] ) ? $data_taxonomy['item_icon_taxonomy'] : '';
					$item_taxonomy_label 		= isset( $data_taxonomy['item_taxonomy_label'] ) ? $data_taxonomy['item_taxonomy_label'] : '';
					$item_taxonomy_placeholder 	= isset( $data_taxonomy['item_taxonomy_placeholder'] ) ? $data_taxonomy['item_taxonomy_placeholder'] : '';

					if ( ! $item_slug_taxonomy ) continue;

					$item_slug_default 		= isset( $data_get[$item_slug_taxonomy.'_name'] ) ? sanitize_text_field( $data_get[$item_slug_taxonomy.'_name'] ) : 'all';
					$item_taxonomy_value 	= isset( $data_taxonomy['item_taxonomy_value_'.$item_slug_taxonomy] ) ? sanitize_text_field( $data_taxonomy['item_taxonomy_value_'.$item_slug_taxonomy] ) : '';

					if ( $item_taxonomy_value ) {
						$item_slug_default = $item_taxonomy_value;
					}

					if ( isset( $data_get[$item_slug_taxonomy.'_name'] ) && sanitize_text_field( $data_get[$item_slug_taxonomy.'_name'] ) ) {
						$item_slug_default = sanitize_text_field( $data_get[$item_slug_taxonomy.'_name'] );
					}

					$item_tern = get_taxonomy( $item_slug_taxonomy );
				?>
					<?php if ( $item_slug_taxonomy && ! empty( $item_tern ) ):
						array_push( $data_taxonomies, trim($item_slug_taxonomy) );
					?>
						<div class="search-field">
							<div class="ovabrw-label">
								<?php 
								    if ( $item_icon_taxonomy ) {
								    	\Elementor\Icons_Manager::render_icon( $item_icon_taxonomy, [ 'aria-hidden' => 'true' ] );
								    }     
							    ?>
								<span class="label"><?php echo esc_html( $item_taxonomy_label ); ?></span>
							</div>
							<div class="ovabrw-input search_in_taxonomy">
								<?php echo ovabrw_search_taxonomy_dropdown( $item_slug_taxonomy, $item_taxonomy_placeholder, $item_slug_default, 'required' ); ?>
							</div>
						</div>
			<?php endif; endforeach; endif; ?>

            <!-- time check in -->
            <?php if ( $show_check_in === 'yes' ): ?>
				<div class="search-field">
					<div class="ovabrw-input">
						<div class="ovabrw-label">
							<?php 
							    if( $icon_check_in ) {
							    	\Elementor\Icons_Manager::render_icon( $icon_check_in, [ 'aria-hidden' => 'true' ] );
							    }     
						    ?>
							<span class="label"><?php echo esc_html( $check_in_label ); ?></span>
						</div>
						<input
							type="text"
							name="ovabrw_pickup_date" 
							value="<?php echo esc_attr( $ovabrw_pickup_date ); ?>"
							class="ovabrw_datetimepicker ovabrw_start_date" 
							placeholder="<?php echo esc_attr( $placeholder ); ?>" 
							autocomplete="off" 
							data-hour_default="<?php echo esc_attr( $hour_default ); ?>" 
							data-time_step="<?php echo esc_attr( $time_step ); ?>" 
							data-dateformat="<?php echo esc_attr( $dateformat ); ?>" 
							onkeydown="return false" 
							onfocus="blur();" 
			            />
					</div>
				</div>
			<?php endif; ?>

            <!-- guest -->
            <?php if ( $show_guests === 'yes' ): ?>
				<div class="search-field guestspicker-control">
					<div class="ovabrw-input ovabrw-guestspicker-content-wrapper">
						<div class="ovabrw-label">
							<?php 
							    if( $icon_guests ) {
							    	\Elementor\Icons_Manager::render_icon( $icon_guests, [ 'aria-hidden' => 'true' ] );
							    }     
						    ?>
							<span class="label"><?php echo esc_html( $guests_label ); ?></span>
						</div>
						<div class="ovabrw-guestspicker">
							<div class="guestspicker">
								<span class="gueststotal"><?php echo esc_html( $guests ); ?></span>
								<span class="guestslabel"><?php echo esc_html( $guests_placeholder ); ?></span>
							</div>
						</div>
						<div class="ovabrw-guestspicker-content">
							<?php if ( $show_adult === 'yes' ): ?>
								<div class="guests-buttons">
									<div class="description">
										<label><?php echo esc_html( $adults_label ); ?></label>
									</div>
									<div class="guests-button">
										<div class="guests-icon minus">
											<i class="fas fa-minus"></i>
										</div>
										<input 
											type="text" 
											id="ovabrw_adults" 
											name="ovabrw_adults" 
											class="ovabrw_adults" 
											value="<?php echo esc_attr( $default_adult_number ); ?>" 
											min="<?php echo esc_attr( $min_adult ); ?>" 
											max="<?php echo esc_attr( $max_adult ); ?>" />
										<div class="guests-icon plus">
											<i class="fas fa-plus"></i>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $show_children === 'yes' ): ?>
								<div class="guests-buttons">
									<div class="description">
										<label><?php echo esc_html( $childrens_label ); ?></label>
									</div>
									<div class="guests-button">
										<div class="guests-icon minus">
											<i class="fas fa-minus"></i>
										</div>
										<input 
											type="text" 
											id="ovabrw_childrens" 
											name="ovabrw_childrens" 
											class="ovabrw_childrens" 
											value="<?php echo esc_attr( $default_children_number ); ?>" 
											min="<?php echo esc_attr( $min_children ); ?>" 
											max="<?php echo esc_attr( $max_children ); ?>" />
										<div class="guests-icon plus">
											<i class="fas fa-plus"></i>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $show_baby === 'yes' ): ?>
								<div class="guests-buttons">
									<div class="description">
										<label><?php echo esc_html( $babies_label ); ?></label>
									</div>
									<div class="guests-button">
										<div class="guests-icon minus">
											<i class="fas fa-minus"></i>
										</div>
										<input 
											type="text" 
											id="ovabrw_babies" 
											name="ovabrw_babies" 
											class="ovabrw_babies" 
											value="<?php echo esc_attr( $default_babies_number ); ?>" 
											min="<?php echo esc_attr( $min_baby ); ?>" 
											max="<?php echo esc_attr( $max_baby ); ?>" />
										<div class="guests-icon plus">
											<i class="fas fa-plus"></i>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- button -->
			<div class="ovabrw-search-btn">
				<?php if ( isset( $args['search_result'] ) && ( 'default' == $args['search_result'] ) ): ?>
					<input type="hidden" name="ovabrw_search_product" value="ovabrw_search_product" />
					<?php if( !empty($search_categories) ) : ?>
						<input type="hidden" name="cat" value="<?php echo esc_attr( implode( '|', $search_categories ) ); ?>" />
					<?php endif; ?>
					<input type="hidden" name="ovabrw_slug_custom_taxonomy" value="<?php echo esc_attr( $slug_custom_taxonomy ); ?>" />
					<input type="hidden" name="ovabrw_slug_taxonomies" value="<?php echo esc_attr( implode( '|', $data_taxonomies ) ); ?>" />
	                <input type="hidden" name="ovabrw_search" value="search_item" />
	                <input type="hidden" name="post_type" value="product" />
				<?php endif; ?>
				<button class="ovabrw-btn" type="submit">
					<?php 
					    if( $icon_button ) {
					    	\Elementor\Icons_Manager::render_icon( $icon_button, [ 'aria-hidden' => 'true' ] );
					    }   
					    printf( $button_label );    
				    ?>
				</button>		
			</div>
		</div>
	</form>
</div>