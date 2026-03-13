@extends('gold.page-shell')

@section('page-label', 'Cong cu')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Tinh gia vang theo khoi luong',
    'toolDesc'     => 'Nhap khoi luong va don gia de tinh tong gia tri vang.',
    'fields'       => [
        ['label'=>'Khoi luong','type'=>'number','placeholder'=>'1','default'=>'1',],
        ['label'=>'Don vi','options'=>['Luong','Chi','Gram'],],
        ['label'=>'Don gia (VND/luong)','type'=>'number','placeholder'=>'92500000','default'=>'92500000',],
    ],
    'buttonLabel'  => 'Tinh gia',
    'instructions' => [
        'Nhap khoi luong vang ban muon tinh',
        'Chon don vi phu hop',
        'Nhap don gia hien tai hoac don gia ban mua',
        'Ket qua = Khoi luong x Don gia (quy ve luong)',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
