<?php
/**
 * Casos de exito (archive y single): CTA desde Customizer.
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
	'mwm_casos_archive_cta',
	array(
		'heading'       => __( '¿Quieres ser el próximo caso de éxito? Escríbenos y te ayudamos', THEME_TEXT_DOMAIN ),
		'description'   => '',
		'theme'         => 'claro',
		'button_text'   => __( 'Descubre el programa Libertad', THEME_TEXT_DOMAIN ),
		'button_url'    => home_url( '/programa-libertad/' ),
		'opens_new_tab' => false,
	)
);

if ( '' !== $html ) {
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render.
}
