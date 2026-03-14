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
                <span><strong>GoldPrice</strong><small>Thông tin giá vàng</small></span>
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
            <p class="text-xs uppercase tracking-wide text-slate-500">@yield('page-label', 'Trang chuyên mục')</p>
            <h1 class="mt-2 text-2xl font-bold text-[#001061] md:text-3xl">{{ $title }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $description }}</p>
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

        <section class="mt-4 grid gap-5 lg:grid-cols-[2fr_1fr]">
            <div class="grid gap-5">@yield('page-content')</div>
            <aside class="grid gap-5 content-start">
                @yield('page-sidebar')
                @if (!empty($children))
                <div class="glass-card p-4">
                    <h3 class="text-lg font-bold text-[#001061]">Chuyên mục con</h3>
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
            <span>&copy; 2026 GoldPrice. Mọi quyền được bảo lưu.</span>
            <div>
                <a href="/gioi-thieu">Giới thiệu</a>
                <a href="/lien-he">Liên hệ</a>
                <a href="/chinh-sach-bao-mat">Bảo mật</a>
                <a href="/dieu-khoan-su-dung">Điều khoản</a>
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
        <span class="live-badge"><i></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">Cập nhật liên tục</span>
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
            <thead class="bg-[#f5f5f5]"><tr><th class="p-3 text-left font-semibold">Thương hiệu</th><th class="p-3 text-right font-semibold">Mua vào</th><th class="p-3 text-right font-semibold">Bán ra</th><th class="p-3 text-right font-semibold">Thay đổi</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3 font-medium">SJC 1L</td><td class="p-3 text-right">91,500,000</td><td class="p-3 text-right">92,500,000</td><td class="p-3 text-right font-semibold text-emerald-600">+500,000</td></tr>
                <tr><td class="p-3 font-medium">DOJI 1L</td><td class="p-3 text-right">91,400,000</td><td class="p-3 text-right">92,400,000</td><td class="p-3 text-right font-semibold text-emerald-600">+400,000</td></tr>
                <tr><td class="p-3 font-medium">PNJ 1L</td><td class="p-3 text-right">91,300,000</td><td class="p-3 text-right">92,300,000</td><td class="p-3 text-right font-semibold text-emerald-600">+300,000</td></tr>
                <tr><td class="p-3 font-medium">Phú Quý</td><td class="p-3 text-right">91,200,000</td><td class="p-3 text-right">92,200,000</td><td class="p-3 text-right font-semibold text-emerald-600">+200,000</td></tr>
                <tr><td class="p-3 font-medium">Mỹ Hưng</td><td class="p-3 text-right">91,100,000</td><td class="p-3 text-right">92,100,000</td><td class="p-3 text-right font-semibold text-emerald-600">+100,000</td></tr>
                <tr><td class="p-3 font-medium">Bảo Tín Minh Châu</td><td class="p-3 text-right">91,350,000</td><td class="p-3 text-right">92,350,000</td><td class="p-3 text-right font-semibold text-emerald-600">+350,000</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <h3 class="font-bold text-blue-900">Nhận định thị trường</h3>
        <p class="mt-2 text-sm leading-relaxed text-blue-800">Giá vàng trong nước tăng nhẹ theo đà phục hồi của thị trường quốc tế. Đồng USD suy yếu, lo ngại lạm phát và căng thẳng địa chính trị là các yếu tố hỗ trợ. Dự báo dao động 91.0 – 93.0 triệu/lượng trong phiên hôm nay.</p>
    </div>
</div>
BLADE);

// --- world-price ---
w("$sec/world-price.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center gap-3 mb-4">
        <span class="live-badge"><i></i> Trực tuyến</span>
        <span class="text-sm text-slate-500">Dữ liệu từ thị trường quốc tế</span>
    </div>
    <div class="rounded-sm border border-blue-200 bg-linear-to-br from-blue-50 to-indigo-50 p-5">
        <p class="text-sm font-medium text-blue-800">XAU/USD – Giá vàng thế giới</p>
        <p class="mt-1 text-3xl font-bold text-blue-900">2,918.45 <small class="text-lg font-normal text-blue-700">USD/oz</small></p>
        <p class="mt-2 text-sm font-bold text-emerald-600">▲ 12.30 (0.42%) so với phiên trước</p>
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
            <h4 class="font-semibold text-sm">Yếu tố ảnh hưởng</h4>
            <ul class="mt-2 space-y-1 text-sm leading-relaxed text-slate-600">
                <li>• Chỉ số USD (DXY) giảm 0.3%</li>
                <li>• Lợi suất trái phiếu Mỹ hạ</li>
                <li>• Căng thẳng địa chính trị leo thang</li>
                <li>• Nhu cầu trú ẩn an toàn tăng</li>
            </ul>
        </div>
        <div class="rounded-sm border border-slate-200 p-3">
            <h4 class="font-semibold text-sm">Phiên giao dịch</h4>
            <ul class="mt-2 space-y-1 text-sm leading-relaxed text-slate-600">
                <li>Mở cửa: <strong>2,906.15</strong></li>
                <li>Cao nhất: <strong>2,925.30</strong></li>
                <li>Thấp nhất: <strong>2,901.60</strong></li>
                <li>Khối lượng: <strong>185,420 lots</strong></li>
            </ul>
        </div>
    </div>
</div>
BLADE);

// --- price-table (all brands) ---
w("$sec/price-table.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">Tổng hợp bảng giá vàng hôm nay</h2>
        <span class="chip">Cập nhật: 10:30</span>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr>
                    <th class="p-3 text-left font-semibold">Thương hiệu</th>
                    <th class="p-3 text-right font-semibold">Mua vào (VND/lượng)</th>
                    <th class="p-3 text-right font-semibold">Bán ra (VND/lượng)</th>
                    <th class="p-3 text-right font-semibold">Chênh lệch</th>
                    <th class="p-3 text-right font-semibold">Thay đổi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-sjc" class="text-blue-700 hover:underline">SJC</a></td><td class="p-3 text-right">91,500,000</td><td class="p-3 text-right">92,500,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+500,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-doji" class="text-blue-700 hover:underline">DOJI</a></td><td class="p-3 text-right">91,400,000</td><td class="p-3 text-right">92,400,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+400,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-pnj" class="text-blue-700 hover:underline">PNJ</a></td><td class="p-3 text-right">91,300,000</td><td class="p-3 text-right">92,300,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+300,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-phu-quy" class="text-blue-700 hover:underline">Phú Quý</a></td><td class="p-3 text-right">91,200,000</td><td class="p-3 text-right">92,200,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+200,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-mi-hong" class="text-blue-700 hover:underline">Mỹ Hưng</a></td><td class="p-3 text-right">91,100,000</td><td class="p-3 text-right">92,100,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+100,000</td></tr>
                <tr><td class="p-3 font-medium"><a href="/bang-gia-vang/gia-vang-bao-tin-minh-chau" class="text-blue-700 hover:underline">Bảo Tín Minh Châu</a></td><td class="p-3 text-right">91,350,000</td><td class="p-3 text-right">92,350,000</td><td class="p-3 text-right text-slate-500">1,000,000</td><td class="p-3 text-right font-semibold text-emerald-600">+350,000</td></tr>
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-xs text-slate-400">Giá mang tính tham khảo. Dữ liệu cập nhật từ các thương hiệu chính hãng.</p>
</div>
BLADE);

