(function () {
	"use strict";

	const root = document.querySelector("[data-gallery-root]");
	if (!root) {
		return;
	}

	const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	const formatter = new Intl.NumberFormat("id-ID");

	function setupHeader() {
		const updateHeader = () => {
			document.body.classList.toggle("village-gallery-header-scrolled", window.scrollY > 48);
		};

		window.addEventListener("scroll", updateHeader, { passive: true });
		updateHeader();
	}

	function setupYearFilter() {
		const select = root.querySelector("[data-gallery-year-select]");
		const sections = Array.from(root.querySelectorAll("[data-gallery-year-section]"));
		if (!select || !sections.length) {
			return;
		}

		const update = () => {
			const selectedYear = select.value;
			sections.forEach((section) => {
				const isVisible = !selectedYear || section.dataset.galleryYearSection === selectedYear;
				section.hidden = !isVisible;
				if (isVisible) {
					section.classList.add("is-visible");
				}
			});

			root.classList.toggle("is-filtered", Boolean(selectedYear));
		};

		select.addEventListener("change", update);
		update();
	}

	function setupReveal() {
		const sections = Array.from(root.querySelectorAll("[data-gallery-year-section]"));
		if (!sections.length || reducedMotion || !("IntersectionObserver" in window)) {
			sections.forEach((section) => section.classList.add("is-visible"));
			return;
		}

		document.documentElement.classList.add("gallery-motion-ready");
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
			{ rootMargin: "0px 0px -8%", threshold: 0.08 }
		);

		sections.forEach((section) => observer.observe(section));
	}

	function setupCarousels() {
		const sections = Array.from(root.querySelectorAll("[data-gallery-year-section]"));
		sections.forEach((section, sectionIndex) => {
			const track = section.querySelector("[data-gallery-track]");
			const previous = section.querySelector("[data-gallery-prev]");
			const next = section.querySelector("[data-gallery-next]");
			if (!track) {
				return;
			}

			let inView = false;
			let paused = false;
			let manualPauseUntil = 0;
			let dragActive = false;
			let dragMoved = false;
			let preventPhotoClick = false;
			let dragPointerId = null;
			let dragStartX = 0;
			let dragStartScroll = 0;

			const cardStep = () => {
				const card = track.querySelector("[data-gallery-open]");
				if (!card) {
					return track.clientWidth * 0.75;
				}
				const styles = window.getComputedStyle(track);
				return card.getBoundingClientRect().width + Number.parseFloat(styles.columnGap || styles.gap || 0);
			};

			const move = (direction, manual) => {
				if (manual) {
					manualPauseUntil = Date.now() + 9000;
				}
				const step = cardStep();
				const atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - step * 0.35;
				const atStart = track.scrollLeft <= step * 0.2;

				if (direction > 0 && atEnd) {
					track.scrollTo({ left: 0, behavior: reducedMotion ? "auto" : "smooth" });
					return;
				}

				if (direction < 0 && atStart) {
					track.scrollTo({ left: track.scrollWidth, behavior: reducedMotion ? "auto" : "smooth" });
					return;
				}

				track.scrollBy({ left: step * direction, behavior: reducedMotion ? "auto" : "smooth" });
			};

			previous?.addEventListener("click", () => move(-1, true));
			next?.addEventListener("click", () => move(1, true));
			track.addEventListener("mouseenter", () => {
				paused = true;
			});
			track.addEventListener("mouseleave", () => {
				paused = false;
			});
			track.addEventListener("focusin", () => {
				paused = true;
			});
			track.addEventListener("focusout", () => {
				paused = false;
			});
			track.addEventListener("pointerdown", (event) => {
				manualPauseUntil = Date.now() + 9000;
				if (event.pointerType === "touch" || event.button !== 0) {
					return;
				}

				dragActive = true;
				dragMoved = false;
				dragPointerId = event.pointerId;
				dragStartX = event.clientX;
				dragStartScroll = track.scrollLeft;
				track.classList.add("is-dragging");
				track.setPointerCapture(event.pointerId);
			});

			track.addEventListener("pointermove", (event) => {
				if (!dragActive || event.pointerId !== dragPointerId) {
					return;
				}

				const distance = event.clientX - dragStartX;
				if (Math.abs(distance) > 5) {
					dragMoved = true;
				}
				if (!dragMoved) {
					return;
				}

				track.scrollLeft = dragStartScroll - distance;
				event.preventDefault();
			});

			const finishDrag = (event) => {
				if (!dragActive || event.pointerId !== dragPointerId) {
					return;
				}

				preventPhotoClick = dragMoved;
				dragActive = false;
				dragPointerId = null;
				track.classList.remove("is-dragging");
				if (track.hasPointerCapture(event.pointerId)) {
					track.releasePointerCapture(event.pointerId);
				}
			};

			track.addEventListener("pointerup", finishDrag);
			track.addEventListener("pointercancel", finishDrag);
			track.addEventListener(
				"click",
				(event) => {
					if (!preventPhotoClick) {
						return;
					}
					event.preventDefault();
					event.stopPropagation();
					preventPhotoClick = false;
				},
				true
			);

			if ("IntersectionObserver" in window) {
				const observer = new IntersectionObserver(
					(entries) => {
						inView = entries.some((entry) => entry.isIntersecting);
					},
					{ threshold: 0.35 }
				);
				observer.observe(section);
			} else {
				inView = true;
			}

			if (!reducedMotion) {
				window.setInterval(
					() => {
						if (!section.hidden && inView && !paused && !document.hidden && Date.now() > manualPauseUntil) {
							move(1, false);
						}
					},
					5200 + sectionIndex * 350
				);
			}
		});
	}

	function setupLightbox() {
		const dialog = document.querySelector("[data-gallery-lightbox]");
		if (!dialog) {
			return;
		}

		const image = dialog.querySelector("[data-lightbox-image]");
		const yearLabel = dialog.querySelector("[data-lightbox-year]");
		const counter = dialog.querySelector("[data-lightbox-counter]");
		const previous = dialog.querySelector("[data-lightbox-prev]");
		const next = dialog.querySelector("[data-lightbox-next]");
		const closeButtons = Array.from(dialog.querySelectorAll("[data-gallery-close]"));
		let activePhotos = [];
		let activeIndex = 0;

		const render = () => {
			const photo = activePhotos[activeIndex];
			if (!photo || !image) {
				return;
			}
			image.src = photo.dataset.galleryFull || "";
			image.alt = photo.dataset.galleryAlt || "Foto galeri Desa Kubang Tangah";
			if (yearLabel) {
				yearLabel.textContent = `Tahun ${photo.dataset.galleryYear || ""}`;
			}
			if (counter) {
				counter.textContent = `${activeIndex + 1} dari ${activePhotos.length}`;
			}
		};

		const open = (photo) => {
			const year = photo.dataset.galleryYear;
			activePhotos = Array.from(
				root.querySelectorAll(`[data-gallery-open][data-gallery-year="${year}"]`)
			);
			activeIndex = Math.max(0, activePhotos.indexOf(photo));
			render();
			document.body.classList.add("village-gallery-lightbox-open");
			if (typeof dialog.showModal === "function") {
				dialog.showModal();
			} else {
				dialog.setAttribute("open", "");
			}
		};

		const close = () => {
			document.body.classList.remove("village-gallery-lightbox-open");
			if (dialog.open && typeof dialog.close === "function") {
				dialog.close();
			} else {
				dialog.removeAttribute("open");
			}
			if (image) {
				image.src = "";
			}
		};

		const move = (direction) => {
			if (!activePhotos.length) {
				return;
			}
			activeIndex = (activeIndex + direction + activePhotos.length) % activePhotos.length;
			render();
		};

		root.addEventListener("click", (event) => {
			const photo = event.target.closest("[data-gallery-open]");
			if (photo) {
				open(photo);
			}
		});

		closeButtons.forEach((button) => button.addEventListener("click", close));
		previous?.addEventListener("click", () => move(-1));
		next?.addEventListener("click", () => move(1));
		dialog.addEventListener("cancel", () => {
			document.body.classList.remove("village-gallery-lightbox-open");
		});
		dialog.addEventListener("keydown", (event) => {
			if (event.key === "ArrowLeft") {
				move(-1);
			}
			if (event.key === "ArrowRight") {
				move(1);
			}
		});
	}

	setupHeader();
	setupYearFilter();
	setupReveal();
	setupCarousels();
	setupLightbox();
})();
