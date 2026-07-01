<?php
/**
 * Home — New arrivals. Mirrors index.tsx new arrivals block.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$new = luxora_query_products( 'new', 3 );
if ( empty( $new ) ) {
	return;
}
?>
<section class="container-luxe pb-24 md:pb-32">
	<?php
	luxora_section_heading(
		array(
			'eyebrow' => __( 'Just In', 'luxora' ),
			'title'   => __( 'New arrivals', 'luxora' ),
			'align'   => 'between',
			'action'  => '<a href="' . esc_url( luxora_shop_url( array( 'orderby' => 'date' ) ) ) . '" class="link-underline text-sm uppercase tracking-[0.18em]">' . esc_html__( 'See all', 'luxora' ) . '</a>',
		)
	);
	?>
	<div class="grid grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>
		<?php
		foreach ( $new as $pid ) :
			echo '<div data-reveal-item>';
			luxora_render_product_card( $pid );
			echo '</div>';
		endforeach;
		?>
	</div>
</section>
