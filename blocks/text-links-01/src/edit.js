import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

const DEFAULT_COLUMNS = [
	{
		title: 'Programa de aceleración para agencias práctico',
		buttonText: 'Programación',
		buttonUrl: '',
		opensInNewTab: false,
		buttonVariant: 'primary',
	},
	{
		title: 'Eventos para<br>agencias ',
		buttonText: 'Eventos',
		buttonUrl: '',
		opensInNewTab: false,
		buttonVariant: 'light',
	},
	{
		title: 'Una comunidad de dueños de agencia real',
		buttonText: 'Casos de éxito',
		buttonUrl: '',
		opensInNewTab: false,
		buttonVariant: 'light',
	},
];

const VARIANT_OPTIONS = [
	{ label: __('Primario', 'zenyx'), value: 'primary' },
	{ label: __('Claro', 'zenyx'), value: 'light' },
	{ label: __('Oscuro', 'zenyx'), value: 'dark' },
	{ label: __('Ghost', 'zenyx'), value: 'ghost' },
];

/** Incluye zenyx/underline (botón en toolbar; core/underline no muestra UI). */
const RICH_TEXT_FORMATS = ['core/bold', 'core/italic', 'core/link', 'zenyx/underline'];

function variantClass(variant) {
	switch (variant) {
		case 'light':
			return 'mwm-btn--light';
		case 'dark':
			return 'mwm-btn--dark';
		case 'ghost':
			return 'mwm-btn--ghost';
		default:
			return 'mwm-btn--primary';
	}
}

