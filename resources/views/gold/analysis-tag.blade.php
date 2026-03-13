@extends('gold.page-shell')

@section('page-label', 'Phân tích giá vàng')

@section('page-content')
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-1">Bài viết theo tag: <span class="text-[#001061]">{{ $tag }}</span></h2>
    <p class="text-sm text-slate-500 mb-6">{{ $articles->total() }} bài viết</p>

    @if ($articles->isEmpty())
        <p class="text-slate-500">Chưa có bài viết nào cho tag này.</p>
    @else
        <div class="grid gap-4">
            @foreach ($articles as $article)
                <a href="/phan-tich/{{ $article->slug }}" class="group flex gap-4 rounded-sm border border-slate-200 bg-white p-4 hover:shadow-md transition">
                    @if ($article->thumbnail_path)
                        <img src="{{ asset('storage/' . $article->thumbnail_path) }}" alt="{{ $article->title }}" class="w-32 h-20 object-cover rounded-sm flex-shrink-0" loading="lazy" />
                    @else
                        <div class="w-32 h-20 bg-gradient-to-br from-amber-100 to-amber-50 rounded-sm flex items-center justify-center flex-shrink-0">
                            <span class="text-amber-400 text-2xl">📊</span>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-slate-800 group-hover:text-[#001061] transition line-clamp-2">{{ $article->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $article->published_at?->format('H:i d/m/Y') }}</p>
                        @if (!empty($article->tags))
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach (array_slice($article->tags, 0, 5) as $t)
                                    <span class="inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600">{{ $t }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection

@section('page-sidebar')
<div class="rounded-sm border border-[#bcbcbc] bg-white p-4">
    <h3 class="text-lg font-bold text-slate-900 mb-3">Tags phổ biến</h3>
    <div class="flex flex-wrap gap-2">
        @php
            $popularTags = [
                'gia-vang' => 'giá vàng',
                'phan-tich-gia-vang' => 'phân tích giá vàng',
                'gia-vang-sjc' => 'giá vàng SJC',
                'bien-dong-gia-vang' => 'biến động giá vàng',
                'gia-vang-the-gioi' => 'giá vàng thế giới',
                'xau-usd' => 'XAU/USD',
                'ban-tin-gia-vang' => 'bản tin giá vàng',
                'gia-vang-doji' => 'giá vàng DOJI',
                'gia-vang-pnj' => 'giá vàng PNJ',
                'gia-vang-btmc' => 'giá vàng BTMC',
            ];
        @endphp
        @foreach ($popularTags as $slug => $label)
            <a href="/phan-tich/tag/{{ $slug }}"
               class="inline-block rounded-full border px-3 py-1 text-xs font-medium transition
                      {{ $tagSlug === $slug ? 'bg-[#001061] text-white border-[#001061]' : 'bg-slate-50 text-slate-700 border-slate-300 hover:bg-[#001061] hover:text-white hover:border-[#001061]' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>
@endsection
