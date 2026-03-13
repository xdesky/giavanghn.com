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
        {"@@type": "Question", "name": "Biểu đồ giá vàng 30 ngày cho thấy xu hướng gì?", "acceptedAnswer": {"@@type": "Answer", "text": "Biểu đồ 30 ngày cho thấy xu hướng trung hạn của giá vàng. Đây là khung phù hợp nhất để xác định vùng hỗ trợ/kháng cự, phát hiện mẫu hình kỹ thuật (đáy đôi, đỉnh đôi), và đánh giá tác động chính sách tiền tệ."}},
        {"@@type": "Question", "name": "Vùng hỗ trợ và kháng cự trên biểu đồ 30 ngày là gì?", "acceptedAnswer": {"@@type": "Answer", "text": "Hỗ trợ là mức giá mà cầu mua đủ mạnh ngăn giá giảm thêm. Kháng cự là mức giá mà áp lực bán ngăn giá tăng tiếp. Trên biểu đồ 30 ngày, các mức này rõ ràng hơn do ít nhiễu hơn biểu đồ ngắn hạn."}},
        {"@@type": "Question", "name": "Giá vàng tháng qua tăng bao nhiêu phần trăm?", "acceptedAnswer": {"@@type": "Answer", "text": "Xem phần thống kê (Thay đổi%) trên biểu đồ để biết mức tăng/giảm chính xác trong 30 ngày. Biểu đồ từng thương hiệu bên dưới cho thấy hiệu suất riêng của SJC, DOJI, PNJ, BTMC, Phú Quý, Mi Hồng, Bảo Tín MH, Ngọc Thẩm."}},
        {"@@type": "Question", "name": "Nên kết hợp biểu đồ 30 ngày với khung nào?", "acceptedAnswer": {"@@type": "Answer", "text": "Kết hợp 30 ngày (xu hướng tháng) + 7 ngày (xác nhận ngắn hạn) + hôm nay (tìm điểm vào). Xem thêm biểu đồ 1 năm để đặt trong bối cảnh dài hạn hơn."}}
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.chart', ['period' => '30d', 'periodLabel' => '30 ngày'])

@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $sjcVariant = $sjc ? ($sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first()) : null;
@endphp
<div class="rounded-sm border border-violet-200 bg-gradient-to-r from-violet-50 to-purple-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="calendar-range" class="h-5 w-5 text-violet-500"></i> Tóm tắt giá vàng 30 ngày gần nhất
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
            <div><span class="font-semibold">So sánh:</span> <span class="text-slate-600">8 thương hiệu + XAU quy đổi</span></div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="clock" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div><span class="font-semibold">Giai đoạn:</span> <span class="text-slate-600">{{ now()->subDays(30)->format('d/m') }} – {{ now()->format('d/m/Y') }}</span></div>
        </div>
    </div>
</div>

