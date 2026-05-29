<?php
/**
 * Server-side rendering for `zenyx/hero-03`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_breadcrumbs = array(
	array(
		'label' => 'Home',
		'url'   => '/',
	),
	array(
		'label' => 'Quienes somos',
		'url'   => '',
	),
);

$background_media_type = isset( $attributes['backgroundMediaType'] ) ? (string) $attributes['backgroundMediaType'] : 'image';
$background_media_type = in_array( $background_media_type, array( 'image', 'video' ), true ) ? $background_media_type : 'image';
$background_image_id   = isset( $attributes['backgroundImageId'] ) ? (int) $attributes['backgroundImageId'] : 0;
$background_image_url  = isset( $attributes['backgroundImageUrl'] ) ? (string) $attributes['backgroundImageUrl'] : '';
$background_image_alt  = isset( $attributes['backgroundImageAlt'] ) ? (string) $attributes['backgroundImageAlt'] : '';
$background_video_id   = isset( $attributes['backgroundVideoId'] ) ? (int) $attributes['backgroundVideoId'] : 0;
$background_video_url  = isset( $attributes['backgroundVideoUrl'] ) ? (string) $attributes['backgroundVideoUrl'] : '';

$heading            = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$description        = isset( $attributes['description'] ) ? (string) $attributes['description'] : '';
$show_breadcrumbs   = ! isset( $attributes['showBreadcrumbs'] ) || ! empty( $attributes['showBreadcrumbs'] );
$show_decorative_svg = ! isset( $attributes['showDecorativeSvg'] ) || ! empty( $attributes['showDecorativeSvg'] );

if ( '' === $background_image_url && $background_image_id > 0 ) {
	$background_image_url = (string) wp_get_attachment_image_url( $background_image_id, 'full' );
}

if ( '' === $background_image_alt && $background_image_id > 0 ) {
	$background_image_alt = (string) get_post_meta( $background_image_id, '_wp_attachment_image_alt', true );
}

if ( '' === $background_video_url && $background_video_id > 0 ) {
	$background_video_url = (string) wp_get_attachment_url( $background_video_id );
}

$has_background_media = ( 'video' === $background_media_type ) ? '' !== trim( $background_video_url ) : '' !== trim( $background_image_url );

$raw_breadcrumbs = isset( $attributes['breadcrumbs'] ) && is_array( $attributes['breadcrumbs'] ) ? $attributes['breadcrumbs'] : $default_breadcrumbs;
$breadcrumbs     = array();
$svg_id_base     = wp_unique_id( 'mwm-hero-03-' );
$svg_grad_id     = $svg_id_base . '-paint';
$svg_clip_id     = $svg_id_base . '-clip';

foreach ( $raw_breadcrumbs as $item ) {
	if ( ! is_array( $item ) ) {
		continue;
	}

	$label = isset( $item['label'] ) ? trim( (string) $item['label'] ) : '';
	$url   = isset( $item['url'] ) ? trim( (string) $item['url'] ) : '';

	if ( '' === $label ) {
		continue;
	}

	$breadcrumbs[] = array(
		'label' => $label,
		'url'   => $url,
	);
}

if ( empty( $breadcrumbs ) ) {
	$breadcrumbs = $default_breadcrumbs;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-hero-03 relative isolate w-full overflow-hidden',
	)
);
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> style="padding-top: calc(var(--header-height, 68px));">
	<div class="mwm-hero-03__bg absolute inset-0 -z-20 overflow-hidden bg-neutral-light" aria-hidden="true">
		<?php if ( $has_background_media ) : ?>
			<?php if ( 'video' === $background_media_type ) : ?>
				<video class="mwm-hero-03__bg-media h-full w-full object-cover mix-blend-luminosity" autoplay muted loop playsinline>
					<source src="<?php echo esc_url( $background_video_url ); ?>" type="video/mp4" />
				</video>
			<?php else : ?>
				<img
					class="mwm-hero-03__bg-media h-full w-full object-cover mix-blend-luminosity"
					src="<?php echo esc_url( $background_image_url ); ?>"
					alt="<?php echo esc_attr( $background_image_alt ); ?>"
				/>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="mwm-hero-03__gradient absolute inset-0 -z-10" aria-hidden="true"></div>

	<div class="mwm-max-1">
		<div class="mwm-hero-03__shell flex min-h-[600px] w-full flex-col py-[35px] pt-3 md:min-h-[680px] lg:min-h-[768px]">
			<div class="mwm-hero-03__content flex min-h-0 flex-1 flex-col gap-[35px]">
				<?php if ( $show_breadcrumbs && ! empty( $breadcrumbs ) ) : ?>
					<nav class="mwm-hero-03__breadcrumbs flex flex-wrap items-center gap-3" aria-label="<?php esc_attr_e( 'Breadcrumb', 'zenyx' ); ?>">
						<?php foreach ( $breadcrumbs as $index => $crumb ) : ?>
							<?php
							$crumb_label = $crumb['label'];
							$crumb_url   = $crumb['url'];
							$is_first    = 0 === $index;
							?>
							<?php if ( $is_first && '' !== $crumb_url ) : ?>
								<a class="mwm-hero-03__breadcrumb-link no-underline hover:text-acento transition-colors" href="<?php echo esc_url( $crumb_url ); ?>">
									<?php echo esc_html( $crumb_label ); ?>
								</a>
							<?php else : ?>
								<span class="mwm-hero-03__breadcrumb-current"><?php echo esc_html( $crumb_label ); ?></span>
							<?php endif; ?>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

				<div class="mwm-hero-03__center relative flex min-h-0 flex-1 flex-col items-center justify-end">
					<?php if ( $show_decorative_svg ) : ?>
						<div class="mwm-hero-03__decor absolute inset-x-0 bottom-0" aria-hidden="true">
							<svg class="mwm-hero-03__decor-svg" width="1296" height="406" viewBox="0 0 1296 406" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
								<g clip-path="url(#<?php echo esc_attr( $svg_clip_id ); ?>)">
									<path d="M922.17 210.211L985.27 51.4189H1043.77L894.565 405.892H836.553L893.432 272.856L800.168 51.4189H858.65L922.17 210.211ZM441.24 47.417C475.487 47.4171 502.93 58.3688 522.791 79.9795C542.49 101.428 552.485 130.297 552.485 165.774H552.469V193.897H378.06C380.198 210.081 386.257 222.766 396.463 232.519C408.208 243.745 422.853 249.188 441.24 249.188C456.322 249.188 468.91 245.997 478.63 239.695C488.204 233.491 494.49 225.05 497.859 213.904L498.588 211.491H551.983L550.071 219.834C544.952 242.222 532.365 260.901 512.634 275.384C493.08 289.737 469.363 297.011 442.131 297.011C407.09 297.011 378.06 285.589 355.866 263.071C333.688 240.602 322.445 209.952 322.445 171.995C322.445 134.039 333.607 103.47 355.639 81.1299C377.687 58.7577 406.993 47.417 441.24 47.417ZM699.802 47.417C727.484 47.419 750.533 56.686 768.318 74.9414C786.041 93.1502 795.032 117.58 795.032 147.55V292.572H741.88V152.442C741.88 135.513 737.344 122.358 728.013 112.233C718.844 102.287 707.617 97.459 691.79 97.459C675.963 97.459 663.359 102.676 653.299 113.4C643.109 124.254 638.151 137.165 638.151 152.879V292.556H585.453V51.3379L608.992 51.4189H636.386V71.2148C641.1 66.6951 646.511 62.645 652.603 59.0811C665.804 51.3387 681.693 47.418 699.802 47.417ZM1175.07 130.637L1228.17 51.4189H1291.48L1211.47 169.307L1296 292.572H1232.64L1175.1 208.316L1116.72 292.572H1054.51L1137.1 168.464L1058.06 51.4189H1120.73L1175.07 130.637ZM80.6113 243.567L223.884 243.875V292.426H0V243.567L71.5713 171.995H152.183L80.6113 243.567ZM305.435 52.1152V99.873L233.279 171.996H152.669L224.823 99.873H81.0967L80.9512 52.1152H305.435ZM440.803 95.2402C422.351 95.2402 407.738 100.375 396.123 110.921C386.128 119.993 380.182 132.289 378.076 148.311H499.301C497.697 133.164 492.529 120.738 483.911 111.326C473.981 100.505 459.254 95.2403 440.803 95.2402ZM376.035 28.5928L353.16 51.5811H324.454V0H376.035V28.5928Z" fill="url(#<?php echo esc_attr( $svg_grad_id ); ?>)" fill-opacity="0.5"></path>
								</g>
								<defs>
									<linearGradient id="<?php echo esc_attr( $svg_grad_id ); ?>" x1="648.001" y1="0" x2="648.001" y2="405.892" gradientUnits="userSpaceOnUse">
										<stop stop-color="#C1D9E4" stop-opacity="0.6"></stop>
										<stop offset="1" stop-color="#C1D9E4" stop-opacity="0.1"></stop>
									</linearGradient>
									<clipPath id="<?php echo esc_attr( $svg_clip_id ); ?>">
										<rect width="1296" height="405.891" fill="white"></rect>
									</clipPath>
								</defs>
							</svg>
						</div>
					<?php endif; ?>

					<div class="mwm-hero-03__text-wrap relative z-10 mx-auto flex w-full max-w-[1296px] flex-col items-center gap-6 px-4 sm:px-[110px] lg:px-[220px]">
						<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
							<h1 class="mwm-hero-03__heading w-full text-center text-[2rem] font-heading leading-[1.2] text-neutral-light md:text-5xl">
								<?php echo wp_kses_post( $heading ); ?>
							</h1>
						<?php endif; ?>

						<?php if ( '' !== trim( wp_strip_all_tags( $description ) ) ) : ?>
							<p class="mwm-hero-03__description max-w-[636px] text-center text-lg leading-[1.3] text-white md:text-xl">
								<?php echo wp_kses_post( $description ); ?>
							</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
