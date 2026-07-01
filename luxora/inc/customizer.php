<?php
/**
 * Customizer: logo, colors, typography, social links, announcement & contact.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer defaults.
 *
 * @return array
 */
function luxora_customizer_defaults() {
	return array(
		'luxora_announcement'   => __( 'Complimentary delivery across Bangladesh on orders over ৳10,000', 'luxora' ),
		'luxora_currency_label' => 'BDT',
		'luxora_color_ink'      => '#111111',
		'luxora_color_gold'     => '#C8A96A',
		'luxora_color_cream'    => '#FAF8F5',
		'luxora_color_beige'    => '#EEE6DA',
		'luxora_font_display'   => 'Playfair Display',
		'luxora_font_serif'     => 'Cormorant Garamond',
		'luxora_font_sans'      => 'Inter',
		'luxora_footer_tagline' => __( 'A curated maison of luxury handbags — hand-selected, beautifully delivered across Bangladesh.', 'luxora' ),
		'luxora_footer_address' => "Gulshan Avenue\nDhaka 1212, Bangladesh",
		'luxora_footer_phone'   => '+880 1700-000 000',
		'luxora_footer_email'   => 'care@luxora.bd',
		'luxora_social_instagram' => '#',
		'luxora_social_facebook'  => '#',
		'luxora_social_youtube'   => '#',
		'luxora_social_twitter'   => '#',
		'luxora_newsletter_eyebrow'  => __( 'The List', 'luxora' ),
		'luxora_newsletter_title'    => __( "Be the first\nto know.", 'luxora' ),
		'luxora_newsletter_subtitle' => __( 'New arrivals, private previews, and seasonal editorials — delivered to your inbox once a fortnight.', 'luxora' ),
		'luxora_newsletter_action'   => '',
		'luxora_hero_eyebrow'        => __( 'Maison Luxora — Autumn Edit', 'luxora' ),
		'luxora_hero_heading'        => __( 'The art of<br /><em class="font-serif italic text-gold">carrying</em><br />quietly.', 'luxora' ),
		'luxora_hero_subtitle'       => __( 'A considered edit of handbags — sculpted, hand-finished, and delivered to your door across Bangladesh.', 'luxora' ),
		'luxora_editorial_eyebrow'   => __( 'Our Promise', 'luxora' ),
		'luxora_editorial_title'     => __( 'Imported. <em class="font-serif italic text-gold">Curated.</em> Delivered with care.', 'luxora' ),
		'luxora_editorial_text'      => __( 'Every piece in the Luxora edit is hand-selected from artisanal makers and authenticated before it reaches your door. We believe in slow luxury — fewer, better things, made beautifully.', 'luxora' ),
		'luxora_brands_list'         => 'Luxora Atelier, Maison Luxora, Luxora Noir, Cèline Maison, Maison Ivoire',
		'luxora_instagram_handle'    => '@luxora.bd',
		'luxora_contact_address'     => "House 27, Road 113, Gulshan 2\nDhaka 1212",
		'luxora_contact_phone'       => '+880 1700-000 000',
		'luxora_contact_email'       => 'care@luxora.bd',
	);
}

