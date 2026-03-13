@extends('gold.page-shell')

@section('page-label', 'Quốc tế')

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "{{ $title }}",
    "description": "{{ $description }}",
    "dateModified": "{{ now()->toIso8601String() }}",
    "breadcrumb": {
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {"@@type": "ListItem", "position": 1, "name": "Trang chủ", "item": "{{ url('/') }}"},
            {"@@type": "ListItem", "position": 2, "name": "{{ $title }}", "item": "{{ url('/' . $path) }}"}
        ]
    }
}
</script>
@endpush

@section('page-content')
@include('gold.sections.world-price')

{{-- Liên kết theo cặp tiền --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="coins" class="h-5 w-5"></i> Giá theo cặp tiền & kim loại
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 text-sm">
        @foreach ($children as $child)
            <a href="/{{ $child['path'] }}" class="flex items-center gap-2 rounded-sm border border-slate-200 px-3 py-2.5 font-medium text-blue-700 no-underline transition hover:bg-blue-50">
                <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i>
                {{ $child['title'] }}
            </a>
        @endforeach
    </div>
</div>

{{-- Bài viết SEO --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-p:text-slate-700 prose-p:leading-relaxed">
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-blue-400 pl-3 !mt-0">Giá vàng thế giới hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng thế giới</strong> (XAU/USD) là mức giá giao dịch vàng quốc tế tính bằng đô la Mỹ trên mỗi ounce troy (31.1035 gram). Đây là chỉ số tham chiếu quan trọng nhất cho thị trường vàng toàn cầu và ảnh hưởng trực tiếp đến giá vàng trong nước Việt Nam.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Các cặp tiền vàng phổ biến</h3>
    <p>Ngoài XAU/USD, giá vàng còn được giao dịch bằng nhiều đồng tiền: <strong>XAU/EUR</strong> (Euro), <strong>XAU/GBP</strong> (Bảng Anh), <strong>XAU/CNY</strong> (Nhân dân tệ) và <strong>XAU/JPY</strong> (Yên Nhật). Mỗi cặp phản ánh quan hệ giữa giá vàng và sức mạnh đồng tiền tương ứng.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Kim loại quý khác</h3>
    <p>Bên cạnh vàng, thị trường kim loại quý còn có <strong>bạc (XAG/USD)</strong>, <strong>bạch kim (XPT/USD)</strong> và <strong>palladium (XPD/USD)</strong>. Tỷ lệ Gold/Silver Ratio (vàng/bạc) là chỉ báo quan trọng để đánh giá giá trị tương đối giữa hai kim loại.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Yếu tố ảnh hưởng giá vàng thế giới</h3>
    <p>Giá vàng quốc tế chịu tác động từ: chính sách lãi suất của Fed và các ngân hàng trung ương lớn, chỉ số USD (DXY), lợi suất trái phiếu Mỹ, lạm phát, căng thẳng địa chính trị, và nhu cầu trú ẩn an toàn. Khi DXY giảm hoặc bất ổn tăng, giá vàng thường tăng.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-blue-300 pl-3">Mối liên hệ với giá vàng Việt Nam</h3>
    <p>Giá vàng SJC trong nước = Giá XAU/USD × tỷ giá USD/VND ÷ 31.1035 × 37.5 + premium. Chênh lệch premium phụ thuộc vào chính sách nhập khẩu vàng, cung cầu nội địa và tâm lý thị trường. Nhà đầu tư nên theo dõi cả hai thị trường để có quyết định chính xác.</p>
</article>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
