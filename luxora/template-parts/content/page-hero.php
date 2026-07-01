<?php
/**
 * Reusable page hero (eyebrow + title + subtitle). Mirrors interior route heroes.
 *
 * @package Luxora
 *
 * Expects $args via set_query_var( 'luxora_hero', array(...) ) or get_template_part 3rd arg.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'  => '',
		'title'    => get_the_title(),
		'subtitle' => '',
		'tone'     => 'cream', // cream|ink.
	)
);

$bg = 'ink' === $hero['tone'] ? 'bg-ink text-cream' : 'bg-cream';
?>
<section class="<?php echo esc_attr( $bg ); ?>" data-reveal>
	<div class="container-luxe py-16 md:py-24 text-center max-w-3xl mx-auto">
		<?php if ( $hero['eyebrow'] ) : ?>
			<p class="eyebrow mb-5 <?php echo 'ink' === $hero['tone'] ? 'text-cream/60' : ''; ?>"><?php echo esc_html( $hero['eyebrow'] ); ?></p>
		<?php endif; ?>
		<h1 class="font-display text-4xl md:text-5xl lg:text-6xl tracking-tight"><?php echo esc_html( $hero['title'] ); ?></h1>
		<?php if ( $hero['subtitle'] ) : ?>
			<p class="mt-5 font-serif text-lg md:text-xl <?php echo 'ink' === $hero['tone'] ? 'text-cream/70' : 'text-muted-foreground'; ?> leading-relaxed"><?php echo esc_html( $hero['subtitle'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
