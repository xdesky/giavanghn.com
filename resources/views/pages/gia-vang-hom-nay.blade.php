@extends('gold.page-shell')

@section('page-label', 'Cập nhật giá vàng')

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
            "name": "Giá vàng SJC hôm nay bao nhiêu?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Giá vàng SJC hôm nay được cập nhật liên tục tại GiaVangHN. Bảng giá bao gồm giá mua vào và bán ra vàng miếng SJC 1 lượng, 5 chỉ, 2 chỉ, 1 chỉ từ các thương hiệu lớn nhất Việt Nam."
            }
        },
        {
            "@@type": "Question",
            "name": "Giá vàng nhẫn 9999 hôm nay bao nhiêu?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Giá vàng nhẫn 9999 (vàng nhẫn tròn) có độ tinh khiết 99.99%, giá thường thấp hơn vàng miếng SJC. Xem bảng giá cập nhật từ DOJI, PNJ, Bảo Tín Minh Châu, Phú Quý và các thương hiệu khác."
            }
        },
        {
            "@@type": "Question",
            "name": "Nên mua vàng miếng SJC hay vàng nhẫn 9999?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Vàng miếng SJC có tính thanh khoản cao, phù hợp tích trữ dài hạn. Vàng nhẫn 9999 có chênh lệch mua-bán thấp hơn, sát giá thế giới, phù hợp giao dịch linh hoạt. Lựa chọn phụ thuộc vào mục đích đầu tư của bạn."
            }
        },
        {
            "@@type": "Question",
            "name": "Giá vàng trong nước và thế giới chênh lệch bao nhiêu?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Chênh lệch (premium) giữa giá vàng SJC và giá thế giới quy đổi thường dao động từ 3-15 triệu đồng/lượng, tùy thời điểm. Vàng nhẫn 9999 thường có chênh lệch thấp hơn vàng miếng SJC."
            }
        },
        {
            "@@type": "Question",
            "name": "Giá vàng cập nhật lúc mấy giờ?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Các thương hiệu vàng Việt Nam cập nhật giá từ khoảng 8h00 sáng đến 17h30 chiều các ngày trong tuần. GiaVangHN tự động đồng bộ giá mới nhất mỗi 15 phút."
            }
        },
        {
            "@@type": "Question",
            "name": "Mua vàng ở đâu uy tín nhất?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Nên mua tại hệ thống chính hãng hoặc đại lý ủy quyền của SJC, DOJI, PNJ, Bảo Tín Minh Châu, Phú Quý. So sánh giá giữa các thương hiệu trước khi giao dịch để có giá tốt nhất."
            }
        }
    ]
}
</script>
@endpush

@section('page-content')
@include('gold.sections.today-price')

{{-- Tóm tắt nhanh cho người dùng --}}
@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $us  = $snapshot['usCard'] ?? null;
    $topBrands = $snapshot['topBrands'] ?? [];
    $sjcVariant = $sjc ? ($sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first()) : null;
    $usVariant = $us ? ($us['variants'][$us['selected']] ?? collect($us['variants'])->first()) : null;
