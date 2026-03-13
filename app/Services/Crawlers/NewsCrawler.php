<?php

namespace App\Services\Crawlers;

use App\Models\NewsArticle;

class NewsCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'news';
    }

    public function run(): int
    {
        $count = 0;

        $count += $this->crawlVnExpress();

        return $count;
    }

    private function crawlVnExpress(): int
    {
        $count = 0;

        // VnExpress kinh-doanh RSS contains gold-related articles
        $rss = $this->fetch('https://vnexpress.net/rss/kinh-doanh.rss');

        // Handle both CDATA and plain titles
        preg_match_all(
            '/<item>.*?<title>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/title>.*?<link>(.*?)<\/link>.*?<description>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/description>.*?<pubDate>(.*?)<\/pubDate>.*?<\/item>/si',
            $rss,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $title = trim(strip_tags(html_entity_decode($m[1])));
            $url = trim($m[2]);
            $desc = $m[3];
            $pubDate = $m[4];
            $imageUrl = $this->extractImage($desc);

            if (empty($title) || mb_strlen($title) < 15) {
                continue;
            }

            // Only keep gold-related news
            if (!preg_match('/vàng|gold|kim loại|quý|XAU|SJC|giá vàng/iu', $title)) {
                continue;
            }

            if (NewsArticle::where('title', $title)->exists()) {
                continue;
            }

            if (!$imageUrl && $url) {
                $imageUrl = $this->fetchOgImage($url);
            }

            NewsArticle::create([
                'tag' => $this->detectTag($title),
                'title' => $title,
                'url' => $url,
                'image_url' => $imageUrl,
                'source' => 'vnexpress',
                'impact' => $this->detectImpact($title),
                'published_at' => \Carbon\Carbon::parse($pubDate),
            ]);

            $count++;
        }

        // Also try VnExpress general latest news for more gold coverage
        try {
            $rss2 = $this->fetch('https://vnexpress.net/rss/tin-moi-nhat.rss');
            preg_match_all(
                '/<item>.*?<title>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/title>.*?<link>(.*?)<\/link>.*?<description>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/description>.*?<pubDate>(.*?)<\/pubDate>.*?<\/item>/si',
                $rss2,
                $matches2,
                PREG_SET_ORDER
            );

            foreach ($matches2 as $m) {
                $title = trim(strip_tags(html_entity_decode($m[1])));
                $url = trim($m[2]);
                $desc2 = $m[3];
                $pubDate = $m[4];
                $imageUrl = $this->extractImage($desc2);

                if (empty($title) || mb_strlen($title) < 15) {
                    continue;
                }

                if (!preg_match('/vàng|gold|XAU|SJC|giá vàng/iu', $title)) {
                    continue;
                }

                if (NewsArticle::where('title', $title)->exists()) {
                    continue;
                }

                if (!$imageUrl && $url) {
                    $imageUrl = $this->fetchOgImage($url);
                }

                NewsArticle::create([
                    'tag' => $this->detectTag($title),
                    'title' => $title,
                    'url' => $url,
                    'image_url' => $imageUrl,
                    'source' => 'vnexpress',
                    'impact' => $this->detectImpact($title),
                    'published_at' => \Carbon\Carbon::parse($pubDate),
                ]);

                $count++;
            }
        } catch (\Throwable) {
            // ok
        }

        return $count;
    }

    private function detectTag(string $title): string
    {
        $lower = mb_strtolower($title);

        if (str_contains($lower, 'nóng') || str_contains($lower, 'sốc') || str_contains($lower, 'khẩn')) return 'Nóng';
        if (str_contains($lower, 'phân tích') || str_contains($lower, 'nhận định')) return 'Phân tích';
        if (str_contains($lower, 'dự báo') || str_contains($lower, 'triển vọng')) return 'Dự báo';
        if (str_contains($lower, 'thế giới') || str_contains($lower, 'quốc tế')) return 'Quốc tế';
        if (str_contains($lower, 'sjc') || str_contains($lower, 'trong nước') || str_contains($lower, 'việt nam')) return 'Trong nước';

        return 'Tin tức';
    }

    private function detectImpact(string $title): string
    {
        $lower = mb_strtolower($title);

        $positive = ['tang', 'tăng', 'vuot', 'vượt', 'ho tro', 'hỗ trợ', 'tich cuc', 'tích cực', 'ky luc', 'kỷ lục', 'mua rong', 'mua ròng'];
        $negative = ['giam', 'giảm', 'tut', 'tụt', 'mat', 'mất', 'ban thao', 'bán tháo', 'suy yeu', 'suy yếu', 'lo ngai', 'lo ngại'];

        foreach ($positive as $word) {
            if (str_contains($lower, $word)) return 'positive';
        }
        foreach ($negative as $word) {
            if (str_contains($lower, $word)) return 'negative';
        }

        return 'neutral';
    }

    private function extractImage(string $html): ?string
    {
        if (preg_match('/<img[^>]+src=["\']([^"\'>]+)["\']/', $html, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Fetch og:image from an article page URL.
     */
    private function fetchOgImage(string $url): ?string
    {
        try {
            $html = $this->fetch($url);
            if (preg_match('/<meta\s[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m)) {
                return $m[1];
            }
            if (preg_match('/<meta\s[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']/i', $html, $m)) {
                return $m[1];
            }
        } catch (\Throwable) {
            // ignore
        }

        return null;
    }
}
