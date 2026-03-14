<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Lich su gia vang {{ $yearLabel }}</h2>
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="/lich-su-gia-vang/gia-vang-2026" class="chip {{ $year === '2026' ? 'positive' : '' }}">2026</a>
        <a href="/lich-su-gia-vang/gia-vang-2025" class="chip {{ $year === '2025' ? 'positive' : '' }}">2025</a>
        <a href="/lich-su-gia-vang/gia-vang-2024" class="chip {{ $year === '2024' ? 'positive' : '' }}">2024</a>
        <a href="/lich-su-gia-vang/gia-vang-2023" class="chip {{ $year === '2023' ? 'positive' : '' }}">2023</a>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr><th class="p-3 text-left font-semibold">Thang</th><th class="p-3 text-right font-semibold">Mo cua</th><th class="p-3 text-right font-semibold">Cao nhat</th><th class="p-3 text-right font-semibold">Thap nhat</th><th class="p-3 text-right font-semibold">Dong cua</th><th class="p-3 text-right font-semibold">Thay doi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($months as $m)
                <tr>
                    <td class="p-3 font-medium">{{ $m['label'] }}</td>
                    <td class="p-3 text-right">{{ $m['open'] }}</td>
                    <td class="p-3 text-right">{{ $m['high'] }}</td>
                    <td class="p-3 text-right">{{ $m['low'] }}</td>
                    <td class="p-3 text-right">{{ $m['close'] }}</td>
                    <td class="p-3 text-right font-semibold {{ str_starts_with($m['change'], '+') ? 'text-emerald-600' : 'text-rose-600' }}">{{ $m['change'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <p class="text-xs text-slate-500 mb-2">Bieu do gia vang {{ $yearLabel }}</p>
        <svg viewBox="0 0 700 150" class="w-full h-36"><polyline fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linejoin="round" points="0,120 58,110 116,100 175,105 233,90 291,85 350,75 408,70 466,60 525,55 583,50 641,45 700,40"/></svg>
    </div>
</div>