@php
    $usCard = $snapshot['usCard'] ?? null;
    $globalMarkets = $snapshot['globalMarkets'] ?? [];
    $wpDetail = $snapshot['worldPriceDetail'] ?? [];
    $xauDetail = $wpDetail['XAU/USD'] ?? null;
    $macroFactors = $snapshot['macroFactors'] ?? [];
@endphp

{{-- Hero: XAU/USD --}}
@if ($usCard)
@php $uv = $usCard['variants'][$usCard['selected']] ?? collect($usCard['variants'])->first(); @endphp
<div class="rounded-sm border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-5">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-semibold text-blue-800">{{ $usCard['title'] }}</p>
            @if ($uv)
                <p class="mt-2 text-4xl font-bold text-blue-900">{{ number_format($uv['price'], 2) }} <small class="text-lg font-normal text-blue-700">{{ $uv['unit'] ?? 'USD/oz' }}</small></p>
                <p class="mt-2 text-sm font-bold {{ str_starts_with($uv['dayChangeLabel'] ?? '', '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $uv['dayChangeLabel'] ?? '' }}</p>
            @endif
        </div>
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold {{ $usCard['trendPercent'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
            <i data-lucide="{{ $usCard['trendPercent'] >= 0 ? 'trending-up' : 'trending-down' }}" class="mr-1 h-3 w-3"></i>
            {{ sprintf('%+.2f%%', $usCard['trendPercent']) }}
        </span>
    </div>
</div>
@endif

{{-- Bảng giá kim loại quý quốc tế --}}
@if (count($globalMarkets) > 0)
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061]">
            <i data-lucide="globe" class="h-5 w-5"></i> Giá kim loại quý quốc tế
        </h2>
        <span class="flex items-center gap-1 text-xs text-slate-500">
            <i data-lucide="clock" class="h-3 w-3"></i> {{ now()->format('H:i d/m/Y') }}
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[520px] text-sm">
            <caption class="sr-only">Giá kim loại quý quốc tế hôm nay {{ now()->format('d/m/Y') }}</caption>
            <thead>
                <tr class="bg-[#f5f5f5]">
                    <th class="border-b border-[#bcbcbc] p-3 text-left font-semibold text-slate-700">Symbol</th>
                    <th class="border-b border-[#bcbcbc] p-3 text-right font-semibold text-slate-700">Giá</th>
                    <th class="border-b border-[#bcbcbc] p-3 text-right font-semibold text-slate-700">Thay đổi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($globalMarkets as $gm)
                    <tr class="transition hover:bg-[#f5f5f5]">
                        <td class="border-b border-[#ebebeb] p-3 font-medium">{{ $gm['name'] }}</td>
                        <td class="border-b border-[#ebebeb] p-3 text-right tabular-nums font-bold">{{ $gm['price'] }}</td>
                        <td class="border-b border-[#ebebeb] p-3 text-right tabular-nums font-bold {{ $gm['trend'] === 'up' ? 'text-[#008236]' : 'text-[#e7000b]' }}">{{ $gm['change'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Biểu đồ XAU/USD 30 ngày --}}
@if ($xauDetail && count($xauDetail['chartDates'] ?? []) > 1)
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061] mb-4">
        <i data-lucide="chart-line" class="h-5 w-5"></i> Biểu đồ XAU/USD 30 ngày
    </h2>
    <div id="worldChartXauUsd" class="h-72 w-full"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.am5) return;
            var root = am5.Root.new('worldChartXauUsd');
            root.setThemes([am5themes_Animated.new(root)]);
            var chart = root.container.children.push(am5xy.XYChart.new(root, { panX: false, panY: false, wheelY: 'none' }));
            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, { categoryField: 'date', renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 60 }) }));
            xAxis.data.setAll(@json(collect($xauDetail['chartDates'])->map(fn($d) => ['date' => $d])->toArray()));
            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, { renderer: am5xy.AxisRendererY.new(root, {}) }));
            var series = chart.series.push(am5xy.LineSeries.new(root, { name: 'XAU/USD', xAxis: xAxis, yAxis: yAxis, valueYField: 'price', categoryXField: 'date', stroke: am5.color('#3b82f6'), tooltip: am5.Tooltip.new(root, { labelText: '{categoryX}: {valueY}' }) }));
            series.strokes.template.setAll({ strokeWidth: 2 });
            var chartData = @json(collect($xauDetail['chartDates'])->zip($xauDetail['chartPrices'])->map(fn($pair) => ['date' => $pair[0], 'price' => $pair[1]])->toArray());
            series.data.setAll(chartData);
            chart.appear(500);
        });
    </script>
</div>
@endif

{{-- Yếu tố ảnh hưởng --}}
@if (count($macroFactors) > 0)
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061] mb-4">
        <i data-lucide="activity" class="h-5 w-5"></i> Yếu tố ảnh hưởng giá vàng
    </h2>
    <div class="grid gap-3 sm:grid-cols-2">
        @foreach ($macroFactors as $mf)
            <div class="flex items-start gap-3 rounded-sm border border-slate-200 p-3">
                <span class="mt-0.5 inline-block h-2.5 w-2.5 rounded-full {{ ($mf['signal'] ?? '') === 'positive' ? 'bg-emerald-500' : (($mf['signal'] ?? '') === 'negative' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $mf['factor'] }}</p>
                    <p class="text-sm text-slate-600">{{ $mf['value'] }} — {{ $mf['impact'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif