document.addEventListener('DOMContentLoaded', () => {
	const root = document.documentElement;
	const desktop = window.matchMedia('(min-width: 1025px)');
	const coarse = window.matchMedia('(pointer: coarse)');
	const TRIGGER_RATIO = 0.3; // box ke top se kitni door (30%) item "active" mana jaye
	const END_SPACE = 50;      // aakhri item ke neeche itni khali jagah (px)
	const MIN_RANGE = 80;      // text kam ho tab bhi itna scroll-room, taake "end" ek alag state ho
	const HOLD_MS = 250;       // start/end pe pahunchne ke baad itni der lock, taake trackpad inertia page na ura de
	const MAX_JUMP = 300;      // is se bada jump = anchor/Home-End/focus jump, lock mat lagao
	const sections = [];
	let active = null;         // jis section ne abhi page lock kiya hua hai

	const canLock = () => desktop.matches && !coarse.matches;

	// Lock ke dauran header chhupa rehne ke liye (theme scrolldown/scrollup classes use karti hai)
	const markScrollDown = () => {
		document.body.classList.add('scrolldown');
		document.body.classList.remove('scrollup');
	};

	document.querySelectorAll('.section_scroll_text_box').forEach((section) => {
		// Gutenberg editor mein scroll-lock nahi chahiye
		if (
			document.body.classList.contains('wp-admin') ||
			section.closest('.block-editor-block-list__layout, .block-editor-iframe__body')
		) return;

		const scroller = section.querySelector('.content_boxes');
		const items = Array.from(section.querySelectorAll('.content_item'));
		const boxImages = Array.from(section.querySelectorAll('.box_img'));

		if (!scroller || !items.length) return;

		const s = {
			section,
			scroller,
			prevOffset: 0,
			lockY: 0,
			holdUntil: 0,
			pinTop: () => parseFloat(getComputedStyle(section).getPropertyValue('--pin-top')) || 0,
		};

		let activeId = null;
		let lastActiveImageId = boxImages.length ? boxImages[0].dataset.imageId : null;
		let ticking = false;

		function setActive(id) {
			if (id === activeId) return;
			activeId = id;

			items.forEach(item => {
				item.classList.toggle('active', item.dataset.contentId === id);
			});

			// Agar is item ki apni image nahi, to pichli image dikhti rehne do
			const hasOwnImage = boxImages.some(img => img.dataset.imageId === id);
			const targetImageId = hasOwnImage ? id : lastActiveImageId;

			if (targetImageId) {
				boxImages.forEach(img => {
					img.classList.toggle('active', img.dataset.imageId === targetImageId);
				});
				lastActiveImageId = targetImageId;
			}
		}

		function update() {
			ticking = false;
			if (!desktop.matches) return;

			const scrollerTop = scroller.getBoundingClientRect().top;
			const line = scroller.clientHeight * TRIGGER_RATIO + 2; // +2 sub-pixel tolerance

			let current = items[0];
			items.forEach(item => {
				if (item.getBoundingClientRect().top - scrollerTop <= line) current = item;
			});

			// Scroll end tak pahunch gaya to aakhri item active (chhote spacer ke sath wo line tak nahi pahunchta)
			const max = scroller.scrollHeight - scroller.clientHeight;
			if (max > 1 && scroller.scrollTop >= max - 1) current = items[items.length - 1];

			setActive(current.dataset.contentId);
		}

		function measure() {
			if (!desktop.matches) {
				scroller.style.removeProperty('--end-space');
				if (active === s) release(s);
				return;
			}
			// Aakhri item ke neeche END_SPACE jagah. Content chhota ho to itna extra ke
			// doosra-aakhri item bhi line tak pahunche aur "end" ek alag state ho.
			const H = scroller.clientHeight;
			const line = H * TRIGGER_RATIO;
			const origin = scroller.getBoundingClientRect().top - scroller.scrollTop;
			const topOf = (el) => el.getBoundingClientRect().top - origin;
			const last = items[items.length - 1];
			const prev = items[items.length - 2] || last;
			const naturalMax = topOf(last) + last.offsetHeight - H;
			const needMax = Math.max(topOf(prev) - line + 4, MIN_RANGE);
			const spacer = items.length < 2 ? 0 : Math.max(END_SPACE, needMax - naturalMax);
			scroller.style.setProperty('--end-space', spacer + 'px');
			update();
		}

		scroller.addEventListener('scroll', () => {
			if (!ticking) {
				ticking = true;
				requestAnimationFrame(update);
			}
		}, { passive: true });

		let resizeTimer;
		window.addEventListener('resize', () => {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(measure, 150);
		});

		desktop.addEventListener('change', measure);
		window.addEventListener('load', measure);
		if (document.fonts && document.fonts.ready) document.fonts.ready.then(measure);

		measure();
		sections.push(s);
	});

	if (!sections.length) return;

	// ---------- Scroll-lock ----------
	const atStart = (s) => s.scroller.scrollTop <= 0;
	const atEnd = (s) => s.scroller.scrollTop >= s.scroller.scrollHeight - s.scroller.clientHeight - 1;
	const offsetOf = (s) => s.section.getBoundingClientRect().top - s.pinTop();

	function resetOffsets() {
		sections.forEach(s => { s.prevOffset = offsetOf(s); });
	}

	// Section stick hua: page ko pin point pe lao aur <html> overflow hidden kar do
	function lock(s, offset) {
		window.scrollTo({ top: window.scrollY + offset, behavior: 'instant' });
		s.lockY = window.scrollY;
		s.holdUntil = 0;
		active = s;
		root.classList.add('scroll-text-locked');
		markScrollDown();
	}

	// Items khatam: overflow wapas normal, page aage chalega
	function release(s) {
		if (active === s) active = null;
		root.classList.remove('scroll-text-locked');
		s.prevOffset = offsetOf(s);
	}

	// Wheel/keyboard ka delta text column ko do. Start/end pe pahunchte hi page chhod do.
	function drive(s, dy, e) {
		const now = performance.now();
		const blocked = (dy > 0 && atEnd(s)) || (dy < 0 && atStart(s));

		if (blocked) {
			if (now < s.holdUntil) { // abhi abhi boundary pe pahunche, inertia ko page tak mat jane do
				e.preventDefault();
				e.stopImmediatePropagation();
				return;
			}
			release(s);
			return;
		}

		e.preventDefault();
		e.stopImmediatePropagation(); // theme ke smooth-scroll scripts ko event mat dikhao
		s.scroller.scrollTop += dy;
		markScrollDown();

		if (atStart(s) || atEnd(s)) s.holdUntil = now + HOLD_MS;
	}

	// Capture phase: theme ke wheel listeners se pehle chalta hai
	window.addEventListener('wheel', (e) => {
		if (!active || !canLock() || e.ctrlKey || !e.deltaY) return; // ctrlKey = pinch zoom

		const unit = e.deltaMode === 1 ? 16 : (e.deltaMode === 2 ? window.innerHeight : 1);
		drive(active, e.deltaY * unit, e);
	}, { passive: false, capture: true });

	// Overflow hidden mein keyboard page scroll nahi karta, to keys se bhi items scroll karo
	window.addEventListener('keydown', (e) => {
		if (!active || !canLock() || e.defaultPrevented || e.ctrlKey || e.metaKey || e.altKey) return;

		const t = e.target instanceof Element ? e.target : null;
		if (t && t.closest('input, textarea, select, [contenteditable="true"]')) return;

		const page = active.scroller.clientHeight * 0.8;
		let dy = 0;

		if (e.key === 'ArrowDown') dy = 60;
		else if (e.key === 'ArrowUp') dy = -60;
		else if (e.key === 'PageDown') dy = page;
		else if (e.key === 'PageUp') dy = -page;
		else if (e.key === ' ') {
			if (t && t.closest('button, a, summary')) return;
			dy = e.shiftKey ? -page : page;
		}
		else if (e.key === 'Home' || e.key === 'End') { release(active); return; }
		else return;

		drive(active, dy, e);
	}, true);

	window.addEventListener('scroll', () => {
		if (!canLock()) { if (active) release(active); return; }

		// Locked: page apni jagah rahe. Chhota drift wapas lao, bada jump (focus/anchor) ho to release.
		if (active) {
			markScrollDown(); // hamari corrective scroll ko theme "scrollup" na samjhe
			const drift = window.scrollY - active.lockY;
			if (Math.abs(drift) > MAX_JUMP) release(active);
			else if (drift) window.scrollTo({ top: active.lockY, behavior: 'instant' });
			return;
		}

		// Pin point cross hua? To lock karo (agar text column us direction mein scroll ho sakta hai)
		for (const s of sections) {
			const offset = offsetOf(s);
			const prev = s.prevOffset;
			s.prevOffset = offset;

			if (Math.abs(offset - prev) > MAX_JUMP) continue; // bada jump, lock mat lagao

			const crossedDown = prev > 0 && offset <= 0;
			const crossedUp = prev < 0 && offset >= 0;

			if ((crossedDown && !atEnd(s)) || (crossedUp && !atStart(s))) {
				lock(s, offset);
				return;
			}
		}
	}, { passive: true });

	window.addEventListener('resize', () => {
		if (active) release(active);
		resetOffsets();
	});
	window.addEventListener('load', resetOffsets);
	resetOffsets();
});