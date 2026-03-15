<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thêm người dùng - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-6 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6 flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">← Quay lại</a>
                <h1 class="text-2xl font-bold text-slate-800">Thêm người dùng</h1>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-sm bg-rose-50 border border-rose-200">
                    @foreach ($errors->all() as $error)
                        <p class="text-rose-700">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-sm border border-slate-200 p-6 space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Họ tên <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Mật khẩu <span class="text-rose-500">*</span></label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Xác nhận mật khẩu <span class="text-rose-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2 rounded-sm border border-slate-300 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Vai trò <span class="text-rose-500">*</span></label>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($roles as $role)
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-700">{{ $role->display_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-semibold text-slate-700">Kích hoạt tài khoản</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-6 py-2 rounded-sm bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                        Tạo người dùng
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2 rounded-sm border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
