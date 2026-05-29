<?php
/**
 * Blog (home.php y single post): CTA desde Customizer.
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
	'mwm_blog_archive_cta',
	array(
		'heading'       => __( '¿Te gustaría aplicar esto con ayuda y no solo leerlo?', THEME_TEXT_DOMAIN ),
		'description'   => '',
		'theme'         => 'oscuro',
		'button_text'   => __( 'Descubre cómo podemos ayudarte', THEME_TEXT_DOMAIN ),
		'button_url'    => home_url( '/contacto/' ),
		'opens_new_tab' => false,
	)
);

if ( '' !== $html ) {
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render.
}
