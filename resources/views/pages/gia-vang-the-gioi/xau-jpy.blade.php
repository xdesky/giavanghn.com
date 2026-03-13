@extends('gold.page-shell')

@section('page-label', 'Quốc tế')

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FinancialProduct",
    "name": "Giá vàng XAU/JPY hôm nay",
    "description": "Biểu đồ và bảng giá vàng XAU/JPY (vàng quốc tế giao dịch bằng Yên Nhật) cập nhật liên tục.",
    "url": "{{ url('/gia-vang-the-gioi/xau-jpy') }}",
    "provider": {"@@type": "Organization", "name": "GiaVangHN.vn"}
}
</script>
@endpush

@section('page-content')
@php $symbolKey = 'XAU/JPY'; @endphp
@include('gold.sections.world-price-detail', ['symbolKey' => $symbolKey])
@include('gold.sections.world-intro', ['symbolKey' => $symbolKey])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
