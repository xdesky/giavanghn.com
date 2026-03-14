<?php
/**
 * generate_pages_v2.php
 * Run from project root:  php scripts/generate_pages_v2.php
 *
 * Overwrites:
 *   resources/views/gold/page-shell.blade.php   (layout)
 *   resources/views/gold/sections/*.blade.php    (section partials)
 *   resources/views/pages/**\/*.blade.php         (55 page files)
 */

$base = __DIR__ . '/../resources/views';

function w(string $path, string $content): void {
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($path, $content);
}

// ============================================================
// 1) LAYOUT
// ============================================================
w("$base/gold/page-shell.blade.php", <<<'BLADE'
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title }} - GoldPrice</title>
    <meta name="description" content="{{ $description }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="gold-body">
    <header class="top-nav">
        <div class="top-nav-inner container-main">
            <a href="{{ route('home') }}" class="brand-link">
                <span class="brand-logo">G</span>
                <span><strong>GoldPrice</strong><small>Thong tin gia vang</small></span>
            </a>
            <nav class="nav-menu">
                <a href="{{ route('home') }}">Dashboard</a>
                @foreach ($topLevel as $item)
                    <a href="/{{ $item['path'] }}"
                       class="{{ $item['path'] === $rootPath ? 'is-active' : '' }}">{{ $item['title'] }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="container-main py-6">
        <section class="glass-card p-4 md:p-6">
            <p class="text-xs uppercase tracking-wide text-slate-500">@yield('page-label', 'Trang chuyen muc')</p>
            <h1 class="mt-2 text-2xl font-bold text-[#001061] md:text-3xl">{{ $title }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $description }}</p>
            <nav class="mt-4 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Trang chu</a>
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

        <section class="mt-4 grid gap-5 lg:grid-cols-[2fr_1fr]">
            <div class="grid gap-5">@yield('page-content')</div>
            <aside class="grid gap-5 content-start">
                @yield('page-sidebar')
                @if (!empty($children))
                <div class="glass-card p-4">
                    <h3 class="text-lg font-bold text-[#001061]">Chuyen muc con</h3>
                    <div class="mt-3 grid gap-2">
                        @foreach ($children as $child)
                        <a href="/{{ $child['path'] }}"
                           class="rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50">{{ $child['title'] }}</a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </section>
    </main>

    <footer class="footer-wrap">
        <div class="container-main footer-bottom">
            <span>&copy; 2026 GoldPrice. Moi quyen duoc bao luu.</span>
            <div>
                <a href="/gioi-thieu">Gioi thieu</a>
                <a href="/lien-he">Lien he</a>
                <a href="/chinh-sach-bao-mat">Bao mat</a>
                <a href="/dieu-khoan-su-dung">Dieu khoan</a>
            </div>
        </div>
    </footer>
</body>
</html>
BLADE);
echo "=> page-shell.blade.php\n";

// ============================================================
// 2) SECTION PARTIALS
// ============================================================
$sec = "$base/gold/sections";

// --- today-price ---
w("$sec/today-price.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center gap-3 mb-4">
        <span class="live-badge"><i></i> Truc tuyen</span>
        <span class="text-sm text-slate-500">Cap nhat lien tuc</span>
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="rounded-sm border border-amber-200 bg-linear-to-br from-amber-50 to-yellow-50 p-4">
            <p class="text-sm font-medium text-amber-800">Vàng SJC 1 Lượng</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">92,500,000 <small class="text-base font-normal text-amber-700">VND</small></p>
            <div class="mt-2 flex gap-4 text-sm"><span>Mua: <strong>91,500,000</strong></span><span>Bán: <strong>92,500,000</strong></span></div>
            <p class="mt-1 text-sm font-bold text-emerald-600">▲ 500,000 (0.54%)</p>
        </div>
        <div class="rounded-sm border border-blue-200 bg-linear-to-br from-blue-50 to-indigo-50 p-4">
            <p class="text-sm font-medium text-blue-800">XAU/USD</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">2,918.45 <small class="text-base font-normal text-blue-700">USD/oz</small></p>
            <div class="mt-2 flex gap-4 text-sm"><span>Mở cửa: <strong>2,906.15</strong></span><span>Cao: <strong>2,925.30</strong></span></div>
            <p class="mt-1 text-sm font-bold text-emerald-600">▲ 12.30 (0.42%)</p>
        </div>
    </div>
    <div class="mt-4 table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]"><tr><th class="p-3 text-left font-semibold">Thuong hieu</th><th class="p-3 text-right font-semibold">Mua vao</th><th class="p-3 text-right font-semibold">Ban ra</th><th class="p-3 text-right font-semibold">Thay doi</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3 font-medium">SJC 1L</td><td class="p-3 text-right">91,500,000</td><td class="p-3 text-right">92,500,000</td><td class="p-3 text-right font-semibold text-emerald-600">+500,000</td></tr>
                <tr><td class="p-3 font-medium">DOJI 1L</td><td class="p-3 text-right">91,400,000</td><td class="p-3 text-right">92,400,000</td><td class="p-3 text-right font-semibold text-emerald-600">+400,000</td></tr>
                <tr><td class="p-3 font-medium">PNJ 1L</td><td class="p-3 text-right">91,300,000</td><td class="p-3 text-right">92,300,000</td><td class="p-3 text-right font-semibold text-emerald-600">+300,000</td></tr>
                <tr><td class="p-3 font-medium">Phu Quy</td><td class="p-3 text-right">91,200,000</td><td class="p-3 text-right">92,200,000</td><td class="p-3 text-right font-semibold text-emerald-600">+200,000</td></tr>
                <tr><td class="p-3 font-medium">Mi Hong</td><td class="p-3 text-right">91,100,000</td><td class="p-3 text-right">92,100,000</td><td class="p-3 text-right font-semibold text-emerald-600">+100,000</td></tr>
                <tr><td class="p-3 font-medium">Bao Tin Minh Chau</td><td class="p-3 text-right">91,350,000</td><td class="p-3 text-right">92,350,000</td><td class="p-3 text-right font-semibold text-emerald-600">+350,000</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <h3 class="font-bold text-blue-900">Nhan dinh thi truong</h3>
        <p class="mt-2 text-sm leading-relaxed text-blue-800">Gia vang trong nuoc tang nhe theo da phuc hoi cua thi truong quoc te. Dong USD suy yeu, lo ngai lam phat va cang thang dia chinh tri la cac yeu to ho tro. Du bao dao dong 91.0 – 93.0 trieu/luong trong phien hom nay.</p>
    </div>
</div>
BLADE);

// --- world-price ---
w("$sec/world-price.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center gap-3 mb-4">
        <span class="live-badge"><i></i> Truc tuyen</span>
        <span class="text-sm text-slate-500">Du lieu tu thi truong quoc te</span>
    </div>
    <div class="rounded-sm border border-blue-200 bg-linear-to-br from-blue-50 to-indigo-50 p-5">
        <p class="text-sm font-medium text-blue-800">XAU/USD – Gia vang the gioi</p>
        <p class="mt-1 text-3xl font-bold text-blue-900">2,918.45 <small class="text-lg font-normal text-blue-700">USD/oz</small></p>
        <p class="mt-2 text-sm font-bold text-emerald-600">▲ 12.30 (0.42%) so voi phien truoc</p>
    </div>
    <div class="mt-4 grid gap-5 sm:grid-cols-3">
        <div class="rounded-sm border border-slate-200 p-3 text-center">
            <p class="text-xs text-slate-500">London Fix PM</p>
            <p class="mt-1 text-lg font-bold">2,915.00</p>
            <p class="text-xs text-emerald-600 font-semibold">+10.50</p>
        </div>
        <div class="rounded-sm border border-slate-200 p-3 text-center">
            <p class="text-xs text-slate-500">Shanghai Au99.99</p>
            <p class="mt-1 text-lg font-bold">682.50 CNY/g</p>
            <p class="text-xs text-emerald-600 font-semibold">+3.20</p>
        </div>
        <div class="rounded-sm border border-slate-200 p-3 text-center">
            <p class="text-xs text-slate-500">COMEX Futures</p>
            <p class="mt-1 text-lg font-bold">2,922.80</p>
            <p class="text-xs text-emerald-600 font-semibold">+14.10</p>
        </div>
    </div>
    <div class="mt-4 chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <div class="flex justify-between text-xs text-slate-500 mb-2"><span>24h</span><span>XAU/USD</span></div>
        <svg viewBox="0 0 600 120" class="w-full h-28"><polyline fill="none" stroke="#3b82f6" stroke-width="2" points="0,80 50,75 100,60 150,65 200,50 250,55 300,40 350,45 400,35 450,30 500,25 550,28 600,20"/><polyline fill="none" stroke="#3b82f6" stroke-width="0" points="0,80 50,75 100,60 150,65 200,50 250,55 300,40 350,45 400,35 450,30 500,25 550,28 600,20 600,120 0,120" fill="url(#blueGrad)" opacity="0.1"/></svg>
    </div>
    <div class="mt-4 grid gap-5 sm:grid-cols-2">
        <div class="rounded-sm border border-slate-200 p-3">
            <h4 class="font-semibold text-sm">Yeu to anh huong</h4>
            <ul class="mt-2 space-y-1 text-sm leading-relaxed text-slate-600">
                <li>• Chi so USD (DXY) giam 0.3%</li>
                <li>• Loi suat trai phieu My ha</li>
                <li>• Cang thang dia chinh tri leo thang</li>
                <li>• Nhu cau tru an toan tang</li>
            </ul>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h4 class="font-semibold text-sm">Phien giao dich</h4>
            <ul class="mt-2 space-y-1 text-sm leading-relaxed text-slate-600">
                <li>Mo cua: <strong>2,906.15</strong></li>
                <li>Cao nhat: <strong>2,925.30</strong></li>
                <li>Thap nhat: <strong>2,901.60</strong></li>
                <li>Khoi luong: <strong>185,420 lots</strong></li>
            </ul>
        </div>
    </div>
</div>
BLADE);

