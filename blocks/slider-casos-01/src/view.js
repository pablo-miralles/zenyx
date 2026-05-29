/**
 * Frontend: una card activa (hover/focus); el track solo se desplaza con flechas.
 * Sin bucle infinito: prev/next se detienen al inicio/fin; el translate se limita al contenido.
 */
( function () {
	'use strict';

	const mq = window.matchMedia( '(min-width: 1024px)' );

	/**
	 * @param {HTMLElement} root
	 */
	function init( root ) {
		const row = root.querySelector( '[data-mwm-slider-casos-row]' );
		const track = root.querySelector( '[data-mwm-slider-casos-track]' );
		const viewport = root.querySelector( '[data-mwm-slider-casos-viewport]' );
		const prevBtn = root.querySelector( '[data-mwm-slider-casos-prev]' );
		const nextBtn = root.querySelector( '[data-mwm-slider-casos-next]' );

		if ( ! row || ! track || ! viewport ) {
			return;
		}

		const cards = () =>
			Array.prototype.slice.call(
				row.querySelectorAll( '[data-mwm-card-caso]' )
			);

		/** Solo flechas; rango [0, n-1], sin wrap. */
		let slideIndex = 0;

		function clampSlideIndex( i, n ) {
			if ( n < 1 ) {
				return 0;
			}
			return Math.max( 0, Math.min( n - 1, i ) );
		}

		function setActiveClasses( index ) {
			const list = cards();
			if ( ! list.length ) {
				return;
			}
			const n = list.length;
			const i = clampSlideIndex( index, n );
			list.forEach( ( c, j ) => {
				c.classList.toggle( 'is-active', j === i );
			} );
		}

		function getMaxScroll() {
			const rowWidth = row.scrollWidth;
			const vpWidth = viewport.clientWidth;
			return Math.max( 0, rowWidth - vpWidth );
		}

		function alignToSlide( index ) {
			const list = cards();
			if ( ! list.length ) {
				return;
			}
			const n = list.length;
			const i = clampSlideIndex( index, n );
			const el = list[ i ];
			if ( ! el ) {
				return;
			}
			requestAnimationFrame( () => {
				const maxScroll = getMaxScroll();
				let translate = -el.offsetLeft;
				translate = Math.max( -maxScroll, Math.min( 0, translate ) );
				track.style.transform = 'translate3d(' + translate + 'px,0,0)';
			} );
		}

		function updateNavButtons() {
			const list = cards();
			const n = list.length;
			const maxScroll = getMaxScroll();

			if ( ! prevBtn || ! nextBtn ) {
				return;
			}

			if ( n <= 1 || maxScroll < 1 ) {
				prevBtn.disabled = true;
				nextBtn.disabled = true;
				return;
			}

			prevBtn.disabled = slideIndex <= 0;
			nextBtn.disabled = slideIndex >= n - 1;
		}

		function goPrev() {
			const list = cards();
			const n = list.length;
			if ( n < 1 || slideIndex <= 0 ) {
				return;
			}
			slideIndex = slideIndex - 1;
			alignToSlide( slideIndex );
			updateNavButtons();
		}

		function goNext() {
			const list = cards();
			const n = list.length;
			if ( n < 1 || slideIndex >= n - 1 ) {
				return;
			}
			slideIndex = slideIndex + 1;
			alignToSlide( slideIndex );
			updateNavButtons();
		}

		slideIndex = 0;
		alignToSlide( 0 );
		updateNavButtons();

		cards().forEach( ( card, index ) => {
			card.addEventListener( 'pointerenter', () => {
				if ( mq.matches ) {
					setActiveClasses( index );
				}
			} );
			card.addEventListener( 'focusin', () => {
				if ( mq.matches ) {
					setActiveClasses( index );
				}
			} );
		} );

		if ( prevBtn ) {
			prevBtn.addEventListener( 'click', goPrev );
		}
		if ( nextBtn ) {
			nextBtn.addEventListener( 'click', goNext );
		}

		function onMqChange() {
			const n = cards().length;
			slideIndex = clampSlideIndex( slideIndex, n );
			alignToSlide( slideIndex );
			updateNavButtons();
		}

		if ( typeof mq.addEventListener === 'function' ) {
			mq.addEventListener( 'change', onMqChange );
		} else if ( typeof mq.addListener === 'function' ) {
			mq.addListener( onMqChange );
		}

		let resizeTimer;
		window.addEventListener( 'resize', () => {
			clearTimeout( resizeTimer );
			resizeTimer = setTimeout( () => {
				const n = cards().length;
				slideIndex = clampSlideIndex( slideIndex, n );
				alignToSlide( slideIndex );
				updateNavButtons();
			}, 200 );
		} );
	}

	const roots = document.querySelectorAll( '[data-mwm-slider-casos-root]' );
	roots.forEach( init );
} )();