/**
 * Get a theme mod with the Luxora default fallback.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function luxora_opt( $key ) {
	$defaults = luxora_customizer_defaults();

	// Normalize: settings are stored with a `luxora_` prefix. Accept either form.
	if ( ! isset( $defaults[ $key ] ) && isset( $defaults[ 'luxora_' . $key ] ) ) {
		$key = 'luxora_' . $key;
	}

	$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( $key, $default );
}

/**
 * Register Customizer settings & controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function luxora_customize_register( $wp_customize ) {
	$defaults = luxora_customizer_defaults();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	// ============ PANEL ============
	$wp_customize->add_panel(
		'luxora_panel',
		array(
			'title'    => __( 'Luxora Theme Options', 'luxora' ),
			'priority' => 1,
		)
	);

	// ---------- Announcement / Topbar ----------
	$wp_customize->add_section(
		'luxora_topbar',
		array(
			'title' => __( 'Announcement Bar', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);
	$wp_customize->add_setting( 'luxora_announcement', array( 'default' => $defaults['luxora_announcement'], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'luxora_announcement', array( 'label' => __( 'Announcement text', 'luxora' ), 'section' => 'luxora_topbar', 'type' => 'text' ) );
	$wp_customize->add_setting( 'luxora_currency_label', array( 'default' => $defaults['luxora_currency_label'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'luxora_currency_label', array( 'label' => __( 'Currency label (header)', 'luxora' ), 'section' => 'luxora_topbar', 'type' => 'text' ) );

	// ---------- Colors ----------
	$wp_customize->add_section(
		'luxora_colors',
		array(
			'title' => __( 'Color Settings', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);
	$colors = array(
		'luxora_color_ink'   => __( 'Ink (soft black)', 'luxora' ),
		'luxora_color_gold'  => __( 'Gold accent', 'luxora' ),
		'luxora_color_cream' => __( 'Cream (warm white)', 'luxora' ),
		'luxora_color_beige' => __( 'Beige', 'luxora' ),
	);
	foreach ( $colors as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => $defaults[ $key ], 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage' ) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize, $key, array( 'label' => $label, 'section' => 'luxora_colors' ) )
		);
	}

	// ---------- Typography ----------
	$wp_customize->add_section(
		'luxora_typography',
		array(
			'title' => __( 'Typography Settings', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);
	$fonts = array(
		'luxora_font_display' => __( 'Display font (headings)', 'luxora' ),
		'luxora_font_serif'   => __( 'Serif font (editorial)', 'luxora' ),
		'luxora_font_sans'    => __( 'Body font', 'luxora' ),
	);
	foreach ( $fonts as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => $defaults[ $key ], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ) );
		$wp_customize->add_control( $key, array( 'label' => $label, 'section' => 'luxora_typography', 'type' => 'text', 'description' => __( 'CSS font-family name. Add a matching @font-face or Google Font.', 'luxora' ) ) );
	}

	// ---------- Social links ----------
	$wp_customize->add_section(
		'luxora_social',
		array(
			'title' => __( 'Social Links', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);
	$socials = array(
		'luxora_social_instagram' => __( 'Instagram URL', 'luxora' ),
		'luxora_social_facebook'  => __( 'Facebook URL', 'luxora' ),
		'luxora_social_youtube'   => __( 'YouTube URL', 'luxora' ),
		'luxora_social_twitter'   => __( 'Twitter / X URL', 'luxora' ),
	);
	foreach ( $socials as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => $defaults[ $key ], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( $key, array( 'label' => $label, 'section' => 'luxora_social', 'type' => 'url' ) );
	}

	// ---------- Footer / Contact ----------
	$wp_customize->add_section(
		'luxora_footer',
		array(
			'title' => __( 'Footer & Contact', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);
	$wp_customize->add_setting( 'luxora_footer_tagline', array( 'default' => $defaults['luxora_footer_tagline'], 'sanitize_callback' => 'wp_kses_post' ) );
	$wp_customize->add_control( 'luxora_footer_tagline', array( 'label' => __( 'Footer tagline', 'luxora' ), 'section' => 'luxora_footer', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'luxora_footer_address', array( 'default' => $defaults['luxora_footer_address'], 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'luxora_footer_address', array( 'label' => __( 'Visit address', 'luxora' ), 'section' => 'luxora_footer', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'luxora_footer_phone', array( 'default' => $defaults['luxora_footer_phone'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'luxora_footer_phone', array( 'label' => __( 'Phone', 'luxora' ), 'section' => 'luxora_footer', 'type' => 'text' ) );
	$wp_customize->add_setting( 'luxora_footer_email', array( 'default' => $defaults['luxora_footer_email'], 'sanitize_callback' => 'sanitize_email' ) );
	$wp_customize->add_control( 'luxora_footer_email', array( 'label' => __( 'Email', 'luxora' ), 'section' => 'luxora_footer', 'type' => 'text' ) );

	// ---------- Newsletter ----------
	$wp_customize->add_section(
		'luxora_newsletter',
		array(
			'title' => __( 'Newsletter Area', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);
	$wp_customize->add_setting( 'luxora_newsletter_eyebrow', array( 'default' => $defaults['luxora_newsletter_eyebrow'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'luxora_newsletter_eyebrow', array( 'label' => __( 'Eyebrow', 'luxora' ), 'section' => 'luxora_newsletter', 'type' => 'text' ) );
	$wp_customize->add_setting( 'luxora_newsletter_title', array( 'default' => $defaults['luxora_newsletter_title'], 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'luxora_newsletter_title', array( 'label' => __( 'Title (line breaks honored)', 'luxora' ), 'section' => 'luxora_newsletter', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'luxora_newsletter_subtitle', array( 'default' => $defaults['luxora_newsletter_subtitle'], 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'luxora_newsletter_subtitle', array( 'label' => __( 'Subtitle', 'luxora' ), 'section' => 'luxora_newsletter', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'luxora_newsletter_action', array( 'default' => $defaults['luxora_newsletter_action'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'luxora_newsletter_action', array( 'label' => __( 'Form action URL (Mailchimp/etc). Leave blank to store locally.', 'luxora' ), 'section' => 'luxora_newsletter', 'type' => 'url' ) );

	// ---------- Homepage Content ----------
	$wp_customize->add_section(
		'luxora_home',
		array(
			'title' => __( 'Homepage Content', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);

	$home_text = array(
		'luxora_hero_eyebrow'      => array( __( 'Hero eyebrow', 'luxora' ), 'sanitize_text_field', 'text' ),
		'luxora_hero_heading'      => array( __( 'Hero heading (allows <br>, <em>)', 'luxora' ), 'wp_kses_post', 'textarea' ),
		'luxora_hero_subtitle'     => array( __( 'Hero subtitle', 'luxora' ), 'sanitize_textarea_field', 'textarea' ),
		'luxora_editorial_eyebrow' => array( __( 'Editorial eyebrow', 'luxora' ), 'sanitize_text_field', 'text' ),
		'luxora_editorial_title'   => array( __( 'Editorial title (allows <em>)', 'luxora' ), 'wp_kses_post', 'textarea' ),
		'luxora_editorial_text'    => array( __( 'Editorial paragraph', 'luxora' ), 'sanitize_textarea_field', 'textarea' ),
		'luxora_brands_list'       => array( __( 'Brand strip (comma separated; used if no pa_brand attribute)', 'luxora' ), 'sanitize_text_field', 'text' ),
		'luxora_instagram_handle'  => array( __( 'Instagram handle label', 'luxora' ), 'sanitize_text_field', 'text' ),
	);
	foreach ( $home_text as $key => $cfg ) {
		$wp_customize->add_setting( $key, array( 'default' => $defaults[ $key ], 'sanitize_callback' => $cfg[1] ) );
		$wp_customize->add_control( $key, array( 'label' => $cfg[0], 'section' => 'luxora_home', 'type' => $cfg[2] ) );
	}

	$wp_customize->add_setting( 'luxora_hero_image', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control( $wp_customize, 'luxora_hero_image', array( 'label' => __( 'Hero image', 'luxora' ), 'section' => 'luxora_home' ) )
	);
	$wp_customize->add_setting( 'luxora_editorial_image', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control( $wp_customize, 'luxora_editorial_image', array( 'label' => __( 'Editorial / lifestyle image', 'luxora' ), 'section' => 'luxora_home' ) )
	);

	// ---------- Contact & About ----------
	$wp_customize->add_section(
		'luxora_contact',
		array(
			'title' => __( 'Contact & About', 'luxora' ),
			'panel' => 'luxora_panel',
		)
	);

	$wp_customize->add_setting( 'luxora_contact_address', array( 'default' => $defaults['luxora_contact_address'], 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'luxora_contact_address', array( 'label' => __( 'Contact address (line breaks honored)', 'luxora' ), 'section' => 'luxora_contact', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'luxora_contact_phone', array( 'default' => $defaults['luxora_contact_phone'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'luxora_contact_phone', array( 'label' => __( 'Contact phone', 'luxora' ), 'section' => 'luxora_contact', 'type' => 'text' ) );
	$wp_customize->add_setting( 'luxora_contact_email', array( 'default' => $defaults['luxora_contact_email'], 'sanitize_callback' => 'sanitize_email' ) );
	$wp_customize->add_control( 'luxora_contact_email', array( 'label' => __( 'Contact email (also receives form messages)', 'luxora' ), 'section' => 'luxora_contact', 'type' => 'text' ) );

	$wp_customize->add_setting( 'luxora_about_hero_image', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control( $wp_customize, 'luxora_about_hero_image', array( 'label' => __( 'About — story image', 'luxora' ), 'section' => 'luxora_contact' ) )
	);
	$wp_customize->add_setting( 'luxora_about_craft_image', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control( $wp_customize, 'luxora_about_craft_image', array( 'label' => __( 'About — craft image', 'luxora' ), 'section' => 'luxora_contact' ) )
	);
}
add_action( 'customize_register', 'luxora_customize_register' );

/**
 * Output dynamic CSS variables from the Customizer color/typography settings.
 * Hex values are converted so they override the OKLCH defaults from main.css.
 */
