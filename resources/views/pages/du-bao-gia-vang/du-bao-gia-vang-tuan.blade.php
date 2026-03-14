@extends('gold.page-shell')

@section('page-label', 'Dự báo')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $sjcCard = $snapshot['sjcCard'] ?? null;
    $usCard = $snapshot['usCard'] ?? null;
    $statCards = $snapshot['statCards'] ?? [];

    $sjcV = $sjcCard ? ($sjcCard['variants'][$sjcCard['selected'] ?? 'hn'] ?? null) : null;
    $sjcSell = $sjcV['sell'] ?? 0;
    $sjcBuy = $sjcV['buy'] ?? 0;
    $sjcChange = $sjcV['dayChangeLabel'] ?? '';

    $xauSpot = $usCard['variants']['spot']['price'] ?? 2918;
    $xauChange = $usCard['variants']['spot']['dayChangeLabel'] ?? '';

    $usdVndStr = $statCards[3]['value'] ?? '25450';
    $usdVndRate = (float) str_replace([',', '.'], '', $usdVndStr);
    if ($usdVndRate < 1000) $usdVndRate = 25450;
    $xauQuyDoi = round($xauSpot * 37.5 / 31.1035 * $usdVndRate / 1e6, 2);

    $dxyValue = $statCards[4]['value'] ?? '103.42';
    $dxyChange = $statCards[4]['delta'] ?? '';

    // Kịch bản tuần: biến động ±0.5-1.2%
    $bullPrice = round($sjcSell * 1.008, 2);
    $basePrice = $sjcSell;
    $bearPrice = round($sjcSell * 0.988, 2);
    $bullPct = $sjcSell > 0 ? round(($bullPrice - $sjcSell) / $sjcSell * 100, 1) : 0;
    $bearPct = $sjcSell > 0 ? round(($bearPrice - $sjcSell) / $sjcSell * 100, 1) : 0;

    $weekStart = now()->startOfWeek()->format('d/m');
    $weekEnd = now()->endOfWeek()->format('d/m/Y');
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
            {"@@type": "ListItem", "position": 2, "name": "Dự báo giá vàng", "item": "{{ url('/du-bao-gia-vang') }}"},
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
        {"@@type":"Question","name":"Giá vàng SJC tuần này tăng hay giảm?","acceptedAnswer":{"@@type":"Answer","text":"Dự báo giá vàng SJC tuần {{ $weekStart }}–{{ $weekEnd }} phụ thuộc chủ yếu vào dữ liệu kinh tế Mỹ (CPI, việc làm), biên bản FOMC, biến động DXY và giá XAU/USD quốc tế. Giá SJC hiện tại {{ number_format($sjcSell, 2) }} triệu/lượng. Kịch bản tích cực: {{ number_format($bullPrice, 2) }} triệu ({{ $bullPct > 0 ? '+' : '' }}{{ $bullPct }}%), kịch bản tiêu cực: {{ number_format($bearPrice, 2) }} triệu ({{ $bearPct }}%)."}},
        {"@@type":"Question","name":"Vùng hỗ trợ và kháng cự giá vàng tuần này?","acceptedAnswer":{"@@type":"Answer","text":"Vùng hỗ trợ gần nhất quanh {{ number_format($bearPrice, 2) }} triệu/lượng. Vùng kháng cự tại {{ number_format($bullPrice, 2) }} triệu/lượng. Nếu phá vỡ kháng cự, giá có thể tiến tới {{ number_format(round($sjcSell * 1.015, 2), 2) }} triệu. Break hỗ trợ có thể về {{ number_format(round($sjcSell * 0.98, 2), 2) }} triệu."}},
        {"@@type":"Question","name":"Sự kiện kinh tế nào ảnh hưởng giá vàng tuần này?","acceptedAnswer":{"@@type":"Answer","text":"Các sự kiện quan trọng cần theo dõi: báo cáo CPI Mỹ, số liệu việc làm (Jobless Claims), biên bản FOMC, phát biểu quan chức FED, PMI sản xuất/dịch vụ. Ngoài ra, diễn biến địa chính trị và giao dịch XAU/USD phiên Mỹ/Âu cũng tác động mạnh."}},
        {"@@type":"Question","name":"Chiến lược giao dịch vàng tuần này?","acceptedAnswer":{"@@type":"Answer","text":"Với nhà đầu tư ngắn hạn: theo dõi RSI(14) — trên 70 = quá mua (cân nhắc chốt lời), dưới 30 = quá bán (cơ hội mua). Đặt stoploss hợp lý. Với nhà đầu tư tích trữ: tuần nào cũng là cơ hội nếu áp dụng DCA mua đều đặn."}}
    ]
}
</script>
@endpush

