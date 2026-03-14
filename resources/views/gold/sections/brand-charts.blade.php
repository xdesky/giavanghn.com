{{-- Per-brand individual mini charts --}}
@php $brandPeriod = $brandPeriod ?? '30d'; @endphp
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-1 flex items-center gap-2">
        <i data-lucide="layers" class="h-5 w-5 text-amber-500"></i>
        Biểu đồ từng thương hiệu
    </h2>
    <p class="text-xs text-slate-500 mb-4">Xem riêng biểu đồ giá bán ra của từng thương hiệu vàng — đơn vị: triệu VNĐ/lượng</p>

    <div class="grid gap-5 sm:grid-cols-2">
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#b8860b] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#b8860b]"></span> SJC</h3>
            <div id="brandChart_SJC" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#3b82f6] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#3b82f6]"></span> DOJI</h3>
            <div id="brandChart_DOJI" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#15803d] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#15803d]"></span> PNJ</h3>
            <div id="brandChart_PNJ" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#dc2626] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#dc2626]"></span> BTMC</h3>
            <div id="brandChart_BTMC" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#7c3aed] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#7c3aed]"></span> Phú Quý</h3>
            <div id="brandChart_PhuQuy" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#ea580c] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#ea580c]"></span> Mi Hồng</h3>
            <div id="brandChart_MiHong" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#0891b2] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#0891b2]"></span> Bảo Tín MH</h3>
            <div id="brandChart_BaoTinMH" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h3 class="text-sm font-semibold text-[#be185d] mb-1 flex items-center gap-1"><span class="inline-block w-3 h-0.5 rounded bg-[#be185d]"></span> Ngọc Thẩm</h3>
            <div id="brandChart_NgocTham" style="height:200px"><div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Đang tải…</div></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var bPeriod = @json($brandPeriod);
    var brandMap = {
        'SJC': { id: 'brandChart_SJC', color: '#b8860b' },
        'DOJI': { id: 'brandChart_DOJI', color: '#3b82f6' },
        'PNJ': { id: 'brandChart_PNJ', color: '#15803d' },
        'BTMC': { id: 'brandChart_BTMC', color: '#dc2626' },
        'Phú Quý': { id: 'brandChart_PhuQuy', color: '#7c3aed' },
        'Mi Hồng': { id: 'brandChart_MiHong', color: '#ea580c' },
        'Bảo Tín MH': { id: 'brandChart_BaoTinMH', color: '#0891b2' },
        'Ngọc Thẩm': { id: 'brandChart_NgocTham', color: '#be185d' }
    };

    function waitForAmCharts(cb) {
        if (window.am5 && window.am5xy && window.am5themes_Animated) { cb(); }
        else { setTimeout(function() { waitForAmCharts(cb); }, 200); }
    }

    fetch('/api/v1/all-brands-chart?period=' + encodeURIComponent(bPeriod))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data || !data.length) return;
            waitForAmCharts(function() { renderBrandCharts(data); });
        });

    function renderBrandCharts(data) {
        var isToday = bPeriod === 'today';
        Object.keys(brandMap).forEach(function(brand) {
            var cfg = brandMap[brand];
            var holder = document.getElementById(cfg.id);
            if (!holder) return;

            var vals = data.filter(function(d) { return d[brand] != null; });
            if (!vals.length) {
                holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-xs text-slate-400">Chưa có dữ liệu</div>';
                return;
            }

            holder.innerHTML = '';
            var root = am5.Root.new(cfg.id);
            if (root._logo) root._logo.dispose();
            root.setThemes([am5themes_Animated.new(root)]);

            var chart = root.container.children.push(
                am5xy.XYChart.new(root, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX', paddingLeft: 0 })
            );

            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                baseInterval: { timeUnit: isToday ? 'minute' : 'day', count: 1 },
                renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 50 }),
                dateFormats: isToday ? { minute: 'HH:mm', hour: 'HH:mm' } : { month: 'MM/yy', day: 'dd/MM' },
                periodChangeDateFormats: isToday ? { minute: 'HH:mm', hour: 'dd/MM HH:mm' } : { month: 'MM/yy', day: 'dd/MM' },
            }));
            xAxis.get('renderer').labels.template.setAll({ fontSize: 10, fill: am5.color(0x94a3b8) });

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {}),
                numberFormat: '#,###.#',
                extraMin: 0.02, extraMax: 0.02,
            }));
            yAxis.get('renderer').labels.template.setAll({ fontSize: 10, fill: am5.color(0x94a3b8) });

            var color = am5.color(cfg.color);
            var series = chart.series.push(am5xy.LineSeries.new(root, {
                name: brand, xAxis: xAxis, yAxis: yAxis,
                valueYField: 'v', valueXField: 'ts',
                stroke: color, fill: color,
                tooltip: am5.Tooltip.new(root, { labelText: '{valueY} tr', pointerOrientation: 'horizontal', getFillFromSprite: false }),
            }));
            series.get('tooltip').get('background').setAll({ fill: am5.color(0x0f172a), fillOpacity: 0.9, stroke: am5.color(0x0f172a) });
            series.get('tooltip').label.setAll({ fill: am5.color(0xffffff), fontSize: 11 });
            series.strokes.template.setAll({ strokeWidth: 2 });

            series.fills.template.setAll({ fillOpacity: 0.08, visible: true });

            var chartData = data.map(function(d) {
                return { ts: new Date(d.date).getTime(), v: d[brand] };
            }).filter(function(d) { return d.v != null; });
            series.data.setAll(chartData);

            // Stats bar
            var values = chartData.map(function(d) { return d.v; });
            var high = Math.max.apply(null, values);
            var low = Math.min.apply(null, values);
            var last = values[values.length - 1];
            var first = values[0];
            var change = first > 0 ? ((last - first) / first * 100) : 0;

            var statsDiv = document.createElement('div');
            statsDiv.className = 'flex justify-between text-xs mt-1 text-slate-500';
            statsDiv.innerHTML = '<span>Cao: <b class="text-slate-700">' + high.toFixed(2) + '</b></span>'
                + '<span>Thấp: <b class="text-slate-700">' + low.toFixed(2) + '</b></span>'
                + '<span>Hiện tại: <b class="text-slate-700">' + last.toFixed(2) + '</b></span>'
                + '<span class="' + (change >= 0 ? 'text-emerald-600' : 'text-rose-600') + '">' + (change >= 0 ? '+' : '') + change.toFixed(1) + '%</span>';
            holder.parentNode.appendChild(statsDiv);

            chart.appear(600, 50);
        });
    }
});
</script>
@endpush
