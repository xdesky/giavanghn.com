@extends('gold.page-shell')

@section('page-label', 'Quốc tế')

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FinancialProduct",
    "name": "Giá vàng XAU/GBP hôm nay",
    "description": "Biểu đồ và bảng giá vàng XAU/GBP (vàng quốc tế giao dịch bằng Bảng Anh) cập nhật liên tục.",
    "url": "{{ url('/gia-vang-the-gioi/xau-gbp') }}",
    "provider": {"@@type": "Organization", "name": "GiaVangHN.vn"}
}
</script>
@endpush

@section('page-content')
@php $symbolKey = 'XAU/GBP'; @endphp
@include('gold.sections.world-price-detail', ['symbolKey' => $symbolKey])
@include('gold.sections.world-intro', ['symbolKey' => $symbolKey])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
