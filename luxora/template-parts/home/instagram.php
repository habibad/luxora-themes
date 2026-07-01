<?php
/**
 * Home — Instagram grid. Mirrors index.tsx instagram block.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gram     = luxora_query_products( 'recent', 6 );
$ig_handle = luxora_opt( 'instagram_handle' );
$ig_url    = luxora_opt( 'social_instagram' );
if ( empty( $gram ) ) {
	return;
}
?>
<section class="container-luxe py-24 md:py-32">
	<?php
	luxora_section_heading(
		array(
			'eyebrow' => $ig_handle ? $ig_handle : '@luxora.bd',
			'title'   => __( 'Carried by you', 'luxora' ),
		)
	);
	?>
	<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 md:gap-3" data-reveal-stagger>
		<?php
		foreach ( $gram as $pid ) :
			$pimg = get_the_post_thumbnail_url( $pid, 'luxora-square' );
			$pimg = $pimg ? $pimg : wc_placeholder_img_src( 'luxora-square' );
			$href = $ig_url ? $ig_url : get_permalink( $pid );
			?>
			<a href="<?php echo esc_url( $href ); ?>"<?php echo $ig_url ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> class="group relative aspect-square overflow-hidden bg-muted" data-reveal-item aria-label="<?php echo esc_attr( get_the_title( $pid ) ); ?>">
				<img src="<?php echo esc_url( $pimg ); ?>" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-[900ms] group-hover:scale-110" />
				<div class="absolute inset-0 bg-ink/0 group-hover:bg-ink/30 grid place-items-center transition">
					<span class="text-cream opacity-0 group-hover:opacity-100 transition"><?php echo luxora_icon( 'arrow-up-right', 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>