// --- price-table (all brands) ---
w("$sec/price-table.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">Tong hop bang gia vang hom nay</h2>
        <span class="chip">Cap nhat: 10:30</span>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr>
                    <th class="p-3 text-left font-semibold">Thuong hieu</th>
                    <th class="p-3 text-right font-semibold">Mua vao (VND/luong)</th>
                    <th class="p-3 text-right font-semibold">Ban ra (VND/luong)</th>
                    <th class="p-3 text-right font-semibold">Chenh lech</th>
                    <th class="p-3 text-right font-semibold">Thay doi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-sjc" class="text-blue-700 hover:underline">SJC</a></td><td class="p-3 text-right">91,500,000</td><td class="p-3 text-right">92,500,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+500,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-doji" class="text-blue-700 hover:underline">DOJI</a></td><td class="p-3 text-right">91,400,000</td><td class="p-3 text-right">92,400,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+400,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-pnj" class="text-blue-700 hover:underline">PNJ</a></td><td class="p-3 text-right">91,300,000</td><td class="p-3 text-right">92,300,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+300,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-phu-quy" class="text-blue-700 hover:underline">Phu Quy</a></td><td class="p-3 text-right">91,200,000</td><td class="p-3 text-right">92,200,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+200,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-mi-hong" class="text-blue-700 hover:underline">Mi Hong</a></td><td class="p-3 text-right">91,100,000</td><td class="p-3 text-right">92,100,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+100,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-bao-tin-minh-chau" class="text-blue-700 hover:underline">Bao Tin Minh Chau</a></td><td class="p-3 text-right">91,350,000</td><td class="p-3 text-right">92,350,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+350,000</td></tr>
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-xs text-slate-400">Gia mang tinh tham khao. Du lieu cap nhat tu cac thuong hieu chinh hang.</p>
</div>
BLADE);

// --- price-brand ---
w("$sec/price-brand.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">Bang gia {{ $brandName }} hom nay</h2>
        <span class="chip">Cap nhat: 10:30</span>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr><th class="p-3 text-left font-semibold">Loai vang</th><th class="p-3 text-right font-semibold">Mua vao</th><th class="p-3 text-right font-semibold">Ban ra</th><th class="p-3 text-right font-semibold">Thay doi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3 font-medium">Vang mieng 1 luong</td><td class="p-3 text-right">91,500,000</td><td class="p-3 text-right">92,500,000</td><td class="p-3 text-right font-semibold text-emerald-600">+500,000</td></tr>
                <tr><td class="p-3 font-medium">Vang mieng 5 chi</td><td class="p-3 text-right">91,300,000</td><td class="p-3 text-right">92,300,000</td><td class="p-3 text-right font-semibold text-emerald-600">+300,000</td></tr>
                <tr><td class="p-3 font-medium">Vang mieng 2 chi</td><td class="p-3 text-right">91,200,000</td><td class="p-3 text-right">92,200,000</td><td class="p-3 text-right font-semibold text-emerald-600">+200,000</td></tr>
                <tr><td class="p-3 font-medium">Vang mieng 1 chi</td><td class="p-3 text-right">91,100,000</td><td class="p-3 text-right">92,100,000</td><td class="p-3 text-right font-semibold text-emerald-600">+100,000</td></tr>
                <tr><td class="p-3 font-medium">Nhan tron 9999</td><td class="p-3 text-right">82,500,000</td><td class="p-3 text-right">83,600,000</td><td class="p-3 text-right font-semibold text-emerald-600">+400,000</td></tr>
                <tr><td class="p-3 font-medium">Vang 24K</td><td class="p-3 text-right">82,300,000</td><td class="p-3 text-right">83,400,000</td><td class="p-3 text-right font-semibold text-emerald-600">+350,000</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <p class="text-xs text-slate-500 mb-2">Bien dong gia {{ $brandName }} trong ngay</p>
        <svg viewBox="0 0 600 100" class="w-full h-24"><polyline fill="none" stroke="#f59e0b" stroke-width="2" points="0,60 60,55 120,50 180,45 240,48 300,42 360,38 420,35 480,33 540,30 600,28"/></svg>
    </div>
    <div class="mt-4 rounded-sm border border-amber-100 bg-amber-50 p-4">
        <h3 class="font-bold text-amber-900">Gioi thieu {{ $brandName }}</h3>
        <p class="mt-2 text-sm leading-relaxed text-amber-800">{{ $brandName }} la mot trong nhung thuong hieu vang uy tin hang dau tai Viet Nam, cung cap da dang san pham vang mieng, vang nhan va vang trang suc chat luong cao. Gia duoc cap nhat lien tuc trong ngay giao dich.</p>
    </div>
</div>
BLADE);

// --- chart ---
w("$sec/chart.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">Bieu do gia vang {{ $periodLabel }}</h2>
        <div class="flex gap-1">
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-hom-nay" class="chip {{ $period === 'today' ? 'positive' : '' }}">Hom nay</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-7-ngay" class="chip {{ $period === '7d' ? 'positive' : '' }}">7 ngay</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-30-ngay" class="chip {{ $period === '30d' ? 'positive' : '' }}">30 ngay</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-1-nam" class="chip {{ $period === '1y' ? 'positive' : '' }}">1 nam</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-10-nam" class="chip {{ $period === '10y' ? 'positive' : '' }}">10 nam</a>
        </div>
    </div>
    <div class="chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <svg viewBox="0 0 700 200" class="w-full h-52">
            <defs><linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3"/><stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/></linearGradient></defs>
            <polyline fill="url(#chartGrad)" stroke="none" points="0,140 70,130 140,120 210,125 280,100 350,110 420,90 490,80 560,70 630,65 700,50 700,200 0,200"/>
            <polyline fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linejoin="round" points="0,140 70,130 140,120 210,125 280,100 350,110 420,90 490,80 560,70 630,65 700,50"/>
        </svg>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-5 sm:grid-cols-4">
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Cao nhat</p><p class="mt-1 text-lg font-bold text-[#001061]">92,800,000</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thap nhat</p><p class="mt-1 text-lg font-bold text-[#001061]">90,200,000</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Trung binh</p><p class="mt-1 text-lg font-bold text-[#001061]">91,500,000</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thay doi</p><p class="mt-1 text-lg font-bold text-emerald-600">+2.8%</p></div>
    </div>
</div>
BLADE);

