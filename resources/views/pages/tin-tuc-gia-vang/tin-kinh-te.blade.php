@extends('gold.page-shell')

@section('page-label', 'Tin tức')

@section('page-content')
@include('gold.sections.news-list', [
    'category'      => 'domestic',
    'categoryLabel' => 'Tin kinh tế',
    'articles'      => $newsArticles ?? [],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
