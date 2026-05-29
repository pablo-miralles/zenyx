<?php
/**
 * Server-side rendering for `zenyx/how-it-works-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading        = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$intro          = isset( $attributes['intro'] ) ? (string) $attributes['intro'] : '';
$button_text    = isset( $attributes['buttonText'] ) ? (string) $attributes['buttonText'] : '';
$button_url_raw = isset( $attributes['buttonUrl'] ) ? (string) $attributes['buttonUrl'] : '';
$opens_new      = ! empty( $attributes['opensInNewTab'] );

$button_url = '' !== trim( $button_url_raw ) ? esc_url( $button_url_raw ) : '';

$steps_raw = isset( $attributes['steps'] ) && is_array( $attributes['steps'] ) ? $attributes['steps'] : array();
$steps     = array();

foreach ( $steps_raw as $raw ) {
	if ( ! is_array( $raw ) ) {
		continue;
	}
	$steps[] = array(
		'label' => isset( $raw['label'] ) ? (string) $raw['label'] : '',
		'text'  => isset( $raw['text'] ) ? (string) $raw['text'] : '',
	);
}

$visible_steps = array();
foreach ( $steps as $step ) {
	$lab = isset( $step['label'] ) ? (string) $step['label'] : '';
	$txt = isset( $step['text'] ) ? (string) $step['text'] : '';
	if ( '' !== trim( wp_strip_all_tags( $lab ) ) || '' !== trim( wp_strip_all_tags( $txt ) ) ) {
		$visible_steps[] = $step;
	}
}

$has_heading = '' !== trim( wp_strip_all_tags( $heading ) );
$has_intro   = '' !== trim( wp_strip_all_tags( $intro ) );
$has_button  = '' !== trim( $button_text );
$has_steps   = count( $visible_steps ) > 0;

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-how-it-works-01 w-full overflow-hidden bg-neutral-light py-[120px]',
	)
);

$cta_classes = 'mwm-how-it-works-01__cta';
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-20 lg:gap-[80px]">
		<?php if ( $has_heading || $has_intro ) : ?>
			<header class="mwm-how-it-works-01__header flex max-w-[634px] flex-col gap-6">
				<?php if ( $has_heading ) : ?>
					<h2 class="mwm-how-it-works-01__heading text-[1.75rem] font-heading leading-[1.2] text-inherit md:text-3xl lg:text-[40px]">
						<?php echo wp_kses_post( $heading ); ?>
					</h2>
				<?php endif; ?>
				<?php if ( $has_intro ) : ?>
					<div class="mwm-how-it-works-01__intro pr-0 text-left text-base leading-[1.2] text-inherit lg:pr-[110px] lg:text-xl lg:leading-[1.2]">
						<?php echo wp_kses_post( $intro ); ?>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $has_steps || $has_button ) : ?>
			<div class="mwm-how-it-works-01__row flex flex-col gap-10 lg:flex-row lg:items-end lg:gap-6">
				<?php foreach ( $visible_steps as $step ) : ?>
					<?php
					$lab = isset( $step['label'] ) ? (string) $step['label'] : '';
					$txt = isset( $step['text'] ) ? (string) $step['text'] : '';
					?>
					<div class="mwm-how-it-works-01__step flex min-w-0 flex-1 flex-col gap-6 pt-3">
						<div class="mwm-how-it-works-01__step-head flex items-center gap-6 self-stretch">
							<?php if ( '' !== trim( wp_strip_all_tags( $lab ) ) ) : ?>
								<span class="mwm-how-it-works-01__step-label shrink-0 text-left font-heading text-base text-acento">
									<?php echo wp_kses_post( $lab ); ?>
								</span>
							<?php endif; ?>
							<span class="mwm-how-it-works-01__step-line min-h-px min-w-0 flex-1 bg-acento" aria-hidden="true"></span>
						</div>
						<?php if ( '' !== trim( wp_strip_all_tags( $txt ) ) ) : ?>
							<div class="mwm-how-it-works-01__step-text text-left text-base leading-[1.2] text-inherit lg:text-xl lg:leading-[1.2]">
								<?php echo wp_kses_post( $txt ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

				<?php if ( $has_button ) : ?>
					<div class="mwm-how-it-works-01__cta-col flex min-w-0 flex-1 flex-col justify-start gap-2.5 self-stretch lg:min-h-0">
						<?php if ( function_exists( 'mwm_render_button' ) ) : ?>
							<?php if ( '' !== $button_url ) : ?>
								<?php
								mwm_render_button(
									array(
										'text'          => $button_text,
										'url'           => $button_url,
										'variant'       => 'primary',
										'icon'          => 'none',
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
										'variant'       => 'primary',
										'icon'          => 'none',
										'icon_position' => 'after',
										'size'          => 'md',
										'class'         => $cta_classes,
										'disabled'      => true,
										'aria_disabled' => true,
									)
								);
								?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
