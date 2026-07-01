<?php
/**
 * Checkout payment — split into the "Payment" (step 2) and "Review" (step 3)
 * wizard panels while remaining a single #payment block for WooCommerce AJAX.
 * Override of woocommerce/checkout/payment.php
 *
 * @package Luxora
 *
 * @var array  $available_gateways
 * @var string $order_button_text
 * @var WC_Checkout $checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment luxora-payment">

	<!-- Step 2 — Payment methods -->
	<div class="luxora-step" data-step="2">
		<h2 class="font-display text-2xl mb-8"><?php esc_html_e( 'Payment', 'luxora' ); ?></h2>

		<?php if ( WC()->cart->needs_payment() ) : ?>
			<ul class="wc_payment_methods payment_methods methods list-none p-0 m-0 flex flex-col gap-3">
				<?php
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
					}
				} else {
					echo '<li>';
					wc_print_notice(
						apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput
							'woocommerce_no_available_payment_methods_message',
							WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' )
						),
						'notice'
					);
					echo '</li>';
				}
				?>
			</ul>
		<?php else : ?>
			<p class="text-sm text-muted-foreground"><?php esc_html_e( 'No payment is required for this order.', 'luxora' ); ?></p>
		<?php endif; ?>
	</div>

	<!-- Step 3 — Review + place order -->
	<div class="luxora-step" data-step="3">
		<h2 class="font-display text-2xl mb-8"><?php esc_html_e( 'Review your order', 'luxora' ); ?></h2>
		<p class="font-serif text-lg text-muted-foreground mb-8"><?php esc_html_e( 'Please confirm your details before placing the order. Your bag will be hand-packed and dispatched within 24 hours.', 'luxora' ); ?></p>

		<div class="luxora-review-recap grid sm:grid-cols-2 gap-6 text-sm mb-8" data-review-recap></div>

		<div class="form-row place-order">
			<noscript>
				<?php esc_html_e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the Update Totals button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ); ?>
				<br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
			</noscript>

			<?php wc_get_template( 'checkout/terms.php' ); ?>

			<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

			<?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt btn-luxe w-full justify-center luxora-place-order" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

			<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

			<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
		</div>
	</div>
</div>
