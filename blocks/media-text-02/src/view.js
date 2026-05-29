/**
 * Media text 02: opacidad por tarjeta + texto izquierdo por slide (fade).
 * Solo viewport ≥1024px (lg). En móvil el texto va en el markup por slide; sin IO ni transiciones.
 */
const FADED_CLASS = 'mwm-media-text-02__card--faded';
const IN_VIEW_CLASS = 'mwm-media-text-02__card--in-view';
const TEXT_FADE_CLASS = 'mwm-media-text-02__text-stage--fade';
const MOBILE_MQ = '(max-width: 1023px)';

const IO_THRESHOLDS = [0, 0.15, 0.35, 0.55, 0.75, 1];

/**
 * @param {HTMLElement} root
 */
function initMediaText02(root) {
	const raw = root.getAttribute('data-mwm-mt02-panel-json');
	if (!raw) {
		return;
	}

	let panels;
	try {
		panels = JSON.parse(raw);
	} catch {
		return;
	}

	if (!Array.isArray(panels) || panels.length === 0) {
		return;
	}

	const cards = root.querySelectorAll('[data-mwm-mt02-card]');
	if (!cards.length) {
		return;
	}

	const textStage = root.querySelector('[data-mwm-mt02-text-stage]');
	const quoteEl = root.querySelector('[data-mwm-mt02-quote]');
	const authorNameEl = root.querySelector('[data-mwm-mt02-author-name]');
	const authorRoleEl = root.querySelector('[data-mwm-mt02-author-role]');

	const reduceMotion =
		typeof window !== 'undefined' &&
		window.matchMedia &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	const fadeMs = reduceMotion ? 0 : 280;

	/** @type {number[]} */
	const ratios = new Array(cards.length).fill(0);
	/** @type {number} índice de slide en panels[] */
	let activeSlideIndex = 0;
	/** @type {IntersectionObserver | null} */
	let io = null;

	function pickActiveCardIndex() {
		let best = 0;
		let bestR = -1;
		for (let i = 0; i < ratios.length; i++) {
			const r = ratios[i];
			if (r > bestR) {
				bestR = r;
				best = i;
			} else if (r === bestR && r > 0) {
				best = Math.min(best, i);
			}
		}
		if (bestR <= 0) {
			return -1;
		}
		return best;
	}

	function syncCardOpacity() {
		cards.forEach((card, i) => {
			const r = ratios[i];
			const inView = r >= 0.2;
			card.classList.toggle(FADED_CLASS, !inView);
			card.classList.toggle(IN_VIEW_CLASS, inView);
		});
	}

	function getSlideIndexForCard(cardIndex) {
		const el = cards[cardIndex];
		if (!el) {
			return 0;
		}
		const s = el.getAttribute('data-mwm-mt02-slide-index');
		const n = s !== null ? parseInt(s, 10) : 0;
		return Number.isFinite(n) && n >= 0 ? n : 0;
	}

	function applyPanelHtml(slideIndex) {
		const p = panels[slideIndex];
		if (!p || !quoteEl || !authorNameEl || !authorRoleEl) {
			return;
		}
		quoteEl.innerHTML = typeof p.quote === 'string' ? p.quote : '';
		authorNameEl.innerHTML =
			typeof p.authorName === 'string' ? p.authorName : '';
		authorRoleEl.innerHTML =
			typeof p.authorRole === 'string' ? p.authorRole : '';
	}

	function updateTextWithFade(nextSlideIndex) {
		if (!textStage || nextSlideIndex === activeSlideIndex) {
			return;
		}
		const target = nextSlideIndex;

		if (fadeMs <= 0) {
			applyPanelHtml(target);
			activeSlideIndex = target;
			return;
		}

		textStage.classList.add(TEXT_FADE_CLASS);
		window.setTimeout(() => {
			applyPanelHtml(target);
			activeSlideIndex = target;
			textStage.classList.remove(TEXT_FADE_CLASS);
		}, fadeMs);
	}

	function onIntersectionUpdate() {
		syncCardOpacity();
		const cardIdx = pickActiveCardIndex();
		if (cardIdx < 0) {
			return;
		}
		const slideIdx = getSlideIndexForCard(cardIdx);
		if (slideIdx >= panels.length) {
			return;
		}
		updateTextWithFade(slideIdx);
	}

	function disconnectObserver() {
		if (!io) {
			return;
		}
		cards.forEach((card) => io.unobserve(card));
		io.disconnect();
		io = null;
	}

	function applyMobileStatic() {
		root.setAttribute('data-mwm-mt02-mobile-static', '1');
		disconnectObserver();
		ratios.fill(0);
		cards.forEach((card) => {
			card.classList.remove(FADED_CLASS);
			card.classList.add(IN_VIEW_CLASS);
		});
	}

	function applyDesktopInteractive() {
		root.removeAttribute('data-mwm-mt02-mobile-static');
		ratios.fill(0);
		cards.forEach((card) => {
			card.classList.add(FADED_CLASS);
			card.classList.remove(IN_VIEW_CLASS);
		});

		io = new IntersectionObserver(
			(entries) => {
				for (const entry of entries) {
					const el = entry.target;
					const idxAttr = el.getAttribute('data-mwm-mt02-index');
					if (idxAttr === null) {
						continue;
					}
					const idx = parseInt(idxAttr, 10);
					if (!Number.isFinite(idx) || idx < 0 || idx >= ratios.length) {
						continue;
					}
					ratios[idx] = entry.intersectionRatio;
				}
				onIntersectionUpdate();
			},
			{
				threshold: IO_THRESHOLDS,
				rootMargin: '0px 0px -8% 0px',
			}
		);

		cards.forEach((card) => io.observe(card));

		syncCardOpacity();
		const firstCard = pickActiveCardIndex();
		const initialSlide =
			firstCard >= 0 ? getSlideIndexForCard(firstCard) : 0;
		activeSlideIndex = initialSlide;
		applyPanelHtml(initialSlide);
	}

	function syncViewportMode() {
		const mobile =
			typeof window !== 'undefined' &&
			window.matchMedia &&
			window.matchMedia(MOBILE_MQ).matches;

		if (mobile) {
			applyMobileStatic();
			return;
		}
		applyDesktopInteractive();
	}

	syncViewportMode();

	const mq =
		typeof window !== 'undefined' && window.matchMedia
			? window.matchMedia(MOBILE_MQ)
			: null;
	if (mq && typeof mq.addEventListener === 'function') {
		mq.addEventListener('change', syncViewportMode);
	} else if (mq && typeof mq.addListener === 'function') {
		mq.addListener(syncViewportMode);
	}
}

function boot() {
	const roots = document.querySelectorAll('[data-mwm-media-text-02-root]');
	roots.forEach((root) => {
		if (!(root instanceof HTMLElement)) {
			return;
		}
		initMediaText02(root);
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', boot);
} else {
	boot();
}
