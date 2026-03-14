@extends('gold.page-shell')

@section('page-label', 'Dự báo')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $currentMonth = now()->format('m/Y');
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

    // Kịch bản tháng: biến động ±2-3%
    $bullPrice = round($sjcSell * 1.03, 2);
    $basePrice = round($sjcSell * 1.01, 2);
    $bearPrice = round($sjcSell * 0.97, 2);
    $bullPct = $sjcSell > 0 ? round(($bullPrice - $sjcSell) / $sjcSell * 100, 1) : 0;
    $basePct = $sjcSell > 0 ? round(($basePrice - $sjcSell) / $sjcSell * 100, 1) : 0;
    $bearPct = $sjcSell > 0 ? round(($bearPrice - $sjcSell) / $sjcSell * 100, 1) : 0;
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
        {"@@type":"Question","name":"Giá vàng SJC tháng {{ $currentMonth }} tăng hay giảm?","acceptedAnswer":{"@@type":"Answer","text":"Dự báo giá vàng SJC tháng {{ $currentMonth }} phụ thuộc vào chính sách FED, dữ liệu CPI Mỹ, biến động DXY và nhu cầu NHTW. Giá hiện tại {{ number_format($sjcSell, 2) }} triệu/lượng. Kịch bản cơ sở: {{ number_format($basePrice, 2) }} triệu (+{{ $basePct }}%), tích cực: {{ number_format($bullPrice, 2) }} triệu (+{{ $bullPct }}%), tiêu cực: {{ number_format($bearPrice, 2) }} triệu ({{ $bearPct }}%)."}},
        {"@@type":"Question","name":"Nên mua vàng tháng này hay tháng sau?","acceptedAnswer":{"@@type":"Answer","text":"Về dài hạn, biến động 1-2 tháng không quá quan trọng. Nếu đầu tư tích trữ, áp dụng DCA mua đều đặn hàng tháng. Nếu trading, theo dõi lịch FOMC và báo cáo CPI để chọn thời điểm tối ưu."}},
        {"@@type":"Question","name":"FED ảnh hưởng giá vàng tháng này thế nào?","acceptedAnswer":{"@@type":"Answer","text":"Chính sách lãi suất FED là yếu tố số 1 tác động giá vàng trung hạn. FED giữ/giảm lãi suất → chi phí cơ hội nắm giữ vàng giảm → hỗ trợ giá tăng. FED tăng lãi suất hoặc phát biểu hawkish → USD mạnh → áp lực giảm giá vàng."}},
        {"@@type":"Question","name":"Chỉ số DXY tác động giá vàng ra sao?","acceptedAnswer":{"@@type":"Answer","text":"DXY (Dollar Index) đo sức mạnh USD so với 6 đồng tiền chính. DXY tăng → USD mạnh → vàng giảm (tương quan nghịch). DXY hiện ở mức {{ $dxyValue }}. Dưới 103, DXY yếu là tín hiệu tích cực cho giá vàng."}}
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
        <div class="rounded-sm border border-slate-300 bg-slate-50 p-3 text-center">
            <p class="text-xs font-semibold text-slate-600">USD/VND</p>
            <p class="mt-1 text-xl font-bold text-slate-800">{{ $usdVndStr }}</p>
            <p class="text-xs text-slate-500">tỷ giá</p>
        </div>
    </div>
</div>

