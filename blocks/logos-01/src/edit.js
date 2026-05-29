import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = [
	'core/bold',
	'core/italic',
	'core/link',
	'zenyx/underline',
];

const EMPTY_ITEM = {
	imageId: 0,
	imageUrl: '',
	imageAlt: '',
};

function ensureItem(item = {}) {
	return { ...EMPTY_ITEM, ...item };
}

function ensureItems(items = []) {
	return (Array.isArray(items) ? items : []).map(ensureItem);
}

function cssUrlForMask(url) {
	if (!url) {
		return '';
	}
	return `url(${url})`;
}

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		className: 'mwm-logos-01 w-full bg-neutral-light',
		'data-dark': true,
	});

	const heading = attributes?.heading ?? '';
	const items = ensureItems(attributes?.items);

	const updateItem = (index, partial) => {
		const next = [...items];
		next[index] = ensureItem({ ...next[index], ...partial });
		setAttributes({ items: next });
	};

	const addItem = () => {
		setAttributes({ items: [...items, { ...EMPTY_ITEM }] });
	};

	const removeItem = (index) => {
		setAttributes({ items: items.filter((_, i) => i !== index) });
	};

	const moveItem = (index, direction) => {
		const target = index + direction;
		if (target < 0 || target >= items.length) {
			return;
		}
		const next = [...items];
		[next[index], next[target]] = [next[target], next[index]];
		setAttributes({ items: next });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Logos', 'zenyx')} initialOpen={true}>
					<p style={{ marginTop: 0, fontSize: '12px', color: '#757575' }}>
						{__(
							'Para el color único del bloque, usa PNG o SVG con fondo transparente. JPEG puede verse mal.',
							'zenyx'
						)}
					</p>
					<Button
						variant="primary"
						onClick={addItem}
						style={{ marginBottom: '12px', width: '100%' }}
					>
						{__('Añadir logo', 'zenyx')}
					</Button>

					{items.map((item, index) => (
						<div
							key={`logo-${index}`}
							style={{
								border: '1px solid #ddd',
								padding: '10px',
								marginBottom: '8px',
								borderRadius: '4px',
							}}
						>
							<p style={{ margin: '0 0 8px', fontWeight: 600, fontSize: '13px' }}>
								{__('Logo', 'zenyx')} {index + 1}
							</p>

							<MediaUploadCheck>
								<MediaUpload
									allowedTypes={['image']}
									value={item.imageId || undefined}
									onSelect={(media) => {
										if (!media || media.type !== 'image') {
											return;
										}
										updateItem(index, {
											imageId: media.id || 0,
											imageUrl: media.url || '',
											imageAlt: media.alt || '',
										});
									}}
									render={({ open }) => (
										<div style={{ display: 'grid', gap: '8px' }}>
											{item.imageUrl ? (
												<button
													type="button"
													onClick={open}
													style={{
														margin: 0,
														padding: '8px',
														border: '1px solid #ddd',
														borderRadius: '4px',
														background: '#f6f7f7',
														cursor: 'pointer',
														display: 'flex',
														alignItems: 'center',
														justifyContent: 'center',
														minHeight: '72px',
														width: '100%',
														boxSizing: 'border-box',
													}}
												>
													<img
														src={item.imageUrl}
														alt={item.imageAlt || ''}
														style={{
															display: 'block',
															maxWidth: '100%',
															maxHeight: '120px',
															width: 'auto',
															height: 'auto',
															objectFit: 'contain',
														}}
													/>
												</button>
											) : null}
											<div style={{ display: 'grid', gap: '6px' }}>
												<Button variant="secondary" onClick={open} size="small">
													{item.imageId
														? __('Reemplazar imagen', 'zenyx')
														: __('Seleccionar imagen', 'zenyx')}
												</Button>
												{item.imageId > 0 && (
													<Button
														variant="tertiary"
														size="small"
														onClick={() =>
															updateItem(index, {
																imageId: 0,
																imageUrl: '',
																imageAlt: '',
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

							<div style={{ display: 'flex', gap: '6px', marginTop: '8px' }}>
								<Button
									variant="secondary"
									size="small"
									onClick={() => moveItem(index, -1)}
									disabled={index === 0}
								>
									&#9650;
								</Button>
								<Button
									variant="secondary"
									size="small"
									onClick={() => moveItem(index, 1)}
									disabled={index === items.length - 1}
								>
									&#9660;
								</Button>
								<Button
									variant="tertiary"
									isDestructive
									size="small"
									onClick={() => removeItem(index)}
								>
									{__('Eliminar', 'zenyx')}
								</Button>
							</div>
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="mwm-max-1 flex flex-col-reverse gap-16 py-16 lg:gap-20 lg:py-[120px]">
					{items.some((i) => i.imageUrl) ? (
						<ul
							className="mwm-logos-01__grid grid list-none grid-cols-2 gap-6 p-0 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
							role="list"
						>
							{items.map((item, index) =>
								item.imageUrl ? (
									<li
										key={`preview-${index}`}
										className="mwm-logos-01__cell flex min-h-[60px] items-center justify-center"
									>
										<div
											className="mwm-logos-01__logo relative inline-block max-w-full"
											aria-hidden="true"
										>
											<img
												className="mwm-logos-01__sizer"
												src={item.imageUrl}
												alt=""
												aria-hidden="true"
											/>
											<div
												className="mwm-logos-01__tint absolute inset-0"
												style={{
													'--mwm-logos-src': cssUrlForMask(
														item.imageUrl
													),
												}}
												aria-hidden="true"
											/>
										</div>
									</li>
								) : null
							)}
						</ul>
					) : null}

					<div className="mwm-logos-01__heading-wrap flex justify-center px-0">
						<RichText
							tagName="p"
							className="mwm-logos-01__heading max-w-[636px] text-center font-body text-xl text-inherit lg:text-[20px]"
							value={heading}
							onChange={(value) => setAttributes({ heading: value })}
							allowedFormats={RICH_TEXT_FORMATS}
							placeholder={__('Texto introductorio…', 'zenyx')}
						/>
					</div>
				</div>
			</section>
		</>
	);
}
