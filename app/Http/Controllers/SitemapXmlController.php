<?php

namespace App\Http\Controllers;

use App\Models\AnalysisArticle;
use Illuminate\Http\Response;

class SitemapXmlController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $now = now()->toW3cString();

        $urls = [];

        // Homepage — highest priority, changes frequently
        $urls[] = [
            'loc' => $baseUrl . '/',
            'lastmod' => $now,
            'changefreq' => 'always',
            'priority' => '1.0',
        ];

        // All pages from gold_sitemap config
        $sitemap = config('gold_sitemap', []);
        $this->collectSitemapUrls($sitemap, '', $baseUrl, $now, $urls);

        // Published analysis articles
        $articles = AnalysisArticle::whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->select(['slug', 'published_at', 'updated_at'])
            ->get();

        foreach ($articles as $article) {
            $urls[] = [
                'loc' => $baseUrl . '/tin-tuc-gia-vang/trong-nuoc/' . $article->slug,
                'lastmod' => ($article->updated_at ?? $article->published_at)->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }

        $xml = $this->buildXml($urls);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    private function collectSitemapUrls(array $nodes, string $prefix, string $baseUrl, string $now, array &$urls): void
    {
        foreach ($nodes as $slug => $meta) {
            $fullPath = $prefix === '' ? $slug : $prefix . '/' . $slug;
            $isParent = !empty($meta['children']);

            $urls[] = [
                'loc' => $baseUrl . '/' . $fullPath,
                'lastmod' => $now,
                'changefreq' => $isParent ? 'daily' : 'weekly',
                'priority' => $prefix === '' ? '0.8' : '0.6',
            ];

            if ($isParent) {
                $this->collectSitemapUrls($meta['children'], $fullPath, $baseUrl, $now, $urls);
            }
        }
    }

    private function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $entry) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($entry['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            if (isset($entry['lastmod'])) {
                $xml .= '    <lastmod>' . $entry['lastmod'] . "</lastmod>\n";
            }
            $xml .= '    <changefreq>' . $entry['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $entry['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
