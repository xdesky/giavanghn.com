<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $toolTitle }}</h2>
    <p class="text-sm leading-relaxed text-slate-600 mb-4">{{ $toolDesc }}</p>
    <div class="rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <div class="grid gap-3">
            @foreach ($fields as $field)
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $field['label'] }}</label>
                @if (isset($field['options']))
                <select class="w-full rounded-sm border border-blue-200 bg-white px-3 py-2.5 text-sm">
                    @foreach ($field['options'] as $opt)
                    <option>{{ $opt }}</option>
                    @endforeach
                </select>
                @else
                <input type="{{ $field['type'] ?? 'number' }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                       class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" value="{{ $field['default'] ?? '' }}">
                @endif
            </div>
            @endforeach
        </div>
        <button class="btn-primary mt-4 w-full py-3" onclick="alert('Tính năng đang phát triển')">{{ $buttonLabel ?? 'Tính toán' }}</button>
    </div>
    <div class="mt-4 rounded-sm border-2 border-dashed border-blue-200 bg-blue-50/50 p-4 text-center">
        <p class="text-sm text-blue-700 font-medium">Kết quả sẽ hiển thị tại đây</p>
        <p class="mt-1 text-xl sm:text-3xl font-bold text-blue-900">—</p>
    </div>
    @if (!empty($instructions))
    <div class="mt-4 rounded-sm border border-slate-200 p-4">
        <h3 class="font-bold text-sm mb-2">Hướng dẫn sử dụng</h3>
        <ul class="space-y-1 text-sm leading-relaxed text-slate-600">
            @foreach ($instructions as $inst)
            <li>• {{ $inst }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>