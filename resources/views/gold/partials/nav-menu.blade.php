{{-- Desktop Navigation --}}
<nav class="hidden xl:flex items-center gap-0.5" id="desktopNav">
    @foreach ($menuTree as $slug => $item)
        @php
            $hasChildren = !empty($item['children']);
            $fullPath = $slug;
            $isActive = isset($currentPath) && (str_starts_with($currentPath, $slug . '/') || $currentPath === $slug);
        @endphp

        @if ($hasChildren)
            <div class="group relative">
                <a href="/{{ $fullPath }}"
                   class="flex items-center gap-1 px-3 py-5 text-sm font-medium no-underline transition {{ $isActive ? 'text-[#ffc300] border-b-2 border-[#ffc300]' : 'text-white hover:text-[#ffc300]' }}">
                    {{ $item['title'] }}
                    <i data-lucide="chevron-down" class="h-3 w-3 opacity-60"></i>
                </a>
                <div class="invisible absolute left-0 top-full z-50 min-w-64 rounded-sm border border-[#bcbcbc] bg-white py-1 shadow-xl opacity-0 transition-all duration-150 group-hover:visible group-hover:opacity-100">
                    @foreach ($item['children'] as $childSlug => $child)
                        @php $childPath = $fullPath . '/' . $childSlug; @endphp
                        <a href="/{{ $childPath }}"
                           class="block px-4 py-2 text-sm text-slate-700 no-underline transition hover:bg-blue-50 hover:text-blue-700 {{ ($currentPath ?? '') === $childPath ? 'bg-blue-50 font-semibold text-blue-700' : '' }}">{{ $child['title'] }}</a>
                    @endforeach
                </div>
            </div>
        @else
            @if (!in_array($slug, ['gioi-thieu', 'lien-he', 'chinh-sach-bao-mat', 'dieu-khoan-su-dung']))
                <a href="/{{ $fullPath }}"
                   class="px-3 py-5 text-sm font-medium no-underline transition {{ $isActive ? 'text-[#ffc300] border-b-2 border-[#ffc300]' : 'text-white hover:text-[#ffc300]' }}">{{ $item['title'] }}</a>
            @endif
        @endif
    @endforeach

    {{-- Auth Buttons --}}
    <div class="ml-auto flex items-center gap-2">
        @auth
            <div class="group relative">
                <button class="flex items-center gap-1.5 px-3 py-3 text-sm font-medium text-white no-underline transition hover:text-[#ffc300]">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-white/30">
                    @else
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#ffc300] text-sm font-bold text-slate-900 ring-2 ring-white/30">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                    @endif
                    <i data-lucide="chevron-down" class="h-3 w-3 opacity-60"></i>
                </button>
                <div class="invisible absolute right-0 top-full z-50 min-w-52 rounded-sm border border-[#bcbcbc] bg-white py-1 shadow-xl opacity-0 transition-all duration-150 group-hover:visible group-hover:opacity-100">
                    <div class="border-b border-slate-200 px-4 py-2">
                        <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 no-underline transition hover:bg-blue-50 hover:text-blue-700">
                        <i data-lucide="layout-dashboard" class="mr-2 inline h-4 w-4"></i>Dashboard
                    </a>
                    <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-sm text-slate-700 no-underline transition hover:bg-blue-50 hover:text-blue-700">
                        <i data-lucide="settings" class="mr-2 inline h-4 w-4"></i>Hồ sơ
                    </a>
                    @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 no-underline transition hover:bg-blue-50 hover:text-blue-700">
                        <i data-lucide="shield" class="mr-2 inline h-4 w-4"></i>Quản trị
                    </a>
                    @endif
                    <hr class="my-1 border-slate-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 transition hover:bg-red-50">
                            <i data-lucide="log-out" class="mr-2 inline h-4 w-4"></i>Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="rounded-sm px-3 py-2 text-sm font-medium text-white no-underline transition hover:text-[#ffc300]">
                <i data-lucide="log-in" class="mr-1 inline h-4 w-4"></i>Đăng nhập
            </a>
            <a href="{{ route('register') }}" class="rounded-sm bg-[#ffc300] px-3 py-2 text-sm font-semibold text-slate-900 no-underline transition hover:bg-yellow-300">
                Đăng ký
            </a>
        @endauth
    </div>
