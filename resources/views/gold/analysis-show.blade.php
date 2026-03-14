@extends('gold.page-shell')

@section('page-label', 'Phân tích giá vàng')
@section('hide-page-header', '1')

@push('head')
<script type="application/ld+json">
@php
$ldJson = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article->title,
    'description' => $article->summary ?? 'Phân tích giá vàng chi tiết',
    'datePublished' => $article->published_at?->toIso8601String(),
    'dateModified' => $article->updated_at?->toIso8601String(),
    'author' => ['@type' => 'Organization', 'name' => 'GiaVangHN'],
    'publisher' => ['@type' => 'Organization', 'name' => 'GiaVangHN'],
    'wordCount' => $article->word_count ?? 0,
    'articleSection' => 'Phân tích giá vàng',
];
if ($article->thumbnail_path) {
    $ldJson['image'] = asset('storage/' . $article->thumbnail_path);
}
@endphp
{!! json_encode($ldJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('page-content')
<style>
    .article-body { font-size: 16px; line-height: 1.75; overflow-wrap: break-word; word-break: break-word; }
    .article-body figcaption { text-align: center; }
    .article-body h2 { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-top: 1.5rem; margin-bottom: 0.75rem; line-height: 1.3; }
    .article-body h3 { font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-top: 1.25rem; margin-bottom: 0.5rem; line-height: 1.4; }
    .article-body a { color: #001061; text-decoration: underline; text-underline-offset: 2px; }
    .article-body a:hover { color: #b8860b; }
    .article-body img { max-width: 100%; height: auto; border-radius: 4px; }
    .article-body table { display: block; overflow-x: auto; max-width: 100%; }
    .article-body iframe { max-width: 100%; }
    .article-body pre { overflow-x: auto; max-width: 100%; }
    @media (min-width: 640px) {
        .article-body h2 { font-size: 1.5rem; margin-top: 2rem; }
        .article-body h3 { font-size: 1.25rem; margin-top: 1.5rem; }
    }
</style>
<article class="article-body rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-8 prose prose-slate max-w-none">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-4">{{ $article->title }}</h1>

    @if ($article->meta['sentiment'] ?? null)
        <div class="not-prose mb-4 flex flex-wrap items-center gap-3">
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ ucfirst($article->trigger_type) }}</span>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ $article->meta['sentiment'] }}</span>
            <span class="text-xs text-slate-400">{{ $article->published_at?->diffForHumans() }}</span>
        </div>
    @endif

    @if ($article->content)
        <div class="not-prose mb-6 rounded-sm border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 break-words">
            {{ Str::words(trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<nav[^>]*>.*?<\/nav>/s', '', $article->content)))), 100, '...') }}
        </div>
    @endif

    {!! $article->content !!}

    @if (!empty($article->tags))
        <div class="not-prose mt-6 flex flex-wrap gap-2">
            @foreach ($article->tags as $tag)
                <a href="/tin-tuc-gia-vang/trong-nuoc/tag/{{ Str::slug($tag) }}" class="inline-block rounded-full border border-slate-300 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 no-underline hover:bg-[#001061] hover:text-white hover:border-[#001061] transition">{{ $tag }}</a>
            @endforeach
        </div>
    @endif

    <div class="not-prose mt-8 border-t border-slate-200 pt-4 text-sm text-slate-500">
        <p>Bài viết được tạo lúc {{ $article->published_at?->format('H:i d/m/Y') }} Bởi <strong>giavanghn</strong></p>
    </div>
</article>

{{-- Related articles --}}
@if ($relatedArticles->isNotEmpty())
<section class="mt-8">
    <h2 class="text-lg font-bold text-slate-900 mb-4">Bài viết liên quan</h2>
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($relatedArticles as $related)
            <a href="/tin-tuc-gia-vang/trong-nuoc/{{ $related->slug }}" class="group block rounded-sm border border-slate-200 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                @if ($related->thumbnail_path)
                    <img src="{{ asset('storage/' . $related->thumbnail_path) }}" alt="{{ $related->title }}" class="w-full h-40 object-cover" loading="lazy" />
                @else
                    <div class="w-full h-40 bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center">
                        <span class="text-amber-400 text-3xl">📊</span>
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition line-clamp-2">{{ Str::limit($related->title, 90) }}</h3>
                    <p class="text-xs text-slate-500 mt-2">{{ $related->published_at?->diffForHumans() }} · bởi: giavanghn</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection

@section('page-sidebar')
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4">
    <h3 class="text-lg font-bold text-slate-900 mb-3">Bài phân tích gần đây</h3>
    <div class="grid gap-2">
        @foreach ($recentArticles as $recent)
            <a href="/tin-tuc-gia-vang/trong-nuoc/{{ $recent->slug }}" class="block rounded-sm border border-slate-100 px-3 py-2 text-sm {{ $recent->id === $article->id ? 'bg-blue-50 font-semibold text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                {{ Str::limit($recent->title, 80) }}
                <span class="block text-xs text-slate-400 mt-0.5">{{ $recent->published_at?->diffForHumans() }}</span>
            </a>
        @endforeach
    </div>
</div>
@endsection
