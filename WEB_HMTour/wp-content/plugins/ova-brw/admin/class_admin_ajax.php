<?php  defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'ovabrw_admin_ajax' ) ) {
	class ovabrw_admin_ajax{
		public function __construct(){
			$this->init();
		}

		public function init(){
			// Define All Ajax function
			$arr_ajax =  array(
				'update_order_status_woo',
				'ovabrw_get_custom_tax_in_cat',
				'ovabrw_update_insurance'
			);

			foreach ( $arr_ajax as $val ) {
				add_action( 'wp_ajax_'.$val, array( $this, $val ) );
				add_action( 'wp_ajax_nopriv_'.$val, array( $this, $val ) );
			}
		}

		/**
		 * Schedule Ajax
		 */
		public static function update_order_status_woo() {
			$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
			$new_order_status = isset( $_POST['new_order_status'] ) ? sanitize_text_field( $_POST['new_order_status'] ) : ''  ;

			if ( $order_id && $new_order_status ) {
				$order = new WC_Order($order_id);

				if ( ! current_user_can( apply_filters( 'ovabrw_update_order_status' ,'publish_posts' ) ) ) {
					echo 'error_permission';	
				} elseif ( $order->update_status( $new_order_status ) ) {
					echo 'true';
				} else {
					echo 'false';
				}
			} else {
				echo 'false';
			}
			
			wp_die();
		}

		/**
		 * Get Custom Taxonomy choosed in Category
		 */
		public static function ovabrw_get_custom_tax_in_cat() {
			$checked_tax = isset( $_POST['checked_tax'] ) ?  $_POST['checked_tax'] : '';
			
			$list_tax_values = array();
			
			if ( $checked_tax ) {
				foreach ( $checked_tax as $key => $term_id ) {
					$ovabrw_custom_tax = get_term_meta($term_id, 'ovabrw_custom_tax', true);
					
					if ( $ovabrw_custom_tax ) {
						foreach ( $ovabrw_custom_tax as $key => $value ) {
							if ( ! in_array( $value, $list_tax_values ) ) {
								if ( $value ) {
									array_push( $list_tax_values, $value);		
								}
							}
						}
					}
				}
			}
			
			echo implode( ",", $list_tax_values ); 
			wp_die();
		}

		// Update insurance amount
		public function ovabrw_update_insurance() {
			$order_id 	= isset( $_POST['order_id'] ) ? (int) $_POST['order_id'] : '';
			$item_id 	= isset( $_POST['item_id'] ) ? (int) $_POST['item_id'] : '';
			$amount 	= isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
			$tax 		= isset( $_POST['tax'] ) ? floatval( $_POST['tax'] ) : 0;

			if ( ! $order_id || ! $item_id || $amount < 0 || $tax < 0 ) wp_die();

			$order = wc_get_order( $order_id );
            if ( ! $order ) wp_die();

			$item = WC_Order_Factory::get_order_item( absint( $item_id ) );
            if ( ! $item ) wp_die();

            // Insurance key
           	$insurance_key = $order->get_meta( '_ova_insurance_key' );

            // Total insurance
            $order_insurance 		= floatval( $order->get_meta( '_ova_insurance_amount' ) );
            $order_insurance_tax 	= floatval( $order->get_meta( '_ova_insurance_tax' ) );

            // Item insurance
            $item_insurance = floatval( $item->get_meta( 'ovabrw_insurance_amount' ) );

            // Item insurance tax
            $item_insurance_tax = floatval( $item->get_meta( 'ovabrw_insurance_tax' ) );

            // Original order and item
            $original_order 	= $original_item = false;
            $original_item_id 	= $order->get_meta( '_ova_original_item_id' );

            if ( absint( $original_item_id ) ) {
            	$original_item = WC_Order_Factory::get_order_item( absint( $original_item_id ) );

            	if ( $original_item ) {
            		$original_order = $original_item->get_order();
            	}
            }

            // Get fees
            $fees = $order->get_fees();
            
            // Update order insurance amount
            if ( ! empty( $fees ) && is_array( $fees ) ) {
            	foreach ( $fees as $item_fee_id => $item_fee ) {
            		$fee_key = sanitize_title( $item_fee->get_name() );

            		if ( $fee_key === $insurance_key ) {
            			$order_insurance -= $item_insurance;
            			$order_insurance += $amount;

            			$order_insurance_tax -= $item_insurance_tax;
            			$order_insurance_tax += $tax;

            			if ( $order_insurance < 0 ) $order_insurance = 0;
            			if ( $order_insurance_tax < 0 ) $order_insurance_tax = 0;

            			// Update item fee
            			if ( wc_tax_enabled() ) {
                            $order_taxes = $order->get_taxes();
                            $tax_item_id = 0;

                            foreach ( $order_taxes as $tax_item ) {
                                $tax_item_id = $tax_item->get_rate_id();

                                if ( $tax_item_id ) break;
                            }

                            $item_fee->set_props(
								array(
									'total'     => $order_insurance,
									'subtotal'  => $order_insurance,
									'total_tax' => $order_insurance_tax,
									'taxes'     => array(
										'total' => array( $tax_item_id => $order_insurance_tax ),
									),
								)
							);

                            // Update original item
                            if ( $original_item ) {
                            	// Get original item remaining insurance amount
                            	$item_remaining_insurance = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance' ) );

                            	// Get original item remaining insurance tax amount
                            	$item_remaining_insurance_tax = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance_tax' ) );

                            	// Update original item meta data
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance', $order_insurance );
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance_tax', $order_insurance_tax );
                            	$original_item->save();

                            	// Update original order
	                            if ( $original_order ) {
	                            	// Get original order remaining insurance amount
	                            	$order_remaining_insurance = floatval( $original_order->get_meta( '_ova_remaining_insurance' ) );
	                            	$order_remaining_insurance -= $item_remaining_insurance;
	                            	$order_remaining_insurance += $order_insurance;

	                            	// Get original order remaining insurance tax amount
	                            	$order_remaining_insurance_tax = floatval( $original_order->get_meta( '_ova_remaining_insurance_tax' ) );
	                            	$order_remaining_insurance_tax -= $item_remaining_insurance_tax;
	                            	$order_remaining_insurance_tax += $order_insurance_tax;

	                            	// Update original order meta data
	                            	$original_order->update_meta_data( '_ova_remaining_insurance', $order_remaining_insurance );
	                            	$original_order->update_meta_data( '_ova_remaining_insurance_tax', $order_remaining_insurance_tax );
	                            	$original_order->save();
	                            }
                            }
            			} else {
            				$item_fee->set_props(
								array(
									'total'     => $order_insurance,
									'subtotal'  => $order_insurance
								)
							);

							// Update original order and item
                            if ( $original_item ) {
                            	// Get original item remaining insurance amount
                            	$item_remaining_insurance = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance' ) );

                            	// Update original item meta data
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance', $order_insurance );
                            	$original_item->save();

                            	// Update original order
	                            if ( $original_order ) {
	                            	// Get original order remaining insurance amount
	                            	$order_remaining_insurance = floatval( $original_order->get_meta( '_ova_remaining_insurance' ) );
	                            	$order_remaining_insurance -= $item_remaining_insurance;
	                            	$order_remaining_insurance += $order_insurance;

	                            	// Update original order meta data
	                            	$original_order->update_meta_data( '_ova_remaining_insurance', $order_remaining_insurance );
	                            	$original_order->save();
	                            }
                            }
            			}

            			$item_fee->set_amount( $order_insurance );
            			$item_fee->save();

            			// Update item insurance
        				$item->update_meta_data( 'ovabrw_insurance_amount', $amount );
        				$item->update_meta_data( 'ovabrw_insurance_tax', $tax );
        				$item->save();

        				// Update order insurance
        				$order->update_meta_data( '_ova_insurance_amount', $order_insurance );
        				$order->update_meta_data( '_ova_insurance_tax', $order_insurance_tax );
        				$order->update_taxes();
        				$order->calculate_totals( false );
            		}
            	}
            }

            wp_die();
		}
	}

	new ovabrw_admin_ajax();
}