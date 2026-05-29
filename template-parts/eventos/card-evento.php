<?php
/**
 * Fila de evento en archive: media (blur + Play) + tarjeta de texto con clip-path.
 *
 * Args:
 * - post_id     (int)  Requerido.
 * - media_first (bool) true = media a la izquierda en desktop (orden DOM: media, texto; en móvil el texto va arriba con flex order).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$card_args = array();
if ( isset( $args ) && is_array( $args ) ) {
	$card_args = $args;
}
$card_args = wp_parse_args(
	$card_args,
	array(
		'post_id'     => 0,
		'media_first' => true,
	)
);

$post_id = absint( $card_args['post_id'] );
if ( $post_id < 1 ) {
	return;
}

$post = get_post( $post_id );
if ( ! $post || MWM_EVENTO_POST_TYPE !== $post->post_type ) {
	return;
}

$media_first = ! empty( $card_args['media_first'] );

$title = get_the_title( $post_id );

$terms = get_the_terms( $post_id, MWM_EVENTO_CATEGORIA_TAX );
$label = '';
if ( $terms && ! is_wp_error( $terms ) && isset( $terms[0] ) ) {
	$label = $terms[0]->name;
}

$fecha_display = get_post_meta( $post_id, MWM_EVENTO_FECHA_META, true );
$fecha_display = is_string( $fecha_display ) ? trim( $fecha_display ) : '';

$lugar = get_post_meta( $post_id, MWM_EVENTO_LUGAR_META, true );
$lugar = is_string( $lugar ) ? trim( $lugar ) : '';
if ( function_exists( 'mb_strtoupper' ) ) {
	$lugar = '' !== $lugar ? mb_strtoupper( $lugar, 'UTF-8' ) : '';
} else {
	$lugar = '' !== $lugar ? strtoupper( $lugar ) : '';
}

$video_url = get_post_meta( $post_id, MWM_EVENTO_VIDEO_URL_META, true );
$video_url = is_string( $video_url ) ? trim( $video_url ) : '';
$has_video = '' !== $video_url;

$thumb_html = '';
if ( has_post_thumbnail( $post_id ) ) {
	$thumb_html = get_the_post_thumbnail(
		$post_id,
		'large',
		array(
			'class'    => 'mwm-card-evento__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => '',
		)
	);
}

$row_classes = array(
	'mwm-card-evento__row',
	'flex',
	'flex-col',
	'min-h-0',
	'gap-6',
	'lg:min-h-[416px]',
	'lg:flex-row',
	'lg:items-stretch',
);
if ( ! $media_first ) {
	$row_classes[] = 'lg:flex-row-reverse';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $row_classes ) ); ?>">
	<?php /* Móvil: texto arriba, media abajo (orden DOM sigue siendo media→texto para desktop). */ ?>
	<div class="mwm-card-evento__media relative order-last min-h-[280px] flex-1 overflow-hidden lg:order-none lg:min-h-0">
		<div class="mwm-card-evento__bg" aria-hidden="true">
			<?php if ( '' !== $thumb_html ) : ?>
				<div class="mwm-card-evento__bg-blur">
					<?php echo $thumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- thumbnail HTML. ?>
				</div>
			<?php else : ?>
				<div class="mwm-card-evento__bg-fallback bg-protagonista"></div>
			<?php endif; ?>
		</div>
		<div class="mwm-card-evento__overlay absolute inset-0 z-20 flex items-end gap-5 bg-black/30 p-5">
			<?php if ( $has_video && function_exists( 'mwm_render_button' ) ) : ?>
				<?php
				mwm_render_button(
					array(
						'text'            => __( 'Play', THEME_TEXT_DOMAIN ),
						'url'             => $video_url,
						'variant'         => 'play-outline',
						'icon'            => 'play',
						'class'           => 'mwm-card-evento__video-trigger whitespace-nowrap',
						'icon_position'   => 'before',
						'size'            => 'md',
						'data_attributes' => array(
							// Mismo valor que card-caso: scripts.js solo hace Fancybox.bind a "caso-video".
							'fancybox' => 'caso-video',
							'caption'  => $title,
						),
						'aria_label'      => sprintf(
							/* translators: %s: event title */
							__( 'Reproducir vídeo: %s', THEME_TEXT_DOMAIN ),
							$title
						),
					)
				);
				?>
			<?php endif; ?>
		</div>
	</div>

	<div class="mwm-card-evento__clip order-first w-full max-w-none self-stretch shrink-0 lg:order-none lg:max-w-[416px] lg:self-auto">
		<div class="mwm-card-evento__surface relative flex min-h-[min(100%,419px)] w-full flex-col justify-between bg-white p-5 lg:min-h-[416px]">
			<div class="flex min-h-0 flex-col gap-3">
				<?php if ( '' !== $label ) : ?>
					<p class="w-full max-w-full font-body text-[16px] font-medium uppercase leading-[1.2] text-protagonista">
						<?php echo esc_html( $label ); ?>
					</p>
				<?php endif; ?>
				<h3 class="w-full max-w-full font-body text-2xl font-normal leading-[1.2] text-protagonista md:text-[32px]">
					<?php echo esc_html( $title ); ?>
				</h3>
			</div>
			<div class="mt-6 flex flex-col gap-3 pr-0 lg:mt-0 lg:pr-[90px]">
				<?php if ( '' !== $fecha_display ) : ?>
					<p class="font-body text-xl leading-[1.2] text-acento"><?php echo esc_html( $fecha_display ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $lugar ) : ?>
					<p class="font-body text-xl leading-[1.2] text-acento"><?php echo esc_html( $lugar ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php
