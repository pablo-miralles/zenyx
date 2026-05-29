import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const EMPTY_IMAGE = {
	imageId: 0,
	imageUrl: '',
	imageAlt: '',
};

function ensureImage( item = {} ) {
	return { ...EMPTY_IMAGE, ...item };
}

function ensureImages( items = [] ) {
	return ( Array.isArray( items ) ? items : [] ).map( ensureImage );
}

export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( {
		className: 'mwm-gallery-01 w-full bg-neutral-light',
	} );

	const heading = attributes?.heading ?? '';
	const description = attributes?.description ?? '';
	const images = ensureImages( attributes?.images );

	const updateImage = ( index, partial ) => {
		const next = [ ...images ];
		next[ index ] = ensureImage( { ...next[ index ], ...partial } );
		setAttributes( { images: next } );
	};

	const addImage = () => {
		setAttributes( { images: [ ...images, { ...EMPTY_IMAGE } ] } );
	};

	const removeImage = ( index ) => {
		setAttributes( { images: images.filter( ( _, i ) => i !== index ) } );
	};

	const moveImage = ( index, direction ) => {
		const target = index + direction;
		if ( target < 0 || target >= images.length ) return;
		const next = [ ...images ];
		[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
		setAttributes( { images: next } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Imágenes', 'zenyx' ) } initialOpen={ true }>
					<Button
						variant="primary"
						onClick={ addImage }
						style={ { marginBottom: '12px', width: '100%' } }
					>
						{ __( 'Añadir imagen', 'zenyx' ) }
					</Button>

					{ images.map( ( img, index ) => (
						<div
							key={ `img-${ index }` }
							style={ {
								border: '1px solid #ddd',
								padding: '10px',
								marginBottom: '8px',
								borderRadius: '4px',
							} }
						>
							<p style={ { margin: '0 0 8px', fontWeight: 600, fontSize: '13px' } }>
								{ __( 'Imagen', 'zenyx' ) } { index + 1 }
							</p>

							{ img.imageUrl ? (
								<img
									src={ img.imageUrl }
									alt={ img.imageAlt }
									style={ {
										width: '100%',
										height: '80px',
										objectFit: 'cover',
										marginBottom: '8px',
										borderRadius: '2px',
									} }
								/>
							) : null }

							<MediaUploadCheck>
								<MediaUpload
									allowedTypes={ [ 'image' ] }
									value={ img.imageId || undefined }
									onSelect={ ( media ) => {
										if ( ! media || media.type !== 'image' ) return;
										updateImage( index, {
											imageId: media.id || 0,
											imageUrl: media.url || '',
											imageAlt: media.alt || '',
										} );
									} }
									render={ ( { open } ) => (
										<div style={ { display: 'grid', gap: '6px' } }>
											<Button variant="secondary" onClick={ open } size="small">
												{ img.imageId
													? __( 'Reemplazar', 'zenyx' )
													: __( 'Seleccionar imagen', 'zenyx' ) }
											</Button>
											{ img.imageId > 0 && (
												<Button
													variant="tertiary"
													size="small"
													onClick={ () =>
														updateImage( index, {
															imageId: 0,
															imageUrl: '',
															imageAlt: '',
														} )
													}
												>
													{ __( 'Quitar', 'zenyx' ) }
												</Button>
											) }
										</div>
									) }
								/>
							</MediaUploadCheck>

							<div style={ { display: 'flex', gap: '6px', marginTop: '8px' } }>
								<Button
									variant="secondary"
									size="small"
									onClick={ () => moveImage( index, -1 ) }
									disabled={ index === 0 }
								>
									&#9650;
								</Button>
								<Button
									variant="secondary"
									size="small"
									onClick={ () => moveImage( index, 1 ) }
									disabled={ index === images.length - 1 }
								>
									&#9660;
								</Button>
								<Button
									variant="tertiary"
									isDestructive
									size="small"
									onClick={ () => removeImage( index ) }
								>
									{ __( 'Eliminar', 'zenyx' ) }
								</Button>
							</div>
						</div>
					) ) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div
					className="mwm-gallery-01__text"
					style={ {
						display: 'flex',
						flexDirection: 'column',
						alignItems: 'center',
						justifyContent: 'center',
						padding: '80px 35px',
					} }
				>
					<div style={ { maxWidth: '636px', width: '100%' } }>
						<RichText
							tagName="h2"
							className="mwm-gallery-01__heading text-center font-heading text-display-m text-protagonista"
							value={ heading }
							onChange={ ( value ) => setAttributes( { heading: value } ) }
							allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
							placeholder={ __( 'Título de la galería…', 'zenyx' ) }
							style={ { margin: 0 } }
						/>
					</div>
					<div style={ { maxWidth: '636px', width: '100%', marginTop: '24px' } }>
						<RichText
							tagName="p"
							className="mwm-gallery-01__desc text-center text-body-l text-protagonista"
							value={ description }
							onChange={ ( value ) => setAttributes( { description: value } ) }
							allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
							placeholder={ __( 'Descripción de la galería…', 'zenyx' ) }
							style={ { margin: 0 } }
						/>
					</div>
				</div>

				{ images.length > 0 && (
					<div
						className="mwm-gallery-01__editor-grid"
						style={ {
							display: 'grid',
							gridTemplateColumns: 'repeat(3, 1fr)',
							gap: '8px',
							padding: '0 35px 40px',
						} }
					>
						{ images.map( ( img, index ) =>
							img.imageUrl ? (
								<div
									key={ `preview-${ index }` }
									style={ {
										overflow: 'hidden',
										borderRadius: '4px',
										aspectRatio: '4/3',
									} }
								>
									<img
										src={ img.imageUrl }
										alt={ img.imageAlt }
										style={ {
											width: '100%',
											height: '100%',
											objectFit: 'cover',
											display: 'block',
										} }
									/>
								</div>
							) : null
						) }
					</div>
				) }
			</div>
		</>
	);
}
