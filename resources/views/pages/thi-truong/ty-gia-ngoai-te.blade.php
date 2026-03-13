@extends('gold.page-shell')

@section('page-label', 'Thi truong')

@section('page-content')
@include('gold.sections.market', [
    'marketLabel' => 'Ty gia ngoai te hom nay',
    'columns'     => ['Ngoai te','Mua TM','Mua CK','Ban'],
    'rows'        => [
        ['USD','25,150','25,250','25,480'],
        ['EUR','26,950','27,150','27,680'],
        ['GBP','31,800','32,100','32,750'],
        ['JPY','165','168','172'],
        ['CHF','28,200','28,450','29,000'],
        ['AUD','16,350','16,500','16,850'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
