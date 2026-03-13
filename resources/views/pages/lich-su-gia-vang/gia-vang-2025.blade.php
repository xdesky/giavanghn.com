@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $thisYear = 2025;
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
        {"@@type":"Question","name":"Giá vàng SJC năm 2025 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2025, giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu và kết thúc ở {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Tháng nào giá vàng SJC tăng mạnh nhất 2025?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu OHLC và phần trăm thay đổi để xác định tháng biến động mạnh nhất năm 2025."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2025 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất năm 2025 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
@php
    $thisYear = 2025;
    $yearEvents = [
        '<strong>Tháng 1:</strong> Trump nhậm chức (20/01). Ngay lập tức ký loạt sắc lệnh thuế quan — đe dọa chiến tranh thương mại 2.0. Vàng tăng mạnh làm "safe haven".',
        '<strong>Tháng 2:</strong> Trump công bố thuế 25% lên Canada, Mexico, EU và 10% bổ sung lên Trung Quốc. Thị trường tài chính toàn cầu chao đảo, XAU/USD vượt 2,900 USD/oz.',
        '<strong>Tháng 3:</strong> XAU/USD phá mốc lịch sử 3,000 USD/oz lần đầu tiên. FED giữ nguyên lãi suất nhưng phát tín hiệu có thể cắt giảm thêm nếu kinh tế yếu đi.',
        '<strong>Tháng 4:</strong> Trump tuyên bố "Liberation Day" — áp thuế toàn diện lên hầu hết đối tác thương mại. Vàng lập đỉnh mới liên tục. SJC trong nước phá kỷ lục.',
        '<strong>Tháng 5-6:</strong> FED cắt giảm 25bps. Đàm phán thương mại Mỹ-Trung có tín hiệu tích cực nhưng bất ổn vẫn cao. SJC biến động trong biên độ rộng.',
    ];
    $yearFactors = [
        '<strong>Thuế quan Trump 2.0:</strong> Chính sách thuế quan rộng khắp tạo bất ổn thương mại toàn cầu — yếu tố bullish mạnh nhất cho vàng 2025.',
        '<strong>FED tiếp tục nới lỏng:</strong> Cắt giảm lãi suất thêm trong bối cảnh kinh tế Mỹ giảm tốc do thuế quan. Lãi suất thực giảm hỗ trợ vàng.',
        '<strong>De-dollarization tăng tốc:</strong> Các nước BRICS và đối tác đẩy mạnh giao dịch bằng đồng nội tệ. NHTW tiếp tục mua vàng dự trữ.',
        '<strong>Địa chính trị:</strong> Nga-Ukraine chưa kết thúc, căng thẳng Trung Đông, biển Đông. Rủi ro toàn cầu ở mức cao kỷ lục.',
        '<strong>Lạm phát dai dẳng:</strong> Thuế quan đẩy giá hàng hóa lên, lạm phát Mỹ khó về mục tiêu 2%. Vàng được hưởng lợi từ vai trò phòng ngừa lạm phát.',
    ];
    $yearAnalysis = 'Năm 2025, vàng tiếp nối đà tăng mạnh từ 2024 với XAU/USD phá mốc 3,000 USD/oz lịch sử. Golden Cross (SMA 50 > SMA 200) duy trì liên tục. RSI thường xuyên trên 70 nhưng không tạo divergence bearish rõ ràng — xu hướng tăng cực mạnh. Hỗ trợ quan trọng tại 2,800 và 2,600 USD/oz theo Fibonacci.';
@endphp

@include('gold.sections.history-year')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.am5 || !window.am5xy) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('2025-01-01').getTime();
        var yearEnd = new Date('2025-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm 2025</p>'; return; }

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
