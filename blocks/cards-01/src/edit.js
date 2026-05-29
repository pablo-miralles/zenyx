import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = ['core/bold', 'core/italic', 'core/link', 'zenyx/underline'];

function normalizeHeadingMarkup(value) {
	const raw = String(value ?? '');
	return raw
		.replace(/<\/p>\s*<p[^>]*>/gi, '<br />')
		.replace(/<p[^>]*>/gi, '')
		.replace(/<\/p>/gi, '');
}

const DEFAULT_CARDS = [
	{
		title: '¿Cómo conseguir clientes para tu agencia de forma predecible?',
		body: 'Deja de depender del boca a boca. Trabajamos el Go-To-Market (GTM) de tu agencia para que dejes de ser uno más en el mercado',
	},
	{
		title: 'Cómo sistematizar tu agencia para que funcione sin ti',
		body: '¿Todo pasa por ti? implementamos framework claros para que tus clientes se queden sin que tengas que estar encima de cada tarea.',
	},
	{
		title: 'Cómo construir una agencia rentable con márgenes sanos',
		body: 'Te ayudamos a mejorar tu oferta y fijar mejor tus precios, controla tus márgenes para construir un negocio que genere beneficio real.',
	},
	{
		title: 'Cómo gestionar un equipo de agencia autónomo y eficaz',
		body: 'Contratar es fácil, construir un equipo fiable no. Te enseñamos a delegar, liderar y crear responsables claros para que no tengas que empujar todo tú.',
	},
];

function formatIndex(n) {
	return `(${String(n).padStart(2, '0')})`;
}

function getCardTypographyClasses(size) {
	const isLarge = size === 'large';
	return {
		titleIndex: isLarge ? 'text-[24px] leading-normal' : 'text-[20px] leading-normal',
		body: isLarge ? 'text-[20px] leading-normal' : 'text-[16px] leading-normal',
	};
}

function getGridClassName(cardCount) {
	const n = Math.min(Math.max(cardCount, 1), 4);
	const base = 'mwm-cards-01__grid grid items-start gap-6 justify-items-center';
	const cols = {
		1: 'grid-cols-1',
		2: 'grid-cols-1 sm:grid-cols-2',
		3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
		4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
	};
	return `${base} ${cols[n]}`;
}

