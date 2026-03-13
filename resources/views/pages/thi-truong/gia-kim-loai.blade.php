@extends('gold.page-shell')

@section('page-label', 'Thi truong')

@section('page-content')
@include('gold.sections.market', [
    'marketLabel' => 'Gia kim loai quy hom nay',
    'columns'     => ['Kim loai','Gia (USD/oz)','Thay doi','% 30 ngay'],
    'rows'        => [
        ['Vang (XAU)','2,918.45','+12.30','+2.8%'],
        ['Bac (XAG)','32.45','+0.28','+3.1%'],
        ['Platinum (XPT)','985.60','+5.40','+1.5%'],
        ['Palladium (XPD)','975.30','-8.20','-2.1%'],
        ['Dong (HG)','4.15','+0.03','+1.2%'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
