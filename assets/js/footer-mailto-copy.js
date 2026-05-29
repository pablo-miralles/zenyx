/**
 * Enlaces mailto: copian el email al portapapeles (footer, formulario contacto, etc.).
 */
(function () {
	'use strict';

	var ROOT_SELECTORS = '#colophon, .wp-block-zenyx-form-01, .mwm-form-01';

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

	function copyTextToClipboard(text) {
		return new Promise(function (resolve, reject) {
			function tryFallback() {
				if (copyTextFallback(text)) {
					resolve();
				} else {
					reject();
				}
			}

			if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
				navigator.clipboard.writeText(text).then(resolve, tryFallback);
			} else {
				tryFallback();
			}
		});
	}

	function emailFromMailtoHref(href) {
		if (!href) {
			return '';
		}
		var match = href.match(/^mailto:([^?]+)/i);
		if (!match || !match[1]) {
			return '';
		}
		try {
			return decodeURIComponent(match[1].trim());
		} catch (e) {
			return match[1].trim();
		}
	}

	function isMailtoCopyLink(link) {
		if (!link) {
			return false;
		}
		var roots = document.querySelectorAll(ROOT_SELECTORS);
		for (var i = 0; i < roots.length; i++) {
			if (roots[i].contains(link)) {
				return true;
			}
		}
		return false;
	}

	function init() {
		if (typeof window.Notyf !== 'function') {
			return;
		}

		var strings =
			typeof window.zenyxFooterMailtoCopy === 'object' && window.zenyxFooterMailtoCopy !== null
				? window.zenyxFooterMailtoCopy
				: { copied: '', error: '' };

		var notyf = new window.Notyf({
			duration: 3000,
			position: { x: 'center', y: 'bottom' },
			dismissible: true,
			types: [
				{
					type: 'success',
					background: '#04202c',
					icon: false,
				},
				{
					type: 'error',
					background: '#8b2e2e',
					icon: false,
				},
			],
		});

		document.addEventListener('click', function (event) {
			var link = event.target.closest('a[href^="mailto:"]');
			if (!link || !isMailtoCopyLink(link)) {
				return;
			}

			var email = emailFromMailtoHref(link.getAttribute('href'));
			if (!email) {
				return;
			}

			event.preventDefault();

			copyTextToClipboard(email)
				.then(function () {
					notyf.success(strings.copied || email);
				})
				.catch(function () {
					notyf.error(strings.error || '');
				});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
