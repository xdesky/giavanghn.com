@extends('gold.page-shell')

@section('page-label', 'Lich su')

@section('page-content')
@include('gold.sections.history', [
    'year'      => '2026',
    'yearLabel' => 'nam 2026',
    'months'    => [
        ['label'=>'Thang 1','open'=>'89,000,000','high'=>'90,500,000','low'=>'88,200,000','close'=>'90,000,000','change'=>'+1,000,000'],
        ['label'=>'Thang 2','open'=>'90,000,000','high'=>'91,800,000','low'=>'89,500,000','close'=>'91,200,000','change'=>'+1,200,000'],
        ['label'=>'Thang 3','open'=>'91,200,000','high'=>'92,800,000','low'=>'90,800,000','close'=>'92,500,000','change'=>'+1,300,000'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
