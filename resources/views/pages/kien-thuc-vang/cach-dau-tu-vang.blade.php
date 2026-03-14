@extends('gold.page-shell')

@section('page-label', 'Kiến thức')

@section('page-content')
@include('gold.sections.knowledge', [
    'articleTitle' => 'Cách đầu tư vàng hiệu quả',
    'toc'          => ['Tại sao nên đầu tư vàng?','Các hình thức đầu tư vàng','Chiến lược DCA','Những sai lầm cần tránh'],
    'sections'     => [
        ['heading'=>'Tại sao nên đầu tư vàng?','body'=>'<p>Vàng là tài sản trú ẩn an toàn, bảo toàn giá trị trước lạm phát và bất ổn kinh tế. Trong 20 năm qua, giá vàng tăng trung bình 8-10%/năm, vượt xa gửi tiết kiệm.</p>'],
        ['heading'=>'Các hình thức đầu tư vàng','body'=>'<ul class=\"list-disc list-inside space-y-1 mt-1\"><li><strong>Mua vàng vật chất</strong>: Vàng miếng, vàng nhẫn - an toàn, đơn giản</li><li><strong>Tài khoản vàng</strong>: Mở tại ngân hàng, giao dịch online</li><li><strong>ETF vàng</strong>: Quỹ ETF theo dõi giá vàng (SPDR, iShares)</li><li><strong>Hợp đồng tương lai vàng</strong>: Mua bán quyền chọn trên sàn quốc tế</li></ul>'],
        ['heading'=>'Chiến lược DCA','body'=>'<p><strong>Dollar Cost Averaging (DCA)</strong> là chiến lược mua vàng định kỳ với số tiền cố định (ví dụ: mỗi tháng mua 1 chỉ). Giúp giảm rủi ro mua đỉnh và lấy giá trung bình tốt trong dài hạn.</p>'],
        ['heading'=>'Những sai lầm cần tránh','body'=>'<ul class=\"list-disc list-inside space-y-1 mt-1\"><li>Mua đuổi khi giá tăng nóng (FOMO)</li><li>Đầu tư toàn bộ vốn vào vàng</li><li>Không đa dạng hoá danh mục</li><li>Mua bán theo tin đồn, không phân tích</li><li>Không tính phí chênh lệch mua-bán</li></ul>'],
    ],
])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
