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

    // Kịch bản dự báo tổng hợp dựa trên giá hiện tại
    $bullWeek = round($sjcSell * 1.008, 2);
    $baseWeek = $sjcSell;
    $bearWeek = round($sjcSell * 0.988, 2);
    $bullMonth = round($sjcSell * 1.03, 2);
    $baseMonth = round($sjcSell * 1.01, 2);
    $bearMonth = round($sjcSell * 0.97, 2);
    $bullYear = round($sjcSell * 1.10, 2);
    $baseYear = round($sjcSell * 1.05, 2);
    $bearYear = round($sjcSell * 0.92, 2);
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
            {"@@type": "ListItem", "position": 2, "name": "{{ $title }}", "item": "{{ url('/' . $path) }}"}
        ]
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type":"Question","name":"Giá vàng SJC ngày mai tăng hay giảm?","acceptedAnswer":{"@@type":"Answer","text":"Dự báo giá vàng SJC phụ thuộc vào nhiều yếu tố: giá XAU/USD quốc tế, tỷ giá USD/VND, chỉ số DXY, chính sách FED và tâm lý thị trường. Hiện tại giá SJC bán ra {{ number_format($sjcSell, 2) }} triệu/lượng, XAU/USD ở mức {{ number_format($xauSpot, 2) }} USD/oz. Xem 3 kịch bản chi tiết ở bảng dự báo phía trên."}},
        {"@@type":"Question","name":"Nên mua vàng bây giờ hay đợi?","acceptedAnswer":{"@@type":"Answer","text":"Quyết định mua vàng phụ thuộc vào mục tiêu đầu tư và khung thời gian. Nếu đầu tư dài hạn (1-3 năm), biến động ngắn hạn ít quan trọng — có thể áp dụng chiến lược DCA mua đều đặn. Nếu giao dịch ngắn hạn, cần theo dõi sát kỹ thuật (RSI, MACD) và lịch kinh tế (CPI, Non-farm, FOMC)."}},
        {"@@type":"Question","name":"Dự báo giá vàng năm 2026 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Các tổ chức tài chính lớn đưa ra nhiều mức dự báo khác nhau cho năm 2026. Giá vàng phụ thuộc chính sách FED, lạm phát toàn cầu, nhu cầu ngân hàng trung ương và rủi ro địa chính trị. Xem phân tích chi tiết tại trang dự báo giá vàng năm 2026."}},
        {"@@type":"Question","name":"Yếu tố nào ảnh hưởng giá vàng nhiều nhất?","acceptedAnswer":{"@@type":"Answer","text":"5 yếu tố chính: (1) Chính sách lãi suất FED — giảm lãi suất hỗ trợ giá vàng tăng; (2) Chỉ số USD (DXY) — USD yếu thì vàng tăng; (3) Lạm phát — lạm phát cao đẩy nhu cầu vàng; (4) Nhu cầu NHTW — các ngân hàng trung ương mua vàng dự trữ; (5) Rủi ro địa chính trị — bất ổn toàn cầu làm tăng nhu cầu vàng."}}
    ]
}
</script>
@endpush

