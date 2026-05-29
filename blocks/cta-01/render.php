<?php
/**
 * Server-side rendering for `zenyx/cta-01`.
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
$theme          = isset( $attributes['theme'] ) ? (string) $attributes['theme'] : 'oscuro';
$button_text    = isset( $attributes['buttonText'] ) ? (string) $attributes['buttonText'] : '';
$button_url_raw = isset( $attributes['buttonUrl'] ) ? (string) $attributes['buttonUrl'] : '';
$opens_new      = ! empty( $attributes['opensInNewTab'] );
$hide_arrow_on_mobile = ! empty( $attributes['hideArrowOnMobile'] );
$is_light_theme = 'claro' === $theme;

$button_url = '' !== trim( $button_url_raw ) ? esc_url( $button_url_raw ) : '';

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => sprintf(
			'mwm-cta-01 py-16 lg:py-[70px] w-full %s',
			$is_light_theme ? 'bg-neutral-light' : 'bg-protagonista'
		),
		'data-mwm-header-hide-boundary' => '1',
	)
);

$cta_classes = 'mwm-cta-01__cta';
if ( $hide_arrow_on_mobile ) {
	$cta_classes .= ' mwm-cta-01__cta--hide-icon-mobile';
}
?>

<section <?php echo $is_light_theme ? 'data-dark' : 'data-light'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-cta-01__shell relative overflow-hidden">
		<div class="mwm-max-1">
			<div class="mwm-cta-01__stage relative">
				<div class="mwm-cta-01__glow" aria-hidden="true"></div>
				<div class="mwm-cta-01__content relative z-10 mx-auto flex w-full max-w-full flex-col items-center justify-center gap-12 px-4 pt-16 pb-12 sm:px-5 md:pt-20 md:pb-14 lg:gap-[60px] lg:px-6 lg:pt-[100px] lg:pb-[60px]">
				<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
					<h2 class="mwm-cta-01__heading w-full max-w-[636px] text-center font-heading text-display-m leading-[1.2] text-protagonista">
						<?php echo wp_kses_post( $heading ); ?>
					</h2>
				<?php endif; ?>

				<?php if ( '' !== trim( wp_strip_all_tags( $description ) ) ) : ?>
					<p class="mwm-cta-01__description w-full max-w-[636px] text-center font-body text-base leading-[1.4] text-protagonista lg:text-[24px] lg:leading-[1.4]">
						<?php echo wp_kses_post( $description ); ?>
					</p>
				<?php endif; ?>

				<?php if ( '' !== trim( $button_text ) ) : ?>
					<?php if ( function_exists( 'mwm_render_button' ) ) : ?>
						<?php if ( '' !== $button_url ) : ?>
							<?php
							mwm_render_button(
								array(
									'text'          => $button_text,
									'url'           => $button_url,
									'variant'       => 'primary',
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
									'variant'       => 'primary',
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
					<?php endif; ?>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
