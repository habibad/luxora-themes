<?php
/**
 * Site header.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'min-h-screen flex flex-col bg-background text-foreground antialiased' ); ?>>
<?php wp_body_open(); ?>

<a class="sr-only focus:not-sr-only focus:absolute focus:z-[100] focus:top-2 focus:left-2 focus:bg-ink focus:text-cream focus:px-4 focus:py-2" href="#luxora-main"><?php esc_html_e( 'Skip to content', 'luxora' ); ?></a>

<div class="luxora-site min-h-screen flex flex-col">

	<?php $announcement = luxora_opt( 'luxora_announcement' ); ?>
	<?php if ( $announcement ) : ?>
		<div class="bg-ink text-cream text-[11px] tracking-[0.24em] uppercase py-2.5 text-center luxora-announcement-text">
			<?php echo esc_html( $announcement ); ?>
		</div>
	<?php endif; ?>

	<header id="luxora-header" class="sticky top-0 z-50 transition-all duration-500 bg-background" data-header>
		<div class="container-luxe">
			<div class="grid grid-cols-[auto_1fr_auto] items-center h-20 gap-6">

				<button type="button" class="lg:hidden -ml-2 p-2" aria-label="<?php esc_attr_e( 'Open menu', 'luxora' ); ?>" data-drawer-open aria-controls="luxora-drawer" aria-expanded="false">
					<?php echo luxora_icon( 'menu', 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>

				<nav class="hidden lg:flex items-center gap-8 text-[12px] uppercase tracking-[0.18em] font-medium" aria-label="<?php esc_attr_e( 'Primary', 'luxora' ); ?>">
					<?php
					luxora_flat_menu(
						'primary',
						'link-underline hover:text-ink/80',
						'<a class="link-underline hover:text-ink/80" href="' . esc_url( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop' ) ) . '">' . esc_html__( 'Shop', 'luxora' ) . '</a>'
					);
					?>
				</nav>

				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="justify-self-center font-display text-2xl md:text-3xl tracking-[0.32em] font-medium" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<span class="luxora-site-title">' . esc_html( strtoupper( get_bloginfo( 'name' ) ) ) . '</span>';
					}
					?>
				</a>

				<div class="flex items-center gap-1 md:gap-2 justify-end">
					<button type="button" class="p-2 hover:text-gold transition" aria-label="<?php esc_attr_e( 'Search', 'luxora' ); ?>" data-search-open aria-controls="luxora-search" aria-expanded="false">
						<?php echo luxora_icon( 'search', 'h-4.5 w-4.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>

					<a href="<?php echo esc_url( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'dashboard' ) : home_url( '/my-account' ) ); ?>" class="p-2 hover:text-gold transition hidden md:inline-flex" aria-label="<?php esc_attr_e( 'Account', 'luxora' ); ?>">
						<?php echo luxora_icon( 'user', 'h-4.5 w-4.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>

					<a href="<?php echo esc_url( home_url( '/wishlist' ) ); ?>" class="p-2 hover:text-gold transition relative" aria-label="<?php esc_attr_e( 'Wishlist', 'luxora' ); ?>">
						<?php echo luxora_icon( 'heart', 'h-4.5 w-4.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="luxora-wishlist-count absolute -top-0 -right-0 bg-gold text-ink text-[10px] rounded-full h-4 w-4 grid place-items-center font-medium <?php echo count( luxora_get_wishlist() ) ? '' : 'hidden'; ?>"><?php echo esc_html( count( luxora_get_wishlist() ) ); ?></span>
					</a>

					<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart' ) ); ?>" class="p-2 hover:text-gold transition relative" aria-label="<?php esc_attr_e( 'Cart', 'luxora' ); ?>" data-cart-toggle aria-controls="luxora-mini-cart">
						<?php echo luxora_icon( 'bag', 'h-4.5 w-4.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php luxora_cart_count_bubble(); ?>
					</a>

					<?php $currency = luxora_opt( 'luxora_currency_label' ); ?>
					<?php if ( $currency ) : ?>
						<div class="hidden md:flex items-center gap-1 ml-2 text-[11px] uppercase tracking-[0.18em] text-muted-foreground">
							<?php echo esc_html( $currency ); ?> <?php echo luxora_icon( 'chevron-down', 'h-3 w-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if ( has_nav_menu( 'mega' ) ) : ?>
			<div class="hidden lg:block border-t border-border/60">
				<div class="container-luxe">
					<nav class="flex items-center justify-center gap-10 h-11 text-[11px] uppercase tracking-[0.24em] text-muted-foreground" aria-label="<?php esc_attr_e( 'Categories', 'luxora' ); ?>">
						<?php luxora_flat_menu( 'mega', 'hover:text-ink' ); ?>
					</nav>
				</div>
			</div>
		<?php elseif ( function_exists( 'wc_get_product_category_list' ) || taxonomy_exists( 'product_cat' ) ) : ?>
			<?php
			$cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 8,
					'parent'     => 0,
				)
			);
			?>
			<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
				<div class="hidden lg:block border-t border-border/60">
					<div class="container-luxe">
						<nav class="flex items-center justify-center gap-10 h-11 text-[11px] uppercase tracking-[0.24em] text-muted-foreground" aria-label="<?php esc_attr_e( 'Categories', 'luxora' ); ?>">
							<?php foreach ( $cats as $cat ) : ?>
								<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="hover:text-ink"><?php echo esc_html( $cat->name ); ?></a>
							<?php endforeach; ?>
						</nav>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</header>

	<?php
	// Mobile drawer.
	get_template_part( 'template-parts/header', 'drawer' );
	// Search overlay.
	get_template_part( 'template-parts/header', 'search' );
	// Mini cart drawer.
	get_template_part( 'template-parts/header', 'minicart' );
	?>

	<main id="luxora-main" class="flex-1" tabindex="-1">