function EditorButtonPreview({ text, url }) {
	if (!text?.trim()) {
		return null;
	}
	const btnClass =
		'mwm-btn mwm-btn--primary mwm-btn--md mwm-btn--has-icon mwm-btn--icon-after mwm-cards-01__cta';
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
		cards: rawCards = [],
		buttonText = '',
		buttonUrl = '',
		opensInNewTab = false,
		centerHeader = false,
		cardTextSize = 'normal',
	} = attributes;

	const cards =
		Array.isArray(rawCards) && rawCards.length
			? rawCards.map((item, idx) => ({
				...DEFAULT_CARDS[idx % DEFAULT_CARDS.length],
				...item,
			}))
			: DEFAULT_CARDS.map((c) => ({ ...c }));

	const updateCard = (index, field, value) => {
		const next = cards.map((c, i) => {
			const base = { title: c.title ?? '', body: c.body ?? '' };
			if (i === index) {
				return { ...base, [field]: value ?? '' };
			}
			return base;
		});
		setAttributes({ cards: next });
	};

	const addCard = () => {
		setAttributes({
			cards: [...cards, { title: '', body: '' }],
		});
	};

	const removeCard = (index) => {
		const next = cards.filter((_, i) => i !== index);
		setAttributes({ cards: next });
	};

	const cardTypography = getCardTypographyClasses(cardTextSize);

	const blockProps = useBlockProps({
		className: [
			'mwm-cards-01 mwm-cards-01--editor-preview w-full bg-protagonista py-[120px]',
			cardTextSize === 'large' ? 'mwm-cards-01--text-large' : '',
		]
			.filter(Boolean)
			.join(' '),
	});

	const headerClass = centerHeader
		? 'mwm-cards-01__header flex w-full flex-col gap-6 items-center text-center lg:flex-row lg:flex-wrap lg:items-center lg:justify-center lg:gap-8'
		: 'mwm-cards-01__header flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between lg:gap-6';

	const headingWrapClass = centerHeader
		? 'mwm-cards-01__heading-wrap min-w-0 w-full max-w-[636px] mx-auto text-[1.75rem] md:text-3xl lg:text-[40px]'
		: 'mwm-cards-01__heading-wrap min-w-0 flex-1 lg:max-w-[636px] text-[1.75rem] md:text-3xl lg:text-[40px]';

	const headingTextClass =
		'mwm-cards-01__heading text-[1.75rem] font-heading leading-[1.2] text-neutral-light md:text-3xl lg:text-[40px]';

	const ctaWrapClass = centerHeader
		? 'mwm-cards-01__cta-wrap flex w-full shrink-0 flex-col items-center justify-center lg:w-auto lg:max-w-[636px]'
		: 'mwm-cards-01__cta-wrap flex w-full shrink-0 flex-col items-stretch justify-end lg:w-auto lg:max-w-[636px] lg:items-end';

	const showCta = Boolean(buttonText?.trim() && buttonUrl?.trim());
	const gridClassName = getGridClassName(cards.length);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Disposicion', 'zenyx')} initialOpen={false}>
					<ToggleControl
						label={__('Centrar titular y boton', 'zenyx')}
						checked={centerHeader}
						onChange={(value) => setAttributes({ centerHeader: !!value })}
						__nextHasNoMarginBottom
					/>
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

				<PanelBody title={__('Tarjetas', 'zenyx')} initialOpen={true}>
					<SelectControl
						label={__('Tamaño de textos', 'zenyx')}
						value={cardTextSize === 'large' ? 'large' : 'normal'}
						options={[
							{
								label: __('Normal — titulo e indice 20px, cuerpo 16px', 'zenyx'),
								value: 'normal',
							},
							{
								label: __('Grande — titulo e indice 24px, cuerpo 20px', 'zenyx'),
								value: 'large',
							},
						]}
						onChange={(value) => setAttributes({ cardTextSize: value === 'large' ? 'large' : 'normal' })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<Button variant="primary" onClick={addCard} style={{ marginBottom: '12px' }}>
						{__('Anadir tarjeta', 'zenyx')}
					</Button>
					{cards.map((_card, index) => (
						<div
							key={`card-${index}`}
							style={{
								marginBottom: '16px',
								paddingBottom: '12px',
								borderBottom: '1px solid #ddd',
							}}
						>
							<Button variant="link" isDestructive onClick={() => removeCard(index)}>
								{__('Eliminar tarjeta', 'zenyx')}
							</Button>
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps} data-light="">
				<div className="mwm-max-1 flex flex-col gap-20">
					<div className={headerClass}>
						<div className={headingWrapClass}>
							<RichText
								tagName="h2"
								className={headingTextClass}
								value={normalizeHeadingMarkup(heading)}
								onChange={(value) =>
									setAttributes({ heading: normalizeHeadingMarkup(value) })
								}
								placeholder={__('Titular…', 'zenyx')}
								allowedFormats={RICH_TEXT_FORMATS}
							/>
						</div>

						{showCta && (
							<div className={ctaWrapClass}>
								<EditorButtonPreview text={buttonText} url={buttonUrl} />
							</div>
						)}
					</div>

					<div className={gridClassName} role="list">
						{cards.map((card, index) => {
							const rawTitle = card.title ? String(card.title) : '';
							const hasTitle = '' !== rawTitle.replace(/<[^>]+>/g, '').trim();
							return (
								<div key={`card-${index}`} className="mwm-cards-01__card-wrap w-full h-full" role="listitem">
									<div
										className="mwm-cards-01__card outline-none transition-colors duration-300 focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-protagonista"
										tabIndex={0}
									>
										<div className="mwm-cards-01__clip-media w-full max-w-[306px] min-h-0 shrink-0 overflow-hidden">
											<div className="mwm-cards-01__surface relative flex min-h-0 w-full flex-col overflow-hidden">
												<div
													className={`mwm-cards-01__copy relative flex flex-1 flex-col px-5 pt-5 ${hasTitle ? 'min-h-0' : 'min-h-20'
														}`}
												>
													<div className="mwm-cards-01__title-wrap relative z-2 shrink-0">
														<RichText
															tagName="div"
															className={`mwm-cards-01__title max-w-[266px] text-left font-heading text-protagonista ${cardTypography.titleIndex}`}
															value={card.title}
															onChange={(value) => updateCard(index, 'title', value)}
															placeholder={__('Titulo de la tarjeta…', 'zenyx')}
															allowedFormats={RICH_TEXT_FORMATS}
														/>
													</div>

													<div className="mwm-cards-01__body-wrap">
														<RichText
															tagName="div"
															className={`mwm-cards-01__body max-w-[266px] text-left text-protagonista ${cardTypography.body}`}
															value={card.body}
															onChange={(value) => updateCard(index, 'body', value)}
															placeholder={__('Texto al hover…', 'zenyx')}
															allowedFormats={RICH_TEXT_FORMATS}
														/>
													</div>
												</div>

												<div className="mwm-cards-01__index mt-auto flex w-full shrink-0 px-5 pb-5 pt-2 font-heading">
													<p
														className={`mwm-cards-01__index-text w-full max-w-[266px] text-left ${cardTypography.titleIndex}`}
													>
														{formatIndex(index + 1)}
													</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							);
						})}
					</div>
				</div>
			</section>
		</>
	);
}
