import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { Button, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = ['core/bold', 'core/italic', 'core/link', 'zenyx/underline'];

const EMPTY_ROW = { text: '' };

function ensureRow(item = {}) {
	return {
		...EMPTY_ROW,
		...item,
		text: item?.text ?? '',
	};
}

function ensureRows(rows = []) {
	return (Array.isArray(rows) ? rows : []).map(ensureRow);
}

function CheckIcon({ clipId }) {
	return (
		<svg
			className="mwm-podcast-info-01__check-icon h-6 w-6 shrink-0"
			width="24"
			height="24"
			viewBox="0 0 24 24"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
			focusable="false"
		>
			<g clipPath={`url(#${clipId})`}>
				<path
					d="M6.75 9.00002L10.044 13.611C10.1796 13.8009 10.3569 13.9571 10.5623 14.0677C10.7677 14.1783 10.9958 14.2403 11.2289 14.249C11.462 14.2577 11.694 14.2128 11.9071 14.1178C12.1202 14.0228 12.3086 13.8802 12.458 13.701L23.25 0.749023"
					stroke="currentColor"
					strokeWidth="1.5"
					strokeLinecap="round"
					strokeLinejoin="round"
				/>
				<path
					d="M21.75 10.5V20.25C21.75 21.0456 21.4339 21.8087 20.8713 22.3713C20.3087 22.9339 19.5456 23.25 18.75 23.25H3.75C2.95435 23.25 2.19129 22.9339 1.62868 22.3713C1.06607 21.8087 0.75 21.0456 0.75 20.25V5.25C0.75 4.45435 1.06607 3.69129 1.62868 3.12868C2.19129 2.56607 2.95435 2.25 3.75 2.25H16.5"
					stroke="currentColor"
					strokeWidth="1.5"
					strokeLinecap="round"
					strokeLinejoin="round"
				/>
			</g>
			<defs>
				<clipPath id={clipId}>
					<rect width="24" height="24" fill="white" />
				</clipPath>
			</defs>
		</svg>
	);
}

function CornerDecor() {
	return (
		<svg
			className="mwm-podcast-info-01__corner pointer-events-none absolute bottom-0 right-0 h-[172px] w-[172px] shrink-0"
			width="172"
			height="172"
			viewBox="0 0 172 172"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
			focusable="false"
			preserveAspectRatio="xMidYMid meet"
		>
			<path d="M0 172L172 0V172H0Z" fill="var(--color-neutral-light)" />
		</svg>
	);
}

export default function Edit({ attributes, setAttributes, clientId }) {
	const leftKicker = attributes?.leftKicker ?? '';
	const rightKicker = attributes?.rightKicker ?? '';
	const leftItems = ensureRows(attributes?.leftItems);
	const topicItems = ensureRows(attributes?.topicItems);

	const idBase = String(clientId || 'pi01').replace(/[^a-zA-Z0-9_-]/g, '');

	const updateLeftRow = (index, text) => {
		const next = [...leftItems];
		next[index] = ensureRow({ ...next[index], text: text ?? '' });
		setAttributes({ leftItems: next });
	};

	const updateTopicRow = (index, text) => {
		const next = [...topicItems];
		next[index] = ensureRow({ ...next[index], text: text ?? '' });
		setAttributes({ topicItems: next });
	};

	const addLeftRow = () => {
		setAttributes({ leftItems: [...leftItems, { ...EMPTY_ROW }] });
	};

	const removeLeftRow = (index) => {
		setAttributes({ leftItems: leftItems.filter((_, i) => i !== index) });
	};

	const moveLeftRow = (index, direction) => {
		const target = index + direction;
		if (target < 0 || target >= leftItems.length) {
			return;
		}
		const next = [...leftItems];
		[next[index], next[target]] = [next[target], next[index]];
		setAttributes({ leftItems: next });
	};

	const addTopicRow = () => {
		setAttributes({ topicItems: [...topicItems, { ...EMPTY_ROW }] });
	};

	const removeTopicRow = (index) => {
		setAttributes({ topicItems: topicItems.filter((_, i) => i !== index) });
	};

	const moveTopicRow = (index, direction) => {
		const target = index + direction;
		if (target < 0 || target >= topicItems.length) {
			return;
		}
		const next = [...topicItems];
		[next[index], next[target]] = [next[target], next[index]];
		setAttributes({ topicItems: next });
	};

	const blockProps = useBlockProps({
		className: 'mwm-podcast-info-01 w-full bg-neutral-light py-[120px]',
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Ítems “Para ti si…”', 'zenyx')} initialOpen={false}>
					<Button variant="primary" onClick={addLeftRow} style={{ width: '100%' }} __next40pxDefaultSize>
						{__('Añadir ítem', 'zenyx')}
					</Button>
				</PanelBody>
				{leftItems.map((_, index) => (
					<PanelBody
						key={`left-${index}`}
						title={__('Ítem', 'zenyx') + ` ${index + 1}`}
						initialOpen={false}
					>
						<div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap', marginTop: '8px' }}>
							<Button
								variant="secondary"
								isSmall
								onClick={() => moveLeftRow(index, -1)}
								disabled={index === 0}
							>
								{__('Subir', 'zenyx')}
							</Button>
							<Button
								variant="secondary"
								isSmall
								onClick={() => moveLeftRow(index, 1)}
								disabled={index === leftItems.length - 1}
							>
								{__('Bajar', 'zenyx')}
							</Button>
							<Button variant="tertiary" isDestructive isSmall onClick={() => removeLeftRow(index)}>
								{__('Eliminar', 'zenyx')}
							</Button>
						</div>
					</PanelBody>
				))}

				<PanelBody title={__('Temas “Hablamos de…”', 'zenyx')} initialOpen={false}>
					<Button variant="primary" onClick={addTopicRow} style={{ width: '100%' }} __next40pxDefaultSize>
						{__('Añadir tema', 'zenyx')}
					</Button>
				</PanelBody>
				{topicItems.map((_, index) => (
					<PanelBody
						key={`topic-${index}`}
						title={__('Tema', 'zenyx') + ` ${index + 1}`}
						initialOpen={false}
					>
						<div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap', marginTop: '8px' }}>
							<Button
								variant="secondary"
								isSmall
								onClick={() => moveTopicRow(index, -1)}
								disabled={index === 0}
							>
								{__('Subir', 'zenyx')}
							</Button>
							<Button
								variant="secondary"
								isSmall
								onClick={() => moveTopicRow(index, 1)}
								disabled={index === topicItems.length - 1}
							>
								{__('Bajar', 'zenyx')}
							</Button>
							<Button variant="tertiary" isDestructive isSmall onClick={() => removeTopicRow(index)}>
								{__('Eliminar', 'zenyx')}
							</Button>
						</div>
					</PanelBody>
				))}
			</InspectorControls>

			<section {...blockProps} data-dark="">
				<div className="mwm-max-1 flex flex-col gap-12 lg:flex-row lg:justify-between lg:gap-8">
					<div className="mwm-podcast-info-01__left flex min-w-0 max-w-[636px] flex-1 flex-col justify-between gap-12">
						<div className="mwm-podcast-info-01__left-kicker-wrap flex flex-col gap-3">
							<RichText
								tagName="p"
								className="mwm-podcast-info-01__left-kicker text-left text-xl leading-normal text-acento"
								value={leftKicker}
								onChange={(value) => setAttributes({ leftKicker: value ?? '' })}
								placeholder={__('Titular columna izquierda…', 'zenyx')}
								allowedFormats={RICH_TEXT_FORMATS}
							/>
						</div>

						<div className="mwm-podcast-info-01__grid grid grid-cols-1 gap-6 sm:grid-cols-2">
							{leftItems.map((row, index) => {
								const clipId = `mwm-pi01-check-${idBase}-${index}`;
								return (
									<div
										key={`left-cell-${index}`}
										className="mwm-podcast-info-01__cell flex flex-col gap-5 text-protagonista"
									>
										<CheckIcon clipId={clipId} />
										<RichText
											tagName="div"
											className="mwm-podcast-info-01__cell-text text-left text-2xl leading-normal text-protagonista"
											value={row.text}
											onChange={(value) => updateLeftRow(index, value)}
											placeholder={__('Texto del ítem…', 'zenyx')}
											allowedFormats={RICH_TEXT_FORMATS}
										/>
									</div>
								);
							})}
						</div>
					</div>

					<div className="mwm-podcast-info-01__card relative flex w-full justify-between min-h-[416px] max-w-[416px] shrink-0 flex-col gap-6 overflow-hidden bg-white p-6">
						<div className="mwm-podcast-info-01__card-header relative z-10 flex flex-col gap-3">
							<RichText
								tagName="p"
								className="mwm-podcast-info-01__right-kicker text-left text-xl leading-normal text-acento"
								value={rightKicker}
								onChange={(value) => setAttributes({ rightKicker: value ?? '' })}
								placeholder={__('Titular tarjeta…', 'zenyx')}
								allowedFormats={RICH_TEXT_FORMATS}
							/>
						</div>

						<div className="mwm-podcast-info-01__topics relative z-10 flex flex-col gap-6">
							{topicItems.map((row, index) => (
								<div key={`topic-${index}`} className="mwm-podcast-info-01__topic-row flex flex-col gap-5">
									<RichText
										tagName="div"
										className="mwm-podcast-info-01__topic-text text-left text-2xl leading-normal text-protagonista"
										value={row.text}
										onChange={(value) => updateTopicRow(index, value)}
										placeholder={__('Tema…', 'zenyx')}
										allowedFormats={RICH_TEXT_FORMATS}
									/>
								</div>
							))}
						</div>

						<CornerDecor />
					</div>
				</div>
			</section>
		</>
	);
}
