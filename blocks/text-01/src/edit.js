import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const DEFAULT_COLUMNS = [
	{
		title: 'Construcción',
		body: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.',
	},
	{
		title: 'Crecimiento',
		body: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.',
	},
	{
		title: 'Expansión',
		body: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.',
	},
];

const RICH_TEXT_FORMATS = [ 'core/bold', 'core/italic', 'core/link', 'zenyx/underline' ];

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', lead = '', columns: rawColumns = [] } = attributes;

	const safeColumns = Array.isArray( rawColumns ) && rawColumns.length ? rawColumns : DEFAULT_COLUMNS;
	const columns = [ 0, 1, 2 ].map( ( idx ) => ( {
		...DEFAULT_COLUMNS[ idx ],
		...( safeColumns[ idx ] || {} ),
	} ) );

	const updateColumn = ( index, field, value ) => {
		const next = [ ...columns ];
		next[ index ] = {
			...next[ index ],
			[ field ]: value ?? '',
		};
		setAttributes( { columns: next } );
	};

	const blockProps = useBlockProps( {
		className: 'mwm-text-01 w-full bg-[#083b51] py-[120px]',
	} );

	return (
		<section { ...blockProps } data-dark="">
			<div className="mwm-max-1 flex flex-col gap-16 lg:gap-[120px]">
				<div className="mwm-text-01__intro flex flex-col items-center gap-10">
					<div className="mwm-text-01__heading-wrap w-full max-w-[648px]">
						<RichText
							tagName="h2"
							className="mwm-text-01__heading text-center text-[2rem] font-heading leading-[1.2] text-neutral-light md:text-4xl"
							value={ heading }
							onChange={ ( value ) => setAttributes( { heading: value ?? '' } ) }
							placeholder={ __( 'Titular…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
					</div>
					<div className="mwm-text-01__lead-wrap w-full max-w-[416px] text-center text-lg leading-[1.3] text-acento md:text-xl">
						<RichText
							tagName="div"
							className="mwm-text-01__lead"
							value={ lead }
							onChange={ ( value ) => setAttributes( { lead: value ?? '' } ) }
							placeholder={ __( 'Texto destacado…', 'zenyx' ) }
							allowedFormats={ RICH_TEXT_FORMATS }
						/>
					</div>
				</div>

				<div className="mwm-text-01__columns grid grid-cols-1 gap-6 md:grid-cols-3 md:gap-6">
					{ columns.map( ( col, index ) => (
						<div key={ `col-${ index }` } className="mwm-text-01__column flex flex-col items-center gap-5 px-6">
							<div className="mwm-text-01__column-title-wrap w-full max-w-[368px]">
								<RichText
									tagName="h3"
									className="mwm-text-01__column-title text-center text-2xl font-medium leading-tight text-white"
									value={ col.title }
									onChange={ ( value ) => updateColumn( index, 'title', value ) }
									placeholder={ __( 'Título de columna…', 'zenyx' ) }
									allowedFormats={ RICH_TEXT_FORMATS }
								/>
							</div>
							<div className="mwm-text-01__column-body-wrap w-full max-w-[368px] text-center text-base leading-normal text-neutral-light">
								<RichText
									tagName="div"
									className="mwm-text-01__column-body"
									value={ col.body }
									onChange={ ( value ) => updateColumn( index, 'body', value ) }
									placeholder={ __( 'Texto de columna…', 'zenyx' ) }
									allowedFormats={ RICH_TEXT_FORMATS }
								/>
							</div>
						</div>
					) ) }
				</div>
			</div>
		</section>
	);
}
