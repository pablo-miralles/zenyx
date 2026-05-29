<?php
/**
 * Reusable button component.
 */

if ( ! function_exists( 'mwm_get_button_icon_svg' ) ) {
	/**
	 * Returns SVG markup for button icon.
	 *
	 * @param string $icon Icon key.
	 * @return string
	 */
	function mwm_get_button_icon_svg( $icon ) {
		if ( 'play' === $icon ) {
			return '<svg class="mwm-btn__icon-svg" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M0 15.6801C0.00476224 15.9565 0.0851898 16.2264 0.232528 16.4602C0.379865 16.6941 0.588489 16.8831 0.835714 17.0066C1.08259 17.1462 1.36134 17.2195 1.6449 17.2195C1.92846 17.2195 2.20721 17.1462 2.45408 17.0066L13.9286 9.90974C14.1782 9.78946 14.3889 9.60116 14.5364 9.3665C14.6838 9.13183 14.7619 8.86033 14.7619 8.58321C14.7619 8.30608 14.6838 8.03458 14.5364 7.79992C14.3889 7.56526 14.1782 7.37695 13.9286 7.25668L2.45408 0.212798C2.20721 0.0733041 1.92846 0 1.6449 0C1.36134 0 1.08259 0.0733041 0.835714 0.212798C0.588489 0.336416 0.379865 0.525431 0.232528 0.759305C0.0851898 0.993172 0.00476224 1.26296 0 1.53933V15.6801Z" fill="currentColor"/></svg>';
		}

		if ( 'arrow-right' === $icon ) {
			return '<svg class="mwm-btn__icon-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M0.625 10H19.375" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10.625 18.75L19.375 10L10.625 1.25" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
		}

		if ( 'campus' === $icon ) {
			return '<svg class="mwm-btn__icon-svg" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M2.96875 5.90625C2.96875 6.72677 3.2947 7.51365 3.87488 8.09385C4.45508 8.67405 5.24198 9 6.0625 9C6.88302 9 7.66992 8.67405 8.2501 8.09385C8.8303 7.51365 9.15625 6.72677 9.15625 5.90625C9.15625 5.08573 8.8303 4.29883 8.2501 3.71863C7.66992 3.13845 6.88302 2.8125 6.0625 2.8125C5.24198 2.8125 4.45508 3.13845 3.87488 3.71863C3.2947 4.29883 2.96875 5.08573 2.96875 5.90625Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 15.0625C1 13.7199 1.53337 12.4322 2.48277 11.4828C3.43218 10.5334 4.71984 10 6.0625 10C7.40516 10 8.69282 10.5334 9.64225 11.4828C10.5916 12.4322 11.125 13.7199 11.125 15.0625" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M11 7.5C11 8.16304 11.2633 8.79889 11.7322 9.26778C12.2011 9.73659 12.8369 10 13.5 10C14.1631 10 14.7989 9.73659 15.2678 9.26778C15.7367 8.79889 16 8.16304 16 7.5C16 6.83696 15.7367 6.20107 15.2678 5.73223C14.7989 5.26339 14.1631 5 13.5 5C12.8369 5 12.2011 5.26339 11.7322 5.73223C11.2633 6.20107 11 6.83696 11 7.5Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 11.2469C12.5614 11.024 13.1645 10.9505 13.758 11.0326C14.3515 11.1147 14.9178 11.35 15.4088 11.7185C15.8997 12.087 16.3007 12.5778 16.5778 13.1491C16.8548 13.7205 16.9997 14.3554 17 15" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
		}

		if ( 'copy' === $icon ) {
			$clip_id = 'mwm-btn-copy-' . uniqid( '', false );
			return '<svg class="mwm-btn__icon-svg" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><g clip-path="url(#' . esc_attr( $clip_id ) . ')"><path d="M13.9583 3.75V1.45833C13.9583 0.9981 13.5852 0.625 13.125 0.625H1.45833C0.9981 0.625 0.625 0.998092 0.625 1.45833V13.125C0.625 13.5852 0.9981 13.9583 1.45833 13.9583H3.75" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.04102 6.87508C6.04102 6.41484 6.41412 6.04175 6.87435 6.04175H18.541C19.0013 6.04175 19.3743 6.41485 19.3743 6.87508V18.5417C19.3743 19.002 19.0013 19.3751 18.541 19.3751H6.87435C6.41411 19.3751 6.04102 19.002 6.04102 18.5417V6.87508Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"></path></g><defs><clipPath id="' . esc_attr( $clip_id ) . '"><rect width="20" height="20" fill="white"></rect></clipPath></defs></svg>';
		}

		return '';
	}
}

