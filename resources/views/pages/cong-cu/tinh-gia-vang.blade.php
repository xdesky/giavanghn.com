@extends('gold.page-shell')

@section('page-label', 'Công cụ')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Tính giá vàng theo khối lượng',
    'toolDesc'     => 'Nhập khối lượng và đơn giá để tính tổng giá trị vàng.',
    'fields'       => [
        ['label'=>'Khối lượng','type'=>'number','placeholder'=>'1','default'=>'1',],
        ['label'=>'Đơn vị','options'=>['Lượng','Chỉ','Gram'],],
        ['label'=>'Đơn giá (VND/lượng)','type'=>'number','placeholder'=>'92500000','default'=>'92500000',],
    ],
    'buttonLabel'  => 'Tính giá',
    'instructions' => [
        'Nhập khối lượng vàng bạn muốn tính',
        'Chọn đơn vị phù hợp',
        'Nhập đơn giá hiện tại hoặc đơn giá bạn mua',
        'Kết quả = Khối lượng x Đơn giá (quy về lượng)',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
