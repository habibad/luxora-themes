<?php
/**
 * Reusable display helpers + breadcrumbs.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function luxora_woo_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * URL to a bundled theme image.
 *
 * @param string $file File name within assets/images/.
 * @return string
 */
function luxora_asset_img( $file ) {
	return LUXORA_URI . '/assets/images/' . ltrim( $file, '/' );
}

/**
 * Shop page URL, optionally with query args (orderby, etc.).
 *
 * @param array $args Query args.
 * @return string
 */
function luxora_shop_url( $args = array() ) {
	$url = luxora_woo_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	if ( ! empty( $args ) ) {
		$url = add_query_arg( $args, $url );
	}
	return $url;
}

/**
 * Collections URL — a page titled "Collections", else the shop.
 *
 * @return string
 */
function luxora_collections_url() {
	return luxora_page_url_by_title( 'Collections', luxora_shop_url() );
}

/**
 * Resolve a page URL by title with a fallback path.
 *
 * @param string $title    Page title.
 * @param string $fallback Fallback relative path or absolute URL.
 * @return string
 */
function luxora_page_url_by_title( $title, $fallback = '/' ) {
	$pages = get_posts(
		array(
			'post_type'              => 'page',
			'title'                  => $title,
			'post_status'            => 'publish',
			'numberposts'            => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	if ( ! empty( $pages ) ) {
		return get_permalink( $pages[0] );
	}
	return ( 0 === strpos( $fallback, 'http' ) ) ? $fallback : home_url( $fallback );
}

/**
 * Known color-name => hex map for swatches (mirrors the source palette).
 *
 * @return array
 */
function luxora_color_map() {
	return apply_filters(
		'luxora_color_map',
		array(
			'camel'     => '#C8A96A',
			'noir'      => '#111111',
			'black'     => '#111111',
			'cream'     => '#FAF8F5',
			'ivory'     => '#F5EFE6',
			'ivoire'    => '#F5EFE6',
			'bordeaux'  => '#5B1A20',
			'tan'       => '#B98A57',
			'sand'      => '#D9C5A6',
			'blush'     => '#E8C7C0',
			'champagne' => '#D8C39A',
			'cognac'    => '#9A5B2A',
			'gold'      => '#C8A96A',
			'white'     => '#FFFFFF',
			'beige'     => '#EEE6DA',
		)
	);
}

/**
 * Resolve a hex colour for a swatch from a name or stored term meta.
 *
 * @param string $name Color name.
 * @return string Hex value.
 */
function luxora_color_hex( $name ) {
	$map = luxora_color_map();
	$key = strtolower( trim( wp_strip_all_tags( $name ) ) );
	if ( isset( $map[ $key ] ) ) {
		return $map[ $key ];
	}
	// Try CSS-named colour as a graceful fallback; otherwise neutral grey.
	return $key ? $key : '#D9C5A6';
}

/**
 * Get a product's "brand" — pa_brand attribute, custom 'brand' attribute,
 * else its primary category name.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function luxora_get_brand( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	foreach ( array( 'pa_brand', 'brand', 'pa_designer', 'designer' ) as $attr ) {
		$value = $product->get_attribute( $attr );
		if ( $value ) {
			$parts = array_map( 'trim', explode( ',', $value ) );
			return esc_html( $parts[0] );
		}
	}

	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		return esc_html( $terms[0]->name );
	}

	return '';
}

/**
 * Get up to N colour swatches for a product from its colour attribute.
 *
 * @param WC_Product $product Product.
 * @param int        $limit   Max swatches.
 * @return array[] Each item: [ 'name' => string, 'hex' => string ].
 */
function luxora_get_colors( $product, $limit = 3 ) {
	$out = array();
	if ( ! $product instanceof WC_Product ) {
		return $out;
	}

	$candidates = array( 'pa_color', 'pa_colour', 'color', 'colour' );
	foreach ( $candidates as $attr ) {
		$value = $product->get_attribute( $attr );
		if ( $value ) {
			$names = array_map( 'trim', explode( ',', $value ) );
			foreach ( $names as $name ) {
				if ( '' === $name ) {
					continue;
				}
				$out[] = array(
					'name' => $name,
					'hex'  => luxora_color_hex( $name ),
				);
				if ( count( $out ) >= $limit ) {
					break 2;
				}
			}
			break;
		}
	}

	return $out;
}

/**
 * Whether a product should display the "New" marker.
 *
 * @param WC_Product $product Product.
 * @return bool
 */
function luxora_is_new( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return false;
	}
	if ( has_term( array( 'new', 'new-arrivals', 'new-arrival' ), 'product_tag', $product->get_id() ) ) {
		return true;
	}
	$days    = (int) apply_filters( 'luxora_new_product_days', 30 );
	$created = $product->get_date_created();
	if ( $created ) {
		return ( time() - $created->getTimestamp() ) < ( $days * DAY_IN_SECONDS );
	}
	return false;
}

/**
 * Get the product card "hover" image URL (2nd gallery image, else featured).
 *
 * @param WC_Product $product Product.
 * @param string     $size    Image size.
 * @return string
 */
function luxora_hover_image_url( $product, $size = 'luxora-card' ) {
	$gallery = $product instanceof WC_Product ? $product->get_gallery_image_ids() : array();
	if ( ! empty( $gallery ) ) {
		$url = wp_get_attachment_image_url( $gallery[0], $size );
		if ( $url ) {
			return $url;
		}
	}
	$id = $product->get_image_id();
	return $id ? (string) wp_get_attachment_image_url( $id, $size ) : wc_placeholder_img_src( $size );
}

/**
 * Render the signature Luxora product card. Used by the homepage sections AND
 * the WooCommerce loop (woocommerce/content-product.php) so the design is
 * shared in one place.
 *
 * @param WC_Product|int|null $product Product or ID. Defaults to global.
 */
function luxora_render_product_card( $product = null ) {
	if ( null === $product ) {
		global $product;
	}
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );
	}
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$pid       = $product->get_id();
	$link      = get_permalink( $pid );
	$name      = $product->get_name();
	$brand     = luxora_get_brand( $product );
	$in_stock  = $product->is_in_stock();
	$on_sale   = $product->is_on_sale();
	$is_new    = luxora_is_new( $product );
	$rating    = $product->get_average_rating();
	$colors    = luxora_get_colors( $product, 3 );
	$img_id    = $product->get_image_id();
	$main_img  = $img_id ? wp_get_attachment_image_url( $img_id, 'luxora-card' ) : wc_placeholder_img_src( 'luxora-card' );
	$hover_img = luxora_hover_image_url( $product, 'luxora-card' );
	$rating    = $rating ? number_format( (float) $rating, 1 ) : '5.0';
	?>
	<div class="group relative luxora-card" data-product-id="<?php echo esc_attr( $pid ); ?>">
		<a href="<?php echo esc_url( $link ); ?>" class="block">
			<div class="relative overflow-hidden bg-cream aspect-[4/5]">
				<?php if ( ! $in_stock ) : ?>
					<span class="absolute top-4 left-4 z-10 bg-ink text-cream text-[10px] tracking-[0.22em] uppercase px-3 py-1.5"><?php esc_html_e( 'Sold out', 'luxora' ); ?></span>
				<?php endif; ?>
				<?php if ( $on_sale && $in_stock ) : ?>
					<span class="absolute top-4 left-4 z-10 bg-gold text-ink text-[10px] tracking-[0.22em] uppercase px-3 py-1.5"><?php esc_html_e( 'Sale', 'luxora' ); ?></span>
				<?php endif; ?>
				<?php if ( $is_new ) : ?>
					<span class="absolute top-4 right-4 z-10 text-[10px] tracking-[0.22em] uppercase text-ink/70"><?php esc_html_e( 'New', 'luxora' ); ?></span>
				<?php endif; ?>

				<img src="<?php echo esc_url( $main_img ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-all duration-[900ms] group-hover:scale-105 group-hover:opacity-0" />
				<img src="<?php echo esc_url( $hover_img ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-[900ms] group-hover:opacity-100" />

				<div class="absolute bottom-4 left-4 right-4 flex items-center justify-between opacity-0 translate-y-2 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
					<button type="button" class="luxora-add-to-cart flex-1 bg-ink/95 text-cream text-[11px] tracking-[0.22em] uppercase py-3 backdrop-blur hover:bg-gold hover:text-ink transition disabled:opacity-60"
						data-product-id="<?php echo esc_attr( $pid ); ?>"
						<?php disabled( ! $in_stock || ! $product->is_purchasable() ); ?>>
						<?php echo luxora_icon( 'bag', 'inline h-3.5 w-3.5 mr-2' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $in_stock ? esc_html__( 'Add to bag', 'luxora' ) : esc_html__( 'Sold out', 'luxora' ); ?>
					</button>
					<a href="<?php echo esc_url( $link ); ?>" class="ml-2 h-11 w-11 grid place-items-center bg-cream/95 hover:bg-gold transition" aria-label="<?php esc_attr_e( 'Quick view', 'luxora' ); ?>">
						<?php echo luxora_icon( 'eye', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				</div>

				<button type="button" class="luxora-wishlist absolute top-4 right-4 h-9 w-9 grid place-items-center bg-cream/0 hover:bg-cream/95 rounded-full transition <?php echo luxora_in_wishlist( $pid ) ? 'is-active' : ''; ?>" data-product-id="<?php echo esc_attr( $pid ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'luxora' ); ?>" aria-pressed="<?php echo luxora_in_wishlist( $pid ) ? 'true' : 'false'; ?>">
					<?php echo luxora_icon( 'heart', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			</div>

			<div class="pt-5 pb-2">
				<div class="flex items-start justify-between gap-3">
					<div class="min-w-0">
						<?php if ( $brand ) : ?><p class="eyebrow"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
						<h3 class="mt-2 font-display text-lg leading-tight truncate"><?php echo esc_html( $name ); ?></h3>
					</div>
					<div class="flex items-center gap-1 text-xs text-muted-foreground shrink-0 mt-1">
						<?php echo luxora_icon( 'star-fill', 'h-3 w-3 fill-gold text-gold' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( $rating ); ?>
					</div>
				</div>

				<div class="mt-2 flex items-center justify-between">
					<div class="flex items-baseline gap-2 luxora-price">
						<?php echo wp_kses_post( $product->get_price_html() ); ?>
					</div>
					<?php if ( $colors ) : ?>
						<div class="flex gap-1.5">
							<?php foreach ( $colors as $c ) : ?>
								<span class="h-3 w-3 rounded-full ring-1 ring-border" style="background: <?php echo esc_attr( $c['hex'] ); ?>" title="<?php echo esc_attr( $c['name'] ); ?>"></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</a>
	</div>
	<?php
}

/**
 * Section heading partial (mirrors SectionHeading.tsx).
 *
 * @param array $args eyebrow, title, subtitle, align (center|left|between), action (HTML).
 */
function luxora_section_heading( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow'  => '',
			'title'    => '',
			'subtitle' => '',
			'align'    => 'center',
			'action'   => '',
		)
	);

	if ( 'between' === $args['align'] ) {
		?>
		<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12" data-reveal>
			<div>
				<?php if ( $args['eyebrow'] ) : ?><p class="eyebrow mb-4"><?php echo esc_html( $args['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="font-display text-4xl md:text-5xl tracking-tight"><?php echo esc_html( $args['title'] ); ?></h2>
				<?php if ( $args['subtitle'] ) : ?><p class="mt-4 font-serif text-lg text-muted-foreground max-w-xl"><?php echo esc_html( $args['subtitle'] ); ?></p><?php endif; ?>
			</div>
			<?php echo $args['action']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php
		return;
	}
	$center = 'center' === $args['align'] ? 'text-center mx-auto max-w-2xl' : '';
	?>
	<div class="mb-14 <?php echo esc_attr( $center ); ?>" data-reveal>
		<?php if ( $args['eyebrow'] ) : ?><p class="eyebrow mb-5"><?php echo esc_html( $args['eyebrow'] ); ?></p><?php endif; ?>
		<h2 class="font-display text-4xl md:text-5xl lg:text-6xl tracking-tight"><?php echo esc_html( $args['title'] ); ?></h2>
		<?php if ( $args['subtitle'] ) : ?><p class="mt-5 font-serif text-lg md:text-xl text-muted-foreground leading-relaxed"><?php echo esc_html( $args['subtitle'] ); ?></p><?php endif; ?>
	</div>
	<?php
}

/**
 * Breadcrumbs (semantic, schema-friendly). Mirrors the on-page breadcrumb style.
 */
function luxora_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$sep   = '<span class="mx-2" aria-hidden="true">/</span>';
	$home  = home_url( '/' );
	$items = array();

	$items[] = '<a href="' . esc_url( $home ) . '" class="hover:text-ink">' . esc_html__( 'Home', 'luxora' ) . '</a>';

	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() || is_product() ) ) {
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id && ! is_shop() ) {
			$items[] = '<a href="' . esc_url( get_permalink( $shop_id ) ) . '" class="hover:text-ink">' . esc_html__( 'Shop', 'luxora' ) . '</a>';
		}
		if ( is_product() ) {
			$items[] = '<span class="text-ink">' . esc_html( get_the_title() ) . '</span>';
		} elseif ( is_product_category() || is_product_tag() ) {
			$items[] = '<span class="text-ink">' . esc_html( single_term_title( '', false ) ) . '</span>';
		} elseif ( is_shop() ) {
			$items[] = '<span class="text-ink">' . esc_html__( 'Shop', 'luxora' ) . '</span>';
		}
	} elseif ( is_singular() ) {
		$items[] = '<span class="text-ink">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$items[] = '<span class="text-ink">' . esc_html( single_term_title( '', false ) ) . '</span>';
	} elseif ( is_search() ) {
		$items[] = '<span class="text-ink">' . esc_html__( 'Search', 'luxora' ) . '</span>';
	} elseif ( is_404() ) {
		$items[] = '<span class="text-ink">' . esc_html__( 'Not found', 'luxora' ) . '</span>';
	} else {
		$items[] = '<span class="text-ink">' . esc_html( wp_get_document_title() ) . '</span>';
	}

	// echo '<nav class="container-luxe pt-8 pb-4 text-xs uppercase tracking-[0.18em] text-muted-foreground" aria-label="' . esc_attr__( 'Breadcrumb', 'luxora' ) . '">';
	// echo wp_kses_post( implode( $sep, $items ) );
	// echo '</nav>';
}

