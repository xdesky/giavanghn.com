@extends('gold.page-shell')

@section('page-label', 'Du bao')

@section('page-content')
@include('gold.sections.forecast', [
    'periodLabel' => 'thang nay',
    'bullPrice'   => '95,000,000',
    'bullChange'  => '+2.7%',
    'basePrice'   => '93,500,000',
    'baseChange'  => '+1.1%',
    'bearPrice'   => '90,000,000',
    'bearChange'  => '-2.7%',
    'analysis'    => 'Xu huong tang trung han duoc ho tro boi nhu cau mua vao cua ngan hang trung uong va lo ngai lam phat. Kich ban tieu cuc neu Fed tang lai suat bat ngo.',
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
