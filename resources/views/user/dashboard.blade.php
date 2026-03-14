<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Giá Vàng Hôm Nay</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-6 py-8">
        <div class="mx-auto max-w-5xl">
        {{-- Welcome --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Xin chào, {{ Auth::user()->name }}!</h1>
            <p class="mt-1 text-slate-600">Quản lý tài khoản và theo dõi thị trường vàng</p>
        </div>

        {{-- Quick Links --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('home') }}" class="group rounded-sm border border-[#bcbcbc] bg-white p-6 no-underline transition hover:shadow-xl hover:border-blue-300">
                <div class="mb-3 grid h-12 w-12 place-items-center rounded-xl bg-blue-100 text-blue-600">
                    <i data-lucide="bar-chart-3" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-700">Giá vàng hôm nay</h3>
                <p class="mt-1 text-sm text-slate-500">Xem bảng giá vàng mới nhất từ các nguồn</p>
            </a>

            <a href="{{ route('user.profile') }}" class="group rounded-sm border border-[#bcbcbc] bg-white p-6 no-underline transition hover:shadow-xl hover:border-blue-300">
                <div class="mb-3 grid h-12 w-12 place-items-center rounded-xl bg-green-100 text-green-600">
                    <i data-lucide="user-cog" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-700">Hồ sơ cá nhân</h3>
                <p class="mt-1 text-sm text-slate-500">Cập nhật thông tin tài khoản</p>
            </a>

            <a href="{{ route('user.subscription') }}" class="group rounded-sm border border-[#bcbcbc] bg-white p-6 no-underline transition hover:shadow-xl hover:border-blue-300">
                <div class="mb-3 grid h-12 w-12 place-items-center rounded-xl bg-amber-100 text-amber-600">
                    <i data-lucide="bell-ring" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-700">Thông báo giá</h3>
                <p class="mt-1 text-sm text-slate-500">Cài đặt cảnh báo biến động giá vàng</p>
            </a>
        </div>

        {{-- Account Info --}}
        <div class="mt-6 rounded-sm border border-[#bcbcbc] bg-white p-6 shadow-lg">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Thông tin tài khoản</h2>
            <dl class="grid gap-3 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Họ tên</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Email</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ Auth::user()->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Ngày tham gia</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ Auth::user()->created_at->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Vai trò</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900">
                        @if(Auth::user()->hasRole('admin'))
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">Admin</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Thành viên</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
        </div>
    </div>

    @include('gold.partials.footer')</body>
</html>
