<?php
/**
 * Degradado decorativo (PNG del tema), capa absolute; no es contenido editable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$degradado_src = get_template_directory_uri() . '/assets/images/media-text-02-degradado.png';
?>
<div class="mwm-media-text-02__left-deco pointer-events-none absolute inset-0 z-0 overflow-hidden" aria-hidden="true">
	<img
		class="mwm-media-text-02__left-deco-img"
		src="<?php echo esc_url( $degradado_src ); ?>"
		alt=""
		loading="lazy"
		decoding="async"
	/>
</div>
