@extends('gold.page-shell')

@section('page-label', 'Tin tuc')

@section('page-content')
@include('gold.sections.news-list', [
    'category'      => 'economy',
    'categoryLabel' => 'Tin kinh te',
    'articles'      => [
        ['icon'=>'📊','title'=>'Gia vang phuc hoi manh sau phien giam sau','excerpt'=>'Gia vang SJC tang 500,000 dong/luong trong phien sang nay sau khi thi truong quoc te phat tin hieu tich cuc tu chinh sach Fed.','date'=>'07/03/2026'],
        ['icon'=>'💰','title'=>'USD suy yeu day gia vang the gioi len dinh moi','excerpt'=>'Chi so DXY giam 0.3%, ho tro gia vang XAU/USD vuot moc 2,920 USD/oz lan dau trong tuan.','date'=>'06/03/2026'],
        ['icon'=>'🏦','title'=>'Ngan hang trung uong cac nuoc tiep tuc mua vang','excerpt'=>'Theo du lieu WGC, quy I/2026, cac NHTW mua rong 280 tan vang, tang 15% so voi cung ky nam truoc.','date'=>'05/03/2026'],
        ['icon'=>'🌍','title'=>'Cang thang dia chinh tri day nhu cau tru an toan','excerpt'=>'Tinh hinh bat on o Trung Dong va bien Dong lam tang nhu cau vang nhu tai san tru an toan an toan.','date'=>'04/03/2026'],
        ['icon'=>'📈','title'=>'Phan tich ky thuat: Vang hinh thanh dang nen tang','excerpt'=>'Mau hinh gia hien tai cho thay vang dang tich luy trong vung 2,900-2,930 truoc khi co dot pha lon.','date'=>'03/03/2026'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
