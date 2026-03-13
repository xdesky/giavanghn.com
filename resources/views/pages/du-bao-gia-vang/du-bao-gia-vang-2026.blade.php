@extends('gold.page-shell')

@section('page-label', 'Du bao')

@section('page-content')
@include('gold.sections.forecast', [
    'periodLabel' => 'nam 2026',
    'bullPrice'   => '100,000,000',
    'bullChange'  => '+8.1%',
    'basePrice'   => '95,000,000',
    'baseChange'  => '+2.7%',
    'bearPrice'   => '85,000,000',
    'bearChange'  => '-8.1%',
    'analysis'    => 'Nam 2026, gia vang duoc ky vong duy tri xu huong tang dai han nho cac yeu to: bat on dia chinh tri, lam phat toan cau va chinh sach tien te noi long. Goldman Sachs du bao XAU/USD dat 3,100 USD/oz cuoi nam.',
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
