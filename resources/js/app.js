import './bootstrap';
import { initIcons } from './icons';

// Initialize icons on all pages
document.addEventListener('DOMContentLoaded', () => initIcons());

const snapshotElement = document.getElementById('snapshot-data');

if (snapshotElement) {
	let snapshot = JSON.parse(snapshotElement.textContent || '{}');

	const toast = document.getElementById('toast');
	const usVariantSelect = document.getElementById('usVariantSelect');
	const sjcVariantSelect = document.getElementById('sjcVariantSelect');
	const sjcBrandVariantSelect = document.getElementById('sjcBrandVariantSelect');
	const btmcVariantSelect = document.getElementById('btmcVariantSelect');
	const pnjVariantSelect = document.getElementById('pnjVariantSelect');
	const dojiVariantSelect = document.getElementById('dojiVariantSelect');
	const phuquyVariantSelect = document.getElementById('phuquyVariantSelect');
	const mihongVariantSelect = document.getElementById('mihongVariantSelect');
	const btmhVariantSelect = document.getElementById('btmhVariantSelect');
	const ngocthamVariantSelect = document.getElementById('ngocthamVariantSelect');

	const rebuildSelect = (selectEl, variants, selectedKey) => {
		if (!selectEl) return;
		selectEl.innerHTML = '';
		Object.entries(variants).forEach(([key, v]) => {
			const opt = document.createElement('option');
			opt.value = key;
			opt.textContent = v.label;
			if (key === selectedKey) opt.selected = true;
			selectEl.appendChild(opt);
		});
	};

	const showToast = (message, isError = false) => {
		if (!toast) {
			return;
		}

		toast.textContent = message;
		toast.style.background = isError ? '#b91c1c' : '#1e293b';
		toast.style.transform = 'translateY(0)';

		window.setTimeout(() => {
			toast.style.transform = '';
		}, 2300);
	};

	const numberFormat = (number) => new Intl.NumberFormat('vi-VN').format(number);

	const formatVndDeltaLabel = (changePercent, sellPrice) => {
		const amount = Math.round(((changePercent || 0) / 100) * (sellPrice || 0));
		if (amount === 0) return 'Không đổi hôm nay';
		const sign = amount > 0 ? '+' : '-';
		return `${sign} ${numberFormat(Math.abs(amount))}đ hôm nay`;
	};

	const applyDayChangeColor = (el, label) => {
		if (!el) return;
		el.textContent = label;
		if (label.startsWith('-')) {
			el.style.color = '#e7000b';
		} else if (label.startsWith('+')) {
			el.style.color = '#008236';
		} else {
			el.style.color = '#666';
		}
	};

	// Shared tooltip element
	const chartTooltip = (() => {
		const el = document.createElement('div');
		el.style.cssText = 'position:fixed;pointer-events:none;opacity:0;transition:opacity .15s;z-index:9999;background:#1e293b;color:#fff;font-size:12px;padding:4px 10px;border-radius:6px;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,.18)';
		document.body.appendChild(el);
		return el;
	})();

	const showTooltip = (evt, html) => {
		chartTooltip.innerHTML = html;
		chartTooltip.style.opacity = '1';
		const rect = chartTooltip.getBoundingClientRect();
		chartTooltip.style.left = Math.min(evt.clientX + 12, window.innerWidth - rect.width - 8) + 'px';
		chartTooltip.style.top = (evt.clientY - rect.height - 10) + 'px';
	};
	const hideTooltip = () => { chartTooltip.style.opacity = '0'; };

	const addHoverHitArea = (svg, points, step, mapY, formatter, dates) => {
		const width = svg.viewBox.baseVal.width || 500;
		const height = svg.viewBox.baseVal.height || 80;
		const slotW = Math.max(width / points.length, 4);
		points.forEach((val, i) => {
			const cx = i * step;
			// vertical guide line
			const guide = document.createElementNS('http://www.w3.org/2000/svg', 'line');
			guide.setAttribute('x1', cx); guide.setAttribute('y1', 0); guide.setAttribute('x2', cx); guide.setAttribute('y2', height);
			guide.setAttribute('stroke', '#94a3b8'); guide.setAttribute('stroke-width', '1'); guide.setAttribute('stroke-dasharray', '3 2');
			guide.style.opacity = '0';
			// invisible hit rect
			const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
			rect.setAttribute('x', cx - slotW / 2); rect.setAttribute('y', 0);
			rect.setAttribute('width', slotW); rect.setAttribute('height', height);
			rect.setAttribute('fill', 'transparent');
			const dateStr = dates && dates[i] ? dates[i] : '';
			const show = (e) => { guide.style.opacity = '1'; showTooltip(e, formatter(val, i, dateStr)); };
			const hide = () => { guide.style.opacity = '0'; hideTooltip(); };
			rect.addEventListener('mouseenter', show);
			rect.addEventListener('mousemove', (e) => showTooltip(e, formatter(val, i, dateStr)));
			rect.addEventListener('mouseleave', hide);
			svg.appendChild(guide);
			svg.appendChild(rect);
		});
	};

	const addDateLabels = (svg, dates) => {
		if (!dates || dates.length < 2) return;
		let container = svg.parentElement.querySelector('.chart-date-labels');
		if (!container) {
			container = document.createElement('div');
			container.className = 'chart-date-labels';
			container.style.cssText = 'display:flex;justify-content:space-between;font-size:10px;color:#94a3b8;margin-top:2px;';
			svg.parentElement.appendChild(container);
		}
		const first = dates[0];
		const last = dates[dates.length - 1];
		const mid = dates[Math.floor(dates.length / 2)];
		container.innerHTML = `<span>${first}</span><span>${mid}</span><span>${last}</span>`;
	};

	const drawLine = (svg, points, color, dates, valueFmt) => {
		if (!svg || !points?.length) {
			return;
		}

		const width = svg.viewBox.baseVal.width || 500;
		const height = svg.viewBox.baseVal.height || 80;
		const max = Math.max(...points);
		const min = Math.min(...points);
		const step = points.length > 1 ? width / (points.length - 1) : width;

		const mapY = (value) => {
			if (max === min) {
				return height / 2;
			}

			return ((max - value) / (max - min)) * (height - 10) + 5;
		};

		const line = points
			.map((value, index) => `${(index * step).toFixed(1)},${mapY(value).toFixed(1)}`)
			.join(' ');

		const fmt = valueFmt || ((v) => `$${v.toFixed(2)}`);
		svg.innerHTML = `<polyline fill="none" stroke="${color}" stroke-width="1" points="${line}" />`;
		addHoverHitArea(svg, points, step, mapY, (v, i, d) => `${d ? `<span style="color:#94a3b8">${d}</span> ` : ''}${fmt(v)}`, dates);
		addDateLabels(svg, dates);
	};

	const drawDualLine = (svg, sellPoints, buyPoints, sellColor = '#15803d', buyColor = '#dc2626', dates = []) => {
		if (!svg || sellPoints.length < 2 || buyPoints.length < 2) {
			if (svg) svg.innerHTML = '';
			return;
		}

		const width = svg.viewBox.baseVal.width || 500;
		const height = svg.viewBox.baseVal.height || 80;
		const all = [...sellPoints, ...buyPoints];
		const max = Math.max(...all);
		const min = Math.min(...all);
		const len = Math.min(sellPoints.length, buyPoints.length);
		const step = len > 1 ? width / (len - 1) : width;

		const mapY = (value) => {
			if (max === min) return height / 2;
			return ((max - value) / (max - min)) * (height - 10) + 5;
		};

		const sellLine = sellPoints.slice(0, len).map((value, index) => `${(index * step).toFixed(1)},${mapY(value).toFixed(1)}`).join(' ');
		const buyLine = buyPoints.slice(0, len).map((value, index) => `${(index * step).toFixed(1)},${mapY(value).toFixed(1)}`).join(' ');

		svg.innerHTML = `
			<polyline fill="none" stroke="${sellColor}" stroke-width="1" points="${sellLine}" />
			<polyline fill="none" stroke="${buyColor}" stroke-width="1" stroke-dasharray="6 4" points="${buyLine}" />
		`;

		// Add combined hover hit areas showing both sell & buy
		const slotW = Math.max(width / len, 4);
		for (let i = 0; i < len; i++) {
			const cx = i * step;
			const sVal = sellPoints[i], bVal = buyPoints[i];
			// vertical guide line
			const guide = document.createElementNS('http://www.w3.org/2000/svg', 'line');
			guide.setAttribute('x1', cx); guide.setAttribute('y1', 0); guide.setAttribute('x2', cx); guide.setAttribute('y2', height);
			guide.setAttribute('stroke', '#94a3b8'); guide.setAttribute('stroke-width', '1'); guide.setAttribute('stroke-dasharray', '3 2');
			guide.style.opacity = '0';
			// hit rect
			const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
			rect.setAttribute('x', cx - slotW / 2); rect.setAttribute('y', 0);
			rect.setAttribute('width', slotW); rect.setAttribute('height', height);
			rect.setAttribute('fill', 'transparent');
			const dateStr = dates && dates[i] ? `<span style="color:#94a3b8">${dates[i]}</span> ` : '';
			const html = `${dateStr}<span style="color:${sellColor}">Bán: ${sVal.toFixed(2)}</span> · <span style="color:${buyColor}">Mua: ${bVal.toFixed(2)}</span>`;
			const show = (e) => { guide.style.opacity = '1'; showTooltip(e, html); };
			const hide = () => { guide.style.opacity = '0'; hideTooltip(); };
			rect.addEventListener('mouseenter', show);
			rect.addEventListener('mousemove', (e) => showTooltip(e, html));
			rect.addEventListener('mouseleave', hide);
			svg.appendChild(guide); svg.appendChild(rect);
		}
		addDateLabels(svg, dates);
	};

	const getSeries = (card) => {
		const sellPoints = Array.isArray(card?.weekSellPoints)
			? card.weekSellPoints
			: (Array.isArray(card?.weekPoints) ? card.weekPoints : []);
		const buyPoints = Array.isArray(card?.weekBuyPoints) ? card.weekBuyPoints : [];
		const dates = Array.isArray(card?.weekDates) ? card.weekDates : [];
		return { sellPoints, buyPoints, dates };
	};

	const renderBuySellMiniChart = (card, pointsId, chartId, sellColor, buyColor = '#2563eb') => {
		const { sellPoints, buyPoints, dates } = getSeries(card);
		const pointsTextEl = document.getElementById(pointsId);
		const chartEl = document.getElementById(chartId);

		if (sellPoints.length < 2 || buyPoints.length < 2) {
			if (pointsTextEl) pointsTextEl.textContent = 'Chưa đủ dữ liệu biểu đồ';
			if (chartEl) chartEl.innerHTML = '';
			return;
		}

		if (pointsTextEl) {
			const sellLast = sellPoints[sellPoints.length - 1];
			const buyLast = buyPoints[buyPoints.length - 1];
			pointsTextEl.textContent = `Bán: ${sellLast.toFixed(2)} | Mua: ${buyLast.toFixed(2)} (triệu)`;
		}

		drawDualLine(chartEl, sellPoints, buyPoints, sellColor, buyColor, dates);
	};

	let chart24hRoot = null;
	const drawMainChart = (retries = 0) => {
		const holder = document.getElementById('chart24hAmChart');
		const data = snapshot.chart24h?.series;

		if (!holder) return;
		if (!window.am5 || !window.am5xy || !window.am5themes_Animated) {
			if (retries < 30) {
				setTimeout(() => drawMainChart(retries + 1), 200);
			}
			return;
		}

		if (!data || !data.length) {
			holder.innerHTML = '<div class="grid h-full place-items-center text-sm font-semibold text-slate-500">Chưa có dữ liệu biểu đồ 30 ngày.</div>';
			return;
		}

		if (chart24hRoot) {
			chart24hRoot.dispose();
			chart24hRoot = null;
		}

		chart24hRoot = window.am5.Root.new('chart24hAmChart');
		chart24hRoot._logo?.dispose();
		chart24hRoot.setThemes([window.am5themes_Animated.new(chart24hRoot)]);

		const chart = chart24hRoot.container.children.push(
			window.am5xy.XYChart.new(chart24hRoot, {
				panX: true,
				panY: false,
				wheelX: 'panX',
				wheelY: 'zoomX',
				layout: chart24hRoot.verticalLayout,
			})
		);

		const xAxis = chart.xAxes.push(
			window.am5xy.CategoryAxis.new(chart24hRoot, {
				categoryField: 'time',
				renderer: window.am5xy.AxisRendererX.new(chart24hRoot, { minGridDistance: 60 }),
			})
		);
		xAxis.get('renderer').labels.template.setAll({ fontSize: 10 });
		xAxis.data.setAll(data);

		const yAxis = chart.yAxes.push(
			window.am5xy.ValueAxis.new(chart24hRoot, {
				renderer: window.am5xy.AxisRendererY.new(chart24hRoot, {}),
				numberFormat: "#.## 'tr'",
				extraMin: 0.01,
				extraMax: 0.01,
			})
		);
		yAxis.get('renderer').labels.template.setAll({ fontSize: 10 });

		const cursor = chart.set(
			'cursor',
			window.am5xy.XYCursor.new(chart24hRoot, { behavior: 'zoomX', xAxis })
		);
		cursor.lineY.set('visible', false);

		const brandConfig = [
			{ key: 'sjc',      name: 'SJC',       color: 0x2563eb },
			{ key: 'btmc',     name: 'BTMC',      color: 0x8b5cf6 },
			{ key: 'doji',     name: 'DOJI',      color: 0xf59e0b },
			{ key: 'pnj',      name: 'PNJ',       color: 0x22c55e },
			{ key: 'phuquy',   name: 'Phú Quý',   color: 0xec4899 },
			{ key: 'mihong',   name: 'Mi Hồng',   color: 0x06b6d4 },
			{ key: 'btmh',     name: 'BTMH',      color: 0xef4444 },
			{ key: 'ngoctham', name: 'Ngọc Thẩm', color: 0xa855f7 },
			{ key: 'world',    name: 'Thế giới',  color: 0xb8860b },
		];

		const tooltipText = brandConfig.map(b =>
			`[${am5.color(b.color).toCSSHex()}]${b.name}: {${b.key}} tr[/]`
		).join('\n');

		let firstSeries = null;
		brandConfig.forEach((b, idx) => {
			const hasData = data.some(d => d[b.key] != null);
			if (!hasData) return;

			const series = chart.series.push(
				window.am5xy.LineSeries.new(chart24hRoot, {
					name: b.name,
					xAxis,
					yAxis,
					valueYField: b.key,
					categoryXField: 'time',
					stroke: window.am5.color(b.color),
					fill: window.am5.color(b.color),
					connect: true,
					tooltip: !firstSeries ? window.am5.Tooltip.new(chart24hRoot, {
						labelText: `[bold]{categoryX}[/]\n${tooltipText}`,
						pointerOrientation: 'horizontal',
						getFillFromSprite: false,
						getStrokeFromSprite: false,
					}) : undefined,
				})
			);

			series.strokes.template.setAll({ strokeWidth: 1 });

			if (b.key === 'world') {
				series.strokes.template.setAll({ strokeDasharray: [6, 4] });
			}

			if (!firstSeries) {
				firstSeries = series;
				series.get('tooltip').get('background').setAll({
					fill: window.am5.color(0x000000),
					fillOpacity: 0.9,
					stroke: window.am5.color(0x000000),
				});
				series.get('tooltip').label.setAll({ fill: window.am5.color(0xffffff), fontSize: 12 });
			}

			series.data.setAll(data);
		});

		const legend = chart.children.push(window.am5.Legend.new(chart24hRoot, {
			centerX: window.am5.percent(50),
			x: window.am5.percent(50),
			marginTop: 8,
		}));
		legend.labels.template.setAll({ fontSize: 11 });
		legend.data.setAll(chart.series.values);

		chart.children.push(window.am5.Label.new(chart24hRoot, {
			text: 'giavanghn.com',
			x: window.am5.percent(100),
			centerX: window.am5.percent(100),
			y: window.am5.percent(100),
			centerY: window.am5.percent(100),
			paddingRight: 10,
			paddingBottom: -10,
			fontSize: 11,
			opacity: 0.4,
		}));
	};

	let sjcYearChartRoot = null;
	const renderSjcYearAmChart = (retries = 0) => {
		const holder = document.getElementById('sjcYearAmChart');
		const data = Array.isArray(snapshot?.sjcYearlyChart) ? snapshot.sjcYearlyChart : [];

		if (!holder) return;
		if (!window.am5 || !window.am5xy || !window.am5themes_Animated) {
			if (retries < 30) {
				setTimeout(() => renderSjcYearAmChart(retries + 1), 200);
			}
			return;
		}

		if (!data.length) {
			holder.innerHTML = '<div class="grid h-full place-items-center text-sm font-semibold text-slate-500">Chưa có dữ liệu biểu đồ SJC 1 năm.</div>';
			return;
		}

		if (sjcYearChartRoot) {
			sjcYearChartRoot.dispose();
			sjcYearChartRoot = null;
		}

		sjcYearChartRoot = window.am5.Root.new('sjcYearAmChart');

		// Remove amCharts logo
		sjcYearChartRoot._logo?.dispose();

		sjcYearChartRoot.setThemes([window.am5themes_Animated.new(sjcYearChartRoot)]);

		const chart = sjcYearChartRoot.container.children.push(
			window.am5xy.XYChart.new(sjcYearChartRoot, {
				panX: true,
				panY: false,
				wheelX: 'panX',
				wheelY: 'zoomX',
			})
		);

		const xAxis = chart.xAxes.push(
			window.am5xy.DateAxis.new(sjcYearChartRoot, {
				baseInterval: { timeUnit: 'day', count: 1 },
				renderer: window.am5xy.AxisRendererX.new(sjcYearChartRoot, { minGridDistance: 60 }),
				dateFormats: { month: 'MM/yyyy', day: 'MM/yyyy' },
				periodChangeDateFormats: { month: 'MM/yyyy', day: 'MM/yyyy' },
			})
		);
		xAxis.get('renderer').labels.template.setAll({ fontSize: 10 });

		const yAxis = chart.yAxes.push(
			window.am5xy.ValueAxis.new(sjcYearChartRoot, {
				renderer: window.am5xy.AxisRendererY.new(sjcYearChartRoot, {}),
				numberFormat: "#.# 'tr'",
			})
		);
		yAxis.get('renderer').labels.template.setAll({ fontSize: 10 });

		/* ── shared cursor tooltip showing both sell & buy ── */
		const cursor = chart.set(
			'cursor',
			window.am5xy.XYCursor.new(sjcYearChartRoot, { behavior: 'zoomX', xAxis })
		);
		cursor.lineY.set('visible', false);

		const sellSeries = chart.series.push(
			window.am5xy.LineSeries.new(sjcYearChartRoot, {
				name: 'Bán ra',
				xAxis,
				yAxis,
				valueYField: 'sell',
				valueXField: 'dateTs',
				stroke: window.am5.color(0x15803d),
				fill: window.am5.color(0x15803d),
				tooltip: window.am5.Tooltip.new(sjcYearChartRoot, {
					labelText: "[bold]{valueX.formatDate('dd/MM/yyyy')}[/]\nBán ra: [bold #15803d]{sell} tr[/]\nMua vào: [bold #dc2626]{buy} tr[/]",
					pointerOrientation: 'horizontal',
					getFillFromSprite: false,
					getStrokeFromSprite: false,
				}),
			})
		);
		sellSeries.get('tooltip').get('background').setAll({
			fill: window.am5.color(0x000000),
			fillOpacity: 0.9,
			stroke: window.am5.color(0x000000),
		});
		sellSeries.get('tooltip').label.setAll({ fill: window.am5.color(0xffffff) });

		const buySeries = chart.series.push(
			window.am5xy.LineSeries.new(sjcYearChartRoot, {
				name: 'Mua vào',
				xAxis,
				yAxis,
				valueYField: 'buy',
				valueXField: 'dateTs',
				stroke: window.am5.color(0xdc2626),
				fill: window.am5.color(0xdc2626),
			})
		);

		buySeries.strokes.template.setAll({ strokeDasharray: [6, 4] });

		const chartData = data.map((point) => ({
			...point,
			dateTs: new Date(point.date).getTime(),
		}));

		sellSeries.data.setAll(chartData);
		buySeries.data.setAll(chartData);

		const legend = chart.children.unshift(window.am5.Legend.new(sjcYearChartRoot, {
			centerX: window.am5.percent(50),
			x: window.am5.percent(50),
			marginBottom: 8,
		}));
		legend.data.setAll([sellSeries, buySeries]);

		const scrollbar = window.am5.Scrollbar.new(sjcYearChartRoot, { orientation: 'horizontal' });
		chart.set('scrollbarX', scrollbar);
		chart.bottomAxesContainer.children.push(scrollbar);

		// Add giavanghn.com branding
		chart.children.push(window.am5.Label.new(sjcYearChartRoot, {
			text: 'giavanghn.com',
			x: window.am5.percent(100),
			centerX: window.am5.percent(100),
			y: window.am5.percent(100),
			centerY: window.am5.percent(100),
			paddingRight: 10,
			paddingBottom: -10,
			fontSize: 11,
			fill: window.am5.color(0x999999),
			opacity: 0.7,
		}));

		chart.appear(1000, 100);
	};

	/* ── SJC Chart period switching ── */
	const sjcPeriodBtns = document.querySelectorAll('.sjc-period-btn');
	const activePeriodClasses = ['border-[#b8860b]', 'bg-[#b8860b]', 'text-white'];
	const inactivePeriodClasses = ['border-[#ccc]', 'bg-white', 'text-[#555]'];

	sjcPeriodBtns.forEach((btn) => {
		btn.addEventListener('click', async () => {
			const period = btn.dataset.period;

			// Update active state
			sjcPeriodBtns.forEach((b) => {
				b.classList.remove(...activePeriodClasses);
				b.classList.add(...inactivePeriodClasses);
			});
			btn.classList.remove(...inactivePeriodClasses);
			btn.classList.add(...activePeriodClasses);

			// Fetch new data
			try {
				if (sjcYearChartRoot) {
					sjcYearChartRoot.dispose();
					sjcYearChartRoot = null;
				}
				const holder = document.getElementById('sjcYearAmChart');
				if (holder) holder.innerHTML = '<div class="grid h-full place-items-center text-sm text-slate-400">Đang tải...</div>';

				const res = await fetch(`/api/v1/sjc-chart?period=${period}`);
				const data = await res.json();
				snapshot.sjcYearlyChart = data;
				if (holder) holder.innerHTML = '';
				renderSjcYearAmChart();
			} catch (e) {
				console.error('SJC chart period fetch error:', e);
			}
		});
	});

	const renderUSCard = () => {
		if (!document.getElementById('usPriceText')) return;
		const selected = usVariantSelect?.value || snapshot.usCard.selected;
		const variant = snapshot.usCard.variants[selected];

		document.getElementById('usPriceText').textContent = `$${variant.price.toFixed(2)}`;
		document.getElementById('usUnitText').textContent = variant.unit;
		applyDayChangeColor(document.getElementById('usDayChangeText'), variant.dayChangeLabel);
		document.getElementById('usTrendPercent').textContent = `${snapshot.usCard.trendPercent >= 0 ? '+' : ''}${snapshot.usCard.trendPercent.toFixed(2)}%`;

		const usPointsEl = document.getElementById('usPointsText');
		if (usPointsEl) usPointsEl.textContent = `${snapshot.usCard.weekPoints.reduce((sum, n) => sum + n, 0)} diem`;
		drawLine(document.getElementById('usMiniChart'), snapshot.usCard.weekPoints, '#1d4ed8', snapshot.usCard.weekDates);

		// Hero mini chart — 24h intraday
		const usHeroChart = document.getElementById('usHeroMiniChart');
		const us24h = snapshot.usCard.chart24hPoints || [];
		const us24hLabels = snapshot.usCard.chart24hLabels || [];
		if (usHeroChart && us24h.length >= 2) {
			drawLine(usHeroChart, us24h, '#1d4ed8', us24hLabels);
			const usHeroPts = document.getElementById('usHeroPointsText');
			if (usHeroPts) {
				const last = us24h[us24h.length - 1];
				usHeroPts.textContent = `$${last.toFixed(2)}`;
			}
		}
	};

	const renderSJCCard = () => {
		if (!document.getElementById('sjcPriceText')) return;
		const selected = sjcVariantSelect?.value || snapshot.sjcCard.selected;
		const variant = snapshot.sjcCard.variants[selected];

		document.getElementById('sjcPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('sjcUnitText').textContent = variant.unit;
		const sjcBuySellEl = document.getElementById('sjcBuySellText');
		if (sjcBuySellEl) sjcBuySellEl.textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('sjcDayChangeText'), variant.dayChangeLabel);
		document.getElementById('sjcTrendPercent').textContent = `${snapshot.sjcCard.trendPercent >= 0 ? '+' : ''}${snapshot.sjcCard.trendPercent.toFixed(2)}%`;
		// Hero mini chart — weekly buy/sell (same pattern as other brand cards)
		renderBuySellMiniChart(snapshot.sjcCard, 'sjcHeroPointsText', 'sjcHeroMiniChart', '#15803d', '#dc2626');
	};

	const renderSjcBrandCard = () => {
		if (!document.getElementById('sjcBrandPriceText')) return;
		const selected = sjcBrandVariantSelect?.value || snapshot.sjcCard.selected;
		const variant = snapshot.sjcCard.variants[selected];

		document.getElementById('sjcBrandPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('sjcBrandUnitText').textContent = variant.unit;
		document.getElementById('sjcBrandBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('sjcBrandDayChangeText'), variant.dayChangeLabel);
		document.getElementById('sjcBrandTrendPercent').textContent = `${snapshot.sjcCard.trendPercent >= 0 ? '+' : ''}${snapshot.sjcCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.sjcCard, 'sjcBrandPointsText', 'sjcBrandMiniChart', '#15803d', '#dc2626');
	};

	const renderBtmcCard = () => {
		if (!document.getElementById('btmcPriceText')) return;
		const selected = btmcVariantSelect?.value || snapshot.btmcCard.selected;
		const variant = snapshot.btmcCard.variants[selected];

		document.getElementById('btmcPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('btmcUnitText').textContent = variant.unit;
		document.getElementById('btmcBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('btmcDayChangeText'), variant.dayChangeLabel);
		document.getElementById('btmcTrendPercent').textContent = `${snapshot.btmcCard.trendPercent >= 0 ? '+' : ''}${snapshot.btmcCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.btmcCard, 'btmcPointsText', 'btmcMiniChart', '#15803d', '#dc2626');
	};

	const renderPnjCard = () => {
		if (!document.getElementById('pnjPriceText')) return;
		const selected = pnjVariantSelect?.value || snapshot.pnjCard.selected;
		const variant = snapshot.pnjCard.variants[selected];

		document.getElementById('pnjPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('pnjUnitText').textContent = variant.unit;
		document.getElementById('pnjBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('pnjDayChangeText'), variant.dayChangeLabel);
		document.getElementById('pnjTrendPercent').textContent = `${snapshot.pnjCard.trendPercent >= 0 ? '+' : ''}${snapshot.pnjCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.pnjCard, 'pnjPointsText', 'pnjMiniChart', '#15803d', '#dc2626');
	};

	const renderDojiCard = () => {
		if (!document.getElementById('dojiPriceText')) return;
		const selected = dojiVariantSelect?.value || snapshot.dojiCard.selected;
		const variant = snapshot.dojiCard.variants[selected];

		document.getElementById('dojiPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('dojiUnitText').textContent = variant.unit;
		document.getElementById('dojiBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('dojiDayChangeText'), variant.dayChangeLabel);
		document.getElementById('dojiTrendPercent').textContent = `${snapshot.dojiCard.trendPercent >= 0 ? '+' : ''}${snapshot.dojiCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.dojiCard, 'dojiPointsText', 'dojiMiniChart', '#15803d', '#dc2626');
	};

	const renderPhuquyCard = () => {
		if (!document.getElementById('phuquyPriceText')) return;
		const selected = phuquyVariantSelect?.value || snapshot.phuquyCard.selected;
		const variant = snapshot.phuquyCard.variants[selected];

		document.getElementById('phuquyPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('phuquyUnitText').textContent = variant.unit;
		document.getElementById('phuquyBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('phuquyDayChangeText'), variant.dayChangeLabel);
		document.getElementById('phuquyTrendPercent').textContent = `${snapshot.phuquyCard.trendPercent >= 0 ? '+' : ''}${snapshot.phuquyCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.phuquyCard, 'phuquyPointsText', 'phuquyMiniChart', '#15803d', '#dc2626');
	};

	const renderMihongCard = () => {
		if (!document.getElementById('mihongPriceText')) return;
		const selected = mihongVariantSelect?.value || snapshot.mihongCard.selected;
		const variant = snapshot.mihongCard.variants[selected];

		document.getElementById('mihongPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('mihongUnitText').textContent = variant.unit;
		document.getElementById('mihongBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('mihongDayChangeText'), variant.dayChangeLabel);
		document.getElementById('mihongTrendPercent').textContent = `${snapshot.mihongCard.trendPercent >= 0 ? '+' : ''}${snapshot.mihongCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.mihongCard, 'mihongPointsText', 'mihongMiniChart', '#15803d', '#dc2626');
	};

	const renderBtmhCard = () => {
		if (!document.getElementById('btmhPriceText')) return;
		const selected = btmhVariantSelect?.value || snapshot.btmhCard.selected;
		const variant = snapshot.btmhCard.variants[selected];

		document.getElementById('btmhPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('btmhUnitText').textContent = variant.unit;
		document.getElementById('btmhBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('btmhDayChangeText'), variant.dayChangeLabel);
		document.getElementById('btmhTrendPercent').textContent = `${snapshot.btmhCard.trendPercent >= 0 ? '+' : ''}${snapshot.btmhCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.btmhCard, 'btmhPointsText', 'btmhMiniChart', '#15803d', '#dc2626');
	};

	const renderNgocthamCard = () => {
		if (!document.getElementById('ngocthamPriceText')) return;
		const selected = ngocthamVariantSelect?.value || snapshot.ngocthamCard.selected;
		const variant = snapshot.ngocthamCard.variants[selected];

		document.getElementById('ngocthamPriceText').textContent = variant.price.toFixed(1);
		document.getElementById('ngocthamUnitText').textContent = variant.unit;
		document.getElementById('ngocthamBuySellText').textContent = `Mua: ${variant.buy.toFixed(2)}tr | Bán: ${variant.sell.toFixed(2)}tr`;
		applyDayChangeColor(document.getElementById('ngocthamDayChangeText'), variant.dayChangeLabel);
		document.getElementById('ngocthamTrendPercent').textContent = `${snapshot.ngocthamCard.trendPercent >= 0 ? '+' : ''}${snapshot.ngocthamCard.trendPercent.toFixed(2)}%`;
		renderBuySellMiniChart(snapshot.ngocthamCard, 'ngocthamPointsText', 'ngocthamMiniChart', '#15803d', '#dc2626');
	};

	const renderTopBrands = () => {
		const body = document.getElementById('topBrandsTableBody');

		if (!body) {
			return;
		}

		body.innerHTML = snapshot.topBrands
			.map((brand) => {
				const cssClass = brand.change >= 0 ? 'text-[#008236]' : 'text-[#e7000b]';

				return `
					<tr class="hover:bg-[#f5f5f5] transition">
						<td class="border-b border-[#ebebeb] px-1.5 py-2 sm:p-3 text-left font-medium whitespace-nowrap">${brand.brand}</td>
						<td class="border-b border-[#ebebeb] px-1.5 py-2 sm:p-3 text-right font-bold whitespace-nowrap">${numberFormat(brand.buy)}</td>
						<td class="border-b border-[#ebebeb] px-1.5 py-2 sm:p-3 text-right font-bold whitespace-nowrap">${numberFormat(brand.sell)}</td>
						<td class="border-b border-[#ebebeb] px-1.5 py-2 sm:p-3 text-right text-[#666] hidden sm:table-cell whitespace-nowrap">${numberFormat(brand.sell - brand.buy)}</td>
						<td class="border-b border-[#ebebeb] px-1.5 py-2 sm:p-3 text-right font-bold whitespace-nowrap ${cssClass}">${brand.change >= 0 ? '+' : ''}${brand.change.toFixed(2)}%</td>
					</tr>
				`;
			})
			.join('');
	};

	const renderNews = () => {
		const list = document.getElementById('newsList');

		if (!list) {
			return;
		}

		const emojiMap = { positive: '📈', negative: '📉', neutral: '📰' };

		list.innerHTML = snapshot.news
			.map(
				(news) => {
					const emoji = emojiMap[news.impact] || '📰';
					const sourceBadge = news.source
						? `<span class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] text-blue-600">${news.source.toUpperCase()}</span>`
						: '';
					const titleHtml = news.source === 'giavanghn' && news.url ? `<a href="${news.url}" class="hover:text-[#b8860b] transition">${news.title}</a>` : news.title;

					const iconHtml = news.source === 'giavanghn' && news.image_url
						? `<img src="${news.image_url}" alt="" class="shrink-0 w-16 h-16 rounded-sm object-cover">`
						: `<div class="shrink-0 w-16 h-16 rounded-sm bg-linear-to-br from-slate-100 to-slate-200 grid place-items-center text-xl">${emoji}</div>`;

					return `
					<article class="flex items-start gap-3 rounded-sm border border-slate-200 bg-white p-3">
						${iconHtml}
						<div class="flex-1">
							<h3 class="text-base font-semibold text-slate-900 m-0">${titleHtml}</h3>
							<div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-400">
								<span>${news.time}</span>
								<span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">${news.tag}</span>
								${sourceBadge}
							</div>
						</div>
					</article>`;
				},
			)
			.join('');
	};

	const renderStats = () => {
		const grid = document.getElementById('statsGrid');

		if (!grid) {
			return;
		}

		grid.innerHTML = snapshot.statCards
			.map((card) => {
				const deltaClass = card.trend === 'down' ? 'text-[#e7000b]' : (card.trend === 'up' ? 'text-[#008236]' : 'text-[#666]');

				return `
					<article class="rounded-sm border border-[#bcbcbc] bg-white p-4">
						<h3 class="m-0 text-xs font-medium text-[#666] uppercase tracking-wide">${card.title}</h3>
						<p class="mt-2 text-2xl font-bold">${card.value}</p>
						<p class="mt-1 text-xs text-[#666]">${card.unit}</p>
						<p class="mt-2 text-sm font-bold ${deltaClass}">${card.delta}</p>
					</article>
				`;
			})
			.join('');
	};

	const renderAll = () => {
		renderUSCard();
		renderSJCCard();
		renderSjcBrandCard();
		renderBtmcCard();
		renderPnjCard();
		renderDojiCard();
		renderPhuquyCard();
		renderMihongCard();
		renderBtmhCard();
		renderNgocthamCard();
		renderTopBrands();
		renderNews();
		renderStats();
		drawMainChart();
		renderSjcYearAmChart();
		initIcons();
	};

	const syncClock = () => {
		const now = new Date();
		const clock = document.getElementById('liveClock');

		if (clock) {
			clock.textContent = now.toLocaleTimeString('vi-VN', { hour12: false });
		}
	};

	const refreshSnapshot = async (silent = false) => {
		try {
			const response = await fetch('/dashboard-api/snapshot', {
				method: 'GET',
				headers: {
					Accept: 'application/json',
				},
			});

			if (!response.ok) {
				throw new Error('Khong the tai du lieu moi.');
			}

			const payload = await response.json();
			snapshot = payload.snapshot;
			renderAll();

			const updatedAt = document.getElementById('updatedAtText');
			if (updatedAt) {
				updatedAt.textContent = payload.updatedAt;
			}

			if (!silent) {
				showToast('Da cap nhat du lieu tu dong.');
			}
		} catch (error) {
			if (!silent) {
				showToast(error.message || 'Loi tai du lieu.', true);
			}
		}
	};

	const bindQuickActions = () => {
		document.querySelectorAll('[data-action]').forEach((button) => {
			button.addEventListener('click', () => {
				const action = button.getAttribute('data-action');
				showToast(`Da mo: ${action}`);
			});
		});
	};

	const bindSubscribe = () => {
		const modal = document.getElementById('subscribeModal');
		const openBtn = document.getElementById('openSubscribeBtn');
		const closeBtn = document.getElementById('closeSubscribeBtn');
		const form = document.getElementById('subscribeForm');

		if (!modal || !openBtn || !closeBtn || !form) {
			return;
		}

		openBtn.addEventListener('click', () => modal.showModal());
		closeBtn.addEventListener('click', () => modal.close());

		form.addEventListener('submit', async (event) => {
			event.preventDefault();

			const submitBtn = form.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Dang gui...';
			}

			try {
				const formData = new FormData(form);
				const response = await fetch('/dashboard-api/subscribe', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
						Accept: 'application/json',
					},
					body: formData,
				});

				if (!response.ok) {
					throw new Error('Dang ky that bai. Vui long kiem tra email.');
				}

				const payload = await response.json();
				modal.close();
				form.reset();
				showToast(payload.message || 'Dang ky thanh cong.');
			} catch (error) {
				showToast(error.message || 'Co loi xay ra.', true);
			} finally {
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = 'Xac nhan dang ky';
				}
			}
		});
	};

	usVariantSelect?.addEventListener('change', renderUSCard);
	sjcVariantSelect?.addEventListener('change', renderSJCCard);
	sjcBrandVariantSelect?.addEventListener('change', renderSjcBrandCard);
	btmcVariantSelect?.addEventListener('change', renderBtmcCard);
	pnjVariantSelect?.addEventListener('change', renderPnjCard);
	dojiVariantSelect?.addEventListener('change', renderDojiCard);
	phuquyVariantSelect?.addEventListener('change', renderPhuquyCard);
	mihongVariantSelect?.addEventListener('change', renderMihongCard);
	btmhVariantSelect?.addEventListener('change', renderBtmhCard);
	ngocthamVariantSelect?.addEventListener('change', renderNgocthamCard);

	renderAll();
	bindQuickActions();
	bindSubscribe();
	syncClock();

	window.setInterval(syncClock, 1000);
	window.setInterval(() => {
		refreshSnapshot(true);
	}, 15 * 60 * 1000);

	/* ── Price Feed auto-refresh ── */
	const priceFeedList = document.getElementById('priceFeedList');
	const priceFeedUpdatedAt = document.getElementById('priceFeedUpdatedAt');

	const renderPriceFeedItems = (items) => {
		if (!priceFeedList) return;
		if (!items.length) {
			priceFeedList.innerHTML = '<div class="py-6 text-center text-sm text-[#999]">Chưa có biến động giá hôm nay. Giá sẽ tự động cập nhật khi có thay đổi.</div>';
			return;
		}
		priceFeedList.innerHTML = items.map((item) => {
			const sell = (item.sell / 1_000_000).toFixed(2);
			const changeK = Math.round(item.change / 1000);
			const sign = item.change >= 0 ? '+' : '';
			const pctSign = item.changePct >= 0 ? '+' : '';
			const color = item.change >= 0 ? 'text-[#168307]' : 'text-[#e7000b]';
			return `<div class="flex flex-wrap sm:flex-nowrap items-center gap-x-2 sm:gap-x-3 gap-y-0.5 border-b border-[#f0f0f0] py-2.5 last:border-0 text-sm">
				<span class="shrink-0 w-10 sm:w-12 font-mono text-xs text-[#999]">${item.time}</span>
				<span class="shrink-0 font-semibold text-[#001061]">${item.source}</span>
				<span class="w-[calc(100%-6rem)] sm:w-auto sm:flex-1 truncate text-[#555]">${item.brand}</span>
				<span class="ml-auto shrink-0 font-semibold text-[#333]">${sell}tr</span>
				<span class="shrink-0 text-right font-semibold ${color}">${sign}${changeK.toLocaleString()}k (${pctSign}${item.changePct.toFixed(2)}%)</span>
			</div>`;
		}).join('');
	};

	const refreshPriceFeed = async () => {
		try {
			const res = await fetch('/api/v1/price-feed');
			const data = await res.json();
			renderPriceFeedItems(data);
			if (priceFeedUpdatedAt) {
				const now = new Date();
				priceFeedUpdatedAt.textContent = `Cập nhật lúc ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
			}
		} catch (e) {
			console.error('Price feed refresh error:', e);
		}
	};

	// Refresh price feed every 3 minutes
	window.setInterval(refreshPriceFeed, 3 * 60 * 1000);

	/* ── History Date Picker — updates the 2 main cards ── */
	const historyDatePicker = document.getElementById('historyDatePicker');
	const historyLookupBtn = document.getElementById('historyLookupBtn');
	const historyTodayBtn = document.getElementById('historyTodayBtn');
	const historyDismissBtn = document.getElementById('historyDismissBtn');
	const historyLoading = document.getElementById('historyLoading');
	const historyNoData = document.getElementById('historyNoData');
	const historyActiveBadge = document.getElementById('historyActiveBadge');
	const historyActiveDate = document.getElementById('historyActiveDate');

	// Store original snapshot for restoring
	const originalSnapshot = JSON.parse(JSON.stringify(snapshot));

	const restoreToday = () => {
		snapshot.usCard = JSON.parse(JSON.stringify(originalSnapshot.usCard));
		snapshot.sjcCard = JSON.parse(JSON.stringify(originalSnapshot.sjcCard));
		snapshot.btmcCard = JSON.parse(JSON.stringify(originalSnapshot.btmcCard));
		snapshot.pnjCard = JSON.parse(JSON.stringify(originalSnapshot.pnjCard));
		snapshot.dojiCard = JSON.parse(JSON.stringify(originalSnapshot.dojiCard));
		snapshot.phuquyCard = JSON.parse(JSON.stringify(originalSnapshot.phuquyCard));
		snapshot.mihongCard = JSON.parse(JSON.stringify(originalSnapshot.mihongCard));
		snapshot.btmhCard = JSON.parse(JSON.stringify(originalSnapshot.btmhCard));
		snapshot.ngocthamCard = JSON.parse(JSON.stringify(originalSnapshot.ngocthamCard));
		snapshot.statCards = JSON.parse(JSON.stringify(originalSnapshot.statCards));
		rebuildSelect(sjcVariantSelect, snapshot.sjcCard.variants, snapshot.sjcCard.selected);
		rebuildSelect(sjcBrandVariantSelect, snapshot.sjcCard.variants, snapshot.sjcCard.selected);
		rebuildSelect(btmcVariantSelect, snapshot.btmcCard.variants, snapshot.btmcCard.selected);
		rebuildSelect(pnjVariantSelect, snapshot.pnjCard.variants, snapshot.pnjCard.selected);
		rebuildSelect(dojiVariantSelect, snapshot.dojiCard.variants, snapshot.dojiCard.selected);
		rebuildSelect(phuquyVariantSelect, snapshot.phuquyCard.variants, snapshot.phuquyCard.selected);
		rebuildSelect(mihongVariantSelect, snapshot.mihongCard.variants, snapshot.mihongCard.selected);
		rebuildSelect(btmhVariantSelect, snapshot.btmhCard.variants, snapshot.btmhCard.selected);
		rebuildSelect(ngocthamVariantSelect, snapshot.ngocthamCard.variants, snapshot.ngocthamCard.selected);
		renderUSCard();
		renderSJCCard();
		renderSjcBrandCard();
		renderBtmcCard();
		renderPnjCard();
		renderDojiCard();
		renderPhuquyCard();
		renderMihongCard();
		renderBtmhCard();
		renderNgocthamCard();
		renderStats();
		if (historyActiveBadge) historyActiveBadge.classList.add('hidden');
		if (historyNoData) historyNoData.classList.add('hidden');
		if (historyDatePicker) historyDatePicker.value = new Date().toISOString().split('T')[0];
	};

	const loadHistoryByDate = async (date) => {
		if (!historyLoading) return;

		historyLoading.classList.remove('hidden');
		if (historyActiveBadge) historyActiveBadge.classList.add('hidden');
		if (historyNoData) historyNoData.classList.add('hidden');

		try {
			const res = await fetch(`/api/v1/prices-by-date?date=${encodeURIComponent(date)}`, {
				headers: { Accept: 'application/json' },
			});

			if (!res.ok) throw new Error('Khong the tai du lieu');

			const data = await res.json();

			const hasData = data.world?.length > 0 ||
				Object.values(data.brands).some((b) => b.length > 0);

			if (!hasData) {
				historyNoData.classList.remove('hidden');
				historyLoading.classList.add('hidden');
				return;
			}

			const displayDate = new Date(data.date + 'T00:00:00');
			const isToday = data.date === new Date().toISOString().split('T')[0];

			// ── Update US Card (XAU/USD) ──
			const xau = data.world.find((w) => w.symbol === 'XAU/USD');
			if (xau) {
				const price = parseFloat(xau.price);
				const change = parseFloat(xau.change_percent) || 0;

				snapshot.usCard.trendPercent = change;
				snapshot.usCard.variants.spot.price = price;
				snapshot.usCard.variants.spot.dayChangeLabel = isToday
					? `${change >= 0 ? '+' : ''}${change.toFixed(2)}% hom nay`
					: `Ngay ${displayDate.toLocaleDateString('vi-VN')}`;
				snapshot.usCard.variants.future.price = price + 4.35;
				snapshot.usCard.variants.future.dayChangeLabel = snapshot.usCard.variants.spot.dayChangeLabel;
				snapshot.usCard.variants.london.price = price - 3.5;
				snapshot.usCard.variants.london.dayChangeLabel = snapshot.usCard.variants.spot.dayChangeLabel;
				if (data.usWeekPoints?.length) snapshot.usCard.weekPoints = data.usWeekPoints;
				renderUSCard();
			}

			// ── Update SJC Card ──
			const sjcItems = data.brands.sjc || [];
			if (sjcItems.length > 0) {
				const regionNames = { ha_noi: 'HN', tp_hcm: 'HCM', da_nang: 'ĐN' };
				const brandCounts = {};
				sjcItems.forEach((i) => { brandCounts[i.brand] = (brandCounts[i.brand] || 0) + 1; });
				const sorted = [...sjcItems].sort((a, b) => b.sell_price - a.sell_price);
				const sjcMain = sorted[0];
				const change = parseFloat(sjcMain.change_percent) || 0;
				const dateLabel = formatVndDeltaLabel(change, sjcMain.sell_price);

				snapshot.sjcCard.trendPercent = change;
				const newVariants = {};
				sorted.forEach((item, i) => {
					let label = item.brand || ('Sản phẩm ' + (i + 1));
					if ((brandCounts[item.brand] || 0) > 1 && regionNames[item.region]) {
						label += ' (' + regionNames[item.region] + ')';
					}
					newVariants['p' + i] = {
						label,
						price: item.sell_price / 1_000_000,
						buy: item.buy_price / 1_000_000,
						sell: item.sell_price / 1_000_000,
						unit: 'Triệu đồng/Lượng',
						dayChangeLabel: dateLabel,
					};
				});
				snapshot.sjcCard.variants = newVariants;
				snapshot.sjcCard.selected = 'p0';
				rebuildSelect(sjcVariantSelect, newVariants, 'p0');
				rebuildSelect(sjcBrandVariantSelect, newVariants, 'p0');

				if (data.sjcWeekSellPoints?.length) snapshot.sjcCard.weekSellPoints = data.sjcWeekSellPoints;
				if (data.sjcWeekBuyPoints?.length) snapshot.sjcCard.weekBuyPoints = data.sjcWeekBuyPoints;
				if (data.sjcWeekPoints?.length) snapshot.sjcCard.weekPoints = data.sjcWeekPoints;
				renderSJCCard();
				renderSjcBrandCard();
			}

			// ── Update BTMC Card ──
			const btmcItems = data.brands.btmc || [];
			if (btmcItems.length > 0) {
				const sorted = [...btmcItems].sort((a, b) => b.sell_price - a.sell_price);
				const btmcMain = sorted[0];
				const btmcChange = parseFloat(btmcMain.change_percent) || 0;
				const btmcDateLabel = formatVndDeltaLabel(btmcChange, btmcMain.sell_price);

				snapshot.btmcCard.trendPercent = btmcChange;
				const newVariants = {};
				sorted.forEach((item, i) => {
					newVariants['p' + i] = {
						label: item.brand || ('Sản phẩm ' + (i + 1)),
						price: item.sell_price / 1_000_000,
						buy: item.buy_price / 1_000_000,
						sell: item.sell_price / 1_000_000,
						unit: 'Triệu đồng/Lượng',
						dayChangeLabel: btmcDateLabel,
					};
				});
				snapshot.btmcCard.variants = newVariants;
				snapshot.btmcCard.selected = 'p0';
				rebuildSelect(btmcVariantSelect, newVariants, 'p0');

				if (data.btmcWeekSellPoints?.length) snapshot.btmcCard.weekSellPoints = data.btmcWeekSellPoints;
				if (data.btmcWeekBuyPoints?.length) snapshot.btmcCard.weekBuyPoints = data.btmcWeekBuyPoints;
				if (data.btmcWeekPoints?.length) snapshot.btmcCard.weekPoints = data.btmcWeekPoints;
				renderBtmcCard();
			}

			// ── Update PNJ Card ──
			const pnjItems = data.brands.pnj || [];
			if (pnjItems.length > 0) {
				// Filter zone 11 (HN), fallback to all
				let zoneItems = pnjItems.filter((i) => i.zone === '11');
				if (zoneItems.length === 0) zoneItems = pnjItems;
				const sorted = [...zoneItems].sort((a, b) => b.sell_price - a.sell_price);

				const pnjMain = sorted[0];
				const pnjChange = parseFloat(pnjMain.change_percent) || 0;
				const pnjDateLabel = formatVndDeltaLabel(pnjChange, pnjMain.sell_price);

				snapshot.pnjCard.trendPercent = pnjChange;
				const newVariants = {};
				sorted.forEach((item, i) => {
					newVariants['p' + i] = {
						label: item.brand || ('Sản phẩm ' + (i + 1)),
						price: item.sell_price / 1_000_000,
						buy: item.buy_price / 1_000_000,
						sell: item.sell_price / 1_000_000,
						unit: 'Triệu đồng/Lượng',
						dayChangeLabel: pnjDateLabel,
					};
				});
				snapshot.pnjCard.variants = newVariants;
				snapshot.pnjCard.selected = 'p0';
				rebuildSelect(pnjVariantSelect, newVariants, 'p0');

				if (data.pnjWeekSellPoints?.length) snapshot.pnjCard.weekSellPoints = data.pnjWeekSellPoints;
				if (data.pnjWeekBuyPoints?.length) snapshot.pnjCard.weekBuyPoints = data.pnjWeekBuyPoints;
				if (data.pnjWeekPoints?.length) snapshot.pnjCard.weekPoints = data.pnjWeekPoints;
				renderPnjCard();
			}

			// ── Helper for simple brand cards ──
			const updateBrandCard = (brandKey, cardKey, selectEl, weekSellPointsKey, weekBuyPointsKey, renderFn) => {
				const items = data.brands[brandKey] || [];
				if (items.length > 0) {
					const sorted = [...items].sort((a, b) => b.sell_price - a.sell_price);
					const main = sorted[0];
					const ch = parseFloat(main.change_percent) || 0;
					const label = formatVndDeltaLabel(ch, main.sell_price);

					snapshot[cardKey].trendPercent = ch;
					const newVariants = {};
					sorted.forEach((item, i) => {
						newVariants['p' + i] = {
							label: item.brand || ('Sản phẩm ' + (i + 1)),
							price: item.sell_price / 1_000_000,
							buy: item.buy_price / 1_000_000,
							sell: item.sell_price / 1_000_000,
							unit: 'Triệu đồng/Lượng',
							dayChangeLabel: label,
						};
					});
					snapshot[cardKey].variants = newVariants;
					snapshot[cardKey].selected = 'p0';
					rebuildSelect(selectEl, newVariants, 'p0');
					if (data[weekSellPointsKey]?.length) snapshot[cardKey].weekSellPoints = data[weekSellPointsKey];
					if (data[weekBuyPointsKey]?.length) snapshot[cardKey].weekBuyPoints = data[weekBuyPointsKey];
					if (data[weekSellPointsKey]?.length) snapshot[cardKey].weekPoints = data[weekSellPointsKey];
					renderFn();
				}
			};

			updateBrandCard('doji', 'dojiCard', dojiVariantSelect, 'dojiWeekSellPoints', 'dojiWeekBuyPoints', renderDojiCard);
			updateBrandCard('phuquy', 'phuquyCard', phuquyVariantSelect, 'phuquyWeekSellPoints', 'phuquyWeekBuyPoints', renderPhuquyCard);
			updateBrandCard('mihong', 'mihongCard', mihongVariantSelect, 'mihongWeekSellPoints', 'mihongWeekBuyPoints', renderMihongCard);
			updateBrandCard('baotinmanhhai', 'btmhCard', btmhVariantSelect, 'btmhWeekSellPoints', 'btmhWeekBuyPoints', renderBtmhCard);
			updateBrandCard('ngoctham', 'ngocthamCard', ngocthamVariantSelect, 'ngocthamWeekSellPoints', 'ngocthamWeekBuyPoints', renderNgocthamCard);

			// Show active badge if not today
			if (!isToday && historyActiveBadge && historyActiveDate) {
				historyActiveDate.textContent = displayDate.toLocaleDateString('vi-VN');
				historyActiveBadge.classList.remove('hidden');
			}

			// ── Update Stat Cards ──
			if (data.statCards && data.statCards.length > 0) {
				snapshot.statCards = data.statCards;
				renderStats();
			}

			showToast(`Da cap nhat gia vang ngay ${displayDate.toLocaleDateString('vi-VN')}`);
		} catch (err) {
			historyNoData.classList.remove('hidden');
			showToast(err.message || 'Loi tai du lieu lich su', true);
		} finally {
			historyLoading.classList.add('hidden');
		}
	};

	if (historyDatePicker) {
		historyDatePicker.addEventListener('change', () => {
			loadHistoryByDate(historyDatePicker.value);
		});
	}

	if (historyLookupBtn && historyDatePicker) {
		historyLookupBtn.addEventListener('click', () => {
			loadHistoryByDate(historyDatePicker.value);
		});
	}

	if (historyTodayBtn) {
		historyTodayBtn.addEventListener('click', restoreToday);
	}

	if (historyDismissBtn) {
		historyDismissBtn.addEventListener('click', restoreToday);
	}
}

/* ── Mobile drawer & accordion ── */
(() => {
	const drawer = document.getElementById('mobileDrawer');
	const openBtn = document.getElementById('mobileMenuBtn');
	const closeBtn = document.getElementById('mobileCloseBtn');
	const overlay = document.getElementById('mobileOverlay');

	if (!drawer || !openBtn) return;

	const open = () => drawer.classList.remove('hidden');
	const close = () => drawer.classList.add('hidden');

	openBtn.addEventListener('click', open);
	closeBtn?.addEventListener('click', close);
	overlay?.addEventListener('click', close);

	drawer.querySelectorAll('.mobile-toggle').forEach((btn) => {
		btn.addEventListener('click', () => {
			const sub = btn.nextElementSibling;
			const chevron = btn.querySelector('.mobile-chevron');
			if (!sub) return;
			const isOpen = !sub.classList.contains('hidden');
			sub.classList.toggle('hidden', isOpen);
			chevron?.classList.toggle('rotate-180', !isOpen);
		});
	});
})();

/* ── Tool calculators for sub-pages ── */
(() => {
	const toolWrappers = document.querySelectorAll('[data-tool-wrapper]');
	if (!toolWrappers.length) return;

	const toNumber = (v) => {
		const n = Number(v);
		return Number.isFinite(n) ? n : 0;
	};

	const formatNumber = (n, fractionDigits = 4) =>
		new Intl.NumberFormat('vi-VN', { maximumFractionDigits: fractionDigits }).format(n);

	const formatMoney = (n) => `${new Intl.NumberFormat('vi-VN').format(Math.round(n))} VND`;

	const unitsToGram = {
		luong: 37.5,
		chi: 3.75,
		gram: 1,
		ounce: 31.1035,
	};

	toolWrappers.forEach((wrapper) => {
		const toolKey = wrapper.getAttribute('data-tool-wrapper');
		const btn = wrapper.querySelector('[data-tool-action]');
		const mainResult = wrapper.querySelector('[data-tool-main-result]');
		const subResult = wrapper.querySelector('[data-tool-sub-result]');

		if (!btn || !mainResult || !subResult) return;

		const calc = () => {
			if (toolKey === 'convert-units') {
				const value = toNumber(wrapper.querySelector('[data-tool-input="value"]')?.value);
				const from = wrapper.querySelector('[data-tool-input="from"]')?.value || 'luong';
				const to = wrapper.querySelector('[data-tool-input="to"]')?.value || 'gram';

				const gram = value * (unitsToGram[from] || 1);
				const converted = gram / (unitsToGram[to] || 1);

				mainResult.textContent = `${formatNumber(converted)} ${to}`;
				subResult.textContent = `${formatNumber(value)} ${from} = ${formatNumber(gram)} gram`;
				return;
			}

			if (toolKey === 'gold-value') {
				const weight = toNumber(wrapper.querySelector('[data-tool-input="weight"]')?.value);
				const unit = wrapper.querySelector('[data-tool-input="unit"]')?.value || 'luong';
				const pricePerLuong = toNumber(wrapper.querySelector('[data-tool-input="pricePerLuong"]')?.value);

				const gram = weight * (unitsToGram[unit] || 1);
				const luong = gram / unitsToGram.luong;
				const total = luong * pricePerLuong;

				mainResult.textContent = formatMoney(total);
				subResult.textContent = `${formatNumber(weight)} ${unit} ~ ${formatNumber(luong, 6)} lượng`;
				return;
			}

			if (toolKey === 'profit') {
				const buyPrice = toNumber(wrapper.querySelector('[data-tool-input="buyPrice"]')?.value);
				const currentPrice = toNumber(wrapper.querySelector('[data-tool-input="currentPrice"]')?.value);
				const quantity = toNumber(wrapper.querySelector('[data-tool-input="quantity"]')?.value);

				const pnl = (currentPrice - buyPrice) * quantity;
				const roi = buyPrice > 0 ? ((currentPrice - buyPrice) / buyPrice) * 100 : 0;

				mainResult.textContent = `${pnl >= 0 ? '+' : ''}${formatMoney(pnl)}`;
				subResult.textContent = `ROI: ${roi >= 0 ? '+' : ''}${roi.toFixed(2)}%`;
				mainResult.classList.toggle('text-emerald-700', pnl >= 0);
				mainResult.classList.toggle('text-rose-700', pnl < 0);
				return;
			}

			if (toolKey === 'vnd-usd') {
				const vndPrice = toNumber(wrapper.querySelector('[data-tool-input="vndPrice"]')?.value);
				const usdVndRate = toNumber(wrapper.querySelector('[data-tool-input="usdVndRate"]')?.value);
				const usd = usdVndRate > 0 ? vndPrice / usdVndRate : 0;

				mainResult.textContent = `${formatNumber(usd, 2)} USD`;
				subResult.textContent = `${formatMoney(vndPrice)} / ${formatNumber(usdVndRate, 2)} = ${formatNumber(usd, 2)} USD`;
			}
		};

		btn.addEventListener('click', calc);
		wrapper.querySelectorAll('[data-tool-input]').forEach((el) => {
			el.addEventListener('input', calc);
			el.addEventListener('change', calc);
		});

		calc();
	});
})();
