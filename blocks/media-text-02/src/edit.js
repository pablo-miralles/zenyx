import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	SelectControl,
	TextareaControl,
	TextControl,
} from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

const ALLOWED_IMAGE_TYPES = ['image'];
const ALLOWED_VIDEO_TYPES = ['video'];

const DEFAULT_PANEL_QUOTE =
	'“Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris aliquet vestibulum mi. Proin varius nunc purus, ac sagittis sem dictum sit amet.”';

/**
 * Alineado con mwm_media_text_02_quote_to_html() en render.php: párrafos sin exigir <p> manual.
 *
 * @param {string} raw
 * @return {string}
 */
function quoteToHtml(raw) {
	const s = String(raw ?? '').trim();
	if (!s) {
		return '';
	}
	if (/^\s*</.test(s)) {
		if (/^\s*<(p|div|blockquote|h[1-6]|ul|ol|figure)\b/i.test(s)) {
			return s;
		}
		return `<p>${s}</p>`;
	}
	const blocks = s.split(/\n\n+/);
	return blocks
		.map((block) => {
			const inner = block.trim().replace(/\n/g, '<br />');
			return inner ? `<p>${inner}</p>` : '';
		})
		.filter(Boolean)
		.join('');
}

/** Separador visual entre texto del slide y medios. */
function InspectorSectionDivider() {
	return (
		<hr
			style={{
				margin: '16px 0',
				border: 0,
				borderTop: '1px solid #e0e0e0',
			}}
		/>
	);
}

function emptyMediaItem() {
	return {
		mediaType: 'image',
		mediaImageId: 0,
		mediaImageUrl: '',
		mediaImageAlt: '',
		mediaVideoId: 0,
		mediaVideoUrl: '',
	};
}

function emptyRow() {
	return {
		layout: 'one',
		panelQuote: DEFAULT_PANEL_QUOTE,
		panelAuthorName: 'Matías Balinot',
		panelAuthorRole: 'Co-founder en Balinot Tech Consulting',
		items: [emptyMediaItem()],
	};
}

/**
 * Fila con textos en el slide (nuevo esquema) o migración desde panel en el primer ítem (contenido antiguo).
 *
 * @param {object} row
 * @return {object}
 */
function normalizeRowForEdit(row) {
	const r = { ...emptyRow(), ...row };
	const items = Array.isArray(r.items) ? r.items : [];
	const first = { ...emptyMediaItem(), ...items[0] };

	const strip = (s) => String(s || '').replace(/<[^>]+>/g, '').trim();

	if (!strip(r.panelQuote) && first.panelQuote) {
		r.panelQuote = first.panelQuote;
	}
	if (!strip(r.panelAuthorName) && first.panelAuthorName) {
		r.panelAuthorName = first.panelAuthorName;
	}
	if (!strip(r.panelAuthorRole) && first.panelAuthorRole) {
		r.panelAuthorRole = first.panelAuthorRole;
	}
	return r;
}

function itemHasMedia(item) {
	const mediaType = item.mediaType || 'image';
	if (mediaType === 'video') {
		return '' !== String(item.mediaVideoUrl || '').trim();
	}
	return '' !== String(item.mediaImageUrl || '').trim();
}

/**
 * Textos del primer slide que tenga al menos un medio (vista previa del lienzo).
 *
 * @param {Array} rows
 * @return {{ panelQuote: string, panelAuthorName: string, panelAuthorRole: string }}
 */
function getPreviewPanelFromRows(rows) {
	for (let ri = 0; ri < rows.length; ri++) {
		const row = normalizeRowForEdit(rows[ri] || {});
		const layout = row.layout === 'two' ? 'two' : 'one';
		const slotCount = layout === 'two' ? 2 : 1;
		for (let ii = 0; ii < slotCount; ii++) {
			const item = { ...emptyMediaItem(), ...(row.items || [])[ii] };
			if (itemHasMedia(item)) {
				return {
					panelQuote: row.panelQuote ?? '',
					panelAuthorName: row.panelAuthorName ?? '',
					panelAuthorRole: row.panelAuthorRole ?? '',
				};
			}
		}
	}
	const r0 = rows.length ? normalizeRowForEdit(rows[0]) : emptyRow();
	return {
		panelQuote: r0.panelQuote ?? '',
		panelAuthorName: r0.panelAuthorName ?? '',
		panelAuthorRole: r0.panelAuthorRole ?? '',
	};
}

