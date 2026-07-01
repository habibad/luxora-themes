<?php
/**
 * Empty state.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="text-center py-20" data-reveal>
	<h2 class="font-display text-3xl md:text-4xl"><?php esc_html_e( 'Nothing here yet.', 'luxora' ); ?></h2>
	<p class="mt-4 font-serif text-lg text-muted-foreground max-w-md mx-auto">
		<?php esc_html_e( 'We could not find anything to show. Try a different search or explore the edit.', 'luxora' ); ?>
	</p>
	<div class="mt-8 max-w-md mx-auto">
		<?php get_search_form(); ?>
	</div>
</div>
