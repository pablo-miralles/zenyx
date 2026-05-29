<?php
/**
 * Server-side rendering for `zenyx/slider-casos-01`.
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

$section_title = isset( $attributes['sectionTitle'] ) ? (string) $attributes['sectionTitle'] : '';
$lead_text     = isset( $attributes['leadText'] ) ? (string) $attributes['leadText'] : '';
$cta_text      = isset( $attributes['ctaText'] ) ? trim( (string) $attributes['ctaText'] ) : '';
$cta_url       = isset( $attributes['ctaUrl'] ) ? trim( (string) $attributes['ctaUrl'] ) : '';
$center_content = ! empty( $attributes['centerContent'] );

$source_mode = isset( $attributes['sourceMode'] ) ? (string) $attributes['sourceMode'] : 'manual';
if ( 'related' !== $source_mode && 'manual' !== $source_mode ) {
	$source_mode = 'manual';
}

$related_limit = isset( $attributes['relatedLimit'] ) ? (int) $attributes['relatedLimit'] : 8;
$related_limit = max( 1, min( 24, $related_limit ) );

$raw_manual = isset( $attributes['manualPostIds'] ) && is_array( $attributes['manualPostIds'] )
	? $attributes['manualPostIds']
	: array();

$post_ids = array();

$cpt = defined( 'MWM_CASO_EXITO_POST_TYPE' ) ? MWM_CASO_EXITO_POST_TYPE : 'caso_exito';

if ( 'manual' === $source_mode ) {
	foreach ( $raw_manual as $pid ) {
		$pid = absint( $pid );
		if ( $pid < 1 ) {
			continue;
		}
		$p = get_post( $pid );
		if ( ! $p || $cpt !== $p->post_type || 'publish' !== $p->post_status ) {
			continue;
		}
		$post_ids[] = $pid;
	}
} else {
	// Modo "relacionados": otros casos publicados excluyendo el actual (solo en single del CPT).
	// Si se añade taxonomía al CPT, se puede sustituir por tax_query compartida.
	if ( is_singular( $cpt ) ) {
		$current_id = (int) get_queried_object_id();
		$q          = new WP_Query(
			array(
				'post_type'      => $cpt,
				'post_status'    => 'publish',
				'posts_per_page' => $related_limit,
				'post__not_in'   => $current_id > 0 ? array( $current_id ) : array(),
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		if ( $q->have_posts() ) {
			$post_ids = array_map( 'absint', $q->posts );
		}
		wp_reset_postdata();
	}
}

$instance_id = wp_unique_id( 'mwm-slider-casos-' );

if ( '' === $cta_url ) {
	$archive = get_post_type_archive_link( $cpt );
	$cta_url = is_string( $archive ) ? $archive : '';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'wp-block-zenyx-slider-casos-01 mwm-slider-casos-01 w-full bg-neutral-light overflow-hidden',
	)
);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-dark>
	<div class="mwm-max-1 flex flex-col gap-12 px-[35px] py-16 lg:gap-20 lg:py-[120px]">
		<?php if ( '' !== trim( wp_strip_all_tags( $section_title ) ) ) : ?>
			<div class="mwm-slider-casos-01__heading w-full max-w-[636px] <?php echo $center_content ? 'mx-auto text-center' : ''; ?>">
				<h2 class="font-heading text-[2.5rem] font-normal leading-tight text-inherit lg:text-4xl <?php echo $center_content ? 'text-center' : ''; ?>">
					<?php echo wp_kses_post( $section_title ); ?>
				</h2>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $post_ids ) ) : ?>
			<div
				class="mwm-slider-casos-01__slider-wrap flex w-full flex-col"
				data-mwm-slider-casos-root="<?php echo esc_attr( $instance_id ); ?>"
			>
				<div
					class="mwm-slider-casos-01__viewport w-full overflow-visible"
					data-mwm-slider-casos-viewport
				>
					<div
						class="mwm-slider-casos-01__track will-change-transform transition-transform duration-500 ease-out"
						data-mwm-slider-casos-track
						style="transform: translate3d(0,0,0);"
					>
						<div
							class="mwm-card-caso-row flex w-full max-w-none flex-row flex-nowrap gap-6 lg:w-max"
							data-mwm-slider-casos-row
						>
							<?php
							foreach ( $post_ids as $index => $post_id ) {
								get_template_part(
									'template-parts/card-caso',
									null,
									array(
										'post_id'   => (int) $post_id,
										'is_active' => 0 === (int) $index,
										'heading_level' => 'h3',
									)
								);
							}
							?>
						</div>
					</div>
				</div>
				<div class="mwm-slider-casos-01__nav mt-6 flex gap-2 <?php echo $center_content ? 'justify-center' : 'justify-end'; ?>">
					<button
						type="button"
						class="mwm-slider-casos-01__btn-prev inline-flex h-10 w-10 cursor-pointer items-center justify-center text-protagonista transition-opacity hover:opacity-70 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-30"
						data-mwm-slider-casos-prev
						aria-label="<?php esc_attr_e( 'Caso anterior', 'zenyx' ); ?>"
					>
						<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M11.9518 7L4 15M4 15L11.9518 23M4 15H26" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
						</svg>
					</button>
					<button
						type="button"
						class="mwm-slider-casos-01__btn-next inline-flex h-10 w-10 cursor-pointer items-center justify-center text-protagonista transition-opacity hover:opacity-70 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-30"
						data-mwm-slider-casos-next
						aria-label="<?php esc_attr_e( 'Caso siguiente', 'zenyx' ); ?>"
					>
						<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M18.0482 23L26 15M26 15L18.0482 7M26 15L4 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
						</svg>
					</button>
				</div>
			</div>
		<?php elseif ( 'related' === $source_mode && ! is_singular( $cpt ) ) : ?>
			<p class="font-body text-body-m text-protagonista/70">
				<?php esc_html_e( 'El modo casos relacionados solo muestra entradas en la ficha de un caso de exito.', 'zenyx' ); ?>
			</p>
		<?php elseif ( empty( $post_ids ) && 'manual' === $source_mode ) : ?>
			<p class="font-body text-body-m text-protagonista/70">
				<?php esc_html_e( 'Selecciona al menos un caso en el panel del bloque.', 'zenyx' ); ?>
			</p>
		<?php elseif ( empty( $post_ids ) && 'related' === $source_mode && is_singular( $cpt ) ) : ?>
			<p class="font-body text-body-m text-protagonista/70">
				<?php esc_html_e( 'No hay otros casos publicados para mostrar.', 'zenyx' ); ?>
			</p>
		<?php endif; ?>

		<div class="mwm-slider-casos-01__footer flex w-full max-w-[636px] flex-col gap-6 <?php echo $center_content ? 'mx-auto items-center text-center' : ''; ?>">
			<?php if ( '' !== trim( wp_strip_all_tags( $lead_text ) ) ) : ?>
				<div class="mwm-slider-casos-01__lead font-body text-[20px] font-medium leading-snug text-inherit <?php echo $center_content ? 'text-center' : ''; ?>">
					<?php echo wp_kses_post( $lead_text ); ?>
				</div>
			<?php endif; ?>
			<?php if ( '' !== $cta_text && '' !== $cta_url && function_exists( 'mwm_render_button' ) ) : ?>
				<div class="mwm-slider-casos-01__cta <?php echo $center_content ? 'flex justify-center' : ''; ?>">
					<?php
					mwm_render_button(
						array(
							'text'         => $cta_text,
							'url'          => $cta_url,
							'variant'      => 'primary',
							'icon'         => 'arrow-right',
							'icon_position' => 'after',
							'size'         => 'md',
							'class'        => 'mwm-slider-casos-01__cta-btn',
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
