<?php defined( 'ABSPATH' ) || exit();

// Custom Calculate Total Add To Cart
add_action( 'woocommerce_before_calculate_totals',  'ovabrw_woocommerce_before_calculate_totals' , 10, 1 ); 
if ( ! function_exists( 'ovabrw_woocommerce_before_calculate_totals' ) ) {
    function ovabrw_woocommerce_before_calculate_totals( $cart_object ) {
        $deposit_amount     = $remaining_amount = $remaining_tax = 0;
        $insurance_amount   = $insurance_tax = $remaining_insurance = $remaining_insurance_tax = 0;
        $has_deposit        = false;

        // Init deposit
        WC()->cart->deposit_info = array();

        // Loop cart object
        foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
            // Get product id
            $product_id = $cart_item['data']->get_id();

            // Check rental product
            if ( ! $product_id || ! $cart_item['data']->is_type( 'ovabrw_car_rental' ) ) continue;

            // Quantity
            $quantity = ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );

            // Check-in date
            $checkin_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickup_date', $cart_item ) );

            // Check-out date
            $checkout_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickoff_date', $cart_item ) );

            // Number of adults
            $numberof_adults = (int)ovabrw_get_meta_data( 'ovabrw_adults', $cart_item );

            // Number of children
            $numberof_children = (int)ovabrw_get_meta_data( 'ovabrw_childrens', $cart_item );

            // Number of babies
            $numberof_babies = (int)ovabrw_get_meta_data( 'ovabrw_babies', $cart_item );

            // Total number of guests
            $numberof_guests = $numberof_adults + $numberof_children + $numberof_babies;

            $subtotal = get_price_by_guests( $product_id, $checkin_date, $checkout_date, $cart_item );

            // Insurance amount
            $sub_insurance = (float)get_post_meta( $product_id, 'ovabrw_amount_insurance', true );
            $sub_insurance = $sub_insurance*$numberof_guests*$quantity;
            $sub_remaining_insurance = 0;

            if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
                $subtotal       = ovabrw_convert_price( $subtotal );
                $sub_insurance  = ovabrw_convert_price( $sub_insurance );
            }

            // Get deposit
            $is_deposit = isset( $cart_item['is_deposit'] ) ? $cart_item['is_deposit'] : false;

            if ( $is_deposit ) {
                $has_deposit    = true;
                $sub_deposit    = 0;
                $deposit_type   = get_post_meta( $product_id, 'ovabrw_type_deposit', true );
                $deposit_value  = (float)get_post_meta( $product_id, 'ovabrw_amount_deposit', true );

                // Calculate deposit
                if ( 'percent' === $deposit_type ) {
                    $sub_deposit = ( $subtotal * $deposit_value ) / 100;

                    if ( $sub_insurance && !ovabrw_insurance_paid_once() ) {
                        $sub_remaining_insurance = $sub_insurance - floatval( ( $sub_insurance * $deposit_value ) / 100 );
                        $sub_insurance = floatval( ( $sub_insurance * $deposit_value ) / 100 );
                    }
                } elseif ( 'value' === $deposit_type ) {
                    $sub_deposit = $deposit_value;
                }

                // Sub remaining
                $sub_remaining          = floatval( $subtotal - $sub_deposit );
                $sub_remaining_taxes    = ovabrw_get_taxes_by_price( $cart_item['data'], ovabrw_convert_price( $sub_remaining, array(), false ) );
                $remaining_tax          += $sub_remaining_taxes;

                // Cart item add data
                $cart_item['data']->add_meta_data( 'is_deposit', $is_deposit, true );
                $cart_item['data']->add_meta_data( 'deposit_type', $deposit_type, true );
                $cart_item['data']->add_meta_data( 'deposit_value', $deposit_value, true );
                $cart_item['data']->add_meta_data( 'deposit_amount', round( $sub_deposit, wc_get_price_decimals() ), true );
                $cart_item['data']->add_meta_data( 'remaining_amount', round( $sub_remaining, wc_get_price_decimals() ), true );
                $cart_item['data']->add_meta_data( 'remaining_tax', round( $sub_remaining_taxes, wc_get_price_decimals() ), true );
                $cart_item['data']->add_meta_data( 'total_payable', round( $subtotal, wc_get_price_decimals() ), true );
                // Set item price
                $cart_item['data']->set_price( round( $sub_deposit / $quantity, wc_get_price_decimals() ) );

                $deposit_amount     += $sub_deposit;
                $remaining_amount   += $sub_remaining;
            } else {
                // Set item price
                $cart_item['data']->set_price( round( $subtotal / $quantity, wc_get_price_decimals() ) );
            }

            // Insurance
            if ( $sub_insurance ) {
                $insurance_amount += $sub_insurance;
                $cart_item['data']->add_meta_data( 'insurance_amount', round( $sub_insurance, wc_get_price_decimals() ), true );

                $sub_insurance_tax = ovabrw_get_insurance_tax_amount( ovabrw_convert_price( $sub_insurance, array(), false ) );

                if ( $sub_insurance_tax ) {
                    $insurance_tax += $sub_insurance_tax;

                    $cart_item['data']->add_meta_data( 'insurance_tax', round( $sub_insurance_tax, wc_get_price_decimals() ), true );
                }
            }

            // Remaining insurance
            if ( $sub_remaining_insurance ) {
                $remaining_insurance += $sub_remaining_insurance;
                $cart_item['data']->add_meta_data( 'remaining_insurance', round( $sub_remaining_insurance, wc_get_price_decimals() ), true );

                $sub_remaining_insurance_tax = ovabrw_get_insurance_tax_amount( ovabrw_convert_price( $sub_remaining_insurance, array(), false ) );

                if ( $sub_remaining_insurance_tax ) {
                    $remaining_insurance_tax += $sub_remaining_insurance_tax;

                    $cart_item['data']->add_meta_data( 'remaining_insurance_tax', round( $sub_remaining_insurance_tax, wc_get_price_decimals() ), true );
                }
            }

            // Quantity
            $cart_object->cart_contents[ $cart_item_key ]['quantity'] = $quantity;
        }
        // End loop cart object

        // Deposit info
        if ( $has_deposit ) {
            WC()->cart->deposit_info[ 'has_deposit' ]       = $has_deposit;
            WC()->cart->deposit_info[ 'deposit_amount' ]    = round( $deposit_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'remaining_amount' ]  = round( $remaining_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'remaining_tax' ]     = round( $remaining_tax, wc_get_price_decimals() );
        }

        // Cart fee - Insurance
        if ( $insurance_amount ) {
            $insurance_name         = ovabrw_get_insurance_fee_name();
            $enable_insurance_tax   = ovabrw_insurance_tax_enabled();
            $tax_class              = ovabrw_get_insurance_tax_class();

            WC()->cart->add_fee( $insurance_name, ovabrw_convert_price( $insurance_amount, array(), false ), $enable_insurance_tax, $tax_class );

            WC()->cart->deposit_info[ 'insurance_amount' ]  = $insurance_amount;
            WC()->cart->deposit_info[ 'insurance_tax' ]     = $insurance_tax;
            WC()->cart->deposit_info[ 'insurance_key' ]     = ovabrw_get_insurance_fee_key();
        }
        if ( $remaining_insurance ) {
            WC()->cart->deposit_info[ 'remaining_insurance' ]       = $remaining_insurance;
            WC()->cart->deposit_info[ 'remaining_insurance_tax' ]   = $remaining_insurance_tax;
        }
    }
}

