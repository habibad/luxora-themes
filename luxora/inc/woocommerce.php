<?php
/**
 * WooCommerce integration.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bail early if WooCommerce isn't active.
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	/**
	 * Admin notice prompting WooCommerce activation.
	 */
	function luxora_woocommerce_notice() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'Luxora is designed to run with WooCommerce. Please install & activate WooCommerce to unlock the shop, cart, checkout and account pages.', 'luxora' );
		echo '</p></div>';
	}
	add_action( 'admin_notices', 'luxora_woocommerce_notice' );
	return;
}

/* -------------------------------------------------------------------------
 * Theme-level WooCommerce tweaks
 * ---------------------------------------------------------------------- */

/**
 * Products per row / per page.
 */
add_filter( 'loop_shop_columns', function () { return 3; } );
add_filter( 'loop_shop_per_page', function () { return 12; } );

/**
 * Related products: 4 items, 4 columns (matches "Pairs beautifully").
 */
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

/**
 * Up-sells columns.
 */
add_filter( 'woocommerce_upsell_display_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

/* -------------------------------------------------------------------------
 * Remove default WooCommerce chrome; we supply our own in the templates.
 * ---------------------------------------------------------------------- */

// Default content wrappers — replaced by our own .container-luxe wrappers.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// Default breadcrumb (we render our own semantic breadcrumb).
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// Sidebar — shop layout is bespoke.
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Default result count & ordering placement — we place these ourselves.
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Our shop content wrappers.
 */
function luxora_before_main_content() {
	echo '<div class="luxora-woo container-luxe">';
}
add_action( 'woocommerce_before_main_content', 'luxora_before_main_content', 10 );

function luxora_after_main_content() {
	echo '</div>';
}
add_action( 'woocommerce_after_main_content', 'luxora_after_main_content', 10 );

/* -------------------------------------------------------------------------
 * Loop product card — funnel WooCommerce's loop into our shared card design.
 * The card itself is rendered by content-product.php (template override).
 * We strip the default loop hooks so only our card markup remains.
 * ---------------------------------------------------------------------- */

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

/* -------------------------------------------------------------------------
 * Single product layout — rebuild with our hooks.
 * ---------------------------------------------------------------------- */

// Use our gallery columns.
add_filter( 'woocommerce_product_thumbnails_columns', function () { return 4; } );

/* -------------------------------------------------------------------------
 * Recently Viewed products
 * ---------------------------------------------------------------------- */

/**
 * Track recently viewed products in a cookie.
 */
function luxora_track_recently_viewed() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}
	global $post;

	$viewed = isset( $_COOKIE['luxora_recently_viewed'] ) ? (array) explode( '|', sanitize_text_field( wp_unslash( $_COOKIE['luxora_recently_viewed'] ) ) ) : array();
	$viewed = array_filter( array_map( 'absint', $viewed ) );

	$viewed = array_diff( $viewed, array( $post->ID ) );
	array_unshift( $viewed, $post->ID );
	$viewed = array_slice( array_unique( $viewed ), 0, 8 );

	wc_setcookie( 'luxora_recently_viewed', implode( '|', $viewed ) );
}
add_action( 'template_redirect', 'luxora_track_recently_viewed', 20 );

/**
 * Get recently viewed product IDs (excluding the current product).
 *
 * @param int $exclude Product ID to exclude.
 * @return int[]
 */
function luxora_get_recently_viewed( $exclude = 0 ) {
	$viewed = isset( $_COOKIE['luxora_recently_viewed'] ) ? (array) explode( '|', sanitize_text_field( wp_unslash( $_COOKIE['luxora_recently_viewed'] ) ) ) : array();
	$viewed = array_filter( array_map( 'absint', $viewed ) );
	if ( $exclude ) {
		$viewed = array_diff( $viewed, array( absint( $exclude ) ) );
	}
	return array_values( $viewed );
}

/* -------------------------------------------------------------------------
 * Header mini-cart endpoint helpers
 * ---------------------------------------------------------------------- */

/**
 * Number formatting safety for the placeholder price image size.
 */
