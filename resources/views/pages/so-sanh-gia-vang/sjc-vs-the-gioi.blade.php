@extends('gold.page-shell')

@section('page-label', 'So sánh')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $sjcCard = $snapshot['sjcCard'] ?? null;
    $usCard = $snapshot['usCard'] ?? null;
    $statCards = $snapshot['statCards'] ?? [];

    $sjcV = $sjcCard ? ($sjcCard['variants'][$sjcCard['selected'] ?? 'hn'] ?? null) : null;
    $sjcSell = $sjcV['sell'] ?? 0;
    $sjcBuy = $sjcV['buy'] ?? 0;
    $sjcSpread = ($sjcSell - $sjcBuy) * 1e6;
    $sjcChange = $sjcV['dayChangeLabel'] ?? '';

    $xauSpot = $usCard['variants']['spot']['price'] ?? 2918;
    $xauChange = $usCard['variants']['spot']['dayChangeLabel'] ?? '';
    $usdVndStr = $statCards[3]['value'] ?? '25450';
    $usdVndRate = (float) str_replace([',', '.'], '', $usdVndStr);
    if ($usdVndRate < 1000) $usdVndRate = 25450;

    $xauQuyDoi = round($xauSpot * 37.5 / 31.1035 * $usdVndRate / 1e6, 2);
    $xauQuyDoiVnd = $xauQuyDoi * 1e6;
    $premium = ($sjcSell - $xauQuyDoi) * 1e6;
    $premiumPercent = $xauQuyDoi > 0 ? round($premium / ($xauQuyDoi * 1e6) * 100, 2) : 0;
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
        {"@@type":"Question","name":"Chênh lệch giá vàng SJC và thế giới hôm nay là bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Premium (chênh lệch) giữa giá vàng SJC và giá thế giới quy đổi thường dao động từ 10-20 triệu VND/lượng, phụ thuộc vào cung cầu nội địa, chính sách NHNN và tỷ giá USD/VND. Kiểm tra bảng so sánh trên trang để xem mức chênh mới nhất."}},
        {"@@type":"Question","name":"Công thức quy đổi giá vàng thế giới sang VND/lượng?","acceptedAnswer":{"@@type":"Answer","text":"Giá XAU (USD/oz) × 37.5 (gram/lượng) ÷ 31.1035 (gram/oz) × Tỷ giá USD/VND = Giá VND/lượng. Ví dụ: 2,918 × 37.5 ÷ 31.1035 × 25,450 ≈ 89.5 triệu VND/lượng."}},
        {"@@type":"Question","name":"Tại sao vàng SJC đắt hơn giá thế giới?","acceptedAnswer":{"@@type":"Answer","text":"Do: (1) NHNN kiểm soát số lượng vàng miếng SJC lưu hành → cung hạn chế, (2) Thuế nhập khẩu và phí gia công, (3) Nhu cầu nội địa cao đặc biệt dịp lễ tết, (4) Chính sách tỷ giá và hạn chế nhập vàng từ 2013."}},
        {"@@type":"Question","name":"Khi nào premium SJC giảm?","acceptedAnswer":{"@@type":"Answer","text":"Premium thường giảm khi: NHNN bán vàng can thiệp bình ổn thị trường, nhu cầu nội địa giảm (sau tết, mùa hè), hoặc khi giá vàng thế giới tăng mạnh đột ngột (người dân bán ra thu lời)."}}
    ]
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-gradient-to-br from-amber-50/80 to-yellow-50/80 p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700"><i class="block h-2 w-2 rounded-full bg-emerald-500"></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">{{ $now }}</span>
    </div>
    <h2 class="text-lg sm:text-2xl font-bold text-[#001061] mb-4">So sánh giá vàng SJC và Thế giới hôm nay</h2>

    <div class="grid gap-5 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border-2 border-amber-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-amber-700 mb-1">SJC (bán ra)</p>
            <p class="text-xl sm:text-3xl font-bold text-amber-900 tabular-nums">{{ $sjcSell > 0 ? number_format($sjcSell * 1e6, 0, ',', '.') : '—' }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">Mua: {{ $sjcBuy > 0 ? number_format($sjcBuy * 1e6, 0, ',', '.') : '—' }}</span>
                @if ($sjcChange)<span class="font-bold {{ str_starts_with($sjcChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $sjcChange }}</span>@endif
            </div>
        </div>
        <div class="rounded-sm border-2 border-yellow-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-yellow-700 mb-1">XAU quy đổi VND</p>
            <p class="text-xl sm:text-3xl font-bold text-yellow-900 tabular-nums">{{ $xauQuyDoi > 0 ? number_format($xauQuyDoiVnd, 0, ',', '.') : '—' }}</p>
            <p class="text-xs text-slate-500 mb-1">VNĐ/Lượng</p>
            <div class="flex items-center justify-center gap-3 text-xs">
                <span class="text-slate-500">XAU: {{ number_format($xauSpot, 2) }} USD/oz</span>
                @if ($xauChange)<span class="font-bold {{ str_starts_with($xauChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $xauChange }}</span>@endif
            </div>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <div class="rounded-sm border {{ $premium >= 0 ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} p-3 text-center">
            <p class="text-xs font-medium {{ $premium >= 0 ? 'text-rose-700' : 'text-emerald-700' }}">Premium (chênh lệch)</p>
            <p class="text-xl font-bold {{ $premium >= 0 ? 'text-rose-800' : 'text-emerald-800' }} tabular-nums">{{ $premium >= 0 ? '+' : '' }}{{ number_format($premium, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-600">VNĐ/Lượng</p>
        </div>
        <div class="rounded-sm border border-indigo-200 bg-indigo-50 p-3 text-center">
            <p class="text-xs font-medium text-indigo-700">Premium (%)</p>
            <p class="text-xl font-bold text-indigo-800 tabular-nums">{{ $premiumPercent >= 0 ? '+' : '' }}{{ number_format($premiumPercent, 2) }}%</p>
            <p class="text-xs text-slate-600">So với giá thế giới</p>
        </div>
        <div class="rounded-sm border border-slate-200 bg-slate-50 p-3 text-center">
            <p class="text-xs font-medium text-slate-700">Tỷ giá USD/VND</p>
            <p class="text-xl font-bold text-slate-800 tabular-nums">{{ number_format($usdVndRate, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-600">{{ $statCards[3]['delta'] ?? '' }}</p>
        </div>
    </div>
</div>

{{-- Công thức quy đổi --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3">Công thức quy đổi giá vàng thế giới</h2>
    <div class="rounded-sm bg-slate-50 border border-slate-200 p-4 text-sm font-mono text-slate-700 overflow-x-auto">
        <p class="mb-2"><strong>Giá VND/lượng</strong> = XAU × 37.5 ÷ 31.1035 × USD/VND</p>
        <p class="text-slate-500">= {{ number_format($xauSpot, 2) }} × 37.5 ÷ 31.1035 × {{ number_format($usdVndRate, 0, ',', '.') }}</p>
        <p class="text-amber-700 font-bold">= {{ number_format($xauQuyDoiVnd, 0, ',', '.') }} VNĐ/Lượng</p>
    </div>
    <div class="mt-3 grid gap-2 sm:grid-cols-3 text-sm">
        <div class="flex items-start gap-2 text-slate-600"><i data-lucide="info" class="h-4 w-4 text-blue-400 mt-0.5 shrink-0"></i> <span><strong>37.5 gram</strong> = 1 lượng vàng Việt Nam</span></div>
        <div class="flex items-start gap-2 text-slate-600"><i data-lucide="info" class="h-4 w-4 text-blue-400 mt-0.5 shrink-0"></i> <span><strong>31.1035 gram</strong> = 1 troy ounce quốc tế</span></div>
        <div class="flex items-start gap-2 text-slate-600"><i data-lucide="info" class="h-4 w-4 text-blue-400 mt-0.5 shrink-0"></i> <span><strong>Hệ số quy đổi</strong> = 37.5 ÷ 31.1035 ≈ 1.2057</span></div>
    </div>
</div>

{{-- Biểu đồ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-slate-900 mb-2">Biểu đồ SJC vs XAU quy đổi (7 ngày)</h2>
    <p class="text-xs text-slate-500 mb-3">So sánh diễn biến giá bán SJC và giá vàng thế giới quy đổi VND trong 7 ngày</p>
    <div id="sjcWorldChart" class="w-full" class="h-[260px] sm:h-[360px]">
        <div class="flex items-center justify-center h-full text-slate-400">
            <svg class="animate-spin h-6 w-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Đang tải biểu đồ...
        </div>
    </div>
</div>

{{-- Phân tích --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-yellow-400 pl-3">Phân tích chênh lệch SJC và giá thế giới (Premium)</h2>

    <h3>Premium SJC là gì?</h3>
    <p><strong>Premium SJC</strong> là phần chênh lệch giữa giá vàng miếng SJC trong nước và giá vàng thế giới (XAU/USD) quy đổi sang VND/lượng. Premium này phản ánh yếu tố cung cầu nội địa, chi phí nhập khẩu, thuế, phí gia công và chính sách quản lý vàng miếng của NHNN.</p>

    <h3>Lịch sử premium SJC</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Giai đoạn</th><th class="border border-slate-200 p-2 text-left font-semibold">Premium</th><th class="border border-slate-200 p-2 text-left font-semibold">Nguyên nhân</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">2020-2021</td><td class="border border-slate-200 p-2">5-10 triệu</td><td class="border border-slate-200 p-2 text-slate-600">NHNN chưa can thiệp mạnh, cung SJC tương đối ổn</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2022-2023</td><td class="border border-slate-200 p-2">10-20 triệu</td><td class="border border-slate-200 p-2 text-slate-600">Cung SJC khan hiếm, nhu cầu tích trữ tăng mạnh</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2024</td><td class="border border-slate-200 p-2">15-25 triệu</td><td class="border border-slate-200 p-2 text-slate-600">Premium đỉnh lịch sử, NHNN bán vàng can thiệp</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2025-2026</td><td class="border border-slate-200 p-2">10-20 triệu</td><td class="border border-slate-200 p-2 text-slate-600">NHNN tiếp tục bình ổn, premium dần thu hẹp</td></tr>
            </tbody>
        </table>
    </div>

    <h3>Khi nào nên mua SJC dựa vào premium?</h3>
    <ul>
        <li><strong>Premium thấp (&lt; 10 triệu):</strong> Cơ hội tốt để mua SJC, giá gần giá trị thực tế.</li>
        <li><strong>Premium trung bình (10-15 triệu):</strong> Mức bình thường, chấp nhận được cho đầu tư dài hạn.</li>
        <li><strong>Premium cao (&gt; 20 triệu):</strong> Cân nhắc mua vàng nhẫn 9999 thay vì SJC, hoặc chờ NHNN can thiệp.</li>
    </ul>

    <h3>Yếu tố ảnh hưởng tỷ giá USD/VND</h3>
    <p>Tỷ giá USD/VND ảnh hưởng trực tiếp đến giá quy đổi: VND yếu đi → giá vàng quy đổi tăng → premium có thể giảm. Các yếu tố chính: chính sách lãi suất của Fed, dòng vốn FDI, cán cân thương mại, và can thiệp của NHNN trên thị trường ngoại hối.</p>
</article>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Chênh lệch SJC và giá thế giới hôm nay là bao nhiêu?', 'a' => 'Premium hiện tại: ' . number_format($premium, 0, ',', '.') . ' VNĐ/lượng (' . number_format($premiumPercent, 2) . '%). Kiểm tra phần đầu trang để xem số liệu mới nhất.'],
            ['q' => 'Công thức quy đổi giá vàng thế giới sang VNĐ?', 'a' => 'XAU (USD/oz) × 37.5 (gram/lượng) ÷ 31.1035 (gram/oz) × Tỷ giá USD/VND = Giá VNĐ/lượng. Hệ số 37.5 ÷ 31.1035 ≈ 1.2057.'],
            ['q' => 'Tại sao SJC đắt hơn giá thế giới?', 'a' => 'Do cung SJC bị NHNN kiểm soát, thuế nhập khẩu vàng, phí gia công, và nhu cầu tích trữ cao tại VN. Premium cao nhất khi cung khan hiếm.'],
            ['q' => 'Khi nào premium SJC giảm?', 'a' => 'Khi NHNN bán vàng can thiệp, nhu cầu giảm (sau tết/mùa hè), hoặc giá thế giới tăng đột ngột khiến người dân bán ra.'],
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
        <a href="/so-sanh-gia-vang/sjc-vs-doji" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs DOJI</a>
        <a href="/so-sanh-gia-vang/sjc-vs-pnj" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs PNJ</a>
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
    if (!window.am5 || !window.am5xy) return;
    fetch('/api/v1/all-brands-chart?period=7d').then(function(r){return r.json();}).then(function(res) {
        var data = res.data || res;
        if (!data || !data.length) return;
        document.getElementById('sjcWorldChart').innerHTML = '';
        var root = am5.Root.new('sjcWorldChart');
        if (root._logo) root._logo.dispose();
        root.setThemes([am5themes_Animated.new(root)]);
        var chart = root.container.children.push(am5xy.XYChart.new(root, { panX:false, panY:false, wheelX:'none', wheelY:'none', layout:root.verticalLayout }));
        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, { baseInterval:{timeUnit:'day',count:1}, renderer:am5xy.AxisRendererX.new(root,{minGridDistance:60}), dateFormats:{day:'dd/MM'}, periodChangeDateFormats:{day:'dd/MM'} }));
        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, { renderer:am5xy.AxisRendererY.new(root,{}), numberFormat:'#,###.##' }));
        function addLine(name, key, color, dashed) {
            var s = chart.series.push(am5xy.LineSeries.new(root, { name:name, xAxis:xAxis, yAxis:yAxis, valueYField:'value', valueXField:'dateTs', stroke:am5.color(color), tooltip:am5.Tooltip.new(root,{labelText:name+': {valueY.formatNumber("#,###.##")} tr'}) }));
            s.strokes.template.setAll({strokeWidth:2.5, strokeDasharray: dashed ? [6,3] : []});
            var mapped = data.filter(function(d){return d[key] && d[key] > 0;}).map(function(d){ return {dateTs:new Date(d.date).getTime(), value:d[key]}; });
            s.data.setAll(mapped);
            return s;
        }
        addLine('SJC', 'SJC', '#b8860b', false);
        addLine('XAU quy đổi', 'XAU quy đổi', '#f59e0b', true);
        var legend = chart.children.push(am5.Legend.new(root, {centerX:am5.percent(50), x:am5.percent(50), y:am5.percent(100)}));
        legend.labels.template.setAll({fontSize:11});
        legend.data.setAll(chart.series.values);
        chart.set('cursor', am5xy.XYCursor.new(root, {}));
        chart.appear(800, 100);
    });
});
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
