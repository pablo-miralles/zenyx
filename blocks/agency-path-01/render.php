<?php
/**
 * Server-side rendering for `zenyx/agency-path-01`.
 *
 * Layout interior alineado a Figma (guide-00): frame-27 1076×475, group-2 (offset + alto),
 * grupo contenido 795×445.764, degradado translate(115,166), puntos/marcador en coords de frame.
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

$intro                  = isset( $attributes['intro'] ) ? (string) $attributes['intro'] : '';
$tagline                = isset( $attributes['tagline'] ) ? (string) $attributes['tagline'] : '';
$validation_title       = isset( $attributes['validationTitle'] ) ? (string) $attributes['validationTitle'] : '';
$level1_title           = isset( $attributes['level1Title'] ) ? (string) $attributes['level1Title'] : '';
$level1_fundamentos     = isset( $attributes['level1LabelFundamentos'] ) ? (string) $attributes['level1LabelFundamentos'] : '';
$level1_escalar         = isset( $attributes['level1LabelEscalar'] ) ? (string) $attributes['level1LabelEscalar'] : '';
$level2_title           = isset( $attributes['level2Title'] ) ? (string) $attributes['level2Title'] : '';
$level2_asentar         = isset( $attributes['level2LabelAsentar'] ) ? (string) $attributes['level2LabelAsentar'] : '';
$level2_escalar         = isset( $attributes['level2LabelEscalar'] ) ? (string) $attributes['level2LabelEscalar'] : '';
$level3_title           = isset( $attributes['level3Title'] ) ? (string) $attributes['level3Title'] : '';
$level3_libertad        = isset( $attributes['level3LabelLibertad'] ) ? (string) $attributes['level3LabelLibertad'] : '';
$marker_label           = isset( $attributes['markerLabel'] ) ? (string) $attributes['markerLabel'] : 'A';
$mobile_static_svg_id   = isset( $attributes['mobileStaticSvgId'] ) ? absint( $attributes['mobileStaticSvgId'] ) : 0;

$raw_validation = isset( $attributes['validationItems'] ) && is_array( $attributes['validationItems'] ) ? array_values( $attributes['validationItems'] ) : array();
$validation_items = array();
foreach ( $raw_validation as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$lab = isset( $row['label'] ) ? (string) $row['label'] : '';
	if ( '' === trim( $lab ) ) {
		continue;
	}
	$validation_items[] = $lab;
}

$degradado_src     = get_template_directory_uri() . '/assets/images/agency-path-01-degradado.png';
$is_block_preview  = defined( 'REST_REQUEST' ) && REST_REQUEST;
$section_classes   = $is_block_preview
	? 'mwm-agency-path-01 mwm-agency-path-01--block-preview flex w-full min-h-0 flex-col justify-center bg-neutral-light py-6 text-protagonista md:py-8'
	: 'mwm-agency-path-01 flex w-full md:min-h-[100dvh] flex-col justify-center bg-neutral-light py-8 md:py-12 lg:py-[120px] text-protagonista transition-[padding] duration-300';

$wrapper_args = array(
	'class'                   => $section_classes,
	'data-mwm-agency-path-01' => '1',
);
if ( $is_block_preview ) {
	$wrapper_args['data-agp01-editor-preview'] = '1';
}
$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1">
		<div
			class="mwm-agency-path-01__intro mb-10 px-2 md:mb-14 lg:mb-16"
			data-agp01-intro
		>
			<?php if ( '' !== trim( wp_strip_all_tags( $intro ) ) ) : ?>
				<p class="mwm-agency-path-01__intro-text mx-auto max-w-[1076px] text-center font-body text-xl leading-snug md:text-[28px] lg:text-[32px]">
					<?php echo wp_kses_post( $intro ); ?>
				</p>
			<?php endif; ?>
		</div>

		<div
			class="mwm-agency-path-01__mobile-static mb-6 w-full max-w-[min(100%,912px)] md:hidden [&_img]:block [&_img]:h-auto [&_img]:w-full [&_img]:max-w-full [&_svg]:block [&_svg]:h-auto [&_svg]:w-full [&_svg]:max-w-full"
			aria-hidden="true"
		>
			<?php
			$mobile_z_url = $mobile_static_svg_id > 0 ? wp_get_attachment_url( $mobile_static_svg_id ) : '';
			if ( $is_block_preview ) {
				// Vista previa del editor (REST): nada de SVG a tamaño completo; evita romper el lienzo.
				$prev_src = $mobile_z_url
					? $mobile_z_url
					: get_template_directory_uri() . '/assets/images/agency-path-01-mobile-z.svg';
				?>
				<div class="overflow-hidden rounded border border-dashed border-protagonista/20 bg-protagonista/5 px-2 py-3 text-center">
					<img
						src="<?php echo esc_url( $prev_src ); ?>"
						alt=""
						class="mx-auto h-auto max-h-[140px] w-full max-w-full object-contain"
						loading="lazy"
						decoding="async"
					/>
					<p class="m-0 mt-2 text-xs leading-tight text-protagonista/60">
						<?php
						esc_html_e( 'Solo móvil en el sitio; aquí es una miniatura.', 'zenyx' );
						?>
					</p>
				</div>
				<?php
			} elseif ( $mobile_z_url ) {
				$mime = get_post_mime_type( $mobile_static_svg_id );
				$path = get_attached_file( $mobile_static_svg_id );
				$is_svg = ( is_string( $mime ) && ( 'image/svg+xml' === $mime || 'image/svg' === $mime ) )
					|| ( is_string( $path ) && preg_match( '/\.svg$/i', $path ) );
				if ( $is_svg && $path && is_readable( $path ) ) {
					$svg_raw = file_get_contents( $path );
					if ( is_string( $svg_raw ) && '' !== $svg_raw ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- adjunto subido; mismo criterio que SVG del tema.
						echo $svg_raw;
					} else {
						echo '<img src="' . esc_url( $mobile_z_url ) . '" alt="" class="block h-auto w-full max-w-full" loading="lazy" decoding="async" />';
					}
				} else {
					echo '<img src="' . esc_url( $mobile_z_url ) . '" alt="" class="block h-auto w-full max-w-full" loading="lazy" decoding="async" />';
				}
			} else {
				$mobile_z = get_template_directory() . '/assets/images/agency-path-01-mobile-z.svg';
				if ( file_exists( $mobile_z ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG estático del tema.
					echo file_get_contents( $mobile_z );
				}
			}
			?>
		</div>

		<div class="mwm-agency-path-01__layout hidden w-full md:block">
			<div
				class="mwm-agency-path-01__scene relative mx-auto w-full max-w-[1076px]"
				data-agp01-diagram
			>
				<div
					class="mwm-agency-path-01__board relative w-full overflow-visible"
					data-agp01-board
					style="aspect-ratio:1076/475;"
				>
					<div
						class="mwm-agency-path-01__degradado pointer-events-none absolute z-0 overflow-hidden opacity-90 select-none [&_img]:h-full [&_img]:w-full [&_img]:max-w-none [&_img]:object-cover"
						data-agp01-degradado
						aria-hidden="true"
						style="left:-4%;top:0;width:59.1086%;aspect-ratio:636/542;"
					>
						<img src="<?php echo esc_url( $degradado_src ); ?>" alt="" width="636" height="542" loading="lazy" decoding="async" />
					</div>

					<div
						class="mwm-agency-path-01__group2 absolute left-0 right-0 z-1"
						data-agp01-group2
						style="top:6.9255%;height:93.8453%;"
					>
						<div
							class="mwm-agency-path-01__content absolute left-0 top-0 h-full"
							data-agp01-content
							style="width:73.9326%;"
						>
							<div
								class="mwm-agency-path-01__jaula-layer pointer-events-none absolute z-1 overflow-visible"
								style="left:0.245%;top:29.686%;width:88.27%;"
							>
								<?php
								// Grupo `z` (techo + base) respecto a `jaula` — guide-00/style.css (702.387×309.359 vs 483.709×353.877).
								$agp01_jaula_w = 702.387;
								$agp01_jaula_h = 309.359;
								$agp01_z_w     = 483.709;
								$agp01_z_h     = 353.877;
								$agp01_jaula_x = 1.947;
								$agp01_jaula_y = 132.332;
								$agp01_z_x     = 311.604;
								$agp01_z_y     = 88.331;
								$agp01_z_stack_left = ( ( $agp01_z_x - $agp01_jaula_x ) / $agp01_jaula_w ) * 100;
								$agp01_z_stack_top  = ( ( $agp01_z_y - $agp01_jaula_y ) / $agp01_jaula_h ) * 100;
								$agp01_z_stack_w    = ( $agp01_z_w / $agp01_jaula_w ) * 100;
								// Casa dentro del `z-stack`: fracciones de ancho/alto del propio stack + nudge en px (ajustar fracciones al retocar diseño).
								$agp01_house_top_frac   = 55.2283 / 100;
								$agp01_house_width_frac = 58.5379 / 100;
								$agp01_house_left_px    = -8;
								$agp01_house_top_px     = $agp01_house_top_frac * $agp01_z_h;
								$agp01_house_width_px   = $agp01_house_width_frac * $agp01_z_w;
								$agp01_house_top        = ( $agp01_house_top_px / $agp01_z_h ) * 100;
								$agp01_house_w          = ( $agp01_house_width_px / $agp01_z_w ) * 100;
								// Techo (outline/fill/accent): rectángulo de layout = fracción del `z-stack` (ajustar fracciones al retocar).
								$agp01_roof_left_frac   = 19.5264 / 100;
								$agp01_roof_top_frac    = 0;
								$agp01_roof_width_frac  = 76.4736 / 100;
								$agp01_roof_height_frac = 56.2283 / 100;
								$agp01_roof_left_px     = $agp01_roof_left_frac * $agp01_z_w;
								$agp01_roof_top_px      = $agp01_roof_top_frac * $agp01_z_h;
								$agp01_roof_width_px    = $agp01_roof_width_frac * $agp01_z_w;
								$agp01_roof_height_px   = $agp01_roof_height_frac * $agp01_z_h;
								$agp01_roof_left        = ( $agp01_roof_left_px / $agp01_z_w ) * 100;
								$agp01_roof_top         = ( $agp01_roof_top_px / $agp01_z_h ) * 100;
								$agp01_roof_w           = ( $agp01_roof_width_px / $agp01_z_w ) * 100;
								$agp01_roof_h           = ( $agp01_roof_height_px / $agp01_z_h ) * 100;
								// viewBox: margen izquierdo para el stroke del contorno (mitad del trazo quedaba fuera y se recortaba).
								$agp01_roof_vb_pad = 1;
								$agp01_roof_vb_x  = 104.125 - $agp01_roof_vb_pad;
								$agp01_roof_vb_y   = 0;
								$agp01_roof_vb_w   = ( 483.709 - 104.125 ) + $agp01_roof_vb_pad;
								$agp01_roof_vb_h   = 198.979;
								// Alinear `frame-extended` con `frame-simple` (delta Y entre líneas superiores del marco en el mismo viewBox).
								$agp01_frame_align_dy = 0.400391 - 16.1963;
								?>
								<div
									class="mwm-agency-path-01__z-stack pointer-events-none absolute z-0"
									style="<?php echo esc_attr( sprintf( 'left:%s%%;top:%s%%;width:%s%%;aspect-ratio:%s/%s;', round( $agp01_z_stack_left, 4 ), round( $agp01_z_stack_top, 4 ), round( $agp01_z_stack_w, 4 ), $agp01_z_w, $agp01_z_h ) ); ?>"
									aria-hidden="true"
								>
									<svg
										class="mwm-agency-path-01__roof-svg mwm-agency-path-01__roof-svg--house absolute block h-auto max-w-none"
										style="<?php echo esc_attr( sprintf( 'left:%dpx;top:%s%%;width:%s%%;', $agp01_house_left_px, round( $agp01_house_top, 4 ), round( $agp01_house_w, 4 ) ) ); ?>"
										viewBox="0 0 287.99 154.898"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										aria-hidden="true"
									>
										<path
											data-agp01-house-base
											d="M0 92.0485V154.898H287.99V92.4565L106.719 92.0485L196.387 0H92.0708L0 92.0485Z"
											fill="white"
										/>
									</svg>
									<svg
										class="mwm-agency-path-01__roof-svg mwm-agency-path-01__roof-svg--roof pointer-events-none absolute block max-w-none overflow-visible"
										style="<?php echo esc_attr( sprintf( 'left:%s%%;top:%s%%;width:%s%%;height:%s%%;', round( $agp01_roof_left, 4 ), round( $agp01_roof_top, 4 ), round( $agp01_roof_w, 4 ), round( $agp01_roof_h, 4 ) ) ); ?>"
										viewBox="<?php echo esc_attr( sprintf( '%s %s %s %s', round( $agp01_roof_vb_x, 3 ), round( $agp01_roof_vb_y, 3 ), round( $agp01_roof_vb_w, 3 ), round( $agp01_roof_vb_h, 3 ) ) ); ?>"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										preserveAspectRatio="xMidYMid meet"
										aria-hidden="true"
									>
										<path
											class="opacity-0"
											data-agp01-roof-outline
											d="M104.125 44.041L104.318 105.466L288.853 105.466L196.389 198.256M300.081 198.256L392.894 105.466"
											stroke="currentColor"
											stroke-width="0.6"
											stroke-linecap="round"
											stroke-linejoin="round"
											fill="none"
										/>
										<path
											class="opacity-0"
											data-agp01-roof-fill
											d="M392.894 44.7637H104.125L104.318 106.189H288.853L196.389 198.979H300.081L392.894 106.189V44.7637Z"
											fill="white"
										/>
										<path
											class="opacity-0"
											data-agp01-roof-accent
											transform="translate(0 -12)"
											d="M454.28 66.3571H417.359V0H483.709V36.7942L454.28 66.3571Z"
											fill="#FE7756"
										/>
									</svg>
								</div>
								<svg class="relative z-1 block w-full overflow-visible text-protagonista" viewBox="0 0 720 326" fill="none" xmlns="http://www.w3.org/2000/svg" overflow="visible" aria-hidden="true">
									<g class="mwm-agency-path-01__jaula-frames">
										<g data-agp01-frame-simple class="text-protagonista">
										<path d="M340.535 0.453125H702.388V278.527C702.388 295.66 688.482 309.566 671.349 309.566H309.496V31.492C309.496 14.3594 323.402 0.453125 340.535 0.453125Z" stroke="currentColor" stroke-width="0.6" stroke-miterlimit="10"/>
										<path d="M659.161 309.76H0.232422" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										<path data-agp01-midline-full d="M702.024 154.597L172.053 154.597L0.285156 154.597" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										<g class="opacity-0" data-agp01-midline-broken-simple>
											<path d="M702.024 154.597L252.553 154.597" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
											<path d="M0.285156 154.597L172.053 154.597" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										</g>
										<path d="M702.385 0.400391H0.232422" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										</g>
									<g data-agp01-frame-extended class="text-protagonista opacity-0" transform="<?php echo esc_attr( 'translate(0 ' . round( $agp01_frame_align_dy, 4 ) . ')' ); ?>">
										<path d="M340.535 16.249H702.388V294.323C702.388 311.456 688.482 325.362 671.349 325.362H309.496V47.2879C309.496 30.1553 323.402 16.249 340.535 16.249Z" stroke="currentColor" stroke-width="0.6" stroke-miterlimit="10"/>
										<path d="M659.161 325.556H0.232422" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										<path d="M702.024 170.393L252.553 170.393" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										<path d="M0.285156 170.393L172.053 170.393" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										<path d="M702.385 16.1963H0.232422" stroke="currentColor" stroke-width="0.8" stroke-miterlimit="10"/>
										<path d="M685.711 177.966L702.45 161.219L719.197 177.966" fill="#C1D9E4"/>
										<path d="M685.711 177.966L702.45 161.219L719.197 177.966" stroke="currentColor" stroke-width="0.6" stroke-miterlimit="10"/>
										<path d="M326.307 161.219L309.56 177.966L292.82 161.219" fill="#C1D9E4"/>
										<path d="M326.307 161.219L309.56 177.966L292.82 161.219" stroke="currentColor" stroke-width="0.6" stroke-miterlimit="10"/>
										<path d="M520.048 33.6983L503.309 16.9588L520.048 0.211914" stroke="currentColor" stroke-width="0.6" stroke-miterlimit="10"/>
									</g>
									</g>
								</svg>
							</div>

							<div
								class="mwm-agency-path-01__level mwm-agency-path-01__level--1 absolute z-2 flex items-end justify-between gap-2 pr-[8.4%]"
								data-agp01-level
								data-agp01-level-idx="1"
								style="left:0;top:76.47%;width:46%;"
							>
								<p class="max-w-[28%] shrink-0 text-2xl font-heading uppercase leading-tight whitespace-nowrap"><?php echo esc_html( $level1_title ); ?></p>
								<div class="flex min-w-0 flex-col items-start gap-1 text-lg leading-tight">
									<div class="relative min-h-[1.25em] w-full">
										<div class="flex flex-col gap-0.5" data-agp01-l1-fund-wrap>
											<?php
											echo '<span class="inline-flex items-center" data-agp01-l1-icon-h>';
											echo '<svg width="14" height="5" viewBox="0 0 14 5" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-protagonista" aria-hidden="true"><path d="M13.5059 2.27734H0.600844" stroke="currentColor" stroke-width="0.56" stroke-miterlimit="10"/><path d="M2.44791 4.54639L2.82617 4.14591L0.816232 2.27688L2.82617 0.407909L2.44791 0L0.000414572 2.27688L2.44791 4.54639Z" fill="currentColor"/></svg>';
											echo '</span>';
											echo '<span data-agp01-l1-fund>' . esc_html( $level1_fundamentos ) . '</span>';
											?>
										</div>
										<div class="absolute left-0 top-0 flex flex-col gap-0.5 opacity-0" data-agp01-l1-esc-wrap>
											<?php
											echo '<span class="inline-flex items-center" data-agp01-l1-icon-d>';
											echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-protagonista" aria-hidden="true"><path d="M0.196289 9.55416L9.31881 0.431641" stroke="currentColor" stroke-width="0.56" stroke-miterlimit="10"/><path d="M9.62023 3.33751L9.06398 3.32271L9.16778 0.578524L6.42364 0.682368L6.40137 0.118692L9.73888 0L9.62023 3.33751Z" fill="currentColor"/></svg>';
											echo '</span>';
											echo '<span data-agp01-l1-esc>' . esc_html( $level1_escalar ) . '</span>';
											?>
										</div>
									</div>
								</div>
							</div>

							<div
								class="mwm-agency-path-01__level mwm-agency-path-01__level--2 absolute z-2 opacity-0"
								data-agp01-level
								data-agp01-level-idx="2"
								style="left:0;top:42.73%;width:39.22%;"
							>
								<div class="relative flex items-start justify-between gap-2 pr-[8.4%]">
									<p class="w-[38%] text-2xl font-heading uppercase leading-tight"><?php echo esc_html( $level2_title ); ?></p>
									<div class="absolute flex flex-col items-start gap-0.5 pl-2 text-lg leading-tight" data-agp01-l2-asentar-wrap style="right:4.5rem;top:4.5625rem;">
										<svg width="14" height="5" viewBox="0 0 14 5" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-protagonista" aria-hidden="true">
											<path d="M0 2.27734H12.905" stroke="currentColor" stroke-width="0.56" stroke-miterlimit="10"/>
											<path d="M11.0579 4.54639L10.6797 4.14591L12.6896 2.27688L10.6797 0.407909L11.0579 0L13.5054 2.27688L11.0579 4.54639Z" fill="currentColor"/>
										</svg>
										<span><?php echo esc_html( $level2_asentar ); ?></span>
									</div>
									<div class="absolute flex flex-col items-start gap-0.5 text-lg leading-tight opacity-0" data-agp01-l2-esc-wrap style="right:4.5rem;top:0;">
										<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-protagonista" aria-hidden="true">
											<path d="M0.196289 9.55416L9.31881 0.431641" stroke="currentColor" stroke-width="0.56" stroke-miterlimit="10"/>
											<path d="M9.62023 3.33751L9.06398 3.32271L9.16778 0.578524L6.42364 0.682368L6.40137 0.118692L9.73888 0L9.62023 3.33751Z" fill="currentColor"/>
										</svg>
										<span><?php echo esc_html( $level2_escalar ); ?></span>
									</div>
								</div>
							</div>

							<div
								class="mwm-agency-path-01__level mwm-agency-path-01__level--3 absolute z-2 opacity-0"
								data-agp01-level
								data-agp01-level-idx="3"
								style="left:0;top:7%;width:38.96%;"
							>
								<div class="flex items-start justify-between gap-2 pr-[8.4%]">
									<p class="w-[38%] text-2xl font-heading uppercase leading-tight"><?php echo esc_html( $level3_title ); ?></p>
									<div class="flex flex-col items-start gap-0.5 text-lg leading-tight" style="position: absolute; top: 0; right: 3.75rem;">
										<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-protagonista" aria-hidden="true">
											<path d="M0.196289 9.55416L9.31881 0.431641" stroke="currentColor" stroke-width="0.56" stroke-miterlimit="10"/>
											<path d="M9.62023 3.33751L9.06398 3.32271L9.16778 0.578524L6.42364 0.682368L6.40137 0.118692L9.73888 0L9.62023 3.33751Z" fill="currentColor"/>
										</svg>
										<span><?php echo esc_html( $level3_libertad ); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="mwm-agency-path-01__dots pointer-events-none absolute inset-0 z-3" data-agp01-dots-wrap aria-hidden="true">
						<span class="mwm-agency-path-01__dot absolute h-2 w-2 rounded-full bg-current" data-agp01-dot="0"></span>
						<span class="mwm-agency-path-01__dot absolute h-2 w-2 rounded-full bg-current" data-agp01-dot="1"></span>
					</div>

					<div
						class="mwm-agency-path-01__marker-a pointer-events-none absolute z-3 flex items-center gap-1 text-protagonista"
						data-agp01-marker
						style="left: 56.6937%;top: 88.199%;"
					>
						<span class="relative inline-flex h-[14px] w-[8px] shrink-0 items-center justify-center md:h-[13px] md:w-[6.5px]" aria-hidden="true">
							<span class="absolute inset-0 flex items-center justify-center" data-agp01-marker-chevron>
								<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M6.96948 13.1603L0.524414 6.7078L6.96948 0.262695" stroke="currentColor" stroke-width="0.75" stroke-miterlimit="10"/>
								</svg>
							</span>
							<span class="absolute inset-0 flex items-center justify-center opacity-0" data-agp01-marker-chevron-final>
								<svg width="7" height="14" viewBox="0 0 6.445 12.898" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M6.44506 12.8976L0 6.44511L6.44506 0" stroke="currentColor" stroke-width="0.742" stroke-miterlimit="10"/>
								</svg>
							</span>
						</span>
						<span class="text-[2rem] font-heading leading-none md:text-[2.4rem]" data-agp01-marker-label><?php echo esc_html( $marker_label ); ?></span>
					</div>
				</div>

				<?php if ( count( $validation_items ) > 0 && '' !== trim( $validation_title ) ) : ?>
					<div
						class="mwm-agency-path-01__validation relative z-1 mt-8 min-h-[96px] px-4 opacity-0 lg:absolute lg:left-[70%] lg:top-[57%] lg:z-4 lg:mt-0 lg:w-[min(355.325px,33.03%)] lg:px-0"
						data-agp01-validation
					>
						<p class="absolute left-4 text-lg lg:left-0" style="top:50%; transform: translateY(-50%);"><?php echo esc_html( $validation_title ); ?></p>
						<ul class="ml-[28%] space-y-2 border-0 text-lg leading-tight list-disc lg:ml-[42.3%]" role="list">
							<?php foreach ( $validation_items as $vlabel ) : ?>
								<li><?php echo esc_html( $vlabel ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( '' !== trim( wp_strip_all_tags( $tagline ) ) ) : ?>
					<p
						class="mwm-agency-path-01__tagline relative z-1 mt-6 max-w-[320px] px-4 text-lg leading-snug opacity-0 lg:absolute lg:left-[80%] lg:top-[27%] lg:z-4 lg:mt-0 lg:max-w-[min(292.948px,27.23%)] lg:px-0"
						data-agp01-tagline
					>
						<?php echo wp_kses_post( $tagline ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
