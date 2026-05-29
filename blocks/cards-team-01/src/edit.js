import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const EMPTY_MEMBER = {
	imageId: 0,
	imageUrl: '',
	imageAlt: '',
	name: '',
	role: '',
	linkedinUrl: '',
};

function ensureMember( item = {} ) {
	return {
		...EMPTY_MEMBER,
		...item,
		imageId: item?.imageId ? Number( item.imageId ) : 0,
	};
}

function ensureMembers( members = [] ) {
	return ( Array.isArray( members ) ? members : [] ).map( ensureMember );
}

function LinkedInIcon( { clipId } ) {
	return (
		<svg
			className="mwm-cards-team-01__linkedin-icon h-6 w-6 shrink-0"
			width="24"
			height="24"
			viewBox="0 0 24 24"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
			focusable="false"
		>
			<g clipPath={ `url(#${ clipId })` }>
				<path
					d="M20.447 20.452H16.893V14.883C16.893 13.555 16.866 11.846 15.041 11.846C13.188 11.846 12.905 13.291 12.905 14.785V20.452H9.351V9H12.765V10.561H12.811C13.288 9.661 14.448 8.711 16.181 8.711C19.782 8.711 20.448 11.081 20.448 14.166L20.447 20.452ZM5.337 7.433C4.193 7.433 3.274 6.507 3.274 5.368C3.274 4.23 4.194 3.305 5.337 3.305C6.477 3.305 7.401 4.23 7.401 5.368C7.401 6.507 6.476 7.433 5.337 7.433ZM7.119 20.452H3.555V9H7.119V20.452ZM22.225 0H1.771C0.792 0 0 0.774 0 1.729V22.271C0 23.227 0.792 24 1.771 24H22.222C23.2 24 24 23.227 24 22.271V1.729C24 0.774 23.2 0 22.222 0H22.225Z"
					fill="currentColor"
				/>
			</g>
			<defs>
				<clipPath id={ clipId }>
					<rect width="24" height="24" fill="white" />
				</clipPath>
			</defs>
		</svg>
	);
}

