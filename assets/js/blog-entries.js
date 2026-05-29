/**
 * Blog entries: category filters + load more (admin-ajax).
 */
(function () {
	'use strict';

	const root = document.getElementById('mwm-entries-root');
	const cfg = typeof window.mwmBlogEntries === 'object' && window.mwmBlogEntries ? window.mwmBlogEntries : null;

	if (!root || !cfg || !cfg.ajaxUrl || !cfg.nonce) {
		return;
	}

	const grid = document.getElementById('mwm-entries-grid');
	const loadBtn = document.getElementById('mwm-entries-load-more');
	const statusEl = document.getElementById('mwm-entries-status');
	const filterAll = document.getElementById('mwm-entries-filter-all');
	const catFilters = root.querySelectorAll('.mwm-entries-filter[data-cat-id]');

	if (!grid || !loadBtn) {
		return;
	}

	const strings = cfg.strings || {};
	const maxPages = Math.max(1, parseInt(root.getAttribute('data-max-pages'), 10) || 1);

	let nextPage = maxPages > 1 ? 2 : null;
	const selectedCats = new Set();

	const initialCatsRaw = root.getAttribute('data-initial-categories');
	if (initialCatsRaw) {
		try {
			const parsed = JSON.parse(initialCatsRaw);
			if (Array.isArray(parsed)) {
				parsed.forEach(function (id) {
					const n = parseInt(id, 10);
					if (n) {
						selectedCats.add(n);
					}
				});
			}
		} catch (e) {
			// ignore invalid JSON
		}
	}

	function categoryPayload() {
		return JSON.stringify(Array.from(selectedCats));
	}

	function announce(msg) {
		if (statusEl && msg) {
			statusEl.textContent = msg;
		}
	}

	function setLoadLoading(loading) {
		loadBtn.disabled = loading;
		loadBtn.setAttribute('aria-busy', loading ? 'true' : 'false');
		const label = loadBtn.querySelector('.mwm-entries-load-more__label');
		const loadingEl = loadBtn.querySelector('.mwm-entries-load-more__loading');
		if (label && loadingEl) {
			label.classList.toggle('hidden', loading);
			loadingEl.classList.toggle('hidden', !loading);
		}
	}

	function setLoadVisible(show) {
		loadBtn.hidden = !show;
	}

	function syncFilterPressed() {
		if (filterAll) {
			filterAll.setAttribute('aria-pressed', selectedCats.size === 0 ? 'true' : 'false');
		}
		catFilters.forEach(function (btn) {
			const id = parseInt(btn.getAttribute('data-cat-id'), 10);
			btn.setAttribute('aria-pressed', selectedCats.has(id) ? 'true' : 'false');
		});
	}

	function applyGridHtml(html, replace) {
		const trimmed = (html || '').trim();
		if (!trimmed) {
			grid.innerHTML =
				'<p class="col-span-full font-body text-center text-white/80">' +
				(strings.empty || '') +
				'</p>';
			return;
		}
		if (replace) {
			grid.innerHTML = trimmed;
		} else {
			grid.insertAdjacentHTML('beforeend', trimmed);
		}
	}

	async function requestPage(paged) {
		const body = new URLSearchParams();
		body.set('action', cfg.action);
		body.set('nonce', cfg.nonce);
		body.set('paged', String(paged));
		body.set('categories', categoryPayload());

		const res = await fetch(cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			},
			body: body.toString(),
		});

		const json = await res.json();
		if (!json || !json.success || !json.data) {
			throw new Error(strings.error || 'Error');
		}
		return json.data;
	}

	async function onFilterChange() {
		syncFilterPressed();
		nextPage = null;
		setLoadLoading(true);
		setLoadVisible(false);
		try {
			const data = await requestPage(1);
			applyGridHtml(data.html, true);
			if (data.has_more) {
				nextPage = data.next_page;
				setLoadVisible(true);
			}
			announce(strings.announced || '');
		} catch (e) {
			grid.innerHTML =
				'<p class="col-span-full font-body text-center text-acento">' + (strings.error || '') + '</p>';
			setLoadVisible(false);
		} finally {
			setLoadLoading(false);
		}
	}

	async function onLoadMore() {
		if (!nextPage) {
			return;
		}
		const paged = nextPage;
		setLoadLoading(true);
		try {
			const data = await requestPage(paged);
			applyGridHtml(data.html, false);
			if (data.has_more) {
				nextPage = data.next_page;
				setLoadVisible(true);
			} else {
				nextPage = null;
				setLoadVisible(false);
			}
			announce(strings.loadedMore || '');
		} catch (e) {
			announce(strings.error || '');
		} finally {
			setLoadLoading(false);
		}
	}

	if (filterAll) {
		filterAll.addEventListener('click', function () {
			selectedCats.clear();
			onFilterChange();
		});
	}

	catFilters.forEach(function (btn) {
		btn.addEventListener('click', function () {
			const id = parseInt(btn.getAttribute('data-cat-id'), 10);
			if (!id) {
				return;
			}
			if (selectedCats.has(id)) {
				selectedCats.delete(id);
			} else {
				selectedCats.add(id);
			}
			onFilterChange();
		});
	});

	loadBtn.addEventListener('click', onLoadMore);

	syncFilterPressed();
})();
