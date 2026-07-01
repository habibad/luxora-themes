<?php
/**
 * Home — Trending now. Mirrors index.tsx trending block.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$trending = luxora_query_products( 'recent', 4 );
if ( empty( $trending ) ) {
	return;
}
?>
<section class="bg-muted/40 py-24 md:py-32">
	<div class="container-luxe">
		<?php
		luxora_section_heading(
			array(
				'eyebrow' => __( 'In Demand', 'luxora' ),
				'title'   => __( 'Trending now', 'luxora' ),
				'align'   => 'between',
				'action'  => '<a href="' . esc_url( luxora_shop_url() ) . '" class="link-underline text-sm uppercase tracking-[0.18em]">' . esc_html__( 'Shop all', 'luxora' ) . '</a>',
			)
		);
		?>
		<div class="grid grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>
			<?php
			foreach ( $trending as $pid ) :
				echo '<div data-reveal-item>';
				luxora_render_product_card( $pid );
				echo '</div>';
			endforeach;
			?>
		</div>
	</div>
</section>
