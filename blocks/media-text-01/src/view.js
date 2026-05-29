/**
 * Media text 01: pin sin scrub; cada gesto de scroll (rueda / swipe) anima al slide siguiente o anterior.
 * Por debajo de `md` (768px en Tailwind) no hay GSAP, pin ni listeners: scroll nativo (layout móvil en render.php).
 */
import 'swiper/css';
import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/**
 * Distancia de scroll por paso dentro del pin (no hace falta 100vh; menos = menos scroll total y salida más cómoda).
 */
const pinScrollStep = () => {
	const h =
		typeof window !== 'undefined' && window.innerHeight > 0
			? window.innerHeight
			: 800;
	return Math.max(260, Math.round(h * 0.42));
};

const ANIM = {
	duration: 0.55,
	ease: 'power2.out',
};

const PIN_GAP_TOP = 48;
const PIN_GAP_BOTTOM = 48;

/** Tiempo tras terminar una animación donde no se acepta otro paso (evita saltos con rueda rápida). */
const STEP_COOLDOWN_MS = 200;

/** Bloques con pin activo (para overscroll en html sin quitar la clase si hay otro bloque). */
let pinActiveLockCount = 0;

function pinActiveEnter() {
	if (pinActiveLockCount++ === 0) {
		document.documentElement.classList.add('mwm-media-text-01-pin-active');
	}
}

function pinActiveLeave() {
	if (--pinActiveLockCount <= 0) {
		pinActiveLockCount = 0;
		document.documentElement.classList.remove('mwm-media-text-01-pin-active');
	}
}

/**
 * Scroll del documento compatible con Lenis (si existe).
 *
 * @param {number} y
 */
function scrollDocTo(y) {
	const lenis = window.lenis;
	if (lenis && typeof lenis.scrollTo === 'function') {
		// `force`: aplica aunque Lenis esté “stopped”; evita que un gesto fuerte deje el scroll fuera de sitio.
		lenis.scrollTo(y, { immediate: true, force: true });
	} else {
		window.scrollTo({ top: y, behavior: 'instant' });
	}
}

/**
 * Posición vertical coherente con Lenis (no usar solo pageYOffset: con smooth scroll difiere del valor interno).
 *
 * @return {number}
 */
function getDocScrollY() {
	const lenis = window.lenis;
	if (
		lenis &&
		typeof lenis.scroll === 'number' &&
		Number.isFinite(lenis.scroll)
	) {
		return lenis.scroll;
	}
	return (
		window.pageYOffset ||
		document.documentElement.scrollTop ||
		0
	);
}

/**
 * @param {HTMLElement} root
 * @param {number} instanceIndex
 */
