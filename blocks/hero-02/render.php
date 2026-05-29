<?php
/**
 * Server-side rendering for `zenyx/hero-02`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bg_type = isset( $attributes['backgroundMediaType'] ) ? (string) $attributes['backgroundMediaType'] : 'video';
$bg_type = in_array( $bg_type, array( 'video', 'image' ), true ) ? $bg_type : 'video';

$bg_image_id   = isset( $attributes['backgroundImageId'] ) ? (int) $attributes['backgroundImageId'] : 0;
$bg_image_url  = isset( $attributes['backgroundImageUrl'] ) ? (string) $attributes['backgroundImageUrl'] : '';
$bg_image_alt  = isset( $attributes['backgroundImageAlt'] ) ? (string) $attributes['backgroundImageAlt'] : '';
$bg_video_id   = isset( $attributes['backgroundVideoId'] ) ? (int) $attributes['backgroundVideoId'] : 0;
$bg_video_url  = isset( $attributes['backgroundVideoUrl'] ) ? (string) $attributes['backgroundVideoUrl'] : '';

$clip_type = isset( $attributes['clipMediaType'] ) ? (string) $attributes['clipMediaType'] : 'image';
$clip_type = in_array( $clip_type, array( 'video', 'image' ), true ) ? $clip_type : 'image';

$clip_image_id   = isset( $attributes['clipImageId'] ) ? (int) $attributes['clipImageId'] : 0;
$clip_image_url  = isset( $attributes['clipImageUrl'] ) ? (string) $attributes['clipImageUrl'] : '';
$clip_image_alt  = isset( $attributes['clipImageAlt'] ) ? (string) $attributes['clipImageAlt'] : '';
$clip_video_id   = isset( $attributes['clipVideoId'] ) ? (int) $attributes['clipVideoId'] : 0;
$clip_video_url  = isset( $attributes['clipVideoUrl'] ) ? (string) $attributes['clipVideoUrl'] : '';

if ( '' === $bg_image_url && $bg_image_id > 0 ) {
	$bg_image_url = (string) wp_get_attachment_image_url( $bg_image_id, 'full' );
}
if ( '' === $bg_image_alt && $bg_image_id > 0 ) {
	$bg_image_alt = (string) get_post_meta( $bg_image_id, '_wp_attachment_image_alt', true );
}
if ( '' === $bg_video_url && $bg_video_id > 0 ) {
	$bg_video_url = (string) wp_get_attachment_url( $bg_video_id );
}

if ( '' === $clip_image_url && $clip_image_id > 0 ) {
	$clip_image_url = (string) wp_get_attachment_image_url( $clip_image_id, 'full' );
}
if ( '' === $clip_image_alt && $clip_image_id > 0 ) {
	$clip_image_alt = (string) get_post_meta( $clip_image_id, '_wp_attachment_image_alt', true );
}
if ( '' === $clip_video_url && $clip_video_id > 0 ) {
	$clip_video_url = (string) wp_get_attachment_url( $clip_video_id );
}

$has_bg_media = ( 'video' === $bg_type ) ? '' !== trim( $bg_video_url ) : '' !== trim( $bg_image_url );

$has_clip_video = '' !== trim( $clip_video_url );
$has_clip_image = '' !== trim( $clip_image_url );
$has_clip       = $has_clip_video || $has_clip_image;

$clip_show_video = false;
if ( $has_clip_video && $has_clip_image ) {
	$clip_show_video = ( 'video' === $clip_type );
} elseif ( $has_clip_video ) {
	$clip_show_video = true;
} else {
	$clip_show_video = false;
}

$overlay_opacity = 0.8;

$heading = isset( $attributes['title'] ) ? trim( (string) $attributes['title'] ) : '';
if ( '' === $heading ) {
	$heading = __( 'Construimos los 4 procesos clave que te permiten escalar a +83.333€ al mes y tener:', 'zenyx' );
}

$default_features = array(
	__( 'Ventas estables que no dependen de un mes bueno o malo.', 'zenyx' ),
	__( 'Finanzas que te permiten dormir tranquilo.', 'zenyx' ),
	__( 'Clientes que se quedan y son rentables.', 'zenyx' ),
	__( 'Un equipo que responde sin que estés encima.', 'zenyx' ),
);

$features_raw = isset( $attributes['featureItems'] ) && is_array( $attributes['featureItems'] ) ? $attributes['featureItems'] : array();
$features     = array();
for ( $i = 0; $i < 4; $i++ ) {
	$line = isset( $features_raw[ $i ] ) ? trim( (string) $features_raw[ $i ] ) : '';
	$features[] = '' !== $line ? $line : $default_features[ $i ];
}

$button_text = isset( $attributes['buttonText'] ) ? trim( (string) $attributes['buttonText'] ) : '';
$button_url  = isset( $attributes['buttonUrl'] ) ? trim( (string) $attributes['buttonUrl'] ) : '';
$opens_new   = ! empty( $attributes['opensInNewTab'] );

$show_breadcrumbs = ! isset( $attributes['showBreadcrumbs'] ) || ! empty( $attributes['showBreadcrumbs'] );

$breadcrumb_home = isset( $attributes['breadcrumbHomeLabel'] ) ? trim( (string) $attributes['breadcrumbHomeLabel'] ) : '';
$breadcrumb_home = '' !== $breadcrumb_home ? $breadcrumb_home : __( 'Home', 'zenyx' );

$breadcrumb_current = isset( $attributes['breadcrumbCurrentLabel'] ) ? trim( (string) $attributes['breadcrumbCurrentLabel'] ) : '';
$breadcrumb_current = '' !== $breadcrumb_current ? $breadcrumb_current : __( 'Programa libertad', 'zenyx' );

$home_url = home_url( '/' );

$title_id = wp_unique_id( 'mwm-hero-02-title-' );
$grad_id  = wp_unique_id( 'mwm-hero-02-grad-' );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'           => 'mwm-hero-02 relative isolate w-full overflow-hidden',
		'aria-labelledby' => $title_id,
		'style'           => 'padding-top: calc(var(--header-height, 68px));',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-hero-02__bg absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
		<?php if ( $has_bg_media ) : ?>
			<?php if ( 'video' === $bg_type ) : ?>
				<video class="mwm-hero-02__bg-media h-full w-full object-cover" autoplay muted loop playsinline>
					<source src="<?php echo esc_url( $bg_video_url ); ?>" type="video/mp4" />
				</video>
			<?php else : ?>
				<img
					class="mwm-hero-02__bg-media h-full w-full object-cover"
					src="<?php echo esc_url( $bg_image_url ); ?>"
					alt="<?php echo esc_attr( $bg_image_alt ); ?>"
				/>
			<?php endif; ?>
		<?php endif; ?>
		<div
			class="mwm-hero-02__overlay absolute inset-0 bg-neutral-light"
			style="<?php echo esc_attr( 'opacity:' . $overlay_opacity . ';' ); ?>"
		></div>
	</div>
	<div class="mwm-hero-02__decor pointer-events-none absolute top-0 left-1/2 z-0 h-full w-[min(100%,1200px)] -translate-x-1/2 opacity-10" aria-hidden="true">
		<svg
			class="h-full w-full"
			width="976"
			height="768"
			viewBox="0 0 976 768"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			preserveAspectRatio="xMidYMax meet"
		>
			<path
				d="M486.296 383.149L257.594 611.835L715.391 612.863V768H0V611.835L228.721 383.149H486.296ZM975.975 0V152.613L745.422 383.149H487.829L718.399 152.613H259.107L258.641 0H975.975Z"
				fill="url(#<?php echo esc_attr( $grad_id ); ?>)"
				style="mix-blend-mode:plus-darker"
			/>
			<defs>
				<linearGradient
					id="<?php echo esc_attr( $grad_id ); ?>"
					x1="487.987"
					y1="0"
					x2="487.987"
					y2="768"
					gradientUnits="userSpaceOnUse"
				>
					<stop stop-color="#083B51" stop-opacity="0.8" />
					<stop offset="1" stop-color="#083B51" stop-opacity="0.3" />
				</linearGradient>
			</defs>
		</svg>
	</div>

	<div class="mwm-max-1 relative z-2">

		<div class="relative z-10 flex min-h-[min(768px,90svh)] flex-col gap-[60px] pb-9 pt-3 lg:min-h-[768px]">
			<?php if ( $show_breadcrumbs ) : ?>
				<nav class="w-full shrink-0" aria-label="<?php esc_attr_e( 'Migas de pan', 'zenyx' ); ?>">
					<ol class="m-0 flex list-none flex-wrap items-center gap-3 p-0 text-sm text-protagonista">
						<li class="m-0">
							<a class="text-protagonista no-underline hover:underline" href="<?php echo esc_url( $home_url ); ?>">
								<?php echo esc_html( $breadcrumb_home ); ?>
							</a>
						</li>
						<li class="m-0 font-medium" aria-current="page">
							<?php echo esc_html( $breadcrumb_current ); ?>
						</li>
					</ol>
				</nav>
			<?php endif; ?>

			<div class="flex min-h-0 flex-1 flex-col-reverse gap-10 lg:flex-row lg:items-end lg:justify-between lg:gap-6">
				<div class="flex min-h-0 w-full min-w-0 max-w-[746px] flex-1 flex-col justify-between gap-9 self-stretch lg:pr-4">
					<h1
						id="<?php echo esc_attr( $title_id ); ?>"
						class="m-0 max-w-[746px] font-heading text-[2rem] font-normal leading-[1.2] text-protagonista md:text-5xl lg:text-[48px]"
					>
						<?php echo esc_html( $heading ); ?>
					</h1>

					<div class="flex max-w-[636px] flex-col gap-9">
						<ul class="mwm-hero-02__features m-0 grid list-none grid-cols-1 gap-6 p-0 md:grid-cols-2 md:gap-6" role="list">
							<?php foreach ( $features as $feat ) : ?>
								<li class="max-w-[306px] text-xl font-medium leading-[1.2] text-protagonista">
									<?php echo esc_html( $feat ); ?>
								</li>
							<?php endforeach; ?>
						</ul>

						<?php if ( '' !== $button_text && function_exists( 'mwm_render_button' ) ) : ?>
							<div class="mwm-hero-02__cta-wrap flex flex-col items-start">
								<?php
								if ( '' !== $button_url ) {
									mwm_render_button(
										array(
											'text'          => $button_text,
											'url'           => $button_url,
											'variant'       => 'primary',
											'icon'          => 'arrow-right',
											'icon_position' => 'after',
											'size'          => 'md',
											'target'        => $opens_new ? '_blank' : '',
											'class'         => 'mwm-hero-02__cta',
										)
									);
								} else {
									mwm_render_button(
										array(
											'text'          => $button_text,
											'as'            => 'button',
											'variant'       => 'primary',
											'icon'          => 'arrow-right',
											'icon_position' => 'after',
											'size'          => 'md',
											'class'         => 'mwm-hero-02__cta',
											'disabled'      => true,
											'aria_disabled' => true,
										)
									);
								}
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="relative w-full max-w-[416px] shrink-0 justify-self-end lg:self-stretch">
					<div class="mwm-hero-02__clip-inner relative aspect-416/419 w-full overflow-hidden bg-neutral-light">
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
