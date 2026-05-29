import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, RangeControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from '../block.json';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link' ];

export default function Edit( { attributes, setAttributes } ) {
	const {
		sectionTitle = '',
		leadText = '',
		centerContent = true,
		marqueeDurationSeconds = 40,
		imageIds = [],
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'mwm-marquee-media-01-editor',
	} );

	const mediaById = useSelect(
		( select ) => {
			const core = select( 'core' );
			const out = {};
			for ( const raw of imageIds ) {
				const id = Number( raw );
				if ( id > 0 ) {
					out[ id ] = core.getMedia( id );
				}
			}
			return out;
		},
		[ imageIds ]
	);

	const getPreviewSrc = ( id ) => {
		const m = mediaById[ Number( id ) ];
		if ( ! m || typeof m !== 'object' ) {
			return '';
		}
		const details = m.media_details || m.mediaDetails;
		const sizes = details?.sizes;
		return (
			sizes?.thumbnail?.source_url ||
			sizes?.medium?.source_url ||
			m.source_url ||
			''
		);
	};

	const addImage = ( media ) => {
		const id = media && media.id ? Number( media.id ) : 0;
		if ( ! id ) {
			return;
		}
		if ( imageIds.includes( id ) ) {
			return;
		}
		setAttributes( { imageIds: [ ...imageIds, id ] } );
	};

	const removeImage = ( id ) => {
		setAttributes( {
			imageIds: imageIds.filter( ( x ) => Number( x ) !== Number( id ) ),
		} );
	};

	const moveImage = ( id, direction ) => {
		const index = imageIds.findIndex(
			( x ) => Number( x ) === Number( id )
		);
		if ( index < 0 ) {
			return;
		}
		const targetIndex = direction === 'up' ? index - 1 : index + 1;
		if ( targetIndex < 0 || targetIndex >= imageIds.length ) {
			return;
		}
		const next = [ ...imageIds ];
		const tmp = next[ targetIndex ];
		next[ targetIndex ] = next[ index ];
		next[ index ] = tmp;
		setAttributes( { imageIds: next } );
	};

	return (
		<>
			<style>
				{ `
				.mwm-marquee-media-01-editor .mwm-marquee-media-01__intro {
					display: none !important;
				}
			` }
			</style>
			<InspectorControls>
				<PanelBody title={ __( 'Disposicion', 'zenyx' ) } initialOpen={ true }>
					<ToggleControl
						label={ __( 'Centrar titulo y texto', 'zenyx' ) }
						checked={ !! centerContent }
						onChange={ ( value ) =>
							setAttributes( { centerContent: !! value } )
						}
						__nextHasNoMarginBottom
					/>
					<RangeControl
						label={ __( 'Duracion del marquee (segundos)', 'zenyx' ) }
						value={ Number( marqueeDurationSeconds ) || 40 }
						onChange={ ( value ) =>
							setAttributes( {
								marqueeDurationSeconds:
									value == null ? 40 : Number( value ),
							} )
						}
						min={ 10 }
						max={ 120 }
						step={ 1 }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				<PanelBody title={ __( 'Imagenes del marquee', 'zenyx' ) } initialOpen={ true }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ addImage }
							allowedTypes={ [ 'image' ] }
							value={ undefined }
							render={ ( { open } ) => (
								<Button variant="primary" onClick={ open }>
									{ __( 'Anadir imagen', 'zenyx' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ imageIds.length > 0 && (
						<div
							style={ {
								display: 'grid',
								gap: '8px',
								marginTop: '12px',
							} }
						>
							<strong>
								{ __( 'Orden en el carril', 'zenyx' ) } (
								{ imageIds.length })
							</strong>
							{ imageIds.map( ( rawId, index ) => {
								const previewSrc = getPreviewSrc( rawId );
								return (
								<div
									key={ rawId }
									style={ {
										display: 'flex',
										flexDirection: 'column',
										alignItems: 'stretch',
										gap: '8px',
										padding: '8px',
										border: '1px solid #dcdcde',
										borderRadius: '4px',
										background: '#fff',
									} }
								>
									<div
										style={ {
											width: '100%',
											minHeight: 72,
											maxHeight: 120,
											borderRadius: '4px',
											overflow: 'hidden',
											background: '#f0f0f1',
											display: 'flex',
											alignItems: 'center',
											justifyContent: 'center',
										} }
										aria-hidden={ previewSrc ? undefined : true }
									>
										{ previewSrc ? (
											<img
												src={ previewSrc }
												alt=""
												style={ {
													maxWidth: '100%',
													maxHeight: 120,
													width: 'auto',
													height: 'auto',
													objectFit: 'contain',
												} }
											/>
										) : (
											<span
												style={ {
													fontSize: 10,
													color: '#787c82',
												} }
											>
												…
											</span>
										) }
									</div>
									<div
										style={ {
											display: 'flex',
											flexDirection: 'column',
											alignItems: 'center',
											gap: '6px',
										} }
									>
										<span
											style={ {
												fontSize: 11,
												color: '#646970',
												fontWeight: 400,
											} }
										>
											#{ rawId }
										</span>
										<div
											style={ {
												display: 'flex',
												flexWrap: 'wrap',
												justifyContent: 'center',
												gap: '4px',
											} }
										>
											<Button
												variant="tertiary"
												disabled={ index === 0 }
												onClick={ () =>
													moveImage( rawId, 'up' )
												}
											>
												{ __( 'Subir', 'zenyx' ) }
											</Button>
											<Button
												variant="tertiary"
												disabled={
													index ===
													imageIds.length - 1
												}
												onClick={ () =>
													moveImage( rawId, 'down' )
												}
											>
												{ __( 'Bajar', 'zenyx' ) }
											</Button>
											<Button
												variant="link"
												isDestructive
												onClick={ () =>
													removeImage( rawId )
												}
											>
												{ __( 'Quitar', 'zenyx' ) }
											</Button>
										</div>
									</div>
								</div>
								);
							} ) }
						</div>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="mwm-max-1 flex flex-col gap-8 px-4 py-8">
					<div
						className={ `flex w-full flex-col gap-10 ${ centerContent ? 'items-center text-center' : 'items-start' }` }
					>
						<RichText
							tagName="h2"
							className={ `font-heading max-w-[648px] text-2xl text-protagonista lg:text-4xl ${ centerContent ? 'text-center' : '' }` }
							value={ sectionTitle }
							onChange={ ( value ) =>
								setAttributes( { sectionTitle: value ?? '' } )
							}
							placeholder={ __( 'Titular de seccion…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
						<RichText
							tagName="div"
							className={ `font-body w-full max-w-[416px] text-lg text-acento ${ centerContent ? 'text-center' : '' }` }
							value={ leadText }
							onChange={ ( value ) =>
								setAttributes( { leadText: value ?? '' } )
							}
							placeholder={ __( 'Texto destacado…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
					</div>
					<ServerSideRender
						block={ metadata.name }
						attributes={ attributes }
					/>
				</div>
			</div>
		</>
	);
}
