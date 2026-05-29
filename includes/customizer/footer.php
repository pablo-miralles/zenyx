<?php
/**
 * Footer customizer settings.
 */

if ( ! function_exists( 'mwm_customize_register_footer' ) ) {
	function mwm_customize_register_footer( $wp_customize ) {
		$wp_customize->add_section(
			'mwm_footer_section',
			array(
				'title'       => __( 'Footer', THEME_TEXT_DOMAIN ),
				'description' => __( 'Configuracion de textos y redes del footer.', THEME_TEXT_DOMAIN ),
				'priority'    => 165,
			)
		);

		$wp_customize->add_setting(
			'mwm_footer_text_1',
			array(
				'type'              => 'option',
				'default'           => __( 'Powered by Balinot Tech Consulting', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'mwm_sanitize_footer_text_1',
			)
		);
		$wp_customize->add_control(
			'mwm_footer_text_1',
			array(
				'label'       => __( 'Footer texto 1', THEME_TEXT_DOMAIN ),
				'description' => __( 'Puedes usar HTML para enlaces, por ejemplo: Powered by &lt;a href=&quot;https://ejemplo.com&quot;&gt;Empresa&lt;/a&gt;', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_footer_section',
				'type'        => 'textarea',
				'input_attrs' => array(
					'placeholder' => __( 'Powered by Balinot Tech Consulting', THEME_TEXT_DOMAIN ),
					'rows'        => 3,
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_footer_text_2',
			array(
				'type'              => 'option',
				'default'           => 'Zenyx [CURRENT_YEAR] ©',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_footer_text_2',
			array(
				'label'       => __( 'Footer copyright', THEME_TEXT_DOMAIN ),
				'description' => __( 'Usa [CURRENT_YEAR] para insertar el año actual automáticamente.', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_footer_section',
				'type'        => 'text',
				'input_attrs' => array(
					'placeholder' => 'Zenyx [CURRENT_YEAR] ©',
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_footer_linkedin_url',
			array(
				'type'              => 'option',
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'mwm_footer_linkedin_url',
			array(
				'label'       => __( 'LinkedIn URL', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_footer_section',
				'type'        => 'url',
				'input_attrs' => array(
					'placeholder' => 'https://linkedin.com/company/...',
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_footer_instagram_url',
			array(
				'type'              => 'option',
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'mwm_footer_instagram_url',
			array(
				'label'       => __( 'Instagram URL', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_footer_section',
				'type'        => 'url',
				'input_attrs' => array(
					'placeholder' => 'https://instagram.com/...',
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_footer_youtube_url',
			array(
				'type'              => 'option',
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'mwm_footer_youtube_url',
			array(
				'label'       => __( 'YouTube URL', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_footer_section',
				'type'        => 'url',
				'input_attrs' => array(
					'placeholder' => 'https://youtube.com/@...',
				),
			)
		);
	}
	add_action( 'customize_register', 'mwm_customize_register_footer' );
}
