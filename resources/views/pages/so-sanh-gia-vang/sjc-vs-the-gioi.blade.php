@extends('gold.page-shell')

@section('page-label', 'So sanh')

@section('page-content')
@include('gold.sections.comparison', [
    'leftName'    => 'SJC',
    'rightName'   => 'Gia the gioi (quy doi)',
    'leftPrice'   => '92,500,000 VND',
    'rightPrice'  => '74,000,000 VND',
    'leftChange'  => '+500,000',
    'rightChange' => '+312,000',
    'spread'      => '18,500,000 VND',
    'spreadNote'  => 'Chenh lech do thue, phi va cung cau noi dia',
    'leftHigh'    => '93,200,000 VND',
    'rightHigh'   => '74,800,000 VND',
    'leftLow'     => '89,500,000 VND',
    'rightLow'    => '72,100,000 VND',
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
