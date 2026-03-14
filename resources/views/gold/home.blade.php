<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giá vàng hôm nay - Cập nhật giá vàng SJC, 9999, PNJ mới nhất | giavanghn.com</title>
    <meta name="description" content="Giá vàng hôm nay cập nhật liên tục: vàng SJC, vàng 9999, PNJ, DOJI và giá vàng thế giới. Xem biểu đồ giá vàng realtime, phân tích xu hướng và dự báo thị trường vàng mới nhất.">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:site_name" content="Giá Vàng Hôm Nay">
    <meta property="og:title" content="Giá vàng hôm nay - Cập nhật giá vàng SJC, 9999, PNJ mới nhất | giavanghn.com">
    <meta property="og:description" content="Giá vàng hôm nay cập nhật liên tục: vàng SJC, vàng 9999, PNJ, DOJI và giá vàng thế giới. Xem biểu đồ giá vàng realtime, phân tích xu hướng và dự báo thị trường vàng mới nhất.">
    <meta property="og:url" content="{{ url('/') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Giá vàng hôm nay - Cập nhật giá vàng SJC, 9999, PNJ mới nhất | giavanghn.com">
    <meta name="twitter:description" content="Giá vàng hôm nay cập nhật liên tục: vàng SJC, vàng 9999, PNJ, DOJI và giá vàng thế giới. Xem biểu đồ giá vàng realtime, phân tích xu hướng và dự báo thị trường vàng mới nhất.">

    <link rel="preload" href="/images/logo.svg" as="image" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
    (function(){var d=false;function l(){if(d)return;d=true;
    ['https://cdn.amcharts.com/lib/5/index.js','https://cdn.amcharts.com/lib/5/xy.js','https://cdn.amcharts.com/lib/5/themes/Animated.js'].forEach(function(u){var s=document.createElement('script');s.src=u;s.async=false;document.head.appendChild(s)});
    }if('requestIdleCallback' in window){requestIdleCallback(l)}else{setTimeout(l,1500)}
    ['scroll','touchstart','mouseover','keydown'].forEach(function(e){document.addEventListener(e,l,{once:true,passive:true})})})();
    </script>
    <script src="https://unpkg.com/lucide@0.477.0" defer></script>

    {{-- Organization + WebSite Schema --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "Giá Vàng Hôm Nay"
        "url": "{{ url('/') }}",
        "description": "Giá vàng hôm nay cập nhật liên tục: vàng SJC, vàng 9999, PNJ, DOJI và giá vàng thế giới. Xem biểu đồ giá vàng realtime, phân tích xu hướng và dự báo thị trường vàng mới nhất."
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "Giá Vàng Hôm Nay"
        "url": "{{ url('/') }}",
        "description": "Giá vàng hôm nay cập nhật liên tục: vàng SJC, vàng 9999, PNJ, DOJI và giá vàng thế giới. Xem biểu đồ giá vàng realtime, phân tích xu hướng và dự báo thị trường vàng mới nhất."
        "inLanguage": "vi"
    }
    </script>
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen" data-updated-at="{{ $updatedAt }}">
    <script id="snapshot-data" type="application/json">@json($snapshot)</script>

    {{-- HEADER --}}
    @include('gold.partials.header')

    {{-- MAIN --}}
    <main class="container-site" id="dashboard">

        {{-- Breadcrumb Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-[#ebebeb] px-3 py-1.5 text-sm text-[#333]"><i data-lucide="home" class="h-4 w-4"></i> Giá vàng hôm nay</span>
                <span class="text-sm text-[#555]">Tổng hợp dữ liệu thị trường, chỉ số kỹ thuật và nhận định bởi chuyên gia trí tuệ nhân tạo</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 text-xs"><i class="block h-2.5 w-2.5 rounded-full bg-[#168307] animate-pulse"></i><span class="text-[#168307]">Dữ liệu real-time</span></span>
                <span class="inline-flex items-center gap-2 text-xs text-[#555]"><i data-lucide="clock" class="h-3 w-3"></i> <span id="liveClock">--:--:--</span></span>
                <span class="inline-flex items-center gap-2 text-xs text-[#555]"><i data-lucide="refresh-cw" class="h-3 w-3"></i> Cập nhật: <span id="updatedAtText">{{ $updatedAt }}</span></span>
                <button id="openSubscribeBtn" class="cursor-pointer rounded-full bg-[#00a63e] px-3 py-1.5 text-sm font-semibold text-white transition hover:brightness-110 inline-flex items-center gap-2"><i data-lucide="bell" class="h-5 w-5"></i> Đăng ký nhận thông tin</button>
            </div>
        </div>

        {{-- Hero: Two-Column Layout --}}
        <section class="flex flex-col gap-5 px-5 pb-5 xl:flex-row">
            {{-- Left Column --}}
            <div class="flex min-w-0 flex-col gap-5 xl:w-3/5">
                {{-- Title + Date Picker --}}
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between" id="historyLookup">
                    <h1 class="m-0 text-xl font-bold text-[#001061] md:text-2xl">Giá vàng hôm nay</h1>
                    <div class="flex items-center gap-2">
                        <label for="historyDatePicker" class="sr-only">Chọn ngày xem giá</label>
                        <div class="inline-flex items-center rounded-sm border border-[#666] pl-3 pr-2 py-2">
                            <input type="date" id="historyDatePicker" class="border-none bg-transparent text-sm text-[#333] focus:outline-none" value="{{ now()->format('Y-m-d') }}" min="2026-01-02" max="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div id="historyLoading" class="hidden"><div class="inline-flex items-center gap-2 text-sm text-[#001061]"><i data-lucide="loader-circle" class="h-5 w-5 animate-spin"></i> Đang tải dữ liệu...</div></div>
                <div id="historyActiveBadge" class="hidden">
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#f4f9ff] px-3 py-1 text-xs font-semibold text-[#001061]">
                        <i data-lucide="calendar-days" class="h-3.5 w-3.5"></i> Đang xem ngày: <span id="historyActiveDate"></span>
                        <button id="historyDismissBtn" class="ml-1 cursor-pointer rounded-full bg-[#001061]/10 px-2 py-0.5 text-xs font-bold text-[#001061] hover:bg-[#001061]/20">✕ Quay lại hôm nay</button>
                    </span>
                </div>
                <div id="historyNoData" class="hidden rounded-sm border border-[#bcbcbc] bg-[#fff5ea] p-4 text-center text-sm text-[#e17100]">Không có dữ liệu cho ngày đã chọn. Dữ liệu lịch sử có từ 07/02/2026.</div>

                {{-- Featured Price Cards --}}
                <div class="flex flex-col gap-5 md:flex-row" id="phan-tich">
                    {{-- SJC Card --}}
                    <article class="flex-1 rounded-sm border border-[#bcbcbc] bg-white p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col gap-3">
                                <h2 class="m-0 text-xl font-bold text-[#333]">{{ $snapshot['sjcCard']['title'] }}</h2>
                                <label>
                                    <select id="sjcVariantSelect" aria-label="Chọn loại vàng SJC" class="rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                                        @foreach ($snapshot['sjcCard']['variants'] as $key => $variant)
                                            <option value="{{ $key }}" @selected($snapshot['sjcCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                            <span id="sjcTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-sm text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['sjcCard']['trendPercent']) }}</span>
                        </div>
                        <div class="flex gap-4 mt-3">
                            <div class="flex flex-col gap-2">
                                <p class="text-2xl font-bold leading-none text-[#001061] md:text-3xl xl:text-[40px]" id="sjcPriceText"></p>
                                <p class="text-base font-medium text-[#001061] md:text-lg xl:text-[22px]" id="sjcUnitText"></p>
                                <p class="mt-1 text-sm text-[#008236] md:text-base" id="sjcDayChangeText"></p>
                                <p class="text-sm font-bold md:text-base" id="sjcBuySellText"></p>
                            </div>
                            <div class="flex flex-1 flex-col items-end justify-center">
                                <svg id="sjcHeroMiniChart" class="h-20 w-full" viewBox="0 0 200 80" preserveAspectRatio="none"></svg>
                                <span class="mt-1 text-xs text-[#555]" id="sjcHeroPointsText"></span>
                            </div>
                        </div>
                        <a href="/gia-vang-hom-nay/gia-vang-sjc" class="mt-4 flex items-center justify-end gap-2">
                            <i data-lucide="external-link" class="h-4 w-4 text-[#155dfc]"></i>
                            <span class="text-base font-medium text-[#155dfc]">Xem chi tiết</span>
                        </a>
                    </article>

                    {{-- US (World Gold) Card --}}
                    <article class="flex-1 rounded-sm border border-[#bcbcbc] bg-white p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col gap-3">
                                <h2 class="m-0 text-xl font-bold text-[#333]">{{ $snapshot['usCard']['title'] }}</h2>
                                <label>
                                    <select id="usVariantSelect" aria-label="Chọn loại vàng thế giới" class="rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                                        @foreach ($snapshot['usCard']['variants'] as $key => $variant)
                                            <option value="{{ $key }}" @selected($snapshot['usCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                            <span id="usTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-sm text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['usCard']['trendPercent']) }}</span>
                        </div>
                        <div class="flex gap-4 mt-3">
                            <div class="flex flex-col gap-2">
                                <p class="text-2xl font-bold leading-none text-[#001061] md:text-3xl xl:text-[40px]" id="usPriceText"></p>
                                <p class="text-base font-medium text-[#001061] md:text-lg xl:text-[22px]" id="usUnitText"></p>
                                <p class="mt-1 text-sm text-[#008236] md:text-base" id="usDayChangeText"></p>
                            </div>
                            <div class="flex flex-1 flex-col items-end justify-center">
                                <svg id="usHeroMiniChart" class="h-20 w-full" viewBox="0 0 200 80" preserveAspectRatio="none"></svg>
                                <span class="mt-1 text-xs text-[#555]" id="usHeroPointsText"></span>
                            </div>
                        </div>
                        <a href="/gia-vang-the-gioi/xau-usd" class="mt-4 flex items-center justify-end gap-2">
                            <i data-lucide="external-link" class="h-4 w-4 text-[#155dfc]"></i>
                            <span class="text-base font-medium text-[#155dfc]">Xem chi tiết</span>
                        </a>
                    </article>
                </div>

                {{-- Analyst Opinion --}}
                @php
                    $ao = $snapshot['analystOpinion'];
                    $aoBias = $ao['bias'] ?? 'neutral';
                    $aoBadgeColor = match($aoBias) {
                        'bullish' => 'bg-[#e2ffde] text-[#168307]',
                        'bearish' => 'bg-[#ffe2e2] text-[#e7000b]',
                        default => 'bg-[#fff5ea] text-[#e17100]',
                    };
                @endphp
                <div class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <i data-lucide="bot" class="h-6 w-6 text-[#001061]"></i>
                        <h3 class="m-0 text-xl font-bold text-[#001061]">Nhận định chuyên gia Ai</h3>
                        <span class="inline-flex items-center rounded-full {{ $aoBadgeColor }} px-2 py-1 text-sm font-semibold">{{ $ao['recommendation'] }}</span>
                        @if(isset($ao['compositeScore']))
                            <span class="text-sm text-[#333]">Điểm tổng hợp: <strong class="{{ $ao['compositeScore'] >= 58 ? 'text-[#168307]' : ($ao['compositeScore'] >= 45 ? 'text-[#e17100]' : 'text-[#e7000b]') }}">{{ $ao['compositeScore'] }}/100</strong></span>
                        @endif
                    </div>
                    <div class="mt-4 rounded-sm bg-[#f4f9ff] p-5">
                        <p class="m-0 text-base font-medium leading-relaxed text-[#333]">{{ $ao['summary'] }}</p>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm md:text-base">
                        <span class="text-[#008236]">Mục tiêu: <strong>{{ $ao['target'] }}</strong></span>
                        <span class="hidden h-5 w-px bg-[#bcbcbc] md:block"></span>
                        <span class="text-[#e7000b]">Cắt lỗ: <strong>{{ $ao['stopLoss'] }}</strong></span>
                        <span class="hidden h-5 w-px bg-[#bcbcbc] md:block"></span>
                        <span class="text-[#193cb8]">R/R: <strong>{{ $ao['riskReward'] }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Right Column: Chart --}}
            <div class="min-w-0 self-stretch rounded-sm border border-[#bcbcbc] bg-white p-5 flex flex-col gap-5 xl:w-2/5" id="bieu-do-sjc-1y">
                <div>
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="chart-no-axes-combined" class="h-6 w-6 text-[#333]"></i>
                        <h3 class="m-0 text-xl font-bold text-[#333]">Biểu đồ giá vàng SJC</h3>
                    </div>
                    <p class="mt-3 text-sm text-[#666]">Dữ liệu lịch sử mua vào và bán ra SJC phục vụ theo dõi xu hướng trung hạn.</p>
                    <div class="mt-3 flex flex-wrap gap-2" id="sjcChartPeriodButtons">
                        <button data-period="7d" class="sjc-period-btn rounded-full border border-[#ccc] bg-white px-3 py-1 text-xs font-medium text-[#555] transition hover:border-[#b8860b] hover:text-[#b8860b]">7 ngày</button>
                        <button data-period="1m" class="sjc-period-btn rounded-full border border-[#ccc] bg-white px-3 py-1 text-xs font-medium text-[#555] transition hover:border-[#b8860b] hover:text-[#b8860b]">1 tháng</button>
                        <button data-period="3m" class="sjc-period-btn rounded-full border border-[#ccc] bg-white px-3 py-1 text-xs font-medium text-[#555] transition hover:border-[#b8860b] hover:text-[#b8860b]">3 tháng</button>
                        <button data-period="6m" class="sjc-period-btn rounded-full border border-[#ccc] bg-white px-3 py-1 text-xs font-medium text-[#555] transition hover:border-[#b8860b] hover:text-[#b8860b]">6 tháng</button>
                        <button data-period="1y" class="sjc-period-btn rounded-full border border-[#001061] bg-[#001061] px-3 py-1 text-xs font-medium text-white">1 năm</button>
                        <button data-period="all" class="sjc-period-btn rounded-full border border-[#ccc] bg-white px-3 py-1 text-xs font-medium text-[#555] transition hover:border-[#b8860b] hover:text-[#b8860b]">Tất cả</button>
                    </div>
                </div>
                <div id="sjcYearAmChart" class="flex-1 min-h-[300px] w-full overflow-hidden md:min-h-[400px]"></div>
            </div>
        </section>

        {{-- Stat Cards — 6 cols --}}
        <section class="grid gap-5 px-5 pb-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6" id="statsGrid">
            @foreach ($snapshot['statCards'] as $card)
                <article class="rounded-sm border border-[#bcbcbc] bg-white p-4 shadow-lg">
                    <h3 class="m-0 text-xs font-medium text-[#555] uppercase tracking-wide">{{ $card['title'] }}</h3>
                    <p class="mt-2 text-2xl font-bold">{{ $card['value'] }}</p>
                    <p class="mt-1 text-xs text-[#555]">{{ $card['unit'] }}</p>
                    <p class="mt-2 text-sm font-bold {{ $card['trend'] === 'down' ? 'text-[#e7000b]' : ($card['trend'] === 'up' ? 'text-[#008236]' : 'text-[#666]') }}">{{ $card['delta'] }}</p>
                </article>
            @endforeach
        </section>
        <section class="grid gap-5 px-5 pb-5 lg:grid-cols-3 lg:grid-rows-[1fr]">
            {{-- Bản tin cập nhật giá vàng --}}
            <div id="ban-tin-gia-vang" class="flex min-h-0">
                <div class="flex flex-1 flex-col rounded-sm border border-[#bcbcbc] bg-white p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="radio" class="h-5 w-5 text-[#e7000b]"></i>
                            <h3 class="m-0 text-lg font-bold text-[#333]">Bản tin cập nhật giá vàng</h3>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#e2ffde] px-2 py-0.5 text-[11px] font-semibold text-[#168307]">
                                <span class="block h-1.5 w-1.5 rounded-full bg-[#168307] animate-pulse"></span> LIVE
                            </span>
                        </div>
                        <span class="text-xs text-[#999]" id="priceFeedUpdatedAt">Cập nhật lúc {{ now()->format('H:i') }}</span>
                    </div>
                    <div class="flex-1 overflow-y-auto max-h-[480px]" id="priceFeedList">
                        @forelse ($snapshot['priceFeed'] as $item)
                            <div class="flex items-center gap-3 border-b border-[#f0f0f0] py-2.5 last:border-0 text-sm">
                                <span class="shrink-0 w-12 font-mono text-xs text-[#999]">{{ $item['time'] }}</span>
                                <span class="shrink-0 font-semibold text-[#001061]">{{ $item['source'] }}</span>
                                <span class="flex-1 truncate text-[#555]">{{ $item['brand'] }}</span>
                                <span class="shrink-0 font-semibold text-[#333]">{{ number_format($item['sell'] / 1_000_000, 2) }}tr</span>
                                <span class="shrink-0 w-20 text-right font-semibold {{ $item['change'] >= 0 ? 'text-[#168307]' : 'text-[#e7000b]' }}">
                                    {{ $item['change'] >= 0 ? '+' : '' }}{{ number_format($item['change'] / 1000) }}k
                                    ({{ $item['changePct'] >= 0 ? '+' : '' }}{{ number_format($item['changePct'], 2) }}%)
                                </span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-sm text-[#999]" id="priceFeedEmpty">Chưa có biến động giá hôm nay. Giá sẽ tự động cập nhật khi có thay đổi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            {{-- TradingView World Gold Prices --}}
            <div class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-4 flex items-center gap-2.5">
                    <i data-lucide="globe" class="h-5 w-5 text-[#b8860b]"></i>
                    <h3 class="m-0 text-lg font-bold text-[#333]">Giá vàng thế giới trực tiếp</h3>
                </div>
                <div class="grid gap-5 lg:grid-rows-3">
                    {{-- XAU/USD --}}
                    <div class="rounded border border-slate-200 overflow-hidden">
                        <div class="tradingview-widget-container" style="height:350px">
                            <div class="tradingview-widget-container__widget" style="height:100%;width:100%"></div>
                            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
                            {
                                "symbol": "OANDA:XAUUSD",
                                "width": "100%",
                                "height": "100%",
                                "locale": "vi_VN",
                                "dateRange": "1M",
                                "colorTheme": "light",
                                "isTransparent": true,
                                "autosize": true,
                                "largeChartUrl": ""
                            }
                            </script>
                        </div>
                    </div>
                    {{-- XAG/USD --}}
                    <div class="rounded border border-slate-200 overflow-hidden">
                        <div class="tradingview-widget-container" style="height:350px">
                            <div class="tradingview-widget-container__widget" style="height:100%;width:100%"></div>
                            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
                            {
                                "symbol": "OANDA:XAGUSD",
                                "width": "100%",
                                "height": "100%",
                                "locale": "vi_VN",
                                "dateRange": "1M",
                                "colorTheme": "light",
                                "isTransparent": true,
                                "autosize": true,
                                "largeChartUrl": ""
                            }
                            </script>
                        </div>
                    </div>
                    {{-- XAU/EUR --}}
                    <div class="rounded border border-slate-200 overflow-hidden">
                        <div class="tradingview-widget-container" style="height:350px">
                            <div class="tradingview-widget-container__widget" style="height:100%;width:100%"></div>
                            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
                            {
                                "symbol": "OANDA:XAUEUR",
                                "width": "100%",
                                "height": "100%",
                                "locale": "vi_VN",
                                "dateRange": "1M",
                                "colorTheme": "light",
                                "isTransparent": true,
                                "autosize": true,
                                "largeChartUrl": ""
                            }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            {{-- News --}}
            <div class="rounded-sm border border-[#bcbcbc] bg-white p-5" id="tin-van">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="m-0 flex items-center gap-2 text-lg font-bold"><i data-lucide="newspaper" class="h-5 w-5 text-[#333]"></i> Tin Vắn Tài Chính & Phân Tích</h3>
                    <a href="/tin-tuc-gia-vang/trong-nuoc" class="inline-flex items-center gap-1 rounded-full border border-[#b8860b] px-3 py-1 text-xs font-semibold text-[#b8860b] transition hover:bg-[#b8860b] hover:text-white">Xem thêm <i data-lucide="arrow-right" class="h-3 w-3"></i></a>
                </div>
                <div id="newsList" class="grid gap-3">
                    @forelse ($snapshot['news'] as $news)
                        @php
                            $isExternal = !empty($news['url']) && !str_starts_with($news['url'], '/');
                            $emoji = match($news['impact'] ?? 'neutral') {
                                'positive' => '📈',
                                'negative' => '📉',
                                default => '📰',
                            };
                        @endphp
                        <article class="flex items-start gap-3 bg-white">
                            @if (!empty($news['image_url']))
                                <a href="{{ $news['url'] ?? '#' }}"@if($isExternal) target="_blank" rel="noopener"@endif class="shrink-0">
                                    <img src="{{ $news['image_url'] }}" alt="{{ $news['title'] }}" class="shrink-0 w-16 h-16 rounded-sm object-cover" width="64" height="64" loading="lazy">
                                </a>
                            @else
                                <div class="shrink-0 w-16 h-16 rounded-sm bg-linear-to-br from-slate-100 to-slate-200 grid place-items-center text-xl">{{ $emoji }}</div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-slate-900 m-0">
                                    @if (!empty($news['url']))
                                        <a href="{{ $news['url'] }}"@if($isExternal) target="_blank" rel="noopener"@endif class="hover:text-[#b8860b] transition">{{ $news['title'] }}</a>
                                    @else
                                        {{ $news['title'] }}
                                    @endif
                                </h3>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span>{{ $news['time'] }}</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">{{ $news['tag'] }}</span>
                                    @if (!empty($news['source']))
                                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] text-blue-600">{{ strtoupper($news['source']) }}</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-sm border border-amber-200 bg-amber-50 px-4 py-6 text-center text-sm text-amber-700">
                            Chưa có dữ liệu tin tức. Hệ thống đang tự động thu thập và sẽ hiển thị ngay khi có bản ghi mới.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Insights: Sentiment + Technical + Forecast --}}
        <section class="grid gap-5 px-5 pb-5 lg:grid-cols-3" id="bieu-do">
            @php
                $s = $snapshot['sentiment'];
                $fgColor = match(true) {
                    $s['fearGreedIndex'] >= 80 => ['text-rose-600', 'border-rose-200', 'bg-rose-50'],
                    $s['fearGreedIndex'] >= 60 => ['text-orange-600', 'border-orange-200', 'bg-orange-50'],
                    $s['fearGreedIndex'] >= 40 => ['text-yellow-600', 'border-yellow-200', 'bg-yellow-50'],
                    $s['fearGreedIndex'] >= 20 => ['text-emerald-600', 'border-emerald-200', 'bg-emerald-50'],
                    default => ['text-green-700', 'border-green-200', 'bg-green-50'],
                };
                $trendColor = match($s['trendDirection']) {
                    'up' => ['text-emerald-700', 'border-emerald-200', 'bg-emerald-50'],
                    'down' => ['text-rose-600', 'border-rose-200', 'bg-rose-50'],
                    default => ['text-yellow-600', 'border-yellow-200', 'bg-yellow-50'],
                };
            @endphp
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-4 flex items-center gap-2 text-lg font-bold">
                    <i data-lucide="brain" class="h-5 w-5 text-[#001061]"></i>
                    Tâm lý thị trường
                </h3>

                {{-- Fear & Greed Index --}}
                <div class="mb-5">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm font-semibold text-[#333]">Chỉ số Tham lam &amp; Sợ hãi</span>
                        <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold {{ implode(' ', $fgColor) }}">{{ $s['fearGreedLabel'] }}</span>
                    </div>
                    <div class="relative h-5 rounded-full" style="background: linear-gradient(to right, #22c55e, #84cc16, #eab308, #f97316, #ef4444)">
                        <div class="absolute top-1/2 -translate-x-1/2 -translate-y-1/2 h-7 w-2 rounded-sm bg-white ring-2 ring-slate-600 shadow" style="left: {{ $s['fearGreedIndex'] }}%"></div>
                    </div>
                    <div class="mt-1.5 flex justify-between text-xs text-[#555]">
                        <span>0 - Sợ hãi</span>
                        <span>50</span>
                        <span>100 - Tham lam</span>
                    </div>
                </div>

                {{-- Sentiment Breakdown --}}
                <div class="space-y-3">
                    {{-- Tích cực (Mua) --}}
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="trending-up" class="h-4 w-4 text-emerald-500"></i>
                                <span class="font-semibold">Tích cực (Mua)</span>
                            </span>
                            <strong class="text-emerald-600">{{ $s['buyPercent'] }}%</strong>
                        </div>
                        <div class="mt-1 h-2.5 rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ $s['buyPercent'] }}%"></div>
                        </div>
                    </div>
                    {{-- Trung lập --}}
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="minus" class="h-4 w-4 text-blue-500"></i>
                                <span class="font-semibold">Trung lập</span>
                            </span>
                            <strong class="text-blue-600">{{ $s['neutralPercent'] }}%</strong>
                        </div>
                        <div class="mt-1 h-2.5 rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-blue-500 transition-all" style="width: {{ $s['neutralPercent'] }}%"></div>
                        </div>
                    </div>
                    {{-- Tiêu cực (Bán) --}}
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="trending-down" class="h-4 w-4 text-rose-500"></i>
                                <span class="font-semibold">Tiêu cực (Bán)</span>
                            </span>
                            <strong class="text-rose-600">{{ $s['sellPercent'] }}%</strong>
                        </div>
                        <div class="mt-1 h-2.5 rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-rose-500 transition-all" style="width: {{ $s['sellPercent'] }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Xu hướng chung --}}
                <div class="mt-4 flex items-center justify-between border-t border-[#ebebeb] pt-3">
                    <span class="text-sm font-semibold text-[#333]">Xu hướng chung</span>
                    <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-xs font-bold {{ implode(' ', $trendColor) }}">
                        @if($s['trendDirection'] === 'up')
                            <i data-lucide="chevron-up" class="h-3.5 w-3.5"></i>
                        @elseif($s['trendDirection'] === 'down')
                            <i data-lucide="chevron-down" class="h-3.5 w-3.5"></i>
                        @else
                            <i data-lucide="minus" class="h-3.5 w-3.5"></i>
                        @endif
                        {{ $s['trendLabel'] }}
                    </span>
                </div>

                {{-- Chú thích cách tính --}}
                @if(isset($s['scores']))
                <details class="mt-4 group">
                    <summary class="flex cursor-pointer items-center gap-1.5 text-xs font-medium text-[#555] hover:text-[#333] transition-colors select-none">
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5 transition-transform group-open:rotate-90"></i>
                        <i data-lucide="info" class="h-3 w-3"></i>
                        Cách tính chỉ số
                    </summary>
                    <div class="mt-3 space-y-2.5 rounded-sm bg-[#f5f5f5] p-3 text-xs text-[#555]">
                        @php
                            $scores = $s['scores'];
                            $components = [
                                ['label' => 'Xu hướng giá XAU/USD', 'desc' => 'So sánh giá hiện tại với SMA-7 & SMA-30', 'weight' => 40, 'score' => $scores['priceTrend'], 'color' => 'bg-amber-400'],
                                ['label' => 'Đồng thuận nội địa', 'desc' => '8 thương hiệu vàng tăng hay giảm', 'weight' => 25, 'score' => $scores['consensus'], 'color' => 'bg-blue-400'],
                                ['label' => 'Động lượng (Momentum)', 'desc' => 'Tốc độ & hướng biến động giá', 'weight' => 20, 'score' => $scores['momentum'], 'color' => 'bg-violet-400'],
                                ['label' => 'Biên độ mua-bán', 'desc' => 'Spread hẹp = thị trường ổn định', 'weight' => 15, 'score' => $scores['spread'], 'color' => 'bg-teal-400'],
                            ];
                        @endphp
                        @foreach($components as $c)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block h-2 w-2 rounded-full {{ $c['color'] }}"></span>
                                    <span class="font-semibold text-[#333]">{{ $c['label'] }}</span>
                                    <span class="text-[#666]">({{ $c['weight'] }}%)</span>
                                </div>
                                <span class="font-bold {{ $c['score'] >= 60 ? 'text-emerald-600' : ($c['score'] >= 40 ? 'text-yellow-600' : 'text-rose-600') }}">{{ number_format($c['score'], 1) }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-[#ebebeb]">
                                <div class="h-full rounded-full {{ $c['color'] }} transition-all" style="width: {{ min($c['score'], 100) }}%"></div>
                            </div>
                            <p class="mt-0.5 text-[10px] text-[#666] leading-tight">{{ $c['desc'] }}</p>
                        </div>
                        @endforeach
                        <p class="mt-2 border-t border-[#ebebeb] pt-2 text-[10px] text-[#666] leading-relaxed">
                            <strong class="text-[#333]">Công thức:</strong> Chỉ số = Xu hướng×40% + Đồng thuận×25% + Động lượng×20% + Biên độ×15%. Dữ liệu cập nhật mỗi 30 phút từ giá XAU/USD quốc tế và 8 thương hiệu vàng trong nước.
                        </p>
                    </div>
                </details>
                @endif
            </article>

            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="bar-chart-3" class="h-5 w-5 text-[#001061]"></i> Chỉ số kỹ thuật</h3>
                <ul class="m-0 grid list-none gap-1 p-0">
                    @foreach ($snapshot['technical'] as $item)
                        @php
                            $signalColor = match($item['signal'] ?? 'neutral') {
                                'buy' => 'text-[#008236]',
                                'sell' => 'text-[#e7000b]',
                                default => 'text-[#666]',
                            };
                            $signalBg = match($item['signal'] ?? 'neutral') {
                                'buy' => 'bg-[#e2ffde]',
                                'sell' => 'bg-[#fff5ea]',
                                default => 'bg-[#f5f5f5]',
                            };
                        @endphp
                        <li class="flex justify-between items-center border-b border-dashed border-[#bcbcbc] py-1.5 text-sm {{ $signalBg }} px-2 rounded-sm">
                            <span>{{ $item['name'] }}</span>
                            <strong class="{{ $signalColor }}">{{ $item['value'] }}</strong>
                        </li>
                    @endforeach
                </ul>
            </article>

            {{-- Forecast --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="target" class="h-5 w-5 text-[#e17100]"></i> Dự báo giá vàng SJC</h3>
                <div class="grid gap-3">
                    @foreach ($snapshot['forecast'] as $fc)
                        <div class="rounded-sm border border-[#ebebeb] bg-[#f5f5f5] p-3">
                            <div class="flex items-baseline justify-between">
                                <strong class="text-sm">{{ $fc['period'] }}</strong>
                                <span class="text-xs text-[#555]">Độ tin cậy: {{ $fc['confidence'] }}%</span>
                            </div>
                            <p class="mt-1 text-lg font-bold text-[#001061]">{{ $fc['range'] }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <div class="h-1.5 flex-1 rounded-full bg-[#ebebeb]">
                                    <div class="h-full rounded-full bg-[#193cb8]" style="width: {{ $fc['confidence'] }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-[#008236]">{{ $fc['bias'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        {{-- Remaining Brand Cards --}}
        <section class="grid gap-5 px-5 pb-5 md:grid-cols-2 xl:grid-cols-4">
            {{-- SJC Brand Card --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['sjcCard']['title'] }}</h2>
                    <span id="sjcBrandTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['sjcCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="sjcBrandVariantSelect" aria-label="Chọn loại vàng SJC" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['sjcCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['sjcCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="sjcBrandPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="sjcBrandUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="sjcBrandBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="sjcBrandDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="sjcBrandPointsText"></span>
                    </div>
                    <svg id="sjcBrandMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-sjc" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>

            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['btmcCard']['title'] }}</h2>
                    <span id="btmcTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['btmcCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="btmcVariantSelect" aria-label="Chọn loại vàng Bảo Tín Minh Châu" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['btmcCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['btmcCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="btmcPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="btmcUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="btmcBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="btmcDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="btmcPointsText"></span>
                    </div>
                    <svg id="btmcMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-bao-tin-minh-chau" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>

            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['pnjCard']['title'] }}</h2>
                    <span id="pnjTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['pnjCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="pnjVariantSelect" aria-label="Chọn loại vàng PNJ" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['pnjCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['pnjCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="pnjPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="pnjUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="pnjBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="pnjDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="pnjPointsText"></span>
                    </div>
                    <svg id="pnjMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-pnj" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>

            {{-- DOJI Card --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['dojiCard']['title'] }}</h2>
                    <span id="dojiTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['dojiCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="dojiVariantSelect" aria-label="Chọn loại vàng DOJI" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['dojiCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['dojiCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="dojiPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="dojiUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="dojiBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="dojiDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="dojiPointsText"></span>
                    </div>
                    <svg id="dojiMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-doji" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>

            {{-- Phú Quý Card --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['phuquyCard']['title'] }}</h2>
                    <span id="phuquyTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['phuquyCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="phuquyVariantSelect" aria-label="Chọn loại vàng Phú Quý" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['phuquyCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['phuquyCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="phuquyPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="phuquyUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="phuquyBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="phuquyDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="phuquyPointsText"></span>
                    </div>
                    <svg id="phuquyMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-phu-quy" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>

            {{-- Mi Hồng Card --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['mihongCard']['title'] }}</h2>
                    <span id="mihongTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['mihongCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="mihongVariantSelect" aria-label="Chọn loại vàng Mi Hồng" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['mihongCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['mihongCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="mihongPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="mihongUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="mihongBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="mihongDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="mihongPointsText"></span>
                    </div>
                    <svg id="mihongMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-mi-hong" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>

            {{-- Bảo Tín Mạnh Hải Card --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['btmhCard']['title'] }}</h2>
                    <span id="btmhTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['btmhCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="btmhVariantSelect" aria-label="Chọn loại vàng Bảo Tín Mạnh Hải" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['btmhCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['btmhCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="btmhPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="btmhUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="btmhBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="btmhDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="btmhPointsText"></span>
                    </div>
                    <svg id="btmhMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-bao-tin-manh-hai" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>

            {{-- Ngọc Thẩm Card --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="m-0 text-lg font-bold">{{ $snapshot['ngocthamCard']['title'] }}</h2>
                    <span id="ngocthamTrendPercent" class="inline-flex items-center rounded-full bg-[#e2ffde] px-2 py-1 text-xs font-semibold text-[#168307]">{{ sprintf('%+.2f%%', $snapshot['ngocthamCard']['trendPercent']) }}</span>
                </div>
                <label>
                    <select id="ngocthamVariantSelect" aria-label="Chọn loại vàng Ngọc Thẩm" class="w-full rounded-sm border border-[#666] bg-white px-3 py-2 text-sm text-[#333]">
                        @foreach ($snapshot['ngocthamCard']['variants'] as $key => $variant)
                            <option value="{{ $key }}" @selected($snapshot['ngocthamCard']['selected'] === $key)>{{ $variant['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="mt-3 text-4xl font-bold leading-none text-[#001061] max-sm:text-3xl" id="ngocthamPriceText"></p>
                <p class="mt-1 text-sm text-[#001061]" id="ngocthamUnitText"></p>
                <p class="mt-1 text-sm text-[#666]" id="ngocthamBuySellText"></p>
                <p class="mt-2 font-bold text-[#008236]" id="ngocthamDayChangeText"></p>
                <div class="mt-3 rounded-sm border border-[#bcbcbc] bg-white/70 p-3">
                    <div class="mb-2 flex justify-between text-xs text-[#555]">
                        <span>Biến động 7 ngày</span>
                        <span id="ngocthamPointsText"></span>
                    </div>
                    <svg id="ngocthamMiniChart" class="h-14 w-full" viewBox="0 0 500 80" preserveAspectRatio="none"></svg>
                </div>
                <a href="/gia-vang-hom-nay/gia-vang-ngoc-tham" class="mt-3 block w-full rounded-sm border border-[#bcbcbc] bg-white px-3 py-2 text-center text-sm font-semibold text-[#155dfc] transition hover:bg-gray-50">Xem chi tiết →</a>
            </article>
        </section>

        {{-- GLOBAL MARKETS + SUPPORT/RESISTANCE --}}
        <section class="grid gap-5 px-5 pb-5 lg:grid-cols-2">
            {{-- Global Markets --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="globe" class="h-5 w-5 text-[#001061]"></i> Thị trường kim loại quý toàn cầu</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="border-b border-[#bcbcbc] pb-2 font-medium text-[#666]">Thị trường</th>
                                <th class="border-b border-[#bcbcbc] pb-2 text-right font-medium text-[#666]">Giá</th>
                                <th class="border-b border-[#bcbcbc] pb-2 text-right font-medium text-[#666]">Thay đổi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($snapshot['globalMarkets'] as $market)
                                <tr>
                                    <td class="border-b border-[#ebebeb] py-2 font-medium">{{ $market['name'] }}</td>
                                    <td class="border-b border-[#ebebeb] py-2 text-right font-bold">{{ $market['price'] }}</td>
                                    <td class="border-b border-[#ebebeb] py-2 text-right font-bold {{ $market['trend'] === 'up' ? 'text-[#008236]' : 'text-[#e7000b]' }}">{{ $market['change'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>

            {{-- Support / Resistance --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="layers" class="h-5 w-5 text-[#001061]"></i> Hỗ trợ & Kháng cự SJC</h3>
                <div class="grid gap-2">
                    @foreach ($snapshot['supports'] as $level)
                        @php $isSupport = str_starts_with($level['level'], 'Hỗ trợ'); @endphp
                        <div class="flex items-center justify-between rounded-sm border {{ $isSupport ? 'border-[#168307] bg-[#e2ffde]' : 'border-[#e7000b] bg-[#fff5ea]' }} px-4 py-2">
                            <div>
                                <strong class="block text-sm {{ $isSupport ? 'text-[#168307]' : 'text-[#e7000b]' }}">{{ $level['level'] }}</strong>
                                <small class="text-xs text-[#555]">{{ $level['type'] }}</small>
                            </div>
                            <span class="text-lg font-bold {{ $isSupport ? 'text-[#168307]' : 'text-[#e7000b]' }}">{{ $level['price'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        {{-- MACRO FACTORS + CORRELATIONS --}}
        <section class="grid gap-5 px-5 pb-5 lg:grid-cols-2">
            {{-- Macro --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="landmark" class="h-5 w-5 text-[#333]"></i> Yếu tố vĩ mô tác động</h3>
                <div class="grid gap-2">
                    @foreach ($snapshot['macroFactors'] as $macro)
                        <div class="flex items-start gap-3 rounded-sm border border-[#ebebeb] bg-[#f5f5f5] p-2">
                            <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full {{ $macro['signal'] === 'positive' ? 'bg-[#e2ffde] text-[#168307]' : 'bg-[#ebebeb] text-[#666]' }}">@if($macro['signal'] === 'positive')<i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>@else<i data-lucide="minus" class="h-3.5 w-3.5"></i>@endif</span>
                            <div class="flex-1">
                                <div class="flex items-baseline justify-between">
                                    <strong class="text-sm">{{ $macro['factor'] }}</strong>
                                    <span class="text-sm font-bold text-[#001061]">{{ $macro['value'] }}</span>
                                </div>
                                <p class="mt-1 text-xs text-[#555]">{{ $macro['impact'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            {{-- Correlations --}}
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="git-compare" class="h-5 w-5 text-[#001061]"></i> Tương quan với các tài sản</h3>
                <div class="grid gap-3">
                    @foreach ($snapshot['correlations'] as $corr)
                        @php
                            $val = (float) $corr['corr'];
                            $absVal = abs($val);
                            $barColor = $val >= 0 ? 'bg-[#193cb8]' : 'bg-[#e7000b]';
                        @endphp
                        <div class="py-2">
                            <div class="flex items-baseline justify-between text-sm">
                                <span class="font-medium">{{ $corr['asset'] }}</span>
                                <span class="font-bold {{ $val >= 0 ? 'text-[#193cb8]' : 'text-[#e7000b]' }}">{{ $corr['corr'] }}</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-[#ebebeb]">
                                <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $absVal * 100 }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-[#555]">{{ $corr['note'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        {{-- Top Brands Table --}}
        <section class="px-5 pb-5">
            <div class="rounded-sm border border-[#bcbcbc] bg-white p-5" id="bang-gia">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="m-0 flex items-center gap-2 text-lg font-bold"><i data-lucide="table" class="h-5 w-5 text-[#001061]"></i> Bảng Giá Vàng Các Thương Hiệu</h3>
                    <button class="cursor-pointer rounded-sm text-sm font-semibold text-[#155dfc] transition hover:underline">Xem tất cả →</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] border-collapse">
                        <thead>
                            <tr class="bg-[#f5f5f5]">
                                <th class="border-b border-[#bcbcbc] p-3 text-left text-sm font-medium text-[#666]">Thương hiệu</th>
                                <th class="border-b border-[#bcbcbc] p-3 text-right text-sm font-medium text-[#666]">Mua vào (VND)</th>
                                <th class="border-b border-[#bcbcbc] p-3 text-right text-sm font-medium text-[#666]">Bán ra (VND)</th>
                                <th class="border-b border-[#bcbcbc] p-3 text-right text-sm font-medium text-[#666]">Chênh lệch</th>
                                <th class="border-b border-[#bcbcbc] p-3 text-right text-sm font-medium text-[#666]">Thay đổi</th>
                            </tr>
                        </thead>
                        <tbody id="topBrandsTableBody">
                            @foreach ($snapshot['topBrands'] as $brand)
                                <tr class="hover:bg-[#f5f5f5] transition">
                                    <td class="border-b border-[#ebebeb] p-3 text-left text-sm font-medium">{{ $brand['brand'] }}</td>
                                    <td class="border-b border-[#ebebeb] p-3 text-right text-sm font-bold">{{ number_format($brand['buy'], 0, ',', '.') }}</td>
                                    <td class="border-b border-[#ebebeb] p-3 text-right text-sm font-bold">{{ number_format($brand['sell'], 0, ',', '.') }}</td>
                                    <td class="border-b border-[#ebebeb] p-3 text-right text-sm text-[#666]">{{ number_format($brand['sell'] - $brand['buy'], 0, ',', '.') }}</td>
                                    <td class="border-b border-[#ebebeb] p-3 text-right text-sm font-bold {{ $brand['change'] >= 0 ? 'text-[#008236]' : 'text-[#e7000b]' }}">{{ sprintf('%+.2f%%', $brand['change']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- CENTRAL BANK ACTIVITY --}}
        <section class="px-5 pb-5">
            <div class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="building-2" class="h-5 w-5 text-[#e17100]"></i> Hoạt động mua vàng của NHTW</h3>
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    @foreach ($snapshot['centralBanks'] as $cb)
                        <div class="rounded-sm border border-[#ebebeb] bg-[#f5f5f5] p-3">
                            <h4 class="m-0 text-sm font-bold text-[#333]">{{ $cb['bank'] }}</h4>
                            <p class="mt-2 text-lg font-bold {{ $cb['trend'] === 'up' ? 'text-[#008236]' : 'text-[#666]' }}">{{ $cb['action'] }}</p>
                            <p class="mt-1 text-xs text-[#555]">{{ $cb['period'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 7-day Chart --}}
        <section class="px-5 pb-5">
            <div class="rounded-sm border border-[#bcbcbc] bg-white p-5" id="bieu-do-30-ngay">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="m-0 flex items-center gap-2 text-lg font-bold"><i data-lucide="chart-line" class="h-5 w-5 text-[#001061]"></i> Biến động giá vàng 30 ngày</h3>
                    <span class="text-xs text-[#555]">Đơn vị: triệu VNĐ/lượng</span>
                </div>
                <div class="rounded-sm border border-[#bcbcbc] bg-[#f5f5f5] p-3">
                    <div id="chart24hAmChart" class="h-[400px] w-full"></div>
                </div>
            </div>
        </section>

        {{-- Comparisons + Performance --}}
        <section class="grid gap-5 lg:grid-cols-2 px-5 pb-5" id="so-sanh">
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="scale" class="h-5 w-5 text-[#001061]"></i> So sánh giá vàng</h3>
                <div class="grid gap-2">
                    @foreach ($snapshot['comparisons'] as $row)
                        <div class="flex justify-between gap-3 rounded-sm border border-[#ebebeb] bg-[#f5f5f5] p-2">
                            <div>
                                <strong class="block text-sm">{{ $row['title'] }}</strong>
                                <small class="mt-1 block text-xs text-[#555]">{{ $row['subtitle'] }}</small>
                            </div>
                            <div class="text-right">
                                <strong class="block text-[#001061]">{{ $row['value'] }}</strong>
                                <small class="mt-1 block text-xs text-[#555]">{{ $row['note'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="wallet" class="h-5 w-5 text-[#008236]"></i> Hiệu suất đầu tư (1 lượng SJC)</h3>
                <div class="grid gap-2">
                    @foreach ($snapshot['performance'] as $row)
                        <div class="flex justify-between items-center gap-3 rounded-sm border border-[#ebebeb] bg-[#f5f5f5] p-3">
                            <div>
                                <strong class="block text-sm">{{ $row['period'] }}</strong>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-bold text-[#008236]">{{ $row['percent'] }}</span>
                                <span class="text-sm font-bold text-[#001061]">{{ $row['profit'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        {{-- Movers --}}
        <section class="px-5 pb-5">
            <div class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="flame" class="h-5 w-5 text-[#e17100]"></i> Biến động & Nổi bật</h3>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    @foreach ($snapshot['movers'] as $item)
                        <article class="rounded-sm border border-[#ebebeb] bg-[#f5f5f5] p-3">
                            <span class="inline-flex items-center rounded-full {{ ($item['trend'] ?? 'up') === 'up' ? 'bg-[#e2ffde] text-[#168307]' : (($item['trend'] ?? 'up') === 'down' ? 'bg-[#fff5ea] text-[#e7000b]' : 'bg-[#ebebeb] text-[#666]') }} px-2 py-0.5 text-xs font-semibold">{{ $item['type'] }}</span>
                            <h4 class="mt-2 m-0 text-sm font-bold">{{ $item['name'] }}</h4>
                            <p class="mt-1 text-xs text-[#555]">{{ $item['price'] }}</p>
                            <strong class="text-sm {{ ($item['trend'] ?? 'up') === 'up' ? 'text-[#008236]' : (($item['trend'] ?? 'up') === 'down' ? 'text-[#e7000b]' : 'text-[#666]') }}">{{ $item['extra'] }}</strong>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        {{-- Quick Actions + Knowledge --}}
        <section class="grid gap-5 lg:grid-cols-3 px-5 pb-5">
            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="zap" class="h-5 w-5 text-[#e17100]"></i> Hành động nhanh</h3>
                <div class="grid gap-2">
                    @foreach ($snapshot['actions'] as $action)
                        @if ($action['url'] === '#subscribeDialog')
                            <button class="cursor-pointer rounded-sm border border-[#bcbcbc] bg-[#f5f5f5] px-3 py-2 text-left text-sm font-semibold text-[#001061] transition hover:bg-[#ebebeb]" onclick="document.getElementById('subscribeModal')?.showModal()">{{ $action['label'] }}</button>
                        @else
                            <a href="{{ $action['url'] }}" class="block rounded-sm border border-[#bcbcbc] bg-[#f5f5f5] px-3 py-2 text-left text-sm font-semibold text-[#001061] transition hover:bg-[#ebebeb]">{{ $action['label'] }}</a>
                        @endif
                    @endforeach
                </div>
            </article>

            <article class="rounded-sm border border-[#bcbcbc] bg-white p-5 lg:col-span-2">
                <h3 class="mb-3 flex items-center gap-2 text-lg font-bold"><i data-lucide="book-open" class="h-5 w-5 text-[#001061]"></i> Kiến thức đầu tư vàng</h3>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($snapshot['knowledge'] as $item)
                        <a href="{{ $item['url'] ?? '#' }}" class="block rounded-sm border border-[#ebebeb] bg-[#f5f5f5] p-3 transition hover:bg-[#ebebeb]">
                            <h4 class="m-0 text-sm font-bold">{{ $item['title'] }}</h4>
                            <p class="mt-1 text-xs text-[#555]">{{ $item['desc'] }}</p>
                        </a>
                    @endforeach
                </div>
            </article>
        </section>
    </main>

    <section class="container-site px-6 pb-6">
        <div class="rounded-sm border border-[#bcbcbc] bg-white p-4 md:p-6 prose prose-sm max-w-none prose-headings:text-slate-900 prose-p:text-slate-700">
            <h2 class="font-bold text-xl mb-1">Giá vàng hôm nay mới nhất</h2>
            <p>Giá vàng hôm nay được cập nhật liên tục từ các thương hiệu lớn như SJC, PNJ, DOJI, Bảo Tín Minh Châu và Phú Quý. Trang web cung cấp bảng giá vàng chi tiết theo từng loại vàng như vàng miếng SJC, vàng nhẫn 9999, vàng 24K và vàng trang sức.</p>
            <p>Ngoài giá vàng trong nước, chúng tôi còn cập nhật giá vàng thế giới (XAU/USD) theo thời gian thực cùng biểu đồ biến động 24h, 7 ngày và dài hạn.</p>
            <p>Thông tin giúp nhà đầu tư theo dõi thị trường, phân tích xu hướng và đưa ra quyết định mua bán vàng hiệu quả.</p>
        </div>
    </section>

    {{-- FOOTER --}}
    @include('gold.partials.footer')

    {{-- Subscribe Modal --}}
    <dialog id="subscribeModal" class="m-auto w-[min(430px,calc(100%-1rem))] rounded-sm border-0 p-0 backdrop:bg-black/45">
        <form method="dialog" class="grid gap-3 p-4" id="subscribeForm">
            <h3 class="m-0 text-xl font-bold">Đăng ký nhận thông tin giá</h3>
            <p class="m-0">Nhập thông tin để nhận biến động giá vàng theo email.</p>
            <label class="text-sm text-[#333]">
                Họ tên
                <input type="text" name="name" placeholder="Nguyen Van A" class="mt-1 w-full rounded-sm border border-[#bcbcbc] px-3 py-2">
            </label>
            <label class="text-sm text-[#333]">
                Email
                <input type="email" name="email" placeholder="you@example.com" required class="mt-1 w-full rounded-sm border border-[#bcbcbc] px-3 py-2">
            </label>
            <fieldset class="rounded-sm border border-[#bcbcbc] p-3">
                <legend class="text-sm font-semibold text-[#333]">Thương hiệu vàng trong nước</legend>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <label><input type="checkbox" name="markets[]" value="sjc" checked> SJC</label>
                    <label><input type="checkbox" name="markets[]" value="doji" checked> DOJI</label>
                    <label><input type="checkbox" name="markets[]" value="pnj"> PNJ</label>
                    <label><input type="checkbox" name="markets[]" value="phuquy"> Phú Quý</label>
                    <label><input type="checkbox" name="markets[]" value="btmc"> Bảo Tín Minh Châu</label>
                    <label><input type="checkbox" name="markets[]" value="mihong"> Mi Hồng</label>
                </div>
            </fieldset>
            <fieldset class="rounded-sm border border-[#bcbcbc] p-3">
                <legend class="text-sm font-semibold text-[#333]">Thị trường vàng thế giới</legend>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <label><input type="checkbox" name="markets[]" value="xau-usd" checked> XAU/USD</label>
                    <label><input type="checkbox" name="markets[]" value="xag-usd"> XAG/USD</label>
                    <label><input type="checkbox" name="markets[]" value="xpt-usd"> XPT/USD</label>
                    <label><input type="checkbox" name="markets[]" value="xpd-usd"> XPD/USD</label>
                </div>
            </fieldset>
            <div class="flex justify-end gap-2">
                <button value="cancel" class="cursor-pointer rounded-sm border border-[#bcbcbc] bg-white px-4 py-2 text-sm font-semibold text-[#001061] transition hover:bg-[#f5f5f5]" type="button" id="closeSubscribeBtn">Đóng</button>
                <button class="cursor-pointer rounded-sm px-4 py-2 text-sm font-semibold text-white transition bg-[#001061] hover:bg-[#193cb8]" type="submit">Xác nhận đăng ký</button>
            </div>
        </form>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
</body>
</html>
