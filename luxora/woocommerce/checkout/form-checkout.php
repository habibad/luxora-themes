<?php
/**
 * Multi-step checkout — Shipping → Billing → Payment → Review.
 * Override of woocommerce/checkout/form-checkout.php
 *
 * Preserves every WooCommerce field, hook, gateway and nonce; the four steps
 * are JS-driven panels over the real checkout form. All fields stay in the DOM,
 * so validation, AJAX order-review refresh, and payment processing are intact.
 *
 * @package Luxora
 *
 * @var WC_Checkout $checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo '<div class="container-luxe py-24 text-center">';
	esc_html_e( 'You must be logged in to checkout.', 'luxora' );
	echo '</div>';
	return;
}

$steps = array(
	__( 'Shipping', 'luxora' ),
	__( 'Billing', 'luxora' ),
	__( 'Payment', 'luxora' ),
	__( 'Review', 'luxora' ),
);
?>

<section class="container-luxe py-16 md:py-24 luxora-checkout" data-checkout-wizard>
	<h1 class="font-display text-4xl md:text-5xl mb-10 text-center"><?php esc_html_e( 'Checkout', 'luxora' ); ?></h1>

	<!-- Stepper -->
	<div class="flex items-center justify-center gap-3 md:gap-6 mb-14 luxora-checkout-steps" data-step-indicator>
		<?php foreach ( $steps as $i => $label ) : ?>
			<div class="flex items-center gap-3 luxora-step-dot" data-dot="<?php echo esc_attr( $i ); ?>">
				<div class="luxora-step-bubble h-9 w-9 rounded-full grid place-items-center border text-xs <?php echo 0 === $i ? 'bg-ink text-cream border-ink' : 'border-border text-muted-foreground'; ?>">
					<span class="luxora-step-num"><?php echo esc_html( $i + 1 ); ?></span>
					<span class="luxora-step-check hidden"><?php echo luxora_icon( 'check', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</div>
				<span class="hidden md:inline text-xs uppercase tracking-[0.18em] luxora-step-label <?php echo 0 === $i ? 'text-ink' : 'text-muted-foreground'; ?>"><?php echo esc_html( $label ); ?></span>
				<?php if ( $i < count( $steps ) - 1 ) : ?>
					<span class="hidden md:inline-block w-12 h-px bg-border"></span>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" novalidate>

		<div class="grid lg:grid-cols-[1fr_400px] gap-16">

			<div class="luxora-checkout-fields">

				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div id="customer_details">
						<!-- Step 0 — Shipping (WooCommerce billing = the delivery address) -->
						<div class="luxora-step is-active" data-step="0">
							<h2 class="font-display text-2xl mb-8"><?php esc_html_e( 'Shipping address', 'luxora' ); ?></h2>
							<div class="col-1">
								<?php do_action( 'woocommerce_checkout_billing' ); ?>
							</div>
						</div>

						<!-- Step 1 — Billing (ship-to-different / separate address) -->
						<div class="luxora-step" data-step="1">
							<h2 class="font-display text-2xl mb-8"><?php esc_html_e( 'Billing address', 'luxora' ); ?></h2>
							<div class="mb-6">
								<label class="luxora-same-address-label flex items-center gap-3 font-sans text-sm text-foreground cursor-pointer">
									<input type="checkbox" id="luxora-same-address-checkbox" checked class="accent-ink h-4 w-4" />
									<span><?php esc_html_e( 'Same as shipping address', 'luxora' ); ?></span>
								</label>
							</div>
							<div class="col-2">
								<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							</div>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>

				<!-- Steps 2 & 3 live inside #payment (rendered by our payment.php override). -->
				<div id="order_review_payment" class="woocommerce-checkout-review-order">
					<?php woocommerce_checkout_payment(); ?>
				</div>

				<!-- Wizard navigation -->
				<div class="mt-10 flex items-center justify-between luxora-checkout-nav" data-step-nav>
					<button type="button" class="text-xs uppercase tracking-[0.18em] text-muted-foreground hover:text-ink disabled:opacity-30" data-step-back disabled><?php esc_html_e( 'Back', 'luxora' ); ?></button>
					<button type="button" class="btn-luxe" data-step-next><?php esc_html_e( 'Continue', 'luxora' ); ?></button>
				</div>
			</div>

			<!-- Persistent order summary -->
			<aside class="bg-cream p-8 h-fit lg:sticky lg:top-32 luxora-checkout-review">
				<h2 class="font-display text-2xl mb-6"><?php esc_html_e( 'Your order', 'luxora' ); ?></h2>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php woocommerce_order_review(); ?>
				</div>

				<div class="mt-6 flex items-center gap-2 text-xs text-muted-foreground">
					<?php echo luxora_icon( 'lock', 'h-3 w-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Secure SSL checkout', 'luxora' ); ?>
				</div>
			</aside>
		</div>
	</form>
</section>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
