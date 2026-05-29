<?php
/**
 * Eventos archive: CTA (Customizer).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mwm_render_cta_from_options' ) ) {
	return;
}

$html = mwm_render_cta_from_options(
	'mwm_eventos_archive_cta',
	array(
		'heading'       => __( 'Apúntate al próximo evento', THEME_TEXT_DOMAIN ),
		'description'   => '',
		'theme'         => 'claro',
		'button_text'   => __( 'Escribirnos es el primer paso', THEME_TEXT_DOMAIN ),
		'button_url'    => '',
		'opens_new_tab' => true,
	)
);

if ( '' !== $html ) {
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render.
}
