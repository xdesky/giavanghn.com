<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $apiTitle }}</h2>
    <p class="text-sm text-slate-600 mb-4">{{ $apiDesc }}</p>

    @foreach ($endpoints as $ep)
    <div class="mb-4 rounded-sm border border-slate-200 overflow-hidden">
        <div class="flex items-center gap-3 bg-slate-50 px-4 py-3">
            <span class="rounded-sm px-2 py-1 text-xs font-bold {{ $ep['method'] === 'GET' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">{{ $ep['method'] }}</span>
            <code class="text-sm font-mono font-semibold text-slate-800">{{ $ep['path'] }}</code>
        </div>
        <div class="p-4">
            <p class="text-sm text-slate-600">{{ $ep['desc'] }}</p>
            @if (!empty($ep['params']))
            <div class="mt-3">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Tham so</p>
                <div class="table-wrap rounded border border-slate-200">
                    <table class="w-full text-sm">
                        <thead class="bg-[#f5f5f5]"><tr><th class="p-2 text-left font-semibold text-xs">Ten</th><th class="p-2 text-left font-semibold text-xs">Kieu</th><th class="p-2 text-left font-semibold text-xs">Mo ta</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($ep['params'] as $p)
                            <tr><td class="p-2"><code class="text-xs">{{ $p['name'] }}</code></td><td class="p-2 text-xs text-slate-500">{{ $p['type'] }}</td><td class="p-2 text-xs text-slate-600">{{ $p['desc'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if (!empty($ep['response']))
            <div class="mt-3">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Response</p>
                <pre class="rounded-sm bg-slate-900 p-3 text-xs text-green-400 overflow-x-auto"><code>{{ $ep['response'] }}</code></pre>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>