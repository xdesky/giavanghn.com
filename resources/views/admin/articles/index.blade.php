<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý bài viết - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest" defer></script>
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-6 py-8">
        <div class="max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                <p class="text-emerald-700 font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-sm border border-slate-200 p-6 mb-6">
            <form action="{{ route('admin.articles.index') }}" method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm kiếm theo tiêu đề..." 
                       class="px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500">
                <select name="trigger_type" class="px-4 py-2 rounded-sm border border-slate-300">
                    <option value="">Tất cả loại</option>
                    <option value="daily" {{ request('trigger_type') === 'daily' ? 'selected' : '' }}>Hàng ngày</option>
                    <option value="change" {{ request('trigger_type') === 'change' ? 'selected' : '' }}>Biến động</option>
                </select>
                <button type="submit" class="px-6 py-2 rounded-sm bg-blue-600 text-white font-semibold hover:bg-blue-700">Lọc</button>
            </form>
        </div>

        <!-- Articles Table -->
        <div class="bg-white rounded-sm border border-slate-200 overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50 border-b">
                    <tr class="text-left text-sm font-semibold text-slate-700">
                        <th class="px-6 py-4">Tiêu đề</th>
                        <th class="px-6 py-4">Loại</th>
                        <th class="px-6 py-4">Ngày</th>
                        <th class="px-6 py-4">Số từ</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($articles as $article)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.articles.show', $article) }}" class="font-medium text-blue-600 hover:text-blue-700">
                                    {{ Str::limit($article->title, 80) }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $article->trigger_type === 'daily' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $article->trigger_type === 'daily' ? 'Hàng ngày' : 'Biến động' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($article->analysis_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ number_format($article->word_count) }} từ</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.articles.toggle-publish', $article) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded text-xs font-semibold {{ $article->published_at ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $article->published_at ? '✓ Đã xuất bản' : '✕ Chưa xuất bản' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.articles.show', $article) }}" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-sm font-semibold">Xem</a>
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="px-3 py-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200 text-sm font-semibold">Sửa</a>
                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 rounded bg-rose-100 text-rose-700 hover:bg-rose-200 text-sm font-semibold">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Chưa có bài viết nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t">{{ $articles->links() }}</div>
        </div>
        </div>
    </div>

    @include('gold.partials.footer')

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
