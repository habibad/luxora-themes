<?php
/**
 * Template Name: Wishlist
 *
 * Mirrors wishlist.tsx — renders saved products from the Luxora wishlist store.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ids   = function_exists( 'luxora_get_wishlist' ) ? luxora_get_wishlist() : array();
$count = count( $ids );
?>
<section class="container-luxe py-20 md:py-24 border-b border-border" data-reveal>
	<p class="eyebrow mb-4"><?php esc_html_e( 'Saved for later', 'luxora' ); ?></p>
	<h1 class="font-display text-5xl md:text-6xl"><?php esc_html_e( 'My wishlist', 'luxora' ); ?></h1>
	<p class="mt-4 text-muted-foreground luxora-wishlist-count-text">
		<?php
		/* translators: %d: number of saved pieces */
		printf( esc_html( _n( '%d piece saved', '%d pieces saved', $count, 'luxora' ) ), absint( $count ) );
		?>
	</p>
</section>

<?php if ( $count && luxora_woo_active() ) : ?>
	<div class="container-luxe py-16 grid grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-12 md:gap-x-8 luxora-wishlist-grid" data-reveal-stagger>
		<?php
		foreach ( $ids as $wid ) :
			$wproduct = wc_get_product( $wid );
			if ( ! $wproduct || 'publish' !== get_post_status( $wid ) ) {
				continue;
			}
			echo '<div data-reveal-item data-wishlist-card="' . esc_attr( $wid ) . '">';
			luxora_render_product_card( $wproduct );
			echo '</div>';
		endforeach;
		?>
	</div>
<?php else : ?>
	<div class="container-luxe py-24 text-center luxora-wishlist-empty">
		<p class="font-display text-2xl mb-6"><?php esc_html_e( 'Your wishlist is empty.', 'luxora' ); ?></p>
		<p class="font-serif text-lg text-muted-foreground max-w-md mx-auto mb-8"><?php esc_html_e( 'Tap the heart on any piece to save it here for later.', 'luxora' ); ?></p>
		<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Discover the edit', 'luxora' ); ?></a>
	</div>
<?php endif; ?>

<?php
get_footer();
