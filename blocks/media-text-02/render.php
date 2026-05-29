<?php
/**
 * Server-side rendering for `zenyx/media-text-02`.
 *
 * Cada fila (slide) tiene un único bloque de texto + 1 o 2 medios en columnas.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normaliza solo datos de archivo (sin textos de panel; van a nivel de fila/slide).
 *
 * @param array $item Raw item.
 * @return array
 */
if ( ! function_exists( 'mwm_media_text_02_normalize_item' ) ) {
	function mwm_media_text_02_normalize_item( array $item ) {
		$defaults = array(
			'mediaType'     => 'image',
			'mediaImageId'  => 0,
			'mediaImageUrl' => '',
			'mediaImageAlt' => '',
			'mediaVideoId'  => 0,
			'mediaVideoUrl' => '',
		);
		$merged = array_merge( $defaults, $item );

		$media_type = isset( $merged['mediaType'] ) ? (string) $merged['mediaType'] : 'image';
		$media_type = in_array( $media_type, array( 'image', 'video' ), true ) ? $media_type : 'image';

		$image_id  = isset( $merged['mediaImageId'] ) ? (int) $merged['mediaImageId'] : 0;
		$image_url = isset( $merged['mediaImageUrl'] ) ? (string) $merged['mediaImageUrl'] : '';
		$image_alt = isset( $merged['mediaImageAlt'] ) ? (string) $merged['mediaImageAlt'] : '';
		$video_id  = isset( $merged['mediaVideoId'] ) ? (int) $merged['mediaVideoId'] : 0;
		$video_url = isset( $merged['mediaVideoUrl'] ) ? (string) $merged['mediaVideoUrl'] : '';

		if ( '' === $image_url && $image_id > 0 ) {
			$image_url = (string) wp_get_attachment_image_url( $image_id, 'full' );
		}
		if ( '' === $image_alt && $image_id > 0 ) {
			$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		}
		if ( '' === $video_url && $video_id > 0 ) {
			$video_url = (string) wp_get_attachment_url( $video_id );
		}

		return array(
			'mediaType' => $media_type,
			'imageUrl'  => $image_url,
			'imageAlt'  => $image_alt,
			'videoUrl'  => $video_url,
		);
	}
}

/**
 * Extrae textos de panel de una fila (atributos de fila o migración desde el primer ítem).
 *
 * @param array $row Raw row.
 * @return array{ panelQuote: string, panelAuthorName: string, panelAuthorRole: string }
 */
if ( ! function_exists( 'mwm_media_text_02_row_panel_fields' ) ) {
	function mwm_media_text_02_row_panel_fields( array $row ) {
		$pq = isset( $row['panelQuote'] ) ? (string) $row['panelQuote'] : '';
		$an = isset( $row['panelAuthorName'] ) ? (string) $row['panelAuthorName'] : '';
		$ar = isset( $row['panelAuthorRole'] ) ? (string) $row['panelAuthorRole'] : '';

		$items = isset( $row['items'] ) && is_array( $row['items'] ) ? $row['items'] : array();
		$first = isset( $items[0] ) && is_array( $items[0] ) ? $items[0] : array();

		if ( '' === trim( wp_strip_all_tags( $pq ) ) && isset( $first['panelQuote'] ) ) {
			$pq = (string) $first['panelQuote'];
		}
		if ( '' === trim( wp_strip_all_tags( $an ) ) && isset( $first['panelAuthorName'] ) ) {
			$an = (string) $first['panelAuthorName'];
		}
		if ( '' === trim( wp_strip_all_tags( $ar ) ) && isset( $first['panelAuthorRole'] ) ) {
			$ar = (string) $first['panelAuthorRole'];
		}

		return array(
			'panelQuote'      => $pq,
			'panelAuthorName' => $an,
			'panelAuthorRole' => $ar,
		);
	}
}

/**
 * Aplica cita/autor globales legacy al primer slide si sigue vacío.
 *
 * @param array $panel     Panel fields.
 * @param array $block_attrs Block attributes.
 * @param int   $slide_idx Índice de slide (0 = primero).
 * @return array
 */
