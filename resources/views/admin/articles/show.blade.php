<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $article->title }} - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-6 py-8">
        <div class="max-w-4xl mx-auto">

            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                    <p class="text-emerald-700 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('admin.articles.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">&larr; Danh sách bài viết</a>
            </div>

            {{-- Article meta --}}
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $article->trigger_type === 'daily' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $article->trigger_type === 'daily' ? 'Hàng ngày' : 'Biến động' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $article->published_at ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $article->published_at ? '✓ Đã xuất bản' : '✕ Chưa xuất bản' }}
                </span>
                <span class="text-xs text-slate-400">{{ $article->analysis_date->format('d/m/Y') }} · {{ number_format($article->word_count) }} từ</span>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-6">{{ $article->title }}</h1>

            {{-- Actions --}}
            <div class="flex items-center gap-2 mb-6">
                <a href="{{ route('admin.articles.edit', $article) }}" class="px-4 py-2 rounded-sm bg-amber-100 text-amber-700 hover:bg-amber-200 text-sm font-semibold">Sửa</a>
                <form method="POST" action="{{ route('admin.articles.toggle-publish', $article) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-sm bg-blue-100 text-blue-700 hover:bg-blue-200 text-sm font-semibold">
                        {{ $article->published_at ? 'Ẩn bài' : 'Xuất bản' }}
                    </button>
                </form>
                @if ($article->published_at && $article->slug)
                    <a href="{{ route('analysis.show', $article->slug) }}" target="_blank" class="px-4 py-2 rounded-sm bg-emerald-100 text-emerald-700 hover:bg-emerald-200 text-sm font-semibold">Xem trang công khai ↗</a>
                @endif
                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Xóa bài viết này?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-sm bg-rose-100 text-rose-700 hover:bg-rose-200 text-sm font-semibold">Xóa</button>
                </form>
            </div>

            {{-- Summary --}}
            @if ($article->summary)
                <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-[#bcbcbc] text-slate-700 text-sm italic">
                    {{ $article->summary }}
                </div>
            @endif

            {{-- Content --}}
            <div class="bg-white rounded-sm border border-slate-200 p-6 md:p-8 prose prose-slate max-w-none">
                {!! $article->content !!}
            </div>
        </div>
    </div>

    @include('gold.partials.footer')</body>
</html>