/**
 * Get Price a product with Pick-up date, Drop-off date
 * @param  [type] $product_id
 * @param  [strtotime] $checkin_date
 * @param  [strtotime] $checkout_date
 * @return $line_total
 */
if ( ! function_exists( 'get_price_by_guests' ) ) {
    function get_price_by_guests( $product_id = false, $checkin_date = '', $checkout_date = '', $cart_item = [] ) {
        // Get New Date to match per product
        $date_format    = ovabrw_get_date_format();
        $new_date       = ovabrw_new_input_date( $product_id, $checkin_date, $checkout_date, $date_format );
        $new_checkin    = $new_date['pickup_date_new'];
        $new_checkout   = $new_date['pickoff_date_new'];

        // Number of adults
        $numberof_adults = (int)ovabrw_get_meta_data( 'ovabrw_adults', $cart_item );

        // Number of children
        $numberof_children = (int)ovabrw_get_meta_data( 'ovabrw_childrens', $cart_item );

        // Number of babies
        $numberof_babies = (int)ovabrw_get_meta_data( 'ovabrw_babies', $cart_item );

        // Time From
        $time_from = '';

        if ( ovabrw_check_array( $cart_item, 'ovabrw_time_from' ) ) {
            $time_from = $cart_item['ovabrw_time_from'];
        }

        // Global price
        $line_total = ovabrw_price_global( $product_id, $new_checkin, $new_checkout, $numberof_adults, $numberof_children, $numberof_babies, $time_from );

        // Resources
        if ( ovabrw_check_array( $cart_item, 'ovabrw_resources' ) ) {
            $resources          = $cart_item['ovabrw_resources'];
            $total_resources    = ovabrw_get_total_resoures( $product_id, $resources, $numberof_adults, $numberof_children, $numberof_babies );
            $line_total         += $total_resources;
        }

        // Services
        if ( ovabrw_check_array( $cart_item, 'ovabrw_services' ) ) {
            $services       = $cart_item['ovabrw_services'];
            $total_services = ovabrw_get_total_services( $product_id, $services, $numberof_adults, $numberof_children, $numberof_babies );
            $line_total     += $total_services;
        }

        // Quantity
        $ovabrw_quantity = 1;
        if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
            $ovabrw_quantity = absint( $cart_item['ovabrw_quantity'] );
        }

        $line_total *= $ovabrw_quantity;

        // Custom Checkout Fields
        if ( isset( $cart_item['custom_ckf'] ) ) {
            $price_ckf = ovabrw_get_price_ckf( $product_id, $cart_item['custom_ckf'] );

            if ( $price_ckf ) {
                $line_total += $price_ckf;
            }
        }

        // Decimals
        $line_total = round( $line_total, wc_get_price_decimals() );

        return apply_filters( 'ovabrw_get_price_by_guests', $line_total, $product_id, $checkin_date, $checkout_date, $cart_item );
    }
}

