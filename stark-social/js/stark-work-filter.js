/**
 * /work/ filters — industry + service chips (v27.0.12).
 * Cards carry data-industry="slug" and data-service="slug slug".
 * Both filters combine (AND). Without JS all cards stay visible.
 */
(function () {
	var root = document.querySelector('[data-stark-work-filters]');
	if (!root) return;

	var cards = Array.prototype.slice.call(document.querySelectorAll('.stark-work-card'));
	var empty = document.querySelector('[data-stark-work-empty]');
	var count = root.querySelector('[data-stark-work-count]');
	var state = { industry: 'all', service: 'all' };

	function matches(card) {
		var industryOk = state.industry === 'all' || card.dataset.industry === state.industry;
		var serviceOk = state.service === 'all' || (' ' + card.dataset.service + ' ').indexOf(' ' + state.service + ' ') !== -1;
		return industryOk && serviceOk;
	}

	function render() {
		var shown = 0;
		cards.forEach(function (card) {
			var show = matches(card);
			card.hidden = !show;
			if (show) shown++;
		});
		root.querySelectorAll('[data-filter]').forEach(function (btn) {
			btn.setAttribute('aria-pressed', String(state[btn.dataset.filter] === btn.dataset.value));
		});
		var grid = document.querySelector('.stark-work-grid');
		if (grid) grid.classList.toggle('is-filtered', state.industry !== 'all' || state.service !== 'all');
		if (empty) empty.hidden = shown !== 0;
		if (count) count.textContent = shown + (shown === 1 ? ' case study' : ' case studies') + ' shown';
	}

	root.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-filter]');
		if (!btn) return;
		state[btn.dataset.filter] = btn.dataset.value;
		render();
	});

	var reset = document.querySelector('[data-stark-work-reset]');
	if (reset) {
		reset.addEventListener('click', function () {
			state.industry = 'all';
			state.service = 'all';
			render();
		});
	}
})();
