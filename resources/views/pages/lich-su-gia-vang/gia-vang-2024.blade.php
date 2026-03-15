@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $thisYear = 2024;
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
        {"@@type":"Question","name":"Giá vàng SJC năm 2024 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2024, giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu và kết thúc ở {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Tháng nào giá vàng SJC tăng mạnh nhất 2024?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu OHLC và phần trăm thay đổi để xác định tháng biến động mạnh nhất năm 2024."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2024 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất năm 2024 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
@php
    $thisYear = 2024;
    $yearEvents = [
        '<strong>Tháng 3:</strong> XAU/USD lập đỉnh mới trên 2,200 USD/oz khi FED phát tín hiệu sẽ cắt giảm lãi suất 3 lần trong năm. Kỳ vọng nới lỏng tiền tệ thúc đẩy giá vàng mạnh.',
        '<strong>Tháng 4:</strong> Căng thẳng Iran-Israel leo thang (Iran tấn công drone vào Israel). SJC trong nước vượt mốc 80 triệu/lượng lần đầu. NHNN tổ chức đấu thầu vàng miếng để bình ổn.',
        '<strong>Tháng 5-6:</strong> NHNN bán vàng miếng trực tiếp qua 4 ngân hàng quốc doanh và SJC. Chênh lệch SJC-thế giới thu hẹp đáng kể từ 18 triệu xuống còn 3-5 triệu/lượng.',
        '<strong>Tháng 9:</strong> FED chính thức cắt giảm lãi suất 50bps — lần đầu sau 4 năm. XAU/USD vượt 2,600 USD/oz. Chu kỳ nới lỏng chính thức bắt đầu.',
        '<strong>Tháng 10-11:</strong> Donald Trump thắng cử Tổng thống. USD mạnh lên nhưng vàng vẫn tăng nhờ kỳ vọng chính sách tài khóa mở rộng và rủi ro địa chính trị.',
        '<strong>Tháng 12:</strong> FED cắt giảm thêm 25bps. XAU/USD kết năm quanh 2,620 USD/oz. SJC kết thúc năm ở mức cao kỷ lục lịch sử.',
    ];
    $yearFactors = [
        '<strong>FED pivot — Cắt giảm lãi suất:</strong> 3 lần giảm tổng 100bps (5.50% → 4.50%). Lãi suất thực giảm là yếu tố bullish mạnh nhất cho vàng.',
        '<strong>NHTW mua ròng kỷ lục:</strong> Tiếp tục xu hướng mua mạnh, đặc biệt Trung Quốc, Ấn Độ, Thổ Nhĩ Kỳ. Nhu cầu dự trữ vàng chiến lược tăng.',
        '<strong>NHNN Việt Nam can thiệp:</strong> Đấu thầu và bán vàng miếng trực tiếp lần đầu sau nhiều năm. Chênh lệch SJC-thế giới giảm mạnh — thay đổi cấu trúc thị trường trong nước.',
        '<strong>Bầu cử Mỹ:</strong> Trump 2.0 với chính sách thuế quan, giảm thuế doanh nghiệp, tăng chi tiêu → lo ngại lạm phát và thâm hụt ngân sách dài hạn.',
        '<strong>Địa chính trị:</strong> Iran-Israel, Nga-Ukraine tiếp diễn, biển Đông căng thẳng. Rủi ro toàn cầu duy trì ở mức cao.',
    ];
    $yearAnalysis = 'Năm 2024 là năm vàng tăng mạnh nhất trong nhiều năm với XAU/USD tăng khoảng 25%. SMA 50 ngày luôn nằm trên SMA 200 ngày suốt cả năm — Golden Cross duy trì. RSI nhiều lần chạm 75-80 nhưng không tạo tín hiệu đảo chiều lớn, cho thấy xu hướng tăng rất mạnh. Fibonacci retracement levels tại 2,150 và 2,300 USD/oz đóng vai trò hỗ trợ tốt qua các đợt điều chỉnh.';
@endphp

@include('gold.sections.history-year')
@endsection

@push('scripts')
<script>
(function initChart(retries) {
    if (!window.am5 || !window.am5xy) { if (retries < 50) { setTimeout(function(){ initChart(retries + 1); }, 200); } else { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; } return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('2024-01-01').getTime();
        var yearEnd = new Date('2024-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm 2024</p>'; return; }

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
