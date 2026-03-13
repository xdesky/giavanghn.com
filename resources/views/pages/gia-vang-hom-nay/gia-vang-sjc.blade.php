@extends('gold.page-shell')

@section('page-label', 'Bảng giá vàng SJC')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $card = $snapshot['sjcCard'] ?? null;
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
    "name": "Giá vàng SJC hôm nay {{ now()->format('d/m/Y') }}",
    "description": "{{ $description }}",
    "dateModified": "{{ now()->toIso8601String() }}",
    "mainEntity": {
        "@@type": "Product",
        "name": "Vàng SJC",
        "brand": {"@@type": "Brand", "name": "SJC"},
        "description": "Vàng miếng SJC - Thương hiệu vàng quốc gia Việt Nam",
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
            Bảng giá vàng SJC hôm nay
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
            <caption class="sr-only">Bảng giá vàng SJC hôm nay {{ now()->format('d/m/Y') }}</caption>
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

@include('gold.sections.brand-chart', ['chartBrand' => 'sjc', 'chartLabel' => 'SJC'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-amber-400 pl-3 !mt-0">Giá vàng SJC hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng SJC</strong> là mức giá niêm yết chính thức của <strong>Công ty Vàng bạc Đá quý Sài Gòn (SJC)</strong> – thương hiệu vàng miếng quốc gia được Ngân hàng Nhà nước Việt Nam chỉ định gia công. Vàng SJC có tính thanh khoản cao nhất thị trường trong nước.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Đặc điểm vàng miếng SJC</h3>
    <p>Vàng miếng SJC có các quy cách phổ biến: 1 lượng, 5 chỉ, 2 chỉ, 1 chỉ và 0.5 chỉ. Mỗi miếng đều có số seri riêng, được niêm phong và kèm phiếu bảo đảm. Chênh lệch giá mua-bán vàng SJC thường dao động từ 500.000 – 2.000.000 VNĐ/lượng tùy thời điểm.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Yếu tố ảnh hưởng giá vàng SJC</h3>
    <p>Giá vàng SJC chịu tác động từ giá vàng thế giới (XAU/USD), tỷ giá USD/VND, chính sách quản lý của Ngân hàng Nhà nước và cung cầu thị trường nội địa. Chênh lệch giữa giá SJC và giá thế giới quy đổi phản ánh chi phí nhập khẩu, thuế và mức cung hạn chế.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Nên mua vàng SJC ở đâu?</h3>
    <p>Nhà đầu tư nên mua vàng SJC tại các hệ thống phân phối ủy quyền: SJC chính hãng, DOJI, PNJ, Bảo Tín Minh Châu, Phú Quý. So sánh giá giữa các thương hiệu trước khi giao dịch để có mức giá tốt nhất.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Xu hướng giá vàng SJC tháng {{ now()->format('m/Y') }}</h3>
    <p>Giá vàng SJC trong tháng {{ now()->format('m/Y') }} chịu ảnh hưởng bởi diễn biến giá vàng quốc tế, chính sách lãi suất của Fed và nhu cầu mua vàng trong nước. Nhà đầu tư nên theo dõi biểu đồ giá vàng SJC phía trên để nắm bắt xu hướng, xác định vùng giá hỗ trợ và kháng cự khi giao dịch.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Quy đổi giá vàng SJC</h3>
    <p>1 lượng vàng = 10 chỉ = 37.5 gram. Khi giao dịch vàng miếng SJC, giá niêm yết theo đơn vị lượng. Đối với vàng nhẫn SJC 99.99, giá cũng được quy đổi về lượng để dễ so sánh với các thương hiệu khác.</p>
</article>

@include('gold.sections.brand-faq', ['brandKey' => 'sjc'])

<nav class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h3 class="text-base font-bold text-slate-900 mb-3">Giá vàng thương hiệu khác</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
        <a href="/gia-vang-hom-nay/gia-vang-doji" class="rounded-sm border border-slate-200 px-3 py-2 text-center font-medium text-blue-700 hover:bg-blue-50">DOJI</a>
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
