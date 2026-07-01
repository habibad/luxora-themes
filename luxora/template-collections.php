<?php
/**
 * Template Name: Collections
 *
 * Mirrors collections.tsx — product category grid with editorial overlays.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$terms = array();
if ( luxora_woo_active() ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'exclude'    => array( get_option( 'default_product_cat' ) ),
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 9,
		)
	);
	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}
}
?>
<section class="container-luxe py-20 md:py-28 text-center" data-reveal>
	<p class="eyebrow mb-5"><?php esc_html_e( 'Maison Edits', 'luxora' ); ?></p>
	<h1 class="font-display text-5xl md:text-7xl"><?php esc_html_e( 'Collections', 'luxora' ); ?></h1>
	<p class="mt-6 font-serif text-lg md:text-xl text-muted-foreground max-w-xl mx-auto">
		<?php esc_html_e( 'Considered families of bags — each with a distinct silhouette and story.', 'luxora' ); ?>
	</p>
</section>

<?php if ( $terms ) : ?>
	<div class="container-luxe pb-24 grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8" data-reveal-stagger>
		<?php
		foreach ( $terms as $i => $term ) :
			$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			$img      = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'luxora-portrait' ) : wc_placeholder_img_src( 'luxora-portrait' );
			$feature  = ( 0 === $i % 5 );
			?>
			<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="group relative overflow-hidden bg-muted <?php echo $feature ? 'aspect-[3/4] md:row-span-2 md:aspect-[3/5]' : 'aspect-[4/5]'; ?>" data-reveal-item>
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-[1200ms] group-hover:scale-110" />
				<div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/10 to-transparent"></div>
				<div class="absolute bottom-0 left-0 right-0 p-8 text-cream">
					<p class="eyebrow text-cream/70 mb-3">
						<?php
						/* translators: %d: number of products */
						printf( esc_html( _n( '%d piece', '%d pieces', $term->count, 'luxora' ) ), absint( $term->count ) );
						?>
					</p>
					<h2 class="font-display text-3xl md:text-4xl"><?php echo esc_html( $term->name ); ?></h2>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<div class="container-luxe pb-24 text-center">
		<p class="font-serif text-lg text-muted-foreground mb-6"><?php esc_html_e( 'Collections will appear here once product categories are added.', 'luxora' ); ?></p>
		<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Browse the shop', 'luxora' ); ?></a>
	</div>
<?php endif; ?>

<?php
get_footer();
