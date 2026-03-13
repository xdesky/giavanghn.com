@extends('gold.page-shell')

@section('page-label', 'Quốc tế')

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FinancialProduct",
    "name": "Giá bạch kim XPT/USD hôm nay",
    "description": "Biểu đồ và bảng giá bạch kim XPT/USD (bạch kim quốc tế giao dịch bằng đô la Mỹ) cập nhật liên tục.",
    "url": "{{ url('/gia-vang-the-gioi/xpt-usd') }}",
    "provider": {"@@type": "Organization", "name": "GiaVangHN.vn"}
}
</script>
@endpush

@section('page-content')
@php $symbolKey = 'XPT/USD'; @endphp
@include('gold.sections.world-price-detail', ['symbolKey' => $symbolKey])
@include('gold.sections.world-intro', ['symbolKey' => $symbolKey])
@endsection

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
