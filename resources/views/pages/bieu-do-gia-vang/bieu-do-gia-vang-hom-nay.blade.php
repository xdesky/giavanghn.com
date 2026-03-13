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
        {"@@type": "Question", "name": "Biểu đồ giá vàng hôm nay cập nhật bao lâu một lần?", "acceptedAnswer": {"@@type": "Answer", "text": "Biểu đồ giá vàng hôm nay cập nhật mỗi 15 phút trong giờ giao dịch (8h-17h30). Dữ liệu hiển thị theo phút, giúp theo dõi biến động giá gần thời gian thực."}},
        {"@@type": "Question", "name": "Nên mua vàng lúc nào trong ngày?", "acceptedAnswer": {"@@type": "Answer", "text": "Giá vàng biến động mạnh nhất đầu phiên sáng (8h-9h30) và cuối phiên chiều (15h-17h). Biểu đồ hôm nay giúp xác định thời điểm giá thấp nhất trong ngày để tối ưu giá mua."}},
        {"@@type": "Question", "name": "Tại sao giá vàng các thương hiệu khác nhau trong ngày?", "acceptedAnswer": {"@@type": "Answer", "text": "Mỗi thương hiệu cập nhật giá theo tần suất khác nhau và có chính sách giá riêng. SJC thường cập nhật 2-3 lần/ngày, DOJI và PNJ thường xuyên hơn. Chênh lệch thường từ 50.000-200.000đ/lượng."}},
        {"@@type": "Question", "name": "Đường nét đứt trên biểu đồ là gì?", "acceptedAnswer": {"@@type": "Answer", "text": "Đường vàng nét đứt là giá vàng thế giới XAU/USD quy đổi sang VNĐ/lượng. Khoảng cách giữa đường này và SJC chính là premium — chênh lệch giá vàng nội địa so với quốc tế."}}
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.chart', ['period' => 'today', 'periodLabel' => 'hôm nay'])

@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $us  = $snapshot['usCard'] ?? null;
    $sjcVariant = $sjc ? ($sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first()) : null;
@endphp
<div class="rounded-sm border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="zap" class="h-5 w-5 text-emerald-500"></i> Tóm tắt giá vàng hôm nay {{ now()->format('d/m/Y') }}
    </h2>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
        @if ($sjcVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-amber-500 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">SJC bán ra:</span> <span class="font-bold text-amber-900">{{ number_format($sjcVariant['sell'] * 1000000, 0, ',', '.') }} đ/lượng</span></div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-blue-500 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">SJC mua vào:</span> <span class="font-bold text-blue-900">{{ number_format($sjcVariant['buy'] * 1000000, 0, ',', '.') }} đ/lượng</span></div>
        </div>
        @endif
        @if ($sjc)
        <div class="flex items-start gap-2">
            <i data-lucide="trending-up" class="h-4 w-4 {{ $sjc['trendPercent'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">Biến động:</span> <span class="font-bold {{ $sjc['trendPercent'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ sprintf('%+.2f%%', $sjc['trendPercent']) }}</span></div>
        </div>
        @endif
        <div class="flex items-start gap-2">
            <i data-lucide="clock" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">Cập nhật:</span> <span class="text-slate-600">{{ now()->format('H:i d/m/Y') }}</span></div>
        </div>
    </div>
</div>

@include('gold.sections.brand-charts', ['brandPeriod' => 'today'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-emerald-400 pl-3">Phân tích biến động giá vàng trong ngày {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Biểu đồ giá vàng hôm nay</strong> hiển thị diễn biến giá theo phút, giúp nhà đầu tư nắm bắt biến động giá vàng trong phiên giao dịch hiện tại. Dữ liệu bao gồm 8 thương hiệu lớn nhất Việt Nam và giá vàng thế giới XAU/USD quy đổi.</p>

    <h3>Các phiên giao dịch vàng trong ngày</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Phiên</th><th class="border border-slate-200 p-2 text-left font-semibold">Thời gian</th><th class="border border-slate-200 p-2 text-left font-semibold">Đặc điểm</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">Mở cửa sáng</td><td class="border border-slate-200 p-2">08:00 – 09:30</td><td class="border border-slate-200 p-2 text-slate-600">Biến động mạnh theo phiên đêm thế giới, gap mở cửa</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Giữa phiên</td><td class="border border-slate-200 p-2">09:30 – 14:00</td><td class="border border-slate-200 p-2 text-slate-600">Ổn định, giao dịch nhẹ, giá ít biến động</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Chiều / Châu Âu mở</td><td class="border border-slate-200 p-2">14:00 – 17:30</td><td class="border border-slate-200 p-2 text-slate-600">Tăng thanh khoản theo thị trường châu Âu</td></tr>
            </tbody>
        </table>
    </div>

    <h3>Mẹo đọc biểu đồ trong ngày</h3>
    <ul>
        <li><strong>Gap mở cửa:</strong> Giá mở cửa so với đóng cửa hôm qua — gap lớn = biến động mạnh từ thế giới</li>
        <li><strong>Đường phẳng:</strong> Đoạn ngang = thương hiệu chưa cập nhật giá mới</li>
        <li><strong>So sánh:</strong> Thương hiệu nào có giá bán thấp nhất = mua lợi nhất thời điểm đó</li>
        <li><strong>Đường nét đứt XAU:</strong> Giá thế giới quy đổi — khoảng cách với SJC chính là premium nội địa</li>
    </ul>
</article>

<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2">
        <i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp
    </h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Biểu đồ giá vàng hôm nay cập nhật bao lâu một lần?', 'a' => 'Dữ liệu cập nhật mỗi 15 phút trong giờ giao dịch (8h-17h30 ngày thường). Biểu đồ hiển thị từng phút cho phép tracking gần thời gian thực.'],
            ['q' => 'Nên mua vàng lúc nào trong ngày?', 'a' => 'Giá thường biến động mạnh nhất đầu phiên sáng (8h-9h30). Nếu giá pullback sau mở cửa, có thể là cơ hội tốt. Tránh mua ngay khi giá gap lên.'],
            ['q' => 'Tại sao đường biểu đồ có đoạn ngang?', 'a' => 'Đoạn ngang nghĩa là thương hiệu chưa cập nhật giá mới. SJC cập nhật 2-3 lần/ngày, DOJI thường xuyên hơn.'],
            ['q' => 'Giá vàng ban đêm có cập nhật không?', 'a' => 'Biểu đồ hôm nay hiển thị giờ giao dịch trong nước (8h-17h30). Giá thế giới biến động 24/7 nhưng các thương hiệu VN chỉ cập nhật trong giờ hành chính.'],
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
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-7-ngay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> 7 ngày</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-30-ngay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> 30 ngày</a>
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-1-nam" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> 1 năm</a>
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
