import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const {
		heading = '',
		description = '',
		theme = 'oscuro',
		buttonText = '',
		buttonUrl = '',
		opensInNewTab = false,
		hideArrowOnMobile = false,
	} = attributes;

	const isLightTheme = theme === 'claro';
	const blockProps = useBlockProps({
		className: `mwm-cta-01 w-full py-16 lg:py-[70px] ${isLightTheme ? 'bg-neutral-light' : 'bg-protagonista'
			}`,
		...(isLightTheme ? { 'data-dark': true } : { 'data-light': true }),
		'data-mwm-header-hide-boundary': '1',
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('CTA 01', 'zenyx')} initialOpen={true}>
					<SelectControl
						label={__('Tema', 'zenyx')}
						value={theme}
						options={[
							{ label: __('Oscuro', 'zenyx'), value: 'oscuro' },
							{ label: __('Claro', 'zenyx'), value: 'claro' },
						]}
						onChange={(value) => setAttributes({ theme: value || 'oscuro' })}
					/>
					<TextControl
						label={__('Texto del botón', 'zenyx')}
						value={buttonText}
						onChange={(value) => setAttributes({ buttonText: value ?? '' })}
					/>
					<TextControl
						label={__('URL del botón', 'zenyx')}
						value={buttonUrl}
						onChange={(value) => setAttributes({ buttonUrl: value ?? '' })}
						type="url"
						placeholder="https://"
					/>
					<ToggleControl
						label={__('Abrir en nueva pestaña', 'zenyx')}
						checked={opensInNewTab}
						onChange={(value) => setAttributes({ opensInNewTab: value })}
					/>
					<ToggleControl
						label={__('Ocultar flecha en móvil', 'zenyx')}
						checked={hideArrowOnMobile}
						onChange={(value) => setAttributes({ hideArrowOnMobile: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="mwm-cta-01__shell relative overflow-hidden">
					<div className="mwm-max-1">
						<div className="mwm-cta-01__stage relative">
							<div className="mwm-cta-01__glow" aria-hidden="true"></div>
							<div className="mwm-cta-01__content relative z-10 mx-auto flex w-full max-w-full flex-col items-center justify-center gap-12 px-4 pt-16 pb-12 sm:px-5 md:pt-20 md:pb-14 lg:gap-[60px] lg:px-6 lg:pt-[100px] lg:pb-[60px]">
							<RichText
								tagName="h2"
								className="mwm-cta-01__heading w-full max-w-[636px] text-center font-heading text-display-m leading-[1.2] text-protagonista"
								value={heading}
								onChange={(value) => setAttributes({ heading: value ?? '' })}
								allowedFormats={['core/bold', 'core/italic', 'core/link']}
								placeholder={__('Escribe el titular...', 'zenyx')}
							/>

							<RichText
								tagName="p"
								className="mwm-cta-01__description w-full max-w-[636px] text-center font-body text-base leading-[1.4] text-protagonista lg:text-[24px] lg:leading-[1.4]"
								value={description}
								onChange={(value) => setAttributes({ description: value ?? '' })}
								allowedFormats={['core/bold', 'core/italic', 'core/link']}
								placeholder={__('Descripción (opcional)...', 'zenyx')}
							/>

							{buttonText ? (
								<a
									href={buttonUrl || '#'}
									className="mwm-cta-01__cta mwm-btn mwm-btn--primary"
									target={opensInNewTab ? '_blank' : undefined}
									rel={opensInNewTab ? 'noopener noreferrer' : undefined}
									onClick={(event) => event.preventDefault()}
								>
									{buttonText}
								</a>
							) : null}
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
