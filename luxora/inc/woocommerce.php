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

/* -------------------------------------------------------------------------
 * Variation swatches — replace native <select> with swatch buttons.
 * ---------------------------------------------------------------------- */

/**
 * Determine whether an attribute name is colour-like.
 *
 * @param string $name Attribute name / taxonomy slug.
 * @return bool
 */
function luxora_is_color_attribute( $name ) {
	$name = strtolower( $name );
	return in_array( $name, array( 'color', 'colour', 'pa_color', 'pa_colour' ), true )
		|| false !== strpos( $name, 'color' )
		|| false !== strpos( $name, 'colour' );
}

/**
 * Replace WooCommerce's <select> with custom swatch / button markup.
 * Outputs: hidden native select + swatch buttons + attribute label header.
 *
 * @param string $html    Default WooCommerce dropdown HTML.
 * @param array  $args    Dropdown arguments.
 * @return string         Replaced markup.
 */
function luxora_variation_swatch_html( $html, $args ) {
	$attribute = isset( $args['attribute'] ) ? $args['attribute'] : '';
	$options   = isset( $args['options'] ) ? $args['options'] : array();
	$product   = isset( $args['product'] ) ? $args['product'] : null;
	$name      = isset( $args['name'] ) ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$selected  = isset( $args['selected'] ) ? $args['selected'] : '';
	$id        = isset( $args['id'] ) ? $args['id'] : sanitize_title( $attribute );

	if ( empty( $options ) || ! $product ) {
		return $html;
	}

	// Resolve slugs → term objects when taxonomy exists.
	$terms = array();
	if ( taxonomy_exists( $attribute ) ) {
		$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
	}

	$is_color = luxora_is_color_attribute( $attribute );

	// Human-readable attribute label (e.g. "Color").
	$attr_label = wc_attribute_label( $attribute, $product );

	// Determine the initially selected label.
	$selected_label = '';
	if ( $selected ) {
		if ( $terms ) {
			foreach ( $terms as $term ) {
				if ( $term->slug === $selected || $term->name === $selected ) {
					$selected_label = $term->name;
					break;
				}
			}
		}
		if ( ! $selected_label ) {
			$selected_label = $selected;
		}
	}

	// Build the hidden native select (required by WooCommerce variation.js).
	$native  = '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="luxora-swatch-select" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" aria-hidden="true" style="display:none;">';
	$native .= '<option value="">' . esc_html__( 'Choose an option', 'luxora' ) . '</option>';
	foreach ( $options as $option ) {
		$native .= '<option value="' . esc_attr( $option ) . '"' . selected( $selected, $option, false ) . '>' . esc_html( $option ) . '</option>';
	}
	$native .= '</select>';

	// Attribute label row: "COLOR — Camel".
	$label_text = $selected_label
		? esc_html( $attr_label ) . ' <span class="text-muted-foreground text-xs mx-1">—</span> <span class="text-ink font-medium" data-swatch-label="' . esc_attr( $attribute ) . '">' . esc_html( $selected_label ) . '</span>'
		: esc_html( $attr_label ) . ' <span class="text-muted-foreground text-xs mx-1">—</span> <span class="text-ink font-medium" data-swatch-label="' . esc_attr( $attribute ) . '">' . esc_html__( 'Choose an option', 'luxora' ) . '</span>';

	$header = '<div class="flex items-center gap-1 mb-4">';
	$header .= '<span class="eyebrow">' . $label_text . '</span>';
	$header .= '</div>';

	// Build the swatch row.
	$swatches = '<div class="luxora-swatch-wrap" data-swatch-group="' . esc_attr( $attribute ) . '" data-select-id="' . esc_attr( $id ) . '">';

	foreach ( $options as $option ) {
		$term_slug = '';
		$hex       = '';
		$label     = $option;

		// Try to get term meta colour hex.
		if ( $terms ) {
			foreach ( $terms as $term ) {
				if ( $term->slug === $option || $term->name === $option ) {
					$label     = $term->name;
					$term_slug = $term->slug;
					// Try multiple meta keys — support Luxora's own key + popular plugins.
					$hex = get_term_meta( $term->term_id, 'product_attribute_color', true )
						?: get_term_meta( $term->term_id, 'color', true )
						?: get_term_meta( $term->term_id, 'pa_color', true )
						?: get_term_meta( $term->term_id, 'swatch_color', true )
						?: '';
					break;
				}
			}
		}

		$is_selected = ( $option === $selected || $term_slug === $selected );

		if ( $is_color ) {
			// Colour swatch circle.
			if ( ! $hex ) {
				// Map common English colour names to hex as a fallback.
				$name_map = array(
					'cream'    => '#FAF8F5', 'beige'    => '#EEE6DA', 'black'    => '#111111',
					'white'    => '#FFFFFF', 'brown'    => '#8B6C5C', 'tan'      => '#D4A96A',
					'navy'     => '#1E3A5F', 'red'      => '#C0392B', 'green'    => '#2E7D32',
					'blue'     => '#1565C0', 'grey'     => '#9E9E9E', 'gray'     => '#9E9E9E',
					'gold'     => '#C8A96A', 'camel'    => '#C19A6B', 'noir'     => '#111111',
					'sand'     => '#C4A882', 'olive'    => '#808000', 'pink'     => '#E91E8C',
					'burgundy' => '#800020', 'cognac'   => '#9A4612', 'ivory'    => '#FFFFF0',
					'taupe'    => '#8B8589', 'blush'    => '#DE5D83', 'cognac'   => '#9A4612',
					'forest'   => '#228B22', 'mustard'  => '#FFDB58', 'slate'    => '#708090',
				);
				$hex = isset( $name_map[ strtolower( $label ) ] ) ? $name_map[ strtolower( $label ) ] : '#cccccc';
			}

			$active_class = $is_selected ? 'is-active' : '';
			$swatches    .= sprintf(
				'<button type="button" class="luxora-swatch-color %s" data-value="%s" data-label="%s" style="background:%s;" title="%s" aria-label="%s" aria-pressed="%s"></button>',
				esc_attr( $active_class ),
				esc_attr( $option ),
				esc_attr( $label ),
				esc_attr( $hex ),
				esc_attr( $label ),
				esc_attr( $label ),
				$is_selected ? 'true' : 'false'
			);
		} else {
			// Text / size swatch pill.
			$active_class = $is_selected ? 'is-active' : '';
			$swatches    .= sprintf(
				'<button type="button" class="luxora-swatch-text %s" data-value="%s" data-label="%s" aria-pressed="%s">%s</button>',
				esc_attr( $active_class ),
				esc_attr( $option ),
				esc_attr( $label ),
				$is_selected ? 'true' : 'false',
				esc_html( $label )
			);
		}
	}

	$swatches .= '</div>';

	// "Clear" link that WooCommerce variation.js expects.
	$clear = '<a class="luxora-swatch-reset reset_variations" href="#">' . esc_html__( 'Clear', 'luxora' ) . '</a>';

	return $native . $header . $swatches . $clear;
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'luxora_variation_swatch_html', 20, 2 );

