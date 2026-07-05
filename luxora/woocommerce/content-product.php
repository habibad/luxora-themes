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
<?php
$classes = wc_get_product_class( array( 'w-full', 'luxora-loop-item' ), $product );
?>
<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-reveal-item>
	<?php luxora_render_product_card( $product ); ?>
</li>
