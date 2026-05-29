import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link', 'zenyx/underline' ];
const ALLOWED_IMAGE_TYPES = [ 'image' ];

const DEFAULT_EJECUCION_ITEMS = [
	{ id: 'ej-1', step: '(01)', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
	{ id: 'ej-2', step: '(02)', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
	{ id: 'ej-3', step: '(03)', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
	{ id: 'ej-4', step: '(04)', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
];

const DEFAULT_RESULTADOS_ITEMS = [
	{ id: 'res-1', value: '+20%', label: 'Lorem ipsum dolor sit amet' },
	{ id: 'res-2', value: '+35%', label: 'Lorem ipsum dolor sit amet' },
	{ id: 'res-3', value: '+20h', label: 'Lorem ipsum dolor sit amet' },
	{ id: 'res-4', value: '+20h', label: 'Lorem ipsum dolor sit amet' },
];

function EditorButtonPreview( { text, url, variant } ) {
	if ( ! text?.trim() ) {
		return null;
	}
	const isPlay = variant === 'play-outline';
	const btnClass = `mwm-btn mwm-btn--${ variant } mwm-btn--md mwm-btn--has-icon ${ isPlay ? 'mwm-btn--icon-before' : 'mwm-btn--icon-after' }`;
	const iconMarkup = isPlay ? (
		<span className="mwm-btn__icon" aria-hidden="true">
			<svg className="mwm-btn__icon-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M2.61914 17.0702V2.92947C2.6239 2.6531 2.70433 2.38331 2.85167 2.14944C2.99901 1.91557 3.20763 1.72655 3.45485 1.60293C3.70174 1.46344 3.98048 1.39014 4.26404 1.39014C4.5476 1.39014 4.82635 1.46344 5.07322 1.60293L16.5477 8.64681C16.7974 8.76709 17.008 8.95539 17.1555 9.19005C17.3029 9.42472 17.381 9.69622 17.381 9.97334C17.381 10.2505 17.3029 10.522 17.1555 10.7566C17.008 10.9913 16.7974 11.1796 16.5477 11.2999L5.07322 18.3968C4.82635 18.5363 4.5476 18.6097 4.26404 18.6097C3.98048 18.6097 3.70174 18.5363 3.45485 18.3968C3.20763 18.2733 2.99901 18.0842 2.85167 17.8504C2.70433 17.6165 2.6239 17.3467 2.61914 17.0702Z" fill="currentColor" />
			</svg>
		</span>
	) : (
		<span className="mwm-btn__icon" aria-hidden="true">
			<svg className="mwm-btn__icon-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.625 10H19.375" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
				<path d="M10.625 18.75L19.375 10L10.625 1.25" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
			</svg>
		</span>
	);

	if ( url?.trim() ) {
		return (
			<a href={ url } className={ btnClass } onClick={ ( event ) => event.preventDefault() }>
				{ isPlay && iconMarkup }
				<span className="mwm-btn__label">{ text }</span>
				{ ! isPlay && iconMarkup }
			</a>
		);
	}

	return (
		<button type="button" className={ btnClass } disabled>
			{ isPlay && iconMarkup }
			<span className="mwm-btn__label">{ text }</span>
			{ ! isPlay && iconMarkup }
		</button>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const {
		retosTitle = '',
		retosTextLeft = '',
		retosTextRight = '',
		ejecucionTitle = '',
		ejecucionItems: rawEjecucionItems = [],
		resultadosTitle = '',
		resultadoItems: rawResultadoItems = [],
		cardPretitle = '',
		cardTitle = '',
		cardVideoUrl = '',
		cardPlayLabel = '',
		cardImageUrl = '',
		cardImageAlt = '',
	} = attributes;

	const ejecucionItems = Array.isArray( rawEjecucionItems ) && rawEjecucionItems.length ? rawEjecucionItems : DEFAULT_EJECUCION_ITEMS;
	const resultadoItems = Array.isArray( rawResultadoItems ) && rawResultadoItems.length ? rawResultadoItems : DEFAULT_RESULTADOS_ITEMS;

	const updateEjecucionItem = ( index, field, value ) => {
		const next = [ ...ejecucionItems ];
		next[ index ] = { ...next[ index ], [ field ]: value ?? '' };
		setAttributes( { ejecucionItems: next } );
	};

	const updateResultadoItem = ( index, field, value ) => {
		const next = [ ...resultadoItems ];
		next[ index ] = { ...next[ index ], [ field ]: value ?? '' };
		setAttributes( { resultadoItems: next } );
	};

	const addEjecucionItem = () => {
		setAttributes( {
			ejecucionItems: [
				...ejecucionItems,
				{ id: `ej-${ Date.now() }`, step: `(0${ ejecucionItems.length + 1 })`, text: '' },
			],
		} );
	};

	const removeEjecucionItem = ( index ) => {
		setAttributes( { ejecucionItems: ejecucionItems.filter( ( _, idx ) => idx !== index ) } );
	};

	const addResultadoItem = () => {
		setAttributes( {
			resultadoItems: [
				...resultadoItems,
				{ id: `res-${ Date.now() }`, value: '', label: '' },
			],
		} );
	};

	const removeResultadoItem = ( index ) => {
		setAttributes( { resultadoItems: resultadoItems.filter( ( _, idx ) => idx !== index ) } );
	};

	const moveItem = ( items, from, to, key ) => {
		if ( to < 0 || to >= items.length ) {
			return;
		}
		const next = [ ...items ];
		const tmp = next[ from ];
		next[ from ] = next[ to ];
		next[ to ] = tmp;
		setAttributes( { [ key ]: next } );
	};

	const blockProps = useBlockProps( {
		className: 'mwm-caso-exito-content-01 w-full bg-protagonista py-[120px]',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Card final', 'zenyx' ) } initialOpen={ true }>
					<Notice status="info" isDismissible={ false }>
						{ __( 'Estos campos sobrescriben los datos del caso actual en la card. Si los dejas vacios, se usan los valores del caso por defecto.', 'zenyx' ) }
					</Notice>
					<TextControl
						label={ __( 'Pretitulo', 'zenyx' ) }
						value={ cardPretitle }
						onChange={ ( value ) => setAttributes( { cardPretitle: value ?? '' } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={ __( 'Titulo', 'zenyx' ) }
						value={ cardTitle }
						onChange={ ( value ) => setAttributes( { cardTitle: value ?? '' } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={ __( 'URL de video', 'zenyx' ) }
						value={ cardVideoUrl }
						onChange={ ( value ) => setAttributes( { cardVideoUrl: value ?? '' } ) }
						type="url"
						placeholder="https://"
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={ __( 'Texto boton Play', 'zenyx' ) }
						value={ cardPlayLabel }
						onChange={ ( value ) => setAttributes( { cardPlayLabel: value ?? '' } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={ __( 'Alt de imagen', 'zenyx' ) }
						value={ cardImageAlt }
						onChange={ ( value ) => setAttributes( { cardImageAlt: value ?? '' } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									cardImageUrl: media?.url || '',
									cardImageAlt: media?.alt || cardImageAlt,
								} )
							}
							allowedTypes={ ALLOWED_IMAGE_TYPES }
							render={ ( { open } ) => (
								<div className="mt-3">
									<Button variant={ cardImageUrl ? 'secondary' : 'primary' } onClick={ open }>
										{ cardImageUrl ? __( 'Reemplazar imagen', 'zenyx' ) : __( 'Seleccionar imagen', 'zenyx' ) }
									</Button>
									{ cardImageUrl && (
										<Button
											variant="link"
											isDestructive
											onClick={ () => setAttributes( { cardImageUrl: '' } ) }
											style={ { marginLeft: '8px' } }
										>
											{ __( 'Eliminar', 'zenyx' ) }
										</Button>
									) }
								</div>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps } data-light="">
				<div className="mwm-max-1 flex flex-col gap-20">
					<div className="mwm-caso-exito-content-01__retos grid grid-cols-1 gap-6 lg:grid-cols-2">
						<div className="mwm-caso-exito-content-01__retos-title-wrap">
							<RichText
								tagName="h2"
								className="mwm-caso-exito-content-01__section-title text-[32px] font-heading leading-[1.2] text-white"
								value={ retosTitle }
								onChange={ ( value ) => setAttributes( { retosTitle: value ?? '' } ) }
								placeholder={ __( 'Retos...', 'zenyx' ) }
								allowedFormats={ RICH_TEXT_FORMATS }
							/>
						</div>
						<div className="mwm-caso-exito-content-01__retos-texts flex flex-col gap-12 pt-12">
							<div className="grid grid-cols-1 gap-6 md:grid-cols-2">
								<div className="min-h-[171px]">
									<RichText
										tagName="p"
										className="text-base leading-[1.2] text-neutral-light"
										value={ retosTextLeft }
										onChange={ ( value ) => setAttributes( { retosTextLeft: value ?? '' } ) }
										placeholder={ __( 'Texto reto izquierda...', 'zenyx' ) }
										allowedFormats={ RICH_TEXT_FORMATS }
									/>
								</div>
								<div className="hidden min-h-[171px] md:block" aria-hidden="true" />
							</div>
							<div className="grid grid-cols-1 gap-6 md:grid-cols-2">
								<div className="hidden min-h-[171px] md:block" aria-hidden="true" />
								<div className="min-h-[171px]">
									<RichText
										tagName="p"
										className="text-base leading-[1.2] text-neutral-light"
										value={ retosTextRight }
										onChange={ ( value ) => setAttributes( { retosTextRight: value ?? '' } ) }
										placeholder={ __( 'Texto reto derecha...', 'zenyx' ) }
										allowedFormats={ RICH_TEXT_FORMATS }
									/>
								</div>
							</div>
						</div>
					</div>

					<div className="mwm-caso-exito-content-01__ejecucion grid grid-cols-1 gap-6 lg:grid-cols-2">
						<div className="mwm-caso-exito-content-01__ejecucion-title-wrap">
							<RichText
								tagName="h2"
								className="mwm-caso-exito-content-01__section-title text-[32px] font-heading leading-[1.2] text-white"
								value={ ejecucionTitle }
								onChange={ ( value ) => setAttributes( { ejecucionTitle: value ?? '' } ) }
								placeholder={ __( 'Ejecucion...', 'zenyx' ) }
								allowedFormats={ RICH_TEXT_FORMATS }
							/>
						</div>
						<div className="mwm-caso-exito-content-01__ejecucion-items flex flex-col gap-12 pt-12">
							{ ejecucionItems.map( ( item, index ) => (
								<div key={ item?.id || `ej-${ index }` } className="rounded border border-neutral-light/30 p-4">
									<RichText
										tagName="p"
										className="text-2xl leading-[1.2] text-acento"
										value={ item?.step || '' }
										onChange={ ( value ) => updateEjecucionItem( index, 'step', value ) }
										placeholder={ __( '(01)', 'zenyx' ) }
										allowedFormats={ RICH_TEXT_FORMATS }
									/>
									<RichText
										tagName="p"
										className="mt-4 text-base leading-[1.2] text-neutral-light"
										value={ item?.text || '' }
										onChange={ ( value ) => updateEjecucionItem( index, 'text', value ) }
										placeholder={ __( 'Descripcion...', 'zenyx' ) }
										allowedFormats={ RICH_TEXT_FORMATS }
									/>
									<div className="mt-4 flex gap-2">
										<Button
											size="compact"
											variant="secondary"
											onClick={ () => moveItem( ejecucionItems, index, index - 1, 'ejecucionItems' ) }
											disabled={ index === 0 }
										>
											{ __( 'Subir', 'zenyx' ) }
										</Button>
										<Button
											size="compact"
											variant="secondary"
											onClick={ () => moveItem( ejecucionItems, index, index + 1, 'ejecucionItems' ) }
											disabled={ index === ejecucionItems.length - 1 }
										>
											{ __( 'Bajar', 'zenyx' ) }
										</Button>
										<Button
											size="compact"
											variant="tertiary"
											isDestructive
											onClick={ () => removeEjecucionItem( index ) }
											disabled={ ejecucionItems.length <= 1 }
										>
											{ __( 'Eliminar', 'zenyx' ) }
										</Button>
									</div>
								</div>
							) ) }
							<Button variant="primary" onClick={ addEjecucionItem }>
								{ __( 'Agregar paso', 'zenyx' ) }
							</Button>
						</div>
					</div>

					<div className="mwm-caso-exito-content-01__resultados flex flex-col gap-10">
						<RichText
							tagName="h2"
							className="mwm-caso-exito-content-01__section-title text-[32px] font-heading leading-[1.2] text-acento"
							value={ resultadosTitle }
							onChange={ ( value ) => setAttributes( { resultadosTitle: value ?? '' } ) }
							placeholder={ __( 'Resultados...', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
						<div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
							{ resultadoItems.map( ( item, index ) => (
								<div key={ item?.id || `res-${ index }` } className="rounded border border-neutral-light/30 p-4">
									<RichText
										tagName="p"
										className="text-5xl leading-none text-white"
										value={ item?.value || '' }
										onChange={ ( value ) => updateResultadoItem( index, 'value', value ) }
										placeholder={ __( '+20%', 'zenyx' ) }
										allowedFormats={ RICH_TEXT_FORMATS }
									/>
									<RichText
										tagName="p"
										className="mt-4 text-xl leading-[1.2] text-neutral-light"
										value={ item?.label || '' }
										onChange={ ( value ) => updateResultadoItem( index, 'label', value ) }
										placeholder={ __( 'Descripcion...', 'zenyx' ) }
										allowedFormats={ RICH_TEXT_FORMATS }
									/>
									<div className="mt-4 flex gap-2">
										<Button
											size="compact"
											variant="secondary"
											onClick={ () => moveItem( resultadoItems, index, index - 1, 'resultadoItems' ) }
											disabled={ index === 0 }
										>
											{ __( 'Subir', 'zenyx' ) }
										</Button>
										<Button
											size="compact"
											variant="secondary"
											onClick={ () => moveItem( resultadoItems, index, index + 1, 'resultadoItems' ) }
											disabled={ index === resultadoItems.length - 1 }
										>
											{ __( 'Bajar', 'zenyx' ) }
										</Button>
										<Button
											size="compact"
											variant="tertiary"
											isDestructive
											onClick={ () => removeResultadoItem( index ) }
											disabled={ resultadoItems.length <= 1 }
										>
											{ __( 'Eliminar', 'zenyx' ) }
										</Button>
									</div>
								</div>
							) ) }
						</div>
						<Button className="w-fit" variant="primary" onClick={ addResultadoItem }>
							{ __( 'Agregar resultado', 'zenyx' ) }
						</Button>
					</div>

					<div className="mwm-caso-exito-content-01__card relative flex min-h-[300px] overflow-hidden lg:min-h-[408px]">
						{ cardImageUrl ? (
							<div className="absolute inset-0">
								<img src={ cardImageUrl } alt={ cardImageAlt || '' } className="h-full w-full object-cover" />
							</div>
						) : (
							<div className="absolute inset-0 bg-protagonista" />
						) }
						<div className="absolute inset-0 bg-black/30 backdrop-blur-[20px]" />
						<div className="relative z-10 flex min-h-[inherit] w-full flex-col justify-between gap-5 p-5">
							<div className="flex flex-col gap-3">
								<p className="font-body text-base font-medium uppercase leading-[1.2] text-neutral-light">
									{ cardPretitle }
								</p>
								<p className="font-body text-base font-normal uppercase leading-[1.2] text-white">
									{ cardTitle }
								</p>
							</div>
							<div className="flex flex-wrap items-center gap-4">
								<EditorButtonPreview text={ cardPlayLabel } url={ cardVideoUrl } variant="play-outline" />
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
