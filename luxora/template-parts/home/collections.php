<?php
/**
 * Home — Featured Collections. Mirrors index.tsx collections block.
 * Pulls product categories from WooCommerce.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cats = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => 4,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'exclude'    => array( get_option( 'default_product_cat' ) ),
	)
);

if ( is_wp_error( $cats ) || empty( $cats ) ) {
	return;
}
?>
<section class="container-luxe py-24 md:py-32">
	<?php
	luxora_section_heading(
		array(
			'eyebrow'  => __( 'The Edit', 'luxora' ),
			'title'    => __( 'Featured Collections', 'luxora' ),
			'subtitle' => __( 'From everyday totes to evening jewels — a maison for every chapter.', 'luxora' ),
			'align'    => 'between',
			'action'   => '<a href="' . esc_url( luxora_collections_url() ) . '" class="link-underline text-sm uppercase tracking-[0.18em] inline-flex items-center gap-2">' . esc_html__( 'View all', 'luxora' ) . luxora_icon( 'arrow-up-right', 'h-4 w-4' ) . '</a>',
		)
	);
	?>
	<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6" data-reveal-stagger>
		<?php
		foreach ( $cats as $cat ) :
			$thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
			$cat_image = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'luxora-portrait' ) : wc_placeholder_img_src( 'luxora-portrait' );
			?>
			<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="group relative aspect-[3/4] overflow-hidden bg-muted" data-reveal-item>
				<img src="<?php echo esc_url( $cat_image ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-[1200ms] group-hover:scale-110" />
				<div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/0 to-transparent"></div>
				<div class="absolute bottom-0 left-0 right-0 p-6 text-cream">
					<p class="eyebrow text-cream/70 mb-2">
						<?php
						/* translators: %d: number of products in collection */
						printf( esc_html( _n( '%d piece', '%d pieces', $cat->count, 'luxora' ) ), absint( $cat->count ) );
						?>
					</p>
					<h3 class="font-display text-2xl"><?php echo esc_html( $cat->name ); ?></h3>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>
