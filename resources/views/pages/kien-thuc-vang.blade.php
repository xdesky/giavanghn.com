@extends('gold.page-shell')

@section('page-label', 'Kiến thức')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Kiến thức về vàng</h2>
    <div class="grid gap-5">
        <a href="/kien-thuc-vang/vang-9999-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vàng 9999 là gì?</h3>
            <p class="mt-1 text-sm text-slate-500">Tìm hiểu về vàng 4 số 9 và cách phân biệt với các loại vàng khác trên thị trường.</p>
        </a>
        <a href="/kien-thuc-vang/vang-sjc-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vàng SJC là gì?</h3>
            <p class="mt-1 text-sm text-slate-500">Lịch sử hình thành, đặc điểm và vị trí của vàng SJC trong thị trường Việt Nam.</p>
        </a>
        <a href="/kien-thuc-vang/nen-mua-vang-nao" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Nên mua vàng nào?</h3>
            <p class="mt-1 text-sm text-slate-500">So sánh vàng miếng, vàng nhẫn, vàng trang sức để chọn loại phù hợp với mục đích.</p>
        </a>
        <a href="/kien-thuc-vang/cach-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Cách đầu tư vàng hiệu quả</h3>
            <p class="mt-1 text-sm text-slate-500">Hướng dẫn chiến lược đầu tư vàng cho người mới bắt đầu.</p>
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
