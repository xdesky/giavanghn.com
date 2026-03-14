@php
    $usCard = $snapshot['usCard'] ?? null;
    $usv = $usCard ? ($usCard['variants'][$usCard['selected']] ?? collect($usCard['variants'])->first()) : null;
    $sidebarBrands = $snapshot['topBrands'] ?? [];
@endphp
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4">
    <h3 class="text-lg font-bold text-[#001061] mb-3">Giá vàng thế giới</h3>
    @if ($usv)
    <div class="rounded-sm border border-blue-200 bg-blue-50 p-3 text-center">
        <p class="text-2xl font-bold text-blue-900">{{ number_format($usv['price'], 2) }}</p>
        <p class="text-xs text-blue-700">USD/oz &nbsp; <span class="font-semibold {{ str_starts_with($usv['dayChangeLabel'] ?? '', '-') ? 'text-rose-600' : 'text-emerald-600' }}">{{ $usv['dayChangeLabel'] ?? '' }}</span></p>
    </div>
    @endif
    <div class="mt-3 grid gap-2 text-sm">
        @foreach (array_slice($sidebarBrands, 0, 4) as $sb)
            <div class="flex justify-between">
                <span class="text-slate-500">{{ $sb['brand'] }}</span>
                <span class="font-semibold tabular-nums">{{ number_format($sb['sell'], 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>
</div>