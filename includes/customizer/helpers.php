<?php
/**
 * Customizer helper functions.
 */

if ( ! function_exists( 'mwm_parse_image_ids_json' ) ) {
	/**
	 * Convierte JSON (o array) de IDs de adjunto en lista validada.
	 *
	 * @param mixed $value Raw value.
	 * @return array<int>
	 */
	function mwm_parse_image_ids_json( $value ) {
		if ( is_array( $value ) ) {
			$raw = $value;
		} else {
			$value = trim( (string) $value );
			if ( '' === $value ) {
				return array();
			}
			$raw = json_decode( $value, true );
			if ( ! is_array( $raw ) ) {
				$raw = preg_split( '/\s*,\s*/', $value );
				$raw = is_array( $raw ) ? $raw : array();
			}
		}

		$ids = array();
		foreach ( $raw as $id ) {
			$id = absint( $id );
			if ( $id < 1 ) {
				continue;
			}
			if ( ! wp_attachment_is_image( $id ) ) {
				continue;
			}
			$ids[] = $id;
		}

		return array_values( array_unique( $ids ) );
	}
}

if ( ! function_exists( 'mwm_sanitize_image_ids_json' ) ) {
	/**
	 * Sanitiza y guarda IDs de imagen como JSON.
	 *
	 * @param mixed $value Raw value.
	 * @return string JSON array.
	 */
	function mwm_sanitize_image_ids_json( $value ) {
		$ids = mwm_parse_image_ids_json( $value );
		$json = wp_json_encode( $ids );
		return false === $json ? '[]' : $json;
	}
}

if ( ! function_exists( 'mwm_sanitize_checkbox' ) ) {
	/**
	 * @param mixed $value Raw value.
	 * @return bool
	 */
	function mwm_sanitize_checkbox( $value ) {
		return (bool) $value;
	}
}

if ( ! function_exists( 'mwm_sanitize_cta_theme' ) ) {
	/**
	 * @param string $value Raw value.
	 * @return string
	 */
	function mwm_sanitize_cta_theme( $value ) {
		$value = (string) $value;
		return in_array( $value, array( 'claro', 'oscuro' ), true ) ? $value : 'oscuro';
	}
}

