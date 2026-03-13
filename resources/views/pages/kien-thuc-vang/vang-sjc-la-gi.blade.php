@extends('gold.page-shell')

@section('page-label', 'Kien thuc')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Vang SJC la gi?',
    'toc'          => ['Gioi thieu vang SJC','Lich su hinh thanh','Vi tri tren thi truong','Cach mua ban vang SJC'],
    'sections'     => [
        ['heading'=>'Gioi thieu vang SJC','body'=>'<p>Vang SJC la thuong hieu vang mieng do Cong ty TNHH MTV Vang Bac Da Quy Sai Gon (SJC) san xuat. Day la thuong hieu vang duoc Nha nuoc uy quyen san xuat doc quyen khu 1 luong tai Viet Nam.</p>'],
        ['heading'=>'Lich su hinh thanh','body'=>'<p>SJC duoc thanh lap nam 1988, tro thanh don vi san xuat vang mieng lon nhat Viet Nam. Tu nam 2012, theo Nghi dinh 24, SJC la thuong hieu vang mieng quoc gia duy nhat.</p>'],
        ['heading'=>'Vi tri tren thi truong','body'=>'<p>Vang SJC chiem thi phan lon nhat trong giao dich vang mieng tai Viet Nam. Gia vang SJC thuong cao hon cac thuong hieu khac va gia quoc te quy doi do tinh khan hiem va thuong hieu manh.</p>'],
        ['heading'=>'Cach mua ban vang SJC','body'=>'<p>Ban co the mua ban vang SJC tai cac cua hang SJC, ngan hang (Vietcombank, BIDV, Eximbank...) va cac dai ly uy quyen tren toan quoc. Can mang theo CMND/CCCD khi giao dich.</p>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
