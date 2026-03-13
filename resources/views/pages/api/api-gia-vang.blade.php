@extends('gold.page-shell')

@section('page-label', 'API')

@section('page-content')
@include('gold.sections.api-doc', [
    'apiTitle'  => 'API Gia vang',
    'apiDesc'   => 'Danh sach endpoint lay du lieu gia vang theo thoi gian thuc.',
    'endpoints' => [
        [
            'method'   => 'GET',
            'path'     => '/dashboard-api/snapshot',
            'desc'     => 'Lay toan bo du lieu gia vang hien tai (trong nuoc + the gioi).',
            'params'   => [
            ],
            'response' => '{
    \"usCard\": {
        \"label\": \"XAU\\/USD\",
        \"price\": \"2,918.45\",
        \"change\": \"+12.30\"
    },
    \"sjcCard\": {
        \"label\": \"SJC 1L\",
        \"price\": \"92,500,000\",
        \"change\": \"+500,000\"
    }
}',
        ],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