if ( ! function_exists( 'mwm_sanitize_cta_button_url' ) ) {
	/**
	 * URL absoluta o ruta relativa al sitio (p. ej. /contacto/).
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	function mwm_sanitize_cta_button_url( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		if ( str_starts_with( $value, '/' ) ) {
			return esc_url_raw( home_url( $value ) );
		}
		return esc_url_raw( $value );
	}
}

if ( ! function_exists( 'mwm_customize_register_cta_block' ) ) {
	/**
	 * Registra campos CTA 01 en una sección del Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Manager.
	 * @param string               $section      Section ID.
	 * @param string               $prefix       Prefijo de option (sin guión final).
	 * @param array<string, mixed> $defaults     Valores por defecto.
	 */
	function mwm_customize_register_cta_block( $wp_customize, $section, $prefix, array $defaults = array() ) {
		$defaults = wp_parse_args(
			$defaults,
			array(
				'heading'          => '',
				'description'      => '',
				'theme'            => 'oscuro',
				'button_text'      => '',
				'button_url'       => '',
				'opens_new_tab'    => false,
			)
		);

		$wp_customize->add_setting(
			$prefix . '_heading',
			array(
				'type'              => 'option',
				'default'           => $defaults['heading'],
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_control(
			$prefix . '_heading',
			array(
				'label'   => __( 'Titular', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			$prefix . '_description',
			array(
				'type'              => 'option',
				'default'           => $defaults['description'],
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_control(
			$prefix . '_description',
			array(
				'label'   => __( 'Descripcion (opcional)', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			$prefix . '_theme',
			array(
				'type'              => 'option',
				'default'           => $defaults['theme'],
				'sanitize_callback' => 'mwm_sanitize_cta_theme',
			)
		);
		$wp_customize->add_control(
			$prefix . '_theme',
			array(
				'label'   => __( 'Tema de fondo', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'select',
				'choices' => array(
					'claro'  => __( 'Claro', THEME_TEXT_DOMAIN ),
					'oscuro' => __( 'Oscuro', THEME_TEXT_DOMAIN ),
				),
			)
		);

		$wp_customize->add_setting(
			$prefix . '_button_text',
			array(
				'type'              => 'option',
				'default'           => $defaults['button_text'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$prefix . '_button_text',
			array(
				'label'   => __( 'Texto del boton', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			$prefix . '_button_url',
			array(
				'type'              => 'option',
				'default'           => $defaults['button_url'],
				'sanitize_callback' => 'mwm_sanitize_cta_button_url',
			)
		);
		$wp_customize->add_control(
			$prefix . '_button_url',
			array(
				'label'   => __( 'URL del boton', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'url',
			)
		);

		$wp_customize->add_setting(
			$prefix . '_opens_new_tab',
			array(
				'type'              => 'option',
				'default'           => ! empty( $defaults['opens_new_tab'] ),
				'sanitize_callback' => 'mwm_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$prefix . '_opens_new_tab',
			array(
				'label'   => __( 'Abrir enlace en nueva pestaña', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'checkbox',
			)
		);
	}
}

if ( ! function_exists( 'mwm_render_cta_from_options' ) ) {
	/**
	 * Renderiza zenyx/cta-01 desde options del Customizer.
	 *
	 * @param string               $prefix   Prefijo de option (sin guión final).
	 * @param array<string, mixed> $defaults Fallbacks si la option está vacía.
	 * @return string HTML del bloque o vacío.
	 */
	function mwm_render_cta_from_options( $prefix, array $defaults = array() ) {
		if ( ! function_exists( 'mwm_render_theme_block' ) ) {
			return '';
		}

		$defaults = wp_parse_args(
			$defaults,
			array(
				'heading'       => '',
				'description'   => '',
				'theme'         => 'oscuro',
				'button_text'   => '',
				'button_url'    => '',
				'opens_new_tab' => false,
			)
		);

		$heading = get_option( $prefix . '_heading' );
		$heading = is_string( $heading ) ? $heading : '';
		if ( '' === trim( wp_strip_all_tags( $heading ) ) ) {
			$heading = (string) $defaults['heading'];
		}

		$description = get_option( $prefix . '_description' );
		$description = is_string( $description ) ? $description : (string) $defaults['description'];

		$theme = get_option( $prefix . '_theme' );
		$theme = in_array( (string) $theme, array( 'claro', 'oscuro' ), true ) ? $theme : (string) $defaults['theme'];

		$button_text = get_option( $prefix . '_button_text' );
		$button_text = is_string( $button_text ) ? trim( $button_text ) : '';
		if ( '' === $button_text ) {
			$button_text = (string) $defaults['button_text'];
		}

		$button_url = get_option( $prefix . '_button_url' );
		$button_url = is_string( $button_url ) ? trim( $button_url ) : '';
		if ( '' === $button_url ) {
			$button_url = (string) $defaults['button_url'];
		}

		$opens_new_tab = get_option( $prefix . '_opens_new_tab' );
		if ( false === $opens_new_tab || '' === $opens_new_tab ) {
			$opens_new_tab = ! empty( $defaults['opens_new_tab'] );
		} else {
			$opens_new_tab = (bool) $opens_new_tab;
		}

		return mwm_render_theme_block(
			'zenyx/cta-01',
			array(
				'heading'           => $heading,
				'description'       => $description,
				'theme'             => $theme,
				'buttonText'        => $button_text,
				'buttonUrl'         => $button_url,
				'opensInNewTab'     => $opens_new_tab,
				'hideArrowOnMobile' => false,
			)
		);
	}
}

if ( ! function_exists( 'mwm_sanitize_url_or_hash' ) ) {
	/**
	 * Allows full URLs or same-page anchors (e.g. #articulos).
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	function mwm_sanitize_url_or_hash( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '#articulos';
		}
		if ( preg_match( '/^#[\w-]+$/', $value ) ) {
			return $value;
		}
		return esc_url_raw( $value );
	}
}
