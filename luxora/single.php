<?php
/**
 * Single blog post.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
luxora_breadcrumbs();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'luxora-single' ); ?>>
		<header class="container-luxe pt-8 pb-12 md:pt-12 md:pb-16 text-center max-w-3xl mx-auto" data-reveal>
			<?php
			$cats = get_the_category_list( ', ' );
			if ( $cats ) :
				?>
				<p class="eyebrow mb-5"><?php echo wp_kses_post( $cats ); ?></p>
			<?php endif; ?>
			<h1 class="font-display text-4xl md:text-5xl lg:text-6xl tracking-tight"><?php the_title(); ?></h1>
			<p class="mt-6 text-xs uppercase tracking-[0.2em] text-muted-foreground">
				<?php echo esc_html( get_the_date() ); ?> &middot; <?php echo esc_html( get_the_author() ); ?>
			</p>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container-luxe mb-12 md:mb-16" data-reveal>
				<div class="aspect-[16/9] overflow-hidden bg-muted">
					<?php the_post_thumbnail( 'luxora-hero', array( 'class' => 'h-full w-full object-cover', 'loading' => 'eager' ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="container-luxe pb-16 md:pb-24">
			<div class="prose-luxe max-w-2xl mx-auto font-serif text-lg leading-relaxed">
				<?php the_content(); ?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="mt-8 text-sm uppercase tracking-[0.18em]">' . esc_html__( 'Pages:', 'luxora' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php if ( has_tag() ) : ?>
				<div class="max-w-2xl mx-auto mt-12 flex flex-wrap gap-2">
					<?php
					foreach ( get_the_tags() as $tag ) :
						?>
						<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" class="text-[11px] uppercase tracking-[0.18em] border border-border px-3 py-1.5 hover:border-gold transition"><?php echo esc_html( $tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="max-w-2xl mx-auto mt-16">
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