add_filter( 'woocommerce_get_image_size_single', function ( $size ) {
	$size['width']  = 900;
	$size['height'] = 1125;
	$size['crop']   = 1;
	return $size;
} );
add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function ( $size ) {
	$size['width']  = 160;
	$size['height'] = 160;
	$size['crop']   = 1;
	return $size;
} );
add_filter( 'woocommerce_get_image_size_thumbnail', function ( $size ) {
	$size['width']  = 720;
	$size['height'] = 900;
	$size['crop']   = 1;
	return $size;
} );

/**
 * Move the sale flash text styling note: WooCommerce sale flash handled in template.
 */

/**
 * Product query helpers for the homepage (best-sellers / new / featured).
 *
 * @param string $type  one of: featured|new|best|sale|recent.
 * @param int    $limit Number of products.
 * @return WC_Product[]
 */
function luxora_query_products( $type = 'recent', $limit = 4 ) {
	$args = array(
		'status'     => 'publish',
		'limit'      => $limit,
		'orderby'    => 'date',
		'order'      => 'DESC',
		'return'     => 'ids',
		'visibility' => 'catalog',
	);

	switch ( $type ) {
		case 'featured':
			$args['featured'] = true;
			break;
		case 'sale':
			$args['include'] = wc_get_product_ids_on_sale();
			break;
		case 'best':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['orderby']  = 'meta_value_num';
			break;
		case 'new':
			$tag_ids = array();
			foreach ( array( 'new', 'new-arrivals', 'new-arrival' ) as $slug ) {
				$term = get_term_by( 'slug', $slug, 'product_tag' );
				if ( $term ) {
					$tag_ids[] = $term->term_id;
				}
			}
			if ( $tag_ids ) {
				$args['tag_id'] = implode( ',', $tag_ids );
			}
			break;
	}

	$products = wc_get_products( $args );

	// Graceful fallback to recent products if a query returns nothing.
	if ( empty( $products ) && 'recent' !== $type ) {
		$products = wc_get_products(
			array(
				'status'     => 'publish',
				'limit'      => $limit,
				'orderby'    => 'date',
				'order'      => 'DESC',
				'visibility' => 'catalog',
				'return'     => 'ids',
			)
		);
	}

	return $products;
}

/**
 * Render a responsive product grid from an array of products using our card.
 *
 * @param WC_Product[] $products Products.
 * @param string       $cols     Grid columns classes.
 */
function luxora_product_grid( $products, $cols = 'grid-cols-2 lg:grid-cols-4' ) {
	if ( empty( $products ) ) {
		return;
	}
	echo '<div class="grid ' . esc_attr( $cols ) . ' gap-x-5 gap-y-12 md:gap-x-8" data-reveal-stagger>';
	foreach ( $products as $product ) {
		luxora_render_product_card( $product );
	}
	echo '</div>';
}

/**
 * Custom catalog ordering dropdown (mirrors the "Sort: Featured" control).
 */
