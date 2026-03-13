<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập - Giá Vàng Hôm Nay</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest" defer></script>
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen flex flex-col">
    @include('gold.partials.header')

    <main class="container-site flex-1 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Giá Vàng Hôm Nay</h1>
            <p class="text-slate-600">Đăng nhập để nhận thông báo giá vàng</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-sm shadow-xl border border-[#bcbcbc] p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Đăng nhập</h2>

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200">
                    <p class="text-sm text-rose-700 font-semibold">{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-4 py-3 rounded-sm border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Mật khẩu</label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-4 py-3 rounded-sm border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-600">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-semibold">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg">
                    Đăng nhập
                </button>
            </form>

            <!-- Social Login -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-slate-500">Hoặc đăng nhập bằng</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center px-4 py-3 rounded-sm border border-slate-300 hover:bg-slate-50 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    </a>
                    <a href="{{ route('social.redirect', 'facebook') }}" class="flex items-center justify-center px-4 py-3 rounded-sm border border-slate-300 hover:bg-slate-50 transition">
                        <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="{{ route('social.redirect', 'apple') }}" class="flex items-center justify-center px-4 py-3 rounded-sm border border-slate-300 hover:bg-slate-50 transition">
                        <svg class="w-5 h-5" fill="#000000" viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Register Link -->
            <p class="mt-6 text-center text-sm text-slate-600">
                Chưa có tài khoản? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Đăng ký ngay</a>
            </p>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-slate-600 hover:text-slate-900">← Quay lại trang chủ</a>
        </div>
    </div>
    </main>

    @include('gold.partials.footer')

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
