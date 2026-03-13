@extends('gold.page-shell')

@section('page-label', 'Kien thuc')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Nen mua vang nao?',
    'toc'          => ['Cac loai vang pho bien','Vang mieng vs Vang nhan','Vang trang suc co nen mua?','Loi khuyen chon vang'],
    'sections'     => [
        ['heading'=>'Cac loai vang pho bien','body'=>'<p>Tren thi truong Viet Nam co 3 loai vang chinh:</p><ul class=\"list-disc list-inside space-y-1 mt-1\"><li><strong>Vang mieng SJC</strong>: Vang mieng quoc gia, do tinh khiet 99.99%</li><li><strong>Vang nhan 9999</strong>: Vang tron, de giao dich, gia sat the gioi hon</li><li><strong>Vang trang suc</strong>: 18K-24K, co them phi gia cong</li></ul>'],
        ['heading'=>'Vang mieng vs Vang nhan','body'=>'<p><strong>Vang mieng SJC</strong> co gia cao hon do thuong hieu va tinh khan hiem, phu hop tich tru lon. <strong>Vang nhan 9999</strong> co gia gan voi gia quoc te hon, phu hop giao dich linh hoat va dau tu ngan han.</p>'],
        ['heading'=>'Vang trang suc co nen mua?','body'=>'<p>Vang trang suc khong nen mua de dau tu vi khi ban lai se bi tru phi gia cong (thuong 500,000 - 2,000,000 dong/chi). Chi nen mua de su dung va deo.</p>'],
        ['heading'=>'Loi khuyen chon vang','body'=>'<p><strong>Dau tu dai han</strong>: Vang mieng SJC<br><strong>Dau tu linh hoat</strong>: Vang nhan 9999<br><strong>Su dung ca nhan</strong>: Vang trang suc 18K-24K<br>Luon mua tai cua hang uy tin va giu hoa don, chung chi.</p>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
