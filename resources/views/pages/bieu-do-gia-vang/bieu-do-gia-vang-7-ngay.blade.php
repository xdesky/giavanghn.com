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
        {"@@type": "Question", "name": "Biểu đồ giá vàng 7 ngày cho thấy xu hướng gì?", "acceptedAnswer": {"@@type": "Answer", "text": "Biểu đồ 7 ngày hiển thị xu hướng ngắn hạn của giá vàng 8 thương hiệu trong tuần. Giúp xác nhận xu hướng tăng/giảm, phát hiện breakout hoặc pullback, và so sánh biến động giữa các thương hiệu."}},
        {"@@type": "Question", "name": "Giá vàng tuần qua tăng hay giảm?", "acceptedAnswer": {"@@type": "Answer", "text": "Xem biểu đồ 7 ngày để thấy rõ xu hướng tuần. Nếu đường nghiêng lên = tăng, nghiêng xuống = giảm, đi ngang = sideway. Phần thống kê (Cao nhất, Thấp nhất, Thay đổi%) cho con số chính xác."}},
        {"@@type": "Question", "name": "Nên dùng biểu đồ 7 ngày khi nào?", "acceptedAnswer": {"@@type": "Answer", "text": "Biểu đồ 7 ngày phù hợp cho: tracking biến động tuần, xác nhận tín hiệu mua/bán ngắn hạn, so sánh giá giữa các thương hiệu trong tuần, và phát hiện xu hướng mới hình thành."}},
        {"@@type": "Question", "name": "Chênh lệch giá giữa các thương hiệu trong tuần là bao nhiêu?", "acceptedAnswer": {"@@type": "Answer", "text": "Chênh lệch giá bán giữa SJC (cao nhất) và các thương hiệu khác thường dao động từ 500.000đ đến 3.000.000đ/lượng tùy loại sản phẩm (vàng miếng SJC vs vàng nhẫn 9999)."}}
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.chart', ['period' => '7d', 'periodLabel' => '7 ngày'])

@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $sjcVariant = $sjc ? ($sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first()) : null;
@endphp
<div class="rounded-sm border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="calendar-days" class="h-5 w-5 text-blue-500"></i> Tóm tắt giá vàng 7 ngày gần nhất
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
            <div><span class="font-semibold">Biến động:</span> <span class="font-bold {{ $sjc['trendPercent'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ sprintf('%+.2f%%', $sjc['trendPercent']) }}</span></div>
        </div>
        @endif
        <div class="flex items-start gap-2">
            <i data-lucide="layers" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">So sánh:</span> <span class="text-slate-600">8 thương hiệu VN + XAU thế giới</span></div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="clock" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">Cập nhật:</span> <span class="text-slate-600">{{ now()->format('H:i d/m/Y') }}</span></div>
        </div>
    </div>
</div>

@include('gold.sections.brand-charts', ['brandPeriod' => '7d'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-blue-400 pl-3">Phân tích biểu đồ giá vàng 7 ngày — Xu hướng tuần</h2>

    <p><strong>Biểu đồ giá vàng 7 ngày</strong> cho thấy toàn cảnh biến động giá trong tuần qua. Đây là khung thời gian phù hợp nhất để xác nhận xu hướng ngắn hạn, phát hiện tín hiệu đảo chiều, và so sánh hiệu suất giữa các thương hiệu vàng lớn tại Việt Nam.</p>

    <h3>Ý nghĩa phân tích khung 7 ngày</h3>
    <ul>
        <li><strong>Xác nhận xu hướng:</strong> Nếu giá tăng đều 5-7 ngày liên tục = xu hướng tăng rõ ràng, có thể tiếp tục</li>
        <li><strong>Breakout:</strong> Giá vượt mức cao nhất tuần = tín hiệu mua; phá mức thấp nhất tuần = tín hiệu bán</li>
        <li><strong>So sánh tuần:</strong> Thương hiệu nào tăng nhiều nhất, giảm ít nhất = có sức mạnh nội tại tốt hơn</li>
        <li><strong>Chênh lệch thu hẹp:</strong> Khoảng cách SJC vs thế giới hẹp lại trong tuần = premium đang giảm</li>
    </ul>

    <h3>Các yếu tố ảnh hưởng giá vàng trong tuần</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Yếu tố</th><th class="border border-slate-200 p-2 text-left font-semibold">Tác động</th><th class="border border-slate-200 p-2 text-left font-semibold">Thường công bố</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">Số liệu việc làm Mỹ</td><td class="border border-slate-200 p-2 text-slate-600">Việc làm tăng → USD mạnh → vàng giảm</td><td class="border border-slate-200 p-2">Thứ 6 đầu tháng</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Quyết định Fed</td><td class="border border-slate-200 p-2 text-slate-600">Tăng lãi suất → vàng giảm mạnh</td><td class="border border-slate-200 p-2">Thứ 4 (8 lần/năm)</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">CPI Mỹ</td><td class="border border-slate-200 p-2 text-slate-600">Lạm phát tăng → kỳ vọng giữ lãi suất → vàng biến động</td><td class="border border-slate-200 p-2">Giữa tháng</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Tỷ giá USD/VND</td><td class="border border-slate-200 p-2 text-slate-600">USD tăng → giá vàng nội địa tăng (dù XAU không đổi)</td><td class="border border-slate-200 p-2">Liên tục</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Căng thẳng địa chính trị</td><td class="border border-slate-200 p-2 text-slate-600">Rủi ro tăng → vàng tăng (tài sản trú ẩn)</td><td class="border border-slate-200 p-2">Bất kỳ lúc nào</td></tr>
            </tbody>
        </table>
    </div>
</article>

<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Biểu đồ 7 ngày cho thấy xu hướng gì?', 'a' => 'Xem đường nghiêng tổng thể: lên = tăng, xuống = giảm, ngang = sideway. Phần thống kê cho % thay đổi chính xác trong tuần.'],
            ['q' => 'Nên kết hợp biểu đồ 7 ngày với khung nào?', 'a' => 'Kết hợp 7 ngày + 30 ngày: dùng 30 ngày để xác định xu hướng lớn, rồi dùng 7 ngày để tìm điểm vào cụ thể.'],
            ['q' => 'Chênh lệch SJC vs các thương hiệu khác trong tuần?', 'a' => 'Biểu đồ so sánh trực quan. Khoảng cách giữa đường SJC và các đường khác chính là chênh lệch giá — thường 500.000đ–3.000.000đ/lượng.'],
            ['q' => 'Biểu đồ 7 ngày có giao dịch ngày chủ nhật không?', 'a' => 'Thị trường vàng Việt Nam không giao dịch chủ nhật. Biểu đồ sẽ có khoảng trống (gap) vào cuối tuần, giá mở đầu tuần mới phản ánh biến động thế giới.'],
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
