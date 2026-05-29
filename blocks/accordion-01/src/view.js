/**
 * Accordion 01: cada ítem abre/cierra de forma independiente; todos cerrados al cargar.
 */
( function () {
	'use strict';

	/**
	 * @param {HTMLElement} root
	 */
	function initAccordion( root ) {
		const items = root.querySelectorAll( '[data-mwm-accordion-item]' );

		items.forEach( ( item ) => {
			const btn = item.querySelector( '[data-mwm-accordion-trigger]' );
			const panel = item.querySelector( '[data-mwm-accordion-panel]' );

			if ( ! btn || ! panel ) {
				return;
			}

			// Compat: bloques guardados con `hidden` (display:none) no animan; quitamos y usamos CSS + inert.
			panel.removeAttribute( 'hidden' );

			btn.setAttribute( 'aria-expanded', 'false' );
			panel.setAttribute( 'aria-hidden', 'true' );
			panel.setAttribute( 'inert', '' );
			item.classList.remove( 'is-open' );

			btn.addEventListener( 'click', () => {
				const expanded = btn.getAttribute( 'aria-expanded' ) === 'true';
				const next = ! expanded;

				btn.setAttribute( 'aria-expanded', String( next ) );

				if ( next ) {
					panel.removeAttribute( 'hidden' );
					panel.removeAttribute( 'inert' );
					panel.removeAttribute( 'aria-hidden' );
					item.classList.add( 'is-open' );
				} else {
					panel.setAttribute( 'inert', '' );
					panel.setAttribute( 'aria-hidden', 'true' );
					item.classList.remove( 'is-open' );
				}
			} );
		} );
	}

	const roots = document.querySelectorAll( '.wp-block-zenyx-accordion-01' );
	roots.forEach( initAccordion );
} )();
