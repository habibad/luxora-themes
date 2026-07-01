<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'luxora_setup' ) ) {
	/**
	 * Core theme setup.
	 */
	function luxora_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'luxora', LUXORA_DIR . '/languages' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable post thumbnails.
		add_theme_support( 'post-thumbnails' );

		// RSS feed links.
		add_theme_support( 'automatic-feed-links' );

		// HTML5 markup.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		// Custom logo (Customizer "Logo upload").
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 60,
				'width'       => 240,
				'flex-height' => true,
				'flex-width'  => true,
				'unlink-homepage-logo' => true,
			)
		);

		// Selective refresh for widgets in the Customizer.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Responsive embeds & editor alignment.
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );

		// Register navigation menus (WordPress Menu API).
		register_nav_menus(
			array(
				'primary'      => __( 'Primary Menu', 'luxora' ),
				'mega'         => __( 'Category Strip (Mega)', 'luxora' ),
				'mobile'       => __( 'Mobile Drawer Menu', 'luxora' ),
				'footer-maison' => __( 'Footer — Maison', 'luxora' ),
				'footer-shop'   => __( 'Footer — Shop', 'luxora' ),
				'footer-care'   => __( 'Footer — Care', 'luxora' ),
				'footer-legal'  => __( 'Footer — Legal (bottom bar)', 'luxora' ),
			)
		);

		// Editorial image sizes used across the boutique.
		add_image_size( 'luxora-card', 720, 900, true );      // 4:5 product card.
		add_image_size( 'luxora-portrait', 900, 1200, true ); // 3:4 collection / editorial.
		add_image_size( 'luxora-square', 800, 800, true );    // 1:1 instagram / cart thumb.
		add_image_size( 'luxora-hero', 1200, 1500, true );    // hero / editorial split.

		// WooCommerce support.
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'luxora_setup' );

/**
 * Content width.
 */
function luxora_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'luxora_content_width', 1408 );
}
add_action( 'after_setup_theme', 'luxora_content_width', 0 );

/**
 * Register additional image sizes in the admin chooser.
 */
function luxora_custom_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'luxora-card'     => __( 'Luxora Card (4:5)', 'luxora' ),
			'luxora-portrait' => __( 'Luxora Portrait (3:4)', 'luxora' ),
			'luxora-square'   => __( 'Luxora Square (1:1)', 'luxora' ),
			'luxora-hero'     => __( 'Luxora Hero', 'luxora' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'luxora_custom_image_size_names' );
