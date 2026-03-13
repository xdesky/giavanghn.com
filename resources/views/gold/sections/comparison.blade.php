<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">So sanh {{ $leftName }} va {{ $rightName }}</h2>
    <div class="grid gap-3 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border-2 border-blue-200 bg-blue-50 p-4 text-center">
            <p class="text-sm font-medium text-blue-700">{{ $leftName }}</p>
            <p class="mt-2 text-3xl fontbold text-blue-900">{{ $leftPrice }}</p>
            <p class="mt-1 text-sm font-bold text-emerald-600">{{ $leftChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-indigo-200 bg-indigo-50 p-4 text-center">
            <p class="text-sm font-medium text-indigo-700">{{ $rightName }}</p>
            <p class="mt-2 text-3xl fontbold text-indigo-900">{{ $rightPrice }}</p>
            <p class="mt-1 text-sm font-bold text-emerald-600">{{ $rightChange }}</p>
        </div>
    </div>
    <div class="rounded-sm border border-amber-200 bg-amber-50 p-4 mb-4">
        <p class="text-sm font-semibold text-amber-900">Chenh lech hien tai</p>
        <p class="mt-1 text-2xl fontbold text-amber-800">{{ $spread }}</p>
        <p class="mt-1 text-xs text-amber-700">{{ $spreadNote }}</p>
    </div>
    <div class="table-wrap rounded-sm border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left font-semibold">Chi tieu</th><th class="p-3 text-right font-semibold">{{ $leftName }}</th><th class="p-3 text-right font-semibold">{{ $rightName }}</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3">Gia hien tai</td><td class="p-3 text-right font-medium">{{ $leftPrice }}</td><td class="p-3 text-right font-medium">{{ $rightPrice }}</td></tr>
                <tr><td class="p-3">Tang/Giam 24h</td><td class="p-3 text-right">{{ $leftChange }}</td><td class="p-3 text-right">{{ $rightChange }}</td></tr>
                <tr><td class="p-3">Cao nhat 30 ngay</td><td class="p-3 text-right">{{ $leftHigh ?? 'N/A' }}</td><td class="p-3 text-right">{{ $rightHigh ?? 'N/A' }}</td></tr>
                <tr><td class="p-3">Thap nhat 30 ngay</td><td class="p-3 text-right">{{ $leftLow ?? 'N/A' }}</td><td class="p-3 text-right">{{ $rightLow ?? 'N/A' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>