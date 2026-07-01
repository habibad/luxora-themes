<?php
/**
 * Newsletter section — mirrors Newsletter.tsx, wired to AJAX + Customizer.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ns_eyebrow  = luxora_opt( 'newsletter_eyebrow' );
$ns_title    = luxora_opt( 'newsletter_title' );
$ns_subtitle = luxora_opt( 'newsletter_subtitle' );
$ns_action   = luxora_opt( 'newsletter_action' );
?>
<section class="bg-beige luxora-newsletter" data-reveal>
	<div class="container-luxe py-24 md:py-32 grid md:grid-cols-2 gap-12 items-center">
		<div>
			<?php if ( $ns_eyebrow ) : ?>
				<p class="eyebrow mb-5"><?php echo esc_html( $ns_eyebrow ); ?></p>
			<?php endif; ?>
			<h2 class="font-display text-4xl md:text-5xl lg:text-6xl leading-[1.05]"><?php echo wp_kses( $ns_title, array( 'br' => array() ) ); ?></h2>
			<?php if ( $ns_subtitle ) : ?>
				<p class="mt-6 font-serif text-lg text-ink/70 max-w-md"><?php echo esc_html( $ns_subtitle ); ?></p>
			<?php endif; ?>
		</div>
		<form class="luxora-newsletter-form flex flex-col gap-4 max-w-md md:ml-auto w-full" novalidate<?php echo $ns_action ? ' action="' . esc_url( $ns_action ) . '" method="post"' : ''; ?>>
			<?php wp_nonce_field( 'luxora_nonce', 'luxora_news_nonce' ); ?>
			<input
				type="email"
				name="email"
				required
				placeholder="<?php esc_attr_e( 'Your email address', 'luxora' ); ?>"
				aria-label="<?php esc_attr_e( 'Your email address', 'luxora' ); ?>"
				class="luxora-newsletter-email bg-transparent border-b border-ink/30 focus:border-ink outline-none py-4 text-base placeholder:text-ink/40"
			/>
			<button type="submit" class="btn-luxe self-start"><?php esc_html_e( 'Subscribe', 'luxora' ); ?></button>
			<p class="luxora-newsletter-msg text-xs text-ink/50" role="status" aria-live="polite">
				<?php
				printf(
					/* translators: %s: privacy policy link */
					esc_html__( 'By subscribing you agree to our %s.', 'luxora' ),
					'<a href="' . esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : '#' ) . '" class="link-underline">' . esc_html__( 'Privacy Policy', 'luxora' ) . '</a>'
				);
				?>
			</p>
		</form>
	</div>
</section>
