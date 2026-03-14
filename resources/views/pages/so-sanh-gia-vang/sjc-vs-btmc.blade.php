@extends('gold.page-shell')

@section('page-label', 'So sánh')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $sjcCard = $snapshot['sjcCard'] ?? null;
    $otherCard = $snapshot['btmcCard'] ?? null;
    $otherName = 'BTMC';
    $otherColor = '#dc2626';
    $otherSlug = 'btmc';

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
    "@@type": "WebPage",
    "name": "{{ $title }}",
    "description": "{{ $description }}",
    "url": "{{ url('/' . $path) }}",
    "inLanguage": "vi",
    "dateModified": "{{ now()->toIso8601String() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "GiaVangHN",
        "url": "{{ url('/') }}"
    },
    "breadcrumb": {
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {"@@type": "ListItem", "position": 1, "name": "Trang chủ", "item": "{{ url('/') }}"},
            {"@@type": "ListItem", "position": 2, "name": "So sánh giá vàng", "item": "{{ url('/so-sanh-gia-vang') }}"},
            {"@@type": "ListItem", "position": 3, "name": "{{ $title }}", "item": "{{ url('/' . $path) }}"}
        ]
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type":"Question","name":"Giá vàng SJC và Bảo Tín Minh Châu chênh nhau bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá vàng miếng SJC tại SJC và BTMC thường chênh lệch từ 0 đến 500.000 VND/lượng. Giá vàng nhẫn 9999 giữa hai nơi cũng có mức chênh tương tự. BTMC thường có giá cạnh tranh cho vàng nhẫn Rồng Thăng Long."}},
        {"@@type":"Question","name":"Nên mua vàng ở SJC hay Bảo Tín Minh Châu?","acceptedAnswer":{"@@type":"Answer","text":"SJC phù hợp cho đầu tư vàng miếng 1 lượng với thanh khoản cao. BTMC nổi tiếng với nhẫn vàng Rồng Thăng Long 9999 giá cạnh tranh. Nếu ở Hà Nội, BTMC có nhiều cửa hàng tại các quận nội thành tiện cho giao dịch."}},
        {"@@type":"Question","name":"Vàng nhẫn Rồng Thăng Long BTMC là gì?","acceptedAnswer":{"@@type":"Answer","text":"Nhẫn vàng Rồng Thăng Long là sản phẩm vàng 9999 thương hiệu riêng của Bảo Tín Minh Châu, khắc hình rồng đặc trưng Thăng Long. Sản phẩm có nhiều trọng lượng từ 1 chỉ đến 5 chỉ, được người Hà Nội ưa chuộng."}},
        {"@@type":"Question","name":"BTMC có bán vàng miếng SJC không?","acceptedAnswer":{"@@type":"Answer","text":"Có, Bảo Tín Minh Châu là một trong những đại lý bán vàng miếng SJC lớn tại Hà Nội. BTMC kinh doanh cả vàng miếng SJC và vàng nhẫn 9999 thương hiệu riêng."}}
    ]
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-gradient-to-br from-amber-50/80 to-red-50/80 p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700"><i class="block h-2 w-2 rounded-full bg-emerald-500"></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">{{ $now }}</span>
    </div>
    <h2 class="text-2xl font-bold text-[#001061] mb-4">So sánh giá vàng SJC và Bảo Tín Minh Châu hôm nay</h2>

    <div class="grid gap-5 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border-2 border-amber-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-amber-700 mb-1">SJC (bán ra)</p>
            <p class="text-3xl font-bold text-amber-900 tabular-nums">{{ number_format($sjcSell * 1e6, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">Mua: {{ number_format($sjcBuy * 1e6, 0, ',', '.') }}</span>
                <span class="font-bold {{ str_starts_with($sjcChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $sjcChange }}</span>
            </div>
        </div>
        <div class="rounded-sm border-2 border-red-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-red-700 mb-1">{{ $otherName }} (bán ra)</p>
            <p class="text-3xl font-bold text-red-900 tabular-nums">{{ number_format($otherSell * 1e6, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">Mua: {{ number_format($otherBuy * 1e6, 0, ',', '.') }}</span>
                <span class="font-bold {{ str_starts_with($otherChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $otherChange }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
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

{{-- Bảng so sánh theo loại --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-slate-900 mb-3">So sánh chi tiết theo loại sản phẩm</h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-slate-700">Loại vàng</th>
                    <th class="p-3 text-right font-semibold text-amber-700">SJC Mua</th>
                    <th class="p-3 text-right font-semibold text-amber-700">SJC Bán</th>
                    <th class="p-3 text-right font-semibold text-red-700">{{ $otherName }} Mua</th>
                    <th class="p-3 text-right font-semibold text-red-700">{{ $otherName }} Bán</th>
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
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
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
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-headings:text-[#001061] prose-p:text-slate-700">
    <h2 class="!mt-0">Phân tích so sánh giá vàng SJC và Bảo Tín Minh Châu</h2>

    <h3>Bảo Tín Minh Châu — Thương hiệu vàng uy tín Hà Nội</h3>
    <p><strong>Bảo Tín Minh Châu (BTMC)</strong> là doanh nghiệp kinh doanh vàng bạc đá quý lâu đời tại Hà Nội, nổi tiếng với nhẫn vàng <strong>Rồng Thăng Long</strong>. Đặc điểm nổi bật:</p>
    <ul>
        <li><strong>Nhẫn Rồng Thăng Long 9999:</strong> Sản phẩm thương hiệu độc quyền, khắc hình rồng Thăng Long, được ưa chuộng tại miền Bắc.</li>
        <li><strong>Giá cạnh tranh:</strong> BTMC thường niêm yết giá vàng nhẫn 9999 ở mức cạnh tranh so với nhiều thương hiệu khác.</li>
        <li><strong>Mạng lưới Hà Nội:</strong> Hệ thống cửa hàng tập trung tại các quận nội thành Hà Nội, tiện cho người mua tại miền Bắc.</li>
        <li><strong>Chính sách mua lại:</strong> BTMC cam kết mua lại sản phẩm vàng thương hiệu với giá niêm yết, đảm bảo thanh khoản.</li>
    </ul>

    <h3>SJC — Thanh khoản và uy tín quốc gia</h3>
    <p>Vàng miếng SJC vẫn là lựa chọn hàng đầu cho đầu tư lớn nhờ thanh khoản cao nhất thị trường. Mọi ngân hàng và tiệm vàng đều chấp nhận mua bán vàng miếng SJC chuẩn, không phân biệt nơi mua.</p>

    <h3>Khi nào chọn SJC, khi nào chọn BTMC?</h3>
    <ul>
        <li><strong>Đầu tư vàng miếng 1 lượng:</strong> Chọn nơi nào có giá bán thấp hơn tại thời điểm giao dịch. SJC thường có spread hẹp hơn.</li>
        <li><strong>Mua vàng nhẫn 9999:</strong> BTMC có nhẫn Rồng Thăng Long giá tốt. Nếu ở Hà Nội, BTMC là lựa chọn thuận tiện.</li>
        <li><strong>Tích trữ dài hạn:</strong> Vàng miếng SJC dễ bán lại hơn ở mọi nơi trên toàn quốc. Nhẫn BTMC thanh khoản tốt ở miền Bắc.</li>
    </ul>

    <h3>So sánh spread mua-bán</h3>
    <p>Hiện tại: SJC spread <strong>{{ number_format($sjcSpread, 0, ',', '.') }} VNĐ</strong> vs BTMC <strong>{{ number_format($otherSpread, 0, ',', '.') }} VNĐ</strong>. Spread ảnh hưởng trực tiếp đến chi phí khi bạn muốn bán lại vàng.</p>
</article>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>Giá vàng SJC và BTMC hôm nay chênh nhau bao nhiêu?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">Chênh lệch giá bán vàng tại SJC và BTMC thường từ 0 đến 500.000 VND/lượng. Kiểm tra bảng so sánh phía trên để xem mức chênh lệch mới nhất.</p>
        </details>
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>Nên mua vàng ở SJC hay Bảo Tín Minh Châu?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">Nếu ở Hà Nội và mua vàng nhẫn 9999, BTMC có giá cạnh tranh và sản phẩm Rồng Thăng Long đặc trưng. Nếu mua vàng miếng đầu tư lớn, so sánh giá bán và spread để quyết định.</p>
        </details>
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>Nhẫn Rồng Thăng Long BTMC có tốt không?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">Nhẫn Rồng Thăng Long vàng 9999 là sản phẩm uy tín của BTMC, hàm lượng vàng nguyên chất 99.99%. Thanh khoản tốt tại miền Bắc, BTMC cam kết mua lại theo giá niêm yết.</p>
        </details>
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>BTMC có bán vàng miếng SJC không?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">Có, BTMC kinh doanh cả vàng miếng SJC và sản phẩm vàng nhẫn 9999 thương hiệu riêng. Giá vàng miếng SJC tại BTMC thường tương đương các đại lý lớn khác.</p>
        </details>
    </div>
</div>

@php
$compareLinks = [
    ['slug' => 'sjc-vs-doji', 'label' => 'SJC vs DOJI'],
    ['slug' => 'sjc-vs-btmc', 'label' => 'SJC vs BTMC'],
    ['slug' => 'sjc-vs-pnj', 'label' => 'SJC vs PNJ'],
    ['slug' => 'sjc-vs-phuquy', 'label' => 'SJC vs Phú Quý'],
    ['slug' => 'sjc-vs-mihong', 'label' => 'SJC vs Mi Hồng'],
    ['slug' => 'sjc-vs-btmh', 'label' => 'SJC vs Bảo Tín MH'],
    ['slug' => 'sjc-vs-ngoctham', 'label' => 'SJC vs Ngọc Thẩm'],
    ['slug' => 'sjc-vs-the-gioi', 'label' => 'SJC vs Thế giới'],
    ['slug' => 'vang-vs-usd', 'label' => 'Vàng vs USD'],
];
@endphp
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3" style="font-family: 'Philosopher', serif;">So sánh giá vàng khác</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
        @foreach($compareLinks as $link)
            @if(!str_contains($link['slug'], $otherSlug))
                <a href="/so-sanh-gia-vang/{{ $link['slug'] }}" class="text-blue-700 hover:underline">{{ $link['label'] }}</a>
            @endif
        @endforeach
    </div>
    <div class="mt-3 pt-3 border-t text-sm">
        <a href="/so-sanh-gia-vang" class="text-blue-700 hover:underline font-medium">← Tất cả so sánh giá vàng</a>
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
