<?php
/**
 * Frontend scripts and styles.
 */

if ( ! function_exists( 'mwm_enqueue_scripts' ) ) {
	function mwm_enqueue_scripts() {
		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$fancybox_js_path  = $dir . '/assets/vendor/fancybox/fancybox.umd.js';
		$fancybox_css_path = $dir . '/assets/vendor/fancybox/fancybox.css';

		$lenis_js          = $dir . '/assets/dist/lenis/index.js';
		$lenis_asset_path = $dir . '/assets/dist/lenis/index.asset.php';
		$has_lenis         = file_exists( $lenis_js ) && file_exists( $lenis_asset_path );
		if ( $has_lenis ) {
			$len_asset = require $lenis_asset_path;
			wp_enqueue_script(
				'mwm-lenis',
				$uri . '/assets/dist/lenis/index.js',
				$len_asset['dependencies'],
				$len_asset['version'],
				true
			);
		}

		$script_deps = array( 'jquery' );
		if ( $has_lenis ) {
			$script_deps[] = 'mwm-lenis';
		}
		if ( file_exists( $fancybox_js_path ) ) {
			wp_enqueue_script(
				'mwm-fancybox',
				$uri . '/assets/vendor/fancybox/fancybox.umd.js',
				array(),
				(string) filemtime( $fancybox_js_path ),
				true
			);
			$script_deps[] = 'mwm-fancybox';
		}

		wp_enqueue_script(
			'mwm-scripts',
			$uri . '/assets/js/scripts.js',
			$script_deps,
			file_exists( $dir . '/assets/js/scripts.js' ) ? (string) filemtime( $dir . '/assets/js/scripts.js' ) : '1.0.0',
			true
		);

		$header_zones_js = $dir . '/assets/js/header-section-colors.js';
		if ( file_exists( $header_zones_js ) ) {
			wp_enqueue_script(
				'zenyx-header-section-colors',
				$uri . '/assets/js/header-section-colors.js',
				array( 'mwm-scripts' ),
				(string) filemtime( $header_zones_js ),
				true
			);
		}

		$group_section_color_js     = $dir . '/assets/dist/group-section-color-transition/index.js';
		$group_section_color_asset = $dir . '/assets/dist/group-section-color-transition/index.asset.php';
		if ( file_exists( $group_section_color_js ) && file_exists( $group_section_color_asset ) ) {
			$group_asset = require $group_section_color_asset;
			$group_deps  = array_merge( array( 'mwm-scripts' ), $group_asset['dependencies'] );
			if ( $has_lenis ) {
				$group_deps[] = 'mwm-lenis';
			}
			if ( file_exists( $header_zones_js ) ) {
				$group_deps[] = 'zenyx-header-section-colors';
			}
			wp_enqueue_script(
				'mwm-group-section-color-transition',
				$uri . '/assets/dist/group-section-color-transition/index.js',
				$group_deps,
				$group_asset['version'],
				true
			);
		}

		$theme_css  = $dir . '/assets/dist/theme.css';
		$style_file = $dir . '/style.css';

		if ( file_exists( $fancybox_css_path ) ) {
			wp_enqueue_style(
				'mwm-fancybox',
				$uri . '/assets/vendor/fancybox/fancybox.css',
				array(),
				(string) filemtime( $fancybox_css_path )
			);
		}

		$fancybox_enabled = file_exists( $fancybox_css_path );

		// Base del tema después de librería de bloques y estilos globales (theme.json).
		$style_deps = array(
			'wp-block-library',
			'global-styles',
		);
		if ( $fancybox_enabled ) {
			$style_deps[] = 'mwm-fancybox';
		}

		wp_enqueue_style(
			'mwm-styles',
			$uri . '/style.css',
			$style_deps,
			file_exists( $style_file ) ? (string) filemtime( $style_file ) : '1.0.0'
		);

		// Tailwind al final del tema para máxima prioridad frente a style.css y a estilos encolados antes.
		wp_enqueue_style(
			'mwm-tailwind',
			$uri . '/assets/dist/theme.css',
			array( 'mwm-styles' ),
			file_exists( $theme_css ) ? (string) filemtime( $theme_css ) : '1.0.0'
		);
	}
	// Prioridad alta: el tema va después de la mayoría de plugins que encolan en el hook por defecto.
	add_action( 'wp_enqueue_scripts', 'mwm_enqueue_scripts', 100 );
}

