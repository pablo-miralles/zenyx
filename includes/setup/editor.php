<?php
/**
 * Block editor styles and assets.
 */

if ( ! function_exists( 'mwm_editor_styles' ) ) {
	function mwm_editor_styles() {
		add_theme_support( 'editor-styles' );
		// theme.css (Tailwind) al final para que gane sobre style.css y el resto en el iframe del editor.
		add_editor_style(
			array(
				'style.css',
				'assets/css/editor.css',
				'assets/dist/theme.css',
			)
		);
	}
	add_action( 'after_setup_theme', 'mwm_editor_styles', 20 );
}

if ( ! function_exists( 'mwm_block_editor_assets' ) ) {
	function mwm_block_editor_assets() {
		wp_register_style( 'zenyx-editor-admin', false );
		wp_enqueue_style( 'zenyx-editor-admin' );
		wp_add_inline_style(
			'zenyx-editor-admin',
			'.components-popover.block-editor-block-popover { z-index: 99999 !important; }'
		);
	}
	add_action( 'enqueue_block_editor_assets', 'mwm_block_editor_assets' );
}

if ( ! function_exists( 'mwm_media_text_02_editor_globals' ) ) {
	/**
	 * URL del degradado PNG para la vista previa del bloque media-text-02 en el editor.
	 */
	function mwm_media_text_02_editor_globals() {
		wp_register_script(
			'zenyx-media-text-02-editor-data',
			false,
			array( 'wp-blocks' ),
			null,
			true
		);
		wp_enqueue_script( 'zenyx-media-text-02-editor-data' );
		wp_add_inline_script(
			'zenyx-media-text-02-editor-data',
			'window.zenyxMediaText02=' . wp_json_encode(
				array(
					'degradadoUrl' => get_template_directory_uri() . '/assets/images/media-text-02-degradado.png',
				)
			) . ';',
			'after'
		);
	}
	add_action( 'enqueue_block_editor_assets', 'mwm_media_text_02_editor_globals', 4 );
}

if ( ! function_exists( 'mwm_enqueue_core_group_extension' ) ) {
	/**
	 * Editor: filtros para core/group (Inspector + clase guardada).
	 */
	function mwm_enqueue_core_group_extension() {
		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$path = $dir . '/assets/dist/core-group-extension/index.js';
		$asset_path = $dir . '/assets/dist/core-group-extension/index.asset.php';
		if ( ! file_exists( $path ) || ! file_exists( $asset_path ) ) {
			return;
		}
		$asset = require $asset_path;
		wp_enqueue_script(
			'mwm-core-group-extension',
			$uri . '/assets/dist/core-group-extension/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
		wp_set_script_translations( 'mwm-core-group-extension', 'zenyx', $dir . '/languages' );
	}
	add_action( 'enqueue_block_editor_assets', 'mwm_enqueue_core_group_extension', 20 );
}

if ( ! function_exists( 'mwm_enqueue_block_editor_slider_assets' ) ) {
	function mwm_enqueue_block_editor_slider_assets() {
		if ( ! is_admin() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();

		wp_enqueue_script(
			'mwm-scripts-editor',
			$uri . '/assets/js/scripts.js',
			array( 'jquery' ),
			file_exists( $dir . '/assets/js/scripts.js' ) ? (string) filemtime( $dir . '/assets/js/scripts.js' ) : '1.0.0',
			true
		);
	}
	add_action( 'enqueue_block_assets', 'mwm_enqueue_block_editor_slider_assets' );
}
