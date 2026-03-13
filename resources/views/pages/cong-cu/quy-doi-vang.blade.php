@extends('gold.page-shell')

@section('page-label', 'Cong cu')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Quy doi don vi vang',
    'toolDesc'     => 'Chuyen doi nhanh giua cac don vi: luong, chi, gram, ounce troy.',
    'fields'       => [
        ['label'=>'Gia tri','type'=>'number','placeholder'=>'1','default'=>'1',],
        ['label'=>'Tu don vi','options'=>['Luong','Chi','Gram','Ounce troy'],],
        ['label'=>'Sang don vi','options'=>['Gram','Chi','Luong','Ounce troy'],],
    ],
    'buttonLabel'  => 'Quy doi',
    'instructions' => [
        '1 luong = 37.5 gram',
        '1 luong = 10 chi',
        '1 ounce troy = 31.1035 gram',
        '1 luong = 1.2057 ounce troy',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