if ( ! function_exists( 'mwm_media_text_02_apply_legacy_row_panel' ) ) {
	function mwm_media_text_02_apply_legacy_row_panel( array $panel, array $block_attrs, $slide_idx ) {
		if ( 0 !== (int) $slide_idx ) {
			return $panel;
		}
		if ( '' === trim( wp_strip_all_tags( $panel['panelQuote'] ) ) && isset( $block_attrs['quote'] ) ) {
			$panel['panelQuote'] = (string) $block_attrs['quote'];
		}
		if ( '' === trim( wp_strip_all_tags( $panel['panelAuthorName'] ) ) && isset( $block_attrs['authorName'] ) ) {
			$panel['panelAuthorName'] = (string) $block_attrs['authorName'];
		}
		if ( '' === trim( wp_strip_all_tags( $panel['panelAuthorRole'] ) ) && isset( $block_attrs['authorRole'] ) ) {
			$panel['panelAuthorRole'] = (string) $block_attrs['authorRole'];
		}
		return $panel;
	}
}

/**
 * Convierte la cita (texto plano o HTML del inspector) en HTML seguro: párrafos con wpautop o un <p> si no hay bloque.
 *
 * @param string $raw Raw desde atributo.
 * @return string HTML seguro.
 */
if ( ! function_exists( 'mwm_media_text_02_quote_to_html' ) ) {
	function mwm_media_text_02_quote_to_html( $raw ) {
		$raw = trim( (string) $raw );
		if ( '' === $raw ) {
			return '';
		}

		// Empieza por marca: aplicar kses; si no es un bloque reconocido, envolver en <p>.
		if ( preg_match( '/^\s*</', $raw ) ) {
			$html = wp_kses_post( $raw );
			if ( '' === trim( wp_strip_all_tags( $html ) ) ) {
				return '';
			}
			if ( ! preg_match( '/^\s*<(p|div|blockquote|h[1-6]|ul|ol|figure)\b/i', $html ) ) {
				return '<p>' . $html . '</p>';
			}
			return $html;
		}

		// Texto plano: WordPress genera <p> y <br> como en el editor clásico.
		return wp_kses_post( wpautop( $raw, true ) );
	}
}

/**
 * Whether item has renderable media.
 *
 * @param array $normalized Output of mwm_media_text_02_normalize_item().
 */
if ( ! function_exists( 'mwm_media_text_02_item_has_media' ) ) {
	function mwm_media_text_02_item_has_media( array $normalized ) {
		if ( 'video' === $normalized['mediaType'] ) {
			return '' !== trim( $normalized['videoUrl'] );
		}
		return '' !== trim( $normalized['imageUrl'] );
	}
}

/**
 * Render one card (large or small variant).
 *
 * @param array  $normalized Normalized item (solo media).
 * @param string $size        'lg'|'sm'.
 * @param int    $card_index  Índice global de tarjeta (IO).
 * @param int    $slide_index Índice de slide (texto compartido si hay 2 columnas).
 */
if ( ! function_exists( 'mwm_media_text_02_render_card' ) ) {
	function mwm_media_text_02_render_card( array $normalized, $size = 'lg', $card_index = 0, $slide_index = 0 ) {
		$size = 'sm' === $size ? 'sm' : 'lg';
		if ( ! mwm_media_text_02_item_has_media( $normalized ) ) {
			return;
		}

		$card_class = 'mwm-media-text-02__card mwm-media-text-02__card--' . $size . ' mwm-media-text-02__card--faded';

		$media_type = $normalized['mediaType'];
		$image_url  = $normalized['imageUrl'];
		$image_alt  = $normalized['imageAlt'];
		$video_url  = $normalized['videoUrl'];
		?>
		<div
			class="<?php echo esc_attr( $card_class ); ?>"
			data-mwm-mt02-card
			data-mwm-mt02-index="<?php echo esc_attr( (string) (int) $card_index ); ?>"
			data-mwm-mt02-slide-index="<?php echo esc_attr( (string) (int) $slide_index ); ?>"
		>
			<?php if ( 'video' === $media_type ) : ?>
				<video
					class="mwm-media-text-02__card-media"
					autoplay
					muted
					loop
					playsinline
				>
					<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4" />
				</video>
			<?php else : ?>
				<img
					class="mwm-media-text-02__card-media"
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $image_alt ); ?>"
					loading="lazy"
					decoding="async"
				/>
			<?php endif; ?>
		</div>
		<?php
	}
}

$left_title = isset( $attributes['leftTitle'] ) ? (string) $attributes['leftTitle'] : '';

$raw_rows = isset( $attributes['rightRows'] ) && is_array( $attributes['rightRows'] ) ? array_values( $attributes['rightRows'] ) : array();

