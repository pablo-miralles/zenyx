<?php
/**
 * Server-side rendering for `zenyx/caso-exito-content-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$retos_title      = isset( $attributes['retosTitle'] ) ? (string) $attributes['retosTitle'] : 'Retos';
$retos_text_left  = isset( $attributes['retosTextLeft'] ) ? (string) $attributes['retosTextLeft'] : '';
$retos_text_right = isset( $attributes['retosTextRight'] ) ? (string) $attributes['retosTextRight'] : '';
$ejecucion_title  = isset( $attributes['ejecucionTitle'] ) ? (string) $attributes['ejecucionTitle'] : 'Ejecucion';
$resultados_title = isset( $attributes['resultadosTitle'] ) ? (string) $attributes['resultadosTitle'] : 'Resultados';

$default_ejecucion_items = array(
	array(
		'id'   => 'ej-1',
		'step' => '(01)',
		'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut pulvinar diam dapibus lectus euismod suscipit et nec sem.',
	),
	array(
		'id'   => 'ej-2',
		'step' => '(02)',
		'text' => 'Maecenas venenatis enim a nibh fermentum, eu elementum odio interdum. Interdum et malesuada fames ac ante ipsum primis in faucibus.',
	),
	array(
		'id'   => 'ej-3',
		'step' => '(03)',
		'text' => 'Ut accumsan mauris quis felis pellentesque placerat eget vitae mi. Integer quis sem at velit ultricies fermentum.',
	),
	array(
		'id'   => 'ej-4',
		'step' => '(04)',
		'text' => 'Donec vitae neque nec arcu elementum tincidunt. Sed blandit sem vel turpis volutpat, in volutpat lacus efficitur.',
	),
);

$default_resultado_items = array(
	array(
		'id'    => 'res-1',
		'value' => '+20%',
		'label' => 'Lorem ipsum dolor sit amet',
	),
	array(
		'id'    => 'res-2',
		'value' => '+35%',
		'label' => 'Lorem ipsum dolor sit amet',
	),
	array(
		'id'    => 'res-3',
		'value' => '+20h',
		'label' => 'Lorem ipsum dolor sit amet',
	),
	array(
		'id'    => 'res-4',
		'value' => '+20h',
		'label' => 'Lorem ipsum dolor sit amet',
	),
);

$raw_ejecucion_items = isset( $attributes['ejecucionItems'] ) && is_array( $attributes['ejecucionItems'] ) ? array_values( $attributes['ejecucionItems'] ) : array();
$raw_resultado_items = isset( $attributes['resultadoItems'] ) && is_array( $attributes['resultadoItems'] ) ? array_values( $attributes['resultadoItems'] ) : array();

$ejecucion_items = array();
if ( ! empty( $raw_ejecucion_items ) ) {
	foreach ( $raw_ejecucion_items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$ejecucion_items[] = array(
			'id'   => isset( $item['id'] ) ? (string) $item['id'] : '',
			'step' => isset( $item['step'] ) ? (string) $item['step'] : '',
			'text' => isset( $item['text'] ) ? (string) $item['text'] : '',
		);
	}
}
if ( empty( $ejecucion_items ) ) {
	$ejecucion_items = $default_ejecucion_items;
}

$resultado_items = array();
if ( ! empty( $raw_resultado_items ) ) {
	foreach ( $raw_resultado_items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$resultado_items[] = array(
			'id'    => isset( $item['id'] ) ? (string) $item['id'] : '',
			'value' => isset( $item['value'] ) ? (string) $item['value'] : '',
			'label' => isset( $item['label'] ) ? (string) $item['label'] : '',
		);
	}
}
if ( empty( $resultado_items ) ) {
	$resultado_items = $default_resultado_items;
}

$card_pretitle_override = isset( $attributes['cardPretitle'] ) ? trim( (string) $attributes['cardPretitle'] ) : '';
$card_title_override    = isset( $attributes['cardTitle'] ) ? trim( (string) $attributes['cardTitle'] ) : '';
$card_video_override    = isset( $attributes['cardVideoUrl'] ) ? trim( (string) $attributes['cardVideoUrl'] ) : '';
$card_play_override     = isset( $attributes['cardPlayLabel'] ) ? trim( (string) $attributes['cardPlayLabel'] ) : '';
$card_img_url_override  = isset( $attributes['cardImageUrl'] ) ? trim( (string) $attributes['cardImageUrl'] ) : '';
$card_img_alt_override  = isset( $attributes['cardImageAlt'] ) ? (string) $attributes['cardImageAlt'] : '';

$case_post_id = 0;
if ( is_singular() ) {
	$queried_id = (int) get_queried_object_id();
	if ( $queried_id > 0 ) {
		$case_post_id = $queried_id;
	}
}

$case_post_type = '';
if ( $case_post_id > 0 ) {
	$case_post_type = (string) get_post_type( $case_post_id );
}
$is_case_single = ( defined( 'MWM_CASO_EXITO_POST_TYPE' ) && MWM_CASO_EXITO_POST_TYPE === $case_post_type ) || 'caso_exito' === $case_post_type;

$case_pretitle = '';
$case_title    = '';
$case_video    = '';
$case_img_url  = '';
$case_img_alt  = '';

if ( $is_case_single && $case_post_id > 0 ) {
	$pretitle_meta_key = defined( 'MWM_CASO_EXITO_PRE_TITULO_META' ) ? MWM_CASO_EXITO_PRE_TITULO_META : 'mwm_caso_exito_pre_titulo';
	$video_meta_key    = defined( 'MWM_CASO_EXITO_VIDEO_URL_META' ) ? MWM_CASO_EXITO_VIDEO_URL_META : 'mwm_caso_exito_video_url';

	$case_pretitle = trim( (string) get_post_meta( $case_post_id, $pretitle_meta_key, true ) );
	$case_title    = trim( (string) get_the_title( $case_post_id ) );
	$case_video    = trim( (string) get_post_meta( $case_post_id, $video_meta_key, true ) );

	$case_thumb_id = (int) get_post_thumbnail_id( $case_post_id );
	if ( $case_thumb_id > 0 ) {
		$case_img_url = (string) wp_get_attachment_image_url( $case_thumb_id, 'large' );
		$case_img_alt = (string) get_post_meta( $case_thumb_id, '_wp_attachment_image_alt', true );
	}
}

$card_pretitle = '' !== $card_pretitle_override ? $card_pretitle_override : $case_pretitle;
$card_title    = '' !== $card_title_override ? $card_title_override : $case_title;
$card_video    = '' !== $card_video_override ? $card_video_override : $case_video;
$card_play     = '' !== $card_play_override ? $card_play_override : 'Play';
$card_img_url  = '' !== $card_img_url_override ? $card_img_url_override : $case_img_url;
$card_img_alt  = '' !== trim( $card_img_alt_override ) ? $card_img_alt_override : $case_img_alt;

if ( '' === $card_pretitle ) {
	$card_pretitle = 'Matias y Francisco';
}
if ( '' === $card_title ) {
	$card_title = 'Balinot tech consulting';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-caso-exito-content-01 w-full bg-protagonista py-[120px]',
	)
);
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-20">
		<div class="mwm-caso-exito-content-01__retos grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="mwm-caso-exito-content-01__retos-title-wrap">
				<?php if ( '' !== trim( wp_strip_all_tags( $retos_title ) ) ) : ?>
					<h2 class="mwm-caso-exito-content-01__section-title text-[32px] font-heading leading-[1.2] text-white">
						<?php echo wp_kses_post( $retos_title ); ?>
					</h2>
				<?php endif; ?>
			</div>
			<div class="mwm-caso-exito-content-01__retos-texts flex flex-col gap-12 pt-12">
				<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
					<div class="min-h-[171px]">
						<?php if ( '' !== trim( wp_strip_all_tags( $retos_text_left ) ) ) : ?>
							<p class="text-base leading-[1.2] text-neutral-light">
								<?php echo wp_kses_post( $retos_text_left ); ?>
							</p>
						<?php endif; ?>
					</div>
					<div class="hidden min-h-[171px] md:block" aria-hidden="true"></div>
				</div>
				<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
					<div class="hidden min-h-[171px] md:block" aria-hidden="true"></div>
					<div class="min-h-[171px]">
						<?php if ( '' !== trim( wp_strip_all_tags( $retos_text_right ) ) ) : ?>
							<p class="text-base leading-[1.2] text-neutral-light">
								<?php echo wp_kses_post( $retos_text_right ); ?>
							</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="mwm-caso-exito-content-01__ejecucion grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="mwm-caso-exito-content-01__ejecucion-title-wrap">
				<?php if ( '' !== trim( wp_strip_all_tags( $ejecucion_title ) ) ) : ?>
					<h2 class="mwm-caso-exito-content-01__section-title text-[32px] font-heading leading-[1.2] text-white">
						<?php echo wp_kses_post( $ejecucion_title ); ?>
					</h2>
				<?php endif; ?>
			</div>
			<div class="mwm-caso-exito-content-01__ejecucion-items flex flex-col gap-12 pt-12">
				<?php foreach ( $ejecucion_items as $item ) : ?>
					<?php
					$step = isset( $item['step'] ) ? (string) $item['step'] : '';
					$text = isset( $item['text'] ) ? (string) $item['text'] : '';
					?>
					<div class="mwm-caso-exito-content-01__ejecucion-item flex flex-col gap-6">
						<?php if ( '' !== trim( wp_strip_all_tags( $step ) ) ) : ?>
							<p class="text-2xl font-heading leading-[1.2] text-acento">
								<?php echo wp_kses_post( $step ); ?>
							</p>
						<?php endif; ?>
						<?php if ( '' !== trim( wp_strip_all_tags( $text ) ) ) : ?>
							<p class="text-base leading-[1.2] text-neutral-light">
								<?php echo wp_kses_post( $text ); ?>
							</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="mwm-caso-exito-content-01__resultados flex flex-col gap-10">
			<?php if ( '' !== trim( wp_strip_all_tags( $resultados_title ) ) ) : ?>
				<h2 class="mwm-caso-exito-content-01__section-title text-[32px] font-heading leading-[1.2] text-acento">
					<?php echo wp_kses_post( $resultados_title ); ?>
				</h2>
			<?php endif; ?>
			<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
				<?php foreach ( $resultado_items as $item ) : ?>
					<?php
					$value = isset( $item['value'] ) ? (string) $item['value'] : '';
					$label = isset( $item['label'] ) ? (string) $item['label'] : '';
					?>
					<div class="mwm-caso-exito-content-01__resultado-item flex flex-col gap-6">
						<?php if ( '' !== trim( wp_strip_all_tags( $value ) ) ) : ?>
							<p class="text-5xl leading-none text-white">
								<?php echo wp_kses_post( $value ); ?>
							</p>
						<?php endif; ?>
						<?php if ( '' !== trim( wp_strip_all_tags( $label ) ) ) : ?>
							<p class="text-xl leading-[1.2] text-neutral-light">
								<?php echo wp_kses_post( $label ); ?>
							</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="mwm-caso-exito-content-01__card mwm-card-caso is-active relative flex min-h-[300px] overflow-hidden lg:min-h-[408px]">
			<div class="mwm-card-caso__bg" aria-hidden="true">
				<?php if ( '' !== $card_img_url ) : ?>
					<div class="mwm-card-caso__bg-blur">
						<img src="<?php echo esc_url( $card_img_url ); ?>" alt="<?php echo esc_attr( $card_img_alt ); ?>" class="mwm-card-caso__img h-full w-full object-cover" loading="lazy" decoding="async" />
					</div>
				<?php else : ?>
					<div class="mwm-card-caso__bg-fallback bg-protagonista"></div>
				<?php endif; ?>
			</div>
			<div class="mwm-card-caso__panel relative z-10 flex min-h-0 flex-1 flex-col p-5">
				<div class="flex min-h-0 flex-1 flex-col gap-3">
					<p class="mwm-card-caso__line-a w-full max-w-full text-[16px] uppercase leading-[1.2] text-neutral-light">
						<?php echo esc_html( $card_pretitle ); ?>
					</p>
					<h3 class="mwm-card-caso__line-b w-full max-w-full text-[16px] uppercase leading-[1.2] text-white">
						<?php echo esc_html( $card_title ); ?>
					</h3>
				</div>
				<div class="mwm-card-caso__actions flex min-h-0 flex-1 items-end gap-5">
					<?php if ( function_exists( 'mwm_render_button' ) && '' !== $card_video ) : ?>
						<?php
						mwm_render_button(
							array(
								'text'            => $card_play,
								'url'             => $card_video,
								'variant'         => 'play-outline',
								'icon'            => 'play',
								'class'           => 'whitespace-nowrap mwm-card-caso__video-trigger',
								'icon_position'   => 'before',
								'size'            => 'md',
								'data_attributes' => array(
									'fancybox' => 'caso-video',
									'caption'  => $card_title,
								),
							)
						);
						?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
