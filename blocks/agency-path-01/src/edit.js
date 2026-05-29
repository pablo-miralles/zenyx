import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from '../block.json';

const RICH_TEXT_FORMATS = ['core/bold', 'core/italic', 'core/link', 'zenyx/underline'];

const EMPTY_VALIDATION = () => ({ label: '' });

const DEFAULT_VALIDATION_ITEMS = [
	{ label: 'Ventas' },
	{ label: 'Procesos' },
	{ label: 'Equipos' },
];

export default function Edit({ attributes, setAttributes }) {
	const {
		intro = '',
		tagline = '',
		validationTitle = '',
		validationItems: rawValidation = [],
		level1Title = '',
		level1LabelFundamentos = '',
		level1LabelEscalar = '',
		level2Title = '',
		level2LabelAsentar = '',
		level2LabelEscalar = '',
		level3Title = '',
		level3LabelLibertad = '',
		markerLabel = '',
		mobileStaticSvgId = 0,
		mobileStaticSvgUrl = '',
	} = attributes;

	const validationItems = Array.isArray(rawValidation) && rawValidation.length ? rawValidation : DEFAULT_VALIDATION_ITEMS;

	const updateValidation = (index, value) => {
		const next = validationItems.map((row, i) =>
			i === index ? { ...row, label: value ?? '' } : row
		);
		setAttributes({ validationItems: next });
	};

	const addValidation = () => {
		setAttributes({ validationItems: [...validationItems, EMPTY_VALIDATION()] });
	};

	const removeValidation = (index) => {
		const next = validationItems.filter((_, i) => i !== index);
		setAttributes({ validationItems: next.length ? next : [EMPTY_VALIDATION()] });
	};

	const blockProps = useBlockProps({
		className: 'mwm-agency-path-01-editor w-full min-w-0',
	});

	const ssrAttributes = {
		...attributes,
		intro: '',
		tagline: '',
	};

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Textos de niveles', 'zenyx')} initialOpen={true}>
					<TextControl
						label={__('Nivel 1 — título', 'zenyx')}
						value={level1Title}
						onChange={(v) => setAttributes({ level1Title: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Nivel 1 — primera etiqueta (fundamentos)', 'zenyx')}
						value={level1LabelFundamentos}
						onChange={(v) => setAttributes({ level1LabelFundamentos: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Nivel 1 — segunda etiqueta (escalar)', 'zenyx')}
						value={level1LabelEscalar}
						onChange={(v) => setAttributes({ level1LabelEscalar: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Nivel 2 — título', 'zenyx')}
						value={level2Title}
						onChange={(v) => setAttributes({ level2Title: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Nivel 2 — asentar', 'zenyx')}
						value={level2LabelAsentar}
						onChange={(v) => setAttributes({ level2LabelAsentar: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Nivel 2 — escalar', 'zenyx')}
						value={level2LabelEscalar}
						onChange={(v) => setAttributes({ level2LabelEscalar: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Nivel 3 — título', 'zenyx')}
						value={level3Title}
						onChange={(v) => setAttributes({ level3Title: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Nivel 3 — libertad', 'zenyx')}
						value={level3LabelLibertad}
						onChange={(v) => setAttributes({ level3LabelLibertad: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Marcador del diagrama (letra)', 'zenyx')}
						value={markerLabel}
						onChange={(v) => setAttributes({ markerLabel: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				<PanelBody title={__('Validación', 'zenyx')} initialOpen={false}>
					<TextControl
						label={__('Título columna', 'zenyx')}
						value={validationTitle}
						onChange={(v) => setAttributes({ validationTitle: v ?? '' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{validationItems.map((row, index) => (
						<div key={index} style={{ marginBottom: 12 }}>
							<TextControl
								label={sprintf(__('Ítem %d', 'zenyx'), index + 1)}
								value={row.label ?? ''}
								onChange={(v) => updateValidation(index, v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<Button
								isDestructive
								variant="link"
								onClick={() => removeValidation(index)}
							>
								{__('Quitar', 'zenyx')}
							</Button>
						</div>
					))}
					<Button variant="secondary" onClick={addValidation}>
						{__('Añadir ítem', 'zenyx')}
					</Button>
				</PanelBody>
				<PanelBody title={__('SVG móvil (estático)', 'zenyx')} initialOpen={false}>
					<p style={{ marginTop: 0, fontSize: 12, color: '#757575' }}>
						{__(
							'Diagrama en pantallas pequeñas. Si no eliges nada, se usa el del tema. Recomendado: SVG; también PNG.',
							'zenyx'
						)}
					</p>
					<MediaUploadCheck>
						<MediaUpload
							allowedTypes={['image']}
							value={mobileStaticSvgId || undefined}
							onSelect={(media) => {
								if (!media) {
									return;
								}
								setAttributes({
									mobileStaticSvgId: media.id || 0,
									mobileStaticSvgUrl: media.url || media.source_url || '',
								});
							}}
							render={({ open }) => (
								<div style={{ display: 'grid', gap: 8 }}>
									<Button variant={mobileStaticSvgId ? 'secondary' : 'primary'} onClick={open}>
										{mobileStaticSvgId
											? __('Reemplazar archivo', 'zenyx')
											: __('Subir o elegir de la biblioteca', 'zenyx')}
									</Button>
									{mobileStaticSvgId > 0 && (
										<Button
											variant="tertiary"
											isDestructive
											onClick={() =>
												setAttributes({
													mobileStaticSvgId: 0,
													mobileStaticSvgUrl: '',
												})
											}
										>
											{__('Quitar (volver al del tema)', 'zenyx')}
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>

			<div className="mwm-max-1 w-full">
				<div
					className="mwm-agency-path-01__intro mb-10 px-2 md:mb-14 lg:mb-16"
					data-agp01-intro
				>
					<RichText
						tagName="p"
						className="mwm-agency-path-01__intro-text mx-auto max-w-[1076px] text-center font-body text-xl leading-snug md:text-[28px] lg:text-[32px]"
						value={intro}
						onChange={(v) => setAttributes({ intro: v ?? '' })}
						placeholder={__('Texto introductorio…', 'zenyx')}
						allowedFormats={RICH_TEXT_FORMATS}
					/>
				</div>
			</div>

			<p className="mb-3 text-xs text-gray-600">
				{__(
					'Edita el tagline aquí abajo; la animación por scroll solo se ve en la web publicada.',
					'zenyx'
				)}
			</p>
			<div className="mb-6 rounded border border-gray-200 bg-neutral-light/30 p-4">
				<RichText
					tagName="p"
					className="text-base text-protagonista"
					value={tagline}
					onChange={(v) => setAttributes({ tagline: v ?? '' })}
					placeholder={__('Tagline final…', 'zenyx')}
					allowedFormats={RICH_TEXT_FORMATS}
				/>
			</div>

			<div className="mwm-agency-path-01-editor__ssr w-full min-w-0 max-w-full overflow-x-auto">
				<ServerSideRender block={metadata.name} attributes={ssrAttributes} />
			</div>
		</div>
	);
}
