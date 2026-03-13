@extends('gold.page-shell')

@section('page-label', 'Biểu đồ')

@push('head')
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
            {"@@type": "ListItem", "position": 2, "name": "Biểu đồ giá vàng", "item": "{{ url('/bieu-do-gia-vang') }}"},
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
        {"@@type": "Question", "name": "Giá vàng 1 năm qua tăng bao nhiêu phần trăm?", "acceptedAnswer": {"@@type": "Answer", "text": "Xem phần thống kê (Thay đổi%) trên biểu đồ tổng hợp để biết mức tăng/giảm chính xác trong 365 ngày. Biểu đồ từng thương hiệu cho thấy hiệu suất riêng của SJC, DOJI, PNJ, BTMC, Phú Quý, Mi Hồng, Bảo Tín MH, Ngọc Thẩm."}},
        {"@@type": "Question", "name": "Biểu đồ 1 năm phù hợp với ai?", "acceptedAnswer": {"@@type": "Answer", "text": "Biểu đồ 1 năm phù hợp nhà đầu tư trung hạn, người muốn đánh giá chu kỳ tăng/giảm, so sánh hiệu suất thương hiệu, và xem mối tương quan giữa giá vàng Việt Nam và thế giới qua các mùa."}},
        {"@@type": "Question", "name": "Chu kỳ giá vàng năm thường như thế nào?", "acceptedAnswer": {"@@type": "Answer", "text": "Giá vàng thường có quy luật mùa: tăng mạnh cuối năm và quý 1 (nhu cầu mua sắm, Tết Nguyên đán), điều chỉnh giữa năm, và tăng trở lại từ tháng 8-9 (mùa cưới, tích trữ)."}},
        {"@@type": "Question", "name": "So sánh giá vàng SJC vs thế giới năm qua?", "acceptedAnswer": {"@@type": "Answer", "text": "Đường SJC (nét liền) và XAU quy đổi (nét đứt) trên biểu đồ cho thấy premium nội địa. Khoảng cách này thay đổi theo thời gian — thu hẹp khi NHNN bán vàng can thiệp, mở rộng khi nguồn cung hạn chế."}}
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.chart', ['period' => '1y', 'periodLabel' => '1 năm'])

