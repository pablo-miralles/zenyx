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
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const EMPTY_ITEM = {
	contentType: 'text',
	text: '',
	variant: 'default',
	imageId: 0,
	imageUrl: '',
	imageAlt: '',
};

const DEFAULT_ITEMS = [
	{
		contentType: 'text',
		text: 'Clientes de +2.000€ al mes o proyectos +6.000€',
		variant: 'default',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		contentType: 'text',
		text: 'Libertad: irte una semana de vacaciones sin portatil',
		variant: 'accent',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		contentType: 'text',
		text: 'Operativa 100% delegada',
		variant: 'default',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		contentType: 'text',
		text: 'Margen bruto por proyecto de +50%',
		variant: 'default',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		contentType: 'text',
		text: 'Tiempo de vida de tus clientes en +1 año',
		variant: 'default',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		contentType: 'text',
		text: 'EBITDA final de +35%',
		variant: 'default',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
];

function ensureItems(items) {
	const raw = Array.isArray(items) ? items : [];
	const out = [];
	for (let i = 0; i < 6; i++) {
		const row = raw[i] && typeof raw[i] === 'object' ? raw[i] : {};
		const def = DEFAULT_ITEMS[i] || EMPTY_ITEM;
		const ctype =
			row.contentType === 'image' ? 'image' : 'text';
		out.push({
			contentType: ctype,
			text: typeof row.text === 'string' ? row.text : def.text,
			variant:
				row.variant === 'accent' ? 'accent' : 'default',
			imageId:
				row.imageId !== undefined && row.imageId !== null
					? Number(row.imageId) || 0
					: def.imageId,
			imageUrl:
				typeof row.imageUrl === 'string' ? row.imageUrl : def.imageUrl,
			imageAlt:
				typeof row.imageAlt === 'string' ? row.imageAlt : def.imageAlt,
		});
	}
	return out;
}

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		className: 'mwm-benefits-01 w-full bg-protagonista',
	});

	const heading = attributes?.heading ?? '';
	const imageId = attributes?.imageId ?? 0;
	const imageUrl = attributes?.imageUrl ?? '';
	const imageAlt = attributes?.imageAlt ?? '';
	const items = ensureItems(attributes?.items);

	const setItem = (index, partial) => {
		const next = [...items];
		next[index] = { ...next[index], ...partial };
		setAttributes({ items: next });
	};

	const updateItemImage = (index, media) => {
		if (!media || media.type !== 'image') {
			return;
		}
		setItem(index, {
			imageId: media.id || 0,
			imageUrl: media.url || '',
			imageAlt: media.alt || '',
		});
	};

	const clearItemImage = (index) => {
		setItem(index, {
			imageId: 0,
			imageUrl: '',
			imageAlt: '',
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Imagen central', 'zenyx')} initialOpen={true}>
					{imageUrl ? (
						<img
							src={imageUrl}
							alt={imageAlt}
							style={{
								width: '100%',
								maxHeight: '160px',
								objectFit: 'cover',
								marginBottom: '12px',
								borderRadius: '4px',
							}}
						/>
					) : null}
					<MediaUploadCheck>
						<MediaUpload
							allowedTypes={['image']}
							value={imageId || undefined}
							onSelect={(media) => {
								if (!media || media.type !== 'image') {
									return;
								}
								setAttributes({
									imageId: media.id || 0,
									imageUrl: media.url || '',
									imageAlt: media.alt || '',
								});
							}}
							render={({ open }) => (
								<div style={{ display: 'grid', gap: '8px' }}>
									<Button variant="secondary" onClick={open}>
										{imageId
											? __('Reemplazar imagen', 'zenyx')
											: __('Seleccionar imagen', 'zenyx')}
									</Button>
									{imageId > 0 && (
										<Button
											variant="tertiary"
											onClick={() =>
												setAttributes({
													imageId: 0,
													imageUrl: '',
													imageAlt: '',
												})
											}
										>
											{__('Quitar imagen', 'zenyx')}
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>

				<PanelBody title={__('Beneficios (6 tarjetas)', 'zenyx')} initialOpen={true}>
					{items.map((item, index) => (
						<div
							key={`benefit-${index}`}
							style={{
								border: '1px solid #ddd',
								padding: '10px',
								marginBottom: '10px',
								borderRadius: '4px',
							}}
						>
							<p style={{ margin: '0 0 8px', fontWeight: 600, fontSize: '13px' }}>
								{__('Tarjeta', 'zenyx')} {index + 1}
							</p>
							<SelectControl
								label={__('Contenido', 'zenyx')}
								value={item.contentType}
								options={[
									{
										label: __('Texto', 'zenyx'),
										value: 'text',
									},
									{
										label: __('Imagen', 'zenyx'),
										value: 'image',
									},
								]}
								onChange={(value) =>
									setItem(index, {
										contentType: value === 'image' ? 'image' : 'text',
									})
								}
								__nextHasNoMarginBottom
								__next40pxDefaultSize
							/>
							{item.contentType === 'image' ? (
								<>
									{item.imageUrl ? (
										<img
											src={item.imageUrl}
											alt={item.imageAlt}
											style={{
												width: '100%',
												maxHeight: '100px',
												objectFit: 'cover',
												marginBottom: '8px',
												borderRadius: '4px',
											}}
										/>
									) : null}
									<MediaUploadCheck>
										<MediaUpload
											allowedTypes={['image']}
											value={item.imageId || undefined}
											onSelect={(media) =>
												updateItemImage(index, media)
											}
											render={({ open }) => (
												<div
													style={{
														display: 'grid',
														gap: '6px',
														marginBottom: '8px',
													}}
												>
													<Button
														variant="secondary"
														size="small"
														onClick={open}
													>
														{item.imageId
															? __('Reemplazar imagen', 'zenyx')
															: __('Seleccionar imagen', 'zenyx')}
													</Button>
													{item.imageId > 0 && (
														<Button
															variant="tertiary"
															size="small"
															onClick={() =>
																clearItemImage(index)
															}
														>
															{__('Quitar imagen', 'zenyx')}
														</Button>
													)}
												</div>
											)}
										/>
									</MediaUploadCheck>
								</>
							) : (
								<TextareaControl
									label={__('Texto', 'zenyx')}
									value={item.text}
									onChange={(value) =>
										setItem(index, { text: value })
									}
									rows={3}
									__nextHasNoMarginBottom
								/>
							)}
							{item.contentType === 'text' ? (
								<SelectControl
									label={__('Estilo', 'zenyx')}
									value={item.variant}
									options={[
										{
											label: __(
												'Por defecto (fondo blanco)',
												'zenyx'
											),
											value: 'default',
										},
										{
											label: __('Acento (coral)', 'zenyx'),
											value: 'accent',
										},
									]}
									onChange={(value) =>
										setItem(index, {
											variant:
												value === 'accent' ? 'accent' : 'default',
										})
									}
									__nextHasNoMarginBottom
									__next40pxDefaultSize
								/>
							) : null}
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div
					className="mwm-benefits-01__text"
					style={{
						display: 'flex',
						flexDirection: 'column',
						alignItems: 'center',
						justifyContent: 'center',
						padding: '80px 35px',
					}}
				>
					<div style={{ maxWidth: '636px', width: '100%' }}>
						<RichText
							tagName="h2"
							className="mwm-benefits-01__heading text-center font-heading text-display-m text-inherit"
							value={heading}
							onChange={(value) => setAttributes({ heading: value })}
							allowedFormats={['core/bold', 'core/italic', 'core/link']}
							placeholder={__('Título de la sección…', 'zenyx')}
							style={{ margin: 0 }}
						/>
					</div>
				</div>

				<div
					className="mwm-benefits-01__editor-preview"
					style={{
						padding: '0 35px 48px',
					}}
				>
					<p
						style={{
							margin: '0 0 12px',
							fontSize: '12px',
							color: '#757575',
						}}
					>
						{__(
							'Vista simplificada. El collage final se ve en el sitio.',
							'zenyx'
						)}
					</p>
					<div
						style={{
							display: 'grid',
							gridTemplateColumns: 'repeat(2, 1fr)',
							gap: '8px',
						}}
					>
						{items.map((item, index) => (
							<div
								key={`prev-${index}`}
								style={{
									background:
										item.contentType === 'image'
											? '#e8e8e8'
											: item.variant === 'accent'
												? '#fe7756'
												: '#fff',
									color:
										item.variant === 'accent' ? '#fff' : '#083b51',
									padding: '12px',
									borderRadius: '4px',
									fontSize: '13px',
									lineHeight: 1.3,
									minHeight: '72px',
									overflow: 'hidden',
								}}
							>
								{item.contentType === 'image' ? (
									item.imageUrl ? (
										<img
											src={item.imageUrl}
											alt={item.imageAlt}
											style={{
												width: '100%',
												height: '64px',
												objectFit: 'cover',
												display: 'block',
												borderRadius: '2px',
											}}
										/>
									) : (
										<span style={{ color: '#757575' }}>
											{__('(Sin imagen)', 'zenyx')}
										</span>
									)
								) : (
									item.text || __('(Sin texto)', 'zenyx')
								)}
							</div>
						))}
					</div>
					{imageUrl ? (
						<div style={{ marginTop: '12px' }}>
							<img
								src={imageUrl}
								alt={imageAlt}
								style={{
									width: '100%',
									maxWidth: '320px',
									height: 'auto',
									borderRadius: '4px',
									display: 'block',
								}}
							/>
						</div>
					) : null}
				</div>
			</div>
		</>
	);
}
