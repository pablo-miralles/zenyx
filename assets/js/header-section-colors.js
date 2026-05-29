/**
 * Header: colores según solape vertical con [data-light] / [data-dark] en el main.
 * Prioridad: data-light → mwm-header--over-light (menú claro); data-dark → mwm-header--over-dark.
 */
(function () {
	'use strict';
	var MODE_NONE = 'none';
	var MODE_LIGHT = 'light';
	var MODE_DARK = 'dark';
	var SWITCH_STABILITY_MS = 90;
	var NONE_GRACE_MS = 180;
	var lastMode = MODE_NONE;
	var lastModeAt = 0;
	var pendingMode = MODE_NONE;
	var pendingModeAt = 0;

	/**
	 * Zonas de color: solo contenido principal (excluye footer u otros data-* fuera del main).
	 *
	 * @param {string} selector [data-light] o [data-dark].
	 * @returns {NodeListOf<Element>}
	 */
	function getZoneElements(selector) {
		var main = document.querySelector('.mwm-main-container');
		var root = main || document;
		return root.querySelectorAll(selector);
	}

	/**
	 * @param {DOMRect} rect
	 * @param {number} y
	 * @param {number} bleed
	 * @returns {boolean}
	 */
	function containsY(rect, y, bleed) {
		return y >= rect.top - bleed && y <= rect.bottom + bleed;
	}

	function applyHeaderMode(header, mode) {
		header.classList.toggle('mwm-header--over-light', mode === MODE_LIGHT);
		header.classList.toggle('mwm-header--over-dark', mode === MODE_DARK);
	}

	function checkHeaderSectionColors() {
		var header = document.getElementById('mwm-header');
		if (!header) {
			return;
		}

		if (document.body.classList.contains('mwm-mobile-menu-open')) {
			header.classList.remove('mwm-header--over-light', 'mwm-header--over-dark');
			return;
		}

		var lightEls = getZoneElements('[data-light]');
		var darkEls = getZoneElements('[data-dark]');
		var headerRect = header.getBoundingClientRect();
		var probeY = headerRect.top + Math.min(headerRect.height * 0.5, 28);
		var bleed = 2;
		var overLight = false;
		var overDark = false;
		var i;
		var now = Date.now();
		var nextMode = MODE_NONE;

		for (i = 0; i < lightEls.length; i++) {
			if (containsY(lightEls[i].getBoundingClientRect(), probeY, bleed)) {
				overLight = true;
				break;
			}
		}

		if (!overLight) {
			for (i = 0; i < darkEls.length; i++) {
				if (containsY(darkEls[i].getBoundingClientRect(), probeY, bleed)) {
					overDark = true;
					break;
				}
			}
		}

		if (overLight) {
			nextMode = MODE_LIGHT;
		} else if (overDark) {
			nextMode = MODE_DARK;
		}

		if (nextMode === MODE_NONE && lastMode !== MODE_NONE && now - lastModeAt < NONE_GRACE_MS) {
			nextMode = lastMode;
		}

		if (nextMode !== lastMode) {
			if (pendingMode !== nextMode) {
				pendingMode = nextMode;
				pendingModeAt = now;
				// Carga inicial: aplicar de inmediato (sin esperar histéresis).
				if (lastMode === MODE_NONE && nextMode !== MODE_NONE) {
					lastMode = nextMode;
					lastModeAt = now;
					pendingMode = MODE_NONE;
					pendingModeAt = 0;
				} else {
					return;
				}
			} else if (now - pendingModeAt < SWITCH_STABILITY_MS) {
				return;
			} else {
				lastMode = nextMode;
				lastModeAt = now;
				pendingMode = MODE_NONE;
				pendingModeAt = 0;
			}
		} else {
			pendingMode = MODE_NONE;
			pendingModeAt = 0;
			lastModeAt = now;
		}

		applyHeaderMode(header, lastMode);
	}

	function init() {
		var scheduled = false;
		var scheduleCheck = function () {
			if (scheduled) {
				return;
			}
			scheduled = true;
			window.requestAnimationFrame(function () {
				scheduled = false;
				checkHeaderSectionColors();
			});
		};

		checkHeaderSectionColors();
		window.requestAnimationFrame(function () {
			checkHeaderSectionColors();
		});

		// Tras Lenis / ScrollTrigger (layout del footer y heroes).
		window.setTimeout(function () {
			checkHeaderSectionColors();
		}, 150);

		window.addEventListener('scroll', scheduleCheck, { passive: true });
		window.addEventListener('resize', scheduleCheck);
		document.addEventListener('zenyx:header-zones-refresh', scheduleCheck);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
