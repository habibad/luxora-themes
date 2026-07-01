<?php
/**
 * Site footer.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$luxora_tagline = luxora_opt( 'luxora_footer_tagline' );
$luxora_address = luxora_opt( 'luxora_footer_address' );
$luxora_phone   = luxora_opt( 'luxora_footer_phone' );
$luxora_email   = luxora_opt( 'luxora_footer_email' );
$luxora_social  = luxora_social_links();
$social_icons   = array(
	'instagram' => 'instagram',
	'facebook'  => 'facebook',
	'youtube'   => 'youtube',
	'twitter'   => 'twitter',
);
?>
	</main><!-- #luxora-main -->

	<footer class="bg-ink text-cream mt-32">
		<div class="container-luxe py-20 grid gap-14 lg:grid-cols-[1.4fr_1fr_1fr_1fr_1.2fr]">

			<div>
				<div class="font-display text-3xl tracking-[0.32em]"><?php echo esc_html( strtoupper( get_bloginfo( 'name' ) ) ); ?></div>
				<?php if ( $luxora_tagline ) : ?>
					<p class="mt-6 font-serif text-lg text-cream/70 max-w-sm leading-relaxed"><?php echo esc_html( $luxora_tagline ); ?></p>
				<?php endif; ?>
				<?php if ( $luxora_social ) : ?>
					<div class="flex gap-4 mt-8">
						<?php foreach ( $luxora_social as $key => $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" class="h-10 w-10 grid place-items-center border border-cream/20 hover:border-gold hover:text-gold transition" aria-label="<?php echo esc_attr( ucfirst( $key ) ); ?>" rel="noopener" target="_blank">
								<?php echo luxora_icon( $social_icons[ $key ], 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div>
				<h4 class="eyebrow text-cream/60"><?php esc_html_e( 'Maison', 'luxora' ); ?></h4>
				<div class="mt-5 flex flex-col gap-3 text-sm text-cream/80 [&_a:hover]:text-gold">
					<?php
					luxora_flat_menu(
						'footer-maison',
						'',
						'<a href="' . esc_url( home_url( '/about' ) ) . '">' . esc_html__( 'Our story', 'luxora' ) . '</a>'
						. '<a href="' . esc_url( home_url( '/contact' ) ) . '">' . esc_html__( 'Contact', 'luxora' ) . '</a>'
					);
					?>
				</div>
			</div>

			<div>
				<h4 class="eyebrow text-cream/60"><?php esc_html_e( 'Shop', 'luxora' ); ?></h4>
				<div class="mt-5 flex flex-col gap-3 text-sm text-cream/80 [&_a:hover]:text-gold">
					<?php
					luxora_flat_menu(
						'footer-shop',
						'',
						'<a href="' . esc_url( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop' ) ) . '">' . esc_html__( 'All bags', 'luxora' ) . '</a>'
						. '<a href="' . esc_url( home_url( '/new-arrivals' ) ) . '">' . esc_html__( 'New arrivals', 'luxora' ) . '</a>'
						. '<a href="' . esc_url( home_url( '/best-sellers' ) ) . '">' . esc_html__( 'Best sellers', 'luxora' ) . '</a>'
					);
					?>
				</div>
			</div>

			<div>
				<h4 class="eyebrow text-cream/60"><?php esc_html_e( 'Care', 'luxora' ); ?></h4>
				<div class="mt-5 flex flex-col gap-3 text-sm text-cream/80 [&_a:hover]:text-gold">
					<?php
					luxora_flat_menu(
						'footer-care',
						'',
						'<a href="' . esc_url( home_url( '/track' ) ) . '">' . esc_html__( 'Track order', 'luxora' ) . '</a>'
						. '<a href="' . esc_url( home_url( '/faq' ) ) . '">' . esc_html__( 'FAQ', 'luxora' ) . '</a>'
					);
					?>
				</div>
			</div>

			<div>
				<h4 class="eyebrow text-cream/60"><?php esc_html_e( 'Visit', 'luxora' ); ?></h4>
				<?php if ( $luxora_address ) : ?>
					<p class="mt-5 font-serif text-lg leading-relaxed"><?php echo nl2br( esc_html( $luxora_address ) ); ?></p>
				<?php endif; ?>
				<?php if ( $luxora_phone || $luxora_email ) : ?>
					<p class="mt-4 text-sm text-cream/70">
						<?php if ( $luxora_phone ) : ?><?php echo esc_html( $luxora_phone ); ?><br /><?php endif; ?>
						<?php if ( $luxora_email ) : ?><a href="mailto:<?php echo esc_attr( $luxora_email ); ?>" class="hover:text-gold transition"><?php echo esc_html( $luxora_email ); ?></a><?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="border-t border-cream/10">
			<div class="container-luxe py-6 flex flex-col md:flex-row items-center justify-between gap-3 text-[11px] uppercase tracking-[0.24em] text-cream/50">
				<span>
					<?php
					/* translators: %1$s: year, %2$s: site name. */
					printf( esc_html__( '© %1$s %2$s. All rights reserved.', 'luxora' ), esc_html( gmdate( 'Y' ) ), esc_html( get_bloginfo( 'name' ) ) );
					?>
				</span>
				<div class="flex gap-6 [&_a:hover]:text-cream">
					<?php
					luxora_flat_menu(
						'footer-legal',
						'',
						'<a href="' . esc_url( home_url( '/privacy' ) ) . '">' . esc_html__( 'Privacy', 'luxora' ) . '</a>'
						. '<a href="' . esc_url( home_url( '/terms' ) ) . '">' . esc_html__( 'Terms', 'luxora' ) . '</a>'
					);
					?>
				</div>
			</div>
		</div>
	</footer>

</div><!-- .luxora-site -->

<?php wp_footer(); ?>
</body>
</html>
