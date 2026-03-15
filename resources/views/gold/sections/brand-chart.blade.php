{{-- Brand monthly price chart (AmCharts 5) --}}
@php $chartBrand = $chartBrand ?? 'sjc'; $chartLabel = $chartLabel ?? 'SJC'; @endphp
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] mb-1">
        <i data-lucide="bar-chart-3" class="h-5 w-5 text-amber-500"></i>
        Biểu đồ giá vàng {{ $chartLabel }} 30 ngày gần nhất
    </h2>
    <p class="text-xs text-slate-500 mb-4">Đơn vị: triệu VNĐ/lượng · Dữ liệu cập nhật hàng ngày</p>

    <div class="flex gap-2 mb-3 flex-wrap">
        <button data-period="7d" class="brand-chart-period rounded-sm border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">7 ngày</button>
        <button data-period="1m" class="brand-chart-period rounded-sm border border-amber-400 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">30 ngày</button>
        <button data-period="3m" class="brand-chart-period rounded-sm border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">3 tháng</button>
        <button data-period="6m" class="brand-chart-period rounded-sm border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">6 tháng</button>
    </div>

    <div id="brandPriceChart" class="w-full h-[250px] sm:h-[340px]"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var brandKey = @json($chartBrand);
    var chartRoot = null;

    function loadBrandChart(period) {
        fetch('/api/v1/brand-chart?brand=' + encodeURIComponent(brandKey) + '&period=' + encodeURIComponent(period))
            .then(function (r) { return r.json(); })
            .then(function (data) { renderBrandChart(data); })
            .catch(function () {
                document.getElementById('brandPriceChart').innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-500">Không thể tải dữ liệu biểu đồ.</div>';
            });
    }

    function renderBrandChart(data) {
        var holder = document.getElementById('brandPriceChart');
        if (!holder) return;
        if (!window.am5 || !window.am5xy || !window.am5themes_Animated) {
            setTimeout(function () { renderBrandChart(data); }, 200);
            return;
        }
        if (!data || !data.length) {
            holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm font-semibold text-slate-500">Chưa có dữ liệu biểu đồ.</div>';
            return;
        }
        if (chartRoot) { chartRoot.dispose(); chartRoot = null; }

        chartRoot = am5.Root.new('brandPriceChart');
        if (chartRoot._logo) chartRoot._logo.dispose();
        chartRoot.setThemes([am5themes_Animated.new(chartRoot)]);

        var chart = chartRoot.container.children.push(
            am5xy.XYChart.new(chartRoot, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX' })
        );

        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(chartRoot, {
            baseInterval: { timeUnit: 'day', count: 1 },
            renderer: am5xy.AxisRendererX.new(chartRoot, { minGridDistance: 50 }),
            dateFormats: { month: 'MM/yyyy', day: 'dd/MM' },
            periodChangeDateFormats: { month: 'MM/yyyy', day: 'dd/MM' },
        }));
        xAxis.get('renderer').labels.template.setAll({ fontSize: 11, fill: am5.color(0x64748b) });

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(chartRoot, {
            renderer: am5xy.AxisRendererY.new(chartRoot, {}),
            numberFormat: '#,###.##',
            extraMin: 0.01,
            extraMax: 0.01,
        }));
        yAxis.get('renderer').labels.template.setAll({ fontSize: 11, fill: am5.color(0x64748b) });

        var cursor = chart.set('cursor', am5xy.XYCursor.new(chartRoot, { behavior: 'zoomX', xAxis: xAxis }));
        cursor.lineY.set('visible', false);

        // Bán ra (sell) line — gold
        var sellSeries = chart.series.push(am5xy.LineSeries.new(chartRoot, {
            name: 'Bán ra',
            xAxis: xAxis, yAxis: yAxis,
            valueYField: 'sell', openValueYField: 'buy', valueXField: 'dateTs',
            stroke: am5.color(0xb8860b), fill: am5.color(0xb8860b),
            tooltip: am5.Tooltip.new(chartRoot, {
                labelText: "[bold]{valueX.formatDate('dd/MM/yyyy')}[/]\nBán ra: [bold #b8860b]{sell}[/] tr\nMua vào: [bold #3b82f6]{buy}[/] tr",
                pointerOrientation: 'horizontal',
                getFillFromSprite: false, getStrokeFromSprite: false,
            }),
        }));
        sellSeries.get('tooltip').get('background').setAll({ fill: am5.color(0x0f172a), fillOpacity: 0.92, stroke: am5.color(0x0f172a) });
        sellSeries.get('tooltip').label.setAll({ fill: am5.color(0xffffff), fontSize: 12 });
        sellSeries.strokes.template.setAll({ strokeWidth: 1 });
        sellSeries.fills.template.setAll({ fillOpacity: 0.08, visible: true });

        // Mua vào (buy) line — blue
        var buySeries = chart.series.push(am5xy.LineSeries.new(chartRoot, {
            name: 'Mua vào',
            xAxis: xAxis, yAxis: yAxis,
            valueYField: 'buy', valueXField: 'dateTs',
            stroke: am5.color(0x3b82f6), fill: am5.color(0x3b82f6),
        }));
        buySeries.strokes.template.setAll({ strokeWidth: 1 });

        var chartData = data.map(function (p) {
            return { dateTs: new Date(p.date).getTime(), sell: p.sell, buy: p.buy };
        });
        sellSeries.data.setAll(chartData);
        buySeries.data.setAll(chartData);

        // Legend
        var legend = chart.children.push(am5.Legend.new(chartRoot, { centerX: am5.percent(50), x: am5.percent(50), y: am5.percent(100) }));
        legend.labels.template.setAll({ fontSize: 11 });
        legend.data.setAll(chart.series.values);

        chart.appear(800, 100);
    }

    // Period buttons
    document.querySelectorAll('.brand-chart-period').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.brand-chart-period').forEach(function (b) {
                b.className = 'brand-chart-period rounded-sm border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50';
            });
            btn.className = 'brand-chart-period rounded-sm border border-amber-400 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700';
            loadBrandChart(btn.dataset.period);
        });
    });

    loadBrandChart('1m');
});
</script>
