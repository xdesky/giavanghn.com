@extends('gold.page-shell')

@section('page-label', 'Cập nhật giá vàng')

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
@include('gold.sections.today-price')

{{-- Liên kết thương hiệu --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-3 flex items-center gap-2">
        <i data-lucide="store" class="h-5 w-5"></i> Giá vàng theo thương hiệu
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
    <h2 class="flex items-center gap-2 text-xl font-bold text-[#001061] border-l-4 border-amber-400 pl-3 !mt-0">Giá vàng hôm nay {{ now()->format('d/m/Y') }}</h2>
    <p><strong>Giá vàng hôm nay</strong> được cập nhật liên tục từ 8 thương hiệu uy tín nhất Việt Nam: SJC, DOJI, PNJ, Bảo Tín Minh Châu, Phú Quý, Mi Hồng, Bảo Tín Mạnh Hải và Ngọc Thẩm. Bảng giá bao gồm giá mua vào, bán ra vàng miếng SJC và vàng nhẫn 9999.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Các loại vàng phổ biến</h3>
    <p><strong>Vàng miếng SJC</strong> là thương hiệu vàng quốc gia, có tính thanh khoản cao nhất. <strong>Vàng nhẫn 9999</strong> (còn gọi vàng nhẫn tròn) có độ tinh khiết 99.99%, giá thường thấp hơn vàng miếng SJC và sát giá thế giới quy đổi hơn.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Yếu tố ảnh hưởng giá vàng</h3>
    <p>Giá vàng trong nước chịu tác động từ: giá vàng thế giới (XAU/USD), tỷ giá USD/VND, chính sách lãi suất của Fed và Ngân hàng Nhà nước Việt Nam, cung cầu thị trường nội địa và tâm lý nhà đầu tư trước các sự kiện kinh tế – chính trị.</p>

    <h3 class="text-lg font-semibold text-slate-800 border-l-4 border-amber-300 pl-3">Nên mua vàng ở đâu?</h3>
    <p>Nhà đầu tư nên so sánh giá giữa các thương hiệu trước khi giao dịch. Vàng nhẫn 9999 thường có chênh lệch mua-bán thấp hơn, phù hợp với giao dịch ngắn hạn. Mua trực tiếp tại cửa hàng chính hãng hoặc đại lý ủy quyền để đảm bảo chất lượng.</p>
</article>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-tools')
@endsection
