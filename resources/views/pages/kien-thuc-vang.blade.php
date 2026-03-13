@extends('gold.page-shell')

@section('page-label', 'Kien thuc')

@section('page-content')
<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Kien thuc ve vang</h2>
    <div class="grid gap-3">
        <a href="/kien-thuc-vang/vang-9999-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vang 9999 la gi?</h3>
            <p class="mt-1 text-sm text-slate-500">Tim hieu ve vang 4 so 9 va cach phan biet voi cac loai vang khac tren thi truong.</p>
        </a>
        <a href="/kien-thuc-vang/vang-sjc-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vang SJC la gi?</h3>
            <p class="mt-1 text-sm text-slate-500">Lich su hinh thanh, dac diem va vi tri cua vang SJC trong thi truong Viet Nam.</p>
        </a>
        <a href="/kien-thuc-vang/nen-mua-vang-nao" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Nen mua vang nao?</h3>
            <p class="mt-1 text-sm text-slate-500">So sanh vang mieng, vang nhan, vang trang suc de chon loai phu hop voi muc dich.</p>
        </a>
        <a href="/kien-thuc-vang/cach-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Cach dau tu vang hieu qua</h3>
            <p class="mt-1 text-sm text-slate-500">Huong dan chien luoc dau tu vang cho nguoi moi bat dau.</p>
        </a>
    </div>
</div>
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@endsection
