/**
 * Smooth scroll (Lenis) + sincronía con GSAP ScrollTrigger.
 * @see https://github.com/darkroomengineering/lenis#gsap-scrolltrigger
 *
 * Lenis + proxy + defaults se inicializan en cuanto carga este módulo (no en DOMContentLoaded),
 * para que otros ScrollTriggers (grupos, bloques) usen el mismo scroller que Lenis.
 */
import Lenis from 'lenis';
import 'lenis/dist/lenis.css';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const mqReduce = window.matchMedia('(prefers-reduced-motion: reduce)');

function setupFooterScrollEffects() {
	initFooterParallax();
	initHideHeaderOnFooter();
}

if (!mqReduce.matches) {
	const lenis = new Lenis({
		autoRaf: false,
		smoothWheel: true,
	});

	lenis.on('scroll', ScrollTrigger.update);

	gsap.ticker.add((time) => {
		lenis.raf(time * 1000);
	});
	gsap.ticker.lagSmoothing(0);

	window.lenis = lenis;

	ScrollTrigger.scrollerProxy(document.documentElement, {
		scrollTop(value) {
			if (arguments.length) {
				lenis.scrollTo(value, { immediate: true });
			}
			return lenis.scroll;
		},
		getBoundingClientRect() {
			return {
				top: 0,
				left: 0,
				width: window.innerWidth,
				height: window.innerHeight,
			};
		},
	});

	ScrollTrigger.defaults({ scroller: document.documentElement });

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setupFooterScrollEffects);
	} else {
		setupFooterScrollEffects();
	}

	let resizeDebounce;
	window.addEventListener('resize', () => {
		clearTimeout(resizeDebounce);
		resizeDebounce = window.setTimeout(() => {
			lenis.resize();
			ScrollTrigger.refresh();
		}, 200);
	});
}

/**
 * Rango de scroll del footer: en móvil se acorta para que transform + overlay terminen antes.
 */
function footerParallaxScrollEnd() {
	return window.matchMedia('(max-width: 1023px)').matches ? 'top 18%' : 'bottom bottom';
}

/**
 * Desplazamiento inicial del bloque interior: en móvil menos recorrido (mismo tramo corto que el end).
 */
function footerParallaxInnerYFrom() {
	return window.matchMedia('(max-width: 1023px)').matches ? -88 : -140;
}

/**
 * Misma idea que centrotadi: el contenido interior sube al entrar en viewport y la capa oscura se disuelve.
 */
function initFooterParallax() {
	const footers = document.querySelectorAll('[data-footer-parallax]');
	if (!footers.length) {
		return;
	}

	ScrollTrigger.getAll()
		.filter((st) => {
			return (
				st &&
				st.vars &&
				typeof st.vars.id === 'string' &&
				st.vars.id.indexOf('footerParallax-') === 0
			);
		})
		.forEach((st) => {
			st.kill(true);
		});

	footers.forEach((el, idx) => {
		const inner = el.querySelector('[data-footer-parallax-inner]');
		const dark = el.querySelector('[data-footer-parallax-dark]');

		if (inner) {
			gsap.set(inner, { clearProps: 'transform' });
		}
		if (dark) {
			gsap.set(dark, { clearProps: 'opacity' });
		}

		const stCommon = {
			trigger: el,
			start: 'top bottom',
			scrub: true,
			invalidateOnRefresh: true,
			refreshPriority: -10,
		};

		if (inner) {
			gsap.timeline({
				scrollTrigger: {
					...stCommon,
					id: 'footerParallax-' + idx,
					end: () => footerParallaxScrollEnd(),
				},
			}).fromTo(
				inner,
				{ y: footerParallaxInnerYFrom },
				{ y: 0, ease: 'none' },
				0
			);
		}

		if (dark) {
			gsap.timeline({
				scrollTrigger: {
					...stCommon,
					id: 'footerParallax-dark-' + idx,
					end: () => footerParallaxScrollEnd(),
				},
			}).fromTo(dark, { opacity: 0.8 }, { opacity: 0, ease: 'none' }, 0);
		}
	});

	ScrollTrigger.refresh();
	document.dispatchEvent(new CustomEvent('zenyx:header-zones-refresh'));
}

/**
 * Oculta el header fijo al acercarse al final de página: primero al bloque CTA 01
 * (`data-mwm-header-hide-boundary`), si existe; si no, al footer con parallax.
 */
function initHideHeaderOnFooter() {
	const header = document.querySelector('#mwm-header');
	const ctaBoundary = document.querySelector('[data-mwm-header-hide-boundary]');
	const footerWrap = document.querySelector('[data-footer-parallax]');
	const triggerEl = ctaBoundary || footerWrap;

	if (!header || !triggerEl) {
		return;
	}

	ScrollTrigger.getAll()
		.filter((st) => st.vars && st.vars.id === 'zenyxHideHeaderFooter')
		.forEach((st) => st.kill(true));

	gsap.set(header, { clearProps: 'transform' });

	const margin = 48;

	ScrollTrigger.create({
		id: 'zenyxHideHeaderFooter',
		trigger: triggerEl,
		start: 'top center',
		end: 'top top',
		scrub: true,
		invalidateOnRefresh: true,
		anticipatePin: 1,
		onUpdate(self) {
			const dist = header.offsetHeight + margin;
			gsap.set(header, { y: -self.progress * dist });
		},
		onLeaveBack() {
			gsap.set(header, { y: 0 });
		},
	});
}
