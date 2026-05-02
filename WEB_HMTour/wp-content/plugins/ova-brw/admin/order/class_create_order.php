<?php
if ( ! defined( 'ABSPATH' ) ) exit();


// Display Manage Booking
function ovabrw_create_order() {
	$date_format 		= ovabrw_get_date_format();
	$placeholder_date   = ovabrw_get_placeholder_date();

	// Get all Products has Product Data: Rental
	$all_products = ovabrw_get_all_products();
    $html_list_option_product = '<option value="">'.esc_html__("Select Product", "ova-brw" ).'</option>';
	    while ( $all_products->have_posts() ) : $all_products->the_post();
	        global $product;
	        $html_list_option_product .= '<option value="'.get_the_id().'">'.get_the_title().'</option>';
	    endwhile; wp_reset_postdata(); wp_reset_query();

	// Defautl country
    $country_setting = get_option( 'woocommerce_default_country', 'US:CA' );
	if ( strstr( $country_setting, ':' ) ) {
		$country_setting = explode( ':', $country_setting );
		$country         = current( $country_setting );
		$state           = end( $country_setting );
	} else {
		$country = $country_setting;
		$state   = '*';
	}

	?>
	<div class="wrap">
	    <form 
	    	id="booking-filter" 
	    	method="POST" 
	    	enctype="multipart/form-data" 
	    	action="<?php echo admin_url('/edit.php?post_type=product&page=ovabrw-create-order'); ?>"
	    	autocomplete="off">
	    	<h2><?php esc_html_e( 'Create Order', 'ova-brw' ); ?></h2>
	    	<div class="ovabrw-wrap">
	    		<?php
    			// Multi currency
    			$currencies = [];

    			if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
    				$setting 	= WOOMULTI_CURRENCY_F_Data::get_ins();
    				$currencies = $setting->get_list_currencies();
    			}
    			if ( is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
    				$setting 	= WOOMULTI_CURRENCY_Data::get_ins();
    				$currencies = $setting->get_list_currencies();
    			}
    			if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
    				// WPML multi currency
		    		global $woocommerce_wpml;

		    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
		    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

				        $currencies 	= $woocommerce_wpml->get_setting( 'currency_options' );
				        $current_lang 	= apply_filters( 'wpml_current_language', NULL );

				        if ( $current_lang != 'all' && ! empty( $currencies ) && is_array( $currencies ) ) {
				        	foreach ( $currencies as $k => $data ) {
				        		if ( isset( $currencies[$k]['languages'][$current_lang] ) && $currencies[$k]['languages'][$current_lang] ) {
				        			continue;
				        		} else {
				        			unset( $currencies[$k] );
				        		}
				        	}
				        }
				    }
    			}

    			if ( ! empty( $currencies ) && is_array( $currencies ) ):
					$current_currency = isset( $_GET['currency'] ) && $_GET['currency'] ? $_GET['currency'] : '';

					if ( ! $current_currency ) $current_currency = array_key_first( $currencies ); 
				?>
					<div class="ovabrw-row ovabrw-order-currency">
		    			<label for="ovabrw-currency"><?php esc_html_e( 'Currency', 'ova-brw' ); ?></label>
		    			<select name="currency" id="ovabrw-currency">
		    		<?php
					foreach ( $currencies as $currency => $rate ): ?>
	    				<option value="<?php echo esc_attr( $currency ); ?>"<?php selected( $currency, $current_currency ); ?>>
	    					<?php esc_html_e( $currency ); ?>
	    				</option>
			    	<?php endforeach; ?>
			    		</select>
			    		<input
			    			type="hidden"
			    			name="ovabrw-admin-url"
			    			value="<?php echo esc_url( get_admin_url() ); ?>"
			    		/>
		    		</div>
			    <?php endif; ?>
	    		<div class="ovabrw-row ovabrw-choose-order-status">
	    			<label for="stattus-order">
	    				<?php esc_html_e( 'Status', 'ova-brw' ) ?>
	    			</label>
	    			<select name="status_order" id="stattus-order">
	    				<option value="completed" selected >
	    					<?php esc_html_e( 'Completed', 'ova-brw' ); ?>
	    				</option>
	    				<option value="processing">
	    					<?php esc_html_e( 'Processing', 'ova-brw' ); ?>
	    				</option>
	    				<option value="pending">
	    					<?php esc_html_e( 'Pending payment', 'ova-brw' ); ?>
	    				</option>
	    				<option value="on-hold">
	    					<?php esc_html_e( 'On hold', 'ova-brw' ); ?>
	    				</option>
	    				<option value="cancelled">
	    					<?php esc_html_e( 'Cancelled', 'ova-brw' ); ?>
	    				</option>
	    				<option value="refunded">
	    					<?php esc_html_e( 'Refunded', 'ova-brw' ); ?>
	    				</option>
	    				<option value="failed">
	    					<?php esc_html_e( 'Failed', 'ova-brw' ); ?>
	    				</option>
	    			</select>
	    		</div>
	            <div class="ovabrw-row ova-column-3">
	            	<div class="item">
	            		<input 
	            			type="text" 
	            			name="ovabrw_first_name" 
	            			placeholder="<?php esc_html_e( 'First Name', 'ova-brw' ); ?>" 
	            			required />
	            	</div>
	            	<div class="item">
	            		<input 
	            			type="text" 
	            			name="ovabrw_last_name" 
	            			placeholder="<?php esc_html_e( 'Last Name', 'ova-brw' ); ?>" 
	            			required />
	            	</div>
	            	<div class="item">
	            		<input 
	            			type="text" 
	            			name="ovabrw_company" 
	            			placeholder="<?php esc_html_e( 'Company', 'ova-brw' ); ?>" />
	            	</div>
	            	<div class="item">
	            		<input 
	            			type="email" 
	            			name="ovabrw_email" 
	            			placeholder="<?php esc_html_e( 'Email', 'ova-brw' ); ?>" 
	            			required />
	            	</div>
	            	<div class="item">
	            		<input 
	            			type="text" 
	            			name="ovabrw_phone" 
	            			placeholder="<?php esc_html_e( 'Phone', 'ova-brw' ); ?>" 
	            			required />
	            	</div>
	            	<div class="item">
	            		<input 
	            			type="text" 
	            			name="ovabrw_address_1" 
	            			placeholder="<?php esc_html_e( 'Address 1', 'ova-brw' ); ?>" />
	            	</div>
	            	<div class="item">
	            		<input 
	            			type="text" 
	            			name="ovabrw_address_2" 
	            			placeholder="<?php esc_html_e( 'Address 2', 'ova-brw' ); ?>" />
	            	</div>
	            	<div class="item">
	            		<input 
	            			type="text" 
	            			name="ovabrw_city" 
	            			placeholder="<?php esc_html_e( 'City', 'ova-brw' ); ?>" />
	            	</div>
	            	<div class="item">
	            		<select name="ovabrw_country" class="ovabrw_country" style="width: 100%;">
							<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
						</select>
	            	</div>
	            </div>
	            <div class="wrap_item">
	            	<div class="ovabrw-order">
		            	<div class="item">
		            		<div class="sub-item">
		            			<h3 class="title">
		            				<?php esc_html_e('Product', 'ova-brw'); ?>
		            			</h3>
		            			<div class="rental_item">
		            				<label for="ovabrw-data-product">
		            					<?php esc_html_e( 'Choose Product *', 'ova-brw' ); ?>
		            				</label>
		            				<select 
		            					id="ovabrw-data-product" 
		            					class="required ovabrw-data-product" 
		            					name="ovabrw-data-product[]" 
		            					data-symbol="<?php echo get_woocommerce_currency_symbol(); ?>" 
		            					data-date_format="<?php echo $date_format; ?>" 
		            					data-error="<?php echo esc_attr( 'Choose Product is required.', 'ova-brw' ); ?>">
		            					<?php echo $html_list_option_product ?>
		            				</select>
		            				<div class="loading">
			            				<div class="dashicons-before dashicons-update-alt"></div>
			            			</div>
		            			</div>
		            		</div>
		            		<div class="sub-item ovabrw-meta">
		            			<h3 class="title">
		            				<?php esc_html_e('Add Meta', 'ova-brw'); ?>
		            			</h3>
		            			<div class="rental_item ovabrw-adult-price">
									<label for="ovabrw-adult-price">
										<?php esc_html_e( 'Adult Price', 'ova-brw' ); ?>
									</label>
									<input 
										id="ovabrw-adult-price" 
										type="text" name="ovabrw_adult_price[]" 
										class="ovabrw_adult_price" readonly />
									<span class="ovabrw-current-currency">
										<?php echo get_woocommerce_currency_symbol(); ?>
									</span>
									<div class="loading-total">
			            				<div class="dashicons-before dashicons-update-alt"></div>
			            			</div>
								</div>
								<div class="rental_item ovabrw-children-price">
									<label for="ovabrw-children-price">
										<?php esc_html_e( 'Children Price', 'ova-brw' ); ?>
									</label>
									<input 
										id="ovabrw-children-price" 
										type="text" name="ovabrw_children_price[]" 
										class="ovabrw_children_price" readonly />
									<span class="ovabrw-current-currency">
										<?php echo get_woocommerce_currency_symbol(); ?>
									</span>
									<div class="loading-total">
			            				<div class="dashicons-before dashicons-update-alt"></div>
			            			</div>
								</div>
								<div class="rental_item ovabrw-baby-price">
									<label for="ovabrw-baby-price">
										<?php esc_html_e( 'Baby Price', 'ova-brw' ); ?>
									</label>
									<input 
										id="ovabrw-baby-price" 
										type="text" name="ovabrw_baby_price[]" 
										class="ovabrw_baby_price" readonly />
									<span class="ovabrw-current-currency">
										<?php echo get_woocommerce_currency_symbol(); ?>
									</span>
									<div class="loading-total">
			            				<div class="dashicons-before dashicons-update-alt"></div>
			            			</div>
								</div>
								<div class="rental_item ovabrw-pickup">
									<label for="ovabrw-pickup-date">
										<?php esc_html_e( 'Check-in *', 'ova-brw' ); ?>
									</label>
									<input 
										type="text" name="ovabrw_pickup_date[]" 
										id="ovabrw-pickup-date" 
										data-days="1" 
										data-date-format="<?php echo esc_attr( $date_format ); ?>" 
										class="required ovabrw_start_date ovabrw_datetimepicker" 
										data-error="<?php echo esc_attr( 'Check-in is required.', 'ova-brw' ); ?>" 
										autocomplete="off" 
										placeholder="<?php echo esc_attr( $placeholder_date ); ?>" />
								</div>
								<div class="rental_item ovabrw-dropoff-date">
									<label>
										<?php esc_html_e( 'Check-out *', 'ova-brw' ); ?>
									</label>
									<input 
										type="text" 
										name="ovabrw_pickoff_date[]" 
										id="ovabrw-dropoff-date" 
										class="required" 
										data-error="<?php echo esc_attr( 'Check-out is required.', 'ova-brw' ); ?>" 
										autocomplete="off" 
										placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
										readonly />
								</div>
								<div class="rental_item ovabrw-number-adults">
								    <label>
								    	<?php esc_html_e( 'Number of adults', 'ova-brw' ); ?>
								    </label>
								    <input 
				                        type="number" 
				                        name="ovabrw_adults[]" 
				                        id="ovabrw_adults" 
				                        class="ovabrw_adults" 
				                        value="1" 
				                        max="" 
				                        min="1" 
				                        placeholder="1" 
				                        data-error="<?php esc_html_e( 'Adults is required.', 'ova-brw' ); ?>" />
								</div>
								<div class="rental_item ovabrw-number-childrens">
								    <label>
								    	<?php esc_html_e( 'Number of children', 'ova-brw' ); ?>
								    </label>
								    <input 
				                        type="number" 
				                        name="ovabrw_childrens[]" 
				                        id="ovabrw_childrens" 
				                        class="ovabrw_childrens" 
				                        value="0" 
				                        max="" 
				                        min="0" 
				                        placeholder="0" 
				                        data-error="<?php esc_html_e( 'Children is required.', 'ova-brw' ); ?>" />
								</div>
								<div class="rental_item ovabrw-number-babies">
								    <label>
								    	<?php esc_html_e( 'Number of babies', 'ova-brw' ); ?>
								    </label>
								    <input 
				                        type="number" 
				                        name="ovabrw_babies[]" 
				                        id="ovabrw_babies" 
				                        class="ovabrw_babies" 
				                        value="0" 
				                        max="" 
				                        min="0" 
				                        placeholder="0" 
				                        data-error="<?php esc_html_e( 'Babies is required.', 'ova-brw' ); ?>" />
								</div>
								<div class="rental_item ovabrw-custom_ckf"></div>
								<div class="rental_item ovabrw-resources">
									<label for="ovabrw-resources">
										<?php esc_html_e( 'Resources', 'ova-brw' ); ?>
									</label>
									<span class="ovabrw-resources-span"></span>
								</div>
								<div class="rental_item ovabrw-services">
									<label for="ovabrw-services">
										<?php esc_html_e( 'Services', 'ova-brw' ); ?>
									</label>
									<span class="ovabrw-services-span"></span>
								</div>
								<div class="rental_item ovabrw-amount-insurance">
									<label for="ovabrw-amount-insurance">
										<?php esc_html_e( 'Amount of insurance', 'ova-brw' ); ?>
									</label>
									<input 
										id="ovabrw-amount-insurance" 
										type="text" 
										name="ovabrw_amount_insurance[]" 
										class="ovabrw_amoun_insurance" 
										placeholder="0" 
										readonly />
									<span class="ovabrw-current-currency">
										<?php echo get_woocommerce_currency_symbol(); ?>
									</span>
								</div>
								<div class="rental_item ovabrw-amount-deposite">
									<label for="ovabrw-amount-deposite">
										<?php esc_html_e( 'Deposit Amount', 'ova-brw' ); ?>
									</label>
									<input 
										id="ovabrw-amount-deposite" 
										type="text" 
										name="ovabrw_amount_deposite[]" 
										class="ovabrw_amoun_deposite" 
										placeholder="0" />
									<span class="ovabrw-current-currency">
										<?php echo get_woocommerce_currency_symbol(); ?>
									</span>
								</div>
								<div class="rental_item ovabrw-amount-remaining">
									<label for="ovabrw-amount-remaining">
										<?php esc_html_e( 'Remaining Amount', 'ova-brw' ); ?>
									</label>
									<input 
										id="ovabrw-amount-remaining" 
										type="text" 
										name="ovabrw_amount_remaining[]" 
										class="ovabrw_amount_remaining" 
										placeholder="0" 
										readonly />
									<span class="ovabrw-current-currency">
										<?php echo get_woocommerce_currency_symbol(); ?>
									</span>
									<div class="loading-total">
			            				<div class="dashicons-before dashicons-update-alt"></div>
			            			</div>
								</div>
								<div class="rental_item ovabrw-total">
		            				<label for="ovabrw-total-product">
		            					<?php esc_html_e( 'Total', 'ova-brw' ); ?>
		            				</label>
		            				<input 
		            					id="ovabrw-total-product" 
		            					type="text" 
		            					name="ovabrw-total-product[]" 
		            					class="ovabrw-total-product" 
		            					data-error="<?php echo esc_attr( 'Total is required.', 'ova-brw' ); ?>" 
		            					readonly />
		            				<span class="ovabrw-current-currency">
										<?php echo get_woocommerce_currency_symbol(); ?>
									</span>
		            				<div class="loading-total">
			            				<div class="dashicons-before dashicons-update-alt"></div>
			            			</div>
		            			</div>
		            			<div class="rental_item ovabrw-error">
									<span class="ovabrw-error-span"></span>
								</div>
		            		</div>
		            	</div>
		            	<a href="#" class="delete_order">x</a>
		            </div>
	            </div>
				<div class="ovabrw-row">
					<a href="#" class="button insert_wrap_item" data-row="
						<?php
							ob_start();
							?>
								<div class="ovabrw-order">
					            	<div class="item">
					            		<div class="sub-item">
					            			<h3 class="title">
					            				<?php esc_html_e('Product', 'ova-brw'); ?>
					            			</h3>
					            			<div class="rental_item">
					            				<label for="ovabrw-data-product">
					            					<?php esc_html_e( 'Choose Product', 'ova-brw' ); ?>
					            				</label>
					            				<select 
					            					id="ovabrw-data-product" 
					            					class="ovabrw-data-product" 
					            					name="ovabrw-data-product[]" 
					            					data-symbol="<?php echo get_woocommerce_currency_symbol(); ?>" 
					            					data-date_format="<?php echo $date_format; ?>">
					            					<?php echo $html_list_option_product ?>
					            				</select>
					            				<div class="loading">
						            				<div class="dashicons-before dashicons-update-alt"></div>
						            			</div>
					            			</div>
					            		</div>
					            		<div class="sub-item ovabrw-meta">
					            			<h3 class="title">
					            				<?php esc_html_e('Add Meta', 'ova-brw'); ?>
					            			</h3>
					            			<div class="rental_item ovabrw-adult-price">
												<label for="ovabrw-adult-price">
													<?php esc_html_e( 'Adult Price', 'ova-brw' ); ?>
												</label>
												<input 
													id="ovabrw-adult-price" 
													type="text" name="ovabrw_adult_price[]" 
													class="ovabrw_adult_price" readonly />
												<span class="ovabrw-current-currency">
													<?php echo get_woocommerce_currency_symbol(); ?>
												</span>
												<div class="loading-total">
						            				<div class="dashicons-before dashicons-update-alt"></div>
						            			</div>
											</div>
											<div class="rental_item ovabrw-children-price">
												<label for="ovabrw-children-price">
													<?php esc_html_e( 'Children Price', 'ova-brw' ); ?>
												</label>
												<input 
													id="ovabrw-children-price" 
													type="text" name="ovabrw_children_price[]" 
													class="ovabrw_children_price" readonly />
												<span class="ovabrw-current-currency">
													<?php echo get_woocommerce_currency_symbol(); ?>
												</span>
												<div class="loading-total">
						            				<div class="dashicons-before dashicons-update-alt"></div>
						            			</div>
											</div>
											<div class="rental_item ovabrw-baby-price">
												<label for="ovabrw-baby-price">
													<?php esc_html_e( 'Baby Price', 'ova-brw' ); ?>
												</label>
												<input 
													id="ovabrw-baby-price" 
													type="text" name="ovabrw_baby_price[]" 
													class="ovabrw_baby_price" readonly />
												<span class="ovabrw-current-currency">
													<?php echo get_woocommerce_currency_symbol(); ?>
												</span>
												<div class="loading-total">
						            				<div class="dashicons-before dashicons-update-alt"></div>
						            			</div>
											</div>
											<div class="rental_item ovabrw-pickup">
												<label for="ovabrw-pickup-date">
													<?php esc_html_e( 'Check-in *', 'ova-brw' ); ?>
												</label>
												<input 
													type="text" 
													name="ovabrw_pickup_date[]" 
													id="ovabrw-pickup-date" 
													data-days="1" 
													data-date-format="<?php echo esc_attr( $date_format ); ?>" 
													class="required ovabrw_start_date ovabrw_datetimepicker" 
													autocomplete="off" 
													placeholder="<?php echo esc_attr( $placeholder_date ); ?>" />
											</div>
											<div class="rental_item ovabrw-dropoff-date">
												<label>
													<?php esc_html_e( 'Check-out *', 'ova-brw' ); ?>
												</label>
												<input 
													type="text" 
													name="ovabrw_pickoff_date[]" 
													id="ovabrw-dropoff-date" 
													class="required" 
													data-error="<?php echo esc_attr( 'Check-out is required.', 'ova-brw' ); ?>" 
													autocomplete="off" 
													placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
													readonly />
											</div>
											<div class="rental_item ovabrw-number-adults">
											    <label>
											    	<?php esc_html_e( 'Number of adults', 'ova-brw' ); ?>
											    </label>
											    <input 
							                        type="number" 
							                        name="ovabrw_adults[]" 
							                        id="ovabrw_adults" 
							                        class="required ovabrw_adults" 
							                        value="1" 
							                        max="" 
							                        min="1" 
							                        placeholder="1" 
							                        data-error="<?php esc_html_e( 'Adults is required.', 'ova-brw' ); ?>" />
											</div>
											<div class="rental_item ovabrw-number-childrens">
											    <label>
											    	<?php esc_html_e( 'Number of children', 'ova-brw' ); ?>
											    </label>
											    <input 
							                        type="number" 
							                        name="ovabrw_childrens[]" 
							                        id="ovabrw_childrens" 
							                        class="ovabrw_childrens" 
							                        value="0" 
							                        max="" 
							                        min="0" 
							                        placeholder="0" 
							                        data-error="<?php esc_html_e( 'Children is required.', 'ova-brw' ); ?>" />
											</div>
											<div class="rental_item ovabrw-number-babies">
											    <label>
											    	<?php esc_html_e( 'Number of babies', 'ova-brw' ); ?>
											    </label>
											    <input 
							                        type="number" 
							                        name="ovabrw_babies[]" 
							                        id="ovabrw_babies" 
							                        class="ovabrw_babies" 
							                        value="0" 
							                        max="" 
							                        min="0" 
							                        placeholder="0" 
							                        data-error="<?php esc_html_e( 'Babies is required.', 'ova-brw' ); ?>" />
											</div>
											<div class="rental_item ovabrw-custom_ckf"></div>
											<div class="rental_item ovabrw-resources">
												<label for="ovabrw-resources">
													<?php esc_html_e( 'Resources', 'ova-brw' ); ?>
												</label>
												<span class="ovabrw-resources-span"></span>
											</div>

											<div class="rental_item ovabrw-services">
												<label for="ovabrw-services">
													<?php esc_html_e( 'Services', 'ova-brw' ); ?>
												</label>
												<span class="ovabrw-services-span"></span>
											</div>
											<div class="rental_item ovabrw-amount-insurance">
												<label for="ovabrw-amount-insurance">
													<?php esc_html_e( 'Amount of insurance', 'ova-brw' ); ?>
												</label>
												<input 
													id="ovabrw-amount-insurance" 
													type="text" 
													name="ovabrw_amount_insurance[]" 
													class="ovabrw_amoun_insurance" 
													placeholder="0" 
													readonly />
												<span class="ovabrw-current-currency">
													<?php echo get_woocommerce_currency_symbol(); ?>
												</span>
											</div>
											<div class="rental_item ovabrw-amount-deposite">
												<label for="ovabrw-amount-deposite">
													<?php esc_html_e( 'Deposit Amount', 'ova-brw' ); ?>
												</label>
												<input 
													id="ovabrw-amount-deposite" 
													type="text" 
													name="ovabrw_amount_deposite[]" 
													class="ovabrw_amoun_deposite" 
													placeholder="0" />
												<span class="ovabrw-current-currency">
													<?php echo get_woocommerce_currency_symbol(); ?>
												</span>
											</div>
											<div class="rental_item ovabrw-amount-remaining">
												<label for="ovabrw-amount-remaining">
													<?php esc_html_e( 'Remaining Amount', 'ova-brw' ); ?>
												</label>
												<input 
													id="ovabrw-amount-remaining" 
													type="text" 
													name="ovabrw_amount_remaining[]" 
													class="ovabrw_amount_remaining" 
													placeholder="0" 
													readonly />
												<span class="ovabrw-current-currency">
													<?php echo get_woocommerce_currency_symbol(); ?>
												</span>
												<div class="loading-total">
						            				<div class="dashicons-before dashicons-update-alt"></div>
						            			</div>
											</div>
											<div class="rental_item ovabrw-total">
					            				<label for="ovabrw-total-product">
					            					<?php esc_html_e( 'Total', 'ova-brw' ); ?>
					            				</label>
					            				<input 
					            					id="ovabrw-total-product" 
					            					type="text" 
					            					name="ovabrw-total-product[]" 
					            					class="ovabrw-total-productt" 
					            					readonly />
					            				<span class="ovabrw-current-currency">
													<?php echo get_woocommerce_currency_symbol(); ?>
												</span>
					            				<div class="loading-total">
						            				<div class="dashicons-before dashicons-update-alt"></div>
						            			</div>
					            			</div>
					            			<div class="rental_item ovabrw-error">
												<span class="ovabrw-error-span"></span>
											</div>
					            		</div>
					            	</div>
					            	<a href="#" class="delete_order">x</a>
					            </div>
							<?php
							echo esc_attr( ob_get_clean() );
						?>
					">
					<?php esc_html_e( 'Add Item', 'ova-brw' ); ?></a>
					</a>
				</div>
				<button type="submit" class="button"><?php esc_html_e( 'Create Order', 'ova-brw' ); ?></button>
				<span class="create-order-error"></span>
	    	</div>
	        <input type="hidden" name="post_type" value="product" />
	        <input type="hidden" name="ovabrw_create_order" value="create_order" />
	        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    </form>
	</div>
<?php
}