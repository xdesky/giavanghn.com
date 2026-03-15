<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Giá Vàng Hôm Nay | giavanghn.com</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="/images/favicon.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen flex flex-col">

    {{-- Common Header --}}
    @include('gold.partials.header', ['menuTree' => config('gold_sitemap', []), 'currentPath' => ''])

    {{-- Content --}}
    <main class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="text-center max-w-lg">
            <div class="mb-6 text-[#001061]">
                @yield('icon')
            </div>
            <p class="text-[40px] md:text-[64px] font-extrabold text-[#001061] leading-none mb-2">@yield('code')</p>
            <h1 class="text-2xl font-bold text-[#001061] mb-3">@yield('title')</h1>
            <p class="text-slate-500 text-base leading-relaxed mb-8">@yield('message')</p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="/"
                   class="inline-flex items-center gap-2 rounded-lg bg-[#001061] px-6 py-3 text-sm font-semibold text-white no-underline transition hover:bg-[#0a1d7a]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-4 0a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-2z"/></svg>
                    Trang chủ
                </a>
                <button onclick="history.back()"
                   class="inline-flex items-center gap-2 rounded-lg border border-[#001061] bg-white px-6 py-3 text-sm font-semibold text-[#001061] no-underline transition hover:bg-slate-50 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                    Quay lại
                </button>
            </div>
        </div>
    </main>

    {{-- Common Footer --}}
    @include('gold.partials.footer', ['menuTree' => config('gold_sitemap', [])])
</body>
</html>
