@php
    $now = now()->format('d/m/Y H:i');
    $sjcCard = $snapshot['sjcCard'] ?? null;

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

    $monthNames = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
    $bestMonth = null;
    $worstMonth = null;
    foreach ($monthsData as $m) {
        if ($bestMonth === null || $m['changePct'] > $bestMonth['changePct']) $bestMonth = $m;
        if ($worstMonth === null || $m['changePct'] < $worstMonth['changePct']) $worstMonth = $m;
    }
@endphp

{{-- Hero stats --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#001061] px-3 py-1 text-sm font-bold text-white">
            <i data-lucide="calendar" class="h-3.5 w-3.5"></i> {{ $thisYear }}
        </span>
        <span class="text-xs text-slate-400">Cập nhật {{ $now }}</span>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 mb-5">
        <div class="rounded-sm border border-slate-200 bg-slate-50/50 p-3 text-center">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Mở đầu năm</p>
            <p class="mt-1 text-lg font-bold text-slate-800 tabular-nums">{{ number_format($yearOpen, 2, ',', '.') }}</p>
            <p class="text-[10px] text-slate-400">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-emerald-200 bg-emerald-50/60 p-3 text-center">
            <p class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wide">Cao nhất</p>
            <p class="mt-1 text-lg font-bold text-emerald-800 tabular-nums">{{ number_format($yearHigh, 2, ',', '.') }}</p>
            <p class="text-[10px] text-emerald-600/70">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-rose-200 bg-rose-50/60 p-3 text-center">
            <p class="text-[11px] font-semibold text-rose-700 uppercase tracking-wide">Thấp nhất</p>
            <p class="mt-1 text-lg font-bold text-rose-800 tabular-nums">{{ number_format($yearLow, 2, ',', '.') }}</p>
            <p class="text-[10px] text-rose-600/70">triệu/lượng</p>
        </div>
        <div class="rounded-sm border-2 border-[#001061]/20 bg-blue-50/40 p-3 text-center">
            <p class="text-[11px] font-semibold text-[#001061] uppercase tracking-wide">Kết thúc năm</p>
            <p class="mt-1 text-lg font-bold text-[#001061] tabular-nums">{{ number_format($yearClose, 2, ',', '.') }}</p>
            <p class="text-[10px] text-[#001061]/60">triệu/lượng</p>
        </div>
        <div class="rounded-sm border border-slate-200 bg-slate-50/50 p-3 text-center">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Cả năm</p>
            <p class="mt-1 text-lg font-bold {{ $yearChangePct >= 0 ? 'text-emerald-700' : 'text-rose-700' }} tabular-nums">{{ sprintf('%+.2f%%', $yearChangePct) }}</p>
            <p class="text-[10px] text-slate-400">biên độ {{ number_format($yearHigh - $yearLow, 2) }} tr</p>
        </div>
    </div>

    {{-- Year navigation --}}
    <nav class="flex flex-wrap gap-1.5" aria-label="Chọn năm">
        @foreach ([2026, 2025, 2024, 2023, 2022, 2021, 2020] as $yr)
        <a href="/lich-su-gia-vang/gia-vang-{{ $yr }}" class="px-3 py-1.5 rounded text-[13px] font-semibold no-underline transition-all {{ $yr === $thisYear ? 'bg-[#001061] text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100 hover:text-[#001061]' }}">{{ $yr }}</a>
        @endforeach
        <a href="/lich-su-gia-vang" class="px-3 py-1.5 rounded text-[13px] font-semibold no-underline transition-all text-slate-500 hover:bg-slate-100 hover:text-[#001061]">
            <i data-lucide="layers" class="inline h-3.5 w-3.5 -mt-0.5"></i> Tổng hợp
        </a>
    </nav>
</div>

{{-- Chart --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061]">
            <i data-lucide="trending-up" class="h-5 w-5 text-[#ffc300]"></i>
            Biểu đồ giá vàng SJC {{ $thisYear }}
        </h2>
        <span class="text-[11px] text-slate-400">{{ $dataPoints }} phiên</span>
    </div>
    <div id="historyYearChart" class="w-full" style="height:380px">
        <div class="flex items-center justify-center h-full text-slate-400 text-sm">
            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Đang tải biểu đồ...
        </div>
    </div>
</div>

{{-- Monthly table --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061] mb-4">
        <i data-lucide="table-2" class="h-5 w-5 text-[#ffc300]"></i>
        Giá vàng SJC theo tháng — {{ $thisYear }}
    </h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm" itemscope itemtype="https://schema.org/Table">
            <caption class="sr-only" itemprop="about">Bảng giá vàng SJC Open/High/Low/Close theo tháng năm {{ $thisYear }}</caption>
            <thead class="bg-[#f5f5f5]">
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
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-3 font-medium text-slate-800">{{ $m['label'] }}</td>
                    <td class="p-3 text-right tabular-nums">{{ number_format($m['open'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums text-emerald-700 font-semibold">{{ number_format($m['high'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums text-rose-700 font-semibold">{{ number_format($m['low'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right tabular-nums font-bold">{{ number_format($m['close'], 2, ',', '.') }}</td>
                    <td class="p-3 text-right font-bold {{ $m['changePct'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ sprintf('%+.2f%%', $m['changePct']) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-400">
                    <i data-lucide="database" class="mx-auto mb-1 h-8 w-8 text-slate-200"></i>
                    <p>Chưa có dữ liệu lịch sử cho năm {{ $thisYear }}.</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Article --}}
<article class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-headings:text-[#001061] prose-p:text-slate-700 prose-li:text-slate-700 prose-strong:text-slate-900">
    <h2 class="!mt-0 !text-lg">Tổng kết giá vàng SJC năm {{ $thisYear }}</h2>

    <h3>Diễn biến chung</h3>
    <p>Năm {{ $thisYear }}, giá vàng SJC mở đầu ở mức <strong>{{ number_format($yearOpen, 2) }} triệu/lượng</strong> và kết thúc ở <strong>{{ number_format($yearClose, 2) }} triệu</strong>, tương đương {{ $yearChangePct >= 0 ? 'tăng' : 'giảm' }} <strong>{{ sprintf('%.2f%%', abs($yearChangePct)) }}</strong> trong cả năm. Mức cao nhất đạt <strong>{{ number_format($yearHigh, 2) }} triệu</strong>, thấp nhất <strong>{{ number_format($yearLow, 2) }} triệu</strong>, biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu.</p>

    @if ($bestMonth && $worstMonth && count($monthsData) > 1)
    <h3>Tháng nổi bật</h3>
    <ul>
        <li><strong>{{ $bestMonth['label'] }}</strong> là tháng tăng mạnh nhất ({{ sprintf('%+.2f%%', $bestMonth['changePct']) }}), đóng cửa ở {{ number_format($bestMonth['close'], 2) }} triệu.</li>
        <li><strong>{{ $worstMonth['label'] }}</strong> là tháng giảm mạnh nhất ({{ sprintf('%+.2f%%', $worstMonth['changePct']) }}), đóng cửa ở {{ number_format($worstMonth['close'], 2) }} triệu.</li>
    </ul>
    @endif

    @if (!empty($yearEvents))
    <h3>Sự kiện nổi bật {{ $thisYear }}</h3>
    <ul>
        @foreach ($yearEvents as $event)
        <li>{!! $event !!}</li>
        @endforeach
    </ul>
    @endif

    @if (!empty($yearFactors))
    <h3>Yếu tố chi phối giá vàng {{ $thisYear }}</h3>
    <ul>
        @foreach ($yearFactors as $factor)
        <li>{!! $factor !!}</li>
        @endforeach
    </ul>
    @endif

    @if (!empty($yearAnalysis))
    <h3>Phân tích kỹ thuật</h3>
    <p>{!! $yearAnalysis !!}</p>
    @endif

    <h3>Liên kết hữu ích</h3>
    <ul>
        <li><a href="/gia-vang-hom-nay">Giá vàng hôm nay</a> — Cập nhật giá SJC, DOJI, PNJ mới nhất</li>
        <li><a href="/bieu-do-gia-vang">Biểu đồ giá vàng</a> — Phân tích kỹ thuật trực quan</li>
        <li><a href="/du-bao-gia-vang">Dự báo giá vàng</a> — Nhận định xu hướng tương lai</li>
        @if ($thisYear > 2020)
        <li><a href="/lich-su-gia-vang/gia-vang-{{ $thisYear - 1 }}">Giá vàng {{ $thisYear - 1 }}</a> — So sánh với năm trước</li>
        @endif
        @if ($thisYear < 2026)
        <li><a href="/lich-su-gia-vang/gia-vang-{{ $thisYear + 1 }}">Giá vàng {{ $thisYear + 1 }}</a> — Xem năm tiếp theo</li>
        @endif
    </ul>
</article>

{{-- FAQ --}}
<div class="mt-5 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061] mb-4">
        <i data-lucide="help-circle" class="h-5 w-5 text-[#ffc300]"></i>
        Câu hỏi thường gặp
    </h2>
    <div class="divide-y divide-slate-200">
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-[#001061]">
                <span>Giá vàng SJC năm {{ $thisYear }} biến động ra sao?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">SJC mở đầu {{ number_format($yearOpen, 2) }} triệu, kết thúc {{ number_format($yearClose, 2) }} triệu ({{ sprintf('%+.2f%%', $yearChangePct) }}). Cao nhất {{ number_format($yearHigh, 2) }} triệu, thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu/lượng.</p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-[#001061]">
                <span>Tháng nào giá vàng SJC tăng mạnh nhất {{ $thisYear }}?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                @if ($bestMonth)
                {{ $bestMonth['label'] }} tăng mạnh nhất ({{ sprintf('%+.2f%%', $bestMonth['changePct']) }}), đóng cửa {{ number_format($bestMonth['close'], 2) }} triệu.
                @else
                Xem bảng giá theo tháng phía trên để so sánh chi tiết.
                @endif
            </p>
        </details>
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-[#001061]">
                <span>Giá vàng SJC cao nhất năm {{ $thisYear }} bao nhiêu?</span>
                <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">Cao nhất {{ number_format($yearHigh, 2) }} triệu/lượng. Thấp nhất {{ number_format($yearLow, 2) }} triệu. Biên độ dao động {{ number_format($yearHigh - $yearLow, 2) }} triệu.</p>
        </details>
        @if (!empty($yearFaqExtra))
        @foreach ($yearFaqExtra as $faq)
        <details class="group py-3">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-800 hover:text-[#001061]">
                <span>{{ $faq['q'] }}</span>
                <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180"></i>
            </summary>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $faq['a'] }}</p>
        </details>
        @endforeach
        @endif
    </div>
</div>