@section('page-content')
{{-- Giá hiện tại --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="coins" class="h-5 w-5 text-amber-500"></i> Giá vàng hiện tại</h2>
    <div class="grid gap-5 sm:grid-cols-3">
        <div class="rounded-sm border border-[#b8860b] bg-[#fffbe6] p-4 text-center">
            <p class="text-xs font-semibold text-[#b8860b] uppercase">SJC bán ra</p>
            <p class="mt-1 text-2xl font-bold text-[#b8860b]">{{ number_format($sjcSell, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            <p class="mt-1 text-sm {{ str_contains($sjcChange, '+') ? 'text-emerald-600' : (str_contains($sjcChange, '-') ? 'text-rose-600' : 'text-slate-500') }}">{{ $sjcChange }}</p>
        </div>
        <div class="rounded-sm border border-blue-300 bg-blue-50 p-4 text-center">
            <p class="text-xs font-semibold text-blue-700 uppercase">XAU/USD</p>
            <p class="mt-1 text-2xl font-bold text-blue-800">{{ number_format($xauSpot, 2) }}</p>
            <p class="text-xs text-slate-500">USD/oz</p>
            <p class="mt-1 text-sm {{ str_contains($xauChange, '+') ? 'text-emerald-600' : (str_contains($xauChange, '-') ? 'text-rose-600' : 'text-slate-500') }}">{{ $xauChange }}</p>
        </div>
        <div class="rounded-sm border border-slate-300 bg-slate-50 p-4 text-center">
            <p class="text-xs font-semibold text-slate-600 uppercase">XAU quy đổi VND</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($xauQuyDoi, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
            @if($sjcSell > 0)
                <p class="mt-1 text-sm text-[#b8860b]">Premium: {{ number_format(($sjcSell - $xauQuyDoi), 2) }} tr</p>
            @endif
        </div>
    </div>
</div>

{{-- Bảng kịch bản dự báo tổng hợp --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="target" class="h-5 w-5 text-amber-500"></i> 3 kịch bản dự báo giá vàng SJC</h2>
    <div class="overflow-x-auto rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#001061] text-white">
                    <th class="px-4 py-2.5 text-left font-semibold text-white/90">Khung thời gian</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-emerald-300">Tích cực</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-blue-300">Cơ sở</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-rose-300">Tiêu cực</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium"><a href="/du-bao-gia-vang/du-bao-gia-vang-tuan" class="text-blue-700 hover:underline">Tuần này</a></td>
                    <td class="px-4 py-2.5 text-right text-emerald-700 font-semibold">{{ number_format($bullWeek, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-blue-700 font-semibold">{{ number_format($baseWeek, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-rose-700 font-semibold">{{ number_format($bearWeek, 2) }}</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium"><a href="/du-bao-gia-vang/du-bao-gia-vang-thang" class="text-blue-700 hover:underline">Tháng này</a></td>
                    <td class="px-4 py-2.5 text-right text-emerald-700 font-semibold">{{ number_format($bullMonth, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-blue-700 font-semibold">{{ number_format($baseMonth, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-rose-700 font-semibold">{{ number_format($bearMonth, 2) }}</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium"><a href="/du-bao-gia-vang/du-bao-gia-vang-2026" class="text-blue-700 hover:underline">Năm 2026</a></td>
                    <td class="px-4 py-2.5 text-right text-emerald-700 font-semibold">{{ number_format($bullYear, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-blue-700 font-semibold">{{ number_format($baseYear, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-rose-700 font-semibold">{{ number_format($bearYear, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-400">Đơn vị: triệu VND/lượng — Tính từ giá SJC bán ra hiện tại {{ number_format($sjcSell, 2) }} triệu</p>
</div>

{{-- Yếu tố quyết định --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="activity" class="h-5 w-5 text-amber-500"></i> Các yếu tố quyết định giá vàng</h2>
    @php
    $factors = [
        ['name' => 'Chính sách FED', 'value' => 'Kỳ vọng giữ/giảm lãi suất', 'impact' => 'positive', 'detail' => 'Lãi suất thấp giảm chi phí cơ hội nắm giữ vàng, hỗ trợ giá tăng'],
        ['name' => 'Chỉ số USD (DXY)', 'value' => $dxyValue, 'impact' => (float) str_replace(',', '.', $dxyValue) < 104 ? 'positive' : 'negative', 'detail' => 'DXY ' . $dxyChange . ' — USD yếu hỗ trợ vàng, USD mạnh gây áp lực'],
        ['name' => 'Lạm phát toàn cầu', 'value' => 'Vẫn cao', 'impact' => 'positive', 'detail' => 'Lạm phát dai dẳng thúc đẩy nhu cầu vàng như tài sản phòng hộ'],
        ['name' => 'Nhu cầu NHTW', 'value' => 'Tăng mạnh', 'impact' => 'positive', 'detail' => 'Ngân hàng trung ương Trung Quốc, Ấn Độ, Ba Lan tích cực mua vàng dự trữ'],
        ['name' => 'Rủi ro địa chính trị', 'value' => 'Trung bình–cao', 'impact' => 'neutral', 'detail' => 'Xung đột khu vực và cạnh tranh Mỹ–Trung tạo nền hỗ trợ cho vàng'],
    ];
    @endphp
    <div class="space-y-3">
        @foreach($factors as $f)
        <div class="flex items-start gap-3 rounded-sm border p-3 {{ $f['impact'] === 'positive' ? 'border-emerald-200 bg-emerald-50' : ($f['impact'] === 'negative' ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-slate-50') }}">
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-800">{{ $f['name'] }}</span>
                    <span class="text-sm font-bold {{ $f['impact'] === 'positive' ? 'text-emerald-700' : ($f['impact'] === 'negative' ? 'text-rose-700' : 'text-slate-600') }}">{{ $f['value'] }}</span>
                </div>
                <p class="mt-1 text-xs text-slate-500">{{ $f['detail'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Biểu đồ xu hướng --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-1 flex items-center gap-2">
        <i data-lucide="line-chart" class="h-5 w-5 text-amber-500"></i>
        Biểu đồ xu hướng giá vàng
    </h2>
    <p class="text-xs text-slate-500 mb-3">SJC (nét liền) · XAU quy đổi VND (nét đứt) — Đơn vị: triệu VNĐ/lượng</p>

    <div class="flex gap-2 mb-3 flex-wrap" id="fcPeriodBtns">
        <button data-p="7d" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">7 ngày</button>
        <button data-p="30d" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm">30 ngày</button>
        <button data-p="1y" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">1 năm</button>
    </div>

    <div id="forecast-chart" class="w-full" style="height:380px">
        <div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>
    </div>

    <div id="fcStats" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4" style="display:none">
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Cao nhất</p><p id="fcHigh" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thấp nhất</p><p id="fcLow" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Trung bình</p><p id="fcAvg" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thay đổi</p><p id="fcChange" class="mt-1 text-lg font-bold text-emerald-600">—</p></div>
    </div>
</div>

{{-- Bài phân tích --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="file-text" class="h-5 w-5 text-amber-500"></i> Phân tích & nhận định tổng hợp</h2>
    <article class="prose prose-sm max-w-none text-slate-700 space-y-3">
        <p>Giá vàng SJC hiện giao dịch ở mức <strong>{{ number_format($sjcSell, 2) }} triệu VND/lượng</strong>, trong khi giá vàng thế giới (XAU/USD) ở mức <strong>{{ number_format($xauSpot, 2) }} USD/oz</strong> (quy đổi khoảng {{ number_format($xauQuyDoi, 2) }} triệu VND/lượng). Chênh lệch premium giữa SJC và giá quốc tế quy đổi hiện vào khoảng {{ number_format(($sjcSell - $xauQuyDoi), 2) }} triệu VND/lượng.</p>

        <h3 class="text-lg font-semibold text-slate-800">Phân tích kỹ thuật</h3>
        <p>Biểu đồ giá vàng SJC 30 ngày cho thấy xu hướng chung đang {{ str_contains($sjcChange, '+') ? 'tích cực với lực mua mạnh hơn lực bán' : (str_contains($sjcChange, '-') ? 'điều chỉnh nhẹ, tuy nhiên vùng hỗ trợ vẫn vững' : 'đi ngang trong biên độ hẹp, cần tín hiệu breakout rõ ràng hơn') }}. Các chỉ báo kỹ thuật quan trọng cần theo dõi bao gồm RSI (14), MACD, đường trung bình MA(20) và Bollinger Bands.</p>

        <h3 class="text-lg font-semibold text-slate-800">Phân tích cơ bản</h3>
        <p>Về mặt vĩ mô, chỉ số DXY ở mức <strong>{{ $dxyValue }}</strong> ({{ $dxyChange }}). Tỷ giá USD/VND tại {{ $usdVndStr }} VND. Chính sách tiền tệ của FED là yếu tố quyết định hàng đầu — thị trường kỳ vọng FED sẽ giữ hoặc giảm lãi suất, tạo điều kiện thuận lợi cho giá vàng. Nhu cầu mua vàng dự trữ từ các ngân hàng trung ương toàn cầu vẫn ở mức cao kỷ lục.</p>

        <h3 class="text-lg font-semibold text-slate-800">Khuyến nghị</h3>
        <p><strong>Ngắn hạn (tuần):</strong> Theo dõi sát các sự kiện kinh tế Mỹ (CPI, Non-farm, FOMC). Vùng hỗ trợ quanh {{ number_format($bearWeek, 2) }} triệu, kháng cự {{ number_format($bullWeek, 2) }} triệu.</p>
        <p><strong>Trung hạn (tháng):</strong> Xu hướng chung vẫn tích cực nhờ lạm phát và nhu cầu NHTW. Chiến lược DCA (mua đều đặn) phù hợp cho nhà đầu tư cá nhân.</p>
        <p><strong>Dài hạn (năm):</strong> Các tổ chức tài chính lớn duy trì dự báo tăng giá cho vàng trong năm 2026 nhờ bất ổn địa chính trị và chu kỳ nới lỏng tiền tệ.</p>
    </article>
</div>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5 text-amber-500"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y">
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng SJC ngày mai tăng hay giảm?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Dự báo giá vàng SJC phụ thuộc vào giá XAU/USD quốc tế, tỷ giá USD/VND, chỉ số DXY, chính sách FED và tâm lý thị trường. Xem 3 kịch bản chi tiết ở bảng dự báo phía trên.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Nên mua vàng bây giờ hay đợi?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Nếu đầu tư dài hạn (1-3 năm), biến động ngắn hạn ít quan trọng — áp dụng DCA mua đều đặn. Nếu giao dịch ngắn hạn, cần theo dõi RSI, MACD và lịch kinh tế.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Dự báo giá vàng năm 2026 bao nhiêu?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Các tổ chức tài chính lớn dự báo giá vàng tiếp tục tăng trong năm 2026 nhờ chính sách FED nới lỏng, lạm phát dai dẳng và nhu cầu NHTW. Xem chi tiết tại <a href="/du-bao-gia-vang/du-bao-gia-vang-2026" class="text-blue-700 hover:underline">Dự báo giá vàng 2026</a>.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Yếu tố nào ảnh hưởng giá vàng nhiều nhất?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">5 yếu tố chính: (1) Chính sách lãi suất FED; (2) Chỉ số USD (DXY); (3) Lạm phát toàn cầu; (4) Nhu cầu mua vàng NHTW; (5) Rủi ro địa chính trị.</p>
        </details>
    </div>
</div>

{{-- Links --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="link" class="h-5 w-5 text-amber-500"></i> Xem thêm dự báo</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/du-bao-gia-vang/du-bao-gia-vang-tuan" class="text-blue-700 hover:underline">Dự báo tuần này</a>
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
    var currentPeriod = '30d';
    var chartRoot = null;
    var activeClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm';
    var inactiveClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700';

    document.querySelectorAll('#fcPeriodBtns button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentPeriod = this.dataset.p;
            document.querySelectorAll('#fcPeriodBtns button').forEach(function(b) { b.className = inactiveClass; });
            this.className = activeClass;
            loadChart();
        });
    });

    function loadChart() {
        var holder = document.getElementById('forecast-chart');
        holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>';
        document.getElementById('fcStats').style.display = 'none';
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
        var holder = document.getElementById('forecast-chart');
        if (!data || !data.length) { holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-500">Chưa có dữ liệu.</div>'; return; }
        holder.innerHTML = '';
        if (chartRoot) { chartRoot.dispose(); chartRoot = null; }
        chartRoot = am5.Root.new('forecast-chart');
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
        document.getElementById('fcHigh').textContent = high.toFixed(2) + ' tr';
        document.getElementById('fcLow').textContent = low.toFixed(2) + ' tr';
        document.getElementById('fcAvg').textContent = avg.toFixed(2) + ' tr';
        var el = document.getElementById('fcChange');
        el.textContent = (change >= 0 ? '+' : '') + change.toFixed(1) + '%';
        el.className = 'mt-1 text-lg font-bold ' + (change >= 0 ? 'text-emerald-600' : 'text-rose-600');
        document.getElementById('fcStats').style.display = '';
    }

    loadChart();
})();
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