@endphp
<div class="rounded-sm border border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="zap" class="h-5 w-5 text-amber-500"></i> Tóm tắt giá vàng hôm nay {{ now()->format('d/m/Y') }}
    </h2>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 text-sm">
        @if ($sjcVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-amber-500 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Vàng SJC bán ra:</span>
                <span class="font-bold text-amber-900">{{ number_format($sjcVariant['sell'] * 1000000, 0, ',', '.') }} VNĐ/lượng</span>
            </div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-amber-500 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Vàng SJC mua vào:</span>
                <span class="font-bold text-amber-900">{{ number_format($sjcVariant['buy'] * 1000000, 0, ',', '.') }} VNĐ/lượng</span>
            </div>
        </div>
        @endif
        @if ($usVariant)
        <div class="flex items-start gap-2">
            <i data-lucide="circle-dot" class="h-4 w-4 text-blue-500 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Vàng thế giới:</span>
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
        <div class="flex items-start gap-2">
            <i data-lucide="clock" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Cập nhật:</span>
                <span class="text-slate-600">{{ now()->format('H:i d/m/Y') }}</span>
            </div>
        </div>
        <div class="flex items-start gap-2">
            <i data-lucide="building" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-semibold text-slate-800">Nguồn:</span>
                <span class="text-slate-600">{{ count($topBrands) }} thương hiệu</span>
            </div>
        </div>
    </div>
</div>

{{-- Liên kết thương hiệu --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="store" class="h-5 w-5"></i> Giá vàng theo thương hiệu
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

{{-- Hướng dẫn đọc bảng giá --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="book-open" class="h-5 w-5"></i> Hướng dẫn đọc bảng giá vàng
    </h2>
    <div class="grid gap-5 sm:grid-cols-2 text-sm text-slate-700">
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-800">1</span>
            <div><strong>Giá mua vào:</strong> Giá mà tiệm vàng mua lại từ bạn. Đây là giá bạn nhận được khi bán vàng.</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-800">2</span>
            <div><strong>Giá bán ra:</strong> Giá mà tiệm vàng bán cho bạn. Đây là giá bạn phải trả khi mua vàng.</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-800">3</span>
            <div><strong>Chênh lệch:</strong> Hiệu giữa giá bán ra và mua vào. Chênh lệch càng nhỏ, giao dịch càng có lợi cho bạn.</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-800">4</span>
            <div><strong>Thay đổi (%):</strong> Mức tăng/giảm giá so với phiên trước. <span class="text-emerald-600 font-semibold">Xanh = tăng</span>, <span class="text-rose-600 font-semibold">đỏ = giảm</span>.</div>
        </div>
    </div>
</div>

{{-- Bài viết SEO --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-amber-400 pl-3 !mt-0">Giá vàng hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng hôm nay</strong> được cập nhật liên tục từ 8 thương hiệu uy tín nhất Việt Nam: <a href="/gia-vang-hom-nay/gia-vang-sjc" class="text-blue-700 hover:underline">SJC</a>, <a href="/gia-vang-hom-nay/gia-vang-doji" class="text-blue-700 hover:underline">DOJI</a>, <a href="/gia-vang-hom-nay/gia-vang-pnj" class="text-blue-700 hover:underline">PNJ</a>, <a href="/gia-vang-hom-nay/gia-vang-bao-tin-minh-chau" class="text-blue-700 hover:underline">Bảo Tín Minh Châu</a>, <a href="/gia-vang-hom-nay/gia-vang-phu-quy" class="text-blue-700 hover:underline">Phú Quý</a>, <a href="/gia-vang-hom-nay/gia-vang-mi-hong" class="text-blue-700 hover:underline">Mi Hồng</a>, <a href="/gia-vang-hom-nay/gia-vang-bao-tin-manh-hai" class="text-blue-700 hover:underline">Bảo Tín Mạnh Hải</a> và <a href="/gia-vang-hom-nay/gia-vang-ngoc-tham" class="text-blue-700 hover:underline">Ngọc Thẩm</a>. Bảng giá bao gồm giá mua vào, bán ra vàng miếng SJC và vàng nhẫn 9999.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Các loại vàng phổ biến tại Việt Nam</h3>
    <p><strong>Vàng miếng SJC</strong> là thương hiệu vàng quốc gia do Công ty TNHH MTV Vàng Bạc Đá Quý Sài Gòn sản xuất, có tính thanh khoản cao nhất thị trường. <strong>Vàng nhẫn 9999</strong> (còn gọi vàng nhẫn tròn) có độ tinh khiết 99.99%, giá thường thấp hơn vàng miếng SJC từ 1-5 triệu đồng/lượng và sát giá thế giới quy đổi hơn.</p>
    <p>Ngoài ra còn có <strong>vàng 24K</strong> (tương đương 9999), <strong>vàng 18K</strong> (75% vàng ròng, phổ biến trong trang sức), <strong>vàng 14K</strong> (58.5% vàng ròng).</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Yếu tố ảnh hưởng giá vàng</h3>
    <p>Giá vàng trong nước chịu tác động từ nhiều yếu tố:</p>
    <ul class="list-disc pl-5 space-y-1">
        <li><strong>Giá vàng thế giới (<a href="/gia-vang-the-gioi/xau-usd" class="text-blue-700 hover:underline">XAU/USD</a>):</strong> Yếu tố chính quyết định xu hướng giá vàng Việt Nam</li>
        <li><strong>Tỷ giá USD/VND:</strong> Khi USD tăng, giá vàng quy đổi về VNĐ tăng theo</li>
        <li><strong>Chính sách lãi suất Fed:</strong> Lãi suất tăng thường gây áp lực giảm lên giá vàng</li>
        <li><strong>Ngân hàng Nhà nước Việt Nam:</strong> Chính sách điều tiết thị trường vàng miếng SJC</li>
        <li><strong>Cung cầu nội địa:</strong> Nhu cầu mua vàng tích trữ, đặc biệt dịp Thần Tài, lễ Tết</li>
        <li><strong>Tâm lý nhà đầu tư:</strong> Biến động kinh tế – chính trị toàn cầu</li>
    </ul>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Nên mua vàng ở đâu?</h3>
    <p>Nhà đầu tư nên <a href="/so-sanh-gia-vang" class="text-blue-700 hover:underline">so sánh giá giữa các thương hiệu</a> trước khi giao dịch. Vàng nhẫn 9999 thường có chênh lệch mua-bán thấp hơn, phù hợp với giao dịch ngắn hạn. Mua trực tiếp tại cửa hàng chính hãng hoặc đại lý ủy quyền để đảm bảo chất lượng.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Quy đổi đơn vị vàng</h3>
    <div class="overflow-x-auto not-prose">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border border-slate-200 p-2 text-left font-semibold">Đơn vị</th>
                    <th class="border border-slate-200 p-2 text-left font-semibold">Quy đổi</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="border border-slate-200 p-2">1 lượng vàng</td><td class="border border-slate-200 p-2">= 37.5 gram = 10 chỉ</td></tr>
                <tr><td class="border border-slate-200 p-2">1 chỉ vàng</td><td class="border border-slate-200 p-2">= 3.75 gram</td></tr>
                <tr><td class="border border-slate-200 p-2">1 Troy Ounce (oz)</td><td class="border border-slate-200 p-2">= 31.1035 gram</td></tr>
                <tr><td class="border border-slate-200 p-2">1 lượng</td><td class="border border-slate-200 p-2">≈ 1.2057 Troy Ounce</td></tr>
            </tbody>
        </table>
    </div>
</article>

{{-- Câu hỏi thường gặp (FAQ) --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2">
        <i data-lucide="help-circle" class="h-5 w-5"></i> Câu hỏi thường gặp về giá vàng
    </h2>
    <div class="divide-y divide-slate-200" x-data="{ open: null }">
        @php
            $faqs = [
                ['q' => 'Giá vàng SJC hôm nay bao nhiêu?', 'a' => 'Giá vàng SJC được cập nhật liên tục tại bảng giá phía trên. Bạn có thể xem chi tiết giá mua vào, bán ra vàng miếng SJC 1 lượng, 5 chỉ, 2 chỉ, 1 chỉ từ tất cả các thương hiệu lớn tại Việt Nam.'],
                ['q' => 'Giá vàng nhẫn 9999 hôm nay bao nhiêu?', 'a' => 'Giá vàng nhẫn 9999 (vàng nhẫn tròn, độ tinh khiết 99.99%) thường thấp hơn vàng miếng SJC. Xem bảng giá mới nhất từ DOJI, PNJ, Bảo Tín Minh Châu, Phú Quý và các thương hiệu khác ở bảng giá bên trên.'],
                ['q' => 'Nên mua vàng miếng SJC hay vàng nhẫn 9999?', 'a' => 'Vàng miếng SJC có tính thanh khoản cao nhất, phù hợp tích trữ dài hạn, nhưng giá thường cao hơn và chênh lệch mua-bán lớn. Vàng nhẫn 9999 có giá gần giá thế giới hơn, chênh lệch mua-bán thấp, phù hợp giao dịch linh hoạt với số vốn nhỏ.'],
                ['q' => 'Giá vàng trong nước và thế giới chênh lệch bao nhiêu?', 'a' => 'Chênh lệch (premium) giữa giá vàng SJC và giá thế giới quy đổi thường dao động từ 3-15 triệu đồng/lượng. Vàng nhẫn 9999 có chênh lệch thấp hơn. Xem chi tiết tại trang So sánh SJC vs Thế giới.'],
                ['q' => 'Giá vàng cập nhật lúc mấy giờ?', 'a' => 'Các thương hiệu vàng Việt Nam thường cập nhật giá từ khoảng 8h00 đến 17h30 các ngày trong tuần. GiaVangHN tự động đồng bộ giá mới nhất mỗi 15 phút. Một số thương hiệu có cập nhật thêm vào buổi tối khi vàng thế giới biến động mạnh.'],
                ['q' => 'Mua vàng ở đâu uy tín, giá tốt nhất?', 'a' => 'Nên mua tại hệ thống chính hãng: SJC (hàng trăm đại lý toàn quốc), DOJI, PNJ (400+ cửa hàng), Bảo Tín Minh Châu, Phú Quý. So sánh giá giữa các thương hiệu tại GiaVangHN trước khi giao dịch để chọn nơi có giá tốt nhất.'],
                ['q' => '1 lượng vàng bằng bao nhiêu gram?', 'a' => '1 lượng vàng = 37.5 gram = 10 chỉ vàng. 1 chỉ vàng = 3.75 gram. Đây là đơn vị đo phổ biến tại Việt Nam. Quốc tế thường dùng đơn vị Troy Ounce (1 oz ≈ 31.1 gram).'],
                ['q' => 'Vàng 9999 và vàng 24K khác nhau thế nào?', 'a' => 'Vàng 9999 và vàng 24K đều chỉ vàng có độ tinh khiết rất cao. Vàng 9999 có hàm lượng vàng ròng 99.99%, trong khi 24K (karat) tương đương 99.9% trở lên. Trong thực tế thị trường Việt Nam, hai khái niệm này gần như tương đương.'],
            ];
        @endphp
        @foreach ($faqs as $i => $faq)
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

{{-- Liên kết nhanh đến các trang liên quan --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="link" class="h-5 w-5"></i> Xem thêm
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
        <a href="/gia-vang-the-gioi" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="globe" class="h-4 w-4 text-slate-400"></i> Giá vàng thế giới
        </a>
        <a href="/bieu-do-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="bar-chart-3" class="h-4 w-4 text-slate-400"></i> Biểu đồ giá vàng
        </a>
        <a href="/so-sanh-gia-vang" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="git-compare" class="h-4 w-4 text-slate-400"></i> So sánh giá vàng
        </a>
        <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
            <i data-lucide="scale" class="h-4 w-4 text-slate-400"></i> SJC vs Thế giới
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
