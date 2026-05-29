import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ALLOWED_IMAGE_TYPES = ['image'];

function EditorButtonPreview({ text, url }) {
	if (!text?.trim()) {
		return null;
	}

	const btnClass =
		'mwm-btn mwm-btn--primary mwm-btn--md mwm-btn--has-icon mwm-btn--icon-after mwm-hero-04__cta';

	const icon = (
		<span className="mwm-btn__icon" aria-hidden="true">
			<svg className="mwm-btn__icon-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.625 10H19.375" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
				<path d="M10.625 18.75L19.375 10L10.625 1.25" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
			</svg>
		</span>
	);

	if (url?.trim()) {
		return (
			<a href={url} className={btnClass} onClick={(event) => event.preventDefault()}>
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

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		heading = '',
		lead = '',
		supportingText = '',
		buttonText = '',
		buttonUrl = '',
		opensInNewTab = false,
		imageId = 0,
		imageUrl = '',
		imageAlt = '',
		showBreadcrumbs = true,
		breadcrumbsArchiveLabel = '',
	} = attributes;

	const blockProps = useBlockProps({
		className: 'mwm-hero-04 relative w-full overflow-hidden bg-[#c1d9e4]',
		style: { paddingTop: 'calc(var(--header-height, 68px))' },
	});
	const decorIdSafe = String(clientId || 'hero04').replace(/[^a-zA-Z0-9_-]/g, '');
	const decorId = `hero04DecorGradient-${decorIdSafe}`;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Imagen principal', 'zenyx')} initialOpen={true}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									imageId: media.id || 0,
									imageUrl: media.url || '',
									imageAlt: media.alt || '',
								})
							}
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
											onClick={() =>
												setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' })
											}
											style={{ marginLeft: '8px' }}
										>
											{__('Eliminar', 'zenyx')}
										</Button>
									)}
								</>
							)}
						/>
					</MediaUploadCheck>
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
						checked={!!opensInNewTab}
						onChange={(value) => setAttributes({ opensInNewTab: !!value })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody title={__('Breadcrumbs', 'zenyx')} initialOpen={false}>
					<ToggleControl
						label={__('Mostrar breadcrumbs', 'zenyx')}
						checked={!!showBreadcrumbs}
						onChange={(value) => setAttributes({ showBreadcrumbs: !!value })}
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Texto del nivel archivo', 'zenyx')}
						help={__('Ejemplo: Casos de exito', 'zenyx')}
						value={breadcrumbsArchiveLabel}
						onChange={(value) => setAttributes({ breadcrumbsArchiveLabel: value ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps} data-light="">
				<div className="mwm-max-1">
					<div className="mwm-hero-04__shell relative isolate flex min-h-[620px] w-full flex-col gap-8 pb-[35px] pt-3 lg:min-h-[768px]">
						<div className="mwm-hero-04__bg-decor pointer-events-none absolute left-0 bottom-0 z-0" aria-hidden="true">
							<svg className="mwm-hero-04__bg-decor-svg" width="718" height="383" viewBox="0 0 718 383" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
								<g opacity="1">
									<path d="M717.674 0V152.686L487.013 383.331H229.297L459.977 152.686H0.467773L0 0H717.674Z" fill={`url(#${decorId})`} style={{ mixBlendMode: 'plus-darker' }}></path>
								</g>
								<defs>
									<linearGradient id={decorId} x1="717.674" y1="139.666" x2="0" y2="139.666" gradientUnits="userSpaceOnUse">
										<stop stopColor="#083B51"></stop>
										<stop offset="1" stopColor="#083B51" stopOpacity="0"></stop>
									</linearGradient>
								</defs>
							</svg>
						</div>
						{showBreadcrumbs && (
							<nav className="mwm-hero-04__breadcrumbs relative z-10 flex flex-wrap items-center gap-3" aria-label={__('Migas de pan', 'zenyx')}>
								<span className="mwm-hero-04__breadcrumb-link">{__('Home', 'zenyx')}</span>
								<span className="mwm-hero-04__breadcrumb-link">{breadcrumbsArchiveLabel || __('Casos de exito', 'zenyx')}</span>
								<span className="mwm-hero-04__breadcrumb-current">{__('Titulo actual', 'zenyx')}</span>
							</nav>
						)}

						<div className="mwm-hero-04__content relative z-10 grid min-h-0 flex-1 grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-6">
							<div className="mwm-hero-04__left flex min-h-0 flex-1 flex-col justify-between gap-8 lg:gap-6">
								<div className="mwm-hero-04__copy flex flex-col gap-6">
									<RichText
										tagName="h1"
										className="mwm-hero-04__title max-w-[636px] text-[2rem] font-heading leading-[1.2] text-protagonista md:text-5xl"
										value={heading}
										onChange={(value) => setAttributes({ heading: value ?? '' })}
										placeholder={__('Titular...', 'zenyx')}
										allowedFormats={['core/bold', 'core/italic', 'core/link']}
									/>
									<RichText
										tagName="p"
										className="mwm-hero-04__lead max-w-[636px] text-xl leading-[1.2] text-protagonista"
										value={lead}
										onChange={(value) => setAttributes({ lead: value ?? '' })}
										placeholder={__('Lead...', 'zenyx')}
										allowedFormats={['core/bold', 'core/italic', 'core/link']}
									/>
								</div>

								<div className="mwm-hero-04__bottom flex flex-col items-start gap-6 md:flex-row md:items-end md:gap-6">
									<RichText
										tagName="p"
										className="mwm-hero-04__supporting max-w-[306px] text-base leading-[1.2] text-protagonista"
										value={supportingText}
										onChange={(value) => setAttributes({ supportingText: value ?? '' })}
										placeholder={__('Texto de apoyo...', 'zenyx')}
										allowedFormats={['core/bold', 'core/italic', 'core/link']}
									/>
									<div className="mwm-hero-04__cta-wrap flex flex-col items-start gap-1.5">
										<EditorButtonPreview text={buttonText} url={buttonUrl} />
									</div>
								</div>
							</div>

							<div className="mwm-hero-04__right flex min-h-0 items-end justify-end">
								<div className="mwm-hero-04__media-wrap relative aspect-square w-full max-w-[419px] overflow-hidden bg-protagonista">
									{imageUrl ? (
										<img className="mwm-hero-04__media h-full w-full object-cover" src={imageUrl} alt={imageAlt || ''} />
									) : (
										<div className="mwm-hero-04__media-placeholder h-full w-full" />
									)}
									<div className="mwm-hero-04__media-corner" aria-hidden="true"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