function LeftColumnDeco() {
	const url =
		typeof window !== 'undefined' && window.zenyxMediaText02 && window.zenyxMediaText02.degradadoUrl
			? window.zenyxMediaText02.degradadoUrl
			: '';

	if (!url) {
		return null;
	}

	return (
		<div
			className="mwm-media-text-02__left-deco pointer-events-none absolute inset-0 z-0 overflow-hidden"
			aria-hidden="true"
		>
			<img className="mwm-media-text-02__left-deco-img" src={url} alt="" />
		</div>
	);
}

/** Solo archivo: un slide puede tener 1 o 2 medios con el mismo texto. */
function MediaSlotPanel({ item, index, rowIndex, onChangeItem }) {
	const mediaType = item.mediaType || 'image';
	const imageId = item.mediaImageId || 0;
	const imageUrl = item.mediaImageUrl || '';
	const videoId = item.mediaVideoId || 0;
	const videoUrl = item.mediaVideoUrl || '';

	return (
		<div
			className="mwm-mt02-inspector-slot"
			style={{
				marginTop: '8px',
				marginBottom: '4px',
				padding: '10px',
				background: '#f0f0f1',
				borderRadius: '4px',
				border: '1px solid #dcdcde',
			}}
		>
			<p
				style={{
					margin: '0 0 8px',
					fontSize: '11px',
					fontWeight: 600,
					color: '#1e1e1e',
				}}
			>
				{sprintf(
					/* translators: %d: column index (1-based). */
					__('Columna %d', 'zenyx'),
					index + 1
				)}
			</p>
			<SelectControl
				label={__('Tipo', 'zenyx')}
				value={mediaType}
				options={[
					{ label: __('Imagen', 'zenyx'), value: 'image' },
					{ label: __('Video', 'zenyx'), value: 'video' },
				]}
				onChange={(value) => onChangeItem(rowIndex, index, { ...item, mediaType: value })}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			{mediaType === 'image' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={(media) =>
							onChangeItem(rowIndex, index, {
								...item,
								mediaImageId: media.id || 0,
								mediaImageUrl: media.url || '',
								mediaImageAlt: media.alt || '',
							})
						}
						allowedTypes={ALLOWED_IMAGE_TYPES}
						value={imageId}
						render={({ open }) => (
							<div style={{ marginBottom: '4px' }}>
								{imageUrl && (
									<img
										src={imageUrl}
										alt=""
										style={{
											width: '100%',
											maxHeight: '120px',
											marginBottom: '8px',
											objectFit: 'cover',
											borderRadius: '2px',
										}}
									/>
								)}
								<div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px', alignItems: 'center' }}>
									<Button variant={imageId ? 'secondary' : 'primary'} onClick={open}>
										{imageId
											? __('Reemplazar imagen', 'zenyx')
											: __('Seleccionar imagen', 'zenyx')}
									</Button>
									{imageId > 0 && (
										<Button
											variant="link"
											isDestructive
											onClick={() =>
												onChangeItem(rowIndex, index, {
													...item,
													mediaImageId: 0,
													mediaImageUrl: '',
													mediaImageAlt: '',
												})
											}
										>
											{__('Quitar', 'zenyx')}
										</Button>
									)}
								</div>
							</div>
						)}
					/>
				</MediaUploadCheck>
			)}
			{mediaType === 'video' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={(media) =>
							onChangeItem(rowIndex, index, {
								...item,
								mediaVideoId: media.id || 0,
								mediaVideoUrl: media.url || '',
							})
						}
						allowedTypes={ALLOWED_VIDEO_TYPES}
						value={videoId}
						render={({ open }) => (
							<div style={{ marginBottom: '4px' }}>
								{videoUrl && (
									<video
										src={videoUrl}
										style={{
											width: '100%',
											maxHeight: '120px',
											marginBottom: '8px',
											borderRadius: '2px',
										}}
										controls
										muted
									/>
								)}
								<div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px', alignItems: 'center' }}>
									<Button variant={videoId ? 'secondary' : 'primary'} onClick={open}>
										{videoId
											? __('Reemplazar video', 'zenyx')
											: __('Seleccionar video', 'zenyx')}
									</Button>
									{videoId > 0 && (
										<Button
											variant="link"
											isDestructive
											onClick={() =>
												onChangeItem(rowIndex, index, {
													...item,
													mediaVideoId: 0,
													mediaVideoUrl: '',
												})
											}
										>
											{__('Quitar', 'zenyx')}
										</Button>
									)}
								</div>
							</div>
						)}
					/>
				</MediaUploadCheck>
			)}
		</div>
	);
}

