<?php
/**
 * WooCommerce loop item — delegates to the shared Luxora card.
 * Override of woocommerce/content-product.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'luxora-loop-item', $product ); ?> data-reveal-item>
	<?php luxora_render_product_card( $product ); ?>
</li>
