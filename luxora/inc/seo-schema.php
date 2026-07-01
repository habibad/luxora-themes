<?php
/**
 * SEO: JSON-LD schema + meta description. Defers to Yoast/RankMath if active.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a dedicated SEO plugin is handling meta/schema.
 *
 * @return bool
 */
function luxora_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Output a meta description when no SEO plugin is present.
 */
function luxora_meta_description() {
	if ( luxora_has_seo_plugin() ) {
		return;
	}

	$desc = '';

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( function_exists( 'is_product' ) && is_product() ) {
			$product = wc_get_product( $post->ID );
			$desc    = $product ? $product->get_short_description() : '';
			$desc    = $desc ? $desc : ( $product ? $product->get_description() : '' );
		}
		if ( ! $desc && isset( $post->post_excerpt ) && $post->post_excerpt ) {
			$desc = $post->post_excerpt;
		}
		if ( ! $desc && isset( $post->post_content ) ) {
			$desc = $post->post_content;
		}
	} elseif ( is_home() || is_front_page() ) {
		$desc = get_bloginfo( 'description' );
		if ( ! $desc ) {
			$desc = __( 'A curated maison of luxury handbags — totes, crossbody, mini and evening bags — delivered across Bangladesh.', 'luxora' );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$desc = term_description();
	}

	$desc = trim( wp_strip_all_tags( $desc ) );
	if ( ! $desc ) {
		return;
	}
	$desc = wp_html_excerpt( $desc, 160, '…' );

	printf( "<meta name=\"description\" content=\"%s\" />\n", esc_attr( $desc ) );
	printf( "<meta property=\"og:description\" content=\"%s\" />\n", esc_attr( $desc ) );
	printf( "<meta property=\"og:title\" content=\"%s\" />\n", esc_attr( wp_get_document_title() ) );
	printf( "<meta property=\"og:type\" content=\"%s\" />\n", is_singular() ? 'article' : 'website' );
	printf( "<meta name=\"twitter:card\" content=\"summary_large_image\" />\n" );
	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( null, 'large' );
		printf( "<meta property=\"og:image\" content=\"%s\" />\n", esc_url( $img ) );
	}
}
add_action( 'wp_head', 'luxora_meta_description', 1 );

/**
 * Organization + WebSite schema on the front page.
 */
function luxora_site_schema() {
	if ( luxora_has_seo_plugin() || ! is_front_page() ) {
		return;
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'  => 'Organization',
				'@id'    => home_url( '/#organization' ),
				'name'   => get_bloginfo( 'name' ),
				'url'    => home_url( '/' ),
				'sameAs' => array_values( luxora_social_links() ),
			),
			array(
				'@type'           => 'WebSite',
				'@id'             => home_url( '/#website' ),
				'url'             => home_url( '/' ),
				'name'            => get_bloginfo( 'name' ),
				'description'     => get_bloginfo( 'description' ),
				'publisher'       => array( '@id' => home_url( '/#organization' ) ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => array(
						'@type'       => 'EntryPoint',
						'urlTemplate' => home_url( '/?s={search_term_string}' ),
					),
					'query-input' => 'required name=search_term_string',
				),
			),
		),
	);

	echo "<script type=\"application/ld+json\">" . wp_json_encode( $schema ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'luxora_site_schema', 5 );

/**
 * BreadcrumbList schema for inner pages.
 */
function luxora_breadcrumb_schema() {
	if ( luxora_has_seo_plugin() || is_front_page() ) {
		return;
	}

	$items = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'Home', 'luxora' ),
			'item'     => home_url( '/' ),
		),
	);

	$pos = 2;
	if ( function_exists( 'is_product' ) && is_product() ) {
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => __( 'Shop', 'luxora' ),
				'item'     => get_permalink( $shop_id ),
			);
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} elseif ( is_singular() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => single_term_title( '', false ),
			'item'     => get_term_link( get_queried_object() ),
		);
	} else {
		return;
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);

	echo "<script type=\"application/ld+json\">" . wp_json_encode( $schema ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'luxora_breadcrumb_schema', 6 );
