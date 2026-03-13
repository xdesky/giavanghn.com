@extends('gold.page-shell')

@section('page-label', 'Cong cu')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Tinh lai dau tu vang',
    'toolDesc'     => 'Tinh loi nhuan hoac lo tu khoan dau tu vang cua ban.',
    'fields'       => [
        ['label'=>'Gia mua vao (VND/luong)','type'=>'number','placeholder'=>'85000000','default'=>'85000000',],
        ['label'=>'Gia hien tai (VND/luong)','type'=>'number','placeholder'=>'92500000','default'=>'92500000',],
        ['label'=>'So luong (luong)','type'=>'number','placeholder'=>'1','default'=>'1',],
    ],
    'buttonLabel'  => 'Tinh lai/lo',
    'instructions' => [
        'Nhap gia ban da mua vang',
        'Nhap gia vang hien tai',
        'Nhap so luong luong da mua',
        'Loi nhuan = (Gia hien tai - Gia mua) x So luong',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
