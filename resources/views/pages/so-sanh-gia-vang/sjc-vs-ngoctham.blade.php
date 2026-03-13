@extends('gold.page-shell')

@section('page-label', 'So sánh')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $sjcCard = $snapshot['sjcCard'] ?? null;
    $otherCard = $snapshot['ngocthamCard'] ?? null;
    $otherName = 'Ngọc Thẩm';
    $otherColor = '#be185d';
    $otherSlug = 'ngoctham';

    $sjcV = $sjcCard['variants'][$sjcCard['selected'] ?? 'p0'] ?? null;
    $sjcSell = $sjcV['sell'] ?? 0;
    $sjcBuy = $sjcV['buy'] ?? 0;
    $sjcSpread = ($sjcSell - $sjcBuy) * 1e6;
    $sjcChange = $sjcV['dayChangeLabel'] ?? '';

    $otherV = $otherCard['variants'][$otherCard['selected'] ?? 'p0'] ?? null;
    $otherSell = $otherV['sell'] ?? 0;
    $otherBuy = $otherV['buy'] ?? 0;
    $otherSpread = ($otherSell - $otherBuy) * 1e6;
    $otherChange = $otherV['dayChangeLabel'] ?? '';

    $diffSell = ($sjcSell - $otherSell) * 1e6;
    $diffBuy = ($sjcBuy - $otherBuy) * 1e6;
    $cheaperSell = $sjcSell <= $otherSell ? 'SJC' : $otherName;
    $cheaperBuy = $sjcBuy <= $otherBuy ? 'SJC' : $otherName;

    $sjcVariants = $sjcCard['variants'] ?? [];
    $otherVariants = $otherCard['variants'] ?? [];
    $sjcLabels = $sjcCard['labels'] ?? [];
    $otherLabels = $otherCard['labels'] ?? [];
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type":"Question","name":"Giá vàng SJC và Ngọc Thẩm hôm nay chênh nhau bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá bán vàng tại SJC và Ngọc Thẩm thường chênh lệch 0-500.000 VND/lượng. Ngọc Thẩm có giá vàng nhẫn 9999, vàng ta 990 và vàng 18K khá cạnh tranh tại khu vực TP.HCM."}},
        {"@@type":"Question","name":"Nên mua vàng ở SJC hay Ngọc Thẩm?","acceptedAnswer":{"@@type":"Answer","text":"SJC phù hợp cho đầu tư vàng miếng SJC 1 lượng nhờ thanh khoản cao nhất. Ngọc Thẩm có thế mạnh về vàng nhẫn 9999, vàng ta 990 và vàng trang sức 18K với giá cạnh tranh. Nếu ở TP.HCM và muốn đa dạng sản phẩm vàng, Ngọc Thẩm đáng xem xét."}},
        {"@@type":"Question","name":"Tiệm vàng Ngọc Thẩm ở đâu?","acceptedAnswer":{"@@type":"Answer","text":"Ngọc Thẩm là tiệm vàng uy tín tại TP.HCM, hoạt động nhiều năm trong lĩnh vực kinh doanh vàng bạc đá quý. Ngọc Thẩm kinh doanh đa dạng sản phẩm từ vàng miếng SJC, vàng nhẫn 9999, vàng ta 990 đến vàng trang sức 18K."}},
        {"@@type":"Question","name":"Vàng Ngọc Thẩm có uy tín không?","acceptedAnswer":{"@@type":"Answer","text":"Ngọc Thẩm là thương hiệu vàng bạc uy tín tại TP.HCM với lịch sử kinh doanh lâu năm. Giá niêm yết minh bạch, sản phẩm đa dạng từ vàng miếng SJC đến vàng ta và vàng trang sức."}}
    ]
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-gradient-to-br from-amber-50/80 to-pink-50/80 p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700"><i class="block h-2 w-2 rounded-full bg-emerald-500"></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">{{ $now }}</span>
    </div>
    <h2 class="text-2xl font-bold text-[#001061] mb-4">So sánh giá vàng SJC và Ngọc Thẩm hôm nay</h2>

    <div class="grid gap-4 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border-2 border-amber-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-amber-700 mb-1">SJC (bán ra)</p>
            <p class="text-3xl font-bold text-amber-900 tabular-nums">{{ number_format($sjcSell * 1e6, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">Mua: {{ number_format($sjcBuy * 1e6, 0, ',', '.') }}</span>
                <span class="font-bold {{ str_starts_with($sjcChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $sjcChange }}</span>
            </div>
        </div>
        <div class="rounded-sm border-2 border-pink-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-pink-700 mb-1">{{ $otherName }} (bán ra)</p>
            <p class="text-3xl font-bold text-pink-900 tabular-nums">{{ number_format($otherSell * 1e6, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">Mua: {{ number_format($otherBuy * 1e6, 0, ',', '.') }}</span>
                <span class="font-bold {{ str_starts_with($otherChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $otherChange }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-sm border {{ $diffSell >= 0 ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} p-3 text-center">
            <p class="text-xs font-medium {{ $diffSell >= 0 ? 'text-rose-700' : 'text-emerald-700' }}">Chênh lệch bán ra</p>
            <p class="text-xl font-bold {{ $diffSell >= 0 ? 'text-rose-800' : 'text-emerald-800' }} tabular-nums">{{ $diffSell >= 0 ? '+' : '' }}{{ number_format($diffSell, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-600">{{ $cheaperSell }} rẻ hơn</p>
        </div>
        <div class="rounded-sm border {{ $diffBuy >= 0 ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} p-3 text-center">
            <p class="text-xs font-medium {{ $diffBuy >= 0 ? 'text-rose-700' : 'text-emerald-700' }}">Chênh lệch mua vào</p>
            <p class="text-xl font-bold {{ $diffBuy >= 0 ? 'text-rose-800' : 'text-emerald-800' }} tabular-nums">{{ $diffBuy >= 0 ? '+' : '' }}{{ number_format($diffBuy, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-600">{{ $cheaperBuy }} cao hơn</p>
        </div>
        <div class="rounded-sm border border-indigo-200 bg-indigo-50 p-3 text-center">
            <p class="text-xs font-medium text-indigo-700">Spread thấp hơn</p>
            <p class="text-xl font-bold text-indigo-800">{{ $sjcSpread <= $otherSpread ? 'SJC' : $otherName }}</p>
            <p class="text-xs text-slate-600">{{ number_format(min($sjcSpread, $otherSpread), 0, ',', '.') }} vs {{ number_format(max($sjcSpread, $otherSpread), 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Bảng so sánh --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-slate-900 mb-3">So sánh chi tiết theo loại sản phẩm</h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-slate-700">Loại vàng</th>
                    <th class="p-3 text-right font-semibold text-amber-700">SJC Mua</th>
                    <th class="p-3 text-right font-semibold text-amber-700">SJC Bán</th>
                    <th class="p-3 text-right font-semibold text-pink-700">{{ $otherName }} Mua</th>
                    <th class="p-3 text-right font-semibold text-pink-700">{{ $otherName }} Bán</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Chênh (Bán)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($sjcVariants as $key => $sv)
                @php
                    $ov = $otherVariants[$key] ?? null;
                    $label = $sjcLabels[$key] ?? $key;
                    $sellDiff = $ov ? ($sv['sell'] - $ov['sell']) * 1e6 : null;
                @endphp
                <tr>
                    <td class="p-3 text-slate-800 font-medium">{{ $label }}</td>
                    <td class="p-3 text-right tabular-nums">{{ $sv['buy'] > 0 ? number_format($sv['buy'] * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $sv['sell'] > 0 ? number_format($sv['sell'] * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums">{{ $ov && $ov['buy'] > 0 ? number_format($ov['buy'] * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $ov && $ov['sell'] > 0 ? number_format($ov['sell'] * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-bold {{ $sellDiff !== null ? ($sellDiff >= 0 ? 'text-rose-600' : 'text-emerald-600') : 'text-slate-400' }}">
                        @if ($sellDiff !== null) {{ $sellDiff >= 0 ? '+' : '' }}{{ number_format($sellDiff, 0, ',', '.') }} @else — @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-400">Đơn vị: VNĐ/Lượng · Cập nhật: {{ $now }}</p>
</div>

{{-- Biểu đồ --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-slate-900 mb-2">Biểu đồ giá vàng SJC vs {{ $otherName }} (7 ngày)</h2>
    <p class="text-xs text-slate-500 mb-3">So sánh diễn biến giá bán ra giữa SJC và {{ $otherName }} trong 7 ngày gần nhất</p>
    <div id="brandCompareChart" class="w-full" style="height:360px">
        <div class="flex items-center justify-center h-full text-slate-400">
            <svg class="animate-spin h-6 w-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Đang tải biểu đồ...
        </div>
    </div>
</div>

{{-- Phân tích --}}
<article class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-headings:text-[#001061] prose-p:text-slate-700">
    <h2 class="!mt-0">Phân tích so sánh giá vàng SJC và Ngọc Thẩm</h2>

    <h3>Ngọc Thẩm — Đa dạng sản phẩm vàng TP.HCM</h3>
    <p><strong>Ngọc Thẩm</strong> là doanh nghiệp kinh doanh vàng bạc đá quý uy tín tại TP.HCM, nổi bật với dải sản phẩm đa dạng từ vàng miếng đến vàng trang sức. Đặc điểm:</p>
    <ul>
        <li><strong>Đa dạng sản phẩm:</strong> Ngoài vàng miếng SJC, Ngọc Thẩm kinh doanh vàng nhẫn 9999, vàng ta 990, vàng 18K (750) và vàng trang sức cao cấp.</li>
        <li><strong>Giá vàng ta 990:</strong> Ngọc Thẩm là một trong số ít thương hiệu niêm yết giá vàng ta 990, phục vụ thị trường truyền thống miền Nam.</li>
        <li><strong>Vàng 18K trang sức:</strong> Ngọc Thẩm có thế mạnh về vàng trang sức 18K với mẫu mã đa dạng, phù hợp cả đầu tư và trang sức.</li>
        <li><strong>Giá cạnh tranh:</strong> Ngọc Thẩm thường có giá vàng nhẫn 9999 cạnh tranh tại TP.HCM.</li>
    </ul>

    <h3>SJC — Chuẩn vàng miếng, thanh khoản cao nhất</h3>
    <p>Vàng miếng SJC vẫn là sản phẩm có thanh khoản tốt nhất trên thị trường vàng Việt Nam. SJC tập trung vào vàng miếng và nhẫn SJC, không kinh doanh vàng ta hay vàng 18K trang sức. Do đó, nếu cần sản phẩm đa dạng, Ngọc Thẩm có lợi thế hơn.</p>

    <h3>Vàng ta 990 — Sản phẩm truyền thống miền Nam</h3>
    <p>Vàng ta (hay vàng 990) có hàm lượng 99% vàng, là sản phẩm truyền thống được ưa chuộng tại miền Nam. Giá vàng ta thường rẻ hơn vàng 9999 do hàm lượng thấp hơn. Ngọc Thẩm là một trong những nơi niêm yết giá vàng ta 990 minh bạch, phục vụ nhu cầu mua bán truyền thống.</p>

    <h3>Khi nào chọn SJC, khi nào chọn Ngọc Thẩm?</h3>
    <ul>
        <li><strong>Đầu tư vàng miếng SJC:</strong> SJC phù hợp nhờ thanh khoản cao. So sánh giá bán tại cả hai nơi.</li>
        <li><strong>Mua vàng nhẫn 9999:</strong> So sánh giá tại cả hai — Ngọc Thẩm đôi khi có giá tốt hơn.</li>
        <li><strong>Mua vàng ta 990, vàng 18K:</strong> Ngọc Thẩm có lợi thế rõ rệt với nhiều sản phẩm truyền thống mà SJC không chuyên.</li>
        <li><strong>Trang sức vàng:</strong> Ngọc Thẩm có mẫu mã đa dạng hơn SJC trong phân khúc trang sức.</li>
    </ul>

    <h3>So sánh spread</h3>
    <p>Hiện tại: SJC spread <strong>{{ number_format($sjcSpread, 0, ',', '.') }} VNĐ</strong> vs Ngọc Thẩm <strong>{{ number_format($otherSpread, 0, ',', '.') }} VNĐ</strong>. Spread phụ thuộc vào loại sản phẩm — vàng miếng SJC thường có spread hẹp nhất.</p>
</article>

{{-- FAQ --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-[#001061] mb-4">Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng SJC và Ngọc Thẩm hôm nay chênh nhau bao nhiêu?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Chênh lệch giá bán vàng giữa SJC và Ngọc Thẩm thường 0-500.000 VND/lượng. Ngọc Thẩm có giá cạnh tranh cho vàng nhẫn 9999 tại TP.HCM.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Ngọc Thẩm có bán vàng ta 990 không?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Có, Ngọc Thẩm là một trong số ít thương hiệu niêm yết giá vàng ta 990. Vàng ta có hàm lượng 99% vàng, là sản phẩm truyền thống phổ biến tại miền Nam.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Vàng Ngọc Thẩm có uy tín không?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Có, Ngọc Thẩm là thương hiệu vàng bạc đá quý uy tín tại TP.HCM, kinh doanh lâu năm. Sản phẩm đa dạng từ vàng miếng SJC đến vàng ta 990, vàng 18K.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Nên mua vàng ở SJC hay Ngọc Thẩm?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Nếu đầu tư vàng miếng SJC, chọn nơi giá rẻ hơn. Nếu cần sản phẩm đa dạng (vàng ta, vàng 18K, trang sức), Ngọc Thẩm có lợi thế hơn SJC.</p>
        </details>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.am5 || !window.am5xy) { document.getElementById('brandCompareChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; return; }
    Promise.all([
        fetch('/api/v1/brand-chart?brand=sjc&period=7d').then(function(r){return r.json();}),
        fetch('/api/v1/brand-chart?brand={{ $otherSlug }}&period=7d').then(function(r){return r.json();})
    ]).then(function(results) {
        function parse(res) { return (Array.isArray(res) ? res : res.data || []).filter(function(p){return p.sell > 0;}); }
        var sjcData = parse(results[0]), otherData = parse(results[1]);
        document.getElementById('brandCompareChart').innerHTML = '';
        var root = am5.Root.new('brandCompareChart');
        if (root._logo) root._logo.dispose();
        root.setThemes([am5themes_Animated.new(root)]);
        var chart = root.container.children.push(am5xy.XYChart.new(root, { panX:false, panY:false, wheelX:'none', wheelY:'none', layout:root.verticalLayout }));
        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, { baseInterval:{timeUnit:'day',count:1}, renderer:am5xy.AxisRendererX.new(root,{minGridDistance:60}), dateFormats:{day:'dd/MM'}, periodChangeDateFormats:{day:'dd/MM'} }));
        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, { renderer:am5xy.AxisRendererY.new(root,{}), numberFormat:'#,###.##' }));
        function addSeries(name, data, color) {
            var s = chart.series.push(am5xy.LineSeries.new(root, { name:name, xAxis:xAxis, yAxis:yAxis, valueYField:'value', valueXField:'dateTs', stroke:am5.color(color), tooltip:am5.Tooltip.new(root,{labelText:name+': {valueY.formatNumber("#,###.##")} tr'}) }));
            s.strokes.template.setAll({strokeWidth:2.5});
            s.data.setAll(data.map(function(d){ return {dateTs:new Date(d.date).getTime(), value:d.sell}; }));
            return s;
        }
        addSeries('SJC', sjcData, '#b8860b');
        addSeries('{{ $otherName }}', otherData, '{{ $otherColor }}');
        var legend = chart.children.push(am5.Legend.new(root, {centerX:am5.percent(50), x:am5.percent(50), y:am5.percent(100)}));
        legend.labels.template.setAll({fontSize:11});
        legend.data.setAll(chart.series.values);
        chart.set('cursor', am5xy.XYCursor.new(root, {}));
        chart.appear(800, 100);
    }).catch(function(){ document.getElementById('brandCompareChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không thể tải dữ liệu biểu đồ</p>'; });
});
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
