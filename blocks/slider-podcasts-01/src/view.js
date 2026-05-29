/**
 * Frontend: flechas; desktop hover/focus en cards; en touch, arrastre horizontal con snap.
 */
( function () {
	'use strict';

	const mq = window.matchMedia( '(min-width: 1024px)' );
	const AXIS_LOCK_PX = 10;

	/**
	 * @param {HTMLElement} root
	 */
	function init( root ) {
		const row = root.querySelector( '[data-mwm-slider-podcasts-row]' );
		const track = root.querySelector( '[data-mwm-slider-podcasts-track]' );
		const viewport = root.querySelector(
			'[data-mwm-slider-podcasts-viewport]'
		);
		const prevBtn = root.querySelector( '[data-mwm-slider-podcasts-prev]' );
		const nextBtn = root.querySelector( '[data-mwm-slider-podcasts-next]' );

		if ( ! row || ! track || ! viewport ) {
			return;
		}

		const cards = () =>
			Array.prototype.slice.call(
				row.querySelectorAll( '[data-mwm-slider-podcasts-card]' )
			);

		let slideIndex = 0;
		/** @type {number} translate X actual (≤ 0). */
		let currentTranslate = 0;

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

		function applyTranslate( t ) {
			const maxScroll = getMaxScroll();
			currentTranslate = Math.max( -maxScroll, Math.min( 0, t ) );
			track.style.transform =
				'translate3d(' + currentTranslate + 'px,0,0)';
		}

		function translateToNearestSlideIndex( t ) {
			const list = cards();
			if ( ! list.length ) {
				return 0;
			}
			const maxScroll = getMaxScroll();
			const clamped = Math.max( -maxScroll, Math.min( 0, t ) );
			let best = 0;
			let bestDist = Infinity;
			for ( let i = 0; i < list.length; i++ ) {
				const snap = -list[ i ].offsetLeft;
				const d = Math.abs( clamped - snap );
				if ( d < bestDist ) {
					bestDist = d;
					best = i;
				}
			}
			return best;
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
				currentTranslate = translate;
				track.style.transform =
					'translate3d(' + translate + 'px,0,0)';
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
			setActiveClasses( slideIndex );
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
			setActiveClasses( slideIndex );
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

		/* —— Arrastre (pointer): útil en móvil; no bloquea scroll vertical tras decidir eje —— */
		let dragPointerId = null;
		let dragStartX = 0;
		let dragStartY = 0;
		let dragStartTranslate = 0;
		/** @type {null | 'h' | 'v'} */
		let dragAxisLocked = null;
		let dragDidMoveHoriz = false;

		function onPointerDown( e ) {
			if ( e.pointerType === 'mouse' && e.button !== 0 ) {
				return;
			}
			const t = e.target;
			if (
				t instanceof HTMLElement &&
				( t.closest( 'a' ) || t.closest( 'button' ) )
			) {
				return;
			}
			dragPointerId = e.pointerId;
			dragStartX = e.clientX;
			dragStartY = e.clientY;
			dragStartTranslate = currentTranslate;
			dragAxisLocked = null;
			dragDidMoveHoriz = false;
			try {
				viewport.setPointerCapture( e.pointerId );
			} catch ( err ) {
				// ignore
			}
		}

		function onPointerMove( e ) {
			if ( dragPointerId !== e.pointerId ) {
				return;
			}
			const dx = e.clientX - dragStartX;
			const dy = e.clientY - dragStartY;

			if ( dragAxisLocked === null ) {
				if (
					Math.abs( dx ) < AXIS_LOCK_PX &&
					Math.abs( dy ) < AXIS_LOCK_PX
				) {
					return;
				}
				dragAxisLocked =
					Math.abs( dx ) > Math.abs( dy ) ? 'h' : 'v';
			}

			if ( dragAxisLocked === 'v' ) {
				return;
			}

			e.preventDefault();
			dragDidMoveHoriz = true;
			applyTranslate( dragStartTranslate + dx );
		}

		function onPointerUp( e ) {
			if ( dragPointerId !== e.pointerId ) {
				return;
			}
			dragPointerId = null;
			try {
				viewport.releasePointerCapture( e.pointerId );
			} catch ( err ) {
				// ignore
			}

			if ( dragAxisLocked === 'h' && dragDidMoveHoriz ) {
				slideIndex = translateToNearestSlideIndex( currentTranslate );
				alignToSlide( slideIndex );
				updateNavButtons();
				setActiveClasses( slideIndex );
			}
			dragAxisLocked = null;
			dragDidMoveHoriz = false;
		}

		viewport.addEventListener( 'pointerdown', onPointerDown );
		viewport.addEventListener( 'pointermove', onPointerMove );
		viewport.addEventListener( 'pointerup', onPointerUp );
		viewport.addEventListener( 'pointercancel', onPointerUp );
		viewport.addEventListener(
			'lostpointercapture',
			function ( e ) {
				if ( dragPointerId === e.pointerId ) {
					onPointerUp( e );
				}
			}
		);

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

	const roots = document.querySelectorAll( '[data-mwm-slider-podcasts-root]' );
	roots.forEach( init );
} )();
