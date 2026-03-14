{{-- Shared world-price detail section: receives $symbolKey, $symbolSlug from parent --}}
@php
    $worldData = $snapshot['worldPriceDetail'] ?? [];
    $item = $worldData[$symbolKey] ?? null;
    $allSymbols = [
        'XAU/USD' => 'xau-usd', 'XAU/EUR' => 'xau-eur', 'XAU/GBP' => 'xau-gbp',
        'XAU/CNY' => 'xau-cny', 'XAU/JPY' => 'xau-jpy', 'XAG/USD' => 'xag-usd',
        'XPT/USD' => 'xpt-usd', 'XPD/USD' => 'xpd-usd',
    ];
@endphp

@if ($item && $item['price'] > 0)
{{-- Price hero --}}
<div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-5 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700"><i class="block h-2 w-2 rounded-full bg-emerald-500"></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">Cập nhật {{ $item['updatedAt'] ? \Carbon\Carbon::parse($item['updatedAt'])->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
    </div>
    <p class="text-sm font-medium text-blue-800">{{ $symbolKey }} — {{ $item['name'] }}</p>
    <p class="mt-1 text-4xl font-bold text-blue-900">{{ number_format($item['price'], 2) }} <small class="text-lg font-normal text-blue-700">{{ $item['unit'] }}</small></p>
    @php $up = ($item['changePercent'] ?? 0) >= 0; @endphp
    <p class="mt-2 text-sm font-bold {{ $up ? 'text-emerald-600' : 'text-rose-600' }}">{{ sprintf('%+.2f', $item['changeAmount']) }} ({{ sprintf('%+.2f%%', $item['changePercent']) }})</p>
</div>

{{-- Main chart --}}
<div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3">Biểu đồ {{ $item['name'] }}</h2>
    {{-- Period buttons --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach (['7d' => '7 ngày', '1m' => '1 tháng', '3m' => '3 tháng', '6m' => '6 tháng', '1y' => '1 năm'] as $pKey => $pLabel)
        <button data-period="{{ $pKey }}" class="world-period-btn rounded-full border px-4 py-1.5 text-xs font-semibold transition {{ $pKey === '1m' ? 'border-[#b8860b] bg-[#b8860b] text-white' : 'border-[#ccc] bg-white text-[#555] hover:border-[#b8860b] hover:text-[#b8860b]' }}">{{ $pLabel }}</button>
        @endforeach
    </div>
    <div id="worldPriceAmChart" class="w-full" style="min-height:400px"></div>
</div>

{{-- Stats grid --}}
@if (!empty($item['chartPrices']))
@php
    $prices = $item['chartPrices'];
    $highs = $item['chartHigh'];
    $lows = $item['chartLow'];
    $high30 = max($highs);
    $low30 = min($lows);
    $first = $prices[0] ?? 0;
    $last = end($prices);
    $change30 = $first > 0 ? round(($last - $first) / $first * 100, 2) : 0;
@endphp
<div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center">
        <p class="text-xs font-semibold text-slate-500">Giá hiện tại</p>
        <p class="mt-1 text-2xl font-bold text-slate-900 tabular-nums">{{ number_format($item['price'], 2) }}</p>
        <p class="text-xs text-slate-500">{{ $item['unit'] }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center">
        <p class="text-xs font-semibold text-slate-500">Cao nhất 30 ngày</p>
        <p class="mt-1 text-2xl font-bold text-emerald-600 tabular-nums">{{ number_format($high30, 2) }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center">
        <p class="text-xs font-semibold text-slate-500">Thấp nhất 30 ngày</p>
        <p class="mt-1 text-2xl font-bold text-rose-600 tabular-nums">{{ number_format($low30, 2) }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center">
        <p class="text-xs font-semibold text-slate-500">Biến động 30 ngày</p>
        <p class="mt-1 text-2xl font-bold {{ $change30 >= 0 ? 'text-emerald-600' : 'text-rose-600' }} tabular-nums">{{ sprintf('%+.2f%%', $change30) }}</p>
    </div>
</div>
@endif

{{-- Price table + high/low --}}
@if (!empty($item['chartDates']))
<div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3">Lịch sử giá {{ $symbolKey }}</h2>
    <div class="overflow-x-auto max-h-80">
        <table class="w-full text-sm">
            <thead class="sticky top-0 bg-white">
                <tr class="border-b border-slate-200 text-left text-xs font-semibold uppercase text-slate-500">
                    <th class="py-2 pr-3">Ngày</th>
                    <th class="py-2 pr-3 text-right">Trung bình</th>
                    <th class="py-2 pr-3 text-right">Cao nhất</th>
                    <th class="py-2 text-right">Thấp nhất</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_reverse(array_keys($item['chartDates'])) as $idx)
                <tr class="border-b border-slate-100">
                    <td class="py-2 pr-3 text-slate-600">{{ $item['chartDates'][$idx] }}</td>
                    <td class="py-2 pr-3 text-right font-medium tabular-nums">{{ number_format($item['chartPrices'][$idx], 2) }}</td>
                    <td class="py-2 pr-3 text-right tabular-nums text-emerald-600">{{ number_format($item['chartHigh'][$idx], 2) }}</td>
                    <td class="py-2 text-right tabular-nums text-rose-600">{{ number_format($item['chartLow'][$idx], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Other symbols nav --}}
<div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3">Các mã khác</h2>
    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($allSymbols as $sym => $sl)
        @if ($sym !== $symbolKey)
        @php $other = $worldData[$sym] ?? null; $otherUp = ($other['changePercent'] ?? 0) >= 0; @endphp
        <a href="/gia-vang-the-gioi/{{ $sl }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 text-sm transition hover:border-blue-300 hover:bg-blue-50">
            <span class="font-semibold text-slate-700">{{ $sym }}</span>
            @if ($other)
            <span class="font-bold tabular-nums {{ $otherUp ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($other['price'], 2) }}</span>
            @endif
        </a>
        @endif
        @endforeach
    </div>
</div>

@else
<div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
    <p class="text-lg text-slate-500">Chưa có dữ liệu cho {{ $symbolKey }}. Hệ thống đang thu thập dữ liệu.</p>
    <a href="/gia-vang-the-gioi" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:underline">← Quay lại tổng hợp</a>
</div>
@endif

{{-- amCharts rendering script --}}
@php
    $chartInitData = collect($item['chartDates'] ?? [])->map(function($d, $i) use ($item) {
        return ['date' => $d, 'avg' => $item['chartPrices'][$i] ?? 0, 'high' => $item['chartHigh'][$i] ?? 0, 'low' => $item['chartLow'][$i] ?? 0];
    })->values();
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    var symbolKey = @json($symbolKey);
    var initialData = @json($chartInitData);

    var chartRoot = null;

    function renderWorldChart(data) {
        var holder = document.getElementById('worldPriceAmChart');
        if (!holder) return;
        if (!window.am5 || !window.am5xy || !window.am5themes_Animated) {
            setTimeout(function() { renderWorldChart(data); }, 200);
            return;
        }
        if (!data || !data.length) {
            holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm font-semibold text-slate-500">Chưa có dữ liệu biểu đồ.</div>';
            return;
        }
        if (chartRoot) { chartRoot.dispose(); chartRoot = null; }

        chartRoot = am5.Root.new('worldPriceAmChart');
        if (chartRoot._logo) chartRoot._logo.dispose();
        chartRoot.setThemes([am5themes_Animated.new(chartRoot)]);

        var chart = chartRoot.container.children.push(
            am5xy.XYChart.new(chartRoot, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX' })
        );

        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(chartRoot, {
            baseInterval: { timeUnit: 'day', count: 1 },
            renderer: am5xy.AxisRendererX.new(chartRoot, { minGridDistance: 60 }),
            dateFormats: { month: 'MM/yyyy', day: 'dd/MM' },
            periodChangeDateFormats: { month: 'MM/yyyy', day: 'dd/MM' },
        }));

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(chartRoot, {
            renderer: am5xy.AxisRendererY.new(chartRoot, {}),
            numberFormat: '#,###.##',
        }));

        var cursor = chart.set('cursor', am5xy.XYCursor.new(chartRoot, { behavior: 'zoomX', xAxis: xAxis }));
        cursor.lineY.set('visible', false);

        // Avg line (blue solid)
        var avgSeries = chart.series.push(am5xy.LineSeries.new(chartRoot, {
            name: 'Trung bình',
            xAxis: xAxis, yAxis: yAxis,
            valueYField: 'avg', valueXField: 'dateTs',
            stroke: am5.color(0x3b82f6), fill: am5.color(0x3b82f6),
            tooltip: am5.Tooltip.new(chartRoot, {
                labelText: "[bold]{valueX.formatDate('dd/MM/yyyy')}[/]\nTB: [bold #3b82f6]{avg}[/]\nCao: [bold #15803d]{high}[/]\nThấp: [bold #dc2626]{low}[/]",
                pointerOrientation: 'horizontal',
                getFillFromSprite: false, getStrokeFromSprite: false,
            }),
        }));
        avgSeries.get('tooltip').get('background').setAll({ fill: am5.color(0x000000), fillOpacity: 0.9, stroke: am5.color(0x000000) });
        avgSeries.get('tooltip').label.setAll({ fill: am5.color(0xffffff) });

        // High line (green dashed)
        var highSeries = chart.series.push(am5xy.LineSeries.new(chartRoot, {
            name: 'Cao nhất', xAxis: xAxis, yAxis: yAxis,
            valueYField: 'high', valueXField: 'dateTs',
            stroke: am5.color(0x15803d), fill: am5.color(0x15803d),
        }));
        highSeries.strokes.template.setAll({ strokeDasharray: [6, 4] });

        // Low line (red dashed)
        var lowSeries = chart.series.push(am5xy.LineSeries.new(chartRoot, {
            name: 'Thấp nhất', xAxis: xAxis, yAxis: yAxis,
            valueYField: 'low', valueXField: 'dateTs',
            stroke: am5.color(0xdc2626), fill: am5.color(0xdc2626),
        }));
        lowSeries.strokes.template.setAll({ strokeDasharray: [6, 4] });

        var chartData = data.map(function(p) {
            return { dateTs: new Date(p.date).getTime(), avg: p.avg, high: p.high, low: p.low };
        });
        avgSeries.data.setAll(chartData);
        highSeries.data.setAll(chartData);
        lowSeries.data.setAll(chartData);

        var legend = chart.children.unshift(am5.Legend.new(chartRoot, {
            centerX: am5.percent(50), x: am5.percent(50), marginBottom: 8,
        }));
        legend.data.setAll([avgSeries, highSeries, lowSeries]);

        var scrollbar = am5.Scrollbar.new(chartRoot, { orientation: 'horizontal' });
        chart.set('scrollbarX', scrollbar);
        chart.bottomAxesContainer.children.push(scrollbar);

        chart.children.push(am5.Label.new(chartRoot, {
            text: 'giavanghn.com', x: am5.percent(100), centerX: am5.percent(100),
            y: am5.percent(100), centerY: am5.percent(100),
            paddingRight: 10, paddingBottom: 5, marginTop: 40,
            fontSize: 11, fill: am5.color(0x999999), opacity: 0.7,
        }));

        chart.appear(1000, 100);
    }

    renderWorldChart(initialData);

    // Period switching
    var activeCls = ['border-[#b8860b]', 'bg-[#b8860b]', 'text-white'];
    var inactiveCls = ['border-[#ccc]', 'bg-white', 'text-[#555]'];
    document.querySelectorAll('.world-period-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var period = btn.dataset.period;
            document.querySelectorAll('.world-period-btn').forEach(function(b) {
                activeCls.forEach(function(c) { b.classList.remove(c); });
                inactiveCls.forEach(function(c) { b.classList.add(c); });
            });
            inactiveCls.forEach(function(c) { btn.classList.remove(c); });
            activeCls.forEach(function(c) { btn.classList.add(c); });

            var holder = document.getElementById('worldPriceAmChart');
            if (chartRoot) { chartRoot.dispose(); chartRoot = null; }
            if (holder) holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải...</div>';

            fetch('/api/v1/world-chart?symbol=' + encodeURIComponent(symbolKey) + '&period=' + period)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (holder) holder.innerHTML = '';
                    renderWorldChart(data);
                })
                .catch(function(e) { console.error('World chart fetch error:', e); });
        });
    });
});
</script>
