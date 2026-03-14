@extends('gold.page-shell')

@section('page-label', 'Thị trường')

@section('page-content')
@include('gold.sections.market', [
    'marketLabel' => 'Giá bạc hôm nay',
    'columns'     => ['Sản phẩm','Giá (VND/lượng)','Thay đổi'],
    'rows'        => [
        ['Bạc 999 (Thế giới)','32.45 USD/oz','+0.28'],
        ['Bạc 999 Việt Nam','1,050,000','+15,000'],
        ['Bạc 925 (trang sức)','890,000','+12,000'],
        ['Bạc thanh (1kg)','28,500,000','+380,000'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
