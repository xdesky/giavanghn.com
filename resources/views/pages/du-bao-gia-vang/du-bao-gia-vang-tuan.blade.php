@extends('gold.page-shell')

@section('page-label', 'Du bao')

@section('page-content')
@include('gold.sections.forecast', [
    'periodLabel' => 'tuan nay',
    'bullPrice'   => '93,200,000',
    'bullChange'  => '+0.8%',
    'basePrice'   => '92,800,000',
    'baseChange'  => '+0.3%',
    'bearPrice'   => '91,500,000',
    'bearChange'  => '-1.1%',
    'analysis'    => 'Gia vang duoc du bao tang nhe trong tuan nay nho ky vong Fed giu lai suat on dinh va dong USD suy yeu. Rui ro giam gia den tu bao cao viec lam My tot hon ky vong.',
    'factors'     => [
        ['name'=>'Chinh sach Fed','impact'=>'positive','label'=>'Ho tro tang'],
        ['name'=>'Chi so USD (DXY)','impact'=>'positive','label'=>'Dang giam'],
        ['name'=>'Lam phat toan cau','impact'=>'positive','label'=>'Van cao'],
        ['name'=>'Nhu cau NHTW','impact'=>'positive','label'=>'Tang manh'],
        ['name'=>'Rui ro dia chinh tri','impact'=>'neutral','label'=>'Trung binh'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
