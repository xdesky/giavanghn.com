@extends('gold.page-shell')

@section('page-label', 'Lịch sử')

@push('head')
@php
    $now = now()->format('d/m/Y H:i');
    $years = [2026, 2025, 2024, 2023, 2022, 2021, 2020];
    $yearsData = [];
    foreach ($years as $yr) {
        $rows = \App\Models\SjcChartPrice::whereYear('price_date', $yr)->orderBy('price_date')->get();
        if ($rows->isEmpty()) { $yearsData[$yr] = null; continue; }
        $open = $rows->first()->sell_million ?? 0;
        $close = $rows->last()->sell_million ?? 0;
        $high = $rows->max('sell_million') ?: 0;
        $low = $rows->min('sell_million') ?: 0;
        $pct = $open > 0 ? (($close - $open) / $open * 100) : 0;
        $yearsData[$yr] = compact('open', 'close', 'high', 'low', 'pct');
    }
    $allRows = \App\Models\SjcChartPrice::orderBy('price_date')->get();
    $allTimeOpen = $allRows->first()->sell_million ?? 0;
    $allTimeClose = $allRows->last()->sell_million ?? 0;
    $allTimeHigh = $allRows->max('sell_million') ?: 0;
    $allTimeLow = $allRows->min('sell_million') ?: 0;
    $allTimePct = $allTimeOpen > 0 ? (($allTimeClose - $allTimeOpen) / $allTimeOpen * 100) : 0;
    $totalPoints = $allRows->count();
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type":"Question","name":"Giá vàng SJC từ 2020 đến nay biến động ra sao?","acceptedAnswer":{"@@type":"Answer","text":"Từ 2020 đến nay, giá SJC đã tăng từ {{ number_format($allTimeOpen, 2) }} triệu lên {{ number_format($allTimeClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $allTimePct) }}). Cao nhất {{ number_format($allTimeHigh, 2) }} triệu, thấp nhất {{ number_format($allTimeLow, 2) }} triệu."}},
        {"@@type":"Question","name":"Năm nào giá vàng SJC tăng mạnh nhất?","acceptedAnswer":{"@@type":"Answer","text":"Xem bảng so sánh giá vàng SJC theo năm với dữ liệu Open/High/Low/Close và phần trăm thay đổi để so sánh biến động giữa các năm."}},
        {"@@type":"Question","name":"Giá vàng SJC cao nhất mọi thời đại bao nhiêu?","acceptedAnswer":{"@@type":"Answer","text":"Giá SJC cao nhất mọi thời đại đạt {{ number_format($allTimeHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($allTimeLow, 2) }} triệu."}}
    ]
}
</script>
@endpush

