<?php
/**
 * Server-side rendering for `zenyx/text-links-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_columns = array(
	array(
		'title'         => 'Programa de aceleración para agencias práctico',
		'buttonText'    => 'Programación',
		'buttonUrl'     => '',
		'opensInNewTab' => false,
		'buttonVariant' => 'primary',
	),
	array(
		'title'         => 'Eventos para<br>agencias ',
		'buttonText'    => 'Eventos',
		'buttonUrl'     => '',
		'opensInNewTab' => false,
		'buttonVariant' => 'light',
	),
	array(
		'title'         => 'Una comunidad de dueños de agencia real',
		'buttonText'    => 'Casos de éxito',
		'buttonUrl'     => '',
		'opensInNewTab' => false,
		'buttonVariant' => 'light',
	),
);

$main_heading    = isset( $attributes['mainHeading'] ) ? (string) $attributes['mainHeading'] : '';
$intro_label     = isset( $attributes['introLabel'] ) ? (string) $attributes['introLabel'] : '';
$intro_highlight = isset( $attributes['introHighlight'] ) ? (string) $attributes['introHighlight'] : '';

$raw_columns = isset( $attributes['columns'] ) && is_array( $attributes['columns'] ) ? $attributes['columns'] : array();
$columns     = array();

for ( $i = 0; $i < 3; $i++ ) {
	$col = isset( $raw_columns[ $i ] ) && is_array( $raw_columns[ $i ] ) ? $raw_columns[ $i ] : array();
	$def = $default_columns[ $i ];

	$variant = isset( $col['buttonVariant'] ) ? sanitize_html_class( (string) $col['buttonVariant'] ) : $def['buttonVariant'];
	if ( ! in_array( $variant, array( 'primary', 'light', 'dark', 'ghost' ), true ) ) {
		$variant = $def['buttonVariant'];
	}

	$columns[] = array(
		'title'         => isset( $col['title'] ) ? (string) $col['title'] : $def['title'],
		'buttonText'    => isset( $col['buttonText'] ) ? (string) $col['buttonText'] : $def['buttonText'],
		'buttonUrl'     => isset( $col['buttonUrl'] ) ? (string) $col['buttonUrl'] : '',
		'opensInNewTab' => ! empty( $col['opensInNewTab'] ),
		'buttonVariant' => $variant,
	);
}

$has_rich_text = function ( $html ) {
	return '' !== trim( wp_strip_all_tags( (string) $html ) );
};

$wrapper_attrs = get_block_wrapper_attributes(
	array(
		'class'     => 'mwm-text-links-01 relative isolate w-full overflow-hidden bg-protagonista',
		'data-dark' => '',
	)
);

$grad_id = 'mwm_tl01_' . wp_unique_id();
?>

<section data-light <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-text-links-01__decor pointer-events-none absolute inset-0 z-0 overflow-hidden" aria-hidden="true">
		<div class="mwm-text-links-01__decor-inner flex h-full min-h-0 w-full items-end justify-center">
			<svg
				class="mwm-text-links-01__decor-svg"
				width="958"
				height="768"
				viewBox="0 0 958 768"
				fill="none"
				xmlns="http://www.w3.org/2000/svg"
				focusable="false"
				preserveAspectRatio="xMidYMax meet"
			>
				<g>
					<path
						d="M205.345 639.668L570.286 640.513V768H0V639.668L182.328 451.738H387.659L205.345 639.668ZM778.016 136.873V262.288L594.227 451.738H388.881L572.685 262.288H206.552L206.18 136.873H778.016ZM957.856 75.1104L899.578 135.476H826.477V0H957.856V75.1104Z"
						fill="url(#<?php echo esc_attr( $grad_id ); ?>)"
					/>
				</g>
				<defs>
					<linearGradient id="<?php echo esc_attr( $grad_id ); ?>" x1="478.928" y1="0" x2="478.928" y2="768" gradientUnits="userSpaceOnUse">
						<stop offset="0" stop-color="#073549"></stop>
						<stop offset="0.7" stop-color="#073549"></stop>
						<stop offset="1" stop-color="#073549" stop-opacity="0"></stop>
					</linearGradient>
				</defs>
			</svg>
		</div>
	</div>

	<div class="relative z-10 mwm-max-1">
		<div class="mwm-text-links-01__inner py-16 lg:py-[120px]">
			<?php if ( $has_rich_text( $main_heading ) ) : ?>
				<h2 class="mwm-text-links-01__heading font-heading text-4xl font-normal leading-[1.2] text-neutral-light lg:text-[40px]">
					<?php echo wp_kses_post( $main_heading ); ?>
				</h2>
			<?php endif; ?>

			<div class="mwm-text-links-01__intro mt-10 flex flex-col gap-3 lg:mt-12">
				<?php if ( $has_rich_text( $intro_label ) ) : ?>
					<p class="mwm-text-links-01__intro-label text-base font-medium leading-[1.2] text-white">
						<?php echo wp_kses_post( $intro_label ); ?>
					</p>
				<?php endif; ?>
				<?php if ( $has_rich_text( $intro_highlight ) ) : ?>
					<p class="mwm-text-links-01__intro-highlight text-xl leading-[1.2] text-acento lg:text-[20px] max-w-[306px]">
						<?php echo wp_kses_post( $intro_highlight ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="mwm-text-links-01__grid mt-10 grid grid-cols-1 gap-6 md:grid-cols-3 md:gap-6 lg:mt-[140px]">
				<?php
				foreach ( $columns as $col ) :
					$url = $col['buttonUrl'] ? esc_url( $col['buttonUrl'] ) : '';
					?>
					<div class="mwm-text-links-01__col flex flex-col gap-6 lg:max-w-[306px]">
						<?php if ( $has_rich_text( $col['title'] ) ) : ?>
							<h3 class="mwm-text-links-01__col-title font-body text-2xl font-medium leading-[1.2] text-white">
								<?php echo wp_kses_post( $col['title'] ); ?>
							</h3>
						<?php endif; ?>
						<?php
						if ( '' !== trim( (string) $col['buttonText'] ) ) {
							$target = '';
							$rel    = '';
							if ( $col['opensInNewTab'] && $url ) {
								$target = '_blank';
								$rel    = 'noopener noreferrer';
							}

							mwm_render_button(
								array(
									'text'          => $col['buttonText'],
									'url'           => $url ? $url : '#',
									'variant'       => $col['buttonVariant'],
									'icon'          => 'arrow-right',
									'icon_position' => 'after',
									'size'          => 'md',
									'target'        => $target,
									'rel'           => $rel,
									'class'         => 'mwm-text-links-01__cta self-start',
									'as'            => $url ? 'a' : 'button',
									'disabled'      => ! $url,
									'aria_disabled' => ! $url,
								)
							);
						}
						?>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</div>
</section>
