import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link', 'zenyx/underline' ];

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', paragraph1 = '', paragraph2 = '' } = attributes;

	const blockProps = useBlockProps( {
		className: 'mwm-text-02 w-full bg-protagonista py-[120px]',
	} );

	return (
		<section { ...blockProps } data-light="">
			<div className="mwm-max-1 flex flex-col items-center">
				<div className="flex w-full max-w-[636px] flex-col items-start gap-20">
					<RichText
						tagName="h2"
						className="mwm-text-02__heading w-full text-left text-[40px] font-heading leading-[1.2] text-neutral-light"
						value={ heading }
						onChange={ ( value ) => setAttributes( { heading: value ?? '' } ) }
						placeholder={ __( 'Titular…', 'zenyx' ) }
						allowedFormats={ RICH_TEXT_FORMATS }
					/>
					<div className="flex w-full flex-col gap-6">
						<RichText
							tagName="div"
							className="mwm-text-02__paragraph w-full text-left text-xl leading-[1.2] text-neutral-light"
							value={ paragraph1 }
							onChange={ ( value ) => setAttributes( { paragraph1: value ?? '' } ) }
							placeholder={ __( 'Primer párrafo…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
						<RichText
							tagName="div"
							className="mwm-text-02__paragraph w-full text-left text-xl leading-[1.2] text-neutral-light"
							value={ paragraph2 }
							onChange={ ( value ) => setAttributes( { paragraph2: value ?? '' } ) }
							placeholder={ __( 'Segundo párrafo…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
					</div>
				</div>
			</div>
		</section>
	);
}