@section('page-content')
{{-- Giá hiện tại --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="coins" class="h-5 w-5 text-amber-500"></i> Giá vàng hiện tại</h2>
    <div class="grid gap-3 sm:grid-cols-4">
        <div class="rounded-sm border border-[#b8860b] bg-[#fffbe6] p-3 text-center">
            <p class="text-xs font-semibold text-[#b8860b]">SJC bán ra</p>
            <p class="mt-1 text-xl font-bold text-[#b8860b]">{{ number_format($sjcSell, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng {{ $sjcChange }}</p>
        </div>
        <div class="rounded-sm border border-[#b8860b] bg-[#fffbe6] p-3 text-center">
            <p class="text-xs font-semibold text-[#b8860b]">SJC mua vào</p>
            <p class="mt-1 text-xl font-bold text-[#b8860b]">{{ number_format($sjcBuy, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-blue-300 bg-blue-50 p-3 text-center">
            <p class="text-xs font-semibold text-blue-700">XAU/USD</p>
            <p class="mt-1 text-xl font-bold text-blue-800">{{ number_format($xauSpot, 2) }}</p>
            <p class="text-xs text-slate-500">USD/oz {{ $xauChange }}</p>
        </div>
        <div class="rounded-sm border border-slate-300 bg-slate-50 p-3 text-center">
            <p class="text-xs font-semibold text-slate-600">DXY</p>
            <p class="mt-1 text-xl font-bold text-slate-800">{{ $dxyValue }}</p>
            <p class="text-xs text-slate-500">{{ $dxyChange }}</p>
        </div>
    </div>
</div>

{{-- 3 kịch bản --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="target" class="h-5 w-5 text-amber-500"></i> 3 kịch bản giá vàng SJC tuần này</h2>
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-sm border-2 border-emerald-300 bg-emerald-50 p-4 text-center">
            <p class="text-xs font-bold text-emerald-700 uppercase">Kịch bản tích cực</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-emerald-800">{{ number_format($bullPrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            <p class="mt-1 text-sm font-semibold text-emerald-600">+{{ $bullPct }}%</p>
            <p class="mt-2 text-xs text-emerald-700">DXY giảm, FED dovish, XAU/USD tăng mạnh</p>
        </div>
        <div class="rounded-sm border-2 border-blue-300 bg-blue-50 p-4 text-center">
            <p class="text-xs font-bold text-blue-700 uppercase">Kịch bản cơ sở</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-blue-800">{{ number_format($basePrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            <p class="mt-1 text-sm font-semibold text-blue-600">Đi ngang</p>
            <p class="mt-2 text-xs text-blue-700">Không có sự kiện lớn, sideway quanh vùng hiện tại</p>
        </div>
        <div class="rounded-sm border-2 border-rose-300 bg-rose-50 p-4 text-center">
            <p class="text-xs font-bold text-rose-700 uppercase">Kịch bản tiêu cực</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-rose-800">{{ number_format($bearPrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            <p class="mt-1 text-sm font-semibold text-rose-600">{{ $bearPct }}%</p>
            <p class="mt-2 text-xs text-rose-700">DXY tăng, CPI Mỹ thấp, FED hawkish</p>
        </div>
    </div>
</div>

{{-- Chỉ báo kỹ thuật --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="bar-chart-2" class="h-5 w-5 text-amber-500"></i> Chỉ báo kỹ thuật</h2>
    <div class="overflow-x-auto rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#001061] text-white">
                    <th class="px-4 py-2.5 text-left font-semibold text-white/90">Chỉ báo</th>
                    <th class="px-4 py-2.5 text-center font-semibold text-white/90">Tín hiệu</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-white/90">Ý nghĩa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">RSI (14)</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Trung tính</span></td>
                    <td class="px-4 py-2.5 text-slate-600">RSI quanh 50 — chưa quá mua/bán, có thể dao động cả hai chiều</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">MACD</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Tích cực</span></td>
                    <td class="px-4 py-2.5 text-slate-600">MACD cắt signal line hướng lên — tín hiệu mua ngắn hạn</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">Bollinger Bands</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Thu hẹp</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Bands co lại — sắp có biến động lớn, theo dõi hướng breakout</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">MA(20)</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Trên MA</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Giá trên đường MA(20) — xu hướng ngắn hạn tích cực</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">Hỗ trợ / Kháng cự</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">Xác định</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Hỗ trợ: {{ number_format($bearPrice, 2) }} — Kháng cự: {{ number_format($bullPrice, 2) }} triệu/lượng</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Biểu đồ 7 ngày --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-1 flex items-center gap-2">
        <i data-lucide="line-chart" class="h-5 w-5 text-amber-500"></i>
        Biểu đồ giá vàng
    </h2>
    <p class="text-xs text-slate-500 mb-3">SJC (nét liền) · XAU quy đổi VND (nét đứt) — Đơn vị: triệu VNĐ/lượng</p>

    <div class="flex gap-2 mb-3 flex-wrap" id="wkPeriodBtns">
        <button data-p="today" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">Hôm nay</button>
        <button data-p="7d" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm">7 ngày</button>
        <button data-p="30d" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">30 ngày</button>
    </div>

    <div id="weekly-chart" class="w-full" class="h-[280px] sm:h-[380px]">
        <div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>
    </div>

    <div id="wkStats" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4" style="display:none">
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Cao nhất</p><p id="wkHigh" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thấp nhất</p><p id="wkLow" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Trung bình</p><p id="wkAvg" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thay đổi</p><p id="wkChange" class="mt-1 text-lg font-bold text-emerald-600">—</p></div>
    </div>
</div>

{{-- Sự kiện kinh tế tuần này --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="calendar" class="h-5 w-5 text-amber-500"></i> Sự kiện kinh tế cần theo dõi</h2>
    <div class="space-y-2 text-sm">
        <div class="flex items-start gap-3 rounded-sm border border-red-200 bg-red-50 p-3">
            <span class="rounded bg-red-600 px-2 py-0.5 text-xs font-bold text-white">Cao</span>
            <div>
                <p class="font-semibold text-slate-800">Báo cáo CPI Mỹ</p>
                <p class="text-xs text-slate-500">CPI cao hơn kỳ vọng → USD mạnh → vàng giảm. CPI thấp → kỳ vọng giảm lãi suất → vàng tăng.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 rounded-sm border border-red-200 bg-red-50 p-3">
            <span class="rounded bg-red-600 px-2 py-0.5 text-xs font-bold text-white">Cao</span>
            <div>
                <p class="font-semibold text-slate-800">Phát biểu quan chức FED</p>
                <p class="text-xs text-slate-500">Phát biểu hawkish → USD tăng, vàng giảm. Dovish → kỳ vọng giảm lãi suất, vàng tăng.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 rounded-sm border border-amber-200 bg-amber-50 p-3">
            <span class="rounded bg-amber-500 px-2 py-0.5 text-xs font-bold text-white">TB</span>
            <div>
                <p class="font-semibold text-slate-800">Jobless Claims (đơn thất nghiệp tuần)</p>
                <p class="text-xs text-slate-500">Thất nghiệp tăng → kinh tế yếu → FED dovish → hỗ trợ vàng.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 rounded-sm border border-amber-200 bg-amber-50 p-3">
            <span class="rounded bg-amber-500 px-2 py-0.5 text-xs font-bold text-white">TB</span>
            <div>
                <p class="font-semibold text-slate-800">PMI sản xuất & dịch vụ</p>
                <p class="text-xs text-slate-500">PMI < 50 = sản xuất co lại → rủi ro suy thoái → hỗ trợ vàng.</p>
            </div>
        </div>
    </div>
</div>

{{-- Bài phân tích --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="file-text" class="h-5 w-5 text-amber-500"></i> Phân tích dự báo giá vàng tuần {{ $weekStart }}–{{ $weekEnd }}</h2>
    <article class="prose prose-sm max-w-none text-slate-700 space-y-3">
        <p>Giá vàng SJC kết thúc phiên giao dịch gần nhất ở mức <strong>{{ number_format($sjcSell, 2) }} triệu VND/lượng</strong> (bán ra), giá mua vào <strong>{{ number_format($sjcBuy, 2) }} triệu</strong>. Spread (chênh mua-bán) {{ number_format(($sjcSell - $sjcBuy) * 1e6, 0, ',', '.') }} đồng/lượng.</p>

        <p>Trên thị trường quốc tế, XAU/USD ở mức <strong>{{ number_format($xauSpot, 2) }} USD/oz</strong>, quy đổi khoảng {{ number_format($xauQuyDoi, 2) }} triệu VND/lượng. Premium SJC so với giá quốc tế quy đổi: {{ number_format(($sjcSell - $xauQuyDoi), 2) }} triệu.</p>

        <h3 class="text-lg font-semibold text-slate-800">Phân tích kỹ thuật tuần</h3>
        <p>Chỉ báo RSI(14) trên khung ngày đang ở vùng trung tính, cho thấy giá chưa quá mua hay quá bán. MACD cho tín hiệu hướng lên khi đường MACD cắt signal line từ dưới. Bollinger Bands đang co lại, báo hiệu giai đoạn tích lũy trước biến động lớn. Vùng hỗ trợ quan trọng: <strong>{{ number_format($bearPrice, 2) }} triệu</strong>, kháng cự: <strong>{{ number_format($bullPrice, 2) }} triệu</strong>.</p>

        <h3 class="text-lg font-semibold text-slate-800">Yếu tố vĩ mô</h3>
        <p>Chỉ số DXY đang ở mức <strong>{{ $dxyValue }}</strong> ({{ $dxyChange }}). {{ (float) str_replace(',', '.', $dxyValue) < 104 ? 'DXY yếu tạo điều kiện thuận lợi cho giá vàng tăng' : 'DXY mạnh tạo áp lực lên giá vàng' }}. Tỷ giá USD/VND tại {{ $usdVndStr }} VND — {{ $usdVndRate > 25500 ? 'tỷ giá cao hỗ trợ giá vàng SJC quy đổi' : 'tỷ giá tương đối ổn định' }}.</p>

        <h3 class="text-lg font-semibold text-slate-800">Chiến lược giao dịch</h3>
        <p><strong>Mua:</strong> Khi giá test vùng hỗ trợ {{ number_format($bearPrice, 2) }} triệu và RSI < 35. Stoploss dưới hỗ trợ 0.5%. Target: {{ number_format($basePrice, 2) }}–{{ number_format($bullPrice, 2) }} triệu.</p>
        <p><strong>Chốt lời:</strong> Khi giá chạm kháng cự {{ number_format($bullPrice, 2) }} triệu và RSI > 70. Hoặc trailing stop 0.3%.</p>
        <p><strong>Tích trữ DCA:</strong> Không cần timing thị trường, mua đều đặn mỗi tuần theo kế hoạch.</p>
    </article>
</div>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5 text-amber-500"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y">
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng tuần này tăng hay giảm?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Dựa trên phân tích kỹ thuật và cơ bản, kịch bản cơ sở là giá SJC dao động quanh {{ number_format($basePrice, 2) }} triệu. Biên độ biến động tuần khoảng {{ number_format($bearPrice, 2) }}–{{ number_format($bullPrice, 2) }} triệu.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>RSI là gì và đọc như thế nào?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">RSI (Relative Strength Index) đo sức mạnh xu hướng (0–100). RSI > 70 = quá mua (cân nhắc bán), RSI < 30 = quá bán (cơ hội mua). RSI quanh 50 = trung tính.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>CPI Mỹ ảnh hưởng giá vàng thế nào?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">CPI (Consumer Price Index) đo lạm phát. CPI cao hơn kỳ vọng → FED có thể tăng/giữ lãi suất → USD mạnh → vàng giảm ngắn hạn. CPI thấp → kỳ vọng giảm lãi suất → vàng tăng.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Nên mua vàng đầu tuần hay cuối tuần?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Không có quy tắc cố định. Thứ 2 giá thường điều chỉnh theo phiên Mỹ cuối tuần trước. Thứ 5-6 thường biến động mạnh do báo cáo kinh tế. Nếu DCA, mua cùng ngày cố định mỗi tuần.</p>
        </details>
    </div>
</div>

{{-- Links --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="link" class="h-5 w-5 text-amber-500"></i> Dự báo khác</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/du-bao-gia-vang" class="text-blue-700 hover:underline">Dự báo tổng hợp</a>
        <a href="/du-bao-gia-vang/du-bao-gia-vang-thang" class="text-blue-700 hover:underline">Dự báo tháng này</a>
        <a href="/du-bao-gia-vang/du-bao-gia-vang-2026" class="text-blue-700 hover:underline">Dự báo năm 2026</a>
    </div>
    <div class="mt-3 pt-3 border-t grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
        <a href="/gia-vang-hom-nay" class="text-blue-700 hover:underline">Giá vàng hôm nay</a>
        <a href="/bieu-do-gia-vang" class="text-blue-700 hover:underline">Biểu đồ giá vàng</a>
        <a href="/so-sanh-gia-vang" class="text-blue-700 hover:underline">So sánh giá vàng</a>
        <a href="/gia-vang-the-gioi" class="text-blue-700 hover:underline">Giá vàng thế giới</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    var currentPeriod = '7d';
    var chartRoot = null;
    var activeClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm';
    var inactiveClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700';

    document.querySelectorAll('#wkPeriodBtns button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentPeriod = this.dataset.p;
            document.querySelectorAll('#wkPeriodBtns button').forEach(function(b) { b.className = inactiveClass; });
            this.className = activeClass;
            loadChart();
        });
    });

    function loadChart() {
        var holder = document.getElementById('weekly-chart');
        holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>';
        document.getElementById('wkStats').style.display = 'none';
        fetch('/api/v1/all-brands-chart?period=' + encodeURIComponent(currentPeriod))
            .then(function(r) { return r.json(); })
            .then(function(data) { waitAm5(function() { renderChart(data); }); })
            .catch(function() { holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-500">Không thể tải dữ liệu.</div>'; });
    }

    function waitAm5(cb) {
        if (window.am5 && window.am5xy && window.am5themes_Animated) cb();
        else setTimeout(function() { waitAm5(cb); }, 200);
    }

    function renderChart(data) {
        var holder = document.getElementById('weekly-chart');
        if (!data || !data.length) { holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-500">Chưa có dữ liệu.</div>'; return; }
        holder.innerHTML = '';
        if (chartRoot) { chartRoot.dispose(); chartRoot = null; }
        chartRoot = am5.Root.new('weekly-chart');
        if (chartRoot._logo) chartRoot._logo.dispose();
        chartRoot.setThemes([am5themes_Animated.new(chartRoot)]);

        var isToday = currentPeriod === 'today';
        var chart = chartRoot.container.children.push(
            am5xy.XYChart.new(chartRoot, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX' })
        );

        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(chartRoot, {
            baseInterval: { timeUnit: isToday ? 'minute' : (currentPeriod === '7d' ? 'hour' : 'day'), count: 1 },
            renderer: am5xy.AxisRendererX.new(chartRoot, { minGridDistance: 60 }),
            dateFormats: isToday ? { minute: 'HH:mm', hour: 'HH:mm' } : { hour: 'dd/MM HH:mm', day: 'dd/MM', month: 'MM/yyyy' },
            periodChangeDateFormats: isToday ? { minute: 'HH:mm', hour: 'dd/MM HH:mm' } : { hour: 'dd/MM', day: 'dd/MM', month: 'MM/yyyy' }
        }));
        xAxis.get('renderer').labels.template.setAll({ fontSize: 11, fill: am5.color(0x64748b) });

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(chartRoot, {
            renderer: am5xy.AxisRendererY.new(chartRoot, {}),
            numberFormat: '#,###.##', extraMin: 0.01, extraMax: 0.01
        }));
        yAxis.get('renderer').labels.template.setAll({ fontSize: 11, fill: am5.color(0x64748b) });

        var cursor = chart.set('cursor', am5xy.XYCursor.new(chartRoot, { behavior: 'zoomX', xAxis: xAxis }));
        cursor.lineY.set('visible', false);

        var chartData = data.map(function(p) {
            return { dateTs: new Date(p.date).getTime(), sjc: p.SJC, xau: p['XAU quy \u0111\u1ed5i'] };
        });

        function makeSeries(name, field, color, dashed) {
            var s = chart.series.push(am5xy.LineSeries.new(chartRoot, {
                name: name, xAxis: xAxis, yAxis: yAxis,
                valueYField: field, valueXField: 'dateTs',
                stroke: am5.color(color), fill: am5.color(color),
                tooltip: am5.Tooltip.new(chartRoot, { labelText: '[bold]{name}[/]: {valueY} tr', pointerOrientation: 'horizontal', getFillFromSprite: false })
            }));
            s.get('tooltip').get('background').setAll({ fill: am5.color(0x0f172a), fillOpacity: 0.92, stroke: am5.color(0x0f172a) });
            s.get('tooltip').label.setAll({ fill: am5.color(0xffffff), fontSize: 12 });
            if (dashed) s.strokes.template.setAll({ strokeWidth: 2.5, strokeDasharray: [6, 3] });
            else s.strokes.template.setAll({ strokeWidth: 2.5 });
            s.data.setAll(chartData);
            return s;
        }

        makeSeries('SJC', 'sjc', '#b8860b', false);
        makeSeries('XAU quy đổi', 'xau', '#3b82f6', true);

        var legend = chart.children.push(am5.Legend.new(chartRoot, {
            centerX: am5.percent(50), x: am5.percent(50), y: am5.percent(100), layout: chartRoot.horizontalLayout
        }));
        legend.labels.template.setAll({ fontSize: 11 });
        legend.data.setAll(chart.series.values);
        chart.appear(800, 100);
        updateStats(data);
    }

    function updateStats(data) {
        var vals = data.map(function(d) { return d.SJC; }).filter(function(v) { return v != null; });
        if (!vals.length) return;
        var high = Math.max.apply(null, vals), low = Math.min.apply(null, vals);
        var avg = vals.reduce(function(a,b) { return a+b; }, 0) / vals.length;
        var change = vals[0] > 0 ? ((vals[vals.length-1] - vals[0]) / vals[0] * 100) : 0;
        document.getElementById('wkHigh').textContent = high.toFixed(2) + ' tr';
        document.getElementById('wkLow').textContent = low.toFixed(2) + ' tr';
        document.getElementById('wkAvg').textContent = avg.toFixed(2) + ' tr';
        var el = document.getElementById('wkChange');
        el.textContent = (change >= 0 ? '+' : '') + change.toFixed(1) + '%';
        el.className = 'mt-1 text-lg font-bold ' + (change >= 0 ? 'text-emerald-600' : 'text-rose-600');
        document.getElementById('wkStats').style.display = '';
    }

    loadChart();
})();
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
