@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $thisYear = 2022;
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
        {"@@type":"Question","name":"Giá vàng SJC năm 2022 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2022, giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu và kết thúc ở {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Tháng nào giá vàng SJC tăng mạnh nhất 2022?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu OHLC và phần trăm thay đổi để xác định tháng biến động mạnh nhất năm 2022."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2022 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất năm 2022 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
@php
    $thisYear = 2022;
    $yearEvents = [
        '<strong>Tháng 2-3:</strong> Chiến tranh Nga-Ukraine bùng nổ (24/02/2022). Giá vàng thế giới tăng vọt lên gần 2,070 USD/oz. SJC trong nước phá kỷ lục mọi thời đại tại thời điểm đó, vượt 73 triệu/lượng.',
        '<strong>Tháng 3-6:</strong> FED bắt đầu chu kỳ tăng lãi suất mạnh nhất 40 năm: +25bps (tháng 3), +50bps (tháng 5), +75bps (tháng 6). USD mạnh lên, vàng chịu áp lực giảm.',
        '<strong>Tháng 7-9:</strong> FED tiếp tục tăng 75bps mỗi cuộc họp (tháng 7, 9). Chỉ số DXY vượt 114 — cao nhất 20 năm. XAU/USD giảm xuống 1,615 USD/oz — thấp nhất năm.',
        '<strong>Tháng 10-11:</strong> Lạm phát Mỹ bắt đầu hạ nhiệt (CPI giảm từ đỉnh 9.1% xuống 7.1%). Kỳ vọng FED giảm tốc tăng lãi suất → vàng phục hồi mạnh từ đáy.',
        '<strong>Tháng 12:</strong> FED tăng 50bps (giảm từ mức 75bps trước đó). XAU/USD phục hồi lên 1,824 USD/oz. SJC kết thúc năm cao hơn đáng kể so với đáy.',
    ];
    $yearFactors = [
        '<strong>Chiến tranh Nga-Ukraine:</strong> Yếu tố bất ngờ lớn nhất, đẩy giá vàng lên đỉnh đầu năm. Lệnh trừng phạt kinh tế, đóng băng dự trữ ngoại hối Nga thúc đẩy xu hướng de-dollarization.',
        '<strong>FED tăng lãi suất:</strong> 7 lần liên tiếp tổng cộng 425bps (0% → 4.25-4.50%). Lãi suất thực tăng là yếu tố bearish mạnh nhất cho vàng trong năm.',
        '<strong>USD siêu mạnh:</strong> DXY tăng từ 96 lên đỉnh 114, tạo áp lực lớn lên vàng. Vàng và USD thường có tương quan nghịch.',
        '<strong>Lạm phát kỷ lục:</strong> CPI Mỹ đạt đỉnh 9.1% (tháng 6) — cao nhất 40 năm. Mâu thuẫn giữa vai trò phòng lạm phát của vàng vs áp lực từ lãi suất cao.',
        '<strong>NHTW mua vàng:</strong> Năm kỷ lục với 1,136 tấn mua ròng — cao nhất lịch sử. Trung Quốc bắt đầu công bố mua ròng trở lại sau 3 năm im lặng.',
    ];
    $yearAnalysis = 'Năm 2022 đánh dấu biến động mạnh theo hình chữ V ngược rồi phục hồi. Giá tăng mạnh Q1 lên gần 2,070 USD/oz (xung đột Nga-Ukraine), sau đó giảm sâu đến Q3 xuống 1,615 USD/oz (FED tăng lãi suất mạnh), rồi phục hồi Q4. SMA 200 ngày bị thủng tháng 7 và giá nằm dưới đường này đến cuối tháng 11. RSI chạm vùng quá bán (dưới 30) vào tháng 9 — tín hiệu đáy ngắn hạn chính xác.';
@endphp

@include('gold.sections.history-year')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.am5 || !window.am5xy) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('2022-01-01').getTime();
        var yearEnd = new Date('2022-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm 2022</p>'; return; }

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
