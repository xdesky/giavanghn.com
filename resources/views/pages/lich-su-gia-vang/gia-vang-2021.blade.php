@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $thisYear = 2021;
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
        {"@@type":"Question","name":"Giá vàng SJC năm 2021 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2021, giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu và kết thúc ở {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Giá vàng SJC tháng nào tăng mạnh nhất 2021?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu OHLC và phần trăm thay đổi để xác định tháng biến động mạnh nhất năm 2021."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2021 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất năm 2021 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
@php
    $thisYear = 2021;
    $yearEvents = [
        '<strong>Tháng 1-2:</strong> Vàng khởi đầu năm ở mức cao sau đà tăng kỷ lục 2020. Tuy nhiên, vaccine COVID-19 được triển khai rộng rãi → kỳ vọng phục hồi kinh tế → USD mạnh lên → vàng giảm từ 1,950 về 1,770 USD/oz.',
        '<strong>Tháng 3-6:</strong> Lợi suất trái phiếu Mỹ kỳ hạn 10 năm tăng mạnh từ 1.0% lên 1.75%. "Bond tantrum" gây áp lực lớn lên vàng. XAU/USD sideway quanh 1,750-1,900 USD/oz.',
        '<strong>Tháng 8:</strong> "Flash crash" ngày 09/08 — vàng giảm sốc từ 1,760 xuống 1,680 USD/oz trong vài phút do thanh khoản mỏng phiên châu Á. SJC giảm mạnh theo.',
        '<strong>Tháng 9-11:</strong> FED phát tín hiệu bắt đầu tapering (giảm mua tài sản). Lạm phát Mỹ tăng lên 6.2% (tháng 10) — cao nhất 30 năm. Vàng tăng lại do lo ngại lạm phát.',
        '<strong>Tháng 11-12:</strong> Biến thể Omicron xuất hiện (tháng 11) tạo sóng lo ngại mới. FED chính thức bắt đầu tapering. Vàng kết thúc năm sideway quanh 1,820 USD/oz.',
    ];
    $yearFactors = [
        '<strong>Lợi suất trái phiếu Mỹ:</strong> Tăng mạnh từ 0.9% lên 1.75% (Q1) → yếu tố bearish chính. Lãi suất thực tăng làm giảm sức hấp dẫn của vàng.',
        '<strong>Lạm phát tăng vọt:</strong> CPI Mỹ từ 1.4% (tháng 1) lên 7.0% (tháng 12). Tranh cãi "transitory" vs "persistent inflation" chi phối thị trường cả năm.',
        '<strong>Vaccine và phục hồi kinh tế:</strong> Triển khai vaccine COVID-19 toàn cầu → risk-on → dòng tiền chảy từ vàng sang cổ phiếu (S&P tăng 27%).',
        '<strong>FED tapering:</strong> Tháng 11 bắt đầu giảm mua $120 tỷ/tháng tài sản. Thị trường đã discount trước → tác động hạn chế lên vàng.',
        '<strong>Kích thích tài khóa:</strong> Gói hạ tầng $1.2 nghìn tỷ và gói chi tiêu xã hội Build Back Better → lo ngại nợ công, lạm phát dài hạn → hỗ trợ vàng.',
    ];
    $yearAnalysis = 'Năm 2021 là năm sideway điều chỉnh sau đà tăng bùng nổ 2020. XAU/USD giảm nhẹ ~4% cả năm (1,898 → 1,824 USD/oz). Kênh giá chính là 1,680-1,960 USD/oz. Đường SMA 200 ngày là trục trung tâm — giá xoay quanh đường này cả năm. Flash crash tháng 8 tạo đáy kỹ thuật quan trọng tại 1,680 USD/oz. RSI dao động 35-60 chủ yếu, cho thấy momentum yếu và thiếu xu hướng rõ ràng.';
@endphp

@include('gold.sections.history-year')
@endsection

@push('scripts')
<script>
(function initChart(retries) {
    if (!window.am5 || !window.am5xy) { if (retries < 50) { setTimeout(function(){ initChart(retries + 1); }, 200); } else { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; } return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('2021-01-01').getTime();
        var yearEnd = new Date('2021-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm 2021</p>'; return; }

        document.getElementById('historyYearChart').innerHTML = '';
        var root = am5.Root.new('historyYearChart');
        if (root._logo) root._logo.dispose();
        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(am5xy.XYChart.new(root, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX', layout: root.verticalLayout }));
        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, { baseInterval: { timeUnit: 'day', count: 1 }, renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 60 }), dateFormats: { day: 'dd/MM' }, periodChangeDateFormats: { day: 'dd/MM', month: 'MM/yyyy' } }));
        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, { renderer: am5xy.AxisRendererY.new(root, {}), numberFormat: '#,###.##' }));

        var series = chart.series.push(am5xy.LineSeries.new(root, { name: 'SJC (bán)', xAxis: xAxis, yAxis: yAxis, valueYField: 'value', valueXField: 'dateTs', stroke: am5.color('#b8860b'), tooltip: am5.Tooltip.new(root, { labelText: '{valueY.formatNumber("#,###.##")} tr\n{valueX.formatDate("dd/MM/yyyy")}', getFillFromSprite: false, getStrokeFromSprite: false }) }));
        series.get('tooltip').get('background').setAll({ fill: am5.color(0x0f172a), fillOpacity: 0.92, stroke: am5.color(0x0f172a) });
        series.get('tooltip').label.setAll({ fill: am5.color(0xffffff), fontSize: 12 });
        series.strokes.template.setAll({ strokeWidth: 1 });
        series.fills.template.setAll({ visible: true, fillOpacity: 0.08, fill: am5.color('#b8860b') });
        series.data.setAll(data);

        chart.set('cursor', am5xy.XYCursor.new(root, { behavior: 'zoomX' }));
        chart.appear(800, 100);
    }).catch(function(){ document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không thể tải dữ liệu</p>'; });
})(0);
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
