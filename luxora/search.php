<?php
/**
 * Search results.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
luxora_breadcrumbs();

get_template_part( 'template-parts/content/page-hero', null, array(
	'eyebrow' => __( 'Search', 'luxora' ),
	/* translators: %s: search query */
	'title'   => sprintf( __( 'Results for “%s”', 'luxora' ), get_search_query() ),
) );
?>
<div class="container-luxe py-16 md:py-24">
	<?php if ( have_posts() ) : ?>
		<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12" data-reveal-stagger>
			<?php
			while ( have_posts() ) :
				the_post();
				if ( 'product' === get_post_type() && function_exists( 'wc_get_product' ) ) {
					echo '<div data-reveal-item>';
					luxora_render_product_card( get_the_ID() );
					echo '</div>';
				} else {
					get_template_part( 'template-parts/content/post-card' );
				}
			endwhile;
			?>
		</div>
		<?php luxora_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