/* Get Price Per Guests */
if ( ! function_exists( 'ovabrw_price_per_guests' ) ) {
    function ovabrw_price_per_guests( $product_id = false, $checkin_date = '', $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0, $time_from = '' ) {
        // Adults price
        $adult_price    = ovabrw_regular_price_global( $product_id, $checkin_date );
        $child_price    = (float)get_post_meta( $product_id, 'ovabrw_children_price', true );
        $baby_price     = (float)get_post_meta( $product_id, 'ovabrw_baby_price', true );

        // Duration
        $duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
        
        if ( $duration && $time_from ) {
            $weekday = ovabrw_get_weekday( $checkin_date );

            $schedule_price = ovabrw_get_price_from_schedule( $product_id, $weekday, $time_from );

            if ( $schedule_price ) {
                $adult_price    = $schedule_price['adults_price'];
                $child_price    = $schedule_price['childrens_price'];
                $baby_price     = $schedule_price['babies_price'];
            }
        }

        // Global Discount (GD)
        $gd_prices = ovabrw_get_price_by_global_discount( $product_id, $numberof_adults, $numberof_children, $numberof_babies );

        if ( $gd_prices && is_array( $gd_prices ) ) {
            $adult_price    = $gd_prices['adults_price'];
            $child_price    = $gd_prices['childrens_price'];
            $baby_price    = $gd_prices['babies_price'];
        }

        // Special Time (ST)
        $st_prices = ovabrw_get_price_by_special_time( $product_id, $checkin_date, $numberof_adults, $numberof_children, $numberof_babies );
        if ( $st_prices && is_array( $st_prices ) ) {
            $adult_price    = $st_prices['adults_price'];
            $child_price    = $st_prices['childrens_price'];
            $baby_price     = $st_prices['babies_price'];
        }

        $price_guests = array(
            'adults_price'      => $adult_price,
            'childrens_price'   => $child_price,
            'babies_price'      => $baby_price,
        );

        return apply_filters( 'ovabrw_price_per_guests', $price_guests, $product_id, $checkin_date, $numberof_adults, $numberof_children, $numberof_babies, $time_from );
    }
}

