@php
    $labelMap = [
        'SJC' => 'SJC',
        'DOJI' => 'DOJI',
        'PNJ' => 'PNJ',
        'BTMC' => 'Bảo Tín Minh Châu',
        'BTMH' => 'Bảo Tín Mạnh Hải',
    ];
    $vangMieng = $sidebar['vangMieng'] ?? [];
    $vangNhan  = $sidebar['vangNhan'] ?? [];
    $theGioi   = $sidebar['theGioi'] ?? [];
    $bac       = $sidebar['bac'] ?? [];
    $crypto    = $sidebar['crypto'] ?? [];
    $tyGia     = $sidebar['tyGia'] ?? [];

    $cryptoIcons = [
        'BTC' => '🪙',
        'ETH' => '⟠',
        'USDT' => '💲',
        'BNB' => '🔶',
        'XRP' => '✕',
    ];

    $bankLogos = [
        'VCB' => '/images/banks/vcb.png',
        'BIDV' => '/images/banks/bidv.png',
        'CTG' => '/images/banks/ctg.png',
        'TCB' => '/images/banks/tcb.png',
        'ACB' => '/images/banks/acb.png',
        'STB' => '/images/banks/stb.png',
    ];
@endphp

{{-- 1. Giá vàng miếng --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white">
    <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
        <i data-lucide="cuboid" class="h-4 w-4 text-[#ffc300]"></i>
        <h3 class="text-lg font-bold text-[#001061]">Giá vàng miếng</h3>
    </div>
    @if (count($vangMieng))
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 text-xs text-slate-500">
                <th class="px-4 py-2 text-left font-medium">Thương hiệu</th>
                <th class="px-4 py-2 text-right font-medium">Mua</th>
                <th class="px-4 py-2 text-right font-medium">Bán</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($vangMieng as $item)
            <tr>
                <td class="px-4 py-2 font-medium text-slate-700">{{ $labelMap[$item['label']] ?? $item['label'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums text-slate-600">{{ $item['buy'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums font-semibold text-slate-800">{{ $item['sell'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="px-4 py-2 text-[11px] italic text-slate-400">Triệu đồng/lượng</p>
    @else
    <div class="px-4 py-6 text-center text-xs text-slate-400">Đang cập nhật...</div>
    @endif
</div>

{{-- 2. Giá vàng nhẫn --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white">
    <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
        <i data-lucide="circle-dot" class="h-4 w-4 text-[#ffc300]"></i>
        <h3 class="text-lg font-bold text-[#001061]">Giá vàng nhẫn</h3>
    </div>
    @if (count($vangNhan))
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 text-xs text-slate-500">
                <th class="px-4 py-2 text-left font-medium">Thương hiệu</th>
                <th class="px-4 py-2 text-right font-medium">Mua</th>
                <th class="px-4 py-2 text-right font-medium">Bán</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($vangNhan as $item)
            <tr>
                <td class="px-4 py-2 font-medium text-slate-700">{{ $labelMap[$item['label']] ?? $item['label'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums text-slate-600">{{ $item['buy'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums font-semibold text-slate-800">{{ $item['sell'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="px-4 py-2 text-[11px] italic text-slate-400">Triệu đồng/lượng</p>
    @else
    <div class="px-4 py-6 text-center text-xs text-slate-400">Đang cập nhật...</div>
    @endif
</div>

{{-- 3. Giá vàng bạc thế giới --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white">
    <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
        <i data-lucide="globe" class="h-4 w-4 text-[#ffc300]"></i>
        <h3 class="text-lg font-bold text-[#001061]">Giá vàng bạc thế giới</h3>
    </div>
    @if (count($theGioi))
    <div class="divide-y divide-slate-100">
        @foreach ($theGioi as $item)
        <div class="flex items-center justify-between px-4 py-3">
            <div>
                <span class="text-xs font-medium text-slate-500">{{ $item['label'] }}</span>
                <p class="text-lg font-bold tabular-nums text-slate-800">{{ $item['price'] }}</p>
            </div>
            @if (!empty($item['change']))
            <span class="rounded px-2 py-0.5 text-xs font-semibold tabular-nums {{ $item['up'] ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                {{ $item['change'] }}
            </span>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="px-4 py-6 text-center text-xs text-slate-400">Đang cập nhật...</div>
    @endif
</div>

{{-- 4. Giá bạc trong nước --}}
<div class="rounded-sm border border-[#bcbcbc] bg-white">
    <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
        <i data-lucide="gem" class="h-4 w-4 text-[#ffc300]"></i>
        <h3 class="text-lg font-bold text-[#001061]">Giá bạc trong nước</h3>
    </div>
    @if (count($bac))
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 text-xs text-slate-500">
                <th class="px-4 py-2 text-left font-medium">Thương hiệu</th>
                <th class="px-4 py-2 text-right font-medium">Mua</th>
                <th class="px-4 py-2 text-right font-medium">Bán</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($bac as $item)
            <tr>
                <td class="px-4 py-2 font-medium text-slate-700">{{ $labelMap[$item['label']] ?? $item['label'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums text-slate-600">{{ $item['buy'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums font-semibold text-slate-800">{{ $item['sell'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="px-4 py-2 text-[11px] italic text-slate-400">x1.000đ/lượng</p>
    @else
    <div class="px-4 py-6 text-center text-xs text-slate-400">Đang cập nhật...</div>
    @endif
</div>