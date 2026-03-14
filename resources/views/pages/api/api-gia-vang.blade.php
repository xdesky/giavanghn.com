@extends('gold.page-shell')

@section('page-label', 'API')

@section('page-content')
@include('gold.sections.api-doc', [
    'apiTitle'  => 'API Giá vàng',
    'apiDesc'   => 'Danh sách endpoint lấy dữ liệu giá vàng theo thời gian thực.',
    'endpoints' => [
        [
            'method'   => 'GET',
            'path'     => '/dashboard-api/snapshot',
            'desc'     => 'Lấy toàn bộ dữ liệu giá vàng hiện tại (trong nước + thế giới).',
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
