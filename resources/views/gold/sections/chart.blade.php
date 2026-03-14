{{-- All-brands price chart (AmCharts 5) --}}
@php $period = $period ?? '30d'; $periodLabel = $periodLabel ?? '30 ngày'; @endphp
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] mb-1">
        <i data-lucide="bar-chart-3" class="h-5 w-5 text-amber-500"></i>
        Biểu đồ giá vàng {{ $periodLabel }}
    </h2>
    <p class="text-xs text-slate-500 mb-4">Đơn vị: triệu VNĐ/lượng · So sánh giá bán các thương hiệu &amp; giá vàng thế giới quy đổi (nét đứt)</p>

    <div class="flex gap-2 mb-3 flex-wrap">
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-hom-nay"
           class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all no-underline border {{ $period === 'today' ? 'bg-[#001061] text-white border-[#001061] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700' }}">Hôm nay</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-7-ngay"
           class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all no-underline border {{ $period === '7d' ? 'bg-[#001061] text-white border-[#001061] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700' }}">7 ngày</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-30-ngay"
           class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all no-underline border {{ $period === '30d' ? 'bg-[#001061] text-white border-[#001061] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700' }}">30 ngày</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-1-nam"
           class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all no-underline border {{ $period === '1y' ? 'bg-[#001061] text-white border-[#001061] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700' }}">1 năm</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-10-nam"
           class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all no-underline border {{ $period === '10y' ? 'bg-[#001061] text-white border-[#001061] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700' }}">10 năm</a>
    </div>

    <div id="allBrandsChart" class="w-full" style="height: 380px;">
        <div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">
            <span>Đang tải biểu đồ…</span>
        </div>
    </div>

    <div id="chartStats" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4" style="display:none;">
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Cao nhất</p><p id="statHigh" class="mt-1 text-lg font-bold text-[#001061]">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thấp nhất</p><p id="statLow" class="mt-1 text-lg font-bold text-[#001061]">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Trung bình</p><p id="statAvg" class="mt-1 text-lg font-bold text-[#001061]">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thay đổi</p><p id="statChange" class="mt-1 text-lg font-bold text-emerald-600">—</p></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var chartPeriod = @json($period);
    var chartRoot = null;
    var brandColors = {
        'SJC': '#b8860b', 'DOJI': '#3b82f6', 'PNJ': '#15803d', 'BTMC': '#dc2626',
        'Phú Quý': '#7c3aed', 'Mi Hồng': '#ea580c', 'Bảo Tín MH': '#0891b2', 'Ngọc Thẩm': '#be185d',
        'XAU quy đổi': '#f59e0b'
    };

    function loadChart() {
        fetch('/api/v1/all-brands-chart?period=' + encodeURIComponent(chartPeriod))
            .then(function (r) { return r.json(); })
            .then(function (data) { renderChart(data); })
            .catch(function () {
                document.getElementById('allBrandsChart').innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-500">Không thể tải dữ liệu biểu đồ.</div>';
            });
    }

    function renderChart(data) {
        var holder = document.getElementById('allBrandsChart');
        if (!holder) return;
        if (!window.am5 || !window.am5xy || !window.am5themes_Animated) {
            setTimeout(function () { renderChart(data); }, 200);
            return;
        }
        if (!data || !data.length) {
            holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm font-semibold text-slate-500">Chưa có dữ liệu biểu đồ.</div>';
            return;
        }

        holder.innerHTML = '';
        if (chartRoot) { chartRoot.dispose(); chartRoot = null; }

        chartRoot = am5.Root.new('allBrandsChart');
        if (chartRoot._logo) chartRoot._logo.dispose();
        chartRoot.setThemes([am5themes_Animated.new(chartRoot)]);

        var isToday = chartPeriod === 'today';

        var chart = chartRoot.container.children.push(
            am5xy.XYChart.new(chartRoot, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX' })
        );

        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(chartRoot, {
            baseInterval: { timeUnit: isToday ? 'minute' : 'day', count: isToday ? 1 : 1 },
            renderer: am5xy.AxisRendererX.new(chartRoot, { minGridDistance: 60 }),
            dateFormats: isToday ? { minute: 'HH:mm', hour: 'HH:mm' } : { month: 'MM/yyyy', day: 'dd/MM' },
            periodChangeDateFormats: isToday ? { minute: 'HH:mm', hour: 'dd/MM HH:mm' } : { month: 'MM/yyyy', day: 'dd/MM' },
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

        // Detect brand keys (skip _buy keys)
        var brandKeys = [];
        var first = data[0];
        for (var k in first) {
            if (k !== 'date' && k.indexOf('_buy') === -1 && brandColors[k]) {
                brandKeys.push(k);
            }
        }

        // Prepare chart data with timestamps
        var chartData = data.map(function (p) {
            var entry = { dateTs: new Date(p.date).getTime() };
            for (var i = 0; i < brandKeys.length; i++) {
                entry[brandKeys[i]] = p[brandKeys[i]];
            }
            return entry;
        });

        // Create a series for each brand
        brandKeys.forEach(function (brand) {
            var color = am5.color(brandColors[brand] || '#64748b');
            var isXau = brand === 'XAU quy đổi';
            var series = chart.series.push(am5xy.LineSeries.new(chartRoot, {
                name: brand,
                xAxis: xAxis, yAxis: yAxis,
                valueYField: brand, valueXField: 'dateTs',
                stroke: color, fill: color,
                tooltip: am5.Tooltip.new(chartRoot, {
                    labelText: "[bold]{name}[/]: {valueY} tr",
                    pointerOrientation: 'horizontal',
                    getFillFromSprite: false, getStrokeFromSprite: false,
                }),
            }));
            series.get('tooltip').get('background').setAll({ fill: am5.color(0x0f172a), fillOpacity: 0.92, stroke: am5.color(0x0f172a) });
            series.get('tooltip').label.setAll({ fill: am5.color(0xffffff), fontSize: 12 });
            if (isXau) {
                series.strokes.template.setAll({ strokeWidth: 2.5, strokeDasharray: [6, 3] });
            } else {
                series.strokes.template.setAll({ strokeWidth: 2 });
            }
            series.data.setAll(chartData);
        });

        // Legend
        var legend = chart.children.push(am5.Legend.new(chartRoot, {
            centerX: am5.percent(50), x: am5.percent(50), y: am5.percent(100), layout: chartRoot.horizontalLayout
        }));
        legend.labels.template.setAll({ fontSize: 11 });
        legend.data.setAll(chart.series.values);

        chart.appear(800, 100);

        // Update stats from SJC data
        updateStats(data);
    }

    function updateStats(data) {
        if (!data || !data.length) return;
        var sjcKey = 'SJC';
        var vals = data.map(function(d) { return d[sjcKey]; }).filter(function(v) { return v != null; });
        if (!vals.length) return;

        var high = Math.max.apply(null, vals);
        var low = Math.min.apply(null, vals);
        var avg = vals.reduce(function(a,b) { return a + b; }, 0) / vals.length;
        var first = vals[0];
        var last = vals[vals.length - 1];
        var change = first > 0 ? ((last - first) / first * 100) : 0;

        document.getElementById('statHigh').textContent = high.toFixed(2) + ' tr';
        document.getElementById('statLow').textContent = low.toFixed(2) + ' tr';
        document.getElementById('statAvg').textContent = avg.toFixed(2) + ' tr';

        var changeEl = document.getElementById('statChange');
        changeEl.textContent = (change >= 0 ? '+' : '') + change.toFixed(1) + '%';
        changeEl.className = 'mt-1 text-lg font-bold ' + (change >= 0 ? 'text-emerald-600' : 'text-rose-600');

        document.getElementById('chartStats').style.display = '';
    }

    loadChart();
});
</script>
@endpush