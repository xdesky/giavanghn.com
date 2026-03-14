@extends('gold.page-shell')

@section('page-label', 'API')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">GoldPrice API</h2>
    <p class="text-sm text-slate-600 mb-4">Truy cap du lieu gia vang theo thoi gian thuc thong qua REST API. Phu hop cho ung dung tai chinh, bot giao dich va bao dien tu.</p>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/api/api-gia-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">API Gia vang</h3>
            <p class="mt-1 text-sm text-slate-500">Endpoints lay du lieu gia vang real-time</p>
        </a>
        <a href="/api/tai-lieu-api" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Tai lieu API</h3>
            <p class="mt-1 text-sm text-slate-500">Huong dan tich hop va vi du code</p>
        </a>
    </div>
    <div class="mt-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <h3 class="font-bold text-sm text-blue-900">Bat dau nhanh</h3>
        <pre class="mt-2 rounded-sm bg-slate-900 p-3 text-xs text-green-400 overflow-x-auto"><code>GET /dashboard-api/snapshot
Content-Type: application/json</code></pre>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-tools')
@endsection