function CardPreview({ item, size }) {
	const mediaType = item.mediaType || 'image';
	const imageUrl = item.mediaImageUrl || '';
	const videoUrl = item.mediaVideoUrl || '';
	const imageAlt = item.mediaImageAlt || '';
	const cardClass =
		'mwm-media-text-02__card ' +
		(size === 'sm' ? 'mwm-media-text-02__card--sm' : 'mwm-media-text-02__card--lg');

	const hasMedia =
		mediaType === 'video'
			? '' !== String(videoUrl || '').trim()
			: '' !== String(imageUrl || '').trim();

	if (!hasMedia) {
		return (
			<div className={`${cardClass} mwm-media-text-02__card--empty`}>
				<div className="mwm-media-text-02__card-inner flex items-center justify-center border border-dashed border-protagonista/30 p-4 text-center text-sm text-protagonista/60">
					{__('Configura los medios en el panel derecho (Inspector).', 'zenyx')}
				</div>
			</div>
		);
	}

	return (
		<div className={cardClass}>
			{mediaType === 'video' ? (
				<video className="mwm-media-text-02__card-media" autoPlay muted loop playsInline>
					<source src={videoUrl} type="video/mp4" />
				</video>
			) : (
				<img className="mwm-media-text-02__card-media" src={imageUrl} alt={imageAlt} />
			)}
		</div>
	);
}

function CanvasTextPreview({ leftTitle, previewPanel }) {
	const hasTitle = '' !== String(leftTitle || '').replace(/<[^>]+>/g, '').trim();
	const hasQuote = '' !== String(previewPanel.panelQuote || '').replace(/<[^>]+>/g, '').trim();
	const hasName = '' !== String(previewPanel.panelAuthorName || '').trim();
	const hasRole = '' !== String(previewPanel.panelAuthorRole || '').trim();

	return (
		<>
			<div className="mwm-media-text-02__title-wrap w-full max-w-[636px] shrink-0 pointer-events-none select-none">
				{hasTitle ? (
					<RichText.Content
						tagName="h2"
						className="mwm-media-text-02__title text-left font-heading text-[clamp(1.75rem,4vw,2.5rem)] leading-[1.2] text-protagonista lg:text-[40px]"
						value={leftTitle}
					/>
				) : (
					<p className="mwm-media-text-02__canvas-placeholder m-0 text-left font-heading text-[clamp(1rem,3vw,1.25rem)] italic text-protagonista/45">
						{__('Titular: edítalo en el panel lateral →', 'zenyx')}
					</p>
				)}
			</div>

			<div className="mwm-media-text-02__left-body relative hidden flex-1 flex-col justify-end self-stretch overflow-hidden p-6 lg:flex w-full max-w-[636px] shrink-0 pointer-events-none select-none">
				<LeftColumnDeco />

				<div
					className="mwm-media-text-02__text-stage relative z-10 flex min-h-0 w-full flex-1 flex-col justify-end gap-9 pb-4 lg:pb-5"
					data-mwm-mt02-text-stage
				>
					{hasQuote ? (
						<RichText.Content
							tagName="div"
							className="mwm-media-text-02__quote w-full max-w-[588px] text-left text-[24px] leading-[1.35] text-protagonista [&_p]:m-0 [&_p+p]:mt-4"
							value={quoteToHtml(previewPanel.panelQuote || '')}
						/>
					) : (
						!hasName &&
						!hasRole && (
							<p className="mwm-media-text-02__canvas-placeholder m-0 max-w-[588px] text-left text-base italic text-protagonista/45">
								{__(
									'Cita y autor del slide: edítalos en el inspector (vista previa del primer slide con archivo).',
									'zenyx'
								)}
							</p>
						)
					)}

					{(hasName || hasRole) && (
						<div className="mwm-media-text-02__author flex w-full max-w-[306px] flex-col gap-2">
							{hasName && (
								<RichText.Content
									tagName="p"
									className="mwm-media-text-02__author-name m-0 text-left text-[20px] leading-snug text-protagonista"
									value={previewPanel.panelAuthorName || ''}
								/>
							)}
							{hasRole && (
								<RichText.Content
									tagName="p"
									className="mwm-media-text-02__author-role m-0 text-left text-[16px] leading-snug text-protagonista"
									value={previewPanel.panelAuthorRole || ''}
								/>
							)}
						</div>
					)}
				</div>
			</div>
		</>
	);
}

