<?php
/**
 * Cart totals (order summary) — mirrors cart.tsx summary aside.
 * Override of woocommerce/cart/cart-totals.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="luxora-order-summary">
	<h2 class="font-display text-2xl mb-6"><?php esc_html_e( 'Order summary', 'luxora' ); ?></h2>

	<div class="space-y-3 text-sm border-b border-ink/10 pb-6">
		<div class="flex justify-between">
			<span class="text-ink/70"><?php esc_html_e( 'Subtotal', 'luxora' ); ?></span>
			<span class="font-medium luxora-summary-subtotal"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="flex justify-between luxora-coupon" data-coupon="<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span class="text-ink/70"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span class="font-medium"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<div class="flex justify-between">
				<span class="text-ink/70"><?php esc_html_e( 'Shipping', 'luxora' ); ?></span>
				<span class="font-medium luxora-summary-shipping"><?php woocommerce_cart_totals_shipping_html(); ?></span>
			</div>
		<?php else : ?>
			<div class="flex justify-between">
				<span class="text-ink/70"><?php esc_html_e( 'Shipping', 'luxora' ); ?></span>
				<span class="text-muted-foreground"><?php esc_html_e( 'Calculated at checkout', 'luxora' ); ?></span>
			</div>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="flex justify-between">
				<span class="text-ink/70"><?php echo esc_html( $fee->name ); ?></span>
				<span class="font-medium"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<div class="flex justify-between">
						<span class="text-ink/70"><?php echo esc_html( $tax->label ); ?></span>
						<span class="font-medium"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="flex justify-between">
					<span class="text-ink/70"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					<span class="font-medium"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="flex justify-between">
				<span class="text-ink/70"><?php esc_html_e( 'Estimated tax', 'luxora' ); ?></span>
				<span class="text-muted-foreground"><?php esc_html_e( 'Calculated at checkout', 'luxora' ); ?></span>
			</div>
		<?php endif; ?>
	</div>

	<div class="flex items-baseline justify-between py-6 border-b border-ink/10">
		<span class="font-display text-xl"><?php esc_html_e( 'Total', 'luxora' ); ?></span>
		<span class="font-display text-2xl luxora-summary-total"><?php wc_cart_totals_order_total_html(); ?></span>
	</div>

	<?php if ( wc_coupons_enabled() ) : ?>
		<form class="mt-6 flex gap-2 luxora-coupon-form" data-coupon-form>
			<input type="text" name="coupon_code" class="flex-1 bg-transparent border-b border-ink/30 outline-none py-2 text-sm placeholder:text-ink/40" placeholder="<?php esc_attr_e( 'Promo code', 'luxora' ); ?>" data-coupon-input />
			<button type="submit" class="text-xs uppercase tracking-[0.18em] hover:text-gold" data-coupon-apply><?php esc_html_e( 'Apply', 'luxora' ); ?></button>
		</form>
	<?php endif; ?>

	<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>

	<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="block text-center text-xs uppercase tracking-[0.18em] mt-5 text-muted-foreground hover:text-ink"><?php esc_html_e( 'Continue shopping', 'luxora' ); ?></a>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>
</div>
