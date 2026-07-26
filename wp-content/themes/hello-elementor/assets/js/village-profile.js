(function () {
	'use strict';

	const page = document.querySelector('.village-profile');
	if (!page) {
		return;
	}

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const sectionLinks = Array.from(page.querySelectorAll('.village-profile__section-nav a'));
	const profileSections = Array.from(page.querySelectorAll('[data-profile-section]'));
	const sectionNav = page.querySelector('[data-profile-tabs]');
	let map = null;
	let villageBounds = null;

	const updateHeaderVisibility = function () {
		document.body.classList.toggle('village-profile-header-hidden', window.scrollY > 80);
	};

	updateHeaderVisibility();
	window.addEventListener('scroll', updateHeaderVisibility, { passive: true });

	if (!prefersReducedMotion) {
		document.documentElement.classList.add('profile-motion-ready');
	}

	const activateProfileSection = function (targetId, shouldScroll) {
		let activeSection = null;

		profileSections.forEach(function (section) {
			const active = section.id === targetId;
			section.hidden = !active;
			section.classList.toggle('is-visible', active);

			if (active) {
				activeSection = section;
			}
		});

		sectionLinks.forEach(function (link) {
			link.classList.toggle('is-active', link.getAttribute('href') === '#' + targetId);
		});

		if (shouldScroll && sectionNav) {
			sectionNav.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
		}

		if (targetId === 'peta-desa' && map) {
			window.setTimeout(function () {
				map.invalidateSize();
				if (villageBounds && villageBounds.isValid()) {
					map.fitBounds(villageBounds, { animate: false, padding: [32, 32] });
				}
			}, 80);
		}

		return activeSection;
	};

	const validInitialTarget = profileSections.some(function (section) {
		return '#' + section.id === window.location.hash;
	});
	const initialTarget = validInitialTarget ? window.location.hash.substring(1) : (profileSections[0] ? profileSections[0].id : '');
	if (initialTarget) {
		activateProfileSection(initialTarget, false);
	}

	sectionLinks.forEach(function (link) {
		link.addEventListener('click', function (event) {
			const targetId = link.getAttribute('href').replace('#', '');
			event.preventDefault();
			activateProfileSection(targetId, true);

			if (window.history && window.history.replaceState) {
				window.history.replaceState(null, '', '#' + targetId);
			}
		});
	});

	const countElements = Array.from(page.querySelectorAll('[data-profile-count]'));
	if (countElements.length) {
		const completeCount = function (element) {
			const value = Number(element.dataset.profileCount || 0);
			const decimals = Number(element.dataset.profileDecimals || 0);
			element.firstChild.nodeValue = value.toLocaleString('id-ID', {
				minimumFractionDigits: decimals,
				maximumFractionDigits: decimals
			}) + ' ';
		};

		if (prefersReducedMotion || !('IntersectionObserver' in window)) {
			countElements.forEach(completeCount);
		} else {
			const countObserver = new IntersectionObserver(function (entries, observer) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					const element = entry.target;
					const target = Number(element.dataset.profileCount || 0);
					const decimals = Number(element.dataset.profileDecimals || 0);
					const duration = 1000;
					const start = performance.now();

					const step = function (now) {
						const progress = Math.min((now - start) / duration, 1);
						const eased = 1 - Math.pow(1 - progress, 3);
						const current = target * eased;
						element.firstChild.nodeValue = current.toLocaleString('id-ID', {
							minimumFractionDigits: decimals,
							maximumFractionDigits: decimals
						}) + ' ';

						if (progress < 1) {
							window.requestAnimationFrame(step);
						} else {
							completeCount(element);
						}
					};

					window.requestAnimationFrame(step);
					observer.unobserve(element);
				});
			}, { threshold: 0.55 });

			countElements.forEach(function (element) {
				const unit = element.querySelector('small');
				element.textContent = '0 ';
				if (unit) {
					element.appendChild(unit);
				}
				countObserver.observe(element);
			});
		}
	}

	const mapElement = page.querySelector('#village-boundary-map');
	const mapStatus = page.querySelector('[data-map-status]');
	const focusButton = page.querySelector('[data-map-focus]');
	const mapTypeButtons = Array.from(page.querySelectorAll('[data-map-type]'));
	const tabButtons = Array.from(page.querySelectorAll('[data-map-tab]'));
	const tabPanels = Array.from(page.querySelectorAll('[data-map-panel]'));

	const showMapStatus = function (message, isError) {
		if (!mapStatus) {
			return;
		}

		mapStatus.textContent = message;
		mapStatus.classList.toggle('is-error', Boolean(isError));
		mapStatus.classList.remove('is-hidden');
	};

	const hideMapStatus = function () {
		if (mapStatus) {
			mapStatus.classList.add('is-hidden');
		}
	};

	const featureStyle = function (feature) {
		if (feature.properties && feature.properties.boundaryType === 'desa') {
			return {
				color: '#123b2d',
				fillColor: '#3aa66d',
				fillOpacity: 0.16,
				opacity: 1,
				weight: 3.5
			};
		}

		return {
			color: '#d64634',
			dashArray: '10 8',
			opacity: 0.98,
			weight: 3.2
		};
	};

	const formatNumber = function (value) {
		return Number(value || 0).toLocaleString('id-ID');
	};

	const buildDistrictTooltip = function (district) {
		return '<div class="village-map-tooltip">' +
			'<strong>' + district.name + '</strong>' +
			'<span>Total ' + formatNumber(district.total) + ' jiwa</span>' +
			'<dl><div><dt>Pria</dt><dd>' + formatNumber(district.male) + '</dd></div>' +
			'<div><dt>Wanita</dt><dd>' + formatNumber(district.female) + '</dd></div></dl>' +
			'<em>Sarana: ' + district.facilities.join(', ') + '</em>' +
			'</div>';
	};

	const buildFacilityPopup = function (facility) {
		return '<div class="village-map-popup">' +
			'<span>' + facility.type + '</span>' +
			'<strong>' + facility.name + '</strong>' +
			'<p>Dusun ' + facility.district + '</p>' +
			'</div>';
	};

	const createFacilityIcon = function (facility) {
		return window.L.divIcon({
			className: 'village-map-marker',
			html: '<span>' + facility.code + '</span>',
			iconSize: [38, 38],
			iconAnchor: [19, 19],
			popupAnchor: [0, -18]
		});
	};

	if (mapElement && window.L) {
		mapElement.setAttribute('aria-busy', 'true');
		map = window.L.map(mapElement, {
			attributionControl: true,
			doubleClickZoom: true,
			scrollWheelZoom: true,
			touchZoom: true,
			zoomControl: true
		});

		const baseLayers = {
			standard: window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; OpenStreetMap contributors',
				maxZoom: 19
			}),
			satellite: window.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
				attribution: 'Tiles &copy; Esri',
				maxZoom: 19
			}),
			terrain: window.L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; OpenTopoMap contributors',
				maxZoom: 17
			})
		};
		let activeBaseLayer = baseLayers.standard.addTo(map);

		mapTypeButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				const type = button.dataset.mapType;
				if (!baseLayers[type] || activeBaseLayer === baseLayers[type]) {
					return;
				}

				map.removeLayer(activeBaseLayer);
				activeBaseLayer = baseLayers[type].addTo(map);

				mapTypeButtons.forEach(function (item) {
					const active = item === button;
					item.classList.toggle('is-active', active);
					item.setAttribute('aria-pressed', String(active));
				});
			});
		});

		Promise.all([
			fetch(mapElement.dataset.geojsonUrl, { credentials: 'same-origin' }).then(function (response) {
				if (!response.ok) {
					throw new Error('Data peta tidak dapat dimuat.');
				}
				return response.json();
			}),
			fetch(mapElement.dataset.mapDataUrl, { credentials: 'same-origin' }).then(function (response) {
				if (!response.ok) {
					throw new Error('Data pendukung peta tidak dapat dimuat.');
				}
				return response.json();
			})
		])
			.then(function (results) {
				const geojson = results[0];
				const mapData = results[1];
				const districtLayer = window.L.layerGroup().addTo(map);
				const facilityLayer = window.L.layerGroup().addTo(map);

				const boundaryLayer = window.L.geoJSON(geojson, {
					style: featureStyle,
					onEachFeature: function (feature, layer) {
						const label = feature.properties && feature.properties.name ? feature.properties.name : 'Batas wilayah';
						layer.bindTooltip(label, { sticky: true, className: 'village-map-line-tooltip' });

						layer.on('mouseover', function () {
							const style = featureStyle(feature);
							layer.setStyle({ weight: style.weight + 1.8, fillOpacity: style.fillOpacity ? 0.27 : undefined });
						});

						layer.on('mouseout', function () {
							layer.setStyle(featureStyle(feature));
						});
					}
				}).addTo(map);

				(mapData.districts || []).forEach(function (district) {
					const districtCircle = window.L.circle(district.center, {
						className: 'village-map-district-zone',
						color: '#ffffff',
						dashArray: '1 10',
						fillColor: '#1f8a55',
						fillOpacity: 0.13,
						opacity: 0.42,
						radius: district.radius,
						weight: 1.6
					}).addTo(districtLayer);

					districtCircle.bindTooltip(buildDistrictTooltip(district), {
						className: 'village-map-district-tooltip',
						direction: 'top',
						opacity: 1,
						sticky: true
					});

					districtCircle.on('mouseover', function () {
						districtCircle.setStyle({
							fillOpacity: 0.24,
							opacity: 0.8,
							weight: 2.4
						});
					});

					districtCircle.on('mouseout', function () {
						districtCircle.setStyle({
							fillOpacity: 0.13,
							opacity: 0.42,
							weight: 1.6
						});
					});

					window.L.marker(district.center, {
						icon: window.L.divIcon({
							className: 'village-map-district-label',
							html: '<strong>' + district.name + '</strong><span>' + formatNumber(district.total) + ' jiwa</span>',
							iconAnchor: [58, 18]
						}),
						interactive: false
					}).addTo(districtLayer);
				});

				(mapData.facilities || []).forEach(function (facility) {
					const marker = window.L.marker(facility.position, {
						icon: createFacilityIcon(facility),
						title: facility.name
					}).addTo(facilityLayer);

					marker.bindTooltip(facility.name, {
						className: 'village-map-facility-tooltip',
						direction: 'top',
						offset: [0, -14]
					});
					marker.bindPopup(buildFacilityPopup(facility), {
						className: 'village-map-popup-shell',
						maxWidth: 250
					});
				});

				villageBounds = boundaryLayer.getBounds();
				if (villageBounds.isValid()) {
					map.fitBounds(villageBounds, { padding: [32, 32] });
					const center = villageBounds.getCenter();
					window.L.circleMarker(center, {
						color: '#ffffff',
						fillColor: '#176c43',
						fillOpacity: 1,
						radius: 8,
						weight: 3
					})
						.addTo(map)
						.bindTooltip('Desa Kubang Tangah', { direction: 'top', permanent: true, offset: [0, -8] });
				}

				mapElement.setAttribute('aria-busy', 'false');
				hideMapStatus();
			})
			.catch(function () {
				mapElement.setAttribute('aria-busy', 'false');
				showMapStatus('Peta interaktif belum dapat dimuat. Periksa data GeoJSON dan data pendukung peta.', true);
			});
	} else if (mapElement) {
		showMapStatus('Peta interaktif tidak tersedia pada peramban ini.', true);
	}

	if (focusButton) {
		focusButton.addEventListener('click', function () {
			if (map && villageBounds && villageBounds.isValid()) {
				map.fitBounds(villageBounds, { animate: !prefersReducedMotion, padding: [32, 32] });
			}
		});
	}

	const activateMapTab = function (button) {
		const target = button.dataset.mapTab;
		tabButtons.forEach(function (tab) {
			const active = tab === button;
			tab.classList.toggle('is-active', active);
			tab.setAttribute('aria-selected', String(active));
			tab.setAttribute('tabindex', active ? '0' : '-1');
		});

		tabPanels.forEach(function (panel) {
			const active = panel.dataset.mapPanel === target;
			panel.classList.toggle('is-active', active);
			panel.hidden = !active;
		});

		if (target === 'interactive' && map) {
			window.setTimeout(function () {
				map.invalidateSize();
				if (villageBounds && villageBounds.isValid()) {
					map.fitBounds(villageBounds, { animate: false, padding: [32, 32] });
				}
			}, 40);
		}
	};

	tabButtons.forEach(function (button, index) {
		button.addEventListener('click', function () {
			activateMapTab(button);
		});

		button.addEventListener('keydown', function (event) {
			if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
				return;
			}

			event.preventDefault();
			const direction = event.key === 'ArrowRight' ? 1 : -1;
			const nextIndex = (index + direction + tabButtons.length) % tabButtons.length;
			tabButtons[nextIndex].focus();
			activateMapTab(tabButtons[nextIndex]);
		});
	});

	const mapDialog = page.querySelector('[data-map-dialog]');
	const expandMapButton = page.querySelector('[data-map-expand]');
	const closeMapButton = page.querySelector('[data-map-close]');

	if (expandMapButton && mapDialog) {
		expandMapButton.addEventListener('click', function () {
			if (typeof mapDialog.showModal === 'function') {
				mapDialog.showModal();
			}
		});
	}

	if (closeMapButton && mapDialog) {
		closeMapButton.addEventListener('click', function () {
			mapDialog.close();
		});
	}

	if (mapDialog) {
		mapDialog.addEventListener('click', function (event) {
			if (event.target === mapDialog) {
				mapDialog.close();
			}
		});
	}
}());
