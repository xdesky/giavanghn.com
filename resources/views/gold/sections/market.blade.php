@php
    $label = $marketLabel ?? ($title ?? 'Thị trường');
    $dynamicColumns = $columns ?? [];
    $dynamicRows = $rows ?? [];

    if (isset($path) && str_contains($path, 'thi-truong/ty-gia-ngoai-te')) {
        $rates = \App\Models\ExchangeRate::latestByPair()->get();
        $dynamicColumns = ['Cặp tỷ giá', 'Giá', 'Thay đổi'];
        $dynamicRows = $rates->map(fn($r) => [
            $r->pair,
            number_format((float) $r->rate, 2, ',', '.'),
            sprintf('%+.2f%%', (float) $r->change_percent),
            '_color' => ((float) $r->change_percent >= 0 ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-semibold'),
        ])->toArray();
    } elseif (isset($path) && str_contains($path, 'thi-truong/gia-kim-loai')) {
        $markets = $snapshot['globalMarkets'] ?? [];
        $dynamicColumns = ['Kim loại', 'Giá', 'Biến động'];
        $dynamicRows = collect($markets)->map(fn($m) => [
            $m['name'],
            $m['price'],
            $m['change'],
            '_color' => (($m['trend'] ?? 'up') === 'up' ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-semibold'),
        ])->toArray();
    } elseif (isset($path) && str_contains($path, 'thi-truong/gia-bac')) {
        $silver = collect($snapshot['globalMarkets'] ?? [])->first(fn($m) => str_contains(mb_strtolower($m['name']), 'silver'));
        $dynamicColumns = ['Sản phẩm', 'Giá', 'Ghi chú'];
        $dynamicRows = [
            ['Bạc giao ngay (XAG/USD)', $silver['price'] ?? 'Đang cập nhật', $silver['change'] ?? '—'],
        ];
    }
@endphp

<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">{{ $label }}</h2>
    <div class="overflow-x-auto rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    @foreach ($dynamicColumns as $col)
                        <th class="p-3 {{ $loop->first ? 'text-left' : 'text-right' }} font-semibold">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($dynamicRows as $row)
                    <tr>
                        @foreach ($row as $i => $cell)
                            @if ($i !== '_color')
                                <td class="p-3 {{ $i === 0 ? 'font-medium text-left' : 'text-right' }} {{ isset($row['_color']) && $i === count($row)-2 ? $row['_color'] : '' }}">{{ $cell }}</td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($dynamicColumns) ?: 3 }}" class="p-4 text-center text-slate-500">Chưa có dữ liệu thị trường.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <p class="text-xs text-slate-500 mb-2">Biểu đồ {{ $label }} 30 ngày</p>
        <svg viewBox="0 0 600 120" class="w-full h-28"><polyline fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linejoin="round" points="0,90 60,85 120,80 180,75 240,70 300,65 360,60 420,55 480,50 540,48 600,45"/></svg>
    </div>
    <p class="mt-3 text-xs text-slate-400">Dữ liệu mang tính tham khảo, cập nhật từ nguồn chính thức.</p>
</div>