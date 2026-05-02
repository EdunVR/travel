<?php

// Send mail reminder
function ovabrw_mail_remind_event_time( $order, $customer_mail, $product_name, $product_id, $ovabrw_pickup_date  ) {
	$subject = ovabrw_get_setting( get_option( 'reminder_mail_subject', esc_html__( "Remind Check-in Date", 'eventlist') ) );

	$body = apply_filters( 'ovabrw_reminder_content_mail', ovabrw_get_setting( get_option( 'reminder_mail_content', esc_html__( 'You booked the tour: [product-name] at [check-in]', 'ova-brw' ) ) ), $order );

	$body = str_replace('[product-name]', '<a href="'.get_permalink($product_id).'" target="_blank">'.$product_name.'</a>', $body);
	$body = str_replace('[check-in]', $ovabrw_pickup_date, $body);


	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=".get_bloginfo( 'charset' )."\r\n";

	add_filter( 'wp_mail_from', 'ovabrw_mail_sendfrom_remind_time' );
	add_filter( 'wp_mail_from_name', 'ovabrw_mail_remind_time_from_name' );


	if ( wp_mail( $customer_mail, $subject, $body, $headers ) ){
		$result = true;
	} else {
		$result = false;
	}

	remove_filter( 'wp_mail_from', 'ovabrw_mail_sendfrom_remind_time');
	remove_filter( 'wp_mail_from_name', 'ovabrw_mail_remind_time_from_name' );

	return $result;
}

function ovabrw_mail_sendfrom_remind_time() {
	if ( ovabrw_get_setting( get_option( 'reminder_mail_from_email', get_option( 'admin_email' ) ) ) ) {
		return ovabrw_get_setting( get_option( 'reminder_mail_from_email', get_option( 'admin_email' ) ) ) ;
	} else {
		return get_option('admin_email');	
	}
}

function ovabrw_mail_remind_time_from_name() {
	return ovabrw_get_setting( get_option( 'reminder_mail_from_name', esc_html__( 'Remind Pick-up date', 'ova-brw' ) ) );
}
