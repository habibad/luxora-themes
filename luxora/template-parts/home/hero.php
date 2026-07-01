<?php
/**
 * Home — Hero. Mirrors index.tsx hero block.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_img = get_theme_mod( 'luxora_hero_image', luxora_asset_img( 'hero-bag.jpg' ) );

// Featured card: first best-seller / new product, fallback to most recent.
$featured = luxora_query_products( 'best', 1 );
if ( empty( $featured ) ) {
	$featured = luxora_query_products( 'recent', 1 );
}
$f_product = ! empty( $featured ) ? wc_get_product( $featured[0] ) : null;
?>
<section class="relative bg-cream overflow-hidden luxe-hero" data-reveal>
	<div class="container-luxe grid lg:grid-cols-12 gap-10 lg:gap-16 py-16 md:py-24 lg:py-32 items-center">
		<div class="lg:col-span-6 relative z-10">
			<p class="eyebrow mb-6" data-reveal><?php echo esc_html( luxora_opt( 'hero_eyebrow' ) ); ?></p>
			<h1 class="font-display text-[44px] leading-[1] sm:text-6xl lg:text-[88px] tracking-[-0.02em]" data-reveal>
				<?php echo wp_kses( luxora_opt( 'hero_heading' ), array( 'br' => array(), 'em' => array( 'class' => array() ) ) ); ?>
			</h1>
			<p class="mt-8 font-serif text-lg md:text-xl text-ink/70 max-w-md leading-relaxed" data-reveal>
				<?php echo esc_html( luxora_opt( 'hero_subtitle' ) ); ?>
			</p>
			<div class="mt-10 flex flex-wrap gap-4" data-reveal>
				<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Shop the edit', 'luxora' ); ?> <?php echo luxora_icon( 'arrow-right', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<a href="<?php echo esc_url( luxora_collections_url() ); ?>" class="btn-luxe-ghost"><?php esc_html_e( 'View collections', 'luxora' ); ?></a>
			</div>
			<div class="mt-14 flex items-center gap-8 text-[11px] uppercase tracking-[0.22em] text-ink/50" data-reveal>
				<span><?php esc_html_e( 'Free returns', 'luxora' ); ?></span>
				<span class="h-1 w-1 rounded-full bg-ink/30"></span>
				<span><?php esc_html_e( 'Authenticity guaranteed', 'luxora' ); ?></span>
				<span class="hidden md:inline h-1 w-1 rounded-full bg-ink/30"></span>
				<span class="hidden md:inline"><?php esc_html_e( 'Express delivery', 'luxora' ); ?></span>
			</div>
		</div>
		<div class="lg:col-span-6 relative" data-reveal>
			<div class="relative aspect-[4/5] overflow-hidden">
				<img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php esc_attr_e( 'LUXORA signature tote', 'luxora' ); ?>" class="h-full w-full object-cover" fetchpriority="high" decoding="async" />
			</div>
			<?php if ( $f_product ) : ?>
				<a href="<?php echo esc_url( get_permalink( $f_product->get_id() ) ); ?>" class="absolute -bottom-6 -left-6 hidden md:block bg-background border border-border p-5 max-w-[220px] shadow-xl hover:border-gold transition">
					<p class="eyebrow mb-2"><?php esc_html_e( 'Featured', 'luxora' ); ?></p>
					<p class="font-display text-lg leading-tight"><?php echo esc_html( $f_product->get_name() ); ?></p>
					<p class="text-sm text-muted-foreground mt-1"><?php echo wp_kses_post( $f_product->get_price_html() ); ?></p>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
