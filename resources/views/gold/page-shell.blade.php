<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Giá Vàng Hôm Nay | giavanghn.com</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    <link rel="canonical" href="{{ url('/' . $path) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:site_name" content="Giá Vàng Hôm Nay - giavanghn.com">
    <meta property="og:title" content="{{ $title }} - Giá Vàng Hôm Nay">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url('/' . $path) }}">
    <meta property="og:image" content="{{ url('/images/og-gold-price.jpg') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }} - Giá Vàng Hôm Nay">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ url('/images/og-gold-price.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest" defer></script>

    @stack('head')
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    <script id="snapshot-data" type="application/json">@json($snapshot)</script>

    {{-- HEADER --}}
    @include('gold.partials.header')

    {{-- MAIN --}}
    <main class="container-site px-5 py-6">
        {{-- Breadcrumb --}}
        <nav class="mb-4 flex flex-wrap items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="text-blue-600 no-underline hover:underline">
                <i data-lucide="home" class="inline h-4 w-4"></i> Trang chủ
            </a>
            @foreach ($breadcrumbs as $item)
                <span>/</span>
                @if ($loop->last)
                    <span class="font-semibold text-slate-800">{{ $item['title'] }}</span>
                @else
                    <a href="/{{ $item['path'] }}" class="text-blue-600 no-underline hover:underline">{{ $item['title'] }}</a>
                @endif
            @endforeach
        </nav>

        {{-- Page Header --}}
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#001061]/60">@yield('page-label', 'Chuyên mục')</p>
            <h1 class="mt-1 text-2xl font-bold text-[#001061] md:text-3xl">{{ $title }}</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $description }}</p>
        </div>

        {{-- Two-column layout --}}
        <section class="grid gap-5 lg:grid-cols-[2fr_1fr]">
            <div class="grid gap-5">@yield('page-content')</div>
            <aside class="grid gap-5 content-start">
                @yield('page-sidebar')
                @if (!empty($children))
                <div class="rounded-sm border border-[#bcbcbc] bg-white p-4">
                    <h3 class="text-lg font-bold text-slate-900">Chuyên mục con</h3>
                    <div class="mt-3 grid gap-2">
                        @foreach ($children as $child)
                        <a href="/{{ $child['path'] }}"
                           class="flex items-center gap-2 rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm font-medium text-blue-700 no-underline transition hover:bg-blue-50">
                            <i data-lucide="chevron-right" class="h-4 w-4 text-slate-400"></i>
                            {{ $child['title'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </section>
    </main>

    {{-- FOOTER --}}
    @include('gold.partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>