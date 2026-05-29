<?php
/**
 * Walker del menú: añade el chevron SVG dentro del enlace de ítems padre (solo depth 0).
 *
 * Extiende Walker_Nav_Menu y reutiliza todo el markup de core vía parent::start_el(),
 * insertando el icono antes de </a> del primer nivel.
 *
 * @package zenyx
 */

if ( ! function_exists( 'mwm_nav_menu_chevron_html' ) ) {
	/**
	 * Markup del chevron (stroke hereda currentColor del enlace).
	 *
	 * @return string HTML (sin escapar: SVG estático).
	 */
	function mwm_nav_menu_chevron_html() {
		return '<span class="mwm-nav-chevron" aria-hidden="true">'
			. '<svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">'
			. '<path d="M0.59961 0.600006L7.34961 7.35001L14.0996 0.600006" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>'
			. '</svg></span>';
	}
}

if ( ! class_exists( 'MWM_Walker_Nav_Menu' ) ) {
	/**
	 * Walker para HeaderMenu (desktop + móvil): chevron en padres con hijos.
	 */
	class MWM_Walker_Nav_Menu extends Walker_Nav_Menu {

		/**
		 * @param string   $output            Output.
		 * @param WP_Post  $data_object       Ítem.
		 * @param int      $depth             Profundidad.
		 * @param stdClass $args              Args de wp_nav_menu.
		 * @param int      $current_object_id ID actual.
		 */
		public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
			$mark = strlen( $output );
			parent::start_el( $output, $data_object, $depth, $args, $current_object_id );

			if ( 0 !== (int) $depth ) {
				return;
			}

			$classes = empty( $data_object->classes ) ? array() : (array) $data_object->classes;
			if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
				return;
			}

			$chunk = substr( $output, $mark );
			$html  = preg_replace( '/<\/a>/', mwm_nav_menu_chevron_html() . '</a>', $chunk, 1 );

			if ( is_string( $html ) && $html !== $chunk ) {
				$output = substr( $output, 0, $mark ) . $html;
			}
		}
	}
}
