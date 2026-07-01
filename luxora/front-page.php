<?php
/**
 * Front page (homepage) — mirrors index.tsx section order.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="luxora-home">
	<?php
	get_template_part( 'template-parts/home/hero' );

	if ( luxora_woo_active() ) {
		get_template_part( 'template-parts/home/collections' );
		get_template_part( 'template-parts/home/trending' );
	}

	get_template_part( 'template-parts/home/editorial' );

	if ( luxora_woo_active() ) {
		get_template_part( 'template-parts/home/new-arrivals' );
	}

	get_template_part( 'template-parts/home/why' );

	if ( luxora_woo_active() ) {
		get_template_part( 'template-parts/home/best-sellers' );
	}

	get_template_part( 'template-parts/home/brands' );
	get_template_part( 'template-parts/home/reviews' );

	if ( luxora_woo_active() ) {
		get_template_part( 'template-parts/home/instagram' );
	}

	// Allow page content (if a static page is assigned as front page) to render below.
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			$content = get_the_content();
			if ( '' !== trim( wp_strip_all_tags( $content ) ) ) :
				?>
				<section class="container-luxe py-16 md:py-24 prose-luxe max-w-3xl">
					<?php the_content(); ?>
				</section>
				<?php
			endif;
		endwhile;
	endif;

	get_template_part( 'template-parts/content/newsletter' );
	?>
</div>

<?php
get_footer();
