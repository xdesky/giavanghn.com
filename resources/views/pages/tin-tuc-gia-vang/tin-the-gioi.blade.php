@extends('gold.page-shell')

@section('page-label', 'Tin tức')

@section('page-content')
@include('gold.sections.news-list', [
    'category'      => 'world',
    'categoryLabel' => 'Tin thế giới',
    'articles'      => [
        ['icon'=>'📊','title'=>'Giá vàng phục hồi mạnh sau phiên giảm sâu','excerpt'=>'Giá vàng SJC tăng 500,000 đồng/lượng trong phiên sáng nay sau khi thị trường quốc tế phát tín hiệu tích cực từ chính sách Fed.','date'=>'07/03/2026'],
        ['icon'=>'💰','title'=>'USD suy yếu đẩy giá vàng thế giới lên đỉnh mới','excerpt'=>'Chỉ số DXY giảm 0.3%, hỗ trợ giá vàng XAU/USD vượt mốc 2,920 USD/oz lần đầu trong tuần.','date'=>'06/03/2026'],
        ['icon'=>'🏦','title'=>'Ngân hàng trung ương các nước tiếp tục mua vàng','excerpt'=>'Theo dữ liệu WGC, quý I/2026, các NHTW mua ròng 280 tấn vàng, tăng 15% so với cùng kỳ năm trước.','date'=>'05/03/2026'],
        ['icon'=>'🌍','title'=>'Căng thẳng địa chính trị đẩy nhu cầu trú ẩn an toàn','excerpt'=>'Tình hình bất ổn ở Trung Đông và biển Đông làm tăng nhu cầu vàng như tài sản trú ẩn an toàn.','date'=>'04/03/2026'],
        ['icon'=>'📈','title'=>'Phân tích kỹ thuật: Vàng hình thành đáy nền tảng','excerpt'=>'Mẫu hình giá hiện tại cho thấy vàng đang tích luỹ trong vùng 2,900-2,930 trước khi có đột phá lớn.','date'=>'03/03/2026'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
