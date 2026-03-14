@extends('gold.page-shell')

@section('page-label', 'Công cụ')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Bộ công cụ giá vàng</h2>
    <div class="grid gap-3 sm:grid-cols-2">
        <a href="/cong-cu/quy-doi-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⚖️</p>
            <h3 class="font-bold text-slate-900">Quy đổi vàng</h3>
            <p class="mt-1 text-sm text-slate-500">Quy đổi giữa lượng, chỉ, gram, ounce</p>
        </a>
        <a href="/cong-cu/tinh-gia-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🧮</p>
            <h3 class="font-bold text-slate-900">Tính giá vàng</h3>
            <p class="mt-1 text-sm text-slate-500">Tính giá trị vàng theo khối lượng</p>
        </a>
        <a href="/cong-cu/tinh-lai-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">📈</p>
            <h3 class="font-bold text-slate-900">Tính lãi đầu tư vàng</h3>
            <p class="mt-1 text-sm text-slate-500">Tính lợi nhuận, lỗ trên khoản đầu tư</p>
        </a>
        <a href="/cong-cu/doi-vang-sang-usd" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💱</p>
            <h3 class="font-bold text-slate-900">Đổi vàng sang USD</h3>
            <p class="mt-1 text-sm text-slate-500">Quy đổi giá vàng VND sang USD</p>
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
