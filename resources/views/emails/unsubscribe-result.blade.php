<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hủy đăng ký - Giá Vàng Hôm Nay</title>
    @vite(['resources/css/app.css'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md px-6">
        @if ($success)
            <div class="mb-4 text-5xl">✓</div>
            <h1 class="text-xl font-bold text-[#001061] mb-2">Hủy đăng ký thành công</h1>
        @else
            <div class="mb-4 text-5xl">✗</div>
            <h1 class="text-xl font-bold text-rose-700 mb-2">Không thể hủy đăng ký</h1>
        @endif
        <p class="text-slate-600 mb-6">{{ $message }}</p>
        <a href="{{ route('home') }}" class="inline-block px-6 py-2.5 rounded-sm bg-[#001061] text-white text-sm font-semibold no-underline hover:bg-[#001061]/90">Về trang chủ</a>
    </div>
</body>
</html>
