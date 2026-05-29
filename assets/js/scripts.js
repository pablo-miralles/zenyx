jQuery(function ($) {
	const $body = $('body');
	const $toggle = $('#mwm-mobile-toggle');
	const $drawer = $('#mwm-mobile-menu');
	const $backdrop = $('#mwm-mobile-backdrop');
	const $header = $('.mwm-header');
	const $desktopMenuParents = $('.mwm-header-nav__list > li.menu-item-has-children');

	const labelOpen = $toggle.data('label-open') || '';
	const labelClose = $toggle.data('label-close') || '';

	const isDesktopMq = () => window.matchMedia('(min-width: 1024px)').matches;

	const getHeaderBaseHeight = () => {
		const $bar = $('.mwm-header__bar').first();
		return $bar.length ? Math.ceil($bar.outerHeight(true)) : 0;
	};

	const applyDesktopHeaderHeight = (extraHeight = 0) => {
		if (!$header.length || !isDesktopMq() || $body.hasClass('mwm-mobile-menu-open')) {
			return;
		}
		const baseHeight = getHeaderBaseHeight();
		const safeExtra = Math.max(0, Math.ceil(extraHeight));
		$header.css('height', `${baseHeight + safeExtra}px`);
		$header.toggleClass('mwm-header--submenu-expanded', safeExtra > 0);
	};

	const resetDesktopHeaderHeight = () => {
		if (!$header.length) {
			return;
		}
		if (!isDesktopMq() || $body.hasClass('mwm-mobile-menu-open')) {
			$header.css('height', '');
			$header.removeClass('mwm-header--submenu-expanded');
			return;
		}
		applyDesktopHeaderHeight(0);
	};

	const cacheDesktopSubmenuHeights = () => {
		$desktopMenuParents.each(function () {
			const $item = $(this);
			const $sub = $item.children('.sub-menu');
			if (!$sub.length) {
				$item.data('submenuHeight', 0);
				return;
			}
			const submenuHeight = Math.ceil($sub.outerHeight(true) || 0);
			$item.data('submenuHeight', submenuHeight);
		});
	};

	const updateHeaderHeight = () => {
		// Keep this variable tied to base header bar only.
		// Ignore temporary desktop expansion caused by submenu hover.
		const baseHeight = getHeaderBaseHeight();
		document.documentElement.style.setProperty('--header-height', `${Math.round(baseHeight)}px`);
	};

	const refreshHeaderZoneColors = () => {
		document.dispatchEvent(new CustomEvent('zenyx:header-zones-refresh'));
	};

	const closeMobileMenu = () => {
		$body.removeClass('mwm-mobile-menu-open');
		$toggle.attr('aria-expanded', 'false');
		$drawer.attr('aria-hidden', 'true');
		$backdrop.attr('aria-hidden', 'true');
		if (labelOpen) {
			$toggle.attr('aria-label', labelOpen);
		}
		$('.mwm-mobile-nav .menu-item-has-children > .sub-menu').stop(true, true).slideUp(0);
		$('.mwm-mobile-nav .menu-item-has-children').removeClass('is-open');
		refreshHeaderZoneColors();
	};

	const openMobileMenu = () => {
		$body.addClass('mwm-mobile-menu-open');
		$toggle.attr('aria-expanded', 'true');
		$drawer.attr('aria-hidden', 'false');
		$backdrop.attr('aria-hidden', 'false');
		if (labelClose) {
			$toggle.attr('aria-label', labelClose);
		}
		refreshHeaderZoneColors();
	};

	const toggleMobileMenu = () => {
		if ($body.hasClass('mwm-mobile-menu-open')) {
			closeMobileMenu();
		} else {
			openMobileMenu();
		}
	};

	$(window).on('resize', function () {
		cacheDesktopSubmenuHeights();
		resetDesktopHeaderHeight();
		updateHeaderHeight();
		if ($toggle.length && isDesktopMq() && $body.hasClass('mwm-mobile-menu-open')) {
			closeMobileMenu();
		}
	});
	cacheDesktopSubmenuHeights();
	resetDesktopHeaderHeight();
	updateHeaderHeight();

	if ($toggle.length && $drawer.length) {
		$toggle.on('click', function (e) {
			e.preventDefault();
			toggleMobileMenu();
		});

		$backdrop.on('click', function () {
			closeMobileMenu();
		});

		$(document).on('keydown', function (e) {
			if (e.key === 'Escape' && $body.hasClass('mwm-mobile-menu-open')) {
				closeMobileMenu();
			}
		});
	}

	/* Menú móvil: submenús con slideToggle (solo .mwm-mobile-nav) */
	$(document).on('click', '.mwm-mobile-nav .menu-item-has-children > a', function (e) {
		e.preventDefault();
		e.stopPropagation();
		const $link = $(this);
		const $li = $link.parent();
		const $sub = $link.siblings('.sub-menu').first();
		if (!$sub.length) {
			return;
		}
		$sub.stop(true, true).slideToggle(200);
		$li.toggleClass('is-open');
	});

	/* Enlaces padre con # (Customizer) */
	$(document).on('click', 'a.mwm-menu-parent-no-nav', function (e) {
		e.preventDefault();
	});

	/* Desktop: expandir altura del header al abrir submenú por hover/focus */
	$(document)
		.on('mouseenter focusin', '.mwm-header-nav__list > li.menu-item-has-children', function () {
			if (!isDesktopMq() || $body.hasClass('mwm-mobile-menu-open')) {
				return;
			}
			const submenuHeight = Number($(this).data('submenuHeight') || 0);
			applyDesktopHeaderHeight(submenuHeight);
		})
		.on('mouseleave focusout', '.mwm-header-nav__list > li.menu-item-has-children', function () {
			setTimeout(function () {
				const $activeItem = $('.mwm-header-nav__list > li.menu-item-has-children:hover, .mwm-header-nav__list > li.menu-item-has-children:focus-within').first();
				if ($activeItem.length) {
					const submenuHeight = Number($activeItem.data('submenuHeight') || 0);
					applyDesktopHeaderHeight(submenuHeight);
				} else {
					resetDesktopHeaderHeight();
				}
			}, 0);
		});

	/* Desktop: quitar foco al clic con puntero (evita submenú “pegado” por :focus-within) */
	$(document).on('click', '.mwm-header-nav .menu-item-has-children > a', function (e) {
		const pt =
			e.pointerType ||
			(e.originalEvent && e.originalEvent.pointerType) ||
			'';
		if (pt === 'mouse' || pt === 'touch' || pt === 'pen') {
			this.blur();
			resetDesktopHeaderHeight();
		}
	});

	if (window.Fancybox && typeof window.Fancybox.bind === 'function') {
		window.Fancybox.bind('[data-fancybox="caso-video"]', {
			dragToClose: true,
			placeFocusBack: true,
			Carousel: {
				infinite: false,
				Arrows: false,
				Thumbs: false,
			},
			keyboard: {
				Escape: 'close',
				Delete: 'close',
				Backspace: 'close',
				PageUp: false,
				PageDown: false,
				ArrowUp: false,
				ArrowDown: false,
				ArrowRight: false,
				ArrowLeft: false,
			},
			Toolbar: {
				display: {
					left: [],
					middle: [],
					right: ['close'],
				},
			},
		});
	}
});
