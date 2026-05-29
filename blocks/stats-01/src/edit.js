import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DEFAULT_STATS = [
	{ value: '+140', label: 'Más 140 agencias que han escalado' },
	{ value: '+35%', label: 'De rentabilidad media' },
	{ value: '+20h', label: 'De tus horas semanales libres de la operativa' },
];

const RICH_TEXT_FORMATS = ['core/bold', 'core/italic', 'core/link', 'zenyx/underline'];

function EditorButtonPreview({ text, url }) {
	if (!text?.trim()) {
		return null;
	}
	const btnClass =
		'mwm-btn mwm-btn--dark mwm-btn--md mwm-btn--has-icon mwm-btn--icon-after mwm-stats-01__cta';
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
		heading = '',
		description = '',
		buttonText = '',
		buttonUrl = '',
		opensInNewTab = false,
		stats: rawStats = [],
	} = attributes;

	const safeStats = Array.isArray(rawStats) && rawStats.length ? rawStats : DEFAULT_STATS;
	const stats = [0, 1, 2].map((idx) => ({
		...DEFAULT_STATS[idx],
		...(safeStats[idx] || {}),
	}));

	const updateStat = (index, field, value) => {
		const next = [...stats];
		next[index] = {
			...next[index],
			[field]: value ?? '',
		};
		setAttributes({ stats: next });
	};

	const blockProps = useBlockProps({
		className: 'mwm-stats-01 w-full bg-neutral-light py-[120px]',
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Botón', 'zenyx')} initialOpen={true}>
					<TextControl
						label={__('Texto del botón', 'zenyx')}
						value={buttonText}
						onChange={(value) => setAttributes({ buttonText: value ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('URL del botón', 'zenyx')}
						value={buttonUrl}
						onChange={(value) => setAttributes({ buttonUrl: value ?? '' })}
						type="url"
						placeholder="https://"
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Abrir en nueva pestaña', 'zenyx')}
						checked={opensInNewTab}
						onChange={(value) => setAttributes({ opensInNewTab: value })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps} data-light="">
				<div className="mwm-max-1 flex flex-col gap-16 lg:gap-20">
					<div className="mwm-stats-01__intro flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
						<div className="mwm-stats-01__intro-text flex min-w-0 flex-1 flex-col gap-6">
							<div className="mwm-stats-01__heading-wrap w-full max-w-[636px]">
								<RichText
									tagName="h2"
									className="mwm-stats-01__heading text-left font-heading text-[2rem] leading-[1.2] text-protagonista md:text-[40px]"
									value={heading}
									onChange={(value) => setAttributes({ heading: value ?? '' })}
									placeholder={__('Titular…', 'zenyx')}
									allowedFormats={RICH_TEXT_FORMATS}
								/>
							</div>
							<div className="mwm-stats-01__description-wrap w-full max-w-[636px] text-base leading-normal text-protagonista">
								<RichText
									tagName="div"
									className="mwm-stats-01__description"
									value={description}
									onChange={(value) => setAttributes({ description: value ?? '' })}
									placeholder={__('Descripción…', 'zenyx')}
									allowedFormats={RICH_TEXT_FORMATS}
								/>
							</div>
						</div>
						<div className="mwm-stats-01__cta-wrap w-full max-w-[416px] shrink-0 flex-col gap-2.5">
							<EditorButtonPreview text={buttonText} url={buttonUrl} />
						</div>
					</div>

					<div className="mwm-stats-01__stats flex flex-col items-stretch gap-10">
						<div className="mwm-stats-01__stats-grid grid grid-cols-1 gap-6 md:grid-cols-3 md:gap-6">
							{stats.map((stat, index) => (
								<div key={`stat-${index}`} className="mwm-stats-01__stat flex flex-1 gap-6 items-start">
									<div className="mwm-stats-01__stat-value-wrap flex min-w-0 flex-1 items-center justify-center md:justify-start">
										<RichText
											tagName="div"
											className="mwm-stats-01__stat-value w-full min-w-0 text-left font-body text-5xl leading-none text-acento md:text-[64px]"
											value={stat.value}
											onChange={(value) => updateStat(index, 'value', value)}
											placeholder={__('Valor…', 'zenyx')}
											allowedFormats={RICH_TEXT_FORMATS}
										/>
									</div>
									<div className="mwm-stats-01__stat-label-wrap flex min-w-0 flex-1 items-center justify-center pt-3 md:justify-start">
										<RichText
											tagName="div"
											className="mwm-stats-01__stat-label w-full min-w-0 text-left text-base font-medium leading-normal text-protagonista"
											value={stat.label}
											onChange={(value) => updateStat(index, 'label', value)}
											placeholder={__('Etiqueta…', 'zenyx')}
											allowedFormats={RICH_TEXT_FORMATS}
										/>
									</div>
								</div>
							))}
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
