<?php
/**
 * Cart page — mirrors cart.tsx.
 * Override of woocommerce/cart/cart.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_cart' );

$cart       = WC()->cart;
$item_count = $cart ? $cart->get_cart_contents_count() : 0;
?>
<section class="container-luxe py-16 md:py-24 luxora-cart">
	<div class="flex items-end justify-between mb-12 border-b border-border pb-6" data-reveal>
		<div>
			<p class="eyebrow mb-3"><?php esc_html_e( 'Your selection', 'luxora' ); ?></p>
			<h1 class="font-display text-5xl md:text-6xl"><?php esc_html_e( 'Shopping bag', 'luxora' ); ?></h1>
		</div>
		<span class="text-sm text-muted-foreground luxora-cart-item-count">
			<?php
			/* translators: %d: number of items */
			printf( esc_html( _n( '%d item', '%d items', $item_count, 'luxora' ) ), absint( $item_count ) );
			?>
		</span>
	</div>

	<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<div class="grid lg:grid-cols-[1fr_400px] gap-16">
			<div>
				<?php if ( $cart && count( $cart->get_cart() ) > 0 ) : ?>
					<ul class="divide-y divide-border luxora-cart-lines list-none p-0 m-0">
						<?php
						do_action( 'woocommerce_before_cart_contents' );

						foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

							if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
								continue;
							}

							$permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							$brand     = luxora_get_brand( $_product );
							$thumb_id  = $_product->get_image_id();
							$thumb     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'luxora-square' ) : wc_placeholder_img_src( 'luxora-square' );
							?>
							<li class="py-8 grid grid-cols-[100px_1fr_auto] md:grid-cols-[140px_1fr_auto] gap-5 md:gap-8 items-start luxora-cart-line" data-cart-line data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
								<a href="<?php echo esc_url( $permalink ); ?>" class="aspect-square bg-cream overflow-hidden block">
									<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $_product->get_name() ); ?>" loading="lazy" class="h-full w-full object-cover" />
								</a>
								<div class="min-w-0">
									<?php if ( $brand ) : ?><p class="eyebrow"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
									<?php
									$parent_id = $_product->get_parent_id();
									$product_name = $parent_id ? get_the_title( $parent_id ) : $_product->get_name();
									?>
									<a href="<?php echo esc_url( $permalink ); ?>" class="font-display text-xl mt-1 block link-underline w-fit"><?php echo esc_html( $product_name ); ?></a>
									<?php
									$meta = wc_get_formatted_cart_item_data( $cart_item );
									if ( $meta ) :
										?>
										<div class="text-sm text-muted-foreground mt-1 luxora-cart-meta"><?php echo wp_kses_post( $meta ); ?></div>
									<?php endif; ?>

									<div class="mt-4 inline-flex items-center border border-border luxora-cart-qty" data-cart-qty>
										<button type="button" class="h-9 w-9 grid place-items-center hover:bg-ink hover:text-cream transition" data-qty-minus aria-label="<?php esc_attr_e( 'Decrease quantity', 'luxora' ); ?>"><?php echo luxora_icon( 'minus', 'h-3 w-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
										<input type="number" class="w-10 text-center text-sm bg-transparent outline-none luxora-cart-qty-input" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" min="0" step="1" inputmode="numeric" data-cart-qty-input aria-label="<?php esc_attr_e( 'Quantity', 'luxora' ); ?>" />
										<button type="button" class="h-9 w-9 grid place-items-center hover:bg-ink hover:text-cream transition" data-qty-plus aria-label="<?php esc_attr_e( 'Increase quantity', 'luxora' ); ?>"><?php echo luxora_icon( 'plus', 'h-3 w-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
									</div>
								</div>
								<div class="text-right">
									<p class="font-medium luxora-cart-line-total"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_subtotal', $cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); ?></p>
									<button type="button" class="mt-4 text-muted-foreground hover:text-ink luxora-cart-remove" data-cart-remove aria-label="<?php esc_attr_e( 'Remove item', 'luxora' ); ?>">
										<?php echo luxora_icon( 'x', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</button>
								</div>
							</li>
							<?php
						}

						do_action( 'woocommerce_cart_contents' );
						do_action( 'woocommerce_after_cart_contents' );
						?>
					</ul>

					<div class="hidden">
						<?php
						// Preserve WooCommerce update/coupon controls for no-JS fallback + nonces.
						if ( wc_coupons_enabled() ) :
							?>
							<input type="text" name="coupon_code" class="input-text" value="" />
							<button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'luxora' ); ?>"><?php esc_html_e( 'Apply coupon', 'luxora' ); ?></button>
						<?php endif; ?>
						<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'luxora' ); ?>"><?php esc_html_e( 'Update cart', 'luxora' ); ?></button>
						<?php do_action( 'woocommerce_cart_actions' ); ?>
						<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
					</div>
				<?php else : ?>
					<div class="text-center py-24 luxora-cart-empty">
						<p class="font-display text-2xl mb-6"><?php esc_html_e( 'Your bag is empty.', 'luxora' ); ?></p>
						<a href="<?php echo esc_url( luxora_shop_url() ); ?>" class="btn-luxe"><?php esc_html_e( 'Discover the edit', 'luxora' ); ?></a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $cart && count( $cart->get_cart() ) > 0 ) : ?>
				<aside class="bg-cream p-8 md:p-10 h-fit lg:sticky lg:top-32 luxora-cart-summary">
					<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
					<?php woocommerce_cart_totals(); ?>
				</aside>
			<?php endif; ?>
		</div>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_cart' ); ?>

	<?php
	// Cross-sells / "You may also love".
	$related = luxora_query_products( 'best', 4 );
	if ( $related ) :
		?>
		<div class="mt-32">
			<h2 class="font-display text-3xl mb-10"><?php esc_html_e( 'You may also love', 'luxora' ); ?></h2>
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>
				<?php
				foreach ( $related as $rid ) :
					echo '<div data-reveal-item>';
					luxora_render_product_card( $rid );
					echo '</div>';
				endforeach;
				?>
			</div>
		</div>
		<?php
	endif;
	?>
</section>
