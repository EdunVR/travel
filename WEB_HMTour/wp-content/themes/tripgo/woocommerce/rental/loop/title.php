<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$show_title = isset( $args['show_title'] ) ? $args['show_title'] : 'yes';

?>

<?php if($show_title == 'yes') { ?>
    <h2 class="ova-product-title">
        <?php echo get_the_title(); ?>
    </h2>
<?php } ?>