function luxora_customizer_css() {
	$ink   = luxora_opt( 'luxora_color_ink' );
	$gold  = luxora_opt( 'luxora_color_gold' );
	$cream = luxora_opt( 'luxora_color_cream' );
	$beige = luxora_opt( 'luxora_color_beige' );
	$fd    = luxora_opt( 'luxora_font_display' );
	$fs    = luxora_opt( 'luxora_font_serif' );
	$fb    = luxora_opt( 'luxora_font_sans' );

	$css = ':root{';
	if ( $ink ) {
		$css .= '--ink:' . esc_html( $ink ) . ';--foreground:' . esc_html( $ink ) . ';--primary:' . esc_html( $ink ) . ';';
	}
	if ( $gold ) {
		$css .= '--gold:' . esc_html( $gold ) . ';--accent:' . esc_html( $gold ) . ';--ring:' . esc_html( $gold ) . ';';
	}
	if ( $cream ) {
		$css .= '--cream:' . esc_html( $cream ) . ';--secondary:' . esc_html( $cream ) . ';--primary-foreground:' . esc_html( $cream ) . ';';
	}
	if ( $beige ) {
		$css .= '--beige:' . esc_html( $beige ) . ';';
	}
	$css .= '--font-display:"' . esc_html( $fd ) . '",ui-serif,Georgia,serif;';
	$css .= '--font-serif:"' . esc_html( $fs ) . '",ui-serif,Georgia,serif;';
	$css .= '--font-sans:"' . esc_html( $fb ) . '",ui-sans-serif,system-ui,sans-serif;';
	$css .= '}';

	printf( "<style id='luxora-customizer-css'>%s</style>\n", $css ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'luxora_customizer_css', 20 );

/**
 * Live preview JS for the Customizer.
 */
function luxora_customize_preview_js() {
	wp_enqueue_script(
		'luxora-customize-preview',
		LUXORA_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		LUXORA_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'luxora_customize_preview_js' );

/**
 * Return the configured social links (non-empty only).
 *
 * @return array
 */
function luxora_social_links() {
	$links = array(
		'instagram' => luxora_opt( 'luxora_social_instagram' ),
		'facebook'  => luxora_opt( 'luxora_social_facebook' ),
		'youtube'   => luxora_opt( 'luxora_social_youtube' ),
		'twitter'   => luxora_opt( 'luxora_social_twitter' ),
	);
	return array_filter( $links );
}
