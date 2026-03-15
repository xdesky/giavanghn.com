<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Push thông báo - Admin</title>
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
            <a href="{{ route('admin.subscribers.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Subscriber</a>
            <a href="{{ route('admin.subscribers.push') }}" class="px-4 py-2 rounded-sm bg-blue-600 text-white font-semibold">Push thông báo</a>
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Bài viết</a>
            <a href="{{ route('admin.news.index') }}" class="px-4 py-2 rounded-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Tin tức</a>
        </nav>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-sm bg-emerald-50 border border-emerald-200">
                <p class="text-emerald-700 font-semibold m-0">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-sm bg-rose-50 border border-rose-200">
                <p class="text-rose-700 font-semibold m-0">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Push Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-sm border border-slate-200 p-5 sm:p-6">
                    <h2 class="text-lg font-bold text-[#001061] mb-1 mt-0">Gửi thông báo cho Subscriber</h2>
                    <p class="text-sm text-slate-500 mb-5 mt-0">Hiện có <strong class="text-emerald-700">{{ $totalActive }}</strong> subscriber đang hoạt động</p>

                    <form action="{{ route('admin.subscribers.pushSend') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tiêu đề email <span class="text-rose-500">*</span></label>
                            <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full px-4 py-2.5 rounded-sm border border-slate-300 text-sm focus:border-blue-500 focus:outline-none" placeholder="Ví dụ: Cập nhật giá vàng hôm nay...">
                            @error('subject') <p class="text-xs text-rose-500 mt-1 m-0">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nội dung email <span class="text-rose-500">*</span></label>
                            <textarea name="content" required rows="10" class="w-full px-4 py-2.5 rounded-sm border border-slate-300 text-sm focus:border-blue-500 focus:outline-none font-mono" placeholder="Hỗ trợ HTML...">{{ old('content') }}</textarea>
                            @error('content') <p class="text-xs text-rose-500 mt-1 m-0">{{ $message }}</p> @enderror
                            <p class="text-xs text-slate-400 mt-1 m-0">Bạn có thể sử dụng HTML để định dạng nội dung.</p>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Lọc theo thị trường <span class="text-xs text-slate-400 font-normal">(để trống = gửi tất cả)</span></label>
                            <div class="flex flex-wrap gap-3">
                                @foreach (['sjc' => 'SJC', 'doji' => 'DOJI', 'pnj' => 'PNJ', 'phuquy' => 'Phú Quý', 'btmc' => 'BTMC', 'mihong' => 'Mi Hồng', 'xau-usd' => 'XAU/USD', 'xag-usd' => 'XAG/USD'] as $key => $label)
                                    <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                        <input type="checkbox" name="markets[]" value="{{ $key }}" {{ in_array($key, old('markets', [])) ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            @error('markets') <p class="text-xs text-rose-500 mt-1 m-0">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="cursor-pointer rounded-sm bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700" onclick="return confirm('Xác nhận gửi thông báo cho subscriber?')">
                            Gửi thông báo
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info panel --}}
            <div class="lg:col-span-2">
                <div class="bg-slate-50 rounded-sm border border-slate-200 p-5">
                    <h3 class="text-sm font-bold text-slate-700 mt-0 mb-3">Hướng dẫn</h3>
                    <ul class="text-xs text-slate-500 list-disc pl-4 m-0 space-y-1.5">
                        <li>Nội dung hỗ trợ HTML: &lt;b&gt;, &lt;a&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;...</li>
                        <li>Nếu không chọn thị trường nào, email được gửi cho <strong>tất cả</strong> subscriber đang hoạt động.</li>
                        <li>Subscriber sẽ nhận link hủy đăng ký trong mỗi email.</li>
                        <li>Mỗi lần gửi sẽ được ghi vào lịch sử bên dưới.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Notification History --}}
        <div class="mt-8">
            <h2 class="text-lg font-bold text-[#001061] mb-4 mt-0">Lịch sử gửi thông báo</h2>
            <div class="bg-white rounded-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-slate-600">
                                <th class="px-4 py-3 font-semibold">Tiêu đề</th>
                                <th class="px-4 py-3 font-semibold">Thị trường</th>
                                <th class="px-4 py-3 font-semibold text-center">Gửi OK</th>
                                <th class="px-4 py-3 font-semibold text-center">Lỗi</th>
                                <th class="px-4 py-3 font-semibold">Người gửi</th>
                                <th class="px-4 py-3 font-semibold">Thời gian</th>
                                <th class="px-4 py-3 font-semibold text-right">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-medium max-w-[250px] truncate">{{ $log->subject }}</td>
                                    <td class="px-4 py-3">
                                        @if ($log->markets)
                                            @foreach ($log->markets as $m)
                                                <span class="inline-block rounded bg-blue-50 px-1.5 py-0.5 text-xs font-semibold text-blue-700">{{ strtoupper($m) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-slate-400">Tất cả</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-emerald-700">{{ $log->total_sent }}</td>
                                    <td class="px-4 py-3 text-center font-semibold {{ $log->total_failed > 0 ? 'text-rose-600' : 'text-slate-400' }}">{{ $log->total_failed }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $log->sender?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $log->sent_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.subscribers.pushShow', $log) }}" class="text-blue-600 hover:underline text-xs font-semibold">Xem</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">Chưa có thông báo nào được gửi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($logs->hasPages())
                    <div class="border-t border-slate-200 px-4 py-3">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>

        </div>
    </div>

    @include('gold.partials.footer')
</body>
</html>