@include('gold.sections.brand-charts', ['brandPeriod' => '30d'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-violet-400 pl-3">Phân tích biểu đồ giá vàng 30 ngày — Xu hướng tháng</h2>

    <p><strong>Biểu đồ giá vàng 30 ngày</strong> là khung phân tích trung hạn phổ biến nhất. Với 30 ngày dữ liệu, biểu đồ lọc bỏ phần lớn nhiễu ngắn hạn, cho thấy rõ xu hướng thực sự cùng các vùng hỗ trợ và kháng cự quan trọng.</p>

    <h3>Cách phân tích biểu đồ 30 ngày</h3>
    <ul>
        <li><strong>Vùng hỗ trợ:</strong> Mức giá mà biểu đồ "nảy" lên ít nhất 2 lần trong tháng — mức giá mua tiềm năng</li>
        <li><strong>Vùng kháng cự:</strong> Mức giá mà biểu đồ "quay đầu" — mức giá bán hoặc chờ breakout</li>
        <li><strong>Sideway:</strong> Giá dao động trong biên độ hẹp suốt tháng = thị trường tích lũy, chờ xúc tác</li>
        <li><strong>Trendline:</strong> Nối các đáy (xu hướng tăng) hoặc đỉnh (xu hướng giảm) để vẽ đường xu hướng</li>
    </ul>

    <h3>Các mẫu hình thường gặp trên biểu đồ tháng</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Mẫu hình</th><th class="border border-slate-200 p-2 text-left font-semibold">Hình dạng</th><th class="border border-slate-200 p-2 text-left font-semibold">Ý nghĩa</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">Đáy đôi (W)</td><td class="border border-slate-200 p-2 text-slate-600">Giá chạm đáy → lên → chạm đáy lần 2 → tăng mạnh</td><td class="border border-slate-200 p-2 text-emerald-700 font-medium">Tín hiệu tăng mạnh</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Đỉnh đôi (M)</td><td class="border border-slate-200 p-2 text-slate-600">Giá chạm đỉnh → giảm → chạm đỉnh lần 2 → giảm mạnh</td><td class="border border-slate-200 p-2 text-rose-700 font-medium">Tín hiệu giảm mạnh</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Tam giác hội tụ</td><td class="border border-slate-200 p-2 text-slate-600">Biên độ hẹp dần, đỉnh thấp hơn + đáy cao hơn</td><td class="border border-slate-200 p-2 text-amber-700 font-medium">Chờ breakout lớn</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Kênh tăng</td><td class="border border-slate-200 p-2 text-slate-600">Đỉnh cao hơn + đáy cao hơn liên tục</td><td class="border border-slate-200 p-2 text-emerald-700 font-medium">Xu hướng tăng ổn định</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Kênh giảm</td><td class="border border-slate-200 p-2 text-slate-600">Đỉnh thấp hơn + đáy thấp hơn liên tục</td><td class="border border-slate-200 p-2 text-rose-700 font-medium">Xu hướng giảm ổn định</td></tr>
            </tbody>
        </table>
    </div>

    <h3>So sánh hiệu suất thương hiệu 30 ngày</h3>
    <p>Biểu đồ từng thương hiệu bên trên cho phép dễ dàng so sánh: thương hiệu nào có mức tăng tốt nhất, chênh lệch giá nào hẹp nhất (có lợi cho người mua). Thông thường <a href="/gia-vang-hom-nay/gia-vang-sjc" class="text-blue-700 hover:underline">vàng SJC</a> có premium cao nhất, trong khi <a href="/gia-vang-hom-nay/gia-vang-pnj" class="text-blue-700 hover:underline">PNJ</a> và <a href="/gia-vang-hom-nay/gia-vang-doji" class="text-blue-700 hover:underline">DOJI</a> có giá cạnh tranh hơn.</p>
</article>

<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Biểu đồ 30 ngày có giao dịch cuối tuần không?', 'a' => 'Thị trường nội địa nghỉ thứ 7 chiều + chủ nhật. Biểu đồ có khoảng trống cuối tuần. Giá mở đầu tuần phản ánh biến động thế giới trong cuối tuần.'],
            ['q' => 'Vùng hỗ trợ/kháng cự 30 ngày có đáng tin không?', 'a' => 'Khung 30 ngày cho tín hiệu đáng tin hơn 7 ngày do ít nhiễu hơn. Tuy nhiên nên kết hợp với khối lượng giao dịch và tin tức vĩ mô để xác nhận.'],
            ['q' => 'Giá vàng biến động bao nhiêu phần trăm trong tháng?', 'a' => 'Xem phần thống kê (Thay đổi%) ở biểu đồ tổng hợp bên trên. Biến động trung bình tháng thường từ 1-5%, nhưng có thể lên 8-10% nếu có sự kiện lớn (Fed, địa chính trị).'],
            ['q' => 'Nên xem biểu đồ 30 ngày hay 1 năm?', 'a' => 'Cả hai. Dùng 1 năm để nắm xu hướng lớn (tăng, giảm hay sideway), rồi dùng 30 ngày để xác định vùng giá cụ thể để mua/bán.'],
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
