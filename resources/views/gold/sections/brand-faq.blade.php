{{-- FAQ section for brand price pages. Receives $brandKey (e.g. 'sjc', 'doji', 'pnj'...) --}}
@php
$brandFaqs = [
    'sjc' => [
        'title' => 'Câu hỏi thường gặp về giá vàng SJC',
        'faqs' => [
            ['q' => 'Giá vàng SJC hôm nay bao nhiêu?', 'a' => 'Giá vàng SJC được cập nhật liên tục trên trang này với giá mua vào và bán ra theo thời gian thực. Bảng giá bao gồm vàng miếng SJC 1 lượng, 5 chỉ, 2 chỉ, 1 chỉ và 0.5 chỉ.'],
            ['q' => 'Vàng miếng SJC có mấy loại?', 'a' => 'Vàng miếng SJC có 5 quy cách chính: 1 lượng (37.5g), 5 chỉ (18.75g), 2 chỉ (7.5g), 1 chỉ (3.75g) và 0.5 chỉ (1.875g). Mỗi miếng vàng SJC đều có số seri riêng, được niêm phong và kèm giấy chứng nhận.'],
            ['q' => 'Tại sao giá vàng SJC cao hơn giá vàng thế giới quy đổi?', 'a' => 'Giá vàng SJC thường cao hơn giá thế giới quy đổi (gọi là premium) do nhiều yếu tố: chính sách hạn chế nhập khẩu vàng miếng của Ngân hàng Nhà nước, cung cầu nội địa mất cân bằng, thuế nhập khẩu và chi phí gia công. Premium có thể từ 5-20 triệu VNĐ/lượng tùy thời điểm.'],
            ['q' => 'Nên mua vàng SJC ở đâu uy tín?', 'a' => 'Nên mua vàng SJC tại các đơn vị ủy quyền chính thức: Công ty SJC (sjc.com.vn), DOJI, PNJ, Bảo Tín Minh Châu, Phú Quý, và các ngân hàng có giấy phép kinh doanh vàng miếng. Tránh mua tại các cửa hàng không rõ nguồn gốc.'],
            ['q' => 'Chênh lệch giá mua vào và bán ra vàng SJC bao nhiêu?', 'a' => 'Chênh lệch giá mua-bán vàng SJC thường từ 500.000 đến 2.000.000 VNĐ/lượng tùy thời điểm và loại vàng. Vàng miếng 1 lượng có chênh lệch thấp nhất, các quy cách nhỏ hơn có chênh lệch cao hơn.'],
        ],
    ],
    'doji' => [
        'title' => 'Câu hỏi thường gặp về giá vàng DOJI',
        'faqs' => [
            ['q' => 'Giá vàng DOJI hôm nay bao nhiêu?', 'a' => 'Giá vàng DOJI được cập nhật liên tục trên trang này, bao gồm giá mua vào và bán ra của vàng miếng SJC và vàng nhẫn DOJI 9999 tại hệ thống DOJI toàn quốc.'],
            ['q' => 'Vàng nhẫn DOJI 9999 là gì?', 'a' => 'Vàng nhẫn DOJI 9999 là dòng vàng nhẫn tròn trơn có hàm lượng 99.99% vàng nguyên chất, do Tập đoàn DOJI sản xuất. Sản phẩm có trọng lượng từ 1 chỉ đến 5 chỉ, được cấp phiếu bảo đảm và tem chống giả.'],
            ['q' => 'Giá vàng DOJI khác gì so với giá SJC?', 'a' => 'Giá vàng miếng SJC tại DOJI thường bằng hoặc chênh lệch nhỏ so với giá SJC chính hãng. Tuy nhiên, giá vàng nhẫn DOJI 9999 thường thấp hơn vàng miếng SJC từ 8-12 triệu đồng/lượng, phù hợp cho nhà đầu tư nhỏ lẻ.'],
            ['q' => 'Hệ thống cửa hàng DOJI ở đâu?', 'a' => 'DOJI có hơn 200 cửa hàng trên toàn quốc, tập trung tại Hà Nội, TP.HCM, Đà Nẵng, Hải Phòng và nhiều tỉnh thành lớn. Tra cứu cửa hàng gần nhất tại website chính thức doji.vn.'],
        ],
    ],
    'pnj' => [
        'title' => 'Câu hỏi thường gặp về giá vàng PNJ',
        'faqs' => [
            ['q' => 'Giá vàng PNJ hôm nay bao nhiêu?', 'a' => 'Giá vàng PNJ được cập nhật liên tục trên trang này với giá mua vào và bán ra của vàng miếng SJC, vàng nhẫn PNJ 9999 và các sản phẩm vàng 24K tại hệ thống PNJ.'],
            ['q' => 'PNJ có bán vàng miếng SJC không?', 'a' => 'Có, PNJ là đại lý ủy quyền chính thức phân phối vàng miếng SJC. Giá vàng miếng SJC tại PNJ thường ngang bằng các đại lý khác như DOJI và Bảo Tín Minh Châu.'],
            ['q' => 'Vàng nhẫn PNJ 9999 có gì nổi bật?', 'a' => 'Vàng nhẫn PNJ 9999 có hàm lượng 99.99% vàng nguyên chất, mẫu mã đa dạng từ nhẫn tròn trơn đến nhẫn hoa văn. PNJ là doanh nghiệp niêm yết trên sàn HOSE, đảm bảo minh bạch về chất lượng và giá cả.'],
            ['q' => 'Chính sách mua lại vàng tại PNJ như thế nào?', 'a' => 'PNJ mua lại vàng nhẫn PNJ 9999 và vàng miếng SJC tại tất cả cửa hàng trên toàn quốc. Giá mua lại cạnh tranh, áp dụng chính sách rõ ràng. Khách hàng cần mang theo phiếu bảo đảm hoặc hóa đơn mua hàng.'],
            ['q' => 'PNJ có bao nhiêu cửa hàng?', 'a' => 'PNJ có hơn 400 cửa hàng trên toàn quốc, là hệ thống bán lẻ vàng bạc đá quý lớn nhất Việt Nam. Cửa hàng PNJ phân bố tại 63 tỉnh thành, thuận tiện cho việc mua bán và giao dịch vàng.'],
        ],
    ],
    'btmc' => [
        'title' => 'Câu hỏi thường gặp về giá vàng Bảo Tín Minh Châu',
        'faqs' => [
            ['q' => 'Giá vàng Bảo Tín Minh Châu hôm nay bao nhiêu?', 'a' => 'Giá vàng Bảo Tín Minh Châu được cập nhật liên tục trên trang này, bao gồm giá mua vào và bán ra của vàng miếng SJC, vàng nhẫn Rồng Thăng Long 9999 và các sản phẩm vàng 24K.'],
            ['q' => 'Vàng nhẫn Rồng Thăng Long là gì?', 'a' => 'Vàng nhẫn Rồng Thăng Long 9999 là sản phẩm chủ lực của Bảo Tín Minh Châu, được Ngân hàng Nhà nước cấp phép sản xuất. Nhẫn có trọng lượng từ 1 chỉ đến 5 chỉ, khắc logo Rồng Thăng Long và số seri, hàm lượng vàng 99.99%.'],
            ['q' => 'Bảo Tín Minh Châu có mấy cửa hàng?', 'a' => 'Bảo Tín Minh Châu có hệ thống cửa hàng tập trung tại Hà Nội và các tỉnh miền Bắc. Trụ sở chính và cửa hàng giao dịch lớn nhất tại phố Trần Nhân Tông, quận Hai Bà Trưng, Hà Nội.'],
            ['q' => 'So sánh giá vàng Bảo Tín Minh Châu với SJC?', 'a' => 'Giá vàng miếng SJC tại Bảo Tín Minh Châu thường sát giá thị trường. Giá vàng nhẫn Rồng Thăng Long 9999 thường thấp hơn vàng miếng SJC từ 8-15 triệu đồng/lượng, cạnh tranh với DOJI và PNJ.'],
        ],
    ],
    'phuquy' => [
        'title' => 'Câu hỏi thường gặp về giá vàng Phú Quý',
        'faqs' => [
            ['q' => 'Giá vàng Phú Quý hôm nay bao nhiêu?', 'a' => 'Giá vàng Phú Quý được cập nhật liên tục trên trang này, bao gồm giá mua vào và bán ra vàng miếng SJC, vàng nhẫn Phú Quý 9999 và các sản phẩm vàng 24K.'],
            ['q' => 'Tại sao giá vàng Phú Quý thường rẻ hơn?', 'a' => 'Phú Quý có chi phí vận hành thấp hơn các tập đoàn lớn, hệ thống cửa hàng tập trung tại Hà Nội nên tiết kiệm chi phí logistics. Vì vậy giá bán ra thường thuộc nhóm thấp nhất thị trường, hấp dẫn nhà đầu tư tích trữ.'],
            ['q' => 'Phú Quý có bán vàng nhẫn 9999 không?', 'a' => 'Có, Phú Quý kinh doanh vàng nhẫn tròn trơn 9999 với nhiều quy cách trọng lượng. Vàng nhẫn Phú Quý 9999 có chênh lệch mua-bán thấp, phí gia công hợp lý, phù hợp cho đầu tư dài hạn.'],
            ['q' => 'Mua vàng Phú Quý ở đâu?', 'a' => 'Hệ thống cửa hàng Phú Quý tập trung tại Hà Nội. Địa chỉ giao dịch chính tại khu vực trung tâm thành phố. Nhà đầu tư nên kiểm tra giá trên website trước khi đến giao dịch.'],
        ],
    ],
    'mihong' => [
        'title' => 'Câu hỏi thường gặp về giá vàng Mi Hồng',
        'faqs' => [
            ['q' => 'Giá vàng Mi Hồng hôm nay bao nhiêu?', 'a' => 'Giá vàng Mi Hồng được cập nhật liên tục trên trang này, bao gồm giá mua vào và bán ra vàng miếng SJC, vàng nhẫn 9999 và vàng nữ trang tại tiệm vàng Mi Hồng.'],
            ['q' => 'Tiệm vàng Mi Hồng ở đâu?', 'a' => 'Tiệm vàng Mi Hồng là thương hiệu truyền thống tại khu vực Chợ Lớn, Quận 5, TP.HCM. Mi Hồng nổi tiếng với lượng giao dịch vàng lớn mỗi ngày và giá niêm yết sát thị trường.'],
            ['q' => 'Giá vàng Mi Hồng có rẻ hơn SJC không?', 'a' => 'Giá vàng miếng SJC tại Mi Hồng thường ngang bằng giá thị trường. Tuy nhiên, chênh lệch mua-bán tại Mi Hồng thường thấp, giúp nhà đầu tư tiết kiệm chi phí khi giao dịch mua bán vàng.'],
            ['q' => 'Mi Hồng có mua lại vàng không?', 'a' => 'Có, Mi Hồng mua lại vàng miếng SJC, vàng nhẫn 9999 và vàng nữ trang các loại. Giá mua lại cạnh tranh, giao dịch nhanh chóng. Do lượng khách đông, giờ cao điểm có thể phải xếp hàng.'],
        ],
    ],
    'btmh' => [
        'title' => 'Câu hỏi thường gặp về giá vàng Bảo Tín Mạnh Hải',
        'faqs' => [
            ['q' => 'Giá vàng Bảo Tín Mạnh Hải hôm nay bao nhiêu?', 'a' => 'Giá vàng Bảo Tín Mạnh Hải được cập nhật liên tục trên trang này, bao gồm giá mua vào và bán ra của vàng miếng SJC, nhẫn tròn 9999 BTMH, nhẫn Rồng Thăng Long và các sản phẩm vàng 24K.'],
            ['q' => 'Bảo Tín Mạnh Hải có liên quan đến Bảo Tín Minh Châu không?', 'a' => 'Bảo Tín Mạnh Hải và Bảo Tín Minh Châu đều thuộc hệ thống thương hiệu Bảo Tín tại Hà Nội, nhưng là hai công ty hoạt động độc lập. Cả hai đều kinh doanh vàng miếng SJC và vàng nhẫn 9999 với giá niêm yết riêng.'],
            ['q' => 'Bảo Tín Mạnh Hải bán những sản phẩm vàng nào?', 'a' => 'Bảo Tín Mạnh Hải kinh doanh: vàng miếng SJC, nhẫn tròn 9999 BTMH, nhẫn ép vỉ Rồng Thăng Long, vàng trang sức 24K (999 và 9999), vàng Tiểu Kim Cát, và đồng vàng Kim Gia Bảo.'],
            ['q' => 'Cửa hàng Bảo Tín Mạnh Hải ở đâu?', 'a' => 'Bảo Tín Mạnh Hải có hệ thống cửa hàng tại Hà Nội, tập trung khu vực trung tâm. Trụ sở giao dịch chính tại phố Trần Nhân Tông, quận Hai Bà Trưng, Hà Nội. Giá niêm yết cập nhật liên tục trong giờ giao dịch.'],
        ],
    ],
    'ngoctham' => [
        'title' => 'Câu hỏi thường gặp về giá vàng Ngọc Thẩm',
        'faqs' => [
            ['q' => 'Giá vàng Ngọc Thẩm hôm nay bao nhiêu?', 'a' => 'Giá vàng Ngọc Thẩm được cập nhật liên tục trên trang này, bao gồm giá mua vào và bán ra của vàng miếng SJC, vàng nhẫn 9999, vàng ta 990-9999 và vàng 18K tại Ngọc Thẩm.'],
            ['q' => 'Ngọc Thẩm bán vàng loại nào?', 'a' => 'Ngọc Thẩm cung cấp đa dạng sản phẩm: vàng miếng SJC (10 chỉ), nhẫn tròn 9999, vàng ta 990, vàng ta 9999, vàng 18K (750), vàng trắng AU750. Là một trong ít thương hiệu vẫn kinh doanh vàng ta truyền thống.'],
            ['q' => 'Cửa hàng Ngọc Thẩm ở đâu?', 'a' => 'Ngọc Thẩm có hệ thống cửa hàng tại TP.HCM và các tỉnh phía Nam. Thương hiệu phục vụ chủ yếu khách hàng khu vực miền Nam với giá niêm yết cạnh tranh và dịch vụ chuyên nghiệp.'],
            ['q' => 'So sánh giá vàng Ngọc Thẩm với thương hiệu khác?', 'a' => 'Giá vàng miếng SJC tại Ngọc Thẩm thường ngang bằng các đại lý lớn. Giá vàng nhẫn và vàng ta 9999 tại Ngọc Thẩm cạnh tranh so với Mi Hồng và các thương hiệu phía Bắc. Nhà đầu tư nên so sánh giá giữa các thương hiệu.'],
        ],
    ],
];

$faqData = $brandFaqs[$brandKey] ?? null;
@endphp

@if ($faqData)
@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach ($faqData['faqs'] as $i => $faq)
        {
            "@@type": "Question",
            "name": @json($faq['q']),
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": @json($faq['a'])
            }
        }@if ($i < count($faqData['faqs']) - 1),@endif
        @endforeach
    ]
}
</script>
@endpush

<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-[#001061] mb-4">{{ $faqData['title'] }}</h2>
    <div class="divide-y divide-slate-200">
        @foreach ($faqData['faqs'] as $faq)
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>{{ $faq['q'] }}</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $faq['a'] }}</p>
        </details>
        @endforeach
    </div>
</div>
@endif