/**
 * Numbered pagination styled for the maison.
 */
function luxora_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'array',
			'prev_text' => luxora_icon( 'arrow-right', 'h-4 w-4 rotate-180' ) . '<span class="sr-only">' . esc_html__( 'Previous', 'luxora' ) . '</span>',
			'next_text' => '<span class="sr-only">' . esc_html__( 'Next', 'luxora' ) . '</span>' . luxora_icon( 'arrow-right', 'h-4 w-4' ),
		)
	);

	if ( empty( $links ) ) {
		return;
	}

	echo '<nav class="mt-16 flex justify-center" aria-label="' . esc_attr__( 'Pagination', 'luxora' ) . '"><ul class="flex items-center gap-2">';
	foreach ( $links as $link ) {
		$is_current = false !== strpos( $link, 'current' );
		$base       = 'h-11 min-w-11 px-3 grid place-items-center text-sm border transition';
		$state      = $is_current ? ' bg-ink text-cream border-ink' : ' border-border hover:border-gold';
		// Inject our classes into the generated anchor/span.
		$link = preg_replace( '/class="(page-numbers[^"]*)"/', 'class="$1 ' . esc_attr( $base . $state ) . '"', $link );
		if ( false === strpos( $link, 'class="page-numbers' ) ) {
			$link = str_replace( 'page-numbers', 'page-numbers ' . esc_attr( $base . $state ), $link );
		}
		echo '<li>' . wp_kses_post( $link ) . '</li>';
	}
	echo '</ul></nav>';
}

