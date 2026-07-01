<?php
/**
 * Home — Why Luxora. Mirrors index.tsx why block (bg-ink, 4 features).
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$features = array(
	array(
		'icon' => 'shield',
		't'    => __( 'Authenticity Guaranteed', 'luxora' ),
		'd'    => __( 'Every piece is inspected and authenticated by our atelier team.', 'luxora' ),
	),
	array(
		'icon' => 'truck',
		't'    => __( 'Nationwide Delivery', 'luxora' ),
		'd'    => __( 'Express delivery across all 64 districts of Bangladesh.', 'luxora' ),
	),
	array(
		'icon' => 'refresh',
		't'    => __( '14-Day Returns', 'luxora' ),
		'd'    => __( 'Changed your mind? Return any piece within fourteen days.', 'luxora' ),
	),
	array(
		'icon' => 'sparkles',
		't'    => __( 'Signature Packaging', 'luxora' ),
		'd'    => __( 'Each bag arrives gift-wrapped in our signature dustbag and box.', 'luxora' ),
	),
);
?>
<section class="bg-ink text-cream py-24 md:py-32">
	<div class="container-luxe">
		<div class="text-center max-w-2xl mx-auto mb-16" data-reveal>
			<p class="eyebrow text-cream/60 mb-5"><?php esc_html_e( 'Why Luxora', 'luxora' ); ?></p>
			<h2 class="font-display text-4xl md:text-5xl"><?php esc_html_e( 'A maison built on trust.', 'luxora' ); ?></h2>
		</div>
		<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10" data-reveal-stagger>
			<?php foreach ( $features as $f ) : ?>
				<div class="text-center md:text-left" data-reveal-item>
					<span class="block text-gold mb-5 mx-auto md:mx-0 w-7"><?php echo luxora_icon( $f['icon'], 'h-7 w-7' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h3 class="font-display text-xl mb-3"><?php echo esc_html( $f['t'] ); ?></h3>
					<p class="text-sm text-cream/60 leading-relaxed"><?php echo esc_html( $f['d'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
