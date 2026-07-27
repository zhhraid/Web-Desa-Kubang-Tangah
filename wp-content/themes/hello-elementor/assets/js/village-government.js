(function () {
	"use strict";

	const page = document.querySelector(".village-government");

	if (!page) {
		return;
	}

	document.body.classList.add("village-government-page");

	const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	const numberFormatter = new Intl.NumberFormat("id-ID");

	function setupHeaderVisibility() {
		function update() {
			document.body.classList.toggle("village-government-header-hidden", window.scrollY > 80);
		}

		update();
		window.addEventListener("scroll", update, { passive: true });
	}

	function setupRevealAnimations() {
		const sections = Array.from(page.querySelectorAll("[data-government-section]"));

		if (!sections.length || reducedMotion || !("IntersectionObserver" in window)) {
			sections.forEach((section) => section.classList.add("is-visible"));
			return;
		}

		sections.forEach((section) => {
			if (!section.hidden && section.getBoundingClientRect().top < window.innerHeight * 1.08) {
				section.classList.add("is-visible");
			}
		});

		document.documentElement.classList.add("government-motion-ready");

		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (!entry.isIntersecting) {
						return;
					}

					entry.target.classList.add("is-visible");
					observer.unobserve(entry.target);
				});
			},
			{ rootMargin: "0px 0px -10%", threshold: 0.08 }
		);

		sections.forEach((section) => {
			if (!section.classList.contains("is-visible")) {
				observer.observe(section);
			}
		});
	}

	function setupCounters() {
		const counters = Array.from(page.querySelectorAll("[data-government-count]"));

		function setFinalValue(counter) {
			const target = Number(counter.dataset.governmentCount || 0);
			counter.textContent = numberFormatter.format(target);
		}

		if (reducedMotion || !("IntersectionObserver" in window)) {
			counters.forEach(setFinalValue);
			return;
		}

		function animate(counter) {
			if (counter.dataset.governmentCounted === "true") {
				return;
			}

			counter.dataset.governmentCounted = "true";
			const target = Number(counter.dataset.governmentCount || 0);
			const duration = 850;
			const startedAt = performance.now();
			counter.textContent = "0";

			function frame(now) {
				const progress = Math.min((now - startedAt) / duration, 1);
				const eased = 1 - Math.pow(1 - progress, 3);
				counter.textContent = numberFormatter.format(Math.round(target * eased));

				if (progress < 1) {
					window.requestAnimationFrame(frame);
				}
			}

			window.requestAnimationFrame(frame);
		}

		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						animate(entry.target);
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.45 }
		);

		counters.forEach((counter) => observer.observe(counter));
	}

	function setupJumpNavigation() {
		const links = Array.from(page.querySelectorAll("[data-government-tab][href^='#']"));
		const sections = Array.from(page.querySelectorAll("[data-government-section]"));
		const sectionIds = sections.map((section) => section.id).filter(Boolean);
		const tabs = page.querySelector("[data-government-tabs]");

		if (!links.length || !sections.length) {
			return;
		}

		function activate(targetId, options = {}) {
			const target = sections.find((section) => section.id === targetId) || sections[0];

			sections.forEach((section) => {
				const isActive = section === target;
				section.hidden = !isActive;
				section.classList.toggle("is-active", isActive);
				if (isActive) {
					section.classList.add("is-visible");
				}
			});

			links.forEach((link) => {
				const isActive = link.getAttribute("href") === `#${target.id}`;
				link.classList.toggle("is-active", isActive);
				link.setAttribute("aria-selected", String(isActive));
			});

			if (options.scroll && tabs) {
				tabs.scrollIntoView({ behavior: reducedMotion ? "auto" : "smooth", block: "start" });
			}

			if (options.hash) {
				window.history.replaceState(null, "", `#${target.id}`);
			}

			window.setTimeout(() => {
				window.dispatchEvent(new Event("resize"));
			}, 80);
		}

		links.forEach((link) => {
			link.addEventListener("click", (event) => {
				event.preventDefault();
				const targetId = link.getAttribute("href").replace("#", "");
				if (targetId) {
					activate(targetId, { hash: true, scroll: true });
				}
			});
		});

		const initialId = sectionIds.includes(window.location.hash.replace("#", "")) ? window.location.hash.replace("#", "") : sectionIds[0];
		activate(initialId);
	}

	function setupOrganizationCarousel() {
		const track = page.querySelector("[data-organization-track]");
		const previous = page.querySelector("[data-organization-previous]");
		const next = page.querySelector("[data-organization-next]");

		if (!track || !previous || !next) {
			return;
		}

		function updateControls() {
			const remaining = track.scrollWidth - track.clientWidth - track.scrollLeft;
			previous.disabled = track.scrollLeft <= 3;
			next.disabled = remaining <= 3;
		}

		function move(direction) {
			track.scrollBy({
				left: direction * Math.max(track.clientWidth * 0.78, 280),
				behavior: reducedMotion ? "auto" : "smooth",
			});
		}

		previous.addEventListener("click", () => move(-1));
		next.addEventListener("click", () => move(1));
		track.addEventListener("scroll", updateControls, { passive: true });
		window.addEventListener("resize", updateControls, { passive: true });
		updateControls();
	}

	function setupRegulationFilters() {
		const buttons = Array.from(page.querySelectorAll("[data-regulation-filter]"));
		const cards = Array.from(page.querySelectorAll("[data-regulation-card]"));
		const result = page.querySelector("[data-regulation-result]");

		if (!buttons.length || !cards.length) {
			return;
		}

		buttons.forEach((button) => {
			button.addEventListener("click", () => {
				const filter = button.dataset.regulationFilter;
				let visibleCount = 0;

				buttons.forEach((item) => {
					const isActive = item === button;
					item.classList.toggle("is-active", isActive);
					item.setAttribute("aria-pressed", String(isActive));
				});

				cards.forEach((card) => {
					const shouldShow = filter === "all" || card.dataset.regulationCard === filter;
					card.hidden = !shouldShow;
					if (shouldShow) {
						visibleCount += 1;
					}
				});

				if (result) {
					result.textContent = `${visibleCount} dokumen ditampilkan`;
				}
			});
		});
	}

	function setupBudgetFilters() {
		const filters = Array.from(page.querySelectorAll("[data-budget-filter]"));

		filters.forEach((filter) => {
			filter.addEventListener("change", () => {
				const group = filter.dataset.budgetFilter;
				const year = filter.value;
				const items = Array.from(page.querySelectorAll(`[data-budget-item="${group}"]`));

				items.forEach((item) => {
					item.hidden = year !== "all" && item.dataset.budgetYear !== year;
				});
			});
		});
	}

	function setupPreviewDialog() {
		const dialog = page.querySelector("[data-government-dialog]");
		const previewLinks = Array.from(page.querySelectorAll("[data-government-preview]"));

		if (!dialog || typeof dialog.showModal !== "function" || !previewLinks.length) {
			return;
		}

		const image = dialog.querySelector("[data-government-dialog-image]");
		const title = dialog.querySelector("[data-government-dialog-title]");
		const openOriginal = dialog.querySelector("[data-government-dialog-open]");
		const footerText = dialog.querySelector("footer p");
		const closeButton = dialog.querySelector("[data-government-dialog-close]");

		function closeDialog() {
			if (dialog.open) {
				dialog.close();
			}
		}

		previewLinks.forEach((link) => {
			link.addEventListener("click", (event) => {
				event.preventDefault();
				const imageUrl = link.href;
				const previewTitle = link.dataset.previewTitle || "Dokumen Pemerintahan Desa";

				image.src = imageUrl;
				image.alt = previewTitle;
				image.hidden = false;
				title.textContent = previewTitle;
				openOriginal.href = imageUrl;
				openOriginal.hidden = false;
				if (footerText) {
					footerText.hidden = false;
				}
				dialog.showModal();
				document.documentElement.classList.add("government-dialog-open");
			});
		});

		closeButton.addEventListener("click", closeDialog);
		dialog.addEventListener("close", () => {
			document.documentElement.classList.remove("government-dialog-open");
			image.removeAttribute("src");
		});

		dialog.addEventListener("click", (event) => {
			if (event.target !== dialog) {
				return;
			}

			const bounds = dialog.getBoundingClientRect();
			const inside =
				event.clientX >= bounds.left &&
				event.clientX <= bounds.right &&
				event.clientY >= bounds.top &&
				event.clientY <= bounds.bottom;

			if (!inside) {
				closeDialog();
			}
		});
	}

	setupHeaderVisibility();
	setupJumpNavigation();
	setupRevealAnimations();
	setupCounters();
	setupOrganizationCarousel();
	setupRegulationFilters();
	setupBudgetFilters();
	setupPreviewDialog();
})();