$rows_out   = array();
$slide_idx  = 0;

foreach ( $raw_rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$layout = isset( $row['layout'] ) ? (string) $row['layout'] : 'one';
	$layout = ( 'two' === $layout ) ? 'two' : 'one';
	$items  = isset( $row['items'] ) && is_array( $row['items'] ) ? array_values( $row['items'] ) : array();

	$panel = mwm_media_text_02_row_panel_fields( $row );
	$panel = mwm_media_text_02_apply_legacy_row_panel( $panel, $attributes, $slide_idx );

	$norms = array();
	foreach ( $items as $it ) {
		$n = mwm_media_text_02_normalize_item( is_array( $it ) ? $it : array() );
		if ( mwm_media_text_02_item_has_media( $n ) ) {
			$norms[] = $n;
		}
	}
	if ( empty( $norms ) ) {
		continue;
	}
	if ( 'two' === $layout && count( $norms ) === 1 ) {
		$layout = 'one';
	}
	$max_items = ( 'two' === $layout ) ? 2 : 1;
	$norms     = array_slice( $norms, 0, $max_items );

	$rows_out[] = array(
		'layout'          => $layout,
		'panelQuote'      => $panel['panelQuote'],
		'panelAuthorName' => $panel['panelAuthorName'],
		'panelAuthorRole' => $panel['panelAuthorRole'],
		'items'           => $norms,
		'slideIndex'      => $slide_idx,
	);
	++$slide_idx;
}

$panel_flat = array();
foreach ( $rows_out as $row ) {
	$panel_flat[] = array(
		'quote'      => mwm_media_text_02_quote_to_html( $row['panelQuote'] ),
		'authorName' => $row['panelAuthorName'],
		'authorRole' => $row['panelAuthorRole'],
	);
}

$has_left_title = '' !== trim( wp_strip_all_tags( $left_title ) );

$has_panel_text = false;
foreach ( $panel_flat as $p ) {
	if (
		'' !== trim( wp_strip_all_tags( $p['quote'] ) ) ||
		'' !== trim( wp_strip_all_tags( $p['authorName'] ) ) ||
		'' !== trim( wp_strip_all_tags( $p['authorRole'] ) )
	) {
		$has_panel_text = true;
		break;
	}
}

$has_right = ! empty( $rows_out );

if ( ! $has_left_title && ! $has_panel_text && ! $has_right ) {
	return;
}

$panel_json = wp_json_encode(
	$panel_flat,
	JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
);
if ( false === $panel_json ) {
	$panel_json = '[]';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'                       => 'mwm-media-text-02 w-full bg-neutral-light',
		'data-mwm-media-text-02-root' => '',
		'data-mwm-mt02-panel-json'    => esc_attr( $panel_json ),
	)
);

$first_panel = isset( $panel_flat[0] ) ? $panel_flat[0] : array(
	'quote'      => '',
	'authorName' => '',
	'authorRole' => '',
);

$has_quote_first       = '' !== trim( wp_strip_all_tags( $first_panel['quote'] ) );
$has_author_name_first = '' !== trim( wp_strip_all_tags( $first_panel['authorName'] ) );
$has_author_role_first = '' !== trim( wp_strip_all_tags( $first_panel['authorRole'] ) );

