<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sửa bài viết - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest" defer></script>
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-6 py-8">
        <div class="max-w-4xl mx-auto">

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('admin.articles.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">&larr; Danh sách bài viết</a>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-6">Sửa bài viết</h1>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200">
                    <ul class="text-rose-700 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.articles.update', $article) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-sm border border-slate-200 p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">Tiêu đề</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title', $article->title) }}"
                               class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                               required>
                    </div>

                    {{-- Summary --}}
                    <div>
                        <label for="summary" class="block text-sm font-semibold text-slate-700 mb-1">Tóm tắt</label>
                        <textarea name="summary" id="summary" rows="3"
                                  class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">{{ old('summary', $article->summary) }}</textarea>
                    </div>

                    {{-- Content --}}
                    <div>
                        <label for="content" class="block text-sm font-semibold text-slate-700 mb-1">Nội dung (HTML)</label>
                        <textarea name="content" id="content" rows="25"
                                  class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none font-mono text-sm"
                                  required>{{ old('content', $article->content) }}</textarea>
                    </div>

                    {{-- Meta info --}}
                    <div class="flex flex-wrap gap-4 text-sm text-slate-500 border-t pt-4">
                        <span>Loại: <strong>{{ $article->trigger_type === 'daily' ? 'Hàng ngày' : 'Biến động' }}</strong></span>
                        <span>Ngày: <strong>{{ $article->analysis_date->format('d/m/Y') }}</strong></span>
                        <span>Số từ: <strong>{{ number_format($article->word_count) }}</strong></span>
                        <span>Trạng thái: <strong>{{ $article->published_at ? 'Đã xuất bản' : 'Chưa xuất bản' }}</strong></span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-sm bg-blue-600 text-white font-semibold hover:bg-blue-700">Lưu thay đổi</button>
                    <a href="{{ route('admin.articles.show', $article) }}" class="px-6 py-2.5 rounded-sm border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Hủy</a>
                </div>
            </form>
        </div>
    </div>

    @include('gold.partials.footer')

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
