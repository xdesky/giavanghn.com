<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hồ sơ cá nhân - Giá Vàng Hôm Nay</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest" defer></script>
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-6 py-8">
        <div class="mx-auto max-w-3xl">
        <h1 class="text-3xl font-bold text-slate-900 mb-6">Hồ sơ cá nhân</h1>

        @if (session('success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Profile Form --}}
        <div class="rounded-sm border border-[#bcbcbc] bg-white p-6 shadow-lg">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Thông tin cá nhân</h2>

            @if ($errors->profile->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4">
                    @foreach ($errors->profile->all() as $error)
                        <p class="text-sm text-rose-700">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Họ tên</label>
                        <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required
                               class="w-full rounded-sm border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                               class="w-full rounded-sm border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Số điện thoại</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                               class="w-full rounded-sm border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Ngày tham gia</label>
                        <p class="px-4 py-2.5 text-sm text-slate-500">{{ Auth::user()->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tài khoản liên kết</label>
                    <div class="px-1 py-1">
                        @php $socials = Auth::user()->socialAccounts ?? collect(); @endphp
                        @if($socials->count() > 0)
                            @foreach($socials as $social)
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700 mr-1">{{ ucfirst($social->provider) }}</span>
                            @endforeach
                        @else
                            <span class="text-sm text-slate-400">Chưa liên kết</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        <i data-lucide="save" class="inline h-4 w-4 mr-1"></i>Lưu thông tin
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="mt-6 rounded-sm border border-[#bcbcbc] bg-white p-6 shadow-lg">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Đổi mật khẩu</h2>

            @if ($errors->password->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4">
                    @foreach ($errors->password->all() as $error)
                        <p class="text-sm text-rose-700">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('password_success'))
                <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4">
                    <p class="text-sm text-green-700 font-semibold">{{ session('password_success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('user.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="grid gap-4">
                    @if(Auth::user()->password)
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1">Mật khẩu hiện tại</label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full rounded-sm border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    </div>
                    @endif
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Mật khẩu mới</label>
                            <input type="password" id="password" name="password" required
                                   class="w-full rounded-sm border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Xác nhận mật khẩu</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="w-full rounded-sm border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="rounded-xl bg-slate-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <i data-lucide="lock" class="inline h-4 w-4 mr-1"></i>Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center text-sm text-blue-600 hover:underline">
                <i data-lucide="arrow-left" class="inline h-4 w-4 mr-1"></i>Quay lại Dashboard
            </a>
        </div>
        </div>
    </div>

    @include('gold.partials.footer')

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
