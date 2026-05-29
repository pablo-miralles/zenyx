/**
 * Archive casos de éxito: una card activa por fila (hover / focus) en desktop.
 */
(function () {
	'use strict';

	var mq = window.matchMedia('(min-width: 1024px)');

	function setRowActive(row, activeCard) {
		var cards = row.querySelectorAll('[data-mwm-card-caso]');
		cards.forEach(function (c) {
			c.classList.toggle('is-active', c === activeCard);
		});
	}

	/**
	 * Misma lógica que mwm_casos_row_default_active_index() en PHP.
	 *
	 * @param {number} rowIndex
	 * @param {number} n Cards en la fila
	 * @returns {number}
	 */
	function defaultActiveIndex(rowIndex, n) {
		var count = Math.max(1, Math.min(3, n | 0));
		var mod = (Math.max(0, parseInt(rowIndex, 10) || 0) % 3);
		if (mod === 1) {
			return count - 1;
		}
		if (mod === 2) {
			return Math.min(1, count - 1);
		}
		return 0;
	}

	function initRow(row) {
		var cards = row.querySelectorAll('[data-mwm-card-caso]');
		if (!cards.length) {
			return;
		}

		function applyDesktopState() {
			if (mq.matches) {
				var current = row.querySelector('[data-mwm-card-caso].is-active');
				if (current) {
					setRowActive(row, current);
				} else {
					var rowIdx = parseInt(row.getAttribute('data-mwm-caso-row-index') || '0', 10);
					var idx = defaultActiveIndex(rowIdx, cards.length);
					setRowActive(row, cards[idx]);
				}
			} else {
				cards.forEach(function (c) {
					c.classList.remove('is-active');
				});
			}
		}

		applyDesktopState();

		cards.forEach(function (card) {
			card.addEventListener('pointerenter', function () {
				if (!mq.matches) {
					return;
				}
				setRowActive(row, card);
			});
			card.addEventListener('focusin', function () {
				if (!mq.matches) {
					return;
				}
				setRowActive(row, card);
			});
		});

		if (typeof mq.addEventListener === 'function') {
			mq.addEventListener('change', applyDesktopState);
		} else if (typeof mq.addListener === 'function') {
			mq.addListener(applyDesktopState);
		}
	}

	document.querySelectorAll('[data-mwm-caso-row]').forEach(initRow);
})();
