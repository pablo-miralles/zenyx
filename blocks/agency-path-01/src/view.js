/**
 * Agency path 01: pin sin scrub; cada gesto de scroll (rueda / swipe) avanza o retrocede un paso de la historia.
 * Patrón alineado con blocks/media-text-01 (pinScrollStep + wheel + sync scroll).
 */
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/** Pasos 0…5: estado inicial → … → estado final (guide-05). */
const MAX_STEP = 5;

/** Duración de transición entre pasos (no ligada al scroll). */
const ANIM = {
	duration: 0.58,
	ease: 'power2.out',
};

/** Tiempo tras terminar un paso donde no se acepta otro gesto. */
const STEP_COOLDOWN_MS = 220;

/**
 * Distancia de documento por paso dentro del pin (misma idea que media-text-01).
 */
function pinScrollStep() {
	const h =
		typeof window !== 'undefined' && window.innerHeight > 0
			? window.innerHeight
			: 800;
	return Math.max(260, Math.round(h * 0.42));
}

function scrollDocTo(y) {
	const lenis = window.lenis;
	if (lenis && typeof lenis.scrollTo === 'function') {
		lenis.scrollTo(y, { immediate: true, force: true });
	} else {
		window.scrollTo({ top: y, behavior: 'instant' });
	}
}

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

/** Coordenadas de referencia Figma: `data-agp01-board` 1076×475. */
const REF = { w: 1076, h: 475 };

/**
 * @param {number} x
 * @param {number} y
 */
function pct(x, y) {
	return {
		left: `${(x / REF.w) * 100}%`,
		top: `${(y / REF.h) * 100}%`,
	};
}

const DOT_KEYFRAMES = {
	initial: {
		dot0: pct(506, REF.h * 0.856),
		dot1: pct(506, REF.h * 0.982105),
	},
	afterL1: {
		dot0: pct(REF.w * 0.2821, REF.h * 0.85),
		dot1: pct(REF.w * 0.38, REF.h * 0.85),
	},
	afterRoof: {
		dot0: pct(REF.w * 0.362, REF.h * 0.665),
		dot1: pct(REF.w * 0.555, REF.h * 0.665),
	},
	beforeMerge: {
		dot0: pct(REF.w * 0.542, REF.h * 0.475),
		dot1: pct(REF.w * 0.638, REF.h * 0.475),
	},
	merged: {
		dot0: pct(REF.w * 0.458, REF.h * 0.348),
		dot1: pct(REF.w * 0.458, REF.h * 0.348),
	},
};

/**
 * @param {HTMLElement} root
 * @param {{ keepIntroVisible?: boolean }} [opts]
 */
function applyReducedEndState(root, opts = {}) {
	const { keepIntroVisible = false } = opts;
	root.classList.add('mwm-agency-path-01--compact-top');

	const setOp = (sel, opacity) => {
		const el = root.querySelector(sel);
		if (el) {
			gsap.set(el, { opacity });
		}
	};

	const intro = root.querySelector('[data-agp01-intro]');
	if (intro) {
		if (keepIntroVisible) {
			gsap.set(intro, {
				opacity: 1,
				clearProps: 'height,marginBottom,overflow',
			});
		} else {
			gsap.set(intro, { opacity: 0, height: 0, marginBottom: 0, overflow: 'hidden' });
		}
	}

	setOp('[data-agp01-l1-fund-wrap]', 0);
	setOp('[data-agp01-l1-esc-wrap]', 1);
	const roofOutlineReduced = root.querySelector('[data-agp01-roof-outline]');
	if (roofOutlineReduced) {
		gsap.set(roofOutlineReduced, { opacity: 0, attr: { stroke: 'transparent' } });
	}
	setOp('[data-agp01-roof-fill]', 1);
	setOp('[data-agp01-roof-accent]', 1);
	setOp('[data-agp01-midline-full]', 0);
	setOp('[data-agp01-midline-broken-simple]', 1);
	setOp('[data-agp01-frame-simple]', 0);
	setOp('[data-agp01-frame-extended]', 1);

	const level2 = root.querySelector('[data-agp01-level-idx="2"]');
	const level3 = root.querySelector('[data-agp01-level-idx="3"]');
	if (level2) {
		gsap.set(level2, { opacity: 1 });
	}
	if (level3) {
		gsap.set(level3, { opacity: 1 });
	}

	setOp('[data-agp01-l2-esc-wrap]', 1);
	setOp('[data-agp01-validation]', 1);
	setOp('[data-agp01-tagline]', 1);
	setOp('[data-agp01-dots-wrap]', 0);
	setOp('[data-agp01-marker-chevron]', 0);
	setOp('[data-agp01-marker-chevron-final]', 1);

	const marker = root.querySelector('[data-agp01-marker]');
	if (marker) {
		gsap.set(marker, { left: '55.6937%', top: '80.199%', clearProps: 'transform' });
	}
}

