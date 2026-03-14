<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý người dùng - Admin</title>

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

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200">
                @foreach ($errors->all() as $error)
                    <p class="text-rose-700">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Filters & Actions -->
        <div class="bg-white rounded-sm border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Tìm kiếm theo tên hoặc email..." 
                           class="px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500">
                    
                    <select name="role" class="px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500">
                        <option value="">Tất cả vai trò</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="status" class="px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                    
                    <button type="submit" class="px-6 py-2 rounded-sm bg-blue-600 text-white font-semibold hover:bg-blue-700">
                        Lọc
                    </button>
                </form>
                
                <a href="{{ route('admin.users.create') }}" class="px-6 py-2 rounded-sm bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                    + Thêm người dùng
                </a>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-left text-sm font-semibold text-slate-700">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Họ tên</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Vai trò</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4">Ngày đăng ký</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $user->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-slate-900">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @foreach ($user->roles as $role)
                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                            {{ $role->name === 'admin' ? 'bg-rose-100 text-rose-700' : '' }}
                                            {{ $role->name === 'editor' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $role->name === 'user' ? 'bg-blue-100 text-blue-700' : '' }}">
                                            {{ $role->display_name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                            {{ $user->is_active ? '✓ Hoạt động' : '✕ Khóa' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-sm font-semibold">
                                            Sửa
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 rounded bg-rose-100 text-rose-700 hover:bg-rose-200 text-sm font-semibold">
                                                    Xóa
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">Không có người dùng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        </div>
        </div>
    </div>

    @include('gold.partials.footer')

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
