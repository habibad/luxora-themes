/**
 * Luxora — theme interactions.
 * Header, drawers, GSAP reveals, AJAX cart/wishlist/newsletter, gallery, shop filters.
 *
 * @package Luxora
 */
(function () {
	'use strict';

	var L = window.LUXORA || {};
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
	function qsa(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

	/* ---------------------------------------------------------------------
	 * AJAX helper
	 * ------------------------------------------------------------------- */
	function post(action, data) {
		var body = new URLSearchParams();
		body.append('action', action);
		body.append('nonce', L.nonce || '');
		Object.keys(data || {}).forEach(function (k) {
			var v = data[k];
			if (Array.isArray(v)) {
				v.forEach(function (item) { body.append(k + '[]', item); });
			} else if (v !== undefined && v !== null) {
				body.append(k, v);
			}
		});
		return fetch(L.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString()
		}).then(function (r) { return r.json(); });
	}

	/* ---------------------------------------------------------------------
	 * Toast
	 * ------------------------------------------------------------------- */
	var toastTimer;
	function toast(message, isError) {
		var el = qs('#luxora-toast');
		if (!el) {
			el = document.createElement('div');
			el.id = 'luxora-toast';
			el.setAttribute('role', 'status');
			el.setAttribute('aria-live', 'polite');
			el.className = 'luxora-toast';
			document.body.appendChild(el);
		}
		el.textContent = message;
		el.classList.toggle('is-error', !!isError);
		el.classList.add('is-visible');
		clearTimeout(toastTimer);
		toastTimer = setTimeout(function () { el.classList.remove('is-visible'); }, 3200);
	}

	/* ---------------------------------------------------------------------
	 * WooCommerce fragment application
	 * ------------------------------------------------------------------- */
	function applyFragments(fragments) {
		if (!fragments) { return; }
		Object.keys(fragments).forEach(function (selector) {
			qsa(selector).forEach(function (node) {
				var tmp = document.createElement('div');
				tmp.innerHTML = fragments[selector];
				var incoming = tmp.firstElementChild;
				if (incoming) { node.replaceWith(incoming); }
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Header scroll state
	 * ------------------------------------------------------------------- */
	function initHeader() {
		var header = qs('[data-header]');
		if (!header) { return; }
		var scrolledClasses = ['bg-background/90', 'backdrop-blur-md', 'border-b', 'border-border'];
		function onScroll() {
			var on = window.scrollY > 24;
			scrolledClasses.forEach(function (c) { header.classList.toggle(c, on); });
			header.classList.toggle('is-scrolled', on);
		}
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* ---------------------------------------------------------------------
	 * Generic overlay/drawer controller
	 * ------------------------------------------------------------------- */
	function initToggle(openSel, closeSel, panelSel, opts) {
		opts = opts || {};
		var panel = qs(panelSel);
		if (!panel) { return; }

		function open() {
			panel.classList.remove('hidden');
			panel.classList.add('is-open');
			panel.setAttribute('aria-hidden', 'false');
			document.documentElement.classList.add('luxora-no-scroll');
			if (opts.focus) { var f = qs(opts.focus, panel); if (f) { setTimeout(function () { f.focus(); }, 60); } }
			qsa(openSel).forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
		}
		function close() {
			panel.classList.add('hidden');
			panel.classList.remove('is-open');
			panel.setAttribute('aria-hidden', 'true');
			document.documentElement.classList.remove('luxora-no-scroll');
			qsa(openSel).forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
		}

		qsa(openSel).forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				if (panel.classList.contains('is-open') && opts.toggle) { close(); } else { open(); }
			});
		});
		if (closeSel) { qsa(closeSel).forEach(function (btn) { btn.addEventListener('click', function (e) { e.preventDefault(); close(); }); }); }
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { close(); } });

		return { open: open, close: close, panel: panel };
	}

	/* ---------------------------------------------------------------------
	 * GSAP reveals
	 * ------------------------------------------------------------------- */
	function initReveals() {
		if (reduceMotion || typeof window.gsap === 'undefined') { return; }
		var gsap = window.gsap;
		if (window.ScrollTrigger) { gsap.registerPlugin(window.ScrollTrigger); }

		qsa('[data-reveal]').forEach(function (el) {
			gsap.fromTo(el, { autoAlpha: 0, y: 28 }, {
				autoAlpha: 1, y: 0, duration: 0.9, ease: 'power3.out',
				scrollTrigger: { trigger: el, start: 'top 85%', once: true }
			});
		});

		qsa('[data-reveal-stagger]').forEach(function (group) {
			var items = qsa('[data-reveal-item]', group);
			if (!items.length) { return; }
			gsap.fromTo(items, { autoAlpha: 0, y: 30 }, {
				autoAlpha: 1, y: 0, duration: 0.8, ease: 'power3.out', stagger: 0.08,
				scrollTrigger: { trigger: group, start: 'top 82%', once: true }
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Add to cart
	 * ------------------------------------------------------------------- */
	function initAddToCart(miniCart) {
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('.luxora-add-to-cart');
			if (!btn || btn.disabled) { return; }
			e.preventDefault();

			if (!L.isWoo) { window.location.href = btn.href || L.cartUrl; return; }

			var pid = btn.getAttribute('data-product-id');
			var qtyInput = btn.closest('.luxora-buy') ? qs('[data-qty-input]', btn.closest('.luxora-buy')) : null;
			var qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

			btn.classList.add('is-loading');
			post('luxora_add_to_cart', { product_id: pid, quantity: qty }).then(function (res) {
				btn.classList.remove('is-loading');
				if (res && res.success) {
					applyFragments(res.data.fragments);
					toast(res.data.message || (L.i18n && L.i18n.added));
					if (miniCart) { miniCart.open(); }
				} else {
					toast((res && res.data && res.data.message) || (L.i18n && L.i18n.error), true);
				}
			}).catch(function () {
				btn.classList.remove('is-loading');
				toast(L.i18n && L.i18n.error, true);
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Wishlist toggle
	 * ------------------------------------------------------------------- */
	function initWishlist() {
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('.luxora-wishlist');
			if (!btn) { return; }
			e.preventDefault();
			var pid = btn.getAttribute('data-product-id');

			post('luxora_toggle_wishlist', { product_id: pid }).then(function (res) {
				if (res && res.success) {
					var active = res.data.active;
					btn.classList.toggle('is-active', active);
					btn.setAttribute('aria-pressed', active ? 'true' : 'false');
					toast(res.data.message);

					qsa('.luxora-wishlist-count').forEach(function (badge) {
						badge.textContent = res.data.count;
						badge.classList.toggle('hidden', res.data.count < 1);
					});

					// If on the wishlist page and removed, drop the card.
					if (!active) {
						var card = qs('[data-wishlist-card="' + pid + '"]');
						if (card) { card.remove(); }
					}
				} else {
					toast((res && res.data && res.data.message) || (L.i18n && L.i18n.error), true);
				}
			}).catch(function () { toast(L.i18n && L.i18n.error, true); });
		});
	}

	/* ---------------------------------------------------------------------
	 * Quantity steppers (generic) — cart lines also trigger AJAX update
	 * ------------------------------------------------------------------- */
	function initQtySteppers() {
		document.addEventListener('click', function (e) {
			var minus = e.target.closest('[data-qty-minus]');
			var plus = e.target.closest('[data-qty-plus]');
			if (!minus && !plus) { return; }
			var wrap = (minus || plus).closest('[data-qty], [data-cart-qty]');
			if (!wrap) { return; }
			var input = qs('[data-qty-input], [data-cart-qty-input]', wrap);
			if (!input) { return; }
			var min = parseInt(input.getAttribute('min'), 10);
			if (isNaN(min)) { min = 1; }
			var val = parseInt(input.value, 10) || min;
			val = plus ? val + 1 : val - 1;
			if (val < min) { val = min; }
			input.value = val;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		});
	}

	/* ---------------------------------------------------------------------
	 * Cart page — AJAX qty update + remove
	 * ------------------------------------------------------------------- */
	function initCartPage() {
		var cart = qs('.luxora-cart');
		if (!cart) { return; }

		var debounce;
		cart.addEventListener('change', function (e) {
			var input = e.target.closest('[data-cart-qty-input]');
			if (!input) { return; }
			var line = input.closest('[data-cart-line]');
			if (!line) { return; }
			var key = line.getAttribute('data-cart-key');
			var qty = parseInt(input.value, 10);
			if (isNaN(qty) || qty < 0) { qty = 0; }

			clearTimeout(debounce);
			line.classList.add('is-updating');
			debounce = setTimeout(function () {
				post('luxora_update_qty', { cart_key: key, quantity: qty }).then(function (res) {
					line.classList.remove('is-updating');
					if (res && res.success) {
						if (res.data.removed) {
							line.remove();
						} else {
							var lt = qs('.luxora-cart-line-total', line);
							if (lt && res.data.line_total) { lt.textContent = res.data.line_total; }
						}
						refreshCartSummary(res.data);
						applyFragments(res.data.fragments);
					} else {
						toast((res && res.data && res.data.message) || (L.i18n && L.i18n.error), true);
					}
				}).catch(function () { line.classList.remove('is-updating'); toast(L.i18n && L.i18n.error, true); });
			}, 400);
		});

		cart.addEventListener('click', function (e) {
			var rm = e.target.closest('.luxora-cart-remove');
			if (!rm) { return; }
			e.preventDefault();
			var line = rm.closest('[data-cart-line]');
			var key = line.getAttribute('data-cart-key');
			line.classList.add('is-updating');
			post('luxora_remove_item', { cart_key: key }).then(function (res) {
				if (res && res.success) {
					line.remove();
					refreshCartSummary(res.data);
					applyFragments(res.data.fragments);
					if (res.data.count < 1) { window.location.reload(); }
				}
			}).catch(function () { line.classList.remove('is-updating'); });
		});
	}

	function refreshCartSummary(data) {
		if (!data) { return; }
		var sub = qs('.luxora-summary-subtotal');
		var tot = qs('.luxora-summary-total');
		if (sub && data.subtotal) { sub.textContent = data.subtotal; }
		if (tot && data.total) { tot.textContent = data.total; }
		var count = qs('.luxora-cart-item-count');
		if (count && typeof data.count !== 'undefined') {
			count.textContent = data.count + (data.count === 1 ? ' item' : ' items');
		}
	}

	/* ---------------------------------------------------------------------
	 * Newsletter
	 * ------------------------------------------------------------------- */
	function initNewsletter() {
		qsa('.luxora-newsletter-form').forEach(function (form) {
			if (form.getAttribute('action')) { return; } // external integration handles it.
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var emailInput = qs('.luxora-newsletter-email', form);
				var msg = qs('.luxora-newsletter-msg', form.closest('.luxora-newsletter')) || qs('.luxora-newsletter-msg', form);
				var email = emailInput ? emailInput.value : '';
				post('luxora_newsletter', { email: email }).then(function (res) {
					if (res && res.success) {
						if (msg) { msg.textContent = res.data.message; }
						form.reset();
					} else {
						if (msg) { msg.textContent = (res && res.data && res.data.message) || (L.i18n && L.i18n.error); }
					}
				}).catch(function () { if (msg) { msg.textContent = L.i18n && L.i18n.error; } });
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Product gallery
	 * ------------------------------------------------------------------- */
	function initGallery() {
		var gallery = qs('[data-gallery]');
		if (!gallery) { return; }
		var main = qs('[data-gallery-main]', gallery);
		qsa('[data-gallery-thumb]', gallery).forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				var full = thumb.getAttribute('data-full');
				if (full && main) { main.src = full; }
				qsa('[data-gallery-thumb]', gallery).forEach(function (t) {
					t.classList.remove('ring-1', 'ring-ink');
					t.classList.add('opacity-60', 'hover:opacity-100');
				});
				thumb.classList.add('ring-1', 'ring-ink');
				thumb.classList.remove('opacity-60', 'hover:opacity-100');
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Color picker (single product)
	 * ------------------------------------------------------------------- */
	function initColorPicker() {
		var picker = qs('[data-color-picker]');
		if (!picker) { return; }
		var label = qs('[data-color-name]');
		qsa('[data-color]', picker).forEach(function (swatch) {
			swatch.addEventListener('click', function () {
				qsa('[data-color]', picker).forEach(function (s) {
					s.classList.remove('ring-2', 'ring-ink', 'ring-offset-2');
					s.classList.add('ring-border', 'hover:ring-ink/40');
				});
				swatch.classList.add('ring-2', 'ring-ink', 'ring-offset-2');
				swatch.classList.remove('ring-border', 'hover:ring-ink/40');
				if (label) { label.textContent = swatch.getAttribute('data-color-value'); }
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Shop — filters, ordering, price bands, load more
	 * ------------------------------------------------------------------- */
	function initShop() {
		// Mobile filter panel toggle.
		var panel = qs('[data-filter-panel]');
		if (panel) {
			qsa('[data-filter-open]').forEach(function (b) {
				b.addEventListener('click', function () { panel.classList.add('is-open'); document.documentElement.classList.add('luxora-no-scroll'); });
			});
			qsa('[data-filter-close]').forEach(function (b) {
				b.addEventListener('click', function () { panel.classList.remove('is-open'); document.documentElement.classList.remove('luxora-no-scroll'); });
			});
		}

		// AJAX filtering logic
		var filterForm = qs('[data-filter-panel] form');
		if (filterForm) {
			function applyFiltersAjax(url, pushToHistory) {
				if (pushToHistory === undefined) { pushToHistory = true; }
				var mainContent = qs('.luxora-shop-main');
				if (!mainContent) { return; }

				// Add visual loading state
				mainContent.classList.add('opacity-50', 'pointer-events-none');

				fetch(url, { credentials: 'same-origin' })
					.then(function (res) { return res.text(); })
					.then(function (html) {
						var doc = new DOMParser().parseFromString(html, 'text/html');
						var incomingMain = qs('.luxora-shop-main', doc);
						if (incomingMain) {
							mainContent.innerHTML = incomingMain.innerHTML;
						}
						
						var incomingFilters = qs('[data-filter-panel] form', doc);
						if (incomingFilters) {
							filterForm.innerHTML = incomingFilters.innerHTML;
						}

						mainContent.classList.remove('opacity-50', 'pointer-events-none');
						
						if (pushToHistory) {
							window.history.pushState({ path: url }, '', url);
						}

						initReveals();
					})
					.catch(function () {
						mainContent.classList.remove('opacity-50', 'pointer-events-none');
						window.location.href = url;
					});
			}

			// Listen for input changes in the filter form
			filterForm.addEventListener('change', function (e) {
				// Update hidden min/max price fields if a radio is changed
				if (e.target && e.target.classList.contains('luxora-price-band')) {
					var min = qs('[data-price-min]', filterForm);
					var max = qs('[data-price-max]', filterForm);
					if (min) { min.value = e.target.getAttribute('data-min') || ''; }
					if (max) { max.value = e.target.getAttribute('data-max') || ''; }
				}

				// Build query params
				var formData = new FormData(filterForm);
				var params = new URLSearchParams();
				for (var pair of formData.entries()) {
					// Don't submit price_band helper radio itself, only the min/max values
					if (pair[0] !== 'price_band') {
						params.append(pair[0], pair[1]);
					}
				}

				var baseUrl = filterForm.getAttribute('action') || window.location.pathname;
				var url = baseUrl + (params.toString() ? '?' + params.toString() : '');

				applyFiltersAjax(url);
			});

			// Handle browser back/forward buttons
			window.addEventListener('popstate', function () {
				applyFiltersAjax(window.location.href, false);
			});

			// Intercept "Clear all" link click
			document.addEventListener('click', function (e) {
				var clearLink = e.target.closest('[data-filter-panel] form a.link-underline');
				if (clearLink) {
					e.preventDefault();
					filterForm.reset();
					var min = qs('[data-price-min]', filterForm);
					var max = qs('[data-price-max]', filterForm);
					if (min) { min.value = ''; }
					if (max) { max.value = ''; }

					var href = clearLink.getAttribute('href') || window.location.pathname;
					applyFiltersAjax(href);
				}
			});

			filterForm.addEventListener('submit', function (e) {
				e.preventDefault();
			});
		}

		// Ordering dropdown (event delegation).
		document.addEventListener('click', function (e) {
			var toggle = e.target.closest('[data-ordering-toggle]');
			var menu = document.querySelector('[data-ordering-menu]');
			if (toggle && menu) {
				e.stopPropagation();
				var open = menu.classList.toggle('hidden');
				toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
			} else if (menu) {
				menu.classList.add('hidden');
				var activeToggle = document.querySelector('[data-ordering-toggle]');
				if (activeToggle) { activeToggle.setAttribute('aria-expanded', 'false'); }
			}
		});

		// Load more (event delegation).
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-load-more]');
			if (!btn) { return; }
			e.preventDefault();

			var page = parseInt(btn.getAttribute('data-page'), 10);
			var max = parseInt(btn.getAttribute('data-max'), 10);
			var url = btn.getAttribute('href');
			btn.classList.add('is-loading');

			fetch(url, { credentials: 'same-origin' })
				.then(function (r) { return r.text(); })
				.then(function (html) {
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var incoming = qsa('.luxora-products > li', doc);
					var list = qs('.luxora-products');
					if (list && incoming.length) {
						incoming.forEach(function (li) { list.appendChild(document.importNode(li, true)); });
					}
					btn.classList.remove('is-loading');
					if (page >= max) {
						btn.remove();
					} else {
						btn.setAttribute('data-page', page + 1);
						var next = url.replace(/\/page\/\d+/, '/page/' + (page + 1));
						if (next === url) { next = url.replace(/paged=\d+/, 'paged=' + (page + 1)); }
						btn.setAttribute('href', next);
					}
					initReveals();
				})
				.catch(function () {
					btn.classList.remove('is-loading');
					window.location.href = url;
				});
		});
	}

	/* ---------------------------------------------------------------------
	 * Coupon (cart)
	 * ------------------------------------------------------------------- */
	function initCoupon() {
		var form = qs('[data-coupon-form]');
		if (!form) { return; }
		form.addEventListener('submit', function (e) {
			// Let it submit through WooCommerce's cart form fallback if no AJAX endpoint.
			e.preventDefault();
			var input = qs('[data-coupon-input]', form);
			var code = input ? input.value.trim() : '';
			if (!code) { return; }
			// Post to the cart URL with coupon (native handling), then reload.
			var body = new URLSearchParams();
			body.append('coupon_code', code);
			body.append('apply_coupon', 'Apply coupon');
			fetch(L.cartUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body.toString() })
				.then(function () { window.location.reload(); })
				.catch(function () { window.location.reload(); });
		});
	}

	/* ---------------------------------------------------------------------
	 * Contact form
	 * ------------------------------------------------------------------- */
	function initContact() {
		var form = qs('.luxora-contact-form');
		if (!form) { return; }
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var msg = qs('.luxora-contact-msg', form);
			var data = {
				name: (form.querySelector('[name="name"]') || {}).value || '',
				email: (form.querySelector('[name="email"]') || {}).value || '',
				subject: (form.querySelector('[name="subject"]') || {}).value || '',
				message: (form.querySelector('[name="message"]') || {}).value || ''
			};
			var btn = form.querySelector('button[type="submit"]');
			if (btn) { btn.classList.add('is-loading'); }
			post('luxora_contact', data).then(function (res) {
				if (btn) { btn.classList.remove('is-loading'); }
				if (res && res.success) {
					if (msg) { msg.textContent = res.data.message; msg.classList.remove('is-error'); }
					form.reset();
				} else {
					if (msg) { msg.textContent = (res && res.data && res.data.message) || (L.i18n && L.i18n.error); msg.classList.add('is-error'); }
				}
			}).catch(function () {
				if (btn) { btn.classList.remove('is-loading'); }
				if (msg) { msg.textContent = L.i18n && L.i18n.error; msg.classList.add('is-error'); }
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Multi-step checkout wizard
	 * ------------------------------------------------------------------- */
	function initCheckoutWizard() {
		var wizard = qs('[data-checkout-wizard]');
		if (!wizard) { return; }
		var totalSteps = 4;
		var current = 0;
		var nextBtn = qs('[data-step-next]', wizard);
		var backBtn = qs('[data-step-back]', wizard);
		var dots = qsa('[data-dot]', wizard);

		wizard.classList.add('js-wizard');

		function panels() { return qsa('.luxora-step[data-step]', wizard); }

		function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

		function fillRecap() {
			var recap = qs('[data-review-recap]', wizard);
			if (!recap) { return; }
			function v(id) { var el = qs('#' + id); return el ? (el.value || '') : ''; }
			var name = (v('billing_first_name') + ' ' + v('billing_last_name')).trim();
			var addr = [v('billing_address_1'), v('billing_city'), v('billing_state'), v('billing_postcode')].filter(Boolean).join(', ');
			var contact = [v('billing_email'), v('billing_phone')].filter(Boolean).join(' \u00b7 ');
			var payEl = qs('input[name="payment_method"]:checked', wizard);
			var payLabel = '';
			if (payEl) { var lbl = qs('label[for="' + payEl.id + '"]', wizard); payLabel = lbl ? lbl.textContent.trim() : payEl.value; }
			var shipTo = [name, addr, contact].filter(Boolean).map(esc).join('\n');
			recap.innerHTML =
				'<div class="border border-border p-6"><p class="eyebrow mb-3">Shipping to</p><p class="whitespace-pre-line">' + (shipTo || '\u2014') + '</p></div>' +
				'<div class="border border-border p-6"><p class="eyebrow mb-3">Payment</p><p>' + (esc(payLabel) || '\u2014') + '</p></div>';
		}

		function applyStep() {
			panels().forEach(function (p) {
				var s = parseInt(p.getAttribute('data-step'), 10);
				p.classList.toggle('is-active', s === current);
			});
			dots.forEach(function (dot) {
				var d = parseInt(dot.getAttribute('data-dot'), 10);
				var bubble = qs('.luxora-step-bubble', dot);
				var num = qs('.luxora-step-num', dot);
				var chk = qs('.luxora-step-check', dot);
				var label = qs('.luxora-step-label', dot);
				var done = d < current, active = d === current, on = done || active;
				if (bubble) {
					bubble.classList.toggle('bg-ink', on);
					bubble.classList.toggle('text-cream', on);
					bubble.classList.toggle('border-ink', on);
					bubble.classList.toggle('border-border', !on);
					bubble.classList.toggle('text-muted-foreground', !on);
				}
				if (num) { num.classList.toggle('hidden', done); }
				if (chk) { chk.classList.toggle('hidden', !done); }
				if (label) { label.classList.toggle('text-ink', active); label.classList.toggle('text-muted-foreground', !active); }
				dot.classList.toggle('cursor-pointer', done);
			});
			if (backBtn) { backBtn.disabled = (current === 0); }
			if (nextBtn) { nextBtn.classList.toggle('hidden', current === totalSteps - 1); }
			if (current === totalSteps - 1) { fillRecap(); }
		}

		function firstInvalid() {
			var invalid = null;
			qsa('.luxora-step.is-active', wizard).forEach(function (panel) {
				qsa('.validate-required', panel).forEach(function (row) {
					var field = row.querySelector('input, select, textarea');
					if (!field) { return; }
					var ok = (field.type === 'checkbox') ? field.checked : String(field.value || '').trim() !== '';
					row.classList.toggle('woocommerce-invalid', !ok);
					if (!ok && !invalid) { invalid = field; }
				});
			});
			return invalid;
		}

		function go(step) {
			current = Math.max(0, Math.min(totalSteps - 1, step));
			applyStep();
			var top = wizard.getBoundingClientRect().top + window.scrollY - 90;
			window.scrollTo({ top: top, behavior: reduceMotion ? 'auto' : 'smooth' });
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				var invalid = firstInvalid();
				if (invalid) { invalid.focus(); toast('Please complete the required fields.', true); return; }
				if (current === 2) {
					var methods = qsa('input[name="payment_method"]', wizard);
					if (methods.length && !qs('input[name="payment_method"]:checked', wizard)) {
						toast('Please choose a payment method.', true); return;
					}
				}
				// Refresh totals / payment methods when leaving the address steps.
				if (current <= 1 && window.jQuery) { window.jQuery(document.body).trigger('update_checkout'); }
				go(current + 1);
			});
		}
		if (backBtn) { backBtn.addEventListener('click', function () { go(current - 1); }); }

		dots.forEach(function (dot) {
			dot.addEventListener('click', function () {
				var d = parseInt(dot.getAttribute('data-dot'), 10);
				if (d < current) { go(d); }
			});
		});

		// Synchronize same-as-shipping address checkbox
		function initSameAddressToggle() {
			var toggle = qs('#luxora-same-address-checkbox', wizard);
			if (!toggle) { return; }

			var shipCheckbox = qs('#ship-to-different-address-checkbox', wizard);
			if (shipCheckbox) {
				shipCheckbox.checked = true;
			}

			var fieldsToSync = [
				{ from: 'billing_first_name', to: 'shipping_first_name' },
				{ from: 'billing_last_name', to: 'shipping_last_name' },
				{ from: 'billing_address_1', to: 'shipping_address_1' }
			];

			function sync() {
				if (toggle.checked) {
					fieldsToSync.forEach(function (pair) {
						var fromEl = qs('#' + pair.from);
						var toEl = qs('#' + pair.to);
						if (fromEl && toEl) {
							toEl.value = fromEl.value;
							toEl.readOnly = true;
							toEl.classList.add('opacity-60', 'cursor-not-allowed');
						}
					});
				} else {
					fieldsToSync.forEach(function (pair) {
						var toEl = qs('#' + pair.to);
						if (toEl) {
							toEl.readOnly = false;
							toEl.classList.remove('opacity-60', 'cursor-not-allowed');
						}
					});
				}
			}

			sync();
			toggle.addEventListener('change', sync);

			fieldsToSync.forEach(function (pair) {
				var fromEl = qs('#' + pair.from);
				if (fromEl) {
					fromEl.addEventListener('input', sync);
					fromEl.addEventListener('change', sync);
				}
			});

			if (window.jQuery) {
				window.jQuery(document.body).on('updated_checkout', function() {
					var sc = qs('#ship-to-different-address-checkbox', wizard);
					if (sc) { sc.checked = true; }
					sync();
				});
			}
		}

		// Update active payment method selected state
		function updateSelectedPaymentMethod() {
			var methods = qsa('#payment ul.payment_methods li', wizard);
			methods.forEach(function (li) {
				var radio = li.querySelector('input[type="radio"]');
				li.classList.toggle('is-selected', radio && radio.checked);
			});
		}

		// Automatic reordering and alignment of checkout fields
		function reorderCheckoutFields() {
			var billingContainer = qs('.woocommerce-billing-fields__field-wrapper', wizard);
			if (billingContainer) {
				var billingOrder = [
					'billing_first_name_field',
					'billing_last_name_field',
					'billing_email_field',
					'billing_phone_field',
					'billing_address_1_field',
					'billing_city_field',
					'billing_state_field'
				];
				billingOrder.forEach(function (id) {
					var field = qs('#' + id, billingContainer);
					if (field) {
						billingContainer.appendChild(field);
						field.classList.remove('form-row-first', 'form-row-last', 'form-row-wide');
						if (id === 'billing_first_name_field' || id === 'billing_email_field' || id === 'billing_city_field') {
							field.classList.add('form-row-first');
						} else if (id === 'billing_last_name_field' || id === 'billing_phone_field' || id === 'billing_state_field') {
							field.classList.add('form-row-last');
						} else {
							field.classList.add('form-row-wide');
						}
					}
				});
			}

			var shippingContainer = qs('.woocommerce-shipping-fields__field-wrapper', wizard);
			if (shippingContainer) {
				var shippingOrder = [
					'shipping_first_name_field',
					'shipping_last_name_field',
					'shipping_address_1_field',
					'shipping_city_field',
					'shipping_state_field'
				];
				shippingOrder.forEach(function (id) {
					var field = qs('#' + id, shippingContainer);
					if (field) {
						shippingContainer.appendChild(field);
						field.classList.remove('form-row-first', 'form-row-last', 'form-row-wide');
						if (id === 'shipping_first_name_field' || id === 'shipping_city_field') {
							field.classList.add('form-row-first');
						} else if (id === 'shipping_last_name_field' || id === 'shipping_state_field') {
							field.classList.add('form-row-last');
						} else {
							field.classList.add('form-row-wide');
						}
					}
				});
			}
		}

		initSameAddressToggle();
		updateSelectedPaymentMethod();
		reorderCheckoutFields();

		// WooCommerce replaces #payment on refresh — re-apply the active step.
		if (window.jQuery) {
			window.jQuery(document.body).on('updated_checkout', function () {
				applyStep();
				updateSelectedPaymentMethod();
				reorderCheckoutFields();
			});
		}

		applyStep();
	}

	/* ---------------------------------------------------------------------
	 * Variation swatches (variable products)
	 * Syncs custom swatch buttons → hidden WooCommerce <select> so that
	 * WooCommerce's own variation.js can calculate prices / availability.
	 *
	 * The "COLOR — Camel" label header is now rendered by PHP inside
	 * luxora_variation_swatch_html(). JS only needs to update the text of
	 * the [data-swatch-label] span on swatch click / reset.
	 * ------------------------------------------------------------------- */
	function initVariationSwatches() {
		var form = document.querySelector('.variations_form');
		if (!form) { return; }

		// Customize Add to Cart button to match "ADD TO BAG" with bag icon
		var varAddBtn = form.querySelector('.single_add_to_cart_button');
		if (varAddBtn) {
			varAddBtn.classList.remove('button', 'alt');
			varAddBtn.classList.add('btn-luxe', 'w-full', 'justify-center');
			varAddBtn.innerHTML = '<svg class="h-4 w-4 mr-2 inline-block align-middle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4H6z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg><span class="align-middle">' + varAddBtn.textContent.trim().replace(/add to cart/i, 'Add to bag') + '</span>';
		}

		// Customize quantity stepper
		var varQtyContainer = form.querySelector('.quantity');
		if (varQtyContainer && !varQtyContainer.querySelector('[data-qty-minus]')) {
			var qtyInput = varQtyContainer.querySelector('input.qty');
			if (qtyInput) {
				varQtyContainer.className = 'inline-flex items-center border border-ink luxora-qty mr-4';
				varQtyContainer.setAttribute('data-qty', '');
				
				var minusBtn = document.createElement('button');
				minusBtn.type = 'button';
				minusBtn.className = 'h-12 w-12 grid place-items-center hover:bg-ink hover:text-cream transition';
				minusBtn.setAttribute('data-qty-minus', '');
				minusBtn.setAttribute('aria-label', 'Decrease quantity');
				minusBtn.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>';
				
				var plusBtn = document.createElement('button');
				plusBtn.type = 'button';
				plusBtn.className = 'h-12 w-12 grid place-items-center hover:bg-ink hover:text-cream transition';
				plusBtn.setAttribute('data-qty-plus', '');
				plusBtn.setAttribute('aria-label', 'Increase quantity');
				plusBtn.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
				
				qtyInput.className = 'w-10 text-center text-sm font-medium bg-transparent outline-none luxora-qty-input';
				qtyInput.setAttribute('data-qty-input', '');
				
				qtyInput.parentNode.insertBefore(minusBtn, qtyInput);
				qtyInput.parentNode.insertBefore(plusBtn, qtyInput.nextSibling);
			}
		}

		// On page load, mark the initially selected swatch as active.
		qsa('.luxora-swatch-wrap', form).forEach(function (group) {
			var selectId = group.getAttribute('data-select-id');
			var select = document.getElementById(selectId);
			if (select && select.value) {
				var matchSwatch = group.querySelector('[data-value="' + select.value + '"]');
				if (matchSwatch) {
					matchSwatch.classList.add('is-active');
					matchSwatch.setAttribute('aria-pressed', 'true');
				}
			}
		});

		// Listen to jQuery show_variation and reset_data events
		if (window.jQuery) {
			var $form = window.jQuery(form);
			var mainImg = document.querySelector('[data-gallery-main]');
			var originalImgSrc = mainImg ? mainImg.getAttribute('src') : '';
			var priceContainer = document.querySelector('.luxora-price');
			var originalPriceHtml = priceContainer ? priceContainer.innerHTML : '';

			$form.on('show_variation', function (event, variation) {
				// 1. Update main price display below the title
				if (priceContainer && variation.price_html) {
					priceContainer.innerHTML = variation.price_html;
				}
				
				// 2. Update main gallery image to variation image
				if (mainImg && variation.image && variation.image.src) {
					mainImg.setAttribute('src', variation.image.src);
					mainImg.setAttribute('srcset', '');
					if (variation.image.alt) {
						mainImg.setAttribute('alt', variation.image.alt);
					}
					
					// Highlight corresponding thumbnail if one matches
					var thumbs = qsa('[data-gallery-thumb]');
					var matchedThumb = false;
					thumbs.forEach(function (thumb) {
						var fullUrl = thumb.getAttribute('data-full');
						if (fullUrl && (fullUrl.indexOf(variation.image.src) !== -1 || variation.image.src.indexOf(fullUrl) !== -1)) {
							thumbs.forEach(function (t) {
								t.classList.remove('ring-1', 'ring-ink');
								t.classList.add('opacity-60', 'hover:opacity-100');
							});
							thumb.classList.add('ring-1', 'ring-ink');
							thumb.classList.remove('opacity-60', 'hover:opacity-100');
							matchedThumb = true;
						}
					});
					if (!matchedThumb) {
						thumbs.forEach(function (t) {
							t.classList.remove('ring-1', 'ring-ink');
							t.classList.add('opacity-60', 'hover:opacity-100');
						});
					}
				}
			});

			$form.on('reset_data', function () {
				// 1. Reset main price
				if (priceContainer && originalPriceHtml) {
					priceContainer.innerHTML = originalPriceHtml;
				}
				
				// 2. Reset main gallery image
				if (mainImg && originalImgSrc) {
					mainImg.setAttribute('src', originalImgSrc);
					mainImg.setAttribute('srcset', '');
					
					var thumbs = qsa('[data-gallery-thumb]');
					thumbs.forEach(function (t, idx) {
						if (idx === 0) {
							t.classList.add('ring-1', 'ring-ink');
							t.classList.remove('opacity-60', 'hover:opacity-100');
						} else {
							t.classList.remove('ring-1', 'ring-ink');
							t.classList.add('opacity-60', 'hover:opacity-100');
						}
					});
				}
			});
		}

		document.addEventListener('click', function (e) {
			var swatch = e.target.closest('.luxora-swatch-color, .luxora-swatch-text');
			if (!swatch) { return; }
			e.preventDefault();

			var group  = swatch.closest('[data-swatch-group]');
			if (!group) { return; }

			var selectId = group.getAttribute('data-select-id');
			var select   = document.getElementById(selectId);
			if (!select) { return; }

			var value = swatch.getAttribute('data-value');
			var label = swatch.getAttribute('data-label') || value;

			// Deselect siblings, select this swatch.
			qsa('.luxora-swatch-color, .luxora-swatch-text', group).forEach(function (s) {
				s.classList.remove('is-active');
				s.setAttribute('aria-pressed', 'false');
			});
			swatch.classList.add('is-active');
			swatch.setAttribute('aria-pressed', 'true');

			// Update the hidden select value.
			select.value = value;

			// Update the visible "COLOR — Camel" label span rendered by PHP.
			var labelEl = document.querySelector('[data-swatch-label="' + group.getAttribute('data-swatch-group') + '"]');
			if (labelEl) { labelEl.textContent = label; }

			// Dispatch native change so WooCommerce variation.js responds.
			var evt = document.createEvent('Event');
			evt.initEvent('change', true, true);
			select.dispatchEvent(evt);

			// Also trigger jQuery change if jQuery is available.
			if (window.jQuery) {
				window.jQuery(select).trigger('change');
			}
		});

		// When WooCommerce resets variations (e.g. via .reset_variations),
		// clear our swatch active states and reset the label spans.
		form.addEventListener('click', function (e) {
			if (!e.target.classList.contains('reset_variations') && !e.target.closest('.reset_variations')) { return; }
			qsa('.luxora-swatch-color, .luxora-swatch-text', form).forEach(function (s) {
				s.classList.remove('is-active');
				s.setAttribute('aria-pressed', 'false');
			});
			// Reset all label spans to 'Choose an option'.
			qsa('[data-swatch-label]', form).forEach(function (labelEl) {
				labelEl.textContent = 'Choose an option';
			});
		});

		// Reflect WooCommerce's programmatic select changes back to swatches.
		qsa('.luxora-swatch-select', form).forEach(function (select) {
			select.addEventListener('change', function () {
				var group = document.querySelector('[data-select-id="' + select.id + '"]');
				if (!group) { return; }
				var val = select.value;
				var labelVal = 'Choose an option';
				qsa('.luxora-swatch-color, .luxora-swatch-text', group).forEach(function (s) {
					var match = s.getAttribute('data-value') === val;
					s.classList.toggle('is-active', match);
					s.setAttribute('aria-pressed', match ? 'true' : 'false');
					if (match) {
						labelVal = s.getAttribute('data-label') || s.getAttribute('data-value');
					}
				});
				var labelEl = document.querySelector('[data-swatch-label="' + group.getAttribute('data-swatch-group') + '"]');
				if (labelEl) {
					labelEl.textContent = labelVal;
				}
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Auth tabs (login / register)
	 * ------------------------------------------------------------------- */
	function initAuthTabs() {
		var tabBtns = qsa('.luxora-auth-tab');
		if (!tabBtns.length) { return; }

		var indicator = qs('.luxora-auth-tab-indicator');

		function activateTab(btn) {
			var panelId = btn.getAttribute('aria-controls');
			var panel   = document.getElementById(panelId);

			tabBtns.forEach(function (b) {
				b.classList.remove('is-active');
				b.setAttribute('aria-selected', 'false');
				var p = document.getElementById(b.getAttribute('aria-controls'));
				if (p) {
					p.classList.remove('is-active');
					p.setAttribute('hidden', '');
				}
			});

			btn.classList.add('is-active');
			btn.setAttribute('aria-selected', 'true');
			if (panel) {
				panel.classList.add('is-active');
				panel.removeAttribute('hidden');
			}

			// Slide the indicator.
			if (indicator) {
				var idx = tabBtns.indexOf(btn);
				indicator.style.transform = 'translateX(' + (idx * 100) + '%)';
			}
		}

		tabBtns.forEach(function (btn) {
			btn.addEventListener('click', function () { activateTab(btn); });
		});

		// If WooCommerce redirects back with a registration error, open the register tab.
		if (window.location.hash === '#register' || (document.querySelector('.woocommerce-error') && document.querySelector('#panel-register'))) {
			var regBtn = document.getElementById('tab-register');
			if (regBtn) { activateTab(regBtn); }
		}

		// Position indicator on load.
		var activeBtn = qs('.luxora-auth-tab.is-active');
		if (indicator && activeBtn) {
			var idx = tabBtns.indexOf(activeBtn);
			indicator.style.transform = 'translateX(' + (idx * 100) + '%)';
		}
	}

	/* ---------------------------------------------------------------------
	 * Init
	 * ------------------------------------------------------------------- */
	function ready(fn) {
		if (document.readyState !== 'loading') { fn(); }
		else { document.addEventListener('DOMContentLoaded', fn); }
	}

	// Flag JS so GSAP hidden states only apply when JS runs (avoids FOUC / invisible content on no-JS).
	document.documentElement.classList.add('luxe-anim');

	ready(function () {
		initHeader();

		var drawer   = initToggle('[data-drawer-open]', '[data-drawer-close]', '[data-drawer]', { toggle: false });
		var search   = initToggle('[data-search-open]', '[data-search-close]', '[data-search]', { toggle: false, focus: '[data-search-input]' });
		var miniCart = initToggle('[data-cart-toggle]', '[data-minicart-close]', '[data-minicart]', { toggle: true });

		initReveals();
		initAddToCart(miniCart);
		initWishlist();
		initQtySteppers();
		initCartPage();
		initNewsletter();
		initGallery();
		initColorPicker();
		initShop();
		initCoupon();
		initContact();
		initCheckoutWizard();
		initVariationSwatches();
		initAuthTabs();
	});
})();

