<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý tin tức - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <form action="{{ route('admin.news.index') }}" method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm kiếm theo tiêu đề..."
                       class="px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500">
                <select name="source" class="px-4 py-2 rounded-sm border border-slate-300">
                    <option value="">Tất cả nguồn</option>
                    @foreach ($sources as $source)
                        <option value="{{ $source }}" {{ request('source') === $source ? 'selected' : '' }}>{{ strtoupper($source) }}</option>
                    @endforeach
                </select>
                <select name="tag" class="px-4 py-2 rounded-sm border border-slate-300">
                    <option value="">Tất cả thẻ</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag }}" {{ request('tag') === $tag ? 'selected' : '' }}>{{ $tag }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-6 py-2 rounded-sm bg-blue-600 text-white font-semibold hover:bg-blue-700">Lọc</button>
                @if (request()->hasAny(['search', 'source', 'tag']))
                    <a href="{{ route('admin.news.index') }}" class="px-4 py-2 rounded-sm bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Xóa bộ lọc</a>
                @endif
            </form>
        </div>

        <!-- Bulk Actions -->
        <form id="bulkForm" action="{{ route('admin.news.bulk-destroy') }}" method="POST">
            @csrf
            <div id="bulkBar" class="hidden mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3">
                <span class="text-sm font-semibold text-rose-700"><span id="selectedCount">0</span> tin đã chọn</span>
                <button type="submit" onclick="return confirm('Xóa tất cả tin đã chọn?')" class="px-4 py-1.5 rounded-sm bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">Xóa đã chọn</button>
            </div>

            <!-- News Table -->
            <div class="bg-white rounded-sm border border-slate-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b">
                        <tr class="text-left text-sm font-semibold text-slate-700">
                            <th class="px-4 py-4"><input type="checkbox" id="selectAll" class="rounded"></th>
                            <th class="px-4 py-4">Tiêu đề</th>
                            <th class="px-4 py-4">Nguồn</th>
                            <th class="px-4 py-4">Thẻ</th>
                            <th class="px-4 py-4">Thời gian</th>
                            <th class="px-4 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($news as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="bulk-check rounded">
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item->url)
                                        <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-600 hover:text-blue-700">
                                            {{ Str::limit($item->title, 80) }}
                                            <i data-lucide="external-link" class="inline h-3 w-3"></i>
                                        </a>
                                    @else
                                        <span class="font-medium text-slate-800">{{ Str::limit($item->title, 80) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-600">{{ strtoupper($item->source) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">{{ $item->tag }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $item->published_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Xóa tin này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 rounded bg-rose-100 text-rose-700 hover:bg-rose-200 text-sm font-semibold">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Chưa có tin tức nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t">{{ $news->withQueryString()->links() }}</div>
            </div>
        </form>
        </div>
    </div>

    @include('gold.partials.footer')

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectAll = document.getElementById('selectAll');
        const bulkBar = document.getElementById('bulkBar');
        const selectedCount = document.getElementById('selectedCount');
        const checks = document.querySelectorAll('.bulk-check');

        function updateBulkBar() {
            const count = document.querySelectorAll('.bulk-check:checked').length;
            selectedCount.textContent = count;
            bulkBar.classList.toggle('hidden', count === 0);
        }

        selectAll?.addEventListener('change', () => {
            checks.forEach(c => c.checked = selectAll.checked);
            updateBulkBar();
        });

        checks.forEach(c => c.addEventListener('change', updateBulkBar));
    });
    </script>
</body>
</html>
