document.addEventListener("DOMContentLoaded", () => {
	const root = document.querySelector("[data-infographics-page]");

	if (!root) {
		return;
	}

	const regionSelect = root.querySelector("[data-region]");
	const graphControls = root.querySelector("[data-graph-controls]");
	const sectionElements = Array.from(root.querySelectorAll("[data-section]"));
	const jumpLinks = Array.from(root.querySelectorAll("[data-jump]"));
	const viewButtons = Array.from(root.querySelectorAll("[data-stat-view]"));
	const statPanels = Array.from(root.querySelectorAll("[data-stat-panel]"));
	const navTargets = [root.querySelector("[data-nav-section]"), ...sectionElements].filter(Boolean);
	const summaryNumbers = Array.from(root.querySelectorAll("[data-summary-number]"));
	const numberFormat = new Intl.NumberFormat("id-ID");
	const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	const palette = ["#25734a", "#2f8f8b", "#d6a23a", "#d66b52", "#5276a7", "#8a5f8f", "#75a84f", "#b77945", "#3f9ac1", "#89935c", "#77827d"];
	let infographicData = null;

	const createElement = (tagName, className, text) => {
		const element = document.createElement(tagName);
		if (className) {
			element.className = className;
		}
		if (text !== undefined) {
			element.textContent = text;
		}
		return element;
	};

	const formatDecimal = (value, decimals = 1) =>
		new Intl.NumberFormat("id-ID", {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		}).format(value);

	const formatRupiahShort = (value) => `Rp ${formatDecimal(Number(value || 0) / 1000000, 2)} jt`;

	const animateSummaryNumber = (element) => {
		const value = Number(element.dataset.summaryNumber);
		const decimals = Number(element.dataset.summaryDecimals || 0);
		const formatter = new Intl.NumberFormat("id-ID", {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		});

		if (!Number.isFinite(value) || reducedMotion) {
			element.textContent = formatter.format(value);
			return;
		}

		const duration = 1050;
		const start = performance.now();
		const update = (now) => {
			const progress = Math.min((now - start) / duration, 1);
			const easedProgress = 1 - Math.pow(1 - progress, 3);
			const current = decimals ? value * easedProgress : Math.round(value * easedProgress);
			element.textContent = formatter.format(current);
			if (progress < 1) {
				window.requestAnimationFrame(update);
			}
		};

		window.requestAnimationFrame(update);
	};

	summaryNumbers.forEach(animateSummaryNumber);

	const setTooltip = (element, label, value, total, unit = "") => {
		const share = total ? Math.round((value / total) * 1000) / 10 : 0;
		const suffix = unit ? ` ${unit}` : "";
		const tooltip = `${label}: ${numberFormat.format(value)}${suffix} (${numberFormat.format(share)}%)`;
		element.title = tooltip;
		element.setAttribute("aria-label", tooltip);
		element.tabIndex = 0;
	};

	const getSelectedRegion = () => (regionSelect ? regionSelect.value : "all");

	const getValues = (dataset) => {
		const selectedRegion = getSelectedRegion();
		if (selectedRegion === "all" || dataset.filterable === false || !dataset.byDusun || !dataset.byDusun[selectedRegion]) {
			return dataset.total || [];
		}
		return dataset.byDusun[selectedRegion];
	};

	const getScopeLabel = (dataset) => {
		const selectedRegion = getSelectedRegion();
		if (selectedRegion === "all" || dataset.filterable === false || !dataset.byDusun || !dataset.byDusun[selectedRegion]) {
			return "Seluruh Desa";
		}
		return `Dusun ${selectedRegion}`;
	};

	const createGradient = (values, colors = palette) => {
		const total = values.reduce((sum, value) => sum + Number(value || 0), 0);
		let offset = 0;
		const stops = values.map((value, index) => {
			const start = offset;
			offset += total ? (Number(value || 0) / total) * 100 : 0;
			return `${colors[index % colors.length]} ${start}% ${offset}%`;
		});
		return `conic-gradient(${stops.join(",")})`;
	};

	const renderLegend = (legend, categories) => {
		legend.replaceChildren();
		categories.forEach((category, index) => {
			const item = createElement("span", "village-home__legend-item");
			const swatch = createElement("i");
			swatch.style.backgroundColor = palette[index % palette.length];
			item.append(swatch, document.createTextNode(category));
			legend.append(item);
		});
	};

	const renderPie = (chart, legend, dataset, unit) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const wrapper = createElement("div", "village-home__pie-layout");
		const pie = createElement("div", "village-home__pie");
		const center = createElement("div", "village-home__pie-center");
		center.append(createElement("strong", "", numberFormat.format(total)), createElement("span", "", unit));
		pie.style.setProperty("--chart-gradient", createGradient(values, ["#2878bc", "#f35a91"]));
		pie.append(center);

		const details = createElement("div", "village-home__pie-details");
		dataset.categories.forEach((category, index) => {
			const value = Number(values[index] || 0);
			const share = total ? (value / total) * 100 : 0;
			const row = createElement("div", "village-home__pie-detail");
			const swatch = createElement("i");
			swatch.style.backgroundColor = index === 0 ? "#2878bc" : "#f35a91";
			row.append(swatch, createElement("span", "", category), createElement("strong", "", numberFormat.format(value)), createElement("small", "", `${formatDecimal(share, 1)}%`));
			setTooltip(row, category, value, total, unit);
			details.append(row);
		});

		wrapper.append(pie, details);
		chart.append(wrapper);
		legend.replaceChildren();
	};

	const getPyramidValues = (dataset) => {
		const selectedRegion = getSelectedRegion();
		if (selectedRegion !== "all" && dataset.byDusun && dataset.byDusun[selectedRegion]) {
			return dataset.byDusun[selectedRegion];
		}
		return {
			male: dataset.male || [],
			female: dataset.female || [],
		};
	};

	const renderPyramid = (chart, legend, dataset, unit) => {
		const values = getPyramidValues(dataset);
		const male = values.male || [];
		const female = values.female || [];
		const total = male.reduce((sum, value) => sum + Number(value || 0), 0) + female.reduce((sum, value) => sum + Number(value || 0), 0);
		const maximum = Math.max(...male, ...female, 1);
		const wrapper = createElement("div", "village-home__pyramid-chart");
		const head = createElement("div", "village-home__pyramid-head");
		head.append(createElement("span", "", "Perempuan"), createElement("span", "", "Usia"), createElement("span", "", "Laki-laki"));
		wrapper.append(head);

		dataset.categories
			.map((category, index) => ({ category, index }))
			.reverse()
			.forEach((item) => {
				const femaleValue = Number(female[item.index] || 0);
				const maleValue = Number(male[item.index] || 0);
				const row = createElement("div", "village-home__pyramid-row");
				const femaleBar = createElement("span", "village-home__pyramid-bar village-home__pyramid-bar--female");
				const maleBar = createElement("span", "village-home__pyramid-bar village-home__pyramid-bar--male");
				femaleBar.style.setProperty("--bar-size", `${(femaleValue / maximum) * 100}%`);
				maleBar.style.setProperty("--bar-size", `${(maleValue / maximum) * 100}%`);
				femaleBar.append(createElement("b", "", numberFormat.format(femaleValue)));
				maleBar.append(createElement("b", "", numberFormat.format(maleValue)));
				setTooltip(femaleBar, `Perempuan ${item.category}`, femaleValue, total, unit);
				setTooltip(maleBar, `Laki-laki ${item.category}`, maleValue, total, unit);
				row.append(femaleBar, createElement("strong", "", item.category), maleBar);
				wrapper.append(row);
			});

		const note = createElement("p", "village-home__pyramid-note");
		const productive = dataset.productive || {};
		note.textContent = `Usia produktif 15-64 tahun: ${numberFormat.format(productive.total || 0)} jiwa (${formatDecimal(productive.share || 0, 2)}%), terdiri dari ${numberFormat.format(productive.male || 0)} laki-laki dan ${numberFormat.format(productive.female || 0)} perempuan.`;
		chart.append(wrapper, note);
		renderLegend(legend, ["Perempuan", "Laki-laki"]);
	};

	const renderBars = (chart, legend, dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const maximum = Math.max(...values, 1);
		const ordered = dataset.categories
			.map((category, index) => ({ category, value: values[index], color: palette[index % palette.length] }))
			.sort((first, second) => second.value - first.value);
		const wrapper = createElement("div", "village-home__bar-chart");

		ordered.forEach((item) => {
			const row = createElement("div", "village-home__bar-row");
			const track = createElement("div", "village-home__bar-track");
			const fill = createElement("span", "village-home__bar-fill");
			fill.style.setProperty("--bar-size", `${(item.value / maximum) * 100}%`);
			fill.style.backgroundColor = item.color;
			setTooltip(fill, item.category, item.value, total);
			track.append(fill);
			row.append(
				createElement("span", "village-home__bar-label", item.category),
				track,
				createElement("strong", "", numberFormat.format(item.value))
			);
			wrapper.append(row);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderLollipop = (chart, legend, dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const maximum = Math.max(...values, 1);
		const ordered = dataset.categories
			.map((category, index) => ({ category, value: values[index], color: palette[index % palette.length] }))
			.sort((first, second) => second.value - first.value);
		const wrapper = createElement("div", "village-home__lollipop-chart");

		ordered.forEach((item) => {
			const row = createElement("div", "village-home__lollipop-row");
			const track = createElement("div", "village-home__lollipop-track");
			const line = createElement("span", "village-home__lollipop-line");
			const dot = createElement("span", "village-home__lollipop-dot");
			const size = `${(item.value / maximum) * 100}%`;
			line.style.setProperty("--lollipop-size", size);
			line.style.backgroundColor = item.color;
			dot.style.setProperty("--lollipop-position", size);
			dot.style.backgroundColor = item.color;
			setTooltip(dot, item.category, item.value, total);
			track.append(line, dot);
			row.append(
				createElement("span", "village-home__lollipop-label", item.category),
				track,
				createElement("strong", "", numberFormat.format(item.value))
			);
			wrapper.append(row);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderDonut = (chart, legend, dataset, unit) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const wrapper = createElement("div", "village-home__donut-layout");
		const donut = createElement("div", "village-home__donut");
		const center = createElement("div", "village-home__donut-center");
		center.append(createElement("strong", "", numberFormat.format(total)), createElement("span", "", unit));
		donut.style.background = createGradient(values);
		donut.append(center);

		const details = createElement("div", "village-home__donut-details");
		dataset.categories.forEach((category, index) => {
			const row = createElement("div", "village-home__donut-detail");
			const label = createElement("span");
			const swatch = createElement("i");
			swatch.style.backgroundColor = palette[index % palette.length];
			label.append(swatch, document.createTextNode(category));
			row.append(label, createElement("strong", "", numberFormat.format(values[index])));
			setTooltip(row, category, values[index], total);
			details.append(row);
		});

		wrapper.append(donut, details);
		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderColumns = (chart, legend, dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const maximum = Math.max(...values, 1);
		const wrapper = createElement("div", "village-home__column-chart");

		dataset.categories.forEach((category, index) => {
			const column = createElement("div", "village-home__column");
			const track = createElement("div", "village-home__column-track");
			const bar = createElement("span", "village-home__column-bar");
			const label = category === "Tidak Ditentukan" ? "N/A" : `D${category}`;
			bar.style.setProperty("--column-size", `${(values[index] / maximum) * 100}%`);
			bar.style.backgroundColor = palette[index % palette.length];
			setTooltip(bar, category === "Tidak Ditentukan" ? category : `Desil ${category}`, values[index], total);
			track.append(bar);
			column.append(createElement("strong", "", numberFormat.format(values[index])), track, createElement("span", "", label));
			wrapper.append(column);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderProportion = (chart, legend, dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const wrapper = createElement("div", "village-home__proportion-grid");

		dataset.categories.forEach((category, index) => {
			const share = total ? (values[index] / total) * 100 : 0;
			const item = createElement("div", "village-home__proportion-item");
			const ring = createElement("div", "village-home__proportion-ring");
			ring.style.background = `conic-gradient(${palette[index % palette.length]} ${share}%, #e6eee9 ${share}% 100%)`;
			ring.append(createElement("strong", "", `${formatDecimal(share, 1)}%`));
			item.append(ring, createElement("h4", "", category), createElement("p", "", `${numberFormat.format(values[index])} keluarga`));
			setTooltip(item, category, values[index], total);
			wrapper.append(item);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderCreative = (chart, legend, dataset) => {
		const selectedRegion = getSelectedRegion();
		const isAll = selectedRegion === "all";
		const categories = isAll ? dataset.byDusun.categories : dataset.products.categories;
		const values = isAll ? dataset.byDusun.total : dataset.byDusun.byProduct[selectedRegion] || dataset.products.total;
		const total = values.reduce((sum, value) => sum + Number(value || 0), 0);
		const maximum = Math.max(...values, 1);
		const wrapper = createElement("div", "village-home__creative-chart");
		const bars = createElement("div", "village-home__creative-bars");

		categories.forEach((category, index) => {
			const value = Number(values[index] || 0);
			const item = createElement("div", "village-home__creative-item");
			const track = createElement("div", "village-home__creative-track");
			const fill = createElement("span", "village-home__creative-fill");
			fill.style.setProperty("--bar-size", `${(value / maximum) * 100}%`);
			fill.style.backgroundColor = palette[index % palette.length];
			setTooltip(fill, category, value, total, "pelaku");
			track.append(fill);
			item.append(createElement("strong", "", numberFormat.format(value)), track, createElement("span", "", category));
			bars.append(item);
		});

		const summary = createElement("aside", "village-home__creative-summary");
		summary.append(
			createElement("span", "", "Ringkasan Produk"),
			createElement("strong", "", numberFormat.format(dataset.summary.totalActors || 0)),
			createElement("p", "", `Pelaku ekonomi kreatif dengan nilai produksi total ${formatRupiahShort(dataset.summary.totalProductionValue || 0)} per tahun.`),
			createElement("p", "", `${formatDecimal(dataset.summary.individualShare || 0, 2)}% pelaku berbentuk usaha perorangan.`)
		);

		wrapper.append(bars, summary);
		chart.append(wrapper);
		renderLegend(legend, dataset.products.categories);
	};

	const revealChart = (chart) => {
		const bounds = chart.getBoundingClientRect();
		if (bounds.top < window.innerHeight * 0.92 && bounds.bottom > 0) {
			requestAnimationFrame(() => chart.classList.add("is-visible"));
		}
	};

	const updateInsight = (section, dataset, values, total, unit) => {
		const maximum = Math.max(...values);
		const topIndex = values.indexOf(maximum);
		const share = total ? Math.round((maximum / total) * 1000) / 10 : 0;
		const scope = getScopeLabel(dataset).toLowerCase();
		section.querySelector("[data-insight-value]").textContent = numberFormat.format(maximum);
		section.querySelector("[data-insight-title]").textContent = dataset.categories[topIndex];
		section.querySelector("[data-insight-copy]").textContent = `${numberFormat.format(share)}% dari ${numberFormat.format(total)} ${unit} di ${scope}.`;
	};

	const updatePyramidInsight = (section, dataset, total) => {
		const productive = dataset.productive || {};
		const scope = getSelectedRegion() === "all" ? "struktur umur desa" : `struktur umur Dusun ${getSelectedRegion()}`;
		section.querySelector("[data-insight-value]").textContent = numberFormat.format(productive.total || 0);
		section.querySelector("[data-insight-title]").textContent = "Usia produktif 15-64 tahun";
		section.querySelector("[data-insight-copy]").textContent = `${formatDecimal(productive.share || 0, 2)}% dari ${numberFormat.format(total)} jiwa pada ${scope}.`;
	};

	const updateCreativeInsight = (section, dataset) => {
		const values = getSelectedRegion() === "all" ? dataset.byDusun.total : dataset.byDusun.byProduct[getSelectedRegion()] || dataset.products.total;
		const categories = getSelectedRegion() === "all" ? dataset.byDusun.categories : dataset.products.categories;
		const maximum = Math.max(...values);
		const topIndex = values.indexOf(maximum);
		section.querySelector("[data-insight-value]").textContent = numberFormat.format(maximum);
		section.querySelector("[data-insight-title]").textContent = categories[topIndex];
		section.querySelector("[data-insight-copy]").textContent = getSelectedRegion() === "all"
			? "Wilayah dengan pelaku ekonomi kreatif terbanyak."
			: "Produk unggulan terbanyak pada dusun yang dipilih.";
	};

	const renderSection = (section) => {
		const key = section.dataset.section;
		const type = section.dataset.chartType;
		const unit = section.dataset.unit;
		const dataset = infographicData[key];
		const chart = section.querySelector("[data-chart]");
		const legend = section.querySelector("[data-legend]");

		if (!dataset) {
			return;
		}

		chart.className = `village-home__chart village-home__chart--${type}`;
		chart.replaceChildren();
		legend.replaceChildren();

		if (type === "pyramid") {
			const pyramidValues = getPyramidValues(dataset);
			const total = [...(pyramidValues.male || []), ...(pyramidValues.female || [])].reduce((sum, value) => sum + Number(value || 0), 0);
			section.querySelector("[data-total]").textContent = `${getSelectedRegion() === "all" ? "Seluruh Desa" : `Dusun ${getSelectedRegion()}`} - ${numberFormat.format(total)} ${unit}`;
			renderPyramid(chart, legend, dataset, unit);
			updatePyramidInsight(section, dataset, total);
		} else if (type === "creative") {
			const total = dataset.summary ? dataset.summary.totalActors : 0;
			section.querySelector("[data-total]").textContent = `${getSelectedRegion() === "all" ? "Seluruh Desa" : `Dusun ${getSelectedRegion()}`} - ${numberFormat.format(total)} pelaku`;
			renderCreative(chart, legend, dataset);
			updateCreativeInsight(section, dataset);
		} else {
			const values = getValues(dataset);
			const total = values.reduce((sum, value) => sum + Number(value || 0), 0);
			const scope = getScopeLabel(dataset);
			section.querySelector("[data-total]").textContent = `${scope} - ${numberFormat.format(total)} ${unit}`;

			if (type === "pie") {
				renderPie(chart, legend, dataset, unit);
			} else if (type === "bars") {
				renderBars(chart, legend, dataset);
			} else if (type === "lollipop") {
				renderLollipop(chart, legend, dataset);
			} else if (type === "donut") {
				renderDonut(chart, legend, dataset, unit);
			} else if (type === "columns") {
				renderColumns(chart, legend, dataset);
			} else {
				renderProportion(chart, legend, dataset);
			}

			updateInsight(section, dataset, values, total, unit);
		}

		if (reducedMotion) {
			chart.classList.add("is-visible");
		} else {
			revealChart(chart);
		}
	};

	const renderAll = () => {
		if (!infographicData) {
			return;
		}
		sectionElements.forEach(renderSection);
	};

	const setActiveJump = (key) => {
		jumpLinks.forEach((link) => link.classList.toggle("is-active", link.dataset.jump === key));
	};

	const setActiveView = (view) => {
		viewButtons.forEach((button) => {
			const isActive = button.dataset.statView === view;
			button.classList.toggle("is-active", isActive);
			button.setAttribute("aria-selected", isActive ? "true" : "false");
		});
		statPanels.forEach((panel) => {
			const isActive = panel.dataset.statPanel === view;
			panel.hidden = !isActive;
			panel.classList.toggle("is-active", isActive);
		});
		if (graphControls) {
			graphControls.hidden = view !== "grafik";
		}
		if (view === "grafik") {
			renderAll();
		}
	};

	viewButtons.forEach((button) => {
		button.addEventListener("click", () => {
			setActiveView(button.dataset.statView);
		});
	});

	jumpLinks.forEach((link) => {
		link.addEventListener("click", (event) => {
			event.preventDefault();
			setActiveView("grafik");
			const targetId = link.dataset.jump === "all" ? "ringkasan" : link.dataset.jump;
			const target = root.querySelector(`#${targetId}`);
			if (!target) {
				return;
			}
			setActiveJump(link.dataset.jump);
			target.scrollIntoView({ behavior: reducedMotion ? "auto" : "smooth", block: "start" });
			window.history.replaceState(null, "", `#${link.dataset.jump}`);
		});
	});

	if (regionSelect) {
		regionSelect.addEventListener("change", renderAll);
	}

	if ("IntersectionObserver" in window) {
		const chartObserver = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add("is-visible");
					}
				});
			},
			{ threshold: 0.2 }
		);
		sectionElements.forEach((section) => chartObserver.observe(section.querySelector("[data-chart]")));

		const sectionObserver = new IntersectionObserver(
			(entries) => {
				const visible = entries.find((entry) => entry.isIntersecting);
				if (visible) {
					setActiveJump(visible.target.dataset.navSection || visible.target.dataset.section);
				}
			},
			{ rootMargin: "-30% 0px -58% 0px", threshold: 0 }
		);
		navTargets.forEach((section) => sectionObserver.observe(section));
	}

	fetch(root.dataset.source)
		.then((response) => {
			if (!response.ok) {
				throw new Error("Data statistik tidak dapat dimuat.");
			}
			return response.json();
		})
		.then((data) => {
			infographicData = data;
			if (regionSelect && data.meta && Array.isArray(data.meta.dusun)) {
				data.meta.dusun.forEach((region) => {
					const option = createElement("option", "", `Dusun ${region}`);
					option.value = region;
					regionSelect.append(option);
				});
			}
			const initialSection = window.location.hash === "#ringkasan" || !window.location.hash ? "all" : window.location.hash.slice(1);
			setActiveJump(initialSection);
			renderAll();
			if (window.location.hash === "#infografik") {
				setActiveView("infografik");
			}
		})
		.catch(() => {
			sectionElements.forEach((section) => {
				const chart = section.querySelector("[data-chart]");
				chart.replaceChildren(createElement("p", "village-home__chart-error", "Data statistik belum dapat ditampilkan. Silakan muat ulang halaman."));
			});
		});
});
