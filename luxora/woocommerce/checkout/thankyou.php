<?php
/**
 * Order received / thank you — mirrors checkout.tsx Success.
 * Override of woocommerce/checkout/thankyou.php
 *
 * @package Luxora
 *
 * @var WC_Order $order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$track_url = luxora_page_url_by_title( 'Track Order', '/track/' );
?>
<section class="container-luxe py-32 text-center max-w-2xl mx-auto luxora-thankyou" data-reveal>
	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="h-16 w-16 rounded-full bg-ink/10 grid place-items-center mx-auto">
				<?php echo luxora_icon( 'x', 'h-7 w-7' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<h1 class="font-display text-5xl md:text-6xl mt-8"><?php esc_html_e( 'Payment unsuccessful', 'luxora' ); ?></h1>
			<p class="mt-6 font-serif text-lg md:text-xl text-muted-foreground">
				<?php esc_html_e( 'Unfortunately your order could not be processed. Please attempt your purchase again.', 'luxora' ); ?>
			</p>
			<div class="mt-10 flex flex-wrap justify-center gap-4">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Pay again', 'luxora' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn-luxe-ghost"><?php esc_html_e( 'My account', 'luxora' ); ?></a>
				<?php endif; ?>
			</div>

		<?php else : ?>

			<div class="h-16 w-16 rounded-full bg-gold/20 grid place-items-center mx-auto">
				<?php echo luxora_icon( 'check', 'h-7 w-7 text-gold' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<p class="eyebrow mt-8 mb-4">
				<?php
				/* translators: %s: order number */
				printf( esc_html__( 'Order #%s', 'luxora' ), esc_html( $order->get_order_number() ) );
				?>
			</p>
			<h1 class="font-display text-5xl md:text-6xl"><?php esc_html_e( 'Thank you.', 'luxora' ); ?></h1>
			<p class="mt-6 font-serif text-lg md:text-xl text-muted-foreground">
				<?php esc_html_e( 'Your order has been received. A confirmation email is on its way, and our atelier team is preparing your pieces with care.', 'luxora' ); ?>
			</p>

			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

			<div class="mt-10 flex flex-wrap justify-center gap-4">
				<a href="<?php echo esc_url( $track_url ); ?>" class="btn-luxe"><?php esc_html_e( 'Track order', 'luxora' ); ?></a>
				<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe-ghost"><?php esc_html_e( 'Continue shopping', 'luxora' ); ?></a>
			</div>

		<?php endif; ?>

	<?php else : ?>

		<div class="h-16 w-16 rounded-full bg-gold/20 grid place-items-center mx-auto">
			<?php echo luxora_icon( 'check', 'h-7 w-7 text-gold' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<h1 class="font-display text-5xl md:text-6xl mt-8"><?php esc_html_e( 'Thank you.', 'luxora' ); ?></h1>
		<p class="mt-6 font-serif text-lg md:text-xl text-muted-foreground">
			<?php esc_html_e( 'Your order has been received.', 'luxora' ); ?>
		</p>
		<div class="mt-10 flex flex-wrap justify-center gap-4">
			<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe-ghost"><?php esc_html_e( 'Continue shopping', 'luxora' ); ?></a>
		</div>

	<?php endif; ?>
</section>