/* Get Price in Global */
if ( ! function_exists( 'ovabrw_price_global' ) ) {
    function ovabrw_price_global( $product_id = false, $checkin_date = '', $checkout_date = '', $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0, $time_from = '' ) {
        // Adults price
        $adult_price    = ovabrw_regular_price_global( $product_id, $checkin_date );
        $child_price    = (float)get_post_meta( $product_id, 'ovabrw_children_price', true );
        $baby_price     = (float)get_post_meta( $product_id, 'ovabrw_baby_price', true );

        // Duration
        $duration   = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
        $type_price = '';
        
        if ( $duration && $time_from ) {
            $weekday = ovabrw_get_weekday( $checkin_date );

            $schedule_price = ovabrw_get_price_from_schedule( $product_id, $weekday, $time_from );

            if ( $schedule_price ) {
                $adult_price    = $schedule_price['adults_price'];
                $child_price    = $schedule_price['childrens_price'];
                $baby_price     = $schedule_price['babies_price'];
                $type_price     = $schedule_price['type_price'];
            }
        }

        // Global Discount (GD)
        $gd_prices = ovabrw_get_price_by_global_discount( $product_id, $numberof_adults, $numberof_children, $numberof_babies );
        if ( $gd_prices && is_array( $gd_prices ) ) {
            $adult_price    = $gd_prices['adults_price'];
            $child_price    = $gd_prices['childrens_price'];
            $baby_price     = $gd_prices['babies_price'];
        }

        // Special Time (ST)
        $st_prices = ovabrw_get_price_by_special_time( $product_id, $checkin_date, $numberof_adults, $numberof_children, $numberof_babies );
        if ( $st_prices && is_array( $st_prices ) ) {
            $adult_price    = $st_prices['adults_price'];
            $child_price    = $st_prices['childrens_price'];
            $baby_price    = $st_prices['babies_price'];
        }

        $total = $adult_price*$numberof_adults + $child_price*$numberof_children + $baby_price*$numberof_babies;

        if ( $type_price === 'total' ) {
            $total = 0;
            if ( $numberof_adults ) $total += $adult_price;
            if ( $numberof_children ) $total += $child_price;
            if ( $numberof_babies ) $total += $baby_price;
        }

        return apply_filters( 'ovabrw_price_global', floatval( $total ), $product_id, $checkin_date, $checkout_date, $numberof_adults, $numberof_children, $numberof_babies, $time_from );
    }
}

/* Get Sale Price in Global */
if ( ! function_exists( 'ovabrw_regular_price_global' ) ) {
    function ovabrw_regular_price_global( $product_id, $checkin_date ) {
        // Regular Price
        $regular_price = get_post_meta( $product_id, '_regular_price', true );

        if ( ovabrw_wcml_get_product_price( $product_id, '_regular_price' ) ) {
            $regular_price = ovabrw_wcml_get_product_price( $product_id, '_regular_price' );
        }

        // Sale Price
        $sale_price = get_post_meta( $product_id, '_sale_price', true );

        if ( ovabrw_wcml_get_product_price( $product_id, '_sale_price' ) ) {
            $sale_price = ovabrw_wcml_get_product_price( $product_id, '_sale_price' );
        }
        
        if ( $sale_price ) {
            // Sale date
            $sale_from  = absint( get_post_meta( $product_id, '_sale_price_dates_from', true ) );
            $sale_to    = absint( get_post_meta( $product_id, '_sale_price_dates_to', true ) );

            if ( $sale_from && $sale_to ) {
                if ( $sale_from <= $checkin_date && $checkin_date <= $sale_to ) {
                    $regular_price = $sale_price;
                }
            } else if ( $sale_from && !$sale_to ) {
                if ( $sale_from <= $checkin_date ) {
                    $regular_price = $sale_price;
                }
            } else if ( !$sale_from && $sale_to ) {
                if ( $checkin_date <= $sale_to ) {
                    $regular_price = $sale_price;
                }
            } else {
                $regular_price = $sale_price;
            }
        }

        if ( ! $regular_price ) {
            $regular_price = 0;
        }

        return apply_filters( 'ovabrw_regular_price_global', (float)$regular_price, $product_id, $checkin_date );
    }
}

