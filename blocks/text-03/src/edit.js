import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody } from '@wordpress/components';
import { useId } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link', 'zenyx/underline' ];

const DEFAULT_ITEMS = [
	{
		title: 'El Framework Zenyx paso a paso',
		body: 'La hoja de ruta completa con +100 accionables para construir tus 4 procesos principales (ventas, operaciones, finanzas y equipo) sin perder el foco.',
	},
	{
		title: 'Sesiones de Consultoría semanales + Mentorías 1a1',
		body: 'Cada semana, tendrás disponibles talleres, networking y consultorías. Además te acompañamos también en sesiones 1a1 para adaptarnos a tu caso',
	},
	{
		title: 'Arsenal de Recursos: Plantillas, SOPs y Herramientas',
		body: 'Accede a las mismas plantillas, dashboards y procesos que usamos en nuestras propias agencias para ahorrarte cientos de horas.',
	},
	{
		title: 'Comunidad privada de dueños de agencia (sin humo)',
		body: 'El antídoto a la soledad del fundador. Un espacio donde compartir tus retos, celebrar tus victorias y hacer negocios con otros dueños de agencia como tú.',
	},
	{
		title: 'Acompañamiento de consultores que han escalado su propia agencia',
		body: 'Hablarás solo con consultores de agencias que han escalado su propio negocio, por lo que hablarán tu idioma y entenderán todos tus retos',
	},
	{
		title: 'Acceso a Eventos presenciales exclusivos',
		body: 'Invitaciones a nuestros encuentros privados para dueños de agencia. El lugar donde se forjan las alianzas que de verdad impulsan tu negocio.',
	},
];

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', intro = '', items: rawItems = [] } = attributes;

	const reactId = useId();
	const gradientId = `mwm-text-03-${ reactId.replace( /:/g, '' ) }`;

	const items = Array.isArray( rawItems )
		? rawItems.map( ( item, idx ) => ( {
				...DEFAULT_ITEMS[ idx % DEFAULT_ITEMS.length ],
				...item,
		  } ) )
		: DEFAULT_ITEMS.map( ( i ) => ( { ...i } ) );

	const updateItem = ( index, field, value ) => {
		const next = items.map( ( row, i ) => {
			const base = { title: row.title ?? '', body: row.body ?? '' };
			if ( i === index ) {
				return { ...base, [ field ]: value ?? '' };
			}
			return base;
		} );
		setAttributes( { items: next } );
	};

	const addItem = () => {
		setAttributes( { items: [ ...items, { title: '', body: '' } ] } );
	};

	const removeItem = ( index ) => {
		setAttributes( { items: items.filter( ( _, i ) => i !== index ) } );
	};

	const blockProps = useBlockProps( {
		className: 'mwm-text-03 relative w-full overflow-hidden bg-neutral-light py-[120px]',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Elementos', 'zenyx' ) } initialOpen={ true }>
					<Button variant="primary" onClick={ addItem } style={ { marginBottom: '12px' } }>
						{ __( 'Añadir elemento', 'zenyx' ) }
					</Button>
					{ items.map( ( _row, index ) => (
						<div
							key={ `item-${ index }` }
							style={ {
								marginBottom: '16px',
								paddingBottom: '12px',
								borderBottom: '1px solid #ddd',
							} }
						>
							<Button variant="link" isDestructive onClick={ () => removeItem( index ) }>
								{ __( 'Eliminar elemento', 'zenyx' ) }
							</Button>
						</div>
					) ) }
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps } data-dark="">
				<div
					className="mwm-text-03__svg-wrap pointer-events-none absolute bottom-0 right-0 w-full max-w-[min(100%,1076px)]"
					aria-hidden="true"
				>
					<svg
						className="h-auto max-h-[70%] w-full opacity-10"
						width="1076"
						height="837"
						viewBox="0 0 1076 837"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
						preserveAspectRatio="xMaxYMax meet"
					>
						<path
							opacity="0.1"
							d="M230.673 697.044L640.626 697.965V836.889H0V697.044L204.817 492.258H435.474L230.673 697.044ZM873.978 149.152V285.816L667.52 492.26H436.847L643.321 285.816H232.028L231.61 149.152H873.978ZM1076 81.8477L1010.53 147.627H928.416V0H1076V81.8477Z"
							fill={ `url(#${ gradientId })` }
						/>
						<defs>
							<linearGradient
								id={ gradientId }
								x1="538"
								y1="0"
								x2="538"
								y2="836.889"
								gradientUnits="userSpaceOnUse"
							>
								<stop
									stopColor="var(--mwm-text-03-gradient-color, #083b51)"
									stopOpacity="0.8"
								/>
								<stop
									offset="1"
									stopColor="var(--mwm-text-03-gradient-color, #083b51)"
									stopOpacity="0.3"
								/>
							</linearGradient>
						</defs>
					</svg>
				</div>

				<div className="mwm-max-1 relative z-1 flex flex-col gap-[120px]">
					<div className="flex max-w-[636px] flex-col gap-6">
						<RichText
							tagName="h2"
							className="mwm-text-03__heading w-full text-left text-[32px] font-heading leading-[1.2] text-protagonista"
							value={ heading }
							onChange={ ( value ) => setAttributes( { heading: value ?? '' } ) }
							placeholder={ __( 'Titular…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
						<RichText
							tagName="div"
							className="mwm-text-03__intro w-full text-left text-xl leading-[1.2] text-protagonista"
							value={ intro }
							onChange={ ( value ) => setAttributes( { intro: value ?? '' } ) }
							placeholder={ __( 'Introducción…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
					</div>

					<div className="mwm-text-03__grid grid grid-cols-1 gap-x-6 gap-y-20 lg:grid-cols-2">
						{ items.map( ( item, index ) => (
							<div
								key={ `row-${ index }` }
								className="mwm-text-03__item flex flex-col gap-5 md:flex-row md:items-start"
							>
								<RichText
									tagName="h3"
									className="mwm-text-03__item-title min-w-0 flex-1 pr-0 text-left text-[24px] !font-body font-medium text-acento md:pr-6"
									value={ item.title }
									onChange={ ( value ) => updateItem( index, 'title', value ) }
									placeholder={ __( 'Titulo del elemento…', 'zenyx' ) }
									allowedFormats={ RICH_TEXT_FORMATS }
								/>
								<RichText
									tagName="div"
									className="mwm-text-03__item-body min-w-0 flex-1 pr-0 text-left text-base leading-[1.2] text-protagonista md:pr-6"
									value={ item.body }
									onChange={ ( value ) => updateItem( index, 'body', value ) }
									placeholder={ __( 'Texto…', 'zenyx' ) }
									allowedFormats={ RICH_TEXT_FORMATS }
								/>
							</div>
						) ) }
					</div>
				</div>
			</section>
		</>
	);
}
