<?php
/**
 * Blog archive hero (Customizer-driven).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = get_option( 'mwm_blog_hero_title' );
$title   = is_string( $title ) ? trim( $title ) : '';
$title   = '' !== $title ? $title : __( 'Blog sobre escalar agencias', THEME_TEXT_DOMAIN );

$lead = get_option( 'mwm_blog_hero_lead' );
$lead = is_string( $lead ) ? trim( $lead ) : '';
$lead = '' !== $lead ? $lead : __( 'Aqui tienes el blog para duenos de agencias que no se conforman con su facturacion.', THEME_TEXT_DOMAIN );

$subline = get_option( 'mwm_blog_hero_subline' );
$subline = is_string( $subline ) ? trim( $subline ) : '';
$subline = '' !== $subline ? $subline : __( 'Consejos, mentalidad, procesos y estrategias para aprender a escalar desde dentro.', THEME_TEXT_DOMAIN );

$cta_text = get_option( 'mwm_blog_hero_cta_text' );
$cta_text = is_string( $cta_text ) ? trim( $cta_text ) : '';
$cta_text = '' !== $cta_text ? $cta_text : __( 'Ver articulos', THEME_TEXT_DOMAIN );

$cta_url = get_option( 'mwm_blog_hero_cta_url' );
$cta_url = is_string( $cta_url ) ? trim( $cta_url ) : '#articulos';
if ( '' === $cta_url ) {
	$cta_url = '#articulos';
}

$home_url = home_url( '/' );

$gradient_src = get_template_directory_uri() . '/assets/images/blog-hero-degradado.png';
$gradient_path = get_template_directory() . '/assets/images/blog-hero-degradado.png';
if ( ! file_exists( $gradient_path ) ) {
	$gradient_src = '';
}
?>
<section data-dark class="overflow-hidden bg-neutral-light pt-header pb-10 lg:min-h-[700px] lg:pb-9" aria-labelledby="mwm-blog-hero-title" >
	<div class="mwm-max-1">
		<div class="relative">
			<?php if ( '' !== $gradient_src ) : ?>
				<img
					class="pointer-events-none absolute left-0 top-20 z-1 w-[min(100%,39.75rem)] max-w-[85vw] object-cover md:left-[35px] md:top-31"
					src="<?php echo esc_url( $gradient_src ); ?>"
					width="636"
					height="542"
					alt=""
					decoding="async"
					loading="eager"
				/>
			<?php endif; ?>
		
			<div class="relative z-2 mwm-max-1 flex min-h-[min(700px,90svh)] flex-col justify-between gap-10 pt-3 pb-8 lg:gap-14 lg:pb-0">
				<nav class="w-full shrink-0" aria-label="<?php esc_attr_e( 'Migas de pan', THEME_TEXT_DOMAIN ); ?>">
					<ol class="flex flex-wrap items-center gap-3 text-sm text-protagonista">
						<li>
							<a class="no-underline hover:underline" href="<?php echo esc_url( $home_url ); ?>">
								<?php esc_html_e( 'Home', THEME_TEXT_DOMAIN ); ?>
							</a>
						</li>
						<li class="font-medium" aria-current="page">
							<?php esc_html_e( 'Blog', THEME_TEXT_DOMAIN ); ?>
						</li>
					</ol>
				</nav>
		
				<div class="flex w-full flex-col items-center gap-6 text-center lg:max-w-[636px] lg:mx-auto">
					<h1 id="mwm-blog-hero-title" class="font-heading text-display-l text-protagonista w-full leading-[1.2]">
						<?php echo esc_html( $title ); ?>
					</h1>
					<p class="font-body text-body-l text-protagonista w-full max-w-[636px] leading-[1.2]">
						<?php echo esc_html( $lead ); ?>
					</p>
				</div>
		
				<div class="flex w-full flex-col items-center gap-6 text-center lg:max-w-[636px] lg:mx-auto">
					<p class="font-body text-display-s text-acento w-full max-w-[636px] leading-[1.2]">
						<?php echo esc_html( $subline ); ?>
					</p>
					<div class="flex flex-col items-center gap-1.5">
						<a
							class="mwm-btn mwm-btn--primary mwm-btn--md mwm-btn--has-icon mwm-btn--icon-after inline-flex items-center gap-3"
							href="<?php echo preg_match( '/^#[\w-]+$/', $cta_url ) ? esc_attr( $cta_url ) : esc_url( $cta_url ); ?>"
						>
							<span class="mwm-btn__label"><?php echo esc_html( $cta_text ); ?></span>
							<span class="mwm-btn__icon" aria-hidden="true"><?php echo mwm_get_button_icon_svg( 'arrow-right' ); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
