import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link' ];

const emptyItem = () => ( {
	question: '',
	paragraphs: [],
} );

function normalizeItems( rawItems ) {
	if ( ! Array.isArray( rawItems ) || rawItems.length === 0 ) {
		return [ emptyItem() ];
	}
	return rawItems.map( ( row ) => {
		const question = row?.question ?? '';
		let paragraphs = Array.isArray( row?.paragraphs ) ? [ ...row.paragraphs ] : [];
		paragraphs = paragraphs.map( ( p ) => ( typeof p === 'string' ? p : '' ) );
		return { question, paragraphs };
	} );
}

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', items: rawItems = [] } = attributes;

	const items = normalizeItems( rawItems );

	const updateItem = ( index, field, value ) => {
		const next = items.map( ( row, i ) => {
			if ( i !== index ) {
				return { ...row };
			}
			return { ...row, [ field ]: value ?? '' };
		} );
		setAttributes( { items: next } );
	};

	const updateParagraph = ( itemIndex, pIndex, value ) => {
		const next = items.map( ( row, i ) => {
			if ( i !== itemIndex ) {
				return { ...row };
			}
			const paras = [ ...row.paragraphs ];
			paras[ pIndex ] = value ?? '';
			return { ...row, paragraphs: paras };
		} );
		setAttributes( { items: next } );
	};

	const addParagraph = ( itemIndex ) => {
		const next = items.map( ( row, i ) => {
			if ( i !== itemIndex ) {
				return { ...row };
			}
			return { ...row, paragraphs: [ ...row.paragraphs, '' ] };
		} );
		setAttributes( { items: next } );
	};

	const removeParagraph = ( itemIndex, pIndex ) => {
		const next = items.map( ( row, i ) => {
			if ( i !== itemIndex ) {
				return { ...row };
			}
			const paras = row.paragraphs.filter( ( _, j ) => j !== pIndex );
			return { ...row, paragraphs: paras };
		} );
		setAttributes( { items: next } );
	};

	const addItem = () => {
		setAttributes( { items: [ ...items, emptyItem() ] } );
	};

	const removeItem = ( index ) => {
		const next = items.filter( ( _, i ) => i !== index );
		setAttributes( { items: next.length ? next : [ emptyItem() ] } );
	};

	const blockProps = useBlockProps( {
		className:
			'mwm-accordion-01-editor w-full overflow-hidden bg-protagonista py-[120px] text-white',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Elementos FAQ', 'zenyx' ) } initialOpen={ true }>
					<Button
						variant="primary"
						onClick={ addItem }
						style={ { marginBottom: '12px' } }
						__next40pxDefaultSize
					>
						{ __( 'Añadir pregunta', 'zenyx' ) }
					</Button>
					{ items.map( ( _row, index ) => (
						<div
							key={ `acc-item-${ index }` }
							style={ {
								marginBottom: '16px',
								paddingBottom: '12px',
								borderBottom: '1px solid rgba(255,255,255,0.2)',
							} }
						>
							<Button
								variant="link"
								isDestructive
								onClick={ () => removeItem( index ) }
								disabled={ items.length <= 1 }
							>
								{ __( 'Eliminar esta pregunta', 'zenyx' ) }
							</Button>
						</div>
					) ) }
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps } data-light>
				<div className="mwm-max-1 flex flex-col gap-20">
					<div className="flex justify-center">
						<RichText
							tagName="h2"
							className="max-w-[636px] text-center font-heading text-[32px] leading-[1.2] text-white md:text-[40px]"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v ?? '' } ) }
							placeholder={ __( 'Titular de la sección…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
					</div>

					<div className="mx-auto flex w-full max-w-[856px] flex-col gap-6">
						{ items.map( ( item, index ) => (
							<div
								key={ `acc-row-${ index }` }
								className="border-b border-neutral-light pb-[18px]"
							>
								<div className="mb-3 flex items-start gap-3">
									<RichText
										tagName="h3"
										className="mwm-accordion-01__question min-w-0 flex-1 text-xl !font-body !font-medium leading-[1.2] text-white md:text-[20px]"
										value={ item.question }
										onChange={ ( v ) => updateItem( index, 'question', v ) }
										placeholder={ __( 'Pregunta…', 'zenyx' ) }
										allowedFormats={ RICH_TEXT_FORMATS }
									/>
									<span
										className="mt-0.5 h-6 w-6 shrink-0 text-[#FE7756]"
										aria-hidden="true"
									>
										<svg
											className="block h-6 w-6"
											width="24"
											height="24"
											viewBox="0 0 24 24"
											fill="none"
											xmlns="http://www.w3.org/2000/svg"
										>
											<rect
												x="1.5"
												y="1.5"
												width="21"
												height="21"
												stroke="currentColor"
												strokeWidth="1"
											/>
											<path
												d="M18 12H6"
												stroke="currentColor"
												strokeLinejoin="round"
											/>
											<path
												d="M12.0049 18.0059L12.0049 6.00586"
												stroke="currentColor"
												strokeLinejoin="round"
											/>
										</svg>
									</span>
								</div>

								<div className="pr-9">
									{ item.paragraphs.map( ( para, pIndex ) => (
										<div
											key={ `p-${ index }-${ pIndex }` }
											className="mb-3 last:mb-0"
										>
											<RichText
												tagName="div"
												className="mwm-accordion-01__paragraph text-base font-normal leading-[1.2] text-neutral-light"
												value={ para }
												onChange={ ( v ) =>
													updateParagraph( index, pIndex, v )
												}
												placeholder={ __( 'Respuesta…', 'zenyx' ) }
												allowedFormats={ RICH_TEXT_FORMATS }
											/>
											{ item.paragraphs.length > 1 && (
												<Button
													isSmall
													variant="link"
													isDestructive
													onClick={ () =>
														removeParagraph( index, pIndex )
													}
												>
													{ __( 'Eliminar párrafo', 'zenyx' ) }
												</Button>
											) }
										</div>
									) ) }
									<Button
										variant="secondary"
										onClick={ () => addParagraph( index ) }
										__next40pxDefaultSize
									>
										{ __( 'Añadir párrafo', 'zenyx' ) }
									</Button>
								</div>
							</div>
						) ) }
					</div>
				</div>
			</section>
		</>
	);
}
