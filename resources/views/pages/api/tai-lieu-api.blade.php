@extends('gold.page-shell')

@section('page-label', 'API')

@section('page-content')
@include('gold.sections.api-doc', [
    'apiTitle'  => 'Tài liệu API',
    'apiDesc'   => 'Hướng dẫn tích hợp GoldPrice API vào ứng dụng của bạn.',
    'endpoints' => [
        [
            'method'   => 'GET',
            'path'     => '/dashboard-api/snapshot',
            'desc'     => 'Endpoint chính để lấy giá vàng. Không cần xác thực. Rate limit: 60 req/min.',
            'params'   => [
                ['name'=>'format','type'=>'string','desc'=>'Định dạng trả về: json (mặc định)'],
            ],
            'response' => '// JavaScript fetch
const res = await fetch(\'/dashboard-api/snapshot\');
const data = await res.json();
console.log(data.usCard.price);',
        ],
        [
            'method'   => 'POST',
            'path'     => '/dashboard-api/subscribe',
            'desc'     => 'Đăng ký nhận thông báo giá vàng qua email.',
            'params'   => [
                ['name'=>'name','type'=>'string','desc'=>'Tên người đăng ký'],
                ['name'=>'email','type'=>'string','desc'=>'Địa chỉ email'],
                ['name'=>'channels','type'=>'array','desc'=>'Kênh nhận tin: email, sms'],
            ],
            'response' => '{\"ok\": true, \"message\": \"Đăng ký thành công\"}',
        ],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
