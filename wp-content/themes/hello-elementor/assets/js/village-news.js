(function () {
	"use strict";

	const page = document.querySelector(".village-news");
	if (!page) {
		return;
	}

	const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

	function setupHeader() {
		function updateHeader() {
			document.body.classList.toggle("village-news-header-scrolled", window.scrollY > 48);
		}

		window.addEventListener("scroll", updateHeader, { passive: true });
		updateHeader();
	}

	function setupReveal() {
		const sections = Array.from(page.querySelectorAll("[data-news-section]"));
		if (!sections.length || reducedMotion || !("IntersectionObserver" in window)) {
			sections.forEach((section) => section.classList.add("is-visible"));
			return;
		}

		sections.forEach((section) => {
			if (section.getBoundingClientRect().top < window.innerHeight * 1.05) {
				section.classList.add("is-visible");
			}
		});
		document.documentElement.classList.add("news-motion-ready");

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

	setupHeader();
	setupReveal();
})();