function luxora_catalog_ordering() {
	$orderby_options = array(
		'menu_order' => __( 'Featured', 'luxora' ),
		'popularity' => __( 'Best selling', 'luxora' ),
		'rating'     => __( 'Top rated', 'luxora' ),
		'date'       => __( 'Newest', 'luxora' ),
		'price'      => __( 'Price: low to high', 'luxora' ),
		'price-desc' => __( 'Price: high to low', 'luxora' ),
	);

	$current = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : 'menu_order'; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! array_key_exists( $current, $orderby_options ) ) {
		$current = 'menu_order';
	}
	?>
	<div class="luxora-ordering relative">
		<button type="button" class="inline-flex items-center gap-2 text-sm uppercase tracking-[0.18em]" data-ordering-toggle aria-haspopup="listbox" aria-expanded="false">
			<?php esc_html_e( 'Sort:', 'luxora' ); ?> <span data-ordering-label><?php echo esc_html( $orderby_options[ $current ] ); ?></span>
			<?php echo luxora_icon( 'chevron-down', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>
		<ul class="luxora-ordering-menu absolute right-0 top-full mt-3 z-30 min-w-[220px] bg-background border border-border shadow-xl hidden" role="listbox" data-ordering-menu>
			<?php foreach ( $orderby_options as $key => $label ) : ?>
				<li role="option" aria-selected="<?php echo esc_attr( $key === $current ? 'true' : 'false' ); ?>">
					<a href="<?php echo esc_url( add_query_arg( 'orderby', $key ) ); ?>" class="block px-5 py-3 text-sm hover:bg-cream <?php echo $key === $current ? 'text-gold' : ''; ?>"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Load-more control. Uses pagination-aware AJAX; degrades to a real link.
 */
function luxora_shop_load_more() {
	global $wp_query;
	$max   = (int) $wp_query->max_num_pages;
	$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

	if ( $max <= 1 ) {
		return;
	}

	echo '<div class="mt-16 flex flex-col items-center gap-6" data-load-more-wrap>';

	if ( $paged < $max ) {
		$next = get_next_posts_page_link( $max );
		printf(
			'<a href="%1$s" class="btn-luxe-ghost luxora-load-more" data-load-more data-page="%2$d" data-max="%3$d">%4$s</a>',
			esc_url( $next ),
			esc_attr( $paged + 1 ),
			esc_attr( $max ),
			esc_html__( 'Load more', 'luxora' )
		);
	}

	// Numbered fallback for no-JS.
	echo '<div class="luxora-numeric-pagination">';
	luxora_pagination();
	echo '</div>';

	echo '</div>';
}

/**
 * Translate the custom shop filter GET params into the main product query.
 *
 * @param WP_Query $q Query.
 */
function luxora_apply_shop_filters( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( ! ( ( function_exists( 'is_shop' ) && is_shop() ) || is_product_taxonomy() ) ) {
		return;
	}

	$tax_query  = (array) $q->get( 'tax_query' );
	$meta_query = (array) $q->get( 'meta_query' );

	// Category checkboxes.
	if ( isset( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$slugs = array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_cat'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$slugs = array_filter( $slugs );
		if ( $slugs ) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $slugs,
			);
		}
	}

	// Color radio.
	if ( ! empty( $_GET['filter_color'] ) && taxonomy_exists( 'pa_color' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$tax_query[] = array(
			'taxonomy' => 'pa_color',
			'field'    => 'slug',
			'terms'    => array( sanitize_title( wp_unslash( $_GET['filter_color'] ) ) ), // phpcs:ignore WordPress.Security.NonceVerification
		);
	}

	// Price band.
	$min = isset( $_GET['min_price'] ) ? (float) wp_unslash( $_GET['min_price'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification
	$max = isset( $_GET['max_price'] ) ? (float) wp_unslash( $_GET['max_price'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification
	if ( null !== $min || null !== $max ) {
		$price_meta = array( 'key' => '_price', 'type' => 'NUMERIC' );
		if ( null !== $min && ! empty( $max ) ) {
			$price_meta['value']   = array( $min, $max );
			$price_meta['compare'] = 'BETWEEN';
		} elseif ( null !== $min ) {
			$price_meta['value']   = $min;
			$price_meta['compare'] = '>=';
		} else {
			$price_meta['value']   = $max;
			$price_meta['compare'] = '<=';
		}
		$meta_query[] = $price_meta;
	}

	if ( $tax_query ) {
		$q->set( 'tax_query', $tax_query );
	}
	if ( $meta_query ) {
		$q->set( 'meta_query', $meta_query );
	}
}
add_action( 'pre_get_posts', 'luxora_apply_shop_filters' );

/**
 * Replace the default "Proceed to checkout" button with a styled one.
 */
remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
function luxora_proceed_to_checkout_button() {
	?>
	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-luxe w-full justify-center mt-8 luxora-checkout-btn">
		<?php esc_html_e( 'Checkout', 'luxora' ); ?> <?php echo luxora_icon( 'arrow-right', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
	<?php
}
add_action( 'woocommerce_proceed_to_checkout', 'luxora_proceed_to_checkout_button', 20 );
