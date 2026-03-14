@extends('gold.page-shell')

@section('page-label', 'Kiến thức')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Vàng 9999 là gì?',
    'toc'          => ['Thế nào là vàng 9999?','Đặc điểm của vàng 9999','Phân biệt vàng 9999 và các loại khác','Giá trị đầu tư của vàng 9999'],
    'sections'     => [
        ['heading'=>'Thế nào là vàng 9999?','body'=>'<p>Vàng 9999 (hay vàng 4 số 9) là loại vàng có độ tinh khiết cao nhất, đạt 99.99% vàng nguyên chất. Đây là tiêu chuẩn cao nhất trong ngành công nghiệp vàng trên toàn thế giới.</p><p class=\"mt-2\">Ở Việt Nam, vàng 9999 được giao dịch phổ biến dưới dạng vàng miếng (SJC, DOJI, PNJ) và vàng nhẫn trơn.</p>'],
        ['heading'=>'Đặc điểm của vàng 9999','body'=>'<ul class=\"list-disc list-inside space-y-1 mt-1\"><li>Độ tinh khiết: 99.99%</li><li>Màu sắc: Vàng đậm, bóng</li><li>Độ mềm: Mềm hơn vàng 18K, 14K</li><li>Không bị oxy hóa hay biến màu</li><li>Dễ dàng kiểm định bằng phương pháp hóa học và điện tử</li></ul>'],
        ['heading'=>'Phân biệt vàng 9999 và các loại khác','body'=>'<p>Vàng 9999 khác với vàng 24K (99.9%), vàng 18K (75%), vàng 14K (58.5%). Độ tinh khiết cao hơn nên vàng 9999 có giá trị đầu tư tốt hơn nhưng ít phù hợp làm trang sức do mềm.</p>'],
        ['heading'=>'Giá trị đầu tư của vàng 9999','body'=>'<p>Vàng 9999 được xem là kênh đầu tư an toàn, bảo toàn giá trị trước lạm phát. Nhà đầu tư thường mua vàng 9999 dưới dạng vàng miếng SJC hoặc vàng nhẫn để tích trữ tài sản dài hạn.</p>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