function initMediaText01(root, instanceIndex) {
	const slideCount = parseInt(root.getAttribute('data-slide-count') || '1', 10);
	const pinTarget = root.querySelector('[data-mwm-media-text-01-pin-target]');
	const mediaViewport = root.querySelector('[data-mwm-media-text-01-media-viewport]');
	const mediaTrack = root.querySelector('[data-mwm-media-text-01-media-track]');
	const titleViewport = root.querySelector('[data-mwm-media-text-01-title-viewport]');
	const titleTrack = root.querySelector('[data-mwm-media-text-01-title-track]');
	const bodyViewport = root.querySelector('[data-mwm-media-text-01-body-viewport]');
	const bodyTrack = root.querySelector('[data-mwm-media-text-01-body-track]');

	if (
		!pinTarget ||
		!mediaViewport ||
		!mediaTrack ||
		!titleViewport ||
		!titleTrack ||
		!bodyViewport ||
		!bodyTrack
	) {
		return;
	}

	const mqReduce = window.matchMedia('(prefers-reduced-motion: reduce)');
	/** Alineado con `md:` del bloque (768px): misma frontera que grid desktop vs. tarjetas móviles. */
	const mqMobile = window.matchMedia('(max-width: 767px)');

	const triggerId = `mwm-media-text-01-${instanceIndex}`;
	const maxIdx = Math.max(0, slideCount - 1);

	let currentIndex = 0;
	let animating = false;
	/** @type {number} */
	let cooldownUntil = 0;
	let touchStartY = 0;

	/** @type {((e: WheelEvent) => void) | null} */
	let wheelHandler = null;
	/** @type {((e: TouchEvent) => void) | null} */
	let touchStartHandler = null;
	/** @type {((e: TouchEvent) => void) | null} */
	let touchEndHandler = null;
	let pinLockHeld = false;

	/**
	 * Offset estable para el pin (header + barra WP). Evita leer getBoundingClientRect:
	 * con sesión iniciada o con el header animado (hide on footer) provoca saltos al refrescar ST.
	 *
	 * @param {string} name
	 * @param {number} fallback
	 * @return {number}
	 */
	function readRootCssPx(name, fallback = 0) {
		const raw = getComputedStyle(document.documentElement)
			.getPropertyValue(name)
			.trim();
		if (!raw) {
			return fallback;
		}
		const n = parseFloat(raw);
		return Number.isFinite(n) ? n : fallback;
	}

	function getPinStartOffset() {
		const adminBar = readRootCssPx('--wp-admin--admin-bar--height', 0);
		const headerH = readRootCssPx('--header-height', 68);
		return Math.max(0, Math.round(adminBar + headerH + PIN_GAP_TOP));
	}

	function applyPinCssVars() {
		root.style.setProperty('--mwm-media-text-01-pin-gap-bottom', `${PIN_GAP_BOTTOM}px`);
		root.style.setProperty('--mwm-media-text-01-pin-offset', `${getPinStartOffset()}px`);
	}

	const bodySlides = () =>
		bodyTrack.querySelectorAll('[data-mwm-media-text-01-body-slide]');

	/** Solo corregir scroll si el desfase es grande (evita micro-saltos con Lenis / admin bar). */
	const PIN_SCROLL_SYNC_THRESHOLD = 28;

	/**
	 * Misma lógica que syncPinScrollToIndex: posición absoluta del documento para un índice de slide.
	 *
	 * @param {{ start: number, end: number }} st
	 * @param {number} index
	 * @return {number}
	 */
	function scrollYForPinIndex(st, index) {
		const step = pinScrollStep();
		if (index <= 0) {
			return st.start;
		}
		if (index >= maxIdx) {
			return st.end;
		}
		return st.start + index * step;
	}

	/**
	 * Alturas en % sobre flex suelen fallar: el track y cada slide se fijan en px
	 * para coincidir con translateY (-index * bodyViewport.clientHeight).
	 *
	 * @return {boolean}
	 */
	function syncBodyDimensions() {
		const h = bodyViewport.clientHeight;
		if (h < 1) {
			return false;
		}
		bodyTrack.style.height = `${slideCount * h}px`;
		bodySlides().forEach((el) => {
			el.style.height = `${h}px`;
			el.style.flexShrink = '0';
		});
		return true;
	}

	function clearBodyDimensions() {
		bodyTrack.style.removeProperty('height');
		bodySlides().forEach((el) => {
			el.style.removeProperty('height');
			el.style.removeProperty('flex-shrink');
		});
	}

	function applySlideIndex(index) {
		const mw = mediaViewport.clientWidth;
		const th = titleViewport.clientHeight;
		const bh = bodyViewport.clientHeight;
		gsap.set(mediaTrack, { x: -index * mw });
		gsap.set(titleTrack, { y: -index * th });
		if (bh > 0) {
			gsap.set(bodyTrack, { y: -index * bh });
		}
	}

	/** Margen (px): fuera de [start−m, end+m] no forzamos scroll (evita saltar a la sección al recargar arriba del todo). */
	const PIN_SCROLL_SYNC_MARGIN = 2;

	/**
	 * Alinea el scroll del documento con el índice del slide para que el tramo del pin coincida con ST.start / ST.end.
	 * No llama a ScrollTrigger.refresh() aquí: hacerlo en cada paso recalcula el pin y provoca parpadeos / tirones.
	 *
	 * @param {number} index
	 * @param {{ onlyIfAlreadyInPin?: boolean }} [opts]
	 * - onlyIfAlreadyInPin: true en mount/refresh — no hacer scroll si el viewport aún no está en esta sección (carga en top).
	 */
	function syncPinScrollToIndex(index, opts = {}) {
		const { onlyIfAlreadyInPin = false } = opts;
		requestAnimationFrame(() => {
			const st = ScrollTrigger.getById(triggerId);
			if (!st || typeof st.start !== 'number' || typeof st.end !== 'number') {
				return;
			}
			if (onlyIfAlreadyInPin) {
				const yNow = getDocScrollY();
				if (
					yNow < st.start - PIN_SCROLL_SYNC_MARGIN ||
					yNow > st.end + PIN_SCROLL_SYNC_MARGIN
				) {
					return;
				}
			}
			const y = scrollYForPinIndex(st, index);
			if (!Number.isFinite(y)) {
				return;
			}
			const yNow = getDocScrollY();
			if (Math.abs(yNow - y) <= PIN_SCROLL_SYNC_THRESHOLD) {
				return;
			}
			scrollDocTo(y);
		});
	}

	/**
	 * El caller debe haber puesto `animating = true` ya (evita carreras con la rueda).
	 *
	 * @param {number} targetIndex
	 */
	function runSlideAnimation(targetIndex) {
		gsap.killTweensOf([mediaTrack, titleTrack, bodyTrack]);

		const mw = mediaViewport.clientWidth;
		const th = titleViewport.clientHeight;
		const bh = bodyViewport.clientHeight;

		gsap.timeline({
			onComplete: () => {
				currentIndex = targetIndex;
				syncPinScrollToIndex(targetIndex);
				animating = false;
				cooldownUntil = Date.now() + STEP_COOLDOWN_MS;
			},
		})
			.to(
				mediaTrack,
				{
					x: -targetIndex * mw,
					duration: ANIM.duration,
					ease: ANIM.ease,
				},
				0
			)
			.to(
				titleTrack,
				{
					y: -targetIndex * th,
					duration: ANIM.duration,
					ease: ANIM.ease,
				},
				0
			)
			.to(
				bodyTrack,
				{
					y: -targetIndex * bh,
					duration: ANIM.duration,
					ease: ANIM.ease,
				},
				0
			);
	}

	function tryGoNext() {
		const st = ScrollTrigger.getById(triggerId);
		if (
			!st?.isActive ||
			typeof st.start !== 'number' ||
			typeof st.end !== 'number' ||
			animating ||
			Date.now() < cooldownUntil
		) {
			return false;
		}
		if (currentIndex >= maxIdx) {
			return false;
		}
		animating = true;
		const next = currentIndex + 1;
		// El scroll del documento se alinea en onComplete (un solo salto; evita pelear con Lenis a mitad del tween).
		runSlideAnimation(next);
		return true;
	}

	function tryGoPrev() {
		const st = ScrollTrigger.getById(triggerId);
		if (
			!st?.isActive ||
			typeof st.start !== 'number' ||
			typeof st.end !== 'number' ||
			animating ||
			Date.now() < cooldownUntil
		) {
			return false;
		}
		if (currentIndex <= 0) {
			return false;
		}
		animating = true;
		const prev = currentIndex - 1;
		runSlideAnimation(prev);
		return true;
	}

	/**
	 * Durante animación o cooldown: bloquea rueda que intentaría otro paso (evita saltos / desync con el pin).
	 */
	function onWheel(e) {
		const st = ScrollTrigger.getById(triggerId);
		if (!st?.isActive) {
			return;
		}

		if (Math.abs(e.deltaY) < 12) {
			return;
		}

		const wantsNext = e.deltaY > 0;
		const wantsPrev = e.deltaY < 0;

		if (animating || Date.now() < cooldownUntil) {
			if (
				(wantsNext && currentIndex < maxIdx) ||
				(wantsPrev && currentIndex > 0)
			) {
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
			}
			return;
		}

		if (wantsNext) {
			if (currentIndex >= maxIdx) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			tryGoNext();
		} else if (wantsPrev) {
			if (currentIndex <= 0) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			tryGoPrev();
		}
	}

	function onTouchStart(e) {
		if (e.touches.length !== 1) {
			return;
		}
		touchStartY = e.touches[0].clientY;
	}

	function onTouchEnd(e) {
		const st = ScrollTrigger.getById(triggerId);
		if (
			!st?.isActive ||
			animating ||
			Date.now() < cooldownUntil ||
			!e.changedTouches?.length
		) {
			return;
		}

		const endY = e.changedTouches[0].clientY;
		const dy = touchStartY - endY;

		if (Math.abs(dy) < 45) {
			return;
		}

		if (dy > 0) {
			if (currentIndex >= maxIdx) {
				return;
			}
			e.preventDefault();
			tryGoNext();
		} else {
			if (currentIndex <= 0) {
				return;
			}
			e.preventDefault();
			tryGoPrev();
		}
	}

	function teardown() {
		if (wheelHandler) {
			window.removeEventListener('wheel', wheelHandler, {
				capture: true,
			});
			wheelHandler = null;
		}
		if (touchStartHandler) {
			root.removeEventListener('touchstart', touchStartHandler, {
				passive: true,
			});
			touchStartHandler = null;
		}
		if (touchEndHandler) {
			root.removeEventListener('touchend', touchEndHandler);
			touchEndHandler = null;
		}
		if (pinLockHeld) {
			pinActiveLeave();
			pinLockHeld = false;
		}

		ScrollTrigger.getAll().forEach((st) => {
			if (st.trigger === root || st.trigger === pinTarget) {
				st.kill(true);
			}
		});
		gsap.killTweensOf([mediaTrack, titleTrack, bodyTrack]);
		gsap.set([mediaTrack, titleTrack, bodyTrack], { clearProps: 'transform' });
		clearBodyDimensions();
		currentIndex = 0;
		animating = false;
		cooldownUntil = 0;
	}

	/**
	 * @return {boolean}
	 */
	function mount() {
		teardown();
		applyPinCssVars();

		if (mqReduce.matches || mqMobile.matches) {
			return false;
		}

		if (slideCount <= 1) {
			return false;
		}

		if (mediaViewport.clientWidth < 1 || titleViewport.clientHeight < 1) {
			return false;
		}

		// Fuerza cálculo de layout antes de leer altura del body (flex).
		void bodyViewport.offsetHeight;

		if (!syncBodyDimensions()) {
			return false;
		}

		const dist = Math.max(0, slideCount - 1) * pinScrollStep();
		if (dist < 1) {
			return false;
		}

		applySlideIndex(0);

		ScrollTrigger.create({
			id: triggerId,
			trigger: pinTarget,
			start: () => `top top+=${getPinStartOffset()}`,
			end: () => '+=' + dist,
			pin: pinTarget,
			pinSpacing: true,
			anticipatePin: 0,
			fastScrollEnd: true,
			invalidateOnRefresh: true,
			onToggle: (self) => {
				if (self.isActive) {
					pinActiveEnter();
					pinLockHeld = true;
				} else if (pinLockHeld) {
					pinActiveLeave();
					pinLockHeld = false;
				}
			},
		});

		wheelHandler = onWheel;
		window.addEventListener('wheel', wheelHandler, { passive: false, capture: true });

		touchStartHandler = onTouchStart;
		touchEndHandler = onTouchEnd;
		root.addEventListener('touchstart', touchStartHandler, { passive: true });
		root.addEventListener('touchend', touchEndHandler, { passive: false });

		return true;
	}

	function mountAndRefresh() {
		if (!mount()) {
			requestAnimationFrame(() => {
				if (mount()) {
					requestAnimationFrame(() => {
						ScrollTrigger.refresh();
						syncBodyDimensions();
						applySlideIndex(currentIndex);
						syncPinScrollToIndex(currentIndex, {
							onlyIfAlreadyInPin: true,
						});
					});
				}
			});
			return;
		}
		requestAnimationFrame(() => {
			ScrollTrigger.refresh();
			syncBodyDimensions();
			applySlideIndex(currentIndex);
			syncPinScrollToIndex(currentIndex, { onlyIfAlreadyInPin: true });
		});
	}

	let viewportApplyTimer = 0;

	function applyViewportMode() {
		applyPinCssVars();
		if (mqReduce.matches || mqMobile.matches) {
			teardown();
			ScrollTrigger.refresh();
			return;
		}
		requestAnimationFrame(() => {
			requestAnimationFrame(mountAndRefresh);
		});
	}

	function scheduleApplyViewportMode() {
		clearTimeout(viewportApplyTimer);
		viewportApplyTimer = window.setTimeout(() => {
			applyViewportMode();
		}, 50);
	}

	scheduleApplyViewportMode();

	if (document.readyState !== 'complete') {
		window.addEventListener('load', scheduleApplyViewportMode, { once: true });
	}

	if (typeof document.fonts?.ready?.then === 'function') {
		document.fonts.ready.then(scheduleApplyViewportMode);
	}

	let resizeTimer;
	let lastViewportWidth = window.innerWidth;
	const scheduleRemount = () => {
		const nextWidth = window.innerWidth;
		if (Math.abs(nextWidth - lastViewportWidth) < 2) {
			return;
		}
		lastViewportWidth = nextWidth;
		clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(scheduleApplyViewportMode, 200);
	};

	window.addEventListener('resize', scheduleRemount);

	if (typeof mqReduce.addEventListener === 'function') {
		mqReduce.addEventListener('change', applyViewportMode);
	}
	if (typeof mqMobile.addEventListener === 'function') {
		mqMobile.addEventListener('change', applyViewportMode);
	}
}