?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 px-4 pt-2 pb-[120px] sm:px-6 lg:px-8">
		<div class="mwm-media-text-02__grid flex flex-col gap-12 lg:flex-row lg:gap-6 xl:gap-8">
			<div class="mwm-media-text-02__left flex w-full min-w-0 flex-col items-start gap-8 self-stretch lg:min-h-screen lg:justify-center lg:gap-8 lg:sticky lg:max-w-[636px] lg:flex-1 lg:self-start">
				<div class="mwm-media-text-02__left-inner flex w-full min-h-0 flex-1 flex-col justify-between lg:min-h-0">
					<?php if ( $has_left_title ) : ?>
						<div class="mwm-media-text-02__title-wrap w-full max-w-[636px] shrink-0">
							<h2 class="mwm-media-text-02__title text-left font-heading text-[clamp(1.75rem,4vw,2.5rem)] leading-[1.2] text-inherit lg:text-[40px]">
								<?php echo wp_kses_post( $left_title ); ?>
							</h2>
						</div>
					<?php endif; ?>

					<div class="mwm-media-text-02__left-body relative hidden flex-1 flex-col justify-end self-stretch overflow-hidden p-6 lg:flex w-full max-w-[636px] shrink-0">
						<?php require __DIR__ . '/left-deco.php'; ?>

						<?php if ( ! empty( $panel_flat ) ) : ?>
						<div
							class="mwm-media-text-02__text-stage relative z-10 flex min-h-0 w-full flex-1 flex-col justify-end gap-9 pb-4 lg:pb-5"
							data-mwm-mt02-text-stage
						>
							<div
								class="mwm-media-text-02__quote w-full max-w-[588px] text-left text-[24px] leading-[1.35] text-inherit [&_p]:m-0 [&_p+p]:mt-4"
								data-mwm-mt02-quote
							>
								<?php if ( $has_quote_first ) : ?>
									<?php echo wp_kses_post( $first_panel['quote'] ); ?>
								<?php endif; ?>
							</div>

							<div class="mwm-media-text-02__author flex w-full max-w-[306px] flex-col gap-2" data-mwm-mt02-author>
								<p class="mwm-media-text-02__author-name m-0 text-left text-[20px] leading-snug text-inherit" data-mwm-mt02-author-name>
									<?php echo $has_author_name_first ? wp_kses_post( $first_panel['authorName'] ) : ''; ?>
								</p>
								<p class="mwm-media-text-02__author-role m-0 text-left text-[16px] leading-snug text-inherit" data-mwm-mt02-author-role>
									<?php echo $has_author_role_first ? wp_kses_post( $first_panel['authorRole'] ) : ''; ?>
								</p>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php if ( $has_right ) : ?>
				<div class="mwm-media-text-02__right flex min-w-0 flex-1 flex-col gap-8 lg:gap-0">
					<?php
					$render_card_index = 0;
					foreach ( $rows_out as $row ) :
						$layout       = $row['layout'];
						$items        = $row['items'];
						$slide_ix     = (int) $row['slideIndex'];
						$row_class    = 'mwm-media-text-02__row mwm-media-text-02__row--' . $layout;
						$row_quote    = isset( $row['panelQuote'] ) ? (string) $row['panelQuote'] : '';
						$row_author_n = isset( $row['panelAuthorName'] ) ? (string) $row['panelAuthorName'] : '';
						$row_author_r = isset( $row['panelAuthorRole'] ) ? (string) $row['panelAuthorRole'] : '';
						$has_row_quote = '' !== trim( wp_strip_all_tags( $row_quote ) );
						$has_row_name  = '' !== trim( wp_strip_all_tags( $row_author_n ) );
						$has_row_role  = '' !== trim( wp_strip_all_tags( $row_author_r ) );
						?>
						<div class="<?php echo esc_attr( $row_class ); ?>">
							<div class="mwm-media-text-02__row-inner">
								<?php
								foreach ( $items as $norm ) {
									$size = ( 'two' === $layout ) ? 'sm' : 'lg';
									mwm_media_text_02_render_card( $norm, $size, $render_card_index, $slide_ix );
									++$render_card_index;
								}
								?>
							</div>
							<?php if ( $has_row_quote || $has_row_name || $has_row_role ) : ?>
								<div class="mwm-media-text-02__mobile-slide-text mt-8 flex w-full max-w-[636px] flex-col gap-6 lg:hidden">
									<?php if ( $has_row_quote ) : ?>
										<div class="mwm-media-text-02__quote w-full text-left text-[clamp(1.125rem,4.2vw,1.375rem)] leading-[1.4] text-inherit [&_p]:m-0 [&_p+p]:mt-3">
											<?php echo wp_kses_post( mwm_media_text_02_quote_to_html( $row_quote ) ); ?>
										</div>
									<?php endif; ?>
									<?php if ( $has_row_name || $has_row_role ) : ?>
										<div class="mwm-media-text-02__author flex w-full max-w-[306px] flex-col gap-2">
											<?php if ( $has_row_name ) : ?>
												<p class="mwm-media-text-02__author-name m-0 text-left text-[clamp(1rem,3.5vw,1.125rem)] leading-snug text-inherit">
													<?php echo wp_kses_post( $row_author_n ); ?>
												</p>
											<?php endif; ?>
											<?php if ( $has_row_role ) : ?>
												<p class="mwm-media-text-02__author-role m-0 text-left text-[clamp(0.875rem,3vw,1rem)] leading-snug text-inherit">
													<?php echo wp_kses_post( $row_author_r ); ?>
												</p>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
