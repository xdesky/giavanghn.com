<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thông báo giá - Giá Vàng Hôm Nay</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-6 py-8">
        <div class="mx-auto max-w-3xl">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Thông báo giá vàng</h1>
        <p class="text-slate-600 mb-6">Cài đặt cảnh báo khi giá vàng biến động</p>

        @if (session('success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="rounded-sm border border-[#bcbcbc] bg-white p-6 shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-amber-100 text-amber-600">
                    <i data-lucide="bell-ring" class="h-5 w-5"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900">Cảnh báo biến động giá</h2>
            </div>
            <p class="text-sm text-slate-500 mb-4">Tính năng đang được phát triển. Bạn sẽ sớm có thể cài đặt ngưỡng cảnh báo khi giá vàng tăng hoặc giảm theo mức mong muốn.</p>
            <div class="rounded-xl bg-blue-50 border border-blue-200 p-4">
                <p class="text-sm text-blue-700"><i data-lucide="info" class="inline h-4 w-4 mr-1"></i>Hiện tại bạn có thể theo dõi giá vàng trực tiếp trên <a href="{{ route('home') }}" class="font-semibold underline">trang chủ</a>.</p>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center text-sm text-blue-600 hover:underline">
                <i data-lucide="arrow-left" class="inline h-4 w-4 mr-1"></i>Quay lại Dashboard
            </a>
        </div>
        </div>
    </div>

    @include('gold.partials.footer')</body>
</html>
