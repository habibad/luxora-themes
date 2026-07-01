<?php
/**
 * Shop / product archive — mirrors shop.tsx.
 * Override of woocommerce/archive-product.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

luxora_breadcrumbs();

/**
 * Hero.
 */
$total = wc_get_loop_prop( 'total' );
if ( is_product_category() ) {
	$term       = get_queried_object();
	$hero_eye   = __( 'The Atelier', 'luxora' );
	$hero_title = single_term_title( '', false );
	$hero_sub   = $term && $term->description ? $term->description : '';
} elseif ( is_product_tag() ) {
	$hero_eye   = __( 'The Atelier', 'luxora' );
	$hero_title = single_term_title( '', false );
	$hero_sub   = '';
} else {
	$hero_eye   = __( 'The Atelier', 'luxora' );
	$hero_title = __( 'Shop all bags', 'luxora' );
	$hero_sub   = '';
}
?>
<section class="bg-cream" data-reveal>
	<div class="container-luxe py-16 md:py-24 text-center">
		<p class="eyebrow mb-5"><?php echo esc_html( $hero_eye ); ?></p>
		<h1 class="font-display text-5xl md:text-7xl"><?php echo esc_html( $hero_title ); ?></h1>
		<p class="mt-6 font-serif text-lg md:text-xl text-muted-foreground max-w-xl mx-auto">
			<?php
			if ( $hero_sub ) {
				echo esc_html( $hero_sub );
			} else {
				/* translators: %d: product count */
				printf( esc_html( _n( '%d piece, hand-selected for the modern woman.', '%d pieces, hand-selected for the modern woman.', (int) $total, 'luxora' ) ), absint( $total ) );
			}
			?>
		</p>
	</div>
</section>

<div class="container-luxe py-12 md:py-20">
	<div class="grid lg:grid-cols-[260px_1fr] gap-12">

		<?php get_template_part( 'woocommerce/global/shop-filters' ); ?>

		<div class="luxora-shop-main">
			<div class="flex items-center justify-between mb-8 pb-5 border-b border-border">
				<button type="button" class="lg:hidden inline-flex items-center gap-2 text-sm" data-filter-open>
					<?php echo luxora_icon( 'sliders', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Filter', 'luxora' ); ?>
				</button>
				<p class="text-sm text-muted-foreground hidden lg:block">
					<?php
					/* translators: %d: product count */
					printf( esc_html__( 'Showing %d pieces', 'luxora' ), absint( $total ) );
					?>
				</p>
				<?php luxora_catalog_ordering(); ?>
			</div>

			<?php if ( woocommerce_product_loop() ) : ?>

				<?php woocommerce_product_loop_start(); ?>

				<?php
				while ( have_posts() ) :
					the_post();
					wc_get_template_part( 'content', 'product' );
				endwhile;
				?>

				<?php woocommerce_product_loop_end(); ?>

				<?php luxora_shop_load_more(); ?>

			<?php else : ?>
				<?php get_template_part( 'template-parts/content/none' ); ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
get_footer( 'shop' );
