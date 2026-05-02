<?php
// Check Product
function ovabrw_check_product() {
    $all_rooms  = get_all_rooms();
    $product_id = isset( $_GET['product_id'] ) ? sanitize_text_field( $_GET['product_id'] ) : '';
    ?>
    <div class="wrap"><div class="booking_filter">
        <form 
            id="booking-filter" 
            method="GET" 
            action="<?php echo admin_url('/edit.php?post_type=product&page=ovabrw-check-product'); ?>">
            <h2><?php esc_html_e( 'Check Product', 'ova-brw' ); ?></h2>
    		<select name="product_id">
    			<option value="" <?php selected( '', $product_id, 'selected'); ?>>
                    <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
                </option>
    			<?php 
    				if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
    					<option value="<?php the_id(); ?>" <?php selected( get_the_id(), $product_id, 'selected'); ?>>
                            <?php the_title(); ?>
                        </option>
    				<?php endwhile;endif;wp_reset_postdata();
    			?>
    		</select>
			<button type="submit" class="button">
                <?php esc_html_e( 'Display Schedule', 'ova-brw' ); ?>
            </button>
            <div class="total_vehicle">
                <?php esc_html_e( 'Total Available','ova-brw' ); ?>:
                <?php echo get_post_meta( $product_id, 'ovabrw_stock_quantity', true ); ?>
            </div>
            <input type="hidden" name="post_type" value="product" />
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <?php
            if ( $product_id ):
                $statuses   = brw_list_order_status();
                $order_date = get_order_rent_time( $product_id, $statuses );

                wp_localize_script( 'calendar_booking', 'order_time', $order_date );

                $toolbar_nav = apply_filters( 'ovabrw_ft_calendar_show_nav', array(
                    'dayGridMonth',
                    'timeGridWeek',
                    'timeGridDay',
                    'listWeek',
                ));

                $nav            = implode(',', array_filter( $toolbar_nav ) );
                $lang           = ovabrw_get_setting( get_option( 'ova_brw_calendar_language_general', 'en' ) ); 
                $default_view   = apply_filters( 'ovabrw_ft_calendar_default_view', 'dayGridMonth' );

                ?>
                
                <div class="wrap_calendar">
                    <div id="<?php echo 'calendar'.$product_id ?>" 
                        data-id="<?php echo 'calendar'.$product_id ?>" 
                        class="ovabrw__product_calendar" 
                        data-lang="<?php echo esc_attr( $lang ); ?>" 
                        data-nav="<?php echo esc_attr( $nav ); ?>" 
                        data-default_view="<?php echo esc_attr( $default_view ); ?>" 
                        data_event_number="<?php echo apply_filters( 'ovabrw_event_number_cell', 2 ); ?>">
                        <ul class="intruction">
                            <li>
                                <span class="available"></span>
                                <span><?php esc_html_e( 'Available','ova-brw' ) ?></span>     
                            </li>
                            <li>
                                <span class="unavailable" style="background: <?php echo esc_attr( apply_filters( 'ovabrw_ft_background_color_event', '#FF1A1A' ) ); ?>" ></span>
                                <span><?php esc_html_e( 'Unavailable', 'ova-brw' ) ?></span>      
                            </li>
                        </ul>

                    </div>
                </div>
            <?php endif; ?>
        </form>
        <div style="clear:both;"></div><br>
        <form id="available-vehicle" method="GET" action="<?php echo admin_url('/edit.php?post_type=product&page=check-product'); ?>">
            <?php
                $date_format    = ovabrw_get_date_format();
                $from_day       = isset( $_GET['from_day'] ) ? sanitize_text_field( $_GET['from_day'] ) : '';
                $to_day         = isset( $_GET['to_day'] ) ? sanitize_text_field( $_GET['to_day'] ) : '';
                $from_day_new   = $from_day ? strtotime( $from_day ) : '';
                $to_day_new     = $to_day ? strtotime( $to_day ) : '';
                $quantity       = 1;
                $data_available = array();
                $qty_available  = 0;

                if ( $product_id ) {
                    // Set Pick-up, Drop-off Date again
                    $new_input_date   = ovabrw_new_input_date( $product_id, $from_day_new, $to_day_new, $date_format );
                    $pickup_date_new  = $new_input_date['pickup_date_new'];
                    $pickoff_date_new = $new_input_date['pickoff_date_new'];

                    if ( ovabrw_qty_by_guests( $product_id ) ) {
                        if ( ! $pickoff_date_new ) $pickoff_date_new = $pickup_date_new;

                        $qty_available = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );

                        // Unavailable Time (UT)
                        $validate_ut = ovabrw_validate_unavailable_time( $product_id, $pickup_date_new, $pickoff_date_new, 'search' );
                        if ( $validate_ut ) return $qty_available = 0;

                        // Disable week day
                        $validate_dwd = ovabrw_validate_disable_week_day( $product_id, $pickup_date_new, $pickoff_date_new, 'search' );
                        if ( $validate_dwd ) return $qty_available = 0;

                        // Get Guests in Order
                        $guests_in_order = ovabrw_get_guests_in_order( $product_id, $pickup_date_new );

                        // Get Guests available
                        $guests_available = ovabrw_get_guests_available( $product_id, [], [], $guests_in_order, 'search' );

                        if ( ! $guests_available ) {
                            $qty_available = 0;
                        }
                    } else {
                        // Check Count Product in Order
                        $check_quantity_order = ovabrw_quantity_available_in_order( $product_id, $from_day_new, $to_day_new );

                        $stock_quantity = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );
                        $qty_available  = $stock_quantity - $check_quantity_order;

                        // Check Check Unavailable
                        $check_unavailable = ovabrw_check_unavailable( $product_id, $from_day_new, $to_day_new );

                        if ( $check_unavailable ) {
                            $qty_available = 0;
                        }
                    }   
                }
            ?>
            <h3>
                <?php esc_html_e( 'The Available','ova-brw' ); ?>
            </h3>

            <input 
                type="text" 
                name="from_day" 
                value="<?php echo $from_day; ?>" 
                placeholder="<?php esc_html_e('From date', 'ova-brw'); ?>" 
                class="ovabrw_datetimepicker ovabrw_start_date" 
                autocomplete="off"/>
            <?php esc_html_e('to','ova-brw'); ?>
            <input 
                type="text" 
                name="to_day" 
                value="<?php echo $to_day; ?>" 
                placeholder="<?php esc_html_e('To date', 'ova-brw'); ?>"
                class="ovabrw_datetimepicker ovabrw_end_date" 
                autocomplete="off" />
            <select name="product_id">
                <option value="" <?php selected( '', $product_id, 'selected'); ?>>
                    <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
                </option>
                <?php 
                    if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
                        <option value="<?php the_id(); ?>" <?php selected( get_the_id(), $product_id, 'selected'); ?>>
                            <?php the_title(); ?>
                        </option>
                    <?php endwhile;endif;wp_reset_postdata();
                ?>
            </select>
            <button type="submit" class="button"><?php esc_html_e( 'Search', 'ova-brw' ); ?></button>
            <?php if ( $qty_available && $qty_available > 0 ): ?>
                <table class="quantity_available">
                    <thead>
                        <tr>
                            <td>
                                <strong><?php esc_html_e( 'Stock Quantity', 'ova-brw' ); ?></strong>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;">
                                <?php echo esc_html( $qty_available ); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php else:
                esc_html_e( 'Not Found','ova-brw' );
            endif;
            ?>
            <input type="hidden" name="post_type" value="product" />
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        </form>
    </div>
<?php } ?>