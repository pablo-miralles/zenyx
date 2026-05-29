<?php
/**
 * Theme supports.
 */

if ( ! function_exists( 'mwm_theme_supports' ) ) {
	function mwm_theme_supports() {
		add_theme_support( 'align-wide' );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
	}
	add_action( 'after_setup_theme', 'mwm_theme_supports' );
}
