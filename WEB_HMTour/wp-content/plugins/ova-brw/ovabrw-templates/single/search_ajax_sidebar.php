<?php if( ! defined( 'ABSPATH' ) ) exit();

extract( $args );

$dateformat 	 = ovabrw_get_date_format();
$placeholder 	 = ovabrw_get_placeholder_date( $dateformat );
$hour_default 	 = ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) );
$time_step 		 = ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) );

$data_get = $_GET;

$id_selected = isset( $data_get['ovabrw_destination'] )  ? sanitize_text_field( $data_get['ovabrw_destination'] ) : 0;

if ( ! $id_selected && isset( $destination_default ) && $destination_default ) {
	$id_selected = $destination_default;
}

$ovabrw_pickup_date  = isset( $data_get['ovabrw_pickup_date'] )  ? sanitize_text_field( $data_get['ovabrw_pickup_date'] ) : '';

// search position
$search_position     = $args['search_position'];

// search sidebar title
$search_title    = $args['search_title'];

// guests ( adult & children )
$max_adult 		 = $args['max_adult'];
$min_adult 		 = $args['min_adult'];
$show_adult 	 = $args['show_adult'];

$max_children    = $args['max_children'];
$min_children 	 = $args['min_children'];
$show_children 	 = $args['show_children'];

$max_baby   		= $args['max_baby'];
$min_baby 	 		= $args['min_baby'];
$show_baby 			= $args['show_baby'];

$adults_label 	 = $args['adults_label'];
$childrens_label = $args['childrens_label'];
$babies_label 	 = $args['babies_label'];
$icon_guests     = $args['icon_guests'];
$show_guests 	 = $args['show_guests'];

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
$show_check_in 	 = $args['show_check_in'];

// custom taxonomy
$icon_custom_taxonomy        = $args['icon_custom_taxonomy'];
$custom_taxonomy_placeholder = $args['custom_taxonomy_placeholder'];
$slug_custom_taxonomy        = $args['slug_custom_taxonomy'];
$slug_value_selected 		 = '';

if ( $slug_custom_taxonomy ) {
	$slug_value_selected = isset( $data_get[$slug_custom_taxonomy.'_name'] ) ? sanitize_text_field( $data_get[$slug_custom_taxonomy.'_name'] ) : 'all';
}
if ( isset($ctx_slug_value_selected) ) {
	$slug_value_selected = $ctx_slug_value_selected;
}

if ( isset( $data_get[$slug_custom_taxonomy.'_name'] ) && sanitize_text_field( $data_get[$slug_custom_taxonomy.'_name'] ) ) {
	$slug_value_selected = sanitize_text_field( $data_get[$slug_custom_taxonomy.'_name'] );
}

$terms = get_taxonomy( $slug_custom_taxonomy );

// destination
$icon_destination        = $args['icon_destination'];
$destination_placeholder = $args['destination_placeholder'];
$show_destination 		 = $args['show_destination'];

$posts_per_page   	= $args['posts_per_page'];
$orderby 			= isset( $args['orderby'] ) && $args['orderby'] ? $args['orderby'] : 'ID';
$order   			= isset( $args['order'] ) && $args['order'] ? $args['order'] : 'DESC';
$defautl_category 	= $args['default_category'] ? $args['default_category'] : [];

$search_results_layout 	= $args['search_results_layout'];
$grid_column 			= $args['search_results_grid_column'];
$thumbnail_type 		= isset( $args['thumbnail_type'] ) ? $args['thumbnail_type'] : 'image';

// Avanced Search Settings
$show_advanced_search    = $args['show_advanced_search'];

$filter_price_label      = $args['filter_price_label'];
$show_price_filter 		 = $args['show_price_filter'];

$review_label            = $args['review_label'];
$show_review_filter 	 = $args['show_review_filter'];

$filter_category_label   = $args['filter_category_label'];
$show_category_filter 	 = $args['show_category_filter'];
$excl_category 	 		 = $args['excl_category'];

// Show category
$show_category = '';