if ( ! function_exists( 'mwm_render_button' ) ) {
	/**
	 * Prints a reusable button.
	 *
	 * @param array $args Button configuration.
	 * @return void
	 */
	function mwm_render_button( $args = array() ) {
		$defaults = array(
			'text'             => '',
			'url'              => '#',
			'variant'          => 'primary',
			'icon'             => 'none',
			'icon_position'    => 'after',
			'size'             => 'md',
			'target'           => '',
			'rel'              => '',
			'aria_label'       => '',
			'aria_disabled'    => false,
			'disabled'         => false,
			'class'            => '',
			'as'               => 'a',
			'type'             => 'button',
			'data_attributes'  => array(),
		);

		$args    = wp_parse_args( $args, $defaults );
		$text    = (string) $args['text'];
		$tag     = 'button' === $args['as'] ? 'button' : 'a';
		$variant = sanitize_html_class( (string) $args['variant'] );
		$size    = sanitize_html_class( (string) $args['size'] );
		$icon    = (string) $args['icon'];
		$icon_position = 'before' === $args['icon_position'] ? 'before' : 'after';

		if ( '' === trim( $text ) ) {
			return;
		}

		$classes = array(
			'mwm-btn',
			'mwm-btn--' . $variant,
			'mwm-btn--' . $size,
		);

		if ( 'none' !== $icon ) {
			$classes[] = 'mwm-btn--has-icon';
			$classes[] = 'mwm-btn--icon-' . $icon_position;
		}

		if ( ! empty( $args['class'] ) ) {
			$extra_classes = preg_split( '/\s+/', trim( (string) $args['class'] ) );
			if ( is_array( $extra_classes ) ) {
				foreach ( $extra_classes as $extra_class ) {
					$sanitized_class = sanitize_html_class( $extra_class );
					if ( '' !== $sanitized_class ) {
						$classes[] = $sanitized_class;
					}
				}
			}
		}

		$attributes = array(
			'class' => implode( ' ', $classes ),
		);

		if ( 'a' === $tag ) {
			$attributes['href'] = esc_url( (string) $args['url'] );
		} else {
			$btn_type = (string) $args['type'];
			$attributes['type'] = in_array( $btn_type, array( 'button', 'submit', 'reset' ), true ) ? $btn_type : 'button';
			if ( ! empty( $args['disabled'] ) ) {
				$attributes['disabled'] = 'disabled';
			}
		}

		if ( ! empty( $args['data_attributes'] ) && is_array( $args['data_attributes'] ) ) {
			foreach ( $args['data_attributes'] as $data_key => $data_val ) {
				$safe_key = preg_replace( '/[^a-z0-9_-]/i', '', (string) $data_key );
				if ( '' === $safe_key ) {
					continue;
				}
				$attr_name = 'data-' . $safe_key;
				if ( filter_var( $data_val, FILTER_VALIDATE_URL ) ) {
					$attributes[ $attr_name ] = esc_url( (string) $data_val );
				} else {
					$attributes[ $attr_name ] = esc_attr( (string) $data_val );
				}
			}
		}

		if ( ! empty( $args['target'] ) ) {
			$attributes['target'] = sanitize_text_field( (string) $args['target'] );
		}

		$rel = trim( (string) $args['rel'] );
		if ( ! empty( $attributes['target'] ) && '_blank' === $attributes['target'] ) {
			$rel = trim( $rel . ' noopener noreferrer' );
		}
		if ( '' !== $rel ) {
			$attributes['rel'] = $rel;
		}

		if ( ! empty( $args['aria_label'] ) ) {
			$attributes['aria-label'] = sanitize_text_field( (string) $args['aria_label'] );
		}
		if ( ! empty( $args['aria_disabled'] ) ) {
			$attributes['aria-disabled'] = 'true';
		}

		$attrs_html = '';
		foreach ( $attributes as $key => $value ) {
			$attrs_html .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		$icon_svg = mwm_get_button_icon_svg( $icon );

		echo '<' . esc_html( $tag ) . $attrs_html . '>';
		if ( '' !== $icon_svg ) {
			echo 'before' === $icon_position ? '<span class="mwm-btn__icon" aria-hidden="true">' . $icon_svg . '</span>' : '';
		}
		echo '<span class="mwm-btn__label">' . esc_html( $text ) . '</span>';
		if ( '' !== $icon_svg ) {
			echo 'after' === $icon_position ? '<span class="mwm-btn__icon" aria-hidden="true">' . $icon_svg . '</span>' : '';
		}
		echo '</' . esc_html( $tag ) . '>';
	}
}
