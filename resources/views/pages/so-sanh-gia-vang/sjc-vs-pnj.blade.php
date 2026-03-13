@extends('gold.page-shell')

@section('page-label', 'So sánh')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $sjcCard = $snapshot['sjcCard'] ?? null;
    $otherCard = $snapshot['pnjCard'] ?? null;
    $otherName = 'PNJ';
    $otherColor = '#15803d';
    $otherSlug = 'pnj';

    $sjcV = $sjcCard ? ($sjcCard['variants'][$sjcCard['selected'] ?? 'hn'] ?? null) : null;
    $sjcSell = $sjcV['sell'] ?? 0;
    $sjcBuy = $sjcV['buy'] ?? 0;
    $sjcSpread = ($sjcSell - $sjcBuy) * 1e6;
    $sjcChange = $sjcV['dayChangeLabel'] ?? '';

    $otherV = $otherCard ? ($otherCard['variants'][$otherCard['selected'] ?? 'hn'] ?? null) : null;
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
    "publisher": {"@@type": "Organization", "name": "GiaVangHN", "url": "{{ url('/') }}"},
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
        {"@@type":"Question","name":"Giá vàng SJC và PNJ hôm nay chênh nhau bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Chênh lệch giá bán giữa SJC và PNJ thường từ 0 đến 500.000 VND/lượng cho cùng loại sản phẩm. PNJ là đại lý SJC nên giá vàng miếng SJC tại PNJ gần tương đương. Vàng nhẫn PNJ 9999 có giá cạnh tranh riêng."}},
        {"@@type":"Question","name":"Nên mua vàng ở SJC hay PNJ?","acceptedAnswer":{"@@type":"Answer","text":"SJC phù hợp cho đầu tư vàng miếng với thanh khoản cao nhất. PNJ có hệ thống cửa hàng chuyên nghiệp, uy tín niêm yết HOSE, dịch vụ tốt. Vàng nhẫn PNJ 9999 đa dạng mẫu mã, phù hợp trang sức kết hợp tích trữ."}},
        {"@@type":"Question","name":"PNJ có bán vàng miếng SJC không?","acceptedAnswer":{"@@type":"Answer","text":"Có, PNJ là đại lý chính thức bán vàng miếng SJC. Ngoài ra PNJ còn kinh doanh vàng nhẫn PNJ 9999, vàng 24K và trang sức vàng các loại. Giá vàng miếng SJC tại PNJ thường tương đương các đại lý lớn."}},
        {"@@type":"Question","name":"Vàng PNJ 9999 và vàng SJC miếng khác gì?","acceptedAnswer":{"@@type":"Answer","text":"Cả hai đều 99.99% vàng nguyên chất. Vàng miếng SJC thanh khoản cao hơn (thương hiệu quốc gia), giá cao hơn. Vàng nhẫn PNJ 9999 giá rẻ hơn 5-15 triệu/lượng, mẫu mã đẹp hơn, phù hợp tích trữ nhỏ."}}
    ]
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-gradient-to-br from-amber-50/80 to-green-50/80 p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700"><i class="block h-2 w-2 rounded-full bg-emerald-500"></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">{{ $now }}</span>
    </div>
    <h2 class="text-2xl font-bold text-[#001061] mb-4">So sánh giá vàng SJC và PNJ hôm nay</h2>

    <div class="grid gap-4 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border-2 border-amber-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-amber-700 mb-1">SJC (bán ra)</p>
            <p class="text-3xl font-bold text-amber-900 tabular-nums">{{ $sjcSell > 0 ? number_format($sjcSell * 1e6, 0, ',', '.') : '—' }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">Mua: {{ $sjcBuy > 0 ? number_format($sjcBuy * 1e6, 0, ',', '.') : '—' }}</span>
                @if ($sjcChange)<span class="font-bold {{ str_starts_with($sjcChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $sjcChange }}</span>@endif
            </div>
        </div>
        <div class="rounded-sm border-2 border-green-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-green-700 mb-1">{{ $otherName }} (bán ra)</p>
            <p class="text-3xl font-bold text-green-900 tabular-nums">{{ $otherSell > 0 ? number_format($otherSell * 1e6, 0, ',', '.') : '—' }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">Mua: {{ $otherBuy > 0 ? number_format($otherBuy * 1e6, 0, ',', '.') : '—' }}</span>
                @if ($otherChange)<span class="font-bold {{ str_starts_with($otherChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $otherChange }}</span>@endif
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
                    <th class="p-3 text-right font-semibold text-green-700">{{ $otherName }} Mua</th>
                    <th class="p-3 text-right font-semibold text-green-700">{{ $otherName }} Bán</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Chênh (Bán)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($sjcVariants as $key => $sv)
                @php
                    $ov = $otherVariants[$key] ?? null;
                    $label = $sjcLabels[$key] ?? ($otherLabels[$key] ?? $key);
                    $sellDiff = $ov ? ($sv['sell'] - $ov['sell']) * 1e6 : null;
                @endphp
                <tr>
                    <td class="p-3 text-slate-800 font-medium">{{ $label }}</td>
                    <td class="p-3 text-right tabular-nums">{{ ($sv['buy'] ?? 0) > 0 ? number_format($sv['buy'] * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ ($sv['sell'] ?? 0) > 0 ? number_format($sv['sell'] * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums">{{ $ov && ($ov['buy'] ?? 0) > 0 ? number_format($ov['buy'] * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $ov && ($ov['sell'] ?? 0) > 0 ? number_format($ov['sell'] * 1e6, 0, ',', '.') : '—' }}</td>
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
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-green-400 pl-3">Phân tích so sánh giá vàng SJC và PNJ</h2>

    <h3>PNJ — Công ty vàng bạc niêm yết lớn nhất sàn HOSE</h3>
    <p><strong>PNJ</strong> (Công ty CP Vàng bạc Đá quý Phú Nhuận) là doanh nghiệp vàng bạc đá quý lớn nhất Việt Nam niêm yết trên sàn HOSE. PNJ có hệ thống hơn 400 cửa hàng toàn quốc, nổi bật với trang sức cao cấp và vàng nhẫn 9999. Đặc điểm:</p>
    <ul>
        <li><strong>Đại lý SJC:</strong> PNJ là đại lý phân phối vàng miếng SJC tại nhiều tỉnh thành, giá niêm yết cạnh tranh.</li>
        <li><strong>Vàng nhẫn PNJ 9999:</strong> Sản phẩm thương hiệu riêng, hàm lượng 99.99%, mẫu mã đẹp, phù hợp vừa đeo vừa tích trữ.</li>
        <li><strong>Cửa hàng chuyên nghiệp:</strong> Hệ thống bán lẻ chuẩn hóa, bảo hành trang sức, dịch vụ khách hàng tốt.</li>
        <li><strong>Công khai minh bạch:</strong> Là công ty niêm yết, báo cáo tài chính kiểm toán, uy tín cao.</li>
    </ul>

    <h3>Khi nào chọn SJC, khi nào chọn PNJ?</h3>
    <ul>
        <li><strong>Đầu tư vàng miếng:</strong> So sánh giá bán vàng miếng SJC tại SJC và PNJ — chênh lệch thường nhỏ, chọn nơi tiện nhất.</li>
        <li><strong>Mua vàng nhẫn:</strong> PNJ có mẫu mã đa dạng và đẹp, phù hợp cho người thích kết hợp trang sức với tích trữ vàng.</li>
        <li><strong>Mua trang sức:</strong> PNJ có lợi thế rõ ràng với hệ thống trang sức chuyên nghiệp, bảo hành dài hạn.</li>
        <li><strong>Spread và thanh khoản:</strong> So sánh spread mua-bán: nơi nào có spread hẹp hơn sẽ có chi phí giao dịch thấp hơn.</li>
    </ul>

    <h3>So sánh spread mua-bán</h3>
    <p>Spread hiện tại: SJC <strong>{{ number_format($sjcSpread, 0, ',', '.') }} VNĐ</strong> vs PNJ <strong>{{ number_format($otherSpread, 0, ',', '.') }} VNĐ</strong>. Spread thấp hơn = bạn mất ít phí hơn khi bán lại vàng đã mua.</p>
</article>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Giá vàng SJC và PNJ hôm nay chênh nhau bao nhiêu?', 'a' => 'Chênh lệch giá bán thường từ 0 đến 500k VND/lượng. Kiểm tra bảng so sánh phía trên để xem số liệu mới nhất.'],
            ['q' => 'Nên mua vàng ở SJC hay PNJ?', 'a' => 'Nếu mua vàng miếng, so sánh giá 2 nơi. Nếu mua nhẫn 9999 kết hợp trang sức, PNJ có mẫu mã đẹp hơn. PNJ dịch vụ chuyên nghiệp, SJC thanh khoản cao.'],
            ['q' => 'PNJ có bán vàng miếng SJC không?', 'a' => 'Có, PNJ là đại lý chính thức. Giá vàng miếng SJC tại PNJ thường tương đương các đại lý khác.'],
            ['q' => 'Vàng nhẫn PNJ 9999 có thanh khoản tốt không?', 'a' => 'Khá tốt — PNJ mua lại theo giá niêm yết. Tuy nhiên thanh khoản vẫn thấp hơn vàng miếng SJC vì SJC được mọi tiệm vàng chấp nhận.'],
        ]; @endphp
        @foreach ($faqs as $faq)
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition"><span>{{ $faq['q'] }}</span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i></summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">{{ $faq['a'] }}</p>
        </details>
        @endforeach
    </div>
</div>

{{-- Links --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="link" class="h-5 w-5"></i> So sánh khác</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
        <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Thế giới</a>
        <a href="/so-sanh-gia-vang/sjc-vs-doji" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs DOJI</a>
        <a href="/so-sanh-gia-vang/sjc-vs-btmc" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs BTMC</a>
        <a href="/so-sanh-gia-vang/sjc-vs-phuquy" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Phú Quý</a>
        <a href="/so-sanh-gia-vang/sjc-vs-mihong" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Mi Hồng</a>
        <a href="/so-sanh-gia-vang/sjc-vs-btmh" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Bảo Tín MH</a>
        <a href="/so-sanh-gia-vang/sjc-vs-ngoctham" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Ngọc Thẩm</a>
        <a href="/so-sanh-gia-vang/vang-vs-usd" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> Vàng vs USD</a>
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
