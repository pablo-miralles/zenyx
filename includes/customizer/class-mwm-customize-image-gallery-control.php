<?php
/**
 * Customizer: galería de imágenes (IDs ilimitados).
 *
 * @package zenyx
 */

if ( ! class_exists( 'MWM_Customize_Image_Gallery_Control' ) && class_exists( 'WP_Customize_Control' ) ) {
	/**
	 * Control de galería para el Customizer.
	 */
	class MWM_Customize_Image_Gallery_Control extends WP_Customize_Control {
		/**
		 * @var string
		 */
		public $type = 'mwm_image_gallery';

		/**
		 * Enqueue scripts and styles.
		 */
		public function enqueue() {
			$dir = get_template_directory();
			$uri = get_template_directory_uri();

			$js = $dir . '/assets/js/customize-image-gallery.js';
			if ( file_exists( $js ) ) {
				wp_enqueue_script(
					'mwm-customize-image-gallery',
					$uri . '/assets/js/customize-image-gallery.js',
					array( 'jquery', 'jquery-ui-sortable', 'customize-controls', 'media-views' ),
					(string) filemtime( $js ),
					true
				);
				wp_localize_script(
					'mwm-customize-image-gallery',
					'mwmCustomizeGallery',
					array(
						'emptyLabel'   => __( 'No hay imágenes seleccionadas.', THEME_TEXT_DOMAIN ),
						'removeLabel'  => __( 'Quitar imagen', THEME_TEXT_DOMAIN ),
						'frameTitle'   => __( 'Imágenes del carrusel', THEME_TEXT_DOMAIN ),
						'frameButton'  => __( 'Usar estas imágenes', THEME_TEXT_DOMAIN ),
						'reorderHint'  => __( 'Arrastra las imágenes para cambiar el orden', THEME_TEXT_DOMAIN ),
					)
				);
			}

			$css = $dir . '/assets/css/customize-image-gallery.css';
			if ( file_exists( $css ) ) {
				wp_enqueue_style(
					'mwm-customize-image-gallery',
					$uri . '/assets/css/customize-image-gallery.css',
					array(),
					(string) filemtime( $css )
				);
			}

			wp_enqueue_media();
		}

		/**
		 * Render control content.
		 */
		public function render_content() {
			if ( empty( $this->label ) ) {
				return;
			}

			$ids = function_exists( 'mwm_parse_image_ids_json' )
				? mwm_parse_image_ids_json( $this->value() )
				: array();

			$json_value = wp_json_encode( $ids );
			if ( false === $json_value ) {
				$json_value = '[]';
			}
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<input
				type="hidden"
				<?php $this->link(); ?>
				value="<?php echo esc_attr( $json_value ); ?>"
				class="mwm-customize-gallery-input"
			/>
			<div class="mwm-customize-gallery-preview" aria-live="polite">
				<?php $this->render_thumbnails( $ids ); ?>
			</div>
			<div class="mwm-customize-gallery-actions">
				<button type="button" class="button button-secondary mwm-customize-gallery-select">
					<?php esc_html_e( 'Añadir / editar imágenes', THEME_TEXT_DOMAIN ); ?>
				</button>
				<button type="button" class="button-link mwm-customize-gallery-clear"<?php echo empty( $ids ) ? ' hidden' : ''; ?>>
					<?php esc_html_e( 'Quitar todas', THEME_TEXT_DOMAIN ); ?>
				</button>
			</div>
			<?php
		}

		/**
		 * @param array<int> $ids Attachment IDs.
		 */
		private function render_thumbnails( array $ids ) {
			if ( empty( $ids ) ) {
				echo '<p class="mwm-customize-gallery-empty">' . esc_html__( 'No hay imágenes seleccionadas.', THEME_TEXT_DOMAIN ) . '</p>';
				return;
			}

			echo '<p class="mwm-customize-gallery-reorder-hint">' . esc_html__( 'Arrastra las imágenes para cambiar el orden', THEME_TEXT_DOMAIN ) . '</p>';
			echo '<ul class="mwm-customize-gallery-list">';
			foreach ( $ids as $id ) {
				$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
				if ( ! $thumb ) {
					continue;
				}
				printf(
					'<li class="mwm-customize-gallery-item" data-id="%1$d" title="%4$s"><span class="mwm-customize-gallery-handle" aria-hidden="true"></span><img src="%2$s" alt="" width="60" height="60" draggable="false" /><button type="button" class="button-link mwm-customize-gallery-remove" data-id="%1$d" aria-label="%3$s">&times;</button></li>',
					(int) $id,
					esc_url( $thumb ),
					esc_attr__( 'Quitar imagen', THEME_TEXT_DOMAIN ),
					esc_attr__( 'Arrastra para reordenar', THEME_TEXT_DOMAIN )
				);
			}
			echo '</ul>';
		}
	}
}
