import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { Button, ComboboxControl, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from '../block.json';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link' ];

export default function Edit( { attributes, setAttributes } ) {
	const { sectionTitle = '', manualPostIds = [] } = attributes;

	const blockProps = useBlockProps( {
		className: 'mwm-slider-podcasts-01-editor',
	} );

	const [ postToAdd, setPostToAdd ] = useState( '' );

	const posts = useSelect(
		( select ) =>
			select( 'core' ).getEntityRecords( 'postType', 'podcast', {
				per_page: 100,
				status: 'publish',
				orderby: 'title',
				order: 'asc',
			} ),
		[]
	);

	const postOptions = ( Array.isArray( posts ) ? posts : [] ).map( ( p ) => ( {
		label: p.title?.rendered
			? `${ p.title.rendered }` + ( p.id ? ` (#${ p.id })` : '' )
			: `#${ p.id }`,
		value: String( p.id ),
	} ) );

	const selectedIds = new Set(
		( Array.isArray( manualPostIds ) ? manualPostIds : [] ).map( ( id ) =>
			Number( id )
		)
	);

	const availableOptions = postOptions.filter(
		( o ) => ! selectedIds.has( Number( o.value ) )
	);

	const addPost = ( rawValue ) => {
		const id = Number( rawValue );
		if ( ! id || selectedIds.has( id ) ) {
			return;
		}
		setAttributes( {
			manualPostIds: [ ...manualPostIds, id ],
		} );
	};

	const removePost = ( id ) => {
		setAttributes( {
			manualPostIds: manualPostIds.filter(
				( x ) => Number( x ) !== Number( id )
			),
		} );
	};

	const movePost = ( id, direction ) => {
		const index = manualPostIds.findIndex(
			( x ) => Number( x ) === Number( id )
		);
		if ( index < 0 ) {
			return;
		}
		const targetIndex = direction === 'up' ? index - 1 : index + 1;
		if ( targetIndex < 0 || targetIndex >= manualPostIds.length ) {
			return;
		}
		const next = [ ...manualPostIds ];
		const tmp = next[ targetIndex ];
		next[ targetIndex ] = next[ index ];
		next[ index ] = tmp;
		setAttributes( { manualPostIds: next } );
	};

	const selectedPosts = ( Array.isArray( manualPostIds ) ? manualPostIds : [] )
		.map( ( id ) => {
			const p = ( posts || [] ).find( ( x ) => Number( x.id ) === Number( id ) );
			return {
				id: Number( id ),
				label: p?.title?.rendered
					? `#${ id } ${ p.title.rendered }`
					: `#${ id }`,
			};
		} )
		.filter( ( x ) => x.id );

	return (
		<>
			<style>
				{ `
				.mwm-slider-podcasts-01-editor .mwm-slider-podcasts-01__heading {
					display: none !important;
				}
			` }
			</style>
			<InspectorControls>
				<PanelBody title={ __( 'Podcasts en el slider', 'zenyx' ) } initialOpen={ true }>
					<ComboboxControl
						label={ __( 'Buscar y anadir episodio', 'zenyx' ) }
						value={ postToAdd }
						options={ availableOptions }
						onChange={ ( value ) => {
							if ( value ) {
								addPost( value );
							}
							setPostToAdd( '' );
						} }
						help={
							availableOptions.length
								? __(
										'Escribe para filtrar y anadir al listado.',
										'zenyx'
								  )
								: __(
										'No quedan episodios por anadir.',
										'zenyx'
								  )
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{ selectedPosts.length > 0 && (
						<div
							style={ {
								display: 'grid',
								gap: '8px',
								marginTop: '12px',
							} }
						>
							<strong>
								{ __( 'Orden en el slider', 'zenyx' ) } (
								{ selectedPosts.length })
							</strong>
							{ selectedPosts.map( ( item, index ) => (
								<div
									key={ item.id }
									style={ {
										display: 'flex',
										alignItems: 'center',
										justifyContent: 'space-between',
										gap: '8px',
										padding: '6px 8px',
										border: '1px solid #dcdcde',
										borderRadius: '4px',
										background: '#fff',
									} }
								>
									<span style={ { fontWeight: 500 } }>
										{ item.label }
									</span>
									<div style={ { display: 'flex', gap: '4px' } }>
										<Button
											variant="tertiary"
											disabled={ index === 0 }
											onClick={ () =>
												movePost( item.id, 'up' )
											}
										>
											{ __( 'Subir', 'zenyx' ) }
										</Button>
										<Button
											variant="tertiary"
											disabled={
												index ===
												selectedPosts.length - 1
											}
											onClick={ () =>
												movePost( item.id, 'down' )
											}
										>
											{ __( 'Bajar', 'zenyx' ) }
										</Button>
										<Button
											variant="link"
											isDestructive
											onClick={ () =>
												removePost( item.id )
											}
										>
											{ __( 'Quitar', 'zenyx' ) }
										</Button>
									</div>
								</div>
							) ) }
						</div>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="mwm-max-1 flex flex-col gap-8 px-4 py-8">
					<RichText
						tagName="h2"
						className="font-heading text-2xl text-protagonista"
						value={ sectionTitle }
						onChange={ ( value ) =>
							setAttributes( { sectionTitle: value ?? '' } )
						}
						placeholder={ __( 'Titular de seccion…', 'zenyx' ) }
						allowedFormats={ RICH_TEXT_FORMATS }
					/>
					<ServerSideRender block={ metadata.name } attributes={ attributes } />
				</div>
			</div>
		</>
	);
}
