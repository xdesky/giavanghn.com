@extends('gold.page-shell')

@section('page-label', 'So sanh')

@section('page-content')
@include('gold.sections.comparison', [
    'leftName'    => 'SJC',
    'rightName'   => 'PNJ',
    'leftPrice'   => '92,500,000 VND',
    'rightPrice'  => '92,300,000 VND',
    'leftChange'  => '+500,000',
    'rightChange' => '+300,000',
    'spread'      => '200,000 VND',
    'spreadNote'  => 'Chenh lech nho do canh tranh truc tiep giua thuong hieu',
    'leftHigh'    => '93,200,000 VND',
    'rightHigh'   => '93,000,000 VND',
    'leftLow'     => '89,500,000 VND',
    'rightLow'    => '89,300,000 VND',
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