/**
 * Carrusel táctil de tarjetas móvil (solo < md; en desktop el nodo está oculto con md:hidden).
 *
 * @param {HTMLElement} root
 */
function initMediaText01MobileSwiper( root ) {
	const el = root.querySelector( '[data-mwm-media-text-01-mobile-swiper]' );
	if ( ! el ) {
		return;
	}

	const wrapper = el.querySelector( '.swiper-wrapper' );
	const slideEls = wrapper
		? /** @type {HTMLElement[]} */ ( Array.from( wrapper.querySelectorAll( '.swiper-slide' ) ) )
		: [];
	if ( slideEls.length < 1 ) {
		return;
	}

	let swiper = null;
	/** Alineado con el bloque: tarjetas solo bajo 768px. */
	const mq = window.matchMedia( '(max-width: 767px)' );

	/** Pausa vídeos fuera del slide activo. */
	const syncCardVideos = ( activeIndex ) => {
		slideEls.forEach( ( slide, i ) => {
			const v = slide.querySelector(
				'.mwm-media-text-01__mobile-card-video'
			);
			if ( ! ( v instanceof HTMLVideoElement ) ) {
				return;
			}
			if ( i === activeIndex ) {
				const p = v.play();
				if ( p && typeof p.catch === 'function' ) {
					p.catch( () => {} );
				}
			} else {
				v.pause();
			}
		} );
	};

	const mount = () => {
		if ( ! mq.matches ) {
			if ( swiper ) {
				swiper.destroy( true, true );
				swiper = null;
			}
			slideEls.forEach( ( slide ) => {
				const v = slide.querySelector( '.mwm-media-text-01__mobile-card-video' );
				if ( v instanceof HTMLVideoElement ) {
					v.pause();
				}
			} );
			return;
		}

		if ( swiper ) {
			swiper.update();
			return;
		}

		const prevBtn = el.querySelector( '[data-mwm-media-text-01-nav-prev]' );
		const nextBtn = el.querySelector( '[data-mwm-media-text-01-nav-next]' );
		const useNav =
			slideEls.length > 1 && prevBtn && nextBtn;

		const options = {
			modules: useNav ? [ Navigation ] : [],
			slidesPerView: 1,
			spaceBetween: 16,
			speed: 450,
			grabCursor: true,
			loop: false,
			watchOverflow: true,
		};
		if ( useNav ) {
			options.navigation = {
				prevEl: prevBtn,
				nextEl: nextBtn,
			};
		}

		swiper = new Swiper( el, options );

		swiper.slideTo( 0, 0, false );
		syncCardVideos( 0 );
		swiper.on( 'slideChange', () => {
			syncCardVideos( swiper.activeIndex );
		} );
	};

	mount();

	if ( typeof mq.addEventListener === 'function' ) {
		mq.addEventListener( 'change', mount );
	} else if ( typeof mq.addListener === 'function' ) {
		mq.addListener( mount );
	}
}

const roots = document.querySelectorAll( '[data-mwm-media-text-01-root]' );
roots.forEach( ( root, index ) => {
	initMediaText01( root, index );
	initMediaText01MobileSwiper( root );
} );
