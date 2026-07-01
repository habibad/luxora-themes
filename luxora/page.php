<?php
/**
 * Default page template.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// WooCommerce cart / checkout / account bring their own full-width, self-contained
// layouts (with their own .container-luxe and headings), so render the content bare.
if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	get_footer();
	return;
}

luxora_breadcrumbs();

while ( have_posts() ) :
	the_post();

	get_template_part(
		'template-parts/content/page-hero',
		null,
		array(
			'title' => get_the_title(),
		)
	);
	?>
	<div class="container-luxe py-16 md:py-24">
		<div class="prose-luxe max-w-3xl mx-auto font-serif text-lg leading-relaxed">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<div class="mt-8 text-sm uppercase tracking-[0.18em]">' . esc_html__( 'Pages:', 'luxora' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>
		<?php
		if ( comments_open() || get_comments_number() ) {
			echo '<div class="max-w-3xl mx-auto mt-16">';
			comments_template();
			echo '</div>';
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