if ( $show_advanced_search === 'yes' && $show_category_filter === 'yes' ) {
	$show_category = 'yes';
}

$filter_duration_label   = $args['filter_duration_label'];
$show_duration_filter 	 = $args['show_duration_filter'];
$duration_fields         = isset($args['duration_fields']) ? $args['duration_fields'] : '';

// Filter Settings
$show_filter             = $args['show_filter'];
$tour_found_text         = $args['tour_found_text'];
$clear_filter_text       = $args['clear_filter_text'];

// list category
$args_product_categories = array(
    'taxonomy'   => "product_cat",
    'orderby' 	 => 'ID',
	'order' 	 => 'DESC',
	'exclude' 	 => $excl_category,
);
$product_categories  = get_categories( $args_product_categories );

// get min max price
$prices              = ovabrw_get_filtered_price();
$min_price           = floor($prices->min_price);
$max_price           = round($prices->max_price);
$currency_symbol     = get_woocommerce_currency_symbol();

// Taxonomies
$mutiple_custom_taxonomy 	= $args['mutiple_custom_taxonomy'];
$list_taxonomies 			= isset( $args['list_custom_taxonomy'] ) ? $args['list_custom_taxonomy'] : array();
$data_taxonomies 			= array();

?>

<div class="ovabrw-search-ajax ovabrw-search-ajax-sidebar">
	<div class="wrap-search-ajax wrap-search-ajax-sidebar <?php echo esc_attr( $search_position ); ?>" 
	    data-adults="<?php echo esc_attr( $args['default_adult_number'] ) ;?>"
	    data-childrens="<?php echo esc_attr( $args['default_children_number'] ) ;?>" 
	    data-babies="<?php echo esc_attr( $args['default_babies_number'] ) ;?>" 
	    data-sort_by_default="<?php echo esc_attr( $args['sort_by_default'] ) ;?>"
	    data-start-price="<?php echo esc_attr( $min_price ) ;?>"
	    data-end-price="<?php echo esc_attr( $max_price ) ;?>"
	    data-grid_column="<?php echo esc_attr( $grid_column ) ;?>"
	    data-thumbnail-type="<?php echo esc_attr( $thumbnail_type ); ?>">

		<div class="search-main-content">
		    <!-- Filter -->
			<?php if( $show_filter === 'yes') : ?>
		        <div class="ovabrw-tour-filter">
		        	
		        	<div class="left-filter">
		        		<span class="tour-found-text number-result-tour-found">
			        		<?php echo esc_html__( '0', 'ova-brw' ); ?>
			        	</span>
		        		<span class="tour-found-text">
			        		<?php echo esc_html( $tour_found_text ); ?>
			        	</span>
			        	<span class="clear-filter">
			        		<?php echo esc_html( $clear_filter_text ); ?>
			        	</span>
		        	</div>

		        	<div class="right-filter">
		        		<div class="filter-sort">

		        			<input type="text" class="input_select_input" name="sr_sort_by_label" value="<?php echo esc_html__('Sort by','ova-brw'); ?>" autocomplete="off" readonly="readonly">

							<input type="hidden" class="input_select_input_value" name="sr_sort_by" value="date">

							<ul class="input_select_list" style="display: none;">
							    <li class="term_item <?php if( $sort_by_default == 'date' ) { echo 'term_item_selected' ; } ?>" 
							    	data-id="date"
							    	data-value="<?php esc_attr_e('Sort by latest','ova-brw'); ?>"
							    >
								    <?php echo esc_html__('Latest','ova-brw'); ?>
								</li>
								<li class="term_item <?php if( $sort_by_default == 'rating_desc' ) { echo 'term_item_selected' ; } ?>" 
									data-id="rating_desc" 
									data-value="<?php esc_attr_e('Sort by rating','ova-brw'); ?>"
								>
									<?php echo esc_html__('Rating','ova-brw'); ?>
								</li>
								<li class="term_item <?php if( $sort_by_default == 'price_asc' ) { echo 'term_item_selected' ; } ?>" 
									data-id="price_asc" 
									data-value="<?php esc_attr_e('Sort by price: low to high','ova-brw'); ?>"
								>
									<?php echo esc_html__('Price: low to high','ova-brw'); ?>
								</li>
								<li class="term_item <?php if( $sort_by_default == 'price_desc' ) { echo 'term_item_selected' ; } ?>" 
									data-id="price_desc" 
									data-value="<?php esc_attr_e('Sort by price: high to low','ova-brw'); ?>"
								>
									<?php echo esc_html__('Price: high to low','ova-brw'); ?>
								</li>
							</ul>
						</div>

						<div class="asc_desc_sort">
		        			<i aria-hidden="true" class="asc_sort icomoon icomoon-chevron-up"></i>
		        		    <i aria-hidden="true" class="desc_sort icomoon icomoon-chevron-down"></i>
		        		</div>

		        		<div class="filter-result-layout">
			        		<i aria-hidden="true" class="filter-layout <?php if( $search_results_layout == 'list' )  { echo 'filter-layout-active' ; } ?> icomoon icomoon-list" data-layout="list"></i>
							<i aria-hidden="true" class="filter-layout <?php if( $search_results_layout == 'grid' )  { echo 'filter-layout-active' ; } ?> icomoon icomoon-gird" data-layout="grid"></i>
						</div>
		         	</div>	
		        </div>
		    <?php endif; ?>

			<!-- Load more -->
			<div class="wrap-load-more" style="display: none;">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" />
					<circle cx="25" cy="25" r="20" />
				</svg>
			</div>
			<!-- End load more -->

			<!-- Search result -->
			<?php if ( $show_filter === 'yes' ): ?>
				<?php if ( $sort_by_default == 'date' ): ?>
					<div 
						id="brw-search-ajax-result" 
						class="brw-search-ajax-result" 
						data-order="DESC" 
						data-orderby="date" 
						data-defautl-category="<?php echo esc_attr( json_encode( $defautl_category ) ); ?>" 
						data-show-category="<?php echo esc_attr( $show_category ); ?>" 
						data-orderby_meta_key="" 
						data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
					>
					</div>
				<?php elseif ( $sort_by_default == 'rating_desc' ): ?>
		            <div 
						id="brw-search-ajax-result" 
						class="brw-search-ajax-result" 
						data-order="DESC" 
						data-orderby="meta_value_num"
						data-orderby_meta_key="_wc_average_rating" 
						data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>" 
						data-defautl-category="<?php echo esc_attr( json_encode( $defautl_category ) ); ?>" 
						data-show-category="<?php echo esc_attr( $show_category ); ?>"
					>
					</div>
				<?php elseif ( $sort_by_default == 'price_asc' ): ?>
		            <div 
						id="brw-search-ajax-result" 
						class="brw-search-ajax-result" 
						data-order="ASC" 
						data-orderby="meta_value_num"
						data-orderby_meta_key="_price" 
						data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>" 
						data-defautl-category="<?php echo esc_attr( json_encode( $defautl_category ) ); ?>" 
						data-show-category="<?php echo esc_attr( $show_category ); ?>" 
					>
					</div>
				<?php elseif ( $sort_by_default == 'price_desc' ): ?>
		            <div 
						id="brw-search-ajax-result" 
						class="brw-search-ajax-result" 
						data-order="DESC" 
						data-orderby="meta_value_num"
						data-orderby_meta_key="_price" 
						data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>" 
						data-defautl-category="<?php echo esc_attr( json_encode( $defautl_category ) ); ?>" 
						data-show-category="<?php echo esc_attr( $show_category ); ?>" 
					>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<div 
					id="brw-search-ajax-result" 
					class="brw-search-ajax-result" 
					data-order="<?php echo esc_attr( $order ); ?>" 
					data-orderby="<?php echo esc_attr( $orderby ); ?>"
					data-orderby_meta_key="" 
					data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>" 
					data-defautl-category="<?php echo esc_attr( json_encode( $defautl_category ) ); ?>" 
					data-show-category="<?php echo esc_attr( $show_category ); ?>">
				</div>
			<?php endif; ?>
		</div>

		<!-- Search Sidebar -->
		<div class="search-ajax-sidebar">
            <!--Search -->
			<div class="ovabrw-search">
				<?php if ( $search_title ) : ?>
			    	<h4 class="search-title">
			    		<?php echo esc_html( $search_title ) ; ?>
			    		<i aria-hidden="true" class="icomoon icomoon-chevron-up"></i>
			    	</h4>
				<?php endif; ?>
				<form class="ovabrw-search-form" method="POST" autocomplete="off">	
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
								</div>
								<div class="ovabrw-input search_in_destination">
									<?php echo ovabrw_destination_dropdown( $destination_placeholder, $id_selected ); ?>
								</div>
							</div>
						<?php endif; ?>

				        <?php if ( ! empty( $slug_custom_taxonomy ) && !empty( $terms ) ) : ?>
				            <!-- custom taxonomy -->
							<div class="search-field">
								<div class="ovabrw-label">
									<?php 
									    if( $icon_custom_taxonomy ) {
									    	\Elementor\Icons_Manager::render_icon( $icon_custom_taxonomy, [ 'aria-hidden' => 'true' ] );
									    }     
								    ?>
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
									</div>
									<input
										type="text"
										name="ovabrw_pickup_date" 
										class="ovabrw_datetimepicker ovabrw_start_date"
										value="<?php echo esc_attr( $ovabrw_pickup_date ); ?>"
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
					</div>

					<!-- Advanced Search -->
					<?php if( $show_advanced_search === 'yes') : ?>
				        <div class="ovabrw-search-advanced-sidebar">

				        	<div class="search-advanced-field-wrapper">
				        		<!-- Price Filter -->
				        		<?php if ( $show_price_filter === 'yes' ): ?>
					        	    <div class="search-advanced-field price-field">
					        	    	<span class="ovabrw-label">
					        	    		<?php echo esc_html($filter_price_label) ; ?>
					        	    		<i aria-hidden="true" class="icomoon icomoon-chevron-up"></i>
					        	    	</span>
					        	    	<div class="search-advanced-content">
					        	    		<div class="slider-wrapper">
										    <div id="brw-tour-price-slider"></div>
											</div>
											<div class="brw-tour-price-input" data-currency_symbol="<?php echo esc_attr($currency_symbol); ?>" data-auto="<?php echo esc_attr( apply_filters( 'ovabrw-ft-price-auto', false ) ); ?>">
												<div class="tour-price-value">
													<span><?php echo esc_html($currency_symbol);?></span>
							        	    	    <input type="text" class="brw-tour-price-from" value="<?php echo esc_attr($min_price) ;?>" data-value="<?php echo esc_attr($min_price) ;?>"/>
												</div>
												<div class="tour-price-value">
													<span><?php echo esc_html($currency_symbol);?></span>
													<input type="text" class="brw-tour-price-to" value="<?php echo esc_attr($max_price) ;?>" data-value="<?php echo esc_attr($max_price) ;?>"/>
												</div>
											</div>
					        	    	</div>
					        	    </div>
					        	<?php endif; ?>

				        	    <!-- Rating Filter -->
				        	    <?php if ( $show_review_filter === 'yes' ): ?>
					        	    <div class="search-advanced-field rating-field">
					        	    	<span class="ovabrw-label">
					        	    		<?php echo esc_html($review_label) ; ?>
					        	    		<i aria-hidden="true" class="icomoon icomoon-chevron-up"></i>
					        	    	</span>
					        	    	<div class="search-advanced-content">
						        	     	<?php for( $i = 5; $i>=1 ; $i--) { ?>
						        	     		<div class="total-rating-stars">

						        	     			<div class="input-rating">
						        	     				<input id="rating-filter-<?php echo esc_attr($i) ;?>" type="checkbox" class="rating-filter" name="rating_value[<?php echo esc_attr($i) ;?>]" value="<?php echo esc_attr($i) ;?>">

						        	     				<label for="rating-filter-<?php echo esc_attr($i) ;?>">
						        	     					<?php switch ($i) {
						        	     						case 1: ?>
																	<span class="rating-stars">
																		<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
																	</span>
																	<?php break; ?>
																<?php case 2: ?>
																	<span class="rating-stars">
																		<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
																		<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
																	</span>
																	<?php break; ?>
																<?php case 3: ?>
																	<span class="rating-stars">
																		<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
																		<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
																		<span class="star star-3" data-rating-val="3"><i class="fas fa-star"></i></span>
																	</span>
																	<?php break; ?>
																<?php case 4: ?>
																	<span class="rating-stars">
																		<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
																		<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
																		<span class="star star-3" data-rating-val="3"><i class="fas fa-star"></i></span>
																		<span class="star star-4" data-rating-val="4"><i class="fas fa-star"></i></span>
																	</span>
																    <?php break; ?>
							        	     					<?php case 5: ?>
																	<span class="rating-stars">
																		<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
																		<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
																		<span class="star star-3" data-rating-val="3"><i class="fas fa-star"></i></span>
																		<span class="star star-4" data-rating-val="4"><i class="fas fa-star"></i></span>
																		<span class="star star-5" data-rating-val="5"><i class="fas fa-star"></i></span> 
																	</span>
																    <?php break; ?>
														   <?php } ?>
														</label>

						        	     			</div>

						        	     		</div>
						        	     	<?php } ?>
						        	     </div>
					        	    </div>
					        	<?php endif; ?>

				        	    <!-- Tour Categories Filter -->
				        	    <?php if ( $show_category_filter === 'yes' ): ?>
					        	    <div class="search-advanced-field tour-categories-field">
					        	    	<span class="ovabrw-label">
					        	    		<?php echo esc_html($filter_category_label) ; ?>
					        	    	</span>
					        	     	<?php foreach( $product_categories as $pro_cat ) : ?>
					        	     		<?php if($pro_cat->category_parent == 0) { 
					        	     			$cat_id = $pro_cat->term_id; 
					        	     			$args_product_categories_2 = array(
									                'taxonomy'   => 'product_cat',
									                'child_of'   => 0,
									                'parent'     => $cat_id,
									                'orderby' 	 => 'ID',
													'order' 	 => 'DESC',
													'exclude' 	 => $excl_category,
										        );
										        $sub_cats = get_categories( $args_product_categories_2 );
					        	     		?>
						        	     		<div class="tour-category-field">
						        	     			<input 
						        	     				id="tour-category-filter-<?php echo esc_attr( $pro_cat->slug ); ?>" 
						        	     				type="checkbox" 
						        	     				class="tour-category-filter" 
						        	     				name="category_value" 
						        	     				value="<?php echo esc_attr($pro_cat->slug) ;?>" 
						        	     				<?php echo in_array( $pro_cat->slug, $defautl_category ) ? 'checked' : ''; ?>
						        	     			>

							        	     		<label for="tour-category-filter-<?php echo esc_attr( $pro_cat->slug ) ;?>">
														<span class="tour-category-name">
															<?php echo esc_html( $pro_cat->name ) ; ?>
														</span>
													</label>

													<?php if ( $sub_cats ) { foreach ( $sub_cats as $sub_category ) { ?>
											            <div class="tour-category-field-child">
								        	     			<input 
								        	     				id="tour-category-filter-<?php echo esc_attr( $sub_category->slug ) ;?>" 
								        	     				type="checkbox" 
								        	     				class="tour-category-filter" 
								        	     				name="category_value" 
								        	     				value="<?php echo esc_attr( $sub_category->slug ) ;?>" 
								        	     				<?php echo in_array( $sub_category->slug, $defautl_category ) ? 'checked' : ''; ?>
								        	     			>

									        	     		<label for="tour-category-filter-<?php echo esc_attr($sub_category->slug) ;?>">
																<span class="tour-category-name">
																	<?php echo esc_html( $sub_category->name ) ; ?>
																</span>
															</label>
															
															<?php 
									        	     			$sub_cat_id = $sub_category->term_id; 
									        	     			$args_product_categories_3 = array(
													                'taxonomy'   => 'product_cat',
													                'child_of'   => 0,
													                'parent'     => $sub_cat_id,
													                'orderby' 	 => 'ID',
																	'order' 	 => 'DESC',
																	'exclude' 	 => $excl_category,
														        );
														        $sub_cats_2 = get_categories( $args_product_categories_3 );
														    ?>

													        <?php if ( $sub_cats_2 ) { foreach ( $sub_cats_2 as $sub_category_2 ) { ?>
													            <div class="tour-category-field-child">
										        	     			<input 
										        	     				id="tour-category-filter-<?php echo esc_attr($sub_category_2->slug) ;?>" 
										        	     				type="checkbox" 
										        	     				class="tour-category-filter" 
										        	     				name="category_value" 
										        	     				value="<?php echo esc_attr( $sub_category_2->slug) ;?>" 
										        	     				<?php echo in_array( $sub_category_2->slug, $defautl_category ) ? 'checked' : ''; ?>
										        	     			>

											        	     		<label for="tour-category-filter-<?php echo esc_attr($sub_category_2->slug) ;?>">
																		<span class="tour-category-name">
																			<?php echo esc_html( $sub_category_2->name ) ; ?>
																		</span>
																	</label>
																</div>
															<?php } } ?>
															
														</div>
														
											        <?php } } ?> 

							        	     	</div>
							        	    <?php } ?>
					        	     	<?php endforeach;?>
					        	    </div>
					        	<?php endif; ?>

					        	<!-- Duration Filter -->
				        		<?php if ( $show_duration_filter === 'yes' ): ?>
					        	    <div class="search-advanced-field tour-duration-field">
					        	    	<span class="ovabrw-label">
					        	    		<?php echo esc_html($filter_duration_label) ; ?>
					        	    	</span>
					        	    	<?php if( is_array($duration_fields) ) : foreach( $duration_fields as $k => $duration_field) :
				                			if( $duration_field['duration_type'] === "day" ) {
				                				$value_from = $duration_field['duration_day_value_from'];
				                				$value_to   = $duration_field['duration_day_value_to'];

				                		    } elseif( $duration_field['duration_type'] === "hour" ) {
				                		    	$value_from = $duration_field['duration_hour_value_from'];
				                				$value_to   = $duration_field['duration_hour_value_to'];
				                            }
				                		?>
					                		<div class="duration-field">
						                		<input id="duration-filter-<?php echo esc_attr($k) ;?>" type="radio" class="duration-filter" name="duration_value_from" value="<?php echo esc_attr($value_from) ;?>">
						                		<input type="hidden" class="duration-filter-to" name="duration_value_to" value="<?php echo esc_attr($value_to) ;?>">
						                		<input type="hidden" class="duration-filter-type" name="duration_value_type" value="<?php echo esc_attr($duration_field['duration_type']) ;?>">

						        	     		<label for="duration-filter-<?php echo esc_attr($k) ;?>">
													<span class="duration-name">
														<?php echo esc_html( $duration_field['duration_name']) ; ?>
													</span>
												</label>
					                		</div>
					                	<?php endforeach; endif;?>
					        	    </div>
					        	<?php endif; ?>
				        	</div>
				        </div>
				    <?php endif; ?>

				    <!-- button -->
					<div class="ovabrw-search-btn">
						<button class="ovabrw-btn" type="submit">
							<?php 
							    if( $icon_button ) {
							    	\Elementor\Icons_Manager::render_icon( $icon_button, [ 'aria-hidden' => 'true' ] );
							    }   
							    printf( $button_label );    
						    ?>
						</button>		
					</div>
				</form>
			</div>
		</div>

    </div>
</div>