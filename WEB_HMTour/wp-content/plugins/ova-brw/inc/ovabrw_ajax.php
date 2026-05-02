<?php defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'OVABRW_Ajax' ) ) {

	class OVABRW_Ajax {

		public function __construct() {
			$this->init();
		}

		public function init() {
			$arr_ajax = array(
				'ovabrw_calculate_total',
				'ovabrw_show_time',
				'ovabrw_choose_time',
				'ovabrw_duration_change',
				'ovabrw_loading_fixed_dates',
				'ovabrw_check_max_guests',
				'ovabrw_remove_cart',
				'ovabrw_verify_reCAPTCHA',
				'ovabrw_search_ajax',
				'ovabrw_load_product_filter',
				'ovabrw_product_category_ajax',
				'ovabrw_product_destination_ajax',
				'ovabrw_load_data_product_create_order',
				'ovabrw_create_order_get_total',
				'ovabrw_create_order_show_time',
				'ovabrw_create_order_choose_time'
			);

			foreach ( $arr_ajax as $name ) {
				add_action( 'wp_ajax_'.$name, array( $this, $name ) );
				add_action( 'wp_ajax_nopriv_'.$name, array( $this, $name ) );
			}
		}

		/**
		 * Calculate Total
		 */
		public function ovabrw_calculate_total() {
			$product_id 	= isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
			$pickup_date 	= isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
			$time_from 		= isset( $_POST['time_from'] ) ? sanitize_text_field( $_POST['time_from'] ) : '';
			$dropoff_date 	= isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
			$quantity 		= isset( $_POST['quantity'] ) ? absint( sanitize_text_field( $_POST['quantity'] ) ) : 1;
			$deposit 		= isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : '';
			$custom_ckf 	= isset( $_POST['custom_ckf'] ) ? str_replace( '\\', '', $_POST['custom_ckf'] )  : '';
			$resources 		= isset( $_POST['resources'] ) ? recursive_array_replace( '\\', '', $_POST['resources'] )  : '';
			$services 		= isset( $_POST['services'] ) ? recursive_array_replace( '\\', '', $_POST['services'] )  : '';

			// Get new date
			$date_format    = ovabrw_get_date_format();
			$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
		    $min_childrens  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
		    $min_babies  	= absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );

		    $adults 		= isset( $_POST['adults'] ) ? absint( sanitize_text_field( $_POST['adults'] ) ) : 0;
			$childrens 		= isset( $_POST['childrens'] ) ? absint( sanitize_text_field( $_POST['childrens'] ) ) : 0;
			$babies 		= isset( $_POST['babies'] ) ? absint( sanitize_text_field( $_POST['babies'] ) ) : 0;

			// Total number of guests
			$total_numberof_guests = $adults + $childrens + $babies;

			// Guests validation
		    $guests_mesg = ovabrw_guests_validation( $product_id, array(
		        'numberof_adults'   => $adults,
		        'numberof_children' => $childrens,
		        'numberof_babies'   => $babies
		    ));
		    if ( $guests_mesg ) {
		    	$data_total['error'] = $guests_mesg;
		    	echo json_encode($data_total);
		    	wp_die();
		    }

			// Total
			$data_total = array();
			$data_total['adults_price'] 	= '';
			$data_total['childrens_price'] 	= '';
			$data_total['babies_price'] 	= '';

			// Duration
			$duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

			if ( $duration && $time_from ) {
				$pickup_date .= ' ' . $time_from;
			}

			// Check-out
			if ( ! $dropoff_date ) {
				$dropoff_date = ovabrw_get_checkout_date( $product_id, strtotime( $pickup_date ) );
			}

			if ( ovabrw_qty_by_guests( $product_id ) ) {
				$data_total['qty_by_guests'] = true;
			} else {
				// Check product in order
				$store_quantity = ovabrw_quantity_available_in_order( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ) );

				// Check product in cart
				$cart_quantity = ovabrw_quantity_available_in_cart( $product_id, 'cart', strtotime( $pickup_date ), strtotime( $dropoff_date ) );

				// Get array quantity available
			    $data_quantity = ovabrw_get_quantity_available( $product_id, $store_quantity, $cart_quantity, $quantity, false, 'search' );

			    // Number quantity available
			    $data_total['quantity_available'] = isset( $data_quantity['quantity_available'] ) ? absint( $data_quantity['quantity_available'] ) : 0;

			    // Check Unavailable
			    $unavailable = ovabrw_check_unavailable( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ) );
			    if ( $unavailable ) {
			    	$data_total['quantity_available'] = 0;
			    }

			    if ( ! $data_total['quantity_available'] ) {
			    	$data_total['error'] = sprintf( __('%s isn\'t available for this time.<br>Please book other time.', 'ova-brw'), get_the_title( $product_id ) );

			    	echo json_encode($data_total);
			    	wp_die();
			    }
			}

			// Cart Item
			$cart_item['product_id'] 		= $product_id;
			$cart_item['ovabrw_adults']		= $adults;
			$cart_item['ovabrw_childrens']	= $childrens;
			$cart_item['ovabrw_babies']		= $babies;
			$cart_item['ovabrw_quantity'] 	= $quantity;
			$cart_item['custom_ckf'] 		= (array) json_decode( $custom_ckf );
			$cart_item['ovabrw_resources'] 	= (array)json_decode( $resources );
			$cart_item['ovabrw_services'] 	= (array)json_decode( $services );
			$cart_item['ova_type_deposit'] 	= $deposit;
			$cart_item['ovabrw_time_from'] 	= $time_from;

			// Price Per Guests
			$price_per_guests = ovabrw_price_per_guests( $product_id, strtotime( $pickup_date ), $adults, $childrens, $babies, $time_from );

			if ( ovabrw_check_array( $price_per_guests, 'adults_price' ) ) {
				$data_total['adults_price'] = ovabrw_wc_price( $price_per_guests['adults_price'] );
			}

			if ( ovabrw_check_array( $price_per_guests, 'childrens_price' ) ) {
				$data_total['childrens_price'] = ovabrw_wc_price( $price_per_guests['childrens_price'] );
			}

			if ( ovabrw_check_array( $price_per_guests, 'babies_price' ) ) {
				$data_total['babies_price'] = ovabrw_wc_price( $price_per_guests['babies_price'] );
			}

			// Insurance Amount
			$insurance_amount = floatval( get_post_meta( $product_id, 'ovabrw_amount_insurance', true ) );
			$insurance_amount = $insurance_amount*$total_numberof_guests*$quantity;

			// Line Total
			$line_total = get_price_by_guests( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ), $cart_item );

			$data_total['line_total'] = $line_total;

		    // Total amount
		    $total_amount = $line_total;
		    if ( $insurance_amount ) $total_amount += $insurance_amount;

		    // Deposit
		    $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );

		    if ( 'yes' === $deposit_enable ) {
		    	$deposit_value = floatval( get_post_meta ( $product_id, 'ovabrw_amount_deposit', true ) );
		    	$deposit_type  = get_post_meta( $product_id, 'ovabrw_type_deposit', true );

		    	if ( 'deposit' === $deposit ) {
		    		if ( $deposit_type === 'percent' ) {
		    			$line_total = ( $line_total * $deposit_value ) / 100;

		    			if ( $insurance_amount && !ovabrw_insurance_paid_once() ) {
			            	$insurance_amount = floatval( ( $insurance_amount * $deposit_value ) / 100 );
			            }
		    		} else {
		    			$line_total = $deposit_value;
		    		}
		    	}
		    }

		    // Insurance amount
            if ( $insurance_amount ) {
            	$line_total += $insurance_amount;

            	$insurance_html = sprintf( esc_html__( '(includes %s insurance)', 'ova-brw' ), ovabrw_wc_price( $insurance_amount ) );

            	$data_total['insurance_amount'] = apply_filters( 'ovabrw_ajax_insurance_html', $insurance_html, $product_id );
            }

		    $data_total = apply_filters( 'ovabrw_ft_ajax_data_total', $data_total, $product_id );

			if ( $line_total <= 0 ) {
				$data_total['line_total'] = ovabrw_wc_price( 0 );

				if ( apply_filters( 'ovabrw_ft_validate_total_add_to_cart', true ) ) {
					$data_total['error'] = esc_html__( 'The total must be greater than 0', 'ova-brw' );
				}
			} else {
				if ( 'deposit' === $deposit ) {
					$line_total = wp_kses_post( sprintf( __( '<span class="show_total">%s</span> <small>(of %s)</small>', 'ova-brw' ), ovabrw_wc_price( $line_total ), ovabrw_wc_price( $total_amount ) ) );
				} else {
					$line_total = ovabrw_wc_price( $line_total );
				}

				$data_total['line_total'] = apply_filters( 'ovabrw_ajax_total_filter', $line_total, $product_id );
			}

			echo json_encode($data_total);

			wp_die();
		}

		/**
		 * Show Time
		 */
		public function ovabrw_show_time() {
			$product_id 	= isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
			$pickup_date 	= isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';

			if ( ! $product_id || ! strtotime( $pickup_date ) ) wp_die();

			$result 	= array();
			$check_in 	= strtotime( $pickup_date );
			$dateformat = ovabrw_get_date_format();
			$timeformat = ovabrw_get_time_format();
			$datetime_format = $dateformat . ' ' . $timeformat;

			$duration 		= get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
			$number_days 	= apply_filters( 'ovabrw_ft_get_number_day', get_post_meta( $product_id, 'ovabrw_number_days', true ) );
			$number_hours 	= get_post_meta( $product_id, 'ovabrw_number_hours', true );

			if ( ! $number_days ) $number_days = 0;
			if ( ! $number_hours ) $number_hours = 0;

			if ( $duration ) {
				$duration_time = ovabrw_get_duration_time( $product_id, strtotime( $pickup_date ) );

				if ( ! empty( $duration_time ) && is_array( $duration_time ) ) {
					$result['durration'] = ovabrw_get_html_duration( $duration_time );

					$check_in 	= strtotime( $pickup_date . ' ' . $duration_time[0] );
					$check_out 	= apply_filters( 'ovabrw_calculate_checkout_by_hours', $check_in + floatval( $number_hours )*60*60, $check_in, $number_hours );

					$result['checkout'] = date_i18n( $datetime_format, $check_out );
				} else {
					$result['error'] = esc_html__( 'No time, please choose another date!', 'ova-brw' );
					echo json_encode( apply_filters( 'ovabrw_ft_show_time', $result, $product_id, $pickup_date ) );
					wp_die();
				}
			} else {
				if ( $check_in < current_time( 'timestamp' ) ) {
			    	$result['error'] = sprintf( __('%s isn\'t available for this time.<br>Please book other time.', 'ova-brw'), get_the_title( $product_id ) );
			    	echo json_encode( apply_filters( 'ovabrw_ft_show_time', $result, $product_id, $pickup_date ) );
						wp_die();
			    }

				if ( $check_in ) {
					$check_out = apply_filters( 'ovabrw_calculate_checkout_by_days', $check_in + absint( $number_days )*24*60*60, $check_in, $number_days );

					$result['checkout'] = date_i18n( $dateformat, $check_out );
				}
			}

			if ( ovabrw_qty_by_guests( $product_id ) && $check_in && $check_out ) {
				$result['qty_by_guests'] = true;

				$adults 	= isset( $_POST['adults'] ) ? absint( sanitize_text_field( $_POST['adults'] ) ) : 0;
				$children 	= isset( $_POST['children'] ) ? absint( sanitize_text_field( $_POST['children'] ) ) : 0;
				$babies 	= isset( $_POST['babies'] ) ? absint( sanitize_text_field( $_POST['babies'] ) ) : 0;
				$quantity 	= isset( $_POST['quantity'] ) ? absint( sanitize_text_field( $_POST['quantity'] ) ) : 1;

				$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
			    $min_children  	= absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
			    $min_babies  	= absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );

				$guests = [
		            'adults'     => $adults * $quantity,
		            'children'   => $children * $quantity,
		            'babies'     => $babies * $quantity
		        ];

		        $guests_available = ovabrw_validate_guests_available( $product_id, $check_in, $check_out, $guests, 'search' );

		        if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
		        	// Adults
		        	if ( ! $guests_available['adults'] || $guests_available['adults'] < 0 ) {
		        		$result['max_adults'] = 0;
		        		$result['min_adults'] = 0;
		        		$result['val_adults'] = 0;
		        	} elseif ( $guests_available['adults'] <= $min_adults ) {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $guests_available['adults'];
		        		$result['val_adults'] = $guests_available['adults'];
		        	} else if ( $guests_available['adults'] <= $adults ) {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $min_adults;
		        		$result['val_adults'] = $guests_available['adults'];
		        	} else {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $min_adults;
		        		$result['val_adults'] = $result['max_adults'] >= 1 ? 1 : $min_adults;
		        	}

		        	// Children
		        	if ( ! $guests_available['children'] || $guests_available['children'] < 0 ) {
		        		$result['max_children'] = 0;
		        		$result['min_children'] = 0;
		        		$result['val_children'] = 0;
		        	} elseif ( $guests_available['children'] <= $min_children ) {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $guests_available['children'];
		        		$result['val_children'] = $guests_available['children'];
		        	} else if ( $guests_available['children'] <= $children ) {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $min_children;
		        		$result['val_children'] = $guests_available['children'];
		        	} else {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $min_children;
		        		$result['val_children'] = $result['max_children'] >= 1 ? 1 : $min_children;
		        	}

		        	// Babies
		        	if ( ! $guests_available['babies'] || $guests_available['babies'] < 0 ) {
		        		$result['max_babies'] = 0;
		        		$result['min_babies'] = 0;
		        		$result['val_babies'] = 0;
		        	} elseif ( $guests_available['babies'] < $min_babies ) {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $guests_available['babies'];
		        		$result['val_babies'] = $guests_available['babies'];
		        	} elseif ( $guests_available['babies'] < $babies ) {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $min_babies;
		        		$result['val_babies'] = $guests_available['babies'];
		        	} else {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $min_babies;
		        		$result['val_babies'] = $result['max_babies'] >= 1 ? 1 : $min_babies;
		        	}
		        } else {
		            $result['error'] = sprintf( __('%s isn\'t available for this time.<br>Please book other time.', 'ova-brw'), get_the_title( $product_id ) );
		            echo json_encode( apply_filters( 'ovabrw_ft_show_time', $result, $product_id, $pickup_date ) );
					wp_die();
		        }
			}

			echo json_encode( apply_filters( 'ovabrw_ft_show_time', $result, $product_id, $pickup_date ) );

			wp_die();
		}

		/**
		 * Choose Time
		 */
		public function ovabrw_choose_time() {
			$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
			$check_in 	= isset( $_POST['check_in'] ) ? sanitize_text_field( $_POST['check_in'] ) : '';
			$check_out 	= isset( $_POST['check_out'] ) ? sanitize_text_field( $_POST['check_out'] ) : '';

			if ( ! $product_id || ! ovabrw_qty_by_guests( $product_id ) ) wp_die();

			$result = array();

			$adults 	= isset( $_POST['adults'] ) ? absint( sanitize_text_field( $_POST['adults'] ) ) : 0;
			$children 	= isset( $_POST['children'] ) ? absint( sanitize_text_field( $_POST['children'] ) ) : 0;
			$babies 	= isset( $_POST['babies'] ) ? absint( sanitize_text_field( $_POST['babies'] ) ) : 0;
			$quantity 	= isset( $_POST['quantity'] ) ? absint( sanitize_text_field( $_POST['quantity'] ) ) : 1;

			$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
		    $min_children  	= absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
		    $min_babies  	= absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );

			$guests = [
		        'adults'     => $adults * $quantity,
		        'children'   => $children * $quantity,
		        'babies'     => $babies * $quantity
		    ];

		    $guests_available = ovabrw_validate_guests_available( $product_id, strtotime( $check_in ), strtotime( $check_out ), $guests, 'search' );

		    if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
		    	// Adults
		    	if ( ! $guests_available['adults'] || $guests_available['adults'] < 0 ) {
		    		$result['max_adults'] = 0;
		    		$result['min_adults'] = 0;
		    		$result['val_adults'] = 0;
		    	} elseif ( $guests_available['adults'] <= $min_adults ) {
		    		$result['max_adults'] = $guests_available['adults'];
		    		$result['min_adults'] = $guests_available['adults'];
		    		$result['val_adults'] = $guests_available['adults'];
		    	} else if ( $guests_available['adults'] <= $adults ) {
		    		$result['max_adults'] = $guests_available['adults'];
		    		$result['min_adults'] = $min_adults;
		    		$result['val_adults'] = $guests_available['adults'];
		    	} else {
		    		$result['max_adults'] = $guests_available['adults'];
		    		$result['min_adults'] = $min_adults;
		    		$result['val_adults'] = $result['max_adults'] >= 1 ? 1 : $min_adults;
		    	}

		    	// Children
		    	if ( ! $guests_available['children'] || $guests_available['children'] < 0 ) {
		    		$result['max_children'] = 0;
		    		$result['min_children'] = 0;
		    		$result['val_children'] = 0;
		    	} elseif ( $guests_available['children'] <= $min_children ) {
		    		$result['max_children'] = $guests_available['children'];
		    		$result['min_children'] = $guests_available['children'];
		    		$result['val_children'] = $guests_available['children'];
		    	} else if ( $guests_available['children'] <= $children ) {
		    		$result['max_children'] = $guests_available['children'];
		    		$result['min_children'] = $min_children;
		    		$result['val_children'] = $guests_available['children'];
		    	} else {
		    		$result['max_children'] = $guests_available['children'];
		    		$result['min_children'] = $min_children;
		    		$result['val_children'] = $result['max_children'] >= 1 ? 1 : $min_children;
		    	}

		    	// Babies
		    	if ( ! $guests_available['babies'] || $guests_available['babies'] < 0 ) {
		    		$result['max_babies'] = 0;
		    		$result['min_babies'] = 0;
		    		$result['val_babies'] = 0;
		    	} elseif ( $guests_available['babies'] < $min_babies ) {
		    		$result['max_babies'] = $guests_available['babies'];
		    		$result['min_babies'] = $guests_available['babies'];
		    		$result['val_babies'] = $guests_available['babies'];
		    	} elseif ( $guests_available['babies'] < $babies ) {
		    		$result['max_babies'] = $guests_available['babies'];
		    		$result['min_babies'] = $min_babies;
		    		$result['val_babies'] = $guests_available['babies'];
		    	} else {
		    		$result['max_babies'] = $guests_available['babies'];
		    		$result['min_babies'] = $min_babies;
		    		$result['val_babies'] = $result['max_babies'] >= 1 ? 1 : $min_babies;
		    	}
		    } else {
		        $result['error'] = sprintf( __('%s isn\'t available for this time.<br>Please book other time.', 'ova-brw'), get_the_title( $product_id ) );
		        echo json_encode( apply_filters( 'ovabrw_ft_show_time', $result, $product_id, $check_in, $check_out ) );
				wp_die();
		    }

		    echo json_encode( apply_filters( 'ovabrw_ft_choose_time', $result, $product_id, $check_in, $check_out ) );

			wp_die();
		}

		/**
		 * Change Duration
		 */
		public function ovabrw_duration_change() {
			$product_id 	= isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
			$pickup_date 	= isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
			$time 			= isset( $_POST['time'] ) ? sanitize_text_field( $_POST['time'] ) : '';

			if ( ! $product_id || ! strtotime( $pickup_date ) || ! $time ) wp_die();

			$result 	= array();
			$check_in 	= strtotime( $pickup_date );
			$dateformat = ovabrw_get_date_format();
			$timeformat = ovabrw_get_time_format();
			$datetime_format 	= $dateformat . ' ' . $timeformat;
			$number_hours 		= get_post_meta( $product_id, 'ovabrw_number_hours', true );

			if ( ! $number_hours ) $number_hours = 0;

			$check_in = strtotime( $pickup_date . ' ' . $time );

			if ( $check_in ) {
				$check_out = $check_in + floatval( $number_hours )*60*60;

				$result['checkout'] = date_i18n( $datetime_format, $check_out );
			} else {
				$result['error'] = esc_html__( 'Something is wrong!', 'ova-brw' );
			}

			if ( ovabrw_qty_by_guests( $product_id ) && $check_in && $check_out ) {
				$result['qty_by_guests'] = true;

				$adults 	= isset( $_POST['adults'] ) ? absint( sanitize_text_field( $_POST['adults'] ) ) : 0;
				$children 	= isset( $_POST['children'] ) ? absint( sanitize_text_field( $_POST['children'] ) ) : 0;
				$babies 	= isset( $_POST['babies'] ) ? absint( sanitize_text_field( $_POST['babies'] ) ) : 0;
				$quantity 	= isset( $_POST['quantity'] ) ? absint( sanitize_text_field( $_POST['quantity'] ) ) : 1;

				$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
			    $min_children  	= absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
			    $min_babies  	= absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );

				$guests = [
		            'adults'     => $adults * $quantity,
		            'children'   => $children * $quantity,
		            'babies'     => $babies * $quantity
		        ];

		        $guests_available = ovabrw_validate_guests_available( $product_id, $check_in, $check_out, $guests, 'search' );

		        if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
		        	// Adults
		        	if ( ! $guests_available['adults'] || $guests_available['adults'] < 0 ) {
		        		$result['max_adults'] = 0;
		        		$result['min_adults'] = 0;
		        		$result['val_adults'] = 0;
		        	} elseif ( $guests_available['adults'] <= $min_adults ) {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $guests_available['adults'];
		        		$result['val_adults'] = $guests_available['adults'];
		        	} else if ( $guests_available['adults'] <= $adults ) {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $min_adults;
		        		$result['val_adults'] = $guests_available['adults'];
		        	} else {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $min_adults;
		        		$result['val_adults'] = $result['max_adults'] >= 1 ? 1 : $min_adults;
		        	}

		        	// Children
		        	if ( ! $guests_available['children'] || $guests_available['children'] < 0 ) {
		        		$result['max_children'] = 0;
		        		$result['min_children'] = 0;
		        		$result['val_children'] = 0;
		        	} elseif ( $guests_available['children'] <= $min_children ) {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $guests_available['children'];
		        		$result['val_children'] = $guests_available['children'];
		        	} else if ( $guests_available['children'] <= $children ) {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $min_children;
		        		$result['val_children'] = $guests_available['children'];
		        	} else {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $min_children;
		        		$result['val_children'] = $result['max_children'] >= 1 ? 1 : $min_children;
		        	}

		        	// Babies
		        	if ( ! $guests_available['babies'] || $guests_available['babies'] < 0 ) {
		        		$result['max_babies'] = 0;
		        		$result['min_babies'] = 0;
		        		$result['val_babies'] = 0;
		        	} elseif ( $guests_available['babies'] < $min_babies ) {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $guests_available['babies'];
		        		$result['val_babies'] = $guests_available['babies'];
		        	} elseif ( $guests_available['babies'] < $babies ) {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $min_babies;
		        		$result['val_babies'] = $guests_available['babies'];
		        	} else {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $min_babies;
		        		$result['val_babies'] = $result['max_babies'] >= 1 ? 1 : $min_babies;
		        	}
		        } else {
		            $result['error'] = sprintf( __('%s isn\'t available for this time.<br>Please book other time.', 'ova-brw'), get_the_title( $product_id ) );
		            echo json_encode( apply_filters( 'ovabrw_duration_change', $result, $product_id, $pickup_date ) );
					wp_die();
		        }
			}

			echo json_encode( apply_filters( 'ovabrw_duration_change', $result, $product_id, $pickup_date ) );

			wp_die();
		}

		/**
		 * Fixed Date Loading
		 */
		public function ovabrw_loading_fixed_dates() {
			$product_id = isset( $_POST['product_id'] ) ? (int)$_POST['product_id'] : '';
			$next 		= isset( $_POST['next'] ) ? (int)$_POST['next'] : '';

			if ( $product_id && $next ) {
				$fixed_date = ovabrw_get_fixed_dates( $product_id, $next );

				if ( !empty( $fixed_date ) && is_array( $fixed_date ) ) {
					ob_start();

					foreach ( $fixed_date as $date_range => $date_string ) { ?>
						<option value="<?php echo esc_attr( $date_range ); ?>"<?php selected( $date_range, array_key_first( $fixed_date ) ); ?>>
		                    <?php echo esc_html( $date_string ); ?>
		                </option>
					<?php }

					$options = ob_get_contents();
					ob_end_clean();

					echo apply_filters( 'ovabrw_ajax_loading_fixed_dates', $options, $_POST );
				}
			}

			wp_die();
		}

		/**
		 * Check maximum number of guests
		 */
		public function ovabrw_check_max_guests() {
			$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';

			if ( ! $product_id || ! ovabrw_qty_by_guests( $product_id ) ) wp_die();

			$result = array();

			$adults 	= isset( $_POST['adults'] ) ? absint( sanitize_text_field( $_POST['adults'] ) ) : 0;
			$children 	= isset( $_POST['children'] ) ? absint( sanitize_text_field( $_POST['children'] ) ) : 0;
			$babies 	= isset( $_POST['babies'] ) ? absint( sanitize_text_field( $_POST['babies'] ) ) : 0;

			$max_guest = get_post_meta( $product_id, 'ovabrw_max_total_guest', true );

			if ( $max_guest && $max_guest < ( $adults + $children + $babies ) ) {
				$result['error'] = sprintf( __('Maximum total number of guests: %s', 'ova-brw'), $max_guest );

		    	echo json_encode($result);
		    	wp_die();
			}

			wp_die();
		}

		/**
		 * Remove Cart
		 */
		public function ovabrw_remove_cart() {
			$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( $_POST['cart_item_key'] ) : '';

			if ( $cart_item_key && false !== WC()->cart->remove_cart_item( $cart_item_key ) ) {
				$count = WC()->cart->get_cart_contents_count();

				echo absint( $count );
			} else {
				echo '';
			}

			wp_die();
		}

		/**
		 * Verify reCAPTCHA
		 */
		public function ovabrw_verify_reCAPTCHA() {
			// Get error
			$error = ovabrw_get_recaptcha_error();

			if ( ! isset( $_POST ) && empty( $_POST ) ) {
				echo esc_html( $error );
				wp_die();
			}

			$token 	= isset( $_POST['token'] ) ? sanitize_text_field( $_POST['token'] ) : '';
			$action = isset( $_POST['form'] ) ? sanitize_text_field( $_POST['form'] ) : '';
			$score 	= apply_filters( 'ovabrw_recaptcha_score', 0.5 );

			if ( ! $token ) {
				echo esc_html( $error );
				wp_die();
			} else {
				// Params
				$params = [
					'secret'   => ovabrw_get_recaptcha_secret_key(),
					'response' => $token,
					'remoteip' => ovabrw_get_client_ip()
				];

				// Options
				$opts = [
					'http' => [
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => http_build_query( $params )
					]
				];

				$context = stream_context_create( $opts );
				$res     = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify', false, $context );
				$res     = json_decode( $res, true );

				if ( $res['success'] && $res['hostname'] == ovabrw_get_recaptcha_host() ) {
					if ( 'v3' === ovabrw_get_recaptcha_type() && $res['action'] == $action && $res['score'] > $score ) {
						$error = '';
					} elseif ( 'v2' === ovabrw_get_recaptcha_type() ) {
						$error = '';
					}
				} else {
				    if ( isset( $res['error-codes'][0] ) && $res['error-codes'][0] ) {
				        $error = ovabrw_get_recaptcha_error( $res['error-codes'][0] );
				    }
				}
			}

			echo esc_html( $error );

			wp_die();
		}

		/**
		 * Search Ajax
		 */
		public function ovabrw_search_ajax() {
			$data = $_POST;
		    
		    $layout        	 	= isset( $data['layout'] )         		? sanitize_text_field( $data['layout'] )         	: 'grid';
		    $grid_column        = isset( $data['grid_column'] )         ? sanitize_text_field( $data['grid_column'] )       : 'column4';
		    $thumbnail_type     = isset( $data['thumbnail_type'] )      ? sanitize_text_field( $data['thumbnail_type'] )    : 'image';
			$order 				= isset( $data['order'] ) 		   		? sanitize_text_field( $data['order'] ) 		    : 'DESC';
			$orderby 			= isset( $data['orderby'] ) 	   		? sanitize_text_field( $data['orderby'] ) 			: 'ID';
			$orderby_meta_key 	= isset( $data['orderby_meta_key'] ) 	? sanitize_text_field( $data['orderby_meta_key'] ) 	: '';
			$posts_per_page 	= isset( $data['posts_per_page'] ) 		? sanitize_text_field( $data['posts_per_page'] )	: '4';
			$default_category 	= isset( $data['default_category'] ) 	? $data['default_category']							: [];
			$show_category 		= isset( $data['show_category'] ) 		? sanitize_text_field( $data['show_category'] )		: '';
			$paged 				= isset( $data['paged'] ) 		   		? (int)$data['paged']  								:  1;
		    $destination    	= isset( $data['destination'] )    		? sanitize_text_field( $data['destination'] )		: 'all';
		    $custom_taxonomy    = isset( $data['custom_taxonomy'] )     ? $data['custom_taxonomy'] 							: array();
		    $taxonomy_value    	= isset( $data['taxonomy_value'] )     	? $data['taxonomy_value'] 							: array();
		    $pickup_date 		= isset( $data['start_date'] )     		? strtotime( $data['start_date'] ) 					: '';
		    $adults 			= isset( $data['adults'] ) 		   		? sanitize_text_field( $data['adults'] )		    : '';
		    $childrens 			= isset( $data['childrens'] ) 	   		? sanitize_text_field( $data['childrens'] )			: '';
		    $babies 			= isset( $data['babies'] ) 	   			? sanitize_text_field( $data['babies'] )			: '';
		    $start_price    	= isset( $data['start_price'] )    		? (int)$data['start_price']  						:  0;
		    $end_price      	= isset( $data['end_price'] )      		? (int)$data['end_price']  					    	:  '';
		    $review_score 		= isset( $data['review_score'] )   		? $data['review_score']   							: array();
		    $categories 		= isset( $data['categories'] )     		? $data['categories']   							: array();
		    $duration_from 		= isset( $data['duration_from'] )     	? $data['duration_from']   							: '0';
		    $duration_to 		= isset( $data['duration_to'] )     	? $data['duration_to']   							: '';
		    $duration_type 		= isset( $data['duration_type'] )     	? $data['duration_type']   							: '';
		    $clicked 			= isset( $data['clicked'] ) 			? $data['clicked'] 									: '';
		    $list_taxonomy      = ovabrw_create_type_taxonomies();
		    
		    // Base Query
		    $args_base = array(
				'post_type'      	=> 'product',
				'post_status'    	=> 'publish',
				'posts_per_page' 	=> $posts_per_page,
				'paged' 			=> $paged,
				'order' 			=> $order,
				'orderby' 			=> $orderby,
				'meta_key'          => $orderby_meta_key,
				'tax_query' => array(
		            array(
		                'taxonomy' => 'product_type',
		                'field'    => 'slug',
		                'terms'    => 'ovabrw_car_rental', 
		            ),
		        ),
			);

			switch ( $orderby ) {
				case 'featured':
					$args_base['orderby'] = 'ID';

					array_push( $args_base['tax_query'], array(
						'taxonomy' => 'product_visibility',
			            'field'    => 'name',
			            'terms'    => 'featured',
			            'operator' => 'IN'
					));
					break;
				case 'popularity':
					$args_base['meta_key'] 	= 'total_sales';
					$args_base['orderby'] 	= 'meta_value_num';
					break;
				case 'rating':
					$args_base['meta_key'] 	= '_wc_average_rating';
					$args_base['orderby'] 	= 'meta_value_num';
					break;
				case 'price':
					$args_base['meta_key'] 	= '_price';
					$args_base['orderby'] 	= 'meta_value_num';
					break;
			}

			$data_taxonomy = [];

			if ( ! empty( $custom_taxonomy ) && is_array( $custom_taxonomy ) ) {
				foreach( $custom_taxonomy as $k => $slug ) {
					if ( $slug && isset( $taxonomy_value[$k] ) && $taxonomy_value[$k] ) {
						$data_taxonomy[str_replace( '_name', '', $slug)] = $taxonomy_value[$k];
					}
				}
			}
		    
		    // Tax Query custom taxonomy
		    $arg_taxonomy_arr = [];

		    if ( ! empty( $list_taxonomy ) ) {
		        foreach( $list_taxonomy as $taxonomy ) {
		        	$slug = $taxonomy['slug'];
		            $taxonomy_get = isset( $data_taxonomy[$slug] ) ? sanitize_text_field( $data_taxonomy[$slug] ) : 'all';

		            if ( $taxonomy_get && $taxonomy_get != 'all' ) {
		                $arg_taxonomy_arr[] = array(
		                    'taxonomy' => $taxonomy['slug'],
		                    'field'    => 'slug',
		                    'terms'    => $taxonomy_get
		                );
		            }
		        }
		    }
		    
		    $args_meta_query_arr = $args_cus_meta_custom = $args_cus_tax_custom = array();

		    if (  $args_base ) {

		    	if ( $destination != 'all' ) {
			        $args_meta_query_arr[] = [
			            'key'     => 'ovabrw_destination',
			            'value'   => $destination,
			            'compare' => 'LIKE',
			        ];
			    }

			    if ( $review_score != [] ) {
			        $args_meta_query_arr[] = [
			            'key'     => '_wc_average_rating',
			            'value'   => $review_score,
			            'type'    => 'numeric',
			            'compare' => 'IN',
			        ];
			    }

			    if ( $adults != '' ) {
			    	$args_meta_query_arr[] = [
			            'key'     => 'ovabrw_adults_max',
			            'value'   => $adults,
			            'type'    => 'numeric',
			            'compare' => '>=',
			        ];
			    }

			    if ( $childrens != '' ) {
			    	$args_meta_query_arr[] = [
			            'key'     => 'ovabrw_childrens_max',
			            'value'   => $childrens,
			            'type'    => 'numeric',
			            'compare' => '>=',
			        ];
			    }

			    if ( $babies != '' ) {
			    	$args_meta_query_arr[] = [
			            'key'     => 'ovabrw_babies_max',
			            'value'   => $babies,
			            'type'    => 'numeric',
			            'compare' => '>=',
			        ];
			    }

			    if ( $end_price != '' ) {
			        $args_meta_query_arr[] = [
			            'key'     => '_price',
			            'value'   => array($start_price,$end_price),
			            'type'    => 'numeric',
			            'compare' => 'BETWEEN',
			        ];
			    }

		    	if ( $categories != [] ) {
			        $arg_taxonomy_arr[] = [
			            'taxonomy' => 'product_cat',
			            'field'    => 'slug',
			            'terms'    => $categories
			        ];
			    } else {
			    	if ( ! empty( $default_category ) && is_array( $default_category ) && $show_category != 'yes' ) {
			    		$arg_taxonomy_arr[] = [
				            'taxonomy' => 'product_cat',
				            'field'    => 'slug',
				            'terms'    => $default_category
				        ];
			    	}
			    }
			    	
		        // Duration check
		    	if ( $duration_to == '' ) {
			        $duration_to = '9999';
		    	} 

		    	if ( $duration_type == 'day') {
		    		$args_meta_query_arr[] = [
			            'key'     => 'ovabrw_duration_checkbox',
			            'value'   => 1,
			            'type'    => 'numeric',
			            'compare' => '!=',
			        ];
		    		
		    		$args_meta_query_arr[] = [
			            'key'     => 'ovabrw_number_days',
			            'value'   => array( $duration_from, $duration_to ),
			            'type'    => 'numeric',
			            'compare' => 'BETWEEN',
			        ];
		    	}

		    	if ( $duration_type == 'hour') {
		    		$args_meta_query_arr[] = [
			            'key'     => 'ovabrw_duration_checkbox',
			            'value'   => 1,
			            'type'    => 'numeric',
			            'compare' => '=',
			        ];
				    
		    		$args_meta_query_arr[] = [
			            'key'     => 'ovabrw_number_hours',
			            'value'   => array( $duration_from, $duration_to ),
			            'type'    => 'numeric',
			            'compare' => 'BETWEEN',
			        ];	
		    	}

		    	if ( ! empty( $arg_taxonomy_arr ) ) {
			        $args_cus_tax_custom = array(
			            'tax_query' => array(
			                'relation'  => 'AND',
			                $arg_taxonomy_arr
			            )
			        );
			    }

			    if ( ! empty( $args_meta_query_arr ) ) {
			        $args_cus_meta_custom = array(
			            'meta_query' => array(
			                'relation'  => 'AND',
			                $args_meta_query_arr,
			            )
			        );
			    }

			    if ( ! empty( $pickup_date ) ) {
			    	$exclude_ids  = ovabrw_get_exclude_ids( $pickup_date );
			        $args_base['post__not_in'] = $exclude_ids;
			    }

		        $args = array_merge_recursive( $args_base, $args_cus_tax_custom, $args_cus_meta_custom );

		        $products = new WP_Query( apply_filters( 'ovabrw_ft_query_search_ajax', $args, $data ));

		        $number_results_found = $products->found_posts;

		        if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
		        	add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );
		        }

		        ob_start(); ?>
		        <div class="ovabrw-products-result ovabrw-products-result-<?php echo esc_attr( $layout );?> <?php echo esc_attr( $grid_column );?>" data-clicked="<?php echo esc_attr( $clicked ); ?>">

					<?php
						if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
							if ( $thumbnail_type === 'gallery' ) {
								add_filter( 'ovabrw_ft_product_list_card_gallery', '__return_true' );
							} else {
								add_filter( 'ovabrw_ft_product_list_card_gallery', '__return_false' );
							}

							if( $layout == 'grid' ) {
								wc_get_template_part( 'content', 'product' );
							} elseif( $layout == 'list' ) {
		                        wc_get_template_part( 'rental/content-item', 'product-list' );
							}
						endwhile; else :
						?>
							<div class="not_found_product">
								<h3 class="empty-list">
									<?php esc_html_e( 'Not available tours', 'ova-brw' ); ?>
								</h3>
								<p>
									<?php esc_html_e( 'It seems we can’t find what you’re looking for.', 'ova-brw' ); ?>
								</p>
							</div>
					<?php endif; wp_reset_postdata(); ?>
		            <input
		            	type="hidden"
		            	class="tour_number_results_found"
		            	name="tour_number_results_found"
		            	value="<?php echo esc_attr( $number_results_found ); ?>"
		            />
				</div>
		        <?php

		        $total = $products->max_num_pages;

				if (  $total > 1 ): ?>
					<div class="ovabrw-pagination-ajax" data-paged="<?php echo esc_attr( $paged ); ?>">
					<?php
						echo ovabrw_pagination_ajax( $number_results_found , $products->query_vars['posts_per_page'], $paged );
					?>
					</div>
					<?php
				endif;

				$result = ob_get_contents(); 
				ob_end_clean();

				echo json_encode( array( "result" => $result ));
				wp_die();
		    } else {
		    	echo json_encode( array( "result" => $result ));
		    	wp_die();
		    }
		}

		/**
		 * Filter Product
		 */
		public function ovabrw_load_product_filter() {
			$term      		 = isset( $_POST['term'] ) 				? sanitize_text_field( $_POST['term'] ) 		  : 'all';
			$show_on_sale    = isset( $_POST['show_on_sale'] ) 		? sanitize_text_field($_POST['show_on_sale'])	  : 'no';
			$args_show    	 = isset( $_POST['args_show'] ) 		? $_POST['args_show']	  					  : [];
			$posts_per_page  = isset( $_POST['posts_per_page'] ) 	? (int)$_POST['posts_per_page']			  		  : 4 ;
			$orderby      	 = isset( $_POST['orderby'] ) 			? sanitize_text_field( $_POST['orderby'] ) 		  : 'ID';
			$order      	 = isset( $_POST['order'] ) 			? sanitize_text_field( $_POST['order'] ) 		  : 'DESC';
			
			if ( $term != "all" ) {
				$args = array(
				    'post_type'   => 'product',
				    'posts_per_page' => $posts_per_page,
				    'post_status' => 'publish',
				    'orderby'	 => $orderby,
				    'order' 	 => $order,
				    'fields'    => 'ids',
				    'tax_query' => array(
				    	'relation' => 'AND',
				        array(
				            'taxonomy' => 'product_cat',
				            'field'    => 'slug',
				            'terms'    => $term, 
				        ),
				        array(
			                'taxonomy' => 'product_type',
			                'field'    => 'slug',
			                'terms'    => 'ovabrw_car_rental', 
			            ),
				    ),
				);
			} else {
				$args = array(
				    'post_type'   => 'product',
				    'posts_per_page' => $posts_per_page,
				    'post_status' => 'publish',
				    'orderby' => $orderby,
				    'order'	  => $order,
				    'fields'    => 'ids',
				    'tax_query' => array(
				        array(
			                'taxonomy' => 'product_type',
			                'field'    => 'slug',
			                'terms'    => 'ovabrw_car_rental', 
			            ),
				    ),
				);
			}

			if ( 'yes' === $show_on_sale ) {
		        $product_ids_on_sale = wc_get_product_ids_on_sale();
		        $args['post__in'] = $product_ids_on_sale;
		    }

			$list_product = get_posts( $args );

			$results = '';

			ob_start();


			if( !empty($list_product) && is_array($list_product) ) : foreach($list_product as $pid) :

				ovabrw_get_template('elementor/ovabrw_product_filter_ajax.php', array('id' => $pid, 'args_show' => $args_show));

			endforeach; endif; wp_reset_postdata(); 

			$results = ob_get_contents();
			ob_end_clean();

			echo $results;

			wp_die();
		}

		/**
		 * Filter Product by Category
		 */
		public function ovabrw_product_category_ajax() {
			$data = $_POST;

			if ( ! $data ) wp_die();

			$term_id 		= isset( $data['term_id'] ) ? sanitize_text_field( $data['term_id'] ) : '';
			$posts_per_page = isset( $data['posts_per_page'] ) ? sanitize_text_field( $data['posts_per_page'] ) : 9;
			$paged 			= isset( $data['paged'] ) ? sanitize_text_field( $data['paged'] ) : 1;
			$order 			= isset( $data['order'] ) ? sanitize_text_field( $data['order'] ) : 'DESC';
			$orderby 		= isset( $data['orderby'] ) ? sanitize_text_field( $data['orderby'] ) : 'date';
			$layout 		= isset( $data['layout'] ) ? sanitize_text_field( $data['layout'] ) : 'grid';
			$grid_template 	= isset( $data['grid_template'] ) ? sanitize_text_field( $data['grid_template'] ) : 'template_1';
			$column 		= isset( $data['column'] ) ? sanitize_text_field( $data['column'] ) : 'column3';
			$thumbnail_type = isset( $data['thumbnail_type'] ) ? sanitize_text_field( $data['thumbnail_type'] ) : 'image';
			$pagination 	= isset( $data['pagination'] ) ? sanitize_text_field( $data['pagination'] ) : 'yes';

			if ( ! $paged ) $paged = 1;

			$args_query = array(
				'post_type'      	=> 'product',
				'post_status'    	=> 'publish',
				'posts_per_page' 	=> $posts_per_page,
				'paged' 			=> $paged,
				'order' 			=> $order,
				'orderby' 			=> $orderby,
				'tax_query' => array(
					'relation'  => 'AND',
			        array(
			            'taxonomy' => 'product_type',
			            'field'    => 'slug',
			            'terms'    => 'ovabrw_car_rental'
			        )
			    ),
			);

			if ( $term_id ) {
				$args_query['tax_query'][] = array(
			        'taxonomy' => 'product_cat',
			        'field'    => 'term_id',
			        'terms'    => $term_id
			    );
			}

			$products = new WP_Query( apply_filters( 'ovabrw_ft_query_product_category_ajax', $args_query, $data ) );

			ob_start(); ?>
			<div class="ovabrw-products-result ovabrw-products-result-<?php echo esc_attr( $layout );?> grid-layout-<?php echo esc_attr( $grid_template );?> <?php echo esc_attr( $column ); ?>">
				<?php
					if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
						if ( $thumbnail_type === 'gallery' ) {
							add_filter( 'ovabrw_ft_product_list_card_gallery', '__return_true' );
						} else {
							add_filter( 'ovabrw_ft_product_list_card_gallery', '__return_false' );
						}

						if ( $layout == 'grid' ) {
							wc_get_template_part( 'content', 'product' );
						} elseif ( $layout == 'list' ) {
		                    wc_get_template_part( 'rental/content-item', 'product-list' );
						}
					endwhile; else :
					?>
						<div class="not_found_product">
							<h3 class="empty-list">
								<?php esc_html_e( 'Not available tours', 'ova-brw' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'It seems we can’t find what you’re looking for.', 'ova-brw' ); ?>
							</p>
						</div>
					<?php
					endif; wp_reset_postdata();
				?>
			</div>
			<?php if ( $pagination ):
				$total 			= $products->max_num_pages;
				$found_posts 	= $products->found_posts;

				if ( $total > 1 ):
			?>
					<div class="ovabrw-pagination-ajax" data-paged="<?php echo esc_attr( $paged ); ?>">
						<?php
							echo ovabrw_pagination_ajax( $found_posts, $products->query_vars['posts_per_page'], $paged );
						?>
					</div>
			<?php endif; endif;

			$result = ob_get_contents(); 
			ob_end_clean();

			echo json_encode( array( "result" => $result ));

			wp_die();
		}
		
		/**
		 * Filter Product by Destination
		 */
		public function ovabrw_product_destination_ajax() {
			$data = $_POST;

			if ( ! $data ) wp_die();

			$destination_id = isset( $data['destination_id'] ) ? sanitize_text_field( $data['destination_id'] ) : '';
			$posts_per_page = isset( $data['posts_per_page'] ) ? sanitize_text_field( $data['posts_per_page'] ) : 9;
			$paged 			= isset( $data['paged'] ) ? sanitize_text_field( $data['paged'] ) : 1;
			$order 			= isset( $data['order'] ) ? sanitize_text_field( $data['order'] ) : 'DESC';
			$orderby 		= isset( $data['orderby'] ) ? sanitize_text_field( $data['orderby'] ) : 'date';
			$layout 		= isset( $data['layout'] ) ? sanitize_text_field( $data['layout'] ) : 'grid';
			$column 		= isset( $data['column'] ) ? sanitize_text_field( $data['column'] ) : 'column3';
			$thumbnail_type = isset( $data['thumbnail_type'] ) ? sanitize_text_field( $data['thumbnail_type'] ) : 'image';
			$pagination 	= isset( $data['pagination'] ) ? sanitize_text_field( $data['pagination'] ) : 'yes';

			if ( ! $paged ) $paged = 1;

			$args_query = array(
				'post_type'      	=> 'product',
				'post_status'    	=> 'publish',
				'posts_per_page' 	=> $posts_per_page,
				'paged' 			=> $paged,
				'order' 			=> $order,
				'orderby' 			=> $orderby,
				'tax_query' => array(
					'relation'  => 'AND',
			        array(
			            'taxonomy' => 'product_type',
			            'field'    => 'slug',
			            'terms'    => 'ovabrw_car_rental'
			        )
			    ),
			);

			if ( $destination_id ) {
				$args_query['meta_query'][] = array(
			        'key'     => 'ovabrw_destination',
		            'value'   => $destination_id,
		            'compare' => 'LIKE',
			    );
			}

			$products = new WP_Query( apply_filters( 'ovabrw_ft_query_product_category_ajax', $args_query, $data ) );

			ob_start(); ?>
			<div class="ovabrw-products-result ovabrw-products-result-<?php echo esc_attr( $layout );?> <?php echo esc_attr( $column ); ?>">
				<?php
					if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
						if ( $thumbnail_type === 'gallery' ) {
							add_filter( 'ovabrw_ft_product_list_card_gallery', '__return_true' );
						} else {
							add_filter( 'ovabrw_ft_product_list_card_gallery', '__return_false' );
						}

						if ( $layout == 'grid' ) {
							wc_get_template_part( 'content', 'product' );
						} elseif ( $layout == 'list' ) {
		                    wc_get_template_part( 'rental/content-item', 'product-list' );
						}
					endwhile; else :
					?>
						<div class="not_found_product">
							<h3 class="empty-list">
								<?php esc_html_e( 'Not available tours', 'ova-brw' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'It seems we can’t find what you’re looking for.', 'ova-brw' ); ?>
							</p>
						</div>
					<?php
					endif; wp_reset_postdata();
				?>
			</div>
			<?php if ( $pagination ):
				$total 			= $products->max_num_pages;
				$found_posts 	= $products->found_posts;

				if ( $total > 1 ):
			?>
					<div class="ovabrw-pagination-ajax" data-paged="<?php echo esc_attr( $paged ); ?>">
						<?php
							echo ovabrw_pagination_ajax( $found_posts, $products->query_vars['posts_per_page'], $paged );
						?>
					</div>
			<?php endif; endif;

			$result = ob_get_contents(); 
			ob_end_clean();

			echo json_encode( array( "result" => $result ) );

			wp_die();
		}

		/**
		 * Create New Order - Loading Product
		 */
		public function ovabrw_load_data_product_create_order() {
			$product_id 		= isset($_POST['product_id']) ? absint( sanitize_text_field( $_POST['product_id'] ) ) : '';
			$currency 			= isset( $_POST['currency'] ) ? sanitize_text_field( $_POST['currency'] ) : '';
			$currency_symbol 	= get_woocommerce_currency_symbol( $currency );

			// Price
			$adult_price 	= get_post_meta( $product_id, '_regular_price', true );
			$children_price = get_post_meta( $product_id, 'ovabrw_children_price', true );
			$baby_price 	= get_post_meta( $product_id, 'ovabrw_baby_price', true );

			// Multiple Currency
			$adult_price 	= ovabrw_convert_price( $adult_price, ['currency' => $currency] );
			$children_price = ovabrw_convert_price( $children_price, ['currency' => $currency] );
			$baby_price 	= ovabrw_convert_price( $baby_price, ['currency' => $currency] );
			
			// Guests
			$adults_max		= get_post_meta( $product_id, 'ovabrw_adults_max', true );
			$adults_min 	= get_post_meta( $product_id, 'ovabrw_adults_min', true );

			$childrens_max 	= get_post_meta( $product_id, 'ovabrw_childrens_max', true );
			$childrens_min 	= get_post_meta( $product_id, 'ovabrw_childrens_min', true );

			$babies_max 	= get_post_meta( $product_id, 'ovabrw_babies_max', true );
			$babies_min 	= get_post_meta( $product_id, 'ovabrw_babies_min', true );

			// Days
			$days = get_post_meta( $product_id, 'ovabrw_number_days', true );

			// Amount of insurance
			$amount_insurance = get_post_meta( $product_id, 'ovabrw_amount_insurance', true );
			$amount_insurance = ovabrw_convert_price( $amount_insurance, ['currency' => $currency] );

			// Number of Tours
			$stock_quantity = get_post_meta( $product_id, 'ovabrw_stock_quantity', true );

			// Fixed Times
			$html_fixed_times = ovabrw_get_html_fixed_time_order( $product_id );

			// Get html custom checkout fields
			$html_custom_ckf = ovabrw_get_html_ckf_order( $product_id );

			// Get html resources
			$html_resources = ovabrw_get_html_resources_order( $product_id, $currency );

			// Get html services
			$html_services = ovabrw_get_html_services_order( $product_id, $currency );

			$data = [
				'adult_price' 		=> $adult_price ? $adult_price : 0,
				'children_price' 	=> $children_price ? $children_price: 0,
				'baby_price' 		=> $baby_price ? $baby_price: 0,
				'currency_symbol' 	=> $currency_symbol,
				'adults_max' 		=> $adults_max ? $adults_max : 1,
				'adults_min' 		=> $adults_min ? $adults_min : 1,
				'childrens_max' 	=> $childrens_max ? $childrens_max : 0,
				'childrens_min' 	=> $childrens_min ? $childrens_min: 0,
				'babies_max' 		=> $babies_max ? $babies_max: 0,
				'babies_min' 		=> $babies_min ? $babies_min: 0,
				'days' 				=> $days ? $days : 1,
				'amount_insurance' 	=> $amount_insurance ? $amount_insurance : 0,
				'stock_quantity' 	=> $stock_quantity ? $stock_quantity : 1,
				'html_custom_ckf' 	=> $html_custom_ckf,
				'html_resources' 	=> $html_resources,
				'html_services' 	=> $html_services,
				'html_fixed_time' 	=> $html_fixed_times,
				'qty_by_guests' 	=> ovabrw_qty_by_guests( $product_id )
			];

			echo json_encode( $data );

			wp_die();
		}

		/**
		 * Create New Order - Get Total
		 */
		public function ovabrw_create_order_get_total() {
			$product_id 	= isset($_POST['product_id']) ? absint( sanitize_text_field( $_POST['product_id'] ) ) : '';
			$currency 		= isset( $_POST['currency'] ) ? trim( sanitize_text_field( $_POST['currency'] ) ) : '';
			$pickup_date 	= isset($_POST['start_date']) ? trim( sanitize_text_field( $_POST['start_date'] ) ) : '';
			$time_from 		= isset($_POST['time_from']) ? sanitize_text_field( $_POST['time_from'] ) : '';
			$dropoff_date 	= isset($_POST['end_date']) ? trim( sanitize_text_field( $_POST['end_date'] ) ) : '';
			$deposit_amount = isset($_POST['deposit_amount']) ? floatval( sanitize_text_field( $_POST['deposit_amount'] ) ) : 0;
			$custom_ckf 	= isset($_POST['custom_ckf']) ? recursive_array_replace( '\\', '', $_POST['custom_ckf'] )  : '';
			$resources 		= isset($_POST['resources']) ? recursive_array_replace( '\\', '', $_POST['resources'] )  : '';
			$services 		= isset($_POST['services']) ? recursive_array_replace( '\\', '', $_POST['services'] )  : '';

			// Get new date
			$date_format    = ovabrw_get_date_format();
			$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
		    $min_childrens  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
		    $min_babies  	= absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );
		    $adults 		= isset($_POST['adults']) ? absint( sanitize_text_field( $_POST['adults'] ) ) : absint( $min_adults );
			$childrens 		= isset($_POST['childrens']) ? absint( sanitize_text_field( $_POST['childrens'] ) ) : absint( $min_childrens );
			$babies 		= isset($_POST['babies']) ? absint( sanitize_text_field( $_POST['babies'] ) ) : absint( $min_babies );

			$cart_item['product_id'] 		= $product_id;
			$cart_item['ovabrw_adults']		= $adults;
			$cart_item['ovabrw_childrens']	= $childrens;
			$cart_item['ovabrw_babies']		= $babies;
			$cart_item['ovabrw_quantity'] 	= 1;
			$cart_item['custom_ckf'] 		= (array)json_decode( $custom_ckf );
			$cart_item['ovabrw_resources'] 	= (array)json_decode( $resources );
			$cart_item['ovabrw_services'] 	= (array)json_decode( $services );
			$cart_item['ovabrw_time_from'] 	= $time_from;

			// Total
			$data_total = array(
				'error' => '',
				'remaining_amount' => 0,
			);
			$data_total['adults_price'] 	= 0;
			$data_total['childrens_price'] 	= 0;
			$data_total['babies_price'] 	= 0;

			// Duration
			$duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

			if ( $duration && $time_from ) {
				$pickup_date .= ' ' . $time_from;
			}

			// Price Per Guests
			$price_per_guests = ovabrw_price_per_guests( $product_id, strtotime( $pickup_date ), $adults, $childrens, $babies, $time_from );
			
			if ( ovabrw_check_array( $price_per_guests, 'adults_price' ) ) {
				$data_total['adults_price'] = ovabrw_convert_price( $price_per_guests['adults_price'], ['currency' => $currency] );
			}

			if ( ovabrw_check_array( $price_per_guests, 'childrens_price' ) ) {
				$data_total['childrens_price'] = ovabrw_convert_price( $price_per_guests['childrens_price'], ['currency' => $currency] );
			}

			if ( ovabrw_check_array( $price_per_guests, 'babies_price' ) ) {
				$data_total['babies_price'] = ovabrw_convert_price( $price_per_guests['babies_price'], ['currency' => $currency]);
			}

			// Amount Insurance
			$amount_insurance = floatval( get_post_meta( $product_id, 'ovabrw_amount_insurance', true ) );
			$amount_insurance = $amount_insurance * ( $adults + $childrens + $babies );
			$data_total['amount_insurance'] = ovabrw_convert_price( $amount_insurance , ['currency' => $currency] );

			// Line Total
			$line_total = get_price_by_guests( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ), $cart_item );
			$line_total += $amount_insurance;
			$line_total = ovabrw_convert_price( $line_total, ['currency' => $currency] );
			$amount_insurance = ovabrw_convert_price( $amount_insurance, ['currency' => $currency] );

			$data_total['line_total'] = $line_total;

		    $data_total = apply_filters( 'ovabrw_ft_ajax_create_order_data_total', $data_total, $product_id );

		    // Deposit
		    if ( $deposit_amount ) {
		    	if ( $deposit_amount <= $line_total ) {
		    		$remaining_amount = $line_total - $deposit_amount - $amount_insurance;
			    	$data_total['remaining_amount'] = $remaining_amount;
		    	} else {
		    		$data_total['line_total'] = 0;
		    		$data_total['remaining_amount'] = 0;
		    		$data_total['error'] = esc_html__( 'Deposit amount is greater than total.', 'ova-brw' );
		    	}
		    }

			echo json_encode( $data_total );

			wp_die();
		}

		/**
		 * Create New Order - Show Time
		 */
		public function ovabrw_create_order_show_time() {
			$product_id 	= isset($_POST['product_id']) ? sanitize_text_field( $_POST['product_id'] ) : '';
			$pickup_date 	= isset($_POST['pickup_date']) ? sanitize_text_field( $_POST['pickup_date'] ) : '';

			if ( ! $product_id || ! strtotime( $pickup_date ) ) wp_die();

			$result 	= array();
			$check_in 	= strtotime( $pickup_date );
			$dateformat = ovabrw_get_date_format();
			$timeformat = ovabrw_get_time_format();
			$datetime_format = $dateformat . ' ' . $timeformat;

			$duration 		= get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
			$number_days 	= get_post_meta( $product_id, 'ovabrw_number_days', true );
			$number_hours 	= get_post_meta( $product_id, 'ovabrw_number_hours', true );

			if ( ! $number_days ) $number_days = 0;
			if ( ! $number_hours ) $number_hours = 0;

			if ( $duration ) {
				$duration_time = ovabrw_get_duration_time( $product_id, strtotime( $pickup_date ) );

				if ( ! empty( $duration_time ) && is_array( $duration_time ) ) {
					$result['durration'] = ovabrw_create_order_get_html_duration( $product_id, $duration_time );

					$check_in 	= strtotime( $pickup_date . ' ' . $duration_time[0] );
					$check_out 	= apply_filters( 'ovabrw_calculate_checkout_by_hours', $check_in + floatval( $number_hours )*60*60, $check_in, $number_hours );

					$result['checkout'] = date_i18n( $datetime_format, $check_out );
				} else {
					$result['error'] = esc_html__( 'No time, please choose another date!', 'ova-brw' );
					echo json_encode( $result );
					wp_die();
				}
			} else {
				if ( $check_in ) {
					$check_out = apply_filters( 'ovabrw_calculate_checkout_by_days', $check_in + absint( $number_days )*24*60*60, $check_in, $number_days );

					$result['checkout'] = date_i18n( $dateformat, $check_out );
				}
			}

			if ( ovabrw_qty_by_guests( $product_id ) && $check_in && $check_out ) {
				$result['qty_by_guests'] = true;

				$adults 	= isset( $_POST['adults'] ) ? absint( sanitize_text_field( $_POST['adults'] ) ) : 0;
				$children 	= isset( $_POST['children'] ) ? absint( sanitize_text_field( $_POST['children'] ) ) : 0;
				$babies 	= isset( $_POST['babies'] ) ? absint( sanitize_text_field( $_POST['babies'] ) ) : 0;
				$quantity 	= isset( $_POST['quantity'] ) ? absint( sanitize_text_field( $_POST['quantity'] ) ) : 1;

				$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
			    $min_children  	= absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
			    $min_babies  	= absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );

				$guests = [
		            'adults'     => $adults * $quantity,
		            'children'   => $children * $quantity,
		            'babies'     => $babies * $quantity
		        ];

		        $guests_available = ovabrw_validate_guests_available( $product_id, $check_in, $check_out, $guests, 'search' );

		        if ( ! empty( $guests_available ) && is_array( $guests_available ) ) {
		        	// Adults
		        	if ( ! $guests_available['adults'] || $guests_available['adults'] < 0 ) {
		        		$result['max_adults'] = 0;
		        		$result['min_adults'] = 0;
		        		$result['val_adults'] = 0;
		        	} elseif ( $guests_available['adults'] <= $min_adults ) {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $guests_available['adults'];
		        		$result['val_adults'] = $guests_available['adults'];
		        	} else if ( $guests_available['adults'] <= $adults ) {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $min_adults;
		        		$result['val_adults'] = $guests_available['adults'];
		        	} else {
		        		$result['max_adults'] = $guests_available['adults'];
		        		$result['min_adults'] = $min_adults;
		        		$result['val_adults'] = $result['max_adults'] >= 1 ? 1 : $min_adults;
		        	}

		        	// Children
		        	if ( ! $guests_available['children'] || $guests_available['children'] < 0 ) {
		        		$result['max_children'] = 0;
		        		$result['min_children'] = 0;
		        		$result['val_children'] = 0;
		        	} elseif ( $guests_available['children'] <= $min_children ) {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $guests_available['children'];
		        		$result['val_children'] = $guests_available['children'];
		        	} else if ( $guests_available['children'] <= $children ) {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $min_children;
		        		$result['val_children'] = $guests_available['children'];
		        	} else {
		        		$result['max_children'] = $guests_available['children'];
		        		$result['min_children'] = $min_children;
		        		$result['val_children'] = $result['max_children'] >= 1 ? 1 : $min_children;
		        	}

		        	// Babies
		        	if ( ! $guests_available['babies'] || $guests_available['babies'] < 0 ) {
		        		$result['max_babies'] = 0;
		        		$result['min_babies'] = 0;
		        		$result['val_babies'] = 0;
		        	} elseif ( $guests_available['babies'] < $min_babies ) {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $guests_available['babies'];
		        		$result['val_babies'] = $guests_available['babies'];
		        	} elseif ( $guests_available['babies'] < $babies ) {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $min_babies;
		        		$result['val_babies'] = $guests_available['babies'];
		        	} else {
		        		$result['max_babies'] = $guests_available['babies'];
		        		$result['min_babies'] = $min_babies;
		        		$result['val_babies'] = $result['max_babies'] >= 1 ? 1 : $min_babies;
		        	}
		        } else {
		            $result['error'] = sprintf( __('%s isn\'t available for this time.<br>Please book other time.', 'ova-brw'), get_the_title( $product_id ) );
		            echo json_encode( $result );
					wp_die();
		        }
			}

			echo json_encode( $result );

			wp_die();
		}
		
		/**
		 * Create New Order - Choose Time
		 */
		public function ovabrw_create_order_choose_time() {
			wp_die();
		}
	}

	return new OVABRW_Ajax();
}