@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $sjcVariant = $sjc ? ($sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first()) : null;
@endphp
<div class="rounded-sm border border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="trophy" class="h-5 w-5 text-amber-500"></i> Tóm tắt giá vàng 1 năm ({{ now()->subYear()->format('m/Y') }} – {{ now()->format('m/Y') }})
    </h2>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
        @if ($sjcVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-amber-500 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">SJC hiện tại:</span> <span class="font-bold text-amber-900">{{ number_format($sjcVariant['sell'] * 1000000, 0, ',', '.') }} đ/lượng</span></div>
        </div>
        @endif
        @if ($sjc)
        <div class="flex items-start gap-2">
            <i data-lucide="trending-up" class="h-4 w-4 {{ $sjc['trendPercent'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">Biến động hôm nay:</span> <span class="font-bold {{ $sjc['trendPercent'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ sprintf('%+.2f%%', $sjc['trendPercent']) }}</span></div>
        </div>
        @endif
        <div class="flex items-start gap-2">
            <i data-lucide="layers" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">So sánh:</span> <span class="text-slate-600">8 thương hiệu + XAU quy đổi</span></div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="calendar" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">Giai đoạn:</span> <span class="text-slate-600">{{ now()->subYear()->format('d/m/Y') }} – {{ now()->format('d/m/Y') }}</span></div>
        </div>
    </div>
</div>

@include('gold.sections.brand-charts', ['brandPeriod' => '1y'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-amber-400 pl-3">Phân tích biểu đồ giá vàng 1 năm — Chu kỳ & Xu hướng</h2>

    <p><strong>Biểu đồ giá vàng 1 năm</strong> là công cụ phân tích trung-dài hạn quan trọng nhất cho nhà đầu tư. Với 365 ngày dữ liệu, biểu đồ cho thấy rõ ràng các chu kỳ tăng/giảm, mối tương quan giữa giá vàng nội địa và quốc tế, cùng tác động của chính sách tiền tệ và sự kiện vĩ mô.</p>

    <h3>Quy luật mùa vụ giá vàng Việt Nam</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Giai đoạn</th><th class="border border-slate-200 p-2 text-left font-semibold">Tháng</th><th class="border border-slate-200 p-2 text-left font-semibold">Xu hướng</th><th class="border border-slate-200 p-2 text-left font-semibold">Giải thích</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">Quý 1</td><td class="border border-slate-200 p-2">T1-T3</td><td class="border border-slate-200 p-2"><span class="text-emerald-600 font-semibold">Tăng mạnh</span></td><td class="border border-slate-200 p-2 text-slate-600">Tết Nguyên đán, nhu cầu lì xì/tích trữ, ngày vía Thần Tài (10 tháng Giêng)</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Quý 2</td><td class="border border-slate-200 p-2">T4-T6</td><td class="border border-slate-200 p-2"><span class="text-amber-600 font-semibold">Điều chỉnh</span></td><td class="border border-slate-200 p-2 text-slate-600">Nhu cầu giảm sau Tết, chốt lời, thị trường trầm lắng</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Quý 3</td><td class="border border-slate-200 p-2">T7-T9</td><td class="border border-slate-200 p-2"><span class="text-emerald-600 font-semibold">Phục hồi</span></td><td class="border border-slate-200 p-2 text-slate-600">Mùa cưới, nhu cầu trang sức, tích trữ cuối năm bắt đầu</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Quý 4</td><td class="border border-slate-200 p-2">T10-T12</td><td class="border border-slate-200 p-2"><span class="text-emerald-600 font-semibold">Tăng trở lại</span></td><td class="border border-slate-200 p-2 text-slate-600">Mua sắm cuối năm, các NHTW mua vàng dự trữ, kỳ vọng năm mới</td></tr>
            </tbody>
        </table>
    </div>

    <h3>So sánh hiệu suất thương hiệu 1 năm</h3>
    <ul>
        <li><strong>Vàng miếng SJC:</strong> Premium cao nhất, biến động theo chính sách NHNN, giá thường tăng nhanh hơn lúc thị trường bullish</li>
        <li><strong>DOJI, PNJ:</strong> Giá vàng nhẫn 9999 thường bám sát giá thế giới hơn, premium thấp hơn SJC</li>
        <li><strong>XAU quy đổi:</strong> Đường nét đứt cho thấy "giá trị thực" quốc tế — khoảng cách với SJC chính là premium nội địa</li>
        <li><strong>Mối tương quan:</strong> Khi tất cả đường cùng hướng = xu hướng mạnh, phân kỳ = bất thường cung cầu</li>
    </ul>

    <h3>Sự kiện ảnh hưởng giá vàng trong năm</h3>
    <p>Trong 1 năm qua, giá vàng chịu tác động bởi: quyết định lãi suất của Fed (8 lần/năm), dữ liệu CPI Mỹ, căng thẳng địa chính trị, chính sách bán vàng can thiệp của NHNN Việt Nam, và biến động tỷ giá USD/VND. Xem chi tiết tại <a href="/gia-vang-the-gioi" class="text-blue-700 hover:underline">giá vàng thế giới</a>.</p>
</article>

<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Giá vàng 1 năm qua tăng bao nhiêu?', 'a' => 'Xem phần thống kê (Thay đổi%) trên biểu đồ. Biểu đồ từng thương hiệu cho thấy hiệu suất cụ thể của mỗi hãng.'],
            ['q' => 'Chu kỳ giá vàng năm thường như thế nào?', 'a' => 'Tăng mạnh quý 1 (Tết, Thần Tài), điều chỉnh quý 2, phục hồi quý 3 (mùa cưới), tăng cuối năm (tích trữ). Tuy nhiên sự kiện vĩ mô có thể phá vỡ quy luật.'],
            ['q' => 'Nên mua vàng tháng nào trong năm?', 'a' => 'Lịch sử cho thấy giá thường thấp nhất quý 2 (T4-T6). Nhưng đừng quá phụ thuộc quy luật — kết hợp phân tích kỹ thuật (biểu đồ) và cơ bản (tin tức vĩ mô).'],
            ['q' => 'Biểu đồ 1 năm vs 10 năm, nên xem cái nào?', 'a' => '1 năm cho xu hướng trung hạn và đánh giá hiệu suất gần. 10 năm cho góc nhìn dài hạn, super cycle, và quyết định tích trữ tài sản. Nên xem cả hai.'],
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
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2"><i data-lucide="link" class="h-5 w-5"></i> Xem thêm biểu đồ</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-hom-nay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> Hôm nay</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-7-ngay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> 7 ngày</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-30-ngay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> 30 ngày</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-10-nam" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> 10 năm</a>
    </div>
    <div class="grid grid-cols-2 gap-2 text-sm mt-2">
        <a href="/gia-vang-hom-nay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="tag" class="h-3.5 w-3.5 text-slate-400"></i> Giá vàng trong nước</a>
        <a href="/gia-vang-the-gioi" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="globe" class="h-3.5 w-3.5 text-slate-400"></i> Giá vàng thế giới</a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
