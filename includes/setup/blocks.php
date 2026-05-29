<?php
/**
 * Theme blocks registration.
 */

if ( ! function_exists( 'mwm_register_theme_blocks' ) ) {
	function mwm_register_theme_blocks() {
		$blocks_dir = get_template_directory() . '/blocks';

		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}

		$entries = scandir( $blocks_dir );
		if ( ! is_array( $entries ) ) {
			return;
		}

		foreach ( $entries as $entry ) {
			if ( '.' === $entry || '..' === $entry ) {
				continue;
			}

			$block_path = $blocks_dir . '/' . $entry;
			if ( ! is_dir( $block_path ) ) {
				continue;
			}

			if ( file_exists( $block_path . '/block.json' ) ) {
				register_block_type( $block_path );
			}
		}
	}
	add_action( 'init', 'mwm_register_theme_blocks' );
}
