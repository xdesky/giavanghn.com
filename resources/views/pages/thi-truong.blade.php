@extends('gold.page-shell')

@section('page-label', 'Thị trường')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Tổng quan thị trường</h2>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/thi-truong/gia-xang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⛽</p>
            <h3 class="font-bold text-slate-900">Giá xăng</h3>
            <p class="mt-1 text-sm text-slate-500">Cập nhật giá xăng dầu trong nước</p>
        </a>
        <a href="/thi-truong/ty-gia-ngoai-te" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💵</p>
            <h3 class="font-bold text-slate-900">Tỷ giá ngoại tệ</h3>
            <p class="mt-1 text-sm text-slate-500">Tỷ giá các đồng tiền chính</p>
        </a>
        <a href="/thi-truong/gia-bac" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🥈</p>
            <h3 class="font-bold text-slate-900">Giá bạc</h3>
            <p class="mt-1 text-sm text-slate-500">Giá bạc thế giới và trong nước</p>
        </a>
        <a href="/thi-truong/gia-kim-loai" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🔩</p>
            <h3 class="font-bold text-slate-900">Giá kim loại</h3>
            <p class="mt-1 text-sm text-slate-500">Bạc, platinum, palladium và đồng</p>
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
