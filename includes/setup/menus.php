<?php
/**
 * Menu registration.
 */

if ( ! function_exists( 'mwm_register_menus' ) ) {
	function mwm_register_menus() {
		register_nav_menus(
			array(
				'HeaderMenu' => __( 'Menu superior', THEME_TEXT_DOMAIN ),
				'FooterMenu1' => __( 'Menu footer 1', THEME_TEXT_DOMAIN ),
				'FooterMenu2' => __( 'Menu footer 2', THEME_TEXT_DOMAIN ),
				'FooterMenu3' => __( 'Menu footer 3', THEME_TEXT_DOMAIN ),
			)
		);
	}
	add_action( 'after_setup_theme', 'mwm_register_menus' );
}
