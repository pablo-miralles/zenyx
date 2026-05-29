import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Button, PanelBody, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import MediaPanel from './media-panel';
import metadata from '../block.json';

const DEFAULT_SLIDES = [
	{
		panelTitle: 'Trabajamos en los fundamentos de una agencia',
		panelBody:
			'<p>Nos centramos en lo que funciona de verdad <span class="text-acento">(ventas, operaciones, finanzas)</span>, no en tácticas o métodos de moda.</p>',
		mediaType: 'image',
		mediaImageId: 0,
		mediaImageUrl: '',
		mediaImageAlt: '',
		mediaVideoId: 0,
		mediaVideoUrl: '',
	},
];

const EMPTY_SLIDE = () => ( {
	panelTitle: '',
	panelBody: '',
	mediaType: 'image',
	mediaImageId: 0,
	mediaImageUrl: '',
	mediaImageAlt: '',
	mediaVideoId: 0,
	mediaVideoUrl: '',
} );

function normalizeSlides( attributes ) {
	const raw = attributes.slides;
	if ( Array.isArray( raw ) && raw.length > 0 ) {
		return raw;
	}
	if (
		attributes.panelTitle !== undefined ||
		attributes.panelBody !== undefined ||
		( attributes.mediaImageId !== undefined && attributes.mediaImageId > 0 ) ||
		( attributes.mediaVideoId !== undefined && attributes.mediaVideoId > 0 )
	) {
		return [
			{
				panelTitle: attributes.panelTitle ?? '',
				panelBody: attributes.panelBody ?? '',
				mediaType: attributes.mediaType === 'video' ? 'video' : 'image',
				mediaImageId: attributes.mediaImageId ?? 0,
				mediaImageUrl: attributes.mediaImageUrl ?? '',
				mediaImageAlt: attributes.mediaImageAlt ?? '',
				mediaVideoId: attributes.mediaVideoId ?? 0,
				mediaVideoUrl: attributes.mediaVideoUrl ?? '',
			},
		];
	}
	return DEFAULT_SLIDES;
}

export default function Edit( { attributes, setAttributes } ) {
	const slides = normalizeSlides( attributes );

	const blockProps = useBlockProps( {
		className: 'mwm-media-text-01-editor',
	} );

	const mergedAttributes = {
		...attributes,
		sectionHeading: attributes.sectionHeading ?? '',
		slides,
	};

	const updateSlide = ( index, partial ) => {
		const next = slides.map( ( row, i ) =>
			i === index ? { ...row, ...partial } : row
		);
		setAttributes( { slides: next } );
	};

	const addSlide = () => {
		setAttributes( { slides: [ ...slides, EMPTY_SLIDE() ] } );
	};

	const removeSlide = ( index ) => {
		const next = slides.filter( ( _, i ) => i !== index );
		setAttributes( { slides: next.length ? next : [ EMPTY_SLIDE() ] } );
	};

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Titular de sección', 'zenyx' ) } initialOpen={ true }>
					<TextareaControl
						label={ __( 'Titular', 'zenyx' ) }
						value={ attributes.sectionHeading ?? '' }
						onChange={ ( v ) => setAttributes( { sectionHeading: v ?? '' } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				{ slides.map( ( slide, index ) => (
					<MediaPanel
						key={ `media-text-slide-${ index }` }
						title={ __( 'Slide', 'zenyx' ) + ` (${ index + 1 })` }
						mediaType={ slide.mediaType === 'video' ? 'video' : 'image' }
						imageId={ slide.mediaImageId ?? 0 }
						imageUrl={ slide.mediaImageUrl ?? '' }
						videoId={ slide.mediaVideoId ?? 0 }
						videoUrl={ slide.mediaVideoUrl ?? '' }
						onChangeType={ ( value ) =>
							updateSlide( index, { mediaType: value } )
						}
						onSelectImage={ ( media ) =>
							updateSlide( index, {
								mediaImageId: media.id || 0,
								mediaImageUrl: media.url || '',
								mediaImageAlt: media.alt || '',
							} )
						}
						onRemoveImage={ () =>
							updateSlide( index, {
								mediaImageId: 0,
								mediaImageUrl: '',
								mediaImageAlt: '',
							} )
						}
						onSelectVideo={ ( media ) =>
							updateSlide( index, {
								mediaVideoId: media.id || 0,
								mediaVideoUrl: media.url || '',
							} )
						}
						onRemoveVideo={ () =>
							updateSlide( index, { mediaVideoId: 0, mediaVideoUrl: '' } )
						}
					>
						<TextareaControl
							label={ __( 'Título del panel', 'zenyx' ) }
							value={ slide.panelTitle ?? '' }
							onChange={ ( v ) => updateSlide( index, { panelTitle: v ?? '' } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<TextareaControl
							label={ __( 'Texto del panel (HTML permitido)', 'zenyx' ) }
							value={ slide.panelBody ?? '' }
							onChange={ ( v ) => updateSlide( index, { panelBody: v ?? '' } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							rows={ 5 }
						/>
						<Button
							isDestructive
							variant="link"
							onClick={ () => removeSlide( index ) }
							disabled={ slides.length <= 1 }
						>
							{ __( 'Eliminar slide', 'zenyx' ) }
						</Button>
					</MediaPanel>
				) ) }

				<PanelBody>
					<Button variant="primary" onClick={ addSlide }>
						{ __( 'Añadir slide', 'zenyx' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block={ metadata.name } attributes={ mergedAttributes } />
		</div>
	);
}
