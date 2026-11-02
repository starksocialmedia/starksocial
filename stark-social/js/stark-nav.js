/*!
 * stark-nav.js
 * Navigation state, mega menu, mobile accordion, scroll progress.
 */
(function () {
	'use strict';

	var root = document.documentElement;
	var SCROLL_SCROLLED_AT = 20;
	var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var isTouch = window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)').matches;

	var nav, burger, mobileNav, scrim;
	var openMegaItem = null;
	var mobileNavOpen = false;

	function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

	function resolveElements() {
		nav       = nav       || document.querySelector('.stark-nav');
		burger    = burger    || document.querySelector('.stark-nav__burger');
		mobileNav = mobileNav || document.getElementById('stark-mobile-nav');
		scrim     = scrim     || document.querySelector('.stark-nav__scrim');
	}

	/* -------------------------------------------------------------------
	 * Scroll state + progress bar
	 * ------------------------------------------------------------------- */
	function updateScroll() {
		var doc    = document.documentElement;
		var body   = document.body;
		var top    = doc.scrollTop || (body ? body.scrollTop : 0) || 0;
		var height = ((doc.scrollHeight || (body ? body.scrollHeight : 0)) - doc.clientHeight) || 0;
		var p      = height > 0 ? clamp(top / height, 0, 1) : 0;
		root.style.setProperty('--stark-progress', p.toFixed(4));

		if (top > SCROLL_SCROLLED_AT) {
			root.setAttribute('data-stark-scrolled', 'true');
		} else {
			root.setAttribute('data-stark-scrolled', 'false');
		}
	}

	/* -------------------------------------------------------------------
	 * Mega menu (desktop)
	 * ------------------------------------------------------------------- */
	function closeMega() {
		if (openMegaItem) {
			openMegaItem.classList.remove('is-open');
			var link = openMegaItem.querySelector('.stark-nav__link--has-mega');
			if (link) link.setAttribute('aria-expanded', 'false');
			openMegaItem = null;
			root.removeAttribute('data-stark-mega-open');
		}
	}

	function openMega(item) {
		if (openMegaItem === item) return;
		closeMega();
		item.classList.add('is-open');
		var link = item.querySelector('.stark-nav__link--has-mega');
		if (link) link.setAttribute('aria-expanded', 'true');
		openMegaItem = item;
		root.setAttribute('data-stark-mega-open', 'true');
	}

	function initMega() {
		var items = document.querySelectorAll('.stark-nav__item--has-mega');
		for (var i = 0; i < items.length; i++) {
			(function (item) {
				var link = item.querySelector('.stark-nav__link--has-mega');

				if (!isTouch) {
					// Desktop: hover opens, click navigates
					item.addEventListener('mouseenter', function () { openMega(item); });
					item.addEventListener('mouseleave', function () { closeMega(); });
					if (link) {
						link.addEventListener('focus', function () { openMega(item); });
					}
				} else {
					// Touch: click toggles
					if (link) {
						link.addEventListener('click', function (e) {
							if (window.innerWidth > 900) {
								if (openMegaItem === item) {
									closeMega();
								} else {
									e.preventDefault();
									openMega(item);
								}
							}
						});
					}
				}
			})(items[i]);
		}

		// Click outside closes
		document.addEventListener('click', function (e) {
			if (!openMegaItem) return;
			if (!openMegaItem.contains(e.target)) closeMega();
		});

		// Escape closes
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				if (openMegaItem) closeMega();
				if (mobileNavOpen) closeMobileNav();
			}
		});
	}

	/* -------------------------------------------------------------------
	 * Mobile nav (overlay + accordion)
	 * ------------------------------------------------------------------- */
	function openMobileNav() {
		resolveElements();
		if (!mobileNav || mobileNavOpen) return;
		mobileNavOpen = true;
		root.setAttribute('data-stark-mobile-open', 'true');
		mobileNav.setAttribute('aria-hidden', 'false');
		if (burger) burger.setAttribute('aria-expanded', 'true');
		document.body.style.overflow = 'hidden';
	}

	function closeMobileNav() {
		resolveElements();
		if (!mobileNav || !mobileNavOpen) return;
		mobileNavOpen = false;
		root.setAttribute('data-stark-mobile-open', 'false');
		mobileNav.setAttribute('aria-hidden', 'true');
		if (burger) burger.setAttribute('aria-expanded', 'false');
		document.body.style.overflow = '';

		// Collapse any open accordions
		var accordions = mobileNav.querySelectorAll('.menu-item-has-children.is-expanded');
		for (var i = 0; i < accordions.length; i++) {
			accordions[i].classList.remove('is-expanded');
		}
	}

	function initMobileNav() {
		resolveElements();
		if (!burger || !mobileNav) return;

		burger.addEventListener('click', function () {
			if (mobileNavOpen) closeMobileNav(); else openMobileNav();
		});

		if (scrim) scrim.addEventListener('click', closeMobileNav);

		// Services accordion: tap the parent link to expand inline (don't navigate on first tap)
		var accordionLinks = mobileNav.querySelectorAll('.menu-item-has-children > a');
		for (var i = 0; i < accordionLinks.length; i++) {
			(function (link) {
				link.addEventListener('click', function (e) {
					var parent = link.parentElement;
					if (!parent.classList.contains('is-expanded')) {
						e.preventDefault();
						// Close other open accordions
						var others = mobileNav.querySelectorAll('.menu-item-has-children.is-expanded');
						for (var j = 0; j < others.length; j++) others[j].classList.remove('is-expanded');
						parent.classList.add('is-expanded');
					}
				});
			})(accordionLinks[i]);
		}

		// Close mobile nav when any leaf link is clicked
		var leafLinks = mobileNav.querySelectorAll('a');
		for (var k = 0; k < leafLinks.length; k++) {
			var leaf = leafLinks[k];
			if (!leaf.parentElement.classList.contains('menu-item-has-children')) {
				leaf.addEventListener('click', function () {
					setTimeout(closeMobileNav, 50);
				});
			}
		}

		// If viewport grows past mobile breakpoint, close any open mobile nav
		window.addEventListener('resize', function () {
			if (window.innerWidth > 900 && mobileNavOpen) closeMobileNav();
		});
	}

	/* -------------------------------------------------------------------
	 * Init
	 * ------------------------------------------------------------------- */
	function init() {
		resolveElements();
		updateScroll();
		window.addEventListener('scroll', updateScroll, { passive: true });
		window.addEventListener('resize', updateScroll);

		initMega();
		initMobileNav();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
