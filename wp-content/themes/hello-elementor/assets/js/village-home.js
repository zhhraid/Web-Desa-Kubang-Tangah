document.addEventListener("DOMContentLoaded", () => {
	const numbers = document.querySelectorAll(".village-home__stat-number[data-count]");
	const statsPanel = document.querySelector(".village-home__stats");

	if (!numbers.length) {
		return;
	}

	const formatNumber = (value, decimals) => {
		return new Intl.NumberFormat("id-ID", {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		}).format(value);
	};

	const animateNumber = (element) => {
		const target = Number.parseFloat(element.dataset.count || "0");
		const decimals = Number.parseInt(element.dataset.decimals || "0", 10);
		const duration = 1450;
		const startTime = performance.now();

		element.classList.add("is-counting");

		const tick = (currentTime) => {
			const progress = Math.min((currentTime - startTime) / duration, 1);
			const eased = 1 - Math.pow(1 - progress, 3);
			const current = target * eased;

			element.textContent = formatNumber(current, decimals);

			if (progress < 1) {
				requestAnimationFrame(tick);
				return;
			}

			element.textContent = formatNumber(target, decimals);
			window.setTimeout(() => element.classList.remove("is-counting"), 240);
		};

		requestAnimationFrame(tick);
	};

	const animateAllNumbers = () => {
		numbers.forEach(animateNumber);
	};

	let replayTimer = 0;

	const startReplay = () => {
		window.clearInterval(replayTimer);
		animateAllNumbers();
		replayTimer = window.setInterval(animateAllNumbers, 5200);
	};

	const stopReplay = () => {
		window.clearInterval(replayTimer);
		replayTimer = 0;
	};

	if (!("IntersectionObserver" in window) || !statsPanel) {
		startReplay();
		return;
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					startReplay();
					return;
				}

				stopReplay();
			});
		},
		{ threshold: 0.35 }
	);

	observer.observe(statsPanel);
});

document.addEventListener("DOMContentLoaded", () => {
	const gallery = document.querySelector("[data-home-gallery]");

	if (!gallery) {
		return;
	}

	const track = gallery.querySelector("[data-gallery-track]");
	const prevButton = gallery.querySelector("[data-gallery-prev]");
	const nextButton = gallery.querySelector("[data-gallery-next]");
	const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	let timer = 0;

	if (!track) {
		return;
	}

	const move = (direction = 1) => {
		const card = track.querySelector(".village-home__gallery-photo");
		const styles = window.getComputedStyle(track);
		const gap = Number.parseFloat(styles.columnGap || styles.gap || "0") || 0;
		const distance = card ? card.getBoundingClientRect().width + gap : Math.max(track.clientWidth * 0.5, 240);
		const repeatPoint = track.scrollWidth / 2;
		const nearEnd = track.scrollLeft >= repeatPoint - distance - 12;

		if (direction > 0 && nearEnd) {
			track.scrollTo({ left: 0, behavior: "smooth" });
			return;
		}

		if (direction < 0 && track.scrollLeft <= 8) {
			track.scrollTo({ left: track.scrollWidth, behavior: "smooth" });
			return;
		}

		track.scrollBy({ left: distance * direction, behavior: "smooth" });
	};

	const stop = () => {
		window.clearInterval(timer);
		timer = 0;
	};

	const start = () => {
		if (reducedMotion) {
			return;
		}

		stop();
		timer = window.setInterval(() => move(1), 2800);
	};

	prevButton?.addEventListener("click", () => {
		move(-1);
		start();
	});

	nextButton?.addEventListener("click", () => {
		move(1);
		start();
	});

	gallery.addEventListener("mouseenter", stop);
	gallery.addEventListener("mouseleave", start);
	gallery.addEventListener("focusin", stop);
	gallery.addEventListener("focusout", start);

	start();
});

