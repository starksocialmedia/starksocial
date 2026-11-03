/*!
 * stark-global.js
 * Season detection, scroll progress, velocity glow,
 * A11y button + back-to-top choreography.
 *
 * Phase 2: paste the live-site JS from WPCode "Stark Global JS" here.
 * Keep the IIFE wrapper and reduced-motion guard. The scroll progress bar
 * drives --stark-scroll (0..1); velocity drives --stark-vel.
 */
(function () {
	'use strict';

	var root   = document.documentElement;
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* -----------------------------------------------------------------
	 * 1. Season — already set in <head> inline script, kept here as a
	 *    defensive re-apply for dynamic theme switchers.
	 * ----------------------------------------------------------------- */
	if (!root.getAttribute('data-stark-season')) {
		var m = new Date().getMonth();
		var s = 'winter';
		if (m >= 2 && m <= 4) s = 'spring';
		else if (m >= 5 && m <= 7) s = 'summer';
		else if (m >= 8 && m <= 10) s = 'fall';
		root.setAttribute('data-stark-season', s);
	}

	/* -----------------------------------------------------------------
	 * 2–4. Scroll progress, velocity glow, A11y / back-to-top choreography
	 * -----------------------------------------------------------------
	 * Not ported from Phase 1 (v27.3.18 review):
	 * - Scroll progress and the A11y + back-to-top cluster live in
	 *   js/stark-bottom-ui.js (writes --stark-progress).
	 * - The velocity glow (--stark-vel) styled Phase 1's progress bar; no
	 *   Phase 2 CSS reads --stark-vel or --stark-scroll, so porting it would be
	 *   dead code. Live's implementation is in site-content/global/global-js.js.
	 */

	if (reduce) {
		root.style.setProperty('--stark-vel', '0');
	}
})();
