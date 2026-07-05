<?php
/**
 * Single product — mirrors product.$slug.tsx.
 * Override of woocommerce/single-product.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

while ( have_posts() ) :
	the_post();
	global $product;
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}

	/**
	 * Fire notices / structured data hook.
	 */
	do_action( 'woocommerce_before_single_product' );

	$pid        = $product->get_id();
	$brand      = luxora_get_brand( $product );
	$rating     = $product->get_average_rating();
	$rating     = $rating ? number_format( (float) $rating, 1 ) : '5.0';
	$review_cnt = $product->get_review_count();
	$in_stock   = $product->is_in_stock();
	$colors     = luxora_get_colors( $product, 8 );

	// Gallery images: featured first, then gallery.
	$gallery_ids = array();
	if ( $product->get_image_id() ) {
		$gallery_ids[] = $product->get_image_id();
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$gallery_ids[] = $gid;
	}
	$gallery_ids = array_values( array_unique( $gallery_ids ) );

	luxora_breadcrumbs();
	?>

	<section class="container-luxe py-8 md:py-12 grid lg:grid-cols-2 gap-10 lg:gap-20" data-reveal>

		<!-- Gallery -->
		<div class="grid grid-cols-[80px_1fr] gap-4 luxora-gallery" data-gallery>
			<div class="flex flex-col gap-3">
				<?php
				if ( $gallery_ids ) :
					foreach ( $gallery_ids as $i => $gid ) :
						$thumb = wp_get_attachment_image_url( $gid, 'luxora-square' );
						$full  = wp_get_attachment_image_url( $gid, 'luxora-portrait' );
						?>
						<button type="button" class="aspect-square overflow-hidden bg-cream luxora-gallery-thumb <?php echo 0 === $i ? 'ring-1 ring-ink' : 'opacity-60 hover:opacity-100'; ?>" data-gallery-thumb data-full="<?php echo esc_url( $full ); ?>" data-index="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d image number */ __( 'View image %d', 'luxora' ), $i + 1 ) ); ?>">
							<img src="<?php echo esc_url( $thumb ); ?>" alt="" class="h-full w-full object-cover" loading="lazy" />
						</button>
						<?php
					endforeach;
				endif;
				?>
			</div>
			<div class="aspect-[4/5] bg-cream overflow-hidden">
				<?php
				$main = $gallery_ids ? wp_get_attachment_image_url( $gallery_ids[0], 'luxora-portrait' ) : wc_placeholder_img_src( 'luxora-portrait' );
				?>
				<img src="<?php echo esc_url( $main ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="h-full w-full object-cover transition-transform duration-[800ms] hover:scale-110" data-gallery-main fetchpriority="high" />
			</div>
		</div>

		<!-- Details -->
		<div class="lg:pt-6">
			<?php if ( $brand ) : ?><p class="eyebrow mb-4"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
			<h1 class="font-display text-4xl md:text-5xl leading-tight"><?php the_title(); ?></h1>

			<div class="flex items-center gap-4 mt-4">
				<div class="flex items-center gap-1 text-sm">
					<?php echo luxora_icon( 'star-fill', 'h-4 w-4 fill-gold text-gold' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="font-medium"><?php echo esc_html( $rating ); ?></span>
					<span class="text-muted-foreground">(<?php echo esc_html( sprintf( /* translators: %d review count */ _n( '%d review', '%d reviews', $review_cnt, 'luxora' ), $review_cnt ) ); ?>)</span>
				</div>
				<span class="text-xs uppercase tracking-[0.18em] text-muted-foreground"><?php echo $in_stock ? esc_html__( 'In stock', 'luxora' ) : esc_html__( 'Sold out', 'luxora' ); ?></span>
			</div>

			<div class="mt-6 flex items-baseline gap-4 luxora-price font-display text-3xl">
				<?php echo wp_kses_post( $product->get_price_html() ); ?>
			</div>

			<?php
			$desc = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
			if ( $desc ) :
				?>
				<div class="mt-8 font-serif text-lg text-muted-foreground leading-relaxed"><?php echo wp_kses_post( wpautop( $desc ) ); ?></div>
			<?php endif; ?>

			<?php if ( $product->is_type( 'variable' ) ) : ?>
				<!-- Variable products: variation form with custom swatches -->
				<div class="luxora-variations mt-10">
					<?php
					// Pass product into the form builder.
					woocommerce_template_single_add_to_cart();
					?>
				</div>
			<?php else : ?>

				<?php if ( $colors ) : ?>
					<div class="mt-10">
						<div class="flex items-center justify-between mb-4">
							<span class="eyebrow luxora-color-label"><?php esc_html_e( 'Color', 'luxora' ); ?> — <span data-color-name><?php echo esc_html( $colors[0]['name'] ); ?></span></span>
						</div>
						<div class="flex gap-3" data-color-picker>
							<?php foreach ( $colors as $ci => $c ) : ?>
								<button type="button" class="h-10 w-10 rounded-full ring-1 transition <?php echo 0 === $ci ? 'ring-2 ring-ink ring-offset-2' : 'ring-border hover:ring-ink/40'; ?>" style="background: <?php echo esc_attr( $c['hex'] ); ?>" data-color data-color-value="<?php echo esc_attr( $c['name'] ); ?>" aria-label="<?php echo esc_attr( $c['name'] ); ?>"></button>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="mt-10 flex gap-4 luxora-buy">
					<div class="inline-flex items-center border border-ink luxora-qty" data-qty>
						<button type="button" class="h-12 w-12 grid place-items-center hover:bg-ink hover:text-cream transition" data-qty-minus aria-label="<?php esc_attr_e( 'Decrease quantity', 'luxora' ); ?>"><?php echo luxora_icon( 'minus', 'h-3.5 w-3.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
						<input type="number" class="w-10 text-center text-sm font-medium bg-transparent outline-none luxora-qty-input" value="1" min="1" step="1" inputmode="numeric" aria-label="<?php esc_attr_e( 'Quantity', 'luxora' ); ?>" data-qty-input />
						<button type="button" class="h-12 w-12 grid place-items-center hover:bg-ink hover:text-cream transition" data-qty-plus aria-label="<?php esc_attr_e( 'Increase quantity', 'luxora' ); ?>"><?php echo luxora_icon( 'plus', 'h-3.5 w-3.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>
					<button type="button" class="btn-luxe flex-1 luxora-add-to-cart" data-product-id="<?php echo esc_attr( $pid ); ?>" <?php disabled( ! $in_stock || ! $product->is_purchasable() ); ?>>
						<?php echo luxora_icon( 'bag', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo $in_stock ? esc_html__( 'Add to bag', 'luxora' ) : esc_html__( 'Sold out', 'luxora' ); ?>
					</button>
					<button type="button" class="h-12 w-12 grid place-items-center border border-ink hover:bg-ink hover:text-cream transition luxora-wishlist <?php echo luxora_in_wishlist( $pid ) ? 'is-active' : ''; ?>" data-product-id="<?php echo esc_attr( $pid ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'luxora' ); ?>" aria-pressed="<?php echo luxora_in_wishlist( $pid ) ? 'true' : 'false'; ?>">
						<?php echo luxora_icon( 'heart', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>
				</div>
			<?php endif; ?>

			<!-- Promises -->
			<div class="mt-10 grid grid-cols-3 gap-4 text-xs">
				<?php
				$promises = array(
					array( 'truck', __( 'Free delivery', 'luxora' ) ),
					array( 'shield', __( 'Authentic', 'luxora' ) ),
					array( 'refresh', __( '14-day returns', 'luxora' ) ),
				);
				foreach ( $promises as $p ) :
					?>
					<div class="flex flex-col items-center text-center gap-2 py-5 border border-border">
						<?php echo luxora_icon( $p[0], 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="uppercase tracking-[0.15em]"><?php echo esc_html( $p[1] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Specs / accordion -->
			<div class="mt-10 divide-y divide-border border-y border-border luxora-accordion">
				<?php
				$attributes = $product->get_attributes();
				$spec_rows  = array();
				foreach ( $attributes as $attribute ) {
					if ( ! $attribute->get_visible() ) {
						continue;
					}
					$label  = wc_attribute_label( $attribute->get_name() );
					$values = $product->get_attribute( $attribute->get_name() );
					if ( $values ) {
						$spec_rows[] = array( $label, $values );
					}
				}
				// Dimensions / weight fallbacks.
				if ( $product->has_dimensions() ) {
					$spec_rows[] = array( __( 'Dimensions', 'luxora' ), wc_format_dimensions( $product->get_dimensions( false ) ) );
				}
				if ( $product->has_weight() ) {
					$spec_rows[] = array( __( 'Weight', 'luxora' ), wc_format_weight( $product->get_weight() ) );
				}
				if ( $product->get_sku() ) {
					$spec_rows[] = array( __( 'SKU', 'luxora' ), $product->get_sku() );
				}
				?>

				<details class="group py-5" open>
					<summary class="flex justify-between items-center cursor-pointer list-none">
						<span class="font-display text-lg"><?php esc_html_e( 'Specifications', 'luxora' ); ?></span>
						<?php echo luxora_icon( 'chevron-down', 'h-4 w-4 transition-transform group-open:rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</summary>
					<div class="mt-4">
						<?php
						$full_desc = $product->get_description();
						if ( $full_desc ) :
							?>
							<div class="prose-luxe font-serif text-sm text-muted-foreground leading-relaxed mb-6 space-y-4">
								<?php echo wp_kses_post( wpautop( $full_desc ) ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $spec_rows ) : ?>
							<ul class="space-y-2 text-sm">
								<?php foreach ( $spec_rows as $row ) : ?>
									<li class="flex justify-between gap-4">
										<span class="text-muted-foreground"><?php echo esc_html( $row[0] ); ?></span>
										<span class="font-medium text-right"><?php echo esc_html( $row[1] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php elseif ( ! $full_desc ) : ?>
							<p class="text-sm text-muted-foreground"><?php esc_html_e( 'Detailed specifications available on request.', 'luxora' ); ?></p>
						<?php endif; ?>
					</div>
				</details>

				<details class="group py-5">
					<summary class="flex justify-between items-center cursor-pointer list-none">
						<span class="font-display text-lg"><?php esc_html_e( 'Shipping & returns', 'luxora' ); ?></span>
						<?php echo luxora_icon( 'chevron-down', 'h-4 w-4 transition-transform group-open:rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</summary>
					<div class="mt-4">
						<p class="text-sm text-muted-foreground"><?php esc_html_e( 'Complimentary express delivery within 2-4 business days across Bangladesh. Returns accepted within 14 days of receipt.', 'luxora' ); ?></p>
					</div>
				</details>

				<details class="group py-5">
					<summary class="flex justify-between items-center cursor-pointer list-none">
						<span class="font-display text-lg"><?php esc_html_e( 'Care instructions', 'luxora' ); ?></span>
						<?php echo luxora_icon( 'chevron-down', 'h-4 w-4 transition-transform group-open:rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</summary>
					<div class="mt-4">
						<p class="text-sm text-muted-foreground"><?php esc_html_e( 'Store in the provided dustbag. Avoid prolonged exposure to direct sunlight and moisture. Clean with a soft, dry cloth.', 'luxora' ); ?></p>
					</div>
				</details>

				<?php if ( comments_open() || $review_cnt ) : ?>
					<details class="group py-5">
						<summary class="flex justify-between items-center cursor-pointer list-none">
							<span class="font-display text-lg"><?php echo esc_html( sprintf( /* translators: %d reviews */ _n( 'Reviews (%d)', 'Reviews (%d)', $review_cnt, 'luxora' ), $review_cnt ) ); ?></span>
							<?php echo luxora_icon( 'chevron-down', 'h-4 w-4 transition-transform group-open:rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</summary>
						<div class="mt-4 luxora-reviews">
							<?php comments_template(); ?>
						</div>
					</details>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php
	// Related products via our card grid.
	$related_ids = wc_get_related_products( $pid, 4 );
	if ( $related_ids ) :
		?>
		<section class="container-luxe py-24 md:py-32">
			<?php
			luxora_section_heading(
				array(
					'eyebrow' => __( 'You may also love', 'luxora' ),
					'title'   => __( 'Pairs beautifully', 'luxora' ),
				)
			);
			?>
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>
				<?php
				foreach ( $related_ids as $rid ) :
					echo '<div data-reveal-item>';
					luxora_render_product_card( $rid );
					echo '</div>';
				endforeach;
				?>
			</div>
		</section>
		<?php
	endif;

	do_action( 'woocommerce_after_single_product' );

endwhile;

get_footer( 'shop' );
