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

const DEFAULT_STATS = [
	{ value: '1M+', description: '+83.333EUR / mes de facturacion' },
	{ value: '35%', description: 'De rentabilidad media total' },
	{ value: '+20h', description: 'De tus horas semanales libres de la operativa' },
];

function MediaPanel( {
	title,
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
		<PanelBody title={ title } initialOpen={ false }>
			<SelectControl
				label={ __( 'Tipo de media', 'zenyx' ) }
				value={ mediaType }
				options={ [
					{ label: __( 'Imagen', 'zenyx' ), value: 'image' },
					{ label: __( 'Video', 'zenyx' ), value: 'video' },
				] }
				onChange={ onChangeType }
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
									<video
										src={ videoUrl }
										style={ { width: '100%', marginBottom: '8px' } }
										controls
										muted
									/>
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

function EditorButtonPreview( { text, url } ) {
	if ( ! text?.trim() ) {
		return null;
	}
	const btnClass =
		'mwm-btn mwm-btn--primary mwm-btn--md mwm-btn--has-icon mwm-btn--icon-after mwm-hero-01__cta';
	const icon = (
		<span className="mwm-btn__icon" aria-hidden="true">
			<svg className="mwm-btn__icon-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M0.625 10H19.375"
					stroke="currentColor"
					strokeWidth="1.2"
					strokeLinecap="round"
					strokeLinejoin="round"
				/>
				<path
					d="M10.625 18.75L19.375 10L10.625 1.25"
					stroke="currentColor"
					strokeWidth="1.2"
					strokeLinecap="round"
					strokeLinejoin="round"
				/>
			</svg>
		</span>
	);
	if ( url?.trim() ) {
		return (
			<a href={ url } className={ btnClass } onClick={ ( e ) => e.preventDefault() }>
				<span className="mwm-btn__label">{ text }</span>
				{ icon }
			</a>
		);
	}
	return (
		<button type="button" className={ btnClass } disabled>
			<span className="mwm-btn__label">{ text }</span>
			{ icon }
		</button>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const {
		backgroundMediaType = 'video',
		backgroundImageId = 0,
		backgroundImageUrl = '',
		backgroundImageAlt = '',
		backgroundVideoId = 0,
		backgroundVideoUrl = '',
		clipMediaType = 'image',
		clipImageId = 0,
		clipImageUrl = '',
		clipImageAlt = '',
		clipVideoId = 0,
		clipVideoUrl = '',
		heading = '',
		subheading = '',
		description = '',
		buttonText = '',
		buttonUrl = '',
		opensInNewTab = false,
		stats = DEFAULT_STATS,
	} = attributes;

	const safeStats = Array.isArray( stats ) && stats.length ? stats : DEFAULT_STATS;

	const updateStat = ( index, field, value ) => {
		const nextStats = [ ...safeStats ];
		while ( nextStats.length < 3 ) {
			nextStats.push( { ...DEFAULT_STATS[ nextStats.length ] } );
		}

		nextStats[ index ] = {
			...( nextStats[ index ] || { value: '', description: '' } ),
			[ field ]: value ?? '',
		};

		setAttributes( { stats: nextStats.slice( 0, 3 ) } );
	};

	const hasBackgroundMedia =
		backgroundMediaType === 'video'
			? '' !== String( backgroundVideoUrl || '' ).trim()
			: '' !== String( backgroundImageUrl || '' ).trim();

	const hasClipMedia =
		clipMediaType === 'video'
			? '' !== String( clipVideoUrl || '' ).trim()
			: '' !== String( clipImageUrl || '' ).trim();

	const textColumnClasses = [
		'mwm-hero-01__text-column flex min-h-0 min-w-0 flex-1 flex-col-reverse justify-between gap-6 self-stretch pt-0 lg:flex-col lg:pt-3',
		hasClipMedia ? 'lg:min-h-[308px]' : '',
	]
		.filter( Boolean )
		.join( ' ' );

	const blockProps = useBlockProps( {
		className: 'mwm-hero-01 relative isolate w-full overflow-hidden',
		style: { paddingTop: 'calc(var(--header-height, 68px))' },
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Boton', 'zenyx' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Texto del boton', 'zenyx' ) }
						value={ buttonText }
						onChange={ ( value ) => setAttributes( { buttonText: value ?? '' } ) }
					/>
					<TextControl
						label={ __( 'URL del boton', 'zenyx' ) }
						value={ buttonUrl }
						onChange={ ( value ) => setAttributes( { buttonUrl: value ?? '' } ) }
						type="url"
						placeholder="https://"
					/>
					<ToggleControl
						label={ __( 'Abrir en nueva pestana', 'zenyx' ) }
						checked={ opensInNewTab }
						onChange={ ( value ) => setAttributes( { opensInNewTab: !! value } ) }
					/>
				</PanelBody>

				<MediaPanel
					title={ __( 'Media de fondo', 'zenyx' ) }
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

				<MediaPanel
					title={ __( 'Media recortada', 'zenyx' ) }
					mediaType={ clipMediaType }
					imageId={ clipImageId }
					imageUrl={ clipImageUrl }
					videoId={ clipVideoId }
					videoUrl={ clipVideoUrl }
					onChangeType={ ( value ) => setAttributes( { clipMediaType: value } ) }
					onSelectImage={ ( media ) =>
						setAttributes( {
							clipImageId: media.id || 0,
							clipImageUrl: media.url || '',
							clipImageAlt: media.alt || '',
						} )
					}
					onRemoveImage={ () => setAttributes( { clipImageId: 0, clipImageUrl: '', clipImageAlt: '' } ) }
					onSelectVideo={ ( media ) =>
						setAttributes( {
							clipVideoId: media.id || 0,
							clipVideoUrl: media.url || '',
						} )
					}
					onRemoveVideo={ () => setAttributes( { clipVideoId: 0, clipVideoUrl: '' } ) }
				/>
			</InspectorControls>

			<section { ...blockProps } data-dark="">
				<div className="mwm-hero-01__bg absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
					{ hasBackgroundMedia && backgroundMediaType === 'video' && (
						<video className="mwm-hero-01__bg-media h-full w-full object-cover" autoPlay muted loop playsInline>
							<source src={ backgroundVideoUrl } type="video/mp4" />
						</video>
					) }
					{ hasBackgroundMedia && backgroundMediaType === 'image' && (
						<img
							className="mwm-hero-01__bg-media h-full w-full object-cover"
							src={ backgroundImageUrl }
							alt={ backgroundImageAlt || '' }
						/>
					) }
					<div
						className="mwm-hero-01__overlay absolute inset-0 bg-neutral-light"
						style={ { opacity: 0.8 } }
					/>
				</div>

				<div className="mwm-max-1">
					<div className="mwm-hero-01__shell flex w-full min-h-0 flex-1 flex-col">
						<div className="mwm-hero-01__content flex min-h-0 flex-1 flex-col justify-between gap-8 self-stretch py-10 lg:justify-around lg:gap-6 lg:py-[35px]">
							<div className="mwm-hero-01__top flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-center lg:gap-6 lg:self-stretch">
								<div className="mwm-hero-01__heading-col flex min-w-0 flex-1 basis-0 lg:max-w-[636px]">
									<RichText
										tagName="h2"
										className="mwm-hero-01__heading w-full text-left text-[2.125rem] font-heading uppercase leading-[1.2] text-protagonista md:text-5xl lg:text-[64px]"
										value={ heading }
										onChange={ ( value ) => setAttributes( { heading: value ?? '' } ) }
										placeholder={ __( 'Titular…', 'zenyx' ) }
										allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
									/>
								</div>

								<div className="mwm-hero-01__media-row flex min-w-0 flex-1 flex-col gap-6 lg:flex-row lg:items-end lg:justify-start lg:gap-6 lg:self-stretch">
									{ hasClipMedia && (
										<div className="mwm-hero-01__clip-media aspect-306/308 w-full max-w-[306px] shrink-0 overflow-hidden">
											{ clipMediaType === 'video' ? (
												<video className="h-full w-full object-cover" autoPlay muted loop playsInline>
													<source src={ clipVideoUrl } type="video/mp4" />
												</video>
											) : (
												<img
													className="h-full w-full object-cover"
													src={ clipImageUrl }
													alt={ clipImageAlt || '' }
												/>
											) }
										</div>
									) }

									<div className={ textColumnClasses }>
										<RichText
											tagName="p"
											className="mwm-hero-01__subheading max-w-[306px] text-lg font-medium leading-[1.2] text-protagonista md:text-xl lg:text-2xl"
											value={ subheading }
											onChange={ ( value ) => setAttributes( { subheading: value ?? '' } ) }
											placeholder={ __( 'Subtitulo…', 'zenyx' ) }
											allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
										/>

										{ buttonText?.trim() && (
											<div className="mwm-hero-01__cta-wrap flex w-full max-w-[306px] flex-col justify-end gap-1.5">
												<EditorButtonPreview text={ buttonText } url={ buttonUrl } />
											</div>
										) }
									</div>
								</div>
							</div>

							<div className="mwm-hero-01__bottom flex flex-col gap-6 lg:gap-6">
								<div className="mwm-hero-01__desc-row hidden flex-col gap-4 lg:flex lg:flex-row lg:items-end lg:gap-6">
									<RichText
										tagName="p"
										className="mwm-hero-01__description max-w-[306px] text-base font-medium leading-[1.2] text-protagonista"
										value={ description }
										onChange={ ( value ) => setAttributes( { description: value ?? '' } ) }
										placeholder={ __( 'Descripcion…', 'zenyx' ) }
										allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
									/>
									<div className="hidden min-h-[57px] shrink-0 lg:block lg:w-[306px]" aria-hidden="true" />
									<div className="hidden min-h-[57px] flex-1 lg:block" aria-hidden="true" />
								</div>

								<div className="mwm-hero-01__stats-wrap max-w-[966px]">
									<div className="mwm-hero-01__stats">
										<div className="mwm-hero-01__stats-track">
											{ [ 0, 1, 2 ].map( ( idx ) => {
												const statItem = safeStats[ idx ] || DEFAULT_STATS[ idx ];
												return (
													<div key={ idx } className="mwm-hero-01__stat flex max-w-[306px] flex-col gap-1.5">
														<RichText
															tagName="p"
															className="mwm-hero-01__stat-value text-5xl font-normal leading-[1.2] text-white md:text-[64px]"
															value={ statItem.value }
															onChange={ ( value ) => updateStat( idx, 'value', value ) }
															placeholder={ __( 'Valor…', 'zenyx' ) }
															allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
														/>
														<RichText
															tagName="p"
															className="mwm-hero-01__stat-desc text-base font-medium leading-[1.2] text-protagonista"
															value={ statItem.description }
															onChange={ ( value ) => updateStat( idx, 'description', value ) }
															placeholder={ __( 'Descripcion…', 'zenyx' ) }
															allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
														/>
													</div>
												);
											} ) }
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
