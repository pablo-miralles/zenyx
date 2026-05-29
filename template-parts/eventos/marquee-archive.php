<?php
/**
 * Eventos archive: carrusel marquee-media-01 (Customizer).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mwm_render_theme_block' ) ) {
	return;
}

$section_title = get_option( 'mwm_eventos_archive_carousel_title' );
$section_title = is_string( $section_title ) ? $section_title : '';
if ( '' === trim( wp_strip_all_tags( $section_title ) ) ) {
	$section_title = __( 'Así se vive los eventos para agencias de Zenyx', THEME_TEXT_DOMAIN );
}

$lead_text = get_option( 'mwm_eventos_archive_carousel_lead' );
$lead_text = is_string( $lead_text ) ? $lead_text : '';
if ( '' === trim( wp_strip_all_tags( $lead_text ) ) ) {
	$lead_text = '';
}

$center_content = get_option( 'mwm_eventos_archive_carousel_center', true );
$center_content = (bool) $center_content;

$image_ids = function_exists( 'mwm_eventos_archive_get_carousel_image_ids' )
	? mwm_eventos_archive_get_carousel_image_ids()
	: array();

$duration = function_exists( 'mwm_eventos_archive_carousel_duration_seconds' )
	? mwm_eventos_archive_carousel_duration_seconds( count( $image_ids ) )
	: 40;

$html = mwm_render_theme_block(
	'zenyx/marquee-media-01',
	array(
		'sectionTitle'           => $section_title,
		'leadText'               => $lead_text,
		'centerContent'          => $center_content,
		'marqueeDurationSeconds' => $duration,
		'imageIds'               => $image_ids,
	)
);

if ( '' !== $html ) {
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render.
}
