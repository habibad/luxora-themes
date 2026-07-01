<?php
/**
 * Enqueue styles & scripts.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end assets.
 */
function luxora_enqueue_assets() {

	// Google Fonts — Playfair Display / Cormorant Garamond / Inter (matches the source).
	wp_enqueue_style(
		'luxora-google-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,400&family=Inter:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);

	// Compiled design system (Tailwind v4 build of this theme's markup).
	$main_css = LUXORA_DIR . '/assets/css/main.css';
	wp_enqueue_style(
		'luxora-main',
		LUXORA_URI . '/assets/css/main.css',
		array( 'luxora-google-fonts' ),
		file_exists( $main_css ) ? filemtime( $main_css ) : LUXORA_VERSION
	);

	// Parent stylesheet header (kept last for child-theme override clarity).
	wp_enqueue_style(
		'luxora-style',
		get_stylesheet_uri(),
		array( 'luxora-main' ),
		LUXORA_VERSION
	);

	// Comment reply.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// GSAP + ScrollTrigger (animation engine) — loaded in the footer, deferred.
	wp_enqueue_script(
		'gsap',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
		array(),
		'3.12.5',
		true
	);
	wp_enqueue_script(
		'gsap-scrolltrigger',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
		array( 'gsap' ),
		'3.12.5',
		true
	);

	// Theme JavaScript (header scroll, mobile drawer, GSAP reveals, AJAX, search).
	$main_js = LUXORA_DIR . '/assets/js/main.js';
	wp_enqueue_script(
		'luxora-main',
		LUXORA_URI . '/assets/js/main.js',
		array( 'gsap', 'gsap-scrolltrigger' ),
		file_exists( $main_js ) ? filemtime( $main_js ) : LUXORA_VERSION,
		true
	);

	// Expose AJAX endpoints, nonces and i18n strings to JS.
	wp_localize_script(
		'luxora-main',
		'LUXORA',
		array(
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'luxora_nonce' ),
			'cartUrl'        => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
			'isWoo'          => class_exists( 'WooCommerce' ),
			'reducedMotion'  => false,
			'i18n'           => array(
				'added'    => __( 'Added to bag', 'luxora' ),
				'sold_out' => __( 'Sold out', 'luxora' ),
				'saved'    => __( 'Saved to wishlist', 'luxora' ),
				'removed'  => __( 'Removed from wishlist', 'luxora' ),
				'error'    => __( 'Something went wrong. Please try again.', 'luxora' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'luxora_enqueue_assets' );

/**
 * Add async/defer where helpful and preconnect to font CDNs (performance).
 */
function luxora_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = 'https://cdnjs.cloudflare.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'luxora_resource_hints', 10, 2 );

/**
 * Editor styles for a consistent admin experience.
 */
function luxora_block_editor_assets() {
	$main_css = LUXORA_DIR . '/assets/css/main.css';
	if ( file_exists( $main_css ) ) {
		add_editor_style( 'assets/css/main.css' );
	}
}
add_action( 'after_setup_theme', 'luxora_block_editor_assets' );
