<?php
/**
 * Template Name: FAQ
 *
 * Mirrors faq.tsx — accordion of common questions.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Curated defaults (match the source). Filterable so the agency can localise
 * or extend without editing the template.
 */
$faqs = apply_filters(
	'luxora_faqs',
	array(
		array( __( 'How long does shipping take?', 'luxora' ), __( 'Express delivery within 2-4 business days across all 64 districts of Bangladesh. Dhaka metro orders placed before 2pm ship same-day.', 'luxora' ) ),
		array( __( 'Are your bags authentic?', 'luxora' ), __( 'Every Luxora piece is sourced from authorised artisans and inspected by our in-house atelier team. We provide a certificate of authenticity with every order.', 'luxora' ) ),
		array( __( 'What is your return policy?', 'luxora' ), __( 'We accept returns within 14 days of receipt, provided the piece is unused and in its original packaging.', 'luxora' ) ),
		array( __( 'Do you offer gift wrapping?', 'luxora' ), __( 'Yes — every order arrives gift-wrapped in our signature dustbag and box, complimentary.', 'luxora' ) ),
		array( __( 'Can I reserve a piece?', 'luxora' ), __( 'Members of The List may reserve new arrivals 48 hours before public release.', 'luxora' ) ),
		array( __( 'Do you ship internationally?', 'luxora' ), __( 'Not yet — we are currently focused on serving Bangladesh.', 'luxora' ) ),
	)
);
?>
<section class="container-luxe py-24 md:py-32 max-w-3xl luxora-faq" data-reveal>
	<p class="eyebrow mb-5 text-center"><?php esc_html_e( 'Help', 'luxora' ); ?></p>
	<h1 class="font-display text-5xl md:text-6xl text-center"><?php esc_html_e( 'Questions, answered.', 'luxora' ); ?></h1>

	<div class="mt-16 divide-y divide-border border-y border-border">
		<?php foreach ( $faqs as $f ) : ?>
			<details class="group py-6">
				<summary class="flex justify-between items-center cursor-pointer list-none gap-6">
					<span class="font-display text-xl"><?php echo esc_html( $f[0] ); ?></span>
					<?php echo luxora_icon( 'chevron-down', 'h-5 w-5 transition-transform group-open:rotate-180 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</summary>
				<p class="mt-4 font-serif text-lg text-muted-foreground leading-relaxed"><?php echo esc_html( $f[1] ); ?></p>
			</details>
		<?php endforeach; ?>
	</div>

	<?php
	// Optional additional FAQ content from the page body.
	while ( have_posts() ) :
		the_post();
		if ( '' !== trim( wp_strip_all_tags( get_the_content() ) ) ) :
			echo '<div class="prose-luxe mt-16 font-serif text-lg leading-relaxed">';
			the_content();
			echo '</div>';
		endif;
	endwhile;
	?>

	<div class="mt-16 text-center">
		<p class="font-serif text-lg text-muted-foreground mb-6"><?php esc_html_e( 'Still have a question?', 'luxora' ); ?></p>
		<a href="<?php echo esc_url( luxora_page_url_by_title( 'Contact', '/contact/' ) ); ?>" class="btn-luxe"><?php esc_html_e( 'Contact the atelier', 'luxora' ); ?></a>
	</div>
</section>

<?php
get_footer();