</nav>

{{-- Mobile Hamburger Button --}}
<button id="mobileMenuBtn" class="xl:hidden grid h-10 w-10 place-items-center rounded-sm text-white transition hover:bg-white/10" aria-label="Menu">
    <i data-lucide="menu" class="h-6 w-6"></i>
</button>

{{-- Mobile Drawer --}}
<div id="mobileDrawer" class="fixed inset-0 z-50 hidden">
    <div id="mobileOverlay" class="absolute inset-0 bg-slate-900/60"></div>
    <aside class="absolute right-0 top-0 h-full w-80 max-w-[85vw] overflow-y-auto bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
            <span class="text-lg font-bold text-slate-900">Menu</span>
            <button id="mobileCloseBtn" class="grid h-9 w-9 place-items-center rounded-sm transition hover:bg-slate-100" aria-label="Đóng">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <nav class="grid gap-0.5 p-3">
            @foreach ($menuTree as $slug => $item)
                @php
                    $hasChildren = !empty($item['children']);
                    $fullPath = $slug;
                    $isActive = isset($currentPath) && (str_starts_with($currentPath, $slug . '/') || $currentPath === $slug);
                @endphp

                @if ($hasChildren)
                    <div class="mobile-group">
                        <button class="mobile-toggle flex w-full items-center justify-between px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-blue-50 {{ $isActive ? 'text-blue-700' : 'text-slate-900' }}">
                            {{ $item['title'] }}
                            <i data-lucide="chevron-down" class="mobile-chevron h-4 w-4 text-slate-400 transition-transform"></i>
                        </button>
                        <div class="mobile-submenu hidden pl-3">
                            <a href="/{{ $fullPath }}" class="block px-3 py-5 text-sm text-slate-600 no-underline transition hover:bg-blue-50 hover:text-blue-700">Tất cả {{ $item['title'] }}</a>
                            @foreach ($item['children'] as $childSlug => $child)
                                @php $childPath = $fullPath . '/' . $childSlug; @endphp
                                <a href="/{{ $childPath }}"
                                   class="block px-3 py-5 text-sm no-underline transition hover:bg-blue-50 hover:text-blue-700 {{ ($currentPath ?? '') === $childPath ? 'font-semibold text-blue-700' : 'text-slate-600' }}">{{ $child['title'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="/{{ $fullPath }}"
                       class="px-3 py-2.5 text-sm font-semibold no-underline transition hover:bg-blue-50 {{ $isActive ? 'text-blue-700' : 'text-slate-900' }}">{{ $item['title'] }}</a>
                @endif
            @endforeach

            {{-- Mobile Auth --}}
            <hr class="my-2 border-slate-200">
            @auth
                <a href="{{ route('user.dashboard') }}" class="rounded-sm px-3 py-2.5 text-sm font-semibold text-slate-900 no-underline transition hover:bg-blue-50">
                    <i data-lucide="layout-dashboard" class="mr-2 inline h-4 w-4"></i>Dashboard
                </a>
                <a href="{{ route('user.profile') }}" class="rounded-sm px-3 py-2.5 text-sm font-semibold text-slate-900 no-underline transition hover:bg-blue-50">
                    <i data-lucide="settings" class="mr-2 inline h-4 w-4"></i>Hồ sơ
                </a>
                @if(Auth::user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="rounded-sm px-3 py-2.5 text-sm font-semibold text-slate-900 no-underline transition hover:bg-blue-50">
                    <i data-lucide="shield" class="mr-2 inline h-4 w-4"></i>Quản trị
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center rounded-sm px-3 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                        <i data-lucide="log-out" class="mr-2 inline h-4 w-4"></i>Đăng xuất
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-sm px-3 py-2.5 text-sm font-semibold text-blue-700 no-underline transition hover:bg-blue-50">
                    <i data-lucide="log-in" class="mr-2 inline h-4 w-4"></i>Đăng nhập
                </a>
                <a href="{{ route('register') }}" class="rounded-sm bg-[#ffc300] px-3 py-2.5 text-center text-sm font-semibold text-slate-900 no-underline transition hover:bg-yellow-300">
                    Đăng ký
                </a>
            @endauth
        </nav>
    </aside>
</div>
