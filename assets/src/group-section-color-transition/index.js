/**
 * Grupo con .mwm-group-section-color-transition: alterna data-light/dark y bg en los section del grupo.
 * Modo por defecto: al cruzar el segundo section, mismo tema en todos hasta el final del scroll (end: max).
 * Con data-mwm-section-color-second-only, el tema alterno aplica a **todas** las section solo **entre** el
 * umbral del 2.º y el del 3.º; al cruzar el 3.º, todo el grupo vuelve al tema inicial (mín. 3 section).
 * El trigger de scroll sigue siendo el segundo section (mitad de pantalla, etc.; compatible con journey + pin-spacer).
 */
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const GROUP_SEL = '.wp-block-group.mwm-group-section-color-transition';
const GROUP_SECOND_ONLY_ATTR = 'data-mwm-section-color-second-only';
const HEADER_REFRESH = 'zenyx:header-zones-refresh';

/**
 * @param {HTMLElement} section
 * @returns {{ mode: 'light'|'dark', bg: 'protagonista'|'neutral-light' }|null}
 */
function parseTheme(section) {
	const hasDark = section.hasAttribute('data-dark');
	const hasLight = section.hasAttribute('data-light');
	if (hasDark && hasLight) {
		return null;
	}
	const mode = hasDark ? 'dark' : 'light';
	const bg = section.classList.contains('bg-neutral-light')
		? 'neutral-light'
		: 'protagonista';
	return { mode, bg };
}

/**
 * @param {{ mode: string, bg: string }} t
 */
function getOtherTheme(t) {
	return {
		mode: t.mode === 'light' ? 'dark' : 'light',
		bg: t.bg === 'protagonista' ? 'neutral-light' : 'protagonista',
	};
}

/**
 * @param {HTMLElement} section
 * @param {{ mode: string, bg: string }} theme
 */
function applyTheme(section, theme) {
	section.removeAttribute('data-light');
	section.removeAttribute('data-dark');
	section.classList.remove('bg-protagonista', 'bg-neutral-light');
	if (theme.mode === 'light') {
		section.setAttribute('data-light', '');
	} else {
		section.setAttribute('data-dark', '');
	}
	section.classList.add(
		theme.bg === 'protagonista' ? 'bg-protagonista' : 'bg-neutral-light'
	);
}

/**
 * @param {HTMLElement[]} sections
 * @param {{ mode: string, bg: string }} theme
 */
function applyThemePair(sections, theme) {
	for (const el of sections) {
		applyTheme(el, theme);
	}
	document.dispatchEvent(new CustomEvent(HEADER_REFRESH));
}

/**
 * Todos los `section` en orden de árbol dentro del grupo (p. ej. dentro de pin-spacer).
 * Hace falta al menos dos: el primero define el tema inicial; el segundo es el trigger del ScrollTrigger.
 *
 * @param {HTMLElement} group
 * @returns {HTMLElement[]|null}
 */
function getSections(group) {
	const all = group.querySelectorAll('section');
	if (all.length < 2) {
		return null;
	}
	return Array.from(all);
}

/**
 * @param {HTMLElement} group
 * @param {HTMLElement} secondSection
 * @param {{ mode: string, bg: string }} initial
 * @param {{ mode: string, bg: string }} swappedTheme
 * @param {number} index
 */
function bindScrollTrigger(group, secondSection, initial, swappedTheme, index) {
	// Sin `end`, ScrollTrigger usa un final en la propia sección; al pasarlo, `isActive` pasa a
	// false aunque sigas “debajo” del umbral, y el tema deja de alternar bien al subir (pin /
	// journey). `end: "max"` hace que el tramo activo sea desde el umbral hasta el final del
	// scroll, y `onToggle` vuelva a dispararse al cruzar `start` hacia arriba.
	ScrollTrigger.create({
		id: `mwm-group-section-color-${index}`,
		trigger: secondSection,
		start: 'top 55%',
		end: 'max',
		invalidateOnRefresh: true,
		onToggle: (self) => {
			const sections = getSections(group);
			if (!sections) {
				return;
			}
			applyThemePair(sections, self.isActive ? swappedTheme : initial);
		},
	});
}

/**
 * Tema alterno en **todas** las `section` solo entre el umbral del 2.º y el del 3.º (luego vuelve al inicial en todo el grupo).
 *
 * @param {HTMLElement} group
 * @param {HTMLElement} secondSection
 * @param {HTMLElement} thirdSection
 * @param {{ mode: string, bg: string }} initial
 * @param {{ mode: string, bg: string }} swappedTheme
 * @param {number} index
 */
function bindScrollTriggerSecondOnly(
	group,
	secondSection,
	thirdSection,
	initial,
	swappedTheme,
	index
) {
	ScrollTrigger.create({
		id: `mwm-group-section-color-${index}`,
		trigger: secondSection,
		start: 'top 55%',
		endTrigger: thirdSection,
		end: 'top 55%',
		invalidateOnRefresh: true,
		onToggle: (self) => {
			const sections = getSections(group);
			if (!sections || sections.length < 3) {
				return;
			}
			applyThemePair(sections, self.isActive ? swappedTheme : initial);
		},
	});
}

/**
 * @param {HTMLElement} group
 * @param {number} index
 */
function initGroup(group, index) {
	const sections = getSections(group);
	if (!sections) {
		return;
	}
	const secondOnly = group.hasAttribute(GROUP_SECOND_ONLY_ATTR);
	if (secondOnly && sections.length < 3) {
		return;
	}
	const [first, second] = sections;
	const a = parseTheme(first);
	const b = parseTheme(second);
	if (!a || !b) {
		return;
	}
	const initial = { ...a };
	let synced = false;
	for (let i = 1; i < sections.length; i++) {
		const t = parseTheme(sections[i]);
		if (!t || t.mode !== a.mode || t.bg !== a.bg) {
			applyTheme(sections[i], a);
			synced = true;
		}
	}
	if (synced) {
		document.dispatchEvent(new CustomEvent(HEADER_REFRESH));
	}
	const swapped = getOtherTheme(initial);
	if (secondOnly) {
		const third = sections[2];
		bindScrollTriggerSecondOnly(
			group,
			second,
			third,
			initial,
			swapped,
			index
		);
	} else {
		bindScrollTrigger(group, second, initial, swapped, index);
	}
}

function refreshAll() {
	ScrollTrigger.refresh();
}

function run() {
	const groups = document.querySelectorAll(GROUP_SEL);
	if (!groups.length) {
		return;
	}
	groups.forEach((group, i) => initGroup(group, i));
	requestAnimationFrame(() => {
		requestAnimationFrame(refreshAll);
	});
}

function scheduleRun() {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}
}

scheduleRun();

window.addEventListener('load', () => {
	requestAnimationFrame(refreshAll);
});

let resizeTimer;
window.addEventListener('resize', () => {
	clearTimeout(resizeTimer);
	resizeTimer = window.setTimeout(refreshAll, 200);
});
