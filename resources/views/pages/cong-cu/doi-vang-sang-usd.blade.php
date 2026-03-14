@extends('gold.page-shell')

@section('page-label', 'Công cụ')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Đổi giá vàng VND sang USD',
    'toolDesc'     => 'Quy đổi giá vàng từ VND sang USD theo tỷ giá hiện tại.',
    'fields'       => [
        ['label'=>'Giá vàng (VND)','type'=>'number','placeholder'=>'92500000','default'=>'92500000',],
        ['label'=>'Tỷ giá USD/VND','type'=>'number','placeholder'=>'25400','default'=>'25400',],
    ],
    'buttonLabel'  => 'Quy đổi',
    'instructions' => [
        'Nhập giá vàng tính bằng VND',
        'Nhập tỷ giá USD/VND hiện tại',
        'Kết quả = Giá VND / Tỷ giá',
        'So sánh với giá quốc tế để thấy chênh lệch',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