/* -------------------------------------------------------------------------
 * Admin: color picker for pa_color attribute terms.
 * Adds a colour input on the add/edit attribute term screens.
 * ---------------------------------------------------------------------- */

/**
 * Output a colour picker field on the Add Term screen.
 *
 * @param string $taxonomy Current taxonomy slug.
 */
function luxora_color_term_add_field( $taxonomy ) {
	if ( false === strpos( $taxonomy, 'color' ) && false === strpos( $taxonomy, 'colour' ) ) {
		return;
	}
	?>
	<div class="form-field">
		<label for="product_attribute_color"><?php esc_html_e( 'Color', 'luxora' ); ?></label>
		<input type="color" id="product_attribute_color" name="product_attribute_color" value="#cccccc" />
		<p class="description"><?php esc_html_e( 'Pick the display color for this swatch.', 'luxora' ); ?></p>
	</div>
	<?php
}
add_action( 'pa_color_add_form_fields', 'luxora_color_term_add_field' );
add_action( 'pa_colour_add_form_fields', 'luxora_color_term_add_field' );

/**
 * Output a colour picker field on the Edit Term screen.
 *
 * @param WP_Term $term     Current term object.
 * @param string  $taxonomy Current taxonomy slug.
 */
function luxora_color_term_edit_field( $term, $taxonomy ) {
	if ( false === strpos( $taxonomy, 'color' ) && false === strpos( $taxonomy, 'colour' ) ) {
		return;
	}
	$hex = get_term_meta( $term->term_id, 'product_attribute_color', true );
	if ( ! $hex ) {
		$hex = '#cccccc';
	}
	?>
	<tr class="form-field">
		<th scope="row">
			<label for="product_attribute_color"><?php esc_html_e( 'Color', 'luxora' ); ?></label>
		</th>
		<td>
			<input type="color" id="product_attribute_color" name="product_attribute_color" value="<?php echo esc_attr( $hex ); ?>" />
			<p class="description"><?php esc_html_e( 'Pick the display color for this swatch.', 'luxora' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'pa_color_edit_form_fields', 'luxora_color_term_edit_field', 10, 2 );
add_action( 'pa_colour_edit_form_fields', 'luxora_color_term_edit_field', 10, 2 );

/**
 * Save the colour meta when a term is created or updated.
 *
 * @param int $term_id Term ID.
 */
function luxora_save_color_term_meta( $term_id ) {
	if ( ! isset( $_POST['product_attribute_color'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$hex = sanitize_hex_color( wp_unslash( $_POST['product_attribute_color'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( $hex ) {
		update_term_meta( $term_id, 'product_attribute_color', $hex );
	}
}
add_action( 'created_pa_color',  'luxora_save_color_term_meta' );
add_action( 'edited_pa_color',   'luxora_save_color_term_meta' );
add_action( 'created_pa_colour', 'luxora_save_color_term_meta' );
add_action( 'edited_pa_colour',  'luxora_save_color_term_meta' );

/**
 * Show a colour swatch column in the pa_color term list table.
 *
 * @param array  $columns   Existing columns.
 * @return array
 */
function luxora_color_term_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'name' === $key ) {
			$new['swatch'] = __( 'Swatch', 'luxora' );
		}
	}
	return $new;
}
add_filter( 'manage_edit-pa_color_columns',  'luxora_color_term_columns' );
add_filter( 'manage_edit-pa_colour_columns', 'luxora_color_term_columns' );

/**
 * Render the swatch column value.
 *
 * @param string $out      Column output.
 * @param string $column   Column name.
 * @param int    $term_id  Term ID.
 * @return string
 */
function luxora_color_term_column_value( $out, $column, $term_id ) {
	if ( 'swatch' === $column ) {
		$hex = get_term_meta( $term_id, 'product_attribute_color', true );
		if ( $hex ) {
			$out = '<span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:' . esc_attr( $hex ) . ';border:1px solid #ccc;"></span>';
		} else {
			$out = '—';
		}
	}
	return $out;
}
add_filter( 'manage_pa_color_custom_column',  'luxora_color_term_column_value', 10, 3 );
add_filter( 'manage_pa_colour_custom_column', 'luxora_color_term_column_value', 10, 3 );

/* -------------------------------------------------------------------------
 * Account menu — remove Downloads (not needed for a handbag store).
 * ---------------------------------------------------------------------- */
add_filter( 'woocommerce_account_menu_items', function ( $items ) {
	unset( $items['downloads'] );
	return $items;
} );

/* -------------------------------------------------------------------------
 * Variable product: add wishlist button + attribute label display.
 * ---------------------------------------------------------------------- */

/**
 * Render a wishlist button after the variation add-to-cart.
 * Hook: woocommerce_after_add_to_cart_button
 */
function luxora_variable_wishlist_button() {
	global $product;
	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return;
	}
	$pid = $product->get_id();
	?>
	<button type="button" class="luxora-var-wishlist luxora-wishlist <?php echo luxora_in_wishlist( $pid ) ? 'is-active' : ''; ?>" data-product-id="<?php echo esc_attr( $pid ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'luxora' ); ?>" aria-pressed="<?php echo luxora_in_wishlist( $pid ) ? 'true' : 'false'; ?>">
		<?php echo luxora_icon( 'heart', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</button>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_button', 'luxora_variable_wishlist_button', 5 );

/**
 * Attribute label + swatch displays are now built inline within the
 * luxora_variation_swatch_html() filter. No separate header div needed.
 * Keeping this stub so the hook slot still exists for child-themes.
 */
function luxora_variation_attribute_label_display() {
	// Intentionally empty — label is rendered inside luxora_variation_swatch_html().
}

/**
 * Customize checkout fields to match premium layout.
 */
function luxora_customize_checkout_fields( $fields ) {
	// First Name & Last Name
	$fields['billing']['billing_first_name']['priority'] = 10;
	$fields['billing']['billing_last_name']['priority']  = 20;

	// Email -> row-first, priority 25
	$fields['billing']['billing_email']['priority'] = 25;
	$fields['billing']['billing_email']['class']    = array( 'form-row-first' );

	// Phone -> row-last, priority 26
	$fields['billing']['billing_phone']['priority'] = 26;
	$fields['billing']['billing_phone']['class']    = array( 'form-row-last' );
	$fields['billing']['billing_phone']['required'] = true;

	// Address 1 -> label: Address, row-wide, priority 30
	$fields['billing']['billing_address_1']['label']       = __( 'Address', 'luxora' );
	$fields['billing']['billing_address_1']['placeholder'] = '';
	$fields['billing']['billing_address_1']['priority']    = 30;
	$fields['billing']['billing_address_1']['class']       = array( 'form-row-wide' );

	// City -> row-first, priority 40
	$fields['billing']['billing_city']['priority'] = 40;
	$fields['billing']['billing_city']['class']    = array( 'form-row-first' );

	// State/District -> row-last, priority 50
	$fields['billing']['billing_state']['priority'] = 50;
	$fields['billing']['billing_state']['class']    = array( 'form-row-last' );

	// Postcode -> make optional, priority 60
	$fields['billing']['billing_postcode']['required'] = false;
	$fields['billing']['billing_postcode']['priority'] = 60;

	// Shipping fields
	$fields['shipping']['shipping_first_name']['priority'] = 10;
	$fields['shipping']['shipping_last_name']['priority']  = 20;
	
	$fields['shipping']['shipping_address_1']['label']       = __( 'Address', 'luxora' );
	$fields['shipping']['shipping_address_1']['placeholder'] = '';
	$fields['shipping']['shipping_address_1']['priority']    = 30;
	$fields['shipping']['shipping_address_1']['class']       = array( 'form-row-wide' );

	$fields['shipping']['shipping_city']['priority'] = 40;
	$fields['shipping']['shipping_city']['class']    = array( 'form-row-first' );

	$fields['shipping']['shipping_state']['priority'] = 50;
	$fields['shipping']['shipping_state']['class']    = array( 'form-row-last' );

	$fields['shipping']['shipping_postcode']['required'] = false;
	$fields['shipping']['shipping_postcode']['priority'] = 60;

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'luxora_customize_checkout_fields', 999 );

/**
 * Default checkout country to BD (Bangladesh) for hidden field safety.
 */
add_filter( 'default_checkout_billing_country', function () {
	return 'BD';
} );
add_filter( 'default_checkout_shipping_country', function () {
	return 'BD';
} );

/**
 * Force variable product add-to-cart button text to "Add to bag".
 */
add_filter( 'woocommerce_product_single_add_to_cart_text', function () {
	return __( 'Add to bag', 'luxora' );
} );

/**
 * Remove default reset variations link (we render our own styled clear link inside swatches).
 */
add_filter( 'woocommerce_reset_variations_link', '__return_empty_string' );



