<?php
/**
 * Theme helper functions.
 */

if ( ! function_exists( 'mwm_sanitize_footer_text_1' ) ) {
	/**
	 * Footer texto 1: permite enlaces y formato básico (post KSES).
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	function mwm_sanitize_footer_text_1( $value ) {
		return wp_kses_post( (string) $value );
	}
}

if ( ! function_exists( 'mwm_format_footer_text_2' ) ) {
	/**
	 * Footer copyright: sustituye [CURRENT_YEAR] por el año actual.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	function mwm_format_footer_text_2( $value ) {
		$value = (string) $value;
		if ( '' === $value ) {
			return '';
		}
		return str_replace( '[CURRENT_YEAR]', (string) gmdate( 'Y' ), $value );
	}
}

if ( ! function_exists( 'mwm_render_theme_block' ) ) {
	/**
	 * Renderiza un bloque del tema por nombre con atributos (p. ej. desde Customizer).
	 *
	 * @param string               $block_name Block name (zenyx/marquee-media-01).
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string
	 */
	function mwm_render_theme_block( $block_name, array $attributes = array() ) {
		if ( ! function_exists( 'render_block' ) ) {
			return '';
		}

		$block_name = (string) $block_name;
		if ( '' === $block_name ) {
			return '';
		}

		$registry = WP_Block_Type_Registry::get_instance();
		if ( ! $registry->is_registered( $block_name ) ) {
			return '';
		}

		return render_block(
			array(
				'blockName'    => $block_name,
				'attrs'        => $attributes,
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);
	}
}

if ( ! function_exists( 'mwm_get_reusable_block' ) ) {
	/**
	 * Funcion para obtener el contenido de un bloque reutilizable.
	 *
	 * @param int $block_id Reusable block ID.
	 * @return string|void
	 */
	function mwm_get_reusable_block( $block_id = '' ) {
		if ( empty( $block_id ) || (int) $block_id !== $block_id ) {
			return;
		}
		$content = get_post_field( 'post_content', $block_id );
		return apply_filters( 'the_content', $content );
	}
}