// --- comparison ---
w("$sec/comparison.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">So sanh {{ $leftName }} va {{ $rightName }}</h2>
    <div class="grid gap-5 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border-2 border-blue-200 bg-blue-50 p-4 text-center">
            <p class="text-sm font-medium text-blue-700">{{ $leftName }}</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">{{ $leftPrice }}</p>
            <p class="mt-1 text-sm font-bold text-emerald-600">{{ $leftChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-indigo-200 bg-indigo-50 p-4 text-center">
            <p class="text-sm font-medium text-indigo-700">{{ $rightName }}</p>
            <p class="mt-2 text-3xl font-bold text-indigo-900">{{ $rightPrice }}</p>
            <p class="mt-1 text-sm font-bold text-emerald-600">{{ $rightChange }}</p>
        </div>
    </div>
    <div class="rounded-sm border border-amber-200 bg-amber-50 p-4 mb-4">
        <p class="text-sm font-semibold text-amber-900">Chenh lech hien tai</p>
        <p class="mt-1 text-2xl font-bold text-amber-800">{{ $spread }}</p>
        <p class="mt-1 text-xs text-amber-700">{{ $spreadNote }}</p>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]"><tr><th class="p-3 text-left font-semibold">Chi tieu</th><th class="p-3 text-right font-semibold">{{ $leftName }}</th><th class="p-3 text-right font-semibold">{{ $rightName }}</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3">Gia hien tai</td><td class="p-3 text-right font-medium">{{ $leftPrice }}</td><td class="p-3 text-right font-medium">{{ $rightPrice }}</td></tr>
                <tr><td class="p-3">Tang/Giam 24h</td><td class="p-3 text-right">{{ $leftChange }}</td><td class="p-3 text-right">{{ $rightChange }}</td></tr>
                <tr><td class="p-3">Cao nhat 30 ngay</td><td class="p-3 text-right">{{ $leftHigh ?? 'N/A' }}</td><td class="p-3 text-right">{{ $rightHigh ?? 'N/A' }}</td></tr>
                <tr><td class="p-3">Thap nhat 30 ngay</td><td class="p-3 text-right">{{ $leftLow ?? 'N/A' }}</td><td class="p-3 text-right">{{ $rightLow ?? 'N/A' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
BLADE);

// --- forecast ---
w("$sec/forecast.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Du bao gia vang {{ $periodLabel }}</h2>
    <div class="grid gap-5 sm:grid-cols-3 mb-4">
        <div class="rounded-sm border-2 border-emerald-200 bg-emerald-50 p-4 text-center">
            <p class="text-xs font-semibold text-emerald-700 uppercase">Kich ban tich cuc</p>
            <p class="mt-2 text-2xl font-bold text-emerald-800">{{ $bullPrice }}</p>
            <p class="mt-1 text-sm text-emerald-600">{{ $bullChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-blue-200 bg-blue-50 p-4 text-center">
            <p class="text-xs font-semibold text-blue-700 uppercase">Kich ban co so</p>
            <p class="mt-2 text-2xl font-bold text-blue-800">{{ $basePrice }}</p>
            <p class="mt-1 text-sm text-blue-600">{{ $baseChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-rose-200 bg-rose-50 p-4 text-center">
            <p class="text-xs font-semibold text-rose-700 uppercase">Kich ban tieu cuc</p>
            <p class="mt-2 text-2xl font-bold text-rose-800">{{ $bearPrice }}</p>
            <p class="mt-1 text-sm text-rose-600">{{ $bearChange }}</p>
        </div>
    </div>
    <div class="rounded-sm border border-slate-200 p-4 mb-4">
        <h3 class="font-bold text-sm mb-3">Cac yeu to quyet dinh</h3>
        <div class="grid gap-2">
            @foreach ($factors as $f)
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-700">{{ $f['name'] }}</span>
                <span class="font-semibold {{ $f['impact'] === 'positive' ? 'text-emerald-600' : ($f['impact'] === 'negative' ? 'text-rose-600' : 'text-slate-500') }}">{{ $f['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <p class="text-sm leading-relaxed text-blue-800">{{ $analysis }}</p>
    </div>
</div>
BLADE);

// --- news-list ---
w("$sec/news-list.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $categoryLabel }}</h2>
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="/tin-tuc-gia-vang" class="chip {{ $category === 'all' ? 'positive' : '' }}">Tat ca</a>
        <a href="/tin-tuc-gia-vang/tin-thi-truong-vang" class="chip {{ $category === 'market' ? 'positive' : '' }}">Thi truong vang</a>
        <a href="/tin-tuc-gia-vang/tin-tai-chinh" class="chip {{ $category === 'finance' ? 'positive' : '' }}">Tai chinh</a>
        <a href="/tin-tuc-gia-vang/tin-kinh-te" class="chip {{ $category === 'economy' ? 'positive' : '' }}">Kinh te</a>
        <a href="/tin-tuc-gia-vang/tin-the-gioi" class="chip {{ $category === 'world' ? 'positive' : '' }}">The gioi</a>
    </div>
    <div class="grid gap-3">
        @foreach ($articles as $a)
        <article class="news-item">
            <div class="shrink-0 w-16 h-16 rounded-sm bg-linear-to-br from-slate-100 to-slate-200 grid place-items-center text-xl">{{ $a['icon'] }}</div>
            <div>
                <h4 class="text-base font-semibold text-slate-900">{{ $a['title'] }}</h4>
                <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ $a['excerpt'] }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $a['date'] }}</p>
            </div>
        </article>
        @endforeach
    </div>
</div>
BLADE);

// --- history ---
w("$sec/history.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Lich su gia vang {{ $yearLabel }}</h2>
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="/lich-su-gia-vang/gia-vang-2026" class="chip {{ $year === '2026' ? 'positive' : '' }}">2026</a>
        <a href="/lich-su-gia-vang/gia-vang-2025" class="chip {{ $year === '2025' ? 'positive' : '' }}">2025</a>
        <a href="/lich-su-gia-vang/gia-vang-2024" class="chip {{ $year === '2024' ? 'positive' : '' }}">2024</a>
        <a href="/lich-su-gia-vang/gia-vang-2023" class="chip {{ $year === '2023' ? 'positive' : '' }}">2023</a>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr><th class="p-3 text-left font-semibold">Thang</th><th class="p-3 text-right font-semibold">Mo cua</th><th class="p-3 text-right font-semibold">Cao nhat</th><th class="p-3 text-right font-semibold">Thap nhat</th><th class="p-3 text-right font-semibold">Dong cua</th><th class="p-3 text-right font-semibold">Thay doi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($months as $m)
                <tr>
                    <td class="p-3 font-medium">{{ $m['label'] }}</td>
                    <td class="p-3 text-right">{{ $m['open'] }}</td>
                    <td class="p-3 text-right">{{ $m['high'] }}</td>
                    <td class="p-3 text-right">{{ $m['low'] }}</td>
                    <td class="p-3 text-right">{{ $m['close'] }}</td>
                    <td class="p-3 text-right font-semibold {{ str_starts_with($m['change'], '+') ? 'text-emerald-600' : 'text-rose-600' }}">{{ $m['change'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <p class="text-xs text-slate-500 mb-2">Bieu do gia vang {{ $yearLabel }}</p>
        <svg viewBox="0 0 700 150" class="w-full h-36"><polyline fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linejoin="round" points="0,120 58,110 116,100 175,105 233,90 291,85 350,75 408,70 466,60 525,55 583,50 641,45 700,40"/></svg>
    </div>
</div>
BLADE);

// --- tool ---
w("$sec/tool.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $toolTitle }}</h2>
    <p class="text-sm leading-relaxed text-slate-600 mb-4">{{ $toolDesc }}</p>
    <div class="rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <div class="grid gap-3">
            @foreach ($fields as $field)
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $field['label'] }}</label>
                @if (isset($field['options']))
                <select class="w-full rounded-sm border border-blue-200 bg-white px-3 py-2.5 text-sm">
                    @foreach ($field['options'] as $opt)
                    <option>{{ $opt }}</option>
                    @endforeach
                </select>
                @else
                <input type="{{ $field['type'] ?? 'number' }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                       class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" value="{{ $field['default'] ?? '' }}">
                @endif
            </div>
            @endforeach
        </div>
        <button class="btn-primary mt-4 w-full py-3" onclick="alert('Tinh nang dang phat trien')">{{ $buttonLabel ?? 'Tinh toan' }}</button>
    </div>
    <div class="mt-4 rounded-sm border-2 border-dashed border-blue-200 bg-blue-50/50 p-4 text-center">
        <p class="text-sm text-blue-700 font-medium">Ket qua se hien thi tai day</p>
        <p class="mt-1 text-3xl font-bold text-blue-900">—</p>
    </div>
    @if (!empty($instructions))
    <div class="mt-4 rounded-sm border border-slate-200 p-4">
        <h3 class="font-bold text-sm mb-2">Huong dan su dung</h3>
        <ul class="space-y-1 text-sm leading-relaxed text-slate-600">
            @foreach ($instructions as $inst)
            <li>• {{ $inst }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
BLADE);

// --- market ---
w("$sec/market.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $marketLabel }}</h2>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr>
                    @foreach ($columns as $col)
                    <th class="p-3 {{ $loop->first ? 'text-left' : 'text-right' }} font-semibold">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $i => $cell)
                    <td class="p-3 {{ $i === 0 ? 'font-medium text-left' : 'text-right' }} {{ isset($row['_color']) && $i === count($row)-1 ? $row['_color'] : '' }}">{{ $cell }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <p class="text-xs text-slate-500 mb-2">Bieu do {{ $marketLabel }} 30 ngay</p>
        <svg viewBox="0 0 600 120" class="w-full h-28"><polyline fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linejoin="round" points="0,90 60,85 120,80 180,75 240,70 300,65 360,60 420,55 480,50 540,48 600,45"/></svg>
    </div>
    <p class="mt-3 text-xs text-slate-400">Du lieu mang tinh tham khao, cap nhat tu cac san giao dich chinh thuc.</p>
</div>
BLADE);

// --- knowledge ---
w("$sec/knowledge.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $articleTitle }}</h2>

    @if (!empty($toc))
    <nav class="mb-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <p class="font-semibold text-sm text-blue-900 mb-2">Noi dung chinh</p>
        <ol class="list-decimal list-inside space-y-1 text-sm text-blue-700">
            @foreach ($toc as $item)
            <li>{{ $item }}</li>
            @endforeach
        </ol>
    </nav>
    @endif

    @foreach ($sections as $sec)
    <div class="mb-4">
        <h3 class="text-lg font-bold text-[#001061] mb-2">{{ $sec['heading'] }}</h3>
        <div class="text-sm leading-relaxed text-slate-700">{!! $sec['body'] !!}</div>
    </div>
    @endforeach

    <div class="mt-6 rounded-sm border border-amber-200 bg-amber-50 p-4">
        <p class="font-bold text-amber-900 text-sm">Luu y</p>
        <p class="mt-1 text-sm text-amber-800">Thong tin tren mang tinh chat tham khao va giao duc. Hay tham khao y kien chuyen gia truoc khi dua ra quyet dinh dau tu.</p>
    </div>
</div>
BLADE);

// --- api-doc ---
w("$sec/api-doc.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $apiTitle }}</h2>
    <p class="text-sm leading-relaxed text-slate-600 mb-4">{{ $apiDesc }}</p>

    @foreach ($endpoints as $ep)
    <div class="mb-4 rounded-sm border border-slate-200 overflow-hidden">
        <div class="flex items-center gap-3 bg-slate-50 px-4 py-3">
            <span class="rounded-sm px-2 py-1 text-xs font-bold {{ $ep['method'] === 'GET' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">{{ $ep['method'] }}</span>
            <code class="text-sm font-mono font-semibold text-slate-800">{{ $ep['path'] }}</code>
        </div>
        <div class="p-4">
            <p class="text-sm leading-relaxed text-slate-600">{{ $ep['desc'] }}</p>
            @if (!empty($ep['params']))
            <div class="mt-3">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Tham so</p>
                <div class="table-wrap rounded border border-slate-200">
                    <table class="w-full text-sm">
                        <thead class="bg-[#f5f5f5]"><tr><th class="p-2 text-left font-semibold text-xs">Ten</th><th class="p-2 text-left font-semibold text-xs">Kieu</th><th class="p-2 text-left font-semibold text-xs">Mo ta</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($ep['params'] as $p)
                            <tr><td class="p-2"><code class="text-xs">{{ $p['name'] }}</code></td><td class="p-2 text-xs text-slate-500">{{ $p['type'] }}</td><td class="p-2 text-xs text-slate-600">{{ $p['desc'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if (!empty($ep['response']))
            <div class="mt-3">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Response</p>
                <pre class="rounded-sm bg-slate-900 p-3 text-xs text-green-400 overflow-x-auto"><code>{{ $ep['response'] }}</code></pre>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
BLADE);

// --- about ---
w("$sec/about.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Ve GoldPrice</h2>
    <div class="text-sm leading-relaxed text-slate-700 space-y-4">
        <p><strong>GoldPrice</strong> la nen tang cap nhat gia vang truc tuyen hang dau tai Viet Nam, cung cap thong tin gia vang SJC, DOJI, PNJ va the gioi (XAU/USD) theo thoi gian thuc.</p>
        <p>Chung toi tong hop du lieu tu cac thuong hieu vang chinh hang, san giao dich quoc te va nguon tin tai chinh uy tin de mang den cho nguoi dung goc nhin toan dien ve thi truong vang.</p>
        <h3 class="text-lg font-bold text-[#001061] mt-6!">Su menh</h3>
        <p>Giup nguoi dung Viet Nam tiep can thong tin gia vang chinh xac, nhanh chong va mien phi, ho tro dua ra quyet dinh dau tu sang suot.</p>
        <h3 class="text-lg font-bold text-[#001061] mt-6!">Tinh nang noi bat</h3>
        <ul class="list-disc list-inside space-y-1">
            <li>Cap nhat gia vang trong nuoc va quoc te theo thoi gian thuc</li>
            <li>Bieu do phan tich xu huong da khung thoi gian</li>
            <li>So sanh gia giua cac thuong hieu va kenh dau tu</li>
            <li>Cong cu tinh toan va quy doi vang tien loi</li>
            <li>Tin tuc va phan tich chuyen sau ve thi truong vang</li>
            <li>API du lieu cho nha phat trien</li>
        </ul>
        <h3 class="text-lg font-bold text-[#001061] mt-6!">Lien he hop tac</h3>
        <p>Email: <a href="mailto:contact@goldprice.vn" class="text-blue-600 hover:underline">contact@goldprice.vn</a></p>
    </div>
</div>
BLADE);

// --- contact ---
w("$sec/contact.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Lien he voi chung toi</h2>
    <div class="grid gap-5 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Email</h3>
            <p class="mt-1 text-sm text-blue-600">contact@goldprice.vn</p>
        </div>
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Hotline</h3>
            <p class="mt-1 text-sm text-blue-600">1900 xxxx xx</p>
        </div>
    </div>
    <form class="rounded-sm border border-[#bcbcbc] bg-slate-50 p-4 grid gap-3" onsubmit="event.preventDefault();alert('Cam on ban da gop y!')">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Ho va ten</label>
            <input type="text" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Nguyen Van A">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="email@example.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Chu de</label>
            <select class="w-full rounded-sm border border-blue-200 bg-white px-3 py-2.5 text-sm">
                <option>Gop y noi dung</option>
                <option>Hop tac quang cao</option>
                <option>Yeu cau API</option>
                <option>Bao loi</option>
                <option>Khac</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Noi dung</label>
            <textarea rows="5" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Viet noi dung tai day..."></textarea>
        </div>
        <button type="submit" class="btn-primary py-3">Gui tin nhan</button>
    </form>
</div>
BLADE);

// --- privacy ---
w("$sec/privacy.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Chinh sach bao mat</h2>
    <p class="text-xs text-slate-400 mb-4">Cap nhat lan cuoi: 01/03/2026</p>
    <div class="text-sm leading-relaxed text-slate-700 space-y-4">
        <h3 class="text-lg font-bold text-[#001061]">1. Thu thap thong tin</h3>
        <p>Chung toi thu thap thong tin ca nhan khi ban dang ky nhan ban tin, su dung API hoac gui form lien he. Thong tin bao gom: ho ten, email va noi dung tin nhan.</p>
        <h3 class="text-lg font-bold text-[#001061]">2. Muc dich su dung</h3>
        <ul class="list-disc list-inside space-y-1">
            <li>Gui thong bao cap nhat gia vang theo dang ky</li>
            <li>Xu ly yeu cau ho tro va phan hoi</li>
            <li>Cai thien chat luong dich vu va trai nghiem nguoi dung</li>
        </ul>
        <h3 class="text-lg font-bold text-[#001061]">3. Bao ve thong tin</h3>
        <p>Du lieu nguoi dung duoc ma hoa va luu tru an toan. Chung toi khong chia se thong tin ca nhan voi ben thu ba ngoai muc dich da neu.</p>
        <h3 class="text-lg font-bold text-[#001061]">4. Cookie</h3>
        <p>Website su dung cookie de tang trai nghiem duyet web. Ban co the tat cookie trong trinh duyet nhung mot so tinh nang co the bi anh huong.</p>
        <h3 class="text-lg font-bold text-[#001061]">5. Lien he</h3>
        <p>Moi thac mac ve chinh sach bao mat, vui long gui email toi <a href="mailto:privacy@goldprice.vn" class="text-blue-600 hover:underline">privacy@goldprice.vn</a>.</p>
    </div>
</div>
BLADE);

// --- terms ---
w("$sec/terms.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Dieu khoan su dung</h2>
    <p class="text-xs text-slate-400 mb-4">Cap nhat lan cuoi: 01/03/2026</p>
    <div class="text-sm leading-relaxed text-slate-700 space-y-4">
        <h3 class="text-lg font-bold text-[#001061]">1. Chap nhan dieu khoan</h3>
        <p>Khi truy cap va su dung GoldPrice, ban dong y tuan thu cac dieu khoan duoi day. Neu khong dong y, vui long ngung su dung dich vu.</p>
        <h3 class="text-lg font-bold text-[#001061]">2. Noi dung va du lieu</h3>
        <p>Gia vang va thong tin tren website mang tinh tham khao, khong phai loi khuyen dau tu. Chung toi khong chiu trach nhiem ve quyet dinh dau tu cua ban dua tren du lieu tren website.</p>
        <h3 class="text-lg font-bold text-[#001061]">3. Tai khoan va API</h3>
        <p>Khi su dung API, ban cam ket khong su dung qua muc hoac cho muc dich gay hai. Chung toi co quyen gioi han hoac dong tai khoan vi pham.</p>
        <h3 class="text-lg font-bold text-[#001061]">4. Quyen so huu tri tue</h3>
        <p>Toan bo noi dung, thiet ke va ma nguon thuoc quyen so huu cua GoldPrice. Nghiem cam sao chep hoac phan phoi ma khong duoc phep.</p>
        <h3 class="text-lg font-bold text-[#001061]">5. Thay doi dieu khoan</h3>
        <p>Chung toi co quyen thay doi dieu khoan bat ky luc nao. Nguoi dung se duoc thong bao qua email hoac thong bao tren website.</p>
    </div>
</div>
BLADE);

// --- sidebar-price ---
w("$sec/sidebar-price.blade.php", <<<'BLADE'
<div class="glass-card p-4">
    <h3 class="text-lg font-bold text-[#001061] mb-3">Gia vang the gioi</h3>
    <div class="rounded-sm border border-[#bcbcbc] bg-blue-50 p-3 text-center">
        <p class="text-2xl font-bold text-blue-900">2,918.45</p>
        <p class="text-xs text-blue-700">USD/oz &nbsp; <span class="text-emerald-600 font-semibold">+12.30</span></p>
    </div>
    <div class="mt-3 grid gap-2 text-sm">
        <div class="flex justify-between"><span class="text-slate-500">SJC 1L Ban</span><span class="font-semibold">92,500,000</span></div>
        <div class="flex justify-between"><span class="text-slate-500">DOJI 1L Ban</span><span class="font-semibold">92,400,000</span></div>
        <div class="flex justify-between"><span class="text-slate-500">PNJ 1L Ban</span><span class="font-semibold">92,300,000</span></div>
    </div>
</div>
BLADE);

// --- sidebar-tools ---
w("$sec/sidebar-tools.blade.php", <<<'BLADE'
<div class="glass-card p-4">
    <h3 class="text-lg font-bold text-[#001061] mb-3">Cong cu noi bat</h3>
    <div class="grid gap-2">
        <a href="/cong-cu/quy-doi-vang" class="block rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm text-blue-700 hover:bg-blue-50">⚖️ Quy doi vang</a>
        <a href="/cong-cu/tinh-gia-vang" class="block rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm text-blue-700 hover:bg-blue-50">🧮 Tinh gia vang</a>
        <a href="/cong-cu/tinh-lai-dau-tu-vang" class="block rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm text-blue-700 hover:bg-blue-50">📈 Tinh lai dau tu</a>
    </div>
</div>
BLADE);

// --- sidebar-news ---
w("$sec/sidebar-news.blade.php", <<<'BLADE'
<div class="glass-card p-4">
    <h3 class="text-lg font-bold text-[#001061] mb-3">Tin moi nhat</h3>
    <div class="grid gap-2 text-sm">
        <a href="/tin-tuc-gia-vang/tin-thi-truong-vang" class="block text-blue-700 hover:underline">Gia vang phuc hoi manh nho Fed giu nguyen lai suat</a>
        <a href="/tin-tuc-gia-vang/tin-tai-chinh" class="block text-blue-700 hover:underline">USD suy yeu, vang the gioi vuot dinh cu</a>
        <a href="/tin-tuc-gia-vang/tin-kinh-te" class="block text-blue-700 hover:underline">Lam phat Eurozone tang bat ngo, ho tro gia vang</a>
    </div>
</div>
BLADE);

echo "=> " . count(glob("$sec/*.blade.php")) . " section partials\n";

// ============================================================
// 3) PAGE FILES
// ============================================================
$pages = [];

// helper: wrap in @extends + @section
function pg(string $body, string $label = '', string $sidebar = ''): string {
    $out  = "@extends('gold.page-shell')\n\n";
    if ($label) $out .= "@section('page-label', '$label')\n\n";
    $out .= "@section('page-content')\n$body\n@endsection\n";
    if ($sidebar) $out .= "\n@section('page-sidebar')\n$sidebar\n@endsection\n";
    return $out;
}

// ---- gia-vang-hom-nay ----
$pages['gia-vang-hom-nay'] = pg(
    "@include('gold.sections.today-price')",
    'Cap nhat gia vang',
    "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-tools')"
);

// ---- bang-gia-vang ----
$pages['bang-gia-vang'] = pg(
    "@include('gold.sections.price-table')",
    'Bang gia',
    "@include('gold.sections.sidebar-price')"
);

$brands = [
    'gia-vang-sjc'              => 'SJC',
    'gia-vang-doji'             => 'DOJI',
    'gia-vang-pnj'              => 'PNJ',
    'gia-vang-phu-quy'          => 'Phu Quy',
    'gia-vang-mi-hong'          => 'Mi Hong',
    'gia-vang-bao-tin-minh-chau'=> 'Bao Tin Minh Chau',
];
foreach ($brands as $slug => $name) {
    $pages["bang-gia-vang/$slug"] = pg(
        "@include('gold.sections.price-brand', ['brandName' => '$name'])",
        'Bang gia',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- gia-vang-the-gioi ----
$pages['gia-vang-the-gioi'] = pg(
    "@include('gold.sections.world-price')",
    'Quoc te',
    "@include('gold.sections.sidebar-price')"
);

// ---- bieu-do-gia-vang ----
$periods = [
    ''                            => ['all',   'tong hop'],
    'bieu-do-gia-vang-hom-nay'    => ['today', 'hom nay'],
    'bieu-do-gia-vang-7-ngay'     => ['7d',    '7 ngay'],
    'bieu-do-gia-vang-30-ngay'    => ['30d',   '30 ngay'],
    'bieu-do-gia-vang-1-nam'      => ['1y',    '1 nam'],
    'bieu-do-gia-vang-10-nam'     => ['10y',   '10 nam'],
];
foreach ($periods as $slug => $info) {
    $path = $slug === '' ? 'bieu-do-gia-vang' : "bieu-do-gia-vang/$slug";
    $pages[$path] = pg(
        "@include('gold.sections.chart', ['period' => '{$info[0]}', 'periodLabel' => '{$info[1]}'])",
        'Bieu do',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- so-sanh-gia-vang ----
$pages['so-sanh-gia-vang'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">So sanh gia vang</h2>
    <div class="grid gap-5">
        <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">SJC</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">The gioi</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Chenh: 18.5 trieu</span>
        </a>
        <a href="/so-sanh-gia-vang/sjc-vs-pnj" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">SJC</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">PNJ</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Chenh: 200,000</span>
        </a>
        <a href="/so-sanh-gia-vang/vang-vs-usd" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">Vang</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">USD</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Tuong quan: 0.85</span>
        </a>
    </div>
</div>
BLADE, 'So sanh', "@include('gold.sections.sidebar-price')");

$comparisons = [
    'sjc-vs-the-gioi' => [
        'SJC', 'Gia the gioi (quy doi)',
        '92,500,000 VND', '74,000,000 VND',
        '+500,000', '+312,000',
        '18,500,000 VND', 'Chenh lech do thue, phi va cung cau noi dia',
        '93,200,000 VND', '74,800,000 VND',
        '89,500,000 VND', '72,100,000 VND',
    ],
    'sjc-vs-pnj' => [
        'SJC', 'PNJ',
        '92,500,000 VND', '92,300,000 VND',
        '+500,000', '+300,000',
        '200,000 VND', 'Chenh lech nho do canh tranh truc tiep giua thuong hieu',
        '93,200,000 VND', '93,000,000 VND',
        '89,500,000 VND', '89,300,000 VND',
    ],
    'vang-vs-usd' => [
        'Vang (XAU)', 'USD (DXY)',
        '2,918.45 USD', '103.25 pts',
        '+12.30', '-0.32',
        'Tuong quan nghich', 'Khi USD yeu, vang thuong tang va nguoc lai',
        '2,950.00 USD', '104.80 pts',
        '2,850.00 USD', '101.50 pts',
    ],
];
foreach ($comparisons as $slug => $d) {
    $pages["so-sanh-gia-vang/$slug"] = pg(
        "@include('gold.sections.comparison', [\n"
       ."    'leftName'    => '{$d[0]}',\n"
       ."    'rightName'   => '{$d[1]}',\n"
       ."    'leftPrice'   => '{$d[2]}',\n"
       ."    'rightPrice'  => '{$d[3]}',\n"
       ."    'leftChange'  => '{$d[4]}',\n"
       ."    'rightChange' => '{$d[5]}',\n"
       ."    'spread'      => '{$d[6]}',\n"
       ."    'spreadNote'  => '{$d[7]}',\n"
       ."    'leftHigh'    => '{$d[8]}',\n"
       ."    'rightHigh'   => '{$d[9]}',\n"
       ."    'leftLow'     => '{$d[10]}',\n"
       ."    'rightLow'    => '{$d[11]}',\n"
       ."])",
        'So sanh',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- du-bao-gia-vang ----
$forecasts = [
    '' => [
        'tong hop', '93,000,000', '+0.5%', '92,500,000', 'Di ngang', '91,000,000', '-1.6%',
        'Phan tich tong hop cac kich ban du bao gia vang tu ngan han den dai han. Du lieu tu nhieu nguon phan tich ky thuat va co ban.',
    ],
    'du-bao-gia-vang-tuan' => [
        'tuan nay', '93,200,000', '+0.8%', '92,800,000', '+0.3%', '91,500,000', '-1.1%',
        'Gia vang duoc du bao tang nhe trong tuan nay nho ky vong Fed giu lai suat on dinh va dong USD suy yeu. Rui ro giam gia den tu bao cao viec lam My tot hon ky vong.',
    ],
    'du-bao-gia-vang-thang' => [
        'thang nay', '95,000,000', '+2.7%', '93,500,000', '+1.1%', '90,000,000', '-2.7%',
        'Xu huong tang trung han duoc ho tro boi nhu cau mua vao cua ngan hang trung uong va lo ngai lam phat. Kich ban tieu cuc neu Fed tang lai suat bat ngo.',
    ],
    'du-bao-gia-vang-2026' => [
        'nam 2026', '100,000,000', '+8.1%', '95,000,000', '+2.7%', '85,000,000', '-8.1%',
        'Nam 2026, gia vang duoc ky vong duy tri xu huong tang dai han nho cac yeu to: bat on dia chinh tri, lam phat toan cau va chinh sach tien te noi long. Goldman Sachs du bao XAU/USD dat 3,100 USD/oz cuoi nam.',
    ],
];
$forecastFactors = [
    ['name'=>'Chinh sach Fed','impact'=>'positive','label'=>'Ho tro tang'],
    ['name'=>'Chi so USD (DXY)','impact'=>'positive','label'=>'Dang giam'],
    ['name'=>'Lam phat toan cau','impact'=>'positive','label'=>'Van cao'],
    ['name'=>'Nhu cau NHTW','impact'=>'positive','label'=>'Tang manh'],
    ['name'=>'Rui ro dia chinh tri','impact'=>'neutral','label'=>'Trung binh'],
];
foreach ($forecasts as $slug => $d) {
    $path = $slug === '' ? 'du-bao-gia-vang' : "du-bao-gia-vang/$slug";
    // Build factors array as PHP literal for Blade
    $factorsPhp = "[\n";
    foreach ($forecastFactors as $f) {
        $factorsPhp .= "        ['name'=>'{$f['name']}','impact'=>'{$f['impact']}','label'=>'{$f['label']}'],\n";
    }
    $factorsPhp .= "    ]";
    $pages[$path] = pg(
        "@include('gold.sections.forecast', [\n"
       ."    'periodLabel' => '{$d[0]}',\n"
       ."    'bullPrice'   => '{$d[1]}',\n"
       ."    'bullChange'  => '{$d[2]}',\n"
       ."    'basePrice'   => '{$d[3]}',\n"
       ."    'baseChange'  => '{$d[4]}',\n"
       ."    'bearPrice'   => '{$d[5]}',\n"
       ."    'bearChange'  => '{$d[6]}',\n"
       ."    'analysis'    => '{$d[7]}',\n"
       ."    'factors'     => $factorsPhp,\n"
       ."])",
        'Du bao',
        "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-news')"
    );
}

// ---- tin-tuc-gia-vang ----
$newsArticles = [
    ['icon'=>'📊','title'=>'Gia vang phuc hoi manh sau phien giam sau','excerpt'=>'Gia vang SJC tang 500,000 dong/luong trong phien sang nay sau khi thi truong quoc te phat tin hieu tich cuc tu chinh sach Fed.','date'=>'07/03/2026'],
    ['icon'=>'💰','title'=>'USD suy yeu day gia vang the gioi len dinh moi','excerpt'=>'Chi so DXY giam 0.3%, ho tro gia vang XAU/USD vuot moc 2,920 USD/oz lan dau trong tuan.','date'=>'06/03/2026'],
    ['icon'=>'🏦','title'=>'Ngan hang trung uong cac nuoc tiep tuc mua vang','excerpt'=>'Theo du lieu WGC, quy I/2026, cac NHTW mua rong 280 tan vang, tang 15% so voi cung ky nam truoc.','date'=>'05/03/2026'],
    ['icon'=>'🌍','title'=>'Cang thang dia chinh tri day nhu cau tru an toan','excerpt'=>'Tinh hinh bat on o Trung Dong va bien Dong lam tang nhu cau vang nhu tai san tru an toan an toan.','date'=>'04/03/2026'],
    ['icon'=>'📈','title'=>'Phan tich ky thuat: Vang hinh thanh dang nen tang','excerpt'=>'Mau hinh gia hien tai cho thay vang dang tich luy trong vung 2,900-2,930 truoc khi co dot pha lon.','date'=>'03/03/2026'],
];
$newsPhp = "[\n";
foreach ($newsArticles as $a) {
    $newsPhp .= "        ['icon'=>'{$a['icon']}','title'=>'{$a['title']}','excerpt'=>'{$a['excerpt']}','date'=>'{$a['date']}'],\n";
}
$newsPhp .= "    ]";

$newsCategories = [
    ''                     => ['all',     'Tin tuc gia vang moi nhat'],
    'tin-thi-truong-vang'  => ['market',  'Tin thi truong vang'],
    'tin-tai-chinh'        => ['finance', 'Tin tai chinh'],
    'tin-kinh-te'          => ['economy', 'Tin kinh te'],
    'tin-the-gioi'         => ['world',   'Tin the gioi'],
];
foreach ($newsCategories as $slug => $info) {
    $path = $slug === '' ? 'tin-tuc-gia-vang' : "tin-tuc-gia-vang/$slug";
    $pages[$path] = pg(
        "@include('gold.sections.news-list', [\n"
       ."    'category'      => '{$info[0]}',\n"
       ."    'categoryLabel' => '{$info[1]}',\n"
       ."    'articles'      => $newsPhp,\n"
       ."])",
        'Tin tuc',
        "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-news')"
    );
}

// ---- lich-su-gia-vang ----
$sampleMonths = [
    ['label'=>'Thang 1','open'=>'89,000,000','high'=>'90,500,000','low'=>'88,200,000','close'=>'90,000,000','change'=>'+1,000,000'],
    ['label'=>'Thang 2','open'=>'90,000,000','high'=>'91,800,000','low'=>'89,500,000','close'=>'91,200,000','change'=>'+1,200,000'],
    ['label'=>'Thang 3','open'=>'91,200,000','high'=>'92,800,000','low'=>'90,800,000','close'=>'92,500,000','change'=>'+1,300,000'],
];
$monthsPhp = "[\n";
foreach ($sampleMonths as $m) {
    $monthsPhp .= "        ['label'=>'{$m['label']}','open'=>'{$m['open']}','high'=>'{$m['high']}','low'=>'{$m['low']}','close'=>'{$m['close']}','change'=>'{$m['change']}'],\n";
}
$monthsPhp .= "    ]";

$historyYears = [
    ''              => ['all',  'tong hop'],
    'gia-vang-2026' => ['2026', 'nam 2026'],
    'gia-vang-2025' => ['2025', 'nam 2025'],
    'gia-vang-2024' => ['2024', 'nam 2024'],
    'gia-vang-2023' => ['2023', 'nam 2023'],
];
foreach ($historyYears as $slug => $info) {
    $path = $slug === '' ? 'lich-su-gia-vang' : "lich-su-gia-vang/$slug";
    $pages[$path] = pg(
        "@include('gold.sections.history', [\n"
       ."    'year'      => '{$info[0]}',\n"
       ."    'yearLabel' => '{$info[1]}',\n"
       ."    'months'    => $monthsPhp,\n"
       ."])",
        'Lich su',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- cong-cu ----
$pages['cong-cu'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Bo cong cu gia vang</h2>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/cong-cu/quy-doi-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⚖️</p>
            <h3 class="font-bold text-slate-900">Quy doi vang</h3>
            <p class="mt-1 text-sm text-slate-500">Quy doi giua luong, chi, gram, ounce</p>
        </a>
        <a href="/cong-cu/tinh-gia-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🧮</p>
            <h3 class="font-bold text-slate-900">Tinh gia vang</h3>
            <p class="mt-1 text-sm text-slate-500">Tinh gia tri vang theo khoi luong</p>
        </a>
        <a href="/cong-cu/tinh-lai-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">📈</p>
            <h3 class="font-bold text-slate-900">Tinh lai dau tu vang</h3>
            <p class="mt-1 text-sm text-slate-500">Tinh loi nhuan, lo tren khoan dau tu</p>
        </a>
        <a href="/cong-cu/doi-vang-sang-usd" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💱</p>
            <h3 class="font-bold text-slate-900">Doi vang sang USD</h3>
            <p class="mt-1 text-sm text-slate-500">Quy doi gia vang VND sang USD</p>
        </a>
    </div>
</div>
BLADE, 'Cong cu', "@include('gold.sections.sidebar-price')");

// Tool sub-pages
$tools = [
    'quy-doi-vang' => [
        'Quy doi don vi vang',
        'Chuyen doi nhanh giua cac don vi: luong, chi, gram, ounce troy.',
        [
            ['label'=>'Gia tri','type'=>'number','placeholder'=>'1','default'=>'1'],
            ['label'=>'Tu don vi','options'=>['Luong','Chi','Gram','Ounce troy']],
            ['label'=>'Sang don vi','options'=>['Gram','Chi','Luong','Ounce troy']],
        ],
        'Quy doi',
        ['1 luong = 37.5 gram','1 luong = 10 chi','1 ounce troy = 31.1035 gram','1 luong = 1.2057 ounce troy'],
    ],
    'tinh-gia-vang' => [
        'Tinh gia vang theo khoi luong',
        'Nhap khoi luong va don gia de tinh tong gia tri vang.',
        [
            ['label'=>'Khoi luong','type'=>'number','placeholder'=>'1','default'=>'1'],
            ['label'=>'Don vi','options'=>['Luong','Chi','Gram']],
            ['label'=>'Don gia (VND/luong)','type'=>'number','placeholder'=>'92500000','default'=>'92500000'],
        ],
        'Tinh gia',
        ['Nhap khoi luong vang ban muon tinh','Chon don vi phu hop','Nhap don gia hien tai hoac don gia ban mua','Ket qua = Khoi luong x Don gia (quy ve luong)'],
    ],
    'tinh-lai-dau-tu-vang' => [
        'Tinh lai dau tu vang',
        'Tinh loi nhuan hoac lo tu khoan dau tu vang cua ban.',
        [
            ['label'=>'Gia mua vao (VND/luong)','type'=>'number','placeholder'=>'85000000','default'=>'85000000'],
            ['label'=>'Gia hien tai (VND/luong)','type'=>'number','placeholder'=>'92500000','default'=>'92500000'],
            ['label'=>'So luong (luong)','type'=>'number','placeholder'=>'1','default'=>'1'],
        ],
        'Tinh lai/lo',
        ['Nhap gia ban da mua vang','Nhap gia vang hien tai','Nhap so luong luong da mua','Loi nhuan = (Gia hien tai - Gia mua) x So luong'],
    ],
    'doi-vang-sang-usd' => [
        'Doi gia vang VND sang USD',
        'Quy doi gia vang tu VND sang USD theo ty gia hien tai.',
        [
            ['label'=>'Gia vang (VND)','type'=>'number','placeholder'=>'92500000','default'=>'92500000'],
            ['label'=>'Ty gia USD/VND','type'=>'number','placeholder'=>'25400','default'=>'25400'],
        ],
        'Quy doi',
        ['Nhap gia vang tinh bang VND','Nhap ty gia USD/VND hien tai','Ket qua = Gia VND / Ty gia','So sanh voi gia quoc te de thay chenh lech'],
    ],
];
foreach ($tools as $slug => $t) {
    $fieldsPhp = "[\n";
    foreach ($t[2] as $f) {
        $fieldsPhp .= "        [";
        foreach ($f as $k => $v) {
            if (is_array($v)) {
                $fieldsPhp .= "'$k'=>['" . implode("','", $v) . "'],";
            } else {
                $fieldsPhp .= "'$k'=>'$v',";
            }
        }
        $fieldsPhp .= "],\n";
    }
    $fieldsPhp .= "    ]";
    $instPhp = "[\n";
    foreach ($t[4] as $inst) {
        $instPhp .= "        '$inst',\n";
    }
    $instPhp .= "    ]";
    $pages["cong-cu/$slug"] = pg(
        "@include('gold.sections.tool', [\n"
       ."    'toolTitle'    => '{$t[0]}',\n"
       ."    'toolDesc'     => '{$t[1]}',\n"
       ."    'fields'       => $fieldsPhp,\n"
       ."    'buttonLabel'  => '{$t[3]}',\n"
       ."    'instructions' => $instPhp,\n"
       ."])",
        'Cong cu',
        "@include('gold.sections.sidebar-tools')"
    );
}

// ---- thi-truong ----
$pages['thi-truong'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Tong quan thi truong</h2>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/thi-truong/gia-xang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⛽</p>
            <h3 class="font-bold text-slate-900">Gia xang</h3>
            <p class="mt-1 text-sm text-slate-500">Cap nhat gia xang dau trong nuoc</p>
        </a>
        <a href="/thi-truong/ty-gia-ngoai-te" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💵</p>
            <h3 class="font-bold text-slate-900">Ty gia ngoai te</h3>
            <p class="mt-1 text-sm text-slate-500">Ty gia cac dong tien chinh</p>
        </a>
        <a href="/thi-truong/gia-bac" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🥈</p>
            <h3 class="font-bold text-slate-900">Gia bac</h3>
            <p class="mt-1 text-sm text-slate-500">Gia bac the gioi va trong nuoc</p>
        </a>
        <a href="/thi-truong/gia-kim-loai" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🔩</p>
            <h3 class="font-bold text-slate-900">Gia kim loai</h3>
            <p class="mt-1 text-sm text-slate-500">Bac, platinum, palladium va dong</p>
        </a>
    </div>
</div>
BLADE, 'Thi truong', "@include('gold.sections.sidebar-price')");

$markets = [
    'gia-xang' => [
        'Gia xang dau hom nay',
        ['San pham', 'Gia (VND/lit)', 'Thay doi'],
        [
            ['RON 95-III', '23,650', '+320'],
            ['RON 95-V', '23,150', '+280'],
            ['E5 RON 92', '22,450', '+250'],
            ['DO 0.05S', '20,980', '+180'],
            ['Dau hoa', '20,350', '+150'],
        ],
    ],
    'ty-gia-ngoai-te' => [
        'Ty gia ngoai te hom nay',
        ['Ngoai te', 'Mua TM', 'Mua CK', 'Ban'],
        [
            ['USD', '25,150', '25,250', '25,480'],
            ['EUR', '26,950', '27,150', '27,680'],
            ['GBP', '31,800', '32,100', '32,750'],
            ['JPY', '165', '168', '172'],
            ['CHF', '28,200', '28,450', '29,000'],
            ['AUD', '16,350', '16,500', '16,850'],
        ],
    ],
    'gia-bac' => [
        'Gia bac hom nay',
        ['San pham', 'Gia (VND/luong)', 'Thay doi'],
        [
            ['Bac 999 (The gioi)', '32.45 USD/oz', '+0.28'],
            ['Bac 999 Viet Nam', '1,050,000', '+15,000'],
            ['Bac 925 (trang suc)', '890,000', '+12,000'],
            ['Bac thanh (1kg)', '28,500,000', '+380,000'],
        ],
    ],
    'gia-kim-loai' => [
        'Gia kim loai quy hom nay',
        ['Kim loai', 'Gia (USD/oz)', 'Thay doi', '% 30 ngay'],
        [
            ['Vang (XAU)', '2,918.45', '+12.30', '+2.8%'],
            ['Bac (XAG)', '32.45', '+0.28', '+3.1%'],
            ['Platinum (XPT)', '985.60', '+5.40', '+1.5%'],
            ['Palladium (XPD)', '975.30', '-8.20', '-2.1%'],
            ['Dong (HG)', '4.15', '+0.03', '+1.2%'],
        ],
    ],
];
foreach ($markets as $slug => $m) {
    $colsPhp = "['" . implode("','", $m[1]) . "']";
    $rowsPhp = "[\n";
    foreach ($m[2] as $row) {
        $rowsPhp .= "        ['" . implode("','", $row) . "'],\n";
    }
    $rowsPhp .= "    ]";
    $pages["thi-truong/$slug"] = pg(
        "@include('gold.sections.market', [\n"
       ."    'marketLabel' => '{$m[0]}',\n"
       ."    'columns'     => $colsPhp,\n"
       ."    'rows'        => $rowsPhp,\n"
       ."])",
        'Thi truong',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- kien-thuc-vang ----
$pages['kien-thuc-vang'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Kien thuc ve vang</h2>
    <div class="grid gap-5">
        <a href="/kien-thuc-vang/vang-9999-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vang 9999 la gi?</h3>
            <p class="mt-1 text-sm text-slate-500">Tim hieu ve vang 4 so 9 va cach phan biet voi cac loai vang khac tren thi truong.</p>
        </a>
        <a href="/kien-thuc-vang/vang-sjc-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vang SJC la gi?</h3>
            <p class="mt-1 text-sm text-slate-500">Lich su hinh thanh, dac diem va vi tri cua vang SJC trong thi truong Viet Nam.</p>
        </a>
        <a href="/kien-thuc-vang/nen-mua-vang-nao" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Nen mua vang nao?</h3>
            <p class="mt-1 text-sm text-slate-500">So sanh vang mieng, vang nhan, vang trang suc de chon loai phu hop voi muc dich.</p>
        </a>
        <a href="/kien-thuc-vang/cach-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Cach dau tu vang hieu qua</h3>
            <p class="mt-1 text-sm text-slate-500">Huong dan chien luoc dau tu vang cho nguoi moi bat dau.</p>
        </a>
    </div>
</div>
BLADE, 'Kien thuc', "@include('gold.sections.sidebar-price')");

$articles = [
    'vang-9999-la-gi' => [
        'Vang 9999 la gi?',
        ['The nao la vang 9999?', 'Dac diem cua vang 9999', 'Phan biet vang 9999 va cac loai khac', 'Gia tri dau tu cua vang 9999'],
        [
            ['heading'=>'The nao la vang 9999?',
             'body'=>'<p>Vang 9999 (hay vang 4 so 9) la loai vang co do tinh khiet cao nhat, dat 99.99% vang nguyen chat. Day la tieu chuan cao nhat trong nganh cong nghiep vang tren toan the gioi.</p><p class="mt-2">O Viet Nam, vang 9999 duoc giao dich pho bien duoi dang vang mieng (SJC, DOJI, PNJ) va vang nhan tron.</p>'],
            ['heading'=>'Dac diem cua vang 9999',
             'body'=>'<ul class="list-disc list-inside space-y-1 mt-1"><li>Do tinh khiet: 99.99%</li><li>Mau sac: Vang dam, bong</li><li>Do mem: Mem hon vang 18K, 14K</li><li>Khong bi oxy hoa hay bien mau</li><li>De dang kiem dinh bang phuong phap hoa hoc va dien tu</li></ul>'],
            ['heading'=>'Phan biet vang 9999 va cac loai khac',
             'body'=>'<p>Vang 9999 khac voi vang 24K (99.9%), vang 18K (75%), vang 14K (58.5%). Do tinh khiet cao hon nen vang 9999 co gia tri dau tu tot hon nhung it phu hop lam trang suc do mem.</p>'],
            ['heading'=>'Gia tri dau tu cua vang 9999',
             'body'=>'<p>Vang 9999 duoc xem la kenh dau tu an toan, bao toan gia tri truoc lam phat. Nha dau tu thuong mua vang 9999 duoi dang vang mieng SJC hoac vang nhan de tich tru tai san dai han.</p>'],
        ],
    ],
    'vang-sjc-la-gi' => [
        'Vang SJC la gi?',
        ['Gioi thieu vang SJC', 'Lich su hinh thanh', 'Vi tri tren thi truong', 'Cach mua ban vang SJC'],
        [
            ['heading'=>'Gioi thieu vang SJC',
             'body'=>'<p>Vang SJC la thuong hieu vang mieng do Cong ty TNHH MTV Vang Bac Da Quy Sai Gon (SJC) san xuat. Day la thuong hieu vang duoc Nha nuoc uy quyen san xuat doc quyen khu 1 luong tai Viet Nam.</p>'],
            ['heading'=>'Lich su hinh thanh',
             'body'=>'<p>SJC duoc thanh lap nam 1988, tro thanh don vi san xuat vang mieng lon nhat Viet Nam. Tu nam 2012, theo Nghi dinh 24, SJC la thuong hieu vang mieng quoc gia duy nhat.</p>'],
            ['heading'=>'Vi tri tren thi truong',
             'body'=>'<p>Vang SJC chiem thi phan lon nhat trong giao dich vang mieng tai Viet Nam. Gia vang SJC thuong cao hon cac thuong hieu khac va gia quoc te quy doi do tinh khan hiem va thuong hieu manh.</p>'],
            ['heading'=>'Cach mua ban vang SJC',
             'body'=>'<p>Ban co the mua ban vang SJC tai cac cua hang SJC, ngan hang (Vietcombank, BIDV, Eximbank...) va cac dai ly uy quyen tren toan quoc. Can mang theo CMND/CCCD khi giao dich.</p>'],
        ],
    ],
    'nen-mua-vang-nao' => [
        'Nen mua vang nao?',
        ['Cac loai vang pho bien', 'Vang mieng vs Vang nhan', 'Vang trang suc co nen mua?', 'Loi khuyen chon vang'],
        [
            ['heading'=>'Cac loai vang pho bien',
             'body'=>'<p>Tren thi truong Viet Nam co 3 loai vang chinh:</p><ul class="list-disc list-inside space-y-1 mt-1"><li><strong>Vang mieng SJC</strong>: Vang mieng quoc gia, do tinh khiet 99.99%</li><li><strong>Vang nhan 9999</strong>: Vang tron, de giao dich, gia sat the gioi hon</li><li><strong>Vang trang suc</strong>: 18K-24K, co them phi gia cong</li></ul>'],
            ['heading'=>'Vang mieng vs Vang nhan',
             'body'=>'<p><strong>Vang mieng SJC</strong> co gia cao hon do thuong hieu va tinh khan hiem, phu hop tich tru lon. <strong>Vang nhan 9999</strong> co gia gan voi gia quoc te hon, phu hop giao dich linh hoat va dau tu ngan han.</p>'],
            ['heading'=>'Vang trang suc co nen mua?',
             'body'=>'<p>Vang trang suc khong nen mua de dau tu vi khi ban lai se bi tru phi gia cong (thuong 500,000 - 2,000,000 dong/chi). Chi nen mua de su dung va deo.</p>'],
            ['heading'=>'Loi khuyen chon vang',
             'body'=>'<p><strong>Dau tu dai han</strong>: Vang mieng SJC<br><strong>Dau tu linh hoat</strong>: Vang nhan 9999<br><strong>Su dung ca nhan</strong>: Vang trang suc 18K-24K<br>Luon mua tai cua hang uy tin va giu hoa don, chung chi.</p>'],
        ],
    ],
    'cach-dau-tu-vang' => [
        'Cach dau tu vang hieu qua',
        ['Tai sao nen dau tu vang?', 'Cac hinh thuc dau tu vang', 'Chien luoc DCA', 'Nhung sai lam can tranh'],
        [
            ['heading'=>'Tai sao nen dau tu vang?',
             'body'=>'<p>Vang la tai san tru an toan, bao toan gia tri truoc lam phat va bat on kinh te. Trong 20 nam qua, gia vang tang trung binh 8-10%/nam, vuot xa gui tiet kiem.</p>'],
            ['heading'=>'Cac hinh thuc dau tu vang',
             'body'=>'<ul class="list-disc list-inside space-y-1 mt-1"><li><strong>Mua vang vat chat</strong>: Vang mieng, vang nhan - an toan, don gian</li><li><strong>Tai khoan vang</strong>: Mo tai ngan hang, giao dich online</li><li><strong>ETF vang</strong>: Quy ETF theo doi gia vang (SPDR, iShares)</li><li><strong>Huan tien vang</strong>: Mua ban quyen chon tren san quoc te</li></ul>'],
            ['heading'=>'Chien luoc DCA',
             'body'=>'<p><strong>Dollar Cost Averaging (DCA)</strong> la chien luoc mua vang dinh ky voi so tien co dinh (vi du: moi thang mua 1 chi). Giup giam rui ro mua dinh va lay gia trung binh tot trong dai han.</p>'],
            ['heading'=>'Nhung sai lam can tranh',
             'body'=>'<ul class="list-disc list-inside space-y-1 mt-1"><li>Mua duoi khi gia tang nong (FOMO)</li><li>Dau tu toan bo von vao vang</li><li>Khong da dang hoa danh muc</li><li>Mua ban theo tin don, khong phan tich</li><li>Khong tinh phi chenh lech mua-ban</li></ul>'],
        ],
    ],
];
foreach ($articles as $slug => $art) {
    $tocPhp = "['" . implode("','", $art[1]) . "']";
    $secPhp = "[\n";
    foreach ($art[2] as $s) {
        $body = addslashes($s['body']);
        $secPhp .= "        ['heading'=>'{$s['heading']}','body'=>'$body'],\n";
    }
    $secPhp .= "    ]";
    $pages["kien-thuc-vang/$slug"] = pg(
        "@include('gold.sections.knowledge', [\n"
       ."    'articleTitle' => '{$art[0]}',\n"
       ."    'toc'          => $tocPhp,\n"
       ."    'sections'     => $secPhp,\n"
       ."])",
        'Kien thuc',
        "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-news')"
    );
}

// ---- api ----
$pages['api'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">GoldPrice API</h2>
    <p class="text-sm leading-relaxed text-slate-600 mb-4">Truy cap du lieu gia vang theo thoi gian thuc thong qua REST API. Phu hop cho ung dung tai chinh, bot giao dich va bao dien tu.</p>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/api/api-gia-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">API Gia vang</h3>
            <p class="mt-1 text-sm text-slate-500">Endpoints lay du lieu gia vang real-time</p>
        </a>
        <a href="/api/tai-lieu-api" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Tai lieu API</h3>
            <p class="mt-1 text-sm text-slate-500">Huong dan tich hop va vi du code</p>
        </a>
    </div>
    <div class="mt-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <h3 class="font-bold text-sm text-blue-900">Bat dau nhanh</h3>
        <pre class="mt-2 rounded-sm bg-slate-900 p-3 text-xs text-green-400 overflow-x-auto"><code>GET /dashboard-api/snapshot
Content-Type: application/json</code></pre>
    </div>
</div>
BLADE, 'API', "@include('gold.sections.sidebar-tools')");

$apiResponseSample = json_encode([
    'usCard' => ['label'=>'XAU/USD','price'=>'2,918.45','change'=>'+12.30'],
    'sjcCard'=> ['label'=>'SJC 1L','price'=>'92,500,000','change'=>'+500,000'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$apiResponseSample = str_replace("'", "\\'", $apiResponseSample);

$apiPages = [
    'api-gia-vang' => [
        'API Gia vang',
        'Danh sach endpoint lay du lieu gia vang theo thoi gian thuc.',
        [
            [
                'method' => 'GET',
                'path'   => '/dashboard-api/snapshot',
                'desc'   => 'Lay toan bo du lieu gia vang hien tai (trong nuoc + the gioi).',
                'params' => [],
                'response' => $apiResponseSample,
            ],
        ],
    ],
    'tai-lieu-api' => [
        'Tai lieu API',
        'Huong dan tich hop GoldPrice API vao ung dung cua ban.',
        [
            [
                'method' => 'GET',
                'path'   => '/dashboard-api/snapshot',
                'desc'   => 'Endpoint chinh de lay gia vang. Khong can xac thuc. Rate limit: 60 req/min.',
                'params' => [
                    ['name'=>'format','type'=>'string','desc'=>'Dinh dang tra ve: json (mac dinh)'],
                ],
                'response' => "// JavaScript fetch\nconst res = await fetch('/dashboard-api/snapshot');\nconst data = await res.json();\nconsole.log(data.usCard.price);",
            ],
            [
                'method' => 'POST',
                'path'   => '/dashboard-api/subscribe',
                'desc'   => 'Dang ky nhan thong bao gia vang qua email.',
                'params' => [
                    ['name'=>'name','type'=>'string','desc'=>'Ten nguoi dang ky'],
                    ['name'=>'email','type'=>'string','desc'=>'Dia chi email'],
                    ['name'=>'channels','type'=>'array','desc'=>'Kenh nhan tin: email, sms'],
                ],
                'response' => '{"ok": true, "message": "Dang ky thanh cong"}',
            ],
        ],
    ],
];
foreach ($apiPages as $slug => $ap) {
    $epsPhp = "[\n";
    foreach ($ap[2] as $ep) {
        $epsPhp .= "        [\n";
        $epsPhp .= "            'method'   => '{$ep['method']}',\n";
        $epsPhp .= "            'path'     => '{$ep['path']}',\n";
        $epsPhp .= "            'desc'     => '{$ep['desc']}',\n";
        // params
        $epsPhp .= "            'params'   => [\n";
        foreach ($ep['params'] as $p) {
            $epsPhp .= "                ['name'=>'{$p['name']}','type'=>'{$p['type']}','desc'=>'{$p['desc']}'],\n";
        }
        $epsPhp .= "            ],\n";
        // response
        $resp = addslashes($ep['response']);
        $epsPhp .= "            'response' => '$resp',\n";
        $epsPhp .= "        ],\n";
    }
    $epsPhp .= "    ]";
    $pages["api/$slug"] = pg(
        "@include('gold.sections.api-doc', [\n"
       ."    'apiTitle'  => '{$ap[0]}',\n"
       ."    'apiDesc'   => '{$ap[1]}',\n"
       ."    'endpoints' => $epsPhp,\n"
       ."])",
        'API',
        "@include('gold.sections.sidebar-tools')"
    );
}

// ---- static pages ----
$pages['gioi-thieu'] = pg("@include('gold.sections.about')", 'Gioi thieu');
$pages['lien-he']    = pg("@include('gold.sections.contact')", 'Lien he');
$pages['chinh-sach-bao-mat'] = pg("@include('gold.sections.privacy')", 'Chinh sach');
$pages['dieu-khoan-su-dung'] = pg("@include('gold.sections.terms')", 'Phap ly');

// ============================================================
// 4) WRITE PAGE FILES
// ============================================================
$count = 0;
foreach ($pages as $path => $content) {
    $file = "$base/pages/" . str_replace('/', DIRECTORY_SEPARATOR, $path) . '.blade.php';
    w($file, $content);
    $count++;
}

echo "=> $count page files\nDone.\n";
