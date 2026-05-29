<?php
/**
 * Server-side rendering for `zenyx/stats-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading        = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$description    = isset( $attributes['description'] ) ? (string) $attributes['description'] : '';
$button_text    = isset( $attributes['buttonText'] ) ? (string) $attributes['buttonText'] : '';
$button_url_raw = isset( $attributes['buttonUrl'] ) ? (string) $attributes['buttonUrl'] : '';
$opens_new      = ! empty( $attributes['opensInNewTab'] );

$button_url = '' !== trim( $button_url_raw ) ? esc_url( $button_url_raw ) : '';

$default_stats = array(
	array(
		'value' => '+140',
		'label' => 'Más 140 agencias que han escalado',
	),
	array(
		'value' => '+35%',
		'label' => 'De rentabilidad media',
	),
	array(
		'value' => '+20h',
		'label' => 'De tus horas semanales libres de la operativa',
	),
);

$raw_stats = isset( $attributes['stats'] ) && is_array( $attributes['stats'] ) ? array_values( $attributes['stats'] ) : array();
$stats     = array();

for ( $i = 0; $i < 3; $i++ ) {
	$item = isset( $raw_stats[ $i ] ) && is_array( $raw_stats[ $i ] ) ? $raw_stats[ $i ] : array();
	$def  = $default_stats[ $i ];
	$stats[] = array(
		'value' => isset( $item['value'] ) ? (string) $item['value'] : (string) $def['value'],
		'label' => isset( $item['label'] ) ? (string) $item['label'] : (string) $def['label'],
	);
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-stats-01 w-full bg-neutral-light py-[120px]',
	)
);

$cta_classes = 'mwm-stats-01__cta';
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-16 lg:gap-20">
		<div class="mwm-stats-01__intro flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
			<div class="mwm-stats-01__intro-text flex min-w-0 flex-1 flex-col gap-6">
				<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
					<div class="mwm-stats-01__heading-wrap w-full max-w-[636px]">
						<h2 class="mwm-stats-01__heading text-left font-heading text-[2rem] leading-[1.2] text-protagonista md:text-[40px]">
							<?php echo wp_kses_post( $heading ); ?>
						</h2>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( wp_strip_all_tags( $description ) ) ) : ?>
					<div class="mwm-stats-01__description-wrap w-full max-w-[636px] text-base leading-normal text-protagonista">
						<div class="mwm-stats-01__description">
							<?php echo wp_kses_post( $description ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( '' !== trim( $button_text ) && function_exists( 'mwm_render_button' ) ) : ?>
				<div class="mwm-stats-01__cta-wrap max-w-[416px] w-full shrink-0 flex-col gap-2.5">
					<?php if ( '' !== $button_url ) : ?>
						<?php
						mwm_render_button(
							array(
								'text'          => $button_text,
								'url'           => $button_url,
								'variant'       => 'dark',
								'icon'          => 'arrow-right',
								'icon_position' => 'after',
								'size'          => 'md',
								'target'        => $opens_new ? '_blank' : '',
								'class'         => $cta_classes,
							)
						);
						?>
					<?php else : ?>
						<?php
						mwm_render_button(
							array(
								'text'          => $button_text,
								'as'            => 'button',
								'variant'       => 'dark',
								'icon'          => 'arrow-right',
								'icon_position' => 'after',
								'size'          => 'md',
								'class'         => $cta_classes,
								'disabled'      => true,
								'aria_disabled' => true,
							)
						);
						?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="mwm-stats-01__stats flex flex-col items-stretch gap-10">
			<div class="mwm-stats-01__stats-grid grid grid-cols-1 gap-6 md:grid-cols-3 md:gap-6">
				<?php foreach ( $stats as $stat ) : ?>
					<?php
					$val = $stat['value'];
					$lab = $stat['label'];
					?>
					<div class="mwm-stats-01__stat flex flex-1 gap-6 items-start">
						<?php if ( '' !== trim( wp_strip_all_tags( $val ) ) ) : ?>
							<div class="mwm-stats-01__stat-value-wrap flex min-w-0 flex-1 items-center justify-center md:justify-start">
								<div class="mwm-stats-01__stat-value w-full min-w-0 text-left font-body text-5xl leading-none text-acento md:text-[64px]">
									<?php echo wp_kses_post( $val ); ?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( '' !== trim( wp_strip_all_tags( $lab ) ) ) : ?>
							<div class="mwm-stats-01__stat-label-wrap flex min-w-0 flex-1 items-center justify-center pt-3 md:justify-start">
								<div class="mwm-stats-01__stat-label w-full min-w-0 text-left text-base font-medium leading-normal text-protagonista">
									<?php echo wp_kses_post( $lab ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