export default function Edit( { attributes, setAttributes, clientId } ) {
	const heading = attributes?.heading ?? '';
	const intro = attributes?.intro ?? '';
	const members = ensureMembers( attributes?.members );

	const updateMember = ( index, partial ) => {
		const next = [ ...members ];
		next[ index ] = ensureMember( { ...next[ index ], ...partial } );
		setAttributes( { members: next } );
	};

	const addMember = () => {
		setAttributes( { members: [ ...members, { ...EMPTY_MEMBER } ] } );
	};

	const removeMember = ( index ) => {
		setAttributes( { members: members.filter( ( _, i ) => i !== index ) } );
	};

	const moveMember = ( index, direction ) => {
		const target = index + direction;
		if ( target < 0 || target >= members.length ) {
			return;
		}
		const next = [ ...members ];
		[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
		setAttributes( { members: next } );
	};

	const blockProps = useBlockProps( {
		className: 'mwm-cards-team-01 w-full bg-[#083b51] py-[120px]',
	} );

	const idBase = String( clientId || 'cards-team' ).replace( /[^a-zA-Z0-9_-]/g, '' );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Miembros del equipo', 'zenyx' ) } initialOpen={ true }>
					<Button variant="primary" onClick={ addMember } style={ { width: '100%' } }>
						{ __( 'Añadir miembro', 'zenyx' ) }
					</Button>
				</PanelBody>

				{ members.map( ( member, index ) => (
					<PanelBody
						key={ `member-${ index }` }
						title={ __( 'Miembro', 'zenyx' ) + ` ${ index + 1 }` }
						initialOpen={ index === 0 }
					>
						<MediaUploadCheck>
							<MediaUpload
								allowedTypes={ [ 'image' ] }
								value={ member.imageId || undefined }
								onSelect={ ( media ) => {
									if ( ! media || media.type !== 'image' ) {
										return;
									}
									updateMember( index, {
										imageId: media.id || 0,
										imageUrl: media.url || '',
										imageAlt: media.alt || '',
									} );
								} }
								render={ ( { open } ) => (
									<div style={ { display: 'grid', gap: '8px', marginBottom: '12px' } }>
										<Button variant="secondary" onClick={ open } __next40pxDefaultSize>
											{ member.imageId
												? __( 'Reemplazar imagen', 'zenyx' )
												: __( 'Seleccionar imagen', 'zenyx' ) }
										</Button>
										{ member.imageId > 0 && (
											<Button
												variant="tertiary"
												onClick={ () =>
													updateMember( index, { imageId: 0, imageUrl: '', imageAlt: '' } )
												}
											>
												{ __( 'Quitar imagen', 'zenyx' ) }
											</Button>
										) }
									</div>
								) }
							/>
						</MediaUploadCheck>

						<TextControl
							label={ __( 'Nombre', 'zenyx' ) }
							value={ member.name }
							onChange={ ( value ) => updateMember( index, { name: value ?? '' } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={ __( 'Cargo', 'zenyx' ) }
							value={ member.role }
							onChange={ ( value ) => updateMember( index, { role: value ?? '' } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={ __( 'URL de LinkedIn', 'zenyx' ) }
							value={ member.linkedinUrl }
							onChange={ ( value ) => updateMember( index, { linkedinUrl: value ?? '' } ) }
							type="url"
							placeholder="https://"
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>

						<div style={ { display: 'flex', gap: '8px', flexWrap: 'wrap', marginTop: '8px' } }>
							<Button variant="secondary" isSmall onClick={ () => moveMember( index, -1 ) } disabled={ index === 0 }>
								{ __( 'Subir', 'zenyx' ) }
							</Button>
							<Button
								variant="secondary"
								isSmall
								onClick={ () => moveMember( index, 1 ) }
								disabled={ index === members.length - 1 }
							>
								{ __( 'Bajar', 'zenyx' ) }
							</Button>
							<Button variant="tertiary" isDestructive isSmall onClick={ () => removeMember( index ) }>
								{ __( 'Eliminar', 'zenyx' ) }
							</Button>
						</div>
					</PanelBody>
				) ) }
			</InspectorControls>

			<section { ...blockProps } data-dark="">
				<div className="mwm-max-1 flex flex-col gap-20">
					<header className="mwm-cards-team-01__header flex max-w-[636px] flex-col gap-6">
						<div className="mwm-cards-team-01__heading-wrap">
							<RichText
								tagName="h2"
								className="mwm-cards-team-01__heading text-[2rem] font-heading leading-[1.2] text-neutral-light md:text-4xl"
								value={ heading }
								onChange={ ( value ) => setAttributes( { heading: value ?? '' } ) }
								placeholder={ __( 'Titulo de la seccion…', 'zenyx' ) }
								allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
							/>
						</div>
						<div className="mwm-cards-team-01__intro-wrap text-lg leading-[1.3] text-neutral-light md:text-xl">
							<RichText
								tagName="div"
								className="mwm-cards-team-01__intro"
								value={ intro }
								onChange={ ( value ) => setAttributes( { intro: value ?? '' } ) }
								placeholder={ __( 'Texto introductorio…', 'zenyx' ) }
								allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
							/>
						</div>
					</header>

					<div className="mwm-cards-team-01__grid grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
						{ members.map( ( member, index ) => {
							const clipId = `mwm-ct-li-${ idBase }-${ index }`;
							const hasImg = !! String( member.imageUrl || '' ).trim();
							const hasLi = !! String( member.linkedinUrl || '' ).trim();
							return (
								<article key={ `card-${ index }` } className="mwm-cards-team-01__card flex max-w-[306px] flex-col gap-3">
									<figure className="mwm-cards-team-01__media relative aspect-306/336 w-full overflow-hidden bg-white/10">
										{ hasImg ? (
											<img
												className="mwm-cards-team-01__img h-full w-full object-cover"
												src={ member.imageUrl }
												alt={ member.imageAlt || '' }
											/>
										) : (
											<div
												className="flex h-full w-full items-center justify-center text-xs text-neutral-light/60"
												aria-hidden="true"
											>
												{ __( 'Sin imagen', 'zenyx' ) }
											</div>
										) }
									</figure>
									<div className="mwm-cards-team-01__body flex flex-col gap-5">
										<div className="flex flex-col gap-2">
											<p className="mwm-cards-team-01__name text-xl leading-tight text-neutral-light">
												{ member.name || __( 'Nombre', 'zenyx' ) }
											</p>
											<p className="mwm-cards-team-01__role text-base leading-tight text-neutral-light">
												{ member.role || __( 'Cargo', 'zenyx' ) }
											</p>
										</div>
										{ hasLi && (
											<a
												className="mwm-cards-team-01__linkedin inline-flex text-neutral-light no-underline"
												href={ member.linkedinUrl }
												onClick={ ( e ) => e.preventDefault() }
											>
												<LinkedInIcon clipId={ clipId } />
											</a>
										) }
									</div>
								</article>
							);
						} ) }
					</div>
				</div>
			</section>
		</>
	);
}
