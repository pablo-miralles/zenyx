<?php
/**
 * Extiende el bloque core (Grupo) con un atributo y clase para animación entre secciones.
 */

if ( ! function_exists( 'mwm_register_core_group_extension_attributes' ) ) {
	/**
	 * Registra el atributo booleano en core/group para que persista en el comentario del bloque.
	 *
	 * @param array  $args       Argumentos del tipo de bloque.
	 * @param string $block_name Nombre del bloque.
	 * @return array
	 */
	function mwm_register_core_group_extension_attributes( $args, $block_name ) {
		if ( 'core/group' !== $block_name ) {
			return $args;
		}
		if ( ! isset( $args['attributes'] ) || ! is_array( $args['attributes'] ) ) {
			$args['attributes'] = array();
		}
		$args['attributes']['mwmSectionColorTransition'] = array(
			'type'    => 'boolean',
			'default' => false,
		);
		$args['attributes']['mwmSectionColorSecondOnly'] = array(
			'type'    => 'boolean',
			'default' => false,
		);
		return $args;
	}
	add_filter( 'register_block_type_args', 'mwm_register_core_group_extension_attributes', 10, 2 );
}
