<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">{{ $categoryLabel }}</h2>
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="/tin-tuc-gia-vang" class="chip {{ $category === 'all' ? 'positive' : '' }}">Tat ca</a>
        <a href="/tin-tuc-gia-vang/tin-thi-truong-vang" class="chip {{ $category === 'market' ? 'positive' : '' }}">Thi truong vang</a>
        <a href="/tin-tuc-gia-vang/tin-tai-chinh" class="chip {{ $category === 'finance' ? 'positive' : '' }}">Tai chinh</a>
        <a href="/tin-tuc-gia-vang/tin-kinh-te" class="chip {{ $category === 'economy' ? 'positive' : '' }}">Kinh te</a>
        <a href="/tin-tuc-gia-vang/tin-the-gioi" class="chip {{ $category === 'world' ? 'positive' : '' }}">The gioi</a>
    </div>
    <div class="grid gap-3">
        @foreach ($articles as $a)
        <article class="news-item">
            <div class="shrink-0 w-16 h-16 rounded-sm bg-linear-to-br from-slate-100 to-slate-200 grid place-items-center text-xl">{{ $a['icon'] }}</div>
            <div>
                <h4 class="text-base font-semibold text-slate-900">{{ $a['title'] }}</h4>
                <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ $a['excerpt'] }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $a['date'] }}</p>
            </div>
        </article>
        @endforeach
    </div>
</div>