<?php
/**
 * Slide-in mini cart drawer.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}
?>
<div class="fixed inset-0 z-[70] hidden" id="luxora-mini-cart" data-minicart aria-hidden="true">
	<div class="absolute inset-0 bg-ink/40" data-minicart-close></div>
	<div class="absolute inset-y-0 right-0 w-[90%] max-w-md bg-background flex flex-col p-5" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Shopping bag', 'luxora' ); ?>">
		<div class="flex items-center justify-between p-6 border-b border-border">
			<h2 class="font-display text-2xl"><?php esc_html_e( 'Your bag', 'luxora' ); ?></h2>
			<button type="button" data-minicart-close aria-label="<?php esc_attr_e( 'Close', 'luxora' ); ?>">
				<?php echo luxora_icon( 'x', 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>
		<div class="luxora-mini-cart-inner flex-1 overflow-y-auto">
			<?php woocommerce_mini_cart(); ?>
		</div>
	</div>
</div>