// --- price-brand ---
w("$sec/price-brand.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">Bảng giá {{ $brandName }} hôm nay</h2>
        <span class="chip">Cập nhật: 10:30</span>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr><th class="p-3 text-left font-semibold">Loại vàng</th><th class="p-3 text-right font-semibold">Mua vào</th><th class="p-3 text-right font-semibold">Bán ra</th><th class="p-3 text-right font-semibold">Thay đổi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3 font-medium">Vàng miếng 1 lượng</td><td class="p-3 text-right">91,500,000</td><td class="p-3 text-right">92,500,000</td><td class="p-3 text-right font-semibold text-emerald-600">+500,000</td></tr>
                <tr><td class="p-3 font-medium">Vàng miếng 5 chỉ</td><td class="p-3 text-right">91,300,000</td><td class="p-3 text-right">92,300,000</td><td class="p-3 text-right font-semibold text-emerald-600">+300,000</td></tr>
                <tr><td class="p-3 font-medium">Vàng miếng 2 chỉ</td><td class="p-3 text-right">91,200,000</td><td class="p-3 text-right">92,200,000</td><td class="p-3 text-right font-semibold text-emerald-600">+200,000</td></tr>
                <tr><td class="p-3 font-medium">Vàng miếng 1 chỉ</td><td class="p-3 text-right">91,100,000</td><td class="p-3 text-right">92,100,000</td><td class="p-3 text-right font-semibold text-emerald-600">+100,000</td></tr>
                <tr><td class="p-3 font-medium">Nhẫn tròn 9999</td><td class="p-3 text-right">82,500,000</td><td class="p-3 text-right">83,600,000</td><td class="p-3 text-right font-semibold text-emerald-600">+400,000</td></tr>
                <tr><td class="p-3 font-medium">Vang 24K</td><td class="p-3 text-right">82,300,000</td><td class="p-3 text-right">83,400,000</td><td class="p-3 text-right font-semibold text-emerald-600">+350,000</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <p class="text-xs text-slate-500 mb-2">Biến động giá {{ $brandName }} trong ngày</p>
        <svg viewBox="0 0 600 100" class="w-full h-24"><polyline fill="none" stroke="#f59e0b" stroke-width="2" points="0,60 60,55 120,50 180,45 240,48 300,42 360,38 420,35 480,33 540,30 600,28"/></svg>
    </div>
    <div class="mt-4 rounded-sm border border-amber-100 bg-amber-50 p-4">
        <h3 class="font-bold text-amber-900">Giới thiệu {{ $brandName }}</h3>
        <p class="mt-2 text-sm leading-relaxed text-amber-800">{{ $brandName }} là một trong những thương hiệu vàng uy tín hàng đầu tại Việt Nam, cung cấp đa dạng sản phẩm vàng miếng, vàng nhẫn và vàng trang sức chất lượng cao. Giá được cập nhật liên tục trong ngày giao dịch.</p>
    </div>
</div>
BLADE);

// --- chart ---
w("$sec/chart.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">Biểu đồ giá vàng {{ $periodLabel }}</h2>
        <div class="flex gap-1">
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-hom-nay" class="chip {{ $period === 'today' ? 'positive' : '' }}">Hôm nay</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-7-ngay" class="chip {{ $period === '7d' ? 'positive' : '' }}">7 ngày</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-30-ngay" class="chip {{ $period === '30d' ? 'positive' : '' }}">30 ngày</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-1-nam" class="chip {{ $period === '1y' ? 'positive' : '' }}">1 năm</a>
            <a href="/bieu-do-gia-vang/bieu-do-gia-vang-10-nam" class="chip {{ $period === '10y' ? 'positive' : '' }}">10 năm</a>
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
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Cao nhất</p><p class="mt-1 text-lg font-bold text-[#001061]">92,800,000</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thấp nhất</p><p class="mt-1 text-lg font-bold text-[#001061]">90,200,000</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Trung bình</p><p class="mt-1 text-lg font-bold text-[#001061]">91,500,000</p></div>
        <div class="rounded-sm border border-slate-200 p-3 text-center"><p class="text-xs text-slate-500">Thay đổi</p><p class="mt-1 text-lg font-bold text-emerald-600">+2.8%</p></div>
    </div>
</div>
BLADE);

