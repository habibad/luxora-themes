<?php
/**
 * Home — Client reviews. Mirrors index.tsx reviews block.
 * Pulls recent 5-star WooCommerce product reviews, falls back to curated quotes.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reviews = array();

$comments = get_comments(
	array(
		'status'      => 'approve',
		'post_type'   => 'product',
		'number'      => 3,
		'meta_key'    => 'rating', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'  => '5',      // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		'orderby'     => 'comment_date_gmt',
		'order'       => 'DESC',
	)
);

foreach ( $comments as $c ) {
	$reviews[] = array(
		'rating' => 5,
		'text'   => wp_trim_words( $c->comment_content, 32, '…' ),
		'name'   => $c->comment_author,
		'city'   => get_comment_meta( $c->comment_ID, 'luxora_city', true ),
	);
}

if ( count( $reviews ) < 3 ) {
	$reviews = array(
		array(
			'rating' => 5,
			'text'   => __( "The Monaco Tote is exactly what I'd hoped — the leather is buttery, the hardware feels jewel-like. Worth every taka.", 'luxora' ),
			'name'   => 'Tasnia R.',
			'city'   => 'Dhaka',
		),
		array(
			'rating' => 5,
			'text'   => __( 'Packaging arrived like a true luxury house. The Celeste has become my everyday bag.', 'luxora' ),
			'name'   => 'Mehnaz A.',
			'city'   => 'Chattogram',
		),
		array(
			'rating' => 5,
			'text'   => __( 'I gifted my mother the Ivoire Vanity. She has not put it down since. Service was impeccable.', 'luxora' ),
			'name'   => 'Sumaiya K.',
			'city'   => 'Sylhet',
		),
	);
}
?>
<section class="bg-cream py-24 md:py-32">
	<div class="container-luxe">
		<?php
		luxora_section_heading(
			array(
				'eyebrow' => __( 'Whispers', 'luxora' ),
				'title'   => __( 'What clients are saying', 'luxora' ),
			)
		);
		?>
		<div class="grid md:grid-cols-3 gap-8" data-reveal-stagger>
			<?php foreach ( $reviews as $r ) : ?>
				<figure class="bg-background border border-border p-10" data-reveal-item>
					<div class="flex gap-0.5 text-gold mb-6">
						<?php for ( $i = 0; $i < (int) $r['rating']; $i++ ) : ?>
							<?php echo luxora_icon( 'star-fill', 'h-4 w-4 fill-gold' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endfor; ?>
					</div>
					<blockquote class="font-serif text-xl leading-relaxed">&ldquo;<?php echo esc_html( $r['text'] ); ?>&rdquo;</blockquote>
					<figcaption class="mt-8 eyebrow"><?php echo esc_html( $r['name'] ); ?><?php echo $r['city'] ? ' — ' . esc_html( $r['city'] ) : ''; ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
