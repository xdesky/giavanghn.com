@extends('gold.page-shell')

@section('page-label', 'So sanh')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">So sanh gia vang</h2>
    <div class="grid gap-3">
        <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">SJC</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">The gioi</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Chenh: 18.5 trieu</span>
        </a>
        <a href="/so-sanh-gia-vang/sjc-vs-pnj" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">SJC</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">PNJ</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Chenh: 200,000</span>
        </a>
        <a href="/so-sanh-gia-vang/vang-vs-usd" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">Vang</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">USD</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Tuong quan: 0.85</span>
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
