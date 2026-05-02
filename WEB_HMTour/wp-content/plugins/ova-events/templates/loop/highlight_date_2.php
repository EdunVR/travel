<?php if ( !defined( 'ABSPATH' ) ) exit();

if( isset( $args['id'] ) ){
	$id = $args['id'];
}else{
	$id = get_the_id();	
}

$ovaev_start_date = get_post_meta( $id, 'ovaev_start_date_time', true );
$date_event    = $ovaev_start_date != '' ? date_i18n('d', $ovaev_start_date ) : '';
$month_event_M = $ovaev_start_date != '' ? date_i18n('M', $ovaev_start_date ) : '';
$year_event    = $ovaev_start_date != '' ? date_i18n('Y', $ovaev_start_date ) : '';

if( $ovaev_start_date != '' ){ ?>

<div class="date-event">

	<span class="date second_font">
		<?php echo esc_html($date_event);?>
	</span>

	<span class="month-year second_font">

		<span class="month">
			<?php echo esc_html($month_event_M);?>
		</span>
		
		<span class="year">
			<?php echo esc_html($year_event);?>
		</span>

	</span>

</div>

<?php } ?>