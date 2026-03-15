<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý Subscriber - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-3 sm:px-6 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto">

        {{-- Navigation --}}
        <nav class="mb-8 flex flex-wrap gap-3 sm:gap-4">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Dashboard</a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Người dùng</a>
            <a href="{{ route('admin.subscribers.index') }}" class="px-4 py-2 rounded-sm bg-blue-600 text-white font-semibold">Subscriber</a>
            <a href="{{ route('admin.subscribers.push') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Push thông báo</a>
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Bài viết</a>
            <a href="{{ route('admin.news.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Tin tức</a>
        </nav>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-sm bg-emerald-50 border border-emerald-200">
                <p class="text-emerald-700 font-semibold m-0">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-sm border border-[#bcbcbc] p-5">
                <p class="text-sm text-slate-500 m-0">Tổng Subscriber</p>
                <p class="text-2xl font-bold text-[#001061] m-0 mt-1">{{ $totalActive + $totalInactive }}</p>
            </div>
            <div class="bg-white rounded-sm border border-emerald-200 p-5">
                <p class="text-sm text-slate-500 m-0">Đang hoạt động</p>
                <p class="text-2xl font-bold text-emerald-700 m-0 mt-1">{{ $totalActive }}</p>
            </div>
            <div class="bg-white rounded-sm border border-rose-200 p-5">
                <p class="text-sm text-slate-500 m-0">Đã hủy</p>
                <p class="text-2xl font-bold text-rose-700 m-0 mt-1">{{ $totalInactive }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-sm border border-slate-200 p-4 sm:p-6 mb-6">
            <form action="{{ route('admin.subscribers.index') }}" method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo email, tên..." class="px-4 py-2 rounded-sm border border-slate-300 text-sm focus:border-blue-500 focus:outline-none">
                <select name="status" class="px-4 py-2 rounded-sm border border-slate-300 text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã hủy</option>
                </select>
                <select name="market" class="px-4 py-2 rounded-sm border border-slate-300 text-sm">
                    <option value="">Tất cả thị trường</option>
                    @foreach (['sjc' => 'SJC', 'doji' => 'DOJI', 'pnj' => 'PNJ', 'phuquy' => 'Phú Quý', 'btmc' => 'BTMC', 'mihong' => 'Mi Hồng', 'xau-usd' => 'XAU/USD', 'xag-usd' => 'XAG/USD'] as $key => $label)
                        <option value="{{ $key }}" {{ request('market') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-sm bg-[#001061] text-white text-sm font-semibold cursor-pointer hover:bg-[#001061]/90">Lọc</button>
                @if (request()->hasAny(['search', 'status', 'market']))
                    <a href="{{ route('admin.subscribers.index') }}" class="px-4 py-2 rounded-sm border border-slate-300 text-sm text-slate-600 no-underline hover:bg-slate-50">Xóa bộ lọc</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-slate-600">
                            <th class="px-4 py-3 font-semibold">
                                <input type="checkbox" id="selectAll" class="cursor-pointer">
                            </th>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold">Họ tên</th>
                            <th class="px-4 py-3 font-semibold">Thị trường</th>
                            <th class="px-4 py-3 font-semibold">Trạng thái</th>
                            <th class="px-4 py-3 font-semibold">Ngày đăng ký</th>
                            <th class="px-4 py-3 font-semibold">Thông báo gần nhất</th>
                            <th class="px-4 py-3 font-semibold text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($subscribers as $sub)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="row-check cursor-pointer" value="{{ $sub->id }}">
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $sub->email }}</td>
                                <td class="px-4 py-3">{{ $sub->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @foreach ($sub->markets ?? [] as $m)
                                        <span class="inline-block rounded bg-blue-50 px-1.5 py-0.5 text-xs font-semibold text-blue-700 mr-0.5 mb-0.5">{{ strtoupper($m) }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">
                                    @if ($sub->active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Hoạt động</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Đã hủy</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $sub->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $sub->last_notified_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.subscribers.toggle', $sub) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="cursor-pointer rounded-sm border px-2 py-1 text-xs font-semibold transition {{ $sub->active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                                {{ $sub->active ? 'Vô hiệu' : 'Kích hoạt' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.subscribers.destroy', $sub) }}" method="POST" class="inline" onsubmit="return confirm('Xóa subscriber này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="cursor-pointer rounded-sm border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400">Chưa có subscriber nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Bulk actions --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3">
                <form id="bulkDeleteForm" action="{{ route('admin.subscribers.bulkDestroy') }}" method="POST" onsubmit="return confirm('Xóa tất cả subscriber đã chọn?')">
                    @csrf
                    <div id="bulkIds"></div>
                    <button type="submit" class="cursor-pointer rounded-sm border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50" disabled id="bulkDeleteBtn">Xóa đã chọn (<span id="bulkCount">0</span>)</button>
                </form>
                <div>{{ $subscribers->withQueryString()->links() }}</div>
            </div>
        </div>

        </div>
    </div>

    @include('gold.partials.footer')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checks = document.querySelectorAll('.row-check');
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        const bulkCount = document.getElementById('bulkCount');
        const bulkIds = document.getElementById('bulkIds');

        function updateBulk() {
            const checked = document.querySelectorAll('.row-check:checked');
            bulkCount.textContent = checked.length;
            bulkBtn.disabled = checked.length === 0;
            bulkIds.innerHTML = '';
            checked.forEach(c => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = c.value;
                bulkIds.appendChild(inp);
            });
        }

        selectAll?.addEventListener('change', () => {
            checks.forEach(c => c.checked = selectAll.checked);
            updateBulk();
        });
        checks.forEach(c => c.addEventListener('change', updateBulk));
    });
    </script>
</body>
</html>
