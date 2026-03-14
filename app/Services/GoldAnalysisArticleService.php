<?php

namespace App\Services;

use App\Models\AnalysisArticle;
use App\Models\NewsArticle;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoldAnalysisArticleService
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function generate(string $triggerType = 'change', ?Carbon $at = null, bool $force = false): ?AnalysisArticle
    {
        $at = $at ?: now();
        $analysisDate = $at->toDateString();

        if (!in_array($triggerType, ['daily', 'change', 'summary'], true)) {
            throw new \InvalidArgumentException('triggerType must be daily, change or summary');
        }

        $snapshot = $this->dashboardService->buildSnapshot();
        $signature = $this->buildPriceSignature($snapshot);

        if (!$force) {
            if (in_array($triggerType, ['daily', 'summary'], true)) {
                $existing = AnalysisArticle::where('trigger_type', $triggerType)
                    ->whereDate('analysis_date', $analysisDate)
                    ->exists();

                if ($existing) {
                    return null;
                }
            }

            $duplicate = AnalysisArticle::where('trigger_type', $triggerType)
                ->whereDate('analysis_date', $analysisDate)
                ->where('price_signature', $signature)
                ->exists();

            if ($duplicate) {
                return null;
            }
        }

        $title = $this->buildTitle($snapshot, $triggerType, $at);
        $thumbnailPath = $this->createPriceTableThumbnail($snapshot, $triggerType, $at);
        $content = $triggerType === 'summary'
            ? $this->buildDailySummaryContent($snapshot, $at, $thumbnailPath)
            : $this->buildLongFormContent($snapshot, $triggerType, $at, $thumbnailPath);
        $wordCount = $this->countWords($content);
        $summary = $this->buildSummary($content);

        $slug = Str::slug($title) . '-' . $at->format('His');

        $article = AnalysisArticle::create([
            'title' => $title,
            'slug' => Str::limit($slug, 540, ''),
            'trigger_type' => $triggerType,
            'analysis_date' => $analysisDate,
            'price_signature' => $signature,
            'word_count' => $wordCount,
            'thumbnail_path' => $thumbnailPath,
            'summary' => $summary,
            'content' => $content,
            'meta' => [
                'generated_at' => $at->toDateTimeString(),
                'top_brands_count' => count($snapshot['topBrands'] ?? []),
                'sentiment' => $snapshot['sentiment']['fearGreedLabel'] ?? null,
                'trigger_reason' => match ($triggerType) {
                    'daily' => 'scheduled_daily_analysis',
                    'summary' => 'scheduled_daily_summary',
                    default => 'price_change_detected',
                },
            ],
            'tags' => $this->buildTags($snapshot, $triggerType),
            'published_at' => now(),
        ]);

        // After creation, sync a giavanghn news entry pointing to this article
        $this->syncNewsEntry($article, $triggerType, $at);

        return $article;
    }

    private function syncNewsEntry(AnalysisArticle $article, string $triggerType, Carbon $at): void
    {
        $url = '/tin-tuc-gia-vang/trong-nuoc/' . $article->slug;
        $tag = match ($triggerType) {
            'daily' => 'Phân tích',
            'summary' => 'Tổng hợp',
            default => 'Nong',
        };

        // Update existing giavanghn news for same date, or create new one
        $existing = NewsArticle::where('source', 'giavanghn')
            ->whereDate('published_at', $at->toDateString())
            ->where('url', 'like', '/tin-tuc-gia-vang/trong-nuoc/%')
            ->first();

        if ($existing) {
            $existing->update(['url' => $url, 'title' => $article->title]);
        } else {
            NewsArticle::create([
                'tag' => $tag,
                'title' => $article->title,
                'url' => $url,
                'source' => 'giavanghn',
                'impact' => 'neutral',
                'published_at' => $at,
            ]);
        }
    }

    private function buildPriceSignature(array $snapshot): string
    {
        $rows = [];

        foreach (($snapshot['topBrands'] ?? []) as $item) {
            $rows[] = [
                'brand' => $item['brand'] ?? '',
                'buy' => (float) ($item['buy'] ?? 0),
                'sell' => (float) ($item['sell'] ?? 0),
                'change' => (float) ($item['change'] ?? 0),
            ];
        }

        if (empty($rows)) {
            $rows[] = ['brand' => 'fallback', 'buy' => 0, 'sell' => 0, 'change' => 0];
        }

        return hash('sha256', json_encode($rows, JSON_UNESCAPED_UNICODE));
    }

    private function buildTitle(array $snapshot, string $triggerType, Carbon $at): string
    {
        $sjc = $snapshot['sjcCard']['variants']['p0']['sell'] ?? null;
        $movement = $snapshot['sjcCard']['trendPercent'] ?? 0;

        if ($triggerType === 'summary') {
            return sprintf('Giá Vàng Ngày %s', $at->format('d/m/Y'));
        }

        $prefix = $triggerType === 'daily'
            ? 'Bản Tin Phân Tích Giá Vàng Trong Ngày'
            : 'Cập Nhật Nhanh Biến Động Giá Vàng';

        $trend = $movement >= 0 ? 'tăng' : 'giảm';
        $headline = $sjc
            ? sprintf('SJC %.2f triệu/lượng %s (%+.2f%%)', $sjc, $trend, $movement)
            : 'Toàn cảnh thị trường vàng trong nước và thế giới';

        return sprintf('%s %s - %s', $prefix, $at->format('d/m/Y'), $headline);
    }

    private function buildSummary(string $content): string
    {
        // Remove TOC nav block before extracting summary text
        $content = preg_replace('/<nav[^>]*>.*?<\/nav>/s', '', $content);
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($content)));
        return Str::words($clean, 30, '...');
    }

    private function buildLongFormContent(array $snapshot, string $triggerType, Carbon $at, ?string $thumbnailPath = null): string
    {
        $dateLabel = $at->format('d/m/Y H:i');
        $dateShort = $at->format('d/m/Y');
        $sentiment = $snapshot['sentiment'] ?? [];
        $globalMarkets = $snapshot['globalMarkets'] ?? [];
        $topBrands = $snapshot['topBrands'] ?? [];
        $forecast = $snapshot['forecast'] ?? [];
        $macroFactors = $snapshot['macroFactors'] ?? [];
        $comparisons = $snapshot['comparisons'] ?? [];

        $sections = [];

        // --- Table of contents ---
        $tocItems = [
            'toan-canh' => 'Toàn cảnh thị trường vàng',
            'bang-gia' => 'Bảng giá vàng theo thương hiệu',
            'phan-tich-thuong-hieu' => 'Phân tích chi tiết theo thương hiệu',
            'thuong-hieu-noi-dia' => 'Giá vàng thương hiệu nội địa ngoài SJC',
            'quoc-te' => 'Liên thông với thị trường quốc tế',
            'tam-ly' => 'Tâm lý thị trường và động lượng giá',
            'vi-mo' => 'Các yếu tố vĩ mô đáng chú ý',
            'so-sanh' => 'So sánh, tương quan và định vị giá',
            'du-bao' => 'Kịch bản dự báo và kế hoạch hành động',
            'rui-ro' => 'Khung quản trị rủi ro',
        ];

        $toc = '<nav class="not-prose bg-slate-50 rounded-sm p-4 mb-6 border border-slate-200">'
            . '<p class="font-semibold text-slate-800 mb-2">📑 Mục lục bài viết</p>'
            . '<ol class="list-decimal list-inside space-y-1 text-sm text-blue-700">';
        foreach ($tocItems as $id => $label) {
            $toc .= '<li><a href="#' . $id . '" class="hover:underline">' . $label . '</a></li>';
        }
        $toc .= '</ol></nav>';
        $sections[] = $toc;

        // --- Section 1: Overview ---
        $sections[] = '<h2 id="toan-canh">Toàn cảnh thị trường vàng</h2>';

        $introText = $triggerType === 'daily'
            ? "Bản tin phân tích giá vàng hôm nay <strong>{$dateLabel}</strong> tập trung vào tính liên tục của xu hướng, từ đó rút ra mức độ ổn định của mặt bằng giá và điểm nhấn quan trọng trong phiên."
            : "Cập nhật nhanh <strong>biến động giá vàng</strong> lúc {$dateLabel} &ndash; bài viết phân tích nguyên nhân gây thay đổi, mức lan tỏa giữa các thương hiệu, và nhịp phản ứng của dòng tiền.";

        $sections[] = "<p>{$introText}</p>";
        $sections[] = '<p>Mục tiêu của bản phân tích là giúp nhà đầu tư có một khung tham chiếu rõ ràng: <strong>giá vàng hiện tại</strong>, mức biến động, trạng thái tâm lý thị trường, và các kịch bản có khả năng xảy ra trong ngắn hạn.</p>';
        $sections[] = '<p>Khác với bản tin ngắn, bài phân tích dài tập trung vào logic vận động của thị trường, liên kết số liệu giữa các thương hiệu nội địa với thị trường thế giới, đồng thời đánh giá tác động của tỷ giá, lạm phát, và tâm lý rủi ro.</p>';

        // --- Internal links ---
        $internalLinks = [
            ['url' => '/gia-vang-hom-nay', 'anchor' => 'giá vàng hôm nay', 'context' => 'Xem thêm cập nhật <a href="/gia-vang-hom-nay" title="Giá vàng hôm nay" class="article-link">giá vàng hôm nay</a> để theo dõi biến động theo thời gian thực.'],
            ['url' => '/gia-vang-hom-nay', 'anchor' => 'bảng giá vàng', 'context' => 'Tham khảo <a href="/gia-vang-hom-nay" title="Bảng giá vàng" class="article-link">bảng giá vàng</a> đầy đủ theo từng thương hiệu và khu vực giao dịch.'],
            ['url' => '/gia-vang-the-gioi', 'anchor' => 'giá vàng thế giới', 'context' => 'Theo dõi diễn biến <a href="/gia-vang-the-gioi" title="Giá vàng thế giới" class="article-link">giá vàng thế giới</a> (XAU/USD) và các thị trường quốc tế liên quan.'],
            ['url' => '/bieu-do-gia-vang', 'anchor' => 'biểu đồ giá vàng', 'context' => 'Phân tích xu hướng qua <a href="/bieu-do-gia-vang" title="Biểu đồ giá vàng" class="article-link">biểu đồ giá vàng</a> theo nhiều khung thời gian.'],
            ['url' => '/so-sanh-gia-vang', 'anchor' => 'so sánh giá vàng', 'context' => 'Đánh giá chênh lệch giữa các kênh tại trang <a href="/so-sanh-gia-vang" title="So sánh giá vàng" class="article-link">so sánh giá vàng</a>.'],
        ];
        // Pick 2 random internal links for this article
        $pickedLinks = collect($internalLinks)->shuffle()->take(2)->values();
        $linkInsertPositions = [];
        foreach ($pickedLinks as $link) {
            $linkInsertPositions[] = '<p>' . $link['context'] . '</p>';
        }

        // --- Thumbnail image ---
        if ($thumbnailPath) {
            $imgUrl = '/storage/' . $thumbnailPath;
            $altText = 'Bảng giá vàng SJC hôm nay ' . $dateShort . ' - Giá vàng các thương hiệu';
            $sections[] = '<figure class="my-6">'
                . '<img src="' . e($imgUrl) . '" alt="' . e($altText) . '" title="' . e($altText) . '" width="1200" height="630" loading="lazy" class="rounded-sm shadow-md w-full" />'
                . '<figcaption class="text-sm text-slate-500 mt-2">Bảng giá vàng các thương hiệu cập nhật lúc ' . $dateLabel . '</figcaption>'
                . '</figure>';
        }

        // --- Section 2: Price table ---
        $sections[] = '<h2 id="bang-gia">Bảng giá vàng theo thương hiệu hôm nay ' . $dateShort . '</h2>';
        $sections[] = '<p>Dưới đây là bảng tổng hợp <strong>giá vàng mua vào &ndash; bán ra</strong> của các thương hiệu lớn tại Việt Nam, cập nhật theo thời gian thực:</p>';

        $table = '<div class="overflow-x-auto my-4 not-prose"><table class="w-full border-collapse text-sm">';
        $table .= '<thead><tr class="bg-amber-50 border-b-2 border-amber-200">';
        $table .= '<th class="text-left px-4 py-3 font-semibold text-slate-800">STT</th>';
        $table .= '<th class="text-left px-4 py-3 font-semibold text-slate-800">Thương hiệu</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Mua vào (VND)</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Bán ra (VND)</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Chênh lệch</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Thay đổi</th>';
        $table .= '</tr></thead><tbody>';

        foreach ($topBrands as $index => $brand) {
            $name = e($brand['brand'] ?? 'N/A');
            $buyVal = (float) ($brand['buy'] ?? 0);
            $sellVal = (float) ($brand['sell'] ?? 0);
            $buy = $this->formatMoney($buyVal);
            $sell = $this->formatMoney($sellVal);
            $spread = $this->formatMoney(max(0, $sellVal - $buyVal));
            $chg = (float) ($brand['change'] ?? 0);
            $chgLabel = sprintf('%+.2f%%', $chg);
            $chgClass = $chg >= 0 ? 'text-green-600' : 'text-red-600';
            $rowBg = $index % 2 === 0 ? 'bg-white' : 'bg-slate-50';
            $rank = $index + 1;

            $table .= '<tr class="' . $rowBg . ' border-b border-slate-100 hover:bg-blue-50 transition">';
            $table .= '<td class="px-4 py-3 text-slate-600">' . $rank . '</td>';
            $table .= '<td class="px-4 py-3 font-medium text-slate-900">' . $name . '</td>';
            $table .= '<td class="px-4 py-3 text-right text-slate-800">' . $buy . '</td>';
            $table .= '<td class="px-4 py-3 text-right font-semibold text-slate-900">' . $sell . '</td>';
            $table .= '<td class="px-4 py-3 text-right text-slate-600">' . $spread . '</td>';
            $table .= '<td class="px-4 py-3 text-right font-semibold ' . $chgClass . '">' . $chgLabel . '</td>';
            $table .= '</tr>';
        }

        $table .= '</tbody></table></div>';
        $sections[] = $table;
        $sections[] = '<p class="text-xs text-slate-500 italic">* Bảng giá được cập nhật tự động lúc ' . $dateLabel . '. Giá có thể thay đổi theo thời gian thực.</p>';

        // --- Section 3: Brand analysis ---
        $sections[] = '<h2 id="phan-tich-thuong-hieu">Phân tích chi tiết theo thương hiệu</h2>';

        foreach ($topBrands as $index => $brand) {
            $rank = $index + 1;
            $name = e($brand['brand'] ?? 'Thương hiệu');
            $buy = $this->formatMoney((float) ($brand['buy'] ?? 0));
            $sell = $this->formatMoney((float) ($brand['sell'] ?? 0));
            $spread = $this->formatMoney(max(0, (float) ($brand['sell'] ?? 0) - (float) ($brand['buy'] ?? 0)));
            $chg = (float) ($brand['change'] ?? 0);
            $chgLabel = sprintf('%+.2f%%', $chg);
            $tone = $chg >= 0
                ? 'nhịp tăng cho thấy lực cầu vẫn duy trì trên nền giá hiện tại'
                : 'nhịp giảm phản ánh lực chốt lời ngắn hạn đang chiếm ưu thế';

            $sections[] = "<h3>{$rank}. {$name}</h3>";
            $sections[] = "<p><strong>{$name}</strong> ghi nhận mức mua vào <strong>{$buy}</strong> và bán ra <strong>{$sell}</strong>, tương ứng chênh lệch giao dịch khoảng {$spread}. Biến động trong phiên ở mức <strong>{$chgLabel}</strong>, qua đó cho thấy {$tone}.</p>";
            $sections[] = "<p>Khi đặt trong bối cảnh toàn thị trường, mức chênh mua-bán này đóng vai trò như bộ đệm rủi ro cho nhà đầu tư lướt sóng: biên độ càng cao, yêu cầu biên an toàn đầu vào càng lớn; ngược lại, biên độ hẹp thường thuận lợi hơn cho chiến lược vào lệnh từng phần và nắm giữ theo xu hướng.</p>";
        }

        // --- Section 3b: Non-SJC domestic brands ---
        $sections[] = '<h2 id="thuong-hieu-noi-dia">Giá vàng thương hiệu nội địa ngoài SJC</h2>';

        $sjcBrand = null;
        $nonSjcBrands = [];
        foreach ($topBrands as $brand) {
            $brandName = $brand['brand'] ?? '';
            if (stripos($brandName, 'SJC') !== false) {
                $sjcBrand = $brand;
            } else {
                $nonSjcBrands[] = $brand;
            }
        }

        $sjcSell = (float) ($sjcBrand['sell'] ?? 0);

        if (!empty($nonSjcBrands)) {
            $sections[] = '<p>Ngoài SJC – thương hiệu vàng miếng được Ngân hàng Nhà nước cấp phép sản xuất, thị trường trong nước còn có nhiều thương hiệu vàng uy tín khác. Dưới đây là phân tích chi tiết giá vàng của các thương hiệu nội địa nổi bật:</p>';

            foreach ($nonSjcBrands as $brand) {
                $name = e($brand['brand'] ?? 'Thương hiệu');
                $buyVal = (float) ($brand['buy'] ?? 0);
                $sellVal = (float) ($brand['sell'] ?? 0);
                $buy = $this->formatMoney($buyVal);
                $sell = $this->formatMoney($sellVal);
                $spread = $this->formatMoney(max(0, $sellVal - $buyVal));
                $chg = (float) ($brand['change'] ?? 0);
                $chgLabel = sprintf('%+.2f%%', $chg);

                $sections[] = "<h3>Giá vàng {$name}</h3>";

                $gapText = '';
                if ($sjcSell > 0 && $sellVal > 0) {
                    $gap = $sjcSell - $sellVal;
                    $gapFormatted = $this->formatMoney(abs($gap));
                    if ($gap > 0) {
                        $gapText = " So với vàng SJC, giá bán ra của {$name} thấp hơn khoảng <strong>{$gapFormatted}</strong>, tạo lợi thế chi phí đầu vào cho nhà đầu tư ưu tiên dòng vàng nhẫn hoặc vàng thương hiệu.";
                    } elseif ($gap < 0) {
                        $gapText = " Đáng chú ý, giá bán ra {$name} đang cao hơn SJC khoảng <strong>{$gapFormatted}</strong>, phản ánh nhu cầu mạnh đối với dòng sản phẩm này.";
                    } else {
                        $gapText = " Giá bán ra {$name} đang ngang bằng với SJC, cho thấy mặt bằng giá nội địa đang hội tụ.";
                    }
                }

                $toneText = $chg >= 0
                    ? "Biến động {$chgLabel} cho thấy giá vàng {$name} đang trong nhịp tích lũy tăng, phù hợp với xu hướng chung của thị trường."
                    : "Biến động {$chgLabel} phản ánh áp lực điều chỉnh ngắn hạn, nhà đầu tư nên theo dõi thêm trước khi ra quyết định.";

                $sections[] = "<p>Giá vàng {$name} hôm nay ghi nhận mức mua vào <strong>{$buy}</strong>, bán ra <strong>{$sell}</strong>, chênh lệch mua-bán {$spread}. {$toneText}{$gapText}</p>";
            }

            // Summary comparison
            $cheapest = collect($nonSjcBrands)->sortBy(fn($b) => (float) ($b['sell'] ?? 0))->first();
            $narrowest = collect($nonSjcBrands)->sortBy(fn($b) => (float) ($b['sell'] ?? 0) - (float) ($b['buy'] ?? 0))->first();

            $summaryParts = [];
            if ($cheapest) {
                $summaryParts[] = 'giá bán thấp nhất thuộc về <strong>' . e($cheapest['brand']) . '</strong> (' . $this->formatMoney((float) ($cheapest['sell'] ?? 0)) . ')';
            }
            if ($narrowest) {
                $narrowSpread = $this->formatMoney(max(0, (float) ($narrowest['sell'] ?? 0) - (float) ($narrowest['buy'] ?? 0)));
                $summaryParts[] = 'chênh lệch mua-bán hẹp nhất là <strong>' . e($narrowest['brand']) . '</strong> (' . $narrowSpread . ')';
            }
            if (!empty($summaryParts)) {
                $sections[] = '<p>Tổng hợp so sánh giữa các thương hiệu nội địa: ' . implode('; ', $summaryParts) . '. Đây là thông tin hữu ích để nhà đầu tư lựa chọn kênh giao dịch tối ưu về chi phí.</p>';
            }
        } else {
            $sections[] = '<p>Hiện tại chưa có đủ dữ liệu để phân tích các thương hiệu vàng nội địa ngoài SJC trong phiên này.</p>';
        }

        // Insert first internal link after domestic brand analysis
        if (!empty($linkInsertPositions[0])) {
            $sections[] = $linkInsertPositions[0];
        }

        // --- Section 4: Global markets ---
        $sections[] = '<h2 id="quoc-te">Liên thông với thị trường quốc tế</h2>';

        foreach ($globalMarkets as $m) {
            $name = e($m['name'] ?? 'Thị trường quốc tế');
            $price = e($m['price'] ?? 'N/A');
            $change = e($m['change'] ?? '0%');
            $sections[] = "<p>Trên bình diện quốc tế, <strong>{$name}</strong> đang giao dịch quanh <strong>{$price}</strong> với mức thay đổi {$change}. Diễn biến này được theo dõi song song với USD Index và kỳ vọng lãi suất, bởi khi đồng USD suy yếu hoặc lãi suất thực giảm, vàng thường được hưởng lợi nhờ vai trò tài sản phòng vệ. Ngược lại, khi lợi suất trái phiếu tăng nhanh, dòng tiền có xu hướng quay về tài sản sinh lãi cố định, tạo áp lực điều chỉnh ngắn hạn lên kim loại quý.</p>";
        }

        // --- Section 5: Sentiment ---
        $sections[] = '<h2 id="tam-ly">Tâm lý thị trường và động lượng giá</h2>';
        $sections[] = sprintf(
            '<p>Chỉ báo tâm lý hiện tại ghi nhận trạng thái <strong>&ldquo;%s&rdquo;</strong> với Fear &amp; Greed Index ở mức <strong>%s/100</strong>. Tỷ trọng quan điểm: mua vào khoảng %s%%, trung lập %s%%, bán ra %s%%.</p>',
            e($sentiment['fearGreedLabel'] ?? 'Trung lập'),
            (int) ($sentiment['fearGreedIndex'] ?? 50),
            (int) ($sentiment['buyPercent'] ?? 33),
            (int) ($sentiment['neutralPercent'] ?? 34),
            (int) ($sentiment['sellPercent'] ?? 33)
        );
        $sections[] = sprintf(
            '<p>Cấu trúc tâm lý này cho thấy thị trường đang vận động theo hướng <strong>%s</strong>, nghĩa là xác suất xuất hiện các nhịp tăng/giảm mạnh trong phiên kế tiếp sẽ phụ thuộc lớn vào chất lượng thông tin vĩ mô mới được công bố.</p>',
            mb_strtolower(e($sentiment['trendLabel'] ?? 'Trung lập'))
        );

        // Insert second internal link after sentiment analysis
        if (!empty($linkInsertPositions[1])) {
            $sections[] = $linkInsertPositions[1];
        }

        // --- Section 6: Macro factors ---
        $sections[] = '<h2 id="vi-mo">Các yếu tố vĩ mô đáng chú ý</h2>';

        if (!empty($macroFactors)) {
            $macroTable = '<div class="overflow-x-auto my-4 not-prose"><table class="w-full border-collapse text-sm">';
            $macroTable .= '<thead><tr class="bg-slate-100 border-b-2 border-slate-300">';
            $macroTable .= '<th class="text-left px-4 py-3 font-semibold text-slate-800">Yếu tố</th>';
            $macroTable .= '<th class="text-left px-4 py-3 font-semibold text-slate-800">Giá trị</th>';
            $macroTable .= '<th class="text-left px-4 py-3 font-semibold text-slate-800">Tác động</th>';
            $macroTable .= '</tr></thead><tbody>';

            foreach ($macroFactors as $i => $macro) {
                $rowBg = $i % 2 === 0 ? 'bg-white' : 'bg-slate-50';
                $macroTable .= '<tr class="' . $rowBg . ' border-b border-slate-100">';
                $macroTable .= '<td class="px-4 py-3 font-medium text-slate-900">' . e($macro['factor'] ?? 'N/A') . '</td>';
                $macroTable .= '<td class="px-4 py-3 text-slate-800">' . e($macro['value'] ?? 'N/A') . '</td>';
                $macroTable .= '<td class="px-4 py-3 text-slate-700">' . e($macro['impact'] ?? 'N/A') . '</td>';
                $macroTable .= '</tr>';
            }

            $macroTable .= '</tbody></table></div>';
            $sections[] = $macroTable;
        }

        foreach ($macroFactors as $macro) {
            $factor = e($macro['factor'] ?? 'Yếu tố vĩ mô');
            $value = e($macro['value'] ?? 'N/A');
            $impact = e($macro['impact'] ?? 'Tác động trung lập');
            $sections[] = "<p>Về yếu tố <strong>{$factor}</strong>, số liệu cập nhật ở mức {$value}. Theo phân tích định lượng, {$impact}. Nhà đầu tư cần kết hợp chỉ báo này với hành vi giá thực tế trên các mốc hỗ trợ/kháng cự để tránh quyết định vội vàng khi thị trường đang trong vùng nhiễu.</p>";
        }

        // --- Section 7: Comparisons ---
        $sections[] = '<h2 id="so-sanh">So sánh, tương quan và định vị giá</h2>';

        foreach ($comparisons as $cmp) {
            $title = e($cmp['title'] ?? 'So sánh thị trường');
            $value = e($cmp['value'] ?? 'N/A');
            $note = e($cmp['note'] ?? 'Không có ghi chú');
            $sections[] = "<p>Ở lát cắt tương quan, <strong>{$title}</strong> đang ở mức {$value}. Ghi chú: {$note}. Điều này hữu ích trong việc xác định xem biến động nội địa đang đi cùng hay đi ngược thị trường cơ sở, từ đó tối ưu tỷ trọng phân bổ giữa chiến lược phòng thủ và chiến lược theo đà.</p>";
        }

        // --- Section 8: Forecast ---
        $sections[] = '<h2 id="du-bao">Kịch bản dự báo và kế hoạch hành động</h2>';

        foreach ($forecast as $item) {
            $period = e($item['period'] ?? 'Ngắn hạn');
            $range = e($item['range'] ?? 'Đang cập nhật');
            $confidence = (int) ($item['confidence'] ?? 50);
            $bias = e($item['bias'] ?? 'Trung lập');
            $sections[] = "<p><strong>Kịch bản {$period}:</strong> vùng giá kỳ vọng {$range}, độ tin cậy mô hình {$confidence}%, thiên hướng <strong>{$bias}</strong>. Đây không phải khuyến nghị mua bán, mà là vùng tham chiếu để quản trị vị thế. Nhà đầu tư nên đặt ngưỡng cắt lỗ và chốt lời theo tỷ lệ rủi ro/lợi nhuận phù hợp khẩu vị vốn.</p>";
        }

        // --- Section 9: Risk management ---
        $sections[] = '<h2 id="rui-ro">Khung quản trị rủi ro</h2>';
        $sections[] = '<p>Về quản trị rủi ro, nhóm giao dịch ngắn hạn cần ưu tiên nguyên tắc <strong>bảo toàn vốn</strong> trước khi tối đa hóa lợi nhuận. Trong môi trường biến động cao, tỷ trọng mỗi lệnh nên được giới hạn để tránh trạng thái tâm lý bị cuốn theo giá.</p>';
        $sections[] = '<p>Với nhà đầu tư trung hạn, chiến lược gom theo vùng hỗ trợ và hạ tỷ trọng khi giá tiến vào vùng kháng cự thường cho hiệu quả ổn định hơn việc all-in tại một mức giá cố định.</p>';
        $sections[] = '<p>Đối với nhà đầu tư dài hạn, yếu tố quan trọng nhất là <strong>kỷ luật phân bổ tài sản</strong> và tầm nhìn chu kỳ vĩ mô. Vàng nên đóng vai trò thành phần phòng vệ trong danh mục, thay vì trở thành toàn bộ danh mục.</p>';
        $sections[] = '<p>Ngoài ra, việc theo dõi chênh lệch mua-bán giữa các thương hiệu giúp nhận diện sớm giai đoạn thị trường căng thẳng thanh khoản. Khi chênh lệch mở rộng bất thường, rủi ro trượt giá và chi phí cơ hội đều tăng.</p>';
        $sections[] = '<p><em>Bài viết này được sinh tự động từ dữ liệu hệ thống và sẽ tiếp tục cập nhật khi phát hiện biến động mới. Người đọc nên kết hợp thông tin này với chiến lược riêng để đưa ra quyết định phù hợp.</em></p>';

        $content = implode("\n", $sections);

        // Pad to ~2,000 words with additional commentary
        $extraBlocks = [
            '<p>Trong thực tế giao dịch, điểm mấu chốt không nằm ở việc dự đoán chính xác tuyệt đối, mà là khả năng phản ứng khi giả định ban đầu không còn đúng. Vì vậy, ngay cả khi xu hướng tổng thể ủng hộ một kịch bản, nhà đầu tư vẫn cần xác định rõ điểm vô hiệu để giảm thiểu sai số.</p>',
            '<p>Từ góc nhìn dữ liệu, tính nhất quán giữa các thương hiệu giúp xác nhận chất lượng xu hướng. Nếu một số thương hiệu tăng mạnh trong khi phần còn lại đi ngang, thị trường có thể đang ở trạng thái phân hóa và rủi ro nhiễu sẽ cao hơn.</p>',
            '<p>Một tín hiệu đáng chú ý khác là tốc độ thay đổi của chênh lệch mua-bán. Khi giá tăng nhưng chênh lệch mở rộng nhanh, lợi nhuận thực tế sau chi phí có thể thấp hơn kỳ vọng. Ngược lại, khi chênh lệch thu hẹp trong bối cảnh giá ổn định, điều đó thường hỗ trợ giao dịch theo vùng.</p>',
            '<p>Nhà đầu tư nên duy trì nhật ký giao dịch để đánh giá chất lượng quyết định theo thời gian, bao gồm điểm vào, điểm ra, lý do hành động và mức độ tuân thủ kế hoạch. Dữ liệu hành vi này sẽ cải thiện đáng kể hiệu suất dài hạn hơn là chỉ tập trung vào kết quả của một lệnh đơn lẻ.</p>',
        ];

        $i = 0;
        while ($this->countWords($content) < 1900) {
            $content .= "\n" . $extraBlocks[$i % count($extraBlocks)];
            $i++;
        }

        return $this->autoLinkKeywords($this->trimToWordCount($content, 2200));
    }

    private function buildDailySummaryContent(array $snapshot, Carbon $at, ?string $thumbnailPath = null): string
    {
        $dateLabel = $at->format('d/m/Y H:i');
        $dateShort = $at->format('d/m/Y');
        $topBrands = $snapshot['topBrands'] ?? [];
        $globalMarkets = $snapshot['globalMarkets'] ?? [];
        $sentiment = $snapshot['sentiment'] ?? [];

        $sections = [];

        // --- TOC ---
        $tocItems = [
            'tong-quan' => 'Tổng quan giá vàng hôm nay',
            'bang-gia-vn' => 'Bảng giá vàng Việt Nam các thương hiệu',
            'chi-tiet-thuong-hieu' => 'Chi tiết từng thương hiệu',
            'gia-vang-the-gioi' => 'Giá vàng thế giới',
            'chenh-lech' => 'Chênh lệch giá trong nước – thế giới',
            'nhan-dinh' => 'Nhận định cuối ngày',
        ];

        $toc = '<nav class="not-prose bg-slate-50 rounded-sm p-4 mb-6 border border-slate-200">'
            . '<p class="font-semibold text-slate-800 mb-2">📑 Mục lục bài viết</p>'
            . '<ol class="list-decimal list-inside space-y-1 text-sm text-blue-700">';
        foreach ($tocItems as $id => $label) {
            $toc .= '<li><a href="#' . $id . '" class="hover:underline">' . $label . '</a></li>';
        }
        $toc .= '</ol></nav>';
        $sections[] = $toc;

        // --- Section 1: Overview ---
        $sections[] = '<h2 id="tong-quan">Tổng quan giá vàng hôm nay ' . $dateShort . '</h2>';

        $sjcSell = $snapshot['sjcCard']['variants']['p0']['sell'] ?? null;
        $sjcTrend = $snapshot['sjcCard']['trendPercent'] ?? 0;
        $trendWord = $sjcTrend >= 0 ? 'tăng' : 'giảm';

        if ($sjcSell) {
            $sections[] = sprintf(
                '<p>Kết thúc ngày giao dịch <strong>%s</strong>, giá vàng SJC ghi nhận mức bán ra <strong>%.2f triệu đồng/lượng</strong>, %s <strong>%+.2f%%</strong> so với phiên trước. Dưới đây là bảng tổng hợp giá vàng tất cả các thương hiệu tại Việt Nam và thế giới.</p>',
                $dateShort, $sjcSell, $trendWord, $sjcTrend
            );
        } else {
            $sections[] = '<p>Tổng hợp giá vàng cuối ngày <strong>' . $dateShort . '</strong> từ tất cả các thương hiệu vàng lớn tại Việt Nam và thế giới.</p>';
        }

        // --- Thumbnail ---
        if ($thumbnailPath) {
            $imgUrl = '/storage/' . $thumbnailPath;
            $altText = 'Bảng giá vàng hôm nay ' . $dateShort . ' - Tổng hợp các thương hiệu';
            $sections[] = '<figure class="my-6">'
                . '<img src="' . e($imgUrl) . '" alt="' . e($altText) . '" title="' . e($altText) . '" width="1200" height="630" loading="lazy" class="rounded-sm shadow-md w-full" />'
                . '<figcaption class="text-sm text-slate-500 mt-2">Tổng hợp giá vàng các thương hiệu cập nhật cuối ngày ' . $dateShort . '</figcaption>'
                . '</figure>';
        }

        // --- Section 2: Vietnam brand price table ---
        $sections[] = '<h2 id="bang-gia-vn">Bảng giá vàng Việt Nam các thương hiệu ngày ' . $dateShort . '</h2>';
        $sections[] = '<p>Dưới đây là bảng giá vàng mua vào – bán ra của các thương hiệu lớn tại Việt Nam, cập nhật cuối ngày:</p>';

        $table = '<div class="overflow-x-auto my-4 not-prose"><table class="w-full border-collapse text-sm">';
        $table .= '<thead><tr class="bg-amber-50 border-b-2 border-amber-200">';
        $table .= '<th class="text-left px-4 py-3 font-semibold text-slate-800">STT</th>';
        $table .= '<th class="text-left px-4 py-3 font-semibold text-slate-800">Thương hiệu</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Mua vào</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Bán ra</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Chênh lệch</th>';
        $table .= '<th class="text-right px-4 py-3 font-semibold text-slate-800">Thay đổi</th>';
        $table .= '</tr></thead><tbody>';

        foreach ($topBrands as $index => $brand) {
            $name = e($brand['brand'] ?? 'N/A');
            $buyVal = (float) ($brand['buy'] ?? 0);
            $sellVal = (float) ($brand['sell'] ?? 0);
            $buy = $this->formatMoney($buyVal);
            $sell = $this->formatMoney($sellVal);
            $spread = $this->formatMoney(max(0, $sellVal - $buyVal));
            $chg = (float) ($brand['change'] ?? 0);
            $chgLabel = sprintf('%+.2f%%', $chg);
            $chgClass = $chg >= 0 ? 'text-green-600' : 'text-red-600';
            $rowBg = $index % 2 === 0 ? 'bg-white' : 'bg-slate-50';
            $rank = $index + 1;

            $table .= '<tr class="' . $rowBg . ' border-b border-slate-100 hover:bg-blue-50 transition">';
            $table .= '<td class="px-4 py-3 text-slate-600">' . $rank . '</td>';
            $table .= '<td class="px-4 py-3 font-medium text-slate-900">' . $name . '</td>';
            $table .= '<td class="px-4 py-3 text-right text-slate-800">' . $buy . '</td>';
            $table .= '<td class="px-4 py-3 text-right font-semibold text-slate-900">' . $sell . '</td>';
            $table .= '<td class="px-4 py-3 text-right text-slate-600">' . $spread . '</td>';
            $table .= '<td class="px-4 py-3 text-right font-semibold ' . $chgClass . '">' . $chgLabel . '</td>';
            $table .= '</tr>';
        }

        $table .= '</tbody></table></div>';
        $sections[] = $table;
        $sections[] = '<p class="text-xs text-slate-500 italic">* Giá tính theo VND/lượng, cập nhật lúc ' . $dateLabel . '.</p>';

        // --- Section 3: Per-brand detail ---
        $sections[] = '<h2 id="chi-tiet-thuong-hieu">Chi tiết giá vàng từng thương hiệu ngày ' . $dateShort . '</h2>';

        foreach ($topBrands as $index => $brand) {
            $name = e($brand['brand'] ?? 'Thương hiệu');
            $buyVal = (float) ($brand['buy'] ?? 0);
            $sellVal = (float) ($brand['sell'] ?? 0);
            $buy = $this->formatMoney($buyVal);
            $sell = $this->formatMoney($sellVal);
            $spread = $this->formatMoney(max(0, $sellVal - $buyVal));
            $chg = (float) ($brand['change'] ?? 0);
            $chgLabel = sprintf('%+.2f%%', $chg);
            $chgTone = $chg >= 0
                ? 'tăng so với phiên trước, cho thấy lực cầu vẫn duy trì ổn định'
                : 'giảm so với phiên trước, phản ánh áp lực điều chỉnh trong phiên';

            $sections[] = '<h3>' . ($index + 1) . '. Giá vàng ' . $name . '</h3>';
            $sections[] = '<p>Giá vàng <strong>' . $name . '</strong> cuối ngày ' . $dateShort . ': mua vào <strong>' . $buy . '</strong>, bán ra <strong>' . $sell . '</strong>. Chênh lệch mua-bán ở mức ' . $spread . '. Biến động trong ngày <strong>' . $chgLabel . '</strong>, ' . $chgTone . '.</p>';
        }

        // Internal links
        $sections[] = '<p>Xem thêm cập nhật <a href="/gia-vang-hom-nay" title="Giá vàng hôm nay" class="article-link">giá vàng hôm nay</a> để theo dõi biến động theo thời gian thực.</p>';

        // --- Section 4: World gold price ---
        $sections[] = '<h2 id="gia-vang-the-gioi">Giá vàng thế giới ngày ' . $dateShort . '</h2>';

        if (!empty($globalMarkets)) {
            foreach ($globalMarkets as $m) {
                $name = e($m['name'] ?? 'Thị trường quốc tế');
                $price = e($m['price'] ?? 'N/A');
                $change = e($m['change'] ?? '0%');
                $sections[] = '<p><strong>' . $name . '</strong> kết phiên ở mức <strong>' . $price . '</strong>, thay đổi ' . $change . ' so với phiên trước.</p>';
            }
        } else {
            $sections[] = '<p>Dữ liệu giá vàng thế giới đang được cập nhật.</p>';
        }

        $sections[] = '<p>Theo dõi diễn biến chi tiết tại trang <a href="/gia-vang-the-gioi" title="Giá vàng thế giới" class="article-link">giá vàng thế giới</a>.</p>';

        // --- Section 5: Domestic-World gap ---
        $sections[] = '<h2 id="chenh-lech">Chênh lệch giá vàng trong nước – thế giới</h2>';

        $comparisons = $snapshot['comparisons'] ?? [];
        if (!empty($comparisons)) {
            foreach ($comparisons as $cmp) {
                $title = e($cmp['title'] ?? 'So sánh');
                $value = e($cmp['value'] ?? 'N/A');
                $note = e($cmp['note'] ?? '');
                $sections[] = '<p><strong>' . $title . '</strong>: ' . $value . '.' . ($note ? ' ' . $note . '.' : '') . '</p>';
            }
        } else {
            $sections[] = '<p>Chênh lệch giá vàng trong nước so với quy đổi quốc tế đang ở mức ổn định, nhà đầu tư có thể tham khảo thêm tại trang <a href="/so-sanh-gia-vang" title="So sánh giá vàng" class="article-link">so sánh giá vàng</a>.</p>';
        }

        // --- Section 6: End-of-day assessment ---
        $sections[] = '<h2 id="nhan-dinh">Nhận định cuối ngày</h2>';

        $fearGreed = $sentiment['fearGreedLabel'] ?? 'Trung lập';
        $fearGreedIndex = (int) ($sentiment['fearGreedIndex'] ?? 50);
        $sections[] = sprintf(
            '<p>Chỉ báo tâm lý thị trường cuối ngày ghi nhận trạng thái <strong>"%s"</strong> (Fear &amp; Greed Index: <strong>%d/100</strong>). ',
            e($fearGreed), $fearGreedIndex
        );

        if ($sjcSell) {
            $avgBrands = collect($topBrands)->avg(fn($b) => (float) ($b['change'] ?? 0));
            $avgTrend = $avgBrands >= 0 ? 'tăng' : 'giảm';
            $sections[count($sections) - 1] .= sprintf(
                'Nhìn chung, mặt bằng giá vàng các thương hiệu trong ngày %s bình quân %s <strong>%+.2f%%</strong>.</p>',
                $dateShort, $avgTrend, $avgBrands
            );
        } else {
            $sections[count($sections) - 1] .= '</p>';
        }

        $sections[] = '<p>Nhà đầu tư nên theo dõi các yếu tố vĩ mô và diễn biến thị trường quốc tế trong phiên kế tiếp để có chiến lược phù hợp. Tham khảo <a href="/bieu-do-gia-vang" title="Biểu đồ giá vàng" class="article-link">biểu đồ giá vàng</a> để phân tích xu hướng theo nhiều khung thời gian.</p>';
        $sections[] = '<p><em>Bài viết được hệ thống tự động tổng hợp từ dữ liệu giá vàng cuối ngày ' . $dateShort . '.</em></p>';

        $content = implode("\n", $sections);

        return $this->autoLinkKeywords($this->trimToWordCount($content, 2200));
    }

    private function createPriceTableThumbnail(array $snapshot, string $triggerType, Carbon $at): ?string
    {
        if (!function_exists('imagecreatetruecolor')) {
            return null;
        }

        $width = 1200;
        $height = 630;

        $img = imagecreatetruecolor($width, $height);
        if (!$img) {
            return null;
        }

        $bg = imagecolorallocate($img, 247, 250, 255);
        $titleColor = imagecolorallocate($img, 16, 24, 40);
        $muted = imagecolorallocate($img, 71, 85, 105);
        $line = imagecolorallocate($img, 203, 213, 225);
        $headerBg = imagecolorallocate($img, 225, 236, 255);
        $rowAlt = imagecolorallocate($img, 255, 255, 255);

        imagefilledrectangle($img, 0, 0, $width, $height, $bg);

        // Use Arial font if available
        $fontPath = null;
        $fontCandidates = [
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/Arial.ttf',
            '/usr/share/fonts/truetype/msttcorefonts/Arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        foreach ($fontCandidates as $candidate) {
            if (file_exists($candidate)) {
                $fontPath = $candidate;
                break;
            }
        }

        $title = 'Bảng giá vàng - ' . $at->format('d/m/Y H:i');
        $subTitle = $triggerType === 'daily' ? 'Bản tin theo ngày' : 'Cập nhật biến động giá';

        if ($fontPath) {
            imagettftext($img, 18, 0, 40, 48, $titleColor, $fontPath, $title);
            imagettftext($img, 12, 0, 40, 72, $muted, $fontPath, $subTitle);
        } else {
            imagestring($img, 5, 40, 28, $title, $titleColor);
            imagestring($img, 3, 40, 58, $subTitle, $muted);
        }

        $tableX = 40;
        $tableY = 95;
        $tableW = $width - 80;
        $rowH = 52;

        imagefilledrectangle($img, $tableX, $tableY, $tableX + $tableW, $tableY + $rowH, $headerBg);
        imageline($img, $tableX, $tableY, $tableX + $tableW, $tableY, $line);
        imageline($img, $tableX, $tableY + $rowH, $tableX + $tableW, $tableY + $rowH, $line);

        if ($fontPath) {
            imagettftext($img, 13, 0, $tableX + 16, $tableY + 34, $titleColor, $fontPath, 'Thương hiệu');
            imagettftext($img, 13, 0, $tableX + 430, $tableY + 34, $titleColor, $fontPath, 'Mua vào (VND)');
            imagettftext($img, 13, 0, $tableX + 670, $tableY + 34, $titleColor, $fontPath, 'Bán ra (VND)');
            imagettftext($img, 13, 0, $tableX + 905, $tableY + 34, $titleColor, $fontPath, 'Thay đổi');
        } else {
            imagestring($img, 4, $tableX + 16, $tableY + 18, 'Thuong hieu', $titleColor);
            imagestring($img, 4, $tableX + 430, $tableY + 18, 'Mua vao (VND)', $titleColor);
            imagestring($img, 4, $tableX + 670, $tableY + 18, 'Ban ra (VND)', $titleColor);
            imagestring($img, 4, $tableX + 905, $tableY + 18, 'Thay doi', $titleColor);
        }

        $rows = array_slice($snapshot['topBrands'] ?? [], 0, 8);
        foreach ($rows as $i => $row) {
            $y1 = $tableY + $rowH + ($i * $rowH);
            $y2 = $y1 + $rowH;

            imagefilledrectangle($img, $tableX, $y1, $tableX + $tableW, $y2, $rowAlt);
            imageline($img, $tableX, $y2, $tableX + $tableW, $y2, $line);

            $brand = (string) ($row['brand'] ?? '-');
            $buy = number_format((float) ($row['buy'] ?? 0), 0, ',', '.');
            $sell = number_format((float) ($row['sell'] ?? 0), 0, ',', '.');
            $change = sprintf('%+.2f%%', (float) ($row['change'] ?? 0));

            if ($fontPath) {
                imagettftext($img, 12, 0, $tableX + 16, $y1 + 34, $titleColor, $fontPath, Str::limit($brand, 35, '...'));
                imagettftext($img, 12, 0, $tableX + 430, $y1 + 34, $titleColor, $fontPath, $buy);
                imagettftext($img, 12, 0, $tableX + 670, $y1 + 34, $titleColor, $fontPath, $sell);
                imagettftext($img, 12, 0, $tableX + 905, $y1 + 34, $titleColor, $fontPath, $change);
            } else {
                imagestring($img, 4, $tableX + 16, $y1 + 18, Str::limit($brand, 35, '...'), $titleColor);
                imagestring($img, 4, $tableX + 430, $y1 + 18, $buy, $titleColor);
                imagestring($img, 4, $tableX + 670, $y1 + 18, $sell, $titleColor);
                imagestring($img, 4, $tableX + 905, $y1 + 18, $change, $titleColor);
            }
        }

        $footerText = 'GiaVangHN - Cập nhật tự động ' . $at->format('d/m/Y H:i');
        if ($fontPath) {
            imagettftext($img, 10, 0, 40, 608, $muted, $fontPath, $footerText);
        } else {
            imagestring($img, 2, 40, 598, $footerText, $muted);
        }

        ob_start();
        imagepng($img);
        $binary = ob_get_clean();
        imagedestroy($img);

        if ($binary === false) {
            return null;
        }

        $fileName = sprintf('analysis-thumbnails/%s-%s-%s.png', $at->format('Ymd-His'), $triggerType, Str::random(6));
        Storage::disk('public')->put($fileName, $binary);

        return $fileName;
    }

    private function buildTags(array $snapshot, string $triggerType): array
    {
        $tags = ['giá vàng', 'phân tích giá vàng'];

        if ($triggerType === 'daily') {
            $tags[] = 'bản tin giá vàng';
        } elseif ($triggerType === 'summary') {
            $tags[] = 'tổng hợp giá vàng';
            $tags[] = 'giá vàng hôm nay';
        } else {
            $tags[] = 'biến động giá vàng';
        }

        foreach (($snapshot['topBrands'] ?? []) as $brand) {
            $name = $brand['brand'] ?? '';
            if (stripos($name, 'SJC') !== false) {
                $tags[] = 'giá vàng SJC';
            } elseif (stripos($name, 'DOJI') !== false || stripos($name, 'Doji') !== false) {
                $tags[] = 'giá vàng DOJI';
            } elseif (stripos($name, 'PNJ') !== false) {
                $tags[] = 'giá vàng PNJ';
            } elseif (stripos($name, 'Phú Quý') !== false) {
                $tags[] = 'giá vàng Phú Quý';
            } elseif (stripos($name, 'Mi Hồng') !== false) {
                $tags[] = 'giá vàng Mi Hồng';
            } elseif (stripos($name, 'Bảo Tín') !== false || stripos($name, 'BTMC') !== false) {
                $tags[] = 'giá vàng BTMC';
            }
        }

        $sentiment = $snapshot['sentiment']['fearGreedLabel'] ?? '';
        if ($sentiment) {
            $tags[] = mb_strtolower($sentiment);
        }

        if (!empty($snapshot['globalMarkets'])) {
            $tags[] = 'giá vàng thế giới';
            $tags[] = 'XAU/USD';
        }

        return array_values(array_unique($tags));
    }

    /**
     * Auto-link keywords in content HTML. Each keyword is linked at most once.
     * Longer phrases are matched first to avoid partial overlaps.
     * Text already inside <a> or heading tags is skipped.
     */
    private function autoLinkKeywords(string $html): string
    {
        $keywordMap = [
            'giá vàng hôm nay' => ['url' => '/', 'title' => 'Giá vàng hôm nay'],
            'giá vàng SJC' => ['url' => '/gia-vang-hom-nay/gia-vang-sjc', 'title' => 'Giá vàng SJC'],
            'giá vàng DOJI' => ['url' => '/gia-vang-hom-nay/gia-vang-doji', 'title' => 'Giá vàng DOJI'],
            'giá vàng PNJ' => ['url' => '/gia-vang-hom-nay/gia-vang-pnj', 'title' => 'Giá vàng PNJ'],
            'giá vàng Phú Quý' => ['url' => '/gia-vang-hom-nay/gia-vang-phu-quy', 'title' => 'Giá vàng Phú Quý'],
            'giá vàng Mi Hồng' => ['url' => '/gia-vang-hom-nay/gia-vang-mi-hong', 'title' => 'Giá vàng Mi Hồng'],
            'giá vàng Bảo Tín Minh Châu' => ['url' => '/gia-vang-hom-nay/gia-vang-bao-tin-minh-chau', 'title' => 'Giá vàng Bảo Tín Minh Châu'],
            'giá vàng thế giới' => ['url' => '/gia-vang-the-gioi', 'title' => 'Giá vàng thế giới'],
            'biểu đồ giá vàng' => ['url' => '/bieu-do-gia-vang', 'title' => 'Biểu đồ giá vàng'],
            'giá vàng' => ['url' => '/gia-vang-hom-nay', 'title' => 'Giá vàng'],
        ];

        // Sort by keyword length descending so longer phrases match first
        uksort($keywordMap, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        $linked = [];

        foreach ($keywordMap as $keyword => $meta) {
            if (isset($linked[$keyword])) {
                continue;
            }

            // Build case-insensitive regex that only matches inside <p> text,
            // not inside existing <a> tags or HTML attributes
            $escaped = preg_quote($keyword, '/');
            $pattern = '/(?<![<\/\w])(' . $escaped . ')(?![^<]*<\/a>)(?![^<]*>)/iu';

            $replaced = false;
            $html = preg_replace_callback($pattern, function ($m) use ($meta, &$replaced) {
                if ($replaced) {
                    return $m[0]; // Only link first occurrence
                }
                $replaced = true;
                return '<a href="' . e($meta['url']) . '" title="' . e($meta['title']) . '" class="article-link">' . $m[0] . '</a>';
            }, $html);

            if ($replaced) {
                $linked[$keyword] = true;
            }
        }

        return $html;
    }

    private function formatMoney(float $value): string
    {
        return number_format($value, 0, ',', '.') . ' VND';
    }

    private function countWords(string $text): int
    {
        $clean = strip_tags($text);
        preg_match_all('/\p{L}+/u', $clean, $matches);
        return count($matches[0] ?? []);
    }

    private function trimToWordCount(string $text, int $maxWords): string
    {
        if ($this->countWords($text) <= $maxWords) {
            return $text;
        }

        // For HTML content, trim by removing trailing sections rather than breaking mid-tag
        $lines = explode("\n", $text);
        $result = '';

        foreach ($lines as $line) {
            $candidate = $result . ($result ? "\n" : '') . $line;
            if ($this->countWords($candidate) > $maxWords) {
                break;
            }
            $result = $candidate;
        }

        return $result . "\n<p><em>(Bài viết được hệ thống tự động tổng hợp và chuẩn hóa theo ngưỡng độ dài.)</em></p>";
    }
}
