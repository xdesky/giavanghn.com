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

        // VnExpress kinh-doanh RSS — filter for "giá vàng" keyword
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
            $descText = trim(strip_tags(html_entity_decode($desc)));
            $pubDate = $m[4];
            $imageUrl = $this->extractImage($desc);

            if (empty($title) || mb_strlen($title) < 15) {
                continue;
            }

            // Only keep articles with "giá vàng" in title or description
            $haystack = mb_strtolower($title . ' ' . $descText);
            if (!str_contains($haystack, 'giá vàng') && !preg_match('/\bXAU\b|\bSJC\b/i', $title)) {
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
