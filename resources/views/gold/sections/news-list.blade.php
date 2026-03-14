<div class="rounded-sm border border-[#bcbcbc] bg-white">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 md:px-6">
        <h2 class="flex items-center gap-2 text-lg font-bold text-[#001061]">
            <i data-lucide="newspaper" class="h-5 w-5 text-[#ffc300]"></i>
            {{ $categoryLabel }}
        </h2>

        {{-- Category navigation --}}
        <nav class="flex gap-1" aria-label="Danh mục tin tức">
            @php
                $cats = [
                    ['slug' => 'all',      'path' => '/tin-tuc-gia-vang',            'label' => 'Tất cả'],
                    ['slug' => 'domestic', 'path' => '/tin-tuc-gia-vang/trong-nuoc', 'label' => 'Trong nước'],
                    ['slug' => 'world',    'path' => '/tin-tuc-gia-vang/the-gioi',   'label' => 'Thế giới'],
                ];
            @endphp
            @foreach ($cats as $c)
                <a href="{{ $c['path'] }}"
                   class="px-3 py-1.5 rounded text-xs font-semibold no-underline transition-all
                          {{ $category === $c['slug']
                              ? 'bg-[#001061] text-white shadow-sm'
                              : 'text-slate-500 hover:bg-slate-100 hover:text-[#001061]' }}">
                    {{ $c['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Article list --}}
    @if (count($articles) > 0)
    <div class="divide-y divide-slate-100">
        @foreach ($articles as $a)
        @php
            $isExternal = !empty($a['url']) && !str_starts_with($a['url'], '/');
            $isFirst = $loop->first;
        @endphp

        {{-- Featured first article --}}
        @if ($isFirst && !empty($a['image_url']))
        <article class="group" itemscope itemtype="https://schema.org/NewsArticle">
            <a href="{{ $a['url'] ?? '#' }}"@if($isExternal) target="_blank" rel="noopener"@endif class="block no-underline">
                <div class="relative overflow-hidden">
                    <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                         class="w-full h-48 md:h-56 object-cover transition-transform duration-300 group-hover:scale-[1.02]" itemprop="image">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 md:p-5">
                        @if (!empty($a['tag']))
                            <span class="mb-2 inline-block rounded bg-[#ffc300] px-2 py-0.5 text-xs font-bold uppercase tracking-wide text-[#001061]">{{ $a['tag'] }}</span>
                        @endif
                        <h3 class="text-base md:text-lg font-bold text-white leading-snug line-clamp-2 drop-shadow" itemprop="headline">{{ $a['title'] }}</h3>
                        <div class="mt-2 flex items-center gap-2 text-xs text-white/70">
                            @if (!empty($a['date']))
                                <i data-lucide="clock" class="h-3 w-3"></i>
                                <time itemprop="datePublished">{{ $a['date'] }}</time>
                            @endif
                            @if (!empty($a['source']))
                                <span class="text-white/40">•</span>
                                <span itemprop="publisher">{{ $a['source'] }}</span>
                            @endif
                            @if (!empty($a['impact']))
                                <span class="ml-auto inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-xs font-semibold backdrop-blur-sm
                                    {{ $a['impact'] === 'positive' ? 'bg-emerald-500/20 text-emerald-300' : ($a['impact'] === 'negative' ? 'bg-rose-500/20 text-rose-300' : 'bg-white/10 text-white/60') }}">
                                    {{ $a['impact'] === 'positive' ? '▲ Tích cực' : ($a['impact'] === 'negative' ? '▼ Tiêu cực' : '— Trung tính') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </article>

        @else
        {{-- Regular article rows --}}
        <article class="group flex gap-3.5 px-4 py-3 md:px-6 md:py-4 transition-colors hover:bg-slate-50/70" itemscope itemtype="https://schema.org/NewsArticle">
            {{-- Thumbnail --}}
            @if (!empty($a['image_url']))
                <a href="{{ $a['url'] }}"@if($isExternal) target="_blank" rel="noopener"@endif class="shrink-0">
                    <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                         class="w-20 h-14 sm:w-[120px] sm:h-[80px] rounded object-cover transition-shadow group-hover:shadow-md" itemprop="image">
                </a>
            @else
                <a href="{{ $a['url'] ?? '#' }}"@if($isExternal) target="_blank" rel="noopener"@endif class="shrink-0">
                    <div class="w-20 h-14 sm:w-[120px] sm:h-[80px] rounded bg-gradient-to-br from-[#001061]/5 to-[#001061]/10 grid place-items-center">
                        <i data-lucide="file-text" class="h-6 w-6 text-[#001061]/25"></i>
                    </div>
                </a>
            @endif

            {{-- Content --}}
            <div class="min-w-0 flex-1">
                <h3 class="text-sm md:text-[15px] font-semibold leading-snug line-clamp-2" itemprop="headline">
                    <a href="{{ $a['url'] ?? '#' }}"@if($isExternal) target="_blank" rel="noopener"@endif
                       class="text-slate-800 no-underline transition-colors group-hover:text-[#001061]">
                        {{ $a['title'] }}
                    </a>
                </h3>
                <p class="mt-1 hidden text-xs leading-relaxed text-slate-400 line-clamp-1 md:block" itemprop="description">{{ $a['excerpt'] }}</p>
                <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-400">
                    @if (!empty($a['date']))
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="clock" class="h-3 w-3"></i>
                            <time itemprop="datePublished">{{ $a['date'] }}</time>
                        </span>
                    @endif
                    @if (!empty($a['source']))
                        <span class="text-slate-200">|</span>
                        <span class="text-slate-500" itemprop="publisher">{{ $a['source'] }}</span>
                    @endif
                    @if (!empty($a['tag']))
                        <span class="rounded bg-[#001061]/5 px-1.5 py-0.5 font-semibold text-[#001061]/70">{{ $a['tag'] }}</span>
                    @endif
                    @if (!empty($a['impact']))
                        <span class="inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 font-semibold
                            {{ $a['impact'] === 'positive' ? 'bg-emerald-50 text-emerald-600' : ($a['impact'] === 'negative' ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-400') }}">
                            {{ $a['impact'] === 'positive' ? '▲ Tích cực' : ($a['impact'] === 'negative' ? '▼ Tiêu cực' : '— Trung tính') }}
                        </span>
                    @endif
                </div>
            </div>
        </article>
        @endif

        @endforeach
    </div>

    {{-- Pagination --}}
    @if (!empty($newsPaginator) && $newsPaginator->hasPages())
        <div class="border-t border-slate-200 px-4 py-3 md:px-6">
            <nav class="flex justify-center" aria-label="Phân trang tin tức">
                {{ $newsPaginator->links() }}
            </nav>
        </div>
    @endif
    @else
        <div class="px-4 py-10 text-center md:px-6">
            <i data-lucide="inbox" class="mx-auto mb-2 h-10 w-10 text-slate-300"></i>
            <p class="text-sm text-slate-400">Đang cập nhật tin tức mới nhất...</p>
        </div>
    @endif
</div>