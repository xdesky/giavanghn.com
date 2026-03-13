@extends('gold.page-shell')

@section('page-label', 'Du bao')

@section('page-content')
@include('gold.sections.forecast', [
    'periodLabel' => 'tong hop',
    'bullPrice'   => '93,000,000',
    'bullChange'  => '+0.5%',
    'basePrice'   => '92,500,000',
    'baseChange'  => 'Di ngang',
    'bearPrice'   => '91,000,000',
    'bearChange'  => '-1.6%',
    'analysis'    => 'Phan tich tong hop cac kich ban du bao gia vang tu ngan han den dai han. Du lieu tu nhieu nguon phan tich ky thuat va co ban.',
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