/**
 * @param {import('gsap').TweenTarget} els
 */
function killStepTweens(els) {
	gsap.killTweensOf(els);
}

/**
 * @param {HTMLElement} root
 * @param {object} ctx
 * @param {number} step 0…5
 * @return {import('gsap').Timeline}
 */
function buildStepTimeline(root, ctx, step) {
	const {
		dot0,
		dot1,
		l1Fund,
		l1Esc,
		roofOutline,
		roofFill,
		roofAccent,
		midFull,
		midBroken,
		frameSimple,
		frameExtended,
		level2,
		level3,
		l2Esc,
		validation,
		tagline,
		dotsWrap,
		markerChevron,
		markerChevronFinal,
		intro,
	} = ctx;

	const d = ANIM.duration;
	const e = ANIM.ease;
	const p = 0;

	const dk = (() => {
		if (step <= 0) {
			return DOT_KEYFRAMES.initial;
		}
		if (step === 1) {
			return DOT_KEYFRAMES.afterL1;
		}
		if (step === 2) {
			return DOT_KEYFRAMES.afterRoof;
		}
		if (step === 3) {
			return DOT_KEYFRAMES.beforeMerge;
		}
		return DOT_KEYFRAMES.merged;
	})();

	const tl = gsap.timeline();

	tl.eventCallback('onStart', () => {
		if (step >= 5) {
			root.classList.add('mwm-agency-path-01--compact-top');
		} else {
			root.classList.remove('mwm-agency-path-01--compact-top');
		}
		if (intro && step < 5) {
			gsap.set(intro, { clearProps: 'height,marginBottom,overflow' });
		}
	});

	if (dot0) {
		tl.to(dot0, { ...dk.dot0, xPercent: -50, yPercent: -50, duration: d, ease: e }, p);
	}
	if (dot1) {
		tl.to(dot1, { ...dk.dot1, xPercent: -50, yPercent: -50, duration: d, ease: e }, p);
	}

	if (l1Fund) {
		tl.to(l1Fund, { opacity: step >= 1 ? 0 : 1, duration: d, ease: e }, p);
	}
	if (l1Esc) {
		tl.to(l1Esc, { opacity: step >= 1 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (roofOutline) {
		// Solo en el paso 2 el trazo es visible; con el fill (paso ≥3) stroke transparente
		// para que no quede ningún borde descolocado respecto al path de relleno.
		const outlineOn = step === 2;
		tl.to(
			roofOutline,
			{
				opacity: outlineOn ? 1 : 0,
				attr: { stroke: outlineOn ? 'currentColor' : 'transparent' },
				duration: d,
				ease: e,
			},
			p
		);
	}
	if (roofFill) {
		tl.to(roofFill, { opacity: step >= 3 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (roofAccent) {
		tl.to(roofAccent, { opacity: step >= 5 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (midFull) {
		tl.to(midFull, { opacity: step >= 2 ? 0 : 1, duration: d, ease: e }, p);
	}
	if (midBroken) {
		tl.to(midBroken, { opacity: step >= 2 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (frameSimple) {
		tl.to(frameSimple, { opacity: step >= 4 ? 0 : 1, duration: d, ease: e }, p);
	}
	if (frameExtended) {
		tl.to(frameExtended, { opacity: step >= 4 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (level2) {
		tl.to(level2, { opacity: step >= 2 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (level3) {
		tl.to(level3, { opacity: step >= 5 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (l2Esc) {
		tl.to(l2Esc, { opacity: step >= 3 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (validation) {
		tl.to(validation, { opacity: step >= 4 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (tagline) {
		tl.to(tagline, { opacity: step >= 5 ? 1 : 0, duration: d, ease: e }, p);
	}
	if (dotsWrap) {
		tl.to(dotsWrap, { opacity: step >= 5 ? 0 : 1, duration: d, ease: e }, p);
	}
	if (markerChevron) {
		tl.to(markerChevron, { opacity: step >= 4 ? 0 : 1, duration: d, ease: e }, p);
	}
	if (markerChevronFinal) {
		tl.to(markerChevronFinal, { opacity: step >= 4 ? 1 : 0, duration: d, ease: e }, p);
	}

	if (intro) {
		if (step >= 5) {
			tl.to(
				intro,
				{
					opacity: 0,
					marginBottom: 0,
					height: 0,
					overflow: 'hidden',
					duration: d,
					ease: e,
				},
				p
			);
		} else {
			tl.to(intro, { opacity: 1, duration: d, ease: e }, p);
		}
	}

	return tl;
}

/**
 * @param {HTMLElement} root
 * @param {number} instanceIndex
 */
function initSectionAnimated(root, instanceIndex) {
	const mqReduce = window.matchMedia('(prefers-reduced-motion: reduce)');
	const mqMobile = window.matchMedia('(max-width: 1023px)');

	if (mqReduce.matches || mqMobile.matches) {
		applyReducedEndState(root, { keepIntroVisible: mqMobile.matches });
		return;
	}

	const intro = root.querySelector('[data-agp01-intro]');
	const l1Fund = root.querySelector('[data-agp01-l1-fund-wrap]');
	const l1Esc = root.querySelector('[data-agp01-l1-esc-wrap]');
	const roofOutline = root.querySelector('[data-agp01-roof-outline]');
	const roofFill = root.querySelector('[data-agp01-roof-fill]');
	const roofAccent = root.querySelector('[data-agp01-roof-accent]');
	const midFull = root.querySelector('[data-agp01-midline-full]');
	const midBroken = root.querySelector('[data-agp01-midline-broken-simple]');
	const frameSimple = root.querySelector('[data-agp01-frame-simple]');
	const frameExtended = root.querySelector('[data-agp01-frame-extended]');
	const level2 = root.querySelector('[data-agp01-level-idx="2"]');
	const level3 = root.querySelector('[data-agp01-level-idx="3"]');
	const l2Esc = root.querySelector('[data-agp01-l2-esc-wrap]');
	const validation = root.querySelector('[data-agp01-validation]');
	const tagline = root.querySelector('[data-agp01-tagline]');
	const dotsWrap = root.querySelector('[data-agp01-dots-wrap]');
	const dot0 = root.querySelector('[data-agp01-dot="0"]');
	const dot1 = root.querySelector('[data-agp01-dot="1"]');
	const markerChevron = root.querySelector('[data-agp01-marker-chevron]');
	const markerChevronFinal = root.querySelector('[data-agp01-marker-chevron-final]');

	if (!dot0 || !dot1 || !frameSimple || !l1Fund || !l1Esc) {
		return;
	}

	const ctx = {
		dot0,
		dot1,
		l1Fund,
		l1Esc,
		roofOutline,
		roofFill,
		roofAccent,
		midFull,
		midBroken,
		frameSimple,
		frameExtended,
		level2,
		level3,
		l2Esc,
		validation,
		tagline,
		dotsWrap,
		markerChevron,
		markerChevronFinal,
		intro,
	};

	const allTweenTargets = [
		dot0,
		dot1,
		l1Fund,
		l1Esc,
		roofOutline,
		roofFill,
		roofAccent,
		midFull,
		midBroken,
		frameSimple,
		frameExtended,
		level2,
		level3,
		l2Esc,
		validation,
		tagline,
		dotsWrap,
		markerChevron,
		markerChevronFinal,
		intro,
	].filter(Boolean);

	gsap.set(dot0, {
		position: 'absolute',
		...DOT_KEYFRAMES.initial.dot0,
		xPercent: -50,
		yPercent: -50,
	});
	gsap.set(dot1, {
		position: 'absolute',
		...DOT_KEYFRAMES.initial.dot1,
		xPercent: -50,
		yPercent: -50,
	});
	gsap.set(l1Esc, { opacity: 0 });
	if (roofOutline) {
		gsap.set(roofOutline, { opacity: 0, attr: { stroke: 'currentColor' } });
	}
	if (roofFill) {
		gsap.set(roofFill, { opacity: 0 });
	}
	if (roofAccent) {
		gsap.set(roofAccent, { opacity: 0 });
	}
	if (midBroken) {
		gsap.set(midBroken, { opacity: 0 });
	}
	if (frameExtended) {
		gsap.set(frameExtended, { opacity: 0 });
	}
	if (level2) {
		gsap.set(level2, { opacity: 0 });
	}
	if (level3) {
		gsap.set(level3, { opacity: 0 });
	}
	if (l2Esc) {
		gsap.set(l2Esc, { opacity: 0 });
	}
	if (validation) {
		gsap.set(validation, { opacity: 0 });
	}
	if (tagline) {
		gsap.set(tagline, { opacity: 0 });
	}
	if (markerChevron) {
		gsap.set(markerChevron, { opacity: 1 });
	}
	if (markerChevronFinal) {
		gsap.set(markerChevronFinal, { opacity: 0 });
	}
	root.classList.remove('mwm-agency-path-01--compact-top');

	const triggerId = `mwm-agency-path-01-${instanceIndex}`;
	let currentIndex = 0;
	let animating = false;
	let cooldownUntil = 0;
	let activeTimeline = null;

	/** @type {((e: WheelEvent) => void) | null} */
	let wheelHandler = null;
	/** @type {((e: TouchEvent) => void) | null} */
	let touchStartHandler = null;
	/** @type {((e: TouchEvent) => void) | null} */
	let touchEndHandler = null;
	let touchStartY = 0;

	function scrollYForPinIndex(st, index) {
		const step = pinScrollStep();
		if (index <= 0) {
			return st.start;
		}
		if (index >= MAX_STEP) {
			return st.end;
		}
		return st.start + index * step;
	}

	function syncPinScrollToIndex(index, opts = {}) {
		const { onlyIfAlreadyInPin = false } = opts;
		requestAnimationFrame(() => {
			const st = ScrollTrigger.getById(triggerId);
			if (!st || typeof st.start !== 'number' || typeof st.end !== 'number') {
				return;
			}
			if (onlyIfAlreadyInPin) {
				const yNow = getDocScrollY();
				if (yNow < st.start - 2 || yNow > st.end + 2) {
					return;
				}
			}
			const y = scrollYForPinIndex(st, index);
			if (Number.isFinite(y)) {
				scrollDocTo(y);
			}
		});
	}

	function runToStep(targetIndex) {
		killStepTweens(allTweenTargets);
		if (activeTimeline) {
			activeTimeline.kill();
			activeTimeline = null;
		}

		const tl = buildStepTimeline(root, ctx, targetIndex);
		activeTimeline = tl;

		tl.eventCallback('onComplete', () => {
			activeTimeline = null;
			currentIndex = targetIndex;
			cooldownUntil = Date.now() + STEP_COOLDOWN_MS;
			syncPinScrollToIndex(currentIndex);
			animating = false;
		});

		animating = true;
		tl.play(0);
	}

	function tryGoNext() {
		const st = ScrollTrigger.getById(triggerId);
		if (
			!st?.isActive ||
			animating ||
			Date.now() < cooldownUntil ||
			currentIndex >= MAX_STEP
		) {
			return false;
		}
		runToStep(currentIndex + 1);
		return true;
	}

	function tryGoPrev() {
		const st = ScrollTrigger.getById(triggerId);
		if (
			!st?.isActive ||
			animating ||
			Date.now() < cooldownUntil ||
			currentIndex <= 0
		) {
			return false;
		}
		runToStep(currentIndex - 1);
		return true;
	}

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
				(wantsNext && currentIndex < MAX_STEP) ||
				(wantsPrev && currentIndex > 0)
			) {
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
			}
			return;
		}

		if (wantsNext) {
			if (currentIndex >= MAX_STEP) {
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
			if (currentIndex >= MAX_STEP) {
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
			window.removeEventListener('wheel', wheelHandler, { capture: true });
			wheelHandler = null;
		}
		if (touchStartHandler) {
			root.removeEventListener('touchstart', touchStartHandler, { passive: true });
			touchStartHandler = null;
		}
		if (touchEndHandler) {
			root.removeEventListener('touchend', touchEndHandler);
			touchEndHandler = null;
		}

		ScrollTrigger.getAll().forEach((st) => {
			if (st.trigger === root) {
				st.kill(true);
			}
		});
		killStepTweens(allTweenTargets);
		if (activeTimeline) {
			activeTimeline.kill();
			activeTimeline = null;
		}
		root.classList.remove('mwm-agency-path-01--compact-top');
	}

	function mount() {
		teardown();

		const dist = MAX_STEP * pinScrollStep();
		if (dist < 1) {
			return false;
		}

		ScrollTrigger.create({
			id: triggerId,
			trigger: root,
			start: 'top top',
			end: () => '+=' + dist,
			pin: true,
			pinSpacing: true,
			anticipatePin: 1,
			fastScrollEnd: true,
			invalidateOnRefresh: true,
		});

		currentIndex = 0;
		animating = false;
		cooldownUntil = 0;

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
						syncPinScrollToIndex(currentIndex, { onlyIfAlreadyInPin: true });
					});
				}
			});
			return;
		}
		requestAnimationFrame(() => {
			ScrollTrigger.refresh();
			syncPinScrollToIndex(0, { onlyIfAlreadyInPin: true });
		});
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
	const scheduleRemount = () => {
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
			if (mqReduce.matches || mqMobile.matches) {
				teardown();
				applyReducedEndState(root, { keepIntroVisible: mqMobile.matches });
				ScrollTrigger.refresh();
			} else {
				mountAndRefresh();
			}
		});
	}

	if (typeof mqMobile.addEventListener === 'function') {
		mqMobile.addEventListener('change', () => {
			if (mqMobile.matches || mqReduce.matches) {
				teardown();
				applyReducedEndState(root, { keepIntroVisible: mqMobile.matches });
				ScrollTrigger.refresh();
			} else {
				mountAndRefresh();
			}
		});
	}
}

const roots = document.querySelectorAll('[data-mwm-agency-path-01]');
roots.forEach((root, index) => initSectionAnimated(root, index));
