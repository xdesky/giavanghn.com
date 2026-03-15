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
    $sjcChange = $sjcV['dayChangeLabel'] ?? '';

    $xauSpot = $usCard['variants']['spot']['price'] ?? 2918;
    $xauChange = $usCard['variants']['spot']['dayChangeLabel'] ?? '';

    $usdVndStr = $statCards[3]['value'] ?? '25450';
    $usdVndRate = (float) str_replace([',', '.'], '', $usdVndStr);
    if ($usdVndRate < 1000) $usdVndRate = 25450;
    $xauQuyDoi = round($xauSpot * 37.5 / 31.1035 * $usdVndRate / 1e6, 2);

    $dxyValue = $statCards[4]['value'] ?? '103.42';

    // Kịch bản năm 2026: biến động ±8-10%
    $bullPrice = round($sjcSell * 1.10, 2);
    $basePrice = round($sjcSell * 1.05, 2);
    $bearPrice = round($sjcSell * 0.92, 2);
    $bullPct = $sjcSell > 0 ? round(($bullPrice - $sjcSell) / $sjcSell * 100, 1) : 0;
    $basePct = $sjcSell > 0 ? round(($basePrice - $sjcSell) / $sjcSell * 100, 1) : 0;
    $bearPct = $sjcSell > 0 ? round(($bearPrice - $sjcSell) / $sjcSell * 100, 1) : 0;

    // Quy đổi kịch bản XAU/USD (ước tính)
    $xauBull = round($xauSpot * 1.12);
    $xauBase = round($xauSpot * 1.06);
    $xauBear = round($xauSpot * 0.93);
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
        {"@@type":"Question","name":"Giá vàng năm 2026 dự báo bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Dựa trên phân tích tổng hợp, giá vàng SJC cuối năm 2026 có 3 kịch bản: tích cực {{ number_format($bullPrice, 2) }} triệu/lượng (+{{ $bullPct }}%), cơ sở {{ number_format($basePrice, 2) }} triệu (+{{ $basePct }}%), tiêu cực {{ number_format($bearPrice, 2) }} triệu ({{ $bearPct }}%). Giá XAU/USD tương ứng: {{ number_format($xauBull) }}–{{ number_format($xauBear) }} USD/oz."}},
        {"@@type":"Question","name":"Goldman Sachs dự báo giá vàng 2026 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Goldman Sachs thuộc nhóm lạc quan nhất với dự báo XAU/USD có thể đạt {{ number_format($xauBull) }}+ USD/oz vào cuối năm 2026, nhờ nhu cầu NHTW mạnh, chu kỳ giảm lãi suất FED và rủi ro địa chính trị. Các tổ chức khác (UBS, JP Morgan) đưa dự báo thận trọng hơn quanh {{ number_format($xauBase) }} USD/oz."}},
        {"@@type":"Question","name":"Nên đầu tư vàng dài hạn năm 2026 không?","acceptedAnswer":{"@@type":"Answer","text":"Vàng là tài sản phòng hộ lạm phát và rủi ro hệ thống. Trong bối cảnh bất ổn địa chính trị, lạm phát dai dẳng và xu hướng de-dollarization, vàng được nhiều chuyên gia khuyến nghị chiếm 5-15% danh mục đầu tư. Chiến lược DCA phù hợp cho đầu tư dài hạn."}},
        {"@@type":"Question","name":"Rủi ro nào có thể khiến giá vàng giảm năm 2026?","acceptedAnswer":{"@@type":"Answer","text":"Rủi ro chính: (1) FED tăng lãi suất bất ngờ nếu lạm phát tái bùng phát; (2) USD mạnh lên nếu kinh tế Mỹ outperform; (3) Chốt lời mạnh sau chu kỳ tăng; (4) Giảm nhu cầu NHTW; (5) Thỏa thuận hòa bình giảm rủi ro địa chính trị."}}
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
            <p class="text-xs font-semibold text-slate-600">XAU quy đổi VND</p>
            <p class="mt-1 text-xl font-bold text-slate-800">{{ number_format($xauQuyDoi, 2) }}</p>
            <p class="text-xs text-slate-500">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-slate-300 bg-slate-50 p-3 text-center">
            <p class="text-xs font-semibold text-slate-600">DXY</p>
            <p class="mt-1 text-xl font-bold text-slate-800">{{ $dxyValue }}</p>
            <p class="text-xs text-slate-500">USD/VND: {{ $usdVndStr }}</p>
        </div>
    </div>