@section('page-content')
{{-- Hero --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#001061] px-3 py-1 text-sm font-bold text-white">
            <i data-lucide="archive" class="h-3.5 w-3.5"></i> Tổng hợp
        </span>
        <span class="text-xs text-slate-400">Cập nhật {{ $now }}</span>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-5">
        <div class="rounded-sm border border-slate-200 bg-slate-50/50 p-3 text-center">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Đầu kỳ (2020)</p>
            <p class="mt-1 text-lg font-bold text-slate-800 tabular-nums">{{ number_format($allTimeOpen, 2, ',', '.') }}</p>
            <p class="text-[10px] text-slate-400">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-emerald-200 bg-emerald-50/60 p-3 text-center">
            <p class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wide">Cao nhất</p>
            <p class="mt-1 text-lg font-bold text-emerald-800 tabular-nums">{{ number_format($allTimeHigh, 2, ',', '.') }}</p>
            <p class="text-[10px] text-emerald-600/70">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-rose-200 bg-rose-50/60 p-3 text-center">
            <p class="text-[11px] font-semibold text-rose-700 uppercase tracking-wide">Thấp nhất</p>
            <p class="mt-1 text-lg font-bold text-rose-800 tabular-nums">{{ number_format($allTimeLow, 2, ',', '.') }}</p>
            <p class="text-[10px] text-rose-600/70">triệu/lượng</p>
        </div>
        <div class="rounded-sm border-2 border-[#001061]/20 bg-blue-50/40 p-3 text-center">
            <p class="text-[11px] font-semibold text-[#001061] uppercase tracking-wide">Hiện tại</p>
            <p class="mt-1 text-lg font-bold text-[#001061] tabular-nums">{{ number_format($allTimeClose, 2, ',', '.') }}</p>
            <p class="text-[10px] text-[#001061]/60">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-slate-200 bg-slate-50/50 p-3 text-center">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Tổng biến động</p>
            <p class="mt-1 text-lg font-bold {{ $allTimePct >= 0 ? 'text-emerald-700' : 'text-rose-700' }} tabular-nums">{{ sprintf('%+.2f%%', $allTimePct) }}</p>
            <p class="text-[10px] text-slate-400">{{ $totalPoints }} phiên</p>
        </div>
    </div>

    {{-- Year navigation --}}
    <nav class="flex flex-wrap gap-1.5" aria-label="Chọn năm">
        @foreach ($years as $yr)
        <a href="/lich-su-gia-vang/gia-vang-{{ $yr }}" class="px-3 py-1.5 rounded text-[13px] font-semibold no-underline transition-all text-slate-500 hover:bg-slate-100 hover:text-[#001061]">{{ $yr }}</a>
        @endforeach
        <a href="/lich-su-gia-vang" class="px-3 py-1.5 rounded text-[13px] font-semibold no-underline transition-all bg-[#001061] text-white shadow-sm">
            <i data-lucide="layers" class="inline h-3.5 w-3.5 -mt-0.5"></i> Tổng hợp
        </a>
    </nav>
</div>

{{-- All-time chart --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061]">
            <i data-lucide="trending-up" class="h-5 w-5 text-[#ffc300]"></i>
            Biểu đồ giá vàng SJC 2020–2026
        </h2>
        <span class="text-[11px] text-slate-400">{{ $totalPoints }} phiên</span>
    </div>
    <div id="historyAllChart" class="w-full" style="height:400px">
        <div class="flex items-center justify-center h-full text-slate-400 text-sm">
            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Đang tải biểu đồ...
        </div>
    </div>
</div>

{{-- Year-by-year comparison table --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061] mb-4">
        <i data-lucide="table-2" class="h-5 w-5 text-[#ffc300]"></i>
        So sánh giá vàng SJC theo năm
    </h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm" itemscope itemtype="https://schema.org/Table">
            <caption class="sr-only" itemprop="about">Bảng so sánh giá vàng SJC Open/High/Low/Close theo năm từ 2020-2026</caption>
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-slate-700">Năm</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Mở đầu</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Cao nhất</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Thấp nhất</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Kết thúc</th>
                    <th class="p-3 text-right font-semibold text-slate-700">Thay đổi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($years as $yr)
                @php $d = $yearsData[$yr] ?? null; @endphp
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-3 font-medium text-slate-800">
                        <a href="/lich-su-gia-vang/gia-vang-{{ $yr }}" class="text-[#001061] hover:underline font-bold">{{ $yr }}</a>
                        @if ($yr === (int)date('Y'))
                        <span class="ml-1 text-[10px] text-amber-600 font-semibold">đang cập nhật</span>
                        @endif
                    </td>
                    @if ($d)
                    <td class="p-3 text-right tabular-nums">{{ number_format($d['open'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums text-emerald-700 font-semibold">{{ number_format($d['high'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums text-rose-700 font-semibold">{{ number_format($d['low'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums font-bold">{{ number_format($d['close'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right font-bold {{ $d['pct'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ sprintf('%+.2f%%', $d['pct']) }}</td>
                    @else
                    <td colspan="5" class="p-3 text-center text-slate-400">Chưa có dữ liệu</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Article --}}
<article class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-headings:text-[#001061] prose-p:text-slate-700 prose-li:text-slate-700 prose-strong:text-slate-900">
    <h2 class="!mt-0 !text-lg">Lịch sử giá vàng SJC từ 2020 đến nay</h2>

    <h3>Tổng quan</h3>
    <p>Từ đầu năm 2020 đến nay, giá vàng SJC đã trải qua biến động chưa từng có, từ mức <strong>{{ number_format($allTimeOpen, 2) }} triệu/lượng</strong> lên <strong>{{ number_format($allTimeClose, 2) }} triệu</strong> — tương đương tăng <strong>{{ sprintf('%.1f%%', $allTimePct) }}</strong>. Mức cao nhất mọi thời đại đạt <strong>{{ number_format($allTimeHigh, 2) }} triệu</strong>, thấp nhất <strong>{{ number_format($allTimeLow, 2) }} triệu</strong>.</p>

    <h3>Diễn biến qua các năm</h3>
    <ul>
        <li><a href="/lich-su-gia-vang/gia-vang-2020"><strong>2020:</strong></a> COVID-19 bùng phát toàn cầu, FED hạ lãi suất về 0%, vàng thế giới phá kỷ lục 2,075 USD/oz. SJC lần đầu vượt 57 triệu/lượng.</li>
        <li><a href="/lich-su-gia-vang/gia-vang-2021"><strong>2021:</strong></a> Vaccine COVID triển khai, kinh tế phục hồi. Lạm phát bắt đầu tăng. Vàng điều chỉnh nhẹ trong xu hướng tích lũy.</li>
        <li><a href="/lich-su-gia-vang/gia-vang-2022"><strong>2022:</strong></a> Chiến tranh Nga-Ukraine bùng nổ, FED tăng lãi suất mạnh nhất 40 năm (0%→4.50%). Vàng biến động chữ V ngược — đỉnh đầu năm, đáy Q3, hồi phục Q4.</li>
        <li><a href="/lich-su-gia-vang/gia-vang-2023"><strong>2023:</strong></a> Khủng hoảng ngân hàng (SVB), xung đột Israel-Hamas, FED phát tín hiệu pivot. XAU/USD đạt đỉnh mới gần 2,135 USD/oz. SJC bứt phá cuối năm.</li>
        <li><a href="/lich-su-gia-vang/gia-vang-2024"><strong>2024:</strong></a> FED chính thức cắt giảm lãi suất. NHNN bán vàng miếng bình ổn. XAU/USD tăng ~25%, vượt 2,700 USD/oz. SJC lập kỷ lục mới.</li>
        <li><a href="/lich-su-gia-vang/gia-vang-2025"><strong>2025:</strong></a> Thuế quan Trump 2.0, FED tiếp tục nới lỏng. XAU/USD phá mốc 3,000 USD/oz lần đầu lịch sử. SJC tiếp tục phá kỷ lục.</li>
        <li><a href="/lich-su-gia-vang/gia-vang-2026"><strong>2026:</strong></a> Đang cập nhật — theo dõi diễn biến mới nhất.</li>
    </ul>

    <h3>Các yếu tố chính chi phối giá vàng giai đoạn 2020–2026</h3>
    <ul>
        <li><strong>Chính sách tiền tệ FED:</strong> Từ hạ lãi suất khẩn cấp (2020) → tăng mạnh nhất lịch sử (2022-2023) → pivot cắt giảm (2024-2026). Lãi suất thực là yếu tố cốt lõi.</li>
        <li><strong>Địa chính trị:</strong> COVID-19, Nga-Ukraine, Israel-Hamas, thuế quan toàn cầu — chuỗi sự kiện bất ổn liên tiếp thúc đẩy nhu cầu trú ẩn.</li>
        <li><strong>NHTW mua ròng:</strong> Kỷ nguyên de-dollarization — NHTW mua vàng ở mức kỷ lục (>1,000 tấn/năm từ 2022). Trung Quốc, Ấn Độ, Thổ Nhĩ Kỳ dẫn đầu.</li>
        <li><strong>Lạm phát:</strong> Từ 1.2% (2020) lên đỉnh 9.1% (2022) rồi giảm dần nhưng dai dẳng. Vai trò phòng lạm phát của vàng được khẳng định.</li>
        <li><strong>Thị trường trong nước:</strong> Chênh lệch SJC-thế giới từng đạt 18 triệu (2024) trước khi NHNN can thiệp. Cấu trúc thị trường vàng VN thay đổi đáng kể.</li>
    </ul>

    <h3>Liên kết hữu ích</h3>
    <ul>
        <li><a href="/gia-vang-hom-nay">Giá vàng hôm nay</a> — Cập nhật giá SJC, DOJI, PNJ mới nhất</li>
        <li><a href="/bieu-do-gia-vang">Biểu đồ giá vàng</a> — Phân tích kỹ thuật trực quan</li>
        <li><a href="/du-bao-gia-vang">Dự báo giá vàng</a> — Nhận định xu hướng tương lai</li>
        <li><a href="/so-sanh-gia-vang">So sánh giá vàng</a> — So sánh giữa các thương hiệu</li>
    </ul>
</article>

{{-- FAQ --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4 flex items-center gap-2">
        <i data-lucide="help-circle" class="h-5 w-5"></i>
        Câu hỏi thường gặp
    </h2>
    <div class="divide-y divide-slate-200">
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>Giá vàng SJC từ 2020 đến nay biến động ra sao?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">Từ 2020 đến nay, SJC tăng từ {{ number_format($allTimeOpen, 2) }} triệu lên {{ number_format($allTimeClose, 2) }} triệu/lượng ({{ sprintf('%+.2f%%', $allTimePct) }}). Cao nhất {{ number_format($allTimeHigh, 2) }} triệu, thấp nhất {{ number_format($allTimeLow, 2) }} triệu.</p>
        </details>
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>Năm nào giá vàng SJC tăng mạnh nhất?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">Xem bảng so sánh giá vàng SJC theo năm phía trên. Mỗi năm đều ghi nhận các sự kiện kinh tế, chính trị đặc thù tác động đến giá vàng.</p>
        </details>
        <details class="group">
            <summary class="flex cursor-pointer items-center justify-between py-3 text-sm font-semibold text-slate-800 hover:text-[#001061] transition">
                <span>Giá vàng SJC cao nhất mọi thời đại bao nhiêu?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="pb-3 text-sm text-slate-600 leading-relaxed">Giá SJC cao nhất mọi thời đại đạt {{ number_format($allTimeHigh, 2) }} triệu/lượng. Biên độ từ thấp nhất đến cao nhất là {{ number_format($allTimeHigh - $allTimeLow, 2) }} triệu.</p>
        </details>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.am5 || !window.am5xy) { document.getElementById('historyAllChart').innerHTML = '<p class="text-center text-slate-400 py-8">Đang tải thư viện biểu đồ...</p>'; return; }

    fetch('/api/v1/sjc-chart?period=all').then(function(r){ return r.json(); }).then(function(res) {
        var raw = (Array.isArray(res) ? res : res.data || []);
        var data = raw.filter(function(d){ return d.sell > 0; })
            .map(function(d){ return { dateTs: new Date(d.date).getTime(), value: d.sell }; });
        if (!data.length) { document.getElementById('historyAllChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không có dữ liệu</p>'; return; }

        document.getElementById('historyAllChart').innerHTML = '';
        var root = am5.Root.new('historyAllChart');
        if (root._logo) root._logo.dispose();
        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(am5xy.XYChart.new(root, { panX: true, panY: false, wheelX: 'panX', wheelY: 'zoomX', layout: root.verticalLayout }));
        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, { baseInterval: { timeUnit: 'day', count: 1 }, renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 80 }), dateFormats: { day: 'dd/MM/yy', month: 'MM/yyyy' }, periodChangeDateFormats: { day: 'MM/yyyy', month: 'yyyy' } }));
        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, { renderer: am5xy.AxisRendererY.new(root, {}), numberFormat: '#,###.##' }));

        var series = chart.series.push(am5xy.LineSeries.new(root, { name: 'SJC (bán)', xAxis: xAxis, yAxis: yAxis, valueYField: 'value', valueXField: 'dateTs', stroke: am5.color('#b8860b'), tooltip: am5.Tooltip.new(root, { labelText: '{valueY.formatNumber("#,###.##")} tr\n{valueX.formatDate("dd/MM/yyyy")}' }) }));
        series.strokes.template.setAll({ strokeWidth: 2 });
        series.fills.template.setAll({ visible: true, fillOpacity: 0.06, fill: am5.color('#b8860b') });
        series.data.setAll(data);

        chart.set('cursor', am5xy.XYCursor.new(root, { behavior: 'zoomX' }));
        chart.set('scrollbarX', am5.Scrollbar.new(root, { orientation: 'horizontal' }));
        chart.appear(800, 100);
    }).catch(function(){ document.getElementById('historyAllChart').innerHTML = '<p class="text-center text-slate-400 py-8">Không thể tải dữ liệu</p>'; });
});
</script>
@endpush

@section('page-sidebar')
@include('gold.sections.sidebar-price')
@include('gold.sections.sidebar-news')
@endsection
