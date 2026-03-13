@extends('gold.page-shell')

@section('page-label', 'Biểu đồ')

@push('head')
{{-- WebPage + BreadcrumbList Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "{{ $title }}",
    "description": "{{ $description }}",
    "url": "{{ url('/' . $path) }}",
    "inLanguage": "vi",
    "dateModified": "{{ now()->toIso8601String() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "GiaVangHN",
        "url": "{{ url('/') }}"
    },
    "breadcrumb": {
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {"@@type": "ListItem", "position": 1, "name": "Trang chủ", "item": "{{ url('/') }}"},
            {"@@type": "ListItem", "position": 2, "name": "{{ $title }}", "item": "{{ url('/' . $path) }}"}
        ]
    }
}
</script>

{{-- FAQPage Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "Biểu đồ giá vàng hôm nay biến động thế nào?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Biểu đồ giá vàng tại GiaVangHN cập nhật liên tục, so sánh giá bán ra của 8 thương hiệu (SJC, DOJI, PNJ, BTMC, Phú Quý, Mi Hồng, Bảo Tín MH, Ngọc Thẩm) và giá vàng thế giới XAU/USD quy đổi. Xem theo khung thời gian: hôm nay, 7 ngày, 30 ngày, 1 năm, 10 năm."
            }
        },
        {
            "@@type": "Question",
            "name": "Cách đọc biểu đồ giá vàng như thế nào?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Trục ngang là thời gian, trục dọc là giá (triệu VNĐ/lượng). Mỗi đường màu đại diện một thương hiệu. Đường đi lên = giá tăng, đi xuống = giá giảm. Hãy chú ý khoảng cách giữa các đường để thấy chênh lệch giá giữa các thương hiệu."
            }
        },
        {
            "@@type": "Question",
            "name": "Nên xem biểu đồ giá vàng khung thời gian nào?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Mỗi khung thời gian phục vụ mục đích khác: Hôm nay (giao dịch ngắn hạn), 7 ngày (xu hướng tuần), 30 ngày (phân tích tháng), 1 năm (xu hướng trung hạn), 10 năm (đầu tư dài hạn). Kết hợp nhiều khung thời gian cho cái nhìn toàn diện."
            }
        },
        {
            "@@type": "Question",
            "name": "Tại sao giá vàng giữa các thương hiệu lại khác nhau?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Mỗi thương hiệu có chênh lệch mua-bán (spread) khác nhau tùy vào chi phí vận hành, chiến lược giá, và vùng địa lý. SJC thường có giá cao nhất do tính thanh khoản và nhận diện thương hiệu. Vàng nhẫn 9999 các hãng có giá gần nhau hơn vàng miếng."
            }
        },
        {
            "@@type": "Question",
            "name": "Biểu đồ giá vàng thế giới so với Việt Nam ra sao?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Biểu đồ so sánh cho thấy xu hướng giá vàng Việt Nam thường bám sát thế giới, nhưng có premium (chênh lệch). Khi premium thu hẹp, có thể là cơ hội mua. Khi premium mở rộng quá lớn, rủi ro điều chỉnh tăng."
            }
        }
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.chart', ['period' => '1y', 'periodLabel' => 'SJC, DOJI, PNJ, BTMC, Phú Quý, Mi Hồng, Bảo Tín MH, Ngọc Thẩm'])

{{-- Tóm tắt biến động --}}
@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $us  = $snapshot['usCard'] ?? null;
    $topBrands = $snapshot['topBrands'] ?? [];
    $sjcVariant = $sjc ? ($sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first()) : null;
    $usVariant = $us ? ($us['variants'][$us['selected']] ?? collect($us['variants'])->first()) : null;
