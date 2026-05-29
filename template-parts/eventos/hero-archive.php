<?php
/**
 * Eventos archive hero (Customizer-driven).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title_raw = get_option( 'mwm_eventos_archive_hero_title' );
$title_raw = is_string( $title_raw ) ? $title_raw : '';
if ( '' === trim( $title_raw ) ) {
	$title_raw = __( "Eventos para agencias\nde marketing", THEME_TEXT_DOMAIN );
}

$lead = get_option( 'mwm_eventos_archive_hero_lead' );
$lead = is_string( $lead ) ? trim( $lead ) : '';
if ( '' === $lead ) {
	$lead = __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.', THEME_TEXT_DOMAIN );
}

$bg_type = get_option( 'mwm_eventos_archive_hero_bg_type' );
$bg_type = in_array( (string) $bg_type, array( 'video', 'image' ), true ) ? $bg_type : 'video';

$bg_video_id  = absint( get_option( 'mwm_eventos_archive_hero_bg_video_id' ) );
$bg_image_id  = absint( get_option( 'mwm_eventos_archive_hero_bg_image_id' ) );
$bg_image_url = $bg_image_id ? (string) wp_get_attachment_image_url( $bg_image_id, 'full' ) : '';
$bg_image_alt = $bg_image_id ? (string) get_post_meta( $bg_image_id, '_wp_attachment_image_alt', true ) : '';
$bg_video_url = $bg_video_id ? (string) wp_get_attachment_url( $bg_video_id ) : '';

$clip_type = get_option( 'mwm_eventos_archive_hero_clip_type' );
$clip_type = in_array( (string) $clip_type, array( 'video', 'image' ), true ) ? $clip_type : 'image';

$clip_video_id  = absint( get_option( 'mwm_eventos_archive_hero_clip_video_id' ) );
$clip_image_id  = absint( get_option( 'mwm_eventos_archive_hero_clip_image_id' ) );
$clip_image_url = $clip_image_id ? (string) wp_get_attachment_image_url( $clip_image_id, 'full' ) : '';
$clip_image_alt = $clip_image_id ? (string) get_post_meta( $clip_image_id, '_wp_attachment_image_alt', true ) : '';
$clip_video_url = $clip_video_id ? (string) wp_get_attachment_url( $clip_video_id ) : '';

$has_bg_media = ( 'video' === $bg_type ) ? '' !== trim( $bg_video_url ) : '' !== trim( $bg_image_url );

$has_clip_video = '' !== trim( $clip_video_url );
$has_clip_image = '' !== trim( $clip_image_url );
$has_clip       = $has_clip_video || $has_clip_image;

/*
 * Si el tipo elegido no coincide con el adjunto subido (p. ej. defecto "imagen" pero solo hay vídeo en la figura),
 * usar el medio que exista para no dejar la figura vacía.
 */
$clip_show_video = false;
if ( $has_clip_video && $has_clip_image ) {
	$clip_show_video = ( 'video' === $clip_type );
} elseif ( $has_clip_video ) {
	$clip_show_video = true;
} else {
	$clip_show_video = false;
}

$overlay_opacity = 0.8;

$home_url = home_url( '/' );

$title_lines = preg_split( '/\r\n|\r|\n/', $title_raw );
$title_lines = is_array( $title_lines ) ? array_values( array_filter( array_map( 'trim', $title_lines ) ) ) : array();
if ( empty( $title_lines ) ) {
	$title_lines = array( __( 'Eventos', THEME_TEXT_DOMAIN ) );
}

$grad_id = 'mwm-eventos-archive-hero-grad';
?>
<section
	class="mwm-eventos-archive-hero relative isolate w-full overflow-hidden"
	data-dark
	aria-labelledby="mwm-eventos-hero-title"
	style="padding-top: calc(var(--header-height, 68px));"
