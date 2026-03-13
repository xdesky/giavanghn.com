@extends('gold.page-shell')

@section('page-label', 'Quốc tế')

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
            "name": "Giá vàng thế giới hôm nay bao nhiêu?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Giá vàng thế giới (XAU/USD) được cập nhật liên tục tại GiaVangHN. Bảng giá bao gồm giá spot vàng, bạc, bạch kim, palladium tính bằng USD và các đồng tiền chính."
            }
        },
        {
            "@@type": "Question",
            "name": "XAU/USD là gì?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "XAU/USD là mã giao dịch quốc tế của vàng tính bằng đô la Mỹ. XAU là ký hiệu hóa học của vàng (Aurum), USD là đô la Mỹ. 1 XAU = giá 1 Troy Ounce vàng (31.1035 gram)."
            }
        },
        {
            "@@type": "Question",
            "name": "Yếu tố nào ảnh hưởng giá vàng thế giới?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Giá vàng thế giới chịu tác động từ: chính sách lãi suất Fed, chỉ số USD (DXY), lợi suất trái phiếu Mỹ 10 năm, lạm phát CPI, tình hình địa chính trị, nhu cầu mua vàng của ngân hàng trung ương, và dòng vốn ETF vàng."
            }
        },
        {
            "@@type": "Question",
            "name": "Giá vàng thế giới giao dịch ở đâu?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Vàng được giao dịch trên sàn COMEX (New York), LBMA (London), Shanghai Gold Exchange (Trung Quốc), TOCOM (Tokyo). Giá spot XAU/USD là giá tham chiếu phổ biến nhất, giao dịch 23/24h mỗi ngày."
            }
        },
        {
            "@@type": "Question",
            "name": "Quy đổi giá vàng thế giới sang VNĐ như thế nào?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Công thức: Giá 1 lượng vàng (VNĐ) = Giá XAU/USD × Tỷ giá USD/VND ÷ 31.1035 × 37.5. Ví dụ: XAU/USD = 2,900 USD, tỷ giá = 25,400 → 1 lượng ≈ 88.9 triệu VNĐ (chưa tính premium)."
            }
        },
        {
            "@@type": "Question",
            "name": "Gold/Silver Ratio là gì?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Gold/Silver Ratio là tỷ lệ giá vàng chia cho giá bạc. Tỷ lệ này cho biết cần bao nhiêu ounce bạc để mua 1 ounce vàng. Trung bình lịch sử khoảng 60-80. Khi tỷ lệ cao (>80), bạc được coi là rẻ tương đối so với vàng."
            }
        }
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.world-price')

{{-- Tóm tắt thị trường --}}
@php
    $usCard = $snapshot['usCard'] ?? null;
    $usVariant = $usCard ? ($usCard['variants'][$usCard['selected']] ?? collect($usCard['variants'])->first()) : null;
    $globalMarkets = $snapshot['globalMarkets'] ?? [];
    $macroFactors = $snapshot['macroFactors'] ?? [];
