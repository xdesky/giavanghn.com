@php
    $sidebarNews = array_slice($newsArticles ?? [], 0, 5);
@endphp
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4">
    <h3 class="text-lg font-bold text-slate-900 mb-3">Tin mới nhất</h3>
    @if (count($sidebarNews) > 0)
        <div class="grid gap-2.5 text-sm">
            @foreach ($sidebarNews as $sn)
                <a href="{{ $sn['url'] ?? '/tin-tuc-gia-vang' }}" class="block text-blue-700 no-underline transition hover:text-blue-900 hover:underline" target="_blank" rel="noopener">
                    <span class="mr-1">{{ $sn['icon'] ?? '📰' }}</span>{{ $sn['title'] }}
                    @if (!empty($sn['date']))
                        <span class="block mt-0.5 text-xs text-slate-400">{{ $sn['date'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
        <a href="/tin-tuc-gia-vang" class="mt-3 block text-center text-xs font-semibold text-blue-600 no-underline hover:underline">Xem tất cả tin tức →</a>
    @else
        <p class="text-sm text-slate-500">Đang cập nhật tin tức...</p>
    @endif
</div>