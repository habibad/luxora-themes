<?php
/**
 * Blog sidebar.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<div class="luxora-sidebar space-y-10">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div>
