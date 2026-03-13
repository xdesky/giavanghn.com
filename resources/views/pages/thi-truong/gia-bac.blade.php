@extends('gold.page-shell')

@section('page-label', 'Thi truong')

@section('page-content')
@include('gold.sections.market', [
    'marketLabel' => 'Gia bac hom nay',
    'columns'     => ['San pham','Gia (VND/luong)','Thay doi'],
    'rows'        => [
        ['Bac 999 (The gioi)','32.45 USD/oz','+0.28'],
        ['Bac 999 Viet Nam','1,050,000','+15,000'],
        ['Bac 925 (trang suc)','890,000','+12,000'],
        ['Bac thanh (1kg)','28,500,000','+380,000'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