>
	<div class="mwm-eventos-archive-hero__bg absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
		<?php if ( $has_bg_media ) : ?>
			<?php if ( 'video' === $bg_type ) : ?>
				<video class="mwm-eventos-archive-hero__bg-media h-full w-full object-cover" autoplay muted loop playsinline>
					<source src="<?php echo esc_url( $bg_video_url ); ?>" type="video/mp4" />
				</video>
			<?php else : ?>
				<img
					class="mwm-eventos-archive-hero__bg-media h-full w-full object-cover"
					src="<?php echo esc_url( $bg_image_url ); ?>"
					alt="<?php echo esc_attr( $bg_image_alt ); ?>"
				/>
			<?php endif; ?>
		<?php endif; ?>
		<div
			class="mwm-eventos-archive-hero__overlay absolute inset-0 bg-neutral-light"
			style="<?php echo esc_attr( 'opacity:' . $overlay_opacity . ';' ); ?>"
		></div>
	</div>

	
	<div class="mwm-max-1 relative z-2">
		<div class="mwm-eventos-archive-hero__decor pointer-events-none absolute bottom-0 left-0 z-0 max-h-[min(90vh,767px)] w-[min(100%,1183px)] opacity-10" aria-hidden="true">
			<svg
				class="h-auto w-full"
				width="1183"
				height="767"
				viewBox="0 0 1183 767"
				fill="none"
				xmlns="http://www.w3.org/2000/svg"
				preserveAspectRatio="xMidYMax meet"
			>
				<path
					d="M253.597 766.314L704.289 767.326V920.056H0V766.314L225.172 541.177H478.75L253.597 766.314ZM960.829 163.973V314.218L733.854 541.177H480.259L707.252 314.218H255.087L254.627 163.973H960.829ZM1182.93 89.9814L1110.96 162.298H1020.68V0H1182.93V89.9814Z"
					fill="url(#<?php echo esc_attr( $grad_id ); ?>)"
					style="mix-blend-mode:plus-darker"
				/>
				<defs>
					<linearGradient
						id="<?php echo esc_attr( $grad_id ); ?>"
						x1="591.464"
						y1="0"
						x2="591.464"
						y2="920.056"
						gradientUnits="userSpaceOnUse"
					>
						<stop stop-color="#083B51" stop-opacity="0" />
						<stop offset="1" stop-color="#083B51" />
					</linearGradient>
				</defs>
			</svg>
		</div>
		<div class="flex min-h-[min(768px,90svh)] flex-col justify-between gap-10 pb-10 pt-3 lg:min-h-[768px] lg:gap-14 lg:pb-9">
			<nav class="w-full shrink-0" aria-label="<?php esc_attr_e( 'Migas de pan', THEME_TEXT_DOMAIN ); ?>">
				<ol class="flex flex-wrap items-center gap-3 text-sm text-protagonista">
					<li>
						<a class="no-underline hover:underline" href="<?php echo esc_url( $home_url ); ?>">
							<?php esc_html_e( 'Home', THEME_TEXT_DOMAIN ); ?>
						</a>
					</li>
					<li class="font-medium" aria-current="page">
						<?php esc_html_e( 'Eventos', THEME_TEXT_DOMAIN ); ?>
					</li>
				</ol>
			</nav>

			<div class="flex min-h-0 flex-1 flex-col gap-10 lg:flex-row lg:items-end lg:justify-between lg:gap-6">
				<div class="flex min-h-0 min-w-0 flex-1 flex-col justify-between gap-8 self-stretch lg:min-h-[min(419px,50vh)] lg:pr-4">
					<div class="flex flex-col gap-2.5">
						<h1
							id="mwm-eventos-hero-title"
							class="font-heading text-[2rem] leading-[1.2] text-protagonista md:text-5xl lg:max-w-[856px] lg:text-[48px]"
						>
							<?php
							foreach ( $title_lines as $line ) {
								echo '<span class="block">' . esc_html( $line ) . '</span>';
							}
							?>
						</h1>
					</div>
					<div class="flex w-full flex-col items-start lg:items-end">
						<p class="w-full max-w-[416px] text-left text-xl font-medium leading-[1.2] text-protagonista">
							<?php echo esc_html( $lead ); ?>
						</p>
					</div>
				</div>

				<div class="relative w-full max-w-[416px] shrink-0 justify-self-end">
					<div
						class="mwm-eventos-archive-hero__clip-inner relative aspect-416/419 w-full overflow-hidden bg-neutral-light"
					>
						<?php if ( $has_clip ) : ?>
							<?php if ( $clip_show_video ) : ?>
								<video class="h-full w-full object-cover" autoplay muted loop playsinline>
									<source src="<?php echo esc_url( $clip_video_url ); ?>" type="video/mp4" />
								</video>
							<?php else : ?>
								<img
									class="h-full w-full object-cover"
									src="<?php echo esc_url( $clip_image_url ); ?>"
									alt="<?php echo esc_attr( $clip_image_alt ); ?>"
								/>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
