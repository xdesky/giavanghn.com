<header class="sticky top-0 z-20 bg-[#001061]">
    <div class="mx-auto flex min-h-16 w-full items-center justify-between gap-3 px-5">
        <a href="{{ route('home') }}" class="flex items-center no-underline text-white">
            <img src="/images/logo.svg" alt="giavang.hn" class="h-10">
        </a>
        @include('gold.partials.nav-menu', ['menuTree' => $menuTree ?? config('gold_sitemap', []), 'currentPath' => $currentPath ?? ''])
    </div>
</header>
