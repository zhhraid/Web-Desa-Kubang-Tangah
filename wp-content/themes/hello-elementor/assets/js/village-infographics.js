document.addEventListener("DOMContentLoaded", () => {
	const root = document.querySelector("[data-infographics-page]");

	if (!root) {
		return;
	}

	document.body.classList.add("village-infographics-page-body");

	const regionSelect = root.querySelector("[data-region]");
	const regionPicker = root.querySelector("[data-region-picker]");
	const regionToggle = root.querySelector("[data-region-toggle]");
	const regionMenu = root.querySelector("[data-region-menu]");
	const sectionElements = Array.from(root.querySelectorAll("[data-section]"));
	const jumpLinks = Array.from(root.querySelectorAll("[data-jump]"));
	const viewButtons = Array.from(root.querySelectorAll("[data-stat-view]"));
	const statPanels = Array.from(root.querySelectorAll("[data-stat-panel]"));
	const graphControls = root.querySelector("[data-graph-controls]");
	const navTargets = [root.querySelector("[data-nav-section]"), ...sectionElements].filter(Boolean);
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

	const formatShare = (value, decimals = 2) =>
		`${new Intl.NumberFormat("id-ID", {
			maximumFractionDigits: decimals,
		}).format(Number(value || 0))}%`;

	const setTooltip = (element, label, value, total, unit = "") => {
		const share = total ? Math.round((value / total) * 1000) / 10 : 0;
		const suffix = unit ? ` ${unit}` : "";
		const tooltip = `${label}: ${numberFormat.format(value)}${suffix} (${numberFormat.format(share)}%)`;
		element.title = tooltip;
		element.setAttribute("aria-label", tooltip);
		element.tabIndex = 0;
	};

	const getSelectedRegion = () => (regionSelect ? regionSelect.value : "all");

	const getRegionOptionButtons = () => Array.from(root.querySelectorAll("[data-region-option]"));

	const closeRegionMenu = () => {
		if (!regionMenu || !regionToggle) {
			return;
		}

		regionMenu.hidden = true;
		regionToggle.setAttribute("aria-expanded", "false");
	};

	const setSelectedRegion = (value, label, shouldRender = true) => {
		if (regionSelect) {
			regionSelect.value = value;
		}

		if (regionToggle) {
			regionToggle.textContent = label;
		}

		getRegionOptionButtons().forEach((button) => {
			const isActive = button.dataset.regionOption === value;
			button.classList.toggle("is-active", isActive);
			button.setAttribute("aria-selected", String(isActive));
		});

		if (shouldRender) {
			renderAll();
		}
	};

	const setupRegionOption = (button) => {
		if (button.dataset.regionReady === "true") {
			return;
		}

		button.dataset.regionReady = "true";
		button.addEventListener("click", () => {
			setSelectedRegion(button.dataset.regionOption || "all", button.textContent.trim());
			closeRegionMenu();
		});
	};

	if (regionToggle && regionMenu) {
		getRegionOptionButtons().forEach(setupRegionOption);

		regionToggle.addEventListener("click", () => {
			const isOpen = regionMenu.hidden;
			regionMenu.hidden = !isOpen;
			regionToggle.setAttribute("aria-expanded", String(isOpen));
		});

		document.addEventListener("click", (event) => {
			if (regionPicker && !regionPicker.contains(event.target)) {
				closeRegionMenu();
			}
		});

		document.addEventListener("keydown", (event) => {
			if (event.key === "Escape") {
				closeRegionMenu();
			}
		});
	}

	const setupHeaderVisibility = () => {
		const update = () => {
			document.body.classList.toggle("village-infographics-header-hidden", window.scrollY > 80);
		};

		update();
		window.addEventListener("scroll", update, { passive: true });
	};

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

	const sumValues = (values) => values.reduce((sum, value) => sum + Number(value || 0), 0);

	const getProductiveStats = (dataset, pyramidValues) => {
		const values = pyramidValues || getPyramidValues(dataset);
		const male = values.male || [];
		const female = values.female || [];
		const indexes = (dataset.categories || [])
			.map((category, index) => {
				const firstAge = Number(String(category).split("-")[0].replace(/\D/g, ""));
				return Number.isFinite(firstAge) && firstAge >= 15 && firstAge <= 64 ? index : -1;
			})
			.filter((index) => index >= 0);
		const maleTotal = indexes.reduce((sum, index) => sum + Number(male[index] || 0), 0);
		const femaleTotal = indexes.reduce((sum, index) => sum + Number(female[index] || 0), 0);
		const total = maleTotal + femaleTotal;
		const populationTotal = sumValues(male) + sumValues(female);

		return {
			male: maleTotal,
			female: femaleTotal,
			total,
			share: populationTotal ? (total / populationTotal) * 100 : 0,
		};
	};

	const getCreativeData = (dataset) => {
		const selectedRegion = getSelectedRegion();
		const products = dataset.products || {};
		const byDusun = dataset.byDusun || {};
		const isRegion = selectedRegion !== "all" && byDusun.byProduct && byDusun.byProduct[selectedRegion];

		return {
			categories: products.categories || [],
			values: isRegion ? byDusun.byProduct[selectedRegion] : products.total || [],
			valueMeta: isRegion ? [] : products.value || [],
			isAll: !isRegion,
			selectedRegion,
		};
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
		const productive = getProductiveStats(dataset, values);
		note.textContent = `Usia produktif 15-64 tahun: ${numberFormat.format(productive.total || 0)} jiwa (${formatDecimal(productive.share || 0, 2)}%), terdiri dari ${numberFormat.format(productive.male || 0)} laki-laki dan ${numberFormat.format(productive.female || 0)} perempuan.`;
		chart.append(wrapper, note);
		legend.replaceChildren();
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
		const creativeData = getCreativeData(dataset);
		const categories = creativeData.categories;
		const values = creativeData.values;
		const total = sumValues(values);
		const maximum = Math.max(...values, 1);
		const wrapper = createElement("div", "village-home__creative-chart");
		const bars = createElement("div", "village-home__creative-bars");

		categories.forEach((category, index) => {
			const value = Number(values[index] || 0);
			const item = createElement("div", "village-home__creative-item");
			const track = createElement("div", "village-home__creative-track");
			const fill = createElement("span", "village-home__creative-fill");
			const label = createElement("div", "village-home__creative-label");
			fill.style.setProperty("--bar-size", `${(value / maximum) * 100}%`);
			fill.style.backgroundColor = palette[index % palette.length];
			setTooltip(fill, category, value, total, "unit usaha");
			track.append(fill);
			label.append(createElement("span", "", category));
			if (creativeData.valueMeta[index]) {
				label.append(createElement("small", "", `${formatRupiahShort(creativeData.valueMeta[index])}/tahun`));
			}
			item.append(createElement("strong", "", numberFormat.format(value)), track, label);
			bars.append(item);
		});

		const summary = createElement("aside", "village-home__creative-summary");
		const topIndex = values.indexOf(Math.max(...values, 0));
		const summaryTotal = creativeData.isAll ? dataset.summary.totalActors || total : total;
		summary.append(
			createElement("span", "", "Ringkasan Produk"),
			createElement("strong", "", numberFormat.format(summaryTotal)),
			createElement("p", "", creativeData.isAll ? `Unit usaha ekonomi kreatif dengan nilai produksi total ${formatRupiahShort(dataset.summary.totalProductionValue || 0)} per tahun.` : `Unit usaha ekonomi kreatif di Dusun ${creativeData.selectedRegion}.`),
			createElement("p", "", categories[topIndex] ? `${categories[topIndex]} menjadi produk dengan jumlah unit usaha terbanyak pada tampilan ini.` : "Data produk belum tersedia.")
		);

		if (dataset.subsectors && dataset.subsectors.categories) {
			const subsectorTotal = sumValues(dataset.subsectors.total || []);
			const subsectors = createElement("div", "village-home__creative-subsectors");
			dataset.subsectors.categories.forEach((category, index) => {
				const value = Number((dataset.subsectors.total || [])[index] || 0);
				const share = subsectorTotal ? (value / subsectorTotal) * 100 : 0;
				subsectors.append(createElement("p", "", `${category}: ${numberFormat.format(value)} unit usaha (${formatDecimal(share, 1)}%)`));
			});
			summary.append(subsectors);
		}

		wrapper.append(bars, summary);
		chart.append(wrapper);

		const capacityBands = dataset.products && dataset.products.capacityBands ? dataset.products.capacityBands : {};
		const capacityGrid = createElement("div", "village-home__capacity-grid");
		Object.keys(capacityBands).forEach((productName) => {
			const bands = capacityBands[productName] || [];
			const bandMaximum = Math.max(...bands.map((band) => Number(band.count || 0)), 1);
			const card = createElement("div", "village-home__capacity-card");
			card.append(createElement("h4", "", productName));
			bands.forEach((band) => {
				const row = createElement("div", "village-home__capacity-row");
				const track = createElement("span", "village-home__capacity-track");
				const fill = createElement("i");
				const count = Number(band.count || 0);
				fill.style.setProperty("--bar-size", `${(count / bandMaximum) * 100}%`);
				track.append(fill);
				row.append(createElement("span", "", band.label), track, createElement("strong", "", numberFormat.format(count)));
				card.append(row);
			});
			capacityGrid.append(card);
		});

		if (capacityGrid.childElementCount) {
			chart.append(capacityGrid);
		}
		renderLegend(legend, categories);
	};

	const renderHousing = (chart, legend, dataset) => {
		const groups = dataset.groups || {};
		const wrapper = createElement("div", "village-home__housing-grid");

		Object.keys(groups).forEach((groupName, groupIndex) => {
			const group = groups[groupName] || {};
			const categories = group.categories || [];
			const values = group.total || [];
			const groupTotal = sumValues(values);
			const maximum = Math.max(...values, 1);
			const card = createElement("div", "village-home__housing-card");
			const head = createElement("div", "village-home__housing-head");
			head.append(createElement("h4", "", groupName), createElement("span", "", `${numberFormat.format(groupTotal)} rumah tercatat`));
			card.append(head);

			categories.forEach((category, index) => {
				const value = Number(values[index] || 0);
				const share = groupTotal ? (value / groupTotal) * 100 : 0;
				const row = createElement("div", "village-home__housing-row");
				const track = createElement("span", "village-home__housing-track");
				const fill = createElement("i");
				fill.style.setProperty("--bar-size", `${(value / maximum) * 100}%`);
				fill.style.backgroundColor = palette[(groupIndex + index) % palette.length];
				track.append(fill);
				row.append(createElement("span", "", category), track, createElement("strong", "", numberFormat.format(value)), createElement("small", "", `${formatDecimal(share, 1)}%`));
				setTooltip(row, `${groupName} - ${category}`, value, groupTotal, "rumah");
				card.append(row);
			});

			wrapper.append(card);
		});

		chart.append(wrapper);
		legend.replaceChildren();
	};

	const renderAssets = (chart, legend, dataset) => {
		const selectedRegion = getSelectedRegion();
		const byDusun = dataset.byDusun || {};
		const categories = dataset.categories || [];
		const regions = selectedRegion === "all" ? Object.keys(byDusun) : [selectedRegion].filter((region) => byDusun[region]);
		const displayedValues = regions.reduce((items, region) => items.concat(byDusun[region] || []), []);
		const maximum = Math.max(...displayedValues, ...(dataset.total || []), 1);
		const wrapper = createElement("div", "village-home__asset-grid");

		if (selectedRegion === "all" && dataset.total) {
			const summary = createElement("div", "village-home__asset-summary");
			categories.forEach((category, index) => {
				const value = Number(dataset.total[index] || 0);
				const item = createElement("div");
				item.append(createElement("span", "", category), createElement("strong", "", numberFormat.format(value)));
				summary.append(item);
			});
			chart.append(summary);
		}

		regions.forEach((region) => {
			const values = byDusun[region] || [];
			const card = createElement("div", "village-home__asset-card");
			card.append(createElement("h4", "", region));
			categories.forEach((category, index) => {
				const value = Number(values[index] || 0);
				const row = createElement("div", "village-home__asset-row");
				const track = createElement("span", "village-home__asset-track");
				const fill = createElement("i");
				fill.style.setProperty("--bar-size", `${(value / maximum) * 100}%`);
				fill.style.backgroundColor = palette[index % palette.length];
				track.append(fill);
				row.append(createElement("span", "", category), track, createElement("strong", "", numberFormat.format(value)));
				setTooltip(row, `${category} di ${region}`, value, sumValues(values), "catatan");
				card.append(row);
			});
			wrapper.append(card);
		});

		chart.append(wrapper);
		renderLegend(legend, categories);
	};

	const revealChart = (chart) => {
		const bounds = chart.getBoundingClientRect();
		if (bounds.top < window.innerHeight * 0.92 && bounds.bottom > 0) {
			requestAnimationFrame(() => chart.classList.add("is-visible"));
		}
	};

	const updateInsight = (section, dataset, values, total, unit) => {
		if (!values.length) {
			section.querySelector("[data-insight-value]").textContent = "0";
			section.querySelector("[data-insight-title]").textContent = "Data belum tersedia";
			section.querySelector("[data-insight-copy]").textContent = "Belum ada data yang dapat diringkas.";
			return;
		}
		const maximum = Math.max(...values);
		const topIndex = values.indexOf(maximum);
		const share = total ? Math.round((maximum / total) * 1000) / 10 : 0;
		const scope = getScopeLabel(dataset).toLowerCase();
		section.querySelector("[data-insight-value]").textContent = formatShare(share);
		section.querySelector("[data-insight-title]").textContent = dataset.categories[topIndex];
		section.querySelector("[data-insight-copy]").textContent = `${numberFormat.format(maximum)} dari ${numberFormat.format(total)} ${unit} di ${scope}.`;
	};

	const updatePyramidInsight = (section, dataset, total, pyramidValues) => {
		const productive = getProductiveStats(dataset, pyramidValues);
		const scope = getSelectedRegion() === "all" ? "struktur umur desa" : `struktur umur Dusun ${getSelectedRegion()}`;
		section.querySelector("[data-insight-value]").textContent = formatShare(productive.share || 0);
		section.querySelector("[data-insight-title]").textContent = "Usia produktif 15-64 tahun";
		section.querySelector("[data-insight-copy]").textContent = `${numberFormat.format(productive.total || 0)} dari ${numberFormat.format(total)} jiwa pada ${scope}.`;
	};

	const updateCreativeInsight = (section, dataset) => {
		const creativeData = getCreativeData(dataset);
		const values = creativeData.values;
		const categories = creativeData.categories;
		const maximum = Math.max(...values, 0);
		const topIndex = values.indexOf(maximum);
		const total = sumValues(values);
		const share = total ? (maximum / total) * 100 : 0;
		const scope = creativeData.isAll ? "seluruh desa" : `Dusun ${creativeData.selectedRegion}`;
		section.querySelector("[data-insight-value]").textContent = formatShare(share);
		section.querySelector("[data-insight-title]").textContent = categories[topIndex];
		section.querySelector("[data-insight-copy]").textContent = `${numberFormat.format(maximum)} dari ${numberFormat.format(total)} unit usaha ekonomi kreatif di ${scope}.`;
	};

	const updateHousingInsight = (section, dataset) => {
		const groups = dataset.groups || {};
		const rows = [];
		Object.keys(groups).forEach((groupName) => {
			const group = groups[groupName] || {};
			(group.categories || []).forEach((category, index) => {
				rows.push({
					groupName,
					category,
					value: Number((group.total || [])[index] || 0),
					total: sumValues(group.total || []),
				});
			});
		});
		const top = rows.sort((first, second) => second.value - first.value)[0] || { groupName: "Rumah", category: "Data belum tersedia", value: 0, total: 0 };
		const share = top.total ? (top.value / top.total) * 100 : 0;
		section.querySelector("[data-insight-value]").textContent = formatShare(share);
		section.querySelector("[data-insight-title]").textContent = `${top.groupName}: ${top.category}`;
		section.querySelector("[data-insight-copy]").textContent = `${numberFormat.format(top.value)} dari ${numberFormat.format(top.total)} rumah pada data ${top.groupName.toLowerCase()}.`;
	};

	const updateAssetsInsight = (section, dataset) => {
		const selectedRegion = getSelectedRegion();
		const values = selectedRegion === "all" ? dataset.total || [] : dataset.byDusun && dataset.byDusun[selectedRegion] ? dataset.byDusun[selectedRegion] : [];
		const total = sumValues(values);
		const maximum = Math.max(...values, 0);
		const topIndex = values.indexOf(maximum);
		const share = total ? (maximum / total) * 100 : 0;
		const scope = selectedRegion === "all" ? "seluruh desa" : `Dusun ${selectedRegion}`;
		section.querySelector("[data-insight-value]").textContent = formatShare(share);
		section.querySelector("[data-insight-title]").textContent = (dataset.categories || [])[topIndex] || "Data aset";
		section.querySelector("[data-insight-copy]").textContent = `${numberFormat.format(maximum)} dari ${numberFormat.format(total)} catatan aset pada ${scope}.`;
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
			const total = sumValues([...(pyramidValues.male || []), ...(pyramidValues.female || [])]);
			section.querySelector("[data-total]").textContent = `${getSelectedRegion() === "all" ? "Seluruh Desa" : `Dusun ${getSelectedRegion()}`} - ${numberFormat.format(total)} ${unit}`;
			renderPyramid(chart, legend, dataset, unit);
			updatePyramidInsight(section, dataset, total, pyramidValues);
		} else if (type === "creative") {
			const creativeData = getCreativeData(dataset);
			const total = sumValues(creativeData.values);
			section.querySelector("[data-total]").textContent = `${getSelectedRegion() === "all" ? "Seluruh Desa" : `Dusun ${getSelectedRegion()}`} - ${numberFormat.format(total)} unit usaha`;
			renderCreative(chart, legend, dataset);
			updateCreativeInsight(section, dataset);
		} else if (type === "housing") {
			const houses = Number(dataset.houses || 0);
			section.querySelector("[data-total]").textContent = `${numberFormat.format(houses)} rumah tercatat`;
			renderHousing(chart, legend, dataset);
			updateHousingInsight(section, dataset);
		} else if (type === "assets") {
			const selectedRegion = getSelectedRegion();
			const values = selectedRegion === "all" ? dataset.total || [] : dataset.byDusun && dataset.byDusun[selectedRegion] ? dataset.byDusun[selectedRegion] : [];
			const total = sumValues(values);
			const scope = selectedRegion === "all" ? "Seluruh Desa" : `Dusun ${selectedRegion}`;
			section.querySelector("[data-total]").textContent = `${scope} - ${numberFormat.format(total)} ${unit}`;
			renderAssets(chart, legend, dataset);
			updateAssetsInsight(section, dataset);
		} else {
			const values = getValues(dataset);
			const total = sumValues(values);
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

	const setActiveView = (view, shouldUpdateHash = false) => {
		const activeView = view === "infografis" ? "infografis" : "grafik";

		viewButtons.forEach((button) => {
			const isActive = button.dataset.statView === activeView;
			button.classList.toggle("is-active", isActive);
			button.setAttribute("aria-selected", String(isActive));
		});

		statPanels.forEach((panel) => {
			const isActive = panel.dataset.statPanel === activeView;
			panel.hidden = !isActive;
			panel.classList.toggle("is-active", isActive);
		});

		if (graphControls) {
			graphControls.hidden = activeView !== "grafik";
		}

		if (activeView === "grafik") {
			renderAll();
		}

		if (shouldUpdateHash) {
			window.history.replaceState(null, "", activeView === "infografis" ? "#infografis" : "#grafik");
		}
	};

	const setActiveJump = (key) => {
		jumpLinks.forEach((link) => link.classList.toggle("is-active", link.dataset.jump === key));
	};

	viewButtons.forEach((button) => {
		button.addEventListener("click", () => {
			setActiveView(button.dataset.statView, true);
		});
	});

	jumpLinks.forEach((link) => {
		link.addEventListener("click", (event) => {
			event.preventDefault();
			const targetId = link.dataset.jump === "all" ? "ringkasan" : link.dataset.jump;
			const target = root.querySelector(`#${targetId}`);
			if (!target) {
				return;
			}
			setActiveView("grafik");
			setActiveJump(link.dataset.jump);
			target.scrollIntoView({ behavior: reducedMotion ? "auto" : "smooth", block: "start" });
			window.history.replaceState(null, "", `#${link.dataset.jump}`);
		});
	});

	const setupInfographicCarousel = () => {
		const track = root.querySelector("[data-infographic-track]");
		const openButtons = Array.from(root.querySelectorAll("[data-infographic-open]"));
		const dialog = root.querySelector("[data-infographic-dialog]");
		const dialogImage = root.querySelector("[data-infographic-dialog-image]");
		const dialogTitle = root.querySelector("[data-infographic-dialog-title]");
		const closeButton = root.querySelector("[data-infographic-close]");

		if (!track) {
			return;
		}

		const closeDialog = () => {
			if (!dialog) {
				return;
			}
			if (typeof dialog.close === "function" && dialog.open) {
				dialog.close();
			} else {
				dialog.removeAttribute("open");
			}
			dialog.hidden = true;
			dialog.classList.remove("is-open");
		};

		const openDialog = (button) => {
			if (!dialog || !dialogImage) {
				return;
			}
			dialogImage.src = button.dataset.fullImage || "";
			dialogImage.alt = button.dataset.fullAlt || button.dataset.fullTitle || "Infografis Desa Kubang Tangah";
			if (dialogTitle) {
				dialogTitle.textContent = button.dataset.fullTitle || "";
			}
			dialog.hidden = false;
			dialog.classList.add("is-open");
			if (typeof dialog.showModal === "function" && !dialog.open) {
				dialog.showModal();
			} else {
				dialog.setAttribute("open", "");
			}
		};

		openButtons.forEach((button) => {
			button.addEventListener("click", () => openDialog(button));
		});

		closeButton?.addEventListener("click", closeDialog);
		dialog?.addEventListener("click", (event) => {
			if (event.target === dialog) {
				closeDialog();
			}
		});
		document.addEventListener("keydown", (event) => {
			if (event.key === "Escape") {
				closeDialog();
			}
		});
	};

	if (regionSelect) {
		regionSelect.addEventListener("change", () => {
			const selected = regionSelect.options[regionSelect.selectedIndex];
			setSelectedRegion(regionSelect.value, selected ? selected.textContent : "Semua Dusun");
		});
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

	setupHeaderVisibility();
	setupInfographicCarousel();

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

					if (regionMenu) {
						const button = createElement("button", "", `Dusun ${region}`);
						button.type = "button";
						button.dataset.regionOption = region;
						button.setAttribute("role", "option");
						button.setAttribute("aria-selected", "false");
						regionMenu.append(button);
						setupRegionOption(button);
					}
				});
			}
			const initialHash = window.location.hash.replace("#", "");
			const initialSection = !initialHash || initialHash === "ringkasan" || initialHash === "grafik" || initialHash === "infografis" ? "all" : initialHash;
			setActiveJump(initialSection);
			renderAll();
			setSelectedRegion(getSelectedRegion(), regionToggle ? regionToggle.textContent.trim() : "Semua Dusun", false);
			setActiveView(initialHash === "infografis" ? "infografis" : "grafik");
		})
		.catch(() => {
			sectionElements.forEach((section) => {
				const chart = section.querySelector("[data-chart]");
				chart.replaceChildren(createElement("p", "village-home__chart-error", "Data statistik belum dapat ditampilkan. Silakan muat ulang halaman."));
			});
		});
});
