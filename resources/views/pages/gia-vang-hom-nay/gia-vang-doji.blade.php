@extends('gold.page-shell')

@section('page-label', 'Bảng giá vàng DOJI')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $card = $snapshot['dojiCard'] ?? null;
    $variants = $card['variants'] ?? [];
    $offers = [];
    foreach ($variants as $v) {
        if (($v['sell'] ?? 0) <= 0) continue;
        $offers[] = ['@type' => 'Offer', 'name' => $v['label'], 'price' => round($v['sell'] * 1000000), 'priceCurrency' => 'VND', 'priceValidUntil' => now()->endOfDay()->toIso8601String()];
    }
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Giá vàng DOJI hôm nay {{ now()->format('d/m/Y') }}",
    "description": "{{ $description }}",
    "dateModified": "{{ now()->toIso8601String() }}",
    "mainEntity": {
        "@@type": "Product",
        "name": "Vàng DOJI",
        "brand": {"@@type": "Brand", "name": "DOJI"},
        "description": "Vàng DOJI - Tập đoàn Vàng bạc Đá quý DOJI",
        "offers": @json($offers)
    }
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg sm:text-2xl font-bold text-[#001061] flex items-center gap-2">
            <i data-lucide="gem" class="h-6 w-6 text-amber-500"></i>
            Bảng giá vàng DOJI hôm nay
        </h2>
        <div class="flex items-center gap-2">
            @if ($card && $card['trendPercent'] != 0)
                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $card['trendPercent'] >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                    <i data-lucide="{{ $card['trendPercent'] >= 0 ? 'trending-up' : 'trending-down' }}" class="h-3 w-3"></i>
                    {{ sprintf('%+.2f%%', $card['trendPercent']) }}
                </span>
            @endif
            <span class="text-xs text-slate-500">{{ $now }}</span>
        </div>
    </div>

    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <caption class="sr-only">Bảng giá vàng DOJI hôm nay {{ now()->format('d/m/Y') }}</caption>
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="p-3 text-left font-semibold text-slate-700">Loại vàng</th>
                    <th scope="col" class="p-3 text-right font-semibold text-slate-700">Mua vào (VNĐ)</th>
                    <th scope="col" class="p-3 text-right font-semibold text-slate-700">Bán ra (VNĐ)</th>
                    <th scope="col" class="p-3 text-right font-semibold text-slate-700">Chênh lệch</th>
                    <th scope="col" class="p-3 text-right font-semibold text-slate-700">Thay đổi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($variants as $v)
                    <tr class="{{ $loop->even ? 'bg-slate-50/50' : '' }}">
                        <td class="p-3 font-medium text-slate-800">{{ $v['label'] }}</td>
                        <td class="p-3 text-right tabular-nums">{{ $v['buy'] > 0 ? number_format($v['buy'] * 1000000, 0, ',', '.') : '—' }}</td>
                        <td class="p-3 text-right tabular-nums font-semibold">{{ $v['sell'] > 0 ? number_format($v['sell'] * 1000000, 0, ',', '.') : '—' }}</td>
                        <td class="p-3 text-right tabular-nums text-slate-600">
                            @if ($v['buy'] > 0 && $v['sell'] > 0)
                                {{ number_format(($v['sell'] - $v['buy']) * 1000000, 0, ',', '.') }}
                            @else — @endif
                        </td>
                        <td class="p-3 text-right font-semibold {{ str_starts_with($v['dayChangeLabel'] ?? '', '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $v['dayChangeLabel'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-center text-slate-500">Đang cập nhật dữ liệu...</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-400">Đơn vị: VNĐ/Lượng · Cập nhật: {{ $now }}</p>
</div>

@include('gold.sections.brand-chart', ['chartBrand' => 'doji', 'chartLabel' => 'DOJI'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-amber-400 pl-3 !mt-0">Giá vàng DOJI hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng DOJI</strong> là mức giá niêm yết chính thức của <strong>Tập đoàn Vàng bạc Đá quý DOJI</strong> – một trong những đơn vị kinh doanh vàng lớn nhất Việt Nam. DOJI niêm yết giá vàng miếng SJC và vàng nhẫn 9999 tại hơn 200 cửa hàng toàn quốc.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Các sản phẩm vàng DOJI</h3>
    <p>DOJI kinh doanh đa dạng sản phẩm: vàng miếng SJC các quy cách, vàng nhẫn DOJI 9999, vàng trang sức 24K, 18K, 14K. Vàng nhẫn DOJI 9999 có chênh lệch mua-bán thấp hơn vàng miếng SJC, phù hợp cho nhà đầu tư nhỏ lẻ.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">So sánh giá vàng DOJI với SJC</h3>
    <p>Giá vàng miếng SJC tại DOJI thường bằng hoặc chênh lệch nhỏ so với giá SJC chính hãng. Tuy nhiên, giá vàng nhẫn DOJI 9999 thường thấp hơn vàng miếng SJC từ 8-12 triệu đồng/lượng. Nhà đầu tư nên so sánh giá giữa các thương hiệu trước khi giao dịch.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Hệ thống cửa hàng DOJI</h3>
    <p>DOJI có mặt tại Hà Nội, TP.HCM, Đà Nẵng và nhiều tỉnh thành trên cả nước. Giá niêm yết có thể khác nhau theo khu vực do chi phí vận chuyển và logistics.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Xu hướng giá vàng DOJI tháng {{ now()->format('m/Y') }}</h3>
    <p>Giá vàng DOJI biến động theo xu hướng chung của thị trường vàng trong nước và thế giới. Nhà đầu tư nên theo dõi biểu đồ giá vàng DOJI phía trên để nhận diện vùng giá mua vào và bán ra hợp lý, kết hợp với tin tức kinh tế vĩ mô để đưa ra quyết định giao dịch.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Chính sách mua bán vàng tại DOJI</h3>
    <p>DOJI áp dụng chính sách mua lại vàng miếng SJC và vàng nhẫn DOJI 9999 với giá cạnh tranh. Khách hàng có thể giao dịch trực tiếp tại cửa hàng hoặc đặt mua online. Phí chênh lệch mua-bán tại DOJI thuộc nhóm thấp nhất thị trường.</p>
</article>

@include('gold.sections.brand-faq', ['brandKey' => 'doji'])

<nav class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h3 class="text-base font-bold text-slate-900 mb-3">Giá vàng thương hiệu khác</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/gia-vang-hom-nay/gia-vang-sjc" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">SJC</a>
        <a href="/gia-vang-hom-nay/gia-vang-pnj" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">PNJ</a>
        <a href="/gia-vang-hom-nay/gia-vang-bao-tin-minh-chau" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">Bảo Tín Minh Châu</a>
        <a href="/gia-vang-hom-nay/gia-vang-phu-quy" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">Phú Quý</a>
        <a href="/gia-vang-hom-nay/gia-vang-mi-hong" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">Mi Hồng</a>
        <a href="/gia-vang-hom-nay/gia-vang-bao-tin-manh-hai" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">Bảo Tín Mạnh Hải</a>
        <a href="/gia-vang-hom-nay/gia-vang-ngoc-tham" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">Ngọc Thẩm</a>
        <a href="/gia-vang-hom-nay" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">← Tất cả thương hiệu</a>
    </div>
</nav>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
