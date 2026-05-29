/**
 * Extiende core/group: atributo booleano y clase en el wrapper (.wp-block-group).
 */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

const ATTR = 'mwmSectionColorTransition';
const ATTR_SECOND = 'mwmSectionColorSecondOnly';
const CLASS_NAME = 'mwm-group-section-color-transition';
const DATA_SECOND_ONLY = 'data-mwm-section-color-second-only';

function mergeSecondOnlyData( attributes, wrapperProps ) {
	if ( ! attributes?.[ ATTR ] || ! attributes?.[ ATTR_SECOND ] ) {
		return wrapperProps;
	}
	return {
		...wrapperProps,
		[ DATA_SECOND_ONLY ]: '',
	};
}

function mergeGroupClass( props ) {
	const name = props.name ?? props.block?.name;
	const attributes = props.attributes ?? props.block?.attributes ?? {};
	if ( name !== 'core/group' || ! attributes[ ATTR ] ) {
		return props;
	}
	const extra = CLASS_NAME;
	if ( props.wrapperProps ) {
		const w = props.wrapperProps;
		const wc = w.className ? `${ w.className } ${ extra }` : extra;
		return {
			...props,
			wrapperProps: mergeSecondOnlyData( attributes, {
				...w,
				className: wc,
			} ),
		};
	}
	const base = props.className || '';
	const merged = {
		...props,
		className: base ? `${ base } ${ extra }` : extra,
	};
	if ( attributes[ ATTR_SECOND ] ) {
		merged[ DATA_SECOND_ONLY ] = '';
	}
	return merged;
}

addFilter(
	'blocks.getSaveContent.extraProps',
	'mwm/group-section-color-extra-props',
	( props, blockType, attributes ) => {
		if ( blockType.name !== 'core/group' || ! attributes?.[ ATTR ] ) {
			return props;
		}
		const base = props.className || '';
		const next = {
			...props,
			className: base ? `${ base } ${ CLASS_NAME }` : CLASS_NAME,
		};
		if ( attributes?.[ ATTR_SECOND ] ) {
			next[ DATA_SECOND_ONLY ] = '';
		}
		return next;
	}
);

const withGroupListClass = createHigherOrderComponent(
	( BlockListBlock ) => ( props ) => (
		<BlockListBlock { ...mergeGroupClass( props ) } />
	),
	'withMwmGroupSectionColorListClass'
);

addFilter(
	'editor.BlockListBlock',
	'mwm/group-section-color-list',
	( BlockListBlock ) => withGroupListClass( BlockListBlock )
);

function GroupSectionColorControls( { attributes, setAttributes, clientId } ) {
	const innerCount = useSelect(
		( select ) => {
			const { getBlock } = select( 'core/block-editor' );
			const block = getBlock( clientId );
			return block?.innerBlocks?.length ?? 0;
		},
		[ clientId ]
	);

	const animOn = !! attributes[ ATTR ];
	const secondOnly = !! attributes[ ATTR_SECOND ];
	const showSecondWarning =
		animOn && secondOnly && innerCount < 3;

	return (
		<PanelBody
			title={ __( 'MWM', 'zenyx' ) }
			initialOpen={ false }
		>
			<ToggleControl
				label={ __(
					'Animación de cambio de color entre secciones',
					'zenyx'
				) }
				help={ __(
					'Añade la clase al contenedor del grupo para poder enlazar CSS o scripts de transición.',
					'zenyx'
				) }
				checked={ animOn }
				onChange={ ( value ) => {
					if ( ! value ) {
						setAttributes( {
							[ ATTR ]: false,
							[ ATTR_SECOND ]: false,
						} );
					} else {
						setAttributes( { [ ATTR ]: true } );
					}
				} }
			/>
			{ animOn && (
				<>
					<ToggleControl
						label={ __(
							'Tema alterno solo entre el 2.º y el 3.er bloque',
							'zenyx'
						) }
						help={ __(
							'Mientras el scroll va del umbral del segundo al del tercer bloque, todas las secciones del grupo usan el color contrario juntas; al pasar el tercero, todas vuelven al tema inicial. Con la animación principal sin esta opción, el cambio aplica a todo el grupo hasta el final de la página. Requiere al menos tres bloques internos que generen una sección cada uno.',
							'zenyx'
						) }
						checked={ secondOnly }
						onChange={ ( value ) =>
							setAttributes( { [ ATTR_SECOND ]: value } )
						}
					/>
					{ showSecondWarning && (
						<Notice
							status="warning"
							isDismissible={ false }
						>
							{ __(
								'Añade al menos tres bloques dentro del grupo para que esta opción funcione en el sitio.',
								'zenyx'
							) }
						</Notice>
					) }
				</>
			) }
		</PanelBody>
	);
}

const withGroupInspector = createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		if ( props.name !== 'core/group' ) {
			return <BlockEdit { ...props } />;
		}
		const { attributes, setAttributes, clientId } = props;
		return (
			<Fragment>
				<InspectorControls>
					<GroupSectionColorControls
						attributes={ attributes }
						setAttributes={ setAttributes }
						clientId={ clientId }
					/>
				</InspectorControls>
				<BlockEdit { ...props } />
			</Fragment>
		);
	},
	'withMwmGroupSectionColorInspector'
);

addFilter(
	'editor.BlockEdit',
	'mwm/group-section-color-inspector',
	( BlockEdit ) => withGroupInspector( BlockEdit )
);
