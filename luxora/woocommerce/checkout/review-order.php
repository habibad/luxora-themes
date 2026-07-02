<?php
/**
 * Checkout order summary ("Your order") — mirrors the mockup sidebar.
 * Override of woocommerce/checkout/review-order.php
 *
 * Keeps the .woocommerce-checkout-review-order-table class so WooCommerce's
 * AJAX order-review refresh replaces it correctly.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="woocommerce-checkout-review-order-table luxora-order-review">

	<ul class="luxora-review-items divide-y divide-ink/10 list-none p-0 m-0">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$brand    = luxora_get_brand( $_product );
				$thumb_id = $_product->get_image_id();
				$thumb    = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'luxora-square' ) : wc_placeholder_img_src( 'luxora-square' );
				?>
				<li class="flex gap-4 py-4">
					<div class="relative h-20 w-16 bg-background overflow-hidden shrink-0">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" class="h-full w-full object-cover" loading="lazy" />
						<span class="absolute -top-2 -right-2 bg-ink text-cream text-[10px] rounded-full h-5 w-5 grid place-items-center"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
					</div>
					<div class="flex-1 min-w-0">
						<?php
						$parent_id = $_product->get_parent_id();
						$product_name = $parent_id ? get_the_title( $parent_id ) : $_product->get_name();
						$product_name = apply_filters( 'woocommerce_cart_item_name', $product_name, $cart_item, $cart_item_key );
						?>
						<p class="font-display text-base truncate"><?php echo wp_kses_post( $product_name ); ?></p>
						<?php if ( $brand ) : ?><p class="text-xs text-muted-foreground"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
						<?php
						$meta = wc_get_formatted_cart_item_data( $cart_item );
						if ( $meta ) :
							?>
							<div class="text-xs text-muted-foreground mt-0.5"><?php echo wp_kses_post( $meta ); ?></div>
						<?php endif; ?>
					</div>
					<span class="text-sm font-medium whitespace-nowrap"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); ?></span>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</ul>

	<div class="luxora-review-totals mt-6 pt-6 border-t border-ink/10 space-y-2 text-sm">
		<div class="flex justify-between">
			<span class="text-ink/70"><?php esc_html_e( 'Subtotal', 'luxora' ); ?></span>
			<span class="font-medium"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="flex justify-between">
				<span class="text-ink/70"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span class="font-medium"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
			<table class="luxora-review-shipping w-full"><tbody>
				<?php wc_cart_totals_shipping_html(); ?>
			</tbody></table>
			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="flex justify-between">
				<span class="text-ink/70"><?php echo esc_html( $fee->name ); ?></span>
				<span class="font-medium"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) {
					?>
					<div class="flex justify-between">
						<span class="text-ink/70"><?php echo esc_html( $tax->label ); ?></span>
						<span class="font-medium"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
					<?php
				}
			} else {
				?>
				<div class="flex justify-between">
					<span class="text-ink/70"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					<span class="font-medium"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
				<?php
			}
		}
		?>
	</div>

	<div class="flex items-baseline justify-between pt-4 mt-2">
		<span class="font-display text-xl"><?php esc_html_e( 'Total', 'luxora' ); ?></span>
		<span class="font-display text-xl luxora-review-total"><?php wc_cart_totals_order_total_html(); ?></span>
	</div>

	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
</div>
