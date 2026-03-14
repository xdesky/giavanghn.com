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
        {"@@type": "Question", "name": "Giá vàng 10 năm qua tăng bao nhiêu lần?", "acceptedAnswer": {"@@type": "Answer", "text": "Biểu đồ 10 năm cho thấy giá vàng SJC từ khoảng 36-37 triệu (2016) lên mức hiện tại. Mức tăng trưởng cụ thể được hiển thị trong phần thống kê (Thay đổi%) trên biểu đồ tổng hợp."}},
        {"@@type": "Question", "name": "Biểu đồ 10 năm phù hợp với nhà đầu tư nào?", "acceptedAnswer": {"@@type": "Answer", "text": "Biểu đồ 10 năm phù hợp với người tích trữ vàng dài hạn, nhà đầu tư muốn đánh giá vàng như kênh tài sản, và người cần góc nhìn toàn cảnh về super cycle (siêu chu kỳ) giá vàng."}},
        {"@@type": "Question", "name": "Những sự kiện nào ảnh hưởng giá vàng 10 năm qua?", "acceptedAnswer": {"@@type": "Answer", "text": "Các sự kiện lớn: đại dịch COVID-19 (2020-2021) đẩy vàng lên đỉnh lịch sử, lạm phát toàn cầu (2022-2023), chiến tranh Nga-Ukraine (2022), khủng hoảng ngân hàng Mỹ (2023), và chính sách mua vàng dự trữ của NHTW."}},
        {"@@type": "Question", "name": "Vàng SJC được bao nhiêu tiền nếu mua từ 10 năm trước?", "acceptedAnswer": {"@@type": "Answer", "text": "Giá SJC đầu 2016 khoảng 33-36 triệu/lượng. Nếu mua 10 năm trước, lợi nhuận phụ thuộc vào thời điểm mua cụ thể. Biểu đồ cho thấy toàn bộ hành trình giá, từ đó tính được lợi nhuận chính xác."}}
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.chart', ['period' => '10y', 'periodLabel' => '10 năm'])

@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $sjcVariant = $sjc ? ($sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first()) : null;
@endphp
<div class="rounded-sm border border-rose-200 bg-gradient-to-r from-rose-50 to-pink-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="landmark" class="h-5 w-5 text-rose-500"></i> Tóm tắt giá vàng 10 năm ({{ now()->subYears(10)->format('Y') }} – {{ now()->format('Y') }})
    </h2>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 text-sm">
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
            <div><span class="font-semibold">Giai đoạn:</span> <span class="text-slate-600">{{ now()->subYears(10)->format('m/Y') }} – {{ now()->format('m/Y') }}</span></div>
        </div>
    </div>
</div>

@include('gold.sections.brand-charts', ['brandPeriod' => '10y'])

