import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	TextareaControl,
	TextControl,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ALLOWED_IMAGE_TYPES = ['image'];
const ALLOWED_VIDEO_TYPES = ['video'];

const DEFAULT_FEATURES = [
	'Ventas estables que no dependen de un mes bueno o malo.',
	'Finanzas que te permiten dormir tranquilo.',
	'Clientes que se quedan y son rentables.',
	'Un equipo que responde sin que estés encima.',
];

const DEFAULT_TITLE =
	'Construimos los 4 procesos clave que te permiten escalar a +83.333€ al mes y tener:';

function MediaPanel({
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
}) {
	return (
		<PanelBody title={title} initialOpen={false}>
			<SelectControl
				label={__('Tipo de media', 'zenyx')}
				value={mediaType}
				options={[
					{ label: __('Imagen', 'zenyx'), value: 'image' },
					{ label: __('Video', 'zenyx'), value: 'video' },
				]}
				onChange={onChangeType}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>

			{mediaType === 'image' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={onSelectImage}
						allowedTypes={ALLOWED_IMAGE_TYPES}
						value={imageId}
						render={({ open }) => (
							<>
								{imageUrl && (
									<img
										src={imageUrl}
										alt=""
										style={{ width: '100%', marginBottom: '8px', objectFit: 'cover' }}
									/>
								)}
								<Button variant={imageId ? 'secondary' : 'primary'} onClick={open}>
									{imageId ? __('Reemplazar imagen', 'zenyx') : __('Seleccionar imagen', 'zenyx')}
								</Button>
								{imageId > 0 && (
									<Button
										variant="link"
										isDestructive
										onClick={onRemoveImage}
										style={{ marginLeft: '8px' }}
									>
										{__('Eliminar', 'zenyx')}
									</Button>
								)}
							</>
						)}
					/>
				</MediaUploadCheck>
			)}

			{mediaType === 'video' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={onSelectVideo}
						allowedTypes={ALLOWED_VIDEO_TYPES}
						value={videoId}
						render={({ open }) => (
							<>
								{videoUrl && (
									<video
										src={videoUrl}
										style={{ width: '100%', marginBottom: '8px' }}
										controls
										muted
									/>
								)}
								<Button variant={videoId ? 'secondary' : 'primary'} onClick={open}>
									{videoId ? __('Reemplazar video', 'zenyx') : __('Seleccionar video', 'zenyx')}
								</Button>
								{videoId > 0 && (
									<Button
										variant="link"
										isDestructive
										onClick={onRemoveVideo}
										style={{ marginLeft: '8px' }}
									>
										{__('Eliminar', 'zenyx')}
									</Button>
								)}
							</>
						)}
					/>
				</MediaUploadCheck>
			)}
		</PanelBody>
	);
}

function EditorButtonPreview({ text, url }) {
	if (!text?.trim()) {
		return null;
	}
	const btnClass =
		'mwm-btn mwm-btn--primary mwm-btn--md mwm-btn--has-icon mwm-btn--icon-after mwm-hero-02__cta';
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
	if (url?.trim()) {
		return (
			<a href={url} className={btnClass} onClick={(e) => e.preventDefault()}>
				<span className="mwm-btn__label">{text}</span>
				{icon}
			</a>
		);
	}
	return (
		<button type="button" className={btnClass} disabled>
			<span className="mwm-btn__label">{text}</span>
			{icon}
		</button>
	);
}

function getFeatureDisplay(featureItems, index) {
	const raw = Array.isArray(featureItems) ? featureItems[index] : '';
	const v = typeof raw === 'string' ? raw.trim() : '';
	return v !== '' ? v : DEFAULT_FEATURES[index];
}

function getFeatureValue(featureItems, index) {
	const raw = Array.isArray(featureItems) ? featureItems[index] : '';
	return typeof raw === 'string' ? raw : '';
}

