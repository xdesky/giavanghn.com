@php
    $sjc = $snapshot['sjcCard'] ?? null;
    $us  = $snapshot['usCard'] ?? null;
    $topBrands = $snapshot['topBrands'] ?? [];
    $ao  = $snapshot['analystOpinion'] ?? null;
@endphp

{{-- Hero Cards: SJC + Thế giới --}}
<div class="grid gap-5 sm:grid-cols-2">
    {{-- SJC Card --}}
    @if ($sjc)
    <div class="rounded-sm border border-amber-200 bg-amber-50 p-5">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold text-amber-800">{{ $sjc['title'] }}</p>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold {{ $sjc['trendPercent'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ sprintf('%+.2f%%', $sjc['trendPercent']) }}
            </span>
        </div>
        @php $sv = $sjc['variants'][$sjc['selected']] ?? collect($sjc['variants'])->first(); @endphp
        @if ($sv)
            <p class="mt-2 text-xl sm:text-3xl font-bold text-amber-900">{{ number_format($sv['sell'] * 1000000, 0, ',', '.') }} <small class="text-base font-normal text-amber-700">VNĐ</small></p>
            <div class="mt-2 flex gap-4 text-sm text-amber-800">
                <span>Mua: <strong>{{ number_format($sv['buy'] * 1000000, 0, ',', '.') }}</strong></span>
                <span>Bán: <strong>{{ number_format($sv['sell'] * 1000000, 0, ',', '.') }}</strong></span>
            </div>
            <p class="mt-1 text-sm font-semibold {{ str_starts_with($sv['dayChangeLabel'] ?? '', '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $sv['dayChangeLabel'] ?? '' }}</p>
        @endif
    </div>
    @endif

    {{-- World Gold Card --}}
    @if ($us)
    <div class="rounded-sm border border-blue-200 bg-blue-50 p-5">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold text-blue-800">{{ $us['title'] }}</p>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold {{ $us['trendPercent'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ sprintf('%+.2f%%', $us['trendPercent']) }}
            </span>
        </div>
        @php $uv = $us['variants'][$us['selected']] ?? collect($us['variants'])->first(); @endphp
        @if ($uv)
            <p class="mt-2 text-xl sm:text-3xl font-bold text-blue-900">{{ number_format($uv['price'], 2) }} <small class="text-base font-normal text-blue-700">USD/oz</small></p>
            <p class="mt-1 text-sm font-semibold {{ str_starts_with($uv['dayChangeLabel'] ?? '', '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $uv['dayChangeLabel'] ?? '' }}</p>
        @endif
    </div>
    @endif
</div>

{{-- Bảng giá tất cả thương hiệu --}}
@if (count($topBrands) > 0)
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061]">
            <i data-lucide="table" class="h-5 w-5"></i> Bảng giá vàng các thương hiệu
        </h2>
        <span class="flex items-center gap-1 text-xs text-slate-500">
            <i data-lucide="clock" class="h-3 w-3"></i> Cập nhật: {{ now()->format('H:i d/m/Y') }}
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-sm">
            <caption class="sr-only">Bảng giá vàng các thương hiệu hôm nay {{ now()->format('d/m/Y') }}</caption>
            <thead>
                <tr class="bg-[#f5f5f5]">
                    <th class="border-b border-[#bcbcbc] p-3 text-left font-semibold text-slate-700">Thương hiệu</th>
                    <th class="border-b border-[#bcbcbc] p-3 text-right font-semibold text-slate-700">Mua vào (VNĐ)</th>
                    <th class="border-b border-[#bcbcbc] p-3 text-right font-semibold text-slate-700">Bán ra (VNĐ)</th>
                    <th class="border-b border-[#bcbcbc] p-3 text-right font-semibold text-slate-700">Chênh lệch</th>
                    <th class="border-b border-[#bcbcbc] p-3 text-right font-semibold text-slate-700">Thay đổi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topBrands as $brand)
                    <tr class="transition-colors hover:bg-[#f5f5f5]">
                        <td class="border-b border-[#ebebeb] p-3 font-medium">{{ $brand['brand'] }}</td>
                        <td class="border-b border-[#ebebeb] p-3 text-right tabular-nums font-bold">{{ number_format($brand['buy'], 0, ',', '.') }}</td>
                        <td class="border-b border-[#ebebeb] p-3 text-right tabular-nums font-bold">{{ number_format($brand['sell'], 0, ',', '.') }}</td>
                        <td class="border-b border-[#ebebeb] p-3 text-right tabular-nums text-slate-600">{{ number_format($brand['sell'] - $brand['buy'], 0, ',', '.') }}</td>
                        <td class="border-b border-[#ebebeb] p-3 text-right tabular-nums font-bold {{ $brand['change'] >= 0 ? 'text-[#008236]' : 'text-[#e7000b]' }}">{{ sprintf('%+.2f%%', $brand['change']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Nhận định thị trường --}}
@if ($ao)
@php
    $aoBias = $ao['bias'] ?? 'neutral';
    $aoIcon = match($aoBias) { 'bullish' => 'trending-up', 'bearish' => 'trending-down', default => 'minus' };
    $aoColor = match($aoBias) { 'bullish' => 'text-[#008236] bg-emerald-50 border-emerald-200', 'bearish' => 'text-[#e7000b] bg-rose-50 border-rose-200', default => 'text-[#e17100] bg-orange-50 border-orange-200' };
@endphp
<div class="rounded-sm border {{ $aoColor }} p-4">
    <div class="flex items-center gap-2 mb-2">
        <i data-lucide="bot" class="h-5 w-5 text-[#001061]"></i>
        <h3 class="font-bold text-[#001061]">Nhận định thị trường</h3>
        <span class="ml-auto inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-bold {{ $aoColor }}">
            <i data-lucide="{{ $aoIcon }}" class="h-3 w-3"></i> {{ $ao['label'] ?? ucfirst($aoBias) }}
        </span>
    </div>
    <p class="text-sm leading-relaxed text-slate-700">{{ $ao['summary'] ?? '' }}</p>
</div>
@endif