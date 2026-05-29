<?php
/**
 * Menús: ítems padre con submenú y URL “vacía” (#) no deben navegar al hacer clic.
 *
 * Nota: WordPress escapa href con esc_url() y elimina javascript:, así que no usamos
 * javascript:void(0) en el HTML; marcamos el enlace y evitamos el default en JS.
 *
 * @package zenyx
 */

if ( ! function_exists( 'mwm_nav_menu_parent_dummy_link_attributes' ) ) {
	/**
	 * Añade clase a enlaces padre con hijos cuya URL es solo # o vacía.
	 *
	 * @param array    $atts      Atributos HTML del enlace.
	 * @param WP_Post  $item      Objeto del ítem de menú.
	 * @param stdClass $args      Argumentos de wp_nav_menu.
	 * @param int      $depth     Profundidad.
	 * @return array
	 */
	function mwm_nav_menu_parent_dummy_link_attributes( $atts, $item, $args, $depth ) {
		if ( 0 !== (int) $depth ) {
			return $atts;
		}

		if ( ! in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			return $atts;
		}

		$url = isset( $item->url ) ? trim( (string) $item->url ) : '';

		if ( '' === $url || '#' === $url ) {
			$existing      = isset( $atts['class'] ) ? (string) $atts['class'] : '';
			$atts['class'] = trim( $existing . ' mwm-menu-parent-no-nav' );
		}

		return $atts;
	}
	add_filter( 'nav_menu_link_attributes', 'mwm_nav_menu_parent_dummy_link_attributes', 10, 4 );
}