export default function Edit({ attributes, setAttributes, clientId }) {
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
		title = '',
		featureItems = [],
		buttonText = '',
		buttonUrl = '',
		opensInNewTab = false,
		showBreadcrumbs = true,
		breadcrumbHomeLabel = '',
		breadcrumbCurrentLabel = '',
	} = attributes;

	const hasBackgroundMedia =
		backgroundMediaType === 'video'
			? '' !== String(backgroundVideoUrl || '').trim()
			: '' !== String(backgroundImageUrl || '').trim();

	const hasClipVideo = '' !== String(clipVideoUrl || '').trim();
	const hasClipImage = '' !== String(clipImageUrl || '').trim();
	const hasClip = hasClipVideo || hasClipImage;

	let clipShowVideo = false;
	if (hasClipVideo && hasClipImage) {
		clipShowVideo = clipMediaType === 'video';
	} else if (hasClipVideo) {
		clipShowVideo = true;
	} else {
		clipShowVideo = false;
	}

	const displayTitle = title?.trim() ? title : DEFAULT_TITLE;

	const idSafe = String(clientId || 'hero02').replace(/[^a-zA-Z0-9_-]/g, '');
	const titleId = `mwm-hero-02-title-${idSafe}`;
	const gradId = `mwm-hero-02-grad-${idSafe}`;

	const blockProps = useBlockProps({
		className: 'mwm-hero-02 relative isolate w-full overflow-hidden',
		style: { paddingTop: 'calc(var(--header-height, 68px))' },
	});

	const updateFeature = (index, value) => {
		const next = [0, 1, 2, 3].map((i) => getFeatureValue(featureItems, i));
		next[index] = value ?? '';
		setAttributes({ featureItems: next });
	};

	const breadcrumbHome = breadcrumbHomeLabel?.trim() || __('Home', 'zenyx');
	const breadcrumbCurrent = breadcrumbCurrentLabel?.trim() || __('Programa libertad', 'zenyx');

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Contenido', 'zenyx')} initialOpen={true}>
					<TextareaControl
						label={__('Titular', 'zenyx')}
						value={title}
						onChange={(value) => setAttributes({ title: value ?? '' })}
						rows={4}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{[0, 1, 2, 3].map((i) => (
						<TextareaControl
							key={i}
							label={__('Destacado', 'zenyx') + ` ${i + 1}`}
							value={getFeatureValue(featureItems, i)}
							placeholder={DEFAULT_FEATURES[i]}
							onChange={(value) => updateFeature(i, value)}
							rows={2}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					))}
					<ToggleControl
						label={__('Mostrar migas de pan', 'zenyx')}
						checked={showBreadcrumbs}
						onChange={(value) => setAttributes({ showBreadcrumbs: !!value })}
						__nextHasNoMarginBottom
					/>
					{showBreadcrumbs && (
						<>
							<TextControl
								label={__('Texto Home (migas)', 'zenyx')}
								help={__('Dejar vacio para usar "Home".', 'zenyx')}
								value={breadcrumbHomeLabel}
								onChange={(value) => setAttributes({ breadcrumbHomeLabel: value ?? '' })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextControl
								label={__('Pagina actual (migas)', 'zenyx')}
								value={breadcrumbCurrentLabel}
								onChange={(value) => setAttributes({ breadcrumbCurrentLabel: value ?? '' })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Boton', 'zenyx')} initialOpen={false}>
					<TextControl
						label={__('Texto del boton', 'zenyx')}
						value={buttonText}
						onChange={(value) => setAttributes({ buttonText: value ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('URL del boton', 'zenyx')}
						value={buttonUrl}
						onChange={(value) => setAttributes({ buttonUrl: value ?? '' })}
						type="url"
						placeholder="https://"
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Abrir en nueva pestana', 'zenyx')}
						checked={opensInNewTab}
						onChange={(value) => setAttributes({ opensInNewTab: !!value })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<MediaPanel
					title={__('Media de fondo', 'zenyx')}
					mediaType={backgroundMediaType}
					imageId={backgroundImageId}
					imageUrl={backgroundImageUrl}
					videoId={backgroundVideoId}
					videoUrl={backgroundVideoUrl}
					onChangeType={(value) => setAttributes({ backgroundMediaType: value })}
					onSelectImage={(media) =>
						setAttributes({
							backgroundImageId: media.id || 0,
							backgroundImageUrl: media.url || '',
							backgroundImageAlt: media.alt || '',
						})
					}
					onRemoveImage={() =>
						setAttributes({ backgroundImageId: 0, backgroundImageUrl: '', backgroundImageAlt: '' })
					}
					onSelectVideo={(media) =>
						setAttributes({
							backgroundVideoId: media.id || 0,
							backgroundVideoUrl: media.url || '',
						})
					}
					onRemoveVideo={() => setAttributes({ backgroundVideoId: 0, backgroundVideoUrl: '' })}
				/>

				<MediaPanel
					title={__('Media recortada', 'zenyx')}
					mediaType={clipMediaType}
					imageId={clipImageId}
					imageUrl={clipImageUrl}
					videoId={clipVideoId}
					videoUrl={clipVideoUrl}
					onChangeType={(value) => setAttributes({ clipMediaType: value })}
					onSelectImage={(media) =>
						setAttributes({
							clipImageId: media.id || 0,
							clipImageUrl: media.url || '',
							clipImageAlt: media.alt || '',
						})
					}
					onRemoveImage={() => setAttributes({ clipImageId: 0, clipImageUrl: '', clipImageAlt: '' })}
					onSelectVideo={(media) =>
						setAttributes({
							clipVideoId: media.id || 0,
							clipVideoUrl: media.url || '',
						})
					}
					onRemoveVideo={() => setAttributes({ clipVideoId: 0, clipVideoUrl: '' })}
				/>
			</InspectorControls>

			<section {...blockProps} data-dark="" aria-labelledby={titleId}>
				<div className="mwm-hero-02__bg absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
					{hasBackgroundMedia && backgroundMediaType === 'video' && (
						<video className="mwm-hero-02__bg-media h-full w-full object-cover" autoPlay muted loop playsInline>
							<source src={backgroundVideoUrl} type="video/mp4" />
						</video>
					)}
					{hasBackgroundMedia && backgroundMediaType === 'image' && (
						<img
							className="mwm-hero-02__bg-media h-full w-full object-cover"
							src={backgroundImageUrl}
							alt={backgroundImageAlt || ''}
						/>
					)}
					<div
						className="mwm-hero-02__overlay absolute inset-0 bg-neutral-light"
						style={{ opacity: 0.8 }}
					/>
				</div>

				<div className="mwm-max-1 relative z-2">
					<div
						className="mwm-hero-02__decor pointer-events-none absolute bottom-0 left-0 z-0 max-h-[min(90vh,768px)] w-[min(100%,976px)] opacity-10"
						aria-hidden="true"
					>
						<svg
							className="h-auto w-full"
							width="976"
							height="768"
							viewBox="0 0 976 768"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
							preserveAspectRatio="xMidYMax meet"
						>
							<path
								d="M486.296 383.149L257.594 611.835L715.391 612.863V768H0V611.835L228.721 383.149H486.296ZM975.975 0V152.613L745.422 383.149H487.829L718.399 152.613H259.107L258.641 0H975.975Z"
								fill={`url(#${gradId})`}
								style={{ mixBlendMode: 'plus-darker' }}
							/>
							<defs>
								<linearGradient
									id={gradId}
									x1="487.987"
									y1="0"
									x2="487.987"
									y2="768"
									gradientUnits="userSpaceOnUse"
								>
									<stop stopColor="#083B51" stopOpacity="0.8" />
									<stop offset="1" stopColor="#083B51" stopOpacity="0.3" />
								</linearGradient>
							</defs>
						</svg>
					</div>

					<div className="relative z-10 flex min-h-[min(768px,90svh)] flex-col gap-[60px] pb-9 pt-3 lg:min-h-[768px]">
						{showBreadcrumbs && (
							<nav className="w-full shrink-0" aria-label={__('Migas de pan', 'zenyx')}>
								<ol className="m-0 flex list-none flex-wrap items-center gap-3 p-0 text-sm text-protagonista">
									<li className="m-0">
										<a className="text-protagonista no-underline hover:underline" href="/" onClick={(e) => e.preventDefault()}>
											{breadcrumbHome}
										</a>
									</li>
									<li className="m-0 font-medium" aria-current="page">
										{breadcrumbCurrent}
									</li>
								</ol>
							</nav>
						)}

						<div className="flex min-h-0 flex-1 flex-col-reverse gap-10 lg:flex-row lg:items-end lg:justify-between lg:gap-6">
							<div className="flex min-h-0 w-full min-w-0 max-w-[746px] flex-1 flex-col justify-between gap-9 self-stretch lg:pr-4">
								<h1
									id={titleId}
									className="m-0 max-w-[746px] font-heading text-[2rem] font-normal leading-[1.2] text-protagonista md:text-5xl lg:text-[48px]"
								>
									{displayTitle}
								</h1>

								<div className="flex max-w-[636px] flex-col gap-9">
									<ul className="mwm-hero-02__features m-0 grid list-none grid-cols-1 gap-6 p-0 md:grid-cols-2 md:gap-6" role="list">
										{[0, 1, 2, 3].map((i) => (
											<li key={i} className="max-w-[306px] text-xl font-medium leading-[1.2] text-protagonista">
												{getFeatureDisplay(featureItems, i)}
											</li>
										))}
									</ul>

									{buttonText?.trim() && (
										<div className="mwm-hero-02__cta-wrap flex flex-col items-start">
											<EditorButtonPreview text={buttonText} url={buttonUrl} />
										</div>
									)}
								</div>
							</div>

							<div className="relative w-full max-w-[416px] shrink-0 justify-self-end lg:self-stretch">
								<div className="mwm-hero-02__clip-inner relative aspect-416/419 w-full overflow-hidden bg-neutral-light">
									{hasClip && clipShowVideo && (
										<video className="h-full w-full object-cover" autoPlay muted loop playsInline>
											<source src={clipVideoUrl} type="video/mp4" />
										</video>
									)}
									{hasClip && !clipShowVideo && (
										<img
											className="h-full w-full object-cover"
											src={clipImageUrl}
											alt={clipImageAlt || ''}
										/>
									)}
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
