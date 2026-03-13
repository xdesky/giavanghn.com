@extends('gold.page-shell')

@section('page-label', 'Bieu do')

@section('page-content')
@include('gold.sections.chart', ['period' => 'today', 'periodLabel' => 'hom nay'])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
