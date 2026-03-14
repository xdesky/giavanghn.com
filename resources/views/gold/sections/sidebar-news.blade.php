@php
    $sidebarNews = array_slice($newsArticles ?? [], 0, 5);
@endphp
<div class="rounded-sm border border-[#bcbcbc] bg-white">
    <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
        <i data-lucide="flame" class="h-4 w-4 text-[#ffc300]"></i>
        <h3 class="text-lg font-bold text-[#001061]">Tin mới nhất</h3>
    </div>
    @if (count($sidebarNews) > 0)
        <div class="divide-y divide-slate-100">
            @foreach ($sidebarNews as $idx => $sn)
                @php $isExternal = !empty($sn['url']) && !str_starts_with($sn['url'], '/'); @endphp
                <a href="{{ $sn['url'] ?? '/tin-tuc-gia-vang' }}"
                   class="group flex gap-3 px-4 py-2.5 no-underline transition-colors hover:bg-slate-50"@if($isExternal) target="_blank" rel="noopener"@endif>
                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded bg-[#001061]/8 text-[11px] font-bold text-[#001061]/60">{{ $idx + 1 }}</span>
                    <div class="min-w-0 flex-1">
                        <span class="text-[13px] font-medium leading-snug text-slate-700 line-clamp-2 transition-colors group-hover:text-[#001061]">{{ $sn['title'] }}</span>
                        @if (!empty($sn['date']))
                            <span class="mt-0.5 block text-[11px] text-slate-400">{{ $sn['date'] }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="border-t border-slate-200 px-4 py-2.5">
            <a href="/tin-tuc-gia-vang" class="flex items-center justify-center gap-1 text-xs font-semibold text-[#001061] no-underline transition-colors hover:text-[#ffc300]">
                Xem tất cả tin tức
                <i data-lucide="arrow-right" class="h-3 w-3"></i>
            </a>
        </div>
    @else
        <div class="px-4 py-6 text-center">
            <i data-lucide="inbox" class="mx-auto mb-1 h-8 w-8 text-slate-200"></i>
            <p class="text-xs text-slate-400">Đang cập nhật tin tức...</p>
        </div>
    @endif
</div>