<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 article-body">
    <h2 class="flex items-center gap-2 border-l-4 border-rose-400 pl-3">Biểu đồ giá vàng 10 năm — Nhìn lại thập kỷ biến động</h2>

    <p><strong>Biểu đồ giá vàng 10 năm</strong> cho thấy toàn cảnh siêu chu kỳ (super cycle) của vàng tại Việt Nam. Đây là loại biểu đồ dành cho nhà đầu tư dài hạn, người muốn đánh giá vàng như kênh tích trữ tài sản qua nhiều biến cố kinh tế.</p>

    <h3>Các mốc lịch sử giá vàng 10 năm qua</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Năm</th><th class="border border-slate-200 p-2 text-left font-semibold">Sự kiện</th><th class="border border-slate-200 p-2 text-left font-semibold">Tác động lên giá vàng SJC</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">2016-2018</td><td class="border border-slate-200 p-2 text-slate-600">Fed tăng lãi suất, kinh tế Mỹ phục hồi</td><td class="border border-slate-200 p-2">Vàng đi ngang 33-37 triệu, áp lực từ USD mạnh</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2019</td><td class="border border-slate-200 p-2 text-slate-600">Chiến tranh thương mại Mỹ-Trung, Fed đảo chiều giảm lãi suất</td><td class="border border-slate-200 p-2"><span class="text-emerald-600 font-medium">Vàng phá ngưỡng 42 triệu</span></td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2020</td><td class="border border-slate-200 p-2 text-slate-600">Đại dịch COVID-19, nới lỏng tiền tệ toàn cầu</td><td class="border border-slate-200 p-2"><span class="text-emerald-600 font-medium">Lập đỉnh lịch sử ~58 triệu (tháng 8/2020)</span></td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2021-2022</td><td class="border border-slate-200 p-2 text-slate-600">Lạm phát Mỹ kỷ lục 9.1%, Fed tăng lãi suất mạnh, chiến tranh Nga-Ukraine</td><td class="border border-slate-200 p-2">Biến động mạnh 62-74 triệu, premium SCJ tăng vọt</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2023</td><td class="border border-slate-200 p-2 text-slate-600">Khủng hoảng ngân hàng Mỹ (SVB), NHTW mua vàng kỷ lục</td><td class="border border-slate-200 p-2"><span class="text-emerald-600 font-medium">SJC vượt 80 triệu lần đầu tiên</span></td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2024</td><td class="border border-slate-200 p-2 text-slate-600">Fed giữ lãi suất cao, NHNN can thiệp bán vàng, nhu cầu NHTW</td><td class="border border-slate-200 p-2"><span class="text-emerald-600 font-medium">SJC phá mốc 90 triệu</span></td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">2025-2026</td><td class="border border-slate-200 p-2 text-slate-600">Chu kỳ giảm lãi suất, bất ổn địa chính trị, nhu cầu trú ẩn</td><td class="border border-slate-200 p-2">Tiếp tục xu hướng tăng, premium nội địa biến động</td></tr>
            </tbody>
        </table>
    </div>

    <h3>Bài học từ biểu đồ 10 năm</h3>
    <ul>
        <li><strong>Xu hướng dài hạn tăng:</strong> Dù có nhiều đợt điều chỉnh, giá vàng 10 năm cho thấy xu hướng tăng rõ ràng — vàng là kênh bảo toàn tài sản</li>
        <li><strong>Mua khi hoảng loạn:</strong> Các đợt giảm mạnh (COVID sớm, Fed tăng lãi suất) đều là cơ hội mua cho nhà đầu tư kiên nhẫn</li>
        <li><strong>Premium nội địa:</strong> Khoảng cách SJC vs giá thế giới thay đổi qua các năm. Khi NHNN can thiệp = premium thu hẹp = cơ hội mua SJC</li>
        <li><strong>Tương quan USD/VND:</strong> Tỷ giá VND yếu đi làm giá vàng nội địa tăng thêm kể cả khi XAU/USD không đổi</li>
    </ul>

    <h3>Vàng so với các kênh đầu tư khác (10 năm)</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead><tr class="bg-slate-50"><th class="border border-slate-200 p-2 text-left font-semibold">Kênh đầu tư</th><th class="border border-slate-200 p-2 text-left font-semibold">Lợi nhuận ước tính (10 năm)</th><th class="border border-slate-200 p-2 text-left font-semibold">Rủi ro</th></tr></thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium text-amber-700">Vàng SJC</td><td class="border border-slate-200 p-2 text-emerald-600 font-semibold">~150-180%</td><td class="border border-slate-200 p-2 text-slate-600">Trung bình, thanh khoản cao</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Tiết kiệm ngân hàng</td><td class="border border-slate-200 p-2 text-slate-600">~50-70% (lãi kép 5-6%/năm)</td><td class="border border-slate-200 p-2 text-slate-600">Thấp, bảo hiểm tiền gửi</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Bất động sản</td><td class="border border-slate-200 p-2 text-slate-600">Phân hóa mạnh theo vùng</td><td class="border border-slate-200 p-2 text-slate-600">Cao, thanh khoản thấp</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Chứng khoán (VN-Index)</td><td class="border border-slate-200 p-2 text-slate-600">~80-120% (tùy thời điểm)</td><td class="border border-slate-200 p-2 text-slate-600">Cao, biến động mạnh</td></tr>
            </tbody>
        </table>
    </div>
    <p><em>Lưu ý: Số liệu mang tính tham khảo, hiệu suất quá khứ không đảm bảo tương lai. Nên phân bổ tài sản đa dạng.</em></p>
</article>

<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2"><i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        @php $faqs = [
            ['q' => 'Giá vàng 10 năm qua tăng bao nhiêu lần?', 'a' => 'Xem phần thống kê trên biểu đồ. Tổng quan: SJC từ khoảng 33-36 triệu (2016) đến mức hiện tại, tương đương mức tăng trưởng đáng kể qua thập kỷ.'],
            ['q' => 'Nếu mua vàng 10 năm trước thì lãi bao nhiêu?', 'a' => 'Tùy thời điểm mua. Nếu mua SJC đầu 2016 (~36tr) và giữ đến nay, lợi nhuận có thể trên 150%. Biểu đồ cho thấy cụ thể từng mốc giá.'],
            ['q' => 'Giá vàng có giảm được như trước COVID không?', 'a' => 'Khó xảy ra do: NHTW toàn cầu tích cực mua dự trữ vàng, tỷ giá VND yếu đi so với USD, và bối cảnh địa chính trị bất ổn hỗ trợ vàng.'],
            ['q' => 'Biểu đồ 10 năm cập nhật như thế nào?', 'a' => 'Dữ liệu hiển thị theo ngày, lấy giá cuối ngày. Mỗi ngày có 1 điểm dữ liệu cho mỗi thương hiệu. Cập nhật tự động khi có giá mới.'],
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
        <a href="/bieu-do-gia-vang/bieu-do-gia-vang-1-nam" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline hover:bg-blue-50"><i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i> 1 năm</a>
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
