<?php
/**
 * Home — Editorial split. Mirrors index.tsx editorial block.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$editorial_img = get_theme_mod( 'luxora_editorial_image', luxora_asset_img( 'lifestyle-1.jpg' ) );
?>
<section class="container-luxe py-24 md:py-32 grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
	<div class="relative aspect-[4/5] order-2 lg:order-1 overflow-hidden" data-reveal>
		<img src="<?php echo esc_url( $editorial_img ); ?>" alt="<?php esc_attr_e( 'The new shape of luxury', 'luxora' ); ?>" class="h-full w-full object-cover" loading="lazy" decoding="async" />
	</div>
	<div class="order-1 lg:order-2" data-reveal>
		<p class="eyebrow mb-6"><?php echo esc_html( luxora_opt( 'editorial_eyebrow' ) ); ?></p>
		<h2 class="font-display text-4xl md:text-5xl lg:text-6xl leading-[1.05]">
			<?php echo wp_kses( luxora_opt( 'editorial_title' ), array( 'em' => array( 'class' => array() ), 'br' => array() ) ); ?>
		</h2>
		<p class="mt-6 font-serif text-lg text-muted-foreground leading-relaxed max-w-lg">
			<?php echo esc_html( luxora_opt( 'editorial_text' ) ); ?>
		</p>
		<div class="mt-10">
			<a href="<?php echo esc_url( luxora_page_url_by_title( 'About', '/about' ) ); ?>" class="btn-luxe"><?php esc_html_e( 'Our story', 'luxora' ); ?></a>
		</div>
	</div>
</section>
