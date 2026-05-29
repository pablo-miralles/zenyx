/**
 * Marquee media 01: auto-scroll + arrastre (pointer); sustituye la animación CSS en el front.
 */
const DRAG_THRESH = 10;

/**
 * @param {HTMLElement} wrap `data-mwm-marquee-root`
 */
function initMarquee( wrap ) {
	const block = wrap.closest( '.wp-block-zenyx-marquee-media-01' );
	const inner = wrap.querySelector( '.mwm-marquee-media-01__marquee-inner' );
	const viewport = wrap.querySelector( '.mwm-marquee-media-01__viewport' );
	if ( ! block || ! inner || ! viewport ) {
		return;
	}

	const mqReduce = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	if ( mqReduce.matches ) {
		return;
	}

	let half = 0;
	let offset = 0;
	let raf = 0;
	let lastT = 0;
	let auto = true;

	/** @type {number | null} */
	let downPointerId = null;
	let downX = 0;
	let downY = 0;
	let dragStartX = 0;
	let offAtDrag = 0;
	/** 0: no, 1: decidiendo, 2: arrastrando */
	let dragState = 0;

	function getDurationSec() {
		const raw = getComputedStyle( inner ).getPropertyValue( '--mwm-marquee-duration' );
		const n = parseFloat( String( raw || '40' ).replace( 's', '' ) );
		return Math.max( 1, n || 40 );
	}

	function measure() {
		const w = inner.scrollWidth;
		half = w / 2;
		if ( half < 1 ) {
			return;
		}
		while ( offset >= half ) {
			offset -= half;
		}
		while ( offset < 0 ) {
			offset += half;
		}
	}

	function apply() {
		inner.style.transform = `translate3d(${-offset}px,0,0)`;
	}

	function tick( now ) {
		raf = 0;
		if ( half < 1 ) {
			measure();
			if ( half < 1 ) {
				return;
			}
		}
		if ( ! auto ) {
			return;
		}
		if ( ! lastT ) {
			lastT = now;
		}
		const dt = ( now - lastT ) / 1000;
		lastT = now;
		const v = half / getDurationSec();
		offset += v * dt;
		while ( offset >= half ) {
			offset -= half;
		}
		apply();
		raf = requestAnimationFrame( tick );
	}

	function startRaf() {
		if ( raf || ! auto ) {
			return;
		}
		lastT = 0;
		raf = requestAnimationFrame( tick );
	}

	function stopRaf() {
		if ( raf ) {
			cancelAnimationFrame( raf );
			raf = 0;
		}
		lastT = 0;
	}

	function setDragging( on ) {
		if ( on ) {
			block.classList.add( 'mwm-marquee--drag' );
		} else {
			block.classList.remove( 'mwm-marquee--drag' );
		}
	}

	function endPointerSession() {
		window.removeEventListener( 'pointermove', onPointerMove );
		window.removeEventListener( 'pointerup', onPointerUp, true );
		window.removeEventListener( 'pointercancel', onPointerUp, true );
		if ( downPointerId !== null ) {
			try {
				viewport.releasePointerCapture( downPointerId );
			} catch ( err ) {
				// no-op
			}
		}
		downPointerId = null;
		dragState = 0;
		setDragging( false );
		auto = true;
		lastT = 0;
		startRaf();
	}

	function onPointerDown( e ) {
		if ( e.pointerType === 'mouse' && e.button !== 0 ) {
			return;
		}
		measure();
		if ( half < 1 ) {
			return;
		}
		downPointerId = e.pointerId;
		downX = e.clientX;
		downY = e.clientY;
		dragState = 1;
		offAtDrag = offset;
		dragStartX = e.clientX;
		window.addEventListener( 'pointermove', onPointerMove, { passive: false } );
		window.addEventListener( 'pointerup', onPointerUp, true );
		window.addEventListener( 'pointercancel', onPointerUp, true );
	}

	function onPointerMove( e ) {
		if ( e.pointerId !== downPointerId || dragState < 1 ) {
			return;
		}
		const dx = e.clientX - downX;
		const dy = e.clientY - downY;
		if ( dragState === 1 ) {
			if ( Math.hypot( dx, dy ) < DRAG_THRESH ) {
				return;
			}
			if ( Math.abs( dy ) >= Math.abs( dx ) ) {
				downPointerId = null;
				dragState = 0;
				setDragging( false );
				window.removeEventListener( 'pointermove', onPointerMove );
				window.removeEventListener( 'pointerup', onPointerUp, true );
				window.removeEventListener( 'pointercancel', onPointerUp, true );
				/* Sigue el scroll vertical de la página. */
				auto = true;
				return;
			}
			dragState = 2;
			auto = false;
			stopRaf();
			try {
				viewport.setPointerCapture( e.pointerId );
			} catch ( err ) {
				// no-op
			}
			setDragging( true );
		}
		if ( dragState === 2 ) {
			e.preventDefault();
			const delta = e.clientX - dragStartX;
			offset = offAtDrag - delta;
			while ( offset >= half ) {
				offset -= half;
			}
			while ( offset < 0 ) {
				offset += half;
			}
			apply();
		}
	}

	function onPointerUp( e ) {
		if ( e.pointerId !== downPointerId ) {
			return;
		}
		endPointerSession();
	}

	function go() {
		measure();
		if ( half < 1 ) {
			requestAnimationFrame( () => {
				measure();
				if ( half < 1 ) {
					return;
				}
				if ( ! block.classList.contains( 'mwm-marquee-media-01--js' ) ) {
					block.classList.add( 'mwm-marquee-media-01--js' );
					apply();
					startRaf();
				}
			} );
			return;
		}
		block.classList.add( 'mwm-marquee-media-01--js' );
		apply();
		startRaf();
	}

	const ro = new ResizeObserver( () => {
		measure();
		apply();
	} );
	ro.observe( inner );

	inner.querySelectorAll( 'img' ).forEach( ( img ) => {
		if ( img.complete ) {
			return;
		}
		img.addEventListener( 'load', () => {
			measure();
			apply();
			if ( ! block.classList.contains( 'mwm-marquee-media-01--js' ) ) {
				go();
			}
		} );
	} );

	viewport.addEventListener( 'pointerdown', onPointerDown, { passive: true } );

	if ( typeof mqReduce.addEventListener === 'function' ) {
		mqReduce.addEventListener( 'change', () => {
			if ( mqReduce.matches ) {
				auto = true;
				stopRaf();
				inner.style.removeProperty( 'transform' );
				block.classList.remove(
					'mwm-marquee-media-01--js',
					'mwm-marquee--drag'
				);
			} else {
				go();
			}
		} );
	}

	go();
}

document.querySelectorAll( '[data-mwm-marquee-root]' ).forEach( initMarquee );