@endphp
<div class="rounded-sm border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="zap" class="h-5 w-5 text-emerald-500"></i> Tóm tắt biến động giá vàng {{ now()->format('d/m/Y') }}
    </h2>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
        @if ($sjcVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-amber-500 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Vàng SJC bán ra:</span>
                <span class="font-bold text-amber-900">{{ number_format($sjcVariant['sell'] * 1000000, 0, ',', '.') }} VNĐ/lượng</span>
            </div>
        </div>
        @endif
        @if ($usVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-blue-500 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">XAU/USD:</span>
                <span class="font-bold text-blue-900">{{ number_format($usVariant['price'], 2) }} USD/oz</span>
            </div>
        </div>
        @endif
        @if ($sjc)
        <div class="flex items-start gap-2">
            <i data-lucide="trending-up" class="h-4 w-4 {{ $sjc['trendPercent'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Biến động SJC:</span>
                <span class="font-bold {{ $sjc['trendPercent'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ sprintf('%+.2f%%', $sjc['trendPercent']) }}</span>
            </div>
        </div>
        @endif
        @if ($us)
        <div class="flex items-start gap-2">
            <i data-lucide="trending-up" class="h-4 w-4 {{ $us['trendPercent'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Biến động thế giới:</span>
                <span class="font-bold {{ $us['trendPercent'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ sprintf('%+.2f%%', $us['trendPercent']) }}</span>
            </div>
        </div>
        @endif
        <div class="flex items-start gap-2">
            <i data-lucide="building" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">So sánh:</span>
                <span class="text-slate-600">{{ count($topBrands) }} thương hiệu VN + thế giới</span>
            </div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="clock" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Cập nhật:</span>
                <span class="text-slate-600">{{ now()->format('H:i d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Chọn khung thời gian --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="calendar" class="h-5 w-5"></i> Biểu đồ theo khung thời gian
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 text-sm">
        @foreach ($children as $child)
            <a href="/{{ $child['path'] }}" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
                <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i>
                {{ $child['title'] }}
            </a>
        @endforeach
    </div>
</div>

{{-- Hướng dẫn đọc biểu đồ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="book-open" class="h-5 w-5"></i> Hướng dẫn đọc biểu đồ giá vàng
    </h2>
    <div class="grid gap-3 sm:grid-cols-2 text-sm text-slate-700">
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">1</span>
            <div><strong>Trục ngang (X):</strong> Thời gian — ngày/tháng hoặc giờ:phút tùy khung thời gian. Đọc từ trái (quá khứ) sang phải (hiện tại).</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">2</span>
            <div><strong>Trục dọc (Y):</strong> Giá bán ra (triệu VNĐ/lượng). Đường đi lên = giá tăng, đi xuống = giá giảm.</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">3</span>
            <div><strong>Nhiều đường màu:</strong> Mỗi màu là một thương hiệu. <span class="inline-block w-3 h-0.5 bg-[#b8860b] align-middle"></span> SJC, <span class="inline-block w-3 h-0.5 bg-[#3b82f6] align-middle"></span> DOJI, <span class="inline-block w-3 h-0.5 bg-[#15803d] align-middle"></span> PNJ, <span class="inline-block w-3 h-0.5 bg-[#dc2626] align-middle"></span> BTMC</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">4</span>
            <div><strong>So sánh:</strong> Khoảng cách giữa các đường = chênh lệch giá. Đường SJC thường ở trên cùng (giá cao nhất).</div>
        </div>
    </div>
</div>

{{-- Phân tích khung thời gian --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="gauge" class="h-5 w-5"></i> Phân tích theo khung thời gian
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <caption class="sr-only">Phân tích ý nghĩa từng khung thời gian biểu đồ giá vàng</caption>
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Khung thời gian</th>
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Phù hợp với</th>
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Ý nghĩa phân tích</th>
                    <th class="border border-slate-200 p-2.5 text-center font-semibold">Mức độ nhiễu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium"><a href="/bieu-do-gia-vang/bieu-do-gia-vang-hom-nay" class="text-blue-700 hover:underline">Hôm nay</a></td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Giao dịch trong ngày</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Biến động phiên, thời điểm mua/bán tối ưu trong ngày, phản ứng tin tức</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">Cao</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium"><a href="/bieu-do-gia-vang/bieu-do-gia-vang-7-ngay" class="text-blue-700 hover:underline">7 ngày</a></td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Tracking tuần</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Xu hướng ngắn hạn, xác nhận breakout hoặc pullback, chênh lệch thương hiệu</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Trung bình</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium"><a href="/bieu-do-gia-vang/bieu-do-gia-vang-30-ngay" class="text-blue-700 hover:underline">30 ngày</a></td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Phân tích tháng</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Xu hướng trung hạn, vùng hỗ trợ/kháng cự, tác động chính sách tiền tệ</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Thấp</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium"><a href="/bieu-do-gia-vang/bieu-do-gia-vang-1-nam" class="text-blue-700 hover:underline">1 năm</a></td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Đầu tư trung hạn</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Chu kỳ tăng/giảm, so sánh hiệu suất thương hiệu, tương quan với thế giới</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Rất thấp</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium"><a href="/bieu-do-gia-vang/bieu-do-gia-vang-10-nam" class="text-blue-700 hover:underline">10 năm</a></td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Tích trữ dài hạn</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Super cycle, biến động lớn (COVID, khủng hoảng), lợi nhuận tích lũy dài hạn</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Rất thấp</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Phân tích chuyên gia: Cách dùng biểu đồ --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-emerald-400 pl-3 !mt-0">Biểu đồ giá vàng {{ now()->format('d/m/Y') }} — Phân tích chuyên sâu</h2>

    <p><strong>Biểu đồ giá vàng</strong> tại GiaVangHN tổng hợp giá bán ra của 8 thương hiệu lớn nhất Việt Nam: <a href="/gia-vang-hom-nay/gia-vang-sjc" class="text-blue-700 hover:underline">SJC</a>, <a href="/gia-vang-hom-nay/gia-vang-doji" class="text-blue-700 hover:underline">DOJI</a>, <a href="/gia-vang-hom-nay/gia-vang-pnj" class="text-blue-700 hover:underline">PNJ</a>, <a href="/gia-vang-hom-nay/gia-vang-bao-tin-minh-chau" class="text-blue-700 hover:underline">Bảo Tín Minh Châu</a>, <a href="/gia-vang-hom-nay/gia-vang-phu-quy" class="text-blue-700 hover:underline">Phú Quý</a>, <a href="/gia-vang-hom-nay/gia-vang-mi-hong" class="text-blue-700 hover:underline">Mi Hồng</a>, <a href="/gia-vang-hom-nay/gia-vang-bao-tin-manh-hai" class="text-blue-700 hover:underline">Bảo Tín Mạnh Hải</a>, <a href="/gia-vang-hom-nay/gia-vang-ngoc-tham" class="text-blue-700 hover:underline">Ngọc Thẩm</a> — cùng <a href="/gia-vang-the-gioi/xau-usd" class="text-blue-700 hover:underline">giá vàng thế giới XAU/USD</a> quy đổi về VNĐ. Biểu đồ giúp nhà đầu tư so sánh trực quan, phát hiện xu hướng và tìm thời điểm giao dịch tốt nhất.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-emerald-300 pl-3">Tại sao biểu đồ quan trọng với nhà đầu tư vàng?</h3>
    <ul class="list-disc pl-5 space-y-1">
        <li><strong>Phát hiện xu hướng:</strong> Biểu đồ cho thấy giá đang tăng, giảm hay đi ngang — thông tin mà bảng giá đơn lẻ không thể hiện được</li>
        <li><strong>So sánh thương hiệu:</strong> Xem ngay thương hiệu nào có giá tốt nhất (bán rẻ nhất), chênh lệch giữa các hãng là bao nhiêu</li>
        <li><strong>Tìm vùng hỗ trợ/kháng cự:</strong> Mức giá mà thị trường "bật" lên hoặc "quay đầu" nhiều lần — là tín hiệu mua/bán quan trọng</li>
        <li><strong>Đo lường biến động:</strong> Biểu đồ dao động mạnh = rủi ro cao, biến động nhẹ = thị trường ổn định</li>
        <li><strong>Quyết định thời điểm:</strong> Mua khi giá hồi về vùng hỗ trợ, cân nhắc bán khi chạm vùng kháng cự</li>
    </ul>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-emerald-300 pl-3">So sánh giá vàng Việt Nam và thế giới qua biểu đồ</h3>
    <p>Biểu đồ tổng hợp cho phép so sánh xu hướng giữa các thương hiệu Việt Nam với giá thế giới quy đổi. Một số điểm cần lưu ý:</p>
    <ul class="list-disc pl-5 space-y-1">
        <li><strong>Xu hướng đồng pha:</strong> Khi tất cả đường cùng đi lên/xuống, thị trường có xu hướng rõ ràng — tín hiệu mạnh</li>
        <li><strong>Phân kỳ (divergence):</strong> Giá trong nước đi ngược giá thế giới = bất thường, thường do cung cầu nội địa hoặc chính sách</li>
        <li><strong>Premium thu hẹp:</strong> Khoảng cách SJC và giá quy đổi hẹp lại → có thể là cơ hội mua SJC. Xem chi tiết tại <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="text-blue-700 hover:underline">So sánh SJC vs Thế giới</a></li>
        <li><strong>Vàng nhẫn 9999:</strong> Đường giá các thương hiệu vàng nhẫn thường bám sát nhau và gần giá thế giới hơn vàng miếng SJC</li>
    </ul>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-emerald-300 pl-3">Mẹo sử dụng biểu đồ hiệu quả</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2 text-left font-semibold">Mẹo</th>
                    <th class="border border-slate-200 p-2 text-left font-semibold">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">Kết hợp nhiều khung</td>
                    <td class="border border-slate-200 p-2 text-slate-600">Xem 1 năm để nắm xu hướng lớn, rồi 30 ngày để tìm điểm vào, 7 ngày để xác nhận</td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">So sánh thương hiệu</td>
                    <td class="border border-slate-200 p-2 text-slate-600">Thương hiệu nào đường giá thấp nhất = giá bán rẻ nhất, có lợi khi mua</td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">Phát hiện mẫu hình</td>
                    <td class="border border-slate-200 p-2 text-slate-600">Đáy đôi (W) = tín hiệu tăng, đỉnh đôi (M) = tín hiệu giảm, sideway = chờ breakout</td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">Zoom và kéo</td>
                    <td class="border border-slate-200 p-2 text-slate-600">Dùng chuột kéo để zoom vào giai đoạn cụ thể, hover để xem giá chính xác</td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">Theo dõi spread</td>
                    <td class="border border-slate-200 p-2 text-slate-600">Khoảng cách giữa đường cao nhất (SJC) và thấp nhất — thu hẹp = thị trường ổn định</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-emerald-300 pl-3">Các thuật ngữ biểu đồ quan trọng</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2 text-left font-semibold">Thuật ngữ</th>
                    <th class="border border-slate-200 p-2 text-left font-semibold">Giải thích</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2 font-medium">Hỗ trợ (Support)</td><td class="border border-slate-200 p-2 text-slate-600">Mức giá mà cầu mua đủ mạnh để ngăn giá giảm thêm. Giá thường "nảy" lên từ vùng này</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Kháng cự (Resistance)</td><td class="border border-slate-200 p-2 text-slate-600">Mức giá mà áp lực bán đủ mạnh để ngăn giá tăng thêm. Giá thường "quay đầu" tại đây</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Breakout</td><td class="border border-slate-200 p-2 text-slate-600">Giá vượt qua vùng kháng cự = tín hiệu mua mạnh. Giá phá vỡ hỗ trợ = tín hiệu bán</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Sideway</td><td class="border border-slate-200 p-2 text-slate-600">Giá đi ngang trong biên độ hẹp, chờ xúc tác mới. Thường xảy ra trước biến động lớn</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Pullback</td><td class="border border-slate-200 p-2 text-slate-600">Giá hồi về tạm thời trong xu hướng tăng — cơ hội mua cho nhà đầu tư bỏ lỡ đáy</td></tr>
                <tr><td class="border border-slate-200 p-2 font-medium">Spread</td><td class="border border-slate-200 p-2 text-slate-600">Chênh lệch giá giữa hai đường (VD: SJC vs DOJI, hoặc mua vào vs bán ra)</td></tr>
            </tbody>
        </table>
    </div>
</article>

{{-- Câu hỏi thường gặp --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2">
        <i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp về biểu đồ giá vàng
    </h2>
    <div class="divide-y divide-slate-200">
        @php
            $faqs = [
                ['q' => 'Biểu đồ giá vàng hiển thị giá mua hay giá bán?', 'a' => 'Biểu đồ mặc định hiển thị giá bán ra (giá bạn phải trả khi mua vàng) của các thương hiệu. Đây là mức giá người mua quan tâm nhất. Giá mua vào (giá tiệm mua lại) được hiển thị trong bảng giá chi tiết.'],
                ['q' => 'Tại sao đường SJC luôn cao hơn các thương hiệu khác?', 'a' => 'Vàng miếng SJC là thương hiệu vàng quốc gia, có tính thanh khoản cao nhất và được Ngân hàng Nhà nước quản lý nguồn cung. Nguồn cung hạn chế + cầu cao = giá SJC thường cao hơn vàng nhẫn 9999 các thương hiệu khác từ 1-5 triệu/lượng.'],
                ['q' => 'Đường giá thế giới quy đổi có ý nghĩa gì?', 'a' => 'Đường giá thế giới quy đổi = XAU/USD × tỷ giá ÷ 31.1035 × 37.5, cho thấy "giá trị thực" của vàng theo thị trường quốc tế. Khoảng cách giữa đường này và đường SJC chính là premium — chênh lệch nội địa.'],
                ['q' => 'Biểu đồ 10 năm cho thấy điều gì?', 'a' => 'Biểu đồ 10 năm cho thấy super cycle của vàng. Từ 2016-2026, giá vàng SJC tăng từ khoảng 36 triệu lên mức hiện tại. Biểu đồ dài hạn giúp đánh giá vàng như kênh tích trữ tài sản, và cho thấy các giai đoạn biến động mạnh do COVID, lạm phát, chiến tranh.'],
                ['q' => 'Nên xem biểu đồ khi nào để ra quyết định mua/bán?', 'a' => 'Xem biểu đồ 1 năm hoặc 30 ngày để xác định xu hướng lớn (tăng, giảm, sideway). Nếu xu hướng tăng, chờ giá pullback về vùng hỗ trợ để mua. Nếu xu hướng giảm, tránh mua "bắt đáy" quá sớm. Xem thêm biểu đồ 7 ngày để xác nhận thời điểm.'],
                ['q' => 'Biểu đồ giá vàng cập nhật bao lâu một lần?', 'a' => 'Dữ liệu biểu đồ được cập nhật mỗi 15 phút trong giờ giao dịch (8h-17h30 ngày thường). Biểu đồ "Hôm nay" cập nhật theo phút, cho phép tracking biến động trong ngày theo thời gian gần thực.'],
            ];
        @endphp
        @foreach ($faqs as $faq)
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>{{ $faq['q'] }}</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">{{ $faq['a'] }}</p>
        </details>
        @endforeach
    </div>
</div>

{{-- Liên kết nhanh --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="link" class="h-5 w-5"></i> Xem thêm
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
        <a href="/gia-vang-hom-nay" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="tag" class="h-4 w-4 text-slate-400"></i> Giá vàng trong nước
        </a>
        <a href="/gia-vang-the-gioi" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="globe" class="h-4 w-4 text-slate-400"></i> Giá vàng thế giới
        </a>
        <a href="/so-sanh-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="git-compare" class="h-4 w-4 text-slate-400"></i> So sánh giá vàng
        </a>
        <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="scale" class="h-4 w-4 text-slate-400"></i> SJC vs Thế giới (Premium)
        </a>
        <a href="/lich-su-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="history" class="h-4 w-4 text-slate-400"></i> Lịch sử giá vàng
        </a>
        <a href="/tin-tuc-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="newspaper" class="h-4 w-4 text-slate-400"></i> Tin tức giá vàng
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
