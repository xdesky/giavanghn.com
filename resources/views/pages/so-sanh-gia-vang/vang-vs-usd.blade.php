@extends('gold.page-shell')

@section('page-label', 'So sanh')

@section('page-content')
@include('gold.sections.comparison', [
    'leftName'    => 'Vang (XAU)',
    'rightName'   => 'USD (DXY)',
    'leftPrice'   => '2,918.45 USD',
    'rightPrice'  => '103.25 pts',
    'leftChange'  => '+12.30',
    'rightChange' => '-0.32',
    'spread'      => 'Tuong quan nghich',
    'spreadNote'  => 'Khi USD yeu, vang thuong tang va nguoc lai',
    'leftHigh'    => '2,950.00 USD',
    'rightHigh'   => '104.80 pts',
    'leftLow'     => '2,850.00 USD',
    'rightLow'    => '101.50 pts',
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
