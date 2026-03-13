@extends('gold.page-shell')

@section('page-label', 'Thi truong')

@section('page-content')
@include('gold.sections.market', [
    'marketLabel' => 'Gia xang dau hom nay',
    'columns'     => ['San pham','Gia (VND/lit)','Thay doi'],
    'rows'        => [
        ['RON 95-III','23,650','+320'],
        ['RON 95-V','23,150','+280'],
        ['E5 RON 92','22,450','+250'],
        ['DO 0.05S','20,980','+180'],
        ['Dau hoa','20,350','+150'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
