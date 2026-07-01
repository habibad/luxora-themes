/**
 * Luxora — Customizer live preview.
 *
 * @package Luxora
 */
(function () {
	'use strict';

	if (typeof wp === 'undefined' || !wp.customize) { return; }
	var api = wp.customize;

	function setVars(pairs) {
		var root = document.documentElement;
		Object.keys(pairs).forEach(function (name) { root.style.setProperty(name, pairs[name]); });
	}

	// Site title.
	api('blogname', function (value) {
		value.bind(function (to) {
			document.querySelectorAll('.luxora-site-title, .site-title a').forEach(function (el) { el.textContent = to; });
		});
	});

	// Colors.
	api('luxora_color_ink', function (v) {
		v.bind(function (to) { setVars({ '--ink': to, '--foreground': to, '--primary': to }); });
	});
	api('luxora_color_gold', function (v) {
		v.bind(function (to) { setVars({ '--gold': to, '--accent': to, '--ring': to }); });
	});
	api('luxora_color_cream', function (v) {
		v.bind(function (to) { setVars({ '--cream': to, '--secondary': to, '--primary-foreground': to }); });
	});
	api('luxora_color_beige', function (v) {
		v.bind(function (to) { setVars({ '--beige': to }); });
	});

	// Fonts.
	api('luxora_font_display', function (v) {
		v.bind(function (to) { setVars({ '--font-display': '"' + to + '",ui-serif,Georgia,serif' }); });
	});
	api('luxora_font_serif', function (v) {
		v.bind(function (to) { setVars({ '--font-serif': '"' + to + '",ui-serif,Georgia,serif' }); });
	});
	api('luxora_font_sans', function (v) {
		v.bind(function (to) { setVars({ '--font-sans': '"' + to + '",ui-sans-serif,system-ui,sans-serif' }); });
	});

	// Announcement bar text.
	api('luxora_announcement', function (v) {
		v.bind(function (to) {
			var el = document.querySelector('.luxora-announcement-text');
			if (el) { el.textContent = to; }
		});
	});
})();
