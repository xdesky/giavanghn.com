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
    'name' => $title ?? 'Tin tức giá vàng trong nước',
    'description' => $description ?? 'Tin tức giá vàng trong nước từ các báo uy tín.',
    'url' => url('/tin-tuc-gia-vang/trong-nuoc'),
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
    'category'      => 'domestic',
    'categoryLabel' => 'Tin tức giá vàng trong nước',
    'articles'      => $newsArticles,
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
