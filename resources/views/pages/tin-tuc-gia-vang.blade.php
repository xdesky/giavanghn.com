@extends('gold.page-shell')

@section('page-label', 'Tin tức')

@push('head')
@php
$schemaItems = [];
foreach (array_slice($newsArticles ?? [], 0, 10) as $idx => $art) {
    $item = [
        '@type' => 'ListItem',
        'position' => $idx + 1,
        'item' => array_filter([
            '@type' => 'NewsArticle',
            'headline' => $art['title'] ?? '',
            'description' => $art['excerpt'] ?? '',
            'url' => $art['url'] ?? '',
            'image' => $art['image_url'] ?? null,
            'datePublished' => $art['date'] ?? null,
            'publisher' => !empty($art['source']) ? ['@type' => 'Organization', 'name' => $art['source']] : null,
        ]),
    ];
    $schemaItems[] = $item;
}
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $title ?? 'Tin tức giá vàng',
    'description' => $description ?? 'Cập nhật tin tức giá vàng mới nhất từ các nguồn đáng tin cậy.',
    'url' => url('/tin-tuc-gia-vang'),
    'mainEntity' => [
        '@type' => 'ItemList',
        'numberOfItems' => count($newsArticles ?? []),
        'itemListElement' => $schemaItems,
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endpush

@section('page-content')
@include('gold.sections.news-list', [
    'category'      => 'all',
    'categoryLabel' => 'Tin tức giá vàng mới nhất',
    'articles'      => $newsArticles ?? [],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
