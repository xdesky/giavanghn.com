<?php

namespace App\Http\Controllers;

use App\Models\AnalysisArticle;
use App\Models\NewsArticle;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SitemapPageController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function show(string $path): View
    {
        [$flat, $topLevel] = $this->flattenSitemap(config('gold_sitemap', []));

        abort_unless(isset($flat[$path]), 404);

        $page = $flat[$path];

        $viewName = 'pages.' . str_replace('/', '.', $path);
        abort_unless(view()->exists($viewName), 404);

        $segments = explode('/', $path);
        $rootPath = $segments[0];
        $this->refreshNewsIfStale();

        $newsResult = $this->buildNewsArticlesForPath($path);
        $snapshot = $this->dashboardService->buildSnapshot();

        return view($viewName, [
            'path' => $path,
            'currentPath' => $path,
            'rootPath' => $rootPath,
            'title' => $page['title'],
            'description' => $page['description'],
            'page' => $page,
            'breadcrumbs' => $this->buildBreadcrumbs($path, $flat),
            'children' => $page['children'] ?? [],
            'topLevel' => $topLevel,
            'menuTree' => config('gold_sitemap', []),
            'snapshot' => $snapshot,
            'newsArticles' => $newsResult['items'],
            'newsPaginator' => $newsResult['paginator'],
        ]);
    }

    /**
     * @return array{0: array<string, array<string, mixed>>, 1: array<string, array<string, mixed>>}
     */
    private function flattenSitemap(array $nodes): array
    {
        $flat = [];
        $topLevel = [];

        $walker = function (array $items, string $prefix = '') use (&$walker, &$flat, &$topLevel): void {
            foreach ($items as $slug => $meta) {
                $fullPath = $prefix === '' ? $slug : $prefix . '/' . $slug;

                $entry = [
                    'title' => $meta['title'] ?? ucfirst(str_replace('-', ' ', $slug)),
                    'description' => $meta['description'] ?? 'Nội dung đang được cập nhật theo sitemap đã duyệt.',
                    'children' => [],
                ];

                if (isset($meta['children']) && is_array($meta['children'])) {
                    foreach ($meta['children'] as $childSlug => $childMeta) {
                        $childPath = $fullPath . '/' . $childSlug;
                        $entry['children'][] = [
                            'path' => $childPath,
                            'title' => $childMeta['title'] ?? ucfirst(str_replace('-', ' ', $childSlug)),
                        ];
                    }
                }

                $flat[$fullPath] = $entry;

                if ($prefix === '') {
                    $topLevel[$fullPath] = [
                        'path' => $fullPath,
                        'title' => $entry['title'],
                    ];
                }

                if (isset($meta['children']) && is_array($meta['children'])) {
                    $walker($meta['children'], $fullPath);
                }
            }
        };

        $walker($nodes);

        return [$flat, $topLevel];
    }

    /**
     * @param array<string, array<string, mixed>> $flat
     * @return array<int, array{path: string, title: string}>
     */
    private function buildBreadcrumbs(string $path, array $flat): array
    {
        $segments = explode('/', $path);
        $breadcrumbs = [];
        $carry = [];

        foreach ($segments as $segment) {
            $carry[] = $segment;
            $candidate = implode('/', $carry);

            if (!isset($flat[$candidate])) {
                continue;
            }

            $breadcrumbs[] = [
                'path' => $candidate,
                'title' => $flat[$candidate]['title'],
            ];
        }

        return $breadcrumbs;
    }

    private function refreshNewsIfStale(): void
    {
        $latest = NewsArticle::max('published_at');
        $needsRefresh = !$latest || now()->diffInMinutes($latest) >= 30;

        if (!$needsRefresh) {
            return;
        }

        try {
            Artisan::call('crawl:gold', ['--source' => 'news']);
        } catch (\Throwable) {
            // Keep page responsive even if crawling fails.
        }
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, paginator: \Illuminate\Contracts\Pagination\LengthAwarePaginator}
     */
    private function buildNewsArticlesForPath(string $path): array
    {
        // "Tin tức giá vàng" (parent) or "trong-nuoc" — mix analysis + external news
        if ($path === 'tin-tuc-gia-vang' || str_contains($path, '/trong-nuoc')) {
            return $this->buildDomesticNews($path);
        }

        // "Tin tức giá vàng thế giới"
        if (str_contains($path, '/the-gioi')) {
            $query = NewsArticle::goldRelated()->orderByDesc('published_at');
            $query->where(function ($q): void {
                $q->where('tag', 'Quốc tế')
                    ->orWhere('title', 'like', '%thế giới%')
                    ->orWhere('title', 'like', '%quốc tế%')
                    ->orWhere('title', 'like', '%XAU%')
                    ->orWhere('title', 'like', '%USD%');
            });

            $paginator = $query->paginate(12)->withPath('/' . $path);

            $items = $paginator->getCollection()->map(function (NewsArticle $article): array {
                return [
                    'icon' => match ($article->impact) { 'positive' => '📈', 'negative' => '📉', default => '📰' },
                    'title' => $article->title,
                    'excerpt' => $article->summary ?: mb_strimwidth($article->title, 0, 160, '...'),
                    'date' => optional($article->published_at)?->diffForHumans() ?? '',
                    'url' => $article->url,
                    'tag' => $article->tag,
                    'impact' => $article->impact,
                    'source' => $article->source,
                    'image_url' => $article->image_url,
                ];
            })->all();

            return ['items' => $items, 'paginator' => $paginator];
        }

        // Default: return recent news for sidebar on non-news pages
        $recentItems = NewsArticle::goldRelated()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get()
            ->map(fn (NewsArticle $n) => [
                'icon' => match ($n->impact) { 'positive' => '📈', 'negative' => '📉', default => '📰' },
                'title' => $n->title,
                'excerpt' => $n->summary ?: mb_strimwidth($n->title, 0, 160, '...'),
                'date' => optional($n->published_at)?->diffForHumans() ?? '',
                'url' => $n->url,
                'tag' => $n->tag,
                'impact' => $n->impact,
                'source' => $n->source,
                'image_url' => $n->image_url,
            ])->all();

        return ['items' => $recentItems, 'paginator' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12)];
    }

    /**
     * Build news items from analysis articles (giavanghn's own content).
     *
     * @return array{items: array<int, array<string, mixed>>, paginator: \Illuminate\Contracts\Pagination\LengthAwarePaginator}
     */
    private function buildDomesticNews(string $path): array
    {
        // Gather giavanghn analysis articles
        $analysisItems = AnalysisArticle::whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (AnalysisArticle $a) => [
                'icon' => '📊',
                'title' => $a->title,
                'excerpt' => $a->summary ?: \Illuminate\Support\Str::words(strip_tags($a->content), 30, '...'),
                'published_at' => $a->published_at,
                'date' => optional($a->published_at)?->diffForHumans() ?? '',
                'url' => '/tin-tuc-gia-vang/trong-nuoc/' . $a->slug,
                'tag' => 'Phân tích',
                'impact' => null,
                'source' => 'giavanghn',
                'image_url' => $a->thumbnail_path ? asset('storage/' . $a->thumbnail_path) : null,
            ]);

        // Gather external news
        $newsItems = NewsArticle::goldRelated()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (NewsArticle $n) => [
                'icon' => match ($n->impact) { 'positive' => '📈', 'negative' => '📉', default => '📰' },
                'title' => $n->title,
                'excerpt' => $n->summary ?: mb_strimwidth($n->title, 0, 160, '...'),
                'published_at' => $n->published_at,
                'date' => optional($n->published_at)?->diffForHumans() ?? '',
                'url' => $n->url,
                'tag' => $n->tag,
                'impact' => $n->impact,
                'source' => $n->source,
                'image_url' => $n->image_url,
            ]);

        // Merge & sort by date descending
        $merged = $analysisItems->concat($newsItems)
            ->sortByDesc('published_at')
            ->values();

        // Manual pagination
        $page = (int) request()->get('page', 1);
        $perPage = 12;
        $slice = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice, $merged->count(), $perPage, $page,
            ['path' => '/' . $path]
        );

        $items = $slice->map(fn ($item) => collect($item)->except('published_at')->all())->all();

        return ['items' => $items, 'paginator' => $paginator];
    }
}
