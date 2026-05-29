import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ALLOWED_IMAGE_TYPES = [ 'image' ];
const ALLOWED_VIDEO_TYPES = [ 'video' ];

export default function MediaPanel( {
	title,
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
	children,
} ) {
	return (
		<PanelBody title={ title } initialOpen={ false }>
			<SelectControl
				label={ __( 'Tipo de media', 'zenyx' ) }
				value={ mediaType }
				options={ [
					{ label: __( 'Imagen', 'zenyx' ), value: 'image' },
					{ label: __( 'Video', 'zenyx' ), value: 'video' },
				] }
				onChange={ onChangeType }
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			{ mediaType === 'image' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ onSelectImage }
						allowedTypes={ ALLOWED_IMAGE_TYPES }
						value={ imageId }
						render={ ( { open } ) => (
							<>
								{ imageUrl && (
									<img
										src={ imageUrl }
										alt=""
										style={ { width: '100%', marginBottom: '8px', objectFit: 'cover' } }
									/>
								) }
								<Button variant={ imageId ? 'secondary' : 'primary' } onClick={ open }>
									{ imageId
										? __( 'Reemplazar imagen', 'zenyx' )
										: __( 'Seleccionar imagen', 'zenyx' ) }
								</Button>
								{ imageId > 0 && (
									<Button
										variant="link"
										isDestructive
										onClick={ onRemoveImage }
										style={ { marginLeft: '8px' } }
									>
										{ __( 'Eliminar', 'zenyx' ) }
									</Button>
								) }
							</>
						) }
					/>
				</MediaUploadCheck>
			) }
			{ mediaType === 'video' && (
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ onSelectVideo }
						allowedTypes={ ALLOWED_VIDEO_TYPES }
						value={ videoId }
						render={ ( { open } ) => (
							<>
								{ videoUrl && (
									<video
										src={ videoUrl }
										style={ { width: '100%', marginBottom: '8px' } }
										controls
										muted
									/>
								) }
								<Button variant={ videoId ? 'secondary' : 'primary' } onClick={ open }>
									{ videoId
										? __( 'Reemplazar video', 'zenyx' )
										: __( 'Seleccionar video', 'zenyx' ) }
								</Button>
								{ videoId > 0 && (
									<Button
										variant="link"
										isDestructive
										onClick={ onRemoveVideo }
										style={ { marginLeft: '8px' } }
									>
										{ __( 'Eliminar', 'zenyx' ) }
									</Button>
								) }
							</>
						) }
					/>
				</MediaUploadCheck>
			) }
			{ children }
		</PanelBody>
	);
}
