<?php
/**
 * Card de caso de éxito (archive / reutilizable).
 *
 * Args (get_template_part tercer parámetro):
 * - is_active (bool) Desktop: card expandida en la fila.
 * - post_id   (int)  Opcional; por defecto el post actual.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$card_args = array();
if ( isset( $args ) && is_array( $args ) ) {
	$card_args = $args;
} else {
	$card_args = array(
		'post_id'   => isset( $post_id ) ? (int) $post_id : 0,
		'is_active' => ! empty( $is_active ),
	);
}
$card_args = wp_parse_args(
	$card_args,
	array(
		'is_active' => false,
		'post_id'   => 0,
		'heading_level' => 'h2',
	)
);

$post_id = absint( $card_args['post_id'] );
if ( $post_id < 1 ) {
	$post_id = get_the_ID();
}
if ( $post_id < 1 ) {
	return;
}

$post = get_post( $post_id );
if ( ! $post || MWM_CASO_EXITO_POST_TYPE !== $post->post_type ) {
	return;
}

$is_active = ! empty( $card_args['is_active'] );
$heading_level = isset( $card_args['heading_level'] ) ? strtolower( (string) $card_args['heading_level'] ) : 'h2';
if ( ! in_array( $heading_level, array( 'h2', 'h3' ), true ) ) {
	$heading_level = 'h2';
}

$pre_titulo = get_post_meta( $post_id, MWM_CASO_EXITO_PRE_TITULO_META, true );
$pre_titulo = is_string( $pre_titulo ) ? trim( $pre_titulo ) : '';

$video_url = get_post_meta( $post_id, MWM_CASO_EXITO_VIDEO_URL_META, true );
$video_url = is_string( $video_url ) ? trim( $video_url ) : '';
$has_video = '' !== $video_url;
$single_disabled = function_exists( 'mwm_caso_exito_single_disabled' ) && mwm_caso_exito_single_disabled( $post_id );

$title     = get_the_title( $post_id );
$permalink = $single_disabled ? '' : get_permalink( $post_id );

$thumb_html = '';
if ( has_post_thumbnail( $post_id ) ) {
	$thumb_html = get_the_post_thumbnail(
		$post_id,
		'large',
		array(
			'class'    => 'mwm-card-caso__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => '',
		)
	);
}

$article_classes = array(
	'mwm-card-caso',
	'relative',
	'flex',
	'min-h-[280px]',
	'overflow-hidden',
	'outline-none',
	'lg:h-[408px]',
	'lg:min-h-0',
	'lg:min-w-0',
);
if ( $is_active ) {
	$article_classes[] = 'is-active';
}
?>
<article
	<?php post_class( $article_classes, $post_id ); ?>
	tabindex="0"
	data-mwm-card-caso
	aria-label="<?php echo esc_attr( $title ); ?>"
>
	<div class="mwm-card-caso__inner relative flex h-full min-h-[inherit] w-full flex-col">
		<?php /* Imagen de fondo difuminada (visible con estado activo / móvil). */ ?>
		<div class="mwm-card-caso__bg" aria-hidden="true">
			<?php if ( '' !== $thumb_html ) : ?>
				<div class="mwm-card-caso__bg-blur">
					<?php echo $thumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- thumbnail HTML. ?>
				</div>
			<?php else : ?>
				<div class="mwm-card-caso__bg-fallback bg-protagonista"></div>
			<?php endif; ?>
		</div>

		<?php /* Panel único: inactivo = sólido #083b51 + CTAs invisibles; activo = transparente + backdrop blur + CTAs. */ ?>
		<div class="mwm-card-caso__panel relative z-10 flex min-h-0 flex-1 flex-col p-5">
			<div class="flex min-h-0 flex-1 flex-col gap-3">
				<?php if ( '' !== $pre_titulo ) : ?>
					<p class="mwm-card-caso__line-a w-full max-w-full text-[16px] uppercase leading-[1.2] text-neutral-light">
						<?php echo esc_html( $pre_titulo ); ?>
					</p>
				<?php endif; ?>
				<?php echo '<' . esc_html( $heading_level ) . ' class="mwm-card-caso__line-b w-full max-w-full text-[16px] uppercase leading-[1.2] text-white">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( '' !== $permalink ) : ?>
					<a
						class="mwm-card-caso__title-link no-underline transition-colors hover:text-neutral-light focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-acento"
						href="<?php echo esc_url( $permalink ); ?>"
					>
						<?php echo esc_html( $title ); ?>
					</a>
					<?php else : ?>
						<?php echo esc_html( $title ); ?>
					<?php endif; ?>
				<?php echo '</' . esc_html( $heading_level ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<div class="mwm-card-caso__actions flex min-h-0 flex-1 items-end gap-5">
				<?php if ( $has_video ) : ?>
					<?php
					mwm_render_button(
						array(
							'text'          => __( 'Play', THEME_TEXT_DOMAIN ),
							'url'           => $video_url,
							'variant'       => 'play-outline',
							'icon'          => 'play',
							'class'         => 'whitespace-nowrap mwm-card-caso__video-trigger',
							'icon_position' => 'before',
							'size'          => 'md',
							'data_attributes' => array(
								'fancybox' => 'caso-video',
								'caption'  => $title,
							),
							'aria_label'    => sprintf(
								/* translators: %s: caso title */
								__( 'Reproducir vídeo: %s', THEME_TEXT_DOMAIN ),
								$title
							),
						)
					);
					?>
				<?php endif; ?>
				<?php if ( '' !== $permalink ) : ?>
				<?php
				mwm_render_button(
					array(
						'text'          => __( 'Ver caso', THEME_TEXT_DOMAIN ),
						'url'           => $permalink,
						'variant'       => 'light',
						'class'         => 'whitespace-nowrap',
						'icon'          => 'arrow-right',
						'icon_position' => 'after',
						'size'          => 'md',
					)
				);
				?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>
