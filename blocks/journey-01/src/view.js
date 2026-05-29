/**
 * Journey 01: pin + horizontal track (GSAP ScrollTrigger).
 */
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/**
 * @param {HTMLElement} root
 * @param {number}      instanceIndex
 */
function initJourney(root, instanceIndex) {
	const viewport = root.querySelector('[data-mwm-journey-viewport]');
	const track = root.querySelector('[data-mwm-journey-track]');

	if (!viewport || !track) {
		return;
	}

	function updateLineEndInset() {
		const half = Math.max(0, viewport.clientWidth / 2);
		track.style.setProperty('--mwm-journey-line-end', `${half}px`);
	}

	const mqReduce = window.matchMedia('(prefers-reduced-motion: reduce)');

	function getDistance() {
		return Math.max(0, track.scrollWidth - viewport.clientWidth);
	}

	let tweenRef = null;

	/**
	 * Revierte pin y quita espaciadores; killTweensOf limpia la animación.
	 * Orden: ST con kill(true) primero, luego tweens del track.
	 */
	function teardown() {
		ScrollTrigger.getAll().forEach((st) => {
			if (st.trigger === root) {
				st.kill(true);
			}
		});
		gsap.killTweensOf(track);
		tweenRef = null;
		gsap.set(track, { clearProps: 'transform' });
	}

	function mount() {
		teardown();
		updateLineEndInset();

		if (getDistance() < 1) {
			return;
		}

		tweenRef = gsap.to(track, {
			x: () => -getDistance(),
			ease: 'none',
			scrollTrigger: {
				id: `mwm-journey-${instanceIndex}`,
				trigger: root,
				start: 'top top',
				end: () => '+=' + getDistance(),
				pin: true,
				pinSpacing: true,
				scrub: true,
				invalidateOnRefresh: true,
				anticipatePin: 1,
				onRefresh: updateLineEndInset,
			},
		});
	}

	function mountAndRefresh() {
		mount();
		requestAnimationFrame(() => {
			updateLineEndInset();
			ScrollTrigger.refresh();
		});
	}

	updateLineEndInset();

	if (mqReduce.matches) {
		return;
	}

	requestAnimationFrame(() => {
		requestAnimationFrame(mountAndRefresh);
	});

	if (document.readyState === 'complete') {
		mountAndRefresh();
	} else {
		window.addEventListener('load', mountAndRefresh);
	}

	if (typeof document.fonts?.ready?.then === 'function') {
		document.fonts.ready.then(mountAndRefresh);
	}

	let resizeTimer;
	let lastViewportWidth = window.innerWidth;
	const scheduleRemount = () => {
		const nextWidth = window.innerWidth;
		// iOS Safari dispara `resize` al mostrar/ocultar barras del navegador durante scroll
		// (cambia alto, no ancho). Evitamos remount en esos casos para no provocar saltos.
		if (Math.abs(nextWidth - lastViewportWidth) < 2) {
			return;
		}
		lastViewportWidth = nextWidth;
		clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(() => {
			requestAnimationFrame(() => {
				requestAnimationFrame(mountAndRefresh);
			});
		}, 200);
	};

	window.addEventListener('resize', scheduleRemount);

	if (typeof mqReduce.addEventListener === 'function') {
		mqReduce.addEventListener('change', () => {
			if (mqReduce.matches) {
				teardown();
				ScrollTrigger.refresh();
			} else {
				mountAndRefresh();
			}
		});
	}
}

const roots = document.querySelectorAll('[data-mwm-journey-root]');
roots.forEach((root, index) => initJourney(root, index));
