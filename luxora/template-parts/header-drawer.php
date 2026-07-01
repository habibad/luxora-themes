<?php
/**
 * Mobile navigation drawer (mirrors Header.tsx mobile drawer).
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="fixed inset-0 z-[60] lg:hidden hidden" id="luxora-drawer" data-drawer aria-hidden="true">
	<div class="absolute inset-0 bg-ink/40" data-drawer-close></div>
	<div class="absolute inset-y-0 left-0 w-[85%] max-w-sm bg-background p-6 animate-slide-in-right" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'luxora' ); ?>">
		<div class="flex items-center justify-between mb-8">
			<span class="font-display text-2xl tracking-[0.3em]"><?php echo esc_html( strtoupper( get_bloginfo( 'name' ) ) ); ?></span>
			<button type="button" data-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'luxora' ); ?>">
				<?php echo luxora_icon( 'x', 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>
		<nav class="flex flex-col gap-5 text-lg font-display" aria-label="<?php esc_attr_e( 'Mobile', 'luxora' ); ?>">
			<?php
			if ( has_nav_menu( 'mobile' ) ) {
				luxora_flat_menu( 'mobile', '' );
			} else {
				luxora_flat_menu(
					'primary',
					'',
					'<a href="' . esc_url( home_url( '/shop' ) ) . '">' . esc_html__( 'Shop', 'luxora' ) . '</a>'
				);
			}
			?>
			<a href="<?php echo esc_url( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'dashboard' ) : home_url( '/my-account' ) ); ?>"><?php esc_html_e( 'Account', 'luxora' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/track' ) ); ?>"><?php esc_html_e( 'Track Order', 'luxora' ); ?></a>
		</nav>
	</div>
</div>
