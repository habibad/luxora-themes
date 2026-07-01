<?php
/**
 * Luxora Child — functions.
 *
 * @package LuxoraChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue parent + child stylesheets.
 *
 * The parent theme already enqueues its compiled Tailwind bundle as
 * "luxora-main". We simply attach a child stylesheet after it.
 */
function luxora_child_enqueue() {
	// Parent theme.css header (mostly metadata; the real styles live in assets/css/main.css).
	wp_enqueue_style(
		'luxora-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	// Child overrides.
	wp_enqueue_style(
		'luxora-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'luxora-parent-style', 'luxora-main' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'luxora_child_enqueue', 20 );

/**
 * Example: place your custom hooks, filters, and overrides below.
 *
 * You can also copy any parent template (including files inside
 * /woocommerce/) into this child theme to customise it safely.
 */
