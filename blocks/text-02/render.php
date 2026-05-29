<?php
/**
 * Server-side rendering for `zenyx/text-02`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading    = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$paragraph1 = isset( $attributes['paragraph1'] ) ? (string) $attributes['paragraph1'] : '';
$paragraph2 = isset( $attributes['paragraph2'] ) ? (string) $attributes['paragraph2'] : '';

$has_heading    = '' !== trim( wp_strip_all_tags( $heading ) );
$has_paragraph1 = '' !== trim( wp_strip_all_tags( $paragraph1 ) );
$has_paragraph2 = '' !== trim( wp_strip_all_tags( $paragraph2 ) );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-text-02 w-full bg-protagonista py-[120px]',
	)
);
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col">
		<div class="flex w-full max-w-[636px] flex-col items-start gap-20">
			<?php if ( $has_heading ) : ?>
				<h2 class="mwm-text-02__heading w-full text-left text-[40px] font-heading leading-[1.2] text-neutral-light">
					<?php echo wp_kses_post( $heading ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $has_paragraph1 || $has_paragraph2 ) : ?>
				<div class="flex w-full flex-col gap-6">
					<?php if ( $has_paragraph1 ) : ?>
						<div class="mwm-text-02__paragraph w-full text-left text-xl leading-[1.2] text-neutral-light">
							<?php echo wp_kses_post( $paragraph1 ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $has_paragraph2 ) : ?>
						<div class="mwm-text-02__paragraph w-full text-left text-xl leading-[1.2] text-neutral-light">
							<?php echo wp_kses_post( $paragraph2 ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
