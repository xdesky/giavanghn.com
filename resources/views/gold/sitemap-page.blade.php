<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page['title'] }} - GoldPrice</title>
    <meta name="description" content="{{ $page['description'] }}">
    <link rel="canonical" href="{{ url($path ?? '/') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:site_name" content="Giá Vàng Hôm Nay">
    <meta property="og:title" content="{{ $page['title'] }} - GoldPrice">
    <meta property="og:description" content="{{ $page['description'] }}">
    <meta property="og:url" content="{{ url($path ?? '/') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $page['title'] }} - GoldPrice">
    <meta name="twitter:description" content="{{ $page['description'] }}">

    <link rel="preload" href="/images/logo.svg" as="image" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@0.477.0" defer></script>

    {{-- BreadcrumbList Schema --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Trang chủ",
                "item": "{{ url('/') }}"
            }@if(!empty($breadcrumbs))@foreach($breadcrumbs as $i => $item),
            {
                "@@type": "ListItem",
                "position": {{ $i + 2 }},
                "name": "{{ $item['title'] }}",
                "item": "{{ url($item['path']) }}"
            }@endforeach @endif
        ]
    }
    </script>
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <main class="container-site px-6 py-6">
        <section class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 shadow-lg">
            <p class="text-xs uppercase tracking-wide text-slate-500">Sitemap page</p>
            <h1 class="mt-2 text-3xl font-bold text-[#001061]">{{ $page['title'] }}</h1>
            <p class="mt-3 text-sm text-slate-600">{{ $page['description'] }}</p>

            <nav class="mt-4 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Trang chủ</a>
                @foreach ($breadcrumbs as $item)
                    <span>/</span>
                    @if ($loop->last)
                        <span class="font-semibold text-slate-800">{{ $item['title'] }}</span>
                    @else
                        <a href="/{{ $item['path'] }}" class="text-blue-600 hover:underline">{{ $item['title'] }}</a>
                    @endif
                @endforeach
            </nav>
        </section>

        @if (!empty($children))
            <section class="mt-4 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 shadow-lg">
                <h2 class="text-lg font-bold text-[#001061]">Trang con</h2>
                <div class="mt-3 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($children as $child)
                        <a href="/{{ $child['path'] }}" class="rounded-sm border border-[#bcbcbc] bg-white p-3 text-sm font-semibold text-blue-700 hover:bg-blue-50 no-underline">
                            {{ $child['title'] }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-4 rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 shadow-lg">
            <h2 class="text-lg font-bold text-[#001061]">Danh mục chính</h2>
            <div class="mt-3 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($topLevel as $top)
                    <a href="/{{ $top['path'] }}" class="rounded-sm border border-slate-200 bg-slate-50 p-3 text-sm font-medium text-slate-700 hover:border-blue-200 hover:bg-blue-50 no-underline">
                        {{ $top['title'] }}
                    </a>
                @endforeach
            </div>
        </section>
    </main>

    @include('gold.partials.footer')

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
