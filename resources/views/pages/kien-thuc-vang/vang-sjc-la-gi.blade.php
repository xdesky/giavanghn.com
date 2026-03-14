@extends('gold.page-shell')

@section('page-label', 'Kiến thức')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Vàng SJC là gì?',
    'toc'          => ['Giới thiệu vàng SJC','Lịch sử hình thành','Vị trí trên thị trường','Cách mua bán vàng SJC'],
    'sections'     => [
        ['heading'=>'Giới thiệu vàng SJC','body'=>'<p>Vàng SJC là thương hiệu vàng miếng do Công ty TNHH MTV Vàng Bạc Đá Quý Sài Gòn (SJC) sản xuất. Đây là thương hiệu vàng được Nhà nước ủy quyền sản xuất độc quyền khố 1 lượng tại Việt Nam.</p>'],
        ['heading'=>'Lịch sử hình thành','body'=>'<p>SJC được thành lập năm 1988, trở thành đơn vị sản xuất vàng miếng lớn nhất Việt Nam. Từ năm 2012, theo Nghị định 24, SJC là thương hiệu vàng miếng quốc gia duy nhất.</p>'],
        ['heading'=>'Vị trí trên thị trường','body'=>'<p>Vàng SJC chiếm thị phần lớn nhất trong giao dịch vàng miếng tại Việt Nam. Giá vàng SJC thường cao hơn các thương hiệu khác và giá quốc tế quy đổi do tính khan hiếm và thương hiệu mạnh.</p>'],
        ['heading'=>'Cách mua bán vàng SJC','body'=>'<p>Bạn có thể mua bán vàng SJC tại các cửa hàng SJC, ngân hàng (Vietcombank, BIDV, Eximbank...) và các đại lý ủy quyền trên toàn quốc. Cần mang theo CMND/CCCD khi giao dịch.</p>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
