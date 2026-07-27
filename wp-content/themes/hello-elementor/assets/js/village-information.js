(function () {
	"use strict";

	const root = document.querySelector("[data-information-root]");
	if (!root) {
		return;
	}

	const rows = Array.from(root.querySelectorAll("[data-information-row]"));
	const body = root.querySelector("[data-information-body]");
	const search = root.querySelector("[data-information-search]");
	const assistance = root.querySelector("[data-information-assistance]");
	const decile = root.querySelector("[data-information-decile]");
	const reset = root.querySelector("[data-information-reset]");
	const pageSize = root.querySelector("[data-information-page-size]");
	const previous = root.querySelector("[data-information-previous]");
	const next = root.querySelector("[data-information-next]");
	const result = root.querySelector("[data-information-result]");
	const range = root.querySelector("[data-information-range]");
	const pageLabel = root.querySelector("[data-information-page]");
	const empty = root.querySelector("[data-information-empty]");
	const sortButtons = Array.from(root.querySelectorAll("[data-information-sort]"));
	const numberFormatter = new Intl.NumberFormat("id-ID");
	const collator = new Intl.Collator("id-ID", { numeric: true, sensitivity: "base" });
	const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	const state = {
		page: 1,
		pageSize: Number(pageSize?.value || 10),
		sortDirection: 1,
		sortKey: "name",
	};
	const customSelects = new Map();

	const normalize = (value) =>
		String(value || "")
			.toLocaleLowerCase("id-ID")
			.normalize("NFD")
			.replace(/[\u0300-\u036f]/g, "")
			.trim();

	const compareRows = (first, second) => {
		if (state.sortKey === "decile") {
			const firstNumber = Number(first.dataset.decile);
			const secondNumber = Number(second.dataset.decile);
			const firstNumeric = Number.isFinite(firstNumber);
			const secondNumeric = Number.isFinite(secondNumber);
			if (firstNumeric && secondNumeric) {
				return (firstNumber - secondNumber) * state.sortDirection;
			}
			if (firstNumeric !== secondNumeric) {
				return (firstNumeric ? -1 : 1) * state.sortDirection;
			}
		}

		const firstValue = first.dataset[state.sortKey] || "";
		const secondValue = second.dataset[state.sortKey] || "";
		return collator.compare(firstValue, secondValue) * state.sortDirection;
	};

	const updateSortHeadings = () => {
		root.querySelectorAll("[data-information-sort-heading]").forEach((heading) => {
			const key = heading.dataset.informationSortHeading;
			heading.setAttribute(
				"aria-sort",
				key === state.sortKey ? (state.sortDirection > 0 ? "ascending" : "descending") : "none"
			);
		});

		sortButtons.forEach((button) => {
			button.dataset.sortState =
				button.dataset.informationSort === state.sortKey
					? state.sortDirection > 0
						? "ascending"
						: "descending"
					: "none";
		});
	};

	const getSelectLabel = (select) => {
		const option = select?.selectedOptions?.[0];
		return option ? option.textContent.trim() : "";
	};

	const closeCustomSelect = (wrapper) => {
		const toggle = wrapper?.querySelector("[data-information-select-toggle]");
		const menu = wrapper?.querySelector("[data-information-select-menu]");
		if (!toggle || !menu) {
			return;
		}
		toggle.setAttribute("aria-expanded", "false");
		menu.hidden = true;
	};

	const closeOtherCustomSelects = (currentWrapper) => {
		customSelects.forEach(({ wrapper }) => {
			if (wrapper !== currentWrapper) {
				closeCustomSelect(wrapper);
			}
		});
	};

	const syncCustomSelect = (select) => {
		const instance = customSelects.get(select);
		if (!instance) {
			return;
		}

		instance.toggle.textContent = getSelectLabel(select);
		instance.options.forEach((optionButton) => {
			const isSelected = optionButton.dataset.value === select.value;
			optionButton.classList.toggle("is-active", isSelected);
			optionButton.setAttribute("aria-selected", isSelected ? "true" : "false");
		});
	};

	const setupCustomSelect = (select) => {
		if (!select || customSelects.has(select)) {
			return;
		}

		const wrapper = document.createElement("div");
		const toggle = document.createElement("button");
		const menu = document.createElement("div");
		const optionButtons = [];

		wrapper.className = "village-information__select";
		toggle.type = "button";
		toggle.dataset.informationSelectToggle = "";
		toggle.setAttribute("aria-expanded", "false");
		toggle.setAttribute("aria-haspopup", "listbox");
		toggle.textContent = getSelectLabel(select);

		menu.className = "village-information__select-menu";
		menu.dataset.informationSelectMenu = "";
		menu.setAttribute("role", "listbox");
		menu.hidden = true;

		Array.from(select.options).forEach((option) => {
			const optionButton = document.createElement("button");
			optionButton.type = "button";
			optionButton.dataset.value = option.value;
			optionButton.setAttribute("role", "option");
			optionButton.textContent = option.textContent.trim();
			optionButton.addEventListener("click", () => {
				select.value = option.value;
				select.dispatchEvent(new Event("change", { bubbles: true }));
				closeCustomSelect(wrapper);
				toggle.focus();
			});
			optionButtons.push(optionButton);
			menu.appendChild(optionButton);
		});

		select.classList.add("village-information__native-select");
		select.parentNode.insertBefore(wrapper, select);
		wrapper.append(toggle, menu, select);
		customSelects.set(select, { wrapper, toggle, menu, options: optionButtons });
		syncCustomSelect(select);

		toggle.addEventListener("click", () => {
			const willOpen = menu.hidden;
			closeOtherCustomSelects(wrapper);
			menu.hidden = !willOpen;
			toggle.setAttribute("aria-expanded", willOpen ? "true" : "false");
		});

		select.addEventListener("change", () => syncCustomSelect(select));
	};

	[assistance, decile, pageSize].forEach(setupCustomSelect);

	document.addEventListener("click", (event) => {
		customSelects.forEach(({ wrapper }) => {
			if (!wrapper.contains(event.target)) {
				closeCustomSelect(wrapper);
			}
		});
	});

	document.addEventListener("keydown", (event) => {
		if (event.key !== "Escape") {
			return;
		}
		customSelects.forEach(({ wrapper }) => closeCustomSelect(wrapper));
	});

	const render = () => {
		if (!body) {
			return;
		}

		const query = normalize(search?.value);
		const selectedAssistance = assistance?.value || "";
		const selectedDecile = decile?.value || "";
		const filtered = rows
			.filter((row) => {
				const matchesName = !query || normalize(row.dataset.name).includes(query);
				const matchesAssistance = !selectedAssistance || row.dataset.assistance === selectedAssistance;
				const matchesDecile = !selectedDecile || row.dataset.decile === selectedDecile;
				return matchesName && matchesAssistance && matchesDecile;
			})
			.sort(compareRows);

		filtered.forEach((row) => body.appendChild(row));
		rows.filter((row) => !filtered.includes(row)).forEach((row) => body.appendChild(row));

		const totalPages = Math.max(1, Math.ceil(filtered.length / state.pageSize));
		state.page = Math.min(state.page, totalPages);
		const start = (state.page - 1) * state.pageSize;
		const end = Math.min(start + state.pageSize, filtered.length);
		const visibleRows = new Set(filtered.slice(start, end));

		rows.forEach((row) => {
			row.hidden = !visibleRows.has(row);
		});
		filtered.forEach((row, index) => {
			const numberCell = row.querySelector("[data-information-number]");
			if (numberCell) {
				numberCell.textContent = numberFormatter.format(index + 1);
			}
		});

		const hasFilters = Boolean(query || selectedAssistance || selectedDecile);
		if (reset) {
			reset.hidden = !hasFilters;
		}
		if (result) {
			result.textContent = hasFilters
				? `${numberFormatter.format(filtered.length)} dari ${numberFormatter.format(rows.length)} keluarga cocok`
				: `${numberFormatter.format(filtered.length)} keluarga ditemukan`;
		}
		if (range) {
			range.textContent = filtered.length
				? `Menampilkan ${numberFormatter.format(start + 1)} sampai ${numberFormatter.format(end)} dari ${numberFormatter.format(filtered.length)} keluarga`
				: "Tidak ada data untuk ditampilkan";
		}
		if (pageLabel) {
			pageLabel.textContent = `Halaman ${numberFormatter.format(state.page)} dari ${numberFormatter.format(totalPages)}`;
		}
		if (previous) {
			previous.disabled = state.page <= 1 || !filtered.length;
		}
		if (next) {
			next.disabled = state.page >= totalPages || !filtered.length;
		}
		if (empty) {
			empty.hidden = Boolean(filtered.length);
		}

		updateSortHeadings();
	};

	search?.addEventListener("input", () => {
		state.page = 1;
		render();
	});
	assistance?.addEventListener("change", () => {
		state.page = 1;
		render();
	});
	decile?.addEventListener("change", () => {
		state.page = 1;
		render();
	});
	pageSize?.addEventListener("change", () => {
		state.pageSize = Number(pageSize.value || 10);
		state.page = 1;
		render();
	});
	reset?.addEventListener("click", () => {
		if (search) {
			search.value = "";
		}
		if (assistance) {
			assistance.value = "";
		}
		if (decile) {
			decile.value = "";
			syncCustomSelect(decile);
		}
		syncCustomSelect(assistance);
		state.page = 1;
		render();
		search?.focus();
	});
	previous?.addEventListener("click", () => {
		state.page = Math.max(1, state.page - 1);
		render();
		root.querySelector(".village-information__table-meta")?.scrollIntoView({ block: "nearest" });
	});
	next?.addEventListener("click", () => {
		state.page += 1;
		render();
		root.querySelector(".village-information__table-meta")?.scrollIntoView({ block: "nearest" });
	});
	sortButtons.forEach((button) => {
		button.addEventListener("click", () => {
			const key = button.dataset.informationSort;
			if (state.sortKey === key) {
				state.sortDirection *= -1;
			} else {
				state.sortKey = key;
				state.sortDirection = 1;
			}
			state.page = 1;
			render();
		});
	});

	function setupMotion() {
		const countElements = Array.from(root.querySelectorAll("[data-information-count]"));
		const revealElements = [
			...root.querySelectorAll(".village-information__metric"),
			root.querySelector(".village-information__latest"),
			root.querySelector(".village-information__data-heading"),
		].filter(Boolean);

		if (reducedMotion || !("IntersectionObserver" in window)) {
			revealElements.forEach((element) => element.classList.add("is-visible"));
			return;
		}

		document.documentElement.classList.add("information-motion-ready");
		const revealObserver = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (!entry.isIntersecting) {
						return;
					}
					entry.target.classList.add("is-visible");
					revealObserver.unobserve(entry.target);
				});
			},
			{ threshold: 0.16 }
		);
		revealElements.forEach((element) => revealObserver.observe(element));

		const countObserver = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (!entry.isIntersecting) {
						return;
					}
					const element = entry.target;
					const target = Number(element.dataset.informationCount || 0);
					const started = performance.now();
					const duration = 850;
					const tick = (now) => {
						const progress = Math.min(1, (now - started) / duration);
						const eased = 1 - Math.pow(1 - progress, 3);
						element.textContent = numberFormatter.format(Math.round(target * eased));
						if (progress < 1) {
							window.requestAnimationFrame(tick);
						}
					};
					window.requestAnimationFrame(tick);
					countObserver.unobserve(element);
				});
			},
			{ threshold: 0.6 }
		);
		countElements.forEach((element) => countObserver.observe(element));
	}

	render();
	setupMotion();
})();
