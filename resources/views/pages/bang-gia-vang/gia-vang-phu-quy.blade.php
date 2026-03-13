@extends('gold.page-shell')

@section('page-label', 'Bang gia')

@section('page-content')
@include('gold.sections.price-brand', ['brandName' => 'Phu Quy'])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