/**
 * Minimal inline SVG icon set (Lucide-style, stroke 1.4) — replaces lucide-react.
 *
 * @param string $name  Icon key.
 * @param string $class CSS classes.
 * @return string SVG markup.
 */
function luxora_icon( $name, $class = '' ) {
	$attrs = 'xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="' . esc_attr( $class ) . '" aria-hidden="true"';

	$paths = array(
		'search'   => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'heart'    => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
		'bag'      => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
		'user'     => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
		'menu'     => '<line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>',
		'x'        => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'arrow-right'  => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'arrow-up-right' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
		'eye'      => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>',
		'star'     => '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>',
		'truck'    => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
		'shield'   => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
		'sparkles' => '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>',
		'refresh'  => '<path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/>',
		'minus'    => '<path d="M5 12h14"/>',
		'plus'     => '<path d="M5 12h14"/><path d="M12 5v14"/>',
		'instagram'=> '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>',
		'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'youtube'  => '<path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/>',
		'twitter'  => '<path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>',
		'lock'     => '<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
		'check'    => '<path d="M20 6 9 17l-5-5"/>',
		'package'  => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
		'map-pin'  => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
		'sliders'  => '<line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/>',
		'mail'     => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'phone'    => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>',
	);

	$body = isset( $paths[ $name ] ) ? $paths[ $name ] : '';
	if ( 'star-fill' === $name ) {
		$attrs = str_replace( 'fill="none"', 'fill="currentColor"', $attrs );
		$body  = $paths['star'];
	}

	return '<svg ' . $attrs . '>' . $body . '</svg>';
}

