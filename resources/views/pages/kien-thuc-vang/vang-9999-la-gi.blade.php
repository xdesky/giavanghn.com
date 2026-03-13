@extends('gold.page-shell')

@section('page-label', 'Kien thuc')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Vang 9999 la gi?',
    'toc'          => ['The nao la vang 9999?','Dac diem cua vang 9999','Phan biet vang 9999 va cac loai khac','Gia tri dau tu cua vang 9999'],
    'sections'     => [
        ['heading'=>'The nao la vang 9999?','body'=>'<p>Vang 9999 (hay vang 4 so 9) la loai vang co do tinh khiet cao nhat, dat 99.99% vang nguyen chat. Day la tieu chuan cao nhat trong nganh cong nghiep vang tren toan the gioi.</p><p class=\"mt-2\">O Viet Nam, vang 9999 duoc giao dich pho bien duoi dang vang mieng (SJC, DOJI, PNJ) va vang nhan tron.</p>'],
        ['heading'=>'Dac diem cua vang 9999','body'=>'<ul class=\"list-disc list-inside space-y-1 mt-1\"><li>Do tinh khiet: 99.99%</li><li>Mau sac: Vang dam, bong</li><li>Do mem: Mem hon vang 18K, 14K</li><li>Khong bi oxy hoa hay bien mau</li><li>De dang kiem dinh bang phuong phap hoa hoc va dien tu</li></ul>'],
        ['heading'=>'Phan biet vang 9999 va cac loai khac','body'=>'<p>Vang 9999 khac voi vang 24K (99.9%), vang 18K (75%), vang 14K (58.5%). Do tinh khiet cao hon nen vang 9999 co gia tri dau tu tot hon nhung it phu hop lam trang suc do mem.</p>'],
        ['heading'=>'Gia tri dau tu cua vang 9999','body'=>'<p>Vang 9999 duoc xem la kenh dau tu an toan, bao toan gia tri truoc lam phat. Nha dau tu thuong mua vang 9999 duoi dang vang mieng SJC hoac vang nhan de tich tru tai san dai han.</p>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
