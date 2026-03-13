{{-- Rich intro + FAQ text for world price detail pages. Receives $symbolKey --}}
@php
$introData = [
    'XAU/USD' => [
        'h2'    => 'Giá vàng XAU/USD là gì?',
        'intro' => 'XAU/USD là mã giao dịch quốc tế của vàng tính bằng đô la Mỹ (USD) trên thị trường spot. Đây là cặp tỷ giá được theo dõi nhiều nhất trên thế giới, phản ánh giá 1 troy ounce (31.1 gram) vàng nguyên chất 999.9. Giá XAU/USD được giao dịch 24/5 trên các sàn London, New York (COMEX), Tokyo và Sydney.',
        'sections' => [
            [
                'title' => 'Yếu tố ảnh hưởng đến giá vàng XAU/USD',
                'content' => 'Giá vàng XAU/USD chịu tác động bởi nhiều yếu tố vĩ mô: chính sách lãi suất của Cục Dự trữ Liên bang Mỹ (Fed), chỉ số đô la Mỹ (DXY), lạm phát toàn cầu, căng thẳng địa chính trị, và nhu cầu mua vàng dự trữ từ các ngân hàng trung ương. Khi lãi suất USD giảm hoặc căng thẳng gia tăng, giá vàng thường tăng mạnh.',
            ],
            [
                'title' => 'Mối quan hệ giữa XAU/USD và giá vàng Việt Nam',
                'content' => 'Giá vàng SJC và DOJI tại Việt Nam chịu ảnh hưởng trực tiếp từ XAU/USD thông qua tỷ giá USD/VND. Khi XAU/USD tăng kết hợp USD/VND tăng, giá vàng trong nước thường tăng gấp đôi biên độ. Tuy nhiên, do chính sách quản lý nhập khẩu vàng, giá vàng SJC thường có chênh lệch (premium) so với giá quy đổi từ thế giới.',
            ],
            [
                'title' => 'Cách đọc biểu đồ giá vàng XAU/USD',
                'content' => 'Biểu đồ trên trang này hiển thị giá trung bình (đường liền), giá cao nhất (đường xanh đứt) và giá thấp nhất (đường đỏ đứt) trong mỗi phiên giao dịch. Bạn có thể chọn các khung thời gian từ 7 ngày đến 1 năm để phân tích xu hướng ngắn hạn và dài hạn.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá vàng XAU/USD hôm nay bao nhiêu?', 'a' => 'Giá vàng XAU/USD được cập nhật liên tục trên trang này. Bảng giá hiển thị giá spot hiện tại, mức thay đổi trong ngày, cùng biểu đồ biến động theo nhiều khung thời gian.'],
            ['q' => 'XAU/USD giao dịch ở đâu và khi nào?', 'a' => 'Vàng XAU/USD được giao dịch trên thị trường OTC (over-the-counter) và các sàn giao dịch hàng hóa lớn như COMEX (New York), LBMA (London). Thị trường hoạt động 24 giờ/ngày từ thứ Hai đến thứ Sáu theo giờ GMT.'],
            ['q' => '1 troy ounce vàng bằng bao nhiêu gram?', 'a' => '1 troy ounce (oz) vàng bằng 31.1035 gram. Đây là đơn vị đo lường tiêu chuẩn quốc tế cho vàng và kim loại quý. Tại Việt Nam, vàng thường được tính theo đơn vị lượng (1 lượng = 37.5 gram).'],
            ['q' => 'Tại sao giá vàng SJC chênh lệch với giá thế giới?', 'a' => 'Chênh lệch (premium) giữa giá vàng SJC và giá thế giới quy đổi do nhiều yếu tố: thuế nhập khẩu, chi phí gia công, chính sách hạn chế nhập khẩu vàng miếng tại Việt Nam, và quan hệ cung-cầu nội địa.'],
        ],
    ],
    'XAU/EUR' => [
        'h2'    => 'Giá vàng XAU/EUR là gì?',
        'intro' => 'XAU/EUR là giá vàng quốc tế tính bằng đồng Euro (EUR), đồng tiền chung của Khu vực đồng Euro gồm 20 quốc gia châu Âu. Theo dõi XAU/EUR giúp nhà đầu tư châu Âu và những ai nắm giữ EUR đánh giá chính xác giá trị vàng mà không chịu ảnh hưởng từ biến động USD.',
        'sections' => [
            [
                'title' => 'Tại sao theo dõi giá vàng tính bằng EUR?',
                'content' => 'Khi đồng USD mạnh lên, giá XAU/USD có thể giảm nhưng XAU/EUR lại tăng nếu EUR yếu hơn USD. Do đó, XAU/EUR cung cấp góc nhìn khác biệt so với XAU/USD, đặc biệt quan trọng cho nhà đầu tư có thu nhập hoặc chi tiêu bằng EUR.',
            ],
            [
                'title' => 'Yếu tố ảnh hưởng đến XAU/EUR',
                'content' => 'Giá vàng XAU/EUR chịu tác động bởi chính sách tiền tệ của Ngân hàng Trung ương Châu Âu (ECB), lãi suất khu vực Eurozone, tỷ giá EUR/USD, lạm phát châu Âu, và các rủi ro địa chính trị tại khu vực.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá vàng XAU/EUR hôm nay bao nhiêu?', 'a' => 'Giá vàng XAU/EUR được cập nhật liên tục trên trang này, hiển thị giá spot tính bằng Euro cho 1 troy ounce vàng, cùng biểu đồ biến động và lịch sử giá.'],
            ['q' => 'XAU/EUR khác gì so với XAU/USD?', 'a' => 'XAU/EUR phản ánh giá vàng tính bằng Euro, còn XAU/USD tính bằng đô la Mỹ. Khi EUR/USD biến động, hai mã này có thể đi ngược chiều nhau. Nhà đầu tư nên theo dõi cả hai để có cái nhìn toàn diện.'],
            ['q' => 'Ai nên theo dõi giá vàng XAU/EUR?', 'a' => 'Nhà đầu tư có tài sản bằng EUR, người Việt Nam du học/làm việc tại châu Âu, hoặc doanh nghiệp xuất nhập khẩu sang khu vực EU nên theo dõi XAU/EUR để đánh giá đúng giá trị vàng.'],
        ],
    ],
    'XAU/GBP' => [
        'h2'    => 'Giá vàng XAU/GBP là gì?',
        'intro' => 'XAU/GBP là giá vàng quốc tế tính bằng đồng Bảng Anh (GBP). London là trung tâm giao dịch vàng lớn nhất thế giới với hệ thống London Bullion Market Association (LBMA), nơi thiết lập giá tham chiếu vàng toàn cầu hai lần mỗi ngày (London Gold Fix).',
        'sections' => [
            [
                'title' => 'Vai trò của London trong thị trường vàng',
                'content' => 'Thị trường vàng London (LBMA) xử lý khối lượng giao dịch vàng OTC lớn nhất thế giới. Giá vàng LBMA Gold Price (trước đây gọi là London Gold Fix) được công bố lúc 10:30 và 15:00 giờ London, là chuẩn tham chiếu cho giá vàng toàn cầu.',
            ],
            [
                'title' => 'Yếu tố ảnh hưởng đến XAU/GBP',
                'content' => 'Giá XAU/GBP phụ thuộc vào chính sách lãi suất của Ngân hàng Trung ương Anh (Bank of England), tỷ giá GBP/USD, tình hình kinh tế Vương quốc Anh sau Brexit, và các yếu tố vĩ mô toàn cầu.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá vàng XAU/GBP hôm nay bao nhiêu?', 'a' => 'Giá vàng XAU/GBP được cập nhật liên tục trên trang này, hiển thị giá spot tính bằng Bảng Anh cho 1 troy ounce vàng nguyên chất.'],
            ['q' => 'Tại sao giá vàng tính bằng GBP quan trọng?', 'a' => 'London là trung tâm giao dịch vàng lớn nhất thế giới. Giá XAU/GBP phản ánh giá trị vàng tại thị trường giao dịch gốc, không bị ảnh hưởng bởi biến động USD.'],
            ['q' => 'LBMA Gold Price là gì?', 'a' => 'LBMA Gold Price là giá tham chiếu vàng quốc tế, được xác định hai lần mỗi ngày tại London thông qua phiên đấu giá điện tử. Giá này được sử dụng rộng rãi trong hợp đồng, giao dịch và định giá vàng toàn cầu.'],
        ],
    ],
    'XAU/CNY' => [
        'h2'    => 'Giá vàng XAU/CNY là gì?',
        'intro' => 'XAU/CNY là giá vàng quốc tế tính bằng đồng Nhân dân tệ (CNY) của Trung Quốc. Trung Quốc là quốc gia tiêu thụ và sản xuất vàng lớn nhất thế giới, với Sàn Giao dịch Vàng Thượng Hải (SGE) đóng vai trò trung tâm giao dịch vàng hàng đầu châu Á.',
        'sections' => [
            [
                'title' => 'Trung Quốc và thị trường vàng toàn cầu',
                'content' => 'Trung Quốc tiêu thụ hơn 800 tấn vàng mỗi năm và Ngân hàng Nhân dân Trung Quốc (PBOC) là một trong những ngân hàng trung ương tích cực mua vàng dự trữ nhất thế giới. Nhu cầu từ Trung Quốc có tác động đáng kể đến giá vàng quốc tế.',
            ],
            [
                'title' => 'Yếu tố ảnh hưởng đến XAU/CNY',
                'content' => 'Giá XAU/CNY chịu tác động bởi chính sách tiền tệ PBOC, tỷ giá USD/CNY, nhu cầu vàng trang sức và đầu tư tại Trung Quốc, cùng các chính sách nhập khẩu vàng do chính phủ kiểm soát.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá vàng XAU/CNY hôm nay bao nhiêu?', 'a' => 'Giá vàng XAU/CNY được cập nhật liên tục trên trang này, hiển thị giá spot tính bằng Nhân dân tệ cho 1 troy ounce vàng.'],
            ['q' => 'Tại sao thị trường vàng Trung Quốc quan trọng?', 'a' => 'Trung Quốc là quốc gia tiêu thụ vàng lớn nhất thế giới. Sàn Giao dịch Vàng Thượng Hải (SGE) là sàn giao dịch vàng vật chất lớn nhất, và nhu cầu từ Trung Quốc có ảnh hưởng mạnh mẽ đến giá vàng quốc tế.'],
            ['q' => 'Shanghai Gold Premium là gì?', 'a' => 'Shanghai Gold Premium là mức chênh lệch giữa giá vàng trên Sàn Thượng Hải và giá quốc tế. Khi premium cao, phản ánh nhu cầu vàng mạnh tại Trung Quốc và ngược lại.'],
        ],
    ],
    'XAU/JPY' => [
        'h2'    => 'Giá vàng XAU/JPY là gì?',
        'intro' => 'XAU/JPY là giá vàng quốc tế tính bằng đồng Yên Nhật (JPY). Nhật Bản có truyền thống dự trữ vàng lâu đời, và Sàn Giao dịch Hàng hóa Tokyo (TOCOM) là một trong những sàn giao dịch vàng futures lớn nhất châu Á.',
        'sections' => [
            [
                'title' => 'Đặc điểm giá vàng tính bằng Yên Nhật',
                'content' => 'Do đồng Yên Nhật cũng là tài sản an toàn (safe haven), mối quan hệ giữa XAU/JPY phức tạp hơn các cặp khác. Khi thị trường biến động, cả vàng và JPY đều tăng giá, khiến XAU/JPY có biên độ biến động thấp hơn XAU/USD.',
            ],
            [
                'title' => 'Yếu tố ảnh hưởng đến XAU/JPY',
                'content' => 'Giá XAU/JPY phụ thuộc vào chính sách tiền tệ của Ngân hàng Trung ương Nhật Bản (BOJ), tỷ giá USD/JPY, chính sách kiểm soát đường cong lợi suất (YCC), và nhu cầu trú ẩn an toàn trên thị trường.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá vàng XAU/JPY hôm nay bao nhiêu?', 'a' => 'Giá vàng XAU/JPY được cập nhật liên tục trên trang này, hiển thị giá spot tính bằng Yên Nhật cho 1 troy ounce vàng nguyên chất.'],
            ['q' => 'Tại sao XAU/JPY biến động khác XAU/USD?', 'a' => 'Đồng Yên Nhật cũng là tài sản trú ẩn an toàn như vàng. Khi thị trường bất ổn, cả vàng và JPY đều tăng, khiến XAU/JPY biến động ít hơn. Ngược lại, khi BOJ nới lỏng tiền tệ làm JPY yếu, XAU/JPY tăng mạnh.'],
            ['q' => 'TOCOM là gì?', 'a' => 'TOCOM (Tokyo Commodity Exchange) là sàn giao dịch hàng hóa tại Tokyo, nơi giao dịch hợp đồng tương lai vàng, bạc và các kim loại quý khác. Đây là sàn giao dịch vàng futures lớn nhất châu Á.'],
        ],
    ],
    'XAG/USD' => [
        'h2'    => 'Giá bạc XAG/USD là gì?',
        'intro' => 'XAG/USD là giá bạc quốc tế tính bằng đô la Mỹ trên thị trường spot. Bạc vừa là kim loại quý dùng cho trang sức và đầu tư, vừa là kim loại công nghiệp quan trọng được sử dụng rộng rãi trong sản xuất pin năng lượng mặt trời, điện tử, y tế và ngành ô tô.',
        'sections' => [
            [
                'title' => 'Bạc — kim loại quý và công nghiệp',
                'content' => 'Khác với vàng, khoảng 50% nhu cầu bạc đến từ công nghiệp. Bạc là chất dẫn điện và nhiệt tốt nhất trong các kim loại, được dùng trong panel năng lượng mặt trời, chip điện tử, kháng khuẩn y tế. Xu hướng chuyển đổi năng lượng xanh đang đẩy nhu cầu bạc công nghiệp tăng mạnh.',
            ],
            [
                'title' => 'Tỷ lệ vàng/bạc (Gold/Silver Ratio)',
                'content' => 'Tỷ lệ Gold/Silver Ratio cho biết cần bao nhiêu ounce bạc để mua 1 ounce vàng. Trung bình lịch sử khoảng 60-80. Khi tỷ lệ cao (trên 80), bạc được coi là rẻ tương đối so với vàng. Nhà đầu tư thường dùng chỉ số này để quyết định phân bổ giữa vàng và bạc.',
            ],
            [
                'title' => 'Yếu tố ảnh hưởng đến giá bạc',
                'content' => 'Giá bạc XAG/USD chịu tác động kép: vừa theo xu hướng kim loại quý (giống vàng — phản ứng với lãi suất, USD, lạm phát), vừa theo nhu cầu công nghiệp (sản xuất pin mặt trời, điện tử). Bạc thường biến động mạnh hơn vàng tới 2-3 lần.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá bạc XAG/USD hôm nay bao nhiêu?', 'a' => 'Giá bạc XAG/USD được cập nhật liên tục trên trang này, hiển thị giá spot tính bằng USD cho 1 troy ounce bạc nguyên chất, cùng biểu đồ biến động và lịch sử giá.'],
            ['q' => 'Bạc có phải kênh đầu tư tốt không?', 'a' => 'Bạc là kênh đầu tư hấp dẫn do giá thấp hơn vàng nhiều lần, nhu cầu công nghiệp tăng (năng lượng mặt trời, xe điện), và tiềm năng tăng giá khi tỷ lệ Gold/Silver Ratio giảm. Tuy nhiên, bạc biến động mạnh hơn vàng.'],
            ['q' => 'Tỷ lệ Gold/Silver Ratio là gì?', 'a' => 'Gold/Silver Ratio là số ounce bạc cần để mua 1 ounce vàng. Ví dụ: nếu vàng 3.000 USD và bạc 37.5 USD thì ratio = 80. Ratio cao nghĩa là bạc rẻ tương đối so với vàng, và ngược lại.'],
        ],
    ],
    'XPT/USD' => [
        'h2'    => 'Giá bạch kim XPT/USD là gì?',
        'intro' => 'XPT/USD là giá bạch kim (platinum) quốc tế tính bằng đô la Mỹ. Bạch kim là kim loại quý hiếm hơn vàng khoảng 30 lần, được dùng chủ yếu trong bộ chuyển đổi xúc tác ô tô (catalytic converter), trang sức cao cấp, và công nghiệp hóa chất.',
        'sections' => [
            [
                'title' => 'Ứng dụng chính của bạch kim',
                'content' => 'Khoảng 40% bạch kim toàn cầu được dùng cho ngành ô tô (bộ lọc khí thải xe diesel), 30% cho trang sức, và phần còn lại cho công nghiệp hóa chất, điện tử, y tế. Xu hướng chuyển sang pin nhiên liệu hydrogen đang mở ra nhu cầu mới cho bạch kim.',
            ],
            [
                'title' => 'Nguồn cung và sản xuất bạch kim',
                'content' => 'Nam Phi chiếm khoảng 70% sản lượng bạch kim thế giới, tiếp theo là Nga (~12%) và Zimbabwe (~8%). Nguồn cung tập trung khiến giá bạch kim nhạy cảm với các sự kiện tại Nam Phi như đình công, mất điện, và thay đổi chính sách khai thác mỏ.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá bạch kim XPT/USD hôm nay bao nhiêu?', 'a' => 'Giá bạch kim XPT/USD được cập nhật liên tục trên trang này, hiển thị giá spot USD cho 1 troy ounce bạch kim nguyên chất, cùng biểu đồ biến động và lịch sử giá.'],
            ['q' => 'Bạch kim hiếm hơn vàng phải không?', 'a' => 'Đúng, bạch kim hiếm hơn vàng khoảng 30 lần. Tổng lượng bạch kim được khai thác hàng năm chỉ khoảng 190 tấn, so với khoảng 3.600 tấn vàng. Tuy nhiên, giá bạch kim hiện thấp hơn vàng do nhu cầu đầu tư thấp hơn.'],
            ['q' => 'Tại sao bạch kim rẻ hơn vàng dù hiếm hơn?', 'a' => 'Giá bạch kim thấp hơn vàng do nhu cầu đầu tư (ETF, ngân hàng trung ương) vào vàng lớn hơn nhiều. Bạch kim phụ thuộc nhiều vào nhu cầu công nghiệp, đặc biệt ngành ô tô diesel đang suy giảm.'],
        ],
    ],
    'XPD/USD' => [
        'h2'    => 'Giá palladium XPD/USD là gì?',
        'intro' => 'XPD/USD là giá palladium quốc tế tính bằng đô la Mỹ. Palladium là kim loại quý thuộc nhóm Platinum (PGM), được sử dụng chủ yếu trong bộ chuyển đổi xúc tác cho xe xăng (catalytic converter), ngành điện tử, và nha khoa.',
        'sections' => [
            [
                'title' => 'Palladium trong ngành ô tô',
                'content' => 'Hơn 80% palladium được dùng cho bộ lọc khí thải xe hơi chạy xăng (gasoline). Khi các quốc gia thắt chặt tiêu chuẩn khí thải (Euro 7, China 7), nhu cầu palladium trong mỗi xe tăng lên. Tuy nhiên, xu hướng xe điện (EV) đang đe dọa nhu cầu dài hạn.',
            ],
            [
                'title' => 'Nguồn cung palladium',
                'content' => 'Nga (Norilsk Nickel) chiếm khoảng 40% sản lượng palladium thế giới, Nam Phi ~35%. Nguồn cung tập trung cao khiến giá palladium nhạy cảm với rủi ro địa chính trị, đặc biệt lệnh trừng phạt Nga và tình hình khai thác tại Nam Phi.',
            ],
        ],
        'faqs' => [
            ['q' => 'Giá palladium XPD/USD hôm nay bao nhiêu?', 'a' => 'Giá palladium XPD/USD được cập nhật liên tục trên trang này, hiển thị giá spot USD cho 1 troy ounce palladium, cùng biểu đồ biến động và lịch sử giá chi tiết.'],
            ['q' => 'Palladium khác gì so với bạch kim?', 'a' => 'Cả hai đều thuộc nhóm kim loại bạch kim (PGM) nhưng palladium chủ yếu dùng cho xe xăng, trong khi bạch kim cho xe diesel. Palladium nhẹ hơn, có màu trắng sáng hơn, và giá thường biến động mạnh hơn bạch kim.'],
            ['q' => 'Xe điện có ảnh hưởng đến giá palladium không?', 'a' => 'Có, xe điện (EV) không sử dụng bộ lọc khí thải nên không cần palladium. Xu hướng điện hóa phương tiện dự kiến giảm nhu cầu palladium dài hạn, nhưng quá trình chuyển đổi còn nhiều năm nữa mới hoàn thành.'],
        ],
    ],
];

$data = $introData[$symbolKey] ?? null;
@endphp

@if ($data)
@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach ($data['faqs'] as $i => $faq)
        {
            "@@type": "Question",
            "name": @json($faq['q']),
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": @json($faq['a'])
            }
        }@if ($i < count($data['faqs']) - 1),@endif
        @endforeach
    ]
}
</script>
@endpush
<div class="mt-5 rounded-sm border border-slate-200 bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-[#001061] mb-3">{{ $data['h2'] }}</h2>
    <p class="text-sm leading-relaxed text-slate-700 mb-4">{{ $data['intro'] }}</p>

    @foreach ($data['sections'] as $section)
    <h3 class="text-base font-bold text-slate-800 mt-4 mb-2">{{ $section['title'] }}</h3>
    <p class="text-sm leading-relaxed text-slate-600">{{ $section['content'] }}</p>
    @endforeach
</div>

{{-- FAQ Section --}}
@if (!empty($data['faqs']))
<div class="mt-5 rounded-sm border border-slate-200 bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-[#001061] mb-4">Câu hỏi thường gặp về {{ $symbolKey }}</h2>
    <div class="divide-y divide-slate-200">
        @foreach ($data['faqs'] as $faq)
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
@endif