/**
 * Output the cart contents count (used by the header bubble).
 *
 * @return int
 */
function luxora_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}
	return 0;
}

/**
 * Flat menu walker — renders anchors only (no <ul>/<li>), so horizontal
 * flex navs match the original React markup. Falls back gracefully.
 */
class Luxora_Flat_Walker extends Walker_Nav_Menu {
	/**
	 * Start element (anchor only).
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$atts          = array();
		$atts['href']  = ! empty( $item->url ) ? $item->url : '';
		$atts['class'] = isset( $args->link_class ) ? $args->link_class : '';

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' !== $value ) {
				$attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$title   = apply_filters( 'the_title', $item->title, $item->ID );
		$output .= '<a' . $attributes . '>' . esc_html( $title ) . '</a>';
	}

	/**
	 * No list item end.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}

	/**
	 * No <ul> wrappers.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}
}

/**
 * Output a flat (anchor-only) nav menu for a theme location.
 *
 * @param string $location   Theme location.
 * @param string $link_class Class applied to each anchor.
 * @param string $fallback   Optional fallback HTML when no menu assigned.
 */
function luxora_flat_menu( $location, $link_class = '', $fallback = '' ) {
	if ( ! has_nav_menu( $location ) ) {
		echo $fallback; // phpcs:ignore WordPress.Security.EscapeOutput
		return;
	}
	$args = array(
		'theme_location' => $location,
		'container'      => false,
		'items_wrap'     => '%3$s',
		'walker'         => new Luxora_Flat_Walker(),
		'echo'           => true,
		'depth'          => 1,
	);
	$args['link_class'] = $link_class;
	wp_nav_menu( $args );
}
