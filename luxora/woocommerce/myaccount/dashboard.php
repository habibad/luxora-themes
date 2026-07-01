<?php
/**
 * My Account dashboard — fully custom overview.
 * Override of woocommerce/myaccount/dashboard.php
 *
 * @package Luxora
 *
 * @var WP_User $current_user Current user object (provided by WooCommerce).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();

// Pull the customer's most recent orders for a snapshot.
$recent_orders = array();
if ( function_exists( 'wc_get_orders' ) ) {
	$recent_orders = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'limit'       => 3,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);
}
?>
<div class="luxora-dashboard">
	<h2 class="font-display text-3xl mb-6"><?php esc_html_e( 'Overview', 'luxora' ); ?></h2>

	<?php
	// WooCommerce passes $current_user as a WP_User object; derive a safe name string.
	$luxora_user = is_a( $current_user, 'WP_User' ) ? $current_user : wp_get_current_user();
	$luxora_name = $luxora_user->first_name ? $luxora_user->first_name : $luxora_user->display_name;
	?>
	<p class="font-serif text-lg text-muted-foreground leading-relaxed max-w-2xl">
		<?php
		printf(
			/* translators: 1: display name (wrapped in span markup) */
			wp_kses( __( 'Welcome back, %1$s. From here you can follow your orders, revisit saved pieces, and keep your delivery and account details up to date.', 'luxora' ), array( 'span' => array( 'class' => array() ) ) ),
			'<span class="text-ink font-medium">' . esc_html( $luxora_name ) . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput
		);
		?>
	</p>

	<!-- Quick links -->
	<div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="border border-border p-6 hover:border-gold transition group">
			<?php echo luxora_icon( 'package', 'h-6 w-6 text-gold mb-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="font-display text-lg"><?php esc_html_e( 'Orders', 'luxora' ); ?></p>
			<p class="text-sm text-muted-foreground mt-1"><?php esc_html_e( 'Track and review purchases.', 'luxora' ); ?></p>
		</a>
		<a href="<?php echo esc_url( luxora_page_url_by_title( 'Wishlist', '/wishlist/' ) ); ?>" class="border border-border p-6 hover:border-gold transition group">
			<?php echo luxora_icon( 'heart', 'h-6 w-6 text-gold mb-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="font-display text-lg"><?php esc_html_e( 'Wishlist', 'luxora' ); ?></p>
			<p class="text-sm text-muted-foreground mt-1"><?php esc_html_e( 'Pieces you have saved.', 'luxora' ); ?></p>
		</a>
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="border border-border p-6 hover:border-gold transition group">
			<?php echo luxora_icon( 'user', 'h-6 w-6 text-gold mb-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="font-display text-lg"><?php esc_html_e( 'Profile', 'luxora' ); ?></p>
			<p class="text-sm text-muted-foreground mt-1"><?php esc_html_e( 'Update your account details.', 'luxora' ); ?></p>
		</a>
	</div>

	<!-- Recent orders snapshot -->
	<div class="mt-16">
		<div class="flex items-end justify-between mb-8">
			<h3 class="font-display text-2xl"><?php esc_html_e( 'Recent orders', 'luxora' ); ?></h3>
			<?php if ( $recent_orders ) : ?>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="text-xs uppercase tracking-[0.18em] link-underline"><?php esc_html_e( 'View all', 'luxora' ); ?></a>
			<?php endif; ?>
		</div>

		<?php if ( $recent_orders ) : ?>
			<div class="divide-y divide-border border-y border-border">
				<?php foreach ( $recent_orders as $order ) : ?>
					<div class="py-6 grid md:grid-cols-4 gap-4 items-center">
						<div>
							<p class="font-display text-lg">#<?php echo esc_html( $order->get_order_number() ); ?></p>
							<p class="text-xs text-muted-foreground"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></p>
						</div>
						<p class="text-sm"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></p>
						<span class="text-xs uppercase tracking-[0.18em] text-gold"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="text-xs uppercase tracking-[0.18em] link-underline w-fit md:justify-self-end"><?php esc_html_e( 'View details', 'luxora' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="border border-border p-10 text-center">
				<p class="font-display text-xl mb-4"><?php esc_html_e( 'No orders yet.', 'luxora' ); ?></p>
				<p class="text-sm text-muted-foreground mb-6"><?php esc_html_e( 'When you place your first order, it will appear here.', 'luxora' ); ?></p>
				<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Discover the edit', 'luxora' ); ?></a>
			</div>
		<?php endif; ?>
	</div>

	<?php do_action( 'woocommerce_account_dashboard' ); ?>
</div>
