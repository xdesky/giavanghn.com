@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $thisYear = 2022;
    $sjcCard = $snapshot['sjcCard'] ?? null;
    $sjcV = $sjcCard['variants'][$sjcCard['selected'] ?? 'p0'] ?? null;
    $sjcSell = $sjcV['sell'] ?? 0;

    $monthly = \App\Models\SjcChartPrice::query()
        ->whereYear('price_date', $thisYear)
        ->selectRaw('MONTH(price_date) as month_num, MIN(sell_million) as low_v, MAX(sell_million) as high_v, MIN(price_date) as first_day, MAX(price_date) as last_day')
        ->groupByRaw('MONTH(price_date)')
        ->orderByRaw('MONTH(price_date)')
        ->get();

    $monthsData = $monthly->map(function ($row) {
        $open = \App\Models\SjcChartPrice::whereDate('price_date', $row->first_day)->value('sell_million');
        $close = \App\Models\SjcChartPrice::whereDate('price_date', $row->last_day)->value('sell_million');
        $change = ($open && $open > 0 && $close) ? (($close - $open) / $open * 100) : 0;
        return [
            'label' => 'Tháng ' . $row->month_num,
            'open' => (float) $open,
            'high' => (float) $row->high_v,
            'low' => (float) $row->low_v,
            'close' => (float) $close,
            'changePct' => $change,
        ];
    })->all();

    $allRows = \App\Models\SjcChartPrice::whereYear('price_date', $thisYear)
        ->orderBy('price_date')->get();
    $yearOpen = $allRows->first()->sell_million ?? 0;
    $yearClose = $allRows->last()->sell_million ?? 0;
    $yearHigh = $allRows->max('sell_million') ?: 0;
    $yearLow = $allRows->min('sell_million') ?: 0;
    $yearChangePct = $yearOpen > 0 ? (($yearClose - $yearOpen) / $yearOpen * 100) : 0;
    $dataPoints = $allRows->count();
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type":"Question","name":"Giá vàng SJC năm 2022 biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Năm 2022, giá SJC mở đầu ở {{ number_format($yearOpen, 2) }} triệu và kết thúc ở {{ number_format($yearClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Giá vàng SJC tháng nào tăng mạnh nhất 2022?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng giá SJC theo tháng với dữ liệu Open/High/Low/Close và phần trăm thay đổi. Biểu đồ trực quan giúp nhận diện tháng tăng/giảm mạnh nhất năm 2022."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất năm 2022 bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất năm 2022 đạt {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên động {{ number_format($yearHigh - $yearLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
{{-- Hero --}}
<div class="rounded-sm border border-[#bcbcbc] bg-gradient-to-br from-teal-50/80 to-white p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-700">{{ $thisYear }}</span>
        <span class="text-sm text-slate-500">{{ $now }}</span>
    </div>
    <h2 class="text-2xl font-bold text-[#001061] mb-4">Lịch sử giá vàng SJC năm {{ $thisYear }}</h2>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-4">
        <div class="rounded-sm border border-slate-200 bg-white p-3 text-center">
            <p class="text-xs font-semibold text-slate-500 uppercase">Mở đầu năm</p>
            <p class="mt-1 text-lg font-bold text-slate-900 tabular-nums">{{ number_format($yearOpen, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-sm border border-emerald-200 bg-emerald-50 p-3 text-center">
            <p class="text-xs font-semibold text-emerald-700 uppercase">Cao nhất</p>
            <p class="mt-1 text-lg font-bold text-emerald-800 tabular-nums">{{ number_format($yearHigh, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-sm border border-rose-200 bg-rose-50 p-3 text-center">
            <p class="text-xs font-semibold text-rose-700 uppercase">Thấp nhất</p>
            <p class="mt-1 text-lg font-bold text-rose-800 tabular-nums">{{ number_format($yearLow, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-sm border-2 border-teal-300 bg-white p-3 text-center">
            <p class="text-xs font-semibold text-teal-700 uppercase">Kết thúc năm</p>
            <p class="mt-1 text-lg font-bold text-teal-900 tabular-nums">{{ number_format($yearClose, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-sm border border-slate-200 bg-white p-3 text-center">
            <p class="text-xs font-semibold text-slate-500 uppercase">Cả năm</p>
            <p class="mt-1 text-lg font-bold {{ $yearChangePct >= 0 ? 'text-emerald-700' : 'text-rose-700' }} tabular-nums">{{ sprintf('%+.2f%%', $yearChangePct) }}</p>
        </div>
    </div>

    {{-- Year nav --}}
    <div class="flex flex-wrap gap-2">
        @foreach ([2026, 2025, 2024, 2023, 2022, 2021, 2020] as $yr)
        <a href="/lich-su-gia-vang/gia-vang-{{ $yr }}" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold no-underline {{ $yr === $thisYear ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $yr }}</a>
        @endforeach
        <a href="/lich-su-gia-vang" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold no-underline bg-slate-100 text-slate-600 hover:bg-slate-200">Tổng hợp</a>
    </div>
</div>

{{-- Chart --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Biểu đồ giá vàng SJC năm {{ $thisYear }}</h2>
    <p class="text-xs text-slate-500 mb-3">Giá bán SJC (triệu/lượng) từ 01/01/{{ $thisYear }} đến 31/12/{{ $thisYear }} · {{ $dataPoints }} phiên giao dịch</p>
    <div id="historyYearChart" class="w-full" style="height:400px">
        <div class="flex items-center justify-center h-full text-slate-400">
            <svg class="animate-spin h-6 w-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Đang tải biểu đồ...
        </div>
    </div>
</div>

{{-- Monthly Table --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Giá vàng SJC theo tháng — {{ $thisYear }}</h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-slate-700">Tháng</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Mở cửa</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Cao nhất</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Thấp nhất</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Đóng cửa</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Thay đổi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($monthsData as $m)
                <tr>
                    <td class="p-3 font-medium text-slate-800">{{ $m['label'] }}</td>
                    <td class="p-3 text-right tabular-nums">{{ number_format($m['open'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums text-emerald-700 font-semibold">{{ number_format($m['high'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums text-rose-700 font-semibold">{{ number_format($m['low'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums font-bold">{{ number_format($m['close'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right font-bold {{ $m['changePct'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ sprintf('%+.2f%%', $m['changePct']) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-4 text-center text-slate-500">Chưa có dữ liệu lịch sử cho năm {{ $thisYear }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Article --}}
<article class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-headings:text-slate-900 prose-p:text-slate-700">
    <h2 class="!mt-0">Tổng kết giá vàng SJC năm {{ $thisYear }}</h2>

    <h3>Diễn biến giá vàng {{ $thisYear }}</h3>
    <p>Năm {{ $thisYear }}, giá vàng SJC mở đầu ở mức <strong>{{ number_format($yearOpen, 2) }} triệu/lượng</strong> và kết thúc ở <strong>{{ number_format($yearClose, 2) }} triệu</strong>, tương đương {{ $yearChangePct >= 0 ? 'tăng' : 'giảm' }} <strong>{{ sprintf('%.2f%%', abs($yearChangePct)) }}</strong> trong cả năm. Mức cao nhất đạt {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu.</p>

    <h3>Sự kiện nổi bật ảnh hưởng giá vàng 2022</h3>
    <ul>
        <li><strong>Tháng 2-3:</strong> Chiến tranh Nga-Ukraine bùng nổ (24/02/2022). Giá vàng thế giới tăng vọt lên gần 2,070 USD/oz. SJC trong nước phá kỷ lục mọi thời đại tại thời điểm đó, vượt 73 triệu/lượng.</li>
        <li><strong>Tháng 3-6:</strong> FED bắt đầu chu kỳ tăng lãi suất mạnh nhất 40 năm: +25bps (tháng 3), +50bps (tháng 5), +75bps (tháng 6). USD mạnh lên, vàng chịu áp lực giảm.</li>
        <li><strong>Tháng 7-9:</strong> FED tiếp tục tăng 75bps mỗi cuộc họp (tháng 7, 9). Chỉ số DXY vượt 114 — cao nhất 20 năm. XAU/USD giảm xuống 1,615 USD/oz — thấp nhất năm.</li>
        <li><strong>Tháng 10-11:</strong> Lạm phát Mỹ bắt đầu hạ nhiệt (CPI giảm từ đỉnh 9.1% xuống 7.1%). Kỳ vọng FED giảm tốc tăng lãi suất → vàng phục hồi mạnh từ đáy.</li>
        <li><strong>Tháng 12:</strong> FED tăng 50bps (giảm từ mức 75bps trước đó). XAU/USD phục hồi lên 1,824 USD/oz. SJC kết thúc năm cao hơn đáng kể so với đáy.</li>
    </ul>

    <h3>Yếu tố chi phối giá vàng 2022</h3>
    <p>Các yếu tố chính ảnh hưởng giá vàng trong năm {{ $thisYear }}:</p>
    <ul>
        <li><strong>Chiến tranh Nga-Ukraine:</strong> Yếu tố bất ngờ lớn nhất, đẩy giá vàng lên đỉnh đầu năm. Lệnh trừng phạt kinh tế, đóng băng dự trữ ngoại hối Nga thúc đẩy xu hướng de-dollarization.</li>
        <li><strong>FED tăng lãi suất:</strong> 7 lần liên tiếp tổng cộng 425bps (0% → 4.25-4.50%). Lãi suất thực tăng là yếu tố bearish mạnh nhất cho vàng trong năm.</li>
        <li><strong>USD siêu mạnh:</strong> DXY tăng từ 96 lên đỉnh 114, tạo áp lực lớn lên vàng. Vàng và USD thường có tương quan nghịch.</li>
        <li><strong>Lạm phát kỷ lục:</strong> CPI Mỹ đạt đỉnh 9.1% (tháng 6) — cao nhất 40 năm. Mâu thuẫn giữa vai trò phòng lạm phát của vàng vs áp lực từ lãi suất cao.</li>
        <li><strong>NHTW mua vàng:</strong> Năm kỷ lục với 1,136 tấn mua ròng — cao nhất lịch sử. Trung Quốc bắt đầu công bố mua ròng trở lại sau 3 năm im lặng.</li>
    </ul>

    <h3>Phân tích kỹ thuật năm 2022</h3>
    <p>Năm 2022 đánh dấu biến động mạnh theo hình chữ V ngược rồi phục hồi. Giá tăng mạnh Q1 lên gần 2,070 USD/oz (xung đột Nga-Ukraine), sau đó giảm sâu đến Q3 xuống 1,615 USD/oz (FED tăng lãi suất mạnh), rồi phục hồi Q4. SMA 200 ngày bị thủng tháng 7 và giá nằm dưới đường này đến cuối tháng 11. RSI chạm vùng quá bán (dưới 30) vào tháng 9 — tín hiệu đáy ngắn hạn chính xác.</p>
</article>

{{-- FAQ --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Câu hỏi thường gặp</h2>
    <div class="divide-y divide-slate-200">
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng SJC năm {{ $thisYear }} biến động ra sao?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">SJC mở đầu {{ number_format($yearOpen, 1) }} triệu, kết thúc {{ number_format($yearClose, 1) }} triệu ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 1) }} triệu, thấp nhất {{ number_format($yearLow, 1) }} triệu.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng SJC tháng nào tăng mạnh nhất năm {{ $thisYear }}?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Xem bảng giá SJC theo tháng phía trên với dữ liệu Open/High/Low/Close và phần trăm thay đổi để xác định tháng biến động mạnh nhất.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-blue-700">
                <span>Giá vàng SJC cao nhất năm {{ $thisYear }} bao nhiêu?</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Cao nhất {{ number_format($yearHigh, 2) }} triệu/lượng. Biên độ dao động trong năm: {{ number_format($yearHigh - $yearLow, 2) }} triệu.</p>
        </details>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.am5 || !window.am5xy) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var yearStart = new Date('{{ $thisYear }}-01-01').getTime();
        var yearEnd = new Date('{{ $thisYear }}-12-31').getTime();
        var data = raw.filter(function(d){ return d.sell > 0 && new Date(d.date).getTime() >= yearStart && new Date(d.date).getTime() <= yearEnd; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyYearChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu cho năm {{ $thisYear }}</p>'; return; }

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
