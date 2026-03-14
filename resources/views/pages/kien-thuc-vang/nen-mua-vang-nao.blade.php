@extends('gold.page-shell')

@section('page-label', 'Kiến thức')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Nên mua vàng nào?',
    'toc'          => ['Các loại vàng phổ biến','Vàng miếng vs Vàng nhẫn','Vàng trang sức có nên mua?','Lời khuyên chọn vàng'],
    'sections'     => [
        ['heading'=>'Các loại vàng phổ biến','body'=>'<p>Trên thị trường Việt Nam có 3 loại vàng chính:</p><ul class=\"list-disc list-inside space-y-1 mt-1\"><li><strong>Vàng miếng SJC</strong>: Vàng miếng quốc gia, độ tinh khiết 99.99%</li><li><strong>Vàng nhẫn 9999</strong>: Vàng tròn, dễ giao dịch, giá sát thế giới hơn</li><li><strong>Vàng trang sức</strong>: 18K-24K, có thêm phí gia công</li></ul>'],
        ['heading'=>'Vàng miếng vs Vàng nhẫn','body'=>'<p><strong>Vàng miếng SJC</strong> có giá cao hơn do thương hiệu và tính khan hiếm, phù hợp tích trữ lớn. <strong>Vàng nhẫn 9999</strong> có giá gần với giá quốc tế hơn, phù hợp giao dịch linh hoạt và đầu tư ngắn hạn.</p>'],
        ['heading'=>'Vàng trang sức có nên mua?','body'=>'<p>Vàng trang sức không nên mua để đầu tư vì khi bán lại sẽ bị trừ phí gia công (thường 500,000 - 2,000,000 đồng/chỉ). Chỉ nên mua để sử dụng và đeo.</p>'],
        ['heading'=>'Lời khuyên chọn vàng','body'=>'<p><strong>Đầu tư dài hạn</strong>: Vàng miếng SJC<br><strong>Đầu tư linh hoạt</strong>: Vàng nhẫn 9999<br><strong>Sử dụng cá nhân</strong>: Vàng trang sức 18K-24K<br>Luôn mua tại cửa hàng uy tín và giữ hóa đơn, chứng chỉ.</p>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
