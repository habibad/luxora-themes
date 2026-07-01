<?php
/**
 * Custom search form.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$luxora_sf_id = 'luxora-search-' . wp_unique_id();
?>
<form role="search" method="get" class="luxora-searchform flex items-center border-b border-ink/30 focus-within:border-ink transition" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $luxora_sf_id ); ?>" class="sr-only"><?php esc_html_e( 'Search for:', 'luxora' ); ?></label>
	<input
		type="search"
		id="<?php echo esc_attr( $luxora_sf_id ); ?>"
		class="flex-1 bg-transparent outline-none py-3 text-base placeholder:text-ink/40"
		placeholder="<?php esc_attr_e( 'Search…', 'luxora' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	/>
	<button type="submit" class="p-2 text-ink/70 hover:text-ink transition" aria-label="<?php esc_attr_e( 'Submit search', 'luxora' ); ?>">
		<?php echo luxora_icon( 'search', 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</button>
</form>
