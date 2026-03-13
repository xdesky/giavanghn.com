<?php

/**
 * Seed test news articles and an analysis article for today (gold price drop scenario).
 * Usage: php scripts/seed_test_news.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NewsArticle;
use App\Models\AnalysisArticle;
use Illuminate\Support\Str;

$now = now();

// ── News Articles (Tin vắn) ──
$newsItems = [
    [
        'tag'          => 'Nong',
        'title'        => 'Giá vàng SJC giảm 500.000đ/lượng trong phiên sáng 09/03, mất mốc 92 triệu',
        'summary'      => 'Giá vàng SJC bất ngờ giảm mạnh 500.000đ/lượng xuống còn 91.5 triệu đồng/lượng trong phiên giao dịch sáng nay, sau chuỗi tăng liên tiếp 5 phiên.',
        'url'          => null,
        'source'       => 'giavanghn',
        'impact'       => 'negative',
        'published_at' => $now->copy()->subMinutes(5),
    ],
    [
        'tag'          => 'Quoc te',
        'title'        => 'Vàng thế giới lao dốc sau báo cáo việc làm Mỹ vượt kỳ vọng, XAU/USD về $2,880',
        'summary'      => 'Báo cáo Non-Farm Payrolls tháng 2/2026 ghi nhận 315.000 việc làm mới, vượt xa dự báo 200.000, khiến kỳ vọng Fed cắt giảm lãi suất suy yếu.',
        'url'          => null,
        'source'       => 'kitco',
        'impact'       => 'negative',
        'published_at' => $now->copy()->subMinutes(20),
    ],
    [
        'tag'          => 'Phan tich',
        'title'        => 'Chỉ số DXY tăng vọt lên 104.8, gây áp lực lớn lên giá vàng toàn cầu',
        'summary'      => 'Đồng USD mạnh lên đáng kể sau dữ liệu kinh tế Mỹ khả quan, đẩy chỉ số Dollar Index vượt ngưỡng 104.5.',
        'url'          => null,
        'source'       => 'vnexpress',
        'impact'       => 'negative',
        'published_at' => $now->copy()->subMinutes(35),
    ],
    [
        'tag'          => 'Trong nuoc',
        'title'        => 'Các tiệm vàng đồng loạt hạ giá bán, chênh lệch mua-bán SJC thu hẹp còn 1 triệu',
        'summary'      => 'Áp lực bán ra gia tăng tại các cửa hàng vàng lớn trong phiên sáng, khoảng cách mua-bán thu hẹp đáng kể.',
        'url'          => null,
        'source'       => 'cafef',
        'impact'       => 'negative',
        'published_at' => $now->copy()->subMinutes(50),
    ],
    [
        'tag'          => 'Du bao',
        'title'        => 'Chuyên gia dự báo vàng có thể tiếp tục điều chỉnh về vùng 90-91 triệu/lượng',
        'summary'      => 'Nhóm phân tích của DOJI nhận định giá vàng đang trong đà điều chỉnh ngắn hạn và có thể test vùng hỗ trợ 90 triệu đồng/lượng.',
        'url'          => null,
        'source'       => 'doji',
        'impact'       => 'negative',
        'published_at' => $now->copy()->subHour(),
    ],
    [
        'tag'          => 'Vi mo',
        'title'        => 'Lợi suất trái phiếu Mỹ 10 năm tăng lên 4.35%, tạo sức ép lên kim loại quý',
        'summary'      => 'Lãi suất trái phiếu chính phủ Mỹ kỳ hạn 10 năm tăng 8 điểm cơ bản, khiến chi phí cơ hội nắm giữ vàng tăng cao.',
        'url'          => null,
        'source'       => 'bloomberg',
        'impact'       => 'negative',
        'published_at' => $now->copy()->subHours(2),
    ],
];

$newsCount = 0;
foreach ($newsItems as $item) {
    NewsArticle::create($item);
    $newsCount++;
    echo "  + News: {$item['title']}\n";
}

// ── Analysis Article (Bản tin phân tích) ──
$slug = 'gia-vang-giam-manh-09-03-2026-' . $now->format('His');
$title = 'Giá vàng hôm nay 09/03/2026: SJC giảm 500.000đ, vàng thế giới mất mốc $2,900';

$content = <<<'HTML'
<h2>Tổng quan thị trường vàng ngày 09/03/2026</h2>
<p>Phiên giao dịch sáng ngày 09/03/2026, giá vàng trong nước và thế giới đồng loạt giảm mạnh sau báo cáo việc làm Mỹ tháng 2 vượt kỳ vọng, làm suy yếu kỳ vọng Fed cắt giảm lãi suất.</p>

<h3>Giá vàng SJC</h3>
<ul>
<li><strong>Bán ra:</strong> 91.500.000 đ/lượng (giảm 500.000đ so với phiên trước)</li>
<li><strong>Mua vào:</strong> 90.500.000 đ/lượng (giảm 500.000đ)</li>
<li>Chênh lệch mua-bán: 1.000.000đ/lượng</li>
</ul>

<h3>Giá vàng thế giới</h3>
<ul>
<li><strong>XAU/USD:</strong> $2,880/oz (giảm $38 so với phiên trước)</li>
<li>Quy đổi: ~88.2 triệu đồng/lượng</li>
<li>Chênh lệch SJC – thế giới: ~3.3 triệu đồng/lượng</li>
</ul>

<h3>Nguyên nhân giá vàng giảm</h3>
<ol>
<li><strong>Báo cáo NFP vượt kỳ vọng:</strong> Non-Farm Payrolls tháng 2/2026 đạt 315.000 việc làm mới (dự báo 200.000), cho thấy thị trường lao động Mỹ vẫn rất mạnh.</li>
<li><strong>USD tăng mạnh:</strong> Chỉ số DXY vượt 104.8, tạo áp lực lớn lên giá vàng.</li>
<li><strong>Lợi suất trái phiếu tăng:</strong> US 10Y yield lên 4.35%, tăng chi phí cơ hội nắm giữ vàng.</li>
<li><strong>Kỳ vọng Fed hawkish:</strong> Thị trường giảm kỳ vọng cắt giảm lãi suất tại cuộc họp tháng 5 xuống 25% (từ 65%).</li>
</ol>

<h3>Giá vàng các thương hiệu trong nước</h3>
<table>
<thead>
<tr><th>Thương hiệu</th><th>Mua vào</th><th>Bán ra</th><th>Thay đổi</th></tr>
</thead>
<tbody>
<tr><td>SJC</td><td>90.50</td><td>91.50</td><td style="color:red">-500k</td></tr>
<tr><td>BTMC</td><td>90.20</td><td>91.30</td><td style="color:red">-400k</td></tr>
<tr><td>PNJ</td><td>90.10</td><td>91.20</td><td style="color:red">-450k</td></tr>
<tr><td>DOJI</td><td>90.00</td><td>91.30</td><td style="color:red">-350k</td></tr>
<tr><td>Phú Quý</td><td>90.15</td><td>91.35</td><td style="color:red">-300k</td></tr>
</tbody>
</table>

<h3>Nhận định kỹ thuật</h3>
<p>Giá vàng SJC đã phá vỡ ngưỡng hỗ trợ ngắn hạn 92 triệu đồng/lượng. Vùng hỗ trợ tiếp theo nằm tại 90-91 triệu. Nếu mất mốc 90 triệu, giá có thể tiếp tục điều chỉnh về 88.5-89 triệu đồng/lượng.</p>
<p>Đối với vàng thế giới, vùng hỗ trợ $2,850-2,860 là mức quan trọng cần theo dõi. RSI(14) đã giảm từ 72 xuống 45, cho thấy đà tăng đang suy yếu.</p>

<h3>Dự báo ngắn hạn</h3>
<p>Trong tuần tới, giá vàng có thể tiếp tục chịu áp lực giảm nếu:</p>
<ul>
<li>Dữ liệu CPI Mỹ (công bố 12/03) cao hơn kỳ vọng</li>
<li>Fed có phát biểu hawkish</li>
<li>DXY duy trì trên mốc 104</li>
</ul>
<p><strong>Khuyến nghị:</strong> Nhà đầu tư nên thận trọng, chờ tín hiệu rõ ràng hơn trước khi mua vào. Vùng hỗ trợ 90-91 triệu là mức có thể cân nhắc tích lũy dần.</p>
HTML;

$summary = 'Giá vàng SJC giảm 500.000đ/lượng xuống 91.5 triệu trong phiên sáng 09/03. Vàng thế giới mất mốc $2,900 sau báo cáo NFP Mỹ vượt kỳ vọng. DXY tăng mạnh lên 104.8, lợi suất trái phiếu tăng tạo áp lực lên kim loại quý.';

$analysis = AnalysisArticle::create([
    'title'           => $title,
    'slug'            => Str::limit($slug, 540, ''),
    'trigger_type'    => 'change',
    'analysis_date'   => $now->toDateString(),
    'price_signature' => hash('sha256', 'test-drop-' . $now->toDateString()),
    'word_count'      => str_word_count(strip_tags($content)),
    'thumbnail_path'  => null,
    'summary'         => $summary,
    'content'         => $content,
    'meta'            => [
        'generated_at'     => $now->toDateTimeString(),
        'top_brands_count' => 8,
        'sentiment'        => 'Sợ hãi',
        'trigger_reason'   => 'price_change_detected',
    ],
    'published_at'    => $now,
]);

echo "\n  + Analysis: {$title}\n";
echo "\nDone! Created:\n";
echo "  - {$newsCount} news articles (tin vắn)\n";
echo "  - 1 analysis article (bản tin phân tích)\n";
echo "  - Analysis ID: {$analysis->id}, slug: {$analysis->slug}\n";