if ( ! function_exists( 'mwm_enqueue_blog_entries' ) ) {
	/**
	 * Blog index and category archives: filters + load more script.
	 */
	function mwm_enqueue_blog_entries() {
		if ( ! is_home() && ! is_category() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$path = $dir . '/assets/js/blog-entries.js';

		if ( ! file_exists( $path ) ) {
			return;
		}

		wp_enqueue_script(
			'mwm-blog-entries',
			$uri . '/assets/js/blog-entries.js',
			array(),
			(string) filemtime( $path ),
			true
		);

		wp_localize_script(
			'mwm-blog-entries',
			'mwmBlogEntries',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mwm_blog_entries' ),
				'action'  => 'mwm_blog_entries',
				'strings' => array(
					'empty'      => __( 'No hay entradas en estas categorias.', THEME_TEXT_DOMAIN ),
					'error'      => __( 'No se pudieron cargar las entradas.', THEME_TEXT_DOMAIN ),
					'announced'  => __( 'Lista de entradas actualizada.', THEME_TEXT_DOMAIN ),
					'loadedMore' => __( 'Se cargaron mas entradas.', THEME_TEXT_DOMAIN ),
				),
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'mwm_enqueue_blog_entries', 101 );
}

if ( ! function_exists( 'mwm_enqueue_single_post_copy' ) ) {
	/**
	 * Single post: copiar enlace al portapapeles.
	 */
	function mwm_enqueue_single_post_copy() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$dir  = get_template_directory();
		$uri  = get_template_directory_uri();
		$path = $dir . '/assets/js/single-post-copy.js';

		if ( ! file_exists( $path ) ) {
			return;
		}

		wp_enqueue_script(
			'zenyx-single-post-copy',
			$uri . '/assets/js/single-post-copy.js',
			array(),
			(string) filemtime( $path ),
			true
		);

		wp_localize_script(
			'zenyx-single-post-copy',
			'zenyxSinglePostCopy',
			array(
				'copied' => __( 'Enlace copiado al portapapeles.', THEME_TEXT_DOMAIN ),
				'error'  => __( 'No se pudo copiar el enlace.', THEME_TEXT_DOMAIN ),
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'mwm_enqueue_single_post_copy', 102 );
}

if ( ! function_exists( 'mwm_enqueue_casos_archive_cards' ) ) {
	/**
	 * Archive caso_exito: hover / focus por fila en cards.
	 */
	function mwm_enqueue_casos_archive_cards() {
		if ( ! is_post_type_archive( 'caso_exito' ) ) {
			return;
		}

		$dir  = get_template_directory();
		$uri  = get_template_directory_uri();
		$path = $dir . '/assets/js/casos-archive-cards.js';

		if ( ! file_exists( $path ) ) {
			return;
		}

		wp_enqueue_script(
			'zenyx-casos-archive-cards',
			$uri . '/assets/js/casos-archive-cards.js',
			array(),
			(string) filemtime( $path ),
			true
		);
	}
	add_action( 'wp_enqueue_scripts', 'mwm_enqueue_casos_archive_cards', 103 );
}

if ( ! function_exists( 'mwm_enqueue_footer_mailto_copy' ) ) {
	/**
	 * Enlaces mailto (footer, contacto, etc.): copian el email al portapapeles (Notyf).
	 */
	function mwm_enqueue_footer_mailto_copy() {
		$dir  = get_template_directory();
		$uri  = get_template_directory_uri();
		$js   = $dir . '/assets/js/footer-mailto-copy.js';
		$notyf_js  = $dir . '/assets/vendor/notyf/notyf.min.js';
		$notyf_css = $dir . '/assets/vendor/notyf/notyf.min.css';

		if ( ! file_exists( $js ) || ! file_exists( $notyf_js ) || ! file_exists( $notyf_css ) ) {
			return;
		}

		wp_enqueue_style(
			'zenyx-notyf',
			$uri . '/assets/vendor/notyf/notyf.min.css',
			array(),
			(string) filemtime( $notyf_css )
		);

		wp_enqueue_script(
			'zenyx-notyf',
			$uri . '/assets/vendor/notyf/notyf.min.js',
			array(),
			(string) filemtime( $notyf_js ),
			true
		);

		wp_enqueue_script(
			'zenyx-footer-mailto-copy',
			$uri . '/assets/js/footer-mailto-copy.js',
			array( 'zenyx-notyf' ),
			(string) filemtime( $js ),
			true
		);

		wp_localize_script(
			'zenyx-footer-mailto-copy',
			'zenyxFooterMailtoCopy',
			array(
				'copied' => __( 'Email copiado al portapapeles.', THEME_TEXT_DOMAIN ),
				'error'  => __( 'No se pudo copiar el email.', THEME_TEXT_DOMAIN ),
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'mwm_enqueue_footer_mailto_copy', 104 );
}
