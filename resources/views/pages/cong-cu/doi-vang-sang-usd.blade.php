@extends('gold.page-shell')

@section('page-label', 'Cong cu')

@section('page-content')
@include('gold.sections.tool', [
    'toolTitle'    => 'Doi gia vang VND sang USD',
    'toolDesc'     => 'Quy doi gia vang tu VND sang USD theo ty gia hien tai.',
    'fields'       => [
        ['label'=>'Gia vang (VND)','type'=>'number','placeholder'=>'92500000','default'=>'92500000',],
        ['label'=>'Ty gia USD/VND','type'=>'number','placeholder'=>'25400','default'=>'25400',],
    ],
    'buttonLabel'  => 'Quy doi',
    'instructions' => [
        'Nhap gia vang tinh bang VND',
        'Nhap ty gia USD/VND hien tai',
        'Ket qua = Gia VND / Ty gia',
        'So sanh voi gia quoc te de thay chenh lech',
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
