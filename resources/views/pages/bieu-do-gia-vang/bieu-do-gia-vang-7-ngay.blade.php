@extends('gold.page-shell')

@section('page-label', 'Bieu do')

@section('page-content')
@include('gold.sections.chart', ['period' => '7d', 'periodLabel' => '7 ngay'])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
