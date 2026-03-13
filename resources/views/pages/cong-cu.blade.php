@extends('gold.page-shell')

@section('page-label', 'Cong cu')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Bo cong cu gia vang</h2>
    <div class="grid gap-3 sm:grid-cols-2">
        <a href="/cong-cu/quy-doi-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⚖️</p>
            <h3 class="font-bold text-slate-900">Quy doi vang</h3>
            <p class="mt-1 text-sm text-slate-500">Quy doi giua luong, chi, gram, ounce</p>
        </a>
        <a href="/cong-cu/tinh-gia-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🧮</p>
            <h3 class="font-bold text-slate-900">Tinh gia vang</h3>
            <p class="mt-1 text-sm text-slate-500">Tinh gia tri vang theo khoi luong</p>
        </a>
        <a href="/cong-cu/tinh-lai-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">📈</p>
            <h3 class="font-bold text-slate-900">Tinh lai dau tu vang</h3>
            <p class="mt-1 text-sm text-slate-500">Tinh loi nhuan, lo tren khoan dau tu</p>
        </a>
        <a href="/cong-cu/doi-vang-sang-usd" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💱</p>
            <h3 class="font-bold text-slate-900">Doi vang sang USD</h3>
            <p class="mt-1 text-sm text-slate-500">Quy doi gia vang VND sang USD</p>
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
