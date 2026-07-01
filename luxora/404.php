<?php
/**
 * 404 — not found.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="container-luxe py-24 md:py-40 text-center" data-reveal>
	<p class="eyebrow mb-6"><?php esc_html_e( 'Error 404', 'luxora' ); ?></p>
	<h1 class="font-display text-6xl md:text-8xl tracking-tight"><?php esc_html_e( 'Lost in the maison.', 'luxora' ); ?></h1>
	<p class="mt-6 font-serif text-lg md:text-xl text-muted-foreground max-w-md mx-auto">
		<?php esc_html_e( 'The page you are looking for has been moved, renamed, or never existed.', 'luxora' ); ?>
	</p>
	<div class="mt-10 flex flex-wrap gap-4 justify-center">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-luxe"><?php esc_html_e( 'Back home', 'luxora' ); ?></a>
		<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe-ghost"><?php esc_html_e( 'Shop the edit', 'luxora' ); ?></a>
	</div>
	<div class="mt-14 max-w-md mx-auto">
		<?php get_search_form(); ?>
	</div>
</section>
<?php
get_footer();
