@extends('gold.page-shell')

@section('page-label', 'Tin tức')

@push('head')
@php
$schemaItems = [];
foreach (array_slice($newsArticles ?? [], 0, 10) as $idx => $art) {
    $schemaItems[] = [
        '@type' => 'ListItem',
        'position' => $idx + 1,
        'item' => [
            '@type' => 'NewsArticle',
            'headline' => $art['title'] ?? '',
            'description' => $art['excerpt'] ?? '',
            'url' => $art['url'] ?? '',
        ],
    ];
}
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $title ?? 'Tin tức giá vàng thế giới',
    'description' => $description ?? 'Cập nhật tin tức giá vàng thế giới, XAU/USD và thị trường quốc tế.',
    'url' => url('/tin-tuc-gia-vang/the-gioi'),
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
    'category'      => 'world',
    'categoryLabel' => 'Tin tức giá vàng thế giới',
    'articles'      => $newsArticles,
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
