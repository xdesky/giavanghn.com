@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $thisYear = 2020;
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
        {"@@type":"Question","name":"Giá vàng SJC năm 2020 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2020, giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu và kết thúc ở {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Giá vàng SJC tháng nào tăng mạnh nhất 2020?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu OHLC và phần trăm thay đổi để xác định tháng biến động mạnh nhất năm 2020."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2020 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất năm 2020 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
@php
    $thisYear = 2020;
    $yearEvents = [
        '<strong>Tháng 1-2:</strong> COVID-19 bùng phát từ Vũ Hán, Trung Quốc. Thị trường tài chính toàn cầu hoảng loạn. Vàng ban đầu tăng nhẹ do trú ẩn, nhưng sau đó giảm mạnh khi nhà đầu tư bán tháo mọi tài sản để giữ tiền mặt (dash for cash).',
        '<strong>Tháng 3:</strong> WHO tuyên bố COVID-19 là đại dịch (11/03). Chứng khoán Mỹ "circuit breaker" 4 lần trong 10 ngày. Vàng giảm sốc về 1,470 USD/oz trước khi FED cắt lãi suất khẩn cấp về 0% và tung QE không giới hạn.',
        '<strong>Tháng 4-7:</strong> FED bơm hàng nghìn tỷ USD qua các chương trình QE, PPP, gói kích thích $2.2 nghìn tỷ. USD suy yếu, lợi suất thực âm sâu → vàng tăng liên tục. XAU/USD vượt 1,800 USD/oz lần đầu kể từ 2011.',
        '<strong>Tháng 8:</strong> Vàng thế giới lập đỉnh lịch sử 2,075 USD/oz (07/08/2020). SJC trong nước phá kỷ lục vượt 62 triệu/lượng. Cả thế giới chạy đua mua vàng trú ẩn giữa đại dịch.',
        '<strong>Tháng 9-12:</strong> Vàng điều chỉnh sau đỉnh, sideway quanh 1,850-1,950 USD/oz. Vaccine Pfizer/Moderna công bố hiệu quả >90% (tháng 11) → risk-on → vàng giảm. Kết thúc năm quanh 1,900 USD/oz, tăng ~25% cả năm.',
    ];
    $yearFactors = [
        '<strong>Đại dịch COVID-19:</strong> Sự kiện "thiên nga đen" lớn nhất thập kỷ. Lockdown toàn cầu, kinh tế suy thoái sâu → nhu cầu tài sản trú ẩn tăng kỷ lục.',
        '<strong>FED cắt lãi suất về 0%:</strong> Cắt khẩn cấp 150bps trong 2 tuần (tháng 3). Lãi suất thực âm sâu — yếu tố bullish mạnh nhất cho vàng.',
        '<strong>QE không giới hạn:</strong> Bảng cân đối FED từ $4.2 nghìn tỷ tăng lên $7.4 nghìn tỷ. Bơm thanh khoản chưa từng có → USD suy yếu → vàng tăng.',
        '<strong>Kích thích tài khóa khổng lồ:</strong> Tổng cộng $3+ nghìn tỷ gói cứu trợ (CARES Act, PPP, stimulus checks). Lo ngại lạm phát và nợ công → hỗ trợ vàng dài hạn.',
        '<strong>Lợi suất thực âm:</strong> TIPS 10 năm giảm xuống -1.0% — mức âm sâu nhất lịch sử. Chi phí cơ hội nắm giữ vàng gần như bằng 0.',
    ];
    $yearAnalysis = 'Năm 2020 là năm bùng nổ lịch sử của vàng (+25%), phá đỉnh ATH cũ 2011 ($1,921). Xu hướng tăng mạnh mẽ từ đáy tháng 3 ($1,470) lên đỉnh tháng 8 ($2,075) — tăng 41% trong 5 tháng. Sau đỉnh, vàng điều chỉnh về vùng $1,850-$1,950 và sideway Q4. SMA 200 ngày không bị thủng suốt từ tháng 4 đến cuối năm. RSI đạt 80+ vào tháng 7-8 (quá mua cực mạnh) trước khi điều chỉnh. Vùng $1,850 trở thành hỗ trợ then chốt cuối năm.';
@endphp

@include('gold.sections.history-year')
@endsection

@push('scripts')
<script>
(function initChart(retries) {
    if (!window.am5 || !window.am5xy) { if (retries < 50) { setTimeout(function(){ initChart(retries + 1); }, 200); } else { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; } return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('2020-01-01').getTime();
        var yearEnd = new Date('2020-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm 2020</p>'; return; }

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