document.addEventListener("DOMContentLoaded", () => {
	const root = document.querySelector("[data-infographic-source]");

	if (!root) {
		return;
	}

	const chart = root.querySelector("[data-chart]");
	const legend = root.querySelector("[data-chart-legend]");
	const regionSelect = root.querySelector("[data-infographic-region]");
	const tabs = Array.from(root.querySelectorAll("[data-infographic-tab]"));
	const title = root.querySelector("[data-chart-title]");
	const kicker = root.querySelector("[data-chart-kicker]");
	const totalLabel = root.querySelector("[data-chart-total]");
	const insightValue = root.querySelector("[data-insight-value]");
	const insightTitle = root.querySelector("[data-insight-title]");
	const insightCopy = root.querySelector("[data-insight-copy]");
	const numberFormat = new Intl.NumberFormat("id-ID");
	const palette = ["#25734a", "#2f8f8b", "#d6a23a", "#d66b52", "#5276a7", "#8a5f8f", "#75a84f", "#b77945", "#3f9ac1", "#89935c", "#77827d"];
	const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	let activeType = "usia";
	let infographicData = null;

	const chartConfig = {
		usia: {
			title: "Usia Per Dusun",
			kicker: "Komposisi penduduk",
			unit: "penduduk",
			type: "stacked",
		},
		pendidikan: {
			title: "Pendidikan Per Dusun",
			kicker: "Jenjang pendidikan terakhir",
			unit: "penduduk",
			type: "bars",
		},
		pekerjaan: {
			title: "Pekerjaan Per Dusun",
			kicker: "Aktivitas utama penduduk",
			unit: "penduduk",
			type: "lollipop",
		},
		pernikahan: {
			title: "Status Pernikahan",
			kicker: "Status perkawinan penduduk",
			unit: "penduduk",
			type: "donut",
		},
		desil: {
			title: "Distribusi Desil Keluarga",
			kicker: "Kelompok kesejahteraan keluarga",
			unit: "keluarga",
			type: "columns",
		},
		bantuan: {
			title: "Pengelompokan Bantuan",
			kicker: "Cakupan bantuan keluarga",
			unit: "keluarga",
			type: "proportion",
		},
	};

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

	const renderLegend = (categories) => {
		legend.replaceChildren();
		categories.forEach((category, index) => {
			const item = createElement("span", "village-home__legend-item");
			const swatch = createElement("i");
			swatch.style.backgroundColor = palette[index % palette.length];
			item.append(swatch, document.createTextNode(category));
			legend.append(item);
		});
	};

	const renderStacked = (dataset) => {
		const wrapper = createElement("div", "village-home__stacked-chart");
		const regions = regionSelect.value === "all" ? infographicData.meta.dusun : [regionSelect.value];

		regions.forEach((region) => {
			const values = dataset.byDusun[region];
			const rowTotal = values.reduce((sum, value) => sum + value, 0);
			const row = createElement("div", "village-home__stacked-row");
			const rowHead = createElement("div", "village-home__stacked-label");
			rowHead.append(
				createElement("strong", "", region),
				createElement("span", "", numberFormat.format(rowTotal))
			);

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
		renderLegend(dataset.categories);
	};

	const renderBars = (dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const ordered = dataset.categories
			.map((category, index) => ({ category, value: values[index], color: palette[index % palette.length] }))
			.sort((a, b) => b.value - a.value);
		const maximum = Math.max(...values, 1);
		const wrapper = createElement("div", "village-home__bar-chart");

		ordered.forEach((item) => {
			const row = createElement("div", "village-home__bar-row");
			const label = createElement("span", "village-home__bar-label", item.category);
			const track = createElement("div", "village-home__bar-track");
			const fill = createElement("span", "village-home__bar-fill");
			const value = createElement("strong", "", numberFormat.format(item.value));
			fill.style.setProperty("--bar-size", `${(item.value / maximum) * 100}%`);
			fill.style.backgroundColor = item.color;
			setTooltip(fill, item.category, item.value, total);
			track.append(fill);
			row.append(label, track, value);
			wrapper.append(row);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderLollipop = (dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const ordered = dataset.categories
			.map((category, index) => ({ category, value: values[index], color: palette[index % palette.length] }))
			.sort((a, b) => b.value - a.value);
		const maximum = Math.max(...values, 1);
		const wrapper = createElement("div", "village-home__lollipop-chart");

		ordered.forEach((item) => {
			const row = createElement("div", "village-home__lollipop-row");
			const label = createElement("span", "village-home__lollipop-label", item.category);
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
			row.append(label, track, createElement("strong", "", numberFormat.format(item.value)));
			wrapper.append(row);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderDonut = (dataset) => {
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
		center.append(
			createElement("strong", "", numberFormat.format(total)),
			createElement("span", "", chartConfig[activeType].unit)
		);
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

	const renderColumns = (dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const maximum = Math.max(...values, 1);
		const wrapper = createElement("div", "village-home__column-chart");

		dataset.categories.forEach((category, index) => {
			const column = createElement("div", "village-home__column");
			const value = createElement("strong", "", numberFormat.format(values[index]));
			const track = createElement("div", "village-home__column-track");
			const bar = createElement("span", "village-home__column-bar");
			bar.style.setProperty("--column-size", `${(values[index] / maximum) * 100}%`);
			bar.style.backgroundColor = palette[index % palette.length];
			setTooltip(bar, category === "Tidak Ditentukan" ? category : `Desil ${category}`, values[index], total);
			track.append(bar);
			column.append(value, track, createElement("span", "", category === "Tidak Ditentukan" ? "N/A" : `D${category}`));
			wrapper.append(column);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderProportion = (dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const wrapper = createElement("div", "village-home__proportion-grid");

		dataset.categories.forEach((category, index) => {
			const share = total ? (values[index] / total) * 100 : 0;
			const item = createElement("div", "village-home__proportion-item");
			const ring = createElement("div", "village-home__proportion-ring");
			const ringValue = createElement("strong", "", `${numberFormat.format(Math.round(share * 10) / 10)}%`);
			ring.style.background = `conic-gradient(${palette[index % palette.length]} ${share}%, #e6eee9 ${share}% 100%)`;
			ring.append(ringValue);
			item.append(
				ring,
				createElement("h4", "", category),
				createElement("p", "", `${numberFormat.format(values[index])} keluarga`)
			);
			setTooltip(item, category, values[index], total);
			wrapper.append(item);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const updateInsight = (dataset) => {
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const maximum = Math.max(...values);
		const topIndex = values.indexOf(maximum);
		const share = total ? Math.round((maximum / total) * 1000) / 10 : 0;
		const scope = regionSelect.value === "all" ? "seluruh desa" : `Dusun ${regionSelect.value}`;

		insightValue.textContent = numberFormat.format(maximum);
		insightTitle.textContent = dataset.categories[topIndex];
		insightCopy.textContent = `${numberFormat.format(share)}% dari ${numberFormat.format(total)} ${chartConfig[activeType].unit} di ${scope}.`;
	};

	const renderChart = () => {
		const dataset = infographicData[activeType];
		const config = chartConfig[activeType];
		const values = getValues(dataset);
		const total = values.reduce((sum, value) => sum + value, 0);
		const scope = regionSelect.value === "all" ? "Semua Dusun" : `Dusun ${regionSelect.value}`;

		title.textContent = config.title;
		kicker.textContent = config.kicker;
		totalLabel.textContent = `${scope} - ${numberFormat.format(total)} ${config.unit} tercatat`;
		chart.className = `village-home__chart village-home__chart--${config.type}`;
		chart.replaceChildren();

		if (config.type === "stacked") {
			renderStacked(dataset);
		} else if (config.type === "bars") {
			renderBars(dataset);
		} else if (config.type === "lollipop") {
			renderLollipop(dataset);
		} else if (config.type === "donut") {
			renderDonut(dataset);
		} else if (config.type === "columns") {
			renderColumns(dataset);
		} else {
			renderProportion(dataset);
		}

		updateInsight(dataset);
		if (!reducedMotion) {
			requestAnimationFrame(() => chart.classList.add("is-visible"));
		} else {
			chart.classList.add("is-visible");
		}
	};

	const populateRegions = () => {
		infographicData.meta.dusun.forEach((region) => {
			const option = createElement("option", "", `Dusun ${region}`);
			option.value = region;
			regionSelect.append(option);
		});
	};

	const activateTab = (tab) => {
		activeType = tab.dataset.infographicTab;
		tabs.forEach((item) => item.setAttribute("aria-selected", String(item === tab)));
		renderChart();
	};

	tabs.forEach((tab, index) => {
		tab.addEventListener("click", () => activateTab(tab));
		tab.addEventListener("keydown", (event) => {
			if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") {
				return;
			}
			event.preventDefault();
			const direction = event.key === "ArrowRight" ? 1 : -1;
			const nextTab = tabs[(index + direction + tabs.length) % tabs.length];
			nextTab.focus();
			activateTab(nextTab);
		});
	});

	regionSelect.addEventListener("change", renderChart);

	fetch(root.dataset.infographicSource)
		.then((response) => {
			if (!response.ok) {
				throw new Error("Data infografis tidak dapat dimuat.");
			}
			return response.json();
		})
		.then((data) => {
			infographicData = data;
			populateRegions();
			renderChart();
		})
		.catch(() => {
			chart.replaceChildren(createElement("p", "village-home__chart-error", "Data infografis belum dapat ditampilkan. Silakan muat ulang halaman."));
		});
});
