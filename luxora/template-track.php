<?php
/**
 * Template Name: Order Tracking
 *
 * Mirrors track.tsx — order lookup + delivery timeline mapped to WooCommerce order status.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$order        = false;
$lookup_error = '';

// Handle lookup (order number + billing email).
if ( isset( $_GET['luxora_track'] ) && luxora_woo_active() ) { // phpcs:ignore WordPress.Security.NonceVerification
	$order_input = isset( $_GET['order_id'] ) ? sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$email_input = isset( $_GET['order_email'] ) ? sanitize_email( wp_unslash( $_GET['order_email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$order_id    = wc_get_order_id_by_order_key( $order_input );

	if ( ! $order_id ) {
		$order_id = (int) preg_replace( '/[^0-9]/', '', $order_input );
	}

	$maybe_order = $order_id ? wc_get_order( $order_id ) : false;

	if ( $maybe_order && $email_input && strtolower( $maybe_order->get_billing_email() ) === strtolower( $email_input ) ) {
		$order = $maybe_order;
	} elseif ( $maybe_order && is_user_logged_in() && (int) $maybe_order->get_customer_id() === get_current_user_id() ) {
		$order = $maybe_order;
	} else {
		$lookup_error = __( 'We could not find an order matching those details. Please check the order number and email.', 'luxora' );
	}
}

/**
 * Map a WooCommerce status to the 4-stage timeline.
 */
$stage_defs = array(
	array(
		'icon'     => 'check',
		'label'    => __( 'Order placed', 'luxora' ),
		'statuses' => array( 'pending', 'processing', 'on-hold', 'completed' ),
	),
	array(
		'icon'     => 'package',
		'label'    => __( 'Hand-packed', 'luxora' ),
		'statuses' => array( 'processing', 'on-hold', 'completed' ),
	),
	array(
		'icon'     => 'truck',
		'label'    => __( 'In transit', 'luxora' ),
		'statuses' => array( 'completed' ),
	),
	array(
		'icon'     => 'map-pin',
		'label'    => __( 'Delivered', 'luxora' ),
		'statuses' => array( 'completed' ),
	),
);

$order_status = $order ? $order->get_status() : '';
?>
<section class="container-luxe py-20 md:py-28 max-w-3xl luxora-track">
	<p class="eyebrow mb-4"><?php esc_html_e( 'Order tracking', 'luxora' ); ?></p>
	<h1 class="font-display text-5xl md:text-6xl">
		<?php echo $order ? esc_html( '#' . $order->get_order_number() ) : esc_html__( 'Track your order', 'luxora' ); ?>
	</h1>

	<?php if ( $order ) : ?>
		<?php $eta = $order->get_date_created() ? $order->get_date_created()->modify( '+4 days' )->date_i18n( get_option( 'date_format' ) ) : ''; ?>
		<p class="mt-4 text-muted-foreground">
			<?php esc_html_e( 'Status:', 'luxora' ); ?> <span class="text-ink font-medium"><?php echo esc_html( wc_get_order_status_name( $order_status ) ); ?></span>
			<?php if ( $eta && ! $order->has_status( array( 'completed', 'cancelled', 'refunded' ) ) ) : ?>
				&middot; <?php esc_html_e( 'Estimated delivery:', 'luxora' ); ?> <span class="text-ink font-medium"><?php echo esc_html( $eta ); ?></span>
			<?php endif; ?>
		</p>
	<?php else : ?>
		<p class="mt-4 text-muted-foreground"><?php esc_html_e( 'Enter your order number and email to view its progress.', 'luxora' ); ?></p>
	<?php endif; ?>

	<form method="get" class="mt-10 grid sm:grid-cols-2 gap-4 max-w-xl luxora-track-form">
		<input type="hidden" name="luxora_track" value="1" />
		<input type="text" name="order_id" required placeholder="<?php esc_attr_e( 'Order number', 'luxora' ); ?>" value="<?php echo isset( $_GET['order_id'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>" class="bg-transparent border-b border-ink/30 focus:border-ink outline-none py-3" aria-label="<?php esc_attr_e( 'Order number', 'luxora' ); ?>" />
		<input type="email" name="order_email" required placeholder="<?php esc_attr_e( 'Billing email', 'luxora' ); ?>" value="<?php echo isset( $_GET['order_email'] ) ? esc_attr( sanitize_email( wp_unslash( $_GET['order_email'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>" class="bg-transparent border-b border-ink/30 focus:border-ink outline-none py-3" aria-label="<?php esc_attr_e( 'Billing email', 'luxora' ); ?>" />
		<div class="sm:col-span-2">
			<button type="submit" class="btn-luxe"><?php esc_html_e( 'Track', 'luxora' ); ?></button>
		</div>
	</form>

	<?php if ( $lookup_error ) : ?>
		<p class="mt-6 text-sm text-[color:var(--destructive,#b3261e)]"><?php echo esc_html( $lookup_error ); ?></p>
	<?php endif; ?>

	<?php if ( $order ) : ?>
		<div class="mt-16" data-reveal>
			<ol class="relative border-l border-border ml-4">
				<?php
				$reached_active = false;
				foreach ( $stage_defs as $stage ) :
					$done   = in_array( $order_status, $stage['statuses'], true );
					$active = ( $done && ! $reached_active && ! $order->has_status( 'completed' ) );
					// Mark the last completed stage as active pulse.
					if ( $done ) {
						$reached_active = true;
					}
					?>
					<li class="ml-8 pb-12 last:pb-0 relative">
						<span class="absolute -left-[42px] grid place-items-center h-10 w-10 rounded-full <?php echo $done ? 'bg-ink text-cream' : 'bg-cream text-muted-foreground border border-border'; ?> <?php echo $active ? 'ring-4 ring-gold/30' : ''; ?>">
							<?php echo luxora_icon( $stage['icon'], 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</span>
						<p class="font-display text-xl"><?php echo esc_html( $stage['label'] ); ?></p>
						<p class="text-sm text-muted-foreground mt-1">
							<?php echo $done ? esc_html__( 'Completed', 'luxora' ) : esc_html__( 'Pending', 'luxora' ); ?>
						</p>
					</li>
					<?php
				endforeach;
				?>
			</ol>

			<div class="mt-12 border-t border-border pt-8">
				<h2 class="font-display text-2xl mb-6"><?php esc_html_e( 'Order details', 'luxora' ); ?></h2>
				<ul class="divide-y divide-border border-y border-border">
					<?php foreach ( $order->get_items() as $item ) : ?>
						<li class="py-4 flex justify-between gap-4 text-sm">
							<span><?php echo esc_html( $item->get_name() ); ?> &times; <?php echo esc_html( $item->get_quantity() ); ?></span>
							<span class="font-medium"><?php echo wp_kses_post( wc_price( $item->get_total() ) ); ?></span>
						</li>
					<?php endforeach; ?>
					<li class="py-4 flex justify-between gap-4">
						<span class="font-display text-lg"><?php esc_html_e( 'Total', 'luxora' ); ?></span>
						<span class="font-display text-lg"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
					</li>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php
	// Allow static page content below (optional editorial copy).
	while ( have_posts() ) :
		the_post();
		$c = get_the_content();
		if ( '' !== trim( wp_strip_all_tags( $c ) ) ) :
			echo '<div class="prose-luxe mt-16 font-serif text-lg leading-relaxed">';
			the_content();
			echo '</div>';
		endif;
	endwhile;
	?>
</section>

<?php
get_footer();