function EditorButtonPreview({ text, url, variant = 'primary' }) {
	if (!text?.trim()) {
		return null;
	}
	const btnClass = [
		'mwm-btn',
		variantClass(variant),
		'mwm-btn--md',
		'mwm-btn--has-icon',
		'mwm-btn--icon-after',
		'mwm-text-links-01__cta',
	].join(' ');

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

export default function Edit({ attributes, setAttributes }) {
	const {
		mainHeading = '',
		introLabel = '',
		introHighlight = '',
		columns: rawColumns = [],
	} = attributes;

	const safeColumns = Array.isArray(rawColumns) && rawColumns.length ? rawColumns : DEFAULT_COLUMNS;
	const columns = [0, 1, 2].map((idx) => ({
		...DEFAULT_COLUMNS[idx],
		...(safeColumns[idx] || {}),
	}));

	const updateColumn = (index, field, value) => {
		const next = [...columns];
		next[index] = {
			...next[index],
			[field]: value,
		};
		setAttributes({ columns: next });
	};

	const blockProps = useBlockProps({
		className: 'mwm-text-links-01 relative isolate w-full overflow-hidden bg-protagonista',
	});

	return (
		<>
			<InspectorControls>
				{[0, 1, 2].map((idx) => {
					const col = columns[idx];
					const label = sprintf(__('Columna %d', 'zenyx'), idx + 1);
					return (
						<PanelBody title={label} key={idx} initialOpen={idx === 0}>
							<TextControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={__('Texto del boton', 'zenyx')}
								value={col.buttonText ?? ''}
								onChange={(value) => updateColumn(idx, 'buttonText', value ?? '')}
							/>
							<TextControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={__('URL del boton', 'zenyx')}
								value={col.buttonUrl ?? ''}
								onChange={(value) => updateColumn(idx, 'buttonUrl', value ?? '')}
								type="url"
								placeholder="https://"
							/>
							<ToggleControl
								__nextHasNoMarginBottom
								label={__('Abrir en nueva pestana', 'zenyx')}
								checked={!!col.opensInNewTab}
								onChange={(value) => updateColumn(idx, 'opensInNewTab', !!value)}
							/>
							<SelectControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={__('Variante del boton', 'zenyx')}
								value={col.buttonVariant ?? 'primary'}
								options={VARIANT_OPTIONS}
								onChange={(value) => updateColumn(idx, 'buttonVariant', value ?? 'primary')}
							/>
						</PanelBody>
					);
				})}
			</InspectorControls>

			<section {...blockProps} data-dark="">
				<div className="mwm-text-links-01__decor pointer-events-none absolute inset-0 z-0 overflow-hidden" aria-hidden="true">
					<div className="mwm-text-links-01__decor-inner flex h-full min-h-0 w-full items-end justify-center">
						<svg
							className="mwm-text-links-01__decor-svg"
							width="958"
							height="768"
							viewBox="0 0 958 768"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
							focusable="false"
							preserveAspectRatio="xMidYMax meet"
						>
							<g>
								<path
									d="M205.345 639.668L570.286 640.513V768H0V639.668L182.328 451.738H387.659L205.345 639.668ZM778.016 136.873V262.288L594.227 451.738H388.881L572.685 262.288H206.552L206.18 136.873H778.016ZM957.856 75.1104L899.578 135.476H826.477V0H957.856V75.1104Z"
									fill="url(#mwm-tl01-editor-gradient)"
								/>
							</g>
							<defs>
								<linearGradient
									id="mwm-tl01-editor-gradient"
									x1="478.928"
									y1="0"
									x2="478.928"
									y2="768"
									gradientUnits="userSpaceOnUse"
								>
									<stop offset="0" stop-color="#073549"></stop>
									<stop offset="0.7" stop-color="#073549"></stop>
									<stop offset="1" stop-color="#073549" stop-opacity="0"></stop>
								</linearGradient>
							</defs>
						</svg>
					</div>
				</div>

				<div className="relative z-10 mwm-max-1">
					<div className="mwm-text-links-01__inner py-16 lg:py-[120px]">
						<RichText
							tagName="h2"
							className="mwm-text-links-01__heading font-heading text-4xl font-normal leading-[1.2] text-neutral-light lg:text-[40px]"
							value={mainHeading}
							onChange={(value) => setAttributes({ mainHeading: value ?? '' })}
							placeholder={__('Titular…', 'zenyx')}
							allowedFormats={RICH_TEXT_FORMATS}
						/>

						<div className="mwm-text-links-01__intro mt-10 flex flex-col gap-3 lg:mt-12">
							<RichText
								tagName="p"
								className="mwm-text-links-01__intro-label text-base font-medium leading-[1.2] text-white"
								value={introLabel}
								onChange={(value) => setAttributes({ introLabel: value ?? '' })}
								placeholder={__('Etiqueta intro…', 'zenyx')}
								allowedFormats={RICH_TEXT_FORMATS}
							/>
							<RichText
								tagName="p"
								className="mwm-text-links-01__intro-highlight text-xl leading-[1.2] text-acento lg:text-[20px] max-w-[306px]"
								value={introHighlight}
								onChange={(value) => setAttributes({ introHighlight: value ?? '' })}
								placeholder={__('Texto destacado…', 'zenyx')}
								allowedFormats={RICH_TEXT_FORMATS}
							/>
						</div>

						<div className="mwm-text-links-01__grid mt-10 grid grid-cols-1 gap-6 md:grid-cols-3 md:gap-6 lg:mt-12">
							{[0, 1, 2].map((idx) => {
								const col = columns[idx];
								return (
									<div className="mwm-text-links-01__col flex flex-col gap-6" key={idx}>
										<RichText
											tagName="h3"
											className="mwm-text-links-01__col-title font-body text-2xl font-medium leading-[1.2] text-white"
											value={col.title ?? ''}
											onChange={(value) => updateColumn(idx, 'title', value ?? '')}
											placeholder={__('Titulo de columna…', 'zenyx')}
											allowedFormats={RICH_TEXT_FORMATS}
										/>
										{col.buttonText?.trim() && (
											<div className="mwm-text-links-01__cta-wrap flex w-full flex-col">
												<EditorButtonPreview
													text={col.buttonText}
													url={col.buttonUrl ?? ''}
													variant={col.buttonVariant ?? 'primary'}
												/>
											</div>
										)}
									</div>
								);
							})}
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
