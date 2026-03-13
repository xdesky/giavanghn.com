@extends('gold.page-shell')

@section('page-label', 'Thi truong')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Tong quan thi truong</h2>
    <div class="grid gap-3 sm:grid-cols-2">
        <a href="/thi-truong/gia-xang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⛽</p>
            <h3 class="font-bold text-slate-900">Gia xang</h3>
            <p class="mt-1 text-sm text-slate-500">Cap nhat gia xang dau trong nuoc</p>
        </a>
        <a href="/thi-truong/ty-gia-ngoai-te" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💵</p>
            <h3 class="font-bold text-slate-900">Ty gia ngoai te</h3>
            <p class="mt-1 text-sm text-slate-500">Ty gia cac dong tien chinh</p>
        </a>
        <a href="/thi-truong/gia-bac" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🥈</p>
            <h3 class="font-bold text-slate-900">Gia bac</h3>
            <p class="mt-1 text-sm text-slate-500">Gia bac the gioi va trong nuoc</p>
        </a>
        <a href="/thi-truong/gia-kim-loai" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🔩</p>
            <h3 class="font-bold text-slate-900">Gia kim loai</h3>
            <p class="mt-1 text-sm text-slate-500">Bac, platinum, palladium va dong</p>
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