@endphp
<div class="rounded-sm border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="zap" class="h-5 w-5 text-blue-500"></i> Tóm tắt thị trường vàng thế giới {{ now()->format('d/m/Y') }}
    </h2>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
        @if ($usVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-blue-500 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">XAU/USD:</span>
                <span class="font-bold text-blue-900">{{ number_format($usVariant['price'], 2) }} USD/oz</span>
            </div>
        </div>
        @endif
        @if ($usCard)
        <div class="flex items-start gap-2">
            <i data-lucide="trending-up" class="h-4 w-4 {{ $usCard['trendPercent'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Biến động:</span>
                <span class="font-bold {{ $usCard['trendPercent'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ sprintf('%+.2f%%', $usCard['trendPercent']) }}</span>
            </div>
        </div>
        @endif
        @if ($usVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="arrow-up-down" class="h-4 w-4 {{ str_starts_with($usVariant['dayChangeLabel'] ?? '', '-') ? 'text-rose-500' : 'text-emerald-500' }} mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Thay đổi ngày:</span>
                <span class="font-bold {{ str_starts_with($usVariant['dayChangeLabel'] ?? '', '-') ? 'text-rose-700' : 'text-emerald-700' }}">{{ $usVariant['dayChangeLabel'] ?? 'N/A' }}</span>
            </div>
        </div>
        @endif
        <div class="flex items-start gap-2">
            <i data-lucide="clock" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Cập nhật:</span>
                <span class="text-slate-600">{{ now()->format('H:i d/m/Y') }}</span>
            </div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="globe" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Kim loại quý:</span>
                <span class="text-slate-600">{{ count($globalMarkets) }} mã giao dịch</span>
            </div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="activity" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Phiên giao dịch:</span>
                <span class="text-slate-600">{{ now()->hour >= 7 && now()->hour < 20 ? 'Châu Á / Châu Âu' : 'Mỹ' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Liên kết theo cặp tiền --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="coins" class="h-5 w-5"></i> Giá theo cặp tiền & kim loại
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 text-sm">
        @foreach ($children as $child)
            <a href="/{{ $child['path'] }}" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
                <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i>
                {{ $child['title'] }}
            </a>
        @endforeach
    </div>
</div>

{{-- Phân tích chuyên sâu: Phiên giao dịch --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="clock-3" class="h-5 w-5"></i> Phiên giao dịch vàng thế giới (giờ Việt Nam)
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <caption class="sr-only">Lịch phiên giao dịch vàng thế giới theo giờ Việt Nam</caption>
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Phiên</th>
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Giờ VN</th>
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Đặc điểm</th>
                    <th class="border border-slate-200 p-2.5 text-center font-semibold">Biến động</th>
                </tr>
            </thead>
            <tbody>
                <tr class="{{ now()->hour >= 6 && now()->hour < 14 ? 'bg-blue-50' : '' }}">
                    <td class="border border-slate-200 p-2.5 font-medium">🌏 Châu Á (Sydney + Tokyo)</td>
                    <td class="border border-slate-200 p-2.5">06:00 – 14:00</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Thanh khoản thấp, biến động nhẹ. Nhu cầu vàng vật chất từ Trung Quốc, Ấn Độ</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Thấp</span></td>
                </tr>
                <tr class="{{ now()->hour >= 14 && now()->hour < 20 ? 'bg-blue-50' : '' }}">
                    <td class="border border-slate-200 p-2.5 font-medium">🌍 Châu Âu (London)</td>
                    <td class="border border-slate-200 p-2.5">14:00 – 23:00</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Thanh khoản tăng mạnh. LBMA Gold Fix lúc 17:30 VN. Overlap với phiên Mỹ</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Trung bình</span></td>
                </tr>
                <tr class="{{ now()->hour >= 20 || now()->hour < 3 ? 'bg-blue-50' : '' }}">
                    <td class="border border-slate-200 p-2.5 font-medium">🌎 Mỹ (New York)</td>
                    <td class="border border-slate-200 p-2.5">20:00 – 05:00</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Biến động mạnh nhất. Dữ liệu kinh tế Mỹ, phát biểu Fed, COMEX futures</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="inline-block rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">Cao</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-500 flex items-center gap-1"><i data-lucide="info" class="h-3 w-3"></i> Hàng có nền xanh là phiên đang diễn ra</p>
</div>

{{-- Phân tích chuyên sâu: Chỉ báo vĩ mô --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="gauge" class="h-5 w-5"></i> Chỉ báo vĩ mô ảnh hưởng giá vàng
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <caption class="sr-only">Các chỉ báo kinh tế vĩ mô ảnh hưởng đến giá vàng</caption>
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Chỉ báo</th>
                    <th class="border border-slate-200 p-2.5 text-left font-semibold">Ý nghĩa</th>
                    <th class="border border-slate-200 p-2.5 text-center font-semibold">Tác động khi tăng</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium">Lãi suất Fed (Fed Funds Rate)</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Lãi suất cơ bản của Mỹ. Ảnh hưởng chi phí cơ hội nắm giữ vàng</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="text-rose-600 font-semibold">↓ Giá vàng giảm</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium">Chỉ số USD (DXY)</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Sức mạnh đồng USD so với 6 đồng tiền chính. Vàng và USD thường ngược chiều</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="text-rose-600 font-semibold">↓ Giá vàng giảm</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium">Lợi suất US10Y (trái phiếu Mỹ 10 năm)</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Lãi suất thực. Khi lợi suất thực dương cao, vàng kém hấp dẫn</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="text-rose-600 font-semibold">↓ Giá vàng giảm</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium">Lạm phát CPI</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Vàng là tài sản chống lạm phát. CPI tăng → nhu cầu trú ẩn tăng</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="text-emerald-600 font-semibold">↑ Giá vàng tăng</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium">Dòng vốn ETF vàng (GLD, IAU)</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Phản ánh nhu cầu đầu tư tổ chức. Vàng ETF tăng = cầu tăng</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="text-emerald-600 font-semibold">↑ Giá vàng tăng</span></td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2.5 font-medium">Mua vàng NHTW (Central Bank Buying)</td>
                    <td class="border border-slate-200 p-2.5 text-slate-600">Trung Quốc, Ấn Độ, Thổ Nhĩ Kỳ mua ròng hàng trăm tấn/năm</td>
                    <td class="border border-slate-200 p-2.5 text-center"><span class="text-emerald-600 font-semibold">↑ Giá vàng tăng</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Bài viết SEO mở rộng --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-blue-400 pl-3 !mt-0">Giá vàng thế giới hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng thế giới</strong> (<a href="/gia-vang-the-gioi/xau-usd" class="text-blue-700 hover:underline">XAU/USD</a>) là mức giá giao dịch vàng quốc tế tính bằng đô la Mỹ trên mỗi Troy Ounce (31.1035 gram). Đây là chỉ số tham chiếu quan trọng nhất cho thị trường vàng toàn cầu, được giao dịch 23/24h mỗi ngày trên các sàn COMEX, LBMA và SGE.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Các cặp tiền vàng phổ biến</h3>
    <p>Vàng được giao dịch bằng nhiều đồng tiền quốc tế:</p>
    <ul class="list-disc pl-5 space-y-1">
        <li><a href="/gia-vang-the-gioi/xau-usd" class="text-blue-700 hover:underline"><strong>XAU/USD</strong></a> — Đô la Mỹ: Cặp phổ biến nhất, tham chiếu cho toàn cầu</li>
        <li><a href="/gia-vang-the-gioi/xau-eur" class="text-blue-700 hover:underline"><strong>XAU/EUR</strong></a> — Euro: Phản ánh chính sách ECB</li>
        <li><a href="/gia-vang-the-gioi/xau-gbp" class="text-blue-700 hover:underline"><strong>XAU/GBP</strong></a> — Bảng Anh: Giá tham chiếu LBMA London</li>
        <li><a href="/gia-vang-the-gioi/xau-cny" class="text-blue-700 hover:underline"><strong>XAU/CNY</strong></a> — Nhân dân tệ: Nhu cầu vàng lớn nhất thế giới</li>
        <li><a href="/gia-vang-the-gioi/xau-jpy" class="text-blue-700 hover:underline"><strong>XAU/JPY</strong></a> — Yên Nhật: Ảnh hưởng bởi chính sách BOJ</li>
    </ul>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Kim loại quý khác</h3>
    <p>Thị trường kim loại quý bao gồm 4 kim loại chính, mỗi loại có đặc tính riêng:</p>
    <ul class="list-disc pl-5 space-y-1">
        <li><strong>Vàng (XAU)</strong> — Tài sản trú ẩn an toàn, chống lạm phát, thanh khoản cao nhất</li>
        <li><a href="/gia-vang-the-gioi/xag-usd" class="text-blue-700 hover:underline"><strong>Bạc (XAG)</strong></a> — 50% nhu cầu công nghiệp (điện tử, năng lượng mặt trời), biến động mạnh hơn vàng</li>
        <li><a href="/gia-vang-the-gioi/xpt-usd" class="text-blue-700 hover:underline"><strong>Bạch kim (XPT)</strong></a> — Ngành ô tô (bộ xúc tác khí thải), nguồn cung tập trung ở Nam Phi</li>
        <li><a href="/gia-vang-the-gioi/xpd-usd" class="text-blue-700 hover:underline"><strong>Palladium (XPD)</strong></a> — Ô tô xăng (bộ chuyển đổi xúc tác), nguồn cung chính từ Nga</li>
    </ul>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Cách đọc giá vàng thế giới</h3>
    <p><strong>XAU/USD = 2,900</strong> có nghĩa 1 Troy Ounce vàng (≈ 31.1g) có giá 2,900 đô la Mỹ. Giá vàng spot là giá giao ngay, khác với giá futures (hợp đồng tương lai) thường cao hơn do chi phí lưu kho và lãi suất.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Công thức quy đổi sang giá Việt Nam</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2 text-left font-semibold">Bước</th>
                    <th class="border border-slate-200 p-2 text-left font-semibold">Công thức</th>
                    <th class="border border-slate-200 p-2 text-left font-semibold">Ví dụ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">1. Giá 1 gram vàng</td>
                    <td class="border border-slate-200 p-2">XAU/USD ÷ 31.1035</td>
                    <td class="border border-slate-200 p-2">2,900 ÷ 31.1035 = 93.27 USD/gram</td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">2. Giá 1 lượng (37.5g)</td>
                    <td class="border border-slate-200 p-2">Giá 1 gram × 37.5</td>
                    <td class="border border-slate-200 p-2">93.27 × 37.5 = 3,497.6 USD</td>
                </tr>
                <tr>
                    <td class="border border-slate-200 p-2 font-medium">3. Quy đổi sang VNĐ</td>
                    <td class="border border-slate-200 p-2">Giá 1 lượng × Tỷ giá USD/VND</td>
                    <td class="border border-slate-200 p-2">3,497.6 × 25,400 = 88.84 triệu VNĐ</td>
                </tr>
                <tr class="bg-amber-50">
                    <td class="border border-slate-200 p-2 font-bold">4. Giá SJC thực tế</td>
                    <td class="border border-slate-200 p-2 font-medium">Giá quy đổi + Premium SJC</td>
                    <td class="border border-slate-200 p-2 font-medium">88.84 + 5-10 triệu = ~94-99 triệu</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-500">* Premium SJC = chênh lệch giữa giá SJC thực tế và giá thế giới quy đổi, hiện dao động 3-15 triệu/lượng</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Yếu tố ảnh hưởng giá vàng thế giới</h3>
    <p>Là một chuyên gia phân tích, khi đánh giá xu hướng giá vàng cần theo dõi các yếu tố sau:</p>
    <ul class="list-disc pl-5 space-y-1">
        <li><strong>Chính sách Fed:</strong> Lãi suất tăng → chi phí cơ hội nắm giữ vàng tăng → giá vàng có xu hướng giảm. Ngược lại, khi Fed dovish (hạ lãi suất, QE), giá vàng thường bứt phá</li>
        <li><strong>Chỉ số DXY (USD Index):</strong> Vàng và USD có tương quan nghịch mạnh. DXY giảm 1% thường kéo vàng tăng 0.5-1.5%</li>
        <li><strong>Lợi suất trái phiếu Mỹ US10Y:</strong> Lợi suất thực (real yield = US10Y – CPI) là đối trọng trực tiếp của vàng. Lợi suất thực âm = bull market vàng</li>
        <li><strong>Lạm phát CPI/PCE:</strong> Vàng là tài sản chống lạm phát truyền thống. CPI tăng mạnh → nhu cầu vàng tăng</li>
        <li><strong>Dữ liệu việc làm Mỹ (NFP):</strong> Non-Farm Payrolls công bố thứ 6 đầu tháng, ảnh hưởng mạnh đến kỳ vọng lãi suất Fed</li>
        <li><strong>Địa chính trị:</strong> Chiến tranh, căng thẳng thương mại, bất ổn → vàng tăng do nhu cầu trú ẩn</li>
        <li><strong>Ngân hàng trung ương mua vàng:</strong> PBoC, RBI, CBRT mua ròng hàng trăm tấn vàng/năm, tạo cầu nền dài hạn</li>
        <li><strong>Dòng vốn ETF:</strong> SPDR Gold Trust (GLD), iShares Gold Trust (IAU). Dòng tiền vào/ra phản ánh sentiment nhà đầu tư tổ chức</li>
    </ul>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Các mốc giá vàng lịch sử quan trọng</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2 text-left font-semibold">Năm</th>
                    <th class="border border-slate-200 p-2 text-right font-semibold">Giá XAU/USD</th>
                    <th class="border border-slate-200 p-2 text-left font-semibold">Sự kiện</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2">1971</td><td class="border border-slate-200 p-2 text-right font-bold">35</td><td class="border border-slate-200 p-2 text-slate-600">Nixon bỏ bản vị vàng, thả nổi giá</td></tr>
                <tr><td class="border border-slate-200 p-2">1980</td><td class="border border-slate-200 p-2 text-right font-bold">850</td><td class="border border-slate-200 p-2 text-slate-600">Đỉnh lịch sử thời kỳ lạm phát cao, khủng hoảng dầu mỏ</td></tr>
                <tr><td class="border border-slate-200 p-2">2001</td><td class="border border-slate-200 p-2 text-right font-bold">271</td><td class="border border-slate-200 p-2 text-slate-600">Đáy 20 năm, khởi đầu super bull cycle</td></tr>
                <tr><td class="border border-slate-200 p-2">2011</td><td class="border border-slate-200 p-2 text-right font-bold">1,921</td><td class="border border-slate-200 p-2 text-slate-600">Đỉnh sau khủng hoảng 2008, Eurozone nợ công</td></tr>
                <tr><td class="border border-slate-200 p-2">2020</td><td class="border border-slate-200 p-2 text-right font-bold">2,075</td><td class="border border-slate-200 p-2 text-slate-600">COVID-19, Fed QE không giới hạn</td></tr>
                <tr><td class="border border-slate-200 p-2">2024</td><td class="border border-slate-200 p-2 text-right font-bold">2,790</td><td class="border border-slate-200 p-2 text-slate-600">NHTW mua kỷ lục, căng thẳng địa chính trị</td></tr>
            </tbody>
        </table>
    </div>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Mối liên hệ với giá vàng Việt Nam</h3>
    <p>Giá vàng SJC trong nước phụ thuộc trực tiếp vào giá thế giới, nhưng luôn có premium (chênh lệch). Premium phụ thuộc: hạn ngạch nhập khẩu vàng (Ngân hàng Nhà nước quản lý), cung cầu nội địa, và tâm lý thị trường. Nhà đầu tư nên theo dõi cả hai để có quyết định chính xác. Xem <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="text-blue-700 hover:underline">So sánh SJC vs Thế giới</a> để theo dõi premium realtime.</p>
</article>

{{-- Câu hỏi thường gặp (FAQ) --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2">
        <i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp về giá vàng thế giới
    </h2>
    <div class="divide-y divide-slate-200">
        @php
            $faqs = [
                ['q' => 'Giá vàng thế giới hôm nay bao nhiêu?', 'a' => 'Giá vàng thế giới (XAU/USD) được cập nhật liên tục tại bảng giá phía trên. Giá spot vàng giao dịch gần như 24/7, trừ khoảng 1 giờ nghỉ cuối tuần (từ tối thứ 6 đến tối Chủ nhật giờ Việt Nam).'],
                ['q' => 'XAU/USD là gì?', 'a' => 'XAU là ký hiệu quốc tế của vàng (từ tiếng Latin "Aurum"), USD là đô la Mỹ. XAU/USD = giá 1 Troy Ounce vàng tính bằng USD. 1 Troy Ounce = 31.1035 gram. Đây là benchmark toàn cầu cho giá vàng.'],
                ['q' => 'Giá vàng spot và giá futures khác nhau thế nào?', 'a' => 'Giá spot (giao ngay) là giá mua bán vàng ngay tại thời điểm. Giá futures (hợp đồng tương lai) trên COMEX thường cao hơn spot 5-15 USD do chi phí lưu kho, bảo hiểm và lãi suất (contango). Khi spot > futures, gọi là backwardation — thường xảy ra khi cầu vật chất rất cao.'],
                ['q' => 'Quy đổi giá vàng thế giới sang VNĐ như thế nào?', 'a' => 'Công thức: Giá 1 lượng (VNĐ) = XAU/USD × Tỷ giá USD/VND ÷ 31.1035 × 37.5. Ví dụ: XAU = 2,900, tỷ giá = 25,400 → 1 lượng ≈ 88.84 triệu VNĐ. Giá SJC thực tế sẽ cao hơn 3-15 triệu do premium.'],
                ['q' => 'Tại sao giá vàng SJC cao hơn giá thế giới quy đổi?', 'a' => 'Premium SJC do: (1) Ngân hàng Nhà nước quản lý hạn ngạch nhập khẩu vàng miếng, hạn chế nguồn cung; (2) SJC là thương hiệu vàng quốc gia duy nhất, cầu cao; (3) Chi phí sản xuất, phân phối; (4) Tâm lý tích trữ. Vàng nhẫn 9999 có premium thấp hơn nhiều.'],
                ['q' => 'Gold/Silver Ratio là gì và cách sử dụng?', 'a' => 'Gold/Silver Ratio = Giá vàng ÷ Giá bạc. Trung bình lịch sử 50 năm khoảng 60-80. Khi ratio > 80, bạc "rẻ" so với vàng → có thể xem xét mua bạc. Khi ratio < 50, vàng "rẻ" hơn tương đối. Các nhà giao dịch dùng ratio để phân bổ danh mục kim loại quý.'],
                ['q' => 'Nên theo dõi dữ liệu kinh tế nào để dự đoán giá vàng?', 'a' => 'Các dữ liệu quan trọng nhất: Non-Farm Payrolls (NFP, thứ 6 đầu tháng), CPI (lạm phát, giữa tháng), quyết định lãi suất Fed (8 lần/năm), PMI sản xuất, đơn hàng bền. Ngoài ra theo dõi DXY (chỉ số USD), US10Y (lợi suất trái phiếu), và dòng vốn ETF vàng.'],
                ['q' => 'Giá vàng thế giới giao dịch ở đâu?', 'a' => 'Vàng giao dịch trên nhiều sàn: COMEX (New York, hợp đồng tương lai), LBMA (London, thị trường OTC lớn nhất), Shanghai Gold Exchange – SGE (Trung Quốc, giao dịch vàng vật chất lớn nhất), TOCOM (Tokyo). Giá spot XAU/USD là giá tổng hợp từ các nhà tạo lập thị trường lớn.'],
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
        <a href="/bieu-do-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="bar-chart-3" class="h-4 w-4 text-slate-400"></i> Biểu đồ giá vàng
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
        <a href="/du-bao-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="compass" class="h-4 w-4 text-slate-400"></i> Dự báo giá vàng
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
