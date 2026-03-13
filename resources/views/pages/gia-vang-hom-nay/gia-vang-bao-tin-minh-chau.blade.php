@extends('gold.page-shell')

@section('page-label', 'Bảng giá vàng Bảo Tín Minh Châu')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $card = $snapshot['btmcCard'] ?? null;
    $variants = $card['variants'] ?? [];
    $offers = [];
    foreach ($variants as $v) {
        if (($v['sell'] ?? 0) <= 0) continue;
        $offers[] = ['@@type' => 'Offer', 'name' => $v['label'], 'price' => round($v['sell'] * 1000000), 'priceCurrency' => 'VND', 'priceValidUntil' => now()->endOfDay()->toIso8601String()];
    }
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Giá vàng Bảo Tín Minh Châu hôm nay {{ now()->format('d/m/Y') }}",
    "description": "{{ $description }}",
    "dateModified": "{{ now()->toIso8601String() }}",
    "mainEntity": {
        "@@type": "Product",
        "name": "Vàng Bảo Tín Minh Châu",
        "brand": {"@@type": "Brand", "name": "Bảo Tín Minh Châu"},
        "description": "Vàng Bảo Tín Minh Châu - Công ty Vàng bạc Đá quý Bảo Tín Minh Châu",
        "offers": @json($offers)
    }
}
</script>
@endpush

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-[#001061] flex items-center gap-2">
            <i data-lucide="gem" class="h-6 w-6 text-amber-500"></i>
            Bảng giá vàng Bảo Tín Minh Châu hôm nay
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
            <caption class="sr-only">Bảng giá vàng Bảo Tín Minh Châu hôm nay {{ now()->format('d/m/Y') }}</caption>
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

@include('gold.sections.brand-chart', ['chartBrand' => 'btmc', 'chartLabel' => 'Bảo Tín Minh Châu'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-amber-400 pl-3 !mt-0">Giá vàng Bảo Tín Minh Châu hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng Bảo Tín Minh Châu</strong> là giá niêm yết chính thức của <strong>Công ty Vàng bạc Đá quý Bảo Tín Minh Châu</strong> – thương hiệu vàng bạc hàng đầu miền Bắc. Bảo Tín Minh Châu nổi tiếng với sản phẩm vàng nhẫn Rồng Thăng Long và vàng Phượng Hoàng.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Vàng nhẫn Bảo Tín Minh Châu</h3>
    <p>Vàng nhẫn Rồng Thăng Long 9999 là sản phẩm chủ lực của Bảo Tín Minh Châu, được Ngân hàng Nhà nước cấp phép sản xuất. Sản phẩm có trọng lượng từ 1 chỉ đến 5 chỉ, khắc logo và số seri, đảm bảo chất lượng 99.99% vàng nguyên chất.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Hệ thống cửa hàng Bảo Tín Minh Châu</h3>
    <p>Bảo Tín Minh Châu có hệ thống cửa hàng tập trung tại Hà Nội và các tỉnh miền Bắc. Địa chỉ giao dịch chính tại phố Trần Nhân Tông, quận Hai Bà Trưng, Hà Nội. Giá niêm yết cạnh tranh, chính sách mua lại rõ ràng.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">So sánh giá vàng Bảo Tín Minh Châu</h3>
    <p>Giá vàng nhẫn Rồng Thăng Long thường ngang bằng hoặc thấp hơn vàng nhẫn DOJI, PNJ. Giá vàng miếng SJC tại Bảo Tín Minh Châu thường sát giá thị trường. Nhà đầu tư nên so sánh giá giữa các thương hiệu trước khi giao dịch để đạt mức giá tốt nhất.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Xu hướng giá vàng Bảo Tín Minh Châu tháng {{ now()->format('m/Y') }}</h3>
    <p>Trong tháng {{ now()->format('m/Y') }}, giá vàng Bảo Tín Minh Châu diễn biến theo xu hướng chung của thị trường. Biểu đồ giá vàng Bảo Tín Minh Châu phía trên giúp nhà đầu tư nhận diện các mức giá hỗ trợ và kháng cự, từ đó đưa ra chiến lược giao dịch phù hợp.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Vàng Rồng Thăng Long – Sản phẩm đầu tư ưa chuộng</h3>
    <p>Vàng Rồng Thăng Long là sản phẩm vàng nhẫn được Ngân hàng Nhà nước cấp phép, có giá trị đầu tư cao. Sản phẩm phù hợp cho nhà đầu tư muốn tích lũy vàng dài hạn với chi phí thấp hơn vàng miếng SJC.</p>
</article>

@include('gold.sections.brand-faq', ['brandKey' => 'btmc'])

<nav class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h3 class="text-base font-bold text-slate-900 mb-3">Giá vàng thương hiệu khác</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/gia-vang-hom-nay/gia-vang-sjc" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">SJC</a>
        <a href="/gia-vang-hom-nay/gia-vang-doji" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">DOJI</a>
        <a href="/gia-vang-hom-nay/gia-vang-pnj" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">PNJ</a>
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
