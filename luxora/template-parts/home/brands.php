<?php
/**
 * Home — Brands strip. Mirrors index.tsx brands block.
 * Uses pa_brand attribute terms if present, else Customizer fallback list.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brands = array();

if ( taxonomy_exists( 'pa_brand' ) ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'pa_brand',
			'hide_empty' => true,
			'number'     => 6,
		)
	);
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			$brands[] = $t->name;
		}
	}
}

if ( empty( $brands ) ) {
	$raw    = luxora_opt( 'brands_list' );
	$brands = array_filter( array_map( 'trim', explode( ',', (string) $raw ) ) );
}

if ( empty( $brands ) ) {
	return;
}
?>
<section class="border-y border-border">
	<div class="container-luxe py-12 flex flex-wrap items-center justify-around gap-x-12 gap-y-6" data-reveal>
		<?php foreach ( $brands as $b ) : ?>
			<span class="font-display text-xl md:text-2xl text-muted-foreground tracking-[0.18em]"><?php echo esc_html( $b ); ?></span>
		<?php endforeach; ?>
	</div>
</section>
