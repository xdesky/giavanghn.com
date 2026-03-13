@extends('gold.page-shell')

@section('page-label', 'API')

@section('page-content')
@include('gold.sections.api-doc', [
    'apiTitle'  => 'Tai lieu API',
    'apiDesc'   => 'Huong dan tich hop GoldPrice API vao ung dung cua ban.',
    'endpoints' => [
        [
            'method'   => 'GET',
            'path'     => '/dashboard-api/snapshot',
            'desc'     => 'Endpoint chinh de lay gia vang. Khong can xac thuc. Rate limit: 60 req/min.',
            'params'   => [
                ['name'=>'format','type'=>'string','desc'=>'Dinh dang tra ve: json (mac dinh)'],
            ],
            'response' => '// JavaScript fetch
const res = await fetch(\'/dashboard-api/snapshot\');
const data = await res.json();
console.log(data.usCard.price);',
        ],
        [
            'method'   => 'POST',
            'path'     => '/dashboard-api/subscribe',
            'desc'     => 'Dang ky nhan thong bao gia vang qua email.',
            'params'   => [
                ['name'=>'name','type'=>'string','desc'=>'Ten nguoi dang ky'],
                ['name'=>'email','type'=>'string','desc'=>'Dia chi email'],
                ['name'=>'channels','type'=>'array','desc'=>'Kenh nhan tin: email, sms'],
            ],
            'response' => '{\"ok\": true, \"message\": \"Dang ky thanh cong\"}',
        ],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
