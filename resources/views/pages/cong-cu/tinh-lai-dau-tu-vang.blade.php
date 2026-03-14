@extends('gold.page-shell')

@section('page-label', 'Công cụ')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Tính lãi đầu tư vàng',
    'toolDesc'     => 'Tính lợi nhuận hoặc lỗ từ khoản đầu tư vàng của bạn.',
    'fields'       => [
        ['label'=>'Giá mua vào (VND/lượng)','type'=>'number','placeholder'=>'85000000','default'=>'85000000',],
        ['label'=>'Giá hiện tại (VND/lượng)','type'=>'number','placeholder'=>'92500000','default'=>'92500000',],
        ['label'=>'Số lượng (lượng)','type'=>'number','placeholder'=>'1','default'=>'1',],
    ],
    'buttonLabel'  => 'Tính lãi/lỗ',
    'instructions' => [
        'Nhập giá bạn đã mua vàng',
        'Nhập giá vàng hiện tại',
        'Nhập số lượng lượng đã mua',
        'Lợi nhuận = (Giá hiện tại - Giá mua) x Số lượng',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
