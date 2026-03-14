@extends('gold.page-shell')

@section('page-label', 'Bảng giá vàng Mi Hồng')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $card = $snapshot['mihongCard'] ?? null;
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
    "name": "Giá vàng Mi Hồng hôm nay {{ now()->format('d/m/Y') }}",
    "description": "{{ $description }}",
    "dateModified": "{{ now()->toIso8601String() }}",
    "mainEntity": {
        "@@type": "Product",
        "name": "Vàng Mi Hồng",
        "brand": {"@@type": "Brand", "name": "Mi Hồng"},
        "description": "Vàng Mi Hồng - Tiệm vàng Mi Hồng TP.HCM",
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
            Bảng giá vàng Mi Hồng hôm nay
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
            <caption class="sr-only">Bảng giá vàng Mi Hồng hôm nay {{ now()->format('d/m/Y') }}</caption>
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

@include('gold.sections.brand-chart', ['chartBrand' => 'mihong', 'chartLabel' => 'Mi Hồng'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-amber-400 pl-3 !mt-0">Giá vàng Mi Hồng hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng Mi Hồng</strong> là giá niêm yết tại <strong>Tiệm vàng Mi Hồng</strong>, một trong những tiệm vàng lớn và uy tín nhất tại TP.HCM. Mi Hồng nổi tiếng với giá vàng cạnh tranh, đặc biệt vàng nhẫn 9999 và vàng miếng SJC.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Tiệm vàng Mi Hồng – Thương hiệu lâu đời tại TP.HCM</h3>
    <p>Mi Hồng là tiệm vàng truyền thống có lịch sử hoạt động lâu năm tại khu vực Chợ Lớn, Quận 5, TP.HCM. Tiệm vàng Mi Hồng được biết đến với lượng giao dịch vàng lớn mỗi ngày, giá niêm yết sát thị trường.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Sản phẩm vàng tại Mi Hồng</h3>
    <p>Mi Hồng kinh doanh vàng miếng SJC các loại, vàng nhẫn tròn trơn 9999, vàng nữ trang. Đặc biệt, giá vàng nhẫn tại Mi Hồng thường thuộc nhóm rẻ nhất thị trường, thu hút nhiều nhà đầu tư nhỏ lẻ.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Lưu ý khi mua vàng tại Mi Hồng</h3>
    <p>Giá vàng tại Mi Hồng thường chênh lệch mua-bán thấp, giúp nhà đầu tư tiết kiệm chi phí. Tuy nhiên, do lượng khách đông, thời gian giao dịch cao điểm có thể phải xếp hàng. Nên theo dõi giá trước khi đến để chủ động.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Xu hướng giá vàng Mi Hồng tháng {{ now()->format('m/Y') }}</h3>
    <p>Giá vàng Mi Hồng trong tháng {{ now()->format('m/Y') }} phản ánh xu hướng chung của thị trường vàng phía Nam. Nhà đầu tư có thể tham khảo biểu đồ giá vàng Mi Hồng phía trên để phân tích xu hướng ngắn hạn và tìm cơ hội giao dịch.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">So sánh giá vàng Mi Hồng với thương hiệu khác</h3>
    <p>Giá vàng Mi Hồng thường cạnh tranh so với SJC, DOJI, PNJ tại khu vực TP.HCM. Vàng nhẫn 9999 tại Mi Hồng có giá thuộc nhóm thấp nhất, phù hợp cho nhà đầu tư muốn mua vàng tích trữ với chi phí tối ưu.</p>
</article>

@include('gold.sections.brand-faq', ['brandKey' => 'mihong'])

<nav class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h3 class="text-base font-bold text-slate-900 mb-3">Giá vàng thương hiệu khác</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/gia-vang-hom-nay/gia-vang-sjc" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">SJC</a>
        <a href="/gia-vang-hom-nay/gia-vang-doji" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">DOJI</a>
        <a href="/gia-vang-hom-nay/gia-vang-pnj" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">PNJ</a>
        <a href="/gia-vang-hom-nay/gia-vang-bao-tin-minh-chau" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">Bảo Tín Minh Châu</a>
        <a href="/gia-vang-hom-nay/gia-vang-phu-quy" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">Phú Quý</a>
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