// Get Price in Global Discount (GD)
if ( ! function_exists( 'ovabrw_get_price_by_global_discount' ) ) {
    function ovabrw_get_price_by_global_discount( $product_id = false, $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0 ) {
        $ovabrw_gd_duration_min = get_post_meta( $product_id, 'ovabrw_gd_duration_min', true );

        if ( $ovabrw_gd_duration_min && is_array( $ovabrw_gd_duration_min ) ) {
            asort( $ovabrw_gd_duration_min );

            // Total number of guests
            $numberof_guests = apply_filters( 'ovabrw_get_total_guests_by_global_discount', (int)$numberof_adults + (int)$numberof_children + (int)$numberof_babies, $product_id, $numberof_adults, $numberof_children, $numberof_babies );

            foreach ( $ovabrw_gd_duration_min as $key => $duration_min ) {
                $ovabrw_gd_duration_max      = get_post_meta( $product_id, 'ovabrw_gd_duration_max', true );
                $ovabrw_gd_adult_price       = get_post_meta( $product_id, 'ovabrw_gd_adult_price', true );
                $ovabrw_gd_children_price    = get_post_meta( $product_id, 'ovabrw_gd_children_price', true );
                $ovabrw_gd_baby_price        = get_post_meta( $product_id, 'ovabrw_gd_baby_price', true );

                // Duration Max Number
                $gd_duration_max = 0;
                if ( isset( $ovabrw_gd_duration_max[$key] ) && $ovabrw_gd_duration_max[$key] ) {
                    $gd_duration_max = floatval( $ovabrw_gd_duration_max[$key] );
                }

                // Discount Adult Price
                $gd_adult_price = 0;
                if ( isset( $ovabrw_gd_adult_price[$key] ) && $ovabrw_gd_adult_price[$key] ) {
                    $gd_adult_price = floatval( $ovabrw_gd_adult_price[$key] );
                }

                // Discount Children Price
                $gd_child_price = 0;
                if ( isset( $ovabrw_gd_children_price[$key] ) && $ovabrw_gd_children_price[$key] ) {
                    $gd_child_price = floatval( $ovabrw_gd_children_price[$key] );
                }

                // Discount Baby Price
                $gd_baby_price = 0;
                if ( isset( $ovabrw_gd_baby_price[$key] ) && $ovabrw_gd_baby_price[$key] ) {
                    $gd_baby_price = floatval( $ovabrw_gd_baby_price[$key] );
                }

                if ( $numberof_guests >= $duration_min && $numberof_guests <= $gd_duration_max ){
                    $gd_prices = array(
                        'adults_price'      => $gd_adult_price,
                        'childrens_price'   => $gd_child_price,
                        'babies_price'      => $gd_baby_price,
                    );

                    return apply_filters( 'ovabrw_get_price_by_global_discount', $gd_prices, $product_id, $numberof_adults, $numberof_children, $numberof_babies );
                }
            }
        }

        return apply_filters( 'ovabrw_get_price_by_global_discount', false, $product_id, $numberof_adults, $numberof_children, $numberof_babies );
    }
}

/* Get Price Product */
if ( ! function_exists( 'ovabrw_get_price_product' ) ) {
    function ovabrw_get_price_product( $product_id ) {
        $product        = wc_get_product( $product_id );
        $regular_price  = 0;

        if ( $product->is_on_sale() && $product->get_sale_price() ) {
            $regular_price  = $product->get_sale_price();
            $sale_price     = $product->get_regular_price();
        } else {
            $regular_price = $product->get_regular_price();
        }

        return apply_filters( 'ovabrw_get_price_product', $regular_price, $product_id );
    }
}

