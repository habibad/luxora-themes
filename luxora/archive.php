<?php
/**
 * Generic archive (category/tag/author/date) for posts.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
luxora_breadcrumbs();
?>
<?php
get_template_part( 'template-parts/content/page-hero', null, array(
	'eyebrow' => __( 'Journal', 'luxora' ),
	'title'   => get_the_archive_title(),
) );
?>
<div class="container-luxe py-16 md:py-24">
	<div class="grid lg:grid-cols-12 gap-12">
		<div class="lg:col-span-8">
			<?php if ( have_posts() ) : ?>
				<div class="grid sm:grid-cols-2 gap-x-8 gap-y-12" data-reveal-stagger>
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content/post-card' );
					endwhile;
					?>
				</div>
				<?php luxora_pagination(); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content/none' ); ?>
			<?php endif; ?>
		</div>
		<aside class="lg:col-span-4">
			<?php get_sidebar(); ?>
		</aside>
	</div>
</div>
<?php
get_footer();