{{-- 3 kịch bản --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="target" class="h-5 w-5 text-amber-500"></i> 3 kịch bản giá vàng SJC tháng {{ $currentMonth }}</h2>
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-sm border-2 border-emerald-300 bg-emerald-50 p-4 text-center">
            <p class="text-xs font-bold text-emerald-700 uppercase">Kịch bản tích cực</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-emerald-800">{{ number_format($bullPrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            <p class="mt-1 text-sm font-semibold text-emerald-600">+{{ $bullPct }}%</p>
            <p class="mt-2 text-xs text-emerald-700">FED giảm lãi suất, NHTW mua mạnh, USD yếu, địa chính trị bất ổn</p>
        </div>
        <div class="rounded-sm border-2 border-blue-300 bg-blue-50 p-4 text-center">
            <p class="text-xs font-bold text-blue-700 uppercase">Kịch bản cơ sở</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-blue-800">{{ number_format($basePrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            <p class="mt-1 text-sm font-semibold text-blue-600">+{{ $basePct }}%</p>
            <p class="mt-2 text-xs text-blue-700">FED giữ nguyên lãi suất, kinh tế Mỹ ổn định, DXY đi ngang</p>
        </div>
        <div class="rounded-sm border-2 border-rose-300 bg-rose-50 p-4 text-center">
            <p class="text-xs font-bold text-rose-700 uppercase">Kịch bản tiêu cực</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-rose-800">{{ number_format($bearPrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            <p class="mt-1 text-sm font-semibold text-rose-600">{{ $bearPct }}%</p>
            <p class="mt-2 text-xs text-rose-700">FED hawkish, CPI Mỹ thấp, chốt lời mạnh, USD tăng</p>
        </div>
    </div>
</div>

{{-- Yếu tố vĩ mô --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="activity" class="h-5 w-5 text-amber-500"></i> Yếu tố vĩ mô tháng {{ $currentMonth }}</h2>
    <div class="overflow-x-auto rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#001061] text-white">
                    <th class="px-4 py-2.5 text-left font-semibold text-white/90">Yếu tố</th>
                    <th class="px-4 py-2.5 text-center font-semibold text-white/90">Hiện tại</th>
                    <th class="px-4 py-2.5 text-center font-semibold text-white/90">Tác động</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-white/90">Nhận định</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">Lãi suất FED</td>
                    <td class="px-4 py-2.5 text-center">5.25-5.50%</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Hỗ trợ</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Thị trường kỳ vọng FED giữ hoặc giảm → thuận lợi cho vàng</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">DXY</td>
                    <td class="px-4 py-2.5 text-center">{{ $dxyValue }}</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded {{ (float) str_replace(',', '.', $dxyValue) < 104 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} px-2 py-0.5 text-xs font-semibold">{{ (float) str_replace(',', '.', $dxyValue) < 104 ? 'Hỗ trợ' : 'Áp lực' }}</span></td>
                    <td class="px-4 py-2.5 text-slate-600">{{ (float) str_replace(',', '.', $dxyValue) < 104 ? 'USD yếu tạo thuận lợi cho vàng tăng' : 'USD mạnh tạo áp lực lên giá vàng' }}</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">CPI Mỹ</td>
                    <td class="px-4 py-2.5 text-center">~3.0%</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Hỗ trợ</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Lạm phát dai dẳng thúc đẩy nhu cầu phòng hộ bằng vàng</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">Nhu cầu NHTW</td>
                    <td class="px-4 py-2.5 text-center">Tăng mạnh</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Hỗ trợ</span></td>
                    <td class="px-4 py-2.5 text-slate-600">NHTW Trung Quốc, Ấn Độ, Ba Lan, Thổ Nhĩ Kỳ tiếp tục mua vàng</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">USD/VND</td>
                    <td class="px-4 py-2.5 text-center">{{ $usdVndStr }}</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Trung tính</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Tỷ giá ổn định, NHNN điều hành linh hoạt</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">Địa chính trị</td>
                    <td class="px-4 py-2.5 text-center">Bất ổn</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Hỗ trợ</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Xung đột khu vực, cạnh tranh Mỹ–Trung tạo nền hỗ trợ vàng</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Biểu đồ 30 ngày --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-1 flex items-center gap-2">
        <i data-lucide="line-chart" class="h-5 w-5 text-amber-500"></i>
        Biểu đồ giá vàng
    </h2>
    <p class="text-xs text-slate-500 mb-3">SJC (nét liền) · XAU quy đổi VND (nét đứt) — Đơn vị: triệu VNĐ/lượng</p>

    <div class="flex gap-2 mb-3 flex-wrap" id="moPeriodBtns">
        <button data-p="7d" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">7 ngày</button>
        <button data-p="30d" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm">30 ngày</button>
        <button data-p="1y" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">1 năm</button>
    </div>

    <div id="monthly-chart" class="w-full" class="h-[280px] sm:h-[380px]">
        <div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>
    </div>

    <div id="moStats" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4" style="display:none">
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Cao nhất</p><p id="moHigh" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thấp nhất</p><p id="moLow" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Trung bình</p><p id="moAvg" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thay đổi</p><p id="moChange" class="mt-1 text-lg font-bold text-emerald-600">—</p></div>
    </div>
</div>

{{-- Bài phân tích --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="file-text" class="h-5 w-5 text-amber-500"></i> Phân tích dự báo giá vàng tháng {{ $currentMonth }}</h2>
    <article class="prose prose-sm max-w-none text-slate-700 space-y-3">
        <p>Giá vàng SJC hiện giao dịch ở mức <strong>{{ number_format($sjcSell, 2) }} triệu VND/lượng</strong>, trong khi XAU/USD quốc tế ở <strong>{{ number_format($xauSpot, 2) }} USD/oz</strong> (quy đổi {{ number_format($xauQuyDoi, 2) }} triệu). Premium SJC so với giá quốc tế: {{ number_format(($sjcSell - $xauQuyDoi), 2) }} triệu/lượng.</p>

        <h3 class="text-lg font-semibold text-slate-800">Bối cảnh chính sách tiền tệ</h3>
        <p>FED đang trong giai đoạn cân nhắc lộ trình lãi suất. Thị trường futures đang định giá khả năng giảm lãi suất trong các cuộc họp tới. Nếu FED chuyển hướng dovish, giá vàng sẽ được hưởng lợi đáng kể do chi phí cơ hội nắm giữ vàng giảm. Ngược lại, phát biểu hawkish hoặc CPI tăng bất ngờ có thể gây áp lực ngắn hạn.</p>

        <h3 class="text-lg font-semibold text-slate-800">Nhu cầu ngân hàng trung ương</h3>
        <p>Yếu tố hỗ trợ giá vàng mạnh nhất trong giai đoạn trung hạn là nhu cầu mua vàng dự trữ từ các ngân hàng trung ương. NHTW Trung Quốc (PBOC), Ấn Độ (RBI), Ba Lan, Thổ Nhĩ Kỳ và nhiều quốc gia khác tiếp tục tích lũy vàng để đa dạng hóa dự trữ ngoại hối. Xu hướng de-dollarization (giảm phụ thuộc USD) là động lực dài hạn cho giá vàng.</p>

        <h3 class="text-lg font-semibold text-slate-800">Phân tích kỹ thuật trung hạn</h3>
        <p>Trên khung tuần, giá vàng SJC vẫn giao dịch trên đường MA(50) — tín hiệu xu hướng trung hạn tích cực. Vùng hỗ trợ tháng: <strong>{{ number_format($bearPrice, 2) }} triệu</strong>. Vùng kháng cự: <strong>{{ number_format($bullPrice, 2) }} triệu</strong>. MACD tuần cho tín hiệu phân kỳ dương.</p>

        <h3 class="text-lg font-semibold text-slate-800">Chiến lược trung hạn</h3>
        <p><strong>Tích trữ DCA:</strong> Phân bổ mua 2-4 lần/tháng tại các mức giá khác nhau. Ưu tiên vàng nhẫn 9999 nếu vốn nhỏ (1-5 chỉ/lần), vàng miếng SJC nếu đầu tư lớn (1 lượng).</p>
        <p><strong>Trading trung hạn:</strong> Mua khi RSI tuần < 40 và giá gần hỗ trợ {{ number_format($bearPrice, 2) }} triệu. Target: {{ number_format($bullPrice, 2) }} triệu. Stoploss: dưới {{ number_format(round($sjcSell * 0.96, 2), 2) }} triệu.</p>
    </article>
</div>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5 text-amber-500"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y">
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng SJC tháng này tăng hay giảm?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Kịch bản cơ sở: tăng nhẹ +{{ $basePct }}% lên {{ number_format($basePrice, 2) }} triệu, nhờ nhu cầu NHTW và kỳ vọng FED dovish. Biên độ tháng: {{ number_format($bearPrice, 2) }}–{{ number_format($bullPrice, 2) }} triệu.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>FED ảnh hưởng giá vàng thế nào?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">FED giảm lãi suất → USD yếu + chi phí cơ hội nắm giữ vàng giảm → vàng tăng. FED hawkish (tăng/giữ lãi suất) → USD mạnh → vàng giảm ngắn hạn.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>DXY là gì và tại sao quan trọng?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">DXY (Dollar Index) đo sức mạnh USD so với 6 đồng tiền lớn (EUR, JPY, GBP, CAD, SEK, CHF). DXY và vàng có tương quan nghịch: DXY tăng → vàng giảm, DXY giảm → vàng tăng. DXY hiện tại: {{ $dxyValue }}.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Chiến lược DCA mua vàng hàng tháng?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">DCA (Dollar Cost Averaging): mua một lượng cố định mỗi tháng bất kể giá. Ví dụ: mua 1 chỉ vàng nhẫn 9999 mỗi tháng. Chiến lược này giảm rủi ro timing sai, phù hợp cho tích trữ dài hạn.</p>
        </details>
    </div>
</div>

{{-- Links --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="link" class="h-5 w-5 text-amber-500"></i> Dự báo khác</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/du-bao-gia-vang" class="text-blue-700 hover:underline">Dự báo tổng hợp</a>
        <a href="/du-bao-gia-vang/du-bao-gia-vang-tuan" class="text-blue-700 hover:underline">Dự báo tuần này</a>
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
    var currentPeriod = '30d';
    var chartRoot = null;
    var activeClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm';
    var inactiveClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700';

    document.querySelectorAll('#moPeriodBtns button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentPeriod = this.dataset.p;
            document.querySelectorAll('#moPeriodBtns button').forEach(function(b) { b.className = inactiveClass; });
            this.className = activeClass;
            loadChart();
        });
    });

    function loadChart() {
        var holder = document.getElementById('monthly-chart');
        holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>';
        document.getElementById('moStats').style.display = 'none';
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
        var holder = document.getElementById('monthly-chart');
        if (!data || !data.length) { holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-500">Chưa có dữ liệu.</div>'; return; }
        holder.innerHTML = '';
        if (chartRoot) { chartRoot.dispose(); chartRoot = null; }
        chartRoot = am5.Root.new('monthly-chart');
        if (chartRoot._logo) chartRoot._logo.dispose();
        chartRoot.setThemes([am5themes_Animated.new(chartRoot)]);

        var chart = chartRoot.container.children.push(
            am5xy.XYChart.new(chartRoot, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX' })
        );

        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(chartRoot, {
            baseInterval: { timeUnit: 'day', count: 1 },
            renderer: am5xy.AxisRendererX.new(chartRoot, { minGridDistance: 60 }),
            dateFormats: { day: 'dd/MM', month: 'MM/yyyy' },
            periodChangeDateFormats: { day: 'dd/MM', month: 'MM/yyyy' }
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
        document.getElementById('moHigh').textContent = high.toFixed(2) + ' tr';
        document.getElementById('moLow').textContent = low.toFixed(2) + ' tr';
        document.getElementById('moAvg').textContent = avg.toFixed(2) + ' tr';
        var el = document.getElementById('moChange');
        el.textContent = (change >= 0 ? '+' : '') + change.toFixed(1) + '%';
        el.className = 'mt-1 text-lg font-bold ' + (change >= 0 ? 'text-emerald-600' : 'text-rose-600');
        document.getElementById('moStats').style.display = '';
    }

    loadChart();
})();
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
