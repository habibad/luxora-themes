<?php
/**
 * Template Name: About
 *
 * Mirrors about.tsx — the Luxora maison story.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$hero_img  = luxora_opt( 'luxora_about_hero_image' );
$hero_img  = $hero_img ? $hero_img : luxora_asset_img( 'hero-bag.jpg' );
$craft_img = luxora_opt( 'luxora_about_craft_image' );
$craft_img = $craft_img ? $craft_img : luxora_asset_img( 'lifestyle-1.jpg' );

$stats = array(
	array( '2021', __( 'Founded in Dhaka', 'luxora' ), __( 'A small atelier with a single shipment of twelve bags.', 'luxora' ) ),
	array( '12K+', __( 'Women carried Luxora', 'luxora' ), __( 'Hand-packed pieces delivered across all 64 districts.', 'luxora' ) ),
	array( '98%', __( 'Return clients', 'luxora' ), __( 'We measure success in long relationships, not transactions.', 'luxora' ) ),
);
?>
<article class="luxora-about">

	<section class="container-luxe py-24 md:py-32 grid lg:grid-cols-2 gap-16 items-center" data-reveal>
		<div>
			<p class="eyebrow mb-6"><?php esc_html_e( 'Our Story', 'luxora' ); ?></p>
			<h1 class="font-display text-5xl md:text-7xl leading-[1]">
				<?php
				printf(
					/* translators: %s: the word "luxury" styled in gold italic */
					wp_kses( __( 'A quiet kind of %s.', 'luxora' ), array( 'em' => array( 'class' => array() ) ) ),
					'<em class="font-serif italic text-gold">' . esc_html__( 'luxury', 'luxora' ) . '</em>'
				);
				?>
			</h1>
			<p class="mt-8 font-serif text-xl text-muted-foreground leading-relaxed">
				<?php esc_html_e( 'LUXORA was founded on a simple belief: that women in Bangladesh deserve access to the same artisanal craftsmanship celebrated by the world\'s most storied maisons — without compromise, without pretense, and without the inflated middle.', 'luxora' ); ?>
			</p>
		</div>
		<div class="aspect-[4/5] overflow-hidden bg-cream">
			<img src="<?php echo esc_url( $hero_img ); ?>" alt="" class="h-full w-full object-cover" />
		</div>
	</section>

	<section class="bg-cream py-24 md:py-32">
		<div class="container-luxe grid md:grid-cols-3 gap-12" data-reveal-stagger>
			<?php foreach ( $stats as $s ) : ?>
				<div data-reveal-item>
					<p class="font-display text-6xl text-gold"><?php echo esc_html( $s[0] ); ?></p>
					<h3 class="font-display text-2xl mt-4"><?php echo esc_html( $s[1] ); ?></h3>
					<p class="mt-3 text-muted-foreground"><?php echo esc_html( $s[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="container-luxe py-24 md:py-32 grid lg:grid-cols-2 gap-16 items-center" data-reveal>
		<div class="aspect-[4/5] overflow-hidden bg-cream order-2 lg:order-1">
			<img src="<?php echo esc_url( $craft_img ); ?>" alt="" class="h-full w-full object-cover" />
		</div>
		<div class="order-1 lg:order-2">
			<p class="eyebrow mb-6"><?php esc_html_e( 'The craft', 'luxora' ); ?></p>
			<h2 class="font-display text-4xl md:text-5xl leading-[1.05]"><?php esc_html_e( 'Hand-selected. Authenticated. Beautifully delivered.', 'luxora' ); ?></h2>
			<p class="mt-6 font-serif text-lg text-muted-foreground leading-relaxed">
				<?php esc_html_e( 'Each piece in our edit is hand-selected from artisanal makers across Europe and East Asia, then inspected by our atelier team in Dhaka. We refuse the throwaway. We choose the heirloom.', 'luxora' ); ?>
			</p>
		</div>
	</section>

	<?php
	// Optional editorial content from the page body.
	while ( have_posts() ) :
		the_post();
		if ( '' !== trim( wp_strip_all_tags( get_the_content() ) ) ) :
			echo '<section class="container-luxe pb-24 md:pb-32"><div class="prose-luxe max-w-3xl mx-auto font-serif text-lg leading-relaxed">';
			the_content();
			echo '</div></section>';
		endif;
	endwhile;
	?>
</article>
<?php
get_footer();
