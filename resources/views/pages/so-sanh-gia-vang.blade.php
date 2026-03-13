@extends('gold.page-shell')

@section('page-label', 'So sánh')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $sjcCard = $snapshot['sjcCard'] ?? null;
    $sjcV = $sjcCard ? ($sjcCard['variants'][$sjcCard['selected'] ?? 'hn'] ?? null) : null;
    $sjcSell = $sjcV['sell'] ?? 0;

    $brands = [
        ['key' => 'dojiCard', 'name' => 'DOJI', 'slug' => 'sjc-vs-doji', 'color' => '#3b82f6', 'borderColor' => 'border-blue-300'],
        ['key' => 'pnjCard', 'name' => 'PNJ', 'slug' => 'sjc-vs-pnj', 'color' => '#15803d', 'borderColor' => 'border-green-300'],
        ['key' => 'btmcCard', 'name' => 'BTMC', 'slug' => 'sjc-vs-btmc', 'color' => '#dc2626', 'borderColor' => 'border-red-300'],
        ['key' => 'phuquyCard', 'name' => 'Phú Quý', 'slug' => 'sjc-vs-phuquy', 'color' => '#7c3aed', 'borderColor' => 'border-violet-300'],
        ['key' => 'mihongCard', 'name' => 'Mi Hồng', 'slug' => 'sjc-vs-mihong', 'color' => '#ea580c', 'borderColor' => 'border-orange-300'],
        ['key' => 'btmhCard', 'name' => 'Bảo Tín MH', 'slug' => 'sjc-vs-btmh', 'color' => '#0891b2', 'borderColor' => 'border-cyan-300'],
        ['key' => 'ngocthamCard', 'name' => 'Ngọc Thẩm', 'slug' => 'sjc-vs-ngoctham', 'color' => '#be185d', 'borderColor' => 'border-pink-300'],
    ];

    $usCard = $snapshot['usCard'] ?? null;
    $xauSpot = $usCard['variants']['spot']['price'] ?? 2918;
    $statCards = $snapshot['statCards'] ?? [];
    $usdVndStr = $statCards[3]['value'] ?? '25450';
    $usdVndRate = (float) str_replace([',', '.'], '', $usdVndStr);
    if ($usdVndRate < 1000) $usdVndRate = 25450;
    $xauQuyDoi = round($xauSpot * 37.5 / 31.1035 * $usdVndRate / 1e6, 2);
    $premium = $sjcSell > 0 ? round(($sjcSell - $xauQuyDoi), 2) : 0;

    $dxyValue = $statCards[4]['value'] ?? '103.42';
    $dxyChange = $statCards[4]['delta'] ?? '+0.00%';
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
        {"@@type":"Question","name":"Giá vàng SJC hôm nay cao hơn hay thấp hơn các thương hiệu khác?","acceptedAnswer":{"@@type":"Answer","text":"Giá vàng miếng SJC thường cao hơn các thương hiệu khác từ 0 đến 1 triệu VND/lượng do là thương hiệu quốc gia với thanh khoản cao nhất. Kiểm tra bảng so sánh trên trang để xem chênh lệch mới nhất."}},
        {"@@type":"Question","name":"Nên mua vàng ở đâu rẻ nhất?","acceptedAnswer":{"@@type":"Answer","text":"Giá vàng nhẫn 9999 thường rẻ hơn vàng miếng SJC từ 5-15 triệu/lượng. Giá cạnh tranh nhất thường ở Phú Quý, Mi Hồng (TP.HCM) hoặc BTMC, BTMH (Hà Nội). So sánh trực tiếp tại trang này."}},
        {"@@type":"Question","name":"Chênh lệch giá vàng SJC và thế giới (premium) là bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Premium SJC so với giá vàng thế giới quy đổi thường từ 10-20 triệu VND/lượng, phụ thuộc vào cung cầu nội địa và chính sách NHNN. Premium thay đổi liên tục và được cập nhật trên trang."}},
        {"@@type":"Question","name":"Vàng tăng khi USD giảm đúng không?","acceptedAnswer":{"@@type":"Answer","text":"Đúng về xu hướng. Vàng (XAU) và USD (DXY) thường có tương quan nghịch: khi DXY giảm, giá vàng thế giới tính bằng USD sẽ tăng. Tuy nhiên, giá vàng nội địa còn phụ thuộc tỷ giá USD/VND."}}
    ]
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-gradient-to-br from-amber-50/80 to-indigo-50/80 p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700"><i class="block h-2 w-2 rounded-full bg-emerald-500"></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">{{ $now }}</span>
    </div>
    <h2 class="text-2xl font-bold text-[#001061] mb-2">So sánh giá vàng hôm nay — SJC vs các thương hiệu</h2>
    <p class="text-sm text-slate-600 mb-4">Giá bán SJC hiện tại: <span class="font-bold text-amber-900">{{ $sjcSell > 0 ? number_format($sjcSell * 1e6, 0, ',', '.') . ' VNĐ/lượng' : 'Đang cập nhật...' }}</span></p>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($brands as $b)
        @php
            $card = $snapshot[$b['key']] ?? null;
            $v = $card ? ($card['variants'][$card['selected'] ?? array_key_first($card['variants'] ?? [])] ?? null) : null;
            $otherSell = $v['sell'] ?? 0;
            $diff = $sjcSell > 0 && $otherSell > 0 ? ($sjcSell - $otherSell) * 1e6 : null;
        @endphp
        <a href="/so-sanh-gia-vang/{{ $b['slug'] }}" class="block rounded-sm border-2 {{ $b['borderColor'] }} bg-white p-4 hover:shadow-md transition no-underline">
            <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-amber-800">SJC</span>
                <span class="text-xs text-slate-400">vs</span>
                <span class="font-bold" style="color:{{ $b['color'] }}">{{ $b['name'] }}</span>
            </div>
            @if ($otherSell > 0)
            <p class="text-sm text-slate-600">{{ $b['name'] }} bán: <span class="font-semibold">{{ number_format($otherSell * 1e6, 0, ',', '.') }}</span></p>
            @endif
            @if ($diff !== null)
            <p class="text-lg font-bold {{ $diff >= 0 ? 'text-rose-600' : 'text-emerald-600' }} tabular-nums">{{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500">{{ $diff >= 0 ? 'SJC đắt hơn' : $b['name'] . ' đắt hơn' }}</p>
            @else
            <p class="text-sm text-slate-400 mt-2">Đang cập nhật...</p>
            @endif
        </a>
        @endforeach

        <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="block rounded-sm border-2 border-yellow-300 bg-white p-4 hover:shadow-md transition no-underline">
            <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-amber-800">SJC</span>
                <span class="text-xs text-slate-400">vs</span>
                <span class="font-bold text-yellow-700">Thế giới</span>
            </div>
            @if ($xauQuyDoi > 0)
            <p class="text-sm text-slate-600">XAU quy đổi: <span class="font-semibold">{{ number_format($xauQuyDoi * 1e6, 0, ',', '.') }}</span></p>
            @endif
            @if ($premium != 0)
            <p class="text-lg font-bold text-rose-600 tabular-nums">+{{ number_format($premium * 1e6, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Premium nội địa</p>
            @endif
        </a>

        <a href="/so-sanh-gia-vang/vang-vs-usd" class="block rounded-sm border-2 border-emerald-300 bg-white p-4 hover:shadow-md transition no-underline">
            <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-amber-800">Vàng (XAU)</span>
                <span class="text-xs text-slate-400">vs</span>
                <span class="font-bold text-emerald-700">USD (DXY)</span>
            </div>
            <p class="text-sm text-slate-600">XAU: <span class="font-semibold">{{ number_format($xauSpot, 2) }} USD</span></p>
            <p class="text-sm text-slate-600">DXY: <span class="font-semibold">{{ $dxyValue }}</span></p>
            <p class="text-xs text-slate-500 mt-1">Tương quan nghịch</p>
        </a>
    </div>
</div>

{{-- Bảng tổng hợp so sánh --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="bar-chart-3" class="h-5 w-5"></i> Bảng so sánh giá vàng tổng hợp
    </h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-slate-700">Thương hiệu</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Giá mua</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Giá bán</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Spread</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Chênh vs SJC</th>
                    <th class="p-3 text-center font-semibold text-slate-700">Chi tiết</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="bg-amber-50/50">
                    <td class="p-3 font-bold text-amber-800">SJC</td>
                    <td class="p-3 text-right tabular-nums">{{ $sjcV && ($sjcV['buy'] ?? 0) > 0 ? number_format(($sjcV['buy'] ?? 0) * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $sjcSell > 0 ? number_format($sjcSell * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums">{{ $sjcV ? number_format((($sjcV['sell'] ?? 0) - ($sjcV['buy'] ?? 0)) * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right text-slate-400">—</td>
                    <td class="p-3 text-center text-slate-400">—</td>
                </tr>
                @foreach ($brands as $b)
                @php
                    $card = $snapshot[$b['key']] ?? null;
                    $v = $card ? ($card['variants'][$card['selected'] ?? array_key_first($card['variants'] ?? [])] ?? null) : null;
                    $oSell = $v['sell'] ?? 0;
                    $oBuy = $v['buy'] ?? 0;
                    $oSpread = ($oSell - $oBuy) * 1e6;
                    $diff = $sjcSell > 0 && $oSell > 0 ? ($sjcSell - $oSell) * 1e6 : null;
                @endphp
                <tr>
                    <td class="p-3 font-medium" style="color:{{ $b['color'] }}">{{ $b['name'] }}</td>
                    <td class="p-3 text-right tabular-nums">{{ $oBuy > 0 ? number_format($oBuy * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $oSell > 0 ? number_format($oSell * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums">{{ $oSell > 0 ? number_format($oSpread, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right tabular-nums font-bold {{ $diff !== null ? ($diff >= 0 ? 'text-rose-600' : 'text-emerald-600') : 'text-slate-400' }}">
                        @if ($diff !== null) {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }} @else — @endif
                    </td>
                    <td class="p-3 text-center"><a href="/so-sanh-gia-vang/{{ $b['slug'] }}" class="text-blue-600 hover:underline font-medium">Xem →</a></td>
                </tr>
                @endforeach
                <tr class="bg-yellow-50/50">
                    <td class="p-3 font-medium text-yellow-700">XAU quy đổi</td>
                    <td class="p-3 text-right text-slate-400">—</td>
                    <td class="p-3 text-right tabular-nums font-semibold">{{ $xauQuyDoi > 0 ? number_format($xauQuyDoi * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-right text-slate-400">—</td>
                    <td class="p-3 text-right tabular-nums font-bold text-rose-600">{{ $premium > 0 ? '+' . number_format($premium * 1e6, 0, ',', '.') : '—' }}</td>
                    <td class="p-3 text-center"><a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="text-blue-600 hover:underline font-medium">Xem →</a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-400">Đơn vị: VNĐ/Lượng · Cập nhật: {{ $now }}</p>
</div>

{{-- Phân tích --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-indigo-400 pl-3">So sánh giá vàng — Hướng dẫn chọn nơi mua</h2>

    <p><strong>So sánh giá vàng</strong> giữa các thương hiệu giúp người mua tìm được mức giá tốt nhất và hiểu rõ chênh lệch giữa vàng miếng SJC và các loại vàng nhẫn 9999 trên thị trường.</p>

    <h3>Phân loại thương hiệu vàng tại Việt Nam</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Thương hiệu</th><th class="border border-slate-200 p-2 text-left font-semibold">Khu vực</th><th class="border border-slate-200 p-2 text-left font-semibold">Sản phẩm chủ lực</th><th class="border border-slate-200 p-2 text-left font-semibold">Đặc điểm nổi bật</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium text-amber-800">SJC</td><td class="border border-slate-200 p-2">Toàn quốc</td><td class="border border-slate-200 p-2">Vàng miếng SJC 1L, 5 chỉ</td><td class="border border-slate-200 p-2">Thương hiệu quốc gia, thanh khoản cao nhất</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium text-blue-700">DOJI</td><td class="border border-slate-200 p-2">Toàn quốc</td><td class="border border-slate-200 p-2">Vàng nhẫn DOJI 9999, SJC miếng</td><td class="border border-slate-200 p-2">Hệ thống lớn, giá cập nhật sớm</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium text-green-700">PNJ</td><td class="border border-slate-200 p-2">Toàn quốc</td><td class="border border-slate-200 p-2">Vàng nhẫn PNJ 9999, trang sức</td><td class="border border-slate-200 p-2">Niêm yết sàn HOSE, cửa hàng chuyên nghiệp</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium text-red-700">BTMC</td><td class="border border-slate-200 p-2">Hà Nội</td><td class="border border-slate-200 p-2">Nhẫn Rồng Thăng Long 9999</td><td class="border border-slate-200 p-2">Truyền thống Hà Nội, giá cạnh tranh</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium text-violet-700">Phú Quý</td><td class="border border-slate-200 p-2">Hà Nội</td><td class="border border-slate-200 p-2">Vàng nhẫn 9999</td><td class="border border-slate-200 p-2">Giá rẻ nhất Hà Nội, biên lợi nhuận mỏng</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium text-orange-700">Mi Hồng</td><td class="border border-slate-200 p-2">TP.HCM</td><td class="border border-slate-200 p-2">Vàng nhẫn 9999, vàng ta 990</td><td class="border border-slate-200 p-2">Chợ Lớn, chi phí thấp, giá tốt</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium text-cyan-700">Bảo Tín MH</td><td class="border border-slate-200 p-2">Hà Nội</td><td class="border border-slate-200 p-2">Tiểu Kim Cát (1 chỉ nhẫn)</td><td class="border border-slate-200 p-2">DCA vàng theo chỉ, sản phẩm nhỏ</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium text-pink-700">Ngọc Thẩm</td><td class="border border-slate-200 p-2">TP.HCM</td><td class="border border-slate-200 p-2">Vàng nhẫn, vàng ta 990, 18K</td><td class="border border-slate-200 p-2">Đa dạng sản phẩm vùng TP.HCM</td></tr>
            </tbody>
        </table>
    </div>

    <h3>Yếu tố ảnh hưởng chênh lệch giá</h3>
    <ul>
        <li><strong>Loại vàng:</strong> Vàng miếng SJC có giá cao hơn vàng nhẫn 9999 từ 5-15 triệu/lượng do cung hạn chế và thanh khoản cao.</li>
        <li><strong>Spread mua-bán:</strong> Spread thấp = chi phí giao dịch thấp. Thương hiệu lớn thường có spread hẹp hơn.</li>
        <li><strong>Premium nội địa:</strong> Chênh lệch SJC vs giá thế giới quy đổi phụ thuộc vào cung cầu và chính sách NHNN.</li>
        <li><strong>Khu vực:</strong> Giá có thể khác nhau giữa Hà Nội, TP.HCM và các tỉnh do chi phí vận chuyển và cung cầu địa phương.</li>
    </ul>
</article>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Giá vàng SJC hôm nay cao hơn hay thấp hơn các thương hiệu khác?', 'a' => 'Vàng miếng SJC thường cao hơn các thương hiệu vàng nhẫn 9999 từ 5-15 triệu/lượng. So với các đại lý bán vàng miếng SJC (DOJI, BTMC, PNJ), chênh lệch thường từ 0-500k VND.'],
            ['q' => 'Nên mua vàng ở đâu rẻ nhất?', 'a' => 'Giá vàng nhẫn 9999 thường rẻ nhất tại Phú Quý (Hà Nội) và Mi Hồng (TP.HCM). Vàng miếng SJC thì so sánh giá bán tại SJC, DOJI, BTMC để tìm mức tốt nhất.'],
            ['q' => 'Chênh lệch SJC và thế giới (premium) là bao nhiêu?', 'a' => 'Premium SJC thường từ 10-20 triệu/lượng. Khi NHNN bán vàng can thiệp, premium thu hẹp = cơ hội mua SJC giá tốt.'],
            ['q' => 'Vàng tăng khi USD giảm đúng không?', 'a' => 'Xu hướng chung: XAU và DXY tương quan nghịch. Nhưng giá vàng nội địa còn phụ thuộc tỷ giá USD/VND.'],
        ]; @endphp
        @foreach ($faqs as $faq)
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition"><span>{{ $faq['q'] }}</span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i></summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">{{ $faq['a'] }}</p>
        </details>
        @endforeach
    </div>
</div>

<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="link" class="h-5 w-5"></i> Trang liên quan</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/gia-vang-hom-nay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="tag" class="h-3.5 w-3.5 text-slate-400"></i> Giá vàng hôm nay</a>
        <a href="/gia-vang-the-gioi" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="globe" class="h-3.5 w-3.5 text-slate-400"></i> Giá vàng thế giới</a>
        <a href="/bieu-do-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="line-chart" class="h-3.5 w-3.5 text-slate-400"></i> Biểu đồ giá vàng</a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
