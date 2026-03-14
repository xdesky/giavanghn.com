<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản trị - Giá Vàng Hôm Nay</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-3 sm:px-6 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto">
        <!-- Navigation -->
        <nav class="mb-8 flex flex-wrap gap-3 sm:gap-4">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-sm bg-blue-600 text-white font-semibold">Dashboard</a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Quản lý người dùng</a>
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Quản lý bài viết</a>
            <a href="{{ route('admin.news.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Quản lý tin tức</a>
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Xem trang chủ</a>
        </nav>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-sm border border-[#bcbcbc] p-6 shadow-lg">
                <h3 class="text-sm font-medium text-slate-500 mb-2">Tổng người dùng</h3>
                <p class="text-xl sm:text-3xl font-bold text-blue-900">{{ \App\Models\User::count() }}</p>
            </div>
            <div class="bg-white rounded-sm border border-emerald-100 p-6 shadow-lg">
                <h3 class="text-sm font-medium text-slate-500 mb-2">Người dùng hoạt động</h3>
                <p class="text-xl sm:text-3xl font-bold text-emerald-900">{{ \App\Models\User::where('is_active', true)->count() }}</p>
            </div>
            <div class="bg-white rounded-sm border border-amber-100 p-6 shadow-lg">
                <h3 class="text-sm font-medium text-slate-500 mb-2">Bài viết đã xuất bản</h3>
                <p class="text-xl sm:text-3xl font-bold text-amber-900">{{ \App\Models\AnalysisArticle::whereNotNull('published_at')->count() }}</p>
            </div>
            <div class="bg-white rounded-sm border border-purple-100 p-6 shadow-lg">
                <h3 class="text-sm font-medium text-slate-500 mb-2">Đăng ký hôm nay</h3>
                <p class="text-xl sm:text-3xl font-bold text-purple-900">{{ \App\Models\User::whereDate('created_at', today())->count() }}</p>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-sm border border-slate-200 p-6 mb-8">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Người dùng mới nhất</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-slate-500 border-b">
                            <th class="pb-2">Họ tên</th>
                            <th class="pb-2">Email</th>
                            <th class="pb-2">Vai trò</th>
                            <th class="pb-2">Ngày đăng ký</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach (\App\Models\User::with('roles')->latest()->limit(5)->get() as $user)
                            <tr class="border-b">
                                <td class="py-3 font-medium">{{ $user->name }}</td>
                                <td class="py-3">{{ $user->email }}</td>
                                <td class="py-3">
                                    @foreach ($user->roles as $role)
                                        <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs">{{ $role->display_name }}</span>
                                    @endforeach
                                </td>
                                <td class="py-3 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Articles -->
        <div class="bg-white rounded-sm border border-slate-200 p-6 shadow-lg">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Bài viết mới nhất</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-slate-500 border-b">
                            <th class="pb-2">Tiêu đề</th>
                            <th class="pb-2">Loại</th>
                            <th class="pb-2">Ngày phân tích</th>
                            <th class="pb-2">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach (\App\Models\AnalysisArticle::latest('analysis_date')->limit(5)->get() as $article)
                            <tr class="border-b">
                                <td class="py-3 font-medium">{{ Str::limit($article->title, 60) }}</td>
                                <td class="py-3">
                                    <span class="inline-block px-2 py-1 rounded {{ $article->trigger_type === 'daily' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }} text-xs">
                                        {{ $article->trigger_type === 'daily' ? 'Hàng ngày' : 'Biến động' }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-500">{{ \Carbon\Carbon::parse($article->analysis_date)->format('d/m/Y') }}</td>
                                <td class="py-3">
                                    @if ($article->published_at)
                                        <span class="text-emerald-600">✓ Đã xuất bản</span>
                                    @else
                                        <span class="text-slate-400">Chưa xuất bản</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

    @include('gold.partials.footer')</body>
</html>
