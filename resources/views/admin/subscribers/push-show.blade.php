<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết thông báo - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 font-sans text-[#333] bg-white min-h-screen">
    @include('gold.partials.header')

    <div class="container-site px-3 sm:px-6 py-6 sm:py-8">
        <div class="max-w-4xl mx-auto">

        <a href="{{ route('admin.subscribers.push') }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline mb-6">&larr; Quay lại Push thông báo</a>

        <div class="bg-white rounded-sm border border-slate-200 p-5 sm:p-6">
            <h1 class="text-lg font-bold text-[#001061] mt-0 mb-4">{{ $log->subject }}</h1>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-xs text-slate-500 m-0 mb-0.5">Người gửi</p>
                    <p class="text-sm font-semibold m-0">{{ $log->sender?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 m-0 mb-0.5">Thời gian gửi</p>
                    <p class="text-sm font-semibold m-0">{{ $log->sent_at?->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 m-0 mb-0.5">Thị trường</p>
                    <p class="text-sm m-0">
                        @if ($log->markets)
                            @foreach ($log->markets as $m)
                                <span class="inline-block rounded bg-blue-50 px-1.5 py-0.5 text-xs font-semibold text-blue-700">{{ strtoupper($m) }}</span>
                            @endforeach
                        @else
                            <span class="text-slate-400">Tất cả</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="rounded-sm bg-emerald-50 border border-emerald-200 p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-700 m-0">{{ $log->total_sent }}</p>
                    <p class="text-xs text-emerald-600 m-0 mt-1">Gửi thành công</p>
                </div>
                <div class="rounded-sm {{ $log->total_failed > 0 ? 'bg-rose-50 border-rose-200' : 'bg-slate-50 border-slate-200' }} border p-4 text-center">
                    <p class="text-2xl font-bold {{ $log->total_failed > 0 ? 'text-rose-700' : 'text-slate-400' }} m-0">{{ $log->total_failed }}</p>
                    <p class="text-xs {{ $log->total_failed > 0 ? 'text-rose-600' : 'text-slate-400' }} m-0 mt-1">Gửi lỗi</p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-700 mb-2 mt-0">Nội dung email</h3>
                <div class="rounded-sm border border-slate-200 bg-slate-50 p-4 prose prose-sm max-w-none">
                    {!! $log->content !!}
                </div>
            </div>
        </div>

        </div>
    </div>

    @include('gold.partials.footer')
</body>
</html>
