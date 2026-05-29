import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextareaControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from '../block.json';

export default function Edit( { attributes, setAttributes } ) {
	const {
		breadcrumbHome = '',
		breadcrumbHomeUrl = '',
		breadcrumbCurrent = '',
		heroTitle = '',
		heroDescription = '',
		cf7Shortcode = '',
		bottomTitle = '',
		scheduleLabel = '',
		scheduleText = '',
		locationLabel = '',
		locationText = '',
		phoneLabel = '',
		phoneText = '',
		emailLabel = '',
		emailText = '',
	} = attributes;

	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form 01 - Hero', 'zenyx' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Breadcrumb (inicio)', 'zenyx' ) }
						value={ breadcrumbHome }
						onChange={ ( value ) => setAttributes( { breadcrumbHome: value ?? '' } ) }
					/>
					<TextControl
						label={ __( 'URL del breadcrumb inicio', 'zenyx' ) }
						help={ __( 'Dejalo vacio para usar la portada del sitio.', 'zenyx' ) }
						value={ breadcrumbHomeUrl }
						onChange={ ( value ) => setAttributes( { breadcrumbHomeUrl: value ?? '' } ) }
						type="url"
						placeholder="https://"
					/>
					<TextControl
						label={ __( 'Breadcrumb (actual)', 'zenyx' ) }
						value={ breadcrumbCurrent }
						onChange={ ( value ) => setAttributes( { breadcrumbCurrent: value ?? '' } ) }
					/>
					<TextareaControl
						label={ __( 'Titulo principal', 'zenyx' ) }
						value={ heroTitle }
						onChange={ ( value ) => setAttributes( { heroTitle: value ?? '' } ) }
						rows={ 3 }
					/>
					<TextareaControl
						label={ __( 'Descripcion', 'zenyx' ) }
						value={ heroDescription }
						onChange={ ( value ) => setAttributes( { heroDescription: value ?? '' } ) }
						rows={ 4 }
					/>
					<TextControl
						label={ __( 'Shortcode CF7', 'zenyx' ) }
						help={ __( 'Ejemplo: [contact-form-7 id=\"123\" title=\"Contacto\"]', 'zenyx' ) }
						value={ cf7Shortcode }
						onChange={ ( value ) => setAttributes( { cf7Shortcode: value ?? '' } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Form 01 - Datos de contacto', 'zenyx' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Titulo bloque inferior', 'zenyx' ) }
						value={ bottomTitle }
						onChange={ ( value ) => setAttributes( { bottomTitle: value ?? '' } ) }
					/>
					<TextControl
						label={ __( 'Etiqueta horario', 'zenyx' ) }
						value={ scheduleLabel }
						onChange={ ( value ) => setAttributes( { scheduleLabel: value ?? '' } ) }
					/>
					<TextareaControl
						label={ __( 'Texto horario', 'zenyx' ) }
						help={ __( 'Usa saltos de linea para separar bloques.', 'zenyx' ) }
						value={ scheduleText }
						onChange={ ( value ) => setAttributes( { scheduleText: value ?? '' } ) }
						rows={ 5 }
					/>
					<TextControl
						label={ __( 'Etiqueta ubicacion', 'zenyx' ) }
						value={ locationLabel }
						onChange={ ( value ) => setAttributes( { locationLabel: value ?? '' } ) }
					/>
					<TextareaControl
						label={ __( 'Texto ubicacion', 'zenyx' ) }
						value={ locationText }
						onChange={ ( value ) => setAttributes( { locationText: value ?? '' } ) }
						rows={ 3 }
					/>
					<TextControl
						label={ __( 'Etiqueta telefono', 'zenyx' ) }
						value={ phoneLabel }
						onChange={ ( value ) => setAttributes( { phoneLabel: value ?? '' } ) }
					/>
					<TextControl
						label={ __( 'Texto telefono', 'zenyx' ) }
						value={ phoneText }
						onChange={ ( value ) => setAttributes( { phoneText: value ?? '' } ) }
					/>
					<TextControl
						label={ __( 'Etiqueta email', 'zenyx' ) }
						value={ emailLabel }
						onChange={ ( value ) => setAttributes( { emailLabel: value ?? '' } ) }
					/>
					<TextControl
						label={ __( 'Texto email', 'zenyx' ) }
						value={ emailText }
						onChange={ ( value ) => setAttributes( { emailText: value ?? '' } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender block={ metadata.name } attributes={ attributes } />
			</div>
		</>
	);
}
