@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $thisYear = 2026;
    $allRows = \App\Models\SjcChartPrice::whereYear('price_date', $thisYear)->orderBy('price_date')->get();
    $yearOpen = $allRows->first()->sell_million ?? 0;
    $yearClose = $allRows->last()->sell_million ?? 0;
    $yearHigh = $allRows->max('sell_million') ?: 0;
    $yearLow = $allRows->min('sell_million') ?: 0;
    $yearChangePct = $yearOpen > 0 ? (($yearClose - $yearOpen) / $yearOpen * 100) : 0;
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type":"Question","name":"Giá vàng SJC năm 2026 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2026 (đang cập nhật), giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu. Hiện tại ở mức {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Tháng nào giá vàng SJC tăng mạnh nhất 2026?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu OHLC và phần trăm thay đổi. Dữ liệu đang được cập nhật liên tục."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2026 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Tính đến thời điểm hiện tại, giá SJC cao nhất năm 2026 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
@php
    $thisYear = 2026;
    $yearEvents = [
        '<strong>Năm 2026 đang diễn ra</strong> — dữ liệu được cập nhật liên tục. Các sự kiện nổi bật sẽ được bổ sung khi có đủ thông tin.',
    ];
    $yearFactors = [
        '<strong>Chính sách tiền tệ FED:</strong> Thị trường theo dõi sát lộ trình lãi suất của FED, kỳ vọng tiếp tục nới lỏng trong 2026.',
        '<strong>Thuế quan và thương mại:</strong> Diễn biến đàm phán thương mại Mỹ-Trung và các đối tác khác tiếp tục ảnh hưởng giá vàng.',
        '<strong>NHTW mua vàng:</strong> Xu hướng de-dollarization và tích trữ vàng của NHTW các nước tiếp tục là động lực hỗ trợ giá dài hạn.',
        '<strong>Địa chính trị:</strong> Các điểm nóng toàn cầu (Nga-Ukraine, Trung Đông, biển Đông) tiếp tục tác động đến nhu cầu trú ẩn vàng.',
    ];
    $yearAnalysis = 'Năm 2026 đang diễn ra, dữ liệu kỹ thuật sẽ được cập nhật liên tục. Theo dõi các mức hỗ trợ và kháng cự quan trọng qua biểu đồ phía trên.';
@endphp

@include('gold.sections.history-year')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.am5 || !window.am5xy) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('2026-01-01').getTime();
        var yearEnd = new Date('2026-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm 2026</p>'; return; }

        document.getElementById('historyYearChart').innerHTML = '';
        var root = am5.Root.new('historyYearChart');
        if (root._logo) root._logo.dispose();
        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(am5xy.XYChart.new(root, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX', layout: root.verticalLayout }));
        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, { baseInterval: { timeUnit: 'day', count: 1 }, renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 60 }), dateFormats: { day: 'dd/MM' }, periodChangeDateFormats: { day: 'dd/MM', month: 'MM/yyyy' } }));
        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, { renderer: am5xy.AxisRendererY.new(root, {}), numberFormat: '#,###.##' }));

        var series = chart.series.push(am5xy.LineSeries.new(root, { name: 'SJC (bán)', xAxis: xAxis, yAxis: yAxis, valueYField: 'value', valueXField: 'dateTs', stroke: am5.color('#b8860b'), tooltip: am5.Tooltip.new(root, { labelText: '{valueY.formatNumber("#,###.##")} tr\n{valueX.formatDate("dd/MM/yyyy")}' }) }));
        series.strokes.template.setAll({ strokeWidth: 2 });
        series.fills.template.setAll({ visible: true, fillOpacity: 0.08, fill: am5.color('#b8860b') });
        series.data.setAll(data);

        chart.set('cursor', am5xy.XYCursor.new(root, { behavior: 'zoomX' }));
        chart.appear(800, 100);
    }).catch(function(){ document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không thể tải dữ liệu</p>'; });
});
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
