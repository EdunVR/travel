<?php
// Display Manage Booking
function ovabrw_display_order() {
	//Create an instance of our package class...
    $manage_booking = new List_Booking();
    //Fetch, prepare, sort, and filter our data...
    $manage_booking->prepare_items();

    $products       = get_all_rooms();
    $order_id       = ovabrw_get_meta_data( 'order_id', $_GET );
    $customer_name  = ovabrw_get_meta_data( 'customer_name', $_GET );
    $checkin_date   = ovabrw_get_meta_data( 'checkin_date', $_GET );
    $checkout_date  = ovabrw_get_meta_data( 'checkout_date', $_GET );
    $product_id     = ovabrw_get_meta_data( 'product_id', $_GET );
    $order_status   = ovabrw_get_meta_data( 'order_status', $_GET );


    // Admin manage order show columns
    $id         = get_option( 'admin_manage_order_show_id', 1 );
    $customer   = get_option( 'admin_manage_order_show_customer', 2 );
    $time       = get_option( 'admin_manage_order_show_time', 3 );
    $deposit    = get_option( 'admin_manage_order_show_deposit', 4 );
    $insurance  = get_option( 'admin_manage_order_show_insurance', 5 );
    $product    = get_option( 'admin_manage_order_show_product', 6 );
    $status     = get_option( 'admin_manage_order_show_order_status', 7 );

    ?>
    <div class="wrap">
        <form id="booking-filter" method="GET" action="<?php echo admin_url('/edit.php?post_type=product&page=ovabrw-manage-order'); ?>" autocomplete="off">
        	<h2><?php esc_html_e( 'Manage Order', 'ova-brw' ); ?></h2>
        	<div class="booking_filter">
            <?php if ( $id ): ?>
                <input
                    type="text"
                    name="order_id"
                    value="<?php echo esc_attr( $order_id ); ?>"
                    placeholder="<?php esc_html_e( 'Order ID', 'ova-brw' ); ?>"
                    autocomplete="off"
                />
            <?php endif; ?>
            <?php if ( $customer ): ?>
                <input
                    type="text"
                    name="customer_name"
                    value="<?php echo esc_attr( $customer_name ); ?>"
                    placeholder="<?php esc_html_e( 'Customer Name', 'ova-brw' ); ?>"
                    autocomplete="off"
                />
            <?php endif; ?>
            <?php if ( $time ): ?>
                <input
                    type="text"
                    name="checkin_date"
                    value="<?php echo esc_attr( $checkin_date ); ?>"
                    placeholder="<?php esc_html_e( 'Check-in Date', 'ova-brw' ); ?>"
                    class="ovabrw_datetimepicker ova-date-search date_book"
                    autocomplete="off"
                />
                <input
                    type="text"
                    name="checkout_date"
                    value="<?php echo esc_attr( $checkout_date ); ?>"
                    placeholder="<?php esc_html_e( 'Check-out Date', 'ova-brw' ); ?>"
                    class="ovabrw_datetimepicker ova-date-search date_book"
                    autocomplete="off"
                />
            <?php endif; ?>
            <?php if ( $product ): ?>
        		<select name="product_id">
        			<option value="">
                        <?php esc_html_e( 'Choose Product', 'ova-brw' ); ?>
                    </option>
        			<?php 
        				if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post(); ?>
        					<option value="<?php the_id(); ?>"<?php selected( get_the_id(), $product_id ); ?>>
                                <?php the_title(); ?>
                            </option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        		</select>
            <?php endif; ?>
            <?php if ( $status ): ?>
                <select name="order_status" >
                    <option value="">
                        <?php esc_html_e( 'Order Status', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-completed"<?php selected( 'wc-completed', $order_status ); ?>>
                        <?php esc_html_e( 'Completed', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-processing"<?php selected( 'wc-processing', $order_status ); ?>>
                        <?php esc_html_e( 'Processing', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-pending"<?php selected( 'wc-pending', $order_status ); ?>>
                        <?php esc_html_e( 'Pending payment', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-on-hold"<?php selected( 'wc-on-hold', $order_status ); ?>>
                        <?php esc_html_e( 'On hold', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-cancelled"<?php selected( 'wc-cancelled', $order_status ); ?>>
                        <?php esc_html_e( 'Cancel', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-closed"<?php selected( 'wc-closed', $order_status ); ?>>
                        <?php esc_html_e( 'Closed', 'ova-brw' ); ?>
                    </option>
                </select>
            <?php endif; ?>
    			<button type="submit" class="button">
                    <?php esc_html_e( 'Filter', 'ova-brw' ); ?>
                </button>
        	</div>
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="post_type" value="product" />
            <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
            <!-- Now we can render the completed list table -->
            <?php $manage_booking->display() ?>
        </form>
    </div>
<?php }