<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Dự báo giá vàng {{ $periodLabel }}</h2>
    <div class="grid gap-5 sm:grid-cols-3 mb-4">
        <div class="rounded-sm border-2 border-emerald-200 bg-emerald-50 p-4 text-center">
            <p class="text-xs font-semibold text-emerald-700 uppercase">Kịch bản tích cực</p>
            <p class="mt-2 text-2xl font-bold text-emerald-800">{{ $bullPrice }}</p>
            <p class="mt-1 text-sm text-emerald-600">{{ $bullChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-blue-200 bg-blue-50 p-4 text-center">
            <p class="text-xs font-semibold text-blue-700 uppercase">Kịch bản cơ sở</p>
            <p class="mt-2 text-2xl font-bold text-blue-800">{{ $basePrice }}</p>
            <p class="mt-1 text-sm text-blue-600">{{ $baseChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-rose-200 bg-rose-50 p-4 text-center">
            <p class="text-xs font-semibold text-rose-700 uppercase">Kịch bản tiêu cực</p>
            <p class="mt-2 text-2xl font-bold text-rose-800">{{ $bearPrice }}</p>
            <p class="mt-1 text-sm text-rose-600">{{ $bearChange }}</p>
        </div>
    </div>
    <div class="rounded-sm border border-slate-200 p-4 mb-4">
        <h3 class="font-bold text-sm mb-3">Các yếu tố quyết định</h3>
        <div class="grid gap-2">
            @foreach ($factors as $f)
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-700">{{ $f['name'] }}</span>
                <span class="font-semibold {{ $f['impact'] === 'positive' ? 'text-emerald-600' : ($f['impact'] === 'negative' ? 'text-rose-600' : 'text-slate-500') }}">{{ $f['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <p class="text-sm leading-relaxed text-blue-800">{{ $analysis }}</p>
    </div>
</div>