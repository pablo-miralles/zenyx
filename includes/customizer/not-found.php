<?php
/**
 * 404 customizer settings.
 */

if ( ! function_exists( 'mwm_customize_register_not_found' ) ) {
	function mwm_customize_register_not_found( $wp_customize ) {
		$wp_customize->add_section(
			'mwm_404_section',
			array(
				'title'       => __( '404', THEME_TEXT_DOMAIN ),
				'description' => __( 'Configuracion de la pagina 404.', THEME_TEXT_DOMAIN ),
				'priority'    => 160,
			)
		);

		$wp_customize->add_setting(
			'mwm_404_title',
			array(
				'type'              => 'option',
				'default'           => __( 'Pagina no encontrada', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'mwm_404_title',
			array(
				'label'       => __( 'Titulo 404', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_404_section',
				'type'        => 'text',
				'input_attrs' => array(
					'placeholder' => __( 'Pagina no encontrada', THEME_TEXT_DOMAIN ),
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_404_button_text',
			array(
				'type'              => 'option',
				'default'           => __( 'Volver a la home', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'mwm_404_button_text',
			array(
				'label'       => __( 'Texto del boton', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_404_section',
				'type'        => 'text',
				'input_attrs' => array(
					'placeholder' => __( 'Volver a la home', THEME_TEXT_DOMAIN ),
				),
			)
		);
	}
	add_action( 'customize_register', 'mwm_customize_register_not_found' );
}
