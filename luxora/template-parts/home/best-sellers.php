<?php
/**
 * Home — Best sellers. Mirrors index.tsx best sellers block.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$best = luxora_query_products( 'best', 3 );
if ( empty( $best ) ) {
	return;
}
?>
<section class="container-luxe py-24 md:py-32">
	<?php
	luxora_section_heading(
		array(
			'eyebrow' => __( 'Iconic', 'luxora' ),
			'title'   => __( 'Best sellers', 'luxora' ),
			'align'   => 'between',
			'action'  => '<a href="' . esc_url( luxora_shop_url( array( 'orderby' => 'popularity' ) ) ) . '" class="link-underline text-sm uppercase tracking-[0.18em]">' . esc_html__( 'View all', 'luxora' ) . '</a>',
		)
	);
	?>
	<div class="grid grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>
		<?php
		foreach ( $best as $pid ) :
			echo '<div data-reveal-item>';
			luxora_render_product_card( $pid );
			echo '</div>';
		endforeach;
		?>
	</div>
</section>