export default function Edit({ attributes, setAttributes }) {
	const { leftTitle = '', rightRows = [] } = attributes;

	const blockProps = useBlockProps({
		className: 'mwm-media-text-02 w-full bg-neutral-light',
	});

	const updateRow = (rowIndex, nextRow) => {
		const next = [...rightRows];
		next[rowIndex] = nextRow;
		setAttributes({ rightRows: next });
	};

	const onChangeRowPanel = (rowIndex, partial) => {
		const row = normalizeRowForEdit(rightRows[rowIndex] || {});
		updateRow(rowIndex, { ...row, ...partial });
	};

	const setRowLayout = (rowIndex, layout) => {
		const row = normalizeRowForEdit(rightRows[rowIndex] || emptyRow());
		const items = [...(row.items || [])].map((it) => ({ ...emptyMediaItem(), ...it }));
		if (layout === 'two') {
			while (items.length < 2) {
				items.push(emptyMediaItem());
			}
			updateRow(rowIndex, {
				...row,
				layout: 'two',
				items: items.slice(0, 2),
			});
		} else {
			const one = items.slice(0, 1);
			updateRow(rowIndex, {
				...row,
				layout: 'one',
				items: one.length ? one : [emptyMediaItem()],
			});
		}
	};

	const onChangeItem = (rowIndex, itemIndex, item) => {
		const row = normalizeRowForEdit(rightRows[rowIndex] || emptyRow());
		const items = [...(row.items || [])];
		items[itemIndex] = item;
		updateRow(rowIndex, { ...row, items });
	};

	const addRow = () => {
		setAttributes({ rightRows: [...rightRows, emptyRow()] });
	};

	const removeRow = (rowIndex) => {
		const next = rightRows.filter((_, i) => i !== rowIndex);
		setAttributes({ rightRows: next.length ? next : [emptyRow()] });
	};

	const previewPanel = getPreviewPanelFromRows(rightRows);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Titular de la sección', 'zenyx')} initialOpen={true}>
					<p className="components-base-control__help" style={{ marginTop: 0 }}>
						{__('Visible arriba a la izquierda. El lienzo muestra solo una vista previa.', 'zenyx')}
					</p>
					<TextareaControl
						label={__('Titular', 'zenyx')}
						help={__('HTML permitido (por ejemplo &lt;strong&gt;, &lt;em&gt; o &lt;br&gt;).', 'zenyx')}
						value={leftTitle}
						onChange={(value) => setAttributes({ leftTitle: value ?? '' })}
						rows={4}
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody title={__('Slides (columna derecha)', 'zenyx')} initialOpen={true}>
					<p className="components-base-control__help" style={{ marginTop: 0 }}>
						{__(
							'Cada slide tiene un solo texto (cita + autor) y una o dos columnas de imagen o vídeo. El texto cambia al hacer scroll según el slide visible.',
							'zenyx'
						)}
					</p>
					<Button variant="primary" onClick={addRow} style={{ marginBottom: '12px' }}>
						{__('Añadir slide', 'zenyx')}
					</Button>
					{rightRows.map((rowRaw, rowIndex) => {
						const row = normalizeRowForEdit(rowRaw || {});
						const layout = row.layout === 'two' ? 'two' : 'one';
						const items = row.items || [emptyMediaItem()];
						const slotCount = layout === 'two' ? 2 : 1;
						const padded = [...items];
						while (padded.length < slotCount) {
							padded.push(emptyMediaItem());
						}
						return (
							<div
								key={rowIndex}
								style={{
									marginBottom: '16px',
									padding: '12px',
									border: '1px solid #c3c4c7',
									borderRadius: '4px',
									background: '#fff',
								}}
							>
								<p
									style={{
										margin: '0 0 12px',
										fontSize: '13px',
										fontWeight: 600,
									}}
								>
									{sprintf(__('Slide %d', 'zenyx'), rowIndex + 1)}
								</p>

								<TextareaControl
									label={__('Cita del slide', 'zenyx')}
									help={__(
										'Escribe el texto; se mostrará en párrafos. Deja una línea en blanco entre párrafos. También puedes pegar HTML si lo necesitas.',
										'zenyx'
									)}
									value={row.panelQuote ?? ''}
									onChange={(value) =>
										onChangeRowPanel(rowIndex, { panelQuote: value ?? '' })
									}
									rows={5}
									__nextHasNoMarginBottom
								/>
								<TextControl
									label={__('Nombre', 'zenyx')}
									value={row.panelAuthorName ?? ''}
									onChange={(value) =>
										onChangeRowPanel(rowIndex, { panelAuthorName: value ?? '' })
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								<TextControl
									label={__('Cargo o empresa', 'zenyx')}
									value={row.panelAuthorRole ?? ''}
									onChange={(value) =>
										onChangeRowPanel(rowIndex, { panelAuthorRole: value ?? '' })
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>

								<InspectorSectionDivider />

								<p
									style={{
										margin: '0 0 6px',
										fontSize: '11px',
										fontWeight: 600,
										color: '#757575',
									}}
								>
									{__('Disposición y medios', 'zenyx')}
								</p>
								<SelectControl
									label={__('Columnas de medios', 'zenyx')}
									value={layout}
									options={[
										{ label: __('1 columna (grande)', 'zenyx'), value: 'one' },
										{ label: __('2 columnas (pequeñas)', 'zenyx'), value: 'two' },
									]}
									onChange={(value) =>
										setRowLayout(rowIndex, value === 'two' ? 'two' : 'one')
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								{padded.slice(0, slotCount).map((item, itemIndex) => (
									<MediaSlotPanel
										key={itemIndex}
										item={{ ...emptyMediaItem(), ...item }}
										index={itemIndex}
										rowIndex={rowIndex}
										onChangeItem={onChangeItem}
									/>
								))}
								{rightRows.length > 1 && (
									<Button
										isDestructive
										variant="secondary"
										onClick={() => removeRow(rowIndex)}
										style={{ marginTop: '8px' }}
									>
										{__('Eliminar este slide', 'zenyx')}
									</Button>
								)}
							</div>
						);
					})}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps} data-dark="">
				<div className="mwm-max-1 px-4 pt-2 pb-[120px] sm:px-6 lg:px-8">
					<div className="mwm-media-text-02__grid flex flex-col gap-12 lg:flex-row lg:gap-6 xl:gap-8">
						<div className="mwm-media-text-02__left flex w-full min-w-0 flex-col items-start gap-8 self-stretch lg:min-h-screen lg:justify-center lg:gap-8 lg:sticky lg:max-w-[636px] lg:flex-1 lg:self-start">
							<div className="mwm-media-text-02__left-inner flex w-full min-h-0 flex-1 flex-col justify-between lg:min-h-0">
								<CanvasTextPreview leftTitle={leftTitle} previewPanel={previewPanel} />
							</div>
						</div>

						<div className="mwm-media-text-02__right flex min-w-0 flex-1 flex-col gap-8 lg:gap-0">
							{rightRows.map((rowRaw, rowIndex) => {
								const row = normalizeRowForEdit(rowRaw || {});
								const layout = row.layout === 'two' ? 'two' : 'one';
								const items = row.items || [emptyMediaItem()];
								const slotCount = layout === 'two' ? 2 : 1;
								const padded = [...items];
								while (padded.length < slotCount) {
									padded.push(emptyMediaItem());
								}
								const rowClass =
									'mwm-media-text-02__row mwm-media-text-02__row--' + layout;
								const pq = row.panelQuote ?? '';
								const pan = row.panelAuthorName ?? '';
								const par = row.panelAuthorRole ?? '';
								const hasQuote =
									'' !== String(pq || '').replace(/<[^>]+>/g, '').trim();
								const hasName = '' !== String(pan || '').trim();
								const hasRole = '' !== String(par || '').trim();
								const showMobileText = hasQuote || hasName || hasRole;
								return (
									<div className={rowClass} key={rowIndex}>
										<div className="mwm-media-text-02__row-inner">
											{padded.slice(0, slotCount).map((item, i) => (
												<CardPreview
													key={i}
													item={{ ...emptyMediaItem(), ...item }}
													size={layout === 'two' ? 'sm' : 'lg'}
												/>
											))}
										</div>
										{showMobileText && (
											<div className="mwm-media-text-02__mobile-slide-text mt-8 flex w-full max-w-[636px] flex-col gap-6 lg:hidden">
												{hasQuote && (
													<RichText.Content
														tagName="div"
														className="mwm-media-text-02__quote w-full text-left text-[clamp(1.125rem,4.2vw,1.375rem)] leading-[1.4] text-protagonista [&_p]:m-0 [&_p+p]:mt-3"
														value={quoteToHtml(pq)}
													/>
												)}
												{(hasName || hasRole) && (
													<div className="mwm-media-text-02__author flex w-full max-w-[306px] flex-col gap-2">
														{hasName && (
															<RichText.Content
																tagName="p"
																className="mwm-media-text-02__author-name m-0 text-left text-[clamp(1rem,3.5vw,1.125rem)] leading-snug text-protagonista"
																value={pan}
															/>
														)}
														{hasRole && (
															<RichText.Content
																tagName="p"
																className="mwm-media-text-02__author-role m-0 text-left text-[clamp(0.875rem,3vw,1rem)] leading-snug text-protagonista"
																value={par}
															/>
														)}
													</div>
												)}
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
