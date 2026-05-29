/**
 * Copia la URL del artículo al portapapeles (botón .mwm-single-copy-btn).
 * Usa Clipboard API si hay contexto seguro (HTTPS/localhost); si no, fallback con execCommand (HTTP en Local, etc.).
 */
(function () {
	'use strict';

	function announce(el, message) {
		if (!el || !message) {
			return;
		}
		el.textContent = message;
		window.setTimeout(function () {
			el.textContent = '';
		}, 5000);
	}

	/**
	 * Fallback para HTTP o navegadores sin navigator.clipboard.
	 */
	function copyTextFallback(text) {
		var ta = document.createElement('textarea');
		ta.value = text;
		ta.setAttribute('readonly', '');
		ta.setAttribute('aria-hidden', 'true');
		ta.style.position = 'fixed';
		ta.style.left = '-9999px';
		ta.style.top = '0';
		document.body.appendChild(ta);
		ta.focus();
		ta.select();
		ta.setSelectionRange(0, text.length);
		var ok = false;
		try {
			ok = document.execCommand('copy');
		} catch (e) {
			ok = false;
		}
		document.body.removeChild(ta);
		return ok;
	}

	function copyUrlToClipboard(url, status, strings) {
		var copiedMsg = strings.copied || '';
		var errorMsg = strings.error || '';

		function onSuccess() {
			announce(status, copiedMsg);
		}

		function onHardFail() {
			announce(status, errorMsg);
		}

		function tryFallback() {
			if (copyTextFallback(url)) {
				onSuccess();
			} else {
				onHardFail();
			}
		}

		if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
			navigator.clipboard.writeText(url).then(onSuccess, tryFallback);
		} else {
			tryFallback();
		}
	}

	function init() {
		var btn = document.querySelector('.mwm-single-copy-btn');
		var status = document.getElementById('mwm-single-copy-status');
		if (!btn) {
			return;
		}

		var url = btn.getAttribute('data-copy-url');
		if (!url) {
			return;
		}

		var strings =
			typeof window.zenyxSinglePostCopy === 'object' && window.zenyxSinglePostCopy !== null
				? window.zenyxSinglePostCopy
				: { copied: '', error: '' };

		btn.addEventListener('click', function () {
			copyUrlToClipboard(url, status, strings);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
