@extends('gold.page-shell')

@section('page-label', 'Bieu do')

@section('page-content')
@include('gold.sections.chart', ['period' => '10y', 'periodLabel' => '10 nam'])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
