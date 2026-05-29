import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ALLOWED_IMAGE_TYPES = ['image'];
const ALLOWED_VIDEO_TYPES = ['video'];

const RICH_TEXT_FORMATS = ['core/bold', 'core/italic', 'core/link', 'zenyx/underline'];

const DEFAULT_STATS = [
	{ variant: 'single', primary: '+300 agencias analizadas', secondary: '' },
	{ variant: 'single', primary: '+6 años de experiencia', secondary: '' },
	{
		variant: 'double',
		primary: '68,3% de renovación',
		secondary: '(más del doble de la media del sector)',
	},
	{
		variant: 'double',
		primary: 'NPS de 59',
		secondary: '(Clasificado como excelente según Hubspot)',
	},
];

function MediaPanel({
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
		<PanelBody title={__('Media', 'zenyx')} initialOpen={true}>
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
									<video src={videoUrl} style={{ width: '100%', marginBottom: '8px' }} controls muted />
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

export default function Edit({ attributes, setAttributes }) {
	const {
		mediaType = 'image',
		mediaImageId = 0,
		mediaImageUrl = '',
		mediaImageAlt = '',
		mediaVideoId = 0,
		mediaVideoUrl = '',
		stats: rawStats = [],
	} = attributes;

	const hasMedia =
		mediaType === 'video'
			? '' !== String(mediaVideoUrl || '').trim()
			: '' !== String(mediaImageUrl || '').trim();

	const stats = Array.isArray(rawStats)
		? rawStats.map((row, idx) => {
			const def = DEFAULT_STATS[idx % DEFAULT_STATS.length];
			const merged = { ...def, ...(row && typeof row === 'object' ? row : {}) };
			return {
				...merged,
				variant: merged.variant === 'double' ? 'double' : 'single',
			};
		})
		: DEFAULT_STATS.map((s) => ({ ...s }));

	const updateStat = (index, field, value) => {
		const next = stats.map((row, i) => {
			const base = {
				variant: row.variant === 'double' ? 'double' : 'single',
				primary: row.primary ?? '',
				secondary: row.secondary ?? '',
			};
			if (i === index) {
				return { ...base, [field]: value ?? '' };
			}
			return base;
		});
		setAttributes({ stats: next });
	};

	const addStat = () => {
		setAttributes({
			stats: [
				...stats,
				{ variant: 'single', primary: '', secondary: '' },
			],
		});
	};

	const removeStat = (index) => {
		setAttributes({ stats: stats.filter((_, i) => i !== index) });
	};

	const blockProps = useBlockProps({
		className: 'mwm-media-text-03 w-full overflow-hidden bg-neutral-light py-[120px]',
	});

	const primaryClass =
		'mwm-media-text-03__stat-primary text-left text-[24px] font-medium leading-[1.2] text-acento';
	const secondaryClass =
		'mwm-media-text-03__stat-secondary text-left text-base leading-[1.2] text-protagonista md:pr-6';

	return (
		<>
			<InspectorControls>
				<MediaPanel
					mediaType={mediaType}
					imageId={mediaImageId}
					imageUrl={mediaImageUrl}
					videoId={mediaVideoId}
					videoUrl={mediaVideoUrl}
					onChangeType={(value) => setAttributes({ mediaType: value })}
					onSelectImage={(media) =>
						setAttributes({
							mediaImageId: media.id || 0,
							mediaImageUrl: media.url || '',
							mediaImageAlt: media.alt || '',
						})
					}
					onRemoveImage={() =>
						setAttributes({ mediaImageId: 0, mediaImageUrl: '', mediaImageAlt: '' })
					}
					onSelectVideo={(media) =>
						setAttributes({
							mediaVideoId: media.id || 0,
							mediaVideoUrl: media.url || '',
						})
					}
					onRemoveVideo={() => setAttributes({ mediaVideoId: 0, mediaVideoUrl: '' })}
				/>
				<PanelBody title={__('Estadísticas', 'zenyx')} initialOpen={true}>
					<Button variant="primary" onClick={addStat} style={{ marginBottom: '12px' }}>
						{__('Añadir estadística', 'zenyx')}
					</Button>
					{stats.map((row, index) => (
						<div
							key={`stat-${index}`}
							style={{
								marginBottom: '16px',
								paddingBottom: '12px',
								borderBottom: '1px solid #ddd',
							}}
						>
							<SelectControl
								label={__('Tipo de celda', 'zenyx')}
								value={row.variant === 'double' ? 'double' : 'single'}
								options={[
									{ label: __('Una línea', 'zenyx'), value: 'single' },
									{ label: __('Dos líneas', 'zenyx'), value: 'double' },
								]}
								onChange={(v) => updateStat(index, 'variant', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<Button variant="link" isDestructive onClick={() => removeStat(index)}>
								{__('Eliminar estadística', 'zenyx')}
							</Button>
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps} data-dark="">
				<div className="mwm-max-1 flex flex-col gap-6 lg:flex-row lg:item-center">
					<div className="mwm-media-text-03__media-col w-full min-w-0 shrink-0 lg:max-w-[636px] lg:pr-[110px]">
						<div className="mwm-media-text-03__media-shell relative flex lg:min-h-[212px] w-full flex-col overflow-hidden bg-neutral-light">
							{hasMedia && mediaType === 'video' && (
								<video
									className="mwm-media-text-03__media h-full w-full min-h-0 flex-1 object-cover mix-blend-luminosity"
									autoPlay
									muted
									loop
									playsInline
								>
									<source src={mediaVideoUrl} type="video/mp4" />
								</video>
							)}
							{hasMedia && mediaType === 'image' && (
								<img
									className="mwm-media-text-03__media h-full w-full min-h-0 flex-1 object-cover mix-blend-luminosity"
									src={mediaImageUrl}
									alt={mediaImageAlt || ''}
								/>
							)}
						</div>
					</div>

					<div className="mwm-media-text-03__stats grid min-w-0 flex-1 grid-cols-1 gap-6 py-10 xl:grid-cols-2">
						{stats.map((row, index) => {
							const isDouble = row.variant === 'double';
							return (
								<div
									key={`stat-${index}`}
									className={
										isDouble
											? 'mwm-media-text-03__stat mwm-media-text-03__stat--double min-w-0'
											: 'mwm-media-text-03__stat mwm-media-text-03__stat--single min-w-0'
									}
								>
									{isDouble ? (
										<div className="mwm-media-text-03__stat-inner flex flex-col gap-3">
											<RichText
												tagName="div"
												className={primaryClass}
												value={row.primary ?? ''}
												onChange={(v) => updateStat(index, 'primary', v ?? '')}
												placeholder={__('Título…', 'zenyx')}
												allowedFormats={RICH_TEXT_FORMATS}
											/>
											<RichText
												tagName="div"
												className={secondaryClass}
												value={row.secondary ?? ''}
												onChange={(v) => updateStat(index, 'secondary', v ?? '')}
												placeholder={__('Subtítulo…', 'zenyx')}
												allowedFormats={RICH_TEXT_FORMATS}
											/>
										</div>
									) : (
										<RichText
											tagName="div"
											className={primaryClass}
											value={row.primary ?? ''}
											onChange={(v) => updateStat(index, 'primary', v ?? '')}
											placeholder={__('Estadística…', 'zenyx')}
											allowedFormats={RICH_TEXT_FORMATS}
										/>
									)}
								</div>
							);
						})}
					</div>
				</div>
			</section>
		</>
	);
}
