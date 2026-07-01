<?php
/**
 * Template Name: Best Sellers
 *
 * Mirrors best-sellers.tsx — dark hero + best-selling product grid.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ids = luxora_woo_active() ? luxora_query_products( 'best', 9 ) : array();
?>
<section class="bg-ink text-cream">
	<div class="container-luxe py-20 md:py-28 text-center" data-reveal>
		<p class="eyebrow text-cream/60 mb-5"><?php esc_html_e( 'Iconic', 'luxora' ); ?></p>
		<h1 class="font-display text-5xl md:text-7xl"><?php esc_html_e( 'Best Sellers', 'luxora' ); ?></h1>
		<p class="mt-6 font-serif text-lg md:text-xl text-cream/70 max-w-xl mx-auto">
			<?php esc_html_e( 'The pieces our clients return to, season after season.', 'luxora' ); ?>
		</p>
	</div>
</section>

<?php if ( $ids ) : ?>
	<div class="container-luxe py-20 grid grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>
		<?php
		foreach ( $ids as $pid ) :
			echo '<div data-reveal-item>';
			luxora_render_product_card( $pid );
			echo '</div>';
		endforeach;
		?>
	</div>
<?php else : ?>
	<div class="container-luxe py-24 text-center">
		<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Browse the shop', 'luxora' ); ?></a>
	</div>
<?php endif; ?>

<?php
get_footer();
