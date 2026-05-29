/**
 * Customizer: galería de imágenes ilimitada (con reordenación).
 */
(function ($) {
	'use strict';

	function parseIds(raw) {
		if (!raw) {
			return [];
		}
		try {
			var parsed = JSON.parse(raw);
			if (!Array.isArray(parsed)) {
				return [];
			}
			return parsed
				.map(function (id) {
					return parseInt(id, 10);
				})
				.filter(function (id) {
					return id > 0;
				});
		} catch (e) {
			return [];
		}
	}

	function thumbUrl(attachment) {
		if (!attachment) {
			return '';
		}
		var sizes = attachment.sizes || {};
		return (
			sizes.thumbnail?.url ||
			sizes.medium?.url ||
			attachment.url ||
			''
		);
	}

	function mergeSelectionOrder(existingIds, selectedIds) {
		var ordered = [];
		var selected = selectedIds.slice();

		existingIds.forEach(function (id) {
			var idx = selected.indexOf(id);
			if (idx !== -1) {
				ordered.push(id);
				selected.splice(idx, 1);
			}
		});

		selected.forEach(function (id) {
			ordered.push(id);
		});

		return ordered;
	}

	function initSortable($control) {
		var $list = $control.find('.mwm-customize-gallery-list');
		if (!$list.length || typeof $list.sortable !== 'function') {
			return;
		}

		if ($list.hasClass('ui-sortable')) {
			$list.sortable('destroy');
		}

		$list.sortable({
			items: '> .mwm-customize-gallery-item',
			cursor: 'move',
			tolerance: 'pointer',
			placeholder: 'mwm-customize-gallery-sortable-placeholder',
			forcePlaceholderSize: true,
			axis: 'xy',
			update: function () {
				var ids = [];
				$list.find('.mwm-customize-gallery-item').each(function () {
					ids.push(parseInt($(this).data('id'), 10));
				});
				$control
					.find('.mwm-customize-gallery-input')
					.val(JSON.stringify(ids))
					.trigger('change');
			},
		});
	}

	function renderPreview($control, ids) {
		var $preview = $control.find('.mwm-customize-gallery-preview');
		var $clear = $control.find('.mwm-customize-gallery-clear');
		var reorderHint =
			window.mwmCustomizeGallery?.reorderHint ||
			'Arrastra para reordenar';

		if (!ids.length) {
			$preview.html(
				'<p class="mwm-customize-gallery-empty">' +
					(window.mwmCustomizeGallery?.emptyLabel ||
						'No hay imágenes seleccionadas.') +
					'</p>'
			);
			$clear.attr('hidden', 'hidden');
			return;
		}

		$clear.removeAttr('hidden');

		var html =
			'<p class="mwm-customize-gallery-reorder-hint">' + reorderHint + '</p>';
		html += '<ul class="mwm-customize-gallery-list">';
		ids.forEach(function (id) {
			var attachment = wp.media.attachment(id);
			var url = thumbUrl(attachment.attributes);

			if (!url && attachment.fetch) {
				attachment.fetch();
				url = thumbUrl(attachment.attributes);
			}

			html +=
				'<li class="mwm-customize-gallery-item" data-id="' +
				id +
				'" title="' +
				reorderHint +
				'">' +
				'<span class="mwm-customize-gallery-handle" aria-hidden="true"></span>' +
				(url
					? '<img src="' + url + '" alt="" width="60" height="60" draggable="false" />'
					: '<span class="mwm-customize-gallery-placeholder">#' +
					  id +
					  '</span>') +
				'<button type="button" class="button-link mwm-customize-gallery-remove" data-id="' +
				id +
				'" aria-label="' +
				(window.mwmCustomizeGallery?.removeLabel || 'Quitar imagen') +
				'">&times;</button></li>';
		});
		html += '</ul>';
		$preview.html(html);

		ids.forEach(function (id) {
			var attachment = wp.media.attachment(id);
			if (attachment && !attachment.get('url')) {
				attachment.fetch().done(function () {
					var $item = $preview.find(
						'.mwm-customize-gallery-item[data-id="' + id + '"]'
					);
					var src = thumbUrl(attachment.attributes);
					if (src && $item.length) {
						$item.find('.mwm-customize-gallery-placeholder').replaceWith(
							'<img src="' +
								src +
								'" alt="" width="60" height="60" draggable="false" />'
						);
					}
				});
			}
		});

		initSortable($control);
	}

	function setIds($control, ids) {
		var unique = [];
		ids.forEach(function (id) {
			id = parseInt(id, 10);
			if (id > 0 && unique.indexOf(id) === -1) {
				unique.push(id);
			}
		});

		$control
			.find('.mwm-customize-gallery-input')
			.val(JSON.stringify(unique))
			.trigger('change');

		renderPreview($control, unique);
	}

	function openMediaFrame($control) {
		var $input = $control.find('.mwm-customize-gallery-input');
		var ids = parseIds($input.val());

		var frame = wp.media({
			title: window.mwmCustomizeGallery?.frameTitle || 'Seleccionar imágenes',
			button: {
				text: window.mwmCustomizeGallery?.frameButton || 'Usar estas imágenes',
			},
			library: { type: 'image' },
			multiple: 'add',
		});

		frame.on('open', function () {
			var selection = frame.state().get('selection');
			selection.reset();

			ids.forEach(function (id) {
				var attachment = wp.media.attachment(id);
				attachment.fetch();
				selection.add(attachment);
			});
		});

		frame.on('select', function () {
			var selectedIds = frame.state().get('selection').map(function (attachment) {
				return attachment.get('id');
			});
			setIds($control, mergeSelectionOrder(ids, selectedIds));
		});

		frame.open();
	}

	$(document).on('click', '.mwm-customize-gallery-select', function (event) {
		event.preventDefault();
		openMediaFrame($(this).closest('.customize-control-mwm_image_gallery'));
	});

	$(document).on('click', '.mwm-customize-gallery-clear', function (event) {
		event.preventDefault();
		setIds($(this).closest('.customize-control-mwm_image_gallery'), []);
	});

	$(document).on('click', '.mwm-customize-gallery-remove', function (event) {
		event.preventDefault();
		var $control = $(this).closest('.customize-control-mwm_image_gallery');
		var removeId = parseInt($(this).data('id'), 10);
		var ids = parseIds($control.find('.mwm-customize-gallery-input').val()).filter(
			function (id) {
				return id !== removeId;
			}
		);
		setIds($control, ids);
	});

	wp.customize.bind('ready', function () {
		$('.customize-control-mwm_image_gallery').each(function () {
			var $control = $(this);
			var ids = parseIds($control.find('.mwm-customize-gallery-input').val());
			if (ids.length) {
				initSortable($control);
			}
		});
	});
})(jQuery);
