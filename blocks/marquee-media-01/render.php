<?php
/**
 * Server-side rendering for `zenyx/marquee-media-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_title   = isset( $attributes['sectionTitle'] ) ? (string) $attributes['sectionTitle'] : '';
$lead_text       = isset( $attributes['leadText'] ) ? (string) $attributes['leadText'] : '';
$center_content  = array_key_exists( 'centerContent', $attributes ) ? (bool) $attributes['centerContent'] : true;

$marquee_duration = isset( $attributes['marqueeDurationSeconds'] ) ? (int) $attributes['marqueeDurationSeconds'] : 40;
$marquee_duration = max( 10, min( 120, $marquee_duration ) );

$raw_ids = isset( $attributes['imageIds'] ) && is_array( $attributes['imageIds'] )
	? $attributes['imageIds']
	: array();

$image_ids = array();
foreach ( $raw_ids as $id ) {
	$id = absint( $id );
	if ( $id < 1 ) {
		continue;
	}
	$post = get_post( $id );
	if ( ! $post || 'attachment' !== $post->post_type ) {
		continue;
	}
	if ( ! wp_attachment_is_image( $id ) ) {
		continue;
	}
	$image_ids[] = $id;
}

$heading_align = $center_content ? 'text-center' : '';
$intro_align   = $center_content ? 'mx-auto text-center' : '';

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'wp-block-zenyx-marquee-media-01 mwm-marquee-media-01 w-full bg-neutral-light overflow-hidden',
	)
);

/**
 * Imprime una tira de slides (mismo orden de imagenes).
 *
 * @param array<int> $ids       IDs de adjuntos.
 * @param bool       $aria_hidden Si true, marca el contenedor para lectores.
 */
$mwm_marquee_render_strip = static function ( array $ids, $aria_hidden = false ) {
	/* Misma cadena de gap/pe en las dos tiras: el bucle es translateX(-50%). En móvil gap/pe menores = menos aire raro. */
	$strip_class = 'mwm-marquee-media-01__strip flex w-max max-w-none flex-shrink-0 flex-row flex-nowrap items-center justify-start gap-4 pe-4 sm:gap-5 sm:pe-5 md:gap-6 md:pe-6 lg:gap-8 lg:pe-8';
	?>
	<div
		class="<?php echo esc_attr( $strip_class ); ?>"
		<?php echo $aria_hidden ? ' aria-hidden="true"' : ''; ?>
	>
		<?php
		foreach ( $ids as $attachment_id ) {
			$attachment_id = (int) $attachment_id;
			$alt           = (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			?>
			<div class="mwm-marquee-media-01__slide min-w-0 shrink-0">
				<?php
				echo wp_get_attachment_image(
					$attachment_id,
					'full',
					false,
					array(
						'class'      => 'mwm-marquee-media-01__img pointer-events-none block select-none',
						'loading'    => 'lazy',
						'decoding'   => 'async',
						'alt'        => $aria_hidden ? '' : $alt,
						'draggable'  => 'false',
						// Evita 100vw en sizes: en carril cada imagen es un trozo; en móvil acotar a ~lo que pinta el CSS.
						'sizes'      => '(max-width: 767px) 70vw, (max-width: 1295px) 40vw, 400px',
					)
				);
				?>
			</div>
			<?php
		}
		?>
	</div>
	<?php
};
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-dark>
	<div class="mwm-max-1 flex flex-col gap-12 px-[35px] py-16 lg:gap-20 lg:py-[120px]">
		<div class="mwm-marquee-media-01__intro flex w-full flex-col gap-10 <?php echo $center_content ? 'items-center' : 'items-start'; ?>">
			<?php if ( '' !== trim( wp_strip_all_tags( $section_title ) ) ) : ?>
				<div class="mwm-marquee-media-01__heading w-full max-w-[648px] <?php echo esc_attr( $heading_align ); ?>">
					<h2 class="font-heading text-[2.5rem] font-normal leading-tight text-protagonista lg:text-4xl <?php echo esc_attr( $heading_align ); ?>">
						<?php echo wp_kses_post( $section_title ); ?>
					</h2>
				</div>
			<?php endif; ?>

			<?php if ( '' !== trim( wp_strip_all_tags( $lead_text ) ) ) : ?>
				<div class="mwm-marquee-media-01__lead w-full max-w-[416px] font-body text-[20px] font-medium leading-snug text-acento <?php echo esc_attr( $intro_align ); ?>">
					<?php echo wp_kses_post( $lead_text ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $image_ids ) ) : ?>
			<div
				class="mwm-marquee-media-01__marquee-wrap flex w-full flex-col"
				data-mwm-marquee-root
			>
				<div class="mwm-marquee-media-01__viewport w-full">
					<div
						class="mwm-marquee-media-01__marquee-inner flex w-max flex-nowrap items-center will-change-transform"
						style="<?php echo esc_attr( '--mwm-marquee-duration: ' . (string) $marquee_duration . 's;' ); ?>"
					>
						<?php $mwm_marquee_render_strip( $image_ids, false ); ?>
						<?php $mwm_marquee_render_strip( $image_ids, true ); ?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<p class="font-body text-body-m text-protagonista/70">
				<?php esc_html_e( 'Añade al menos una imagen en el panel del bloque.', 'zenyx' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>
