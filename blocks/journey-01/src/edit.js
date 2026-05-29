import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from '../block.json';

const DEFAULT_ITEMS = [
	{
		text: 'Tus clientes exigen mucho, pero pagan poco',
		placement: 'above',
	},
	{
		text: 'Cada mes es una montaña rusa: un mes bien, dos meses mal',
		placement: 'below',
	},
	{
		text: 'Tienes que estar encima de todo y explicar 10 veces lo mismo',
		placement: 'above',
	},
	{
		text: 'Trabajas más horas que nunca, pero la rentabilidad no es muy alta',
		placement: 'below',
	},
	{
		text: 'Tienes equipo, pero si te vas de vacaciones, todo se para',
		placement: 'above',
	},
	{
		text: 'Pasas el día apagando fuegos en lugar de construir tu negocio.',
		placement: 'below',
	},
];

const PLACEMENT_OPTIONS = [
	{ label: __( 'Sobre la línea', 'zenyx' ), value: 'above' },
	{ label: __( 'Bajo la línea', 'zenyx' ), value: 'below' },
];

const EMPTY_ITEM = () => ( {
	text: '',
	placement: 'above',
} );

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', items: rawItems = [] } = attributes;

	const items = Array.isArray( rawItems ) && rawItems.length ? rawItems : DEFAULT_ITEMS;

	const blockProps = useBlockProps( {
		className: 'mwm-journey-01-editor',
	} );

	const updateItem = ( index, field, value ) => {
		const next = items.map( ( row, i ) =>
			i === index ? { ...row, [ field ]: value } : row
		);
		setAttributes( { items: next } );
	};

	const addItem = () => {
		setAttributes( { items: [ ...items, EMPTY_ITEM() ] } );
	};

	const removeItem = ( index ) => {
		const next = items.filter( ( _, i ) => i !== index );
		setAttributes( { items: next.length ? next : [ EMPTY_ITEM() ] } );
	};

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Contenido', 'zenyx' ) } initialOpen={ true }>
					<TextareaControl
						label={ __( 'Titular', 'zenyx' ) }
						value={ heading }
						onChange={ ( v ) => setAttributes( { heading: v ?? '' } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody title={ __( 'Puntos del recorrido', 'zenyx' ) } initialOpen={ true }>
					{ items.map( ( item, index ) => (
						<div
							key={ `journey-item-${ index }` }
							style={ {
								marginBottom: '16px',
								paddingBottom: '8px',
								borderBottom: '1px solid #ddd',
							} }
						>
							<TextareaControl
								label={ __( 'Texto', 'zenyx' ) + ` (${ index + 1 })` }
								value={ item.text ?? '' }
								onChange={ ( v ) => updateItem( index, 'text', v ?? '' ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<SelectControl
								label={ __( 'Posición', 'zenyx' ) }
								value={ item.placement === 'below' ? 'below' : 'above' }
								options={ PLACEMENT_OPTIONS }
								onChange={ ( v ) => updateItem( index, 'placement', v ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<Button
								isDestructive
								variant="link"
								onClick={ () => removeItem( index ) }
								disabled={ items.length <= 1 }
							>
								{ __( 'Eliminar punto', 'zenyx' ) }
							</Button>
						</div>
					) ) }
					<Button variant="primary" onClick={ addItem }>
						{ __( 'Añadir punto', 'zenyx' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block={ metadata.name } attributes={ attributes } />
		</div>
	);
}
