<?php
/**
 * Luxora functions and definitions.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

if ( ! defined( 'LUXORA_VERSION' ) ) {
	define( 'LUXORA_VERSION', '1.1.0' );
}
define( 'LUXORA_DIR', get_template_directory() );
define( 'LUXORA_URI', get_template_directory_uri() );

/**
 * Load theme modules.
 */
$luxora_includes = array(
	'/inc/setup.php',          // Theme supports, menus, image sizes.
	'/inc/setup-pages.php',    // One-time page + menu provisioning on activation.
	'/inc/enqueue.php',        // Styles & scripts.
	'/inc/template-tags.php',  // Reusable display helpers + breadcrumbs.
	'/inc/customizer.php',     // Customizer: logo, colors, typography, social, etc.
	'/inc/widgets.php',        // Sidebar & footer widget areas.
	'/inc/seo-schema.php',     // JSON-LD schema + meta helpers.
	'/inc/wishlist.php',       // Lightweight wishlist (cookie + user meta).
	'/inc/ajax.php',           // AJAX: add-to-cart, mini-cart, qty, wishlist.
	'/inc/woocommerce.php',    // WooCommerce integration & hooks.
);

foreach ( $luxora_includes as $luxora_file ) {
	$path = LUXORA_DIR . $luxora_file;
	if ( is_readable( $path ) ) {
		require_once $path;
	}
}
