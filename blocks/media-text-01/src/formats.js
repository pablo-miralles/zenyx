/**
 * Formato "Acento" para spans con color de marca en RichText.
 */
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';

const FORMAT_NAME = 'zenyx/accent';

const AccentIcon = (
	<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true">
		<circle cx="10" cy="10" r="8" fill="currentColor" opacity="0.35" />
		<circle cx="10" cy="10" r="4" fill="currentColor" />
	</svg>
);

function AccentEdit( { isActive, value, onChange } ) {
	return (
		<RichTextToolbarButton
			icon={ AccentIcon }
			title={ __( 'Acento', 'zenyx' ) }
			onClick={ () => {
				onChange(
					toggleFormat( value, {
						type: FORMAT_NAME,
					} )
				);
			} }
			isActive={ isActive }
		/>
	);
}

registerFormatType( FORMAT_NAME, {
	title: __( 'Acento', 'zenyx' ),
	tagName: 'span',
	className: 'text-acento',
	edit: AccentEdit,
} );
