@extends('gold.page-shell')

@section('page-label', 'Bảng giá vàng PNJ')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $card = $snapshot['pnjCard'] ?? null;
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
    "name": "Giá vàng PNJ hôm nay {{ now()->format('d/m/Y') }}",
    "description": "{{ $description }}",
    "dateModified": "{{ now()->toIso8601String() }}",
    "mainEntity": {
        "@@type": "Product",
        "name": "Vàng PNJ",
        "brand": {"@@type": "Brand", "name": "PNJ"},
        "description": "Vàng PNJ - Công ty Vàng bạc Đá quý Phú Nhuận",
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
            Bảng giá vàng PNJ hôm nay
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
            <caption class="sr-only">Bảng giá vàng PNJ hôm nay {{ now()->format('d/m/Y') }}</caption>
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

@include('gold.sections.brand-chart', ['chartBrand' => 'pnj', 'chartLabel' => 'PNJ'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-amber-400 pl-3 !mt-0">Giá vàng PNJ hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng PNJ</strong> là giá niêm yết chính thức của <strong>Công ty Cổ phần Vàng bạc Đá quý Phú Nhuận (PNJ)</strong> – doanh nghiệp vàng bạc trang sức lớn nhất Việt Nam với hơn 400 cửa hàng toàn quốc. PNJ được niêm yết trên sàn HOSE, minh bạch về giá cả và chất lượng.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Sản phẩm vàng nổi bật tại PNJ</h3>
    <p>PNJ kinh doanh vàng miếng SJC, vàng nhẫn PNJ 9999, vàng trang sức 24K, 18K, 14K, 10K. Vàng nhẫn PNJ 9999 có mẫu mã đa dạng, trọng lượng từ 1 chỉ đến 5 chỉ, phù hợp tích trữ và làm quà tặng.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Ưu thế khi mua vàng tại PNJ</h3>
    <p>PNJ có hệ thống phân phối rộng nhất cả nước với hơn 400 cửa hàng. Chính sách mua lại rõ ràng, giá mua lại cạnh tranh. PNJ thường xuyên có chương trình khuyến mãi, giảm công chế tác, đặc biệt các dịp lễ Tết và ngày vía Thần Tài.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">So sánh giá vàng PNJ với thương hiệu khác</h3>
    <p>Giá vàng miếng SJC tại PNJ thường ngang bằng với SJC và DOJI. Giá vàng nhẫn PNJ 9999 cạnh tranh so với DOJI và Bảo Tín Minh Châu. Nhà đầu tư nên theo dõi giá nhiều nguồn để chọn mức giá tốt nhất.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Xu hướng giá vàng PNJ tháng {{ now()->format('m/Y') }}</h3>
    <p>Giá vàng PNJ biến động theo xu hướng chung của thị trường vàng. Nhà đầu tư có thể tham khảo biểu đồ giá vàng PNJ phía trên để xác định thời điểm mua vào hoặc bán ra phù hợp. Những ngày giá giảm mạnh so với trung bình tháng là cơ hội tốt để tích lũy.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Vàng nhẫn PNJ 9999 – Lựa chọn đầu tư thông minh</h3>
    <p>Vàng nhẫn PNJ 9999 đang ngày càng được ưa chuộng nhờ giá thấp hơn vàng miếng SJC, chênh lệch mua-bán hẹp, dễ mua dễ bán tại hơn 400 điểm giao dịch PNJ trên toàn quốc.</p>
</article>

@include('gold.sections.brand-faq', ['brandKey' => 'pnj'])

<nav class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h3 class="text-base font-bold text-slate-900 mb-3">Giá vàng thương hiệu khác</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/gia-vang-hom-nay/gia-vang-sjc" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">SJC</a>
        <a href="/gia-vang-hom-nay/gia-vang-doji" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">DOJI</a>
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
