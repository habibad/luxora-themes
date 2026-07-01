<?php
/**
 * Mini cart — used inside the header slide-in drawer.
 * Override of woocommerce/cart/mini-cart.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_mini_cart' );

$cart = WC()->cart;
?>

<?php if ( $cart && ! $cart->is_empty() ) : ?>

	<ul class="luxora-minicart-list divide-y divide-border list-none p-0 m-0 flex-1 overflow-y-auto">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}

			$permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			$thumb_id  = $_product->get_image_id();
			$thumb     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'luxora-square' ) : wc_placeholder_img_src( 'luxora-square' );
			$brand     = luxora_get_brand( $_product );
			?>
			<li class="py-5 grid grid-cols-[64px_1fr_auto] gap-4 items-start luxora-minicart-item" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
				<a href="<?php echo esc_url( $permalink ); ?>" class="aspect-square bg-cream overflow-hidden block">
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $_product->get_name() ); ?>" class="h-full w-full object-cover" loading="lazy" />
				</a>
				<div class="min-w-0">
					<?php if ( $brand ) : ?><p class="eyebrow text-[9px] mb-1"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
					<a href="<?php echo esc_url( $permalink ); ?>" class="font-display text-sm leading-tight block truncate"><?php echo esc_html( $_product->get_name() ); ?></a>
					<p class="text-xs text-muted-foreground mt-1">
						<?php echo esc_html( $cart_item['quantity'] ); ?> &times; <?php echo wp_kses_post( $_product->get_price_html() ); ?>
					</p>
				</div>
				<?php
				echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput
					'woocommerce_cart_item_remove_link',
					sprintf(
						'<a href="%s" class="text-muted-foreground hover:text-ink luxora-minicart-remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">%s</a>',
						esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
						esc_attr__( 'Remove this item', 'luxora' ),
						esc_attr( $product_id ),
						esc_attr( $cart_item_key ),
						esc_attr( $_product->get_sku() ),
						luxora_icon( 'x', 'h-4 w-4' )
					),
					$cart_item_key
				);
				?>
			</li>
			<?php
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<div class="luxora-minicart-footer border-t border-border pt-6 mt-2">
		<div class="flex items-baseline justify-between mb-6">
			<span class="eyebrow"><?php esc_html_e( 'Subtotal', 'luxora' ); ?></span>
			<span class="font-display text-xl luxora-minicart-subtotal"><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
		</div>
		<div class="flex flex-col gap-3">
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-luxe w-full justify-center"><?php esc_html_e( 'Checkout', 'luxora' ); ?></a>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn-luxe-ghost w-full justify-center"><?php esc_html_e( 'View bag', 'luxora' ); ?></a>
		</div>
		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
	</div>

<?php else : ?>

	<div class="luxora-minicart-empty flex-1 grid place-items-center text-center py-16">
		<div>
			<p class="font-display text-xl mb-5"><?php esc_html_e( 'Your bag is empty.', 'luxora' ); ?></p>
			<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Discover the edit', 'luxora' ); ?></a>
		</div>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
