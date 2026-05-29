<?php
/**
 * Header inline SVG logo template part.
 *
 * @package zenyx
 */

$logo_svg_path = get_template_directory() . '/assets/images/header-logo.php';

if ( file_exists( $logo_svg_path ) ) {
	include $logo_svg_path;
} elseif ( has_custom_logo() ) {
	echo wp_get_attachment_image(
		get_theme_mod( 'custom_logo' ),
		'medium',
		false,
		array( 'class' => 'h-8 w-auto max-w-[8rem]' )
	);
} else {
	bloginfo( 'name' );
}
