import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ALLOWED_IMAGE_TYPES = [ 'image' ];
const ALLOWED_VIDEO_TYPES = [ 'video' ];

const DEFAULT_BREADCRUMBS = [
	{ label: 'Home', url: '/' },
	{ label: 'Quienes somos', url: '' },
];

function MediaPanel( {
	mediaType,
	imageId,
	imageUrl,
	videoId,
	videoUrl,
	onChangeType,
	onSelectImage,
	onRemoveImage,
	onSelectVideo,
	onRemoveVideo,
} ) {
	return (
		<PanelBody title={ __( 'Media de fondo', 'zenyx' ) } initialOpen={ false }>
			<SelectControl
				label={ __( 'Tipo de media', 'zenyx' ) }
				value={ mediaType }
				options={ [
					{ label: __( 'Imagen', 'zenyx' ), value: 'image' },
					{ label: __( 'Video', 'zenyx' ), value: 'video' },
				] }
				onChange={ onChangeType }
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			{ mediaType === 'image' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ onSelectImage }
						allowedTypes={ ALLOWED_IMAGE_TYPES }
						value={ imageId }
						render={ ( { open } ) => (
							<>
								{ imageUrl && (
									<img
										src={ imageUrl }
										alt=""
										style={ { width: '100%', marginBottom: '8px', objectFit: 'cover' } }
									/>
								) }
								<Button variant={ imageId ? 'secondary' : 'primary' } onClick={ open }>
									{ imageId ? __( 'Reemplazar imagen', 'zenyx' ) : __( 'Seleccionar imagen', 'zenyx' ) }
								</Button>
								{ imageId > 0 && (
									<Button
										variant="link"
										isDestructive
										onClick={ onRemoveImage }
										style={ { marginLeft: '8px' } }
									>
										{ __( 'Eliminar', 'zenyx' ) }
									</Button>
								) }
							</>
						) }
					/>
				</MediaUploadCheck>
			) }
			{ mediaType === 'video' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ onSelectVideo }
						allowedTypes={ ALLOWED_VIDEO_TYPES }
						value={ videoId }
						render={ ( { open } ) => (
							<>
								{ videoUrl && (
									<video src={ videoUrl } style={ { width: '100%', marginBottom: '8px' } } controls muted />
								) }
								<Button variant={ videoId ? 'secondary' : 'primary' } onClick={ open }>
									{ videoId ? __( 'Reemplazar video', 'zenyx' ) : __( 'Seleccionar video', 'zenyx' ) }
								</Button>
								{ videoId > 0 && (
									<Button
										variant="link"
										isDestructive
										onClick={ onRemoveVideo }
										style={ { marginLeft: '8px' } }
									>
										{ __( 'Eliminar', 'zenyx' ) }
									</Button>
								) }
							</>
						) }
					/>
				</MediaUploadCheck>
			) }
		</PanelBody>
	);
}

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		backgroundMediaType = 'image',
		backgroundImageId = 0,
		backgroundImageUrl = '',
		backgroundImageAlt = '',
		backgroundVideoId = 0,
		backgroundVideoUrl = '',
		showBreadcrumbs = true,
		breadcrumbs = DEFAULT_BREADCRUMBS,
		showDecorativeSvg = true,
		heading = '',
		description = '',
	} = attributes;

	const safeBreadcrumbs = Array.isArray( breadcrumbs ) && breadcrumbs.length ? breadcrumbs : DEFAULT_BREADCRUMBS;
	const hasBackgroundMedia =
		backgroundMediaType === 'video'
			? '' !== String( backgroundVideoUrl || '' ).trim()
			: '' !== String( backgroundImageUrl || '' ).trim();

	const svgIdSafe = String( clientId || 'hero03' ).replace( /[^a-zA-Z0-9_-]/g, '' );
	const gradientId = `hero03-paint-${ svgIdSafe }`;
	const clipId = `hero03-clip-${ svgIdSafe }`;

	const updateBreadcrumb = ( index, field, value ) => {
		const next = [ ...safeBreadcrumbs ];
		next[ index ] = {
			...( next[ index ] || { label: '', url: '' } ),
			[ field ]: value ?? '',
		};
		setAttributes( { breadcrumbs: next } );
	};

	const addBreadcrumb = () => {
		setAttributes( {
			breadcrumbs: [ ...safeBreadcrumbs, { label: __( 'Nuevo item', 'zenyx' ), url: '' } ],
		} );
	};

	const removeBreadcrumb = ( index ) => {
		if ( safeBreadcrumbs.length <= 1 ) {
			return;
		}
		setAttributes( { breadcrumbs: safeBreadcrumbs.filter( ( _, i ) => i !== index ) } );
	};

	const blockProps = useBlockProps( {
		className: 'mwm-hero-03 relative isolate w-full overflow-hidden',
		style: { paddingTop: 'calc(var(--header-height, 68px))' },
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Visibilidad', 'zenyx' ) } initialOpen={ true }>
					<ToggleControl
						label={ __( 'Mostrar breadcrumbs', 'zenyx' ) }
						checked={ !! showBreadcrumbs }
						onChange={ ( value ) => setAttributes( { showBreadcrumbs: !! value } ) }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Mostrar SVG decorativo', 'zenyx' ) }
						checked={ !! showDecorativeSvg }
						onChange={ ( value ) => setAttributes( { showDecorativeSvg: !! value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody title={ __( 'Breadcrumbs', 'zenyx' ) } initialOpen={ false }>
					{ safeBreadcrumbs.map( ( crumb, index ) => (
						<div key={ `crumb-${ index }` } style={ { marginBottom: '12px' } }>
							<TextControl
								label={ __( 'Texto', 'zenyx' ) }
								value={ crumb?.label || '' }
								onChange={ ( value ) => updateBreadcrumb( index, 'label', value ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{ index === 0 && (
								<TextControl
									label={ __( 'URL del primer item', 'zenyx' ) }
									help={ __( 'Solo el primer elemento del breadcrumb es un enlace.', 'zenyx' ) }
									value={ crumb?.url || '' }
									onChange={ ( value ) => updateBreadcrumb( index, 'url', value ) }
									type="url"
									placeholder="https://"
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							) }
							<Button
								variant="link"
								isDestructive
								onClick={ () => removeBreadcrumb( index ) }
								disabled={ safeBreadcrumbs.length <= 1 }
							>
								{ __( 'Eliminar item', 'zenyx' ) }
							</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addBreadcrumb }>
						{ __( 'Agregar item', 'zenyx' ) }
					</Button>
				</PanelBody>

				<MediaPanel
					mediaType={ backgroundMediaType }
					imageId={ backgroundImageId }
					imageUrl={ backgroundImageUrl }
					videoId={ backgroundVideoId }
					videoUrl={ backgroundVideoUrl }
					onChangeType={ ( value ) => setAttributes( { backgroundMediaType: value } ) }
					onSelectImage={ ( media ) =>
						setAttributes( {
							backgroundImageId: media.id || 0,
							backgroundImageUrl: media.url || '',
							backgroundImageAlt: media.alt || '',
						} )
					}
					onRemoveImage={ () =>
						setAttributes( { backgroundImageId: 0, backgroundImageUrl: '', backgroundImageAlt: '' } )
					}
					onSelectVideo={ ( media ) =>
						setAttributes( {
							backgroundVideoId: media.id || 0,
							backgroundVideoUrl: media.url || '',
						} )
					}
					onRemoveVideo={ () => setAttributes( { backgroundVideoId: 0, backgroundVideoUrl: '' } ) }
				/>
			</InspectorControls>

			<section { ...blockProps } data-dark="">
				<div className="mwm-hero-03__bg absolute inset-0 -z-20 overflow-hidden" aria-hidden="true">
					{ hasBackgroundMedia && backgroundMediaType === 'video' && (
						<video className="mwm-hero-03__bg-media h-full w-full object-cover" autoPlay muted loop playsInline>
							<source src={ backgroundVideoUrl } type="video/mp4" />
						</video>
					) }
					{ hasBackgroundMedia && backgroundMediaType === 'image' && (
						<img
							className="mwm-hero-03__bg-media h-full w-full object-cover"
							src={ backgroundImageUrl }
							alt={ backgroundImageAlt || '' }
						/>
					) }
				</div>
				<div className="mwm-hero-03__gradient absolute inset-0 -z-10" aria-hidden="true" />

				<div className="mwm-max-1">
					<div className="mwm-hero-03__shell flex min-h-[600px] w-full flex-col py-[35px] pt-3 md:min-h-[680px] lg:min-h-[768px]">
						<div className="mwm-hero-03__content flex min-h-0 flex-1 flex-col gap-[35px]">
							{ showBreadcrumbs && safeBreadcrumbs.length > 0 && (
								<nav className="mwm-hero-03__breadcrumbs flex flex-wrap items-center gap-3" aria-label={ __( 'Breadcrumb', 'zenyx' ) }>
									{ safeBreadcrumbs.map( ( crumb, index ) => {
										const isFirst = index === 0;
										const hasUrl = !! String( crumb?.url || '' ).trim();
										if ( isFirst && hasUrl ) {
											return (
												<a
													key={ `crumb-link-${ index }` }
													className="mwm-hero-03__breadcrumb-link"
													href={ crumb.url }
													onClick={ ( event ) => event.preventDefault() }
												>
													{ crumb.label }
												</a>
											);
										}

										return (
											<span key={ `crumb-current-${ index }` } className="mwm-hero-03__breadcrumb-current">
												{ crumb.label }
											</span>
										);
									} ) }
								</nav>
							) }

							<div className="mwm-hero-03__center relative flex min-h-0 flex-1 flex-col items-center justify-end">
								{ showDecorativeSvg && (
									<div className="mwm-hero-03__decor absolute inset-x-0 bottom-0" aria-hidden="true">
										<svg className="mwm-hero-03__decor-svg" width="1296" height="406" viewBox="0 0 1296 406" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
											<g clipPath={ `url(#${ clipId })` }>
												<path d="M922.17 210.211L985.27 51.4189H1043.77L894.565 405.892H836.553L893.432 272.856L800.168 51.4189H858.65L922.17 210.211ZM441.24 47.417C475.487 47.4171 502.93 58.3688 522.791 79.9795C542.49 101.428 552.485 130.297 552.485 165.774H552.469V193.897H378.06C380.198 210.081 386.257 222.766 396.463 232.519C408.208 243.745 422.853 249.188 441.24 249.188C456.322 249.188 468.91 245.997 478.63 239.695C488.204 233.491 494.49 225.05 497.859 213.904L498.588 211.491H551.983L550.071 219.834C544.952 242.222 532.365 260.901 512.634 275.384C493.08 289.737 469.363 297.011 442.131 297.011C407.09 297.011 378.06 285.589 355.866 263.071C333.688 240.602 322.445 209.952 322.445 171.995C322.445 134.039 333.607 103.47 355.639 81.1299C377.687 58.7577 406.993 47.417 441.24 47.417ZM699.802 47.417C727.484 47.419 750.533 56.686 768.318 74.9414C786.041 93.1502 795.032 117.58 795.032 147.55V292.572H741.88V152.442C741.88 135.513 737.344 122.358 728.013 112.233C718.844 102.287 707.617 97.459 691.79 97.459C675.963 97.459 663.359 102.676 653.299 113.4C643.109 124.254 638.151 137.165 638.151 152.879V292.556H585.453V51.3379L608.992 51.4189H636.386V71.2148C641.1 66.6951 646.511 62.645 652.603 59.0811C665.804 51.3387 681.693 47.418 699.802 47.417ZM1175.07 130.637L1228.17 51.4189H1291.48L1211.47 169.307L1296 292.572H1232.64L1175.1 208.316L1116.72 292.572H1054.51L1137.1 168.464L1058.06 51.4189H1120.73L1175.07 130.637ZM80.6113 243.567L223.884 243.875V292.426H0V243.567L71.5713 171.995H152.183L80.6113 243.567ZM305.435 52.1152V99.873L233.279 171.996H152.669L224.823 99.873H81.0967L80.9512 52.1152H305.435ZM440.803 95.2402C422.351 95.2402 407.738 100.375 396.123 110.921C386.128 119.993 380.182 132.289 378.076 148.311H499.301C497.697 133.164 492.529 120.738 483.911 111.326C473.981 100.505 459.254 95.2403 440.803 95.2402ZM376.035 28.5928L353.16 51.5811H324.454V0H376.035V28.5928Z" fill={ `url(#${ gradientId })` } fillOpacity="0.5"></path>
											</g>
											<defs>
												<linearGradient id={ gradientId } x1="648.001" y1="0" x2="648.001" y2="405.892" gradientUnits="userSpaceOnUse">
													<stop stopColor="#C1D9E4" stopOpacity="0.6"></stop>
													<stop offset="1" stopColor="#C1D9E4" stopOpacity="0.1"></stop>
												</linearGradient>
												<clipPath id={ clipId }>
													<rect width="1296" height="405.891" fill="white"></rect>
												</clipPath>
											</defs>
										</svg>
									</div>
								) }

								<div className="mwm-hero-03__text-wrap relative z-10 mx-auto flex w-full max-w-[1296px] flex-col items-center gap-6 px-4 sm:px-[110px] lg:px-[220px]">
									<RichText
										tagName="h1"
										className="mwm-hero-03__heading w-full text-center text-[2rem] font-heading leading-[1.2] text-neutral-light md:text-5xl"
										value={ heading }
										onChange={ ( value ) => setAttributes( { heading: value ?? '' } ) }
										placeholder={ __( 'Titular...', 'zenyx' ) }
										allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
									/>
									<RichText
										tagName="p"
										className="mwm-hero-03__description max-w-[636px] text-center text-lg leading-[1.3] text-white md:text-xl"
										value={ description }
										onChange={ ( value ) => setAttributes( { description: value ?? '' } ) }
										placeholder={ __( 'Descripcion...', 'zenyx' ) }
										allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
