import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link', 'zenyx/underline' ];

function normalizeHeadingMarkup( value ) {
	const raw = String( value ?? '' );
	return raw
		.replace( /<\/p>\s*<p[^>]*>/gi, '<br />' )
		.replace( /<p[^>]*>/gi, '' )
		.replace( /<\/p>/gi, '' );
}

const DEFAULT_LEVELS = Array.isArray( metadata.attributes?.levels?.default )
	? metadata.attributes.levels.default.map( ( item ) => ( { ...item } ) )
	: [];

function mergeLevels( raw ) {
	if ( ! Array.isArray( raw ) || ! raw.length ) {
		return DEFAULT_LEVELS.map( ( d ) => ( { ...d } ) );
	}
	return raw.map( ( item, i ) => ( {
		...DEFAULT_LEVELS[ i % DEFAULT_LEVELS.length ],
		...item,
	} ) );
}

function getPanelWrapClasses( variant ) {
	const v = variant === 'dark' || variant === 'accent' ? variant : 'light';
	return `mwm-levels-01__panel-surface mwm-levels-01__panel-surface--${ v } mwm-levels-01__panel-surface-inner relative flex min-h-[258px] w-full flex-col justify-between overflow-hidden`;
}

function getPanelTextClass( variant ) {
	const v = variant === 'dark' || variant === 'accent' ? variant : 'light';
	return v === 'light' ? 'text-protagonista' : 'text-white';
}

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', intro = '', levels: rawLevels = [] } = attributes;
	const levels = mergeLevels( rawLevels );

	const updateLevel = ( index, field, value ) => {
		const next = levels.map( ( row, i ) => {
			const base = { ...row };
			if ( i === index ) {
				return { ...base, [ field ]: value ?? '' };
			}
			return base;
		} );
		setAttributes( { levels: next } );
	};

	const addLevel = () => {
		const template = DEFAULT_LEVELS[ levels.length % DEFAULT_LEVELS.length ] || DEFAULT_LEVELS[ 0 ];
		setAttributes( { levels: [ ...levels, { ...template } ] } );
	};

	const removeLevel = ( index ) => {
		setAttributes( { levels: levels.filter( ( _, i ) => i !== index ) } );
	};

	const blockProps = useBlockProps( {
		className: 'mwm-levels-01 mwm-levels-01--editor-preview w-full bg-neutral-light py-[120px]',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Niveles', 'zenyx' ) } initialOpen={ true }>
					<Button variant="primary" onClick={ addLevel } style={ { marginBottom: '12px' } }>
						{ __( 'Anadir nivel', 'zenyx' ) }
					</Button>
					{ levels.map( ( row, index ) => (
						<div
							key={ `level-controls-${ index }` }
							style={ {
								marginBottom: '16px',
								paddingBottom: '12px',
								borderBottom: '1px solid #ddd',
							} }
						>
							<SelectControl
								label={ __( 'Color del panel', 'zenyx' ) }
								value={ row.panelVariant === 'dark' || row.panelVariant === 'accent' ? row.panelVariant : 'light' }
								options={ [
									{ label: __( 'Claro (neutral)', 'zenyx' ), value: 'light' },
									{ label: __( 'Oscuro (protagonista)', 'zenyx' ), value: 'dark' },
									{ label: __( 'Acento', 'zenyx' ), value: 'accent' },
								] }
								onChange={ ( value ) => updateLevel( index, 'panelVariant', value ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<Button variant="link" isDestructive onClick={ () => removeLevel( index ) }>
								{ __( 'Eliminar nivel', 'zenyx' ) }
							</Button>
						</div>
					) ) }
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps } data-dark="">
				<div className="mwm-max-1 flex flex-col gap-20">
					<header className="mwm-levels-01__header flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between lg:gap-6">
						<div className="mwm-levels-01__heading-wrap min-w-0 flex-1 lg:max-w-[636px]">
							<RichText
								tagName="h2"
								className="mwm-levels-01__heading text-[1.75rem] font-heading leading-[1.2] text-protagonista md:text-3xl lg:text-[40px]"
								value={ normalizeHeadingMarkup( heading ) }
								onChange={ ( value ) =>
									setAttributes( { heading: normalizeHeadingMarkup( value ) } )
								}
								placeholder={ __( 'Titular…', 'zenyx' ) }
								allowedFormats={ RICH_TEXT_FORMATS }
							/>
						</div>
						<div className="mwm-levels-01__intro-wrap min-w-0 w-full shrink-0 lg:max-w-[416px] lg:pr-6">
							<RichText
								tagName="div"
								className="mwm-levels-01__intro text-lg leading-normal text-protagonista lg:text-xl"
								value={ intro }
								onChange={ ( value ) => setAttributes( { intro: value ?? '' } ) }
								placeholder={ __( 'Introduccion…', 'zenyx' ) }
								allowedFormats={ RICH_TEXT_FORMATS }
							/>
						</div>
					</header>

					<div className="mwm-levels-01__rows flex flex-col gap-10">
						{ levels.map( ( level, index ) => {
							const textPanel = getPanelTextClass( level.panelVariant );
							return (
								<div
									key={ `level-row-${ index }` }
									className="mwm-levels-01__row flex flex-col gap-6 bg-white lg:flex-row lg:items-stretch lg:gap-6"
								>
									<div className="mwm-levels-01__panel-wrap flex min-h-0 min-w-0 flex-1 items-stretch p-6">
										<div className={ getPanelWrapClasses( level.panelVariant ) }>
											<div className="mwm-levels-01__panel-title relative z-20 px-5 pt-5">
												<RichText
													tagName="div"
													className={ `mwm-levels-01__panel-title-text text-xl font-medium uppercase leading-normal ${ textPanel }` }
													value={ level.levelTitle }
													onChange={ ( value ) => updateLevel( index, 'levelTitle', value ?? '' ) }
													placeholder={ __( 'Nivel…', 'zenyx' ) }
													allowedFormats={ RICH_TEXT_FORMATS }
												/>
											</div>
											<div className="mwm-levels-01__panel-price relative z-20 flex flex-col gap-1 px-5 pb-5 pr-[86px] pt-5">
												<RichText
													tagName="p"
													className={ `mwm-levels-01__panel-desde text-sm leading-normal ${ textPanel }` }
													value={ level.desdeLabel }
													onChange={ ( value ) => updateLevel( index, 'desdeLabel', value ?? '' ) }
													placeholder={ __( 'Desde', 'zenyx' ) }
													allowedFormats={ RICH_TEXT_FORMATS }
												/>
												<RichText
													tagName="p"
													className={ `mwm-levels-01__panel-amount text-xl font-medium leading-normal ${ textPanel }` }
													value={ level.price }
													onChange={ ( value ) => updateLevel( index, 'price', value ?? '' ) }
													placeholder="0€"
													allowedFormats={ RICH_TEXT_FORMATS }
												/>
											</div>
										</div>
									</div>

									<div className="mwm-levels-01__col mwm-levels-01__col--lead flex min-h-0 min-w-0 flex-1 flex-col gap-3 px-6 pb-6 pt-0 lg:px-0 lg:pb-6 lg:pt-6">
										<RichText
											tagName="p"
											className="mwm-levels-01__label text-base font-medium leading-normal text-acento"
											value={ level.paraQuienTitle }
											onChange={ ( value ) => updateLevel( index, 'paraQuienTitle', value ?? '' ) }
											placeholder={ __( 'Subtitulo…', 'zenyx' ) }
											allowedFormats={ RICH_TEXT_FORMATS }
										/>
										<RichText
											tagName="div"
											className="mwm-levels-01__lead text-xl font-medium leading-normal text-protagonista"
											value={ level.paraQuienBody }
											onChange={ ( value ) => updateLevel( index, 'paraQuienBody', value ?? '' ) }
											placeholder={ __( 'Texto destacado…', 'zenyx' ) }
											allowedFormats={ RICH_TEXT_FORMATS }
										/>
									</div>

									<div className="mwm-levels-01__col flex min-h-0 min-w-0 flex-1 flex-col gap-3 px-6 pb-6 pt-0 lg:px-0 lg:pb-6 lg:pt-6">
										<RichText
											tagName="p"
											className="mwm-levels-01__label text-base font-medium leading-normal text-acento"
											value={ level.situacionTitle }
											onChange={ ( value ) => updateLevel( index, 'situacionTitle', value ?? '' ) }
											placeholder={ __( 'Subtitulo…', 'zenyx' ) }
											allowedFormats={ RICH_TEXT_FORMATS }
										/>
										<RichText
											tagName="div"
											className="mwm-levels-01__body text-base leading-normal text-protagonista"
											value={ level.situacionBody }
											onChange={ ( value ) => updateLevel( index, 'situacionBody', value ?? '' ) }
											placeholder={ __( 'Texto…', 'zenyx' ) }
											allowedFormats={ RICH_TEXT_FORMATS }
										/>
									</div>

									<div className="mwm-levels-01__col flex min-h-0 min-w-0 flex-1 flex-col gap-3 px-6 pb-6 pt-0 lg:pr-6 lg:pt-6">
										<RichText
											tagName="p"
											className="mwm-levels-01__label text-base font-medium leading-normal text-acento"
											value={ level.objetivoTitle }
											onChange={ ( value ) => updateLevel( index, 'objetivoTitle', value ?? '' ) }
											placeholder={ __( 'Subtitulo…', 'zenyx' ) }
											allowedFormats={ RICH_TEXT_FORMATS }
										/>
										<RichText
											tagName="div"
											className="mwm-levels-01__body text-base leading-normal text-protagonista"
											value={ level.objetivoBody }
											onChange={ ( value ) => updateLevel( index, 'objetivoBody', value ?? '' ) }
											placeholder={ __( 'Texto…', 'zenyx' ) }
											allowedFormats={ RICH_TEXT_FORMATS }
										/>
									</div>
								</div>
							);
						} ) }
					</div>
				</div>
			</section>
		</>
	);
}
