@extends('gold.page-shell')

@section('page-label', 'Kien thuc')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Cach dau tu vang hieu qua',
    'toc'          => ['Tai sao nen dau tu vang?','Cac hinh thuc dau tu vang','Chien luoc DCA','Nhung sai lam can tranh'],
    'sections'     => [
        ['heading'=>'Tai sao nen dau tu vang?','body'=>'<p>Vang la tai san tru an toan, bao toan gia tri truoc lam phat va bat on kinh te. Trong 20 nam qua, gia vang tang trung binh 8-10%/nam, vuot xa gui tiet kiem.</p>'],
        ['heading'=>'Cac hinh thuc dau tu vang','body'=>'<ul class=\"list-disc list-inside space-y-1 mt-1\"><li><strong>Mua vang vat chat</strong>: Vang mieng, vang nhan - an toan, don gian</li><li><strong>Tai khoan vang</strong>: Mo tai ngan hang, giao dich online</li><li><strong>ETF vang</strong>: Quy ETF theo doi gia vang (SPDR, iShares)</li><li><strong>Huan tien vang</strong>: Mua ban quyen chon tren san quoc te</li></ul>'],
        ['heading'=>'Chien luoc DCA','body'=>'<p><strong>Dollar Cost Averaging (DCA)</strong> la chien luoc mua vang dinh ky voi so tien co dinh (vi du: moi thang mua 1 chi). Giup giam rui ro mua dinh va lay gia trung binh tot trong dai han.</p>'],
        ['heading'=>'Nhung sai lam can tranh','body'=>'<ul class=\"list-disc list-inside space-y-1 mt-1\"><li>Mua duoi khi gia tang nong (FOMO)</li><li>Dau tu toan bo von vao vang</li><li>Khong da dang hoa danh muc</li><li>Mua ban theo tin don, khong phan tich</li><li>Khong tinh phi chenh lech mua-ban</li></ul>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
