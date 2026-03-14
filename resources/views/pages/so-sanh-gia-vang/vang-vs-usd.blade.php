@extends('gold.page-shell')

@section('page-label', 'So sánh')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $usCard = $snapshot['usCard'] ?? null;
    $statCards = $snapshot['statCards'] ?? [];

    $xauSpot = $usCard['variants']['spot']['price'] ?? 2918;
    $xauChange = $usCard['variants']['spot']['dayChangeLabel'] ?? '';
    $xauPercent = $usCard['trendPercent'] ?? 0;

    $dxyValue = $statCards[4]['value'] ?? '103.42';
    $dxyChange = $statCards[4]['delta'] ?? '+0.00%';
    $dxyTrend = $statCards[4]['trend'] ?? 'neutral';

    $usdVndStr = $statCards[3]['value'] ?? '25450';
    $usdVndRate = (float) str_replace([',', '.'], '', $usdVndStr);
    if ($usdVndRate < 1000) $usdVndRate = 25450;
    $usdVndChange = $statCards[3]['delta'] ?? '+0.00%';

    $xauQuyDoi = round($xauSpot * 37.5 / 31.1035 * $usdVndRate / 1e6, 2);
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
        {"@@type":"Question","name":"Vàng và USD có tương quan nghịch không?","acceptedAnswer":{"@@type":"Answer","text":"Về xu hướng tổng thể, vàng (XAU) và chỉ số USD (DXY) thường có tương quan nghịch: khi DXY giảm (USD yếu), giá vàng tính bằng USD tăng. Tuy nhiên, mối tương quan không tuyệt đối — khi căng thẳng địa chính trị cao, cả vàng và USD đều có thể tăng cùng lúc."}},
        {"@@type":"Question","name":"DXY là gì và ảnh hưởng giá vàng thế nào?","acceptedAnswer":{"@@type":"Answer","text":"DXY (US Dollar Index) đo sức mạnh đồng USD so với rổ 6 tiền tệ chính (EUR, JPY, GBP, CAD, SEK, CHF). DXY tăng = USD mạnh = vàng tính bằng USD rẻ hơn (nhưng giá VND có thể tăng do tỷ giá). DXY giảm = vàng thường tăng giá."}},
        {"@@type":"Question","name":"Fed tăng lãi suất thì giá vàng giảm?","acceptedAnswer":{"@@type":"Answer","text":"Thường thì Fed tăng lãi suất → USD mạnh lên → DXY tăng → vàng chịu áp lực giảm. Nhưng thực tế phức tạp hơn: nếu thị trường đã \"định giá trước\" (price in) việc tăng lãi suất, vàng có thể tăng khi tin được công bố (buy the rumor, sell the news)."}},
        {"@@type":"Question","name":"Tỷ giá USD/VND ảnh hưởng giá vàng nội địa thế nào?","acceptedAnswer":{"@@type":"Answer","text":"Giá vàng VND = XAU/USD × hệ số quy đổi × USD/VND. Khi tỷ giá USD/VND tăng (VND yếu), giá vàng nội địa tăng thêm ngay cả khi XAU/USD không đổi. Ngược lại, VND mạnh lên có thể giúp giá vàng nội địa giảm."}}
    ]
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-gradient-to-br from-amber-50/80 to-emerald-50/80 p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700"><i class="block h-2 w-2 rounded-full bg-emerald-500"></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">{{ $now }}</span>
    </div>
    <h2 class="text-2xl font-bold text-[#001061] mb-4">Phân tích tương quan Vàng (XAU) và USD (DXY)</h2>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-4">
        <div class="rounded-sm border-2 border-amber-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-amber-700 mb-1">Vàng (XAU/USD)</p>
            <p class="text-3xl font-bold text-amber-900 tabular-nums">{{ number_format($xauSpot, 2) }}</p>
            <p class="text-xs text-slate-500 mb-1">USD/Ounce</p>
            <div class="flex items-center justify-center gap-2 text-xs">
                @if ($xauChange)<span class="font-bold {{ str_starts_with($xauChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $xauChange }}</span>@endif
                <span class="font-semibold {{ $xauPercent >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">({{ sprintf('%+.2f%%', $xauPercent) }})</span>
            </div>
        </div>
        <div class="rounded-sm border-2 border-emerald-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-emerald-700 mb-1">USD Index (DXY)</p>
            <p class="text-3xl font-bold text-emerald-900 tabular-nums">{{ $dxyValue }}</p>
            <p class="text-xs text-slate-500 mb-1">Điểm (pts)</p>
            <div class="flex items-center justify-center text-xs">
                <span class="font-bold {{ str_starts_with($dxyChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $dxyChange }}</span>
            </div>
        </div>
        <div class="rounded-sm border-2 border-slate-300 bg-white p-4 text-center">
            <p class="text-sm font-semibold text-slate-700 mb-1">Tỷ giá USD/VND</p>
            <p class="text-3xl font-bold text-slate-900 tabular-nums">{{ number_format($usdVndRate, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mb-1">VND</p>
            <div class="flex items-center justify-center text-xs">
                <span class="font-bold {{ str_starts_with($usdVndChange, '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $usdVndChange }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div class="rounded-sm border border-indigo-200 bg-indigo-50 p-3 text-center">
            <p class="text-xs font-medium text-indigo-700">Tương quan XAU - DXY</p>
            <p class="text-xl font-bold text-indigo-800">Nghịch (Inverse)</p>
            <p class="text-xs text-slate-600">DXY giảm → Vàng thường tăng</p>
        </div>
        <div class="rounded-sm border border-amber-200 bg-amber-50 p-3 text-center">
            <p class="text-xs font-medium text-amber-700">XAU quy đổi VND</p>
            <p class="text-xl font-bold text-amber-800 tabular-nums">{{ number_format($xauQuyDoi * 1e6, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-600">VNĐ/Lượng (tham khảo)</p>
        </div>
    </div>
</div>

{{-- Bảng tổng hợp chỉ số --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3">Tổng hợp chỉ số liên quan</h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-slate-700">Chỉ số</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Giá trị</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Thay đổi</th>
                    <th class="p-3 text-left font-semibold text-slate-700">Tác động lên vàng VN</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="p-3 font-medium text-amber-800">XAU/USD (Spot)</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ number_format($xauSpot, 2) }}</td>
                    <td class="p-3 text-right font-bold {{ $xauPercent >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ sprintf('%+.2f%%', $xauPercent) }}</td>
                    <td class="p-3 text-slate-600">Trực tiếp — XAU tăng → vàng VN tăng</td>
                </tr>
                <tr>
                    <td class="p-3 font-medium text-emerald-700">DXY (USD Index)</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $dxyValue }}</td>
                    <td class="p-3 text-right font-bold {{ str_starts_with($dxyChange, '-') ? 'text-emerald-600' : 'text-rose-600' }}">{{ $dxyChange }}</td>
                    <td class="p-3 text-slate-600">Nghịch — DXY giảm → XAU tăng</td>
                </tr>
                <tr>
                    <td class="p-3 font-medium text-slate-700">USD/VND</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ number_format($usdVndRate, 0, ',', '.') }}</td>
                    <td class="p-3 text-right font-bold {{ str_starts_with($usdVndChange, '+') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $usdVndChange }}</td>
                    <td class="p-3 text-slate-600">Trực tiếp — VND yếu → vàng VN tăng thêm</td>
                </tr>
                @if (isset($statCards[5]))
                <tr>
                    <td class="p-3 font-medium text-slate-700">CPI Mỹ (Lạm phát)</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $statCards[5]['value'] ?? '—' }}</td>
                    <td class="p-3 text-right text-slate-600">{{ $statCards[5]['delta'] ?? '' }}</td>
                    <td class="p-3 text-slate-600">CPI cao → Fed giữ lãi suất → vàng giảm ngắn hạn</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-400">Cập nhật: {{ $now }}</p>
