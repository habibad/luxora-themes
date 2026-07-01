<?php
/**
 * Template Name: New Arrivals
 *
 * Mirrors new-arrivals.tsx — beige hero + newest product grid.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ids = luxora_woo_active() ? luxora_query_products( 'new', 9 ) : array();
if ( ! $ids && luxora_woo_active() ) {
	$ids = luxora_query_products( 'recent', 9 );
}
?>
<section class="bg-beige">
	<div class="container-luxe py-20 md:py-28 text-center" data-reveal>
		<p class="eyebrow mb-5"><?php esc_html_e( 'Just Landed', 'luxora' ); ?></p>
		<h1 class="font-display text-5xl md:text-7xl"><?php esc_html_e( 'New Arrivals', 'luxora' ); ?></h1>
		<p class="mt-6 font-serif text-lg md:text-xl text-ink/70 max-w-xl mx-auto">
			<?php esc_html_e( 'The latest additions to the maison — fresh from the atelier.', 'luxora' ); ?>
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
