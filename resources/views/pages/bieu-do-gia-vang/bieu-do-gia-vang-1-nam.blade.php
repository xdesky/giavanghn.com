@extends('gold.page-shell')

@section('page-label', 'Bieu do')

@section('page-content')
@include('gold.sections.chart', ['period' => '1y', 'periodLabel' => '1 nam'])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
