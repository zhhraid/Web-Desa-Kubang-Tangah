document.addEventListener("DOMContentLoaded", () => {
	const root = document.querySelector("[data-infographics-page]");

	if (!root) {
		return;
	}

	const regionSelect = root.querySelector("[data-region]");
	const sectionElements = Array.from(root.querySelectorAll("[data-section]"));
	const jumpLinks = Array.from(root.querySelectorAll("[data-jump]"));
	const navTargets = [root.querySelector("[data-nav-section]"), ...sectionElements].filter(Boolean);
	const overview = root.querySelector("[data-overview]");
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

	const setTooltip = (element, label, value, total) => {
		const share = total ? Math.round((value / total) * 1000) / 10 : 0;
		const tooltip = `${label}: ${numberFormat.format(value)} (${numberFormat.format(share)}%)`;
		element.title = tooltip;
		element.setAttribute("aria-label", tooltip);
		element.tabIndex = 0;
	};

	const getValues = (dataset) => {
		return regionSelect.value === "all" ? dataset.total : dataset.byDusun[regionSelect.value];
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

	const renderStacked = (chart, legend, dataset) => {
		const wrapper = createElement("div", "village-home__stacked-chart");
		const regions = regionSelect.value === "all" ? infographicData.meta.dusun : [regionSelect.value];

		regions.forEach((region) => {
			const values = dataset.byDusun[region];
			const rowTotal = values.reduce((sum, value) => sum + value, 0);
			const row = createElement("div", "village-home__stacked-row");
			const rowHead = createElement("div", "village-home__stacked-label");
			rowHead.append(createElement("strong", "", region), createElement("span", "", numberFormat.format(rowTotal)));

			const track = createElement("div", "village-home__stacked-track");
			values.forEach((value, index) => {
				if (!value) {
					return;
				}
				const segment = createElement("span", "village-home__stacked-segment");
				segment.style.setProperty("--segment-size", `${(value / rowTotal) * 100}%`);
				segment.style.backgroundColor = palette[index % palette.length];
				setTooltip(segment, dataset.categories[index], value, rowTotal);
				track.append(segment);
			});

			row.append(rowHead, track);
			wrapper.append(row);
		});

		chart.append(wrapper);
		renderLegend(legend, dataset.categories);
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
		let offset = 0;
		const stops = values.map((value, index) => {
			const start = offset;
			offset += total ? (value / total) * 100 : 0;
			return `${palette[index % palette.length]} ${start}% ${offset}%`;
		});
		const wrapper = createElement("div", "village-home__donut-layout");
		const donut = createElement("div", "village-home__donut");
		const center = createElement("div", "village-home__donut-center");
		center.append(createElement("strong", "", numberFormat.format(total)), createElement("span", "", unit));
		donut.style.background = `conic-gradient(${stops.join(",")})`;
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
			ring.append(createElement("strong", "", `${numberFormat.format(Math.round(share * 10) / 10)}%`));
			item.append(ring, createElement("h4", "", category), createElement("p", "", `${numberFormat.format(values[index])} keluarga`));
			setTooltip(item, category, values[index], total);
			wrapper.append(item);
		});

		chart.append(wrapper);
		legend.replaceChildren();
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
		const scope = regionSelect.value === "all" ? "seluruh desa" : `Dusun ${regionSelect.value}`;
		section.querySelector("[data-insight-value]").textContent = numberFormat.format(maximum);
		section.querySelector("[data-insight-title]").textContent = dataset.categories[topIndex];
		section.querySelector("[data-insight-copy]").textContent = `${numberFormat.format(share)}% dari ${numberFormat.format(total)} ${unit} di ${scope}.`;
	};

	const renderSection = (section) => {
		const key = section.dataset.section;
		const type = section.dataset.chartType;
		const unit = section.dataset.unit;
		const dataset = infographicData[key];
		const chart = section.querySelector("[data-chart]");
		const legend = section.querySelector("[data-legend]");
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const scope = regionSelect.value === "all" ? "Semua Dusun" : `Dusun ${regionSelect.value}`;

		chart.className = `village-home__chart village-home__chart--${type}`;
		chart.replaceChildren();
		legend.replaceChildren();
		section.querySelector("[data-total]").textContent = `${scope} - ${numberFormat.format(total)} ${unit}`;

		if (type === "stacked") {
			renderStacked(chart, legend, dataset);
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
		if (reducedMotion) {
			chart.classList.add("is-visible");
		} else {
			revealChart(chart);
		}
	};

	const renderAll = () => {
		sectionElements.forEach(renderSection);
	};

	const setActiveJump = (key) => {
		jumpLinks.forEach((link) => link.classList.toggle("is-active", link.dataset.jump === key));
	};

	jumpLinks.forEach((link) => {
		link.addEventListener("click", (event) => {
			event.preventDefault();
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

	regionSelect.addEventListener("change", renderAll);

	if ("IntersectionObserver" in window) {
		if (overview) {
			const overviewObserver = new IntersectionObserver(
				(entries, observer) => {
					if (entries.some((entry) => entry.isIntersecting)) {
						overview.classList.add("is-visible");
						observer.disconnect();
					}
				},
				{ threshold: 0.25 }
			);
			overviewObserver.observe(overview);
		}

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
	} else if (overview) {
		overview.classList.add("is-visible");
	}

	fetch(root.dataset.source)
		.then((response) => {
			if (!response.ok) {
				throw new Error("Data infografis tidak dapat dimuat.");
			}
			return response.json();
		})
		.then((data) => {
			infographicData = data;
			data.meta.dusun.forEach((region) => {
				const option = createElement("option", "", `Dusun ${region}`);
				option.value = region;
				regionSelect.append(option);
			});
			const initialSection = window.location.hash === "#ringkasan" || !window.location.hash ? "all" : window.location.hash.slice(1);
			setActiveJump(initialSection);
			renderAll();
		})
		.catch(() => {
			sectionElements.forEach((section) => {
				const chart = section.querySelector("[data-chart]");
				chart.replaceChildren(createElement("p", "village-home__chart-error", "Data infografis belum dapat ditampilkan. Silakan muat ulang halaman."));
			});
		});
});
