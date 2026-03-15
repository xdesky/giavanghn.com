@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $thisYear = 2023;
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
        {"@@type":"Question","name":"Giá vàng SJC năm 2023 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2023, giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu và kết thúc ở {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Tháng nào giá vàng SJC tăng mạnh nhất 2023?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu OHLC và phần trăm thay đổi để xác định tháng biến động mạnh nhất năm 2023."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2023 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất năm 2023 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
@php
    $thisYear = 2023;
    $yearEvents = [
        '<strong>Tháng 3:</strong> Khủng hoảng ngân hàng Mỹ (SVB, Signature Bank sụp đổ). Giá vàng thế giới tăng mạnh do nhu cầu trú ẩn an toàn, XAU/USD vượt 2,000 USD/oz.',
        '<strong>Tháng 5:</strong> FED tăng lãi suất lần cuối lên 5.25-5.50% — cao nhất 22 năm. Thị trường bắt đầu kỳ vọng kết thúc chu kỳ thắt chặt.',
        '<strong>Tháng 7-8:</strong> Fitch hạ xếp hạng tín dụng Mỹ từ AAA xuống AA+. Vàng biến động mạnh nhưng chịu áp lực từ lợi suất trái phiếu Mỹ tăng cao.',
        '<strong>Tháng 10:</strong> Xung đột Israel-Hamas bùng phát (07/10). Nhu cầu trú ẩn đẩy vàng tăng mạnh trở lại, XAU/USD vượt 2,000 USD/oz lần nữa.',
        '<strong>Tháng 12:</strong> FED phát tín hiệu "pivot" — dự kiến cắt giảm lãi suất 2024. XAU/USD lập đỉnh mới gần 2,135 USD/oz. SJC trong nước bứt phá cuối năm.',
    ];
    $yearFactors = [
        '<strong>Khủng hoảng ngân hàng:</strong> SVB, Signature, First Republic sụp đổ liên tiếp, gợi nhớ 2008. FED phải can thiệp với chương trình BTFP. Vàng được hưởng lợi từ vai trò trú ẩn.',
        '<strong>FED giữ lãi suất cao:</strong> Sau 11 lần tăng liên tiếp, FED giữ nguyên từ tháng 7. Mức 5.25-5.50% là cao nhất kể từ 2001.',
        '<strong>Xung đột địa chính trị:</strong> Israel-Hamas (tháng 10), tiếp diễn Nga-Ukraine. Rủi ro địa chính trị leo thang hỗ trợ giá vàng.',
        '<strong>NHTW tiếp tục mua ròng:</strong> ~1,037 tấn vàng — năm mua ròng cao thứ 2 lịch sử. Trung Quốc, Ba Lan, Singapore dẫn đầu.',
        '<strong>De-dollarization:</strong> BRICS mở rộng thành viên, xu hướng giảm phụ thuộc USD hỗ trợ giá vàng dài hạn.',
    ];
    $yearAnalysis = 'Năm 2023 giá vàng thế giới có 3 đợt tăng mạnh: tháng 3 (khủng hoảng ngân hàng), tháng 10 (Israel-Hamas), và tháng 12 (FED pivot). SMA 200 ngày đóng vai trò hỗ trợ tốt, giá chỉ chạm nhẹ rồi bật lên. RSI vùng 70 vào tháng 12 cảnh báo quá mua nhưng momentum vẫn mạnh. Mô hình Higher Highs - Higher Lows rõ ràng từ tháng 10, xác nhận xu hướng tăng trung hạn.';
@endphp

@include('gold.sections.history-year')
@endsection

@push('scripts')
<script>
(function initChart(retries) {
    if (!window.am5 || !window.am5xy) { if (retries < 50) { setTimeout(function(){ initChart(retries + 1); }, 200); } else { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; } return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('2023-01-01').getTime();
        var yearEnd = new Date('2023-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm 2023</p>'; return; }

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