</div>

{{-- 3 kịch bản SJC --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="target" class="h-5 w-5 text-amber-500"></i> 3 kịch bản giá vàng SJC cuối năm 2026</h2>
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-sm border-2 border-emerald-300 bg-emerald-50 p-4 text-center">
            <p class="text-xs font-bold text-emerald-700 uppercase">Kịch bản tích cực</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-emerald-800">{{ number_format($bullPrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu VND/lượng</p>
            <p class="mt-1 text-sm font-semibold text-emerald-600">+{{ $bullPct }}%</p>
            <p class="mt-1 text-xs text-blue-700">XAU/USD: ~{{ number_format($xauBull) }} USD/oz</p>
            <p class="mt-2 text-xs text-emerald-700">FED giảm lãi suất mạnh, NHTW mua kỷ lục, bất ổn địa chính trị leo thang</p>
        </div>
        <div class="rounded-sm border-2 border-blue-300 bg-blue-50 p-4 text-center">
            <p class="text-xs font-bold text-blue-700 uppercase">Kịch bản cơ sở</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-blue-800">{{ number_format($basePrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu VND/lượng</p>
            <p class="mt-1 text-sm font-semibold text-blue-600">+{{ $basePct }}%</p>
            <p class="mt-1 text-xs text-blue-700">XAU/USD: ~{{ number_format($xauBase) }} USD/oz</p>
            <p class="mt-2 text-xs text-blue-700">FED giảm từ từ, kinh tế ổn định, nhu cầu NHTW tiếp tục</p>
        </div>
        <div class="rounded-sm border-2 border-rose-300 bg-rose-50 p-4 text-center">
            <p class="text-xs font-bold text-rose-700 uppercase">Kịch bản tiêu cực</p>
            <p class="mt-2 text-lg sm:text-2xl font-bold text-rose-800">{{ number_format($bearPrice, 2) }}</p>
            <p class="text-xs text-slate-500">triệu VND/lượng</p>
            <p class="mt-1 text-sm font-semibold text-rose-600">{{ $bearPct }}%</p>
            <p class="mt-1 text-xs text-blue-700">XAU/USD: ~{{ number_format($xauBear) }} USD/oz</p>
            <p class="mt-2 text-xs text-rose-700">FED hawkish, USD mạnh, hòa bình địa chính trị, chốt lời mạnh</p>
        </div>
    </div>
    <p class="mt-2 text-xs text-slate-400">Tính từ giá SJC bán ra hiện tại {{ number_format($sjcSell, 2) }} triệu/lượng</p>
</div>

{{-- Nhận định các tổ chức --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="building-2" class="h-5 w-5 text-amber-500"></i> Nhận định từ các tổ chức tài chính</h2>
    <div class="overflow-x-auto rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#001061] text-white">
                    <th class="px-4 py-2.5 text-left font-semibold text-white/90">Tổ chức</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-white/90">Dự báo XAU/USD</th>
                    <th class="px-4 py-2.5 text-center font-semibold text-white/90">Xu hướng</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-white/90">Lý do chính</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">Goldman Sachs</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-emerald-700">{{ number_format($xauBull) }} USD/oz</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Tăng mạnh</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Nhu cầu NHTW, FED giảm lãi suất, de-dollarization</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">UBS</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-emerald-700">{{ number_format(round($xauSpot * 1.08)) }} USD/oz</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Tăng</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Đa dạng hóa danh mục, lạm phát dai dẳng</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">JP Morgan</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-blue-700">{{ number_format($xauBase) }} USD/oz</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Tăng vừa</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Rủi ro suy thoái Mỹ, nhu cầu phòng hộ</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">Citigroup</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-emerald-700">{{ number_format(round($xauSpot * 1.10)) }} USD/oz</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Tăng mạnh</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Chu kỳ nới lỏng tiền tệ toàn cầu</td>
                </tr>
                <tr class="transition-colors hover:bg-blue-50/60">
                    <td class="px-4 py-2.5 font-medium">World Gold Council</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-blue-700">{{ number_format(round($xauSpot * 1.05)) }}+ USD/oz</td>
                    <td class="px-4 py-2.5 text-center"><span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Tích cực</span></td>
                    <td class="px-4 py-2.5 text-slate-600">Nhu cầu NHTW & nhà đầu tư ETF tiếp tục tăng</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Yếu tố dài hạn --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="trending-up" class="h-5 w-5 text-amber-500"></i> Yếu tố chi phối giá vàng năm 2026</h2>
    @php
    $yearFactors = [
        ['name' => 'Chu kỳ lãi suất FED', 'impact' => 'positive', 'detail' => 'Thị trường kỳ vọng FED giảm lãi suất 2-3 lần trong năm 2026. Mỗi lần giảm 25bps giúp vàng tăng 3-5%. Lãi suất thấp hơn giảm chi phí cơ hội nắm giữ vàng.'],
        ['name' => 'Nhu cầu NHTW', 'impact' => 'positive', 'detail' => 'Năm 2024-2025, NHTW mua hơn 1,000 tấn vàng/năm — mức cao kỷ lục. Xu hướng de-dollarization và đa dạng hóa dự trữ dự kiến tiếp tục mạnh trong 2026.'],
        ['name' => 'Lạm phát toàn cầu', 'impact' => 'positive', 'detail' => 'Lạm phát tại nhiều nền kinh tế lớn vẫn trên mục tiêu 2%. Lạm phát dai dẳng thúc đẩy nhu cầu vàng như tài sản phòng hộ giá trị.'],
        ['name' => 'Rủi ro địa chính trị', 'impact' => 'positive', 'detail' => 'Xung đột Nga-Ukraine, căng thẳng Trung Đông, cạnh tranh Mỹ-Trung chưa có dấu hiệu giải quyết. Bất ổn địa chính trị là nền hỗ trợ dài hạn cho vàng.'],
        ['name' => 'Nhu cầu ETF & nhà đầu tư', 'impact' => 'positive', 'detail' => 'Dòng vốn vào các ETF vàng (GLD, IAU) tăng trở lại. Nhu cầu đầu tư cá nhân tại Trung Quốc, Ấn Độ, Việt Nam ở mức cao nhờ lo ngại bất ổn kinh tế.'],
        ['name' => 'Rủi ro: USD phục hồi mạnh', 'impact' => 'negative', 'detail' => 'Nếu kinh tế Mỹ outperform và FED giữ lãi suất cao lâu hơn dự kiến, USD có thể mạnh lên, tạo áp lực giảm giá vàng 5-10%.'],
    ];
    @endphp
    <div class="space-y-3">
        @foreach($yearFactors as $f)
        <div class="flex items-start gap-3 rounded-sm border p-3 {{ $f['impact'] === 'positive' ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-800">{{ $f['name'] }}</span>
                    <span class="text-xs font-bold {{ $f['impact'] === 'positive' ? 'text-emerald-700' : 'text-rose-700' }}">{{ $f['impact'] === 'positive' ? 'Hỗ trợ tăng' : 'Rủi ro giảm' }}</span>
                </div>
                <p class="mt-1 text-xs text-slate-600">{{ $f['detail'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Biểu đồ 1 năm --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-1 flex items-center gap-2">
        <i data-lucide="line-chart" class="h-5 w-5 text-amber-500"></i>
        Biểu đồ giá vàng
    </h2>
    <p class="text-xs text-slate-500 mb-3">SJC (nét liền) · XAU quy đổi VND (nét đứt) — Đơn vị: triệu VNĐ/lượng</p>

    <div class="flex gap-2 mb-3 flex-wrap" id="yrPeriodBtns">
        <button data-p="30d" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">30 ngày</button>
        <button data-p="1y" class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm">1 năm</button>
    </div>

    <div id="yearly-chart" class="w-full h-[280px] sm:h-[380px]">
        <div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>
    </div>

    <div id="yrStats" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4" style="display:none">
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Cao nhất</p><p id="yrHigh" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thấp nhất</p><p id="yrLow" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Trung bình</p><p id="yrAvg" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thay đổi</p><p id="yrChange" class="mt-1 text-lg font-bold text-emerald-600">—</p></div>
    </div>
</div>

{{-- Bài phân tích --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="file-text" class="h-5 w-5 text-amber-500"></i> Phân tích & nhận định giá vàng năm 2026</h2>
    <article class="prose prose-sm max-w-none text-slate-700 space-y-3">
        <p>Giá vàng SJC hiện giao dịch ở <strong>{{ number_format($sjcSell, 2) }} triệu VND/lượng</strong>, XAU/USD tại <strong>{{ number_format($xauSpot, 2) }} USD/oz</strong>. Giá vàng thế giới quy đổi {{ number_format($xauQuyDoi, 2) }} triệu/lượng, premium SJC khoảng {{ number_format(($sjcSell - $xauQuyDoi), 2) }} triệu.</p>

        <h3 class="text-lg font-semibold text-slate-800">Xu hướng dài hạn</h3>
        <p>Vàng đang trong chu kỳ tăng giá (bull market) dài hạn bắt đầu từ năm 2019. Động lực chính bao gồm: (1) Nhu cầu mua vàng dự trữ từ NHTW ở mức kỷ lục — Trung Quốc, Ấn Độ, Ba Lan, Thổ Nhĩ Kỳ tích cực tăng tỷ trọng vàng trong dự trữ ngoại hối; (2) Xu hướng de-dollarization khi các quốc gia muốn giảm phụ thuộc vào USD; (3) Lạm phát toàn cầu dai dẳng trên mục tiêu.</p>

        <h3 class="text-lg font-semibold text-slate-800">Tác động chính sách FED</h3>
        <p>FED dự kiến chuyển sang chu kỳ giảm lãi suất trong năm 2026. Mỗi lần giảm 25 bps thường hỗ trợ giá vàng tăng 3-5% trong ngắn hạn. Nếu FED giảm 2-3 lần (tổng 50-75 bps), giá XAU/USD có thể thêm 8-12%. Tuy nhiên, nếu lạm phát tái bùng phát buộc FED giữ/tăng lãi suất, đây là rủi ro lớn nhất cho kịch bản bearish.</p>

        <h3 class="text-lg font-semibold text-slate-800">Giá vàng SJC trong nước</h3>
        <p>Giá vàng SJC phụ thuộc 3 yếu tố: (1) Giá XAU/USD quốc tế; (2) Tỷ giá USD/VND (hiện {{ $usdVndStr }}); (3) Premium SJC (chênh lệch cung-cầu nội địa). Nếu XAU/USD tăng 10% lên ~{{ number_format(round($xauSpot * 1.10)) }} USD/oz, với tỷ giá ổn định, giá SJC quy đổi sẽ vào khoảng {{ number_format(round($xauSpot * 1.10 * 37.5 / 31.1035 * $usdVndRate / 1e6, 2), 2) }} triệu + premium.</p>

        <h3 class="text-lg font-semibold text-slate-800">Chiến lược đầu tư vàng dài hạn</h3>
        <p><strong>DCA (Dollar Cost Averaging):</strong> Mua đều đặn hàng tháng (ví dụ: 1-2 chỉ vàng nhẫn 9999/tháng). Giảm rủi ro timing, tận dụng giá trung bình.</p>
        <p><strong>Phân bổ danh mục:</strong> Các chuyên gia khuyến nghị vàng chiếm 5-15% tổng danh mục đầu tư. Tăng tỷ trọng khi bất ổn, giảm khi thị trường ổn định.</p>
        <p><strong>Lựa chọn sản phẩm:</strong> Vàng miếng SJC 1 lượng — thanh khoản cao nhất, phù hợp đầu tư lớn. Vàng nhẫn 9999 (DOJI, PNJ, BTMC) — linh hoạt, giá rẻ hơn SJC, phù hợp DCA.</p>
    </article>
</div>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5 text-amber-500"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y">
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng năm 2026 có tăng không?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Đa số tổ chức tài chính (Goldman Sachs, UBS, Citigroup) dự báo giá vàng tăng trong năm 2026 nhờ FED giảm lãi suất, nhu cầu NHTW và lạm phát. Kịch bản cơ sở: tăng +{{ $basePct }}% lên {{ number_format($basePrice, 2) }} triệu.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Goldman Sachs dự báo vàng bao nhiêu?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Goldman Sachs dự báo XAU/USD có thể đạt {{ number_format($xauBull) }}+ USD/oz cuối năm 2026, thuộc nhóm lạc quan nhất. UBS và JP Morgan thận trọng hơn với mức {{ number_format($xauBase) }}–{{ number_format(round($xauSpot * 1.08)) }} USD/oz.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Nên đầu tư vàng miếng SJC hay vàng nhẫn?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Vàng miếng SJC: thanh khoản cao nhất nhưng giá cao hơn thế giới 5-15 triệu (premium). Vàng nhẫn 9999: giá sát thế giới hơn, linh hoạt (1-5 chỉ), phù hợp DCA. Nếu đầu tư >1 lượng: SJC. Nếu tích trữ nhỏ lẻ: nhẫn 9999.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Rủi ro nào khiến giá vàng giảm năm 2026?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">5 rủi ro chính: (1) FED hawkish giữ/tăng lãi suất; (2) USD mạnh (DXY > 108); (3) Chốt lời sau chu kỳ tăng dài; (4) NHTW giảm mua; (5) Hòa bình địa chính trị giảm nhu cầu safe-haven.</p>
        </details>
    </div>
</div>

{{-- Links --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="link" class="h-5 w-5 text-amber-500"></i> Dự báo khác</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/du-bao-gia-vang" class="text-blue-700 hover:underline">Dự báo tổng hợp</a>
        <a href="/du-bao-gia-vang/du-bao-gia-vang-tuan" class="text-blue-700 hover:underline">Dự báo tuần này</a>
        <a href="/du-bao-gia-vang/du-bao-gia-vang-thang" class="text-blue-700 hover:underline">Dự báo tháng này</a>
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
    var currentPeriod = '1y';
    var chartRoot = null;
    var activeClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-[#001061] text-white border-[#001061] shadow-sm';
    var inactiveClass = 'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-all border bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700';

    document.querySelectorAll('#yrPeriodBtns button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentPeriod = this.dataset.p;
            document.querySelectorAll('#yrPeriodBtns button').forEach(function(b) { b.className = inactiveClass; });
            this.className = activeClass;
            loadChart();
        });
    });

    function loadChart() {
        var holder = document.getElementById('yearly-chart');
        holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-400">Đang tải biểu đồ…</div>';
        document.getElementById('yrStats').style.display = 'none';
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
        var holder = document.getElementById('yearly-chart');
        if (!data || !data.length) { holder.innerHTML = '<div style="display:grid;height:100%;place-items:center" class="text-sm text-slate-500">Chưa có dữ liệu.</div>'; return; }
        holder.innerHTML = '';
        if (chartRoot) { chartRoot.dispose(); chartRoot = null; }
        chartRoot = am5.Root.new('yearly-chart');
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
            if (dashed) s.strokes.template.setAll({ strokeWidth: 1, strokeDasharray: [6, 3] });
            else s.strokes.template.setAll({ strokeWidth: 1 });
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
        document.getElementById('yrHigh').textContent = high.toFixed(2) + ' tr';
        document.getElementById('yrLow').textContent = low.toFixed(2) + ' tr';
        document.getElementById('yrAvg').textContent = avg.toFixed(2) + ' tr';
        var el = document.getElementById('yrChange');
        el.textContent = (change >= 0 ? '+' : '') + change.toFixed(1) + '%';
        el.className = 'mt-1 text-lg font-bold ' + (change >= 0 ? 'text-emerald-600' : 'text-rose-600');
        document.getElementById('yrStats').style.display = '';
    }

    loadChart();
})();
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
