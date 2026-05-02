<?php if ( !defined( 'ABSPATH' ) ) exit();

get_header( );


$id = get_the_ID();
$data_destination = $_GET;

$template = 'single-destination-template1.php';

$single_destination_template = get_theme_mod( 'single_destination_template', 'template1');
if ( $single_destination_template == 'template2' ) {
	$template = 'single-destination-template2.php';
}

if ( isset( $data_destination['destination_template'] ) && $data_destination['destination_template'] ) {
	if ( $data_destination['destination_template'] == 'template2' ) {
		$template = 'single-destination-template2.php';
	} elseif( $data_destination['destination_template'] == 'template1' ) {
	    $template = 'single-destination-template1.php';
	}
}


?>

<div class="row_site">
	<div class="container_site">
		<div class="ova_destination_single">
			<?php ovadestination_get_template( $template, array('id' => $id) ) ?>
		</div>
	</div>
</div>

<div class="ova_destination_single_description">
	<?php the_content(); ?>
</div>


<?php get_footer( );