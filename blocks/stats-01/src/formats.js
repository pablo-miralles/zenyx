/**
 * Subrayado con botón en la barra del RichText.
 * core/underline en Core no pinta control en la toolbar (solo eventos de teclado).
 */
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';

const FORMAT_NAME = 'zenyx/underline';

const UnderlineIcon = (
	<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true">
		<path
			fill="currentColor"
			d="M12 5v6.5c0 1.4-.9 2.5-2.5 2.5S7 12.9 7 11.5V5h1.5v6.5c0 .6.4 1 1 1s1-.4 1-1V5H12zm-9 13h14v-1.5H3V18z"
		/>
	</svg>
);

function UnderlineEdit( { isActive, value, onChange } ) {
	return (
		<RichTextToolbarButton
			icon={ UnderlineIcon }
			title={ __( 'Subrayado', 'zenyx' ) }
			onClick={ () => {
				onChange(
					toggleFormat( value, {
						type: FORMAT_NAME,
						title: __( 'Subrayado', 'zenyx' ),
					} )
				);
			} }
			isActive={ isActive }
		/>
	);
}

registerFormatType( FORMAT_NAME, {
	title: __( 'Subrayado', 'zenyx' ),
	tagName: 'u',
	className: null,
	edit: UnderlineEdit,
} );