// --- comparison ---
w("$sec/comparison.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">So sánh {{ $leftName }} và {{ $rightName }}</h2>
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
        <p class="text-sm font-semibold text-amber-900">Chênh lệch hiện tại</p>
        <p class="mt-1 text-2xl font-bold text-amber-800">{{ $spread }}</p>
        <p class="mt-1 text-xs text-amber-700">{{ $spreadNote }}</p>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]"><tr><th class="p-3 text-left font-semibold">Chỉ tiêu</th><th class="p-3 text-right font-semibold">{{ $leftName }}</th><th class="p-3 text-right font-semibold">{{ $rightName }}</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3">Giá hiện tại</td><td class="p-3 text-right font-medium">{{ $leftPrice }}</td><td class="p-3 text-right font-medium">{{ $rightPrice }}</td></tr>
                <tr><td class="p-3">Tăng/Giảm 24h</td><td class="p-3 text-right">{{ $leftChange }}</td><td class="p-3 text-right">{{ $rightChange }}</td></tr>
                <tr><td class="p-3">Cao nhất 30 ngày</td><td class="p-3 text-right">{{ $leftHigh ?? 'N/A' }}</td><td class="p-3 text-right">{{ $rightHigh ?? 'N/A' }}</td></tr>
                <tr><td class="p-3">Thấp nhất 30 ngày</td><td class="p-3 text-right">{{ $leftLow ?? 'N/A' }}</td><td class="p-3 text-right">{{ $rightLow ?? 'N/A' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
BLADE);

// --- forecast ---
w("$sec/forecast.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Dự báo giá vàng {{ $periodLabel }}</h2>
    <div class="grid gap-5 sm:grid-cols-3 mb-4">
        <div class="rounded-sm border-2 border-emerald-200 bg-emerald-50 p-4 text-center">
            <p class="text-xs font-semibold text-emerald-700 uppercase">Kịch bản tích cực</p>
            <p class="mt-2 text-2xl font-bold text-emerald-800">{{ $bullPrice }}</p>
            <p class="mt-1 text-sm text-emerald-600">{{ $bullChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-blue-200 bg-blue-50 p-4 text-center">
            <p class="text-xs font-semibold text-blue-700 uppercase">Kịch bản cơ sở</p>
            <p class="mt-2 text-2xl font-bold text-blue-800">{{ $basePrice }}</p>
            <p class="mt-1 text-sm text-blue-600">{{ $baseChange }}</p>
        </div>
        <div class="rounded-sm border-2 border-rose-200 bg-rose-50 p-4 text-center">
            <p class="text-xs font-semibold text-rose-700 uppercase">Kịch bản tiêu cực</p>
            <p class="mt-2 text-2xl font-bold text-rose-800">{{ $bearPrice }}</p>
            <p class="mt-1 text-sm text-rose-600">{{ $bearChange }}</p>
        </div>
    </div>
    <div class="rounded-sm border border-slate-200 p-4 mb-4">
        <h3 class="font-bold text-sm mb-3">Các yếu tố quyết định</h3>
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
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">{{ $categoryLabel }}</h2>
        <div class="flex gap-1">
            <a href="/tin-tuc-gia-vang" class="chip {{ $category === 'all' ? 'positive' : '' }}">Tất cả</a>
            <a href="/tin-tuc-gia-vang/trong-nuoc" class="chip {{ $category === 'domestic' ? 'positive' : '' }}">Trong nước</a>
            <a href="/tin-tuc-gia-vang/the-gioi" class="chip {{ $category === 'world' ? 'positive' : '' }}">Thế giới</a>
        </div>
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
    <h2 class="text-lg font-bold text-[#001061] mb-4">Lịch sử giá vàng {{ $yearLabel }}</h2>
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="/lich-su-gia-vang/gia-vang-2026" class="chip {{ $year === '2026' ? 'positive' : '' }}">2026</a>
        <a href="/lich-su-gia-vang/gia-vang-2025" class="chip {{ $year === '2025' ? 'positive' : '' }}">2025</a>
        <a href="/lich-su-gia-vang/gia-vang-2024" class="chip {{ $year === '2024' ? 'positive' : '' }}">2024</a>
        <a href="/lich-su-gia-vang/gia-vang-2023" class="chip {{ $year === '2023' ? 'positive' : '' }}">2023</a>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr><th class="p-3 text-left font-semibold">Tháng</th><th class="p-3 text-right font-semibold">Mở cửa</th><th class="p-3 text-right font-semibold">Cao nhất</th><th class="p-3 text-right font-semibold">Thấp nhất</th><th class="p-3 text-right font-semibold">Đóng cửa</th><th class="p-3 text-right font-semibold">Thay đổi</th></tr>
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
        <p class="text-xs text-slate-500 mb-2">Biểu đồ giá vàng {{ $yearLabel }}</p>
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
        <button class="btn-primary mt-4 w-full py-3" onclick="alert('Tính năng đang phát triển')">{{ $buttonLabel ?? 'Tính toán' }}</button>
    </div>
    <div class="mt-4 rounded-sm border-2 border-dashed border-blue-200 bg-blue-50/50 p-4 text-center">
        <p class="text-sm text-blue-700 font-medium">Kết quả sẽ hiển thị tại đây</p>
        <p class="mt-1 text-3xl font-bold text-blue-900">—</p>
    </div>
    @if (!empty($instructions))
    <div class="mt-4 rounded-sm border border-slate-200 p-4">
        <h3 class="font-bold text-sm mb-2">Hướng dẫn sử dụng</h3>
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
        <p class="text-xs text-slate-500 mb-2">Biểu đồ {{ $marketLabel }} 30 ngày</p>
        <svg viewBox="0 0 600 120" class="w-full h-28"><polyline fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linejoin="round" points="0,90 60,85 120,80 180,75 240,70 300,65 360,60 420,55 480,50 540,48 600,45"/></svg>
    </div>
    <p class="mt-3 text-xs text-slate-400">Dữ liệu mang tính tham khảo, cập nhật từ các sàn giao dịch chính thức.</p>
</div>
BLADE);

// --- knowledge ---
w("$sec/knowledge.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $articleTitle }}</h2>

    @if (!empty($toc))
    <nav class="mb-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <p class="font-semibold text-sm text-blue-900 mb-2">Nội dung chính</p>
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
        <p class="font-bold text-amber-900 text-sm">Lưu ý</p>
        <p class="mt-1 text-sm text-amber-800">Thông tin trên mang tính chất tham khảo và giáo dục. Hãy tham khảo ý kiến chuyên gia trước khi đưa ra quyết định đầu tư.</p>
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
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Tham số</p>
                <div class="table-wrap rounded border border-slate-200">
                    <table class="w-full text-sm">
                        <thead class="bg-[#f5f5f5]"><tr><th class="p-2 text-left font-semibold text-xs">Tên</th><th class="p-2 text-left font-semibold text-xs">Kiểu</th><th class="p-2 text-left font-semibold text-xs">Mô tả</th></tr></thead>
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
    <h2 class="text-lg font-bold text-[#001061] mb-4">Về GoldPrice</h2>
    <div class="text-sm leading-relaxed text-slate-700 space-y-4">
        <p><strong>GoldPrice</strong> là nền tảng cập nhật giá vàng trực tuyến hàng đầu tại Việt Nam, cung cấp thông tin giá vàng SJC, DOJI, PNJ và thế giới (XAU/USD) theo thời gian thực.</p>
        <p>Chúng tôi tổng hợp dữ liệu từ các thương hiệu vàng chính hãng, sàn giao dịch quốc tế và nguồn tin tài chính uy tín để mang đến cho người dùng góc nhìn toàn diện về thị trường vàng.</p>
        <h3 class="text-lg font-bold text-[#001061] mt-6!">Sứ mệnh</h3>
        <p>Giúp người dùng Việt Nam tiếp cận thông tin giá vàng chính xác, nhanh chóng và miễn phí, hỗ trợ đưa ra quyết định đầu tư sáng suốt.</p>
        <h3 class="text-lg font-bold text-[#001061] mt-6!">Tính năng nổi bật</h3>
        <ul class="list-disc list-inside space-y-1">
            <li>Cập nhật giá vàng trong nước và quốc tế theo thời gian thực</li>
            <li>Biểu đồ phân tích xu hướng đa khung thời gian</li>
            <li>So sánh giá giữa các thương hiệu và kênh đầu tư</li>
            <li>Công cụ tính toán và quy đổi vàng tiện lợi</li>
            <li>Tin tức và phân tích chuyên sâu về thị trường vàng</li>
            <li>API dữ liệu cho nhà phát triển</li>
        </ul>
        <h3 class="text-lg font-bold text-[#001061] mt-6!">Liên hệ hợp tác</h3>
        <p>Email: <a href="mailto:giavanghnoffical@gmail.com" class="text-blue-600 hover:underline">giavanghnoffical@gmail.com</a></p>
    </div>
</div>
BLADE);

// --- contact ---
w("$sec/contact.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Liên hệ với chúng tôi</h2>
    <div class="grid gap-5 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Email</h3>
            <p class="mt-1 text-sm text-blue-600">giavanghnoffical@gmail.com</p>
        </div>
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Hotline</h3>
            <p class="mt-1 text-sm text-blue-600">1900 xxxx xx</p>
        </div>
    </div>
    <form class="rounded-sm border border-[#bcbcbc] bg-slate-50 p-4 grid gap-3" onsubmit="event.preventDefault();alert('Cảm ơn bạn đã góp ý!')">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Họ và tên</label>
            <input type="text" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Nguyễn Văn A">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="email@example.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Chủ đề</label>
            <select class="w-full rounded-sm border border-blue-200 bg-white px-3 py-2.5 text-sm">
                <option>Góp ý nội dung</option>
                <option>Hợp tác quảng cáo</option>
                <option>Yêu cầu API</option>
                <option>Báo lỗi</option>
                <option>Khác</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nội dung</label>
            <textarea rows="5" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Viết nội dung tại đây..."></textarea>
        </div>
        <button type="submit" class="btn-primary py-3">Gửi tin nhắn</button>
    </form>
</div>
BLADE);

// --- privacy ---
w("$sec/privacy.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Chính sách bảo mật</h2>
    <p class="text-xs text-slate-400 mb-4">Cập nhật lần cuối: 01/03/2026</p>
    <div class="text-sm leading-relaxed text-slate-700 space-y-4">
        <h3 class="text-lg font-bold text-[#001061]">1. Thu thập thông tin</h3>
        <p>Chúng tôi thu thập thông tin cá nhân khi bạn đăng ký nhận bản tin, sử dụng API hoặc gửi form liên hệ. Thông tin bao gồm: họ tên, email và nội dung tin nhắn.</p>
        <h3 class="text-lg font-bold text-[#001061]">2. Mục đích sử dụng</h3>
        <ul class="list-disc list-inside space-y-1">
            <li>Gửi thông báo cập nhật giá vàng theo đăng ký</li>
            <li>Xử lý yêu cầu hỗ trợ và phản hồi</li>
            <li>Cải thiện chất lượng dịch vụ và trải nghiệm người dùng</li>
        </ul>
        <h3 class="text-lg font-bold text-[#001061]">3. Bảo vệ thông tin</h3>
        <p>Dữ liệu người dùng được mã hoá và lưu trữ an toàn. Chúng tôi không chia sẻ thông tin cá nhân với bên thứ ba ngoài mục đích đã nêu.</p>
        <h3 class="text-lg font-bold text-[#001061]">4. Cookie</h3>
        <p>Website sử dụng cookie để tăng trải nghiệm duyệt web. Bạn có thể tắt cookie trong trình duyệt nhưng một số tính năng có thể bị ảnh hưởng.</p>
        <h3 class="text-lg font-bold text-[#001061]">5. Liên hệ</h3>
        <p>Mọi thắc mắc về chính sách bảo mật, vui lòng gửi email tới <a href="mailto:privacy@goldprice.vn" class="text-blue-600 hover:underline">privacy@goldprice.vn</a>.</p>
    </div>
</div>
BLADE);

// --- terms ---
w("$sec/terms.blade.php", <<<'BLADE'
<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Điều khoản sử dụng</h2>
    <p class="text-xs text-slate-400 mb-4">Cập nhật lần cuối: 01/03/2026</p>
    <div class="text-sm leading-relaxed text-slate-700 space-y-4">
        <h3 class="text-lg font-bold text-[#001061]">1. Chấp nhận điều khoản</h3>
        <p>Khi truy cập và sử dụng GoldPrice, bạn đồng ý tuân thủ các điều khoản dưới đây. Nếu không đồng ý, vui lòng ngừng sử dụng dịch vụ.</p>
        <h3 class="text-lg font-bold text-[#001061]">2. Nội dung và dữ liệu</h3>
        <p>Giá vàng và thông tin trên website mang tính tham khảo, không phải lời khuyên đầu tư. Chúng tôi không chịu trách nhiệm về quyết định đầu tư của bạn dựa trên dữ liệu trên website.</p>
        <h3 class="text-lg font-bold text-[#001061]">3. Tài khoản và API</h3>
        <p>Khi sử dụng API, bạn cam kết không sử dụng quá mức hoặc cho mục đích gây hại. Chúng tôi có quyền giới hạn hoặc đóng tài khoản vi phạm.</p>
        <h3 class="text-lg font-bold text-[#001061]">4. Quyền sở hữu trí tuệ</h3>
        <p>Toàn bộ nội dung, thiết kế và mã nguồn thuộc quyền sở hữu của GoldPrice. Nghiêm cấm sao chép hoặc phân phối mà không được phép.</p>
        <h3 class="text-lg font-bold text-[#001061]">5. Thay đổi điều khoản</h3>
        <p>Chúng tôi có quyền thay đổi điều khoản bất kỳ lúc nào. Người dùng sẽ được thông báo qua email hoặc thông báo trên website.</p>
    </div>
</div>
BLADE);

// --- sidebar-price ---
w("$sec/sidebar-price.blade.php", <<<'BLADE'
<div class="glass-card p-4">
    <h3 class="text-lg font-bold text-[#001061] mb-3">Giá vàng thế giới</h3>
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
    <h3 class="text-lg font-bold text-[#001061] mb-3">Công cụ nổi bật</h3>
    <div class="grid gap-2">
        <a href="/cong-cu/quy-doi-vang" class="block rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm text-blue-700 hover:bg-blue-50">⚖️ Quy đổi vàng</a>
        <a href="/cong-cu/tinh-gia-vang" class="block rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm text-blue-700 hover:bg-blue-50">🧮 Tính giá vàng</a>
        <a href="/cong-cu/tinh-lai-dau-tu-vang" class="block rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-sm text-blue-700 hover:bg-blue-50">📈 Tính lãi đầu tư</a>
    </div>
</div>
BLADE);

// --- sidebar-news ---
w("$sec/sidebar-news.blade.php", <<<'BLADE'
<div class="glass-card p-4">
    <h3 class="text-lg font-bold text-[#001061] mb-3">Tin mới nhất</h3>
    <div class="grid gap-2 text-sm">
        <a href="/tin-tuc-gia-vang/trong-nuoc" class="block text-blue-700 hover:underline">Giá vàng phục hồi mạnh nhờ Fed giữ nguyên lãi suất</a>
        <a href="/tin-tuc-gia-vang/trong-nuoc" class="block text-blue-700 hover:underline">USD suy yếu, vàng thế giới vượt đỉnh cũ</a>
        <a href="/tin-tuc-gia-vang/trong-nuoc" class="block text-blue-700 hover:underline">Lạm phát Eurozone tăng bất ngờ, hỗ trợ giá vàng</a>
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
    'Cập nhật giá vàng',
    "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-tools')"
);

// ---- bang-gia-vang ----
$pages['bang-gia-vang'] = pg(
    "@include('gold.sections.price-table')",
    'Bảng giá',
    "@include('gold.sections.sidebar-price')"
);

$brands = [
    'gia-vang-sjc'              => 'SJC',
    'gia-vang-doji'             => 'DOJI',
    'gia-vang-pnj'              => 'PNJ',
    'gia-vang-phu-quy'          => 'Phú Quý',
    'gia-vang-mi-hong'          => 'Mỹ Hưng',
    'gia-vang-bao-tin-minh-chau'=> 'Bảo Tín Minh Châu',
];
foreach ($brands as $slug => $name) {
    $pages["bang-gia-vang/$slug"] = pg(
        "@include('gold.sections.price-brand', ['brandName' => '$name'])",
        'Bảng giá',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- gia-vang-the-gioi ----
$pages['gia-vang-the-gioi'] = pg(
    "@include('gold.sections.world-price')",
    'Quốc tế',
    "@include('gold.sections.sidebar-price')"
);

// ---- bieu-do-gia-vang ----
$periods = [
    ''                            => ['all',   'tổng hợp'],
    'bieu-do-gia-vang-hom-nay'    => ['today', 'hôm nay'],
    'bieu-do-gia-vang-7-ngay'     => ['7d',    '7 ngày'],
    'bieu-do-gia-vang-30-ngay'    => ['30d',   '30 ngày'],
    'bieu-do-gia-vang-1-nam'      => ['1y',    '1 năm'],
    'bieu-do-gia-vang-10-nam'     => ['10y',   '10 năm'],
];
foreach ($periods as $slug => $info) {
    $path = $slug === '' ? 'bieu-do-gia-vang' : "bieu-do-gia-vang/$slug";
    $pages[$path] = pg(
        "@include('gold.sections.chart', ['period' => '{$info[0]}', 'periodLabel' => '{$info[1]}'])",
        'Biểu đồ',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- so-sanh-gia-vang ----
$pages['so-sanh-gia-vang'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">So sánh giá vàng</h2>
    <div class="grid gap-5">
        <a href="/so-sanh-gia-vang/sjc-vs-the-gioi" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">SJC</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">Thế giới</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Chênh: 18.5 triệu</span>
        </a>
        <a href="/so-sanh-gia-vang/sjc-vs-pnj" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">SJC</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">PNJ</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Chênh: 200,000</span>
        </a>
        <a href="/so-sanh-gia-vang/vang-vs-usd" class="flex items-center justify-between rounded-sm border border-slate-200 p-4 hover:bg-slate-50">
            <div><strong class="text-blue-900">Vàng</strong> <span class="text-slate-400">vs</span> <strong class="text-indigo-900">USD</strong></div>
            <span class="text-sm text-amber-700 font-semibold">Tương quan: 0.85</span>
        </a>
    </div>
</div>
BLADE, 'So sánh', "@include('gold.sections.sidebar-price')");

$comparisons = [
    'sjc-vs-the-gioi' => [
        'SJC', 'Giá thế giới (quy đổi)',
        '92,500,000 VND', '74,000,000 VND',
        '+500,000', '+312,000',
        '18,500,000 VND', 'Chênh lệch do thuế, phí và cung cầu nội địa',
        '93,200,000 VND', '74,800,000 VND',
        '89,500,000 VND', '72,100,000 VND',
    ],
    'sjc-vs-pnj' => [
        'SJC', 'PNJ',
        '92,500,000 VND', '92,300,000 VND',
        '+500,000', '+300,000',
        '200,000 VND', 'Chênh lệch nhỏ do cạnh tranh trực tiếp giữa thương hiệu',
        '93,200,000 VND', '93,000,000 VND',
        '89,500,000 VND', '89,300,000 VND',
    ],
    'vang-vs-usd' => [
        'Vàng (XAU)', 'USD (DXY)',
        '2,918.45 USD', '103.25 pts',
        '+12.30', '-0.32',
        'Tương quan nghịch', 'Khi USD yếu, vàng thường tăng và ngược lại',
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
        'So sánh',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- du-bao-gia-vang ----
$forecasts = [
    '' => [
        'tổng hợp', '93,000,000', '+0.5%', '92,500,000', 'Đi ngang', '91,000,000', '-1.6%',
        'Phân tích tổng hợp các kịch bản dự báo giá vàng từ ngắn hạn đến dài hạn. Dữ liệu từ nhiều nguồn phân tích kỹ thuật và cơ bản.',
    ],
    'du-bao-gia-vang-tuan' => [
        'tuần này', '93,200,000', '+0.8%', '92,800,000', '+0.3%', '91,500,000', '-1.1%',
        'Giá vàng được dự báo tăng nhẹ trong tuần này nhờ kỳ vọng Fed giữ lãi suất ổn định và đồng USD suy yếu. Rủi ro giảm giá đến từ báo cáo việc làm Mỹ tốt hơn kỳ vọng.',
    ],
    'du-bao-gia-vang-thang' => [
        'tháng này', '95,000,000', '+2.7%', '93,500,000', '+1.1%', '90,000,000', '-2.7%',
        'Xu hướng tăng trung hạn được hỗ trợ bởi nhu cầu mua vào của ngân hàng trung ương và lo ngại lạm phát. Kịch bản tiêu cực nếu Fed tăng lãi suất bất ngờ.',
    ],
    'du-bao-gia-vang-2026' => [
        'năm 2026', '100,000,000', '+8.1%', '95,000,000', '+2.7%', '85,000,000', '-8.1%',
        'Năm 2026, giá vàng được kỳ vọng duy trì xu hướng tăng dài hạn nhờ các yếu tố: bất ổn địa chính trị, lạm phát toàn cầu và chính sách tiền tệ nới lỏng. Goldman Sachs dự báo XAU/USD đạt 3,100 USD/oz cuối năm.',
    ],
];
$forecastFactors = [
    ['name'=>'Chính sách Fed','impact'=>'positive','label'=>'Hỗ trợ tăng'],
    ['name'=>'Chỉ số USD (DXY)','impact'=>'positive','label'=>'Đang giảm'],
    ['name'=>'Lạm phát toàn cầu','impact'=>'positive','label'=>'Vẫn cao'],
    ['name'=>'Nhu cầu NHTW','impact'=>'positive','label'=>'Tăng mạnh'],
    ['name'=>'Rủi ro địa chính trị','impact'=>'neutral','label'=>'Trung bình'],
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
        'Dự báo',
        "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-news')"
    );
}

// ---- tin-tuc-gia-vang ----
$newsArticles = [
    ['icon'=>'📊','title'=>'Giá vàng phục hồi mạnh sau phiên giảm sâu','excerpt'=>'Giá vàng SJC tăng 500,000 đồng/lượng trong phiên sáng nay sau khi thị trường quốc tế phát tín hiệu tích cực từ chính sách Fed.','date'=>'07/03/2026'],
    ['icon'=>'💰','title'=>'USD suy yếu đẩy giá vàng thế giới lên đỉnh mới','excerpt'=>'Chỉ số DXY giảm 0.3%, hỗ trợ giá vàng XAU/USD vượt mốc 2,920 USD/oz lần đầu trong tuần.','date'=>'06/03/2026'],
    ['icon'=>'🏦','title'=>'Ngân hàng trung ương các nước tiếp tục mua vàng','excerpt'=>'Theo dữ liệu WGC, quý I/2026, các NHTW mua ròng 280 tấn vàng, tăng 15% so với cùng kỳ năm trước.','date'=>'05/03/2026'],
    ['icon'=>'🌍','title'=>'Căng thẳng địa chính trị đẩy nhu cầu trú ẩn an toàn','excerpt'=>'Tình hình bất ổn ở Trung Đông và biển Đông làm tăng nhu cầu vàng như tài sản trú ẩn an toàn.','date'=>'04/03/2026'],
    ['icon'=>'📈','title'=>'Phân tích kỹ thuật: Vàng hình thành đáy nền tảng','excerpt'=>'Mẫu hình giá hiện tại cho thấy vàng đang tích luỹ trong vùng 2,900-2,930 trước khi có đột phá lớn.','date'=>'03/03/2026'],
];
$newsPhp = "[\n";
foreach ($newsArticles as $a) {
    $newsPhp .= "        ['icon'=>'{$a['icon']}','title'=>'{$a['title']}','excerpt'=>'{$a['excerpt']}','date'=>'{$a['date']}'],\n";
}
$newsPhp .= "    ]";

$newsCategories = [
    ''           => ['all',      'Tin tức giá vàng mới nhất'],
    'trong-nuoc' => ['domestic', 'Tin tức giá vàng trong nước'],
    'the-gioi'   => ['world',   'Tin tức giá vàng thế giới'],
];
foreach ($newsCategories as $slug => $info) {
    $path = $slug === '' ? 'tin-tuc-gia-vang' : "tin-tuc-gia-vang/$slug";
    $pages[$path] = pg(
        "@include('gold.sections.news-list', [\n"
       ."    'category'      => '{$info[0]}',\n"
       ."    'categoryLabel' => '{$info[1]}',\n"
       ."    'articles'      => $newsPhp,\n"
       ."])",
        'Tin tức',
        "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-news')"
    );
}

// ---- lich-su-gia-vang ----
$sampleMonths = [
    ['label'=>'Tháng 1','open'=>'89,000,000','high'=>'90,500,000','low'=>'88,200,000','close'=>'90,000,000','change'=>'+1,000,000'],
    ['label'=>'Tháng 2','open'=>'90,000,000','high'=>'91,800,000','low'=>'89,500,000','close'=>'91,200,000','change'=>'+1,200,000'],
    ['label'=>'Tháng 3','open'=>'91,200,000','high'=>'92,800,000','low'=>'90,800,000','close'=>'92,500,000','change'=>'+1,300,000'],
];
$monthsPhp = "[\n";
foreach ($sampleMonths as $m) {
    $monthsPhp .= "        ['label'=>'{$m['label']}','open'=>'{$m['open']}','high'=>'{$m['high']}','low'=>'{$m['low']}','close'=>'{$m['close']}','change'=>'{$m['change']}'],\n";
}
$monthsPhp .= "    ]";

$historyYears = [
    ''              => ['all',  'tổng hợp'],
    'gia-vang-2026' => ['2026', 'năm 2026'],
    'gia-vang-2025' => ['2025', 'năm 2025'],
    'gia-vang-2024' => ['2024', 'năm 2024'],
    'gia-vang-2023' => ['2023', 'năm 2023'],
];
foreach ($historyYears as $slug => $info) {
    $path = $slug === '' ? 'lich-su-gia-vang' : "lich-su-gia-vang/$slug";
    $pages[$path] = pg(
        "@include('gold.sections.history', [\n"
       ."    'year'      => '{$info[0]}',\n"
       ."    'yearLabel' => '{$info[1]}',\n"
       ."    'months'    => $monthsPhp,\n"
       ."])",
        'Lịch sử',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- cong-cu ----
$pages['cong-cu'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Bộ công cụ giá vàng</h2>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/cong-cu/quy-doi-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⚖️</p>
            <h3 class="font-bold text-slate-900">Quy đổi vàng</h3>
            <p class="mt-1 text-sm text-slate-500">Quy đổi giữa lượng, chỉ, gram, ounce</p>
        </a>
        <a href="/cong-cu/tinh-gia-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🧮</p>
            <h3 class="font-bold text-slate-900">Tính giá vàng</h3>
            <p class="mt-1 text-sm text-slate-500">Tính giá trị vàng theo khối lượng</p>
        </a>
        <a href="/cong-cu/tinh-lai-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">📈</p>
            <h3 class="font-bold text-slate-900">Tính lãi đầu tư vàng</h3>
            <p class="mt-1 text-sm text-slate-500">Tính lợi nhuận, lỗ trên khoản đầu tư</p>
        </a>
        <a href="/cong-cu/doi-vang-sang-usd" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💱</p>
            <h3 class="font-bold text-slate-900">Đổi vàng sang USD</h3>
            <p class="mt-1 text-sm text-slate-500">Quy đổi giá vàng VND sang USD</p>
        </a>
    </div>
</div>
BLADE, 'Công cụ', "@include('gold.sections.sidebar-price')");

// Tool sub-pages
$tools = [
    'quy-doi-vang' => [
        'Quy đổi đơn vị vàng',
        'Chuyển đổi nhanh giữa các đơn vị: lượng, chỉ, gram, ounce troy.',
        [
            ['label'=>'Giá trị','type'=>'number','placeholder'=>'1','default'=>'1'],
            ['label'=>'Từ đơn vị','options'=>['Lượng','Chỉ','Gram','Ounce troy']],
            ['label'=>'Sang đơn vị','options'=>['Gram','Chỉ','Lượng','Ounce troy']],
        ],
        'Quy đổi',
        ['1 lượng = 37.5 gram','1 lượng = 10 chỉ','1 ounce troy = 31.1035 gram','1 lượng = 1.2057 ounce troy'],
    ],
    'tinh-gia-vang' => [
        'Tính giá vàng theo khối lượng',
        'Nhập khối lượng và đơn giá để tính tổng giá trị vàng.',
        [
            ['label'=>'Khối lượng','type'=>'number','placeholder'=>'1','default'=>'1'],
            ['label'=>'Đơn vị','options'=>['Lượng','Chỉ','Gram']],
            ['label'=>'Đơn giá (VND/lượng)','type'=>'number','placeholder'=>'92500000','default'=>'92500000'],
        ],
        'Tính giá',
        ['Nhập khối lượng vàng bạn muốn tính','Chọn đơn vị phù hợp','Nhập đơn giá hiện tại hoặc đơn giá bạn mua','Kết quả = Khối lượng x Đơn giá (quy về lượng)'],
    ],
    'tinh-lai-dau-tu-vang' => [
        'Tính lãi đầu tư vàng',
        'Tính lợi nhuận hoặc lỗ từ khoản đầu tư vàng của bạn.',
        [
            ['label'=>'Giá mua vào (VND/lượng)','type'=>'number','placeholder'=>'85000000','default'=>'85000000'],
            ['label'=>'Giá hiện tại (VND/lượng)','type'=>'number','placeholder'=>'92500000','default'=>'92500000'],
            ['label'=>'Số lượng (lượng)','type'=>'number','placeholder'=>'1','default'=>'1'],
        ],
        'Tính lãi/lỗ',
        ['Nhập giá bạn đã mua vàng','Nhập giá vàng hiện tại','Nhập số lượng lượng đã mua','Lợi nhuận = (Giá hiện tại - Giá mua) x Số lượng'],
    ],
    'doi-vang-sang-usd' => [
        'Đổi giá vàng VND sang USD',
        'Quy đổi giá vàng từ VND sang USD theo tỷ giá hiện tại.',
        [
            ['label'=>'Giá vàng (VND)','type'=>'number','placeholder'=>'92500000','default'=>'92500000'],
            ['label'=>'Tỷ giá USD/VND','type'=>'number','placeholder'=>'25400','default'=>'25400'],
        ],
        'Quy đổi',
        ['Nhập giá vàng tính bằng VND','Nhập tỷ giá USD/VND hiện tại','Kết quả = Giá VND / Tỷ giá','So sánh với giá quốc tế để thấy chênh lệch'],
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
        'Công cụ',
        "@include('gold.sections.sidebar-tools')"
    );
}

// ---- thi-truong ----
$pages['thi-truong'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Tổng quan thị trường</h2>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/thi-truong/gia-xang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">⛽</p>
            <h3 class="font-bold text-slate-900">Giá xăng</h3>
            <p class="mt-1 text-sm text-slate-500">Cập nhật giá xăng dầu trong nước</p>
        </a>
        <a href="/thi-truong/ty-gia-ngoai-te" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">💵</p>
            <h3 class="font-bold text-slate-900">Tỷ giá ngoại tệ</h3>
            <p class="mt-1 text-sm text-slate-500">Tỷ giá các đồng tiền chính</p>
        </a>
        <a href="/thi-truong/gia-bac" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🥈</p>
            <h3 class="font-bold text-slate-900">Giá bạc</h3>
            <p class="mt-1 text-sm text-slate-500">Giá bạc thế giới và trong nước</p>
        </a>
        <a href="/thi-truong/gia-kim-loai" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <p class="text-2xl mb-2">🔩</p>
            <h3 class="font-bold text-slate-900">Giá kim loại</h3>
            <p class="mt-1 text-sm text-slate-500">Bạc, platinum, palladium và đồng</p>
        </a>
    </div>
</div>
BLADE, 'Thị trường', "@include('gold.sections.sidebar-price')");

$markets = [
    'gia-xang' => [
        'Giá xăng dầu hôm nay',
        ['Sản phẩm', 'Giá (VND/lít)', 'Thay đổi'],
        [
            ['RON 95-III', '23,650', '+320'],
            ['RON 95-V', '23,150', '+280'],
            ['E5 RON 92', '22,450', '+250'],
            ['DO 0.05S', '20,980', '+180'],
            ['Dầu hoả', '20,350', '+150'],
        ],
    ],
    'ty-gia-ngoai-te' => [
        'Tỷ giá ngoại tệ hôm nay',
        ['Ngoại tệ', 'Mua TM', 'Mua CK', 'Bán'],
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
        'Giá bạc hôm nay',
        ['Sản phẩm', 'Giá (VND/lượng)', 'Thay đổi'],
        [
            ['Bạc 999 (Thế giới)', '32.45 USD/oz', '+0.28'],
            ['Bạc 999 Việt Nam', '1,050,000', '+15,000'],
            ['Bạc 925 (trang sức)', '890,000', '+12,000'],
            ['Bạc thanh (1kg)', '28,500,000', '+380,000'],
        ],
    ],
    'gia-kim-loai' => [
        'Giá kim loại quý hôm nay',
        ['Kim loại', 'Giá (USD/oz)', 'Thay đổi', '% 30 ngày'],
        [
            ['Vàng (XAU)', '2,918.45', '+12.30', '+2.8%'],
            ['Bạc (XAG)', '32.45', '+0.28', '+3.1%'],
            ['Platinum (XPT)', '985.60', '+5.40', '+1.5%'],
            ['Palladium (XPD)', '975.30', '-8.20', '-2.1%'],
            ['Đồng (HG)', '4.15', '+0.03', '+1.2%'],
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
        'Thị trường',
        "@include('gold.sections.sidebar-price')"
    );
}

// ---- kien-thuc-vang ----
$pages['kien-thuc-vang'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Kiến thức về vàng</h2>
    <div class="grid gap-5">
        <a href="/kien-thuc-vang/vang-9999-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vàng 9999 là gì?</h3>
            <p class="mt-1 text-sm text-slate-500">Tìm hiểu về vàng 4 số 9 và cách phân biệt với các loại vàng khác trên thị trường.</p>
        </a>
        <a href="/kien-thuc-vang/vang-sjc-la-gi" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Vàng SJC là gì?</h3>
            <p class="mt-1 text-sm text-slate-500">Lịch sử hình thành, đặc điểm và vị trí của vàng SJC trong thị trường Việt Nam.</p>
        </a>
        <a href="/kien-thuc-vang/nen-mua-vang-nao" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Nên mua vàng nào?</h3>
            <p class="mt-1 text-sm text-slate-500">So sánh vàng miếng, vàng nhẫn, vàng trang sức để chọn loại phù hợp với mục đích.</p>
        </a>
        <a href="/kien-thuc-vang/cach-dau-tu-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Cách đầu tư vàng hiệu quả</h3>
            <p class="mt-1 text-sm text-slate-500">Hướng dẫn chiến lược đầu tư vàng cho người mới bắt đầu.</p>
        </a>
    </div>
</div>
BLADE, 'Kiến thức', "@include('gold.sections.sidebar-price')");

$articles = [
    'vang-9999-la-gi' => [
        'Vàng 9999 là gì?',
        ['Thế nào là vàng 9999?', 'Đặc điểm của vàng 9999', 'Phân biệt vàng 9999 và các loại khác', 'Giá trị đầu tư của vàng 9999'],
        [
            ['heading'=>'Thế nào là vàng 9999?',
             'body'=>'<p>Vàng 9999 (hay vàng 4 số 9) là loại vàng có độ tinh khiết cao nhất, đạt 99.99% vàng nguyên chất. Đây là tiêu chuẩn cao nhất trong ngành công nghiệp vàng trên toàn thế giới.</p><p class="mt-2">Ở Việt Nam, vàng 9999 được giao dịch phổ biến dưới dạng vàng miếng (SJC, DOJI, PNJ) và vàng nhẫn tròn.</p>'],
            ['heading'=>'Đặc điểm của vàng 9999',
             'body'=>'<ul class="list-disc list-inside space-y-1 mt-1"><li>Độ tinh khiết: 99.99%</li><li>Màu sắc: Vàng đậm, bóng</li><li>Độ mềm: Mềm hơn vàng 18K, 14K</li><li>Không bị oxy hóa hay biến màu</li><li>Dễ dàng kiểm định bằng phương pháp hóa học và điện tử</li></ul>'],
            ['heading'=>'Phân biệt vàng 9999 và các loại khác',
             'body'=>'<p>Vàng 9999 khác với vàng 24K (99.9%), vàng 18K (75%), vàng 14K (58.5%). Độ tinh khiết cao hơn nên vàng 9999 có giá trị đầu tư tốt hơn nhưng ít phù hợp làm trang sức do mềm.</p>'],
            ['heading'=>'Giá trị đầu tư của vàng 9999',
             'body'=>'<p>Vàng 9999 được xem là kênh đầu tư an toàn, bảo toàn giá trị trước lạm phát. Nhà đầu tư thường mua vàng 9999 dưới dạng vàng miếng SJC hoặc vàng nhẫn để tích trữ tài sản dài hạn.</p>'],
        ],
    ],
    'vang-sjc-la-gi' => [
        'Vàng SJC là gì?',
        ['Giới thiệu vàng SJC', 'Lịch sử hình thành', 'Vị trí trên thị trường', 'Cách mua bán vàng SJC'],
        [
            ['heading'=>'Giới thiệu vàng SJC',
             'body'=>'<p>Vàng SJC là thương hiệu vàng miếng do Công ty TNHH MTV Vàng Bạc Đá Quý Sài Gòn (SJC) sản xuất. Đây là thương hiệu vàng được Nhà nước ủy quyền sản xuất độc quyền khối 1 lượng tại Việt Nam.</p>'],
            ['heading'=>'Lịch sử hình thành',
             'body'=>'<p>SJC được thành lập năm 1988, trở thành đơn vị sản xuất vàng miếng lớn nhất Việt Nam. Từ năm 2012, theo Nghị định 24, SJC là thương hiệu vàng miếng quốc gia duy nhất.</p>'],
            ['heading'=>'Vị trí trên thị trường',
             'body'=>'<p>Vàng SJC chiếm thị phần lớn nhất trong giao dịch vàng miếng tại Việt Nam. Giá vàng SJC thường cao hơn các thương hiệu khác và giá quốc tế quy đổi do tính khan hiếm và thương hiệu mạnh.</p>'],
            ['heading'=>'Cách mua bán vàng SJC',
             'body'=>'<p>Bạn có thể mua bán vàng SJC tại các cửa hàng SJC, ngân hàng (Vietcombank, BIDV, Eximbank...) và các đại lý ủy quyền trên toàn quốc. Cần mang theo CMND/CCCD khi giao dịch.</p>'],
        ],
    ],
    'nen-mua-vang-nao' => [
        'Nên mua vàng nào?',
        ['Các loại vàng phổ biến', 'Vàng miếng vs Vàng nhẫn', 'Vàng trang sức có nên mua?', 'Lời khuyên chọn vàng'],
        [
            ['heading'=>'Các loại vàng phổ biến',
             'body'=>'<p>Trên thị trường Việt Nam có 3 loại vàng chính:</p><ul class="list-disc list-inside space-y-1 mt-1"><li><strong>Vàng miếng SJC</strong>: Vàng miếng quốc gia, độ tinh khiết 99.99%</li><li><strong>Vàng nhẫn 9999</strong>: Vàng tròn, dễ giao dịch, giá sát thế giới hơn</li><li><strong>Vàng trang sức</strong>: 18K-24K, có thêm phí gia công</li></ul>'],
            ['heading'=>'Vàng miếng vs Vàng nhẫn',
             'body'=>'<p><strong>Vàng miếng SJC</strong> có giá cao hơn do thương hiệu và tính khan hiếm, phù hợp tích trữ lớn. <strong>Vàng nhẫn 9999</strong> có giá gần với giá quốc tế hơn, phù hợp giao dịch linh hoạt và đầu tư ngắn hạn.</p>'],
            ['heading'=>'Vàng trang sức có nên mua?',
             'body'=>'<p>Vàng trang sức không nên mua để đầu tư vì khi bán lại sẽ bị trừ phí gia công (thường 500,000 - 2,000,000 đồng/chỉ). Chỉ nên mua để sử dụng và đeo.</p>'],
            ['heading'=>'Lời khuyên chọn vàng',
             'body'=>'<p><strong>Đầu tư dài hạn</strong>: Vàng miếng SJC<br><strong>Đầu tư linh hoạt</strong>: Vàng nhẫn 9999<br><strong>Sử dụng cá nhân</strong>: Vàng trang sức 18K-24K<br>Luôn mua tại cửa hàng uy tín và giữ hóa đơn, chứng chỉ.</p>'],
        ],
    ],
    'cach-dau-tu-vang' => [
        'Cách đầu tư vàng hiệu quả',
        ['Tại sao nên đầu tư vàng?', 'Các hình thức đầu tư vàng', 'Chiến lược DCA', 'Những sai lầm cần tránh'],
        [
            ['heading'=>'Tại sao nên đầu tư vàng?',
             'body'=>'<p>Vàng là tài sản trú ẩn an toàn, bảo toàn giá trị trước lạm phát và bất ổn kinh tế. Trong 20 năm qua, giá vàng tăng trung bình 8-10%/năm, vượt xa gửi tiết kiệm.</p>'],
            ['heading'=>'Các hình thức đầu tư vàng',
             'body'=>'<ul class="list-disc list-inside space-y-1 mt-1"><li><strong>Mua vàng vật chất</strong>: Vàng miếng, vàng nhẫn - an toàn, đơn giản</li><li><strong>Tài khoản vàng</strong>: Mở tại ngân hàng, giao dịch online</li><li><strong>ETF vàng</strong>: Quỹ ETF theo dõi giá vàng (SPDR, iShares)</li><li><strong>Huân tiền vàng</strong>: Mua bán quyền chọn trên sàn quốc tế</li></ul>'],
            ['heading'=>'Chiến lược DCA',
             'body'=>'<p><strong>Dollar Cost Averaging (DCA)</strong> là chiến lược mua vàng định kỳ với số tiền cố định (ví dụ: mỗi tháng mua 1 chỉ). Giúp giảm rủi ro mua đỉnh và lấy giá trung bình tốt trong dài hạn.</p>'],
            ['heading'=>'Những sai lầm cần tránh',
             'body'=>'<ul class="list-disc list-inside space-y-1 mt-1"><li>Mua đuổi khi giá tăng nóng (FOMO)</li><li>Đầu tư toàn bộ vốn vào vàng</li><li>Không đa dạng hóa danh mục</li><li>Mua bán theo tin đồn, không phân tích</li><li>Không tính phí chênh lệch mua-bán</li></ul>'],
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
        'Kiến thức',
        "@include('gold.sections.sidebar-price')\n@include('gold.sections.sidebar-news')"
    );
}

// ---- api ----
$pages['api'] = pg(<<<'BLADE'
<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">GoldPrice API</h2>
    <p class="text-sm leading-relaxed text-slate-600 mb-4">Truy cập dữ liệu giá vàng theo thời gian thực thông qua REST API. Phù hợp cho ứng dụng tài chính, bot giao dịch và báo điện tử.</p>
    <div class="grid gap-5 sm:grid-cols-2">
        <a href="/api/api-gia-vang" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">API Giá vàng</h3>
            <p class="mt-1 text-sm text-slate-500">Endpoints lấy dữ liệu giá vàng real-time</p>
        </a>
        <a href="/api/tai-lieu-api" class="rounded-sm border border-slate-200 p-4 hover:bg-slate-50 block">
            <h3 class="font-bold text-slate-900">Tài liệu API</h3>
            <p class="mt-1 text-sm text-slate-500">Hướng dẫn tích hợp và ví dụ code</p>
        </a>
    </div>
    <div class="mt-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <h3 class="font-bold text-sm text-blue-900">Bắt đầu nhanh</h3>
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
        'API Giá vàng',
        'Danh sách endpoint lấy dữ liệu giá vàng theo thời gian thực.',
        [
            [
                'method' => 'GET',
                'path'   => '/dashboard-api/snapshot',
                'desc'   => 'Lấy toàn bộ dữ liệu giá vàng hiện tại (trong nước + thế giới).',
                'params' => [],
                'response' => $apiResponseSample,
            ],
        ],
    ],
    'tai-lieu-api' => [
        'Tài liệu API',
        'Hướng dẫn tích hợp GoldPrice API vào ứng dụng của bạn.',
        [
            [
                'method' => 'GET',
                'path'   => '/dashboard-api/snapshot',
                'desc'   => 'Endpoint chính để lấy giá vàng. Không cần xác thực. Rate limit: 60 req/min.',
                'params' => [
                    ['name'=>'format','type'=>'string','desc'=>'Định dạng trả về: json (mặc định)'],
                ],
                'response' => "// JavaScript fetch\nconst res = await fetch('/dashboard-api/snapshot');\nconst data = await res.json();\nconsole.log(data.usCard.price);",
            ],
            [
                'method' => 'POST',
                'path'   => '/dashboard-api/subscribe',
                'desc'   => 'Đăng ký nhận thông báo giá vàng qua email.',
                'params' => [
                    ['name'=>'name','type'=>'string','desc'=>'Tên người đăng ký'],
                    ['name'=>'email','type'=>'string','desc'=>'Địa chỉ email'],
                    ['name'=>'channels','type'=>'array','desc'=>'Kênh nhận tin: email, sms'],
                ],
                'response' => '{"ok": true, "message": "Đăng ký thành công"}',
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
$pages['gioi-thieu'] = pg("@include('gold.sections.about')", 'Giới thiệu');
$pages['lien-he']    = pg("@include('gold.sections.contact')", 'Liên hệ');
$pages['chinh-sach-bao-mat'] = pg("@include('gold.sections.privacy')", 'Chính sách');
$pages['dieu-khoan-su-dung'] = pg("@include('gold.sections.terms')", 'Pháp lý');

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
