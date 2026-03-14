@extends('gold.page-shell')

@section('page-label', 'Công cụ')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Quy đổi đơn vị vàng',
    'toolDesc'     => 'Chuyển đổi nhanh giữa các đơn vị: lượng, chỉ, gram, ounce troy.',
    'fields'       => [
        ['label'=>'Giá trị','type'=>'number','placeholder'=>'1','default'=>'1',],
        ['label'=>'Từ đơn vị','options'=>['Lượng','Chỉ','Gram','Ounce troy'],],
        ['label'=>'Sang đơn vị','options'=>['Gram','Chỉ','Lượng','Ounce troy'],],
    ],
    'buttonLabel'  => 'Quy đổi',
    'instructions' => [
        '1 lượng = 37.5 gram',
        '1 lượng = 10 chỉ',
        '1 ounce troy = 31.1035 gram',
        '1 lượng = 1.2057 ounce troy',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