/* Get Price in Special Time (ST) */
if ( ! function_exists( 'ovabrw_get_price_by_special_time' ) ) {
    function ovabrw_get_price_by_special_time( $product_id = false, $checkin_date = '', $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0 ) {

        $date_format    = ovabrw_get_date_format();
        $checkin_date   = strtotime( date_i18n( $date_format, $checkin_date ) );
        if ( ! $checkin_date ) return false;

        $st_prices = array();
        $ovabrw_st_startdate = get_post_meta( $product_id, 'ovabrw_st_startdate', true );

        if ( $ovabrw_st_startdate && is_array( $ovabrw_st_startdate ) ) {
            // Total number of guests
            $numberof_guests = (int)$numberof_adults + (int)$numberof_children + (int)$numberof_babies;

            // ST
            $ovabrw_st_enddate          = get_post_meta( $product_id, 'ovabrw_st_enddate', true );
            $ovabrw_st_adult_price      = get_post_meta( $product_id, 'ovabrw_st_adult_price', true );
            $ovabrw_st_children_price   = get_post_meta( $product_id, 'ovabrw_st_children_price', true );
            $ovabrw_st_baby_price       = get_post_meta( $product_id, 'ovabrw_st_baby_price', true );
            $ovabrw_st_discount         = get_post_meta( $product_id, 'ovabrw_st_discount', true );

            foreach ( $ovabrw_st_startdate as $key => $start_date ) {
                // Start date
                if ( ovabrw_check_array( $ovabrw_st_startdate, $key ) ) {
                    $start_date = strtotime( $ovabrw_st_startdate[$key] );
                }

                // End date
                $end_date = '';
                if ( ovabrw_check_array( $ovabrw_st_enddate, $key ) ) {
                    $end_date = strtotime( $ovabrw_st_enddate[$key] );
                }

                // Adult Price
                $adult_price = 0;
                if ( ovabrw_check_array( $ovabrw_st_adult_price, $key ) ) {
                    $adult_price = floatval( $ovabrw_st_adult_price[$key] );
                }

                // Child Price
                $child_price = 0;
                if ( ovabrw_check_array( $ovabrw_st_children_price, $key ) ) {
                    $child_price = floatval( $ovabrw_st_children_price[$key] );
                }

                // Baby Price
                $baby_price = 0;
                if ( ovabrw_check_array( $ovabrw_st_baby_price, $key ) ) {
                    $baby_price = floatval( $ovabrw_st_baby_price[$key] );
                }

                // Discounts
                $discount = array();
                if ( ovabrw_check_array( $ovabrw_st_discount, $key ) ) {
                    $discount = $ovabrw_st_discount[$key];
                }

                if ( $start_date && $end_date ) {
                    if ( $checkin_date >= $start_date && $checkin_date <= $end_date ) {
                        $st_prices = array(
                            'adults_price'      => $adult_price,
                            'childrens_price'   => $child_price,
                            'babies_price'      => $baby_price,
                        );

                        if ( $discount && is_array( $discount ) ) {
                            $dsc_min = $dsc_max = $dsc_adult_price = $dsc_child_price = $dsc_baby_price = array();
                            if ( ovabrw_check_array( $discount, 'min' ) ) {
                                $dsc_min = $discount['min'];
                            }

                            if ( ovabrw_check_array( $discount, 'max' ) ) {
                                $dsc_max = $discount['max'];
                            }

                            if ( ovabrw_check_array( $discount, 'adult_price' ) ) {
                                $dsc_adult_price = $discount['adult_price'];
                            }

                            if ( ovabrw_check_array( $discount, 'children_price' ) ) {
                                $dsc_child_price = $discount['children_price'];
                            }

                            if ( ovabrw_check_array( $discount, 'baby_price' ) ) {
                                $dsc_baby_price = $discount['baby_price'];
                            }

                            if ( $dsc_min && is_array( $dsc_min ) ) {
                                foreach( $dsc_min as $dsc_key => $dsc_min_number ) {
                                    $dsc_min_number = absint( $dsc_min_number );

                                    $dsc_max_number = 0;
                                    if ( ovabrw_check_array( $dsc_max, $dsc_key ) ) {
                                        $dsc_max_number = absint( $dsc_max[$dsc_key] );
                                    }

                                    $dsc_adult_amount = 0;
                                    if ( ovabrw_check_array( $dsc_adult_price, $dsc_key ) ) {
                                        $dsc_adult_amount = floatval( $dsc_adult_price[$dsc_key] );
                                    }

                                    $dsc_child_amount = 0;
                                    if ( ovabrw_check_array( $dsc_children_price, $dsc_key ) ) {
                                        $dsc_child_amount = floatval( $dsc_child_price[$dsc_key] );
                                    }

                                    $dsc_baby_amount = 0;
                                    if ( ovabrw_check_array( $dsc_baby_price, $dsc_key ) ) {
                                        $dsc_baby_amount = floatval( $dsc_baby_price[$dsc_key] );
                                    }

                                    if ( $numberof_guests >= $dsc_min_number && $numberof_guests <= $dsc_max_number  ) {
                                        $st_prices = array(
                                            'adults_price'      => $dsc_adult_amount,
                                            'childrens_price'   => $dsc_child_amount,
                                            'babies_price'      => $dsc_baby_amount,
                                        );
                                    }
                                }
                            }
                        }

                        return apply_filters( 'ovabrw_get_price_by_special_time', $st_prices, $product_id, $checkin_date, $numberof_adults, $numberof_children, $numberof_babies );
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_price_by_special_time', false, $product_id, $checkin_date, $numberof_adults, $numberof_children, $numberof_babies );
    }
}

/* Get Price in Resources */
if ( ! function_exists( 'ovabrw_get_total_resoures' ) ) {
    function ovabrw_get_total_resoures( $product_id = false, $resources = array(), $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0 ) {
        $total_resources = 0;

        if ( $resources && is_array( $resources ) ) {
            $rs_ids             = get_post_meta( $product_id, 'ovabrw_rs_id', true );
            $rs_names           = get_post_meta( $product_id, 'ovabrw_rs_name', true );
            $rs_adult_price     = get_post_meta( $product_id, 'ovabrw_rs_adult_price', true );
            $rs_child_price     = get_post_meta( $product_id, 'ovabrw_rs_children_price', true );
            $rs_baby_price      = get_post_meta( $product_id, 'ovabrw_rs_baby_price', true );
            $rs_duration_type   = get_post_meta( $product_id, 'ovabrw_rs_duration_type', true );

            foreach ( $resources as $rs_id => $rs_name ) {
                $key = array_search( $rs_id, $rs_ids );

                if ( !is_bool( $key ) ) {
                    $adult_price = 0;
                    if ( ovabrw_check_array( $rs_adult_price, $key ) ) {
                        $adult_price = $rs_adult_price[$key];
                    }

                    $child_price = 0;
                    if ( ovabrw_check_array( $rs_child_price, $key ) ) {
                        $child_price = $rs_child_price[$key];
                    }

                    $baby_price = 0;
                    if ( ovabrw_check_array( $rs_baby_price, $key ) ) {
                        $baby_price = $rs_baby_price[$key];
                    }

                    $duration_type = 'person';
                    if ( ovabrw_check_array( $rs_duration_type, $key ) ) {
                        $duration_type = $rs_duration_type[$key];
                    }

                    if ( 'person' === $duration_type ) {
                        $total_resources += floatval( $adult_price*$numberof_adults ) + floatval( $child_price*$numberof_children ) + floatval( $baby_price*$numberof_babies );
                    } else {
                        $total_resources += (float)$adult_price + (float)$child_price + (float)$baby_price;
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_total_resoures', (float)$total_resources, $product_id, $resources, $numberof_adults, $numberof_children, $numberof_babies );
    }
}

/* Get Price in Services */
if ( ! function_exists( 'ovabrw_get_total_services' ) ) {
    function ovabrw_get_total_services( $product_id = false, $services = array(), $numberof_adults = 0, $numberof_children = 0, $numberof_babies = 0 ) {
        $total_services = 0;

        if ( $services && is_array( $services ) ) {
            $service_ids            = get_post_meta( $product_id, 'ovabrw_service_id', true );
            $service_adult_price    = get_post_meta( $product_id, 'ovabrw_service_adult_price', true );
            $service_child_price    = get_post_meta( $product_id, 'ovabrw_service_children_price', true );
            $service_baby_price     = get_post_meta( $product_id, 'ovabrw_service_baby_price', true );
            $service_duration_type  = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

            foreach ( $services as $ovabrw_s_id ) {
                if ( $ovabrw_s_id && $service_ids && is_array( $service_ids ) ) {
                    foreach( $service_ids as $key_id => $service_id_arr ) {
                        $key = array_search( $ovabrw_s_id, $service_id_arr );

                        if ( !is_bool( $key ) ) {
                            $adult_price = 0;
                            if ( ovabrw_check_array( $service_adult_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_adult_price[$key_id], $key ) ) {
                                    $adult_price = $service_adult_price[$key_id][$key];
                                }
                            }

                            $child_price = 0;
                            if ( ovabrw_check_array( $service_child_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_child_price[$key_id], $key ) ) {
                                    $child_price = $service_child_price[$key_id][$key];
                                }
                            }

                            $baby_price = 0;
                            if ( ovabrw_check_array( $service_baby_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_baby_price[$key_id], $key ) ) {
                                    $baby_price = $service_baby_price[$key_id][$key];
                                }
                            }

                            $duration_type = 'person';
                            if ( ovabrw_check_array( $service_duration_type, $key_id ) ) {
                                if ( ovabrw_check_array( $service_duration_type[$key_id], $key ) ) {
                                    $duration_type = $service_duration_type[$key_id][$key];
                                }
                            }

                            if ( 'person' === $duration_type ) {
                                $total_services += floatval( $adult_price*$numberof_adults ) + floatval( $child_price*$numberof_children ) + floatval( $baby_price*$numberof_babies );
                            } else {
                                $total_services += (float)$adult_price + (float)$child_price + (float)$baby_price;
                            }
                        }
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_total_services', $total_services, $product_id, $services, $numberof_adults, $numberof_children, $numberof_babies );
    }
}

/* Get Price in Schedule */
if ( ! function_exists( 'ovabrw_get_price_from_schedule' ) ) {
    function ovabrw_get_price_from_schedule( $product_id = false, $weekday = '', $time_from = '' ) {
        if ( !$product_id || !$weekday || !$time_from ) return false;

        $price_guests = array(
            'adults_price'      => 0,
            'childrens_price'   => 0,
            'babies_price'      => 0,
            'type_price'        => 'person'
        );

        $ovabrw_schedule_time           = get_post_meta( $product_id, 'ovabrw_schedule_time', true );
        $ovabrw_schedule_adult_price    = get_post_meta( $product_id, 'ovabrw_schedule_adult_price', true );
        $ovabrw_schedule_child_price    = get_post_meta( $product_id, 'ovabrw_schedule_children_price', true );
        $ovabrw_schedule_baby_price     = get_post_meta( $product_id, 'ovabrw_schedule_baby_price', true );
        $ovabrw_schedule_type           = get_post_meta( $product_id, 'ovabrw_schedule_type', true );

        if ( ovabrw_check_array( $ovabrw_schedule_time, $weekday ) ) {
            $schedule_time          = $ovabrw_schedule_time[$weekday];
            $schedule_adult_price   = isset( $ovabrw_schedule_adult_price[$weekday] ) ? $ovabrw_schedule_adult_price[$weekday] : array();
            $schedule_child_price   = isset( $ovabrw_schedule_child_price[$weekday] ) ? $ovabrw_schedule_child_price[$weekday] : array();
            $schedule_baby_price    = isset( $ovabrw_schedule_baby_price[$weekday] ) ? $ovabrw_schedule_baby_price[$weekday] : array();
            $schedule_type          = isset( $ovabrw_schedule_type[$weekday] ) ? $ovabrw_schedule_type[$weekday] : array();

            if ( ! empty( $schedule_time ) && is_array( $schedule_time ) ) {
                foreach ( $schedule_time as $k => $time ) {
                    if ( $time === $time_from ) {
                        $price_guests['adults_price']       = isset( $schedule_adult_price[$k] ) ? floatval( $schedule_adult_price[$k] ) : 0;
                        $price_guests['childrens_price']    = isset( $schedule_child_price[$k] ) ? floatval( $schedule_child_price[$k] ) : 0;
                        $price_guests['babies_price']       = isset( $schedule_baby_price[$k] ) ? floatval( $schedule_baby_price[$k] ) : 0;
                        $price_guests['type_price']         = isset( $schedule_type[$k] ) ? $schedule_type[$k] : 'person';

                        break;
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_get_price_from_schedule', $price_guests, $product_id, $weekday, $time_from );
    }
}

// Get Price Custom Checkout Fields
if ( ! function_exists( 'ovabrw_get_price_ckf' ) ) {
    function ovabrw_get_price_ckf( $product_id, $args_ckf ) {
        if ( ! $product_id || empty( $args_ckf ) ) return 0;

        $price = 0;
        $list_custom_ckf = ovabrw_get_list_field_checkout( $product_id );

        foreach ( $args_ckf as $key => $val ) {
            if ( isset( $list_custom_ckf[$key] ) && ! empty( $list_custom_ckf[$key] ) ) {
                $type = $list_custom_ckf[$key]['type'];

                if ( ! $type || ! in_array( $type, array( 'radio', 'select', 'checkbox' ) ) ) continue;

                // Type: Radio
                if ( 'radio' === $type && isset( $list_custom_ckf[$key]['ova_radio_values'] ) && $list_custom_ckf[$key]['ova_radio_values'] ) {
                    foreach ( $list_custom_ckf[$key]['ova_radio_values'] as $k => $v ) {
                        if ( $val === $v && isset( $list_custom_ckf[$key]['ova_radio_prices'][$k] ) ) {
                            $price += floatval( $list_custom_ckf[$key]['ova_radio_prices'][$k] );

                            break;
                        }
                    }
                }
                // End

                // Type: Select
                if ( 'select' === $type && isset( $list_custom_ckf[$key]['ova_options_key'] ) && $list_custom_ckf[$key]['ova_options_key'] ) {
                    foreach ( $list_custom_ckf[$key]['ova_options_key'] as $k => $v ) {
                        if ( $val === $v && isset( $list_custom_ckf[$key]['ova_options_price'][$k] ) ) {
                            $price += floatval( $list_custom_ckf[$key]['ova_options_price'][$k] );

                            break;
                        }
                    }
                }
                // End

                // Type: Checkbox
                if ( 'checkbox' === $type && ! empty( $val ) && is_array( $val ) ) {
                    $checkbox_key   = isset( $list_custom_ckf[$key]['ova_checkbox_key'] ) ? $list_custom_ckf[$key]['ova_checkbox_key'] : '';
                    $checkbox_price = isset( $list_custom_ckf[$key]['ova_checkbox_price'] ) ? $list_custom_ckf[$key]['ova_checkbox_price'] : '';
                    if ( ! empty( $checkbox_key ) && ! empty( $checkbox_price ) ) {
                        foreach ( $val as $val_cb ) {
                            $key_cb = array_search( $val_cb, $checkbox_key );

                            if ( ! is_bool( $key_cb ) ) {
                                if ( ovabrw_check_array( $checkbox_price, $key_cb ) ) {
                                    $price += floatval( $checkbox_price[$key_cb] );
                                }
                            }
                        }
                    }
                }
                // End
            }
        }

        return apply_filters( 'ovabrw_get_price_ckf', (float)$price, $product_id, $args_ckf );
    }
}