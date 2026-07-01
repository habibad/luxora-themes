<?php
/**
 * Blog post card.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'group luxora-post-card' ); ?> data-reveal-item>
	<a href="<?php the_permalink(); ?>" class="block">
		<div class="relative aspect-[4/3] overflow-hidden bg-muted mb-5">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'luxora-card', array( 'class' => 'h-full w-full object-cover transition-transform duration-[1200ms] group-hover:scale-105', 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<div class="h-full w-full grid place-items-center bg-cream"><span class="font-display text-2xl text-muted-foreground">LUXORA</span></div>
			<?php endif; ?>
		</div>
		<?php
		$cats = get_the_category_list( ', ' );
		if ( $cats ) :
			?>
			<p class="eyebrow mb-3"><?php echo wp_kses_post( $cats ); ?></p>
		<?php endif; ?>
		<h2 class="font-display text-2xl leading-tight group-hover:text-gold transition"><?php the_title(); ?></h2>
		<p class="mt-3 text-sm text-muted-foreground leading-relaxed line-clamp-2"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<p class="mt-4 text-[11px] uppercase tracking-[0.2em] text-muted-foreground"><?php echo esc_html( get_the_date() ); ?></p>
	</a>
</article>