</div>

{{-- Phân tích --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-emerald-400 pl-3">Phân tích tương quan Vàng và USD</h2>

    <h3>Tại sao vàng và USD tương quan nghịch?</h3>
    <p>Vàng được giao dịch quốc tế bằng USD. Khi <strong>USD yếu</strong> (DXY giảm), cần nhiều USD hơn để mua cùng lượng vàng → giá XAU/USD tăng. Ngược lại, USD mạnh → vàng rẻ hơn tính bằng USD. Ngoài ra, khi USD yếu, nhà đầu tư chuyển sang vàng như kênh trú ẩn thay thế.</p>

    <h3>Cơ chế truyền dẫn đến giá vàng Việt Nam</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Kịch bản</th><th class="border border-slate-200 p-2 text-left font-semibold">XAU/USD</th><th class="border border-slate-200 p-2 text-left font-semibold">USD/VND</th><th class="border border-slate-200 p-2 text-left font-semibold">Giá vàng VN</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">Fed giảm lãi suất</td><td class="border border-slate-200 p-2 text-emerald-600 font-semibold">↑ Tăng</td><td class="border border-slate-200 p-2 text-emerald-600">↓ Giảm (USD yếu)</td><td class="border border-slate-200 p-2 text-emerald-600 font-bold">↑ Tăng mạnh</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Fed tăng lãi suất</td><td class="border border-slate-200 p-2 text-rose-600 font-semibold">↓ Giảm</td><td class="border border-slate-200 p-2 text-rose-600">↑ Tăng (USD mạnh)</td><td class="border border-slate-200 p-2 text-slate-600">→ Bù trừ</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Khủng hoảng địa chính trị</td><td class="border border-slate-200 p-2 text-emerald-600 font-semibold">↑ Tăng mạnh</td><td class="border border-slate-200 p-2 text-slate-600">→ Tùy tình huống</td><td class="border border-slate-200 p-2 text-emerald-600 font-bold">↑ Tăng</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Lạm phát Mỹ cao</td><td class="border border-slate-200 p-2 text-emerald-600 font-semibold">↑ Tăng (hedge)</td><td class="border border-slate-200 p-2 text-rose-600">↑ Tăng (Fed hawk)</td><td class="border border-slate-200 p-2 text-emerald-600 font-bold">↑ Tăng mạnh</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">NHNN VN can thiệp</td><td class="border border-slate-200 p-2 text-slate-600">—</td><td class="border border-slate-200 p-2 text-emerald-600">↓ Ổn định VND</td><td class="border border-slate-200 p-2 text-slate-600">→ Premium thu hẹp</td></tr>
            </tbody>
        </table>
    </div>

    <h3>Lịch sử DXY và giá vàng</h3>
    <ul>
        <li><strong>2020 (COVID):</strong> Fed hạ lãi suất về 0%, DXY giảm từ 103 → 89 → XAU tăng từ $1,500 → $2,070 (đỉnh lịch sử lúc đó)</li>
        <li><strong>2022 (Lạm phát):</strong> Fed tăng lãi suất mạnh, DXY tăng lên 114 → XAU giảm về $1,620. Nhưng giá vàng VN vẫn cao do tỷ giá USD/VND tăng</li>
        <li><strong>2023-2024:</strong> DXY dao động 100-107, XAU phá kỷ lục mới $2,400+ do NHTW mua vàng và bất ổn địa chính trị</li>
        <li><strong>2025-2026:</strong> Fed bắt đầu giảm lãi suất, DXY giảm → hỗ trợ vàng tiếp tục xu hướng tăng</li>
    </ul>

    <h3>Ảnh hưởng tỷ giá USD/VND</h3>
    <p>Yếu tố thường bị bỏ qua: giá vàng VN = XAU × hệ số × <strong>USD/VND</strong>. Khi VND yếu đi 1%, giá vàng nội địa tăng thêm 1% dù XAU/USD không đổi. Năm 2024, tỷ giá USD/VND tăng ~5% đã góp phần đẩy giá vàng nội địa lên đáng kể.</p>
</article>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Vàng và USD tương quan nghịch đúng không?', 'a' => 'Tổng thể đúng: DXY giảm → XAU thường tăng. Nhưng không tuyệt đối — trong khủng hoảng, cả hai có thể tăng cùng lúc (flight to safety).'],
            ['q' => 'DXY là gì?', 'a' => 'US Dollar Index — chỉ số đo sức mạnh USD so với 6 tiền tệ chính (EUR 57.6%, JPY 13.6%, GBP 11.9%, CAD 9.1%, SEK 4.2%, CHF 3.6%).'],
            ['q' => 'Fed tăng lãi suất thì vàng giảm?', 'a' => 'Thường đúng ngắn hạn. Nhưng nếu thị trường đã price-in trước, vàng có thể tăng khi tin ra ("buy rumor, sell news"). Lạm phát cao cũng hỗ trợ vàng.'],
            ['q' => 'Tỷ giá USD/VND tác động giá vàng VN thế nào?', 'a' => 'Giá vàng VN = XAU × hệ số quy đổi × USD/VND. VND yếu thêm 1% = giá vàng nội địa tăng 1% dù XAU không đổi.'],
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
        <a href="/so-sanh-gia-vang/sjc-vs-pnj" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs PNJ</a>
        <a href="/so-sanh-gia-vang/sjc-vs-btmc" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs BTMC</a>
        <a href="/so-sanh-gia-vang/sjc-vs-phuquy" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Phú Quý</a>
        <a href="/so-sanh-gia-vang/sjc-vs-mihong" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Mi Hồng</a>
        <a href="/so-sanh-gia-vang/sjc-vs-btmh" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Bảo Tín MH</a>
        <a href="/so-sanh-gia-vang/sjc-vs-ngoctham" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> SJC vs Ngọc Thẩm</a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
