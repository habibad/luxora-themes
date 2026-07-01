<?php
/**
 * Related products — Luxora card grid.
 * Override of woocommerce/single-product/related.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $related_products ) ) {
	return;
}
?>
<section class="container-luxe py-24 md:py-32 luxora-related">
	<?php
	luxora_section_heading(
		array(
			'eyebrow' => __( 'You may also love', 'luxora' ),
			'title'   => __( 'Pairs beautifully', 'luxora' ),
		)
	);
	?>
	<div class="grid grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>
		<?php
		foreach ( $related_products as $related_product ) :
			echo '<div data-reveal-item>';
			luxora_render_product_card( $related_product->get_id() );
			echo '</div>';
		endforeach;
		?>
	</div>
</section>
