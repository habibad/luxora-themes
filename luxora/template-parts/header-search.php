<?php
/**
 * Search overlay (triggered by the header search icon).
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$luxora_search_action = function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0
	? get_permalink( wc_get_page_id( 'shop' ) )
	: home_url( '/' );
?>
<div class="fixed inset-0 z-[70] hidden" id="luxora-search" data-search aria-hidden="true">
	<div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" data-search-close></div>
	<div class="absolute top-0 left-0 right-0 bg-background animate-slide-in-right" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'luxora' ); ?>">
		<div class="container-luxe py-10">
			<div class="flex items-center justify-between mb-8">
				<p class="eyebrow"><?php esc_html_e( 'Search the maison', 'luxora' ); ?></p>
				<button type="button" data-search-close aria-label="<?php esc_attr_e( 'Close search', 'luxora' ); ?>">
					<?php echo luxora_icon( 'x', 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			</div>
			<form role="search" method="get" action="<?php echo esc_url( $luxora_search_action ); ?>" class="flex items-center gap-4 border-b border-ink/30 focus-within:border-ink">
				<?php echo luxora_icon( 'search', 'h-5 w-5 text-muted-foreground' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<label class="sr-only" for="luxora-search-field"><?php esc_html_e( 'Search for products', 'luxora' ); ?></label>
				<input id="luxora-search-field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'What are you looking for?', 'luxora' ); ?>" class="flex-1 bg-transparent outline-none py-4 text-xl md:text-2xl font-display placeholder:text-ink/30" autocomplete="off" data-search-input />
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<input type="hidden" name="post_type" value="product" />
				<?php endif; ?>
				<button type="submit" class="btn-luxe"><?php esc_html_e( 'Search', 'luxora' ); ?></button>
			</form>
		</div>
	</div>
</div>
