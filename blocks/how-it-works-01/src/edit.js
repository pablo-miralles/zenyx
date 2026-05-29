import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link', 'zenyx/underline' ];

const DEFAULT_STEPS = [
	{
		label: '(01)',
		text: 'La situación actual de tu agencia (facturación, equipo, operativa)',
	},
	{
		label: '(02)',
		text: 'Tus principales cuellos de botella y oportunidades de mejora',
	},
	{
		label: '(03)',
		text: 'Qué nivel del programa es adecuado para tus retos y objetivos',
	},
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		heading = '',
		intro = '',
		steps: rawSteps = [],
		buttonText = '',
		buttonUrl = '',
		opensInNewTab = false,
	} = attributes;

	const steps = Array.isArray( rawSteps )
		? rawSteps.map( ( step, idx ) => ( {
				...DEFAULT_STEPS[ idx % DEFAULT_STEPS.length ],
				...step,
		  } ) )
		: DEFAULT_STEPS.map( ( s ) => ( { ...s } ) );

	const updateStep = ( index, field, value ) => {
		const next = steps.map( ( row, i ) => {
			const base = { label: row.label ?? '', text: row.text ?? '' };
			if ( i === index ) {
				return { ...base, [ field ]: value ?? '' };
			}
			return base;
		} );
		setAttributes( { steps: next } );
	};

	const addStep = () => {
		setAttributes( { steps: [ ...steps, { label: '', text: '' } ] } );
	};

	const removeStep = ( index ) => {
		setAttributes( { steps: steps.filter( ( _, i ) => i !== index ) } );
	};

	const blockProps = useBlockProps( {
		className:
			'mwm-how-it-works-01 w-full overflow-hidden bg-neutral-light py-[120px]',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Botón CTA', 'zenyx' ) } initialOpen={ true }>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Texto del botón', 'zenyx' ) }
						value={ buttonText }
						onChange={ ( value ) =>
							setAttributes( { buttonText: value ?? '' } )
						}
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'URL del botón', 'zenyx' ) }
						value={ buttonUrl }
						onChange={ ( value ) =>
							setAttributes( { buttonUrl: value ?? '' } )
						}
						type="url"
						placeholder="https://"
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Abrir en nueva pestaña', 'zenyx' ) }
						checked={ opensInNewTab }
						onChange={ ( value ) =>
							setAttributes( { opensInNewTab: value } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Pasos', 'zenyx' ) } initialOpen={ false }>
					<Button
						variant="primary"
						onClick={ addStep }
						style={ { marginBottom: '12px' } }
					>
						{ __( 'Añadir paso', 'zenyx' ) }
					</Button>
					{ steps.map( ( _row, index ) => (
						<div
							key={ `step-${ index }` }
							style={ {
								marginBottom: '16px',
								paddingBottom: '12px',
								borderBottom: '1px solid #ddd',
							} }
						>
							<Button
								variant="link"
								isDestructive
								onClick={ () => removeStep( index ) }
							>
								{ __( 'Eliminar paso', 'zenyx' ) }
							</Button>
						</div>
					) ) }
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps } data-dark="">
				<div className="mwm-max-1 flex flex-col gap-20 lg:gap-[80px]">
					<header className="mwm-how-it-works-01__header flex max-w-[634px] flex-col gap-6">
						<RichText
							tagName="h2"
							className="mwm-how-it-works-01__heading text-[1.75rem] font-heading leading-[1.2] text-protagonista md:text-3xl lg:text-[40px]"
							value={ heading }
							onChange={ ( value ) =>
								setAttributes( { heading: value ?? '' } )
							}
							placeholder={ __( 'Titular…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
						<RichText
							tagName="div"
							className="mwm-how-it-works-01__intro pr-0 text-left text-base leading-[1.2] text-protagonista lg:pr-[110px] lg:text-xl lg:leading-[1.2]"
							value={ intro }
							onChange={ ( value ) =>
								setAttributes( { intro: value ?? '' } )
							}
							placeholder={ __( 'Introducción…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
					</header>

					<div className="mwm-how-it-works-01__row flex flex-col gap-10 lg:flex-row lg:items-end lg:gap-6">
						{ steps.map( ( step, index ) => (
							<div
								key={ `step-col-${ index }` }
								className="mwm-how-it-works-01__step flex min-w-0 flex-1 flex-col gap-6 pt-3"
							>
								<div className="mwm-how-it-works-01__step-head flex items-center gap-6 self-stretch">
									<RichText
										tagName="span"
										className="mwm-how-it-works-01__step-label shrink-0 text-left font-heading text-base text-acento"
										value={ step.label }
										onChange={ ( value ) =>
											updateStep( index, 'label', value )
										}
										placeholder="(01)"
										allowedFormats={ [] }
									/>
									<span
										className="mwm-how-it-works-01__step-line min-h-px min-w-0 flex-1 bg-acento"
										aria-hidden="true"
									/>
								</div>
								<RichText
									tagName="div"
									className="mwm-how-it-works-01__step-text text-left text-base leading-[1.2] text-protagonista lg:text-xl lg:leading-[1.2]"
									value={ step.text }
									onChange={ ( value ) =>
										updateStep( index, 'text', value )
									}
									placeholder={ __( 'Texto del paso…', 'zenyx' ) }
									allowedFormats={ RICH_TEXT_FORMATS }
								/>
							</div>
						) ) }

						<div className="mwm-how-it-works-01__cta-col flex min-w-0 flex-1 flex-col justify-start gap-2.5 self-stretch lg:min-h-0">
							{ buttonText ? (
								<a
									href={ buttonUrl || '#' }
									className="mwm-how-it-works-01__cta mwm-btn mwm-btn--primary mwm-btn--md"
									target={ opensInNewTab ? '_blank' : undefined }
									rel={
										opensInNewTab
											? 'noopener noreferrer'
											: undefined
									}
									onClick={ ( event ) => event.preventDefault() }
								>
									<span className="mwm-btn__label">{ buttonText }</span>
								</a>
							) : null }
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
